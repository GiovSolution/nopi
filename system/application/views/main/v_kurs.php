<?
/* 	These code was generated using phpCIGen v 0.1.b (24/06/2009)
	
	+ Module  		: kurs View
	+ Description	: For record view
	+ Filename 		: v_kurs.php
 	+ Author  		: Isaac & Freddy
 	+ Created on 14/Mar/2012 22:45:00
	
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
var kurs_DataStore;
var kurs_ColumnModel;
var kursListEditorGrid;
var kurs_createForm;
var kurs_createWindow;
var jenis_searchForm;
var jenis_searchWindow;
var jenis_SelectedRow;
var jenis_ContextMenu;
//for detail data
var _DataStor;
var ListEditorGrid;
var _ColumnModel;
var _proxy;
var _writer;
var _reader;
var editor_;

//declare konstant
var post2db = '';
var msg = '';
var pageS=15;
var dt= new Date();

/* declare variable here for Field*/
var kurs_idField;
var kurs_tanggalField;
var kurs_negaraField;
var kurs_initialField;
var kurs_nilaiField;
var kurs_keteranganField;
var kurs_aktifField;
var jenis_idSearchField;
var jenis_kodeSearchField;
var jenis_namaSearchField;
var jenis_keteranganSearchField;
var jenis_aktifSearchField;

/* on ready fuction */
Ext.onReady(function(){
  	Ext.QuickTips.init();	/* Initiate quick tips icon */
  
  	/* Function for Saving inLine Editing */
	function jenis_update(oGrid_event){
		var jenis_id_update_pk="";
		var jenis_kode_update=null;
		var jenis_nama_update=null;
		var jenis_keterangan_update=null;
		var jenis_aktif_update=null;

		jenis_id_update_pk = oGrid_event.record.data.jenis_id;
		if(oGrid_event.record.data.jenis_kode!== null){jenis_kode_update = oGrid_event.record.data.jenis_kode;}
		if(oGrid_event.record.data.jenis_nama!== null){jenis_nama_update = oGrid_event.record.data.jenis_nama;}
		if(oGrid_event.record.data.jenis_keterangan!== null){jenis_keterangan_update = oGrid_event.record.data.jenis_keterangan;}
		if(oGrid_event.record.data.jenis_aktif!== null){jenis_aktif_update = oGrid_event.record.data.jenis_aktif;}

		Ext.Ajax.request({  
			waitMsg: 'Please wait...',
			url: 'index.php?c=c_kurs&m=get_action',
			params: {
				task: "UPDATE",
				jenis_id	: jenis_id_update_pk, 
				jenis_kode	:jenis_kode_update,  
				jenis_nama	:jenis_nama_update,  
				jenis_keterangan	:jenis_keterangan_update,  
				jenis_aktif	:jenis_aktif_update,  
			}, 
			success: function(response){							
				var result=eval(response.responseText);
				switch(result){
					case 1:
						kurs_DataStore.commitChanges();
						kurs_DataStore.reload();
						break;
					default:
						Ext.MessageBox.show({
						   title: 'Warning',
						   msg: 'Data Jenis tidak bisa disimpan.',
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
	function kurs_create(){
	
		if(is_jenis_form_valid()){	
		var kurs_id_create_pk=null; 
		var kurs_tanggal_create_date=null; 
		var kurs_negara_create=null; 
		var kurs_initial_create=null; 
		var kurs_nilai_create=null; 
		var kurs_keterangan_create=null; 
		var kurs_aktif_create=null; 

		if(kurs_idField.getValue()!== null){kurs_id_create_pk = kurs_idField.getValue();}else{kurs_id_create_pk=get_pk_id();} 
		if(kurs_tanggalField.getValue()!== ""){kurs_tanggal_create_date = kurs_tanggalField.getValue().format('Y-m-d H:i:s');}
		if(kurs_negaraField.getValue()!== null){kurs_negara_create = kurs_negaraField.getValue();} 
		if(kurs_initialField.getValue()!== null){kurs_initial_create = kurs_initialField.getValue();} 
		if(kurs_nilaiField.getValue()!== null){kurs_nilai_create = convertToNumber(kurs_nilaiField.getValue());} 
		if(kurs_keteranganField.getValue()!== null){kurs_keterangan_create = kurs_keteranganField.getValue();} 
		if(kurs_aktifField.getValue()!== null){kurs_aktif_create = kurs_aktifField.getValue();} 

		Ext.Ajax.request({  
			waitMsg: 'Please wait...',
			url: 'index.php?c=c_kurs&m=get_action',
			params: {
				task: post2db,
				kurs_id				: kurs_id_create_pk, 
				kurs_tanggal		: kurs_tanggal_create_date, 
				kurs_negara			: kurs_negara_create, 
				kurs_initial		: kurs_initial_create, 
				kurs_nilai			: kurs_nilai_create, 
				kurs_keterangan		: kurs_keterangan_create, 
				kurs_aktif			: kurs_aktif_create, 
			}, 
			success: function(response){             
				var result=eval(response.responseText);
				switch(result){
					case 1:
						Ext.MessageBox.alert(post2db+' OK','Data Kurs berhasil disimpan.');
						kurs_DataStore.reload();
						kurs_createWindow.hide();
						break;
					default:
						Ext.MessageBox.show({
						   title: 'Warning',
						   msg: 'Data Jenis tidak bisa disimpan.',
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
		if(post2db=='UPDATE')
			return kursListEditorGrid.getSelectionModel().getSelected().get('jenis_id');
		else 
			return 0;
	}
	/* End of Function  */
	
	/* Reset form before loading */
	function jenis_reset_form(){
		kurs_idField.reset();
		kurs_idField.setValue(null);
		kurs_tanggalField.reset();
		kurs_negaraField.reset();
		kurs_negaraField.setValue(null);
		kurs_initialField.reset();
		kurs_initialField.setValue(null);
		kurs_nilaiField.reset();
		kurs_nilaiField.setValue(null);
		kurs_keteranganField.reset();
		kurs_keteranganField.setValue(null);
		kurs_aktifField.reset();
		kurs_aktifField.setValue(null);
		kurs_nilaiField.setValue(0);
	}
 	/* End of Function */
  
	/* setValue to EDIT */
	function jenis_set_form(){
		kurs_idField.setValue(kursListEditorGrid.getSelectionModel().getSelected().get('kurs_id'));
		kurs_tanggalField.setValue(kursListEditorGrid.getSelectionModel().getSelected().get('kurs_tanggal'));
		kurs_negaraField.setValue(kursListEditorGrid.getSelectionModel().getSelected().get('kurs_negara'));
		kurs_initialField.setValue(kursListEditorGrid.getSelectionModel().getSelected().get('kurs_initial'));
		kurs_nilaiField.setValue(CurrencyFormatted(kursListEditorGrid.getSelectionModel().getSelected().get('kurs_nilai')));
		kurs_keteranganField.setValue(kursListEditorGrid.getSelectionModel().getSelected().get('kurs_keterangan'));
		kurs_aktifField.setValue(kursListEditorGrid.getSelectionModel().getSelected().get('kurs_aktif'));
	}
	/* End setValue to EDIT*/
  
	/* Function for Check if the form is valid */
	function is_jenis_form_valid(){
		return (kurs_negaraField.isValid() && kurs_initialField.isValid() &&  kurs_nilaiField.isValid()  );
	}
  	/* End of Function */
  
  	/* Function for Displaying  create Window Form */
	function display_form_window(){
		if(!kurs_createWindow.isVisible()){
			jenis_reset_form();
			post2db='CREATE';
			msg='created';
			kurs_createWindow.show();
		} else {
			kurs_createWindow.toFront();
		}
	}
  	/* End of Function */
 
  	/* Function for Delete Confirm */
	function jenis_confirm_delete(){
		// only one jenis is selected here
		if(kursListEditorGrid.selModel.getCount() == 1){
			Ext.MessageBox.confirm('Confirmation','Apakah Anda yakin akan menghapus data berikut?', jenis_delete);
		} else if(kursListEditorGrid.selModel.getCount() > 1){
			Ext.MessageBox.confirm('Confirmation','Apakah Anda yakin akan menghapus data-data berikut?', jenis_delete);
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
	function kurs_confirm_update(){
		/* only one record is selected here */
		if(kursListEditorGrid.selModel.getCount() == 1) {
			jenis_set_form();
			post2db='UPDATE';
			msg='updated';
			kurs_createWindow.show();
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
	function jenis_delete(btn){
		if(btn=='yes'){
			var selections = kursListEditorGrid.selModel.getSelections();
			var prez = [];
			for(i = 0; i< kursListEditorGrid.selModel.getCount(); i++){
				prez.push(selections[i].json.jenis_id);
			}
			var encoded_array = Ext.encode(prez);
			Ext.Ajax.request({ 
				waitMsg: 'Please Wait',
				url: 'index.php?c=c_kurs&m=get_action', 
				params: { task: "DELETE", ids:  encoded_array }, 
				success: function(response){
					var result=eval(response.responseText);
					switch(result){
						case 1:  // Success : simply reload
							kurs_DataStore.reload();
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
	kurs_DataStore = new Ext.data.Store({
		id: 'kurs_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_kurs&m=get_action', 
			method: 'POST'
		}),
		baseParams:{task: "LIST",start: 0, limit: pageS}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'kurs_id'
		},[
			{name: 'kurs_id', type: 'int', mapping: 'kurs_id'}, 
			{name: 'kurs_tanggal', type: 'date', dateFormat: 'Y-m-d H:i:s', mapping: 'kurs_tanggal'}, 
			{name: 'kurs_negara', type: 'string', mapping: 'kurs_negara'}, 
			{name: 'kurs_initial', type: 'string', mapping: 'kurs_initial'}, 
			{name: 'kurs_nilai', type: 'float', mapping: 'kurs_nilai'}, 
			{name: 'kurs_keterangan', type: 'string', mapping: 'kurs_keterangan'}, 
			{name: 'kurs_aktif', type: 'string', mapping: 'kurs_aktif'}, 
			{name: 'kurs_creator', type: 'string', mapping: 'kurs_creator'}, 
			{name: 'kurs_date_create', type: 'date', dateFormat: 'Y-m-d', mapping: 'kurs_date_create'}, 
			{name: 'kurs_update', type: 'string', mapping: 'kurs_update'}, 
			{name: 'kurs_date_update', type: 'date', dateFormat: 'Y-m-d', mapping: 'kurs_date_update'}, 
			{name: 'kurs_revised', type: 'int', mapping: 'kurs_revised'} 
		]),
		sortInfo:{field: 'kurs_id', direction: "DESC"}
	});
	/* End of Function */
    
  	/* Function for Identify of Window Column Model */
	kurs_ColumnModel = new Ext.grid.ColumnModel(
		[/*{
			header: '#',
			readOnly: true,
			dataIndex: 'jenis_id',
			width: 40,
			renderer: function(value, cell){
				cell.css = "readonlycell"; // Mengambil Value dari Class di dalam CSS 
				return value;
				},
			hidden: false
		},*/
		
		{
			header: 'Tanggal dan Jam',
			dataIndex: 'kurs_tanggal',
			renderer: Ext.util.Format.dateRenderer('d-m-Y H:i:s'),
			readOnly: true,
			width: 250,
			sortable: true
			<? if(eregi('U',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
			,
			editor: new Ext.form.TextField({
				allowBlank: false,
				maxLength: 250
          	})
			<? } ?>
		},
		{
			header: 'Negara',
			dataIndex: 'kurs_negara',
			width: 100,
			sortable: true
			<? if(eregi('U',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
			,
			editor: new Ext.form.TextField({
				allowBlank: false,
				maxLength: 10
          	})
			<? } ?>
		}, 
		{
			header: 'Initial',
			dataIndex: 'kurs_initial',
			width: 250,
			sortable: true
			<? if(eregi('U',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
			,
			editor: new Ext.form.TextField({
				allowBlank: false,
				maxLength: 250
          	})
			<? } ?>
		}, 
		{
			header: 'Nilai',
			dataIndex: 'kurs_nilai',
			width: 250,
			sortable: true
			<? if(eregi('U',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
			,
			editor: new Ext.form.TextField({
				allowBlank: false,
				maxLength: 250
          	})
			<? } ?>
		}, 
		{
			header: 'Keterangan',
			dataIndex: 'kurs_keterangan',
			width: 150,
			sortable: true
			<? if(eregi('U',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
			,
			editor: new Ext.form.TextField({
				maxLength: 250
          	})
			<? } ?>
		}, 
		{
			header: 'Status',
			dataIndex: 'kurs_aktif',
			width: 150,
			sortable: true
			<? if(eregi('U',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
			,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				store:new Ext.data.SimpleStore({
					fields:['kurs_aktif_value', 'kurs_aktif_display'],
					data: [['Aktif','Aktif'],['Tidak Aktif','Tidak Aktif']]
					}),
				mode: 'local',
               	displayField: 'kurs_aktif_display',
               	valueField: 'kurs_aktif_value',
               	lazyRender:true,
               	listClass: 'x-combo-list-small'
            })
			<? } ?>
		}, 
		{
			header: 'Creator',
			dataIndex: 'kurs_creator',
			width: 150,
			sortable: true,
			hidden: true,
			readOnly: true,
		}, 
		{
			header: 'Create On',
			dataIndex: 'kurs_date_create',
			width: 150,
			sortable: true,
			hidden: true,
			readOnly: true,
		}, 
		{
			header: 'Last Update by',
			dataIndex: 'kurs_update',
			width: 150,
			sortable: true,
			hidden: true,
			readOnly: true,
		}, 
		{
			header: 'Last Update on',
			dataIndex: 'kurs_date_update',
			width: 150,
			sortable: true,
			hidden: true,
			readOnly: true,
		}, 
		{
			header: 'Revised',
			dataIndex: 'kurs_revised',
			width: 150,
			sortable: true,
			hidden: true,
			readOnly: true,
		}	]);
	
	kurs_ColumnModel.defaultSortable= true;
	/* End of Function */
    
	/* Declare DataStore and  show datagrid list */
	kursListEditorGrid =  new Ext.grid.EditorGridPanel({
		id: 'kursListEditorGrid',
		el: 'fp_kurs',
		title: 'Daftar Kurs',
		autoHeight: true,
		store: kurs_DataStore, // DataStore
		cm: kurs_ColumnModel, // Nama-nama Columns
		enableColLock:false,
		frame: true,
		clicksToEdit:2, // 2xClick untuk bisa meng-Edit inLine Data
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true },
	  	width: 800,
		bbar: new Ext.PagingToolbar({
			pageSize: pageS,
			store: kurs_DataStore,
			displayInfo: true
		}),
		/* Add Control on ToolBar */
		tbar: [
		<? if(eregi('C',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
		{
			text: 'Add',
			tooltip: 'Add new record',
			iconCls:'icon-adds',    				// this is defined in our styles.css
			handler: display_form_window
		}, '-',
		<? } ?>
		<? if(eregi('U|R',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
		{
			text: 'Edit',
			tooltip: 'Edit selected record',
			iconCls:'icon-update',
			handler: kurs_confirm_update   // Confirm before updating
		}, '-',
		<? } ?>
		<? if(eregi('D',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
		{
			text: 'Delete',
			tooltip: 'Delete selected record',
			iconCls:'icon-delete',
			handler: jenis_confirm_delete   // Confirm before deleting
		}, '-', 
		<? }?>
		{
			text: 'Adv Search',
			tooltip: 'Advanced Search',
			iconCls:'icon-search',
			handler: display_form_search_window 
		}, '-', 
			new Ext.app.SearchField({
			store: kurs_DataStore,
			params: {task: 'LIST',start: 0, limit: pageS},
			listeners:{
				specialkey: function(f,e){
					if(e.getKey() == e.ENTER){
						kurs_DataStore.baseParams={task:'LIST',start: 0, limit: pageS};
		            }
				},
				render: function(c){
				Ext.get(this.id).set({qtitle:'Search By'});
				Ext.get(this.id).set({qtip:'- Nama Group 2<br>- Kode Group 2<br>- Kelompok'});
				}
			},
			width: 120
		}),'-',{
			text: 'Refresh',
			tooltip: 'Refresh datagrid',
			handler: jenis_reset_search,
			iconCls:'icon-refresh'
		},'-',{
			text: 'Export Excel',
			tooltip: 'Export to Excel(.xls) Document',
			iconCls:'icon-xls',
			handler: jenis_export_excel
		}, '-',{
			text: 'Print',
			tooltip: 'Print Document',
			iconCls:'icon-print',
			handler: jenis_print  
		}
		]
	});
	kursListEditorGrid.render();
	/* End of DataStore */
     
	/* Create Context Menu */
	jenis_ContextMenu = new Ext.menu.Menu({
		id: 'jenis_ListEditorGridContextMenu',
		items: [
		<? if(eregi('U|R',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
		{ 
			text: 'Edit', tooltip: 'Edit selected record', 
			iconCls:'icon-update',
			handler: jenis_editContextMenu 
		},
		<? } ?>
		<? if(eregi('D',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
		{ 
			text: 'Delete', 
			tooltip: 'Delete selected record', 
			iconCls:'icon-delete',
			handler: jenis_confirm_delete 
		},
		<? } ?>
		'-',
		{ 
			text: 'Print',
			tooltip: 'Print Document',
			iconCls:'icon-print',
			handler: jenis_print 
		},
		{ 
			text: 'Export Excel', 
			tooltip: 'Export to Excel(.xls) Document',
			iconCls:'icon-xls',
			handler: jenis_export_excel 
		}
		]
	}); 
	/* End of Declaration */
	
	/* Event while selected row via context menu */
	function onjenis_ListEditGridContextMenu(grid, rowIndex, e) {
		e.stopEvent();
		var coords = e.getXY();
		jenis_ContextMenu.rowRecord = grid.store.getAt(rowIndex);
		grid.selModel.selectRow(rowIndex);
		jenis_SelectedRow=rowIndex;
		jenis_ContextMenu.showAt([coords[0], coords[1]]);
  	}
  	/* End of Function */
	
	/* function for editing row via context menu */
	function jenis_editContextMenu(){
		kursListEditorGrid.startEditing(jenis_SelectedRow,1);
  	}
	/* End of Function */
  	
	kursListEditorGrid.addListener('rowcontextmenu', onjenis_ListEditGridContextMenu);
	kurs_DataStore.load({params: {start: 0, limit: pageS}});	// load DataStore
	kursListEditorGrid.on('afteredit', jenis_update); // inLine Editing Record
	
	/* Identify  jenis_id Field */
	kurs_idField= new Ext.form.NumberField({
		id: 'kurs_idField',
		allowNegatife : false,
		blankText: '0',
		allowBlank: false,
		allowDecimals: false,
		hidden: true,
		readOnly: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});
	/* Identify  jenis_kode Field */
	kurs_negaraField= new Ext.form.TextField({
		id: 'kurs_negaraField',
		fieldLabel: 'Negara <span style="color: #ec0000">*</span>',
		maxLength: 10,
		allowBlank: false,
		anchor: '95%'
	});
	/* Identify  jenis_nama Field */
	kurs_initialField= new Ext.form.TextField({
		id: 'kurs_initialField',
		fieldLabel: 'Initial <span style="color: #ec0000">*</span>',
		maxLength: 250,
		allowBlank: false,
		anchor: '95%'
	});
	/* Identify  produk_harga Field */
	kurs_nilaiField= new Ext.form.TextField({
		id: 'kurs_nilaiField',
		name: 'kurs_nilaiField',
		fieldLabel: 'Nilai Kurs <span style="color: #ec0000">*</span>',
		valueRenderer: 'numberToCurrency',
		itemCls: 'rmoney',
		allowBlank: true,
		width: 150,
		maskRe: /([0-9]+)$/
	});
	/* Identify  note_tanggal Field */
	kurs_tanggalField= new Ext.form.DateField({
		id: 'kurs_tanggalField',
		fieldLabel: 'Tanggal dan Jam',
		format : 'd-m-Y H:i:s',
		emptyText: dt.format('d-m-Y H:i:s'),
		blankText: dt.format('d-m-Y H:i:s'),	
		allowBlank: true,
		//readOnly: true,
		hideTrigger: false,
		width: 150
	});

	/* Identify  jenis_keterangan Field */
	kurs_keteranganField= new Ext.form.TextArea({
		id: 'kurs_keteranganField',
		fieldLabel: 'Keterangan',
		maxLength: 250,
		anchor: '95%'
	});
	/* Identify  jenis_aktif Field */
	kurs_aktifField= new Ext.form.ComboBox({
		id: 'kurs_aktifField',
		fieldLabel: 'Status',
		store:new Ext.data.SimpleStore({
			fields:['kurs_aktif_value', 'kurs_aktif_display'],
			data:[['Aktif','Aktif'],['Tidak Aktif','Tidak Aktif']]
		}),
		mode: 'local',
		editable:false,
		emptyText: 'Aktif',
		displayField: 'kurs_aktif_display',
		valueField: 'kurs_aktif_value',
		width: 80,
		triggerAction: 'all'	
	});

	
	/* Function for retrieve create Window Panel*/ 
	kurs_createForm = new Ext.FormPanel({
		labelAlign: 'left',
		bodyStyle:'padding:5px',
		autoHeight:true,
		width: 300,        
		items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [kurs_idField, kurs_tanggalField, kurs_negaraField, kurs_initialField, kurs_nilaiField, kurs_keteranganField, kurs_aktifField] 
			}
			],
		buttons: [
			<? if(eregi('U|C',$this->m_security->get_access_group_by_kode('MENU_GROUP2'))){ ?>
			{
				text: 'Save and Close',
				handler: kurs_create
			}
			,
			<? } ?>
			{
				text: 'Cancel',
				handler: function(){
					kurs_createWindow.hide();
				}
			}
		]
	});
	/* End  of Function*/
	
	/* Function for retrieve create Window Form */
	kurs_createWindow= new Ext.Window({
		id: 'kurs_createWindow',
		title: post2db+'Kurs',
		closable:true,
		closeAction: 'hide',
		autoWidth: true,
		autoHeight: true,
		x:0,
		y:0,
		plain:true,
		layout: 'fit',
		modal: true,
		renderTo: 'elwindow_kurs_create',
		items: kurs_createForm
	});
	/* End Window */
	
	/* Function for action list search */
	function jenis_list_search(){
		// render according to a SQL date format.
		var jenis_id_search=null;
		var jenis_kode_search=null;
		var jenis_nama_search=null;
		var jenis_keterangan_search=null;
		var jenis_aktif_search=null;

		if(jenis_idSearchField.getValue()!==null){jenis_id_search=jenis_idSearchField.getValue();}
		if(jenis_kodeSearchField.getValue()!==null){jenis_kode_search=jenis_kodeSearchField.getValue();}
		if(jenis_namaSearchField.getValue()!==null){jenis_nama_search=jenis_namaSearchField.getValue();}
		if(jenis_keteranganSearchField.getValue()!==null){jenis_keterangan_search=jenis_keteranganSearchField.getValue();}
		if(jenis_aktifSearchField.getValue()!==null){jenis_aktif_search=jenis_aktifSearchField.getValue();}
		// change the store parameters
		kurs_DataStore.baseParams = {
			task: 'SEARCH',
			start: 0,
			limit: pageS,
			//variable here
			jenis_id	:	jenis_id_search, 
			jenis_kode	:	jenis_kode_search, 
			jenis_keterangan	:	jenis_keterangan_search, 
			jenis_aktif	:	jenis_aktif_search, 
		};
		// Cause the datastore to do another query : 
		kurs_DataStore.reload({params: {start: 0, limit: pageS}});
	}
		
	/* Function for reset search result */
	function jenis_reset_search(){
		// reset the store parameters
		kurs_DataStore.baseParams = { task: 'LIST', start:0, limit:pageS };
		// Cause the datastore to do another query : 
		kurs_DataStore.reload({params: {start: 0, limit: pageS}});
		//jenis_searchWindow.close();
	};
	/* End of Fuction */
	
	/* Field for search */
	/* Identify  jenis_id Search Field */
	jenis_idSearchField= new Ext.form.NumberField({
		id: 'jenis_idSearchField',
		fieldLabel: 'Id',
		allowNegatife : false,
		blankText: '0',
		allowDecimals: false,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	
	});
	/* Identify  jenis_kode Search Field */
	jenis_kodeSearchField= new Ext.form.TextField({
		id: 'jenis_kodeSearchField',
		fieldLabel: 'Kode',
		maxLength: 10,
		anchor: '95%'
	
	});
	/* Identify  jenis_nama Search Field */
	jenis_namaSearchField= new Ext.form.TextField({
		id: 'jenis_namaSearchField',
		fieldLabel: 'Nama',
		maxLength: 250,
		anchor: '95%'
	
	});
	/* Identify  jenis_keterangan Search Field */
	jenis_keteranganSearchField= new Ext.form.TextArea({
		id: 'jenis_keteranganSearchField',
		fieldLabel: 'Keterangan',
		maxLength: 250,
		anchor: '95%'
	
	});
	/* Identify  jenis_aktif Search Field */
	jenis_aktifSearchField= new Ext.form.ComboBox({
		id: 'jenis_aktifSearchField',
		fieldLabel: 'Status',
		store:new Ext.data.SimpleStore({
			fields:['value', 'jenis_aktif'],
			data:[['Aktif','Aktif'],['Tidak Aktif','Tidak Aktif']]
		}),
		mode: 'local',
		displayField: 'jenis_aktif',
		valueField: 'value',
		emptyText: 'Aktif',
		width: 80,
		triggerAction: 'all'	 
	
	});
    
	/* Function for retrieve search Form Panel */
	jenis_searchForm = new Ext.FormPanel({
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
				items: [jenis_kodeSearchField, jenis_namaSearchField, jenis_keteranganSearchField, jenis_aktifSearchField] 
			}
			]
		}]
		,
		buttons: [{
				text: 'Search',
				handler: jenis_list_search
			},{
				text: 'Close',
				handler: function(){
					jenis_searchWindow.hide();
				}
			}
		]
	});
    /* End of Function */ 
	 
	/* Function for retrieve search Window Form, used for andvaced search */
	jenis_searchWindow = new Ext.Window({
		title: 'Pencarian Group 2',
		closable:true,
		closeAction: 'hide',
		autoWidth: true,
		autoHeight: true,
		plain:true,
		layout: 'fit',
		x: 0,
		y: 0,
		modal: true,
		renderTo: 'elwindow_kurs_search',
		items: jenis_searchForm
	});
    /* End of Function */ 
	
	function jenis_reset_SearchForm(){
		jenis_kodeSearchField.reset();
		jenis_kodeSearchField.setValue(null);
		jenis_namaSearchField.reset();
		jenis_namaSearchField.setValue(null);
		jenis_keteranganSearchField.reset();
		jenis_keteranganSearchField.setValue(null);
		jenis_aktifSearchField.reset();
		jenis_aktifSearchField.setValue(null);
	}
	 
  	/* Function for Displaying  Search Window Form */
	function display_form_search_window(){
		if(!jenis_searchWindow.isVisible()){
			jenis_reset_SearchForm();
			jenis_searchWindow.show();
		} else {
			jenis_searchWindow.toFront();
		}
	}
  	/* End Function */
	
	/* Function for print List Grid */
	function jenis_print(){
		var searchquery = "";
		var jenis_kode_print=null;
		var jenis_nama_print=null;
		var jenis_keterangan_print=null;
		var jenis_aktif_print=null;
		var win;              
		// check if we do have some search data...
		if(kurs_DataStore.baseParams.query!==null){searchquery = kurs_DataStore.baseParams.query;}
		if(kurs_DataStore.baseParams.jenis_kode!==null){jenis_kode_print = kurs_DataStore.baseParams.jenis_kode;}
		if(kurs_DataStore.baseParams.jenis_nama!==null){jenis_nama_print = kurs_DataStore.baseParams.jenis_nama;}
		if(kurs_DataStore.baseParams.jenis_keterangan!==null){jenis_keterangan_print = kurs_DataStore.baseParams.jenis_keterangan;}
		if(kurs_DataStore.baseParams.jenis_aktif!==null){jenis_aktif_print = kurs_DataStore.baseParams.jenis_aktif;}

		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_kurs&m=get_action',
		params: {
			task: "PRINT",
		  	query: searchquery,                    		// if we are doing a quicksearch, use this
			//if we are doing advanced search, use this
			jenis_kode : jenis_kode_print,
			jenis_keterangan : jenis_keterangan_print,
			jenis_aktif : jenis_aktif_print,
		  	currentlisting: kurs_DataStore.baseParams.task // this tells us if we are searching or not
		}, 
		success: function(response){              
		  	var result=eval(response.responseText);
		  	switch(result){
		  	case 1:
				win = window.open('./jenislist.html','jenislist','height=400,width=600,resizable=1,scrollbars=1, menubar=1');
				
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
	function jenis_export_excel(){
		var searchquery = "";
		var jenis_kode_2excel=null;
		var jenis_nama_2excel=null;
		var jenis_keterangan_2excel=null;
		var jenis_aktif_2excel=null;
		var win;              
		// check if we do have some search data...
		if(kurs_DataStore.baseParams.query!==null){searchquery = kurs_DataStore.baseParams.query;}
		if(kurs_DataStore.baseParams.jenis_kode!==null){jenis_kode_2excel = kurs_DataStore.baseParams.jenis_kode;}
		if(kurs_DataStore.baseParams.jenis_nama!==null){jenis_nama_2excel = kurs_DataStore.baseParams.jenis_nama;}
		if(kurs_DataStore.baseParams.jenis_keterangan!==null){jenis_keterangan_2excel = kurs_DataStore.baseParams.jenis_keterangan;}
		if(kurs_DataStore.baseParams.jenis_aktif!==null){jenis_aktif_2excel = kurs_DataStore.baseParams.jenis_aktif;}

		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_kurs&m=get_action',
		params: {
			task: "EXCEL",
		  	query: searchquery,                    		// if we are doing a quicksearch, use this
			//if we are doing advanced search, use this
			jenis_kode : jenis_kode_2excel,
			jenis_nama : jenis_nama_2excel,
			jenis_keterangan : jenis_keterangan_2excel,
			jenis_aktif : jenis_aktif_2excel,
		  	currentlisting: kurs_DataStore.baseParams.task // this tells us if we are searching or not
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
	
	// EVENT //
	kurs_nilaiField.on('focus',function(){ kurs_nilaiField.setValue(convertToNumber(kurs_nilaiField.getValue())); });
	kurs_nilaiField.on('blur',function(){ kurs_nilaiField.setValue(CurrencyFormatted(kurs_nilaiField.getValue())); });
	
});
	</script>
<body>
<div>
	<div class="col">
        <div id="fp_kurs"></div>
		<div id="elwindow_kurs_create"></div>
        <div id="elwindow_kurs_search"></div>
    </div>
</div>
</body>