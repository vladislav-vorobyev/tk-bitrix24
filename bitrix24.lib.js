// CRM
var crm = {
	// deal
	deal: {
		list: (params) => BX24ex.aPromise('crm.deal.list', params),
		get: (id) => BX24ex.aPromise('crm.deal.get', { id: id }),
		update: (params) => BX24ex.aPromise('crm.deal.update', params),
		fields: () => BX24ex.aPromise('crm.deal.fields', {}),
		
		// productrows
		productrows: {
			get: (dealId) => BX24ex.aPromise('crm.deal.productrows.get', { id: dealId }),
		},
		
		// userfield
		userfield: {
			list: (params) => BX24ex.aPromise('crm.deal.userfield.list', params),
			add: (params) => BX24ex.aPromise('crm.deal.userfield.add', params),
			get: (id) => BX24ex.aPromise('crm.deal.userfield.get', { id: id }),
		},
	},
	
	// contact
	contact: {
		list: (params) => BX24ex.aPromise('crm.contact.list', params),
	},
	
	// status
	status: {
		list: (params) => BX24ex.aPromise('crm.status.list', params),
	},
	
	// category
	category: {
		list: (params) => BX24ex.aPromise('crm.category.list', params, 'categories'),
		listByType: (entityTypeId) => BX24ex.aPromise('crm.category.list', crm.category._listByType(entityTypeId), 'categories'),
		_listByType: (entityTypeId) => { return {entityTypeId: entityTypeId} },
	},
	
	// item
	item: {
		list: (params) => BX24ex.aPromise('crm.item.list', params, 'items'),
		get: (id, entityTypeId) => BX24ex.aPromise('crm.item.get', { id: id, entityTypeId: entityTypeId }, 'item'),
		update: (params) => BX24ex.aPromise('crm.item.update', params, 'item'),
		fields: (params) => BX24ex.aPromise('crm.item.fields', params, 'fields'),
		
		// productrow
		productrow: {
			list: (params) => BX24ex.aPromise('crm.item.productrow.list', params, 'productRows'),
			listByDeal: (dealId) => BX24ex.aPromise('crm.item.productrow.list', crm.item.productrow._listByDeal(dealId), 'productRows'),
			_listByDeal: (dealId) => { return {filter: {'=ownerType': 'D', '=ownerId': dealId}} },
			listByDealProduct: (dealId, productId) =>
				BX24ex.aPromise('crm.item.productrow.list', crm.item.productrow._listByDealProduct(dealId, productId), 'productRows'),
			_listByDealProduct: (dealId, productId) => { return {filter: {'=ownerType': 'D', '=ownerId': dealId, '=productId': productId}} },
		},
	},
	
	// orderentity
	orderentity: {
		list: (params) => BX24ex.aPromise('crm.orderentity.list', params, 'orderEntity'),
	},
	
	// documentgenerator
	documentgenerator: {
		// document
		document: {
			list: (params) => BX24ex.aPromise('crm.documentgenerator.document.list', params, 'documents'),
		},
	},
	
	// invoice (item with BX24ex.EntityTypeId.SMART_INVOICE)
	invoice: {
		_p: (params) => { params = params ?? {}; params.entityTypeId = BX24ex.EntityTypeId.SMART_INVOICE; return params; },
		list: (params) => BX24ex.aPromise('crm.item.list', crm.invoice._p(params), 'items'),
		get: (id) => BX24ex.aPromise('crm.item.get', crm.invoice._p({ id: id }), 'item'),
		update: (params) => BX24ex.aPromise('crm.item.update', crm.invoice._p(params), 'item'),
		fields: (params) => BX24ex.aPromise('crm.item.fields', crm.invoice._p(params), 'fields'),
		
		// document
		document: {
			list: (params) => BX24ex.aPromise('crm.documentgenerator.document.list', crm.invoice._p(params), 'documents'),
		},
	},
};


// sale
var sale = {
	// order
	order: {
		list: (params) => BX24ex.aPromise('sale.order.list', params, 'orders'),
		get: (id) => BX24ex.aPromise('sale.order.get', { id: id }, 'order'),
	},
	
	// basketItem
	basketItem: {
		list: (params) => BX24ex.aPromise('sale.basketItem.list', params, 'basketItems'),
		get: (id) => BX24ex.aPromise('sale.basketItem.get', { id: id }, 'basketItem'),
	},
	
	// shipment
	shipment: {
		list: (params) => BX24ex.aPromise('sale.shipment.list', params, 'shipments'),
		get: (id) => BX24ex.aPromise('sale.shipment.get', { id: id }, 'shipment'),
	},
	
	// shipmentitem
	shipmentitem: {
		list: (params) => BX24ex.aPromise('sale.shipmentitem.list', params, 'shipmentItems'),
		get: (id) => BX24ex.aPromise('sale.shipmentitem.get', { id: id }, 'shipmentItem'),
	},
};


// catalog
var catalog = {
	// catalog
	catalog: {
		list: (params) => BX24ex.aPromise('catalog.catalog.list', params, 'catalogs'),
		get: (id) => BX24ex.aPromise('catalog.catalog.get', { id: id }, 'catalog'),
	},
	
	// section
	section: {
		list: (params) => BX24ex.aPromise('catalog.section.list', (params ?? {filter: {iblockId: catalog.product.iblockId}}), 'sections'),
	},
	
	// store
	store: {
		list: (params) => BX24ex.aPromise('catalog.store.list', params, 'stores'),
		get: (id) => BX24ex.aPromise('catalog.store.get', { id: id }, 'store'),
	},
	
	// price
	price: {
		list: (params) => BX24ex.aPromise('catalog.price.list', params, 'prices'),
		listByProduct: (productId, df = 'prices') => BX24ex.aPromise('catalog.price.list', catalog.price._listByProduct(productId), df), // TODO: check solution
		_listByProduct: (productId) => { return {filter: {catalogGroupId: catalog.price.catalogGroupId, productId: productId}} },
		add: (params) => BX24ex.aPromise('catalog.price.add', params, 'price'),
		'delete': (id) => BX24ex.aPromise('catalog.price.delete', { id: id }),
		get: (id) => BX24ex.aPromise('catalog.price.get', { id: id }, 'price'),
		update: (params) => BX24ex.aPromise('catalog.price.update', params, 'price'),
		modifyByProduct: (productId, price) => BX24ex.aPromise('catalog.price.modify', catalog.price._modifyByProduct(productId, price), 'price'),
		_modifyByProduct: (productId, price) => {
			return {fields: {product: {id: productId, prices: [{catalogGroupId: catalog.price.catalogGroupId, price: price, currency: "RUB"}] }}} },
	},
	
	// priceType
	priceType: {
		list: (params) => BX24ex.aPromise('catalog.priceType.list', params, 'priceTypes'),
	},
	
	// productProperty
	productProperty: {
		list: (params) => BX24ex.aPromise('catalog.productProperty.list', params, 'productProperties'),
	},
	
	// productPropertyEnum
	productPropertyEnum: {
		list: (params) => BX24ex.aPromise('catalog.productPropertyEnum.list', params, 'productPropertyEnums'),
	},
	
	// product
	product: {
		list: (params) => BX24ex.aPromise('catalog.product.list', params, 'products'),
		listAll: () => BX24ex.aPromise('catalog.product.list', catalog.product._listAll(), 'products'),
		_listAll: () => { return {select: ['id', 'iblockId', '*'], filter: {iblockId: catalog.product.iblockId}, order: {id: 'ASC'}} },
		add: (params) => BX24ex.aPromise('catalog.product.add', params, 'product'),
		'delete': (id) => BX24ex.aPromise('catalog.product.delete', { id: id }),
		get: (id) => BX24ex.aPromise('catalog.product.get', { id: id }, 'product'),
		update: (params) => BX24ex.aPromise('catalog.product.update', params, 'product'),
		getFieldsByFilter: (params) => BX24ex.aPromise('catalog.product.getFieldsByFilter', params),
		
		// offer
		offer: {
			list: (params) => BX24ex.aPromise('catalog.product.offer.list', params, 'offers'),
			listAll: () => BX24ex.aPromise('catalog.product.offer.list', catalog.product.offer._listAll(), 'offers'),
			_listAll: () => { return {select: ['id', 'iblockId', '*'], filter: {iblockId: catalog.product.offer.iblockId}, order: {id: 'ASC'}} },
			listByParent: (parentId) => BX24ex.aPromise('catalog.product.offer.list', catalog.product.offer._listByParent(parentId), 'offers'),
			_listByParent: (parentId, start) => {
				return {select: ['id', 'iblockId', '*'], filter: {iblockId: catalog.product.offer.iblockId, parentId: parentId}, order: {id: 'ASC'}, start: start} },
			add: (params) => BX24ex.aPromise('catalog.product.offer.add', params, 'offer'),
			'delete': (id) => BX24ex.aPromise('catalog.product.offer.delete', { id: id }),
			get: (id) => BX24ex.aPromise('catalog.product.offer.get', { id: id }, 'offer'),
			update: (params) => BX24ex.aPromise('catalog.product.offer.update', params, 'offer'),
		},
	},
	
	// document
	document: {
		list: (params) => BX24ex.aPromise('catalog.document.list', params, 'documents'),
		// get: (id) => BX24ex.aPromise('catalog.document.get', { id: id }, 'document'), Could not find description of get in Bitrix\\Catalog\\Controller\\Document
		get: (id) => BX24ex.aPromise('catalog.document.list', {filter: {id: id}}, (d) => d.documents?.[0]),
		
		// element
		element: {
			list: (params) => BX24ex.aPromise('catalog.document.element.list', params, 'documentElements'),
			listByDoc: (docId) => BX24ex.aPromise('catalog.document.element.list', catalog.document.element._listByDoc(docId), 'documentElements'),
			_listByDoc: (docId, start) => { return {filter: {docId: docId}, order: {id: 'ASC'}, start: start} },
		},
	},
	
	// userfield
	userfield: {
		// document
		document: {
			list: (params) => BX24ex.aPromise('catalog.userfield.document.list', params, 'documents'),
			listByType: (documentType) => BX24ex.aPromise('catalog.userfield.document.list', catalog.userfield.document._listByType(documentType), 'documents'),
			_listByType: (documentType, start) => { return {filter: {documentType: documentType}, start: start} },
			update: (params) => BX24ex.aPromise('catalog.userfield.document.update', params, 'document'),
		},
	},


	// Main constants initialization function
	init: async () => {
		try {
			let catalogList, priceTypes;
			await Promise.all([
				catalog.catalog.list().then(data => {catalogList = data}),
				catalog.priceType.list().then(data => {priceTypes = data}),
			]);
			// define iblockId
			catalogList.forEach( (item) => {
				if (item.productIblockId) {
					catalog.product.offer.iblockId = item.iblockId ?? item.id;
					catalog.product.offer.skuPropertyId = item.skuPropertyId;
					catalog.product.iblockId = item.productIblockId;
				}
			});
			if (!catalog.product.offer.iblockId) console.warn('catalog.product.offer.iblockId is undefined!');
			if (!catalog.product.offer.skuPropertyId) console.warn('catalog.product.offer.skuPropertyId is undefined!');
			if (!catalog.product.iblockId) console.warn('catalog.product.iblockId is undefined!');
			// define catalogGroupId
			priceTypes.forEach( (item) => {
				if (item.base == 'Y') {
					catalog.price.catalogGroupId = item.id;
					catalog.price.name = item.name;
				}
			});
			if (!catalog.price.catalogGroupId) console.warn('catalog.price.catalogGroupId is undefined!');
			else if (priceTypes.length > 1) console.info('Default price is:', catalog.price.catalogGroupId, catalog.price.name)
			// set final flag
			catalog._ready = true;
			
		} catch(error) {
			catalog._ready = false;
			catalog._init_error = error;
			throw error;
		}
	},
	
	// Promise to wait an initialization finish
	waitReady: () => {
		return new Promise( (resolve, reject) => {
			let _check = () => catalog._ready === true? resolve() : catalog._init_error !== undefined? reject(catalog._init_error) : setTimeout(_check, 10);
			_check();
		});
	},
};

if (typeof DO_NOT_INIT_CATALOG === 'undefined' || !DO_NOT_INIT_CATALOG) {
	// Initialize
	catalog.init()
	.then(() => { console.info('BX24ex catalog ready.'); })
	.catch(BX24ex.warnErrorCallback);
}