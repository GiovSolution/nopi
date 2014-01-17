<? /* 	
	GIOV Solution - Keep IT Simple
*/

class M_master_lunas_hutang extends Model{
		
	//constructor
	function M_master_lunas_hutang() {
		parent::Model();
	}
	
	function get_cabang(){
		$sql="SELECT info_nama FROM info";
		
		$query2=$this->db->query($sql);
		return $query2; //by isaac
	}
	
	function get_laporan($tgl_awal,$tgl_akhir,$periode,$opsi,$customer,$lunas){
		
		/*switch($group){
			case "Tanggal": $order_by=" ORDER BY tanggal";break;
			case "Customer": $order_by=" ORDER BY cust_id";break;
			default: $order_by=" ORDER BY no_bukti";break;
		}*/
		$order_by="";
		
		if($lunas=='1'){
			$query_lunas = " lpiutang_sisa = 0 AND lpiutang_stat_dok='Tertutup'";
		}else{
			$query_lunas = " lpiutang_sisa > 0 AND lpiutang_stat_dok='Terbuka'";
		}
		
		//$sql="SELECT * FROM vu_trans_piutang WHERE tanggal > '2010-07-20' AND lpiutang_sisa>0 AND lpiutang_stat_dok='Terbuka' ".$order_by;
		if($opsi=='rekap'){
			$order_by=" ORDER BY cust_id";
			if($periode=='all')
				$sql="SELECT vu_trans_piutang.*,
					sum(vu_trans_piutang.lpiutang_total) AS piutang_total,
					sum(vu_trans_piutang.lpiutang_sisa) AS piutang_sisa,
					sum(vu_trans_piutang.piutang_tunai) AS piutang_tunai,
					sum(vu_trans_piutang.piutang_card) AS piutang_card,
					sum(vu_trans_piutang.piutang_cek) AS piutang_cek,
					sum(vu_trans_piutang.piutang_transfer) AS piutang_transfer FROM vu_trans_piutang WHERE (tanggal > '2010-07-20') AND  ".$query_lunas." GROUP BY vu_trans_piutang.lpiutang_cust   ";
			else if($periode=='bulan')
				$sql="SELECT vu_trans_piutang.*,
					sum(vu_trans_piutang.lpiutang_total) AS piutang_total,
					sum(vu_trans_piutang.lpiutang_sisa) AS piutang_sisa,
					sum(vu_trans_piutang.piutang_tunai) AS piutang_tunai,
					sum(vu_trans_piutang.piutang_card) AS piutang_card,
					sum(vu_trans_piutang.piutang_cek) AS piutang_cek,
					sum(vu_trans_piutang.piutang_transfer) AS piutang_transfer FROM vu_trans_piutang WHERE (tanggal > '2010-07-20') AND ".$query_lunas ." AND date_format(tanggal,'%Y-%m')='".$tgl_awal."' ";
			else if($periode=='tanggal')
				$sql="SELECT vu_trans_piutang.*,
					sum(vu_trans_piutang.lpiutang_total) AS piutang_total,
					sum(vu_trans_piutang.lpiutang_sisa) AS piutang_sisa,
					sum(vu_trans_piutang.piutang_tunai) AS piutang_tunai,
					sum(vu_trans_piutang.piutang_card) AS piutang_card,
					sum(vu_trans_piutang.piutang_cek) AS piutang_cek,
					sum(vu_trans_piutang.piutang_transfer) AS piutang_transfer FROM vu_trans_piutang WHERE (tanggal > '2010-07-20') AND ".$query_lunas ." AND date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' AND date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' ";
		}else if($opsi=='detail'){
			$order_by=" ORDER BY cust_id, no_bukti, dpiutang_nobukti";
			if($periode=='all')
				$sql="SELECT * FROM vu_detail_lunas_piutang WHERE (tanggal > '2010-07-20') AND ".$query_lunas ." ";
			else if($periode=='bulan')
				$sql="SELECT * FROM vu_detail_lunas_piutang WHERE (tanggal > '2010-07-20') AND ".$query_lunas ." AND date_format(tanggal,'%Y-%m')='".$tgl_awal."' ";
			else if($periode=='tanggal')
				$sql="SELECT * FROM vu_detail_lunas_piutang WHERE (tanggal > '2010-07-20') AND ".$query_lunas ." AND date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' AND date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' ";
		}
		
		if(is_numeric($customer)){
			$sql.=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql.=" cust_id=".$customer;
		}
		$sql.=$order_by;
		
		$query=$this->db->query($sql);
		if($opsi=='faktur')
			return $query;
		else
			return $query->result();
	}
	
	
	function get_faktur_hutang_selected_list($fhutang_id,$query,$start,$end){
		if($fhutang_id>0){
			$sql_dpiutang="SELECT dhutang_master
				FROM detail_lunas_hutang
				LEFT JOIN master_lunas_hutang ON(dhutang_master=fhutang_id)
				LEFT JOIN hutang ON (hutang_id = dhutang_hutang)
				WHERE fhutang_id='".$fhutang_id."'";
			$rs=$this->db->query($sql_dpiutang);
			$rs_rows=$rs->num_rows();
		}
		
		$sql="SELECT lpiutang_id
				,lpiutang_faktur
				,lpiutang_cust
				,date_format(lpiutang_faktur_tanggal,'%d-%m-%Y') AS lpiutang_faktur_tanggal
				,lpiutang_total
				,lpiutang_sisa
				,lpiutang_stat_dok
			FROM master_lunas_piutang";
			
		if($rs_rows){
			$filter="";
			$sql.=eregi("WHERE",$sql)? " AND ":" WHERE ";
			foreach($rs->result() as $row_dpiutang){
				
				$filter.="OR lpiutang_id='".$row_dpiutang->dpiutang_master."' ";
			}
			$sql=$sql."(".substr($filter,2,strlen($filter)).")";
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
			
	/*function get_faktur_piutang_all_list($cust_id,$query,$start,$end){
		
		$sql="SELECT lpiutang_id
				,lpiutang_faktur
				,lpiutang_cust
				,date_format(lpiutang_faktur_tanggal,'%d-%m-%Y') AS lpiutang_faktur_tanggal
				,lpiutang_total
				,lpiutang_sisa
				,lpiutang_stat_dok
			FROM master_lunas_piutang
			WHERE lpiutang_cust='".$cust_id."' and lpiutang_stat_dok='Tertutup'";
		
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
	}*/
	
		
	function get_faktur_hutang_detail_list($supplier_id,$query,$start,$end){
		/*
		$sql="SELECT lpiutang_id
				,lpiutang_faktur
				,lpiutang_cust
				,date_format(lpiutang_faktur_tanggal,'%d-%m-%Y') AS lpiutang_faktur_tanggal
				,lpiutang_total
				,lpiutang_sisa
				,lpiutang_stat_dok
			FROM master_lunas_piutang
			WHERE lpiutang_sisa<>0 and lpiutang_stat_dok='Terbuka'";
			*/
		$sql = "SELECT *
				from hutang
				where hutang.hutang_sisa<>0 and hutang_status = 'Hutang'";
		
		if($supplier_id<>""){
			$sql.=(eregi("WHERE",$sql)?" AND ":" WHERE ");
			$sql.=" hutang_supplier='".$supplier_id."'";
		}
		
		/*if($query!==""){
			$sql.=(eregi("WHERE",$sql)?" AND ":" WHERE ")." produk_nama like '%".$query."%' OR produk_kode like '%".$query."%'";
		}*/
		
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
	function detail_fhutang_byfh_list($fpiutang_nobukti,$query,$start,$end){
		/*
		$sql = "SELECT lpiutang_id
				,lpiutang_faktur
				,lpiutang_faktur_tanggal
				,lpiutang_total
				,lpiutang_sisa
				,lpiutang_stat_dok
				,lpiutang_status
				,lpiutang_keterangan
				,dpiutang_id
				,dpiutang_nilai
				,dpiutang_keterangan
			FROM detail_lunas_piutang
				LEFT JOIN master_lunas_piutang ON(lpiutang_id=dpiutang_master)
			WHERE dpiutang_nobukti='".$fpiutang_nobukti."'";
			*/
			
		
		$sql = "SELECT detail_lunas_hutang.* , master_lunas_hutang.*, hutang.*
				from detail_lunas_hutang
				LEFT JOIN master_lunas_hutang ON (detail_lunas_hutang.dhutang_master = master_lunas_hutang.fhutang_id)
				LEFT JOIN hutang ON (detail_lunas_hutang.dhutang_hutang = hutang.hutang_id)
				WHERE detail_lunas_hutang.dhutang_master = '".$fpiutang_nobukti."'";	
		
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
	//end of function
		
		//get master id, note : not done yet
		/*function get_master_id() {
			$query = "SELECT max(order_id) as master_id from master_lunas_piutang";
			$result = $this->db->query($query);
			if($result->num_rows()){
				$data=$result->row();
				$master_id=$data->master_id;
				return $master_id;
			}else{
				return '0';
			}
		}*/
		//eof
		
		//purge all detail from master
		function detail_detail_lunas_piutang_purge($master_id){
			$sql="DELETE from detail_lunas_piutang where dorder_master='".$master_id."'";
			$result=$this->db->query($sql);
		}
		//*eof
		
	//insert detail record
	function detail_lunas_hutang_insert($fhutang_cust ,$array_dpiutang_id ,$array_lpiutang_id ,$fhutang_id ,$array_dpiutang_nilai
										 ,$fhutang_cara ,$array_dpiutang_keterangan ,$cetak_lp){
		$date_now=date('Y-m-d');
		
		$size_array = sizeof($array_lpiutang_id) - 1;
		
		for($i = 0; $i < sizeof($array_lpiutang_id); $i++){
			$dpiutang_id = $array_dpiutang_id[$i];
			$lpiutang_id = $array_lpiutang_id[$i];
			$dpiutang_nilai = $array_dpiutang_nilai[$i];
			$dpiutang_keterangan = $array_dpiutang_keterangan[$i];
			
			if($dpiutang_id==0){
				//proses insert detail
				$dti = array(
					"dhutang_hutang"=>$lpiutang_id,
					"dhutang_master"=>$fhutang_id,
					"dhutang_nilai"=>$dpiutang_nilai,
					//"dhutang_cara"=>$fhutang_cara,
					"dhutang_keterangan"=>$dpiutang_keterangan
				);
				// $this->db->query('LOCK TABLE detail_lunas_hutang WRITE');
				$this->db->insert('detail_lunas_hutang', $dti);
				// $this->db->query('UNLOCK TABLES');
			}else{
				//proses edit detail
				$dtu = array(
					"dhutang_hutang"=>$lpiutang_id,
					"dhutang_master"=>$fhutang_id,
					"dhutang_nilai"=>$dpiutang_nilai,
					//"dhutang_cara"=>$fhutang_cara,
					"dhutang_keterangan"=>$dpiutang_keterangan
				);
				// $this->db->query('LOCK TABLE detail_lunas_hutang WRITE');
				$this->db->where('dhutang_id', $dpiutang_id);
				$this->db->update('detail_lunas_hutang', $dtu);
				// $this->db->query('UNLOCK TABLES');
			}

			// Pengurangan sisa hutang disini..
			if($cetak_lp==1){
			$sqlu = "UPDATE hutang
							SET hutang_sisa = (hutang_sisa-".$dpiutang_nilai.")
							WHERE hutang_id=".$lpiutang_id;
						// $this->db->query('LOCK TABLE hutang WRITE');
						$this->db->query($sqlu);
						// $this->db->query('UNLOCK TABLES');
			return $fhutang_id;
			}
			else 
			return $fhutang_id;
			
			
			/*
			if($cetak_lp==1 && $i==$size_array){
			
				$sql = "SELECT * FROM vu_hutang_total_lunas WHERE vu_hutang_total_lunas.fhutang_cust='".$fhutang_cust."'";
				$rs = $this->db->query($sql);
				if($rs->num_rows()>0){
					foreach($rs->result() as $row){
						$sqlu = "UPDATE hutang
							SET hutang_sisa = (hutang_total-".$row->total_pelunasan.")
							WHERE hutang_id=".$row->dhutang_hutang;
						$this->db->query('LOCK TABLE hutang WRITE');
						$this->db->query($sqlu);
						$this->db->query('UNLOCK TABLES');
						
						$sqlu_status = "UPDATE hutang
							SET hutang_status = 'Lunas', hutang_stat_dok = 'Tertutup'
							WHERE (hutang_sisa=0 or hutang_sisa < 0) AND hutang_id=".$row->dhutang_hutang;
						$this->db->query('LOCK TABLE hutang WRITE');
						$this->db->query($sqlu_status);
						$this->db->query('UNLOCK TABLES');
					}
				}
		
				$sql = "SELECT fhutang_id FROM master_lunas_hutang WHERE fhutang_id='".$fhutang_id."'";
				$rs = $this->db->query($sql);
				if($rs->num_rows()>0){
					$record = $rs->row_array();
					$fhutang_id = $record['fhutang_id'];
					return $fhutang_id;
				}else{
					return 0;
				}
				
			}else if($cetak_lp<>1 && $i==$size_array){
			
				$sql = "SELECT * FROM vu_hutang_total_lunas WHERE vu_hutang_total_lunas.fhutang_cust='".$fhutang_cust."'";
				$rs = $this->db->query($sql);
				if($rs->num_rows()>0){
					foreach($rs->result() as $row){
						$sqlu = "UPDATE hutang
							SET hutang_sisa = (hutang_total-".$row->total_pelunasan.")
							WHERE hutang_id=".$row->dhutang_master;
						$this->db->query('LOCK TABLE hutang WRITE');
						$this->db->query($sqlu);
						$this->db->query('UNLOCK TABLES');
						
						$sqlu_status = "UPDATE hutang
							SET hutang_status = 'lunas', hutang_stat_dok = 'Tertutup'
							WHERE hutang_sisa=0 AND hutang_id=".$row->dhutang_master;
						$this->db->query('LOCK TABLE hutang WRITE');
						$this->db->query($sqlu_status);
						$this->db->query('UNLOCK TABLES');
					}
				}
				return 0;
			}
			*/
			
		}
		
	}
	//end of function
		
		
		//function for get list record
		function master_lunas_hutang_list($filter,$start,$end){
			/*$query = "SELECT fhutang_id
					,fpiutang_nobukti
					,fhutang_cust
					,date_format(fhutang_tanggal,'%Y-%m-%d') AS fhutang_tanggal
					,fhutang_cara
					,fhutang_bayar
					,fpiutang_stat_dok
					,cust_id
					,cust_nama
					,cust_no
					,vu_piutang_total_bycust.lpiutang_total
					,vu_piutang_total_bycust.lpiutang_sisa
				FROM master_faktur_lunas_piutang
					LEFT JOIN customer ON(cust_id=fhutang_cust)
					LEFT JOIN vu_piutang_total_bycust ON(vu_piutang_total_bycust.lpiutang_cust=master_faktur_lunas_piutang.fhutang_cust)";
					*/
			$query = "SELECT master_lunas_hutang.* , supplier.supplier_nama, supplier.supplier_id, supplier.supplier_alamat ,
						vu_hutang_total_bycust.hutang_total , vu_hutang_total_bycust.hutang_sisa
					from master_lunas_hutang
					LEFT JOIN supplier ON (master_lunas_hutang.fhutang_cust = supplier.supplier_id)
					LEFT JOIN vu_hutang_total_bycust ON(vu_hutang_total_bycust.hutang_supplier=master_lunas_hutang.fhutang_cust)
			";
			
			//$query = "SELECT ";
			// For simple search
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (fpiutang_nobukti LIKE '%".addslashes($filter)."%' OR 
							 cust_nama LIKE '%".addslashes($filter)."%' )";
			}
			
			$query.=" ORDER BY fhutang_tanggal DESC";
			
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
	function master_lunas_hutang_update($fhutang_id ,$fhutang_no ,$fhutang_cust ,$fhutang_tanggal, $fhutang_keterangan ,$fhutang_status
										,$fhutang_cara ,$fhutang_bayar
										,$fhutang_kwitansi_no ,$fhutang_kwitansi_nama
										,$fhutang_card_nama ,$fhutang_card_edc ,$fhutang_card_no
										,$fhutang_cek_nama ,$fhutang_cek_no ,$fhutang_cek_valid ,$fhutang_cek_bank
										,$fpiutang_transfer_bank ,$fpiutang_transfer_nama
										,$array_dpiutang_id ,$array_lpiutang_id ,$array_dpiutang_nilai ,$array_dpiutang_keterangan
										,$cetak_lp){
		$date_now=date('Y-m-d');
		$datetime_now=date('Y-m-d H:i:s');
		
		$jenis_transaksi = 'jual_lunas';
		
		$data = array(
			"fhutang_tanggal"=>$fhutang_tanggal,
			"fhutang_cara"=>$fhutang_cara,
			"fhutang_bayar"=>$fhutang_bayar,
			"fhutang_keterangan"=>$fhutang_keterangan,
			"fhutang_date_update"=>$datetime_now,
			"fhutang_update"=>$_SESSION[SESSION_USERID]
		);
		
		if($cetak_lp==1){
			$data['fhutang_stat_dok'] = 'Tertutup';
		}else{
			$data['fhutang_stat_dok'] = 'Terbuka';
		}
		
		$this->db->query('LOCK TABLE master_lunas_hutang WRITE');
		$this->db->where('fhutang_id' ,$fhutang_id);
		$this->db->update('master_lunas_hutang', $data);
		$rs = $this->db->affected_rows();
		$this->db->query('UNLOCK TABLES');
		if($rs>-1){
			$time_now = date('H:i:s');
			$bayar_date_create_temp = $fhutang_tanggal.' '.$time_now;
			$bayar_date_create = date('Y-m-d H:i:s', strtotime($bayar_date_create_temp));
			
			//delete all transaksi
			$sql="delete from jual_kwitansi where jkwitansi_ref='".$fhutang_no."'";
			$this->db->query($sql);
			if($this->db->affected_rows()>-1){
				$sql="delete from jual_card where jcard_ref='".$fhutang_no."'";
				$this->db->query($sql);
				if($this->db->affected_rows()>-1){
					$sql="delete from jual_cek where jcek_ref='".$fhutang_no."'";
					$this->db->query($sql);
					if($this->db->affected_rows()>-1){
						$sql="delete from jual_transfer where jtransfer_ref='".$fhutang_no."'";
						$this->db->query($sql);
						if($this->db->affected_rows()>-1){
							$sql="delete from jual_tunai where jtunai_ref='".$fhutang_no."'";
							$this->db->query($sql);
							if($this->db->affected_rows()>-1){
								if($fhutang_cara!=null || $fhutang_cara!=''){
									if($fhutang_cara=='kwitansi'){
										$result_bayar = $this->m_public_function->cara_bayar_kwitansi_insert($fhutang_kwitansi_no
																						  ,$fhutang_bayar
																						  ,$fhutang_no
																						  ,$bayar_date_create
																						  ,$jenis_transaksi
																						  ,$cetak_lp);
										
									}elseif($fhutang_cara=='card'){
										$result_bayar = $this->m_public_function->cara_bayar_card_insert($fhutang_card_nama
																					  ,$fhutang_card_edc
																					  ,$fhutang_card_no
																					  ,$fhutang_bayar
																					  ,$fhutang_no
																					  ,$bayar_date_create
																					  ,$jenis_transaksi
																					  ,$cetak_lp);
									}elseif($fhutang_cara=='cek/giro'){
										$result_bayar = $this->m_public_function->cara_bayar_cek_insert($fhutang_cek_nama
																					 ,$fhutang_cek_no
																					 ,$fhutang_cek_valid
																					 ,$fhutang_cek_bank
																					 ,$fhutang_bayar
																					 ,$fhutang_no
																					 ,$bayar_date_create
																					 ,$jenis_transaksi
																					 ,$cetak_lp);
									}elseif($fhutang_cara=='transfer'){
										$result_bayar = $this->m_public_function->cara_bayar_transfer_insert($fpiutang_transfer_bank
																						  ,$fpiutang_transfer_nama
																						  ,$fhutang_bayar
																						  ,$fhutang_no
																						  ,$bayar_date_create
																						  ,$jenis_transaksi
																						  ,$cetak_lp);
									}elseif($fhutang_cara=='tunai'){
										$result_bayar = $this->m_public_function->cara_bayar_tunai_insert($fhutang_bayar
																					   ,$fhutang_no
																					   ,$bayar_date_create
																					   ,$jenis_transaksi
																					   ,$cetak_lp);
									}
								}
							}
						}
					}
				}
			}
			
			$rs_dpiutang_insert = $this->detail_lunas_hutang_insert($fhutang_cust ,$array_dpiutang_id ,$array_lpiutang_id ,$fhutang_id ,$array_dpiutang_nilai , $fhutang_cara, $array_dpiutang_keterangan ,$cetak_lp);
			
			if($cetak_lp==1){
				return $rs_dpiutang_insert;
			}else{
				return '0';
			}
			
		}else{
			return '-1';
		}
	}
	
	//function for create new record
	function master_lunas_hutang_create($fhutang_cust ,$fhutang_tanggal, $fhutang_keterangan ,$fhutang_status
										,$fhutang_cara ,$fhutang_bayar
										,$fhutang_kwitansi_no ,$fhutang_kwitansi_nama
										,$fhutang_card_nama ,$fhutang_card_edc ,$fhutang_card_no
										,$fhutang_cek_nama ,$fhutang_cek_no ,$fhutang_cek_valid ,$fhutang_cek_bank
										,$fpiutang_transfer_bank ,$fpiutang_transfer_nama
										,$array_dpiutang_id ,$array_lpiutang_id ,$array_dpiutang_nilai ,$array_dpiutang_keterangan
										,$cetak_lp){
		$date_now=date('Y-m-d');
		
		$jenis_transaksi = 'jual_lunas_hutang';
		
		$hutang_tanggal_pattern = strtotime($fhutang_tanggal);
		$pattern="LH/".date("ym",$hutang_tanggal_pattern)."-";
		$fhutang_no=$this->m_public_function->get_kode_1('master_lunas_hutang','fhutang_nobukti',$pattern,12);
		
		$data = array(
			"fhutang_nobukti"=>$fhutang_no,
			"fhutang_cust"=>$fhutang_cust,
			"fhutang_tanggal"=>$fhutang_tanggal,
			"fhutang_cara"=>$fhutang_cara,
			"fhutang_bayar"=>$fhutang_bayar,
			"fhutang_keterangan"=>$fhutang_keterangan,
			"fhutang_creator"=>$_SESSION[SESSION_USERID]
		);
		
		if($cetak_lp==1){
			$data['fhutang_stat_dok'] = 'Tertutup';
		}else{
			$data['fhutang_stat_dok'] = 'Terbuka';
		}
		
		$this->db->query('LOCK TABLE master_lunas_hutang WRITE');
		$this->db->insert('master_lunas_hutang', $data);
		$fhutang_id = $this->db->insert_id();
		$rs = $this->db->affected_rows();
		$this->db->query('UNLOCK TABLES');
		if($rs>0){
			$time_now = date('H:i:s');
			$bayar_date_create_temp = $fhutang_tanggal.' '.$time_now;
			$bayar_date_create = date('Y-m-d H:i:s', strtotime($bayar_date_create_temp));
			
			//delete all transaksi
			$sql="delete from jual_kwitansi where jkwitansi_ref='".$fhutang_no."'";
			$this->db->query($sql);
			if($this->db->affected_rows()>-1){
				$sql="delete from jual_card where jcard_ref='".$fhutang_no."'";
				$this->db->query($sql);
				if($this->db->affected_rows()>-1){
					$sql="delete from jual_cek where jcek_ref='".$fhutang_no."'";
					$this->db->query($sql);
					if($this->db->affected_rows()>-1){
						$sql="delete from jual_transfer where jtransfer_ref='".$fhutang_no."'";
						$this->db->query($sql);
						if($this->db->affected_rows()>-1){
							$sql="delete from jual_tunai where jtunai_ref='".$fhutang_no."'";
							$this->db->query($sql);
							if($this->db->affected_rows()>-1){
								if($fhutang_cara!=null || $fhutang_cara!=''){
									if($fhutang_cara=='kwitansi'){
										$result_bayar = $this->m_public_function->cara_bayar_kwitansi_insert($fhutang_kwitansi_no
																						  ,$fhutang_bayar
																						  ,$fhutang_no
																						  ,$bayar_date_create
																						  ,$jenis_transaksi
																						  ,$cetak_lp);
										
									}elseif($fhutang_cara=='card'){
										$result_bayar = $this->m_public_function->cara_bayar_card_insert($fhutang_card_nama
																					  ,$fhutang_card_edc
																					  ,$fhutang_card_no
																					  ,$fhutang_bayar
																					  ,$fhutang_no
																					  ,$bayar_date_create
																					  ,$jenis_transaksi
																					  ,$cetak_lp);
									}elseif($fhutang_cara=='cek/giro'){
										$result_bayar = $this->m_public_function->cara_bayar_cek_insert($fhutang_cek_nama
																					 ,$fhutang_cek_no
																					 ,$fhutang_cek_valid
																					 ,$fhutang_cek_bank
																					 ,$fhutang_bayar
																					 ,$fhutang_no
																					 ,$bayar_date_create
																					 ,$jenis_transaksi
																					 ,$cetak_lp);
									}elseif($fhutang_cara=='transfer'){
										$result_bayar = $this->m_public_function->cara_bayar_transfer_insert($fpiutang_transfer_bank
																						  ,$fpiutang_transfer_nama
																						  ,$fhutang_bayar
																						  ,$fhutang_no
																						  ,$bayar_date_create
																						  ,$jenis_transaksi
																						  ,$cetak_lp);
									}elseif($fhutang_cara=='tunai'){
										$result_bayar = $this->m_public_function->cara_bayar_tunai_insert($fhutang_bayar
																					   ,$fhutang_no
																					   ,$bayar_date_create
																					   ,$jenis_transaksi
																					   ,$cetak_lp);
									}
								}
							}
						}
					}
				}
			}
			
			$rs_dpiutang_insert = $this->detail_lunas_hutang_insert($fhutang_cust ,$array_dpiutang_id ,$array_lpiutang_id ,$fhutang_id
																	 ,$array_dpiutang_nilai ,$fhutang_cara ,$array_dpiutang_keterangan ,$cetak_lp);
			
			if($cetak_lp==1){
				return $rs_dpiutang_insert;
			}else{
				return '0';
			}
			
		}else{
			return '-1';
		}
	}
		
		//fcuntion for delete record
		function master_lunas_piutang_delete($pkid){
			if(sizeof($pkid)<1){
				return '0';
			} else if (sizeof($pkid) == 1){
				$query = "DELETE FROM master_lunas_piutang WHERE order_id = ".$pkid[0];
				$this->db->query($query);
			} else {
				$query = "DELETE FROM master_lunas_piutang WHERE ";
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
	
	function print_paper($fhutang_id){
		$sql="SELECT fhutang_tanggal
				,cust_no
				,cust_nama
				,cust_alamat
				,fpiutang_nobukti
				,lpiutang_faktur
				,dpiutang_nilai
			FROM detail_lunas_piutang
				LEFT JOIN master_lunas_piutang ON(lpiutang_id=dpiutang_master)
				LEFT JOIN master_faktur_lunas_piutang ON(fpiutang_nobukti=dpiutang_nobukti)
				LEFT JOIN customer ON(cust_id=fhutang_cust)
			WHERE fhutang_id='".$fhutang_id."'
			ORDER BY lpiutang_faktur ASC";
		$result = $this->db->query($sql);
		return $result;
	}
	
	function cara_bayar($fhutang_id){
		$sql="SELECT fpiutang_nobukti, fhutang_cara FROM master_faktur_lunas_piutang WHERE fhutang_id='".$fhutang_id."'";
		$rs=$this->db->query($sql);
		if($rs->num_rows()){
			$record=$rs->row();
			$fpiutang_nobukti = $record->fpiutang_nobukti;
			if(($record->fhutang_cara !== NULL || $record->fhutang_cara !== '')){
				if($record->fhutang_cara == 'tunai'){
					$sql = "SELECT jtunai_id FROM jual_tunai WHERE jtunai_ref='".$fpiutang_nobukti."'";
					$rs = $this->db->query($sql);
					
					$sql="SELECT fpiutang_nobukti, fhutang_cara, jtunai_nilai AS bayar_nilai
						FROM master_faktur_lunas_piutang
							LEFT JOIN jual_tunai ON(jtunai_ref=fpiutang_nobukti)
						WHERE fhutang_id='".$fhutang_id."' LIMIT 0,1";
					$rs=$this->db->query($sql);
					if($rs->num_rows()){
						return $rs->row();
					}else{
						return NULL;
					}
				}elseif($record->fhutang_cara == 'kwitansi'){
					$sql = "SELECT jkwitansi_id FROM jual_kwitansi WHERE jkwitansi_ref='".$fpiutang_nobukti."'";
					$rs = $this->db->query($sql);
					
					$sql="SELECT fpiutang_nobukti, fhutang_cara, jkwitansi_nilai AS bayar_nilai
						FROM master_faktur_lunas_piutang
							LEFT JOIN jual_kwitansi ON(jkwitansi_ref=fpiutang_nobukti)
						WHERE fhutang_id='".$fhutang_id."' LIMIT 0,1";
					$rs=$this->db->query($sql);
					if($rs->num_rows()){
						return $rs->row();
					}else{
						return NULL;
					}
				}elseif($record->fhutang_cara == 'card'){
					$sql = "SELECT jcard_id FROM jual_card WHERE jcard_ref='".$fpiutang_nobukti."'";
					$rs = $this->db->query($sql);
					
					$sql="SELECT fpiutang_nobukti, fhutang_cara, jcard_nilai AS bayar_nilai
						FROM master_faktur_lunas_piutang
							LEFT JOIN jual_card ON(jcard_ref=fpiutang_nobukti)
						WHERE fhutang_id='".$fhutang_id."' LIMIT 0,1";
					$rs=$this->db->query($sql);
					if($rs->num_rows()){
						return $rs->row();
					}else{
						return NULL;
					}
				}elseif($record->fhutang_cara == 'cek/giro'){
					$sql = "SELECT jcek_id FROM jual_cek WHERE jcek_ref='".$fpiutang_nobukti."'";
					$rs = $this->db->query($sql);
					
					$sql="SELECT fpiutang_nobukti, fhutang_cara, jcek_nilai AS bayar_nilai
						FROM master_faktur_lunas_piutang
							LEFT JOIN jual_cek ON(jcek_ref=fpiutang_nobukti)
						WHERE fhutang_id='".$fhutang_id."' LIMIT 0,1";
					$rs=$this->db->query($sql);
					if($rs->num_rows()){
						return $rs->row();
					}else{
						return NULL;
					}
				}elseif($record->fhutang_cara == 'transfer'){
					$sql = "SELECT jtransfer_id FROM jual_transfer WHERE jtransfer_ref='".$fpiutang_nobukti."'";
					$rs = $this->db->query($sql);
					
					$sql="SELECT fpiutang_nobukti, fhutang_cara, jtransfer_nilai AS bayar_nilai
						FROM master_faktur_lunas_piutang
							LEFT JOIN jual_transfer ON(jtransfer_ref=fpiutang_nobukti)
						WHERE fhutang_id='".$fhutang_id."' LIMIT 0,1";
					$rs=$this->db->query($sql);
					if($rs->num_rows()){
						return $rs->row();
					}else{
						return NULL;
					}
				}
			}else{
				return NULL;
			}
		}else{
			return NULL;
		}
	}
	
	function master_lunas_hutang_batal($fhutang_id, $fhutang_tanggal){
		$date = date('Y-m-d');
		$date_1 = '01';
		$date_2 = '02';
		$date_3 = '03';
		$month = substr($fhutang_tanggal,5,2);
		$year = substr($fhutang_tanggal,0,4);
		$begin=mktime(0,0,0,$month,1,$year);
		$nextmonth=strtotime("+3month",$begin);
		
		$month_next = substr(date("Y-m-d",$nextmonth),5,2);
		$year_next = substr(date("Y-m-d",$nextmonth),0,4);
		
		$tanggal_1 = $year_next.'-'.$month_next.'-'.$date_1;
		$tanggal_2 = $year_next.'-'.$month_next.'-'.$date_2;
		$tanggal_3 = $year_next.'-'.$month_next.'-'.$date_3;
		$datetime_now = date('Y-m-d H:i:s');
		$sql = "UPDATE master_faktur_lunas_piutang
			SET fpiutang_stat_dok='Batal'
				,fpiutang_update='".@$_SESSION[SESSION_USERID]."'
				,fpiutang_date_update='".$datetime_now."'
				,fpiutang_revised=fpiutang_revised+1
			WHERE fhutang_id='".$fhutang_id."' " ;
		$this->db->query('LOCK TABLE master_faktur_lunas_piutang WRITE');
		$this->db->query($sql);
		$rs = $this->db->affected_rows();
		$this->db->query('UNLOCK TABLES');
		if($rs>0){
			/*update db.master_lunas_piutang.lpiutang_sisa*/
			$sql = "SELECT dpiutang_master, fpiutang_nobukti, fhutang_cara
				FROM detail_lunas_piutang
					LEFT JOIN master_faktur_lunas_piutang ON(fpiutang_nobukti=dpiutang_nobukti)
				WHERE fhutang_id='".$fhutang_id."'";
			$rs = $this->db->query($sql);
			if($rs->num_rows()>0){
				$record = $rs->row_array();
				$fpiutang_nobukti = $record['fpiutang_nobukti'];
				$fhutang_cara = $record['fhutang_cara'];
				
				if($fhutang_cara=='card'){
					$sql = "UPDATE jual_card
						SET jcard_stat_dok='Batal'
						WHERE jcard_ref='".$fpiutang_nobukti."'";
					$this->db->query($sql);
				}elseif($fhutang_cara=='cek/giro'){
					$sql = "UPDATE jual_cek
						SET jcek_stat_dok='Batal'
						WHERE jcek_ref='".$fpiutang_nobukti."'";
					$this->db->query($sql);
				}elseif($fhutang_cara=='transfer'){
					$sql = "UPDATE jual_transfer
						SET jtransfer_stat_dok='Batal'
						WHERE jtransfer_ref='".$fpiutang_nobukti."'";
					$this->db->query($sql);
				}elseif($fhutang_cara=='tunai'){
					$sql = "UPDATE jual_tunai
						SET jtunai_stat_dok='Batal'
						WHERE jtunai_ref='".$fpiutang_nobukti."'";
					$this->db->query($sql);
				}
				
				foreach($rs->result() as $row){
					$sqlu = "UPDATE master_lunas_piutang
							LEFT JOIN vu_piutang_total_lunas ON(vu_piutang_total_lunas.dpiutang_master=master_lunas_piutang.lpiutang_id)
						SET lpiutang_sisa = (lpiutang_total-(ifnull(vu_piutang_total_lunas.total_pelunasan,0))), lpiutang_stat_dok = 'Terbuka', lpiutang_status = 'piutang'
						WHERE lpiutang_id=".$row->dpiutang_master;
					$this->db->query('LOCK TABLE master_lunas_piutang WRITE, vu_piutang_total_lunas WRITE');
					$this->db->query($sqlu);
					$this->db->query('UNLOCK TABLES');
				}
			}
			return '1';
			
		}else{
			return '0';
		}
	}
}
?>