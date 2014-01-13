<?
/* 	GIOV Solution - Keep IT Simple
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
var master_lunas_hutang_DataStore;
var master_lunas_hutang_ColumnModel;
var master_lunas_hutangListEditorGrid;
var master_lunas_hutang_createForm;
var master_lunas_hutang_createWindow;
var master_lunas_hutang_searchForm;
var master_lunas_hutang_searchWindow;
var master_lunas_hutang_SelectedRow;
var master_lunas_hutang_ContextMenu;
//for detail data
var detail_fhutang_bylh_DataStore;
var detail_fhutangListEditorGrid;
var detail_fhutang_ColumnModel;
//var detail_fpiutang_proxy;
var detail_fhutang_writer;
var detail_fhutang_reader;
var editor_detail_fhutang;
var today=new Date().format('Y-m-d');
var firstday=(new Date().format('Y-m'))+'-01';
//declare konstant
var fhutang_post2db = '';
var msg = '';
var fhutang_pageS=15;
var cetak_lh=0;
//var acc_group=<?=$_SESSION[SESSION_GROUPID];?>;
var stat='ADD';
/* declare variable here for Field*/
var fhutang_idField;
var fhutang_nobuktiField;
var fhutang_supplierField;
var fhutang_tanggalField;
var fhutang_keteranganField;

var fhutang_idSearchField;
var fhutang_noSearchField;
var fhutang_tanggalSearchField;
var fhutang_tanggal_akhirSearchField;
var fhutang_carabayarSearchField;
var fhutang_keteranganSearchField;
var fhutang_statusSearchField;
//var detail_fhutang_bylh_DataStore;

var fhutang_cek_bankField;
var fhutang_transfer_bankField;

var dt = new Date();
/* on ready fuction */
Ext.onReady(function(){
  	Ext.QuickTips.init();	/* Initiate quick tips icon */
	
	Ext.util.Format.comboRenderer = function(combo){
		return function(value){
			var record = combo.findRecord(combo.valueField, value);
			return record ? record.get(combo.displayField) : combo.valueNotFoundText;
		}
	}
	
	function fhutang_cetak(master_id){
		Ext.Ajax.request({   
			waitMsg: 'Mohon tunggu...',
			url: 'index.php?c=c_master_lunas_hutang&m=print_paper',
			params: { fhutang_id : master_id}, 
			success: function(response){              
				var result=eval(response.responseText);
				switch(result){
				case 1:
					win = window.open('./fpiutang_paper.html','Cetak Pelunasan Hutang','height=480,width=1340,resizable=1,scrollbars=0, menubar=0');
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
  
  /*Function for pengecekan _dokumen */
	function pengecekan_dokumen(){
		var fhutang_tgl_create = "";
		if(fhutang_tanggalField.getValue()!== ""){
			fhutang_tgl_create = fhutang_tanggalField.getValue().format('Y-m-d');
			Ext.Ajax.request({  
				waitMsg: 'Please wait...',
				url: 'index.php?c=c_master_lunas_hutang&m=get_action',
				params: {
					task: "CEK",
					tanggal_pengecekan	: fhutang_tgl_create
				}, 
				success: function(response){							
					var result=eval(response.responseText);
					switch(result){
						case 1:
							cetak_lh=1;
							master_lunas_hutang_create('print');
						break;
						default:
						Ext.MessageBox.show({
							title: 'Warning',
							msg: 'Data Pelunasan Hutang tidak bisa disimpan, karena telah melebihi batas hari yang diperbolehkan ',
							buttons: Ext.MessageBox.OK,
							animEl: 'save',
							icon: Ext.MessageBox.WARNING,
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
		}else{
			Ext.MessageBox.show({
				title: 'Warning',
				msg: 'Tanggal tidak boleh kosong.',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		}
	}
	
	/*Function for pengecekan _dokumen untuk save */
	function pengecekan_dokumen2(){
		var fhutang_tgl_create = "";
		if(fhutang_tanggalField.getValue()!== ""){
			fhutang_tgl_create = fhutang_tanggalField.getValue().format('Y-m-d');
			Ext.Ajax.request({  
				waitMsg: 'Please wait...',
				url: 'index.php?c=c_master_lunas_hutang&m=get_action',
				params: {
					task: "CEK",
					tanggal_pengecekan	: fhutang_tgl_create
				}, 
				success: function(response){							
					var result=eval(response.responseText);
					switch(result){
						case 1:
							master_lunas_hutang_create();
						break;
						default:
						Ext.MessageBox.show({
						   title: 'Warning',
						   msg: 'Pelunasan Hutang tidak bisa disimpan, karena telah melebihi batas hari yang diperbolehkan ',
						   buttons: Ext.MessageBox.OK,
						   animEl: 'save',
						   icon: Ext.MessageBox.WARNING,
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
		}else{
			Ext.MessageBox.show({
				title: 'Warning',
				msg: 'Tanggal tidak boleh kosong.',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		}  
	}
	
  
  	/* Function for add data, open window create form */
	function master_lunas_hutang_create(opsi){
		
		if(detail_fhutang_bylh_DataStore.getCount()<1){
			Ext.MessageBox.show({
				title: 'Warning',
				msg: 'Data detail harus ada minimal 1 (satu)',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		} else if(is_master_lunas_hutang_form_valid()){
			if(((/^\d+$/.test(fhutang_supplierField.getValue()) && fhutang_post2db=="CREATE") || fhutang_post2db=="UPDATE")
			   && (fhutang_stat_dokField.getValue()=='Terbuka')){
				
				/*
				 * DATA-DATA MASTER
				*/
				var fpiutang_id_create_pk=0;
				var fpiutang_no_create='';
				var fpiutang_cust_create='';
				var fhutang_tgl_create='';
				var fpiutang_keterangan_create='';
				var fpiutang_status_create='';
				var fpiutang_cara_create='';
				
				//bayar
				var fpiutang_bayar_create=0;
				
				//kwitansi
				var fpiutang_kwitansi_nama_create="";
				var fpiutang_kwitansi_nomor_create="";
				//card
				var fpiutang_card_nama_create="";
				var fpiutang_card_edc_create="";
				var fpiutang_card_no_create="";
				//cek
				var fpiutang_cek_nama_create="";
				var fpiutang_cek_nomor_create="";
				var fpiutang_cek_valid_create="";
				var fpiutang_cek_bank_create="";
				//transfer
				var fpiutang_transfer_bank_create="";
				var fpiutang_transfer_nama_create="";
				
				if((fhutang_idField.getValue()!==null) && (fhutang_idField.getValue()!==0) && (fhutang_idField.getValue()!=='')){fpiutang_id_create_pk = fhutang_idField.getValue();}else{fpiutang_id_create_pk=get_pk_id();} 
				if((fhutang_nobuktiField.getValue()!==null) && (fhutang_nobuktiField.getValue()!=='')){fpiutang_no_create = fhutang_nobuktiField.getValue();}
				if((fhutang_supplierField.getValue()!==null) && (fhutang_supplierField.getValue()!=='') && (fhutang_post2db=="CREATE")){
					fpiutang_cust_create = fhutang_supplierField.getValue();
				}else if(fhutang_post2db=="UPDATE"){
					fpiutang_cust_create = fhutang_customer_idField.getValue();
				}
				if((fhutang_tanggalField.getValue()!==null) && (fhutang_tanggalField.getValue()!=='')){fhutang_tgl_create = fhutang_tanggalField.getValue().format('Y-m-d');} 
				if((fhutang_keteranganField.getValue()!==null) && (fhutang_keteranganField.getValue()!=='')){fpiutang_keterangan_create = fhutang_keteranganField.getValue();} 
				if((fhutang_stat_dokField.getValue()!==null) && (fhutang_stat_dokField.getValue()!=='')){fpiutang_status_create = fhutang_stat_dokField.getValue();}
				if((fhutang_caraField.getValue()!==null) && (fhutang_caraField.getValue()!=='')){fpiutang_cara_create = fhutang_caraField.getValue();}
				
				//bayar
				if((fhutang_bayarField.getValue()!==null) && (fhutang_bayarField.getValue()!==0) && (fhutang_bayarField.getValue()!=='')){fpiutang_bayar_create = fhutang_bayarField.getValue();}
				//kwitansi value
				if((fhutang_kwitansi_noField.getValue()!=="") && (fhutang_post2db=='CREATE')){
					fpiutang_kwitansi_nomor_create = fhutang_kwitansi_noField.getValue();
				}else if(fhutang_post2db=='UPDATE'){
					fpiutang_kwitansi_nomor_create = fhutang_kwitansi_idField.getValue();
				}
				if(fhutang_kwitansi_namaField.getValue()!== ""){fpiutang_kwitansi_nama_create = fhutang_kwitansi_namaField.getValue();} 
				//card value
				if(fhutang_card_namaField.getValue()!== ""){fpiutang_card_nama_create = fhutang_card_namaField.getValue();} 
				if(fhutang_card_edcField.getValue()!==""){fpiutang_card_edc_create = fhutang_card_edcField.getValue();} 
				if(fhutang_card_noField.getValue()!==""){fpiutang_card_no_create = fhutang_card_noField.getValue();}
				//cek value
				if(fhutang_cek_namaField.getValue()!== ""){fpiutang_cek_nama_create = fhutang_cek_namaField.getValue();} 
				if(fhutang_cek_noField.getValue()!== ""){fpiutang_cek_nomor_create = fhutang_cek_noField.getValue();} 
				if(fhutang_cek_validField.getValue()!== ""){fpiutang_cek_valid_create = fhutang_cek_validField.getValue().format('Y-m-d');} 
				if(fhutang_cek_bankField.getValue()!== ""){fpiutang_cek_bank_create = fhutang_cek_bankField.getValue();} 
				//transfer value
				if(fhutang_transfer_bankField.getValue()!== ""){fpiutang_transfer_bank_create = fhutang_transfer_bankField.getValue();} 
				if(fhutang_transfer_namaField.getValue()!== ""){fpiutang_transfer_nama_create = fhutang_transfer_namaField.getValue();}
				
				/*
				 * DATA-DATA DETAIL
				*/
				var dhutang_id = [];
				var hutang_id = [];
				var dhutang_nilai = [];
				var dhutang_keterangan = [];
				
				var dcount = detail_fhutang_bylh_DataStore.getCount() - 1;
				
				for(i=0; i<detail_fhutang_bylh_DataStore.getCount(); i++){
					if((/^\d+$/.test(detail_fhutang_bylh_DataStore.getAt(i).data.hutang_id))
					   && detail_fhutang_bylh_DataStore.getAt(i).data.hutang_id!==undefined
					   && detail_fhutang_bylh_DataStore.getAt(i).data.hutang_id!==''
					   && detail_fhutang_bylh_DataStore.getAt(i).data.hutang_id!==0){
						
						dhutang_id.push(detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_id);
						//alert(hutang_id);
						hutang_id.push(detail_fhutang_bylh_DataStore.getAt(i).data.hutang_id);
						
						
						
						if((detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_nilai==undefined)
						   || (detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_nilai=='')){
							dhutang_nilai.push(0);
						}else{
							dhutang_nilai.push(detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_nilai);
						}
						
						if((detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_keterangan==undefined)){
							dhutang_keterangan.push('');
						}else{
							dhutang_keterangan.push(detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_keterangan);
						}
					}
					
					if(i==dcount){
						var encoded_array_dpiutang_id = Ext.encode(dhutang_id);
						var encoded_array_lpiutang_id = Ext.encode(hutang_id);
						var encoded_array_dpiutang_nilai = Ext.encode(dhutang_nilai);
						var encoded_array_dpiutang_keterangan = Ext.encode(dhutang_keterangan);
						
						Ext.Ajax.request({  
							waitMsg: 'Mohon tunggu...',
							url: 'index.php?c=c_master_lunas_hutang&m=get_action',
							params: {
								task				: fhutang_post2db,
								fhutang_id			: fpiutang_id_create_pk, 
								fpiutang_no			: fpiutang_no_create,
								fpiutang_cust		: fpiutang_cust_create,
								fpiutang_tanggal	: fhutang_tgl_create,
								fpiutang_keterangan	: fpiutang_keterangan_create,
								fpiutang_status		: fpiutang_status_create,
								fpiutang_cara		: fpiutang_cara_create, 
								fpiutang_bayar		: fpiutang_bayar_create,
								//kwitansi posting
								fpiutang_kwitansi_no		:	fpiutang_kwitansi_nomor_create,
								fpiutang_kwitansi_nama		:	fpiutang_kwitansi_nama_create,
								//card posting
								fpiutang_card_nama	: 	fpiutang_card_nama_create,
								fpiutang_card_edc	:	fpiutang_card_edc_create,
								fpiutang_card_no		:	fpiutang_card_no_create,
								//cek posting
								fpiutang_cek_nama	: 	fpiutang_cek_nama_create,
								fpiutang_cek_no		:	fpiutang_cek_nomor_create,
								fpiutang_cek_valid	: 	fpiutang_cek_valid_create,
								fpiutang_cek_bank	:	fpiutang_cek_bank_create,
								//transfer posting
								fpiutang_transfer_bank	:	fpiutang_transfer_bank_create,
								fpiutang_transfer_nama	:	fpiutang_transfer_nama_create,
								cetak_lp 	: cetak_lh,
								
								//DATA DETAIL
								dhutang_id: encoded_array_dpiutang_id,
								hutang_id: encoded_array_lpiutang_id,
								dhutang_nilai: encoded_array_dpiutang_nilai,
								dhutang_keterangan: encoded_array_dpiutang_keterangan
							}, 
							success: function(response){
								var result=eval(response.responseText);
								if(result==0){
									Ext.MessageBox.alert(fhutang_post2db+' OK','Data Pelunasan Hutang berhasil disimpan');
									master_lunas_hutang_createWindow.hide();
									master_lunas_hutang_DataStore.reload();
									fpiutang_btn_cancel();
								}else if(result>0){
									fhutang_cetak(result);
									Ext.MessageBox.alert(fhutang_post2db+' OK','Data Pelunasan Hutang berhasil disimpan');
									master_lunas_hutang_createWindow.hide();
									master_lunas_hutang_DataStore.reload();
									fpiutang_btn_cancel();
								}else{
									Ext.MessageBox.show({
									   title: 'Warning',
									   msg: 'Data Pelunasan Hutang tidak bisa disimpan',
									   buttons: Ext.MessageBox.OK,
									   animEl: 'save',
									   icon: Ext.MessageBox.WARNING
									});
									master_lunas_hutang_DataStore.reload();
									fpiutang_btn_cancel();
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
								master_lunas_hutang_DataStore.reload();
								fpiutang_btn_cancel();
							}                      
						});
					}
				}
			}else if(fhutang_post2db=='UPDATE' && fhutang_stat_dokField.getValue()=='Tertutup'){
				if(cetak_lh==1){
					fhutang_cetak(get_pk_id());
					cetak_lh=0;
				}
				fpiutang_btn_cancel();
			}else if(fhutang_post2db=='UPDATE' && fhutang_stat_dokField.getValue()=='Batal'){
				Ext.Ajax.request({  
					waitMsg: 'Mohon  Tunggu...',
					url: 'index.php?c=c_master_lunas_hutang&m=get_action',
					params: {
						task: 'BATAL',
						fhutang_id	: fhutang_idField.getValue(),
						fpiutang_tanggal : fhutang_tanggalField.getValue().format('Y-m-d')
					}, 
					success: function(response){             
						var result=eval(response.responseText);
						if(result==1){
							fhutang_post2db='CREATE';
							Ext.MessageBox.show({
							   title: 'Warning',
							   msg: 'Dokumen Pelunasan Hutang telah dibatalkan.',
							   buttons: Ext.MessageBox.OK,
							   animEl: 'save',
							   icon: Ext.MessageBox.OK
							});
							master_lunas_hutang_createWindow.hide();
							master_lunas_hutang_DataStore.reload();
							fpiutang_btn_cancel();
						}else{
							fhutang_post2db='CREATE';
							Ext.MessageBox.show({
							   title: 'Warning',
							   width: 400,
							   msg: 'Dokumen Pelunasan Hutang tidak bisa dibatalkan, <br/>karena yang boleh dibatalkan adalah Dokumen yang terbit hari ini saja.',
							   buttons: Ext.MessageBox.OK,
							   animEl: 'save',
							   icon: Ext.MessageBox.WARNING
							});
							master_lunas_hutang_createWindow.hide();
							master_lunas_hutang_DataStore.reload();
							fpiutang_btn_cancel();
						}
					},
					failure: function(response){
						fhutang_post2db='CREATE';
						var result=response.responseText;
						Ext.MessageBox.show({
							   title: 'Error',
							   msg: 'Could not connect to the database. retry later.',
							   buttons: Ext.MessageBox.OK,
							   animEl: 'database',
							   icon: Ext.MessageBox.ERROR
						});
						master_lunas_hutang_createWindow.hide();
						master_lunas_hutang_DataStore.reload();
						fpiutang_btn_cancel();
					}                      
				});
			}
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
		if(fhutang_post2db=='UPDATE')
			return master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_id');
		else if(fhutang_post2db=='CREATE')
			return fhutang_idField.getValue();
		else 
			return -1;
	}
	/* End of Function  */
	
	/* Reset form before loading */
	function master_lunas_hutang_reset_form(){
		fhutang_idField.reset();
		fhutang_idField.setValue(null);
		fhutang_nobuktiField.reset();
		fhutang_nobuktiField.setValue(null);
		fhutang_supplierField.reset();
		fhutang_supplierField.setValue(null);
		fhutang_tanggalField.setValue(today);
		fhutang_keteranganField.reset();
		fhutang_keteranganField.setValue(null);
		fhutang_stat_dokField.reset();
		fhutang_stat_dokField.setValue('Terbuka');
		fhutang_totalField.reset();
		fhutang_totalField.setValue(0);
		fhutang_total_cfField.reset();
		fhutang_total_cfField.setValue(0);
		fhutang_bayarField.reset();
		fhutang_bayarField.setValue(0);
		fhutang_bayar_cfField.reset();
		fhutang_bayar_cfField.setValue(0);
		fhutang_sisaField.reset();
		fhutang_sisaField.setValue(0);
		fhutang_sisa_cfField.reset();
		fhutang_sisa_cfField.setValue(0);
		fhutang_alamatField.reset();
		fhutang_alamatField.setValue(null);
		
		detail_fhutang_bAdd.setDisabled(false);
		detail_fhutang_bDel.setDisabled(false);
		
		kwitansi_fhutang_reset_form();
		card_fhutang_reset_form();
		cek_fhutang_reset_form();
		transfer_fhutang_reset_form();
		
		fhutang_caraField.setDisabled(false);
		master_lunas_hutang_tunaiGroup.setDisabled(false);
		master_lunas_hutang_cardGroup.setDisabled(false);
		master_lunas_hutang_cekGroup.setDisabled(false);
		master_lunas_hutang_kwitansiGroup.setDisabled(false);
		master_lunas_hutang_transferGroup.setDisabled(false);
		
		fhutang_idField.setDisabled(false);
		fhutang_nobuktiField.setDisabled(false);
		fhutang_supplierField.setDisabled(false);
		fhutang_tanggalField.setDisabled(false);
		fhutang_keteranganField.setDisabled(false);
		fhutang_stat_dokField.setDisabled(false);
		detail_fhutang_bylh_DataStore.load({params: {fhutang_id:-1}});
		master_lunas_hutang_createForm.fhutang_savePrint.enable();
		
		dcbo_forder_hutang.setDisabled(false);
		dfhutang_sisaField.setDisabled(false);
		dfhutang_bayarField.setDisabled(false);
		dfhutang_ketField.setDisabled(false);
	}
 	/* End of Function */
	
	// Reset kwitansi option
	function kwitansi_fhutang_reset_form(){
		fhutang_kwitansi_namaField.reset();
		fhutang_kwitansi_noField.reset();
		fhutang_kwitansi_sisaField.reset();
		fhutang_kwitansi_namaField.setValue("");
		fhutang_kwitansi_noField.setValue("");
		fhutang_kwitansi_sisaField.setValue(null);
	}
	
	// Reset card option
	function card_fhutang_reset_form(){
		fhutang_card_namaField.reset();
		fhutang_card_edcField.reset();
		fhutang_card_noField.reset();
		fhutang_card_namaField.setValue("");
		fhutang_card_edcField.setValue("");
		fhutang_card_noField.setValue("");
	}
	
	// Reset cek option
	function cek_fhutang_reset_form(){
		fhutang_cek_namaField.reset();
		fhutang_cek_noField.reset();
		fhutang_cek_validField.reset();
		fhutang_cek_bankField.reset();
		fhutang_cek_namaField.setValue(null);
		fhutang_cek_noField.setValue("");
		fhutang_cek_validField.setValue("");
		fhutang_cek_bankField.setValue("");
	}
	
	// Reset transfer option
	function transfer_fhutang_reset_form(){
		fhutang_transfer_bankField.reset();
		fhutang_transfer_namaField.reset();
		fhutang_transfer_bankField.setValue("");
		fhutang_transfer_namaField.setValue(null);
	}
  
	/* setValue to EDIT */
	function master_lunas_hutang_set_form(){
		var total_bayar = 0;
		var total_hutang = master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('hutang_total');
		var sisa_hutang = 0;
		
		fhutang_idField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_id'));
		fhutang_nobuktiField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_nobukti'));
		fhutang_supplierField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('supplier_nama'));
		fhutang_customer_idField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_cust'));
		fhutang_alamatField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('supplier_alamat'));
		fhutang_tanggalField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_tanggal'));
		fhutang_keteranganField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_keterangan'));
		fhutang_stat_dokField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok'));
		fhutang_caraField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_cara'));
		fhutang_total_cfField.setValue(CurrencyFormatted(total_hutang));
		fhutang_totalField.setValue(total_hutang);
		
		for(i=0; i<detail_fhutang_bylh_DataStore.getCount(); i++){
			total_bayar+=detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_nilai;
		}
		sisa_hutang = (total_hutang - total_bayar);
		
		fhutang_bayar_cfField.setValue(CurrencyFormatted(total_bayar));
		fhutang_bayarField.setValue(total_bayar);
		
		fhutang_sisa_cfField.setValue(CurrencyFormatted(sisa_hutang));
		fhutang_sisaField.setValue(sisa_hutang);
		
		cbo_forder_hutangDataStore.load();
		
		if(fhutang_post2db=="UPDATE" && master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok')=="Terbuka"){
			fhutang_idField.setDisabled(false);
			fhutang_nobuktiField.setDisabled(false);
			fhutang_supplierField.setDisabled(false);
			fhutang_tanggalField.setDisabled(false);
			fhutang_keteranganField.setDisabled(false);
			fhutang_stat_dokField.setDisabled(false);
			master_lunas_hutang_createForm.fhutang_savePrint.enable();
		}
		if(fhutang_post2db=="UPDATE" && master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok')=="Tertutup"){
			fhutang_idField.setDisabled(true);
			fhutang_nobuktiField.setDisabled(true);
			fhutang_supplierField.setDisabled(true);
			fhutang_tanggalField.setDisabled(true);
			fhutang_keteranganField.setDisabled(true);
			fhutang_stat_dokField.setDisabled(false);
			if(cetak_lh==1){
				cetak_lh=0;
			}
			master_lunas_hutang_createForm.fhutang_savePrint.disable();
		}
		if(fhutang_post2db=="UPDATE" && master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok')=="Batal"){
			fhutang_idField.setDisabled(true);
			fhutang_nobuktiField.setDisabled(true);
			fhutang_supplierField.setDisabled(true);
			fhutang_tanggalField.setDisabled(true);
			fhutang_keteranganField.setDisabled(true);
			fhutang_stat_dokField.setDisabled(true);
			master_lunas_hutang_createForm.fhutang_savePrint.disable();
		}
		
		update_group_carabayar_lunas_hutang();
		
		switch(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_cara')){
		/*
			case 'kwitansi':
				kwitansi_fhutang_DataStore.load({
					params : {
						no_faktur: fhutang_nobuktiField.getValue(),
						cara_bayar_ke: 1
					},
					callback: function(opts, success, response)  {
						  if (success) {
							if(kwitansi_fhutang_DataStore.getCount()){
								fpiutang_kwitansi_record=kwitansi_fhutang_DataStore.getAt(0).data;
								fhutang_kwitansi_idField.setValue(fpiutang_kwitansi_record.kwitansi_id);
								fhutang_kwitansi_noField.setValue(fpiutang_kwitansi_record.kwitansi_no);
								fhutang_kwitansi_namaField.setValue(fpiutang_kwitansi_record.cust_nama);
								fhutang_kwitansi_sisaField.setValue(fpiutang_kwitansi_record.kwitansi_sisa);
							}
						  }
					  }
				});
				break;
				*/
			case 'card' :
				card_fhutang_DataStore.load({
					params : {
						no_faktur: fhutang_nobuktiField.getValue(),
						cara_bayar_ke: 1
					},
					callback: function(opts, success, response)  {
						 if (success) { 
							if(card_fhutang_DataStore.getCount()){
								fpiutang_card_record=card_fhutang_DataStore.getAt(0).data;
								fhutang_card_namaField.setValue(fpiutang_card_record.jcard_nama);
								fhutang_card_edcField.setValue(fpiutang_card_record.jcard_edc);
								fhutang_card_noField.setValue(fpiutang_card_record.jcard_no);
							}
						 }
					}
				});
				break;
			case 'cek/giro':
				cek_fhutang_DataStore.load({
					params : {
						no_faktur: fhutang_nobuktiField.getValue(),
						cara_bayar_ke: 1
					},
					callback: function(opts, success, response)  {
							if (success) {
								if(cek_fhutang_DataStore.getCount()){
									fpiutang_cek_record=cek_fhutang_DataStore.getAt(0).data;
									fhutang_cek_namaField.setValue(fpiutang_cek_record.jcek_nama);
									fhutang_cek_noField.setValue(fpiutang_cek_record.jcek_no);
									fhutang_cek_validField.setValue(fpiutang_cek_record.jcek_valid);
									fhutang_cek_bankField.setValue(fpiutang_cek_record.jcek_bank);
								}
							}
					 	}
				  });
				break;								
			case 'transfer' :
				transfer_fhutang_DataStore.load({
						params : {
							no_faktur: fhutang_nobuktiField.getValue(),
							cara_bayar_ke: 1
						},
					  	callback: function(opts, success, response)  {
							if (success) {
									if(transfer_fhutang_DataStore.getCount()){
										fhutang_transfer_record=transfer_fhutang_DataStore.getAt(0);
										fhutang_transfer_bankField.setValue(fhutang_transfer_record.data.jtransfer_bank);
										fhutang_transfer_namaField.setValue(fhutang_transfer_record.data.jtransfer_nama);
									}
							}
					 	}
				  });
				break;
			case 'tunai' :
				tunai_fhutang_DataStore.load({
					params : {
						no_faktur: fhutang_nobuktiField.getValue(),
						cara_bayar_ke: 1
					},
					callback: function(opts, success, response)  {
						if (success) {
							if(tunai_fhutang_DataStore.getCount()){
								fpiutang_tunai_record=tunai_fhutang_DataStore.getAt(0);
							}
						}
					}
				});
				break;
		}
		
		fhutang_stat_dokField.on("select",function(){
		var status_awal = master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok');
		if(status_awal =='Terbuka' && fhutang_stat_dokField.getValue()=='Tertutup')
		{
		Ext.MessageBox.show({
			msg: 'Dokumen tidak bisa ditutup. Gunakan Save & Print untuk menutup dokumen',
			buttons: Ext.MessageBox.OK,
			animEl: 'save',
			icon: Ext.MessageBox.WARNING
		   });
		fhutang_stat_dokField.setValue('Terbuka');
		}
		
		else if(status_awal =='Tertutup' && fhutang_stat_dokField.getValue()=='Terbuka')
		{
		Ext.MessageBox.show({
			msg: 'Faktur ini sudah pernah di Save and Print sebelumnya, klik Save dahulu di kanan bawah agar Status Dok menjadi Terbuka',
			buttons: Ext.MessageBox.OK,
			animEl: 'save',
			icon: Ext.MessageBox.WARNING
		   });
		fhutang_stat_dokField.setValue('Terbuka');
		}
		
		else if(status_awal =='Batal' && fhutang_stat_dokField.getValue()=='Terbuka')
		{
		Ext.MessageBox.show({
			msg: 'Status yang sudah Tertutup tidak dapat diganti Terbuka',
			buttons: Ext.MessageBox.OK,
			animEl: 'save',
			icon: Ext.MessageBox.WARNING
		   });
		fhutang_stat_dokField.setValue('Tertutup');
		}
		
		else if(fhutang_stat_dokField.getValue()=='Batal')
		{
		Ext.MessageBox.confirm('Confirmation','Anda yakin untuk membatalkan dokumen ini? Pembatalan dokumen tidak bisa dikembalikan lagi', fhutang_status_batal);
		}
        
       else if(status_awal =='Tertutup' && fhutang_stat_dokField.getValue()=='Tertutup'){
            <?php if(eregi('U|C',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
			master_lunas_hutang_createForm.fhutang_savePrint.enable();
			<?php } ?>
        }
		
		});
		
	}
	/* End setValue to EDIT*/
  
	function fhutang_status_batal(btn){
		if(btn=='yes')
		{
			fhutang_stat_dokField.setValue('Batal');
			<?php if(eregi('U|C',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
			master_lunas_hutang_createForm.fhutang_savePrint.disable();
			<?php } ?>
		}  
		else
			fhutang_stat_dokField.setValue(master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok'));
	}
	
	function master_lunas_hutang_set_updating(){
		if(fhutang_post2db=="UPDATE" && master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok')=="Terbuka"){
			fhutang_supplierField.setDisabled(true);
			fhutang_tanggalField.setDisabled(true);
			fhutang_keteranganField.setDisabled(false);
			fhutang_caraField.setDisabled(false);
			master_lunas_hutang_tunaiGroup.setDisabled(false);
			master_lunas_hutang_cardGroup.setDisabled(false);
			master_lunas_hutang_cekGroup.setDisabled(false);
			master_lunas_hutang_kwitansiGroup.setDisabled(false);
			master_lunas_hutang_transferGroup.setDisabled(false);
			
            fhutang_stat_dokField.setDisabled(false);
			//Enable Add detail
			detail_fhutang_bAdd.setDisabled(false);
			detail_fhutang_bDel.setDisabled(false);
			
			dcbo_forder_hutang.setDisabled(false);
			dfhutang_sisaField.setDisabled(false);
			dfhutang_bayarField.setDisabled(false);
			dfhutang_ketField.setDisabled(false);
		}
		if(fhutang_post2db=="UPDATE" && master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok')=="Tertutup"){
			fhutang_supplierField.setDisabled(true);
			fhutang_tanggalField.setDisabled(true);
			fhutang_keteranganField.setDisabled(true);
			fhutang_caraField.setDisabled(true);
			master_lunas_hutang_tunaiGroup.setDisabled(true);
			master_lunas_hutang_cardGroup.setDisabled(true);
			master_lunas_hutang_cekGroup.setDisabled(true);
			master_lunas_hutang_kwitansiGroup.setDisabled(true);
			master_lunas_hutang_transferGroup.setDisabled(true);
			
			fhutang_stat_dokField.setDisabled(false);
			//Disable Add detail
			detail_fhutang_bAdd.setDisabled(true);
			detail_fhutang_bDel.setDisabled(true);
			
			dcbo_forder_hutang.setDisabled(true);
			dfhutang_sisaField.setDisabled(true);
			dfhutang_bayarField.setDisabled(true);
			dfhutang_ketField.setDisabled(true);
		}
		if(fhutang_post2db=="UPDATE" && master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_stat_dok')=="Batal"){
			fhutang_supplierField.setDisabled(true);
			fhutang_tanggalField.setDisabled(true);
			fhutang_keteranganField.setDisabled(true);
			fhutang_stat_dokField.setDisabled(true);
			fhutang_caraField.setDisabled(true);
			master_lunas_hutang_tunaiGroup.setDisabled(true);
			master_lunas_hutang_cardGroup.setDisabled(true);
			master_lunas_hutang_cekGroup.setDisabled(true);
			master_lunas_hutang_kwitansiGroup.setDisabled(true);
			master_lunas_hutang_transferGroup.setDisabled(true);
			
			//Disable Add detail
			detail_fhutang_bAdd.setDisabled(true);
			detail_fhutang_bDel.setDisabled(true);
			
			dcbo_forder_hutang.setDisabled(true);
			dfhutang_sisaField.setDisabled(true);
			dfhutang_bayarField.setDisabled(true);
			dfhutang_ketField.setDisabled(true);
			
			<?php if(eregi('U|C',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
			master_lunas_hutang_createForm.fhutang_savePrint.disable();
			<?php } ?>
		}
	}
  
  
	/* Function for Check if the form is valid */
	function is_master_lunas_hutang_form_valid(){
		return true;
	}
  	/* End of Function */
  
  	/* Function for Displaying  create Window Form */
	function display_form_window(){
		if(!master_lunas_hutang_createWindow.isVisible()){
			fhutang_post2db='CREATE';
			msg='created';
			master_lunas_hutang_reset_form();
			master_lunas_hutang_createWindow.show();
		} else {
			master_lunas_hutang_createWindow.toFront();
		}
	}
  	/* End of Function */
 
  	/* Function for Delete Confirm */
	function master_lunas_hutang_confirm_delete(){
		// only one master_lunas_piutang is selected here
		if(master_lunas_hutangListEditorGrid.selModel.getCount() == 1){
			Ext.MessageBox.confirm('Confirmation','Anda yakin untuk menghapus data ini?', master_lunas_hutang_delete);
		} else if(master_lunas_hutangListEditorGrid.selModel.getCount() > 1){
			Ext.MessageBox.confirm('Confirmation','Anda yakin untuk menghapus data ini?', master_lunas_hutang_delete);
		} else {
			Ext.MessageBox.show({
				title: 'Warning',
				//msg: 'Tidak ada yang dipilih untuk dihapus',
				msg: 'Anda belum memilih data yang akan dihapus',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		}
	}
  	/* End of Function */
	
	/* Function for Update Confirm */
	function master_lunas_hutang_confirm_update(){
		/* only one record is selected here */
		if(master_lunas_hutangListEditorGrid.selModel.getCount() == 1) {
			fhutang_post2db='UPDATE';
			msg='updated';
			cbo_forder_hutangDataStore.setBaseParam('fhutang_id',master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_id'));
			cbo_forder_hutangDataStore.setBaseParam('task','selected');
			cbo_forder_hutangDataStore.load({
				callback: function(opts, success, response){
					if(success){
						detail_fhutang_bylh_DataStore.setBaseParam('fhutang_id',master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_id'));
						detail_fhutang_bylh_DataStore.load({
							callback: function(opts, success, response){
								if(success){
									master_lunas_hutang_set_form();
									master_lunas_hutang_set_updating();
								}
							}
						});
					}
				}
			});
			master_lunas_hutang_createWindow.show();
		} else {
			Ext.MessageBox.show({
				title: 'Warning',
				//msg: 'Tidak ada data yang dipilih untuk diedit',
				msg: 'Anda belum memilih data yang akan diubah',
				buttons: Ext.MessageBox.OK,
				animEl: 'save',
				icon: Ext.MessageBox.WARNING
			});
		}
	}
  	/* End of Function */
  
  	/* Function for Delete Record */
	function master_lunas_hutang_delete(btn){
		if(btn=='yes'){
			var selections = master_lunas_hutangListEditorGrid.selModel.getSelections();
			var prez = [];
			for(i = 0; i< master_lunas_hutangListEditorGrid.selModel.getCount(); i++){
				prez.push(selections[i].json.fhutang_id);
			}
			var encoded_array = Ext.encode(prez);
			Ext.Ajax.request({ 
				waitMsg: 'Mohon tunggu',
				url: 'index.php?c=c_master_lunas_hutang&m=get_action', 
				params: { task: "DELETE", ids:  encoded_array }, 
				success: function(response){
					var result=eval(response.responseText);
					switch(result){
						case 1:  // Success : simply reload
							master_lunas_hutang_DataStore.reload();
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
	master_lunas_hutang_DataStore = new Ext.data.Store({
		id: 'master_lunas_hutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_action', 
			method: 'POST'
		}),
		baseParams:{task: "LIST", start:0, limit: fhutang_pageS}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'fhutang_id'
		},[
			{name: 'fhutang_id', type: 'int', mapping: 'fhutang_id'}, 
			{name: 'fhutang_nobukti', type: 'string', mapping: 'fhutang_nobukti'},
			{name: 'fhutang_cust', type: 'int', mapping: 'fhutang_cust'}, 
			{name: 'fhutang_tanggal', type: 'date', dateFormat: 'Y-m-d', mapping: 'fhutang_tanggal'}, 
			{name: 'fhutang_cara', type: 'string', mapping: 'fhutang_cara'},
			{name: 'fhutang_bayar', type: 'float', mapping: 'fhutang_bayar'}, 
			{name: 'fhutang_stat_dok', type: 'string', mapping: 'fhutang_stat_dok'},
			{name: 'fhutang_keterangan', type: 'string', mapping: 'fhutang_keterangan'},
			{name: 'supplier_id', type: 'int', mapping: 'supplier_id'}, 
			{name: 'supplier_nama', type: 'string', mapping: 'supplier_nama'}, 
			{name: 'supplier_alamat', type: 'string', mapping: 'supplier_alamat'}, 
			{name: 'cust_no', type: 'string', mapping: 'cust_no'},
			{name: 'hutang_total', type: 'float', mapping: 'hutang_total'},
			{name: 'hutang_sisa', type: 'float', mapping: 'hutang_sisa'}
		]),
		sortInfo:{field: 'fhutang_id', direction: "DESC"}
	});
	/* End of Function */
	
	/* Function for Retrieve Supplier DataStore */
	var cbo_forder_hutangDataStore = new Ext.data.Store({
		id: 'cbo_forder_hutangDataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_fjual_bycust_list', 
			method: 'POST'
		}),
		baseParams:{task: "detail",start:0,limit:fhutang_pageS}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'hutang_id'
		},[
			{name: 'hutang_id', type: 'int', mapping: 'hutang_id'},
			{name: 'hutang_faktur', type: 'string', mapping: 'hutang_faktur'},
			{name: 'hutang_supplier', type: 'int', mapping: 'hutang_supplier'},
			{name: 'hutang_tanggal', type: 'string', mapping: 'hutang_tanggal'},
			{name: 'hutang_total', type: 'float', mapping: 'hutang_total'},
			{name: 'hutang_sisa', type: 'float', mapping: 'hutang_sisa'},
			{name: 'hutang_stat_dok', type: 'string', mapping: 'hutang_stat_dok'}
		]),
		sortInfo:{field: 'hutang_faktur', direction: "ASC"}
	});
	var cbo_fjual_hutang_tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<span><b>{hutang_faktur}, Tgl: {hutang_tanggal}</b><br /></span>',
            'Jumlah Hutang: {hutang_total} | Sisa Hutang: {hutang_sisa}',
        '</div></tpl>'
    );
	
	/* Function for Retrieve Supplier DataStore */
	var cbo_fhutang_supplierDataStore = new Ext.data.Store({
		id: 'cbo_fhutang_supplierDataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_hutang_list', 
			method: 'POST'
		}),
		baseParams:{task: "LIST", start:0, limit:10}, // parameter yang di $_POST ke Controller
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'hutang_id'
		},[
			{name: 'hutang_id', type: 'int', mapping: 'hutang_id'},
			{name: 'hutang_supplier', type: 'int', mapping: 'hutang_supplier'},
			{name: 'supplier_nama', type: 'string',  mapping: 'supplier_nama'},
			{name: 'supplier_alamat', type: 'string',  mapping: 'supplier_alamat'},
			//{name: 'cust_no', type: 'string', mapping: 'cust_no'},
			{name: 'hutang_total', type: 'float', mapping: 'hutang_total'},
			{name: 'hutang_sisa', type: 'float', mapping: 'hutang_sisa'}
		]),
		sortInfo:{field: 'hutang_id', direction: "ASC"}
	});
	
	// Custom rendering Template
    var fhutang_customer_tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<span><b>{supplier_nama}</b><br /></span>',
            'Sisa Hutang: {hutang_sisa}',
        '</div></tpl>'
    );
	
	/* Function for Retrieve Kwitansi DataStore */
	kwitansi_fhutang_DataStore = new Ext.data.Store({
		id: 'kwitansi_fhutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_kwitansi_by_ref', 
			method: 'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'jkwitansi_id'
		},[
		/* dataIndex => insert intomaster_jual_produk_ColumnModel, Mapping => for initiate table column */ 
			{name: 'jkwitansi_id', type: 'int', mapping: 'jkwitansi_id'},
			{name: 'kwitansi_no', type: 'string', mapping: 'kwitansi_no'},
			{name: 'jkwitansi_nilai', type: 'float', mapping: 'jkwitansi_nilai'},
			{name: 'kwitansi_sisa', type: 'float', mapping: 'kwitansi_sisa'},
			{name: 'cust_nama', type: 'string', mapping: 'cust_nama'},
			{name: 'kwitansi_id', type: 'int', mapping: 'kwitansi_id'}
		]),
		sortInfo:{field: 'jkwitansi_id', direction: "DESC"}
	});
	/* End of Function */
	
	/* Function for Retrieve Kwitansi DataStore */
	card_fhutang_DataStore = new Ext.data.Store({
		id: 'card_fhutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_card_by_ref', 
			method: 'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'jcard_id'
		},[
		/* dataIndex => insert intomaster_jual_produk_ColumnModel, Mapping => for initiate table column */ 
			{name: 'jcard_id', type: 'int', mapping: 'jcard_id'}, 
			{name: 'jcard_no', type: 'string', mapping: 'jcard_no'},
			{name: 'jcard_nama', type: 'string', mapping: 'jcard_nama'},
			{name: 'jcard_edc', type: 'string', mapping: 'jcard_edc'},
			{name: 'jcard_nilai', type: 'float', mapping: 'jcard_nilai'}
		]),
		sortInfo:{field: 'jcard_id', direction: "DESC"}
	});
	/* End of Function */
	
	/* Function for Retrieve Kwitansi DataStore */
	cek_fhutang_DataStore = new Ext.data.Store({
		id: 'cek_fhutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_cek_by_ref', 
			method: 'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'jcek_id'
		},[
		/* dataIndex => insert intomaster_jual_produk_ColumnModel, Mapping => for initiate table column */ 
			{name: 'jcek_id', type: 'int', mapping: 'jcek_id'}, 
			{name: 'jcek_nama', type: 'string', mapping: 'jcek_nama'},
			{name: 'jcek_no', type: 'string', mapping: 'jcek_no'},
			{name: 'jcek_valid', type: 'string', mapping: 'jcek_valid'}, 
			{name: 'jcek_bank', type: 'string', mapping: 'jcek_bank'},
			{name: 'jcek_nilai', type: 'double', mapping: 'jcek_nilai'}
		]),
		sortInfo:{field: 'jcek_id', direction: "DESC"}
	});
	/* End of Function */
	
	/* Function for Retrieve Transfer DataStore */
	transfer_fhutang_DataStore = new Ext.data.Store({
		id: 'transfer_fhutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_transfer_by_ref', 
			method: 'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'jtransfer_id'
		},[
		/* dataIndex => insert intomaster_jual_produk_ColumnModel, Mapping => for initiate table column */ 
			{name: 'jtransfer_id', type: 'int', mapping: 'jtransfer_id'}, 
			{name: 'jtransfer_bank', type: 'int', mapping: 'jtransfer_bank'},
			{name: 'jtransfer_nama', type: 'string', mapping: 'jtransfer_nama'},
			{name: 'jtransfer_nilai', type: 'float', mapping: 'jtransfer_nilai'}
		]),
		sortInfo:{field: 'jtransfer_id', direction: "DESC"}
	});
	/* End of Function */
	
	/* Function for Retrieve Tunai DataStore */
	tunai_fhutang_DataStore = new Ext.data.Store({
		id: 'tunai_fhutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_tunai_by_ref', 
			method: 'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'jtunai_id'
		},[
		/* dataIndex => insert intomaster_jual_produk_ColumnModel, Mapping => for initiate table column */ 
			{name: 'jtunai_id', type: 'int', mapping: 'jtunai_id'}, 
			{name: 'jtunai_nilai', type: 'float', mapping: 'jtunai_nilai'}
		]),
		sortInfo:{field: 'jtunai_id', direction: "DESC"}
	});
	/* End of Function */
	
  	/* Function for Identify of Window Column Model */
	master_lunas_hutang_ColumnModel = new Ext.grid.ColumnModel(
		[
		{
			header: '<div align="center">' + 'ID' + '</div>',
			dataIndex: 'fhutang_id',
			width: 70,
			sortable: false,
			hidden: true,
			readOnly: true
		},
		{
			header: '<div align="center">' + 'Tanggal' + '</div>',
			dataIndex: 'fhutang_tanggal',
			width: 60,
			sortable: true,
			renderer: Ext.util.Format.dateRenderer('d-m-Y'),
			readOnly: true
		},
		{
			header: '<div align="center">' + 'No LH' + '</div>',
			dataIndex: 'fhutang_nobukti',
			width: 80,
			sortable: true,
			readOnly: true
		},
		/*
		{
			header: '<div align="center">' + 'Client Card' + '</div>',
			dataIndex: 'cust_no',
			width: 60,
			sortable: false,
			readOnly: true,
		},
		*/
		{
			header: '<div align="center">' + 'Supplier' + '</div>',
			dataIndex: 'supplier_nama',
			width: 150,
			sortable: false,
			readOnly: true,
		},
		{
			header: '<div align="center">' + 'Cara Bayar' + '</div>',
			align: 'right',
			dataIndex: 'fhutang_cara',
			width: 60,
			sortable: false,
			readOnly: true,
		},
		{
			header: '<div align="center">' + 'Tot Bayar' + '</div>',
			align: 'right',
			dataIndex: 'fhutang_bayar',
			width: 80,
			sortable: true,
			readOnly: true,
			renderer: function(val){
				return '<span>'+Ext.util.Format.number(val,'0,000')+'</span>';
			}
		},
		{
			header: '<div align="center">' + 'Keterangan' + '</div>',
			dataIndex: 'fhutang_keterangan',
			width: 200,
			sortable: false,
			editor: new Ext.form.TextField({
				maxLength: 250
          	})
		}, 
		{
			header: '<div align="center">' + 'Stat Dok' + '</div>',
			dataIndex: 'fhutang_stat_dok',
			width: 60
		}	]);
	
	//master_lunas_hutang_ColumnModel.defaultSortable= true;
	/* End of Function */
    var master_fhutang_paging_toolbar=new Ext.PagingToolbar({
			pageSize: fhutang_pageS,
			store: master_lunas_hutang_DataStore,
			displayInfo: true
		});
	/* Declare DataStore and  show datagrid list */
	master_lunas_hutangListEditorGrid =  new Ext.grid.EditorGridPanel({
		id: 'master_lunas_hutangListEditorGrid',
		el: 'fp_master_lunas_hutang',
		title: 'Daftar Pelunasan Hutang',
		autoHeight: true,
		store: master_lunas_hutang_DataStore, // DataStore
		cm: master_lunas_hutang_ColumnModel, // Nama-nama Columns
		enableColLock:false,
		frame: true,
		clicksToEdit:2, // 2xClick untuk bisa meng-Edit inLine Data
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true },
	  	width: 1220,	//900,
		bbar: master_fhutang_paging_toolbar,
		tbar: [
		<?php if(eregi('C',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
		{
			text: 'Add',
			tooltip: 'Add new record',
			iconCls:'icon-adds',    				// this is defined in our styles.css
			handler: display_form_window
		}, '-',
		<?php } ?>
		<?php if(eregi('U|R',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
		{
			text: 'Edit',
			tooltip: 'Edit selected record',
			iconCls:'icon-update',
			handler: master_lunas_hutang_confirm_update   // Confirm before updating
		}, '-',
		<?php } ?>
		<?php if(eregi('D',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
		{
			text: 'Delete',
			tooltip: 'Delete selected record',
			iconCls:'icon-delete',
			disabled: true,
			handler: master_lunas_hutang_confirm_delete   // Confirm before deleting
		}, '-', 
		<?php } ?>
		{
			text: 'Adv Search',
			tooltip: 'Pencarian detail',
			iconCls:'icon-search',
			disabled: true,
			handler: display_form_search_window 
		}, '-', 
			new Ext.app.SearchField({
			store: master_lunas_hutang_DataStore,
			params: {start: 0, limit: fhutang_pageS},
			listeners:{
				specialkey: function(f,e){
					if(e.getKey() == e.ENTER){
						master_lunas_hutang_DataStore.baseParams={task:'LIST',start: 0, limit: fhutang_pageS};
		            }
				},
				render: function(c){
				Ext.get(this.id).set({qtitle:'Search By (aktif only)'});
				Ext.get(this.id).set({qtip:'- No LH<br>- Customer'});
				}
			},
			width: 120
		}),'-',{
			text: 'Refresh',
			tooltip: 'Refresh datagrid',
			handler: master_lunas_hutang_reset_search,
			iconCls:'icon-refresh'
		},'-',{
			text: 'Export Excel',
			tooltip: 'Export to Excel(.xls) Document',
			iconCls:'icon-xls',
			disabled: true//,
			//handler: master_lunas_piutang_export_excel
		}, '-',{
			text: 'Print',
			tooltip: 'Print Document',
			iconCls:'icon-print',
			disabled: true//,
			//handler: master_lunas_piutang_print  
		}
		]
	});
	master_lunas_hutangListEditorGrid.render();
	/* End of DataStore */
     
	/* Create Context Menu */
	master_lunas_hutang_ContextMenu = new Ext.menu.Menu({
		id: 'master_lunas_piutang_ListEditorGridContextMenu',
		items: [
		<?php if(eregi('U|R',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
		{ 
			text: 'Edit', tooltip: 'Edit selected record', 
			iconCls:'icon-update',
			handler: master_lunas_hutang_confirm_update 
		},
		<?php } ?>
		<?php if(eregi('D',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
		{ 
			text: 'Delete', 
			tooltip: 'Delete selected record', 
			iconCls:'icon-delete',
			handler: master_lunas_hutang_confirm_delete 
		}
		<?php } ?>
		]
	}); 
	/* End of Declaration */
	
	/* Event while selected row via context menu */
	function onMaster_lunas_hutang_ListEditGridContextMenu(grid, rowIndex, e) {
		e.stopEvent();
		var coords = e.getXY();
		master_lunas_hutang_ContextMenu.rowRecord = grid.store.getAt(rowIndex);
		grid.selModel.selectRow(rowIndex);
		master_lunas_hutang_SelectedRow=rowIndex;
		master_lunas_hutang_ContextMenu.showAt([coords[0], coords[1]]);
  	}
  	/* End of Function */
	
	/* function for editing row via context menu */
	function master_lunas_hutang_editContextMenu(){
		master_lunas_hutangListEditorGrid.startEditing(master_lunas_hutang_SelectedRow,1);
  	}
	/* End of Function */

	
	/* Identify  fhutang_id Field */
	fhutang_idField= new Ext.form.NumberField({
		id: 'fhutang_idField',
		allowNegatife : false,
		blankText: '0',
		allowBlank: false,
		allowDecimals: false,
		hidden: true,
		readOnly: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});

	/* Identify  fpiutang_no Field */
	fhutang_nobuktiField= new Ext.form.TextField({
		id: 'fhutang_nobuktiField',
		//fieldLabel: 'No Order',
		fieldLabel: 'No LH',
		emptyText: '(Auto)',
		readOnly: true,
		maxLength: 50,
		anchor: '50%'
	});
	/* Identify  fpiutang_customer Field */
	fhutang_customer_idField= new Ext.form.NumberField();
	
	fhutang_supplierField= new Ext.form.ComboBox({
		id: 'fhutang_supplierField',
		fieldLabel: 'Supplier',
		store: cbo_fhutang_supplierDataStore,
		displayField:'supplier_nama',
		mode : 'remote',
		valueField: 'hutang_supplier',
        typeAhead: false,
        loadingText: 'Searching...',
        pageSize:10,
        hideTrigger:false,
		//allowBlank: false,
        tpl: fhutang_customer_tpl,
		forceSelection: true,
        //applyTo: 'search',
        itemSelector: 'div.search-item',
		triggerAction: 'all',
		lazyRender:true,
		listClass: 'x-combo-list-small',
		enableKeyEvents: true,
		anchor: '95%',
		listeners:{
			render: function(c){
				Ext.get(this.id).set({qtitle:'Field ini memunculkan daftar Customer yang memiliki piutang saja'});
				Ext.get(this.id).set({qtip:'(Search by: Client Card, Nama Cust)'});
			}
		}
	});
	fhutang_supplierField.on('select', function(){
		var j=cbo_fhutang_supplierDataStore.findExact('hutang_supplier',fhutang_supplierField.getValue(),0);
		cbo_forder_hutangDataStore.setBaseParam('supplier_id', fhutang_supplierField.getValue());
		cbo_forder_hutangDataStore.load({
			params:{
				task:'detail'
			}
		});
		if(cbo_fhutang_supplierDataStore.getCount()>0){
			fhutang_total_cfField.setValue(CurrencyFormatted(cbo_fhutang_supplierDataStore.getAt(j).data.hutang_sisa));
			fhutang_totalField.setValue(cbo_fhutang_supplierDataStore.getAt(j).data.hutang_sisa);
			fhutang_alamatField.setValue(cbo_fhutang_supplierDataStore.getAt(j).data.supplier_alamat)
		}
	});
	
	fhutang_alamatField= new Ext.form.TextField({
		id: 'fhutang_alamatField',
		fieldLabel: 'Alamat', 
		emptyText : '(Auto)',
		readOnly: true,
		disabled : true,
		anchor : '95%'
	});
	
	/* Identify  fpiutang_tanggal Field */
	fhutang_tanggalField= new Ext.form.DateField({
		id: 'fhutang_tanggalField',
		fieldLabel: 'Tanggal',
		format : 'd-m-Y'
	});
	fhutang_stat_dokField= new Ext.form.ComboBox({
		id: 'fhutang_stat_dokField',
		fieldLabel: 'Status Dok',
		forceSelection: true,
		store:new Ext.data.SimpleStore({
			fields:['fhutang_status_value', 'fhutang_status_display'],
			data:[['Terbuka','Terbuka'],['Tertutup','Tertutup'],['Batal', 'Batal']]
		}),
		mode: 'local',
		displayField: 'fhutang_status_display',
		valueField: 'fhutang_status_value',
		anchor: '60%',
		allowBlank: false,
		editable: false,
		triggerAction: 'all'	
	});
	
	/* START Field master_lunas_hutang_bayarGroup */
	fhutang_subtotalField= new Ext.form.TextField({
		id: 'fhutang_subtotalField',
		fieldLabel: 'Sub Total',
		valueRenderer: 'numberToCurrency',
		itemCls: 'rmoney',
		readOnly: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});
	/* END Field master_lunas_hutang_bayarGroup */
	
	/* Identify  fpiutang_keterangan Field */
	fhutang_keteranganField= new Ext.form.TextArea({
		id: 'fhutang_keteranganField',
		fieldLabel: 'Keterangan',
		maxLength: 500,
		anchor: '95%'
	});
  	/*Fieldset Master*/
	
	master_lunas_hutang_masterGroup = new Ext.form.FieldSet({
		//title: 'Master',
		autoHeight: true,
		//collapsible: true,
		layout:'column',
		items:[
			{
				columnWidth:0.5,
				layout: 'form',
				border:false,
				items: [fhutang_nobuktiField, fhutang_supplierField, fhutang_alamatField, fhutang_tanggalField] 
			},
			{
				columnWidth:0.5,
				layout: 'form',
				border:false,
				items: [fhutang_keteranganField, fhutang_stat_dokField, fhutang_idField] 
				
			}
			]
	});
	
	function update_group_carabayar_lunas_hutang(){
		var value=fhutang_caraField.getValue();
		master_lunas_hutang_tunaiGroup.setVisible(false);
		master_lunas_hutang_cardGroup.setVisible(false);
		master_lunas_hutang_cekGroup.setVisible(false);
		master_lunas_hutang_transferGroup.setVisible(false);
		master_lunas_hutang_kwitansiGroup.setVisible(false);
		//RESET Nilai di Cara Bayar-1
		//lpiutang_tunai_nilaiField.reset();
		//lpiutang_tunai_nilai_cfField.reset();
		//lpiutang_card_nilaiField.reset();
		//lpiutang_card_nilai_cfField.reset();
		//lpiutang_cek_nilaiField.reset();
		//lpiutang_cek_nilai_cfField.reset();
		//lpiutang_transfer_nilaiField.reset();
		//lpiutang_transfer_nilai_cfField.reset();
		//lpiutang_kwitansi_nilaiField.reset();
		//lpiutang_kwitansi_nilai_cfField.reset();
		
		if(value=='card'){
			master_lunas_hutang_cardGroup.setVisible(true);
		}else if(value=='cek/giro'){
			master_lunas_hutang_cekGroup.setVisible(true);
		}else if(value=='transfer'){
			master_lunas_hutang_transferGroup.setVisible(true);
		}
		/*
		else if(value=='kwitansi'){
			master_lunas_hutang_kwitansiGroup.setVisible(true);
		}
		*/
		else if(value=='tunai'){
			master_lunas_hutang_tunaiGroup.setVisible(true);
		}
	}
	
	/* Identify  jproduk_cara Field */
	fhutang_caraField= new Ext.form.ComboBox({
		id: 'fhutang_caraField',
		fieldLabel: 'Cara Bayar',
		store:new Ext.data.SimpleStore({
			fields:['jproduk_cara_value', 'jproduk_cara_display'],
			data:[['tunai','Tunai'],/*['card','Kartu Kredit'],*/['cek/giro','Cek/Giro'],['transfer','Transfer']]
			//,['voucher','Voucher']]
		}),
		mode: 'local',
		displayField: 'jproduk_cara_display',
		valueField: 'jproduk_cara_value',
		editable: false,
		//anchor: '95%',
		width: 100,
		triggerAction: 'all'	
	});
	fhutang_caraField.on('select', update_group_carabayar_lunas_hutang);
	
	/* GET Bank-List.Store */
	fhutang_bankDataStore = new Ext.data.Store({
		id:'fhutang_bankDataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_bank_list', 
			method: 'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'mbank_id'
		},[
		/* dataIndex => insert intomaster_jual_produk_ColumnModel, Mapping => for initiate table column */ 
			{name: 'lpiutang_bank_value', type: 'int', mapping: 'mbank_id'}, 
			{name: 'lpiutang_bank_display', type: 'string', mapping: 'mbank_nama'}
		]),
		sortInfo:{field: 'lpiutang_bank_display', direction: "DESC"}
		});
	/* END GET Bank-List.Store */
	
	/* Function for Retrieve Combo Kwitansi DataStore */
	cbo_kwitansi_lunas_hutang_DataStore = new Ext.data.Store({
		id: 'cbo_kwitansi_lunas_hutang_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=get_kwitansi_list', 
			method: 'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'kwitansi_id'
		},[
		/* dataIndex => insert intomaster_jual_produk_ColumnModel, Mapping => for initiate table column */ 
			{name: 'ckwitansi_id', type: 'int', mapping: 'kwitansi_id'},
			{name: 'ckwitansi_no', type: 'string', mapping: 'kwitansi_no'},
			{name: 'ckwitansi_cust_no', type: 'string', mapping: 'cust_no'},
			{name: 'ckwitansi_cust_nama', type: 'string', mapping: 'cust_nama'},
			{name: 'ckwitansi_cust_alamat', type: 'string', mapping: 'cust_alamat'},
			{name: 'total_sisa', type: 'int', mapping: 'total_sisa'}
		]),
		sortInfo:{field: 'ckwitansi_no', direction: "ASC"}
	});
	var kwitansi_lunas_hutang_tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<span><b>{ckwitansi_no}</b> <br/>',
			'a/n {ckwitansi_cust_nama} [ {ckwitansi_cust_no} ]<br/>',
			'{ckwitansi_cust_alamat}, <br>Sisa: <b> {total_sisa}</b> </span>',
		'</div></tpl>'
    );
	/* End of Function */
	
	//START Field Tunai-1
	/*lpiutang_tunai_nilai_cfField= new Ext.form.TextField({
		id: 'lpiutang_tunai_nilai_cfField',
		fieldLabel: 'Jumlah (Rp)',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		anchor: '95%',
		maskRe: /([0-9]+)$/ 
	});
	lpiutang_tunai_nilaiField= new Ext.form.NumberField({
		id: 'lpiutang_tunai_nilaiField',
		enableKeyEvents: true,
		fieldLabel: 'Jumlah (Rp)',
		allowBlank: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});*/

	master_lunas_hutang_tunaiGroup = new Ext.form.FieldSet({
		title: 'Tunai',
		autoHeight: true,
		collapsible: true,
		layout:'column',
		anchor: '95%',
		hidden: true,
		items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [/*lpiutang_tunai_nilai_cfField*/] 
			}
		]
	});
	// END Tunai-1
	
	// START Field Card
	fhutang_card_namaField= new Ext.form.ComboBox({
		id: 'fhutang_card_namaField',
		fieldLabel: 'Jenis Kartu',
		store:new Ext.data.SimpleStore({
			fields:['lpiutang_card_value', 'lpiutang_card_display'],
			data:[['VISA','VISA'],['MASTERCARD','MASTERCARD'],['Debit','Debit']]
		}),
		mode: 'local',
		displayField: 'lpiutang_card_display',
		valueField: 'lpiutang_card_value',
		allowBlank: true,
		anchor: '50%',
		triggerAction: 'all',
		lazyRenderer: true
	});
	
	fhutang_card_edcField= new Ext.form.ComboBox({
		id: 'fhutang_card_edcField',
		fieldLabel: 'EDC',
		store:new Ext.data.SimpleStore({
			fields:['lpiutang_card_edc_value', 'lpiutang_card_edc_display'],
			data:[['1','1'],['2','2'],['3','3']]
		}),
		mode: 'local',
		displayField: 'lpiutang_card_edc_display',
		valueField: 'lpiutang_card_edc_value',
		allowBlank: true,
		anchor: '50%',
		triggerAction: 'all',
		lazyRenderer: true
	});

	fhutang_card_noField= new Ext.form.TextField({
		id: 'fhutang_card_noField',
		fieldLabel: 'No Kartu',
		maxLength: 30,
		anchor: '95%'
	});
	
	/*lpiutang_card_nilai_cfField= new Ext.form.TextField({
		id: 'lpiutang_card_nilai_cfField',
		fieldLabel: 'Jumlah (Rp)',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		anchor: '95%',
		maskRe: /([0-9]+)$/ 
	});
	lpiutang_card_nilaiField= new Ext.form.NumberField({
		id: 'lpiutang_card_nilaiField',
		fieldLabel: 'Jumlah (Rp)',
		allowBlank: true,
		anchor: '95%',
		enableKeyEvents: true,
		maskRe: /([0-9]+)$/
	});*/
	
	master_lunas_hutang_cardGroup= new Ext.form.FieldSet({
		title: 'Credit Card',
		autoHeight: true,
		collapsible: true,
		layout:'column',
		anchor: '95%',
		hidden: true,
		items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [fhutang_card_namaField,fhutang_card_edcField,fhutang_card_noField/*,lpiutang_card_nilai_cfField*/] 
			}
		]
	});
	// END Field Card
	
	// StART Field Cek
	fhutang_cek_namaField= new Ext.form.TextField({
		id: 'fhutang_cek_namaField',
		fieldLabel: 'Atas Nama',
		allowBlank: true,
		anchor: '95%'
	});
	
	fhutang_cek_noField= new Ext.form.TextField({
		id: 'fhutang_cek_noField',
		fieldLabel: 'No Cek/Giro',
		allowBlank: true,
		anchor: '95%',
		maxLength: 50
	});
	
	fhutang_cek_validField= new Ext.form.DateField({
		id: 'fhutang_cek_validField',
		allowBlank: true,
		fieldLabel: 'Valid',
		format: 'Y-m-d'
	});
	
	fhutang_cek_bankField= new Ext.form.ComboBox({
		id: 'fhutang_cek_bankField',
		fieldLabel: 'Bank',
		store: fhutang_bankDataStore,
		mode: 'remote',
		displayField: 'lpiutang_bank_display',
		valueField: 'lpiutang_bank_value',
		allowBlank: true,
		anchor: '50%',
		triggerAction: 'all',
		lazyRenderer: true,
		renderer: Ext.util.Format.comboRenderer(fhutang_cek_bankField)
	});
	
	/*lpiutang_cek_nilai_cfField= new Ext.form.TextField({
		id: 'lpiutang_cek_nilai_cfField',
		fieldLabel: 'Jumlah (Rp)',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		anchor: '95%',
		maskRe: /([0-9]+)$/ 
	});
	lpiutang_cek_nilaiField= new Ext.form.NumberField({
		id: 'lpiutang_cek_nilaiField',
		fieldLabel: 'Jumlah (Rp)',
		allowBlank: true,
		anchor: '95%',
		enableKeyEvents: true,
		maskRe: /([0-9]+)$/
	});*/
	
	master_lunas_hutang_cekGroup = new Ext.form.FieldSet({
		title: 'Check/Giro',
		collapsible: true,
		layout:'column',
		anchor: '95%',
		hidden: true,
		items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [fhutang_cek_namaField,fhutang_cek_noField,fhutang_cek_validField,fhutang_cek_bankField/*,lpiutang_cek_nilai_cfField*/] 
			}
		]
	});
	// END Field Cek
	
	//START Kwitansi
	fhutang_kwitansi_namaField= new Ext.form.TextField({
		id: 'fhutang_kwitansi_namaField',
		fieldLabel: 'Atas Nama',
		allowBlank: true,
		readOnly: true,
		anchor: '95%'
	});
	
	/*lpiutang_kwitansi_nilai_cfField= new Ext.form.TextField({
		id: 'lpiutang_kwitansi_nilai_cfField',
		fieldLabel: 'Diambil (Rp)',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		anchor: '95%',
		maskRe: /([0-9]+)$/ 
	});
	lpiutang_kwitansi_nilaiField= new Ext.form.NumberField({
		id: 'lpiutang_kwitansi_nilaiField',
		enableKeyEvents: true,
		fieldLabel: 'Diambil (Rp)',
		allowBlank: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});*/
	
	fhutang_kwitansi_idField= new Ext.form.NumberField();
	fhutang_kwitansi_noField= new Ext.form.ComboBox({
		id: 'fhutang_kwitansi_noField',
		fieldLabel: 'Nomor Kuitansi',
		store: cbo_kwitansi_lunas_hutang_DataStore,
		mode: 'remote',
		displayField:'ckwitansi_no',
		valueField: 'ckwitansi_id',
        typeAhead: false,
        loadingText: 'Searching...',
        pageSize:10,
        hideTrigger:false,
        tpl: kwitansi_lunas_hutang_tpl,
        //applyTo: 'search',
        itemSelector: 'div.search-item',
		//triggerAction: 'all',
		triggerAction: 'query',
		lazyRender:true,
		listClass: 'x-combo-list-small',
		queryDelay:720,
		anchor: '95%'
	});
	
	fhutang_kwitansi_sisaField= new Ext.form.NumberField({
		id: 'fhutang_kwitansi_sisaField',
		fieldLabel: 'Sisa',
		readOnly: true,
		anchor: '95%'
	});
	
	fhutang_kwitansi_noField.on("select",function(){
		j=cbo_kwitansi_lunas_hutang_DataStore.findExact('ckwitansi_id',fhutang_kwitansi_noField.getValue(),0);
		if(j>-1){
			fhutang_kwitansi_namaField.setValue(cbo_kwitansi_lunas_hutang_DataStore.getAt(j).data.ckwitansi_cust_nama);
			fhutang_kwitansi_sisaField.setValue(cbo_kwitansi_lunas_hutang_DataStore.getAt(j).data.total_sisa);
		}
	});
	
	master_lunas_hutang_kwitansiGroup = new Ext.form.FieldSet({
		title: 'Kwitansi',
		collapsible: true,
		layout:'column',
		anchor: '95%',
		hidden: true,
		items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [fhutang_kwitansi_noField,fhutang_kwitansi_namaField,fhutang_kwitansi_sisaField/*,lpiutang_kwitansi_nilai_cfField*/] 
			}
		]
	});
	//END Kwitansi
	
	// START Field Transfer
	fhutang_transfer_bankField= new Ext.form.ComboBox({
		id: 'fhutang_transfer_bankField',
		fieldLabel: 'Bank',
		store: fhutang_bankDataStore,
		mode: 'remote',
		displayField: 'lpiutang_bank_display',
		valueField: 'lpiutang_bank_value',
		allowBlank: true,
		anchor: '50%',
		triggerAction: 'all',
		lazyRenderer: true,
		renderer: Ext.util.Format.comboRenderer(fhutang_transfer_bankField)
	});

	fhutang_transfer_namaField= new Ext.form.TextField({
		id: 'fhutang_transfer_namaField',
		fieldLabel: 'Atas Nama',
		allowBlank: true,
		anchor: '95%',
		maxLength: 50
	});
	
	/*lpiutang_transfer_nilai_cfField= new Ext.form.TextField({
		id: 'lpiutang_transfer_nilai_cfField',
		fieldLabel: 'Jumlah (Rp)',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		anchor: '95%',
		maskRe: /([0-9]+)$/ 
	});
	lpiutang_transfer_nilaiField= new Ext.form.NumberField({
		id: 'lpiutang_transfer_nilaiField',
		enableKeyEvents: true,
		fieldLabel: 'Jumlah (Rp)',
		allowBlank: true,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	});*/
	
	master_lunas_hutang_transferGroup= new Ext.form.FieldSet({
		title: 'Transfer',
		collapsible: true,
		layout:'column',
		anchor: '95%',
		hidden: true,
		items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [fhutang_transfer_bankField,fhutang_transfer_namaField/*,lpiutang_transfer_nilai_cfField*/] 
			}
		]
	
	});
	// END Field Transfer
	
	fhutang_total_cfField= new Ext.form.TextField({
		id: 'fhutang_total_cfField',
		fieldLabel: '<span style="font-weight:bold">Total Hutang Keseluruhan</span>',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		width: 120,
		readOnly: true,
		maskRe: /([0-9]+)$/ 
	});
	fhutang_totalField= new Ext.form.NumberField({
		id: 'fhutang_totalField',
		enableKeyEvents: true,
		fieldLabel: '<span style="font-weight:bold">Total Hutang Keseluruhan</span>',
		allowBlank: true,
		anchor: '95%',
		readOnly: true,
		maskRe: /([0-9]+)$/
	});
	
	fhutang_bayar_cfField= new Ext.form.TextField({
		id: 'fhutang_bayar_cfField',
		fieldLabel: 'Total Bayar ',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		width: 120,
		readOnly: true,
		maskRe: /([0-9]+)$/ 
	});
	fhutang_bayarField= new Ext.form.NumberField({
		id: 'fhutang_bayarField',
		enableKeyEvents: true,
		fieldLabel: 'Total Bayar',
		allowBlank: true,
		anchor: '95%',
		readOnly: true,
		maskRe: /([0-9]+)$/
	});
	
	fhutang_sisa_cfField= new Ext.form.TextField({
		id: 'fhutang_sisa_cfField',
		fieldLabel: 'Sisa Hutang',
		allowNegatife : false,
		enableKeyEvents: true,
		itemCls: 'rmoney',
		width: 120,
		readOnly: true,
		maskRe: /([0-9]+)$/ 
	});
	fhutang_sisaField= new Ext.form.NumberField({
		id: 'fhutang_sisaField',
		enableKeyEvents: true,
		fieldLabel: 'Sisa Hutang',
		allowBlank: true,
		anchor: '95%',
		readOnly: true,
		maskRe: /([0-9]+)$/
	});
	
	master_hutang_cara_bayarTabPanel = new Ext.TabPanel({
		plain:true,
		activeTab: 0,
		//autoHeigth: true,
		frame: true,
		height: 232,
		width: 500,
		defaults:{bodyStyle:'padding:10px'},
		items:[{
                title:'Cara Bayar',
                layout:'form',
				frame: true,
                defaults: {width: 230},
                defaultType: 'textfield',
                items: [fhutang_caraField,master_lunas_hutang_tunaiGroup,master_lunas_hutang_cardGroup,master_lunas_hutang_cekGroup,master_lunas_hutang_kwitansiGroup,master_lunas_hutang_transferGroup]
            }]
	});
	
	//master_lunas_piutang_FootGroup
	master_lunas_hutang_bayarGroup = new Ext.form.FieldSet({
		//title: '-',
		autoHeight: true,
		//collapsible: true,
		layout:'column',
		items:[
			{
				columnWidth:0.65,
				layout: 'form',
				labelAlign: 'left',
				border:false,
				labelWidth: 120,
				items: [master_hutang_cara_bayarTabPanel] 
			},{
				columnWidth:0.35,
				layout: 'form',
				labelAlign: 'left',
				labelWidth: 120,
				border:false,
				items: [fhutang_total_cfField, fhutang_bayar_cfField ,fhutang_sisa_cfField]
			}
			]
	});
		
	// Function for json reader of detail
	var detail_fhutang_reader=new Ext.data.JsonReader({
		root: 'results',
		totalProperty: 'total',
		id: 'hutang_id'
	},[
			{name: 'hutang_id', type: 'int', mapping: 'hutang_id'}, 
			{name: 'hutang_faktur', type: 'string', mapping: 'hutang_faktur'}, 
			{name: 'hutang_tanggal', type: 'date', dateFormat: 'Y-m-d', mapping: 'hutang_tanggal'},
			{name: 'hutang_total', type: 'float', mapping: 'hutang_total'},
			{name: 'hutang_sisa', type: 'float', mapping: 'hutang_sisa'}, 
			{name: 'hutang_stat_dok', type: 'string', mapping: 'hutang_stat_dok'}, 
			{name: 'hutang_status', type: 'string', mapping: 'hutang_status'}, 
			{name: 'hutang_keterangan', type: 'string', mapping: 'hutang_keterangan'},
			{name: 'dhutang_id', type: 'int', mapping: 'dhutang_id'},
			{name: 'dhutang_nilai', type: 'float', mapping: 'dhutang_nilai'},
			{name: 'dhutang_keterangan', type: 'string', mapping: 'dhutang_keterangan'}
			
	]);
	//eof
	
	//function for json writer of detail
	var detail_fhutang_writer = new Ext.data.JsonWriter({
		encode: true,
		writeAllFields: false
	});
	//eof
	
	/* Function for Retrieve DataStore of detail*/
	detail_fhutang_bylh_DataStore = new Ext.data.Store({
		id: 'detail_fhutang_bylh_DataStore',
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?c=c_master_lunas_hutang&m=detail_fhutang_byfh_list', 
			method: 'POST'
		}),
		reader: detail_fhutang_reader,
		baseParams:{start:0, limit:fhutang_pageS, task: 'detail'},
		sortInfo:{field: 'hutang_id', direction: 'DESC'}
	});
	/* End of Function */
	
	//function for editor of detail
	var editor_detail_fhutang= new Ext.ux.grid.RowEditor({
        saveText: 'Update'
    });
	//eof
	
	var dcbo_forder_hutang=new Ext.form.ComboBox({
		store: cbo_forder_hutangDataStore,
		mode: 'local',
		typeAhead: false,
		displayField: 'hutang_faktur',
		valueField: 'hutang_id',
		triggerAction: 'all',
		lazyRender: false,
		//pageSize: fhutang_pageS,
		enableKeyEvents: true,
		tpl: cbo_fjual_hutang_tpl,
		itemSelector: 'div.search-item',
		triggerAction: 'all',
		listClass: 'x-combo-list-small',
		anchor: '95%'
	});
	dcbo_forder_hutang.on("select",function(){
		var j=cbo_forder_hutangDataStore.findExact('hutang_id',dcbo_forder_hutang.getValue(),0);
		if(cbo_forder_hutangDataStore.getCount()>0){
			dlhutang_tgl_fakturField.setValue(cbo_forder_hutangDataStore.getAt(j).data.lpiutang_faktur_tanggal);
			dfhutang_sisaField.setValue(cbo_forder_hutangDataStore.getAt(j).data.hutang_sisa);
			dfhutang_lhutang_idField.setValue(cbo_forder_hutangDataStore.getAt(j).data.hutang_id);
		}
	});
	dcbo_forder_hutang.on("focus",function(){
		if(fhutang_stat_dokField.getValue()=='Terbuka' && fhutang_post2db=='UPDATE'){
			cbo_forder_hutangDataStore.load({
				params:{
					supplier_id: master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_cust'),
					task: 'detail'
				}
			});
		}else if((fhutang_stat_dokField.getValue()!=='Terbuka') && (fhutang_post2db=='UPDATE')){
			cbo_forder_hutangDataStore.load({
				params:{
					fhutang_id: master_lunas_hutangListEditorGrid.getSelectionModel().getSelected().get('fhutang_id'),
					task: 'selected'
				}
			});
		}else{
			cbo_forder_hutangDataStore.setBaseParam('supplier_id', fhutang_supplierField.getValue());
			cbo_forder_hutangDataStore.setBaseParam('task', 'detail');
			cbo_forder_hutangDataStore.load();
		}
	});
	
	var dfhutang_bayarField=new Ext.form.NumberField({
		allowDecimals: true,
		allowNegative: false,
		blankText: '0',
		maxLength: 22,
		maskRe: /([0-9]+)$/
	});
	
	var dlhutang_tgl_fakturField= new Ext.form.TextField({
		id: 'dlhutang_tgl_fakturField',
		readOnly: true,
		format: 'd-m-Y'
	});
	
	var dfhutang_sisaField= new Ext.form.TextField({
		id: 'dfhutang_sisaField',
		readOnly: true
	});
	
	var dfhutang_ketField= new Ext.form.TextField({
		id: 'dfhutang_ketField',
		readOnly: false,
		maxLength: 250
	});
	var dfhutang_lhutang_idField= new Ext.form.NumberField({
		id: 'dfhutang_lhutang_idField',
		readOnly: true
	});
	
	
	//declaration of detail coloumn model
	detail_fhutang_ColumnModel = new Ext.grid.ColumnModel(
		[ 
		{
			header: '<div align="center">' + 'ID' + '</div>',
			dataIndex: 'dhutang_id',
			hidden: true,
			width: 60,
			sortable: false
		},
		{
			header: '<div align="center">' + 'No Faktur' + '</div>',
			dataIndex: 'hutang_id',
			width: 100,
			sortable: true,
			editor: dcbo_forder_hutang,
			renderer: Ext.util.Format.comboRenderer(dcbo_forder_hutang)
		},
		{
			header: '<div align="center">' + 'Hutang' + '</div>',
			align: 'right',
			dataIndex: 'hutang_total',
			width: 100,
			sortable: true,
			renderer: Ext.util.Format.numberRenderer('0,000'),
			editor: dfhutang_sisaField
		},
		{
			header: '<div align="center">' + 'Dilunasi' + '</div>',
			align: 'right',
			dataIndex: 'dhutang_nilai',
			width: 100,
			editor:  dfhutang_bayarField,
			renderer: function(val){
				return '<span>'+Ext.util.Format.number(val,'0,000')+'</span>';
			}
		},
		{
			header: '<div align="center">' + 'Keterangan' + '</div>',
			align: 'right',
			dataIndex: 'dhutang_keterangan',
			width: 200,
			sortable: true,
			editor: dfhutang_ketField
		},
		//hutang_id sengaja dimunculkan, karena dipakai utk pengecekan
		{
			header: '<div align="center">' + 'hutang_id' + '</div>',
			dataIndex: 'hutang_id',
			hidden: false,
			width: 60,
			sortable: false,
			readonly: true,
			editor: dfhutang_lhutang_idField
		},
		]
	);
	detail_fhutang_ColumnModel.defaultSortable= true;
	//eof
	var detail_fhutang_bAdd=new Ext.Button({
		text: 'Add',
		tooltip: 'Add new detail record',
		iconCls:'icon-adds',    				// this is defined in our styles.css
		handler: detail_fhutang_add
	});
	var detail_fhutang_bDel=new Ext.Button({
		text: 'Delete',
		tooltip: 'Delete detail selected record',
		iconCls:'icon-delete',    				// this is defined in our styles.css
		handler: detail_fhutang_confirm_delete
	});
	//declaration of detail list editor grid
	detail_fhutangListEditorGrid =  new Ext.grid.EditorGridPanel({
		id: 'detail_fhutangListEditorGrid',
		el: 'fp_detail_fhutang',
		title: 'Detail Item',
		height: 250,
		width: 920,
		autoScroll: true,
		store: detail_fhutang_bylh_DataStore, // DataStore
		colModel: detail_fhutang_ColumnModel, // Nama-nama Columns
		enableColLock:false,
		region: 'center',
        margins: '0 5 5 5',
		plugins: [editor_detail_fhutang],
		frame: true,
		clicksToEdit:2, // 2xClick untuk bisa meng-Edit inLine Data
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
		viewConfig: { forceFit:true}
		<?php if(eregi('U|C',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
		,
		tbar: [detail_fhutang_bAdd
		, '-',detail_fhutang_bDel
		]
		<?php } ?>
	});
	//eof

	//function of detail add
	function detail_fhutang_add(){
		var edit_detail_fpiutang= new detail_fhutangListEditorGrid.store.recordType({
			dhutang_id		:0,
			hutang_id		:0,
			lpiutang_total	:0,
			dhutang_nilai	:0,
			dhutang_keterangan: ''
		});
		editor_detail_fhutang.stopEditing();
		detail_fhutang_bylh_DataStore.insert(0, edit_detail_fpiutang);
		//detail_fhutangListEditorGrid.getView().refresh();
		detail_fhutangListEditorGrid.getSelectionModel().selectRow(0);
		editor_detail_fhutang.startEditing(0);
	}
	
	//function for refresh detail
	function refresh_detail_fhutang(){
		detail_fhutang_bylh_DataStore.commitChanges();
		detail_fhutangListEditorGrid.getView().refresh();
	}
	//eof
	
	//function for insert detail
	function detail_fhutang_insert(pkid,opsi){
        var dlpiutang_id = [];
        var dlpiutang_produk = [];
        var dlpiutang_satuan = [];
        var dlpiutang_jumlah = [];
        var dlpiutang_harga = [];
        
        var dcount = detail_fhutang_bylh_DataStore.getCount() - 1;
        
        if(detail_fhutang_bylh_DataStore.getCount()>0){
            for(i=0; i<detail_fhutang_bylh_DataStore.getCount();i++){
                if((/^\d+$/.test(detail_fhutang_bylh_DataStore.getAt(i).data.dfpiutang_produk))
				   && detail_fhutang_bylh_DataStore.getAt(i).data.dfpiutang_produk!==undefined
				   && detail_fhutang_bylh_DataStore.getAt(i).data.dfpiutang_produk!==''
				   && detail_fhutang_bylh_DataStore.getAt(i).data.dfpiutang_produk!==0
				   && detail_fhutang_bylh_DataStore.getAt(i).data.dfpiutang_satuan!==0
				   && detail_fhutang_bylh_DataStore.getAt(i).data.dfpiutang_jumlah>0){
                    
                  	hutang_id.push(detail_fhutang_bylh_DataStore.getAt(i).data.hutang_id);
					lpiutang_produk.push(detail_fhutang_bylh_DataStore.getAt(i).data.lpiutang_produk);
                   	lpiutang_satuan.push(detail_fhutang_bylh_DataStore.getAt(i).data.lpiutang_satuan);
					lpiutang_jumlah.push(detail_fhutang_bylh_DataStore.getAt(i).data.lpiutang_jumlah);
					lpiutang_harga.push(detail_fhutang_bylh_DataStore.getAt(i).data.lpiutang_harga);
                }
            }
			
			var encoded_array_lpiutang_id = Ext.encode(hutang_id);
			var encoded_array_lpiutang_produk = Ext.encode(lpiutang_produk);
			var encoded_array_lpiutang_satuan = Ext.encode(lpiutang_satuan);
			var encoded_array_lpiutang_jumlah = Ext.encode(lpiutang_jumlah);
			var encoded_array_lpiutang_harga = Ext.encode(lpiutang_harga);
				
			Ext.Ajax.request({
				waitMsg: 'Mohon tunggu...',
				url: 'index.php?c=c_master_lunas_hutang&m=detail_detail_fhutang_insert',
				params:{
					hutang_id		: encoded_array_lpiutang_id,
					lpiutang_master	: pkid, 
					lpiutang_produk	: encoded_array_lpiutang_produk,
					lpiutang_satuan	: encoded_array_lpiutang_satuan,
					lpiutang_jumlah	: encoded_array_lpiutang_jumlah,
					lpiutang_harga	: encoded_array_lpiutang_harga
				},
				success:function(response){
					var result=eval(response.responseText);
					if(opsi=='print'){
						master_lunas_hutang_cetak_faktur(pkid);
					}
					master_lunas_hutang_DataStore.reload()
				},
				failure: function(response){
					Ext.MessageBox.hide();
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
	//eof
	
	
	//function for purge detail
	function detail_fhutang_purge(pkid,opsi){
		Ext.Ajax.request({
			waitMsg: 'Mohon tunggu...',
			url: 'index.php?c=c_master_lunas_hutang&m=detail_detail_fpiutang_purge',
			params:{ master_id: pkid },
			success:function(response){
				detail_fhutang_insert(pkid,opsi); //by masongbee
				/*if(opsi=='print'){
					master_lunas_hutang_cetak_faktur();
				}
				master_lunas_hutang_DataStore.reload();*/ //by masongbee
			}
		});
		
	}
	//eof
	
	/* Function for Delete Confirm of detail */
	function detail_fhutang_confirm_delete(){
		// only one record is selected here
		if(detail_fhutangListEditorGrid.selModel.getCount() == 1){
			Ext.MessageBox.confirm('Confirmation','Apakah Anda yakin akan menghapus data berikut?', detail_fhutang_delete);
		} else if(detail_fhutangListEditorGrid.selModel.getCount() > 1){
			Ext.MessageBox.confirm('Confirmation','Apakah Anda yakin akan menghapus data-data berikut?', detail_fhutang_delete);
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
	//eof
	
	//function for Delete of detail
	function detail_fhutang_delete(btn){
		if(btn=='yes'){
			var s = detail_fhutangListEditorGrid.getSelectionModel().getSelections();
			for(var i = 0, r; r = s[i]; i++){
				if(r.data.dhutang_id==0){
					detail_fhutang_bylh_DataStore.remove(r);
					detail_fhutang_bylh_DataStore.commitChanges();
					detail_hutang_pelunasan_total();
				}else{
					Ext.MessageBox.show({
						title: 'Warning',
						msg: 'Detail Pelunasan Hutang tidak dapat dihapus.',
						buttons: Ext.MessageBox.OK,
						animEl: 'save',
						icon: Ext.MessageBox.WARNING
					});
				}
			}
		} 		
	}
	//eof
	
	/* Function for retrieve create Window Panel*/ 
	master_lunas_hutang_createForm = new Ext.FormPanel({
		labelAlign: 'left',
		bodyStyle:'padding:5px',
		autoHeight:true,
		width: 700,
		monitorValid: true,
		items: [master_lunas_hutang_masterGroup,detail_fhutangListEditorGrid,master_lunas_hutang_bayarGroup],
		buttons: [
			<?php if(eregi('U|C',$this->m_security->get_access_group_by_kode('MENU_LUNASPIUTANG'))){ ?>
			{
				text: 'Save and Print',
				ref: '../fhutang_savePrint',
				handler: pengecekan_dokumen
			},{
				text: 'Save',
				handler: pengecekan_dokumen2
			}
			,
			<?php } ?>
			{
				text: 'Cancel',
				handler: function(){
					fpiutang_btn_cancel();
					master_lunas_hutang_createWindow.hide();
				}
			}
		]
	});
	/* End  of Function*/
	
	
	/* Function for retrieve create Window Form */
	master_lunas_hutang_createWindow= new Ext.Window({
		id: 'master_lunas_hutang_createWindow',
		title: fhutang_post2db+'Pelunasan Hutang',
		closable:true,
		closeAction: 'hide',
		width: 940,
		//autoWidth: true,
		autoHeight: true,
		x:0,
		y:0,
		plain:true,
		layout: 'fit',
		modal: true,
		renderTo: 'elwindow_master_lunas_hutang_create',
		items: master_lunas_hutang_createForm
	});
	/* End Window */
	
	
	/* Function for action list search */
	function master_lunas_hutang_list_search(){
		// render according to a SQL date format.
		var fpiutang_id_search=null;
		var fpiutang_no_search=null;
		var fpiutang_tanggal_search_date="";
		var fpiutang_tanggal_akhir_search_date="";
		var fpiutang_carabayar_search=null;
		var fpiutang_keterangan_search=null;
		var fpiutang_status_search=null;

		if(fhutang_idSearchField.getValue()!==null){fpiutang_id_search=fhutang_idSearchField.getValue();}
		if(fhutang_noSearchField.getValue()!==null){fpiutang_no_search=fhutang_noSearchField.getValue();}
		if(fhutang_tanggalSearchField.getValue()!==""){fpiutang_tanggal_search_date=fhutang_tanggalSearchField.getValue().format('Y-m-d');}
		if(fhutang_tanggal_akhirSearchField.getValue()!==""){fpiutang_tanggal_akhir_search_date=fhutang_tanggal_akhirSearchField.getValue().format('Y-m-d');}
		if(fhutang_carabayarSearchField.getValue()!==null){fpiutang_carabayar_search=fhutang_carabayarSearchField.getValue();}
		if(fhutang_keteranganSearchField.getValue()!==null){fpiutang_keterangan_search=fhutang_keteranganSearchField.getValue();}
		if(fhutang_statusSearchField.getValue()!==null){fpiutang_status_search=fhutang_statusSearchField.getValue();}
		
		// change the store parameters
		master_lunas_hutang_DataStore.baseParams = {
			task				: 'SEARCH',
			fhutang_id			:	fpiutang_id_search, 
			fpiutang_no			:	fpiutang_no_search, 
			fpiutang_tgl_awal		:	fpiutang_tanggal_search_date, 
			fpiutang_tgl_akhir		:	fpiutang_tanggal_akhir_search_date, 
			fpiutang_carabayar		:	fpiutang_carabayar_search,
			fpiutang_keterangan	:	fpiutang_keterangan_search,
			fpiutang_status		:	fpiutang_status_search
		};
		master_lunas_hutang_DataStore.reload({params: {start: 0, limit: fhutang_pageS}});
	}
		
	/* Function for reset search result */
	function master_lunas_hutang_reset_search(){
		// reset the store parameters
		master_lunas_hutang_DataStore.baseParams = { task: 'LIST', start: 0, limit: fhutang_pageS };
		master_lunas_hutang_DataStore.reload({params: {start: 0, limit: fhutang_pageS}});
		//master_lunas_hutang_searchWindow.close();
	};
	/* End of Fuction */
	
	function master_lunas_hutang_cetak_faktur(pkid){
		
		Ext.Ajax.request({   
		waitMsg: 'Please Wait...',
		url: 'index.php?c=c_master_lunas_hutang&m=print_faktur',
		params: {
			faktur	: pkid
		}, 
		success: function(response){              
		  	var result=eval(response.responseText);
		  	switch(result){
		  	case 1:
				win = window.open('./print/fpiutang_faktur.html','fpiutang_faktur','height=800,width=670,resizable=1,scrollbars=1, menubar=1');
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
	
	function master_lunas_piutang_reset_SearchForm(){
		fhutang_noSearchField.reset();
		fhutang_tanggalSearchField.reset();
		fhutang_tanggal_akhirSearchField.reset();
		fhutang_carabayarSearchField.reset();
		fhutang_keteranganSearchField.reset();
		fhutang_statusSearchField.reset();
	}
	/* Field for search */
	/* Identify  fhutang_id Search Field */
	fhutang_idSearchField= new Ext.form.NumberField({
		id: 'fhutang_idSearchField',
		fieldLabel: 'Id Order',
		allowNegatife : false,
		blankText: '0',
		allowDecimals: false,
		anchor: '95%',
		maskRe: /([0-9]+)$/
	
	});
	/* Identify  fpiutang_no Search Field */
	fhutang_noSearchField= new Ext.form.TextField({
		id: 'fhutang_noSearchField',
		//fieldLabel: 'No Order',
		fieldLabel: 'No.LP',
		maxLength: 50,
		anchor: '95%'
	
	});
	/* Identify  fpiutang_tanggal Search Field */
	fhutang_tanggalSearchField= new Ext.form.DateField({
		id: 'fhutang_tanggalSearchField',
		fieldLabel: 'Tanggal',
		format : 'd-m-Y'
//		value: firstday
	
	});

	fhutang_tanggal_akhirSearchField= new Ext.form.DateField({
		id: 'fhutang_tanggal_akhirSearchField',
		fieldLabel: 's/d',
		format : 'd-m-Y'
//		value: today	
	});
	
	fpiutang_label_tanggal_labelField=new Ext.form.Label({html: 'Tanggal :' });
	
	fpiutang_label_tanggalField= new Ext.form.Label({ html: ' &nbsp; s/d  &nbsp;' });
	
	fpiutang_tanggalSearchFieldSet=new Ext.form.FieldSet({
		id:'fpiutang_tanggalSearchFieldSet',
		title: 'Opsi Tanggal',
		layout: 'column',
		boduStyle: 'padding: 5px;',
		frame: false,
		items:[fhutang_tanggalSearchField, fpiutang_label_tanggalField, fhutang_tanggal_akhirSearchField]
	});

	/* Identify  fpiutang_carabayar Search Field */
	fhutang_carabayarSearchField= new Ext.form.ComboBox({
		id: 'fhutang_carabayarSearchField',
		fieldLabel: 'Cara Pembayaran',
		store:new Ext.data.SimpleStore({
			fields:['value', 'fpiutang_carabayar'],
			data:[['Tunai','Tunai'],['Kredit','Kredit'],['Konsinyasi','Konsinyasi']]
		}),
		mode: 'local',
		displayField: 'fpiutang_carabayar',
		valueField: 'value',
		anchor: '41%',
		triggerAction: 'all'	 
	});

	/* Identify  fpiutang_keterangan Search Field */
	fhutang_keteranganSearchField= new Ext.form.TextField({
		id: 'fhutang_keteranganSearchField',
		fieldLabel: 'Keterangan',
		maxLength: 500,
		anchor: '95%'
	});
	
	fhutang_statusSearchField= new Ext.form.ComboBox({
		id: 'fhutang_statusSearchField',
		fieldLabel: 'Status',
		store:new Ext.data.SimpleStore({
			fields:['value', 'fpiutang_status'],
			data:[['Terbuka','Terbuka'],['Tertutup','Tertutup'],['Batal','Batal']]
		}),
		mode: 'local',
		displayField: 'fpiutang_status',
		valueField: 'value',
		anchor: '41%',
		triggerAction: 'all'	 
	});

    
	/* Function for retrieve search Form Panel */
	master_lunas_hutang_searchForm = new Ext.FormPanel({
		labelAlign: 'left',
		bodyStyle:'padding:5px',
		autoHeight:true,
		width: 500,        
		items: [{
			layout:'column',
			border:false,
			items:[
			{
				columnWidth:1,
				layout: 'form',
				border:false,
				items: [
					fhutang_noSearchField, 
					{
						layout:'column',
						border:false,
						items:[
						{
							columnWidth:0.45,
							layout: 'form',
							border:false,
							defaultType: 'datefield',
							items: [						
								fhutang_tanggalSearchField
							]
						},
						{
							columnWidth:0.30,
							layout: 'form',
							border:false,
							labelWidth:30,
							defaultType: 'datefield',
							items: [						
								fhutang_tanggal_akhirSearchField
							]
						}						
				        ]
					},
					fhutang_carabayarSearchField, 
					fhutang_keteranganSearchField,
					fhutang_statusSearchField] 
			}
			]
		}]
		,
		buttons: [{
				text: 'Search',
				handler: master_lunas_hutang_list_search
			},{
				text: 'Close',
				handler: function(){
					master_lunas_hutang_searchWindow.hide();
				}
			}
		]
	});
    /* End of Function */ 
	 
	/* Function for retrieve search Window Form, used for andvaced search */
	master_lunas_hutang_searchWindow = new Ext.Window({
		title: 'Percarian Order Pembelian',
		closable:true,
		closeAction: 'hide',
		autoWidth: true,
		autoHeight: true,
		plain:true,
		layout: 'fit',
		x: 0,
		y: 0,
		modal: true,
		renderTo: 'elwindow_master_lunas_hutang_search',
		items: master_lunas_hutang_searchForm
	});
    /* End of Function */ 
	 
  	/* Function for Displaying  Search Window Form */
	function display_form_search_window(){
		if(!master_lunas_hutang_searchWindow.isVisible()){
			master_lunas_piutang_reset_SearchForm();
			master_lunas_hutang_searchWindow.show();
		} else {
			master_lunas_hutang_searchWindow.toFront();
		}
	}
  	/* End Function */
	
	//EVENTS
	
	function detail_hutang_pelunasan_total(){
		var jml_bayar_field = 0;
		var sisa_hutang_field = 0;
		for(i=0;i<detail_fhutang_bylh_DataStore.getCount();i++){
			jml_bayar_field+=detail_fhutang_bylh_DataStore.getAt(i).data.dhutang_nilai;
		}
		fhutang_bayar_cfField.setValue(CurrencyFormatted(jml_bayar_field));
		fhutang_bayarField.setValue(jml_bayar_field);
		
		sisa_hutang_field = fhutang_totalField.getValue()-jml_bayar_field;
		fhutang_sisa_cfField.setValue(CurrencyFormatted(sisa_hutang_field));
		fhutang_sisaField.setValue(sisa_hutang_field);
	}
	
	master_lunas_hutang_DataStore.load({params:{start:0, limit: fhutang_pageS}});
	detail_fhutang_bylh_DataStore.on("load",detail_hutang_pelunasan_total);
	
	master_lunas_hutangListEditorGrid.addListener('rowcontextmenu', onMaster_lunas_hutang_ListEditGridContextMenu);
	
	detail_fhutang_bylh_DataStore.on("update",function(){
		detail_hutang_pelunasan_total();
		stat='EDIT';
	});
	
	detail_fhutang_bylh_DataStore.on("load", function(){
		if(detail_fhutang_bylh_DataStore.getCount()==fhutang_pageS && detail_fhutang_bylh_DataStore.getTotalCount()>fhutang_pageS){
			detail_fhutang_bAdd.disabled=true;
		}else{
			detail_fhutang_bAdd.disabled=false;
		}
	});
	
	function fpiutang_btn_cancel(){
		master_lunas_hutang_reset_form();
		fhutang_caraField.setValue("tunai");
		master_lunas_hutang_tunaiGroup.setVisible(true);
		fhutang_post2db="CREATE";
		cbo_fhutang_supplierDataStore.reload();
	}
	
	function pertamax(){
		fhutang_caraField.setValue('tunai');
		master_lunas_hutang_tunaiGroup.setVisible(true);
	}
	pertamax();
	
});
	</script>
<body>
<div>
	<div class="col">
        <div id="fp_master_lunas_hutang"></div>
         <div id="fp_detail_fhutang"></div>
		<div id="elwindow_master_lunas_hutang_create"></div>
        <div id="elwindow_master_lunas_hutang_search"></div>
    </div>
</div>
</body>