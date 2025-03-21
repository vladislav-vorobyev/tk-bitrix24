<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ТК плагин</title>

<link rel='stylesheet' type='text/css' href='style.css'>

<script src="/js/jquery-3.2.1.min.js"></script>
<!--<script src="//api.bitrix24.com/api/v1/"></script>-->
<script src="bitrix24.api.v1.js"></script>
<script src="bitrix24.plus.js"></script>
<script src="bitrix24.lib.js"></script>
<script src="utils.js"></script>
</head>
<body>

<!--<h2>Test b24 2</h2>-->

<fieldset id="sendRequest"><legend>Send request</legend>
Function: <input type="text" id="sendmethod" style="width:calc(100% - 110px)" /><br>
<textarea id="sendparams" style="height:150px"></textarea><br>
<button onclick="sendRequest()" style="margin-left:0">Send</button>
</fieldset>


<fieldset id="dealBox"><legend>Сделка</legend>
ID сделки: <input type="text" id="dealIdToShow" style="width:80px" />
<button onclick="toinfoDealbyId($('#dealIdToShow').val())">☇</button>
<button class="main" onclick="showDealProducts()">Показать товары</button>
<button onclick="event.stopPropagation(); showDealProducts()" title="Обновить товары">⟲</button>
<br>
<div id="dealProducts"></div>
</fieldset>


<fieldset id="docBox"><legend>Склад</legend>
ID документа: <input type="text" id="docIdToShow" style="width:80px" />
<button onclick="toinfoDocbyId($('#docIdToShow').val())">☇</button>
<button class="main" onclick="showDocProducts()">Показать товары</button>
<button onclick="event.stopPropagation(); showDocProducts()" title="Обновить товары">⟲</button>
<br>
<div id="docProducts"></div>
</fieldset>


<fieldset id="docFromDeal"><legend>Сравнение / Копирование товарных позиций</legend>
ID сделки: <input type="text" id="dealIdToCopyFrom" style="width:70px" /> =>
ID прихода/оприходования: <input type="text" id="docIdToCopyTo" style="width:70px" />
<button class="main" onclick="compareDealToDoc()">Сравнить</button>
<br>
<label class="pointer"><input type="checkbox" name="do-doc-cleanup" checked /> Удалить старые позиции с товарами из документа до копирования</label>
<br>
<button class="main" onclick="copyProductsToDoc()">Копировать</button>
<br>
<span class="deal-name"></span> => <span class="doc-name"></span>
<div class="products-table"></div>
</fieldset>


<fieldset id="dealsBox">
<legend>Поиск по товару (сделки, склад) <div id="dealsLoad" class="process-info"></div> <span id="dealsInfo"></span>
<button onclick="event.stopPropagation(); getAllToFindByProduct()" style="margin:0" title="Обновить сделки и документы">⟲</button>
</legend>
ID товара: <input type="text" id="productIdToFind" style="width:80px" />
<button onclick="toinfoProductbyId($('#productIdToFind').val())">☇</button>
<button class="main" onclick="findAllByProduct()">Найти по товару</button>
<label class="pointer"><input type="checkbox" name="show-closed-deals" onclick="setTimeout(findAllByProduct, 0)" /> Отобразить закрытые сделки</label>
<br>
Заказы:
<div id="ordersLoad" class="process-info"></div>
<br>

<!--<button onclick="getAllDeals()" style="margin-left:0">Загрузить сделки</button>-->
<!--<button onclick="getAllOrders()" style="margin-left:0">Загрузить заказы</button>-->
<!--<button onclick="findDealsByProduct($('#productIdToFind').val())">Найти сделки по товару</button>-->
<table id="dealTable" class="common">
	<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th title="productRows">Items</th>
			<th>Stage</th>
			<th></th>
			<th class="full-view">row.ID</th>
			<th class="full-view">productId</th>
			<th title="QUANTITY">Q-ty</th>
			<th title="RESERVE_QUANTITY">R-ve</th>
			<th title="DATE_RESERVE_END">R_END</th>
			<th title="RESERVE_ID">R_ID</th>
			<th></th>
			<th class="full-view">Order ID</th>
			<th>Shipment</th>
			<th>dateUpdate</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

Склад:
<div id="documentsLoad" class="process-info"></div>
<!--<button onclick="getAllDocuments()" style="margin-left:0">Загрузить склад</button>-->
<!--<button onclick="findDocsByProduct($('#productIdToFind').val())">Найти документы по товару</button>-->
<table id="docTable" class="common">
	<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Elements</th>
			<th>Status</th>
			<th></th>
			<th class="full-view">e.id</th>
			<th class="full-view">productId</th>
			<th title="amount">Q-ty</th>
			<th title="storeFrom">From</th>
			<th title="storeTo">To</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

<div>
<button class="main" onclick="showReserve()">Показать товары в ожидании</button>
<div id="reserveOutput" class=""></div>
</div>

<div>
<button class="main" onclick="reviewStock()">Проверка отгрузок, остатков и резервов</button>
<div id="reviewOutput" class=""></div>
</div>

<div>
<button class="main" onclick="linkingDocsWithDeals()">Найти связи Склада и Сделок</button>
<label class="pointer"><input type="checkbox" id="show-test-doc-with-deals" onclick="setTimeout(linkingDocsWithDeals, 0)" /> Отобразить тестовые и временные</label>
<div id="linkingDxDoutput" class=""></div>
</div>
</fieldset>


<fieldset id="productsBox">
<legend>Список товаров <div id="productsLoad" class="process-info"></div><span id="productsInfo"></span><span id="offersCount"></span>
<button onclick="event.stopPropagation(); getAllProducts()" style="margin:0" title="Обновить товары">⟲</button>
</legend>
<!--<button onclick="checkProductsXmlId()">Check xmlId</button>-->
<label class="pointer"><input type="checkbox" name="product-price-view" onclick="$('#productTable').toggleClass('product-price')" /> Только цена</label>
<button id="xmlIdFix" style="display:none">Исправить xmlId</button>
<table id="productTable" class="common product-active">
	<thead>
		<tr>
			<th class="price-view">ID</th>
			<th class="name price-view">Name</th>
			<th class="offers">Offers</th>
			<th class="active">Active</th>
			<th class="quantity">Q.</th>
			<th class="reserved">R.</th>
			<th class="ordoc">O/D</th>
			<th class="xmlId fix-only">xmlId</th>
			<th class="price price-view">Price</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
</fieldset>


<!--<fieldset><legend>Set product price by ID</legend>
ID: <input type="text" id="productId" style="width:110px" />
Price: <input type="text" id="productPrice" style="width:110px" />
<button onclick="setCatalogProductOfferPriceByParent($('#productId').val(), $('#productPrice').val())">Set catalog.product.offers price by product ID</button>
<button onclick="doCrmProductUpdatePrice($('#productId').val(), $('#productPrice').val())">Do crm.product.update price by product ID</button>
</fieldset>-->


<fieldset><legend>Цена на товары</legend>
<textarea id="priceInput" style="height:70px"></textarea><br>
<table id="priceTable" class="common">
	<thead><tr><th>ID</th><th>Price</th><th>Offers</th><th>Current</th><th></th></tr></thead>
	<tbody></tbody>
</table>
<button onclick="setCatalogProductPriceByList()" style="margin-left:0">Set prices</button>
<button onclick="checkProductTableCurrent()">Check current prices</button>
<button onclick="upOffersXmlId()">Update xmlId</button>
</fieldset>


<fieldset id="productStruct"><legend>Вариации товара</legend>
ID товара: <input type="text" id="productIdToShow" style="width:110px" />
<button class="main" onclick="showProductStruct()">Показать</button>
<br>

<table id="productStructTable" class="common">
	<thead>
		<tr>
			<th>ID</th>
			<th>Название</th>
			<th class="title">Вариации</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<div class="actions"></div>
</fieldset>


<fieldset id="contactsBox"><legend>Контакты
<button onclick="event.stopPropagation(); showContacts()" style="margin:0" title="Обновить">⟲</button>
</legend>
<label class="pointer"><input type="checkbox" name="contacts-all" onclick="setTimeout(showContacts,0)" /> Все</label>
<br>
<table id="contactsTable" class="common">
	<thead></thead>
	<tbody></tbody>
</table>
<div class="actions"></div>
</fieldset>


<fieldset id="buttonsBox"><legend>Buttons</legend>

<button onclick="updateDealsUTMSource()">Test UF_CRM_UTM_SOURCE1 => utm_source</button>
<button onclick="if (confirm('Update?')) updateDealsUTMSource(true)">Update utm_source</button>
<br>
<button onclick="updateDealsUTMSource2()">Test SOURCE_ID => utm_source</button>
<button onclick="if (confirm('Update?')) updateDealsUTMSource2(true)">Update utm_source</button>
<br>
<button onclick="updateDealsUTMSource3()">Test reset utm_source</button>
<button onclick="if (confirm('Reset?')) updateDealsUTMSource3(true)">Reset utm_source</button>

<br>
<br>
<button onclick="checkDealsTypeVsCategory()">Check Deals Types</button>
<button onclick="checkDealsTypeVsCategory(true)">Fix Deals Types</button>
<br>
<button onclick="checkDealsType5()">Check Deals (Type 5)</button>
<button onclick="changeContactsType()">Change Contacts Type</button>
<br>
<button onclick="getDealFields()">Get Deal Fields + map</button>
<button onclick="up1Deal(prompt('Введите ID'))">Update1 Deal</button>
<button onclick="up1DealList()">Update1 Deal List</button>
<br>
<button onclick="getDeal(prompt('Введите ID'))">Get Deal</button>
<br>
<button onclick="getaPromise('crm.orderentity.list', {}, 'orderEntity')">Get crm.orderentity.list</button>
<button onclick="getaPromise('crm.orderentity.getFields', {})">Get crm.orderentity.getFields</button>
<br>
<button onclick="getaPromise('crm.enum.ownertype', {})">Get crm.enum.ownertype</button>
<button onclick="getaPromise('crm.item.productrow.fields', {}, 'fields')">Get crm.item.productrow.fields</button>
<button onclick="getCrmItemProductrowList(prompt('Введите ownerId'))">Get crm.item.productrow.list by ownerId</button>
<button onclick="getCrmItemProductrowList2(prompt('Введите ownerId'))">Get crm.item.productrow.list by ownerId+productId</button>
<br>
<button onclick="getaFunc('crm.item.fields', {entityTypeId:31})">Get crm.item.fields {entityTypeId:31}</button>

<br>
<br>
<button onclick="getaPromise('crm.catalog.list', {})">Get crm.catalog.list</button>
<button onclick="getCrmProductList(prompt('Введите ID каталога'))">Get crm.product.list</button>
<button onclick="getCrmProductPropertyList()">Get crm.product.property.list</button>

<br>
<br>
<button onclick="getaFunc('catalog.catalog.list')">Get catalog.catalog.list</button>
<button onclick="getCatalogProductList()">Get catalog.product.list</button>
<button onclick="getCatalogProductOfferList()">Get catalog.product.offer.list</button>
<br>
<button onclick="getCatalogProductFieldsByFilter()">Get catalog.product.getFieldsByFilter</button>
<br>
<button onclick="getCatalogProductOfferListBy(prompt('Введите parentId'))">Get catalog.product.offer.list by parentId</button>
<br>
<button onclick="getaFunc('catalog.priceType.list')">Get catalog.priceType.list</button>
<button onclick="getaFunc('catalog.price.list')">Get catalog.price.list</button>
<button onclick="getCatalogPriceListBy(prompt('Введите productId'))">Get catalog.price.list by productId</button>

<br>
<br>
<button onclick="MigrateDealUserfield('UF_CRM_1712163292535', 'UTM_SOURCE1')">Migrate UF_CRM_1712163292535 => UTM_SOURCE1</button>

<br>
<br>
<button onclick="console.info(BX24.placement.info())">BX24.placement.info</button>
<button onclick="console.info(b24Tracker.guest.getTrace())">b24Tracker.guest.getTrace</button>

<br>
<br>
<button onclick="testBX24ext()">Test BX24 ext</button>

</fieldset><!--Buttons-->

<script language="javascript">
var $=jQuery;
var env = {dics:false, stores:false, props:false}; // flags to check environment ready
var env_ready = () => env.dics && env.stores && env.props; // function to check

const dealsExcludedFromCheck = {
	12:true,
};

const stagesDealCheckReserve = {
	'4':true,'EXECUTING':true,'FINAL_INVOICE':true,'UC_CSM3K2':true,
	'C4:1':true,'C4:2':true,'C4:4':true,
	'C6:EXECUTING':true,'C6:FINAL_INVOICE':true,
	'C8:5':true
};

const stagesDealWon = {
	'WON':true,'C2:WON':true,'C4:WON':true,'C6:WON':true,'C8:WON':true,
	'C10:WON':true,'C12:WON':true
};

const DEAL_CATEGORY_TYPES = {
	'0': ['SALE','SERVICE'], // "Заказы"
	'2': ['1','2'], // "Предложения"
	'4': ['COMPLEX','SERVICE'], // "Заказ без предоплаты"
	'6': ['3'], // "Заказ из маркетплейса"
	'8': ['4'], // "Дилерский заказ"
	'10': ['5'], // "Не клиент"
	'12': ['6'], // "Заявка в фонд"
};

const FIELD_NAME_FOR_DEAL = { 'A':'field622', 'S':'field620' };

// get document->deal link
var getDocLink = (document) => document[FIELD_NAME_FOR_DEAL[document.docType]];
// function to check deal stage
var isDealNotLose = (deal) => deal?.CLOSED != 'Y' || stagesDealWon[deal?.STAGE_ID];
var isDealCheckReserve = (deal) => stagesDealCheckReserve[deal?.STAGE_ID];
// function to check deal type by category
var isDealTypeByCategory = (deal) => DEAL_CATEGORY_TYPES[deal?.CATEGORY_ID]?.reduce( (a,v) => a || v == deal.TYPE_ID, false );
// function to get deal deafault type by category
var getDealDefaultType = (categoryId) => DEAL_CATEGORY_TYPES[categoryId]?.[0];
// function to show deal stage
var printDealStage = (deal) => crm.status.dealStages[deal?.STAGE_ID]?.NAME ?? '';
// function to show document status
var printDocStatus = (doc) => doc? (doc.status == 'Y'? 'Проведён' : (doc.statusBy? 'Отменён' : 'Черновик')) : '';
// function to show store title
var printStoreTitle = (storeId) => catalog.store.byId[storeId]?.title ?? storeId;
	
// function to get a basket item id from deal productrow
var getRowBasketId = (productrow) => parseInt(productrow?.XML_ID?.substr(12) ?? 0); // XML_ID: "sale_basket_92"
	
// function to make unique sorted index of product user property enumeration
var enumSortIdx = (_enum) => _enum?.sort * 10000 + _enum?.id;

// prepare a handler to do function fn on press Enter
var doOnPressEnter = (fn) => function(e) { if (e.which == 13) fn(); };

// prepare a html link to bx24 portal
var makehtmlLink = (path, name, target = '_blank') =>
	'<a href="https://' + BX24.getDomain() + path + '" target=' + target + '>' + name + '</a>';
// prepare html link to deal
var htmlDealTitle = (deal) => makehtmlLink( '/crm/deal/details/'+deal?.ID+'/', deal?.TITLE );
// prepare html link to shipment
var htmlShipmentTitle = (shipment) => makehtmlLink( '/shop/documents/details/sales_order/'+shipment?.id+'/', shipment?.accountNumber );
// prepare html link to document
var htmlDocumentTitle = (document) => makehtmlLink( '/shop/documents/details/'+document?.id+'/', document?.title );
// prepare html link to product
var htmlProductTitle = (product) => makehtmlLink( '/crm/catalog/'+catalog.product.iblockId+'/product/'+product?.id+'/', product?.name );
// prepare html link to product offer
var htmlProductOfferTitle = (offer) => {
	let path = '/crm/catalog/' + catalog.product.iblockId + '/product/' + offer?.parentId?.value + '/variation/' + offer?.id + '/';
	let name = offer?.name + ' - ' + offerPropsStr(offer);
	return makehtmlLink(path, name);
}
// prepare html link to contact
var htmlContactTitle = (contact) => makehtmlLink( '/crm/contact/details/'+contact?.ID+'/', contact?.NAME );

// product & offers indexes
catalog.product.byId = [];
catalog.product.offer.byId = [];
// function to update offers index
catalog.product.offer._up = (offers) => {
	offers.forEach( (offer) => {
		offer._idx = catalog.product.offer.byId[offer.id]?._idx;
		catalog.product.offer.byId[offer.id] = offer;
		// product
		let product = catalog.product.byId[offer.parentId.value];
		if (product !== undefined) {
			if (product.type == 3) {
				if (offer._idx !== undefined) {
					product._offers[offer._idx] = offer;
				} else {
					offer._idx = product._offers.length;
					product._offers.push(offer);
				}
			} else {
				console.warn(offer.id, 'Wrong parent:', offer, product);
			}
		}
	});
};


console.info('loading...');
// let loadbar = createLoadbarElement($('#productsLoad'));

// fill dics
crm.status.list().then( (data) => {
	// console.dir(data);
	crm.status.byType = [];
	crm.status.dealStages = [];
	data.forEach( (v) => {
		if (crm.status.byType[v.ENTITY_ID] == undefined)
			crm.status.byType[v.ENTITY_ID] = [];
		crm.status.byType[v.ENTITY_ID][v.STATUS_ID] = v;
		if (v.ENTITY_ID.substr(0,10) == 'DEAL_STAGE')
			crm.status.dealStages[v.STATUS_ID] = v;
	});
	console.info('crm.status.byType:', crm.status.byType);
	console.info('crm.status.dealStages:', crm.status.dealStages);
	env.dics = true;
})
.catch(BX24ex.warnErrorCallback);

// fill stores
catalog.store.list().then( (data) => {
	// console.dir(data);
	catalog.store.byId = [];
	data.forEach( (store) => {
		catalog.store.byId[store.id] = store;
	});
	console.info('catalog.store.byId:', catalog.store.byId);
	env.stores = true;
})
.catch(BX24ex.warnErrorCallback);

// fill offer property dics
catalog.waitReady().then( async () => {
	let properties, propertyEnums;
	const propertiesFilter = {iblockId: catalog.product.offer.iblockId, "!=code": ["CML2_LINK","MORE_PHOTO"]};
	await Promise.all([
		// (async ()=>{ properties = await catalog.productProperty.list({filter: propertiesFilter}); })(),
		// (async ()=>{ propertyEnums = await catalog.productPropertyEnum.list({order:{sort:'ASC'}}); })(),
		catalog.productProperty.list( {filter: propertiesFilter} ).then(data => {properties = data}),
		catalog.productPropertyEnum.list( {order:{sort:'ASC'}} ).then(data => {propertyEnums = data}),
	]);
	catalog.product.userPropsById = [];
	properties.forEach( (property) => {
		property._enumsById = [];
		property._idByValue = [];
		property._values = [];
		property._sorted = [];
		catalog.product.userPropsById[property.id] = property;
	});
	propertyEnums.forEach( (propertyEnum) => {
		let property = catalog.product.userPropsById[propertyEnum.propertyId];
		if (property) {
			property._enumsById[propertyEnum.id] = propertyEnum;
			property._idByValue[propertyEnum.value] = propertyEnum.id;
			property._values[propertyEnum.id] = propertyEnum.value;
			property._sorted[enumSortIdx(propertyEnum)] = propertyEnum;
		}
	});
	console.info('catalog.product.userPropsById:', catalog.product.userPropsById);
	env.props = true;
})
.catch(BX24ex.warnErrorCallback);


$(document).ready(() => {
	// wait for ready of catalog and env
	catalog.waitReady()
	.then(() => wait_until(env_ready))
	.then(() => {
		console.info('BX24.getDomain:', BX24.getDomain());
		console.info('ready');

		$('#dealIdToShow').on('keypress', doOnPressEnter( showDealProducts ));
		$('#docIdToShow').on('keypress', doOnPressEnter( showDocProducts ));
		$('#productIdToFind').on('keypress', doOnPressEnter( findAllByProduct ));
		$('#productIdToShow').on('keypress', doOnPressEnter(showProductStruct ));

		$('fieldset legend').on('click', function() {
			$(this).parent().toggleClass('open');
		}).addClass('pointer');
		
		$('#dealsBox legend').on('click', function() {
			window.setTimeout(() => {
				if ($(this).parent().hasClass('open') && !crm.deal.byId?.length) {
					getAllToFindByProduct();
				}
			}, 1);
		});
		
		$('#productsBox legend').on('click', function() {
			window.setTimeout(() => {
				if ($(this).parent().hasClass('open') && !catalog.product.byId?.length) {
					getAllProducts();
				}
			}, 1);
		});
		
		$('#contactsBox legend').on('click', function() {
			window.setTimeout(() => {
				if ($(this).parent().hasClass('open')) {
					showContacts();
				}
			}, 1);
		});
		
		$('#productTable th.offers').on('click', function() {
			$(this).closest('table').toggleClass('product-type3');
		}).addClass('pointer');
		
		$('#productTable th.active').on('click', function() {
			let $table = $(this).closest('table');
			if ($table.hasClass('product-active')) $table.removeClass('product-active').addClass('product-inactive');
			else if ($table.hasClass('product-inactive')) $table.removeClass('product-inactive');
			else $table.addClass('product-active');
		}).addClass('pointer');

		$('#productTable th.quantity').on('click', function() {
			$(this).closest('table').toggleClass('product-offers');
		}).addClass('pointer');

		$('#priceInput').on('change', function() {
			fillPriceTable($(this).val());
		});
		
		doTest();
	})
	.catch((e) => {
		$('#sendRequest').addClass('open');
		BX24ex.warnErrorCallback(e);
	});
});


// =============================================================================
// show on console actions

// show deal object from index
function toinfoDealbyId(id) {
	console.info(id, 'deal:', crm.deal.byId?.[id]);
}

// show document object from index
function toinfoDocbyId(id) {
	console.info(id, 'document:', catalog.document.byId?.[id]);
}

// show product or offer object from index
function toinfoProductbyId(id) {
	if (catalog.product.byId[id]) {
		console.info(id, 'product:', catalog.product.byId[id]);
	}
	if (catalog.product.offer.byId[id]) {
		console.info(id, 'offer:', catalog.product.offer.byId[id]);
	}
}


// =============================================================================
// click actions

// action on click by deal id
function clickOnDealId() {
	let id = parseInt($(this).text());
	// toinfoDealbyId(id);
	$('#dealIdToShow').val(id);
	if ($('#dealBox').hasClass('open')) showDealProducts();
	$('#dealIdToCopyFrom').val(id);
}

// action on click by document id
function clickOnDocId() {
	let id = parseInt($(this).text());
	// toinfoDocbyId(id);
	$('#docIdToShow').val(id);
	if ($('#docBox').hasClass('open')) showDocProducts();
	$('#docIdToCopyTo').val(id);
}

// action on click by offer id
function clickOnOfferId() {
	let id = parseInt($(this).text());
	// toinfoProductbyId(id);
	$('#productIdToFind').val(id);
	if ($('#dealsBox').hasClass('open')) findAllByProduct();
}

// action on click by product id
function clickOnProductId() {
	let id = parseInt($(this).text());
	// toinfoProductbyId(id);
	$('#productIdToFind').val(id);
	if ($('#dealsBox').hasClass('open')) findAllByProduct();
	$('#productIdToShow').val(id);
	if ($('#productStruct').hasClass('open')) showProductStruct();
}


// =============================================================================
// Deal products
async function showDealProducts(dealId, $box) {
	if (dealId == undefined) dealId = $('#dealIdToShow').val();
	if ($box == undefined) $box = $('#dealProducts');
	
	$box.html(
		'<table class="common offers">'+
		'<thead>'+
		'<tr>'+
		'<th>ID</th>'+
		'<th class="name"></th>'+
		// '<th>row.ID</th>'+
		'<th title="QUANTITY">Q-ty</th>'+
		'<th title="RESERVE_QUANTITY">R-ve</th>'+
		'<th title="DATE_RESERVE_END">R_END</th>'+
		'<th title="RESERVE_ID">R_ID</th>'+
		'<th></th>'+
		'<th title="offer.quantity" class="quantity">O Q-ty</th>'+
		'<th title="offer.quantityReserved" class="reserved">O R-ve</th>'+
		'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'
	);
	
	let productrows;
	await Promise.all([
	
		crm.deal.get(dealId).then( (deal) => {
			$box.find('th.name').html(htmlDealTitle(deal) + ' <i>(' + printDealStage(deal) + ')</i>');
		}),
		
		crm.deal.productrows.get(dealId).then( async (data) => {
			productrows = data;
			// get offer data for each row
			let calls = productrows.map( (productrow) => [ 'catalog.product.offer.get', {id: productrow.PRODUCT_ID} ]);
			(await BX24ex.batchPromise(calls)).forEach(
				(data, i) => { productrows[i]._offer = data.offer; },
				BX24ex.warnErrorCallback
			);
			console.info('productrows:', productrows);
		}),
	]).catch(BX24ex.warnErrorCallback);
	
	// prepare total quantity of each product in the order
	let basket = [];
	productrows?.forEach( (r) => {
		basket[r.PRODUCT_ID] = r.QUANTITY + (basket[r.PRODUCT_ID] ?? 0);
	});
	
	// fill body
	let htmlBody = productrows?.length? '' : '[EMPTY]';
	productrows?.forEach( (productrow, i) => {
		// let offer = catalog.product.offer.byId[productrow.PRODUCT_ID];
		let offer = productrow._offer;
		let availableQuan = offer?.quantity + offer?.quantityReserved;
		let isRed = productrow.RESERVE_ID && basket[productrow.PRODUCT_ID] > availableQuan;
		htmlBody += '<tr>'+
			'<td class=offer-id>'+offer?.id+'</td>'+
			'<td>'+(offer? htmlProductOfferTitle(offer) : productrow.PRODUCT_NAME)+'</td>'+
			// '<td>'+productrow.ID+'</td>'+
			'<td style="'+(isRed?'color:red':'')+'">'+productrow.QUANTITY+'</td>'+
			'<td>'+productrow.RESERVE_QUANTITY+'</td>'+
			'<td>'+productrow.DATE_RESERVE_END+'</td>'+
			'<td>'+productrow.RESERVE_ID+'</td>'+
			'<td></td>'+
			'<td>'+offer?.quantity+'</td>'+
			'<td>'+offer?.quantityReserved+'</td>'+
			'</tr>';
	});
	$box.find('tbody').html(htmlBody);
	
	// set actions
	$box.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
}


// =============================================================================
// Doc products
async function showDocProducts(docId, $box) {
	if (docId == undefined) docId = $('#docIdToShow').val();
	if ($box == undefined) $box = $('#docProducts');
	
	$box.html(
		'<table class="common offers">'+
		'<thead>'+
		'<tr>'+
		'<th>ID</th>'+
		'<th class="name"></th>'+
		'<th class="full-view">e.id</th>'+
		'<th class="full-view">productId</th>'+
		'<th title="amount">Q-ty</th>'+
		'<th title="storeFrom">From</th>'+
		'<th title="storeTo">To</th>'+
		'<th></th>'+
		'<th title="offer.quantity" class="quantity">O Q-ty</th>'+
		'<th title="offer.quantityReserved" class="reserved">O R-ve</th>'+
		'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'
	);
	
	let elements;
	await Promise.all([
	
		catalog.document.get(docId).then( (doc) => {
			$box.find('th.name').html(htmlDocumentTitle(doc) + ' <i>(' + printDocStatus(doc) + ')</i>');
		}),
		
		catalog.document.element.listByDoc(docId).then( async (data) => {
			elements = data;
			// get offer data for each row
			let calls = elements.map( (element) => [ 'catalog.product.offer.get', {id: element.elementId} ]);
			(await BX24ex.batchPromise(calls)).forEach(
				(data, i) => { elements[i]._offer = data.offer; },
				BX24ex.warnErrorCallback
			);
			console.info('elements:', elements);
		}),
	]).catch(BX24ex.warnErrorCallback);
	
	// prepare total quantity of each product in the document
	let basket = [];
	// elements?.forEach( (r) => {
		// basket[r.PRODUCT_ID] = r.QUANTITY + (basket[r.PRODUCT_ID] ?? 0);
	// });
	
	// fill body
	let htmlBody = elements?.length? '' : '[EMPTY]';
	elements?.forEach( (element, i) => {
		let offer = element._offer;
		htmlBody += '<tr>'+
			'<td class=offer-id>'+offer?.id+'</td>'+
			'<td>'+(offer? htmlProductOfferTitle(offer) : element.elementId)+'</td>'+
			'<td class="full-view">'+element.id+'</td>'+
			'<td class="full-view">'+element.elementId+'</td>'+
			'<td>'+element.amount+'</td>'+
			'<td>'+printStoreTitle(element.storeFrom)+'</td>'+
			'<td>'+printStoreTitle(element.storeTo)+'</td>'+
			'<td></td>'+
			'<td>'+offer?.quantity+'</td>'+
			'<td>'+offer?.quantityReserved+'</td>'+
			'</tr>';
	});
	$box.find('tbody').html(htmlBody);
	
	// set actions
	$box.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
}


// =============================================================================
// Compare deal productrows and document elements
async function compareDealToDoc(dealId, docId, $box) {
	if (dealId == undefined) dealId = $('#dealIdToCopyFrom').val();
	if (docId == undefined) docId = $('#docIdToCopyTo').val();
	if ($box == undefined) $box = $('#docFromDeal');
	
	let $table = $box.find('div.products-table');
	$table.html(
		'<table class="common offers">'+
		'<thead>'+
		'<tr>'+
		'<th>ID</th>'+
		'<th class="name"></th>'+
		'<th></th>'+
		'<th title="Deal QUANTITY">Сделка</th>'+
		'<th title="Document amount">Приход</th>'+
		// '<th></th>'+
		// '<th title="offer.quantity" class="quantity">Склад</th>'+
		// '<th title="offer.quantityReserved" class="reserved">Резерв</th>'+
		'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'
	);
	
	let productrows, elements;
	await Promise.all([
	
		crm.deal.get(dealId).then( (deal) => {
			$box.find('span.deal-name').html(htmlDealTitle(deal));
		}),
	
		catalog.document.get(docId).then( (doc) => {
			$box.find('span.doc-name').html(htmlDocumentTitle(doc));
		}),
		
		crm.deal.productrows.get(dealId).then( (data) => { productrows = data; } ),
		
		catalog.document.element.listByDoc(docId).then( (data) => { elements = data; } ),
		
	]).catch(BX24ex.warnErrorCallback);

	// prepare comparing rows per product
	let items = new ItemsArray( (id) => { return {id:id, dealQ:0, docQ:0} } );
	// calc quantity per product in the deal
	productrows.forEach( (p) => items._up(p.PRODUCT_ID, (r) => r.dealQ += p.QUANTITY) );
	// calc quantity per product in the document
	elements.forEach( (e) => items._up(e.elementId, (r) => r.docQ += e.amount) );

	// sort items by diff desc
	let _sortIdx = (r) => (r.dealQ > r.docQ)? 2 : (r.dealQ < r.docQ)? 1 : 0;
	items.sort( (a,b) => (_sortIdx(a) < _sortIdx(b))? 1 : -1 );
	
	// get offer data for each row
	let calls = items.map( (r) => [ 'catalog.product.offer.get', {id: r.id} ] );
	(await BX24ex.batchPromise(calls)).forEach(
		(data, i) => { items[i]._offer = data.offer; },
		BX24ex.warnErrorCallback
	);
	console.info('items:', items);

	// fill products table body
	let htmlBody = items.length? '' : '[EMPTY]';
	items.forEach( (r, i) => {
		let offer = r._offer;
		// let availableQuan = offer?.quantity + offer?.quantityReserved;
		htmlBody += '<tr>' +
			'<td class=offer-id>' + offer?.id + '</td>' +
			'<td>' + (offer? htmlProductOfferTitle(offer) : i) + '</td>' +
			'<td></td>' +
			'<td style="' + (r.dealQ > r.docQ ? 'color:red' : '') + '">' + r.dealQ + '</td>' +
			'<td style="' + (r.dealQ < r.docQ ? 'color:blue' : '') + '">' + r.docQ + '</td>' +
			// '<td></td>' +
			// '<td>' + offer?.quantity + '</td>' +
			// '<td>' + offer?.quantityReserved + '</td>' +
			'</tr>';
	});
	$table.find('tbody').html(htmlBody);
	
	// set actions
	$table.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
}


// =============================================================================
// Copy deal products to document
async function copyProductsToDoc(dealId, docId, isDocCleanup, $box) {
	if (dealId == undefined) dealId = $('#dealIdToCopyFrom').val();
	if (docId == undefined) docId = $('#docIdToCopyTo').val();
	if ($box == undefined) $box = $('#docFromDeal');
	if (isDocCleanup == undefined) isDocCleanup = $box.find('input[name="do-doc-cleanup"]:checked').length? true : false;
	
	let $table = $box.find('div.products-table');
	$table.html(
		'<table class="common offers">'+
		'<thead>'+
		'<tr>'+
		'<th>ID</th>'+
		'<th class="name"></th>'+
		'<th title="offer.quantity" class="quantity">Q.</th>'+
		'<th title="offer.quantityReserved" class="reserved">R.</th>'+
		'<th></th>'+
		'<th>row.ID</th>'+
		'<th title="QUANTITY">Q.</th>'+
		'<th title="RESERVE_QUANTITY">R.</th>'+
		'<th title="DATE_RESERVE_END">R_END</th>'+
		'<th title="RESERVE_ID">R_ID</th>'+
		'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'
	);
	
	if (isDocCleanup) {
		let docProducts = await catalog.document.element.listByDoc(docId);
		if (docProducts.length) {
			let calls = docProducts.map( (element) => [ 'catalog.document.element.delete', {id: element.id} ]);
			(await BX24ex.batchPromise(calls)).forEach(
				(data, i) => { docProducts[i]._delete = data; },
				BX24ex.warnErrorCallback
			);
			console.info('Deleted docProducts:', docProducts);
		} else {
			console.info('Document already empty.');
		}
	}
	
	let productrows;
	await Promise.all([
	
		crm.deal.get(dealId).then( (deal) => {
			$box.find('span.deal-name').html(htmlDealTitle(deal));
		}),
	
		catalog.document.get(docId).then( (doc) => {
			$box.find('span.doc-name').html(htmlDocumentTitle(doc));
		}),
		
		crm.deal.productrows.get(dealId).then( async (data) => {
			productrows = data;
			// copy data to doc for each row
			let calls = productrows.map( (productrow) => [ 'catalog.document.element.add', {
				fields: {
					docId: docId,
					storeFrom: 0,
					storeTo: 1, // TODO store chose
					elementId: productrow.PRODUCT_ID,
					amount: productrow.QUANTITY,
					purchasingPrice: 0
				}
			}]);
			(await BX24ex.batchPromise(calls)).forEach(
				(data, i) => { productrows[i]._copy = data; },
				BX24ex.warnErrorCallback
			);
			console.info('productrows:', productrows);
		}),
	]);
	
	// get offer data for each row
	let calls = productrows.map( (productrow) => [ 'catalog.product.offer.get', {id: productrow.PRODUCT_ID} ]);
	(await BX24ex.batchPromise(calls)).forEach(
		(data, i) => { productrows[i]._offer = data.offer; },
		BX24ex.warnErrorCallback
	);
	console.info('productrows:', productrows);
	
	// fill products table body
	let htmlBody = productrows.length? '' : '[EMPTY]';
	productrows.forEach( (productrow, i) => {
		// let offer = catalog.product.offer.byId[productrow.PRODUCT_ID];
		let offer = productrow._offer;
		let availableQuan = offer?.quantity + offer?.quantityReserved;
		let isRed = productrow.RESERVE_ID && productrow.RESERVE_QUANTITY > availableQuan;
		htmlBody += '<tr>'+
			'<td class=offer-id>'+offer?.id+'</td>'+
			'<td>'+(offer? htmlProductOfferTitle(offer) : productrow.PRODUCT_NAME)+'</td>'+
			'<td>'+offer?.quantity+'</td>'+
			'<td>'+offer?.quantityReserved+'</td>'+
			'<td></td>'+
			'<td>'+productrow.ID+'</td>'+
			'<td style="'+(isRed?'color:red':'')+'">'+productrow.QUANTITY+'</td>'+
			'<td>'+productrow.RESERVE_QUANTITY+'</td>'+
			'<td>'+productrow.DATE_RESERVE_END+'</td>'+
			'<td>'+productrow.RESERVE_ID+'</td>'+
			'</tr>';
	});
	$table.find('tbody').html(htmlBody);
	
	// set actions
	$table.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
}


// =============================================================================
// Find all by productId
function findAllByProduct() {
	let productId = $('#productIdToFind').val();
	let isFull = $('#dealsBox input[name="show-closed-deals"]:checked').length? true : false;
	// console.log('isFull:', isFull);
	findDealsByProduct(productId, isFull, true, false);
	// findOrdersByProduct(productId, true, true);
	findDocsByProduct(productId, isFull, true, false);
}

// Get all to find by productId
async function getAllToFindByProduct() {
	$('#linkingDxDoutput').html('');
	
	let orderentities;
	await Promise.all([
		getAllDeals(),
		getAllOrders(),
		getAllDocuments(),
		crm.orderentity.list().then(data => {orderentities = data}),
	]);
	
	// get orderentity (connection between orders and deals)
	// console.dir(orderentities);
	orderentities.forEach( (orderentity) => {
		let order = sale.order.byId[orderentity.orderId];
		if (order !== undefined) {
			order._ownerId = orderentity.ownerId;
			let deal = crm.deal.byId[orderentity.ownerId];
			if (deal !== undefined) deal._order = order; // can be hidden (non public) deals
		}
	});
	
	// make connection between productrows and basketItems
	crm.deal.byId.forEach( (deal) => {
		deal._productrows.forEach( (productrow) => {
			productrow._basketitem = sale.basketItem.byId[getRowBasketId(productrow)];
			// if (productrow._basketitem == undefined)
				// // console.warn('Basketitem is not found for Deal:', deal, productrow);
				// console.info('(!) _basketitem is undefined for Deal:', deal, productrow);
		});
	});
	
	// check deals
	console.info('checking deals...');
	let dealTableRows = [];
	crm.deal.byId.forEach( (deal) => {
		// check deal type
		if (!isDealTypeByCategory(deal)) console.warn('Wrong type in deal:', deal);
		// check deal order
		if (deal._productrows.length && !dealsExcludedFromCheck[deal.ID]) {
			// shipments and orders for deals in WON status
			if (deal.STAGE_ID.substr(-3) == 'WON') {
				if (deal._order === undefined)
					console.warn('Order is not found for WON Deal:', deal);
				else if (deal._order._shipment === undefined)
					console.warn('Shipment is not found for WON Deal:', deal);
				else {
					// compare productrows, basket and shipmentitems
					if (deal._productrows.length != deal._order._basket.length)
						console.warn('Order is wrong for WON Deal:', deal, deal._productrows.length, '!=', deal._order._basket.length);
					else
						deal._productrows.forEach( (productrow) => {
							let basketItem = productrow._basketitem;
							if (productrow.QUANTITY != basketItem.quantity || productrow.PRODUCT_ID != basketItem.productId)
								console.warn('Basket is wrong for WON Deal:', deal, productrow, basketItem);
							else if (basketItem._shipmentitem === undefined || basketItem.quantity != basketItem._shipmentitem.quantity)
								console.warn('Shipment is wrong for WON Deal:', deal, basketItem);
						});
				}
			}
			// reserve
			// if (stagesDealCheckReserve[deal.STAGE_ID]) {
			if (deal.CLOSED != 'Y') {
				deal._productrows.forEach( (productrow) => {
					if (productrow.RESERVE_QUANTITY) {
						if (productrow.DATE_RESERVE_END && productrow.RESERVE_ID) {
							let diff = Date.parseDMY(productrow.DATE_RESERVE_END).diffDays(Date.now());
							// console.log(deal.ID, productrow.DATE_RESERVE_END, diff);
							if (diff < 15) {
								dealTableRows.push({deal: deal, productrow: productrow});
							}
						} else {
							console.warn(deal.ID, productrow.PRODUCT_ID, productrow.PRODUCT_NAME, productrow.DATE_RESERVE_END, productrow.RESERVE_ID);
							dealTableRows.push({deal: deal, productrow: productrow});
						}
					}
				});
			}
		}
	});
	drawDealTable(dealTableRows);
	fillProductTableOrdoc();
	console.info('done');
}

// ====================================
// Get all deals with productrows
async function getAllDeals() {
	crm.deal.byId = [];
	crm.deal.byRowId = [];
	
	let deals = await crm.deal.list();
	// console.info('deals:', deals);
	let progressbar = createProgressbarElement($('#dealsLoad'), deals.length);
	
	let calls = [];
	deals.forEach( (deal) => {
		crm.deal.byId[deal.ID] = deal;
		// calls[deal.ID] = [ 'crm.item.productrow.list', {filter: {'=ownerType': 'D', '=ownerId': deal.ID}} ];
		calls[deal.ID] = [ 'crm.deal.productrows.get', {id: deal.ID} ];
	});
	
	await Promise.all( BX24ex.splitPerParts(calls).map( (batch) =>
		new Promise( (resolve, reject) => {
			BX24.callBatch(
				batch,
				(results) => {
					// console.info('results:', results);
					results.forEach( (result, id) => {
						if (result.error()) {
							console.warn(id, result.error(), result.error_description());
						} else {
							// let productrows = result.data()?.productRows;
							let productrows = result.data();
							// console.info(id, productrows);
							let deal = crm.deal.byId[id];
							deal._productrows = productrows;
							productrows.forEach( (row) => {
								crm.deal.byRowId[row.ID] = deal;
							});
							// update progressbar
							progressbar.incNumber();
						}
					});
					resolve();
				}
			);
		})
	));
	
	// log
	console.info('crm.deal.byId:', crm.deal.byId);
	
	// info
	let inwork = crm.deal.byId.reduce( (ac, deal) => ac + ((deal.OPENED == 'Y' && deal.CLOSED != 'Y')? 1: 0), 0 );
	$('#dealsInfo').html('(в работе: '+inwork+')');
}

function findDealsByProduct(productId, isFull, isDraw, isInfo) {
	if (crm.deal.byId == undefined) return;
	let dealTableRows = [];
	// case product or offer
	if (catalog.product.byId != undefined && catalog.product.byId[productId] != undefined) {
		let offers = catalog.product.byId[productId]._offers;
		// we should find over all offers of the product
		crm.deal.byId.forEach( (deal) => {
			if (isFull || deal.CLOSED != 'Y') {
				deal._productrows.forEach( (productrow) => {
					offers.forEach( (offer) => {
						if (productrow.PRODUCT_ID == offer.id) {
							// product exists in deal
							dealTableRows.push({deal: deal, productrow: productrow});
							if (isInfo) console.info(deal.ID, offer.id, deal);
						}
					});
				});
			}
		});
	} else {
		// loop over all deals
		crm.deal.byId.forEach( (deal) => {
			if (isFull || deal.CLOSED != 'Y') {
				deal._productrows.forEach( (productrow) => {
					if (productrow.PRODUCT_ID == productId) {
						// product exists in deal
						dealTableRows.push({deal: deal, productrow: productrow});
						if (isInfo) console.info(deal.ID, deal);
					}
				});
			}
		});
	}
	// update table
	if (isDraw) drawDealTable(dealTableRows);
	return dealTableRows;
}

function drawDealTable(dealTableRows) {
	// deals table
	let $tbody = $('#dealTable tbody').html('');
	// loop over all rows
	dealTableRows.forEach( (dealTableRow) => {
		let deal = dealTableRow.deal;
		let productrow = dealTableRow.productrow;
		let order = deal._order;
		let id = deal.ID;
		let html = '<tr id=fd-'+id+'>'+
			'<td class=deal-id>'+id+'</td>'+
			'<td>'+htmlDealTitle(deal)+'</td>'+
			'<td class=productrows>'+deal._productrows.length+'</td>'+
			'<td>'+printDealStage(deal)+'</td>'+
			'<td></td>'+
			'<td class="full-view">'+productrow.ID+'</td>'+
			'<td class="full-view">'+productrow.PRODUCT_ID+'</td>'+
			'<td>'+productrow.QUANTITY+'</td>'+
			'<td>'+productrow.RESERVE_QUANTITY+'</td>'+
			'<td>'+(deal.CLOSED!='Y'? checkDateDiff(productrow.DATE_RESERVE_END, 1) : productrow.DATE_RESERVE_END)+'</td>'+
			'<td>'+(productrow.RESERVE_ID ?? (productrow.RESERVE_ID+(deal.CLOSED!='Y'? ' '+warnSimbol() : '')))+'</td>'+
			'<td></td>'+
			'<td class="full-view">'+order?.id+'</td>'+
			'<td>'+(order?._shipment? htmlShipmentTitle(order._shipment) : '')+'</td>'+
			'<td>'+order?.dateUpdate+'</td>'+
			'</tr>';
		// update table
		$tbody.prepend(html);
	});
	// set actions
	$tbody.find('td.deal-id').on('click', clickOnDealId).addClass('pointer');
}

// ====================================
// Get all sale orders and shipments with items
async function getAllOrders() {
	$('#orderTable tbody').html('');
	let orders, basketItems, shipments, shipmentitems;
	await Promise.all([
		sale.order.list().then(data => {orders = data}),
		sale.basketItem.list().then(data => {basketItems = data}),
		sale.shipment.list().then(data => {shipments = data}),
		sale.shipmentitem.list().then(data => {shipmentitems = data}),
	]);
	let progressbar = createProgressbarElement($('#ordersLoad'), orders.length);
	
	// console.dir(orders);
	sale.order.byId = [];
	orders.forEach( (order) => {
		order._basket = [];
		sale.order.byId[order.id] = order;
		// update progressbar
		progressbar.incNumber();
	});
	
	// get basketItems
	// console.dir(basketItems);
	sale.basketItem.byId = [];
	basketItems.forEach( (basketItem) => {
		let id = basketItem.orderId;
		sale.order.byId[id]._basket.push(basketItem);
		sale.basketItem.byId[basketItem.id] = basketItem;
	});
	
	// get shipments
	// console.dir(shipments);
	sale.shipment.byId = [];
	shipments.forEach( (shipment) => {
		let id = shipment.orderId;
		shipment._items = [];
		sale.order.byId[id]._shipment = shipment;
		sale.shipment.byId[shipment.id] = shipment;
	});
	
	// get shipmentitems
	// console.dir(shipmentitems);
	sale.shipmentitem.byId = [];
	shipmentitems.forEach( (shipmentitem) => {
		sale.shipmentitem.byId[shipmentitem.id] = shipmentitem;
		if (sale.shipment.byId[shipmentitem.orderDeliveryId]) {
			sale.shipment.byId[shipmentitem.orderDeliveryId]._items.push(shipmentitem);
			// assign to basket only this items
			if (sale.basketItem.byId[shipmentitem.basketId])
				if (sale.basketItem.byId[shipmentitem.basketId]._shipmentitem == undefined)
					sale.basketItem.byId[shipmentitem.basketId]._shipmentitem = shipmentitem;
				else
					console.warn('There are a double shipmentitem for', sale.basketItem.byId[shipmentitem.basketId], shipmentitem);
			else
				console.warn('There are no basketItem for', shipmentitem);
		}
		// else
			// console.warn('There are no shipment for', shipmentitem); to many somehow
	});
	
	// log
	console.info('sale.order.byId:', sale.order.byId);
}

function findOrdersByProduct(productId, isDraw, isInfo) {
	if (sale.order.byId == undefined) return;
	let orderTableRows = [];
	// case product or offer
	if (catalog.product.byId != undefined && catalog.product.byId[productId] != undefined) {
		let offers = catalog.product.byId[productId]._offers;
		// we should find over all offers of the product
		sale.order.byId.forEach( (order) => {
			order._basket.forEach( (basketItem) => {
				offers.forEach( (offer) => {
					if (basketItem.productId == offer.id) {
						// product exists in order
						orderTableRows.push({order: order, basketItem: basketItem});
						if (isInfo) console.info(order.id, offer.id, order);
					}
				});
			});
		});
	} else {
		// loop over all orders
		sale.order.byId.forEach( (order) => {
			order._basket.forEach( (basketItem) => {
				if (basketItem.productId == productId) {
					// product exists in order
					orderTableRows.push({order: order, basketItem: basketItem});
					if (isInfo) console.info(order.id, order);
				}
			});
		});
	}
	// update table
	if (isDraw) drawOrderTable(orderTableRows);
	return orderTableRows;
}

function drawOrderTable(orderTableRows) {
	// orders table
	let $tbody = $('#orderTable tbody').html('');
	// loop over all rows
	orderTableRows.forEach( (orderTableRow) => {
		let order = orderTableRow.order;
		let basketItem = orderTableRow.basketItem;
		let id = order.id;
		let html = '<tr id=fso-'+id+'>'+
			'<td>'+id+'</td>'+
			'<td>'+(order._shipment? htmlShipmentTitle(order._shipment) : '')+'</td>'+
			'<td>'+order.dateUpdate+'</td>'+
			'<td>'+order._basket.length+'</td>'+
			'<td></td>'+
			'<td>'+basketItem.id+'</td>'+
			'<td>'+basketItem.productId+'</td>'+
			'<td>'+parseInt(basketItem.quantity)+'</td>'+
			'<td>'+basketItem.xmlId+'</td>'+
			'<td></td>'+
			'<td>'+htmlDealTitle(getDealByBasketItemXml(basketItem.xmlId))+'</td>'+
			'</tr>';
		// update table
		$tbody.prepend(html);
	});
}

function getDealByBasketItemXml(xmlId) {
	return crm.deal.byRowId?.[ parseInt(xmlId.substring(7)) ];
}

// ====================================
// Get all catalog documents with elements
async function getAllDocuments() {
	$('#docTable tbody').html('');
	let data = await catalog.document.list();
	// console.dir(data);
	let progressbar = createProgressbarElement($('#documentsLoad'), data.length);
	
	catalog.document.byId = [];
	let calls = [];
	data.forEach( (document) => {
		let id = document.id;
		catalog.document.byId[id] = document;
		calls[id] = [ 'catalog.document.element.list', catalog.document.element._listByDoc(id) ];
	});
	
	while (calls.length) {
		let results = await BX24ex.batchPromise(calls);
		// console.log('results:', results);
		
		calls = [];
		results.forEach( (data, id, result) => {
			// console.info(id, data.documentElements);
			let elements = catalog.document.byId[id]._elements ?? [];
			catalog.document.byId[id]._elements = elements.concat(data.documentElements);
			// next
			if (result.more()) {
				let start = result.answer.next;
				calls[id] = [ 'catalog.document.element.list', catalog.document.element._listByDoc(id, start) ];
			} else {
				// update progressbar
				progressbar.incNumber();
			}
		});
	}
	
	// fill userfields
	let _fillUserfields = (document) => {
		let _document = catalog.document.byId[document.documentId];
		// _document.field620 = document.field620;
		if (_document !== undefined) {
			for (key in document) {
				if (key != 'documentId' && key != 'documentType') {
					_document[key] = document[key];
				}
			}
		}
	};
	let userfields = await catalog.userfield.document.listByType('S');
	console.info('document userfields type S:', userfields);
	userfields.forEach(_fillUserfields);
	userfields = await catalog.userfield.document.listByType('A');
	console.info('document userfields type A:', userfields);
	userfields.forEach(_fillUserfields);
	
	// log
	console.info('catalog.document.byId:', catalog.document.byId);
}

function isDocDealClosed(document, value) {
	let deal = crm.deal.byId[getDocLink(document)];
	return deal !== undefined && deal.CLOSED === value;
}

function findDocsByProduct(productId, isFull, isDraw, isInfo) {
	if (catalog.document.byId == undefined) return;
	let docTableRows = [];
	// case product or offer
	let isProductView = false;
	if (catalog.product.byId != undefined && catalog.product.byId[productId] != undefined) {
		isProductView = true;
		let offers = catalog.product.byId[productId]._offers;
		// we should find over all offers of the product
		catalog.document.byId.forEach( (document) => {
			if (isFull || !isDocDealClosed(document, 'Y')) {
				document._elements.forEach( (element) => {
					offers.forEach( (offer) => {
						if (element.elementId == offer.id) {
							// product exists in document
							docTableRows.push({document: document, element: element});
							if (isInfo) console.info(document.id, offer.id, document);
						}
					});
				});
			}
		});
	} else {
		// loop over all documents
		catalog.document.byId.forEach( (document) => {
			if (isFull || !isDocDealClosed(document, 'Y')) {
				document._elements.forEach( (element) => {
					if (element.elementId == productId) {
						// product exists in document
						docTableRows.push({document: document, element: element});
						if (isInfo) console.info(document.id, document);
					}
				});
			}
		});
	}
	// update table
	if (isDraw) drawDocTable(docTableRows, isProductView);
	return docTableRows;
}

function drawDocTable(docTableRows, isProductView) {
	// documents table
	let $tbody = $('#docTable tbody').html('');
	// loop over all rows
	docTableRows.forEach( (docTableRow) => {
		let document = docTableRow.document;
		let element = docTableRow.element;
		let html = '<tr id=fde-'+document.id+'>'+
			'<td class="doc-id">'+document.id+'</td>'+
			'<td>'+htmlDocumentTitle(document)+'</td>'+
			'<td>'+document._elements.length+'</td>'+
			'<td>'+printDocStatus(document)+'</td>'+
			'<td></td>'+
			'<td class="full-view">'+element.id+'</td>'+
			'<td class="full-view">'+element.elementId+'</td>'+
			'<td>'+element.amount+'</td>'+
			'<td>'+printStoreTitle(element.storeFrom)+'</td>'+
			'<td>'+printStoreTitle(element.storeTo)+'</td>'+
			(isProductView?
				'<td></td>'+
				'<td>'+htmlProductOfferTitle(catalog.product.offer.byId?.[element.elementId])+'</td>'+
				'<td class="offer-id">'+element.elementId+'</td>'
				: '')+
			'</tr>';
		// update table
		$tbody.prepend(html);
	});
	// set actions
	$tbody.find('td.doc-id').on('click', clickOnDocId).addClass('pointer');
	$tbody.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
}

// ====================================
// Show products reserve
async function showReserve() {
	let $box = $('#reserveOutput');
	
	// insert close button
	let $closeBtn = $box.parent().find('button.close');
	if (!$closeBtn.length) $closeBtn = $('<button class="close">-</button>');
	$box.before($closeBtn.on('click', function() {
		$(this).remove();
		$box.html('');
	}));
	
	$box.html(
		'<table class="common reserve-items">'+
		'<thead>'+
		'<tr>'+
		'<th class="product-id">ID</th>'+
		'<th class="name title">Товар</th>'+
		'<th class="quantity">Ожидание</th>'+
		'<th></th>'+
		'<th title="offer.quantity" class="quantity">Склад</th>'+
		'<th title="offer.quantityReserved" class="reserved">Резерв</th>'+
		'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'
	);
	
	// let items = [];
	// let _up_item = (id, fn) => {
		// let r = items[id] ?? {id:id, reserve:0};
		// fn(r);
		// items[id] = r;
	// };
	let items = new ItemsArray( (id) => { return {id:id, reserve:0} } );
	// reserve
	crm.deal.byId.forEach( (deal) => {
		if (isDealCheckReserve(deal)) {
			// calc reserve quantity per product
			deal._productrows.forEach( (productrow) => {
				items._up(productrow.PRODUCT_ID, (r) => r.reserve += productrow.QUANTITY);
			});
		}
	});
	
	// get offer data for each row
	let calls = items.map( (r) => [ 'catalog.product.offer.get', {id: r.id} ] );
	(await BX24ex.batchPromise(calls)).forEach(
		(data, i) => {
			let r = items[i];
			r._offer = data.offer;
			r.o_quantity = r._offer?.quantity ?? 0;
			r.o_reserve = r._offer?.quantityReserved ?? 0;
		},
		BX24ex.warnErrorCallback
	);
	console.info('items:', items);

	// fill products table body
	let htmlBody = items.length? '' : '[EMPTY]';
	items.forEach( (r, i) => {
		let offer = r._offer;
		htmlBody += '<tr>' +
			'<td class=offer-id>' + offer?.id + '</td>' +
			'<td>' + (offer? htmlProductOfferTitle(offer) : i) + '</td>' +
			'<td>' + r.reserve + '</td>' +
			'<td></td>' +
			'<td>' + r.o_quantity + '</td>' +
			'<td>' + r.o_reserve + '</td>' +
			'</tr>';
	});
	$box.find('tbody').html(htmlBody);
	
	// set actions
	$box.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
}

// ====================================
// Review products quantity by orders, shipments and documents
async function reviewStock() {
	let $box = $('#reviewOutput');
	
	// insert close button
	let $closeBtn = $box.parent().find('button.close');
	if (!$closeBtn.length) $closeBtn = $('<button class="close">-</button>');
	$box.before($closeBtn.on('click', function() {
		$(this).remove();
		$box.html('');
	}));
	
	$box.html(
		'<table class="common review-items">'+
		'<thead>'+
		'<tr>'+
		'<th class="product-id">ID</th>'+
		'<th class="name title">Товар</th>'+
		'<th class="shipment-q">Отгруж.</th>'+
		'<th class="doc-q">Загруж.</th>'+
		'<th class="calc-q">Склад</th>'+
		'<th class="reserve-q">Резерв</th>'+
		'<th></th>'+
		'<th title="offer.quantity" class="quantity">Склад</th>'+
		'<th title="offer.quantityReserved" class="reserved">Резерв</th>'+
		'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'
	);
	
	// let items = [];
	// let _up_item = (id, fn) => {
		// let r = items[id] ?? {id:id, shipments:0, reserve:0, stock:0};
		// fn(r);
		// items[id] = r;
	// };
	let items = new ItemsArray( (id) => { return {id:id, shipments:0, reserve:0, stock:0} } );
	// reserve & shipments
	crm.deal.byId.forEach( (deal) => {
		let order = deal._order;
		if (order?._basket?.length) {
			// deal has not empty basket
			if (order._shipment !== undefined) {
				if (order._shipment.canceled == "N") {
					// calc quantity per product in shipments
					order._basket.forEach( (_item) => items._up(_item.productId, (r) => r.shipments += _item.quantity) );
				} else {
					console.info('Shipment canceled:', order._shipment);
				}
			} else {
				// calc reserve quantity per product
				deal._productrows.forEach( (productrow) => {
					items._up(productrow.PRODUCT_ID, (r) => r.reserve += productrow.RESERVE_ID? productrow.RESERVE_QUANTITY : 0);
				});
			}
		}
	});
	// documents
	catalog.document.byId.forEach( (document) => {
		if (document.status == 'Y') {
			// calc stock quantity per product
			document._elements?.forEach( (e) => items._up(e.elementId, (r) => r.stock += e.amount) );
		}
	});
	
	// get offer data for each row
	let calls = items.map( (r) => [ 'catalog.product.offer.get', {id: r.id} ]);
	(await BX24ex.batchPromise(calls)).forEach(
		(data, i) => {
			let r = items[i];
			r._offer = data.offer;
			r.o_quantity = r._offer?.quantity ?? 0;
			r.o_reserve = r._offer?.quantityReserved ?? 0;
			r.quantity = r.stock - r.shipments - r.reserve;
		},
		BX24ex.warnErrorCallback
	);
	console.info('items:', items);

	// sort items by diff desc
	let _sortIdx = (r) => (r.reserve != r.o_reserve)? 2 : (r.quantity != r.o_quantity)? 1 : 0;
	items.sort( (a,b) => _sortIdx(b) - _sortIdx(a) );

	// fill products table body
	let htmlBody = items.length? '' : '[EMPTY]';
	items.forEach( (r, i) => {
		let offer = r._offer;
		htmlBody += '<tr>' +
			'<td class=offer-id>' + offer?.id + '</td>' +
			'<td>' + (offer? htmlProductOfferTitle(offer) : i) + '</td>' +
			'<td>' + r.shipments + '</td>' +
			'<td>' + r.stock + '</td>' +
			'<td style="' + (r.quantity != r.o_quantity ? 'color:red' : '') + '">' + r.quantity + '</td>' +
			'<td style="' + (r.reserve != r.o_reserve ? 'color:red' : '') + '">' + r.reserve + '</td>' +
			'<td></td>' +
			'<td>' + r.o_quantity + '</td>' +
			'<td>' + r.o_reserve + '</td>' +
			'</tr>';
	});
	$box.find('tbody').html(htmlBody);
	
	// set actions
	$box.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
}

// ====================================
// Find links Document -> Deal
function linkingDocsWithDeals() {
	let isShowTestDD = $('#show-test-doc-with-deals:checked').length? true : false;
	let $box = $('#linkingDxDoutput');
	
	// insert close button
	let $closeBtn = $box.parent().find('button.close');
	if (!$closeBtn.length) $closeBtn = $('<button class="close">-</button>');
	$box.before($closeBtn.on('click', function() {
		$(this).remove();
		$box.html('');
	}));
	
	$box.html(
		'<table class="common doc-links">'+
		'<thead>'+
		'<tr>'+
		'<th class="doc-id">ID</th>'+
		'<th class="name doc-title">Склад</th>'+
		'<th title="elements" class="elements">Строк</th>'+
		'<th class="doc-status">Статус</th>'+
		'<th></th>'+
		'<th class="deal-id">ID</th>'+
		'<th class="name deal-title">Сделки</th>'+
		'<th title="items" class="items">Строк</th>'+
		'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'
	);
	
	// basket index functions
	let _stringify_item = (a,v,i) => a + '{' + i + ':' + v + '}';
	let _docBIndex = (document) => {
		let items = [];
		document._elements?.forEach( (e) => {
			items[e.elementId] = e.amount + (items[e.elementId] ?? 0);
		});
		return items.reduce( _stringify_item, '' );
	};
	let _dealBIndex = (deal) => {
		let items = [];
		deal._productrows?.forEach( (r) => {
			items[r.PRODUCT_ID] = r.QUANTITY + (items[r.PRODUCT_ID] ?? 0);
		});
		return items.reduce( _stringify_item, '' );
	};
	
	let rows = [];
	let rowsU = [];
	let rowsV = [];
	let rowsW = [];
	let docBIdxs = [];
	let _isExists = (rows, doc, deal) => rows.reduce( (a,r) => a || (r.document?.id == doc.id && r.deal?.ID == deal.ID), false );
	// loop over all documents
	catalog.document.byId.forEach( (document) => {
		if (document.docType == 'S' || document.docType == 'A') {
			let dealId = getDocLink(document);
			if (!dealId) { // document with empty link
				let sidoc = _docBIndex(document);
				// add document into index table
				if (sidoc.length) {
					docBIdxs[sidoc] = docBIdxs[sidoc] ?? [];
					docBIdxs[sidoc].push(document);
				}
				// try to find deal id in doc title
				let id = parseInt(document.title);
				let deal = crm.deal.byId[id];
				if (deal !== undefined && isDealNotLose(deal)) {
					// compare elements and deal productrows
					let sideal = _dealBIndex(deal);
					if (sidoc === sideal) {
						if (!deal._doclinks?.length) {
							if (document.status == 'Y')
								rows.push({document:document, deal:deal});
							else
								rowsV.push({document:document, deal:deal});
						} else {
							rowsW.push({document:document, deal:deal, info:'(!) возможно задвоение'});
							console.info('(!) ВОЗМОЖНО ЗАДВОЕНИЕ:', document.title, document, '->', deal);
						}
					} else {
						rowsW.push({document:document, deal:deal, info:sidoc+' != '+sideal});
						console.info('(!) РАСХОЖДЕНИЕ ТОВАРОВ:', document.title, ' != ', deal.TITLE, ':');
						console.info(sidoc, '!=', sideal, {doc:document, deal:deal});
					}
				} else {
					// check responsible is not a test user
					if (isShowTestDD || document.responsibleId != '18') {
						// push for review
						rowsW.push({document:document, info:sidoc});
					}
				}
			} else {
				// already have link
				let deal = crm.deal.byId[dealId];
				if (deal !== undefined) {
					deal._doclinks = deal._doclinks ?? [];
					if (!deal._doclinks.reduce( (a,d) => a || d.id == document.id, false ))
						deal._doclinks.push(document);
					if (deal._doclinks.length > 1) {
						console.info('(!) более одного документа:', deal);
					}
					if (!isDealNotLose(deal)) {
						// add to list to do unlink
						rowsU.push({document:document, deal:deal, info:printDealStage(deal)});
					}
				} else {
					console.warn('Wrong doc link!', document);
					// add to list to do unlink
					rowsU.push({document:document, deal:{ID:dealId}, info:'(!) Сделка не найдена'});
				}
			}
		}
	});
	console.log('docBIdxs:', docBIdxs);
	// loop over all deals
	crm.deal.byId.forEach( (deal) => {
		// look for not lose deal without doclinks and not created by user 1
		if (deal._doclinks === undefined && isDealNotLose(deal) && (isShowTestDD || deal.ASSIGNED_BY_ID != '1')) {
			let idx = _dealBIndex(deal);
			if (idx.length) {
				let docs = docBIdxs[idx];
				if (docs !== undefined) {
					// console.log('docs:', docs);
					docs.forEach( (document) => {
						if (!_isExists(rows, document, deal) && !_isExists(rowsV, document, deal)) {
							rowsV.push({document:document, deal:deal, info:idx});
						}
					});
				} else {
					// push for review
					rowsW.push({deal:deal, info:idx+' '+printDealStage(deal)});
				}
			}
		}
	});
	console.info('Found links:', rows);
	
	// function to prepare API request fields for update call
	let _docUpdateFields = (docId, dealId) => {
		let docType = catalog.document.byId[docId].docType;
		return { documentId:docId, fields:{documentType:docType, [FIELD_NAME_FOR_DEAL[docType]]:dealId} };
	};
	
	// links table
	let $tbody = $box.find('tbody');
	let _rowIdx = (r) => (r.document?.id ?? '') + '-' + (r.deal?.ID ?? '');
	let _make_trow = (r, i) => {
		let $tr = $(
			'<tr idx="' + _rowIdx(r) + '">' +
			'<td class=doc-id>' + (r.document?.id ?? '') + '</td>' +
			'<td>' + (r.document? htmlDocumentTitle(r.document) : '') + '</td>' +
			'<td>' + (r.document?._elements?.length ?? '') + '</td>' +
			'<td>' + printDocStatus(r.document) + '</td>' +
			'<td></td>'+
			'<td class=deal-id>' + (r.deal?.ID ?? '') + '</td>' +
			'<td>' + (r.deal? htmlDealTitle(r.deal) : '') + '</td>' +
			'<td>' + (r.deal?._productrows?.length ?? '') + '</td>' +
			(r.info?
				'<td></td>' +
				'<td>' + r.info + '</td>'
				: ''
			) +
			'<td colspan=2 class="action"></td>' +
			'</tr>'
		);
		if (r.document && r.deal) {
			let link = getDocLink(r.document);
			// link / unlink
			let $btn = link? $('<a>отвязать</a>') : $('<a>связать</a>');
			let dealId = link? '' : r.deal.ID;
			$tr.find('.action').append(
				$btn.on('click', function() {
					$btn.html('...').off('click').removeClass('pointer');
					catalog.userfield.document.update(_docUpdateFields(r.document.id, dealId))
					.then( (result) => {
						console.log('update_result', result);
						console.info('Field updated.');
						$btn.html('Ok');
					})
					.catch(BX24ex.warnErrorCallback);
				}).addClass('pointer')
			);
			// compare button
			if (r.info?.length) {
				$tr.find('.action').append(
					$('<br>'),
					$('<a>сравнить</a>').on('click', function() {
						$('#dealIdToCopyFrom').val(r.deal.ID);
						$('#docIdToCopyTo').val(r.document.id);
						compareDealToDoc();
					}).addClass('pointer')
				);
			}
		}
		// update table
		$tbody.append($tr);
	};
	// loop over all right rows
	rows.forEach(_make_trow);
	// loop over all version rows
	rowsV.forEach(_make_trow);
	// loop over all rows for unlink
	rowsU.forEach(_make_trow);
	// loop over all warning rows
	rowsW.forEach(_make_trow);
	
	// set actions
	$tbody.find('td.doc-id').on('click', clickOnDocId).addClass('pointer');
	$tbody.find('td.deal-id').on('click', clickOnDealId).addClass('pointer');
	
	// batch link action
	if (rows.length) {
		$box.append(
			$('<button>Связать все правильные</button>').on('click', function() {
				let calls = rows.map( (r) => ['catalog.userfield.document.update', _docUpdateFields(r.document.id, r.deal.ID) ]);
				BX24ex.batchPromise(calls, true)
				.then( (results) => {
					console.log('update_results', results);
					console.info('Fields updated.');
					// find all rows in the table and remove action
					$tbody.find( rows.map( (r) => 'tr[idx="' + _rowIdx(r) + '"] .action' ).join(',') ).html('');
				})
				.catch(BX24ex.warnErrorCallback);
			})
		);
	}
	
	// manual link actions
	$box.append(
		'ID док.: <input type="text" id="docIdToLink" style="width:60px" /> ' +
		'ID сделки: <input type="text" id="dealIdToLink" style="width:60px" /> '
	);
	$box.append(
		$('<button>Связать</button>').on('click', function() {
			let docId = $('#docIdToLink').val();
			let dealId = $('#dealIdToLink').val();
			catalog.userfield.document.update(_docUpdateFields(docId, dealId))
			.then( (result) => {
				console.log('update_result', result);
				console.info('Field updated.');
			})
			.catch(BX24ex.warnErrorCallback);
		})
	);
	$box.append(
		$('<button>Отвязать</button>').on('click', function() {
			let docId = $('#docIdToLink').val();
			catalog.userfield.document.update(_docUpdateFields(docId, ''))
			.then( (result) => {
				console.log('update_result', result);
				console.info('Field updated.');
			})
			.catch(BX24ex.warnErrorCallback);
		})
	);
}


// =============================================================================
// Get all products with offers
async function getAllProducts() {
	let $productsInfo = $('#productsInfo').html('');
	let $offersCount = $('#offersCount').html('');
	let loadbar = createLoadbarElement($('#productsLoad'));
	let products, offers;
	await Promise.all([
		catalog.product.listAll().then(data => {products = data}),
		catalog.product.offer.listAll().then(data => {offers = data}),
	]);
	// console.dir(products);
	let progressbar = createProgressbarElement($('#productsLoad'), products.length);
	$offersCount.html(' + '+offers.length+' вариаций');
	
	catalog.product.byId = [];
	products.forEach( (product) => {
		catalog.product.byId[product.id] = product;
		if (product.type == 3) {
			product._offers = [];
		} else {
			progressbar.incNumber(); // update progressbar
		}
	});
	// let activeCount = catalog.product.byId.filter( (product) => (product.active == 'Y') ).length;
	let activeCount = catalog.product.byId.reduce( (ac, product) => ac + ((product.active == 'Y')? 1: 0), 0 );
	$productsInfo.html(' ('+activeCount+' активных)');
	
	catalog.product.offer.byId = [];
	catalog.product.offer._up(offers);
	let activeOfferCount = catalog.product.byId.reduce(
		(ac, product) => ac + ((product.active == 'Y')? product._offers.reduce( (ac, offer) => ac + ((offer.active == 'Y')? 1: 0), 0 ): 0), 0 );
	$offersCount.append(' ('+activeOfferCount+' активных)');
	
	products.forEach( (product) => {
		let id = product.id;
		if (product.type == 3) {
			if (product._offers.length)
				progressbar.incNumber(); // update progressbar
			else
				console.warn(product.id, 'No offers:', product);
		}
	});
	
	// show info
	console.info('catalog.product.byId:', catalog.product.byId);
	console.info('catalog.product last index:', catalog.product.byId.length-1);
	console.info('catalog.product.offer.byId:', catalog.product.offer.byId);
	console.info('catalog.product.offer last index:', catalog.product.offer.byId.length-1);
	
	// fill table
	fillProductTable();
	fillProductsPrice();
	fillProductTableOrdoc();
	checkProductsXmlId();
}

function checkProductsXmlId() {
	let productsListToFix = [];
	// loop over all products
	catalog.product.byId.forEach( (product) => {
		if (product.type == 3) {
			let id = product.id;
			let $xmlId = $('#product-'+id+' .xmlId').html('');
			let isWarn = false;
			product._offers?.forEach( (offer) => {
				if (offer.id != offer.xmlId) {
					$xmlId.append('!x! ');
					isWarn = true;
				}
				if (offer.name != product.name) {
					$xmlId.append('!n! ');
					isWarn = true;
				}
				if ((offer.detailText ?? '') != offerPropsStr(offer)) {
					$xmlId.append('!t! ');
					isWarn = true;
				}
			});
			if (isWarn) { // this product offers have wrong xmlId / name / description
				productsListToFix.push(product);
			}
		}
	});
	// show/hide fix button
	let $btn = $('#xmlIdFix');
	if (productsListToFix.length) {
		$btn.off('click').on('click', async () => {
			$btn.prop('disabled',true);
			await upOffersXmlId(productsListToFix);
			getAllProducts();
		})
		.show().prop('disabled',false);
		$('#productTable').addClass('product-fix');
	} else {
		$btn.off('click').hide();
		$('#productTable').removeClass('product-fix');
	}
}

async function fillProductsPrice() {
	let calls = catalog.product.byId.map( (product) => ['catalog.price.list', catalog.price._listByProduct(product.id)] );
	(await BX24ex.batchPromise(calls)).forEach( (data, id) => {
		// console.log('price:', id, data);
		let price = data.prices?.[0]?.price ?? '-';
		$('#product-' + id + ' .price').html(formatAsCurrency(price));
	});
}

function offerPropsStr(offer) {
	return catalog.product.userPropsById
		.map( (_,i) => (offer?.['property'+i]?.valueEnum ?? null) ).filter( (v) => v!=null ).join(' / ');
}

function fillProductTable() {
	// products table
	let $tbody = $('#productTable tbody').html('');
	// loop over all products
	catalog.product.byId.forEach( (product) => {
		// update table
		let id = product.id;
		let quantity = product.type==1? product.quantity : 0;
		let reserved = product.type==1? product.quantityReserved : 0;
		let htmlROffers = '';
		product._offers?.forEach( (offer) => {
			quantity += offer.quantity ?? 0;
			reserved += offer.quantityReserved ?? 0;
			htmlROffers += '<tr id=offer-' + offer.id + ' class="' + (offer.quantityReserved || offer.quantity? '' : 'full-view') + '">' +
				'<td class="offer-id tw">' + offer.id + '</td>' +
				'<td class="tw">' + htmlProductOfferTitle(offer) + '</td>' +
				'<td class="tw">' + (offer.quantity ?? '') + '</td>' +
				'<td class="tw">' + (offer.quantityReserved ?? '') + '</td>' +
				'<td class="ordoc tw"></td>' +
				'</tr>';
		});
		let html = '<tr id=product-' + id + ' product-type=' + product.type + ' product-active=' + product.active + '>' +
			'<td class="product-id price-view tw">' + id + '</td>' +
			'<td class="name price-view tw">' + htmlProductTitle(product) + '</td>' +
			'<td class="offers tw">' + (product._offers?.length??'') + '</td>' +
			'<td class="active tw">' + (product.active) + '</td>' +
			'<td class="quantity tw">' + (quantity? quantity : '') + '</td>' +
			'<td class="reserved tw">' + (reserved? reserved : '') + '</td>' +
			'<td class="ordoc tw"></td>' +
			'<td class="xmlId fix-only tw"></td>' +
			'<td class="price price-view tw"></td>' +
			'</tr>';
		$tbody.append(html);
		if (htmlROffers.length)
			$tbody.append('<tr class="offers" p="product-' + id + '" product-type=' + product.type + ' product-active=' + product.active + '>' +
				'<td colspan=9>' +
				'<table>' +
				'<tr><th></th><th class="name"></th><th class="quantity"></th><th class="reserved"></th></tr>' +
				'<tbody>' + htmlROffers + '</tbody>' +
				'</table>' +
				'</td>' +
				'</tr>'
			);
	});
	// set actions
	$tbody.find('td.product-id').on('click', clickOnProductId).addClass('pointer');
	$tbody.find('td.offer-id').on('click', clickOnOfferId).addClass('pointer');
	// product offers & quantity cell -> open offers row
	$tbody.find('td.offers, td.quantity').on('click', function() {
		let $this = $(this);
		let _class = $this.hasClass('offers')? 'full-view' : 'simple-view';
		if ($this.hasClass('clicked-on')) {
			$this.removeClass('clicked-on');
			$this.parent().next().removeClass(_class);
		} else {
			$this.addClass('clicked-on');
			$this.parent().next().addClass(_class);
		}
	}).addClass('pointer');
	// product price cell -> set price dialog
	$tbody.find('td.price').on('click', function() {
		let $this = $(this);
		let old = parseInt($this.text());
		let productId = parseInt($this.parent().find('.product-id').text());
		let price = prompt('Price:', old);
		if (price) {
			price = parseInt(price);
			catalog.product.offer.listByParent(productId)
			.then( async (offers) => {
				console.log('set price:', price, offers);
				let calls = offers.map( (offer) => ['catalog.price.modify', catalog.price._modifyByProduct(offer.id, price)] );
				let results = await BX24ex.batchPromise(calls, true);
				console.log(results);
				// update cell
				$this.html(formatAsCurrency(price));
			})
			.catch(BX24ex.warnErrorCallback);
		}
	}).addClass('pointer');
}

// fill ordoc column in the products table
function fillProductTableOrdoc() {
	if (sale.order.byId == undefined) return;
	if (catalog.document.byId == undefined) return;
	let _fill = (id, $tr) => {
		let orders = findOrdersByProduct(id)?.length;
		let docs = findDocsByProduct(id, true)?.length;
		let val = (orders || docs)? (orders? orders : '-') + ' / ' + (docs? docs : '-') : '';
		$tr.find('.ordoc').html(val);
	};
	catalog.product.byId?.forEach( (product) => _fill(product.id, $('#product-'+product.id)) );
	catalog.product.offer.byId?.forEach( (offer) => _fill(offer.id, $('#offer-'+offer.id)) );
}


// =============================================================================
// fill the priceTable from text and prepare productPrices Array
var productPrices = [];
function fillPriceTable(text) {
	let $tbody = $('#priceTable tbody');
	$tbody.html('');
	productPrices = [];
	// split text per lines
	let lines = text.split(/[\r\n]+/g);
	lines.forEach( (line) => {
		// split line per tabs or spaces
		let cols = line.split(/[\t]+/g);
		if (!cols[1]) cols = line.split(/[\s]+/g);
		let id = cols[0];
		if (id) {
			// get price from 3rd or 2nd column (price can be like "5 550")
			let price = cols[1]? parseInt( (cols[2] ?? cols[1]).replace(/\s/g, '') ) : '';
			productPrices.push({id:id, price:price});
			// fill
			let html = '<tr id=p-'+id+'><td>'+id+'</td><td>'+price+'</td><td class=offers></td><td class=cur></td><td class=proc></td></tr>';
			$tbody.append(html);
		}
	});
	console.info('productPrices:', productPrices);
	// fill offers and current columns
	fillPriceTableOffers();
	fillPriceTableCurrent();
}

// fill the offers column of priceTable and productPrices[]._offers
async function fillPriceTableOffers() {
	await Promise.all( productPrices.map( async (parent) => {
		// console.log('get offers for', parent);
		let data = await catalog.product.offer.listByParent(parent.id);
		// console.log(data);
		// update
		parent._offers = data;
		$('#p-'+parent.id+' .offers').html(data.length);
	}));
}

// fill the current price column of priceTable
async function fillPriceTableCurrent() {
	await Promise.all( productPrices.map( async (parent) => {
		// console.log('get price for', parent);
		let $cur = $('#p-'+parent.id+' .cur').html('');
		let current_price = await catalog.price.listByProduct(parent.id, (data) => data.prices?.[0]?.price ?? '-');
		if (current_price != parent.price)
			$cur.append('<red>'+current_price+'</red>');
		else
			$cur.append(current_price);
	}));
}

// make full check of offers current price over priceTable
async function checkProductTableCurrent() {
	await Promise.all( productPrices.map( async (parent) => {
		// console.log('get prices for', parent);
		let progressbar = createProgressbarElement($('#p-'+parent.id+' .proc'), parent._offers.length);
		let $cur = $('#p-'+parent.id+' .cur').html('');
		// prepare batch request
		let calls = parent._offers.map( (offer) => ['catalog.price.list', catalog.price._listByProduct(offer.id)] );
		// get batch request and do check
		let first_price;
		(await BX24ex.batchPromise(calls)).forEach( (data) => {
			let current_price = data.prices?.[0]?.price ?? '-';
			// update content
			if (first_price === undefined) {
				first_price = current_price;
				if (first_price != parent.price)
					$cur.append('<red>'+first_price+'</red>');
				else
					$cur.append(first_price);
			} else {
				if (current_price != first_price && current_price != parent.price)
					$cur.append(', <red>'+current_price+'</red>');
			}
			// update progressbar
			progressbar.incNumber();
		});
	}));
}

// update price for all offers over productsList
async function setCatalogProductPriceByList(productsList) {
	await Promise.all( (productsList ?? productPrices).map( async (parent) => {
		console.log('set prices for', parent);
		let progressbar = createProgressbarElement($('#p-'+parent.id+' .proc'), parent._offers.length);
		// prepare batch request
		let calls = parent._offers.map( (offer) => ['catalog.price.modify', catalog.price._modifyByProduct(offer.id, parent.price)] );
		// get batch request
		let results = await BX24ex.batchPromise(calls);
		console.info(results);
		// check results
		results.forEach( () => { progressbar.incNumber(); });
	}));
}

// update xmlId, name and detailText for all offers over productsList
async function upOffersXmlId(productsList) {
	await Promise.all( (productsList ?? productPrices).map( async (parent) => {
		console.log('update offers xmlId, name, detailText for', parent);
		let progressbar = createProgressbarElement($('#p-'+parent.id+' .proc'), parent._offers.length);
		// prepare batch request
		let name = catalog.product.byId?.[parent.id]?.name ?? (await catalog.product.get(parent.id))?.name;
		let calls = parent._offers.map( (offer) => [
			'catalog.product.offer.update',
			{id: offer.id, fields: {iblockId: offer.iblockId, xmlId: offer.id, name: (name ?? offer.name), detailText: offerPropsStr(offer)}}
		]);
		// get batch request
		let results = await BX24ex.batchPromise(calls);
		console.info(results);
		// check results
		results.forEach( () => { progressbar.incNumber(); });
	}));
}


// =============================================================================
// product structure
async function showProductStruct() {
	let title;
	let $productStruct = $('#productStruct');
	let productId = $('#productIdToShow').val();
	// get product price
	// let prices = await catalog.price.listByProduct(productId);
	// let productPrice = prices?.[0]?.price ?? 0;
	let productPrice = await catalog.price.listByProduct(productId, (data) => data.prices?.[0]?.price ?? 0);
	// get product offers
	let offers = await catalog.product.offer.listByParent(productId);
	catalog.product.offer._up(offers);
	console.dir(offers);
	
	// prepare product variants map
	let props = catalog.product.userPropsById.map(() => []);
	offers.forEach( (offer) => {
		// get product title from first offer
		if (!title) title = offer.name;
		// loop over each property
		catalog.product.userPropsById.forEach( (property, id) => {
			// get property value object from offer
			let oValue = offer['property'+id];
			// if value exists then push this offer into map
			if (oValue && oValue.value !== '0') {
				let _enum = property._enumsById[oValue.value];
				if (_enum == undefined) {
					console.warn('Property enum is not found!', {property:property, offerValue:oValue});
				} else {
					// let name = id==111? colorsList[oValue.value] : oValue.valueEnum;
					let name = _enum.value;
					let idx = enumSortIdx(_enum); // unique sorted index
					if (props[id][idx] == undefined)
						props[id][idx] = { id:_enum.id, name:name, offers:[] }; // init variant
					props[id][idx].offers.push(offer);
				}
			}
		});
	});
	console.info('props:', props);
	
	// product structure table
	let $title = $('#productStructTable .title').html(title+' ('+offers.length+' вариаций)');
	let $tbody = $('#productStructTable tbody');
	$tbody.html('');
	// loop over all variants of the product
	props.forEach( (prop, id) => {
		let property = catalog.product.userPropsById[id];
		// prepare variants list
		let htmlValues = prop.reduce ( (acc, variant) => {
			// console.log('variant:', variant, 'prop:', prop);
			let active = !(variant.offers?.reduce( (a, offer) => (a && offer.active=='N'), true )); // at least one active offer
			let inactive = variant.offers?.reduce( (a, offer) => (a || offer.active=='N'), false ); // at least one inactive offer
			return acc + ' <span val="' + variant.id + '" ' + (active? '' : 'class="inactive"') + '>' + variant.name +
				' <sup ' + (inactive? 'class="inactive"' : '') + '>(' + variant.offers?.length + ')</sup></span>';
		}, '' );
		// update table
		let html = '<tr id=psp-' + id + '><td>' + id + '</td><td>' + property.name + '</td>' +
			'<td>' + (htmlValues.length? htmlValues : '<span class="expand">+</span>') + '</td>' +
			'</tr>';
		$tbody.append(html);
	});
	
	// actions table
	let $actionsBox = $productStruct.find('div.actions').html(
		'<table class="common">' +
		'<tr class="first"><td></td><td>' +
		' <button action="activate">Активировать</button>' +
		' <button action="deactivate">Деактивировать</button>' +
		'</td></tr>' +
		// table rows for each user property to select a variant
		catalog.product.userPropsById.reduce( (acc, property, id) => {
			return acc + '<tr id="prop' + id + '"><th>&nbsp;Варианты&nbsp;</th><td>' +
				// list of property variants (enums) in one row
				property._sorted.reduce( (a, _enum) => a + ' <span class=enum val=' + _enum.id + '>' + _enum.value + '</span>', '' ) +
				'</td></tr>';
		}, '' ) +
		//
		'<tr class="buttons"><td></td><td>' +
		' <button action="copy">Копировать</button>' +
		// ' <button action="replace">Заменить</button>' +
		'</td></tr>' +
		'<tr class="expand"><td></td><td>' +
		' <button action="expand">Добавить</button>' +
		'</td></tr>' +
		'</table>' +
		'<div>' +
		' Последний индекс: <i id="lastOfferIndex">' + Math.max(catalog.product.byId?.length-1, catalog.product.offer.byId?.length-1) + '</i>' +
		'</div>'
	);
	
	// set actions
	var activeProp;
	$tbody.find('span').on('click', function() {
		let $this = $(this);
		let id = parseInt($this.closest('tr').attr('id').substr(4));
		let value = $this.attr('val');
		let _enum = catalog.product.userPropsById[id]?._enumsById[value];
		activeProp = {id:id, value:value, prop:props[id][enumSortIdx(_enum)]};
		console.info(id, 'activeProp:', activeProp);
		$tbody.find('span').removeClass('active');
		$this.addClass('active');
		$actionsBox.find('tr').removeClass('open');
		$actionsBox.find('tr#prop'+id).addClass('open');
		if (value?.length)
			$actionsBox.find('tr.first').addClass('open');
	}).addClass('pointer');
	
	var selectedValue;
	$actionsBox.find('span.enum').on('click', function() {
		let $this = $(this);
		selectedValue = $this.attr('val');
		// change selected enum
		$actionsBox.find('span').removeClass('active');
		$this.addClass('active');
		// open next action depends on selected value or [+]
		if (activeProp.value?.length)
			$actionsBox.find('tr.buttons').addClass('open');
		else
			$actionsBox.find('tr.expand').addClass('open');
	}).addClass('pointer');
	
	// activate / deactivate buttons
	let _set_prop_active = (val) => {
		if (activeProp == undefined) return;
		// offer activation calls
		let calls = activeProp.prop.offers.map((offer) =>
			[ 'catalog.product.offer.update', {id: offer.id, fields: {iblockId: offer.iblockId, active: val}} ]);
		BX24ex.batchPromise(calls, true)
		.then( (results) => {
			// console.log('results', results);
			console.info('Set active to ' + val + ' done.');
			// redraw structure
			showProductStruct();
		})
		.catch(BX24ex.warnErrorCallback);
	};
	$actionsBox.find('button[action=activate]').on('click', () => _set_prop_active('Y')).addClass('pointer');
	$actionsBox.find('button[action=deactivate]').on('click', () => _set_prop_active('N')).addClass('pointer');
	
	// copy button
	$actionsBox.find('button[action=copy]').on('click', function() {
		if (selectedValue == undefined) return;
		console.info('selectedValue:', selectedValue);
		// prepare offer creation calls by list of selected prop
		let calls = activeProp.prop.offers.map( (offer) => {
			let fields = {};
			// copy and cleanup offer data
			Object.assign(fields, offer);
			delete fields.id;
			delete fields.xmlId;
			delete fields.quantity;
			delete fields.quantityReserved;
			delete fields.quantityTrace;
			// set new value
			fields['property'+activeProp.id] = {value: selectedValue};
			return [ 'catalog.product.offer.add', {fields: fields} ];
		});
		// console.log(calls);
		// request
		BX24ex.batchPromise(calls, true)
		.then( (results) => {
			console.log('add_results', results);
			console.info('(!) Added IDs:', results.map((data) => data.offer.id).join(', '));
			console.info('Copy done.');
			// prepare description update calls
			let calls = results.map( (data) => [
				'catalog.product.offer.update',
				{id: data.offer.id, fields: {iblockId: data.offer.iblockId, detailText: offerPropsStr(data.offer)}}
			]);
			// offers update
			return BX24ex.batchPromise(calls, true);
		})
		.then( (results) => {
			console.log('update_results', results);
			console.info('Description updated.');
			// prepare price modify calls
			let calls = results.map( (data) => [ 'catalog.price.modify', catalog.price._modifyByProduct(data.offer.id, productPrice) ]);
			// price modify request
			return BX24ex.batchPromise(calls, true);
		})
		.then( (results) => {
			console.log('modify_results', results);
			console.info('Set price done.');
			// redraw structure
			showProductStruct();
		})
		.catch(BX24ex.warnErrorCallback);
	}).addClass('pointer');
	
	// expand button to set first value for new property
	$actionsBox.find('button[action=expand]').off('click').on('click', function() {
		if (activeProp == undefined) return;
		// all offers of the product update calls
		let fields = {iblockId: offers[0].iblockId, ['property'+activeProp.id]: {value: selectedValue}};
		let calls = offers.map( (offer) => [ 'catalog.product.offer.update', {id: offer.id, fields: fields} ]);
		BX24ex.batchPromise(calls, true)
		.then( (results) => {
			console.log('results', results);
			console.info('New prop done.');
			// prepare description update calls
			let calls = results.map( (data) => [
				'catalog.product.offer.update',
				{id: data.offer.id, fields: {iblockId: data.offer.iblockId, detailText: offerPropsStr(data.offer)}}
			]);
			// offers update
			return BX24ex.batchPromise(calls, true);
		})
		.then( (results) => {
			console.log('update_results', results);
			console.info('Description updated.');
			// redraw structure
			showProductStruct();
		})
		.catch(BX24ex.warnErrorCallback);
	}).addClass('pointer');
}


// =============================================================================
// Contacts

async function showContacts() {
	let $box = $('#contactsBox');
	let $table = $('#contactsTable');
	let isAll = $box.find('input[name="contacts-all"]:checked').length? true : false;
	
	$table.find('thead').html(
		'<tr>' +
		'<th class="contact-id">ID</th>' +
		'<th>Фамилия</th>' +
		'<th>Имя</th>' +
		'<th>Отчество</th>' +
		'<th>Тип</th>' +
		'<th class="title">Вариации</th>' +
		'</tr>'
	);

	let contacts = await crm.contact.list({order: {ID: 'DESC'}});
	
	// filter contacts
	let _hasSpace = (s) => (s ?? '').indexOf(' ') >= 0;
	let _someSpace = (c) => _hasSpace(c.NAME) || _hasSpace(c.LAST_NAME) || _hasSpace(c.SECOND_NAME);
	let _isAnonym = (c) => c.NAME == 'Без имени' && !c.LAST_NAME && !c.SECOND_NAME;
	let _isMisCall = (c) => c.NAME.indexOf('Пропущенный звонок') >= 0 && !c.LAST_NAME && !c.SECOND_NAME;
	let _notSpam = (c) => c.TYPE_ID != 'NOTCLIENT' && c.TYPE_ID != 'SPAM';
	let items = isAll? contacts : contacts.filter( (c) => _notSpam(c) && _someSpace(c) && !_isAnonym(c) && !_isMisCall(c) );
	console.info('items:', items);

	// sort contacts by ID desc
	// items.sort( (a,b) => (a.ID < b.ID)? 1 : -1 );

	// fill table body
	let htmlBody = items.length? '' : '[EMPTY]';
	items.forEach( (r, i) => {
		htmlBody += '<tr>' +
			'<td class=contact-id>' + r.ID + '</td>' +
			'<td>' + r.LAST_NAME + '</td>' +
			'<td>' + htmlContactTitle(r) + '</td>' +
			'<td>' + r.SECOND_NAME + '</td>' +
			'<td>' + r.TYPE_ID + '</td>' +
			'<td></td>' +
			'</tr>';
	});
	$box.find('tbody').html(htmlBody);
}


// =============================================================================
// Test Buttons Section

function getaPromise(method, params, data_field) {
	BX24ex.aPromise(method, params, data_field)
	.then(data => { console.log(method, params, data); })
	.catch(BX24ex.warnErrorCallback);
}

function getaFunc(method, params) {
	let func = method.split('.').reduce((a, name) => a[name], window);
	func(params)
	.then(data => { console.log(method, params, data); })
	.catch(BX24ex.warnErrorCallback);
}

// Special function Buttons

async function MigrateDealUserfield(fromName, toName) { // rename/migrate deal's userfield
	// check input
	if (fromName.substr(0,7) != 'UF_CRM_') fromName = 'UF_CRM_' + fromName;
	if (toName.substr(0,7) != 'UF_CRM_') toName = 'UF_CRM_' + toName;
	// check and get source userfield
	let source_field = (await crm.deal.userfield.list({filter:{FIELD_NAME:fromName}}))?.[0];
	if (!source_field) {
		console.warn(fromName, 'is not found in userfields');
		return;
	}
	source_field = await crm.deal.userfield.get(source_field.ID);
	console.log('source_field:', source_field);
	// check/create new userfield
	let new_field = (await crm.deal.userfield.list({filter:{FIELD_NAME:toName}}))?.[0];
	if (new_field === undefined) {
		console.info(toName, 'creating...');
		let params = {fields:{}};
		Object.assign(params.fields, source_field);
		delete params.fields.ID;
		params.fields.FIELD_NAME = toName;
		params.fields.XML_ID = toName;
		let id = await crm.deal.userfield.add(params);
		new_field = await crm.deal.userfield.get(id);
	}
	console.log('new_field:', new_field);
	// prepare migration function
	let migrate = (val) => val;
	if (source_field.LIST) {
		let s2new = [];
		let new_list = new_field.LIST;
		source_field.LIST.forEach( s => {
			new_list.forEach( n => {
				if (n.VALUE == s.VALUE) {
					s2new[s.ID] = n.ID;
					console.log(s.VALUE, s.ID, s2new[s.ID]);
				}
			});
			if (s2new[s.ID] === undefined) console.warn(s.VALUE, 'is not found');
		});
		// create new migration function
		migrate = (val) => s2new[val];
	}
	// get deals for update
	let filter = {};
	filter[fromName] = '^%^';
	let deals = await crm.deal.list({select:['ID','TITLE',fromName,toName], filter:filter});
	console.log('deals:', deals);
	let calls = deals.filter( deal => deal[fromName] !== undefined && deal[fromName] !== null && migrate(deal[fromName]) !== deal[toName] )
	.map( deal => {
		let params = {id:deal.ID, fields:{}};
		params.fields[toName] = migrate(deal[fromName]) ?? '';
		return ['crm.deal.update', params];
	});
	// console.log('calls:', calls);
	if (calls.length) {
		console.info(toName, 'updating...');
		let results = await BX24ex.batchPromise(calls, true);
		console.info('update results:', results);
	} else {
		console.info('all deals have proper data');
	}
}

// Deals Check Buttons

async function updateDealsUTMSource(do_update) { // set deals UTM_SOURCE
	let source_field = await crm.deal.userfield.list({filter:{FIELD_NAME:'UF_CRM_UTM_SOURCE1'}});
	let source_list = source_field?.[0]?.LIST;
	if (!source_list) {
		console.warn('UF_CRM_UTM_SOURCE1 is not a list', source_field);
		return;
	}
	console.info('source_list:', source_list);
	const source_values_map = {
		'Реклама в Яндекс':'yandex',
		'Телеграм':'telegram',
		'Вконтакте':'vk',
	};
	// const source_map = {'234':'yandex','238':'telegram','240':'vk'};
	let source_map = {};
	source_list.forEach(item => {
		let utm_source = source_values_map[item.VALUE];
		if (utm_source) {
			source_map[item.ID] = utm_source;
		}
	});
	console.info('source_map:', source_map);
	// get deals for update by source1 and prepare update calls
	let deals = await crm.deal.list({select:['ID','TITLE','UF_CRM_UTM_SOURCE1','UTM_SOURCE'], filter:{UF_CRM_UTM_SOURCE1:'^%^'}});
	console.log('deals:', deals);
	let calls = [];
	deals.forEach( deal => {
		if (deal.UF_CRM_UTM_SOURCE1) {
			let val = source_map[deal.UF_CRM_UTM_SOURCE1];
			if (val && deal.UTM_SOURCE != val) {
				calls.push( ['crm.deal.update', {id:deal.ID, fields:{UTM_SOURCE:val}}] );
				console.info('set:', val, deal);
			}
		}
	});
	// do update
	if (calls.length) {
		if (do_update) {
			let results = await BX24ex.batchPromise(calls, true);
			console.info('update done:', results);
		}
	} else {
		console.info('have no deals to update');
	}
}

async function updateDealsUTMSource2(do_update) { // set deals UTM_SOURCE
	// get deals for update by source and prepare update calls
	const source_id_map = {
		// 'TELPHIN-78122009264':'tel',
		// 'TELPHIN-78007773876':'tel',
		// 'CALL':'tel',
		// '1|VK':'vk',
		// '3|TELEGRAM':'telegram',
		// 'WZb67ca33d-aa0b-496d-a0e3-d861f1dbbec3':'telegram',
		'3':'marketplace',
		// 'RECOMMENDATION':'other',
		// 'PARTNER':'other',
		// 'OTHER':'other',
	};
	console.info('source_id_map:', source_id_map);
	let deals = await crm.deal.list({select:['ID','TITLE','TYPE_ID','SOURCE_ID','UTM_SOURCE','UF_CRM_UTM_SOURCE1'], filter:{UTM_SOURCE:'^&^'}});
	console.log('deals:', deals);
	let calls = [];
	deals.forEach( (deal) => {
		let val = deal.TYPE_ID == 3? 'marketplace' : source_id_map[deal.SOURCE_ID];
		if (val && deal.UTM_SOURCE != val) {
			calls.push( ['crm.deal.update', {id:deal.ID, fields:{UTM_SOURCE:val}}] );
			console.info('set:', val, deal);
		}
	});
	// do update
	if (calls.length) {
		if (do_update) {
			// console.info('calls update:', calls);
			let results = await BX24ex.batchPromise(calls, true);
			console.info('update done:', results);
		}
	} else {
		console.info('have no deals to update');
	}
}

async function updateDealsUTMSource3(do_update) { // reset deals UTM_SOURCE
	// get deals for update and prepare update calls
	// let deals = await crm.deal.list({select:['ID','TITLE','TYPE_ID','SOURCE_ID','UTM_SOURCE','UF_CRM_UTM_SOURCE1'], filter:{UTM_SOURCE:'any'}});
	let deals = await crm.deal.list({select:['ID','TITLE','TYPE_ID','SOURCE_ID','UTM_SOURCE','UF_CRM_UTM_SOURCE1'], filter:{UTM_SOURCE:'^%^'}});
	console.log('deals:', deals);
	let calls = deals.map( (deal) => ['crm.deal.update', {id:deal.ID, fields:{UTM_SOURCE:''}}] );
	// do update
	if (calls.length) {
		if (do_update) {
			let results = await BX24ex.batchPromise(calls, true);
			console.info('update done:', results);
		}
	} else {
		console.info('have no deals to update');
	}
}

async function checkDealsType5() {
	let deals = await crm.deal.list({filter:{TYPE_ID:'5'}});
	let ids = deals.map( (deal) => deal.CONTACT_ID );
	let contacts = await crm.contact.list({filter:{ID:ids}});
	console.log('contacts:', contacts);
	let calls = contacts.filter( (contact) => !contact.TYPE_ID ).map( (contact) => ['crm.contact.update', {ID:contact.ID, fields:{TYPE_ID:'NOTCLIENT'}}] );
	if (calls.length) {
		let results = await BX24ex.batchPromise(calls, true);
		console.info('batch update:', results);
	} else {
		console.info('all contacts have type');
	}
}

async function changeContactsType() { // changing contact type
	let contacts = await crm.contact.list({filter:{TYPE_ID:'3'}});
	console.log('contacts:', contacts);
	let calls = contacts.map( (contact) => ['crm.contact.update', {ID:contact.ID, fields:{TYPE_ID:'2'}}] );
	if (calls.length) {
		let results = await BX24ex.batchPromise(calls, true);
		console.info('batch update:', results);
	} else {
		console.info('all contacts have proper type');
	}
}

function checkDealsTypeVsCategory(doFix = false) {
	crm.deal.list()
	.then( (deals) => {
		deals.forEach( (deal) => {
			if (!isDealTypeByCategory(deal)) {
				if (doFix) {
					console.info('Wrong type in deal:', deal);
					crm.deal.update({id: deal.ID, fields: {TYPE_ID:getDealDefaultType(deal.CATEGORY_ID)}})
					.then( (result) => {
						console.info(deal.ID, 'deal type has been updated:', result);
					})
					.catch(BX24ex.warnErrorCallback);
				} else {
					console.warn('Wrong type in deal:', deal);
				}
			}
		});
		console.info('Finished.', deals.length, 'deals checked.');
	})
	.catch(BX24ex.warnErrorCallback);
}

// Deal Buttons

function getDeal(id) {
	crm.deal.get(id)
	.then((data) => { console.log('crm.deal.get', data); })
	.catch(BX24ex.warnErrorCallback);
}

function up1DealList() { // do update for each deal from list
	console.info('up1DealList:');
	crm.deal.list()
	.then((data) => {
		console.dir(data);
		// update each item
		data.forEach( (deal) => {
			up1Deal(deal.ID);
		});
	})
	.catch(BX24ex.warnErrorCallback);
}

function up1Deal(id) { // copy value from old field to new
	crm.deal.get(id)
	.then((data) => {
		if (data['UF_CRM_1699437561279'] && !data['UF_CRM_1703579175']) {
			crm.deal.update({
				id: id,
				fields: { 'UF_CRM_1703579175': [ uf1to2map[data['UF_CRM_1699437561279']] ] },
				params: {}
			})
			.then((data) => {
				console.info('crm.deal.update', id, data);
			})
			.catch(BX24ex.warnErrorCallback);
		} else {
			console.info(id, 'skipped', data);
		}
	})
	.catch(BX24ex.warnErrorCallback);
}

var uf1to2map = {}; // prepared map to copy values
function getDealFields() {
	crm.deal.fields()
	.then((data) => {
		console.info('crm.deal.fields', data);
		// prepare values map
		let oldArr = data['UF_CRM_1699437561279']?.items;
		console.log('oldArr:', oldArr);
		let newArr = data['UF_CRM_1703579175']?.items;
		console.log('newArr:', newArr);
		oldArr?.forEach( (v) => {
			let id = v.ID;
			let val = v.VALUE;
			// console.log(id, val);
			newArr.forEach( (v) => {
				if (v.VALUE == val) {
					uf1to2map[id] = v.ID;
					// console.log(val, id, uf1to2map[id]);
				}
			});
		});
		console.info('uf1to2map:', uf1to2map);
	})
	.catch(BX24ex.warnErrorCallback);
}


// Catalog Buttons

function getCatalogProductList() {
	catalog.product.list({ select: [ 'id', 'iblockId', '*' ], filter: { iblockId : catalog.product.iblockId }, order: { id: 'ASC' } })
	.then((data) => { console.log('catalog.product.list', data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCatalogProductOfferList() {
	catalog.product.offer.list({ select: [ 'id', 'iblockId', '*' ], filter: { iblockId: catalog.product.offer.iblockId }, order: { id: 'ASC' } })
	.then((data) => { console.log('catalog.product.offer.list', data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCatalogProductOfferListBy(parentId) {
	catalog.product.offer.list({ select: [ 'id', 'iblockId', '*' ], filter: { iblockId: catalog.product.offer.iblockId, parentId: parentId }, order: { id: 'ASC' } })
	.then((data) => { console.log('catalog.product.offer.list by parentId', parentId, data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCatalogProductFieldsByFilter() {
	// простой
	catalog.product.getFieldsByFilter({ select: [ '*' ], filter: { iblockId: catalog.product.iblockId, productType: 1 } })
	.then((data) => { console.log('простой товар', data); })
	.catch(BX24ex.warnErrorCallback);
	// товар с предложениями
	catalog.product.getFieldsByFilter({ select: [ '*' ], filter: { iblockId: catalog.product.iblockId, productType: 3 } })
	.then((data) => { console.log('товар с предложениями', data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCatalogPriceListBy(productId) {
	catalog.price.list({ select: [ '*' ], filter: { productId: productId }, order: { id: 'ASC' }, })
	.then((data) => { console.log('catalog.price.list(productId)', productId, data); })
	.catch(BX24ex.warnErrorCallback);
}

function doCatalogPriceUpdate(id, productId, price) {
	catalog.price.update({
		id: id,
		fields: { catalogGroupId: catalog.price.catalogGroupId, currency: 'RUB', price: price, productId: productId }
	})
	.then((data) => { console.log('catalog.price.update', data); })
	.catch(BX24ex.warnErrorCallback);
}

function doCatalogPriceModify(productId, price) {
	BX24ex.aPromise('catalog.price.modify', {
		fields: { product: { id: productId, prices: [{ catalogGroupId: catalog.price.catalogGroupId, price: price, currency: 'RUB', }] } }
	})
	.then((data) => { console.log('catalog.price.modify', data); })
	.catch(BX24ex.warnErrorCallback);
}

function doCatalogPriceUpdateBy(productId, price) {
	// get price id and do update
	catalog.price.list({ select: [ '*' ], filter: { catalogGroupId: catalog.price.catalogGroupId, productId: productId } })
	.then((data) => {
		console.log('catalog.price.list by productId', productId, data);
		// do update
		doCatalogPriceUpdate(data[0].id, productId, price);
	})
	.catch(BX24ex.warnErrorCallback);
}

function setCatalogProductOfferPriceByParent(parentId, price) {
	catalog.product.offer.list({
		select: [ 'id', 'iblockId' ],
		filter: { iblockId: catalog.product.offer.iblockId, parentId: parentId, },
		order: { id: 'ASC' },
	})
	.then((data) => {
		console.log('catalog.product.offer.list', data);
		// update each item
		data.forEach( (v) => {
			let id = v.id;
			// doCatalogPriceUpdateBy(id, price);
			doCatalogPriceModify(id, price);
		});
	})
	.catch(BX24ex.warnErrorCallback);
}


// CRM Product Buttons

function doCrmProductUpdatePrice(productId, price) {
	BX24ex.aPromise('crm.product.update', { id: productId, fields:{ CURRENCY_ID: 'RUB', PRICE: price } })
	.then((data) => { console.log('crm.product.update', data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCrmProductList(catalogId) {
	BX24ex.aPromise('crm.product.list', {
		select: [ '*', 'PROPERTY_*' ],
		filter: { 'CATALOG_ID': catalogId },
		order: { 'ID': 'ASC' },
	})
	.then((data) => { console.log('crm.product.list', data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCrmProductPropertyList() {
	BX24ex.aPromise('crm.product.property.list', {
		order: {'SORT': 'ASC'},
		// filter: {
			// 'PROPERTY_TYPE': 'S',
			// 'USER_TYPE': 'HTML'
		// }
	})
	.then((data) => { console.log('crm.product.property.list', data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCrmItemProductrowList(ownerId) {
	BX24ex.aPromise('crm.item.productrow.list', {
		'filter': {
			'=ownerType': 'D',
			'=ownerId': ownerId,
			// '0': {
				// 'logic': 'OR',
				// '0': {
					// '>discountRate': 10
				// },
				// '1': {
					// '<price': 1000
				// }
			// }
		}
	}, 'productRows')
	.then((data) => { console.log('crm.item.productrow.list', data); })
	.catch(BX24ex.warnErrorCallback);
}

function getCrmItemProductrowList2(ownerId) {
	let productId = prompt('Product ID');
	crm.item.productrow.listByDealProduct(ownerId, productId)
	.then((data) => { console.log('crm.item.productrow.listByDealProduct', data); })
	.catch(BX24ex.warnErrorCallback);
}


function invoicesCheckNames() {
	// находим все счета со статусом не равным "новый" ("DT31_8:N")
	crm.invoice.list({ filter:{"!=stageId":"DT31_8:N"} })
	.then( (list) => list.forEach( async (invoice) => {
		// проверяем поле имени документа на заполненность
		if (!invoice.ufCrmSmartInvoiceSentdocname) {
			// ищем документы на основе нужного шаблона (4)
			let docs = await crm.invoice.document.list({ filter:{entityId: invoice.id, templateId: 4}, order:{id:"desc"} });
			console.log('[', invoice.id, ']', invoice.title, 'documents:', docs);
			// если найден, то заполняем поле и меняем имя
			if (docs.length) {
				let name = docs[0].title;
				console.log('Set invoice name:', name);
				let fields = {title:name, ufCrmSmartInvoiceSentdocname:name};
				let result = await crm.invoice.update({ id: invoice.id, fields: fields });
				console.info('Updated:', result);
			}
		}
	}))
	.catch(BX24ex.warnErrorCallback);
}


// Unit test

async function testBX24ext() {
	console.info('Start');
	let offers = await catalog.product.offer.listAll();
	console.info('offers:', offers);
	let calls = offers.map( (offer) => ['catalog.price.list', catalog.price._listByProduct(offer.id)] );
	let results = await BX24ex.batchPromise( calls, false, (data) => parseInt(data.prices[0].price) );
	console.info('results:', results);
	let prices = results.map( (data) => data );
	console.info('prices:', prices);
	let zero = results.filter( (data) => data == 0 );
	console.info('zero:', zero);
	let middle = results.reduce( (ac, data) => (ac === null? data : (ac + data)/2), null );
	console.info('middle price:', middle);
	console.info('End');
}


function doTest() {
	let $box = $('#buttonsBox');
	
	// Счета
	$box.prepend(
		$('<div></div><br>')
		.append( $('<button>Проверить имена счетов</button>').on('click', invoicesCheckNames) )
		.append( $('<button>Список счетов</button>').on('click', () => getaFunc('crm.invoice.list')) )
	);
	
	// loadbar test button
	$box.append(
		$('<div></div>')
		.append( $('<button>loadbar</button>').on('click', function() {
			let bar = createLoadbarElement($(this).parent());
		}))
	);
}

// crm.status.add {fields:{ENTITY_ID:'CONTACT_TYPE',STATUS_ID:'NOTCLIENT',NAME:'Не клиент'}}

// https://...bitrix24.ru/bitrix/components/bitrix/catalog.productcard.reserved.deal.list/slider.php?productId=2385&storeId=1&IFRAME=Y&IFRAME_TYPE=SIDE_SLIDER

// https://...bitrix24.ru/shop/settings/menu_catalog_attributes_17/details/111/directory-items/?IFRAME=Y&IFRAME_TYPE=SIDE_SLIDER

/*
BX.ready(
  () => {
    // Момент когда слайдер загрузился
    BX.addCustomEvent("SidePanel.Slider:onLoad", event => {
      // Проверяем, что ссылка введет на карточку пользователя
      if (event.getSlider().getUrl().indexOf('/company/personal/user/') !== -1) {
      }
    }
  }
)
*/
/*
	"crm.contact.userfield.add", 
	{
		fields: 
		{
			"FIELD_NAME": "MY_STRING",
			"EDIT_FORM_LABEL": "Моя строка",
			"LIST_COLUMN_LABEL": "Моя строка",
			"USER_TYPE_ID": "string",
			"XML_ID": "MY_STRING",
			"SETTINGS": { "DEFAULT_VALUE": "Привет, мир!" }
		}
	}

	"crm.contact.userfield.add", 
	{
		fields: 
		{
			"FIELD_NAME": "MY_LIST",
			"EDIT_FORM_LABEL": "Мой список",
			"LIST_COLUMN_LABEL": "Мой список",
			"USER_TYPE_ID": "enumeration",
			"LIST": [ { "VALUE": "Элемент #1" }, { "VALUE": "Элемент #2" }, { "VALUE": "Элемент #3" }, { "VALUE": "Элемент #4" }, { "VALUE": "Элемент #5" } ],
			"XML_ID": "MY_LIST",
			"SETTINGS": { "LIST_HEIGHT": 3 }
		}
	}

{
	fields: 
	{
		"FIELD_NAME": "GEO_REGION",
		"EDIT_FORM_LABEL": "Регион",
		"LIST_COLUMN_LABEL": "Регион",
		"USER_TYPE_ID": "string",
		"XML_ID": "GEO_REGION"
	}
}
*/
</script>
</body>
</html>
