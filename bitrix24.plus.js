/**
*
* BX24 extension to provide a working via Promises
*
*/

window.BX24ex = {};


// Constants
/*
Тип сущности	Числовой идентификатор (entityTypeId)	Символьный код (entityTypeName)	Краткий символьный код	Тип сущности пользовательского поля
Лид	1	LEAD	L	CRM_LEAD
Сделка	2	DEAL	D	CRM_DEAL
Контакт	3	CONTACT	C	CRM_CONTACT
Компания	4	COMPANY	CO	CRM_COMPANY
Счет (старый)	5	INVOICE	I	CRM_INVOICE
Счет (новый)	31	SMART_INVOICE	SI	CRM_SMART_INVOICE
Предложение	7	QUOTE	Q	CRM_QUOTE
Реквизит	8	REQUISITE	RQ	CRM_REQUISITE
*/
BX24ex.EntityTypeId = {LEAD:1, DEAL:2, CONTACT:3, COMPANY:4, INVOICE:5, SMART_INVOICE:31, QUOTE:7, REQUISITE:8};


// catch handler
BX24ex.warnErrorCallback = (error) => {
	console.warn(error, error?.error_description?.() ?? error?.answer?.error?.error_description);
}

// function to check/prepare a data extraction function
BX24ex.prepareExtractionFn = (fn) => {
	if (fn === undefined)
		return (data) => data;
	if (typeof fn === 'function')
		return fn;
	let dataField = fn;
	return (data) => data?.[dataField];
}


// Promises to call a methods

BX24ex.simplePromise = function(method, params) {
	return new Promise( (resolve, reject) => {
		BX24.callMethod(
			method,
			params,
			(result) => {
				if (result.error())
					reject(result);
				else
					resolve(result);
			}
		);
	});
}

BX24ex.aPromise = function(method, params, dataFn) {
	// data extraction function
	dataFn = BX24ex.prepareExtractionFn(dataFn);
	// prepare limit for list items to load
	const limit = params?.limit ?? 5000;
	//
	let data, total;
	return new Promise( (resolve, reject) => {
		BX24.callMethod(
			method,
			params,
			async (result) => {
				if (result.error()) {
					reject(result);
				} else {
					data = data === undefined? dataFn(result.data()) : Array.prototype.concat( data, dataFn(result.data()) );
					// request more
					if (result.more()) {
						// define total from first result
						total = total === undefined? result.total() ?? false : total;
						if (total) {
							// get all via batch request
							let next = result.answer.next;
							let calls = [];
							for (let i = next; i < total && i < limit; i += next) {
								let _params = {};
								Object.assign(_params, params);
								_params.start = i;
								calls.push( [method, _params] );
							}
							if (calls.length) {
								// do request
								let results = await BX24ex.batchPromise(calls, true);
								// append recieved data to output array
								results.forEach(
									(_data) => {
										data = Array.prototype.concat( data, dataFn(_data) );
									},
									reject
								);
							}
						} else {
							if (result.answer.next < limit) {
								// get next
								result.next();
							}
						}
					}
					// finalize
					resolve(data);
				}
			}
		);
	});
}

BX24ex.batchPromise = async function(calls, bHaltOnError, dataFn) {
	// split calls into parts that not more than 50 peaces and call each
	let partsResults = await Promise.all(
		BX24ex.splitPerParts(calls).map( (batch) =>
			new Promise( (resolve, reject) => {
				BX24.callBatch(
					batch,
					(results) => { resolve(results); },
					bHaltOnError
				);
			})
		)
	);
	// reduce back to flat array and set as BatchResult
	let results = new BatchResult(
		partsResults.reduce(
			(accumulator, results) => {
				if (accumulator === null)
					accumulator = results;
				else
					for (key in results) accumulator[key] = results[key];
				return accumulator;
			},
			null
		),
		dataFn
	);
	// check results if halt on error is true
	if (bHaltOnError) results.forEach( () => {} );
	// return result
	return results;
}

BX24ex.splitPerParts = (calls) => {
	let keys = Object.keys(calls);
	if (keys.length > 50) {
		let parts = [];
		let batch = [];
		let cnt = 0;
		for (const key of keys) {
			if (cnt == 50) {
				parts.push(batch);
				batch = [];
				cnt = 0;
			}
			batch[key] = calls[key];
			cnt++;
		}
		parts.push(batch);
		return parts;
	} else {
		return [calls];
	}
}


// batch result prototype

var BatchResult = function(results, dataFn) {
	this.results = results;
	this.dataFn = BX24ex.prepareExtractionFn(dataFn);
}

BatchResult.prototype.itemCallback = (callback, errorCallback, dataFn) => (result, i) => {
	if (result.error()) {
		if (errorCallback)
			return errorCallback.call(result, result, i);
		else
			throw result;
	} else {
		return callback.call(result, dataFn(result.data()), i, result);
	}
}

BatchResult.prototype.forEach = function(callback, errorCallback) {
	this.results.forEach( this.itemCallback(callback, errorCallback, this.dataFn) );
}

BatchResult.prototype.map = function(callback, errorCallback) {
	return this.results.map( this.itemCallback(callback, errorCallback, this.dataFn) );
}

BatchResult.prototype.filter = function(callback, errorCallback) {
	return new BatchResult( this.results.filter( this.itemCallback(callback, errorCallback, this.dataFn) ), this.dataFn );
}

BatchResult.prototype.reduce = function(callback, initial, errorCallback) {
	return this.results.reduce(
		(accumulator, result, i) => {
			if (result.error()) {
				if (errorCallback)
					return errorCallback.call(result, result, i);
				else
					throw result;
			} else {
				return callback.call(result, accumulator, this.dataFn(result.data()), i, result);
			}
		},
		initial
	);
}
