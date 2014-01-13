<?php
/* 	
	GIOV Solution - Keep IT Simple
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<style type="text/css">
        p { width:650px; }
		.search-item {
			font:normal 11px tahoma, arial, helvetica, sans-serif;
			padding:3px 10px 3px 10px;
			border:1px solid #fff;
			border-bottom:1px solid #eeeeee;
			white-space:normal;
			color:#555;
		}
		.search-item h3 {
			display:block;
			font:inherit;
			font-weight:bold;
			color:#222;
		}
		
		.search-item h3 span {
			float: right;
			font-weight:normal;
			margin:0 0 5px 5px;
			width:100px;
			display:block;
			clear:none;
		}
    </style>
<script>

/* declare function */		
var laporan_komisi_sales_DataStore;
var laporan_komisi_sales_DataStore2;
var lap_komisi_sales_new_DataStore;
var laporan_komisi_sales_ColumnModel;
var laporan_komisi_sales_ColumnModel2;
var laporan_komisi_salesListEditorGrid;
var laporan_komisi_salesListEditorGrid2;
var laporan_komisi_sales_saveForm;
var laporan_komisi_sales_saveWindow;
var laporan_komisi_sales_searchForm;
var laporan_komisi_sales_searchWindow;
var laporan_komisi_sales_SelectedRow;
var laporan_komisi_sales_ContextMenu;

//declare konstant
var post2db_laporan_komisi_sales = '';
var msg = '';
var pageS_laporan_komisi_sales=15;
var today=new Date().format('Y-m-d');
var firstday=(new Date().format('Y-m'))+'-01';
/* declare variable here for Field*/

var laporan_komisi_salesField;


/* on ready fuction */
Ext.onReady(function(){
  	Ext.QuickTips.init();	/* Initiate quick tips icon */
  
  	/* Function for get PK field */
	function get_pk_id(){
		if(post2db_laporan_komisi_sales=='UPDATE')
			return laporan_komisi_salesListEditorGrid.getSelectionModel().getSelected().get('supplier_id');
		else 
			return 0;
	}
  	/* End of Function */
	
	/* Function for Retrieve DataStore */
	laporan_komisi_sales_DataStore = new Ext.data.Store({
		id: 'laporan_komisi_sales_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_laporan_komisi_sales&m=get_action', 
			method: 'POST',
			timeout: 3600000
		}),
		baseParams:{task: "LIST", start:0, limit: pageS_laporan_komisi_sales}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: '	',
			id: ''
		},[
		/* dataIndex => insert intohpp_ColumnModel, Mapping => for initiate table column */ 
			{name: 'karyawan_nama', type: 'string', mapping: 'karyawan_nama'},
			{name: 'total_biaya', type: 'float', mapping: 'total_biaya'},
			{name: 'retur', type: 'float', mapping: 'retur'},
			{name: 'total', type: 'float', mapping: 'total'},
			{name: 'komisi', type: 'float', mapping: 'komisi'},
			{name: 'poin', type: 'float', mapping: 'poin'}
		]),
		sortInfo:{field: 'karyawan_nama', direction: "ASC"}
	});
	/* End of Function */
	
	/* Function for Retrieve DataStore */
	laporan_komisi_sales_DataStore2 = new Ext.data.Store({
		id: 'laporan_komisi_sales_DataStore2',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_laporan_komisi_sales&m=get_action', 
			method: 'POST',
			timeout: 3600000
		}),
		baseParams:{task: "LIST", start:0, limit: pageS_laporan_komisi_sales}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: '	',
			id: ''
		},[
		/* dataIndex => insert intohpp_ColumnModel, Mapping => for initiate table column */ 
			{name: 'karyawan_nama', type: 'string', mapping: 'karyawan_nama'},
			{name: 'sum_total_biaya', type: 'float', mapping: 'total_biaya'},
			{name: 'sum_retur', type: 'float', mapping: 'retur'},
			{name: 'sum_total', type: 'float', mapping: 'total'},
			{name: 'sum_komisi', type: 'float', mapping: 'komisi'},
			{name: 'sum_poin', type: 'float', mapping: 'poin'}
		]),
		sortInfo:{field: 'karyawan_nama', direction: "ASC"}
	});
	/* End of Function */

	/* Function for new komisis sales data store */ 
	lap_komisi_sales_new_DataStore = new Ext.data.Store({
		id: 'lap_komisi_sales_new_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_laporan_komisi_sales&m=get_action', 
			method: 'POST'
		}),
		baseParams:{task: "LIST2",start:0,limit:pageS_laporan_komisi_sales}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: ''
		},[
			{name: 'karyawan_nama', type: 'string', mapping: 'karyawan_nama'},
			{name: 'total_biaya', type: 'float', mapping: 'total_biaya'},
			{name: 'retur', type: 'float', mapping: 'retur'},
			{name: 'total', type: 'float', mapping: 'total'},
			{name: 'komisi', type: 'float', mapping: 'komisi'},
			{name: 'poin', type: 'float', mapping: 'poin'}
		]),
		sortInfo:{field: 'karyawan_nama', direction: "ASC"}
	});
	/* End of Function */
		
	//ComboBox ambil data Supplier
	cbo_history_transaksi_customerDataStore = new Ext.data.Store({
		id: 'cbo_history_transaksi_customerDataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_laporan_komisi_sales&m=get_supplier_list', 
			method: 'POST'
		}),
		baseParams:{start: 0, limit:pageS_laporan_komisi_sales }, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'supplier_id'
		},[
		/* dataIndex => insert intocustomer_note_ColumnModel, Mapping => for initiate table column */ 
			{name: 'supplier_id', type: 'int', mapping: 'supplier_id'},
			{name: 'supplier_no', type: 'string', mapping: 'supplier_no'},
			{name: 'supplier_alamat', type: 'string', mapping: 'supplier_alamat'},
			{name: 'supplier_notelp', type: 'string', mapping: 'cust_notelp'}
		]),
		sortInfo:{field: 'supplier_no', direction: "ASC"}
	});
	//Template yang akan tampil di ComboBox
	var customer_history_tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<span><b>{supplier_no} : {supplier_nama}</b> | Tgl-Lahir:{cust_tgllahir:date("M j, Y")}<br /></span>',
            'Alamat: {supplier_alamat}&nbsp;&nbsp;&nbsp;[Telp. {supplier_notelp}]',
        '</div></tpl>'
    );
	
	laporan_komisi_sales_ColumnModel = new Ext.grid.ColumnModel(
		[
		/*
		{
			header: '<div align="center">Tanggal</div>',
			dataIndex: 'tanggal_transaksi',
			renderer: Ext.util.Format.dateRenderer('d-m-Y'),
			width: 70,
			sortable: true,
			readOnly: true
		},
		*/
		{
			header: '<div align="center">Karyawan Nama</div>',
			dataIndex: 'karyawan_nama',
			width: 80,
			sortable: true,
			readOnly: true
		}, 
		{
			header: '<div align="center">Total Penjualan</div>',
			dataIndex: 'total_biaya',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Retur</div>',
			dataIndex: 'retur',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Nett Penjualan</div>',
			dataIndex: 'total',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Poin</div>',
			dataIndex: 'poin',
			align: 'right',
			//renderer: Ext.util.Format.numberRenderer('0,000.00'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Komisi</div>',
			dataIndex: 'komisi',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		}
		
		]);
	
	laporan_komisi_sales_ColumnModel.defaultSortable= true;
	/* End of Function */
	
	laporan_komisi_sales_ColumnModel2 = new Ext.grid.ColumnModel(
		[
		/*
		{
			header: '<div align="center">Tanggal</div>',
			dataIndex: 'tanggal_transaksi',
			renderer: Ext.util.Format.dateRenderer('d-m-Y'),
			width: 70,
			sortable: true,
			readOnly: true
		},
		*/
		{
			header: '<div align="center">Karyawan Nama</div>',
			dataIndex: 'karyawan_nama',
			width: 80,
			sortable: true,
			readOnly: true
		}, 
		{
			header: '<div align="center">Total Penjualan</div>',
			dataIndex: 'sum_total_biaya',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Retur</div>',
			dataIndex: 'sum_retur',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Nett Penjualan</div>',
			dataIndex: 'sum_total',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Poin</div>',
			dataIndex: 'sum_poin',
			align: 'right',
			//renderer: Ext.util.Format.numberRenderer('0,000.00'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Komisi</div>',
			dataIndex: 'sum_komisi',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		}
		
		]);
	
	laporan_komisi_sales_ColumnModel2.defaultSortable= true;
	/* End of Function */


	lap_komisi_sales_newColumnModel = new Ext.grid.ColumnModel(
		[
		/*
		{
			header: '<div align="center">Tanggal</div>',
			dataIndex: 'tanggal_transaksi',
			renderer: Ext.util.Format.dateRenderer('d-m-Y'),
			width: 70,
			sortable: true,
			readOnly: true
		},
		*/
		{
			header: '<div align="center">Karyawan Nama</div>',
			dataIndex: 'karyawan_nama',
			width: 80,
			sortable: true,
			readOnly: true
		}, 
		{
			header: '<div align="center">Total Penjualan</div>',
			dataIndex: 'total_biaya',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Retur</div>',
			dataIndex: 'retur',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Nett Penjualan</div>',
			dataIndex: 'total',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Poin</div>',
			dataIndex: 'poin',
			align: 'right',
			//renderer: Ext.util.Format.numberRenderer('0,000.00'),
			width: 60,
			sortable: true,
			readOnly: true
		},
		{
			header: '<div align="center">Total Komisi</div>',
			dataIndex: 'komisi',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			width: 60,
			sortable: true,
			readOnly: true
		}
		
		]);
	
	lap_komisi_sales_newColumnModel.defaultSortable= true;
	/* End of Function */

    
    
	  	/* Function for Displaying  Search Window Form */
	function display_form_search_window(){
		if(!laporan_komisi_sales_searchWindow.isVisible()){
			laporan_komisi_sales_searchWindow.show();
		} else {
			laporan_komisi_sales_searchWindow.toFront();
		}
	}
  	/* End Function */
	
	history_transaksi_customerField=new Ext.form.TextField({
		id: 'history_transaksi_customerField',
		name: 'history_transaksi_customerField',
		fieldLabel: '<b>Supplier</b>',
		width: 200,
		readOnly: true,
		disabled: true
	});
	
	history_transaksi_custnoField=new Ext.form.TextField({
		id: 'history_transaksi_custnoField',
		name: 'history_transaksi_custnoField',
		fieldLabel: '<b>Cust No</b>',
		width : 80,
		readOnly: true,
		disabled: true,
	});
	
	
	history_transaksi_custalamatField=new Ext.form.TextField({
		id: 'history_transaksi_custalamatField',
		name: 'history_transaksi_custalamatField',
		fieldLabel: '<b>Alamat</b>',
		width: 200,
		readOnly: true
	});
	
	
	history_transaksi_custtelpField=new Ext.form.TextField({
		id: 'history_transaksi_custtelpField',
		name: 'history_transaksi_custtelpField',
		fieldLabel: '<b>Telp</b>',
		width : 120,
		readOnly: true
	});
	

	function rounding(num, dec) {
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	}
	
	/* Declare DataStore and  show datagrid list */
	laporan_komisi_salesListEditorGrid =  new Ext.grid.GridPanel({
		id: 'laporan_komisi_salesListEditorGrid',
		title: 'Daftar Komisi Sales',
		el: 'fp_vu_laporan_komisi_sales',
		autoHeight: true,
		store: laporan_komisi_sales_DataStore, // DataStore
		cm: laporan_komisi_sales_ColumnModel, // Nama-nama Columns
		enableColLock:false,
		frame: true,
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true },
	  	width: 1220,
		autoHeight: true,
		/*bbar: [
			new Ext.PagingToolbar({
			//pageSize: pageS_laporan_komisi_sales,
			store: laporan_komisi_sales_DataStore,
			displayInfo: true
		}),
		],*/tbar: [
		{
			text: 'Search',
			tooltip: 'Advanced Search',
			iconCls:'icon-search',
			handler: display_form_search_window 
		}
		/*,'-'
		,{
			'text':'Supplier : '
		}
		,history_transaksi_customerField
		,
		'-',{
			text: 'Refresh',
			tooltip: 'Refresh datagrid',
			handler: history_transaksi_reset_search,
			iconCls:'icon-refresh'
		},'-',{
			text: 'Export Excel',
			tooltip: 'Export to Excel(.xls) Document',
			iconCls:'icon-xls',
			handler: kartu_stok_export_excel
		}
		*/, '-',{
			text: 'Print',
			tooltip: 'Print Document',
			iconCls:'icon-print',
			handler: kartu_stok_print  
		}]
	});
	
	laporan_komisi_salesListEditorGrid.render();
	laporan_komisi_salesListEditorGrid.show();
	
	// subtotal  
	laporan_komisi_salesListEditorGrid2 =  new Ext.grid.EditorGridPanel({
		id: 'laporan_komisi_salesListEditorGrid2',
		el: 'fp_vu_laporan_komisi_sales2',
		title: '',
		autoHeight: true,
		store: laporan_komisi_sales_DataStore2, // DataStore
		cm: laporan_komisi_sales_ColumnModel2, // Nama-nama Columns
		enableColLock:false,
		frame: true,
		//clicksToEdit:2, // 2xClick untuk bisa meng-Edit inLine Data
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true },
	  	width: 1220, //940,//1200,	//970,
	
		/* Add Control on ToolBar */
	
	});
	//laporan_komisi_salesListEditorGrid2.render();
	//laporan_komisi_salesListEditorGrid2.show();

	//Grid panel for new komisi sales terbaru perhitungan dari NOPI
	lap_komisi_sales_newListEditorGrid =  new Ext.grid.EditorGridPanel({
		id: 'lap_komisi_sales_newListEditorGrid',
		el: 'fp_new_komisi_sales',
		title: 'Daftar Komisi Sales New',
		autoHeight: true,
		store: lap_komisi_sales_new_DataStore, // DataStore
		cm: lap_komisi_sales_newColumnModel, // Nama-nama Columns
		enableColLock:false,
		frame: true,
		//clicksToEdit:2, // 2xClick untuk bisa meng-Edit inLine Data
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true },
	  	width: 1220
	});
	lap_komisi_sales_newListEditorGrid.render();

	
	function is_valid_form(){
			return true;
	}
	
	/* Function for action list search */
	function history_transaksi_list_search(){
		// render according to a SQL date format.
		var tanggal_start_search="";
		var tanggal_end_search="";

		if(is_valid_form()){		
		if(history_transaksi_supplier_tanggal_startSearchField.getValue()!==null){tanggal_start_search=history_transaksi_supplier_tanggal_startSearchField.getValue().format('Y-m-d');}
		if(history_transaksi_supplier_tanggal_endSearchField.getValue()!==null){tanggal_end_search=history_transaksi_supplier_tanggal_endSearchField.getValue().format('Y-m-d');}

		
		laporan_komisi_sales_DataStore.baseParams = {
			task			: 'LIST',
			tanggal_start	:	tanggal_start_search, 
			tanggal_end		:	tanggal_end_search,
		};
		// laporan_komisi_sales_DataStore2.baseParams = {
		// 	task			: 'LIST2',
		// 	tanggal_start	:	tanggal_start_search, 
		// 	tanggal_end		:	tanggal_end_search,
		// };
		lap_komisi_sales_new_DataStore.baseParams = {
			task        	: 'LIST2',
			tanggal_start 	: tanggal_start_search,
			tanggal_end 	: tanggal_end_search

		};
		// Cause the datastore to do another query : 
		/*Ext.MessageBox.show({
		   msg: 'Sedang memproses data, mohon tunggu...',
		   progressText: 'proses...',
		   width:350,
		   wait:true
		});*/
		
		laporan_komisi_sales_DataStore.reload({params: {start: 0, limit: pageS_laporan_komisi_sales}});;
		lap_komisi_sales_new_DataStore.reload({params: {start: 0, limit: pageS_laporan_komisi_sales}});;
		// laporan_komisi_sales_DataStore2.reload({params: {start: 0, limit: pageS_laporan_komisi_sales}});;
		
		}else{
			Ext.MessageBox.show({
				title: 'Warning',
				msg: 'Form anda belum lengkap',
				buttons: Ext.MessageBox.OK,
				animEl: 'search',
				icon: Ext.MessageBox.WARNING
			});
		}
	}
		
	/* Function for reset search result */
	function history_transaksi_reset_search(){
		// reset the store parameters
		laporan_komisi_sales_DataStore.baseParams = { task: 'LIST' };
		// Cause the datastore to do another query : 
		laporan_komisi_sales_DataStore.reload({params: {start: 0, limit: 0}});
		laporan_komisi_sales_searchWindow.close();
	};
	/* End of Fuction */
	
	/* Field for search */

	laporan_komisi_salesField= new Ext.form.ComboBox({
		fieldLabel: 'Supplier',
		store: cbo_history_transaksi_customerDataStore,
		mode: 'remote',
		displayField:'supplier_nama',
		valueField: 'supplier_id',
        typeAhead: false,
        loadingText: 'Searching...',
        pageSize:10,
        hideTrigger:false,
        tpl: customer_history_tpl,
        itemSelector: 'div.search-item',
		triggerAction: 'all',
		lazyRender:true,
		listClass: 'x-combo-list-small',
		allowBlank: false,
		disabled:false,
		anchor: '90%'
	});
	

	/* Identify  jenis Combo*/
	jenis_supplier_searchingField= new Ext.form.ComboBox({
		id: 'jenis_supplier_searchingField',
		fieldLabel: 'Jenis',
		store:new Ext.data.SimpleStore({
			fields:['jenis_searching_value', 'jenis_searching_display'],
			data:[['Produk','Produk']]
		}),
		mode: 'local',
		editable:false,
		emptyText: 'Produk',
		displayField: 'jenis_searching_display',
		valueField: 'jenis_searching_value',
		hidden: true,
		width: 150,
		triggerAction: 'all'	
	});
	
	
	history_transaksi_supplier_tanggal_startSearchField=new Ext.form.DateField({
		id: 'history_transaksi_supplier_tanggal_startSearchField',
		fieldLabel: 'Tanggal',
		format: 'd-m-Y',		
		value: firstday
	});
    
	history_transaksi_supplier_tanggal_endSearchField=new Ext.form.DateField({
		id: 'history_transaksi_supplier_tanggal_endSearchField',
		fieldLabel: 's/d',
		format: 'd-m-Y',
		value: today
	});

	
	history_transaksi_supplier_label_tanggalField=new Ext.form.Label({ html: ' &nbsp; s/d  &nbsp;'});
	
	history_transaksi_supplier_tanggal_opsiSearchField=new Ext.form.FieldSet({
		id:'history_transaksi_supplier_tanggal_opsiSearchField',
		title: 'Opsi Tanggal',
		layout: 'column',
		boduStyle: 'padding: 5px;',
		frame: false,
		items:[history_transaksi_supplier_tanggal_startSearchField, history_transaksi_supplier_label_tanggalField, history_transaksi_supplier_tanggal_endSearchField]
	});

	
	/* Function for retrieve search Form Panel */
	laporan_komisi_sales_searchForm = new Ext.FormPanel({
		labelAlign: 'left',
		bodyStyle:'padding:5px',
		autoHeight:true,
		width: 450,        
		items: [{
			layout:'column',
			border:false,
			items:[
			{
				columnWidth: 1,
				layout: 'form',
				border:false,
				items: [history_transaksi_supplier_tanggal_opsiSearchField] 
			}
			]
		}]
		,
		buttons: [{
				text: 'Search',
				handler: history_transaksi_list_search
			},{
				text: 'Close',
				handler: function(){
					laporan_komisi_sales_searchWindow.hide();
				}
			}
		]
	});
    /* End of Function */ 
	 
	/* Function for retrieve search Window Form, used for andvaced search */
	laporan_komisi_sales_searchWindow = new Ext.Window({
		title: 'Pencarian Daftar Komisi Sales',
		closable:true,
		closeAction: 'hide',
		autoWidth: true,
		autoHeight: true,
		plain:true,
		layout: 'fit',
		x: 0,
		y: 0,
		modal: true,
		renderTo: 'elwindow_laporan_komisi_sales_search',
		items: laporan_komisi_sales_searchForm
	});
    /* End of Function */ 
	 
	function reset_search_form(){
		jenis_supplier_searchingField.reset();;
		jenis_supplier_searchingField.setValue(null);
		laporan_komisi_salesField.reset();
		laporan_komisi_salesField.setValue(null);
		
	}
	
  	/* Function for Displaying  Search Window Form */
	function display_form_search_window(){
		reset_search_form();
		if(!laporan_komisi_sales_searchWindow.isVisible()){
			laporan_komisi_sales_searchWindow.show();
		} else {
			laporan_komisi_sales_searchWindow.toFront();
		}
	}
  	/* End Function */
	
	/* Function for print List Grid */
	function kartu_stok_print(){
		// render according to a SQL date format.
	
		//var searchquery = "";
		var tanggal_start_print="";
		var tanggal_end_print="";
		var win;              
		// check if we do have some search data...
		if(is_valid_form()){		
		if(history_transaksi_supplier_tanggal_startSearchField.getValue()!==null){tanggal_start_print=history_transaksi_supplier_tanggal_startSearchField.getValue().format('Y-m-d');}
		if(history_transaksi_supplier_tanggal_endSearchField.getValue()!==null){tanggal_end_print=history_transaksi_supplier_tanggal_endSearchField.getValue().format('Y-m-d');}
		}
		
		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_laporan_komisi_sales&m=get_action',
		params: {
			task: "PRINT",
			task			: 'PRINT',
			tanggal_start	: tanggal_start_print, 
			tanggal_end		: tanggal_end_print,
		}, 
		success: function(response){              
		  	var result=eval(response.responseText);
		  	switch(result){
		  	case 1:
				win = window.open('./print/laporan_komisi_printlist.html','laporan_komisilist','height=400,width=600,resizable=1,scrollbars=1, menubar=1');
				win.print();
				break;
		  	default:
				Ext.MessageBox.show({
					title: 'Warning',
					msg: 'Unable to print the grid!',
					buttons: Ext.MessageBox.OK,
					animEl: 'save',
					icon: Ext.MessageBox.WARNING
				});
				break;
		  	}  
		},
		failure: function(response){
		  	var result=response.responseText;
			Ext.MessageBox.show({
			   title: 'Error',
			   msg: 'Could not connect to the database. retry later.',
			   buttons: Ext.MessageBox.OK,
			   animEl: 'database',
			   icon: Ext.MessageBox.ERROR
			});		
		} 	                     
		});
	}
	/* Enf Function */
	
	/* Function for print Export to Excel Grid */
	function kartu_stok_export_excel(){
		var searchquery = "";
		var produk_id_2excel=null;
		var produk_nama_2excel=null;
		var satuan_id_2excel=null;
		var satuan_nama_2excel=null;
		var stok_saldo_2excel=null;
		var win;              
		// check if we do have some search data...
		if(laporan_komisi_sales_DataStore.baseParams.query!==null){searchquery = laporan_komisi_sales_DataStore.baseParams.query;}
		if(laporan_komisi_sales_DataStore.baseParams.produk_id!==null){produk_id_2excel = laporan_komisi_sales_DataStore.baseParams.produk_id;}
		if(laporan_komisi_sales_DataStore.baseParams.produk_nama!==null){produk_nama_2excel = laporan_komisi_sales_DataStore.baseParams.produk_nama;}
		if(laporan_komisi_sales_DataStore.baseParams.satuan_id!==null){satuan_id_2excel = laporan_komisi_sales_DataStore.baseParams.satuan_id;}
		if(laporan_komisi_sales_DataStore.baseParams.satuan_nama!==null){satuan_nama_2excel = laporan_komisi_sales_DataStore.baseParams.satuan_nama;}
		if(laporan_komisi_sales_DataStore.baseParams.stok_saldo!==null){stok_saldo_2excel = laporan_komisi_sales_DataStore.baseParams.stok_saldo;}

		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_laporan_komisi_sales&m=get_action',
		params: {
			task: "EXCEL",
		  	query: searchquery,                    		// if we are doing a quicksearch, use this
			//if we are doing advanced search, use this
			produk_id : produk_id_2excel,
			produk_nama : produk_nama_2excel,
			satuan_id : satuan_id_2excel,
			satuan_nama : satuan_nama_2excel,
			stok_saldo : stok_saldo_2excel,
		  	currentlisting: laporan_komisi_sales_DataStore.baseParams.task // this tells us if we are searching or not
		},
		success: function(response){              
		  	var result=eval(response.responseText);
		  	switch(result){
		  	case 1:
				win = window.location=('./export2excel.php');
				break;
		  	default:
				Ext.MessageBox.show({
					title: 'Warning',
					msg: 'Unable to convert excel the grid!',
					buttons: Ext.MessageBox.OK,
					animEl: 'save',
					icon: Ext.MessageBox.WARNING
				});
				break;
		  	}  
		},
		failure: function(response){
		  	var result=response.responseText;
			Ext.MessageBox.show({
			   title: 'Error',
			   msg: 'Could not connect to the database. retry later.',
			   buttons: Ext.MessageBox.OK,
			   animEl: 'database',
			   icon: Ext.MessageBox.ERROR
			});    
		} 	                     
		});
	}
	/*End of Function */

	laporan_komisi_sales_searchWindow.show();
	
});
	</script>
<body>
<div>
	<div class="col">
        <div id="fp_laporan_komisi_sales"></div>
         <div id="fp_vu_laporan_komisi_sales"></div>
         <div id="fp_vu_laporan_komisi_sales2"></div>
		<div id="elwindow_laporan_komisi_sales_save"></div>
		<div id="fp_new_komisi_sales"></div>
        <div id="elwindow_laporan_komisi_sales_search"></div>
    </div>
</div>
</body>
</html>