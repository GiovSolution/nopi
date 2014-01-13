<?php
/* 	
	+ Module  		: kurs Controller
	+ Description	: For record controller process back-end
	+ Filename 		: C_kurs.php
 	+ Author  		: Isaac & Freddy
 	+ Created on 14/Mar/2012 22:47:00
	
*/

//class of jenis
class C_kurs extends Controller {

	//constructor
	function C_kurs(){
		parent::Controller();
		session_start();
		$this->load->model('m_kurs', '', TRUE);
		$this->load->plugin('to_excel');
	}
	
	//set index
	function index(){
		$this->load->helper('asset');
		$this->load->view('main/v_kurs');
	}
	
	//event handler action
	function get_action(){
		$task = $_POST['task'];
		switch($task){
			case "LIST":
				$this->kurs_list();
				break;
			case "UPDATE":
				$this->kurs_update();
				break;
			case "CREATE":
				$this->kurs_create();
				break;
			case "DELETE":
				$this->jenis_delete();
				break;
			case "SEARCH":
				$this->jenis_search();
				break;
			case "PRINT":
				$this->jenis_print();
				break;
			case "EXCEL":
				$this->jenis_export_excel();
				break;
			default:
				echo "{failure:true}";
				break;
		}
	}
	
	//function fot list record
	function kurs_list(){
		
		$query = isset($_POST['query']) ? $_POST['query'] : "";
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$result=$this->m_kurs->kurs_list($query,$start,$end);
		echo $result;
	}

	//function for update record
	function kurs_update(){
		//POST variable here
		$kurs_id=trim(@$_POST["kurs_id"]);
		$kurs_tanggal=trim(@$_POST["kurs_tanggal"]);
		$kurs_negara=trim(@$_POST["kurs_negara"]);
		$kurs_negara=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_negara);
		$kurs_negara=str_replace("'", '"',$kurs_negara);
		$kurs_initial=trim(@$_POST["kurs_initial"]);
		$kurs_initial=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_initial);
		$kurs_initial=str_replace("'", '"',$kurs_initial);
		$kurs_nilai=trim(@$_POST["kurs_nilai"]);
		$kurs_keterangan=trim(@$_POST["kurs_keterangan"]);
		$kurs_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_keterangan);
		$kurs_keterangan=str_replace("'", '"',$kurs_keterangan);
		$kurs_aktif=trim(@$_POST["kurs_aktif"]);
		$kurs_aktif=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_aktif);
		$kurs_aktif=str_replace("'", '"',$kurs_aktif);
		$result = $this->m_kurs->kurs_update($kurs_id, $kurs_tanggal ,$kurs_negara ,$kurs_initial ,$kurs_nilai ,$kurs_keterangan, $kurs_aktif );
		echo $result;
	}
	
	//function for create new record
	function kurs_create(){
		//POST varible here
		//auto increment, don't accept anything from form values
		$kurs_tanggal=trim(@$_POST["kurs_tanggal"]);
		$kurs_negara=trim(@$_POST["kurs_negara"]);
		$kurs_negara=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_negara);
		$kurs_negara=str_replace("'", '"',$kurs_negara);
		$kurs_initial=trim(@$_POST["kurs_initial"]);
		$kurs_initial=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_initial);
		$kurs_initial=str_replace("'", '"',$kurs_initial);
		$kurs_nilai=trim(@$_POST["kurs_nilai"]);
		$kurs_keterangan=trim(@$_POST["kurs_keterangan"]);
		$kurs_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_keterangan);
		$kurs_keterangan=str_replace("'", '"',$kurs_keterangan);
		$kurs_aktif=trim(@$_POST["kurs_aktif"]);
		$kurs_aktif=str_replace("/(<\/?)(p)([^>]*>)", "",$kurs_aktif);
		$kurs_aktif=str_replace("'", '"',$kurs_aktif);
		$result=$this->m_kurs->kurs_create($kurs_tanggal ,$kurs_negara ,$kurs_initial ,$kurs_nilai ,$kurs_keterangan, $kurs_aktif );
		echo $result;
	}

	//function for delete selected record
	function jenis_delete(){
		$ids = $_POST['ids']; // Get our array back and translate it :
		$pkid = json_decode(stripslashes($ids));
		$result=$this->m_jenis->jenis_delete($pkid);
		echo $result;
	}

	//function for advanced search
	function jenis_search(){
		//POST varibale here
		$jenis_id=trim(@$_POST["jenis_id"]);
		$jenis_kode=trim(@$_POST["jenis_kode"]);
		$jenis_kode=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_kode);
		$jenis_kode=str_replace("'", '"',$jenis_kode);
		$jenis_nama=trim(@$_POST["jenis_nama"]);
		$jenis_nama=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_nama);
		$jenis_nama=str_replace("'", '"',$jenis_nama);
		$jenis_kelompok=trim(@$_POST["jenis_kelompok"]);
		$jenis_kelompok=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_kelompok);
		$jenis_kelompok=str_replace("'", '"',$jenis_kelompok);
		$jenis_keterangan=trim(@$_POST["jenis_keterangan"]);
		$jenis_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_keterangan);
		$jenis_keterangan=str_replace("'", '"',$jenis_keterangan);
		$jenis_aktif=trim(@$_POST["jenis_aktif"]);
		$jenis_aktif=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_aktif);
		$jenis_aktif=str_replace("'", '"',$jenis_aktif);
		
		$start = (integer) (isset($_POST['start']) ? $_POST['start'] : $_GET['start']);
		$end = (integer) (isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit']);
		$result = $this->m_jenis->jenis_search($jenis_id ,$jenis_kode ,$jenis_nama ,$jenis_kelompok ,$jenis_keterangan ,$jenis_aktif ,$start,$end);
		echo $result;
	}


	function jenis_print(){
  		//POST varibale here
		$jenis_id=trim(@$_POST["jenis_id"]);
		$jenis_kode=trim(@$_POST["jenis_kode"]);
		$jenis_kode=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_kode);
		$jenis_kode=str_replace("'", '"',$jenis_kode);
		$jenis_nama=trim(@$_POST["jenis_nama"]);
		$jenis_nama=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_nama);
		$jenis_nama=str_replace("'", '"',$jenis_nama);
		$jenis_kelompok=trim(@$_POST["jenis_kelompok"]);
		$jenis_kelompok=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_kelompok);
		$jenis_kelompok=str_replace("'", '"',$jenis_kelompok);
		$jenis_keterangan=trim(@$_POST["jenis_keterangan"]);
		$jenis_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_keterangan);
		$jenis_keterangan=str_replace("'", '"',$jenis_keterangan);
		$jenis_aktif=trim(@$_POST["jenis_aktif"]);
		$jenis_aktif=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_aktif);
		$jenis_aktif=str_replace("'", '"',$jenis_aktif);
		$option=$_POST['currentlisting'];
		$filter=$_POST["query"];
		
		$result = $this->m_jenis->jenis_print($jenis_id ,$jenis_kode ,$jenis_nama ,$jenis_kelompok ,$jenis_keterangan ,$jenis_aktif ,$option,$filter);
		$nbrows=$result->num_rows();
		$totcolumn=11;
   		/* We now have our array, let's build our HTML file */
		$file = fopen("jenislist.html",'w');
		fwrite($file, "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' /><title>PRINTING DAFTAR GROUP 2</title><link rel='stylesheet' type='text/css' href='assets/modules/main/css/printstyle.css'/></head>");
		fwrite($file, "<body onload='window.print()'><table summary='Jenis List'><caption>DAFTAR GROUP 2</caption><thead><tr><th scope='col'>No</th><th scope='col'>Kode</th><th scope='col'>Nama</th><th scope='col'>Kelompok</th><th scope='col'>Keterangan</th><th scope='col'>Aktif</th></tr></thead><tfoot><tr><th scope='row'>Total</th><td colspan='$totcolumn'>");
		fwrite($file, $nbrows);
		fwrite($file, " Jenis</td></tr></tfoot><tbody>");
		$i=0;
		if($nbrows>0){
			foreach($result->result_array() as $data){
			$i++;
				fwrite($file,'<tr');
				if($i%1==0){
					fwrite($file," class='odd'");
				}
			
				fwrite($file, "><th scope='row' id='r97'>");
				fwrite($file, $i);
				fwrite($file,"</th><td>");
				fwrite($file, $data['jenis_kode']);
				fwrite($file,"</td><td>");
				fwrite($file, $data['jenis_nama']);
				fwrite($file,"</td><td>");
				fwrite($file, $data['jenis_kelompok']);
				fwrite($file,"</td><td>");
				fwrite($file, $data['jenis_keterangan']);
				fwrite($file,"</td><td>");
				fwrite($file, $data['jenis_aktif']);
				fwrite($file, "</td></tr>");
				fwrite($file, "</td></tr>");
			}
		}
		fwrite($file, "</tbody></table></body></html>");	
		fclose($file);
		echo '1';        
	}
	/* End Of Function */

	/* Function to Export Excel document */
	function jenis_export_excel(){
		//POST varibale here
		$jenis_id=trim(@$_POST["jenis_id"]);
		$jenis_kode=trim(@$_POST["jenis_kode"]);
		$jenis_kode=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_kode);
		$jenis_kode=str_replace("'", '"',$jenis_kode);
		$jenis_nama=trim(@$_POST["jenis_nama"]);
		$jenis_nama=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_nama);
		$jenis_nama=str_replace("'", '"',$jenis_nama);
		$jenis_kelompok=trim(@$_POST["jenis_kelompok"]);
		$jenis_kelompok=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_kelompok);
		$jenis_kelompok=str_replace("'", '"',$jenis_kelompok);
		$jenis_keterangan=trim(@$_POST["jenis_keterangan"]);
		$jenis_keterangan=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_keterangan);
		$jenis_keterangan=str_replace("'", '"',$jenis_keterangan);
		$jenis_aktif=trim(@$_POST["jenis_aktif"]);
		$jenis_aktif=str_replace("/(<\/?)(p)([^>]*>)", "",$jenis_aktif);
		$jenis_aktif=str_replace("'", '"',$jenis_aktif);
		$option=$_POST['currentlisting'];
		$filter=$_POST["query"];
		
		$query = $this->m_jenis->jenis_export_excel($jenis_id ,$jenis_kode ,$jenis_nama ,$jenis_kelompok ,$jenis_keterangan ,$jenis_aktif ,$option,$filter);
		
		to_excel($query,"group_2"); 
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