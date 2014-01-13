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
var list_piutang_DataStore;
var list_piutang_ColumnModel;
var list_piutang_ListEditorGrid;
var list_piutang_createForm;
var list_piutang_createWindow;
var list_piutang_searchForm;
var list_piutang_searchWindow;
var list_piutang_SelectedRow;
var list_piutang_ContextMenu;
//declare konstant
var list_piutang_post2db = '';
var msg = '';
var list_piutang_pageS=15;

/* declare variable here */
var list_piutang_idField;
var list_piutang_kodeField;
var list_piutang_namaField;
var bank_norekField;
var ship_namaField;
var bank_saldoField;
var list_piutang_keteranganField;
var list_piutang_aktifField;

var list_piutang_idSearchField;
var list_piutang_kodeSearchField;
var bank_namaSearchField;
var bank_norekSearchField;
var bank_atasnamaSearchField;
var bank_saldoSearchField;
var list_piutang_keteranganSearchField;
var list_piutang_aktifSearchField;

/* on ready fuction */
Ext.onReady(function(){
  	Ext.QuickTips.init();	/* Initiate quick tips icon */
  
  	// utilize custom extension for Group Summary
    var summary = new Ext.ux.grid.GroupSummary();
  
  /* Function for Saving inLine Editing */
  function list_piutang_update(oGrid_event){
	var bank_id_update_pk="";
	var bank_kode_update=null;
	var bank_nama_update=null;
	var bank_norek_update=null;
	var bank_atasnama_update=null;
	var bank_saldo_update=null;
	var bank_keterangan_update=null;
	var bank_aktif_update=null;

	bank_id_update_pk = oGrid_event.record.data.bank_id;
	if(oGrid_event.record.data.bank_kode!== null){bank_kode_update = oGrid_event.record.data.bank_kode;}
	if(oGrid_event.record.data.bank_nama!== null){bank_nama_update = oGrid_event.record.data.bank_nama;}
	if(oGrid_event.record.data.bank_norek!== null){bank_norek_update = oGrid_event.record.data.bank_norek;}
	if(oGrid_event.record.data.bank_atasnama!== null){bank_atasnama_update = oGrid_event.record.data.bank_atasnama;}
	if(oGrid_event.record.data.bank_saldo!== null){bank_saldo_update = oGrid_event.record.data.bank_saldo;}
	if(oGrid_event.record.data.bank_keterangan!== null){bank_keterangan_update = oGrid_event.record.data.bank_keterangan;}
	if(oGrid_event.record.data.bank_aktif!== null){bank_aktif_update = oGrid_event.record.data.bank_aktif;}

		Ext.Ajax.request({  
			waitMsg: 'Please wait...',
			url: 'index.php?c=c_daftar_piutang&m=get_action',
			params: {
				task: "UPDATE",
				bank_id	: bank_id_update_pk,				
				bank_kode	:bank_kode_update,		
				bank_nama	:bank_nama_update,		
				bank_norek	:bank_norek_update,		
				bank_atasnama	:bank_atasnama_update,		
				bank_saldo	:bank_saldo_update,		
				bank_keterangan	:bank_keterangan_update,		
				bank_aktif	:bank_aktif_update
			}, 
			success: function(response){							
				var result=eval(response.responseText);
				switch(result){
					case 1:
						list_piutang_DataStore.commitChanges();
						list_piutang_DataStore.reload();
						break;
					case 2:
						list_piutang_DataStore.reload();
						break;
					default:
						Ext.MessageBox.show({
							   title: 'Warning',
							   msg: 'Data Ship tidak bisa disimpan.',
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
							   msg: 'Tidak bisa terhubung dengan database server',
							   buttons: Ext.MessageBox.OK,
							   animEl: 'database',
							   icon: Ext.MessageBox.ERROR
				});	
			}									    
		});   
	}
  	/* End of Function */
  
  	/* Function for add data, open window create form */
	function ship_create(){
		if(is_ship_form_valid()){
		
		var ship_id_create_pk=null;
		//var bank_kode_create=null;
		//var bank_nama_create=null;
		//var bank_norek_create=null;
		var ship_nama_create=null;
		//var bank_saldo_create=null;
		var ship_keterangan_create=null;
		var ship_aktif_create=null;

		ship_id_create_pk=get_pk_id();
		//if(list_piutang_kodeField.getValue()!== null){bank_kode_create = list_piutang_kodeField.getValue();}
		//if(list_piutang_namaField.getValue()!== null){bank_nama_create = list_piutang_namaField.getValue();}
		//if(bank_norekField.getValue()!== null){bank_norek_create = bank_norekField.getValue();}
		if(ship_namaField.getValue()!== null){ship_nama_create = ship_namaField.getValue();}
		//if(bank_saldoField.getValue()!== null){bank_saldo_create = convertToNumber(bank_saldoField.getValue());}
		if(list_piutang_keteranganField.getValue()!== null){ship_keterangan_create = list_piutang_keteranganField.getValue();}
		if(list_piutang_aktifField.getValue()!== null){ship_aktif_create = list_piutang_aktifField.getValue();}

		Ext.Ajax.request({  
				waitMsg: 'Please wait...',
				url: 'index.php?c=c_daftar_piutang&m=get_action',
				params: {
					task: list_piutang_post2db,
					ship_id			: ship_id_create_pk,	
					//bank_kode		: bank_kode_create,	
					//bank_nama		: bank_nama_create,	
					//bank_norek	: bank_norek_create,	
					ship_nama		: ship_nama_create,	
					//bank_saldo	: bank_saldo_create,	
					ship_keterangan	: ship_keterangan_create,	
					ship_aktif		: ship_aktif_create
				}, 
				success: function(response){             
					var result=eval(response.responseText);
					switch(result){
						case 1:
							Ext.MessageBox.alert(list_piutang_post2db+' OK','Data Ship berhasil disimpan.');
							list_piutang_DataStore.reload();
							list_piutang_createWindow.hide();
							break;
						default:
							Ext.MessageBox.show({
							   title: 'Warning',
							   msg: 'Data Ship tidak bisa disimpan!.',
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
						   msg: 'Tidak bisa terhubung dengan database server',
						   buttons: Ext.MessageBox.OK,
						   animEl: 'database',
						   icon: Ext.MessageBox.ERROR
					});	
				}                      
			});
		} else {
			Ext.MessageBox.show({
				title: 'Warning',
				msg: 'Isian belum sempurna!.',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		}
	}
 	/* End of Function */
  
  	/* Function for get PK field */
	function get_pk_id(){
		if(list_piutang_post2db=='UPDATE')
			return list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('ship_id');
		else 
			return 0;
	}
	/* End of Function  */
	
	/* Reset form before loading */
	function bank_reset_form(){
		list_piutang_kodeField.reset();
		list_piutang_kodeField.setValue(null);
		list_piutang_namaField.reset();
		list_piutang_namaField.setValue(null);
		bank_norekField.reset();
		bank_norekField.setValue(null);
		ship_namaField.reset();
		ship_namaField.setValue(null);
		bank_saldoField.reset();
		bank_saldoField.setValue(null);
		list_piutang_keteranganField.reset();
		list_piutang_keteranganField.setValue(null);
		list_piutang_aktifField.reset();
		list_piutang_aktifField.setValue('Aktif');
	}
 	/* End of Function */
  
	/* setValue to EDIT */
	function ship_set_form(){
		//list_piutang_kodeField.setValue(list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('bank_kode'));
		//list_piutang_namaField.setValue(list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('bank_nama'));
		//bank_norekField.setValue(list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('bank_norek'));
		ship_namaField.setValue(list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('ship_nama'));
		//bank_saldoField.setValue(CurrencyFormatted(list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('bank_saldo')));
		list_piutang_keteranganField.setValue(list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('ship_keterangan'));
		list_piutang_aktifField.setValue(list_piutang_ListEditorGrid.getSelectionModel().getSelected().get('ship_aktif'));
	}
	/* End setValue to EDIT*/
  
	/* Function for Check if the form is valid */
	function is_ship_form_valid(){
		return (ship_namaField.isValid());
	}
  	/* End of Function */
  
  	/* Function for Displaying  create Window Form */
	function display_form_window(){
		if(!list_piutang_createWindow.isVisible()){
			
			list_piutang_post2db='CREATE';
			msg='created';
			bank_reset_form();
			
			list_piutang_createWindow.show();
		} else {
			list_piutang_createWindow.toFront();
		}
	}
  	/* End of Function */
 
  	/* Function for Delete Confirm */
	function bank_confirm_delete(){
		// only one bank is selected here
		if(list_piutang_ListEditorGrid.selModel.getCount() == 1){
			Ext.MessageBox.confirm('Confirmation','Apakah Anda yakin akan menghapus data berikut?', bank_delete);
		} else if(list_piutang_ListEditorGrid.selModel.getCount() > 1){
			Ext.MessageBox.confirm('Confirmation','Apakah Anda yakin akan menghapus data-data berikut?', bank_delete);
		} else {
			Ext.MessageBox.show({
				title: 'Warning',
				msg: 'Tidak ada yang dipilih untuk dihapus',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		}
	}
  	/* End of Function */
  
	/* Function for Update Confirm */
	function ship_confirm_update(){
		/* only one record is selected here */
		if(list_piutang_ListEditorGrid.selModel.getCount() == 1) {
			
			list_piutang_post2db='UPDATE';
			msg='updated';
			ship_set_form();
			
			list_piutang_createWindow.show();
		} else {
			Ext.MessageBox.show({
				title: 'Warning',
				msg: 'Tidak ada data yang dipilih untuk diedit',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		}
	}
  	/* End of Function */
  
  	/* Function for Delete Record */
	function bank_delete(btn){
		if(btn=='yes'){
			var selections = list_piutang_ListEditorGrid.selModel.getSelections();
			var prez = [];
			for(i = 0; i< list_piutang_ListEditorGrid.selModel.getCount(); i++){
				prez.push(selections[i].json.bank_id);
			}
			var encoded_array = Ext.encode(prez);
			Ext.Ajax.request({ 
				waitMsg: 'Please Wait',
				url: 'index.php?c=c_daftar_piutang&m=get_action', 
				params: { task: "DELETE", ids:  encoded_array }, 
				success: function(response){
					var result=eval(response.responseText);
					switch(result){
						case 1:  // Success : simply reload
							list_piutang_DataStore.reload();
							break;
						default:
							Ext.MessageBox.show({
								title: 'Warning',
								msg: 'Tidak bisa menghapus data yang diplih',
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
					   msg: 'Tidak bisa terhubung dengan database server',
					   buttons: Ext.MessageBox.OK,
					   animEl: 'database',
					   icon: Ext.MessageBox.ERROR
					});	
				}
			});
		}  
	}
  	/* End of Function */
  
	/* Function for Retrieve DataStore */
	list_piutang_DataStore = new Ext.data.Store({
		id: 'list_piutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_daftar_piutang&m=get_action', 
			method: 'POST'
		}),
		baseParams:{task: "LIST", start: 0, limit: list_piutang_pageS}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'lpiutang_id'
		},[
			{name: 'lpiutang_id', type: 'int', mapping: 'lpiutang_id'},
			{name: 'inv_no', type: 'string', mapping: 'inv_no'},
			{name: 'cust_firstname', type: 'string', mapping: 'cust_nama'},
			{name: 'cust_id', type: 'int', mapping: 'cust_id'},
			//{name: 'bank_norek', type: 'string', mapping: 'bank_norek'},
			{name: 'lpiutang_faktur', type: 'string', mapping: 'lpiutang_faktur'},
			{name: 'lpiutang_total', type: 'float', mapping: 'lpiutang_total'},
			{name: 'lpiutang_sisa', type: 'float', mapping: 'lpiutang_sisa'},
			{name: 'lpiutang_keterangan', type: 'string', mapping: 'lpiutang_keterangan'},
			{name: 'lpiutang_stat_dok', type: 'string', mapping: 'lpiutang_stat_dok'},
			{name: 'ship_creator', type: 'string', mapping: 'ship_creator'},
			{name: 'ship_date_create', type: 'date', dateFormat: 'Y-m-d H:i:s', mapping: 'ship_date_create'},
			{name: 'lpiutang_faktur_tanggal', type: 'date', dateFormat: 'Y-m-d', mapping: 'lpiutang_faktur_tanggal'},
			{name: 'ship_update', type: 'string', mapping: 'ship_update'},
			{name: 'ship_date_update', type: 'date', dateFormat: 'Y-m-d H:i:s', mapping: 'ship_date_update'},
			{name: 'ship_revised', type: 'int', mapping: 'ship_revised'}
		]),
		sortInfo:{field: 'lpiutang_id', direction: "ASC"}
	});
	/* End of Function */
	
	cbo_bank_akunDataStore = new Ext.data.Store({
		id: 'cbo_bank_akunDataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_daftar_piutang&m=get_akun_list', 
			method: 'POST'
		}),
			reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'akun_id'
		},[
			{name: 'bank_akun_value', type: 'int', mapping: 'akun_id'},
			{name: 'bank_akun_display', type: 'string', mapping: 'akun_nama'}
		]),
		sortInfo:{field: 'bank_akun_value', direction: "ASC"}
	});
	
	cbo_bank_mbankDataStore = new Ext.data.Store({
	id: 'cbo_bank_mbankDataStore',
	proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_daftar_piutang&m=get_mbank_list', 
			method: 'POST'
		}),
			reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'mbank_id'
		},[
			{name: 'bank_mbank_value', type: 'int', mapping: 'mbank_id'},
			{name: 'bank_mbank_display', type: 'string', mapping: 'mbank_nama'}
		]),
	sortInfo:{field: 'bank_mbank_display', direction: "ASC"}
	});


	/* Start Data Store untuk Detail List Piutang */
	detail_list_piutang_DataStore = new Ext.data.GroupingStore({
		id: 'detail_list_piutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_daftar_piutang&m=detail_list_piutang_list', 
			method: 'POST'
		}),
		baseParams:{task: "LIST",start:0,limit:100}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total'//,
		},[
        	{name: 'dpiutang_id', type: 'int', mapping: 'dpiutang_id'}, 
			{name: 'status_dokumen', type: 'string', mapping: 'status_dokumen'}, 
			{name: 'nobukti', type: 'string', mapping: 'nobukti'}, 
			{name: 'keterangan', type: 'string', mapping: 'keterangan'}, 
			{name: 'creator', type: 'string', mapping: 'creator'}, 
			{name: 'dpiutang_cara', type: 'string', mapping: 'dpiutang_cara'}, 
			{name: 'tanggal', type: 'date', dateFormat: 'Y-m-d', mapping: 'tanggal'}, 
			{name: 'dpiutang_nilai', type: 'float', mapping: 'dpiutang_nilai'},
			{name: 'dfcl_update', type: 'string', mapping: 'dfcl_update'}, 
			{name: 'dfcl_date_update', type: 'date', dateFormat: 'Y-m-d', mapping: 'dfcl_date_update'}
		]),
		sortInfo:{field: 'dpiutang_id', direction: "ASC"}
	});
	/* End DataStore */


  	/* Function for Identify of Window Column Model */
	list_piutang_ColumnModel = new Ext.grid.ColumnModel(
		[{
			header: '#',
			readOnly: true,
			dataIndex: 'lpiutang_id',
			width: 40,
			renderer: function(value, cell){
				cell.css = "readonlycell"; // Mengambil Value dari Class di dalam CSS 
				return value;
				},
			hidden: true
		},

		/*
		{
			header: '<div align="center">' + 'Tgl Faktur' + '</div>',
			dataIndex: 'lpiutang_faktur_tanggal',
			width: 70,	//150,
			sortable: true,
			//renderer: Ext.util.Format.dateRenderer('d-m-Y')
			renderer: function(value, cell, record){
				cell.css = "readonlycell";
				if(record.data.lpiutang_stat_dok=='Tertutup'){
					return '<span style="color:green;">' + value.dateFormat('d-m-Y') + '</span>';
				}
				if(record.data.lpiutang_stat_dok=='Terbuka'){
					return value.dateFormat('d-m-Y');
				}
				return value.dateFormat('d-m-Y');
			}	
		}, 

		*/
		/*
		{
			header: 'No. Nota Jual',
			dataIndex: 'inv_no',
			width: 80,
			sortable: true,
			renderer: function(value, cell, record){
				cell.css = "readonlycell";
				if(record.data.lpiutang_stat_dok=='Tertutup'){
					return '<span style="color:green;">' + value + '</span>';
				}
				if(record.data.lpiutang_stat_dok=='Terbuka'){
					return value;
				}
				return value;
			}
		}, 		
		*/
		{
			header: 'Customer',
			dataIndex: 'cust_firstname',
			width: 100,
			sortable: true,
			renderer: function(value, cell, record){
				cell.css = "readonlycell";
				if(record.data.lpiutang_stat_dok=='Tertutup'){
					return '<span style="color:green;">' + value + '</span>';
				}
				if(record.data.lpiutang_stat_dok=='Terbuka'){
					return value;
				}
				return value;
			}
		},
		{
			header: 'Total (Rp)',
			dataIndex: 'lpiutang_total',
			width: 100,
			align: 'right',
			sortable: true,
			/*
			renderer: function(val){
				return '<span>Rp. '+Ext.util.Format.number(val,'0,000')+'</span>';
			},
			*/
			renderer: function(value, cell, record){
				cell.css = "readonlycell";
				if(record.data.lpiutang_stat_dok=='Tertutup'){
					return '<span style="color:green;">' + Ext.util.Format.number(value,'0,000') + '</span>';
				}
				if(record.data.lpiutang_stat_dok=='Terbuka'){
					return Ext.util.Format.number(value,'0,000');
				}
				return Ext.util.Format.number(value,'0,000');
			}	

		},
		{
			header: 'Sisa (Rp)',
			dataIndex: 'lpiutang_sisa',
			width: 100,
			align: 'right',
			sortable: true,
			/*
			renderer: function(val){
				return '<span>Rp. '+Ext.util.Format.number(val,'0,000')+'</span>';
			}
			*/
			renderer: function(value, cell, record){
				cell.css = "readonlycell";
				if(record.data.lpiutang_stat_dok=='Tertutup'){
					return '<span style="color:green;">' + Ext.util.Format.number(value,'0,000') + '</span>';
				}
				if(record.data.lpiutang_stat_dok=='Terbuka'){
					return Ext.util.Format.number(value,'0,000');
				}
				return Ext.util.Format.number(value,'0,000');
			}	
		},
		{
			header: 'Keterangan',
			dataIndex: 'lpiutang_keterangan',
			width: 100,
			hidden: false,
			sortable: true,
			renderer: function(value, cell, record){
				cell.css = "readonlycell";
				if(record.data.lpiutang_stat_dok=='Tertutup'){
					return '<span style="color:green;">' + value + '</span>';
				}
				if(record.data.lpiutang_stat_dok=='Terbuka'){
					return value;
				}
				return value;
			}
		},
		{
			header: 'Status',
			dataIndex: 'lpiutang_stat_dok',
			width: 80,
			sortable: true,
			renderer: function(value, cell, record){
				cell.css = "readonlycell";
				if(record.data.lpiutang_stat_dok=='Tertutup'){
					return '<span style="color:green;">' + value + '</span>';
				}
				if(record.data.lpiutang_stat_dok=='Terbuka'){
					return value;
				}
				return value;
			}
		},
		{
			header: 'Creator',
			dataIndex: 'ship_creator',
			width: 150,
			sortable: true,
			hidden:true
		},
		{
			header: 'Create on',
			dataIndex: 'ship_date_create',
			width: 150,
			sortable: true,
			renderer: Ext.util.Format.dateRenderer('Y-m-d'),
			hidden:true
		},
		{
			header: 'Last Update By',
			dataIndex: 'ship_update',
			width: 150,
			sortable: true,
			hidden:true
		},
		{
			header: 'Last Update on',
			dataIndex: 'ship_date_update',
			width: 150,
			sortable: true,
			renderer: Ext.util.Format.dateRenderer('Y-m-d'),
			hidden:true
		},
		{
			header: 'Revised',
			dataIndex: 'ship_revised',
			width: 150,
			sortable: true,
			hidden:true
		}]
	);
	list_piutang_ColumnModel.defaultSortable= true;
	/* End of Function */

	//ColumnModel for Detail List Piutang
	detail_list_piutang_ColumnModel = new Ext.grid.ColumnModel(
		[{
			header: '<div align="center">' + 'Tgl' + '</div>',
			dataIndex: 'tanggal',
			width: 50,
			sortable: true,
			renderer: Ext.util.Format.dateRenderer('d-m-Y')
		},
		{
			header: '<div align="center">' + 'Piutang No Bukti' + '</div>',
			dataIndex: 'nobukti',
			width: 80,
			sortable: true
		},
		{
			header: 'Nilai (Rp)',
			dataIndex: 'dpiutang_nilai',
			width: 100,
			align: 'right',
			sortable: true,
			renderer: function(val){
				return '<span>Rp. '+Ext.util.Format.number(val,'0,000')+'</span>';
			}
		},
		{
			header: '<div align="center">' + 'Cara Bayar' + '</div>',
			dataIndex: 'dpiutang_cara',
			width: 130,
			sortable: true
		},
		{
			header: '<div align="center">' + 'Keterangan' + '</div>',
			dataIndex: 'keterangan',
			width: 130,
			sortable: true
		}
		]
    );
    detail_list_piutang_ColumnModel.defaultSortable= true;

    
	/* Declare DataStore and  show datagrid list */
	list_piutang_ListEditorGrid =  new Ext.grid.EditorGridPanel({
		id: 'list_piutang_ListEditorGrid',
		el: 'fp_list_piutang',
		title: 'Daftar Piutang',
		autoHeight: true,
		store: list_piutang_DataStore, // DataStore
		cm: list_piutang_ColumnModel, // Nama-nama Columns
		enableColLock:false,
		frame: true,
		clicksToEdit:2, // 2xClick untuk bisa meng-Edit inLine Data
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true },
	  	width: 1200,
		bbar: new Ext.PagingToolbar({
			pageSize: list_piutang_pageS,
			store: list_piutang_DataStore,
			displayInfo: true
		}),
		/* Add Control on ToolBar */
		tbar: [
		/*
		<?php if(eregi('C',$this->m_security->get_access_group_by_kode('MENU_BANKREK'))){ ?>
		{
			text: 'Add',
			tooltip: 'Add new record',
			iconCls:'icon-adds',    				// this is defined in our styles.css
			handler: display_form_window
		}, '-',
		<?php } ?>
		<?php if(eregi('U|R',$this->m_security->get_access_group_by_kode('MENU_BANKREK'))){ ?>
		{
			text: 'Edit',
			tooltip: 'Edit selected record',
			iconCls:'icon-update',
			handler: ship_confirm_update   // Confirm before updating
		}, '-',
		<?php } ?>
		<?php if(eregi('D',$this->m_security->get_access_group_by_kode('MENU_BANKREK'))){ ?>
		{
			text: 'Delete',
			tooltip: 'Delete selected record',
			iconCls:'icon-delete',
			disabled:true,
			handler: bank_confirm_delete   // Confirm before deleting
		}, '-', 
		<?php } ?>

		*/
		{
			text: 'Adv Search',
			tooltip: 'Advanced Search',
			iconCls:'icon-search',
			disabled:true,
			handler: display_form_search_window 
		}, '-', 
			new Ext.app.SearchField({
			store: list_piutang_DataStore,
			params: {task: 'LIST',start: 0, limit: list_piutang_pageS},
			listeners:{
				specialkey: function(f,e){
					if(e.getKey() == e.ENTER){
						list_piutang_DataStore.baseParams={task:'LIST',start: 0, limit: list_piutang_pageS};
		            }
				},
				render: function(c){
				Ext.get(this.id).set({qtitle:'Search By'});
				Ext.get(this.id).set({qtip:'- Kode Akun<br>- Nama Bank<br>- No.Rekening<br>- Atas Nama'});
				}
			},
			width: 120
		}),'-',{
			text: 'Refresh',
			tooltip: 'Refresh datagrid',
			handler: lpiutang_reset_search,
			iconCls:'icon-refresh'
		},'-',{
			text: 'Export Excel',
			tooltip: 'Export to Excel(.xls) Document',
			iconCls:'icon-xls',
			disabled:true,
			handler: listpiutang_export_excel
		}, '-',{
			text: 'Print',
			tooltip: 'Print Document',
			iconCls:'icon-print',
			disabled:true,
			handler: listpiutang_print  
		}
		]
	});
	list_piutang_ListEditorGrid.render();
	/* End of DataStore */

	/*Event ketika ngeklik List Piutang*/
	list_piutang_ListEditorGrid.on('rowclick', function (list_piutang_ListEditorGrid, rowIndex, eventObj) {
        var dp_recordMaster = list_piutang_ListEditorGrid.getSelectionModel().getSelected();
        detail_list_piutang_DataStore.setBaseParam('master_id',dp_recordMaster.get("lpiutang_id"));
        detail_list_piutang_DataStore.setBaseParam('cust_id',dp_recordMaster.get("cust_id"));
		detail_list_piutang_DataStore.load({params : {master_id : dp_recordMaster.get("lpiutang_id"), cust_id : dp_recordMaster.get("cust_id"), start:0, limit:100}});
		list_piutang_DataStore.reload();
    });

	//
	var detail_list_piutang_Panel = new Ext.grid.GridPanel({
		id: 'detail_list_piutang_Panel',
		title: 'Detail Pelunasan Piutang',
        store: detail_list_piutang_DataStore,
        cm: detail_list_piutang_ColumnModel,
		view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        }),
		plugins: summary,
        stripeRows: true,
        autoExpandColumn: 'nobukti',
        autoHeight: true,
		style: 'margin-top: 10px',
        width: 1200	//800
    });
    detail_list_piutang_Panel.render('fp_detail_list_piutang');
     
	/* Create Context Menu */
	list_piutang_ContextMenu = new Ext.menu.Menu({
		id: 'list_piutang_ListEditorGridContextMenu',
		items: [
		{ 
			text: 'Print',
			tooltip: 'Print Document',
			iconCls:'icon-print',
			handler: listpiutang_print 
		},
		{ 
			text: 'Export Excel', 
			tooltip: 'Export to Excel(.xls) Document',
			iconCls:'icon-xls',
			handler: listpiutang_export_excel 
		}
		]
	}); 
	/* End of Declaration */
	
	/* Event while selected row via context menu */
	function onlpiutang_ListEditGridContextMenu(grid, rowIndex, e) {
		e.stopEvent();
		var coords = e.getXY();
		list_piutang_ContextMenu.rowRecord = grid.store.getAt(rowIndex);
		grid.selModel.selectRow(rowIndex);
		list_piutang_SelectedRow=rowIndex;
		list_piutang_ContextMenu.showAt([coords[0], coords[1]]);
  	}
  	/* End of Function */
	
	/* function for editing row via context menu */
	function lpiutang_editContextMenu(){
      list_piutang_ListEditorGrid.startEditing(list_piutang_SelectedRow,1);
  	}
	/* End of Function */
  	
	list_piutang_ListEditorGrid.addListener('rowcontextmenu', onlpiutang_ListEditGridContextMenu);
	list_piutang_DataStore.load({params: {start: 0, limit: list_piutang_pageS}});	// load DataStore
	list_piutang_ListEditorGrid.on('afteredit', list_piutang_update); // inLine Editing Record
	
	
	/* Identify  bank_kode Field */
	list_piutang_kodeField= new Ext.form.ComboBox({
		id: 'list_piutang_kodeField',
		fieldLabel: 'Kode Akun',
		store: cbo_bank_akunDataStore,
		mode: 'remote',
		editable:false,
		displayField: 'bank_akun_display',
		valueField: 'bank_akun_value',
		anchor: '95%',
		triggerAction: 'all'
	});
	/* Identify  bank_nama Field */
	list_piutang_namaField= new Ext.form.ComboBox({
		id: 'list_piutang_namaField',
		fieldLabel: 'Nama Bank <span style="color: #ec0000">*</span>',
		typeAhead: true,
		triggerAction: 'all',
		store: cbo_bank_mbankDataStore,
		mode: 'remote',
		editable:false,
		displayField: 'bank_mbank_display',
		valueField: 'bank_mbank_value',
		lazyRender:true,
		width: 100,
		allowBlank: false,
		listClass: 'x-combo-list-small'
	});
	/* Identify  bank_norek Field */
	bank_norekField= new Ext.form.TextField({
		id: 'bank_norekField',
		fieldLabel: 'No. Rekening <span style="color: #ec0000">*</span>',
		maxLength: 250,
		allowBlank: false,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});
	/* Identify  bank_atasnama Field */
	ship_namaField= new Ext.form.TextField({
		id: 'ship_namaField',
		fieldLabel: 'Nama Ship <span style="color: #ec0000">*</span>',
		maxLength: 250,
		allowBlank: false,
		anchor: '95%'
	});
	/* Identify  bank_saldo Field */
	bank_saldoField= new Ext.form.TextField({
		id: 'bank_saldoField',
		fieldLabel: 'Saldo',
		itemCls: 'rmoney',
		emptyText: '0',
		allowBlank: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});
	/* Identify  bank_keterangan Field */
	list_piutang_keteranganField= new Ext.form.TextArea({
		id: 'list_piutang_keteranganField',
		fieldLabel: 'Keterangan',
		allowBlank: true,
		anchor: '95%'
	});
	/* Identify  bank_aktif Field */
	list_piutang_aktifField= new Ext.form.ComboBox({
		id: 'list_piutang_aktifField',
		fieldLabel: 'Status',
		store:new Ext.data.SimpleStore({
			fields:['ship_aktif_value', 'ship_aktif_display'],
			data:[['Aktif','Aktif'],['Tidak Aktif','Tidak Aktif']]
		}),
		mode: 'local',
		editable:false,
		emptyText: 'Aktif',
		displayField: 'ship_aktif_display',
		valueField: 'ship_aktif_value',
		width: 80,
		triggerAction: 'all'	
	});
	
	/* Function for retrieve create Window Panel*/ 
	list_piutang_createForm = new Ext.FormPanel({
		labelAlign: 'left',
		bodyStyle:'padding:5px',
		autoHeight:true,
		width: 300,        
		items: [{
			layout:'column',
			border:false,
			items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [ship_namaField, list_piutang_keteranganField, list_piutang_aktifField] 
			}
			]
		}]
		,
		buttons: [
			<?php if(eregi('U|C',$this->m_security->get_access_group_by_kode('MENU_BANKREK'))){ ?>
			{
				text: 'Save and Close',
				handler: ship_create
			}
			,
			<?php } ?>
			{
				text: 'Cancel',
				handler: function(){
					list_piutang_createWindow.hide();
				}
			}
		]
	});
	/* End  of Function*/
	
	/* Function for retrieve create Window Form */
	list_piutang_createWindow= new Ext.Window({
		id: 'list_piutang_createWindow',
		title: list_piutang_post2db+'Ship',
		closable:true,
		closeAction: 'hide',
		autoWidth: true,
		autoHeight: true,
		x:0,
		y:0,
		plain:true,
		layout: 'fit',
		modal: true,
		renderTo: 'elwindow_piutang_create',
		items: list_piutang_createForm
	});
	/* End Window */
	
	
	/* Function for action list search */
	function bank_list_search(){
		// render according to a SQL date format.
		var bank_id_search=null;
		var bank_kode_search=null;
		var bank_nama_search=null;
		var bank_norek_search=null;
		var bank_atasnama_search=null;
		var bank_saldo_search=null;
		var bank_keterangan_search=null;
		var bank_aktif_search=null;

		if(list_piutang_idSearchField.getValue()!==null){bank_id_search=list_piutang_idSearchField.getValue();}
		if(list_piutang_kodeSearchField.getValue()!==null){bank_kode_search=list_piutang_kodeSearchField.getValue();}
		if(bank_namaSearchField.getValue()!==null){bank_nama_search=bank_namaSearchField.getValue();}
		if(bank_norekSearchField.getValue()!==null){bank_norek_search=bank_norekSearchField.getValue();}
		if(bank_atasnamaSearchField.getValue()!==null){bank_atasnama_search=bank_atasnamaSearchField.getValue();}
		if(bank_saldoSearchField.getValue()!==null){bank_saldo_search=bank_saldoSearchField.getValue();}
		if(list_piutang_keteranganSearchField.getValue()!==null){bank_keterangan_search=list_piutang_keteranganSearchField.getValue();}
		if(list_piutang_aktifSearchField.getValue()!==null){bank_aktif_search=list_piutang_aktifSearchField.getValue();}
		// change the store parameters
		list_piutang_DataStore.baseParams = {
			task: 'SEARCH',
			start: 0,
			limit: list_piutang_pageS,
			//variable here
			bank_id	:	bank_id_search, 
			bank_kode	:	bank_kode_search, 
			bank_nama	:	bank_nama_search, 
			bank_norek	:	bank_norek_search, 
			bank_atasnama	:	bank_atasnama_search, 
			bank_saldo	:	bank_saldo_search, 
			bank_keterangan	:	bank_keterangan_search, 
			bank_aktif	:	bank_aktif_search
		};
		// Cause the datastore to do another query : 
		list_piutang_DataStore.reload({params: {start: 0, limit: list_piutang_pageS}});
	}
		
	/* Function for reset search result */
	function lpiutang_reset_search(){
		// reset the store parameters
		list_piutang_DataStore.baseParams = { task: 'LIST' };
		// Cause the datastore to do another query : 
		list_piutang_DataStore.reload({params: {start: 0, limit: list_piutang_pageS}});
		//list_piutang_searchWindow.close();
	};
	/* End of Fuction */
	
	function lpiutang_reset_SearchForm(){
		list_piutang_kodeSearchField.reset();
		list_piutang_kodeSearchField.setValue(null);
		bank_namaSearchField.reset();
		bank_namaSearchField.setValue(null);
		bank_norekSearchField.reset();
		bank_norekSearchField.setValue(null);
		bank_atasnamaSearchField.reset();
		bank_atasnamaSearchField.setValue(null);
		bank_saldoSearchField.reset();
		bank_saldoSearchField.setValue(null);
		list_piutang_keteranganSearchField.reset();
		list_piutang_keteranganSearchField.setValue(null);
		list_piutang_aktifSearchField.reset();
		list_piutang_aktifSearchField.setValue(null);
	}
	
	/* Field for search */
	/* Identify  bank_id Search Field */
	list_piutang_idSearchField= new Ext.form.NumberField({
		id: 'list_piutang_idSearchField',
		fieldLabel: 'Id',
		allowNegatife : false,
		blankText: '0',
		allowDecimals: false,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	
	});
	/* Identify  bank_kode Search Field */
	list_piutang_kodeSearchField= new Ext.form.ComboBox({
		id: 'list_piutang_kodeSearchField',
		fieldLabel: 'Kode Akun',
		store: cbo_bank_akunDataStore,
		mode: 'local',
		displayField: 'bank_akun_display',
		valueField: 'bank_akun_value',
		anchor: '95%',
		triggerAction: 'all'
	});
	/* Identify  bank_nama Search Field */
	bank_namaSearchField= new Ext.form.ComboBox({
		id: 'bank_namaSearchField',
		fieldLabel: 'Nama Bank',
		typeAhead: true,
		triggerAction: 'all',
		store: cbo_bank_mbankDataStore,
		mode: 'remote',
		displayField: 'bank_mbank_display',
		valueField: 'bank_mbank_value',
		lazyRender:true,
		anchor: '95%',
		listClass: 'x-combo-list-small'
	});
	/* Identify  bank_norek Search Field */
	bank_norekSearchField= new Ext.form.TextField({
		id: 'bank_norekSearchField',
		fieldLabel: 'No. Rekening',
		maxLength: 250,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	
	});
	/* Identify  bank_atasnama Search Field */
	bank_atasnamaSearchField= new Ext.form.TextField({
		id: 'bank_atasnamaSearchField',
		fieldLabel: 'Atas Nama',
		maxLength: 250,
		anchor: '95%'
	
	});
	/* Identify  bank_saldo Search Field */
	bank_saldoSearchField= new Ext.form.NumberField({
		id: 'bank_saldoSearchField',
		fieldLabel: 'Saldo',
		allowNegatife : false,
		blankText: '0',
		allowDecimals: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	
	});
	/* Identify  bank_keterangan Search Field */
	list_piutang_keteranganSearchField= new Ext.form.TextArea({
		id: 'list_piutang_keteranganSearchField',
		fieldLabel: 'Keterangan',
		allowBlank: true,
		anchor: '95%'
	});
	/* Identify  bank_aktif Search Field */
	list_piutang_aktifSearchField= new Ext.form.ComboBox({
		id: 'list_piutang_aktifSearchField',
		fieldLabel: 'Status',
		store:new Ext.data.SimpleStore({
			fields:['value', 'bank_aktif'],
			data:[['Aktif','Aktif'],['Tidak Aktif','Tidak Aktif']]
		}),
		mode: 'local',
		displayField: 'bank_aktif',
		valueField: 'value',
		emptyText: 'Aktif',
		width: 80,
		triggerAction: 'all'	 
	
	});
	
	/* Function for retrieve search Form Panel */
	list_piutang_searchForm = new Ext.FormPanel({
		labelAlign: 'left',
		bodyStyle:'padding:5px',
		autoHeight:true,
		width: 300,        
		items: [{
			layout:'column',
			border:false,
			items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [bank_namaSearchField, bank_norekSearchField, bank_atasnamaSearchField, bank_saldoSearchField, list_piutang_keteranganSearchField,
						list_piutang_aktifSearchField] 
			}
			]
		}]
		,
		buttons: [{
				text: 'Search',
				handler: bank_list_search
			},{
				text: 'Close',
				handler: function(){
					list_piutang_searchWindow.hide();
				}
			}
		]
	});
    /* End of Function */ 
	 
	/* Function for retrieve search Window Form, used for andvaced search */
	list_piutang_searchWindow = new Ext.Window({
		title: 'Pencarian Ship',
		closable:true,
		closeAction: 'hide',
		autoWidth: true,
		autoHeight: true,
		plain:true,
		layout: 'fit',
		x: 0,
		y: 0,
		modal: true,
		renderTo: 'elwindow_piutang_search',
		items: list_piutang_searchForm
	});
    /* End of Function */ 
	 
  	/* Function for Displaying  Search Window Form */
	function display_form_search_window(){
		if(!list_piutang_searchWindow.isVisible()){
			lpiutang_reset_SearchForm();
			list_piutang_searchWindow.show();
		} else {
			list_piutang_searchWindow.toFront();
		}
	}
  	/* End Function */
	
	/* Function for print List Grid */
	function listpiutang_print(){
		var searchquery = "";
		var bank_kode_print=null;
		var bank_nama_print=null;
		var bank_norek_print=null;
		var bank_atasnama_print=null;
		var bank_saldo_print=null;
		var bank_keterangan_print=null;
		var bank_aktif_print=null;
		var win;              
		// check if we do have some search data...
		if(list_piutang_DataStore.baseParams.query!==null){searchquery = list_piutang_DataStore.baseParams.query;}
		if(list_piutang_DataStore.baseParams.bank_kode!==null){bank_kode_print = list_piutang_DataStore.baseParams.bank_kode;}
		if(list_piutang_DataStore.baseParams.bank_nama!==null){bank_nama_print = list_piutang_DataStore.baseParams.bank_nama;}
		if(list_piutang_DataStore.baseParams.bank_norek!==null){bank_norek_print = list_piutang_DataStore.baseParams.bank_norek;}
		if(list_piutang_DataStore.baseParams.bank_atasnama!==null){bank_atasnama_print = list_piutang_DataStore.baseParams.bank_atasnama;}
		if(list_piutang_DataStore.baseParams.bank_saldo!==null){bank_saldo_print = list_piutang_DataStore.baseParams.bank_saldo;}
		if(list_piutang_DataStore.baseParams.bank_keterangan!==null){bank_keterangan_print = list_piutang_DataStore.baseParams.bank_keterangan;}
		if(list_piutang_DataStore.baseParams.bank_aktif!==null){bank_aktif_print = list_piutang_DataStore.baseParams.bank_aktif;}
		

		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_daftar_piutang&m=get_action',
		params: {
			task: "PRINT",
		  	query: searchquery,                    		// if we are doing a quicksearch, use this
			//if we are doing advanced search, use this
			bank_kode : bank_kode_print,
			bank_nama : bank_nama_print,
			bank_norek : bank_norek_print,
			bank_atasnama : bank_atasnama_print,
			bank_saldo : bank_saldo_print,
			bank_keterangan : bank_keterangan_print,
			bank_aktif : bank_aktif_print,
		  	currentlisting: list_piutang_DataStore.baseParams.task // this tells us if we are searching or not
		}, 
		success: function(response){              
		  	var result=eval(response.responseText);
		  	switch(result){
		  	case 1:
				win = window.open('./banklist.html','banklist','height=400,width=600,resizable=1,scrollbars=1, menubar=1');
				
				break;
		  	default:
				Ext.MessageBox.show({
					title: 'Warning',
					msg: 'Tidak bisa mencetak data!',
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
			   msg: 'Tidak bisa terhubung dengan database server',
			   buttons: Ext.MessageBox.OK,
			   animEl: 'database',
			   icon: Ext.MessageBox.ERROR
			});		
		} 	                     
		});
	}
	/* Enf Function */
	
	/* Function for print Export to Excel Grid */
	function listpiutang_export_excel(){
		var searchquery = "";
		var bank_kode_2excel=null;
		var bank_nama_2excel=null;
		var bank_norek_2excel=null;
		var bank_atasnama_2excel=null;
		var bank_saldo_2excel=null;
		var bank_keterangan_2excel=null;
		var bank_aktif_2excel=null;
		var win;              
		// check if we do have some search data...
		if(list_piutang_DataStore.baseParams.query!==null){searchquery = list_piutang_DataStore.baseParams.query;}
		if(list_piutang_DataStore.baseParams.bank_kode!==null){bank_kode_2excel = list_piutang_DataStore.baseParams.bank_kode;}
		if(list_piutang_DataStore.baseParams.bank_nama!==null){bank_nama_2excel = list_piutang_DataStore.baseParams.bank_nama;}
		if(list_piutang_DataStore.baseParams.bank_norek!==null){bank_norek_2excel = list_piutang_DataStore.baseParams.bank_norek;}
		if(list_piutang_DataStore.baseParams.bank_atasnama!==null){bank_atasnama_2excel = list_piutang_DataStore.baseParams.bank_atasnama;}
		if(list_piutang_DataStore.baseParams.bank_saldo!==null){bank_saldo_2excel = list_piutang_DataStore.baseParams.bank_saldo;}
		if(list_piutang_DataStore.baseParams.bank_keterangan!==null){bank_keterangan_2excel = list_piutang_DataStore.baseParams.bank_keterangan;}
		if(list_piutang_DataStore.baseParams.bank_aktif!==null){bank_aktif_2excel = list_piutang_DataStore.baseParams.bank_aktif;}
		

		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_daftar_piutang&m=get_action',
		params: {
			task: "EXCEL",
		  	query: searchquery,                    		// if we are doing a quicksearch, use this
			//if we are doing advanced search, use this
			bank_kode : bank_kode_2excel,
			bank_nama : bank_nama_2excel,
			bank_norek : bank_norek_2excel,
			bank_atasnama : bank_atasnama_2excel,
			bank_saldo : bank_saldo_2excel,
			bank_keterangan : bank_keterangan_2excel,
			bank_aktif : bank_aktif_2excel,
		  	currentlisting: list_piutang_DataStore.baseParams.task // this tells us if we are searching or not
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
					msg: 'Tidak bisa meng-export data ke dalam format excel!',
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
			   msg: 'Tidak bisa terhubung dengan database server',
			   buttons: Ext.MessageBox.OK,
			   animEl: 'database',
			   icon: Ext.MessageBox.ERROR
			});    
		} 	                     
		});
	}
	/*End of Function */
	
	bank_saldoField.on('focus',function(){ bank_saldoField.setValue(convertToNumber(bank_saldoField.getValue())); });
	bank_saldoField.on('blur',function(){ bank_saldoField.setValue(CurrencyFormatted(bank_saldoField.getValue())); });
	
/* 	
	created by : GIOV Solution - Keep IT Simple	
*/

});
	</script>
<body>
<div>
	<div class="col">
        <div id="fp_list_piutang"></div>
        <div id="fp_detail_list_piutang"></div>
		<div id="elwindow_piutang_create"></div>
        <div id="elwindow_piutang_search"></div>
    </div>
</div>
</body>