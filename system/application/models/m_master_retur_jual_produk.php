<? /* 	
	GIOV Solution - Keep IT Simple
*/

class M_master_retur_jual_produk extends Model{
		
		//constructor
		function M_master_retur_jual_produk() {
			parent::Model();
		}
		

	/*
	function get_produk_list($query,$start,$end){
		$rs_rows=0;
		if(is_numeric($query)==true){
			// $sql_dproduk="SELECT dproduk_produk FROM detail_jual_produk WHERE dproduk_master='$query'";
			$sql_dproduk = "SELECT * from vu_produk WHERE produk_aktif='Aktif'";

			$rs=$this->db->query($sql_dproduk);
			$rs_rows=$rs->num_rows();
		}elseif(is_string($query)==true){
			// $sql_dproduk="SELECT dproduk_master FROM detail_jual_produk LEFT JOIN master_jual_produk ON(dproduk_master=jproduk_id) WHERE jproduk_nobukti='$query'";
			$sql_dproduk = "SELECT * from vu_produk WHERE produk_aktif='Aktif'";
			$rs=$this->db->query($sql_dproduk);
			if($rs->num_rows()){
				$rs_record=$rs->row_array();
				$query=$rs_record["dproduk_master"];
			}
		}
		
			
		$sql_retur = "select rproduk_nobukti from master_retur_jual_produk where rproduk_nobuktijual = '$query' ";	
		$rs_retur=$this->db->query($sql_retur);
		$rs_rows_retur=$rs_retur->num_rows();
			
		if ($rs_rows_retur == null){
			
			$sql="SELECT produk_id
					,produk_nama
					,detail_jual_produk.dproduk_karyawan as sales_id
					,satuan_id
					,satuan_kode
					,(detail_jual_produk.dproduk_harga*((100-dproduk_diskon)/100)*((100-jproduk_diskon)/100)) AS retur_produk_harga
					,dproduk_jumlah as sisa_produk
					,master_jual_produk.jproduk_cashback as voucher
			FROM detail_jual_produk
				LEFT JOIN master_jual_produk ON(dproduk_master=jproduk_id)
				LEFT JOIN produk ON(dproduk_produk=produk_id)
				LEFT JOIN satuan ON(dproduk_satuan=satuan_id)
			WHERE dproduk_master='$query' 
			group by produk_id";
			

			$sql = " SELECT * from vu_produk WHERE produk_aktif='Aktif'";
		}

		else{
			$sql="SELECT produk_id
					,produk_nama
					,detail_jual_produk.dproduk_karyawan as sales_id
					,satuan_id
					,satuan_kode
					,(detail_jual_produk.dproduk_harga*((100-dproduk_diskon)/100)*((100-jproduk_diskon)/100)) AS retur_produk_harga
					,dproduk_jumlah
					,master_jual_produk.jproduk_cashback as voucher
					,(dproduk_jumlah - ifnull((select sum(dt.drproduk_jumlah) from detail_retur_jual_produk dt left join master_retur_jual_produk ms on (ms.rproduk_id = dt.drproduk_master) where ms.rproduk_nobuktijual = '$query' and ms.rproduk_stat_dok = 'Tertutup'),0)) as sisa_produk
				FROM detail_jual_produk
				LEFT JOIN master_jual_produk ON(dproduk_master=jproduk_id)
				LEFT JOIN produk ON(dproduk_produk=produk_id)
				LEFT JOIN satuan ON(dproduk_satuan=satuan_id)
				LEFT JOIN master_retur_jual_produk on (master_retur_jual_produk.rproduk_nobuktijual = master_jual_produk.jproduk_id)
				LEFT JOIN detail_retur_jual_produk on (master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master)
				WHERE dproduk_master='$query' and detail_jual_produk.dproduk_produk = detail_retur_jual_produk.drproduk_produk
				group by produk_id";
		}
			
		
		
		$result = $this->db->query($sql);
		$nbrows = $result->num_rows();
		if($end!=0){
			$limit = $sql." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit);
		}
		if($nbrows>0){
			foreach($result->result() as $row){
				$arr[] = $row;
			}
			$jsonresult = json_encode($arr);
			return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
		} else {
			return '({"total":"0", "results":""})';
		}
	}
	*/


	function get_satuan_produk_list($selected_id){
			
			$sql="SELECT satuan_id,satuan_kode,satuan_nama FROM vu_satuan_konversi WHERE produk_aktif='Aktif'";
			
			if($selected_id!==""){
				$sql.=(eregi("WHERE",$sql)?" AND ":" WHERE ")." produk_id='".$selected_id."'";
			}
			
			$result = $this->db->query($sql);
			$nbrows = $result->num_rows();
			
			if($nbrows>0){
				foreach($result->result() as $row){
					$arr[] = $row;
				}
				$jsonresult = json_encode($arr);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				return '({"total":"0", "results":""})';
			}
		}
	
	// Get Reveral List Returan Jual Produk
	function get_reveral_list($query,$start,$end){
		$sql="SELECT karyawan_id,karyawan_no,karyawan_username,karyawan_nama,karyawan_tgllahir,karyawan_alamat
		FROM karyawan where karyawan_aktif='Aktif'";
		if($query<>""){
			$sql=$sql." and (karyawan_no like '%".$query."%' or karyawan_nama like '%".$query."%') ";
		}
		
		$result = $this->db->query($sql);
		$nbrows = $result->num_rows();
		$limit = $sql." LIMIT ".$start.",".$end;			
		$result = $this->db->query($limit);  
		if($nbrows>0){
			foreach($result->result() as $row){
				$arr[] = $row;
			}
			$jsonresult = json_encode($arr);
			return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
		} else {
			return '({"total":"0", "results":""})';
		}
	}


	function get_produk_list($query,$start,$end,$aktif){
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_dproduk="SELECT drproduk_produk FROM detail_retur_jual_produk WHERE drproduk_master='$query'";
			$rs=$this->db->query($sql_dproduk);
			$rs_rows=$rs->num_rows();
		}
		
		if($aktif=='yes'){
			$sql="select * from vu_produk WHERE produk_aktif='Aktif'";
		}else{
			$sql="select * from vu_produk WHERE produk_aktif = 'Aktif'";
		}
		
		if($query<>"" && is_numeric($query)==false){
			$sql.=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql.=" (produk_kode like '%".$query."%' or produk_nama like '%".$query."%' ) ";
			//$sql.=" (produk_nama like '%".$query."%' ) ";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("WHERE",$sql)? " AND ":" WHERE ";
				foreach($rs->result() as $row_dproduk){
					
					$filter.="OR produk_id='".$row_dproduk->drproduk_produk."' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
		}
		
		$result = $this->db->query($sql);
		$nbrows = $result->num_rows();
		if(($end!=0)  && ($aktif<>'yesno')){
			$limit = $sql." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit);
		}
		if($nbrows>0){
			foreach($result->result() as $row){
				$arr[] = $row;
			}
			$jsonresult = json_encode($arr);
			return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
		} else {
			return '({"total":"0", "results":""})';
		}
	}
		
		function get_laporan($tgl_awal,$tgl_akhir,$periode,$opsi,$group){
			
			switch($group){
				case "Tanggal": $order_by=" ORDER BY tanggal";break;
				case "Customer": $order_by=" ORDER BY cust_nama ASC";break;
				case "No Faktur": $order_by=" ORDER BY no_bukti";break;
				case "Produk": $order_by=" ORDER BY produk_kode";break;
				case "No Faktur Jual": $order_by=" ORDER BY no_bukti_jual";break;
				default: $order_by=" ORDER BY no_bukti ASC";break;
			}
			
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT distinct * FROM vu_trans_retur_produk WHERE rproduk_stat_dok<>'Batal' ".$order_by;
				else if($periode=='bulan')
					$sql="SELECT distinct * FROM vu_trans_retur_produk WHERE date_format(tanggal,'%Y-%m')='".$tgl_awal."' 
							AND rproduk_stat_dok<>'Batal' ".$order_by;
				else if($periode=='tanggal')
					$sql="SELECT distinct * FROM vu_trans_retur_produk WHERE date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' 
							AND date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' AND rproduk_stat_dok<>'Batal' ".$order_by;
			}else if($opsi=='detail'){
				if($periode=='all')
					$sql="SELECT * FROM vu_detail_retur_jual_produk WHERE rproduk_stat_dok<>'Batal' ".$order_by;
				else if($periode=='bulan')
					$sql="SELECT * FROM vu_detail_retur_jual_produk WHERE date_format(tanggal,'%Y-%m')='".$tgl_awal."' 
							AND  rproduk_stat_dok<>'Batal' ".$order_by;
				else if($periode=='tanggal')
					$sql="SELECT * FROM vu_detail_retur_jual_produk WHERE date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' 
							AND date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' AND rproduk_stat_dok<>'Batal' ".$order_by;
			}
			
			//$this->firephp->log($sql);
			
			$query=$this->db->query($sql);
			return $query->result();
		}
		
		function get_total_item($tgl_awal,$tgl_akhir,$periode,$opsi){
			$sql="";
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(jumlah_barang) as total_item FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(jumlah_barang) as total_item FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(jumlah_barang) as total_item FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}else if($opsi=='detail'){
				if($periode=='all')
					$sql="SELECT SUM(jumlah_barang) as total_item FROM vu_detail_retur_jual_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(jumlah_barang) as total_item FROM vu_detail_retur_jual_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(jumlah_barang) as total_item FROM vu_detail_retur_jual_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_item;
			}else
				return "";
		}
		
		function get_total_diskon($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(cashback) as total_diskon FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(cashback) as total_diskon FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(cashback) as total_diskon FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}else if($opsi=='detail'){
				if($periode=='all')
					$sql="SELECT SUM(diskon_nilai) as total_diskon FROM vu_detail_retur_jual_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(diskon_nilai) as total_diskon FROM vu_detail_retur_jual_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(diskon_nilai) as total_diskon FROM vu_detail_retur_jual_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_diskon;
			}else
				return "";
		}
		
		function get_total_nilai($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(total_nilai) as total_nilai FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(total_nilai) as total_nilai FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(total_nilai) as total_nilai FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}else if($opsi=='detail'){
				if($periode=='all')
					$sql="SELECT SUM(subtotal) as total_nilai FROM vu_detail_retur_jual_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(subtotal) as total_nilai FROM vu_detail_retur_jual_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(subtotal) as total_nilai FROM vu_detail_retur_jual_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_nilai;
			}else
				return "";
		}
		
		function get_total_cek($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(cek) as total_cek FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(cek) as total_cek FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(cek) as total_cek FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_cek;
			}else
				return "";
		}

		//Delete detail_retur_jual_produk
		function detail_retur_jual_produk_delete($drproduk_id){
			$query = "DELETE FROM detail_retur_jual_produk WHERE drproduk_id = ".$drproduk_id;
			$this->db->query($query);
			if($this->db->affected_rows()>0)
				return '1';
			else
				return '0';
		}

		
		function get_total_tunai($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(tunai) as total_tunai FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(tunai) as total_tunai FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(tunai) as total_tunai FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_tunai;
			}else
				return "";
		}
		
		function get_total_transfer($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(transfer) as total_transfer FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(transfer) as total_transfer FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(transfer) as total_transfer FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_transfer;
			}else
				return "";
		}
		
		function get_total_card($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(card) as total_card FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(card) as total_card FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(card) as total_card FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_card;
			}else
				return "";
		}
		
		function get_total_kredit($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(kredit) as total_kredit FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(kredit) as total_kredit FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(kredit) as total_kredit FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_kredit;
			}else
				return "";
		}
		
		function get_total_kuitansi($tgl_awal,$tgl_akhir,$periode,$opsi){
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT SUM(kuitansi) as total_kuitansi FROM vu_trans_retur_produk";
				else if($periode=='bulan')
					$sql="SELECT SUM(kuitansi) as total_kuitansi FROM vu_trans_retur_produk WHERE tanggal like '".$tgl_awal."%'";
				else if($periode=='tanggal')
					$sql="SELECT SUM(kuitansi) as total_kuitansi FROM vu_trans_retur_produk WHERE tanggal>='".$tgl_awal."' AND tanggal<='".$tgl_akhir."'";
			}
			$query=$this->db->query($sql);
			if($query->num_rows()){
				$data=$query->row();
				return $data->total_kuitansi;
			}else
				return "";
		}
	
		function get_jual_produk_list($query,$start,$end){
			$sql="SELECT jproduk_id,jproduk_nobukti,jproduk_tanggal,cust_nama,cust_alamat,cust_id, jproduk_cashback as voucher 
					FROM master_jual_produk,customer 
					WHERE jproduk_cust=cust_id AND jproduk_stat_dok='Tertutup' AND date_add(date_format(master_jual_produk.jproduk_tanggal,'%Y-%m-%d'),INTERVAL 1 YEAR)>=date_format(now(),'%Y-%m-%d')";
				
			if($query<>"")
				$sql.=" and (jproduk_nobukti like '%".$query."%' or jproduk_tanggal like '%".$query."%' or cust_nama like '%".$query."%' or cust_alamat like '%".$query."%' or jproduk_nobukti like '%".$query."%') "; 
			$query = $this->db->query($sql);
			$nbrows = $query->num_rows();
			$limit = $sql." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit);  
			if($nbrows>0){
				foreach($result->result() as $row){
					$arr[] = $row;
				}
				$jsonresult = json_encode($arr);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				return '({"total":"0", "results":""})';
			}
		}
		
		//function for detail
		//get record list
		function detail_detail_retur_jual_produk_list($master_id,$query,$start,$end) {
			$query = "SELECT drproduk_id, drproduk_master, drproduk_produk, drproduk_satuan, satuan_nama, drproduk_jumlah, drproduk_harga, drproduk_harga * drproduk_jumlah as drproduk_subtotal, drproduk_sales_id as sales_id , drproduk_diskon
				FROM detail_retur_jual_produk 
				LEFT JOIN satuan ON(drproduk_satuan=satuan_id) 
				WHERE drproduk_master='".$master_id."'";
			
			$result = $this->db->query($query);
			$nbrows = $result->num_rows();
			$limit = $query." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit);  
			
			if($nbrows>0){
				foreach($result->result() as $row){
					$arr[] = $row;
				}
				$jsonresult = json_encode($arr);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				return '({"total":"0", "results":""})';
			}
		}
		//end of function
		
		//get master id, note : not done yet
		function get_master_id() {
			$query = "SELECT max(rproduk_id) as master_id from master_retur_jual_produk";
			$result = $this->db->query($query);
			if($result->num_rows()){
				$data=$result->row();
				$master_id=$data->master_id;
				return $master_id;
			}else{
				return '0';
			}
		}
		//eof
		
		//purge all detail from master
		function detail_detail_retur_jual_produk_purge($master_id){
			$sql="DELETE from detail_retur_jual_produk where drproduk_master='".$master_id."'";
			$result=$this->db->query($sql);
		}
		//*eof
		
		//insert detail record
		function detail_detail_retur_jual_produk_insert($array_drproduk_id, $drproduk_master, $array_drproduk_produk, $array_drproduk_satuan, $array_drproduk_jumlah, $array_drproduk_harga, $array_drproduk_diskon, $array_sales_id){
			//if master id not capture from view then capture it from max pk from master table
			if($drproduk_master=="" || $drproduk_master==NULL){
				$drproduk_master=$this->get_master_id();
			}
			
			$size_array = sizeof($array_drproduk_produk) - 1;
			for($i = 0; $i < sizeof($array_drproduk_produk); $i++){
				$drproduk_id = $array_drproduk_id[$i];
				$drproduk_master = $drproduk_master;
				$drproduk_produk = $array_drproduk_produk[$i];
				$drproduk_satuan = $array_drproduk_satuan[$i];
				$drproduk_jumlah = $array_drproduk_jumlah[$i];
				$drproduk_harga = $array_drproduk_harga[$i];
				$drproduk_diskon = $array_drproduk_diskon[$i];
				$sales_id 		= $array_sales_id[$i];
	
				$sql = "SELECT drproduk_id
					FROM detail_retur_jual_produk
					WHERE drproduk_id='".$drproduk_id."'";
				$rs = $this->db->query($sql);
				
				if($rs->num_rows()){
				// jika datanya sudah ada maka update saja
					$dtu_detail_retur = array(
						"drproduk_master"=>$drproduk_master,
						"drproduk_produk"=>$drproduk_produk,
						"drproduk_satuan"=>$drproduk_satuan,
						"drproduk_jumlah"=>$drproduk_jumlah,
						"drproduk_harga"=>$drproduk_harga,
						"drproduk_diskon"=>$drproduk_diskon,
						"drproduk_sales_id"=>$sales_id
					);
					$this->db->where('drproduk_id', $drproduk_id);
					$this->db->update('detail_retur_jual_produk', $dtu_detail_retur); 
				}else {
					$data = array(
						"drproduk_master"=>$drproduk_master,
						"drproduk_produk"=>$drproduk_produk,
						"drproduk_satuan"=>$drproduk_satuan,
						"drproduk_jumlah"=>$drproduk_jumlah,
						"drproduk_harga"=>$drproduk_harga,
						"drproduk_diskon"=>$drproduk_diskon,
						"drproduk_sales_id"=>$sales_id
					);
					$this->db->insert('detail_retur_jual_produk', $data); 	
				}	
		}

			if($this->db->affected_rows())
				return $drproduk_master;
			else
				return '0';

		}
		//end of function
		
		//function for get list record
		function master_retur_jual_produk_list($filter,$start,$end){
//			$query = "SELECT * FROM master_retur_jual_produk LEFT JOIN customer ON(rproduk_cust=cust_id) LEFT JOIN master_jual_produk ON(rproduk_nobuktijual=jproduk_id) LEFT JOIN cetak_kwitansi ON(kwitansi_ref=rproduk_nobukti)";
			$query =   "SELECT
							rproduk_id, rproduk_nobukti, jproduk_nobukti, cust_no, cust_nama, cust_id, 
							rproduk_tanggal, rproduk_keterangan, rproduk_stat_dok, rproduk_creator,	rproduk_voucher,
							rproduk_date_create, rproduk_update, rproduk_date_update, rproduk_revised, kwitansi_id, kwitansi_nilai, kwitansi_keterangan
						FROM master_retur_jual_produk m
						LEFT JOIN customer c ON(m.rproduk_cust=c.cust_id) 
						LEFT JOIN master_jual_produk mp ON(m.rproduk_nobuktijual=mp.jproduk_id) 
						LEFT JOIN cetak_kwitansi ck ON(ck.kwitansi_ref=m.rproduk_nobukti)";
				
			// For simple search
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (rproduk_nobukti LIKE '%".addslashes($filter)."%' OR jproduk_nobukti LIKE '%".addslashes($filter)."%' OR cust_nama LIKE '%".addslashes($filter)."%' OR cust_no LIKE '%".addslashes($filter)."%' )";
			}
			
			$query.=" ORDER BY rproduk_nobukti desc";
			
			$result = $this->db->query($query);
			$nbrows = $result->num_rows();
			$limit = $query." LIMIT ".$start.",".$end;		
			$result = $this->db->query($limit);  
			
			if($nbrows>0){
				foreach($result->result() as $row){
					$arr[] = $row;
				}
				$jsonresult = json_encode($arr);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				return '({"total":"0", "results":""})';
			}
		}
		
		//function for update record
		function master_retur_jual_produk_update($rproduk_cetak, $rproduk_id ,$rproduk_nobukti ,$rproduk_nobuktijual ,$rproduk_cust ,$rproduk_tanggal ,$rproduk_keterangan , $rproduk_stat_dok, 
			$array_drproduk_id, $array_drproduk_produk, $array_drproduk_satuan, $array_drproduk_jumlah, $array_drproduk_harga, $array_drproduk_diskon, $array_sales_id){
			
			$sql_check_locked = "SELECT * from master_retur_jual_produk where rproduk_id = '".$rproduk_id."'";
			$rs_check_locked = $this->db->query($sql_check_locked);
			if($rs_check_locked->num_rows()>0){
				$record = $rs_check_locked->row_array();
				$rproduk_tanggal_awal = $record['rproduk_tanggal'];
			}

			$time_rproduk_tanggal_awal = strtotime($rproduk_tanggal_awal);
			$time_rproduk_tanggal = strtotime($rproduk_tanggal);

			if(date("ym",$time_rproduk_tanggal) <> date("ym",$time_rproduk_tanggal_awal)){
			$rproduk_tanggal_pattern=strtotime($rproduk_tanggal);
			$pattern="RJ/".date("ym",$rproduk_tanggal_pattern)."-";
			$rproduk_nobukti=$this->m_public_function->get_kode_1('master_retur_jual_produk','rproduk_nobukti',$pattern,12);
			}

			$data = array(
				"rproduk_id"=>$rproduk_id, 
				"rproduk_nobukti"=>$rproduk_nobukti, 
				//"rproduk_nobuktijual"=>$rproduk_nobuktijual, 
				//"rproduk_cust"=>$rproduk_cust, 
				"rproduk_tanggal"=>$rproduk_tanggal, 
				"rproduk_keterangan"=>$rproduk_keterangan
				// "rproduk_stat_dok"=>$rproduk_stat_dok
			);

			if($rproduk_cetak==1 || $rproduk_cetak==2){
				$data['rproduk_stat_dok'] = 'Tertutup';
			}else{
				$data['rproduk_stat_dok'] = 'Terbuka';
			}

			$this->db->where('rproduk_id', $rproduk_id);
			$this->db->update('master_retur_jual_produk', $data);
			

			$rs_drproduk_insert = $this->detail_detail_retur_jual_produk_insert($array_drproduk_id, $rproduk_id, $array_drproduk_produk, $array_drproduk_satuan, $array_drproduk_jumlah, $array_drproduk_harga, $array_drproduk_diskon, $array_sales_id);
			
			 return $rproduk_id;


			// return '1';
		}
		
		//function for create new record
		function master_retur_jual_produk_create($rproduk_nobukti ,$rproduk_nobuktijual ,$rproduk_cust ,$rproduk_tanggal ,$rproduk_keterangan , $rproduk_stat_dok, $rproduk_kwitansi_nilai ,$rproduk_kwitansi_keterangan,$rproduk_voucher, 
			$array_drproduk_id, $array_drproduk_produk, $array_drproduk_satuan, $array_drproduk_jumlah, $array_drproduk_harga, $array_drproduk_diskon, $array_sales_id){
			
			$sql = "SELECT rproduk_id
				FROM master_retur_jual_produk
				WHERE rproduk_nobuktijual='".$rproduk_nobuktijual."'
					AND rproduk_stat_dok<>'Batal'";
			$this->db->query($sql);
			/*if($this->db->affected_rows()){
				return '-1';
			}else{*/
	//			$pattern="RFT/".date("ym")."-";
				$rproduk_tanggal_pattern=strtotime($rproduk_tanggal);
				$pattern="RJ/".date("ym",$rproduk_tanggal_pattern)."-";
				$rproduk_nobukti=$this->m_public_function->get_kode_1('master_retur_jual_produk','rproduk_nobukti',$pattern,12);
				
				$data = array(
					"rproduk_nobukti"=>$rproduk_nobukti, 
					"rproduk_nobuktijual"=>$rproduk_nobuktijual, 
					"rproduk_cust"=>$rproduk_cust, 
					"rproduk_tanggal"=>$rproduk_tanggal, 
					"rproduk_keterangan"=>$rproduk_keterangan,
					"rproduk_voucher"=>$rproduk_voucher,
					//"rproduk_stat_dok"=>$rproduk_stat_dok
					"rproduk_stat_dok"=>'Tertutup',
					"rproduk_creator"=>@$_SESSION[SESSION_USERID]
				);
				$this->db->insert('master_retur_jual_produk', $data); 

				$rproduk_id = $this->db->insert_id();

				$rs_drproduk_insert = $this->detail_detail_retur_jual_produk_insert($array_drproduk_id, $rproduk_id, $array_drproduk_produk, $array_drproduk_satuan, $array_drproduk_jumlah, $array_drproduk_harga, $array_drproduk_diskon, $array_sales_id);


				if($this->db->affected_rows()){
						return $rproduk_id;
					}else{
						return '1';
					}
				/*
				if($this->db->affected_rows()){
					$pattern="KU/".date('ym')."-";
					$kwitansi_no=$this->m_public_function->get_kode_1("cetak_kwitansi","kwitansi_no",$pattern,12);
					$dti_kwitansi=array(
					"kwitansi_tanggal"=>$rproduk_tanggal,
					"kwitansi_no"=>$kwitansi_no,
					"kwitansi_cust"=>$rproduk_cust,
					"kwitansi_ref"=>$rproduk_nobukti,
					"kwitansi_cara"=>'retur',
					"kwitansi_bayar"=>$rproduk_kwitansi_nilai,
					"kwitansi_nilai"=>$rproduk_kwitansi_nilai,
					"kwitansi_sisa"=>$rproduk_kwitansi_nilai,
					"kwitansi_keterangan"=>$rproduk_kwitansi_keterangan, 
					"kwitansi_status"=>'Tertutup',
					"kwitansi_creator"=>@$_SESSION[SESSION_USERID]
					);
					$this->db->insert('cetak_kwitansi', $dti_kwitansi);
					if($this->db->affected_rows()){
						return $rproduk_nobukti;
					}else{
						return '1';
					}
					
				}else
					return '0';
				*/
		
		}
		
		//fcuntion for delete record
		function master_retur_jual_produk_delete($pkid){
			// You could do some checkups here and return '0' or other error consts.
			// Make a single query to delete all of the master_retur_jual_produks at the same time :
			if(sizeof($pkid)<1){
				return '0';
			} else if (sizeof($pkid) == 1){
				$query = "DELETE FROM master_retur_jual_produk WHERE rproduk_id = ".$pkid[0];
				$this->db->query($query);
			} else {
				$query = "DELETE FROM master_retur_jual_produk WHERE ";
				for($i = 0; $i < sizeof($pkid); $i++){
					$query = $query . "rproduk_id= ".$pkid[$i];
					if($i<sizeof($pkid)-1){
						$query = $query . " OR ";
					}     
				}
				$this->db->query($query);
			}
			if($this->db->affected_rows()>0)
				return '1';
			else
				return '0';
		}
		
		function master_retur_jual_produk_batal($rproduk_id){
			$sql = "SELECT rproduk_nobukti FROM master_retur_jual_produk WHERE rproduk_id='$rproduk_id'";
			$rs = $this->db->query($sql);
			if($rs->num_rows()){
				$record = $rs->row_array();
				$kwitansi_ref = $record['rproduk_nobukti'];
				$dtu_kwitansi=array(
				"kwitansi_status"=>'Batal'
				);
				$this->db->where('kwitansi_ref', $kwitansi_ref);
				$this->db->update('cetak_kwitansi', $dtu_kwitansi);
			}
			
			$dtu_rproduk=array(
			"rproduk_stat_dok"=>'Batal'
			);
			$this->db->where('rproduk_id', $rproduk_id);
			$this->db->update('master_retur_jual_produk', $dtu_rproduk);
			return '1';
		}
		
		//function for advanced search record
		function master_retur_jual_produk_search($rproduk_nobukti ,$rproduk_nobuktijual ,$rproduk_cust ,$rproduk_tanggal , $rproduk_tanggal_akhir, $rproduk_keterangan , $rproduk_stat_dok, $start,$end){
			//full query
			$query="SELECT
							rproduk_id, rproduk_nobukti, jproduk_nobukti, cust_no, cust_nama, cust_id, 
							rproduk_tanggal, rproduk_keterangan, rproduk_stat_dok, rproduk_creator,	rproduk_voucher,
							rproduk_date_create, rproduk_update, rproduk_date_update, rproduk_revised, kwitansi_id, kwitansi_nilai, kwitansi_keterangan
						FROM master_retur_jual_produk m
						LEFT JOIN customer c ON(m.rproduk_cust=c.cust_id) 
						LEFT JOIN master_jual_produk mp ON(m.rproduk_nobuktijual=mp.jproduk_id) 
						LEFT JOIN cetak_kwitansi ck ON(ck.kwitansi_ref=m.rproduk_nobukti)";
			
			if($rproduk_nobukti!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " rproduk_nobukti LIKE '%".$rproduk_nobukti."%'";
			};
			if($rproduk_nobuktijual!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " jproduk_nobukti LIKE '%".$rproduk_nobuktijual."%'";
			};
			if($rproduk_cust!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " cust_nama LIKE '%".$rproduk_cust."%'";
			};
			if($rproduk_tanggal!='' && $rproduk_tanggal_akhir!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " rproduk_tanggal BETWEEN '".$rproduk_tanggal."' AND '".$rproduk_tanggal_akhir."'";
			}
			else if($rproduk_tanggal!='' && $rproduk_tanggal_akhir==''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " rproduk_tanggal='".$rproduk_tanggal."'";
			}
			if($rproduk_keterangan!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " rproduk_keterangan LIKE '%".$rproduk_keterangan."%'";
			};
			if($rproduk_stat_dok!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " rproduk_stat_dok LIKE '%".$rproduk_stat_dok."%'";
			};

			$query.=" ORDER BY rproduk_nobukti desc";
			
			$result = $this->db->query($query);
			$nbrows = $result->num_rows();
			
			$limit = $query." LIMIT ".$start.",".$end;		
			$result = $this->db->query($limit);    
			
			if($nbrows>0){
				foreach($result->result() as $row){
					$arr[] = $row;
				}
				$jsonresult = json_encode($arr);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				return '({"total":"0", "results":""})';
			}
		}
		
		//function for print record
		function master_retur_jual_produk_print($rproduk_nobukti
												,$rproduk_nobuktijual
												,$rproduk_cust
												,$rproduk_tanggal
												,$rproduk_tanggal_akhir
												,$rproduk_keterangan
												,$rproduk_stat_dok
												,$option
												,$filter){
			//full query
			$query =   "SELECT rproduk_tanggal AS tanggal
					,rproduk_nobukti AS no_faktur
					,jproduk_nobukti AS no_faktur_jual
					,cust_no AS no_cust
					,cust_nama AS customer
					,kwitansi_nilai
					,rproduk_keterangan AS keterangan
					,rproduk_stat_dok AS stat_dok
				FROM master_retur_jual_produk m
				LEFT JOIN customer c ON(m.rproduk_cust=c.cust_id) 
				LEFT JOIN master_jual_produk mp ON(m.rproduk_nobuktijual=mp.jproduk_id) 
				LEFT JOIN cetak_kwitansi ck ON(ck.kwitansi_ref=m.rproduk_nobukti)";
				
			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (rproduk_nobukti LIKE '%".addslashes($filter)."%' OR jproduk_nobukti LIKE '%".addslashes($filter)."%' OR cust_nama LIKE '%".addslashes($filter)."%' OR cust_no LIKE '%".addslashes($filter)."%' )";
				$result = $this->db->query($query);
			} else if($option=='SEARCH'){
				if($rproduk_nobukti!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_nobukti LIKE '%".$rproduk_nobukti."%'";
				};
				if($rproduk_nobuktijual!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jproduk_nobukti LIKE '%".$rproduk_nobuktijual."%'";
				};
				if($rproduk_cust!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " cust_nama LIKE '%".$rproduk_cust."%'";
				};
				if($rproduk_tanggal!='' && $rproduk_tanggal_akhir!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_tanggal BETWEEN '".$rproduk_tanggal."' AND '".$rproduk_tanggal_akhir."'";
				}
				else if($rproduk_tanggal!='' && $rproduk_tanggal_akhir==''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_tanggal='".$rproduk_tanggal."'";
				}
				if($rproduk_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_keterangan LIKE '%".$rproduk_keterangan."%'";
				};
				if($rproduk_stat_dok!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_stat_dok LIKE '%".$rproduk_stat_dok."%'";
				};
				$result = $this->db->query($query);
			}
			return $result->result();
		}
		
		//function  for export to excel
		function master_retur_jual_produk_export_excel($rproduk_nobukti
														,$rproduk_nobuktijual
														,$rproduk_cust
														,$rproduk_tanggal
														,$rproduk_tanggal_akhir
														,$rproduk_keterangan
														,$rproduk_stat_dok
														,$option
														,$filter){
			//full query
			$query =   "SELECT rproduk_tanggal AS tanggal
					,rproduk_nobukti AS no_faktur
					,jproduk_nobukti AS no_faktur_jual
					,cust_no AS no_cust
					,cust_nama AS customer
					,kwitansi_nilai AS 'Nilai Kuitansi (Rp)'
					,rproduk_keterangan AS keterangan
					,rproduk_stat_dok AS stat_dok
				FROM master_retur_jual_produk m
				LEFT JOIN customer c ON(m.rproduk_cust=c.cust_id) 
				LEFT JOIN master_jual_produk mp ON(m.rproduk_nobuktijual=mp.jproduk_id) 
				LEFT JOIN cetak_kwitansi ck ON(ck.kwitansi_ref=m.rproduk_nobukti)";
				
			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (rproduk_nobukti LIKE '%".addslashes($filter)."%' OR jproduk_nobukti LIKE '%".addslashes($filter)."%' OR cust_nama LIKE '%".addslashes($filter)."%' OR cust_no LIKE '%".addslashes($filter)."%' )";
				$result = $this->db->query($query);
			} else if($option=='SEARCH'){
				if($rproduk_nobukti!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_nobukti LIKE '%".$rproduk_nobukti."%'";
				};
				if($rproduk_nobuktijual!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jproduk_nobukti LIKE '%".$rproduk_nobuktijual."%'";
				};
				if($rproduk_cust!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " cust_nama LIKE '%".$rproduk_cust."%'";
				};
				if($rproduk_tanggal!='' && $rproduk_tanggal_akhir!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_tanggal BETWEEN '".$rproduk_tanggal."' AND '".$rproduk_tanggal_akhir."'";
				}
				else if($rproduk_tanggal!='' && $rproduk_tanggal_akhir==''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_tanggal='".$rproduk_tanggal."'";
				}
				if($rproduk_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_keterangan LIKE '%".$rproduk_keterangan."%'";
				};
				if($rproduk_stat_dok!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " rproduk_stat_dok LIKE '%".$rproduk_stat_dok."%'";
				};
				$result = $this->db->query($query);
			}
			return $result;
		}
		
		function print_paper($kwitansi_ref){
			/*
			$sql="SELECT kwitansi_id,kwitansi_no,kwitansi_date_create,cust_no,cust_nama,kwitansi_nilai,kwitansi_keterangan,kwitansi_cara FROM cetak_kwitansi,customer WHERE kwitansi_cust=cust_id AND kwitansi_ref='".$kwitansi_ref."'";
			*/
			$sql = "SELECT customer.cust_nama as cust_nama, 
				customer.cust_alamat as cust_alamat, 
				customer.cust_no as cust_no, 
				customer.cust_kota as cust_kota,
				master_retur_jual_produk.rproduk_tanggal as rproduk_tanggal, 
				master_retur_jual_produk.rproduk_nobukti as rproduk_nobukti, 
				master_retur_jual_produk.rproduk_keterangan as rproduk_keterangan,
				produk.produk_nama as produk_nama, 
				produk.produk_kode as produk_kode, 
				satuan.satuan_nama as satuan_nama,
				detail_retur_jual_produk.drproduk_harga as drproduk_harga, 
				detail_retur_jual_produk.drproduk_jumlah as drproduk_jumlah, 
				detail_retur_jual_produk.drproduk_diskon as drproduk_diskon,
						karyawan.karyawan_nama as karyawan_nama, 
						karyawan.karyawan_no as karyawan_no, 
						karyawan.karyawan_username as karyawan_username,
						TIME(rproduk_date_create) AS rproduk_jam
					from detail_retur_jual_produk 
					LEFT JOIN master_retur_jual_produk on (master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master)
					LEFT JOIN customer on (master_retur_jual_produk.rproduk_cust = customer.cust_id)
					LEFT JOIN produk on (produk.produk_id = detail_retur_jual_produk.drproduk_produk)
					LEFT JOIN satuan on (satuan.satuan_id = detail_retur_jual_produk.drproduk_satuan)
					LEFT JOIN karyawan on (karyawan.karyawan_id = detail_retur_jual_produk.drproduk_sales_id)
					WHERE master_retur_jual_produk.rproduk_id = '".$kwitansi_ref."'
					";
			$result = $this->db->query($sql);
			return $result;
		}
		
		function cara_bayar($kwitansi_ref){
			$sql="SELECT kwitansi_id,kwitansi_no,kwitansi_date_create,cust_no,cust_nama,kwitansi_nilai,kwitansi_keterangan FROM cetak_kwitansi,customer WHERE kwitansi_cust=cust_id AND kwitansi_ref='".$kwitansi_ref."'";
			$result = $this->db->query($sql);
			return $result;
		}
		
}
?>