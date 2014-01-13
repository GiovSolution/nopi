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
var history_transaksi_supplier_DataStore;
var history_transaksi_supplier_ColumnModel;
var history_transaksi_supplierListEditorGrid;
var history_transaksi_supplier_saveForm;
var history_transaksi_suppliersaveWindow;
var history_transaksi_suppliersearchForm;
var history_transaksi_supplier_searchWindow;
var history_transaksi_supplier_SelectedRow;
var history_transaksi_supplier_ContextMenu;

//declare konstant
var post2db_history_supplier = '';
var msg = '';
var pageS_history_supplier=15;
var today=new Date().format('Y-m-d');
var firstday=(new Date().format('Y-m'))+'-01';
/* declare variable here for Field*/

var history_transaksi_supplierField;


/* on ready fuction */
Ext.onReady(function(){
  	Ext.QuickTips.init();	/* Initiate quick tips icon */
  
  	/* Function for get PK field */
	function get_pk_id(){
		if(post2db_history_supplier=='UPDATE')
			return history_transaksi_supplierListEditorGrid.getSelectionModel().getSelected().get('supplier_id');
		else 
			return 0;
	}
  	/* End of Function */
	
	/* Function for Retrieve DataStore */
	history_transaksi_supplier_DataStore = new Ext.data.Store({
		id: 'history_transaksi_supplier_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_history_transaksi_supplier&m=get_action', 
			method: 'POST',
			timeout: 3600000
		}),
		baseParams:{task: "LIST", start:0, limit: pageS_history_supplier}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: ''
		},[
		/* dataIndex => insert intohpp_ColumnModel, Mapping => for initiate table column */ 
			//{name: 'supplier_no', type: 'string', mapping: 'supplier_no'},
			{name: 'supplier_id', type: 'int', mapping: 'supplier_id'},
			{name: 'customer_nama', type: 'string', mapping: 'supplier_nama'},
			//{name: 'referal', type: 'string', mapping: 'referal'},
			//{name: 'customer_member', type: 'string', mapping: 'member_no'},
			{name: 'supplier_alamat', type: 'string', mapping: 'supplier_alamat'},
			{name: 'tanggal_transaksi', type: 'date', dateFormat: 'Y-m-d', mapping: 'tanggal_transaksi'},
			{name: 'no_bukti', type: 'string', mapping: 'no_bukti'},
			{name: 'keterangan', type: 'string', mapping: 'keterangan'},
			{name: 'jumlah_transaksi', type: 'int', mapping: 'jumlah_transaksi'},
			{name: 'harga', type: 'float', mapping: 'harga'},
			{name: 'subtotal', type: 'float', mapping: 'subtotal'},
			{name: 'diskon', type: 'int', mapping: 'diskon'},
			{name: 'kode_transaksi', type: 'string', mapping: 'kode_transaksi'}
		]),
		sortInfo:{field: 'supplier_id', direction: "ASC"}
	});
	/* End of Function */

		
	//ComboBox ambil data Supplier
	cbo_history_transaksi_customerDataStore = new Ext.data.Store({
		id: 'cbo_history_transaksi_customerDataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_history_transaksi_supplier&m=get_supplier_list', 
			method: 'POST'
		}),
		baseParams:{start: 0, limit:pageS_history_supplier }, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'supplier_id'
		},[
		/* dataIndex => insert intocustomer_note_ColumnModel, Mapping => for initiate table column */ 
			{name: 'supplier_id', type: 'int', mapping: 'supplier_id'},
			{name: 'supplier_no', type: 'string', mapping: 'supplier_no'},
			{name: 'supplier_nama', type: 'string', mapping: 'supplier_nama'},
			//{name: 'cust_tgllahir', type: 'date', dateFormat: 'Y-m-d', mapping: 'supplier_tgllahir'},
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
	
	history_transaksi_supplier_ColumnModel = new Ext.grid.ColumnModel(
		[
		{
			header: '<div align="center">Tanggal</div>',
			dataIndex: 'tanggal_transaksi',
			renderer: Ext.util.Format.dateRenderer('d-m-Y'),
			width: 60,
			sortable: true,
			readOnly: true
		}, 
		{
			header: '<div align="center">No Faktur</div>',
			dataIndex: 'no_bukti',
			width: 60,
			sortable: true,
			readOnly: true
		}, 
		
		{
			header: '<div align="center">Keterangan</div>',
			dataIndex: 'keterangan',
			width: 200,
			sortable: true,
			//hidden: true,
			readOnly: true
		}, 
		
		{
			header: '<div align="center">Kode</div>',
			dataIndex: 'kode_transaksi',
			width: 50,
			sortable: true,
			readOnly: true
		}, 
		
		{
			header: '<div align="center">Jumlah</div>',
			dataIndex: 'jumlah_transaksi',
			align: 'right',
			//renderer: Ext.util.Format.numberRenderer('0,000.00'),
			width: 60,
			sortable: true,
			readOnly: true
		},

		{
			align : 'Right',
			header: '<div align="center">' + 'Harga (Rp)' + '</div>',
			dataIndex: 'harga',
			width: 80,
			sortable: false
		},
		{
			align : 'Right',
			header: '<div align="center">' + 'Disk' + '</div>',
			dataIndex: 'diskon',
			width: 80,
			sortable: false
		},

		{
			align : 'Right',
			header: '<div align="center">' + 'Sub Total' + '</div>',
			dataIndex: 'subtotal',
			width: 80,
			sortable: false,
			renderer: Ext.util.Format.numberRenderer('0,000')
		}

		/*
		{
			header: '<div align="center">Sales</div>',
			dataIndex: 'referal',
			width: 100,
			sortable: true,
			readOnly: true
		}
		*/
		
		]);
	
	history_transaksi_supplier_ColumnModel.defaultSortable= true;
	/* End of Function */
    
	  	/* Function for Displaying  Search Window Form */
	function display_form_search_window(){
		if(!history_transaksi_supplier_searchWindow.isVisible()){
			history_transaksi_supplier_searchWindow.show();
		} else {
			history_transaksi_supplier_searchWindow.toFront();
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
	history_transaksi_supplierListEditorGrid =  new Ext.grid.GridPanel({
		id: 'history_transaksi_supplierListEditorGrid',
		title: 'Daftar Detail Transaksi Supplier',
		el: 'fp_vu_history_transaksi_supplier',
		autoHeight: true,
		store: history_transaksi_supplier_DataStore, // DataStore
		cm: history_transaksi_supplier_ColumnModel, // Nama-nama Columns
		enableColLock:false,
		frame: true,
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true },
	  	width: 1220,
		autoHeight: true,
		/*bbar: [
			new Ext.PagingToolbar({
			//pageSize: pageS_history_supplier,
			store: history_transaksi_supplier_DataStore,
			displayInfo: true
		}),
		],*/tbar: [
		{
			text: 'Search',
			tooltip: 'Advanced Search',
			iconCls:'icon-search',
			handler: display_form_search_window 
		},'-'
		,{
			'text':'Supplier : '
		}
		,history_transaksi_customerField
		/*,
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
		}, '-',{
			text: 'Print',
			tooltip: 'Print Document',
			iconCls:'icon-print',
			handler: kartu_stok_print  
		}*/]
	});
	
	history_transaksi_supplierListEditorGrid.render();
	history_transaksi_supplierListEditorGrid.show();
	
	function is_valid_form(){
		if(history_transaksi_supplierField.getValue()!=="")
		{
			return true;
		}else
			return false;
	}
	
	/* Function for action list search */
	function history_transaksi_list_search(){
		// render according to a SQL date format.
		var cust_nama_search=null;
		var jenis_search=null;
		var tanggal_start_search="";
		var tanggal_end_search="";

		if(is_valid_form()){
		
		if(history_transaksi_supplierField.getValue()!==null){cust_nama_search=history_transaksi_supplierField.getValue();}
		if(history_transaksi_supplier_tanggal_startSearchField.getValue()!==null){tanggal_start_search=history_transaksi_supplier_tanggal_startSearchField.getValue().format('Y-m-d');}
		if(history_transaksi_supplier_tanggal_endSearchField.getValue()!==null){tanggal_end_search=history_transaksi_supplier_tanggal_endSearchField.getValue().format('Y-m-d');}
		if(jenis_supplier_searchingField.getValue()!==null){jenis_search=jenis_supplier_searchingField.getValue();}
		
		cbo_history_transaksi_customerDataStore.load({
		 	params:{jenis: jenis_search },
		 	callback: function(r,opt,success){
				if(success==true){
					var j=cbo_history_transaksi_customerDataStore.findExact('supplier_id',history_transaksi_supplierField.getValue(),0);
					if(j>-1){
						var cust_record=cbo_history_transaksi_customerDataStore.getAt(j);
						history_transaksi_customerField.setValue(cust_record.data.supplier_nama);
						history_transaksi_custnoField.setValue(cust_record.data.supplier_no);
						history_transaksi_custalamatField.setValue(cust_record.data.supplier_alamat);
						history_transaksi_custtelpField.setValue(cust_record.data.supplier_notelp);
					}
				}
			}
		});
		
		history_transaksi_supplier_DataStore.baseParams = {
			task			: 'LIST',
			supplier_id		:	cust_nama_search, 
			tanggal_start	:	tanggal_start_search, 
			tanggal_end		:	tanggal_end_search,
			jenis			:	jenis_search
		};
		// Cause the datastore to do another query : 
		/*Ext.MessageBox.show({
		   msg: 'Sedang memproses data, mohon tunggu...',
		   progressText: 'proses...',
		   width:350,
		   wait:true
		});*/
		
		history_transaksi_supplier_DataStore.reload({params: {start: 0, limit: pageS_history_supplier}});;
		
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
		history_transaksi_supplier_DataStore.baseParams = { task: 'LIST' };
		// Cause the datastore to do another query : 
		history_transaksi_supplier_DataStore.reload({params: {start: 0, limit: 0}});
		history_transaksi_supplier_searchWindow.close();
	};
	/* End of Fuction */
	
	/* Field for search */

	history_transaksi_supplierField= new Ext.form.ComboBox({
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
	history_transaksi_suppliersearchForm = new Ext.FormPanel({
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
				items: [jenis_supplier_searchingField,history_transaksi_supplierField, history_transaksi_supplier_tanggal_opsiSearchField] 
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
					history_transaksi_supplier_searchWindow.hide();
				}
			}
		]
	});
    /* End of Function */ 
	 
	/* Function for retrieve search Window Form, used for andvaced search */
	history_transaksi_supplier_searchWindow = new Ext.Window({
		title: 'Pencarian Detail Transaksi Supplier',
		closable:true,
		closeAction: 'hide',
		autoWidth: true,
		autoHeight: true,
		plain:true,
		layout: 'fit',
		x: 0,
		y: 0,
		modal: true,
		renderTo: 'elwindow_history_transaksi_supplier_search',
		items: history_transaksi_suppliersearchForm
	});
    /* End of Function */ 
	 
	function reset_search_form(){
		jenis_supplier_searchingField.reset();;
		jenis_supplier_searchingField.setValue(null);
		history_transaksi_supplierField.reset();
		history_transaksi_supplierField.setValue(null);
		
	}
	
  	/* Function for Displaying  Search Window Form */
	function display_form_search_window(){
		reset_search_form();
		
		if(!history_transaksi_supplier_searchWindow.isVisible()){
			history_transaksi_supplier_searchWindow.show();
		} else {
			history_transaksi_supplier_searchWindow.toFront();
		}
	}
  	/* End Function */
	
	/* Function for print List Grid */
	function kartu_stok_print(){
		var searchquery = "";
		var produk_id_print=null;
		var produk_nama_print=null;
		var satuan_id_print=null;
		var satuan_nama_print=null;
		var stok_saldo_print=null;
		var win;              
		// check if we do have some search data...
		if(history_transaksi_supplier_DataStore.baseParams.query!==null){searchquery = history_transaksi_supplier_DataStore.baseParams.query;}
		if(history_transaksi_supplier_DataStore.baseParams.produk_id!==null){produk_id_print = history_transaksi_supplier_DataStore.baseParams.produk_id;}
		if(history_transaksi_supplier_DataStore.baseParams.produk_nama!==null){produk_nama_print = history_transaksi_supplier_DataStore.baseParams.produk_nama;}
		if(history_transaksi_supplier_DataStore.baseParams.satuan_id!==null){satuan_id_print = history_transaksi_supplier_DataStore.baseParams.satuan_id;}
		if(history_transaksi_supplier_DataStore.baseParams.satuan_nama!==null){satuan_nama_print = history_transaksi_supplier_DataStore.baseParams.satuan_nama;}
		if(history_transaksi_supplier_DataStore.baseParams.stok_saldo!==null){stok_saldo_print = history_transaksi_supplier_DataStore.baseParams.stok_saldo;}
		
		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_history_transaksi_supplier&m=get_action',
		params: {
			task: "PRINT",
		  	query: searchquery,                    		// if we are doing a quicksearch, use this
			//if we are doing advanced search, use this
			produk_id : produk_id_print,
			produk_nama : produk_nama_print,
			satuan_id : satuan_id_print,
			satuan_nama : satuan_nama_print,
			stok_saldo : stok_saldo_print,
		  	currentlisting: history_transaksi_supplier_DataStore.baseParams.task // this tells us if we are searching or not
		}, 
		success: function(response){              
		  	var result=eval(response.responseText);
		  	switch(result){
		  	case 1:
				win = window.open('./print/kartu_stok_printlist.html','kartu_stoklist','height=400,width=600,resizable=1,scrollbars=1, menubar=1');
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
		if(history_transaksi_supplier_DataStore.baseParams.query!==null){searchquery = history_transaksi_supplier_DataStore.baseParams.query;}
		if(history_transaksi_supplier_DataStore.baseParams.produk_id!==null){produk_id_2excel = history_transaksi_supplier_DataStore.baseParams.produk_id;}
		if(history_transaksi_supplier_DataStore.baseParams.produk_nama!==null){produk_nama_2excel = history_transaksi_supplier_DataStore.baseParams.produk_nama;}
		if(history_transaksi_supplier_DataStore.baseParams.satuan_id!==null){satuan_id_2excel = history_transaksi_supplier_DataStore.baseParams.satuan_id;}
		if(history_transaksi_supplier_DataStore.baseParams.satuan_nama!==null){satuan_nama_2excel = history_transaksi_supplier_DataStore.baseParams.satuan_nama;}
		if(history_transaksi_supplier_DataStore.baseParams.stok_saldo!==null){stok_saldo_2excel = history_transaksi_supplier_DataStore.baseParams.stok_saldo;}

		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_history_transaksi_supplier&m=get_action',
		params: {
			task: "EXCEL",
		  	query: searchquery,                    		// if we are doing a quicksearch, use this
			//if we are doing advanced search, use this
			produk_id : produk_id_2excel,
			produk_nama : produk_nama_2excel,
			satuan_id : satuan_id_2excel,
			satuan_nama : satuan_nama_2excel,
			stok_saldo : stok_saldo_2excel,
		  	currentlisting: history_transaksi_supplier_DataStore.baseParams.task // this tells us if we are searching or not
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

	history_transaksi_supplier_searchWindow.show();
	
});
	</script>
<body>
<div>
	<div class="col">
        <div id="fp_history_transaksi_supplier"></div>
         <div id="fp_vu_history_transaksi_supplier"></div>
		<div id="elwindow_history_transaksi_supplier_save"></div>
        <div id="elwindow_history_transaksi_supplier_search"></div>
    </div>
</div>
</body>
</html>