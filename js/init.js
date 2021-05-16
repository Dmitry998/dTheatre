
var actorsForSpectacle = [];
Ext.onReady(function(){

    store = new Ext.data.JsonStore({
		url:'../selectActorsForSpectacle.php',
		totalProperty: 'totalCount',
		root: 'items',
		fields:[
            {name:'id'},
			{name:'name'},
			{name:'surname'},
			{name:'age'},
			{name:'experience'}
		],
		remoteSort:true
	});

    store.load();

    var cm = new Ext.grid.ColumnModel({
		columns:[
			{header:"<center>id</center>", align:"center", width:144, dataIndex:'id'},
			{header:"<center>Фамилия</center>",align:"center", width:217, dataIndex:'surname'},
            {header:"<center>Имя</center>",align:"center", width:217, dataIndex:'name'},
            {header:"<center>Возраст</center>",align:"center", width:144, dataIndex:'age'},
            {header:"<center>Стаж</center>",align:"center", width:144, dataIndex:'experience'},
		]
	}); 
    
    var selectModel = new Ext.grid.CheckboxSelectionModel({
		width:25, 
		header:'<div style="background:#F0F1F3; height:90%;></div>',
		listeners: {
			rowselect: function(sm, row, rec) {								// строка grid выбрана
				id = rec.data.id;
			},
			rowdeselect: function(sm, row, rec) {							// не выбрана ни одна строка
				id = '';
			}
		}
	});

    grid = new Ext.grid.EditorGridPanel({
        title: 'Выберите актеров, которые будут играть в спектакле',
		store	: store,
		cm		: cm,
        height	: 500,
        sm: selectModel,
		enableColLock: false,
		loadMask:{msg:'Идет загрузка данных...'}, 
		maskDisabled: false,
		loadMask: true,
		сollapsible: false,
		frame:true
	});

    grid.render('tabs1');
});