<? /* 	
	GIOV Solution - Keep IT Simple
*/

class M_master_order_beli extends Model{
		
		//constructor
		function M_master_order_beli() {
			parent::Model();
		}
		
		function get_cabang(){
			$sql="SELECT info_nama FROM info";
			
			$query2=$this->db->query($sql);
            return $query2; //by isaac
		}
		
		//function for get list record
		function get_permission_op($id){
			$query = "select perm_group from vu_permissions where perm_harga = 1 and menu_id = 31 and perm_group = ".$id."";
					
			
			$result = $this->db->query($query);		
		$nbrows = $result->num_rows();
		return $nbrows;
		}
		
		function get_laporan($tgl_awal,$tgl_akhir,$periode,$opsi,$group,$faktur){
			
			switch($group){
				case "Tanggal": $order_by=" ORDER BY tanggal";break;
				case "Supplier": $order_by=" ORDER BY supplier_id";break;
				case "No Faktur": $order_by=" ORDER BY no_bukti";break;
				case "Produk": $order_by=" ORDER BY produk_kode";break;
				default: $order_by=" ORDER BY no_bukti";break;
			}
			
			if($opsi=='rekap'){
				if($periode=='all')
					$sql="SELECT * FROM vu_trans_order WHERE order_status<>'Batal' ".$order_by;
				else if($periode=='bulan')
					$sql="SELECT * FROM vu_trans_order WHERE order_status<>'Batal' AND date_format(tanggal,'%Y-%m')='".$tgl_awal."' ".$order_by;
				else if($periode=='tanggal')
					$sql="SELECT * FROM vu_trans_order WHERE order_status<>'Batal' AND date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' 
							AND date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' ".$order_by;
			}else if($opsi=='detail'){
				if($periode=='all')
					$sql="SELECT * FROM vu_detail_order_beli WHERE order_status<>'Batal' AND  ".$order_by;
				else if($periode=='bulan')
					$sql="SELECT * FROM vu_detail_order_beli WHERE order_status<>'Batal' AND date_format(tanggal,'%Y-%m')='".$tgl_awal."' ".$order_by;
				else if($periode=='tanggal')
					$sql="SELECT * FROM vu_detail_order_beli WHERE order_status<>'Batal' AND date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' 
							AND date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' ".$order_by;
			}else if($opsi=='faktur'){
				$sql="SELECT DISTINCT * FROM vu_detail_order_beli WHERE dorder_master='".$faktur."'";
			}
			
			$query=$this->db->query($sql);
			if($opsi=='faktur')
				return $query;
			else
				return $query->result();
		}
		
		
		function get_produk_selected_list($master_id,$selected_id,$query,$start,$end){
			$sql="SELECT distinct produk_id,produk_nama,produk_kode,kategori_nama FROM vu_produk ";
			
			if($master_id!=="")
				$sql.=" WHERE produk_id IN(SELECT dorder_produk FROM detail_order_beli WHERE dorder_master='".$master_id."')";
				
			if($selected_id!=="")
			{
				$selected_id=substr($selected_id,0,strlen($selected_id)-1);
				$sql.=(eregi("WHERE",$sql)?" OR ":" WHERE ")." produk_id IN(".$selected_id.")";
			}
			if($query!==""){
				$sql.=(eregi("WHERE",$sql)?" AND ":" WHERE ")." produk_nama like '%".$query."%' OR produk_kode like '%".$query."%'";
			}
			
			$result = $this->db->query($sql);
			$nbrows = $result->num_rows();
/*			$limit = $sql." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit);  
			*/
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
				
		function get_produk_all_list($query,$start,$end){
			
			$sql="SELECT distinct produk_id,produk_nama,produk_kode,kategori_nama FROM vu_produk
						WHERE produk_aktif='Aktif'";
			if($query!==""){
				$sql.=(eregi("WHERE",$sql)?" AND ":" WHERE ")." (produk_nama like '%".$query."%' OR produk_kode like '%".$query."%')";
			}
			
			$result = $this->db->query($sql);
			$nbrows = $result->num_rows();
/*			$limit = $sql." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit); */ 
			
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
		
			
		function get_produk_detail_list($master_id,$query,$start,$end){
			$sql="SELECT distinct produk_id,produk_nama,produk_kode,kategori_nama FROM vu_produk";
			if($master_id<>"")
				$sql.=" WHERE produk_id IN(SELECT dorder_produk FROM detail_order_beli WHERE dorder_master='".$master_id."')";
				
			/*if($query!==""){
				$sql.=(eregi("WHERE",$sql)?" AND ":" WHERE ")." produk_nama like '%".$query."%' OR produk_kode like '%".$query."%'";
			}*/
			
			$result = $this->db->query($sql);
			$nbrows = $result->num_rows();
/*			$limit = $sql." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit);*/  
			
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
		
		/*Function utk mengambil harga terakhir dari pemesanan barang OP berdasarkan Tanggal terbaru yg melekat di faktur dan produk yang sama */
		function get_op_last_price($supplier_id, $produk_id, $order_tanggal){
			$sql="SELECT dorder_harga , dorder_harga_log, order_supplier, dorder_produk
					FROM detail_order_beli 
					LEFT JOIN master_order_beli ON (master_order_beli.order_id = detail_order_beli.dorder_master)
					WHERE detail_order_beli.dorder_produk = '".$produk_id."' AND master_order_beli.order_supplier = '".$supplier_id."'
				ORDER BY detail_order_beli.dorder_harga_log DESC LIMIT 0,5";
				
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
		

		function get_order_gudang_list(){
			$sql="SELECT gudang_id, gudang_nama, gudang_lokasi FROM gudang where gudang_aktif='Aktif'";
			$query = $this->db->query($sql);
			$nbrows = $query->num_rows();
			if($nbrows>0){
				foreach($query->result() as $row){
					$arr[] = $row;
				}
				$jsonresult = json_encode($arr);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				return '({"total":"0", "results":""})';
			}
		}
		
		function get_stok_produk_selected($gudang,$produk_id,$tanggal){

		
			$sql_temp="SELECT konversi_satuan, konversi_nilai FROM satuan_konversi
						WHERE konversi_default=true
						AND konversi_produk='".$produk_id."'";
			$rs=$this->db->query($sql_temp);
			if($rs->num_rows()){
				$row=$rs->row();
				$current_satuan=$row->konversi_satuan;
				$current_konversi=$row->konversi_nilai;
			}
			
			$sql_kecil="SELECT konversi_satuan, konversi_nilai FROM satuan_konversi
						WHERE konversi_nilai=1
						AND konversi_produk='".$produk_id."'";
			$rs_kecil=$this->db->query($sql_kecil);
			if($rs_kecil->num_rows()){
				$row_kecil=$rs_kecil->row();
				$satuan_terkecil=$row_kecil->konversi_satuan;
				//$current_konversi=$row->konversi_nilai;
			}
			
			
			$sql = "SELECT 
				".$satuan_terkecil." as satuan_terkecil,
				SUM(ks_masuk*ks_satuan_nilai/".$current_konversi."), 
				SUM(ks_keluar*ks_satuan_nilai/".$current_konversi."), 
				SUM(ks_masuk*ks_satuan_nilai/".$current_konversi.")-SUM(ks_keluar*ks_satuan_nilai/".$current_konversi.") AS stok_akhir, 		
				kartu_stok_fix.ks_satuan_id as satuan_id, kartu_stok_fix.ks_produk_id as produk_id 
			FROM kartu_stok_fix
					WHERE kartu_stok_fix.ks_produk_id = ".$produk_id."";

			
			$result = $this->db->query($sql);
			$nbrows = $result->num_rows();
			
			if($nbrows<1){
				$sql="SELECT distinct produk_id,produk_kode,produk_nama,0 as jumlah_stok,satuan_kode,satuan_id, satuan_nama 
						FROM vu_produk_satuan_terkecil
						WHERE produk_id='".$produk_id."'";
				$result = $this->db->query($sql);
				$nbrows = $result->num_rows();
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
		
		function get_satuan_produk_list($selected_id){
			
			$sql="SELECT satuan_id,satuan_kode,satuan_nama,konversi_default,konversi_nilai FROM vu_satuan_konversi WHERE produk_aktif='Aktif'";
			
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
		
		function get_satuan_selected_list($selected_id){
			$sql="SELECT satuan_id,satuan_kode,satuan_nama FROM satuan";
			if($selected_id!=="")
			{
				$selected_id=substr($selected_id,0,strlen($selected_id)-1);
				$sql.=" WHERE satuan_id IN(".$selected_id.")";
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
		
		function get_satuan_detail_list($master_id){
			$sql="SELECT satuan_id,satuan_kode,satuan_nama FROM satuan";
			if($master_id<>"")
				$sql.=" WHERE satuan_id IN(SELECT dorder_satuan FROM detail_order_beli WHERE dorder_master='".$master_id."')";
			
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
		
		//function for detail
		//get record list
		function detail_detail_order_beli_list($master_id,$query,$start,$end) {
			$query = "SELECT detail_order_beli.dorder_id as dorder_id, detail_order_beli.dorder_master as dorder_master, detail_order_beli.dorder_produk as dorder_produk,detail_order_beli.dorder_satuan as dorder_satuan,
							detail_order_beli.dorder_jumlah as jumlah_barang, detail_order_beli.dorder_harga as harga_satuan, date_format(dorder_harga_log, '%Y-%m-%d %H:%i:%s') as dorder_harga_log,
							detail_order_beli.dorder_diskon as diskon, detail_order_beli.dorder_diskon2 as diskon2,
							(select sum(detail_terima_beli.dterima_jumlah)
											from detail_terima_beli
											left join master_terima_beli on (master_terima_beli.terima_id = detail_terima_beli.dterima_master)
											where (master_terima_beli.terima_order = master_order_beli.order_id) and (detail_order_beli.dorder_produk = detail_terima_beli.dterima_produk)
										and (detail_order_beli.dorder_satuan = detail_terima_beli.dterima_satuan) and (master_terima_beli.terima_status <> 'Batal')
											) as jumlah_terima, 
							dorder_jumlah * dorder_harga as dorder_subtotal,
							dorder_jumlah * master_order_beli.order_kurs_nilai * dorder_harga as dorder_subtotal_rupiah,
							dorder_stok_akhir, dorder_harga_rata2, dorder_tinggi, dorder_lebar, dorder_panjang, dorder_cbm, dorder_karton, dorder_bekspedisi,dorder_hpp, dorder_komisi
				FROM detail_order_beli
				LEFT JOIN master_order_beli on (master_order_beli.order_id = detail_order_beli.dorder_master)
				WHERE detail_order_beli.dorder_master = '".$master_id."'
						";

			$result = $this->db->query($query);
			$nbrows = $result->num_rows();
/*			$limit = $query." LIMIT ".$start.",".$end;			
			$result = $this->db->query($limit); */ 
			
			
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
			$query = "SELECT max(order_id) as master_id from master_order_beli";
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
		
		
	function catatan_hutang_update($order_create_id){

		$date_now=date('Y-m-d H:i:s');
		
		$sql="sELECT * FROM vu_hutang_pembelian WHERE order_id='$order_create_id'";
		$rs=$this->db->query($sql);
		if($rs->num_rows()){
			$rs_record=$rs->row_array();
			$order_no=$rs_record["order_no"];
			$order_supplier=$rs_record["order_supplier"];
			$order_tanggal=$rs_record["order_tanggal"];
			$order_bayar=$rs_record["order_bayar"];
			$order_totalbiaya=$rs_record["order_totalbiaya"];
			$hutang_total=$rs_record["hutang_total"];
			/* ini artinya: No.Faktur OP ini masih BELUM LUNAS */
			/* untuk itu, No.Faktur ini akan dimasukkan ke db.hutang sebagai daftar hutang nopi untuk dilunasi nantinya */

				/* INSERT db.hutang */
				$dti_hutang=array(
				"hutang_faktur"=>$order_no,
				"hutang_supplier"=>$order_supplier,
				"hutang_op_id"=>$order_create_id,
				"hutang_tanggal"=>$order_tanggal,
				"hutang_status"=>'Hutang',
				"hutang_total"=>$hutang_total,
				"hutang_bayar"=>0,
				"hutang_sisa"=>$hutang_total,
				"hutang_date_create"=>$date_now
				);
				$this->db->insert('hutang', $dti_hutang);
				if($this->db->affected_rows()){
					$sql_update = "UPDATE supplier
									SET supplier_hutang = (supplier_hutang + '$hutang_total')
					";

					return 1;
				}
			
		}else{
			return 0;
		}
	}
		
		/*Function untuk melakukan Save Harga saja */
		function detail_save_harga_insert($array_dorder_id, $array_dorder_harga, $array_dorder_produk){
			$query="";
		   	for($i = 0; $i < sizeof($array_dorder_produk); $i++){

				$data = array(
					"dorder_harga"=>$array_dorder_harga[$i] 
				);
					
				if($array_dorder_id[$i]==0){
					$this->db->insert('detail_order_beli', $data); 
					
					$query = $query.$this->db->insert_id();
					if($i<sizeof($array_dorder_id)-1){
						$query = $query . ",";
					} 
					
				}else{
					$query = $query.$array_dorder_id[$i];
					if($i<sizeof($array_dorder_id)-1){
						$query = $query . ",";
					} 
					$this->db->where('dorder_id', $array_dorder_id[$i]);
					$this->db->update('detail_order_beli', $data);
				}
			}
				
			return '1';
			
		}
		

		//insert detail record
		function detail_detail_order_beli_insert($array_dorder_id
                                                 ,$order_create_id
                                                 ,$array_dorder_produk
                                                 ,$array_dorder_satuan
                                                 ,$array_dorder_jumlah
                                                 ,$array_dorder_harga
                                                 ,$array_dorder_diskon
                                                 ,$array_dorder_diskon2
												 ,$array_dorder_komisi
												 ,$array_dorder_stok_akhir
												 ,$array_dorder_harga_rata2
												,$array_dorder_panjang
												,$array_dorder_lebar
												,$array_dorder_tinggi
												,$array_dorder_cbm
												,$array_dorder_karton
												,$array_dorder_bekspedisi
												,$array_dorder_hpp){
            
          if($order_create_id==0){
          	 return '0';
          }else{
            $query="";
		   	for($i = 0; $i < sizeof($array_dorder_produk); $i++){

				$data = array(
					"dorder_master"=>$order_create_id, 
					"dorder_produk"=>$array_dorder_produk[$i], 
					"dorder_satuan"=>$array_dorder_satuan[$i], 
					"dorder_jumlah"=>$array_dorder_jumlah[$i], 
					"dorder_harga"=>$array_dorder_harga[$i], 
					"dorder_diskon"=>$array_dorder_diskon[$i],
					"dorder_diskon2"=>$array_dorder_diskon2[$i],
					"dorder_komisi"=>$array_dorder_komisi[$i],
					"dorder_stok_akhir"=>$array_dorder_stok_akhir[$i],
					// "dorder_panjang"=>$array_dorder_panjang[$i], // Sementara di tutup gini dl, di set ke 0 smua, karena perhitungan kubikasi blom di pakai.. 
					"dorder_panjang"=>0,
					"dorder_lebar"=>0,
					"dorder_tinggi"=>0,
					//"dorder_cbm"=>$array_dorder_cbm[$i],
					"dorder_karton"=>0,
					"dorder_bekspedisi"=>0,
					"dorder_hpp"=>$array_dorder_hpp[$i]
					
				);
				
								
				if($array_dorder_id[$i]==0){
					$this->db->insert('detail_order_beli', $data); 
					
					$query = $query.$this->db->insert_id();
					if($i<sizeof($array_dorder_id)-1){
						$query = $query . ",";
					} 
					
				}else{
					$query = $query.$array_dorder_id[$i];
					if($i<sizeof($array_dorder_id)-1){
						$query = $query . ",";
					} 
					$this->db->where('dorder_id', $array_dorder_id[$i]);
					$this->db->update('detail_order_beli', $data);
				}
				
				// Ditutup dulu karena perhitungan HPP blom di pakai..
				//$sql = "UPDATE produk SET produk_harga = $array_dorder_hpp[$i] WHERE produk_id = $array_dorder_produk[$i] ";
				//$this->db->query($sql);

				//Ini update harga jual ke Master Produk.. harga_order = harga_jual ..>> Request dari Cintia tanggal 10 September 2013 pas kunjungan kesana, alasan memakai ini, kerja 2x kalau harus ganti harga produk lagi..
				$sql = "UPDATE produk SET produk_harga = $array_dorder_harga[$i] WHERE produk_id = $array_dorder_produk[$i] ";
				$this->db->query($sql);

			}
			
			
			
			if($query<>""){
				$sql="DELETE FROM detail_order_beli WHERE  dorder_master='".$order_create_id."' AND
						dorder_id NOT IN (".$query.")";
				$this->db->query($sql);
			}
			
			return 1;
          }
		}
		//end of function
		
		//function for get list record
		function master_order_beli_list($filter,$start,$end){
			$query = "SELECT * FROM vu_trans_order";
			
			// For simple search
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (no_bukti LIKE '%".addslashes($filter)."%' OR 
							 supplier_nama LIKE '%".addslashes($filter)."%' OR 
							 order_carabayar LIKE '%".addslashes($filter)."%' )";
			}
			
			$query.=" ORDER BY order_id DESC";
			
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
		function master_order_beli_update($order_id ,$order_no ,$order_supplier , $order_gudang, $order_tanggal ,$order_carabayar ,$order_diskon, $order_cashback ,
										  $order_biaya , $order_totalbiaya, $order_bayar ,$order_keterangan, $order_status, $order_status_acc, $cetak_order, $order_muat, $order_bongkar, $order_totalkubik, $array_dorder_id
                                                                            /*,$dorder_master*/
                                                                            ,$array_dorder_produk
                                                                            ,$array_dorder_satuan
                                                                            ,$array_dorder_jumlah
                                                                            ,$array_dorder_harga
                                                                            ,$array_dorder_diskon
                                                                            ,$array_dorder_diskon2
																			,$array_dorder_komisi
																			,$array_dorder_stok_akhir
																			,$array_dorder_harga_rata2
																			,$array_dorder_panjang
																			,$array_dorder_lebar
																			,$array_dorder_tinggi
																			,$array_dorder_cbm
																			,$array_dorder_karton
																			,$array_dorder_bekspedisi
																			,$array_dorder_hpp){
			$data = array(
				"order_id"=>$order_id, 
				"order_no"=>$order_no, 
				"order_tanggal"=>$order_tanggal, 
				"order_carabayar"=>$order_carabayar, 
				"order_keterangan"=>$order_keterangan,
				"order_status"=>$order_status,
				"order_muat"=>$order_muat,
				"order_bongkar"=>$order_bongkar,
				"order_totalkubik"=>$order_totalkubik,
				"order_totalbiaya"=>$order_totalbiaya,
				"order_status_acc"=>$order_status_acc,
				"order_update"=>$_SESSION[SESSION_USERID],
				"order_date_update"=>date('Y-m-d H:i:s')
			);
			
			if(($_SESSION[SESSION_GROUPID]==9) || ($_SESSION[SESSION_GROUPID]==1)){ 
				$data["order_diskon"]=$order_diskon;
				$data["order_cashback"]=$order_cashback;
				$data["order_biaya"]=$order_biaya; 
				$data["order_bayar"]=$order_bayar; 
			}
			
			$sql="select supplier_id from supplier where supplier_id='".$order_supplier."'";
			$query=$this->db->query($sql);
			if($query->num_rows())
				$data["order_supplier"]=$order_supplier;

			$sql="select gudang_id from gudang where gudang_id ='".$order_gudang."'";
			$query=$this->db->query($sql);
			if($query->num_rows())
				$data["order_gudang"]=$order_gudang;

				
			if($cetak_order==1){
				$data['order_status'] = 'Tertutup';
			}
				
			$this->db->where('order_id', $order_id);
			$this->db->update('master_order_beli', $data);
			
			$sql="UPDATE master_order_beli SET order_revised=0 WHERE order_id='".$order_id."' AND order_revised is NULL";
			$result = $this->db->query($sql);
			
			$sql="UPDATE master_order_beli SET order_revised=(order_revised+1) WHERE order_id='".$order_id."'";
			$result = $this->db->query($sql);

			// insert detail
			//$order_create_id = $this->db->insert_id();
			$insert_detail_id = $this->detail_detail_order_beli_insert($array_dorder_id
																			,$order_id
                                                                            ,$array_dorder_produk
                                                                            ,$array_dorder_satuan
                                                                            ,$array_dorder_jumlah
                                                                            ,$array_dorder_harga
                                                                            ,$array_dorder_diskon
                                                                            ,$array_dorder_diskon2
																			,$array_dorder_komisi
																			,$array_dorder_stok_akhir
																			,$array_dorder_harga_rata2
																			,$array_dorder_panjang
																			,$array_dorder_lebar
																			,$array_dorder_tinggi
																			,$array_dorder_cbm
																			,$array_dorder_karton
																			,$array_dorder_bekspedisi
																			,$array_dorder_hpp);
			
			if($insert_detail_id>0)
			{
				$this->catatan_hutang_update($order_id);
				return $order_id;
			}
			else
				return '0';
		}
		
		//function for create new record
		function master_order_beli_create($order_no, $order_supplier,  $order_gudang, $order_tanggal, $order_carabayar, $order_diskon, 
																	 $order_cashback, $order_biaya, $order_totalbiaya, $order_bayar, $order_keterangan, $order_status, 
																	 $order_status_acc,$cetak_order, $order_kurs_id, $order_muat, $order_bongkar, $order_totalkubik,
																	 $array_dorder_id
                                                                            //,$dorder_master
                                                                            ,$array_dorder_produk
                                                                            ,$array_dorder_satuan
                                                                            ,$array_dorder_jumlah
                                                                            ,$array_dorder_harga
                                                                            ,$array_dorder_diskon
                                                                            ,$array_dorder_diskon2
																			,$array_dorder_komisi
																			,$array_dorder_stok_akhir
																			,$array_dorder_harga_rata2
																			,$array_dorder_panjang
																			,$array_dorder_lebar
																			,$array_dorder_tinggi
																			,$array_dorder_cbm
																			,$array_dorder_karton
																			,$array_dorder_bekspedisi
																			,$array_dorder_hpp){
			
			$date_now=date('Y-m-d');
			
			//ngecek kurs nya berapa utk diinsertkan ke tbel master_order_beli
			$sql_kurs="select kurs_nilai, kurs_negara from kurs where kurs_id='".$order_kurs_id."'";
			$query_sql_kurs	= $this->db->query($sql_kurs);
			$data_kurs		= $query_sql_kurs->row();
			$kurs_nilai		= $data_kurs->kurs_nilai;
			$kurs_negara	= $data_kurs->kurs_negara;
			//		

			$order_tanggal_pattern=strtotime($order_tanggal);
			$pattern="OP/".date("ym",$order_tanggal_pattern)."-";
			$order_no=$this->m_public_function->get_kode_1('master_order_beli','order_no',$pattern,12);
			
			
			$data = array(
				"order_no"=>$order_no, 
				"order_supplier"=>$order_supplier, 
				"order_gudang"=>$order_gudang, 
				"order_tanggal"=>$order_tanggal, 
				"order_carabayar"=>$order_carabayar, 
				"order_diskon"=>$order_diskon, 
				"order_cashback"=>$order_cashback, 
				"order_biaya"=>$order_biaya, 
				"order_totalbiaya"=>$order_totalbiaya, 
				"order_bayar"=>$order_bayar, 
				"order_keterangan"=>$order_keterangan,
				//"order_status"=>$order_status,
				"order_muat"=>$order_muat,
				"order_bongkar"=>$order_bongkar,
				"order_totalkubik"=>$order_totalkubik,
				"order_status_acc"=>$order_status_acc,
				"order_kurs_nama"=>$kurs_negara,
				"order_kurs_nilai"=>$kurs_nilai,
				"order_creator"=>$_SESSION[SESSION_USERID],
				"order_date_create"=>date('Y-m-d H:i:s'),
				"order_revised"=>0
			);
			
			if($cetak_order==1){
				$data['order_status'] = 'Tertutup';
				$order_status = 'Tertutup';
				//echo 'masuk';
			}else{
				$data['order_status'] = 'Terbuka';
				$order_status = 'Terbuka';
				//echo 'mestine ngga masuk';
			}
			
			$this->db->insert('master_order_beli', $data); 
			$order_create_id = $this->db->insert_id();
			$insert_detail_id = $this->detail_detail_order_beli_insert($array_dorder_id
                                                                            ,$order_create_id
                                                                            ,$array_dorder_produk
                                                                            ,$array_dorder_satuan
                                                                            ,$array_dorder_jumlah
                                                                            ,$array_dorder_harga
                                                                            ,$array_dorder_diskon
                                                                            ,$array_dorder_diskon2
																			,$array_dorder_komisi
																			,$array_dorder_stok_akhir
																			,$array_dorder_harga_rata2
																			,$array_dorder_panjang
																			,$array_dorder_lebar
																			,$array_dorder_tinggi
																			,$array_dorder_cbm
																			,$array_dorder_karton
																			,$array_dorder_bekspedisi
																			,$array_dorder_hpp);
			//echo $insert_detail_id;
			
			if($insert_detail_id>0)
			{
				if($order_status=='Tertutup')
				{
					$this->catatan_hutang_update($order_create_id);
				}
				return $order_create_id;
			}
			else
				return '0';
		}
		
		//fcuntion for delete record
		function master_order_beli_delete($pkid){
			if(sizeof($pkid)<1){
				return '0';
			} else if (sizeof($pkid) == 1){
				$query = "DELETE FROM master_order_beli WHERE order_id = ".$pkid[0];
				$this->db->query($query);
			} else {
				$query = "DELETE FROM master_order_beli WHERE ";
				for($i = 0; $i < sizeof($pkid); $i++){
					$query = $query . "order_id= ".$pkid[$i];
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
		
		//function for advanced search record
		function master_order_beli_search($order_id,$order_no ,$order_supplier ,$order_tgl_awal, $order_tgl_akhir,
										   $order_carabayar,$order_keterangan, $order_status, $order_status_acc,
										   $start,$end){
			//full query
			$query = "SELECT * FROM vu_trans_order";
			
			if($order_no!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " no_bukti LIKE '%".$order_no."%'";
			};
			if($order_supplier!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " order_supplier = ".$order_supplier;
			};
			if($order_tgl_awal!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " date_format(tanggal,'%Y-%m-%d') >='".$order_tgl_awal."'";
			};
			if($order_tgl_akhir!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " date_format(tanggal,'%Y-%m-%d') <='".$order_tgl_akhir."'";
			};
			if($order_carabayar!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " order_carabayar LIKE '%".$order_carabayar."%'";
			};
			if($order_keterangan!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " order_keterangan LIKE '%".$order_keterangan."%'";
			};
			if($order_status!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " order_status LIKE '%".$order_status."%'";
			};
			if($order_status_acc!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " order_status_acc LIKE '%".$order_status_acc."%'";
			};
			
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
		function master_order_beli_print($order_id,$order_no ,$order_supplier ,$order_tgl_awal, 
											   $order_tgl_akhir,$order_carabayar,$order_keterangan, 
											   $order_status, $order_status_acc,$option,$filter){
			//full query
			$query = "SELECT * FROM vu_trans_order";
			
			// For simple search
			if ($option=="LIST"){
				if($filter<>""){
					$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
					$query .= " (no_bukti LIKE '%".addslashes($filter)."%' OR 
								 supplier_nama LIKE '%".addslashes($filter)."%' OR 
								 order_carabayar LIKE '%".addslashes($filter)."%' )";
				}
				
			} else if($option=='SEARCH'){
				if($order_no!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " no_bukti LIKE '%".$order_no."%'";
				};
				if($order_supplier!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_supplier = ".$order_supplier;
				};
				if($order_tgl_awal!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " date_format(tanggal,'%Y-%m-%d') >='".$order_tgl_awal."'";
				};
				if($order_tgl_akhir!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " date_format(tanggal,'%Y-%m-%d') <='".$order_tgl_akhir."'";
				};
				if($order_carabayar!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_carabayar LIKE '%".$order_carabayar."%'";
				};
				if($order_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_keterangan LIKE '%".$order_keterangan."%'";
				};
				if($order_status!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_status LIKE '%".$order_status."%'";
				};
				if($order_status_acc!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_status_acc LIKE '%".$order_status_acc."%'";
				};
				
			}
			//$this->firephp->log($query);
			
			$result = $this->db->query($query);
			
			return $result->result();
		}
		
		//function  for export to excel
		function master_order_beli_export_excel($order_id,$order_no ,$order_supplier ,$order_tgl_awal, 
											   $order_tgl_akhir,$order_carabayar,$order_keterangan, 
											   $order_status, $order_status_acc,$option,$filter){
			//full query
			/*$query = "SELECT tanggal as Tanggal, no_bukti as 'No Pesanan', supplier_nama as Supplier, jumlah_barang as 'Jumlah Item',
						total_nilai as 'Sub Total', order_diskon as 'Diskon (%)', order_cashback as 'Diskon (Rp)', order_biaya as 'Biaya (Rp)',
						total_nilai+order_biaya-order_cashback-(order_diskon*total_nilai/100) as 'Total Nilai',
						order_keterangan as 'Keterangan' FROM vu_trans_order";*/
						
			$query = "SELECT tanggal as Tanggal, no_bukti as 'No Pesanan', supplier_nama as Supplier, jumlah_barang as 'Jumlah Item',
						order_carabayar as 'Cara Bayar',
						order_keterangan as 'Keterangan' FROM vu_trans_order";
				
			if ($option=="LIST"){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (no_bukti LIKE '%".addslashes($filter)."%' OR 
							 supplier_nama LIKE '%".addslashes($filter)."%' OR 
							 order_carabayar LIKE '%".addslashes($filter)."%' )";
				$result = $this->db->query($query);
			} else if($option=='SEARCH'){
				if($order_no!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " no_bukti LIKE '%".$order_no."%'";
				};
				if($order_supplier!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_supplier = ".$order_supplier;
				};
				if($order_tgl_awal!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " date_format(tanggal,'%Y-%m-%d') >='".$order_tgl_awal."'";
				};
				if($order_tgl_akhir!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " date_format(tanggal,'%Y-%m-%d') <='".$order_tgl_akhir."'";
				};
				if($order_carabayar!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_carabayar LIKE '%".$order_carabayar."%'";
				};
				if($order_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_keterangan LIKE '%".$order_keterangan."%'";
				};
				if($order_status!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_status LIKE '%".$order_status."%'";
				};
				if($order_status_acc!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " order_status_acc LIKE '%".$order_status_acc."%'";
				};
				$result = $this->db->query($query);
			}
			return $result;
		}
		
}
?>