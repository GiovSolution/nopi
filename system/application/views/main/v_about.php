<?
/* 
		GIOV Solution - Keep IT Simple
*/
?>
<div id="welcome">
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

var aboutWindow;
var aboutForm;
var id;

var about_labelField;

Ext.onReady(function(){
  Ext.QuickTips.init();
	
	about_labelField= new Ext.form.Label({
		id: 'about_labelField',
		readOnly: true,
		html: '<p><center><b>Sistem Informasi Nopi Natalin</b><br/><br/>' +
				'Digunakan untuk menyimpan dan mengolah data dan aktivitas transaksi PT Nopi Natalin, dilengkapi pengingat aktivitas dan fitur cetak laporan' +
				'<br/>Jadikan aktivitas PT Anda terkontrol, tertata rapi dan terdokumentasi secara lengkap dan cermat</center><br/><br/>'+
				'<b>Dikembangkan oleh GIOV Solution <br/>Alamat : Somewhere in Surabaya<br/>'+
				'Telp. (081) 7320795, Email: giov.solution@gmail.com <br/>Website: <a href="http://www.giov-solution.com">http://www.giov-solution.com</a></b><br/>'+
				'<br/>Develop Team: Isaac (isec_jc_crew@yahoo.com), Freddy(hypolution@yahoo.com)</p>'
	});
	
	
	aboutForm = new Ext.FormPanel({
		labelAlign: 'top',
		bodyStyle:'padding:5px',
		x:0,
		y:0,
		width: 300, 
		height: 300,
		items: [{
			layout:'column',
			border:false,
			items:[{
				columnWidth:0.99,
				layout: 'form',
				border:false,
				items: [about_labelField] 
			}]
		}],
		monitorValid:true,
		buttons: [{
				text: 'Close',
				handler: function(){
				// because of the global vars, we can only instantiate one window... so let's just hide it.
				aboutWindow.hide();
				mainPanel.remove(mainPanel.getActiveTab().getId());
			}
		}]
		
	});
	
	/* Form Advanced Search */
	aboutWindow = new Ext.Window({
		title: 'About Us',
		closable:false,
		closeAction: 'hide',
		resizable: false,
		plain:true,
		layout: 'fit',
		x: 0,
		y: 0,
		modal: true,
		renderTo: 'elwindow_about',
		items: aboutForm
	});
	aboutForm.getForm().load();
  	aboutWindow.show();
  	
});
	</script>
	<div class="col">
		<div id="elwindow_about"></div>
    </div>
</div>