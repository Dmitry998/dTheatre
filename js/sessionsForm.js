var store;
var dateTimeSpectacle;
var priceFor1Sector;
var priceFor2Sector;
var priceFor3Sector;

var id;
var form;

Ext.onReady(function () {

    store = new Ext.data.JsonStore({
        url: '../selectSessionsForSpectacle.php',
        totalProperty: 'totalCount',
        root: 'items',
        fields: [
            { name: 'id' },
            { name: 'date' },
            { name: 'priceSector1'},
            { name: 'priceSector2'},
            { name: 'priceSector3'},
        ],
        remoteSort: true
    });

    store.load();

    var cm = new Ext.grid.ColumnModel({
        columns: [
            { header: "<center>id</center>", align: "center", hiden: true, width: 50, dataIndex: 'id' },
            { header: "<center>Дата и время сеанса</center>", align: "center", width: 201, dataIndex: 'date' },
            { header: "<center>1 секция</center>", align: "center", width: 133, dataIndex: 'priceSector1' },
            { header: "<center>2 секция</center>", align: "center", width: 133, dataIndex: 'priceSector2' },
            { header: "<center>3 секция</center>", align: "center", width: 133, dataIndex: 'priceSector3' },
        ]
    });


    priceFor1Sector = new Ext.form.NumberField({
        fieldLabel: 'Цена билетов 1 сектора (1-3 ряд)',
        name: 'priceForSpectacle',
        allowNegative: false,
        allowDecimal: false,
        width: 214,
        value: 3500
    });

    priceFor2Sector = new Ext.form.NumberField({
        fieldLabel: 'Цена билетов 1 сектора (1-3 ряд)',
        name: 'priceForSpectacle',
        allowNegative: false,
        allowDecimal: false,
        width: 214,
        value: 2000
    });

    priceFor3Sector = new Ext.form.NumberField({
        fieldLabel: 'Цена билетов 1 сектора (1-3 ряд)',
        name: 'priceForSpectacle',
        allowNegative: false,
        allowDecimal: false,
        width: 214,
        value: 1000
    });

    dateTimeSpectacle = new Ext.form.DateField({
        name: 'dateTimeSpectacle',
        fieldLabel: 'Дата и время спектакля',
        width: 150,
        format: 'd.m.Y H:i',
        value: new Date()
    });

    var selectModel = new Ext.grid.CheckboxSelectionModel({
        width: 25,
        header: '<div style="background:#F0F1F3; height:90%;></div>',
        listeners: {
            rowselect: function (sm, row, rec) {								// строка grid выбрана
                id = rec.data.id;
                console.log(rec.data.date);
                dateTimeSpectacle.setValue(rec.data.date);
                priceFor1Sector.setValue(rec.data.priceSector1);
                priceFor2Sector.setValue(rec.data.priceSector2);
                priceFor3Sector.setValue(rec.data.priceSector3);
            },
            rowdeselect: function (sm, row, rec) {							// не выбрана ни одна строка
                id = '';
            }
        }
    });

    var buttonDelete = new Ext.Button({
        text: 'Удалить запись',
        style: 'padding: 10px 5px 10px 5px',
        handler: deleteSession
    });
    var buttonAdd = new Ext.Button({
        text: 'Добавить запись',
        style: 'padding: 10px 5px 10px 5px',
        handler: addSession
    });
    var buttonChange = new Ext.Button({
        text: 'Изменить запись',
        style: 'padding: 10px 5px 10px 5px',
        handler: changeSession
    });


    /*
        var tip = new Ext.ux.SliderTip({
        getText: function (slider) {
            return String.format('<b>{0} рублей </b>', slider.getValue());
        }
    });

    var priceFor1Sector = new Ext.Slider({
        fieldLabel: 'Цена билетов 1 сектора (1-3 ряд)',
        width: 214,
        minValue: 100,
        maxValue: 5000,
        value: 3500,
        plugins:tip
    });

    var priceFor2Sector =  new Ext.Slider({
        fieldLabel: 'Цена билетов 2 сектора (4-6 ряд)',
        width: 214,
        minValue: 100,
        maxValue: 5000,
        value: 2000,
        plugins:tip
    });

    var priceFor3Sector =  new Ext.Slider({
        fieldLabel: 'Цена билетов 3 сектора (7-9 ряд)',
        width: 214,
        minValue: 100,
        maxValue: 5000,
        value: 1000,
        plugins: tip
    });*/

    grid = new Ext.grid.EditorGridPanel({
        store: store,
        cm: cm,
        width: '98%',
        height: 200,
        sm: selectModel,
        enableColLock: false,
        loadMask: { msg: 'Идет загрузка данных...' },
        maskDisabled: false,
        loadMask: true,
        сollapsible: false,
    });

    grid.on('dblclick', function(){
        document.location.href = "../pages/session.php?session_id="+id;//document.location.href
    })

    var fieldSet = new Ext.form.FieldSet({
        width: '95%',
        items: [dateTimeSpectacle, priceFor1Sector, priceFor2Sector, priceFor3Sector],
        buttons: [buttonDelete, buttonChange, buttonAdd],
        buttonAlign: 'center'
    })

    form = new Ext.FormPanel({
        url: 'save-form.php',
        frame: true,
        title: 'Сеансы',
        width: 720,
        height: 500,
        items: [grid, fieldSet],
    });

    form.render('sessions');
});

function deleteSession(){
    Ext.Ajax.request({
        url: '../deleteSession.php',
        method: 'post',
        params: { id: id },
        callback: function (opts, suss, resp) {
            var resp = Ext.decode(resp.responseText);
            if (resp.success == 1) {
                Ext.Msg.show({
                    title: 'Сообщение',
                    msg: 'Сеанс удален',
                    buttons: Ext.Msg.OK,
                    modal: true,
                    icon: Ext.MessageBox.INFO
                });
                store.load();
            }
        }
    });
}

function changeSession(){
    
    if(!id || id == ''){
        Ext.Msg.show({
            title: 'Ошибка!',
            msg: 'Не выбрана запись для изменения',
            buttons: Ext.Msg.OK,
            modal: true,
            icon: Ext.MessageBox.INFO
        });
        return;
    }
    var dateTime = dateTimeSpectacle.getValue();
    var price1 = priceFor1Sector.getValue();
    var price2 = priceFor2Sector.getValue();
    var price3 = priceFor3Sector.getValue();

    var year = dateTime.getFullYear();
    var month = dateTime.getMonth() + 1;

    console.log(month);
    var day = dateTime.getDate();
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    var hour = dateTime.getHours();
    var minutes = dateTime.getMinutes();
    dateTime = `${year}-${month}-${day} ${hour}:${minutes}:00`;
    form.setTitle('Обновление данных...');

    Ext.Ajax.request({
        url: '../changeSession.php',
        method: 'post',
        params: { id: id, dateTime: dateTime, price1: price1, price2: price2, price3: price3 },
        callback: function (opts, suss, resp) {
            var resp = Ext.decode(resp.responseText);
            if (resp.success == 1) {
                Ext.Msg.show({
                    title: 'Сообщение',
                    msg: 'Сеанс обновлен',
                    buttons: Ext.Msg.OK,
                    modal: true,
                    icon: Ext.MessageBox.INFO
                });
                store.load();
                form.setTitle('Сеансы');
            }
            else{
                Ext.Msg.show({
                    title: 'Сообщение',
                    msg: 'Не удалось обновить сеанс',
                    buttons: Ext.Msg.OK,
                    modal: true,
                    icon: Ext.MessageBox.INFO
                });
                store.load();
                form.setTitle('Сеансы');
            }
        }
    });
}

function addSession() {
    var dateTime = dateTimeSpectacle.getValue();
    var price1 = priceFor1Sector.getValue();
    var price2 = priceFor2Sector.getValue();
    var price3 = priceFor3Sector.getValue();

    var year = dateTime.getFullYear();
    var month = dateTime.getMonth() + 1;
    
    console.log(month);
    var day = dateTime.getDate();
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }

    var hour = dateTime.getHours();
    var minutes = dateTime.getMinutes();

    dateTime = `${year}-${month}-${day} ${hour}:${minutes}:00`;
    form.setTitle('Обновление данных...');
    console.log(dateTime);
    Ext.Ajax.request({
        url: '../addSession.php',
        method: 'post',
        params: { dateTime: dateTime, price1: price1, price2: price2, price3: price3 },
        callback: function (opts, suss, resp) {
            var resp = Ext.decode(resp.responseText);
            if (resp.success == 1) {
                Ext.Msg.show({
                    title: 'Сообщение',
                    msg: 'Сеанс добавлен',
                    buttons: Ext.Msg.OK,
                    modal: true,
                    icon: Ext.MessageBox.INFO
                });
                store.load();
                form.setTitle('Сеансы');
            }
            else {
                Ext.Msg.show({
                    title: 'Сообщение',
                    msg: 'Не удалось добавить сеанс',
                    buttons: Ext.Msg.OK,
                    modal: true,
                    icon: Ext.MessageBox.INFO
                });
                form.setTitle('Сеансы');
            }
        }
    });

}

/*Ext.onReady(function(){

    function formatDate(value){
        return value ? value.dateFormat('M d, Y') : '';
    }

    // shorthand alias
    var fm = Ext.form;

    var checkColumn = new Ext.grid.CheckColumn({
       header: 'Indoor?',
       dataIndex: 'indoor',
       width: 55
    });

    // the column model has information about grid columns
    // dataIndex maps the column to the specific data field in
    // the data store (created below)

    var cm = new Ext.grid.ColumnModel({
        // specify any defaults for each column
        defaults: {
            sortable: true // columns are not sortable by default
        },
        columns: [
            {
                id: 'dateTimeSeans',
                header: 'Дата и время сеанса',
                dataIndex: 'dateTimeSeans',
                width: 220,
                editor: new fm.DateField({
                    format: 'm-d-y h:i:s',
                })
            },
            checkColumn // the plugin instance
        ]
    });

    // create the Data Store
    var store = new Ext.data.Store({
    });

    // create the editor grid
    var grid = new Ext.grid.EditorGridPanel({
        store: store,
        cm: cm,
        renderTo: 'sessions',
        width: 600,
        height: 300,
        autoExpandColumn: 'dateTimeSeans', // column with this id will be expanded
        title: 'Сеансы спектакля',
        frame: true,
        // specify the check column plugin on the grid so the plugin is initialized
        plugins: checkColumn,
        clicksToEdit: 1,
        tbar: [{
            text: 'Add Plant',
            handler : function(){
                // access the Record constructor through the grid's store
                var Plant = grid.getStore().recordType;
                var p = new Plant({
                    common: 'New Plant 1',
                    light: 'Mostly Shade',
                    price: 0,
                    availDate: (new Date()).clearTime(),
                    indoor: false
                });
                grid.stopEditing();
                store.insert(0, p);
                grid.startEditing(0, 0);
            }
        }]
    });

    // manually trigger the data store load
    store.load({
        // store loading is asynchronous, use a load listener or callback to handle results
        callback: function(){
            Ext.Msg.show({
                title: 'Store Load Callback',
                msg: 'store was loaded, data available for processing',
                modal: false,
                icon: Ext.Msg.INFO,
                buttons: Ext.Msg.OK
            });
        }
    });
});*/