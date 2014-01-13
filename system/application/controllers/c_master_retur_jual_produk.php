<?php
/* 	
	GIOV Solution - Keep IT Simple
*/

//class of master_retur_jual_produk
class C_master_retur_jual_produk extends Controller {

	//constructor
	function C_master_retur_jual_produk(){
		parent::Controller();
		session_start();
		$this->load->model('m_master_retur_jual_produk', '', TRUE);

	}
	
	//set index
	function index(){
		$this->load->helper('asset');
		$this->load->view('main/v_master_retur_jual_produk');
	}
	
	function get_produk_list(){
		$query = isset($_POST['query']) ? $_POST['query'] : "";
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$aktif = trim(@$_POST["aktif"]);
		$result = $this->m_master_retur_jual_produk->get_produk_list($query,$start,$end,$aktif);
		echo $result;
	}
	
	function get_reveral_list(){
		$query = isset($_POST['query']) ? $_POST['query'] : "";
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$result=$this->m_master_retur_jual_produk->get_reveral_list($query,$start,$end);
		echo $result;
	}

	function get_satuan_list(){
		//$query = isset($_POST['query']) ? $_POST['query'] : "";
		$task = isset($_POST['task']) ? @$_POST['task'] : @$_GET['task'];
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$selected_id = isset($_POST['selected_id']) ? @$_POST['selected_id'] : @$_GET['selected_id'];

		if($task=='produk')
			$result=$this->m_master_retur_jual_produk->get_satuan_produk_list($selected_id);
		elseif($task=='all')
			$result=$this->m_public_function->get_satuan_list();
		echo $result;
	}
	
	
	function laporan(){
		$this->load->view('main/v_lap_retur_produk');
	}
	
	function print_laporan(){
		$tgl_awal=(isset($_POST['tgl_awal']) ? @$_POST['tgl_awal'] : @$_GET['tgl_awal']);
		$tgl_akhir=(isset($_POST['tgl_akhir']) ? @$_POST['tgl_akhir'] : @$_GET['tgl_akhir']);
		$bulan=(isset($_POST['bulan']) ? @$_POST['bulan'] : @$_GET['bulan']);
		$tahun=(isset($_POST['tahun']) ? @$_POST['tahun'] : @$_GET['tahun']);
		$opsi=(isset($_POST['opsi']) ? @$_POST['opsi'] : @$_GET['opsi']);
		$periode=(isset($_POST['periode']) ? @$_POST['periode'] : @$_GET['periode']);
		$group=(isset($_POST['group']) ? @$_POST['group'] : @$_GET['group']);
		
		$data["jenis"]='Retur Produk';
		if($periode=="all"){
			$data["periode"]="Semua Periode";
		}else if($periode=="bulan"){
			$tgl_awal=$tahun."-".$bulan;
			$data["periode"]=get_ina_month_name($bulan,'long')." ".$tahun;
		}else if($periode=="tanggal"){
			$date = substr($tgl_awal,8,2);
			$month = substr($tgl_awal,5,2);
			$year = substr($tgl_awal,0,4);
			$tgl_awal_show = $date.'-'.$month.'-'.$year;
			
			$date_akhir = substr($tgl_akhir,8,2);
			$month_akhir = substr($tgl_akhir,5,2);
			$year_akhir = substr($tgl_akhir,0,4);
			$tgl_akhir_show = $date_akhir.'-'.$month_akhir.'-'.$year_akhir;
			
			//$tgl_awal_show = $tgl_awal;
			//$tgl_awal_show = date("d-m-Y", $tgl_awal);
			//$tgl_akhir_show = $tgl_akhir;
			//$tgl_akhir_show = date("d-m-Y", $tgl_akhir);
			$data["periode"]="Periode : ".$tgl_awal_show." s/d ".$tgl_akhir_show.", ";
		}
		
		/*$data["total_item"]=$this->m_master_retur_jual_produk->get_total_item($tgl_awal,$tgl_akhir,$periode,$opsi);
		$data["total_diskon"]=$this->m_master_retur_jual_produk->get_total_diskon($tgl_awal,$tgl_akhir,$periode,$opsi);
		$data["total_nilai"]=$this->m_master_retur_jual_produk->get_total_nilai($tgl_awal,$tgl_akhir,$periode,$opsi);*/
		$data["data_print"]=$this->m_master_retur_jual_produk->get_laporan($tgl_awal,$tgl_akhir,$periode,$opsi,$group);
			
		if($opsi=='rekap'){
			switch($group){
				case "Tanggal": $print_view=$this->load->view("main/p_rekap_retur_jual_tanggal.php",$data,TRUE);break;
				case "No Faktur Jual": $print_view=$this->load->view("main/p_rekap_retur_jual_faktur_jual.php",$data,TRUE);break;
				case "Customer": $print_view=$this->load->view("main/p_rekap_retur_jual_customer.php",$data,TRUE);break;
				default: $print_view=$this->load->view("main/p_rekap_retur_jual.php",$data,TRUE);break;
			}
		}else{
			switch($group){
				case "Tanggal": $print_view=$this->load->view("main/p_detail_retur_jual_tanggal.php",$data,TRUE);break;
				case "Customer": $print_view=$this->load->view("main/p_detail_retur_jual_customer.php",$data,TRUE);break;
				case "Produk": $print_view=$this->load->view("main/p_detail_retur_jual_produk.php",$data,TRUE);break;
				case "No Faktur Jual": $print_view=$this->load->view("main/p_detail_retur_jual_faktur_jual.php",$data,TRUE);break;
				default: $print_view=$this->load->view("main/p_detail_retur_jual.php",$data,TRUE);break;
			}
		}
		if(!file_exists("print")){
			mkdir("print");
		}
		if($opsi=='rekap')
			$print_file=fopen("print/report_rproduk.html","w+");
		else
			$print_file=fopen("print/report_rproduk.html","w+");
			
		fwrite($print_file, $print_view);
		echo '1'; 
	}
		
	function get_jual_produk_list(){
		$query = isset($_POST['query']) ? $_POST['query'] : "";
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$result=$this->m_master_retur_jual_produk->get_jual_produk_list($query,$start,$end);
		echo $result;
	}
	
	function get_customer_list(){
		$query = isset($_POST['query']) ? $_POST['query'] : "";
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$result=$this->m_public_function->get_customer_list($query,$start,$end);
		echo $result;
	}
	
	
	//for detail action
	//list detail handler action
	function  detail_detail_retur_jual_produk_list(){
		$query = isset($_POST['query']) ? $_POST['query'] : "";
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$master_id = (integer) (isset($_POST['master_id']) ? $_POST['master_id'] : $_GET['master_id']);
		$result=$this->m_master_retur_jual_produk->detail_detail_retur_jual_produk_list($master_id,$query,$start,$end);
		echo $result;
	}
	//end of handler
	
	//purge all detail
	function detail_detail_retur_jual_produk_purge(){
		$master_id = (integer) (isset($_POST['master_id']) ? $_POST['master_id'] : $_GET['master_id']);
		$result=$this->m_master_retur_jual_produk->detail_detail_retur_jual_produk_purge($master_id);
	}
	//eof
	
	//get master id, note: not done yet
	function get_master_id(){
		$result=$this->m_master_retur_jual_produk->get_master_id();
		echo $result;
	}
	//
	
	//add detail
	function detail_detail_retur_jual_produk_insert(){
	//POST variable here
		$drproduk_id=trim(@$_POST["drproduk_id"]);
		$drproduk_master=trim(@$_POST["drproduk_master"]);
		$drproduk_produk=trim(@$_POST["drproduk_produk"]);
		$drproduk_satuan=trim(@$_POST["drproduk_satuan"]);
		$sales_id=trim(@$_POST["sales_id"]);
		$drproduk_jumlah=trim(@$_POST["drproduk_jumlah"]);
		$drproduk_harga=trim(@$_POST["drproduk_harga"]);
		$drproduk_diskon=trim(@$_POST["drproduk_diskon"]);
		$result=$this->m_master_retur_jual_produk->detail_detail_retur_jual_produk_insert($drproduk_id ,$drproduk_master ,$drproduk_produk ,$drproduk_satuan , $sales_id, $drproduk_jumlah ,$drproduk_harga, $drproduk_diskon);
	}
	
	
	//event handler action
	function get_action(){
		$task = $_POST['task'];
		switch($task){
			case "LIST":
				$this->master_retur_jual_produk_list();
				break;
			case "UPDATE":
				$this->master_retur_jual_produk_update();
				break;
			case "CREATE":
				$this->master_retur_jual_produk_create();
				break;
			case "CEK":
				$this->master_retur_jual_produk_pengecekan();
				break;
			case "DELETE":
				$this->master_retur_jual_produk_delete();
				break;
			case "SEARCH":
				$this->master_retur_jual_produk_search();
				break;
			case "PRINT":
				$this->master_retur_jual_produk_print();
				break;
			case "EXCEL":
				$this->master_retur_jual_produk_export_excel();
				break;
			case "BATAL":
				$this->master_retur_jual_produk_batal();
				break;
			 case "DDELETE":
				$this->detail_retur_jual_produk_delete();
				break;
			default:
				echo "{failure:true}";
				break;
		}
	}
	
	//function fot list record
	function master_retur_jual_produk_list(){
		
		$query = isset($_POST['query']) ? $_POST['query'] : "";
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$result=$this->m_master_retur_jual_produk->master_retur_jual_produk_list($query,$start,$end);
		echo $result;
	}

	// function utk melakukan pengecekan tanggal valid dokumen
	function master_retur_jual_produk_pengecekan(){
		$tanggal_pengecekan=trim(@$_POST["tanggal_pengecekan"]);
		$result=$this->m_public_function->pengecekan_dokumen($tanggal_pengecekan);
		echo $result;
	}
	

	//function for update record
	function master_retur_jual_produk_update(){
		//POST variable here
		$rproduk_cetak=trim(@$_POST["rproduk_cetak"]);
		$rproduk_id=trim(@$_POST["rproduk_id"]);
		$rproduk_nobukti=trim(@$_POST["rproduk_nobukti"]);
		$rproduk_nobukti=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobukti);
		$rproduk_nobukti=str_replace(",", ",",$rproduk_nobukti);
		$rproduk_nobukti=str_replace("'", '"',$rproduk_nobukti);
		$rproduk_nobuktijual=trim(@$_POST["rproduk_nobuktijual"]);
		$rproduk_nobuktijual=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobuktijual);
		$rproduk_nobuktijual=str_replace(",", ",",$rproduk_nobuktijual);
		$rproduk_nobuktijual=str_replace("'", '"',$rproduk_nobuktijual);
		$rproduk_cust=trim(@$_POST["rproduk_cust"]);
		$rproduk_tanggal=trim(@$_POST["rproduk_tanggal"]);
		$rproduk_keterangan=trim(@$_POST["rproduk_keterangan"]);
		$rproduk_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_keterangan);
		$rproduk_keterangan=str_replace(",", ",",$rproduk_keterangan);
		$rproduk_keterangan=str_replace("'", '"',$rproduk_keterangan);
		
		$rproduk_stat_dok=trim(@$_POST["rproduk_stat_dok"]);
		$rproduk_stat_dok=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_stat_dok);
		$rproduk_stat_dok=str_replace(",", ",",$rproduk_stat_dok);
		$rproduk_stat_dok=str_replace("'", '"',$rproduk_stat_dok);

		//Data Detail Penjualan Retur Produk
		$drproduk_id = $_POST['drproduk_id']; // Get our array back and translate it :
		$array_drproduk_id = json_decode(stripslashes($drproduk_id));
		
		$drproduk_produk = $_POST['drproduk_produk']; // Get our array back and translate it :
		$array_drproduk_produk = json_decode(stripslashes($drproduk_produk));
		
		$drproduk_satuan = $_POST['drproduk_satuan']; // Get our array back and translate it :
		$array_drproduk_satuan = json_decode(stripslashes($drproduk_satuan));
		
		$drproduk_jumlah = $_POST['drproduk_jumlah']; // Get our array back and translate it :
		$array_drproduk_jumlah = json_decode(stripslashes($drproduk_jumlah));
		
		$drproduk_harga = $_POST['drproduk_harga']; // Get our array back and translate it :
		$array_drproduk_harga = json_decode(stripslashes($drproduk_harga));

		$drproduk_diskon = $_POST['drproduk_diskon']; // Get our array back and translate it :
		$array_drproduk_diskon = json_decode(stripslashes($drproduk_diskon));
		
		$sales_id = $_POST['sales_id']; // Get our array back and translate it :
		$array_sales_id = json_decode(stripslashes($sales_id));
				
		
		$result = $this->m_master_retur_jual_produk->master_retur_jual_produk_update($rproduk_cetak, $rproduk_id ,$rproduk_nobukti ,$rproduk_nobuktijual ,$rproduk_cust ,$rproduk_tanggal ,$rproduk_keterangan, $rproduk_stat_dok, 
			$array_drproduk_id, $array_drproduk_produk, $array_drproduk_satuan, $array_drproduk_jumlah, $array_drproduk_harga, $array_drproduk_diskon, $array_sales_id );
		echo $result;
	}
	
	//function for create new record
	function master_retur_jual_produk_create(){
		//POST varible here
		//auto increment, don't accept anything from form values
		$rproduk_nobukti=trim(@$_POST["rproduk_nobukti"]);
		$rproduk_nobukti=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobukti);
		$rproduk_nobukti=str_replace("'", '"',$rproduk_nobukti);
		$rproduk_nobuktijual=trim(@$_POST["rproduk_nobuktijual"]);
		$rproduk_nobuktijual=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobuktijual);
		$rproduk_nobuktijual=str_replace("'", '"',$rproduk_nobuktijual);
		$rproduk_cust=trim(@$_POST["rproduk_cust"]);
		$rproduk_tanggal=trim(@$_POST["rproduk_tanggal"]);
		$rproduk_keterangan=trim(@$_POST["rproduk_keterangan"]);
		$rproduk_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_keterangan);
		$rproduk_keterangan=str_replace("'", '"',$rproduk_keterangan);
		
		$rproduk_stat_dok=trim(@$_POST["rproduk_stat_dok"]);
		$rproduk_stat_dok=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_stat_dok);
		$rproduk_stat_dok=str_replace("'", '"',$rproduk_stat_dok);
		
		$rproduk_kwitansi_nilai=trim(@$_POST["rproduk_kwitansi_nilai"]);
		$rproduk_kwitansi_keterangan=trim(@$_POST["rproduk_kwitansi_keterangan"]);
		$rproduk_kwitansi_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_kwitansi_keterangan);
		$rproduk_kwitansi_keterangan=str_replace("'", '"',$rproduk_kwitansi_keterangan);
		$rproduk_voucher=trim(@$_POST["rproduk_voucher"]);

		//Data Detail Penjualan Retur Produk
		$drproduk_id = $_POST['drproduk_id']; // Get our array back and translate it :
		$array_drproduk_id = json_decode(stripslashes($drproduk_id));
		
		$drproduk_produk = $_POST['drproduk_produk']; // Get our array back and translate it :
		$array_drproduk_produk = json_decode(stripslashes($drproduk_produk));
		
		$drproduk_satuan = $_POST['drproduk_satuan']; // Get our array back and translate it :
		$array_drproduk_satuan = json_decode(stripslashes($drproduk_satuan));
		
		$drproduk_jumlah = $_POST['drproduk_jumlah']; // Get our array back and translate it :
		$array_drproduk_jumlah = json_decode(stripslashes($drproduk_jumlah));
		
		$drproduk_harga = $_POST['drproduk_harga']; // Get our array back and translate it :
		$array_drproduk_harga = json_decode(stripslashes($drproduk_harga));

		$drproduk_diskon = $_POST['drproduk_diskon']; // Get our array back and translate it :
		$array_drproduk_diskon = json_decode(stripslashes($drproduk_diskon));
		
		$sales_id = $_POST['sales_id']; // Get our array back and translate it :
		$array_sales_id = json_decode(stripslashes($sales_id));


		$result=$this->m_master_retur_jual_produk->master_retur_jual_produk_create($rproduk_nobukti ,$rproduk_nobuktijual ,$rproduk_cust ,$rproduk_tanggal ,$rproduk_keterangan , $rproduk_stat_dok, $rproduk_kwitansi_nilai ,$rproduk_kwitansi_keterangan, $rproduk_voucher,
			$array_drproduk_id, $array_drproduk_produk, $array_drproduk_satuan, $array_drproduk_jumlah, $array_drproduk_harga, $array_drproduk_diskon, $array_sales_id);
		echo $result;
	}

	//function for delete selected record
	function master_retur_jual_produk_delete(){
		$ids = $_POST['ids']; // Get our array back and translate it :
		$pkid = json_decode(stripslashes($ids));
		$result=$this->m_master_retur_jual_produk->master_retur_jual_produk_delete($pkid);
		echo $result;
	}
	
	function master_retur_jual_produk_batal(){
		$rproduk_id=trim(@$_POST["rproduk_id"]);
		$result=$this->m_master_retur_jual_produk->master_retur_jual_produk_batal($rproduk_id);
		echo $result;
	}

	function detail_retur_jual_produk_delete(){
        $drproduk_id = trim(@$_POST["drproduk_id"]); // Get our array back and translate it :
		$result=$this->m_master_retur_jual_produk->detail_retur_jual_produk_delete($drproduk_id);
		echo $result;
    }

	//function for advanced search
	function master_retur_jual_produk_search(){
		//POST varibale here
		$rproduk_nobukti=trim(@$_POST["rproduk_nobukti"]);
		$rproduk_nobukti=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobukti);
		$rproduk_nobukti=str_replace("'", '"',$rproduk_nobukti);
		$rproduk_nobuktijual=trim(@$_POST["rproduk_nobuktijual"]);
		$rproduk_nobuktijual=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobuktijual);
		$rproduk_nobuktijual=str_replace("'", '"',$rproduk_nobuktijual);
		$rproduk_cust=trim(@$_POST["rproduk_cust"]);
		$rproduk_tanggal=trim(@$_POST["rproduk_tanggal"]);
		$rproduk_tanggal_akhir=trim(@$_POST["rproduk_tanggal_akhir"]);
		$rproduk_keterangan=trim(@$_POST["rproduk_keterangan"]);
		$rproduk_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_keterangan);
		$rproduk_keterangan=str_replace("'", '"',$rproduk_keterangan);
		
		$rproduk_stat_dok=trim(@$_POST["rproduk_stat_dok"]);
		$rproduk_stat_dok=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_stat_dok);
		$rproduk_stat_dok=str_replace("'", '"',$rproduk_stat_dok);
		
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$result = $this->m_master_retur_jual_produk->master_retur_jual_produk_search($rproduk_nobukti ,$rproduk_nobuktijual ,$rproduk_cust ,$rproduk_tanggal, $rproduk_tanggal_akhir, $rproduk_keterangan , $rproduk_stat_dok, $start,$end);
		echo $result;
	}


	function master_retur_jual_produk_print(){
  		//POST varibale here
		$rproduk_nobukti=trim(@$_POST["rproduk_nobukti"]);
		$rproduk_nobukti=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobukti);
		$rproduk_nobukti=str_replace("'", '"',$rproduk_nobukti);
		$rproduk_nobuktijual=trim(@$_POST["rproduk_nobuktijual"]);
		$rproduk_nobuktijual=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobuktijual);
		$rproduk_nobuktijual=str_replace("'", '"',$rproduk_nobuktijual);
		$rproduk_cust=trim(@$_POST["rproduk_cust"]);
		$rproduk_tanggal=trim(@$_POST["rproduk_tanggal"]);
		$rproduk_tanggal_akhir=trim(@$_POST["rproduk_tanggal_akhir"]);
		$rproduk_keterangan=trim(@$_POST["rproduk_keterangan"]);
		$rproduk_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_keterangan);
		$rproduk_keterangan=str_replace("'", '"',$rproduk_keterangan);
		$rproduk_stat_dok=trim(@$_POST["rproduk_stat_dok"]);
		$rproduk_stat_dok=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_stat_dok);
		$rproduk_stat_dok=str_replace("'", '"',$rproduk_stat_dok);
		
		$option=$_POST['currentlisting'];
		$filter=$_POST["query"];
		
		$data["data_print"] = $this->m_master_retur_jual_produk->master_retur_jual_produk_print($rproduk_nobukti
																								,$rproduk_nobuktijual
																								,$rproduk_cust
																								,$rproduk_tanggal
																								,$rproduk_tanggal_akhir
																								,$rproduk_keterangan
																								,$rproduk_stat_dok
																								,$option
																								,$filter);
		$print_view=$this->load->view("main/p_master_retur_jual_produk.php",$data,TRUE);
		if(!file_exists("print")){
			mkdir("print");
		}
		$print_file=fopen("print/master_retur_jual_produklist.html","w+");
		fwrite($print_file, $print_view);
		echo '1';
	}
	/* End Of Function */

	/* Function to Export Excel document */
	function master_retur_jual_produk_export_excel(){
		//POST varibale here
		$this->load->plugin('to_excel');
		$rproduk_nobukti=trim(@$_POST["rproduk_nobukti"]);
		$rproduk_nobukti=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobukti);
		$rproduk_nobukti=str_replace("'", '"',$rproduk_nobukti);
		$rproduk_nobuktijual=trim(@$_POST["rproduk_nobuktijual"]);
		$rproduk_nobuktijual=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_nobuktijual);
		$rproduk_nobuktijual=str_replace("'", '"',$rproduk_nobuktijual);
		$rproduk_cust=trim(@$_POST["rproduk_cust"]);
		$rproduk_tanggal=trim(@$_POST["rproduk_tanggal"]);
		$rproduk_tanggal_akhir=trim(@$_POST["rproduk_tanggal_akhir"]);
		$rproduk_keterangan=trim(@$_POST["rproduk_keterangan"]);
		$rproduk_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_keterangan);
		$rproduk_keterangan=str_replace("'", '"',$rproduk_keterangan);
		$rproduk_stat_dok=trim(@$_POST["rproduk_stat_dok"]);
		$rproduk_stat_dok=str_replace("/(<\/?)(p)([^>]*>)", "",$rproduk_stat_dok);
		$rproduk_stat_dok=str_replace("'", '"',$rproduk_stat_dok);
		
		$option=$_POST['currentlisting'];
		$filter=$_POST["query"];
		
		$query = $this->m_master_retur_jual_produk->master_retur_jual_produk_export_excel($rproduk_nobukti
																						  ,$rproduk_nobuktijual
																						  ,$rproduk_cust
																						  ,$rproduk_tanggal
																						  ,$rproduk_tanggal_akhir
																						  ,$rproduk_keterangan
																						  ,$rproduk_stat_dok
																						  ,$option
																						  ,$filter);
		
		to_excel($query,"master_retur_jual_produk");
		echo '1';
			
	}
	
	function print_paper(){
  		//POST varibale here
		$kwitansi_ref=trim(@$_POST["kwitansi_ref"]);
		
		$result = $this->m_master_retur_jual_produk->print_paper($kwitansi_ref);
		$rs=$result->row();
		$detail_rproduk=$result->result();
		$result_cara_bayar = $this->m_master_retur_jual_produk->cara_bayar($kwitansi_ref);
		
		$data["rproduk_nobukti"]=$rs->rproduk_nobukti;
		$data["rproduk_tanggal"]=$rs->rproduk_tanggal;
		$data["cust_alamat"]=$rs->cust_alamat;
		$data["cust_kota"]=$rs->cust_kota;
		$data["cust_no"]=$rs->cust_no;
		$data["cust_nama"]=$rs->cust_nama;
		$data["rproduk_cust"]=$rs->cust_no."-".$rs->cust_nama;
		$data["drproduk_harga"]="Rp. ".ubah_rupiah($rs->drproduk_harga);
		//$data["total_retur_terbilang"]=strtoupper(terbilang($rs->drproduk_harga))." RUPIAH";
		$data["rproduk_keterangan"]=$rs->rproduk_keterangan;
		$data["drproduk_jumlah"]=$rs->drproduk_jumlah;
		$data["drproduk_harga"]=$rs->drproduk_harga;
		$data["drproduk_diskon"]=$rs->drproduk_diskon;
		$data["karyawan_nama"]=$rs->karyawan_nama;
		$data["karyawan_no"]=$rs->karyawan_no;
		$data["karyawan_username"]=$rs->karyawan_username;
		$data["rproduk_jam"]=$rs->rproduk_jam;
		$data["detail_rproduk"]=$detail_rproduk;
		
		$viewdata=$this->load->view("main/retur_penjualan_formcetak",$data,TRUE);
		$file = fopen("kwitansi_paper.html",'w');
		fwrite($file, $viewdata);	
		fclose($file);
		echo '1';        
	}
	
	// Encodes a SQL array into a JSON formated string
	function JEncode($arr){
		if (version_compare(PHP_VERSION,"5.2","<"))
		{    
			require_once("./JSON.php"); //if php<5.2 need JSON class
			$json = new Services_JSON();//instantiate new json object
			$data=$json->encode($arr);  //encode the data in json format
		} else {
			$data = json_encode($arr);  //encode the data in json format
		}
		return $data;
	}
	
	// Decode a SQL array into a JSON formated string
	function JDecode($arr){
		if (version_compare(PHP_VERSION,"5.2","<"))
		{    
			require_once("./JSON.php"); //if php<5.2 need JSON class
			$json = new Services_JSON();//instantiate new json object
			$data=$json->decode($arr);  //decode the data in json format
		} else {
			$data = json_decode($arr);  //decode the data in json format
		}
		return $data;
	}
	
	// Encodes a YYYY-MM-DD into a MM-DD-YYYY string
	function codeDate ($date) {
	  $tab = explode ("-", $date);
	  $r = $tab[1]."/".$tab[2]."/".$tab[0];
	  return $r;
	}
	
}
?>