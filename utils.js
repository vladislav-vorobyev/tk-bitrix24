// Utils

function bitrix24_timestamp() {
	// "2024-02-08T14:32:53+03:00"
	// '2024-02-09T13:55:42.585Z'
	return (new Date()).toISOString();
}


// String format 'Like {1} or {2}'
if (!String.prototype.format) {
  String.prototype.format = function() {
    let args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined'? args[number] : match;
    });
  };
}


// Work with Date

if (!Date.parseDMY) {
  Date.parseDMY = function(str) {
    let dmy = str.split('.');
    return new Date(dmy[2], dmy[1] - 1, dmy[0]);
  };
}

if (!Date.prototype.diffDays) {
  Date.prototype.diffDays = function(d) {
    return Math.floor((this - d) / (1000 * 60 * 60 * 24)) + 1;
  };
}


// Sleep
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}


// Promise to wait until conditionFn() = true
function wait_until(conditionFn) {
	return new Promise( (resolve) => {
		let _check = () => conditionFn()? resolve() : setTimeout(_check, 10);
		_check();
	});
}


// Visual Elements

// Currency
function formatAsCurrency(number) {
	let pp = ('' + number).split('.');
	return '<span class="currency"><span>' + pp[0].slice(0,-3) + '</span><span>' + pp[0].slice(-3) + (pp[1] ?? '') + '</span></span>';
}

// progressbar
function createProgressbarElement($parent, maxNumber) {
	let num = 0;
	let $num = $('<span></span>');
	let $progressbar = $('<div></div>');
	$parent.html($('<div class=progressbar></div>').html($progressbar)).append($num);
	// procedure to update
	let doUpdate = (_num) => {
		num = _num;
		$num.html(num);
		let pers = Math.round( num * 100 / maxNumber ) + '%';
		$progressbar.css({width:pers});
		if (num >= maxNumber) {
			$progressbar.addClass('done');
		}
	};
	// create an Object
	return {
		$bar: $progressbar,
		getMaxNumber: () => maxNumber,
		getNumber: () => num,
		setNumber: (_num) => doUpdate(_num),
		incNumber: () => doUpdate(num + 1),
	};
}

// loadbar
function createLoadbarElement($parent) {
	let $bar = $('<div class="loadbar active"></div>');
	$parent.html($bar);
	// create an Object
	return {
		$bar: $bar,
		active: () => {$bar.addClass('active')},
		inactive: () => {$bar.removeClass('active')},
	};
}

// check and highlight the date
function checkDateDiff(dateStr, days) {
	if (dateStr)
		try {
			if (Date.parseDMY(dateStr).diffDays(Date.now()) <= days)
				dateStr = '<red>'+dateStr+'</red>';
		} catch(e) { console.warn(e); }
	return dateStr;
}

// warning simbol
function warnSimbol() {
	return '<warn>⚠</warn>';
}


// Array extension with init and update functions
class ItemsArray extends Array {
	constructor(initFn, ...args) {
		super(...args);
		this._initFn = initFn;
	}
	_up(i, fn) {
		let r = this[i] ?? this._initFn(i);
		fn(r);
		this[i] = r;
	}
}


// Send request with data from form
function sendRequest(method, params) {
	if (method == undefined) method = $('#sendmethod').val();
	if (params == undefined) {
		let txt = $('#sendparams').val();
		if (txt && txt.length) {
			// trying to parse as JSON in 3 attempts:
			// on 1st error -> replace ' with "
			// on 2nd error -> surround with "
			let todo = 3;
			while(todo && !params) {
				try {
					params = JSON.parse(txt);
				} catch(error) {
					// console.warn(error);
					if (todo == 3) {
						txt = txt.replace(/'/g,'"');
					} else
					if (todo == 2) {
						// surround words with ""
						txt = txt.split(/([\s\t\[\]\{\}:,]+)/g)
							.map( (s) => ( (/^[a-zA-Z_]+$/).test(s)? '"'+s+'"' : s ) )
							.join('');
					}
					else throw error;
					// apply changes to the input field
					$('#sendparams').val(txt);
					todo--;
				}
			}
		}
	}
	// console.log('call',method,params);
	BX24.callMethod(
		method,
		params,
		(result) => {
			if(result.error()) {
				console.info(result);
				console.warn(result.error(), result.error_description());
			} else {
				console.info(result);
				console.dir(result.data());
				if(result.more())
					result.next();
			}
		}
	);
}
