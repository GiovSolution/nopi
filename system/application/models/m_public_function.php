<?

class M_public_function extends Model{
	
	function M_public_function(){
		parent::Model();
	}
	
	function get_info_pajak(){
		$sql="SELECT cabang_pajak FROM cabang where cabang_id = (select info_cabang from info) and cabang_pajak = '1'";
		
		$result = $this->db->query($sql);		
		$nbrows = $result->num_rows();
		return $nbrows;
	}
	
	
	function add_date($time='',$value=0,$interval='day'){
		$time=mysql_to_unix($time);
		$year=date("Y",$time);
		$month=date("m",$time);
		$day=date("d",$time);
		$in_date=$year."-".$month."-".$day;
		$sql="SELECT DATE_ADD('$in_date', INTERVAL $value $interval) as tanggal ";
		$result=$this->db->query($sql);
		if($result->num_rows()){
			$data=$result->row();
			return $data->tanggal;
		}else{
			return date('Y/m/d');
		}
		
	}

	
	function get_gudang_nama($gudang_id){
		$sql="SELECT gudang_nama FROM gudang WHERE gudang_id='".$gudang_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows())
		{
			$row=$query->row();
			return $row->gudang_nama;
		}else
			return 0;
	}
	
	function get_produk_nama($produk_id){
		$sql="SELECT produk_nama FROM produk WHERE produk_id='".$produk_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows())
		{
			$row=$query->row();
			return $row->produk_nama;
		}else
			return 0;
	}
	
	function get_satuan_nama($satuan_id){
		$sql="SELECT satuan_nama FROM satuan WHERE satuan_id='".$satuan_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows())
		{
			$row=$query->row();
			return $row->satuan_nama;
		}else
			return 0;
	}
	
	function get_perawatan_nama($satuan_id){
		$sql="SELECT rawat_nama FROM rawat WHERE rawat_id='".$rawat_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows())
		{
			$row=$query->row();
			return $row->rawat_nama;
		}else
			return 0;
	}
				
	function get_laporan_terima_kas($tgl_awal,$tgl_akhir,$periode,$opsi, $cabang){
			$this->cabang = $this->load->database($cabang, TRUE);
			
			$sql="";
			if($periode=='bulan')
				$sql=	   "SELECT 		
								case jenis_transaksi
									when 'jual_kwitansi' then 'Penjualan Kuitansi'
									when 'jual_lunas' then 'Pelunasan Piutang'
									when 'jual_paket' then 'Penjualan Paket'
									when 'jual_produk' then 'Penjualan Produk'
									when 'jual_rawat' then 'Penjualan Perawatan'
								end as jenis_transaksi,
								case jenis_transaksi
									when 'jual_kwitansi' then 4
									when 'jual_lunas' then 5
									when 'jual_paket' then 3
									when 'jual_produk' then 1
									when 'jual_rawat' then 2
								end as urutan,
								sum(nilai_card) as nilai_card,
								sum(nilai_cek) as nilai_cek, 
								sum(nilai_kredit) as nilai_kredit, 
								sum(nilai_kwitansi) as nilai_kwitansi, 
								sum(nilai_transfer) as nilai_transfer,
								sum(nilai_tunai) as nilai_tunai,
								sum(nilai_voucher) as nilai_voucher,
								sum(nilai_card+nilai_cek+nilai_kwitansi+nilai_transfer+nilai_tunai) as nilai_total
							FROM vu_trans_terima_jual 
							WHERE date_format(tanggal,'%Y-%m')='".$tgl_awal."' 
								AND stat_dok='Tertutup'
								AND no_ref<>''
							GROUP BY jenis_transaksi 
							ORDER BY urutan";
			else if($periode=='tanggal')
				$sql="SELECT 
								case jenis_transaksi
									when 'jual_kwitansi' then 'Penjualan Kuitansi'
									when 'jual_lunas' then 'Pelunasan Piutang'
									when 'jual_paket' then 'Penjualan Paket'
									when 'jual_produk' then 'Penjualan Produk'
									when 'jual_rawat' then 'Penjualan Perawatan'
								end as jenis_transaksi,
								case jenis_transaksi
									when 'jual_kwitansi' then 4
									when 'jual_lunas' then 5
									when 'jual_paket' then 3
									when 'jual_produk' then 1
									when 'jual_rawat' then 2
								end as urutan,
								sum(nilai_card) as nilai_card,
								sum(nilai_cek) as nilai_cek, 
								sum(nilai_kredit) as nilai_kredit, 
								sum(nilai_kwitansi) as nilai_kwitansi, 
								sum(nilai_transfer) as nilai_transfer,
								sum(nilai_tunai) as nilai_tunai,
								sum(nilai_voucher) as nilai_voucher,
								sum(nilai_card+nilai_cek+nilai_kwitansi+nilai_transfer+nilai_tunai) as nilai_total
					FROM 		vu_trans_terima_jual 
					WHERE 		date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' AND 
								date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' AND 
								stat_dok='Tertutup' 
					GROUP BY  	jenis_transaksi 
					ORDER BY 	urutan";

			//echo $sql;
			$query = $this->cabang->query($sql);
			
			if ($opsi == 'columnmodel') {
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
			else {			
				return $query->result();
			}			
	}
	
	function get_laporan_terima_kas_total($tgl_awal,$tgl_akhir,$periode, $cabang){
			$this->cabang = $this->load->database($cabang, TRUE);
			
			$sql="";
			if($periode=='bulan')
				$sql=	   "SELECT 									
								sum(nilai_card) as nilai_card_total,
								sum(nilai_cek) as nilai_cek_total, 
								sum(nilai_kredit) as nilai_kredit_total, 
								sum(nilai_kwitansi) as nilai_kwitansi_total, 
								sum(nilai_transfer) as nilai_transfer_total,
								sum(nilai_tunai) as nilai_tunai_total,
								sum(nilai_voucher) as nilai_voucher_total,
								sum(nilai_card+nilai_cek+nilai_kwitansi+nilai_transfer+nilai_tunai) as nilai_grand_total
					FROM 		vu_trans_terima_jual 
					WHERE 		date_format(tanggal,'%Y-%m')='".$tgl_awal."' 
								AND stat_dok='Tertutup'
								AND no_ref<>''";
			else if($periode=='tanggal')
				$sql=	   "SELECT 				
								sum(nilai_card) as nilai_card_total,
								sum(nilai_cek) as nilai_cek_total, 
								sum(nilai_kredit) as nilai_kredit_total, 
								sum(nilai_kwitansi) as nilai_kwitansi_total, 
								sum(nilai_transfer) as nilai_transfer_total,
								sum(nilai_tunai) as nilai_tunai_total,
								sum(nilai_voucher) as nilai_voucher_total,
								sum(nilai_card+nilai_cek+nilai_kwitansi+nilai_transfer+nilai_tunai) as nilai_grand_total
							FROM vu_trans_terima_jual 
							WHERE date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."' AND 
								date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."' AND 
								stat_dok='Tertutup'";

			//echo $sql;
			$query = $this->cabang->query($sql);
						
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

	function get_laporan_terima_kas_target($tgl_awal, $tgl_akhir, $periode, $cabang){
			$sql="";
			
			/*if($periode=='bulan')
				$sql = "SELECT 1 as tt_rp";
			else if($periode=='tanggal')
			{	
			*/
				$sql = "SELECT 				
							tt_rp
						FROM temp_target
						WHERE /*date_format(tt_tgl_awal,'%Y-%m-%d') = '".$tgl_awal."' 
							AND date_format(tt_tgl_akhir,'%Y-%m-%d') ='".$tgl_akhir."'
							AND*/ tt_cabang_kode = '".$cabang."'"; 
			
			//}


			//echo $sql;
			$query = $this->db->query($sql);
			/*
			foreach($query->result() as $row){
					$arr[] = $row;
				}
			return $arr; */
						
			$nbrows = $query->num_rows();
			
			if($nbrows>0){
				foreach($query->result() as $row){
					$arr[] = $row;
				}
				$jsonresult = json_encode($arr);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				
				return 1;//'({"total":"0", "results":""})';
			}						
	}

	
	function get_order_beli_detail_by_order_id($orderid){
		$sql="SELECT detail_order_beli.dorder_produk as dorder_produk,detail_order_beli.dorder_satuan as dorder_satuan,
							detail_order_beli.dorder_jumlah as jumlah_order,
							IFNULL((dorder_jumlah - (select sum(detail_terima_beli.dterima_jumlah)
											from detail_terima_beli
											left join master_terima_beli on (master_terima_beli.terima_id = detail_terima_beli.dterima_master)
											where (master_terima_beli.terima_order = master_order_beli.order_id) and (detail_order_beli.dorder_produk = detail_terima_beli.dterima_produk)
										and (detail_order_beli.dorder_satuan = detail_terima_beli.dterima_satuan) and (master_terima_beli.terima_status <> 'Batal')
											)
							),detail_order_beli.dorder_jumlah)as dterima_jumlah,
							IFNULL((dorder_jumlah - (select sum(detail_terima_beli.dterima_jumlah)
											from detail_terima_beli
											left join master_terima_beli on (master_terima_beli.terima_id = detail_terima_beli.dterima_master)
											where (master_terima_beli.terima_order = master_order_beli.order_id) and (detail_order_beli.dorder_produk = detail_terima_beli.dterima_produk)
										and (detail_order_beli.dorder_satuan = detail_terima_beli.dterima_satuan) and (master_terima_beli.terima_status <> 'Batal')
											)
						),detail_order_beli.dorder_jumlah) as jumlah_sisa
				FROM detail_order_beli
				LEFT JOIN master_order_beli on (master_order_beli.order_id = detail_order_beli.dorder_master)
				WHERE detail_order_beli.dorder_master = '".$orderid."'
				group by dorder_produk, dorder_satuan";
		
		/*
		$sql="SELECT produk as dorder_produk,satuan as dorder_satuan,sum(jumlah_order) as jumlah_order, sum(jumlah_terima) as jumlah_terima, sum(jumlah_sisa) as jumlah_sisa
				FROM vu_detail_terima_order WHERE master_order='".$orderid."'
				GROUP BY produk,satuan";
		*/
				
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
	
	function get_bank_list(){
		$sql="SELECT * FROM bank_master WHERE mbank_aktif='Aktif'";
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
	
	function get_group1_list($query,$start,$end){
		$sql="SELECT * FROM produk_group WHERE group_aktif='Aktif'";

		if($query<>""){
			$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql .= " (group_nama LIKE '%".addslashes($query)."%' OR group_kode LIKE '%".addslashes($query)."%')";
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
	
	function get_petugas_list($query, $tgl_app="", $karyawan_jabatan){
		//$sql="SELECT karyawan_id,karyawan_no,karyawan_nama FROM vu_karyawan WHERE karyawan_departemen='$departemen_id' AND karyawan_aktif='Aktif'";
/*		if($rawat_kategori==2)
			$departemen_id=8;
		elseif($rawat_kategori==3)
			$departemen_id=9;
		else
			$departemen_id=0;*/
		//$sql="SELECT karyawan_id,karyawan_no,karyawan_nama,karyawan_username,reportt_jmltindakan FROM vu_karyawan INNER JOIN jabatan ON(karyawan_jabatan=jabatan_id) INNER JOIN absensi ON(karyawan_no=absensi_nik) LEFT JOIN report_tindakan ON(karyawan_no=reportt_nik) WHERE karyawan_jabatan=jabatan_id AND karyawan_no=absensi_nik AND absensi_shift!='OFF' AND jabatan_nama='$karyawan_jabatan' AND karyawan_aktif='Aktif'";
		$bln_now=date('Y-m');
		$sql=  "SELECT karyawan_id,karyawan_no,karyawan_nama, karyawan_sip,karyawan_username,reportt_jmltindakan FROM vu_karyawan INNER JOIN jabatan ON(karyawan_jabatan=jabatan_id) LEFT JOIN (SELECT * FROM report_tindakan WHERE reportt_bln LIKE '$bln_now%') as rt ON(karyawan_id=rt.reportt_karyawan_id) 
				left join cabang on(vu_karyawan.karyawan_cabang=cabang.cabang_id)
				WHERE karyawan_jabatan=jabatan_id AND jabatan_nama='$karyawan_jabatan' AND karyawan_aktif='Aktif'
					AND (karyawan_cabang = (SELECT info_cabang FROM info limit 1) 
					OR substring(karyawan_cabang2,
					(select cabang_value 
						from cabang
						left join info on (cabang.cabang_id = info.info_cabang)
						where info.info_cabang = cabang.cabang_id)
					,1) = '1')";
		if($query<>""){
			$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql .= " (karyawan_nama LIKE '%".addslashes($query)."%')";
		}
		if($tgl_app<>""){
			$tgl_app = date('Y-m-d', strtotime($tgl_app));
			$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql .= " (absensi_tgl='".addslashes($tgl_app)."')";
		}
		//echo $sql;
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
	
	function get_jenis_perawatan_list(){
		$sql="SELECT jenis_id,jenis_nama FROM jenis WHERE jenis_kelompok='perawatan' AND jenis_aktif='Aktif'";
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
	
	function get_jenis_produk_list(){
		$sql="SELECT jenis_id,jenis_nama FROM jenis WHERE jenis_kelompok='produk' AND jenis_aktif='Aktif'";
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
	
	function get_usergroups_list(){
		$sql="SELECT group_id,group_name FROM usergroups WHERE group_active='Aktif'";
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
	
	function get_mbank_list(){
		$sql="SELECT mbank_id,mbank_nama FROM bank_master WHERE mbank_aktif='Aktif'";
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
	
	function get_propinsi_list(){
		$sql="SELECT propinsi_nama FROM propinsi";
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
	
	function get_info(){
		$sql="select * from info";
		$query = $this->db->query($sql);
		return $query->row();
	}
	
	function get_akun_list($start,$end){
		$sql="SELECT akun_id,akun_nama,akun_kode FROM akun where akun_aktif='Aktif'";
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
	
	function get_voucher_list($query,$start,$end){
			$query = "SELECT * FROM voucher LEFT JOIN voucher_kupon ON(kvoucher_master=voucher_id)";
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
	
	function get_evoucher_list($query,$start,$end){
			$query = "SELECT voucher_id, voucher_jenis, voucher_nama, voucher_point, voucher_kadaluarsa, voucher_cashback, voucher_mincash, voucher_diskon, voucher_promo, voucher_allproduk, voucher_allrawat FROM voucher LEFT JOIN voucher_kupon ON(kvoucher_master=voucher_id) WHERE voucher_jenis='reward' AND kvoucher_cust=0 GROUP BY voucher_id";
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
		
	function get_kwitansi_by_ref($ref_id ,$cara_bayar_ke){
		$sql = "SELECT jkwitansi_id FROM jual_kwitansi WHERE jkwitansi_ref='".$ref_id."'";
		$rs = $this->db->query($sql);
		$nbrows = $rs->num_rows();
		
		if($cara_bayar_ke==''){
			$cara_bayar_ke = 1;
		}
		
		if($cara_bayar_ke==1){
			if($nbrows>1){
				$sql="SELECT jkwitansi_id
						,kwitansi_no
						,jkwitansi_nilai
						,cust_nama
						,kwitansi_sisa
						,kwitansi_id
					FROM jual_kwitansi
					LEFT JOIN cetak_kwitansi ON(jkwitansi_master=kwitansi_id)
					LEFT JOIN customer ON(kwitansi_cust=cust_id)
					WHERE jkwitansi_ref='".$ref_id."'
					LIMIT 0,1";
			}else{
				$sql="SELECT jkwitansi_id
						,kwitansi_no
						,jkwitansi_nilai
						,cust_nama
						,kwitansi_sisa
						,kwitansi_id
					FROM jual_kwitansi
					LEFT JOIN cetak_kwitansi ON(jkwitansi_master=kwitansi_id)
					LEFT JOIN customer ON(kwitansi_cust=cust_id)
					WHERE jkwitansi_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==2){
			if($nbrows>1){
				$sql="SELECT jkwitansi_id
						,kwitansi_no
						,jkwitansi_nilai
						,cust_nama
						,kwitansi_sisa
						,kwitansi_id
					FROM jual_kwitansi
					LEFT JOIN cetak_kwitansi ON(jkwitansi_master=kwitansi_id)
					LEFT JOIN customer ON(kwitansi_cust=cust_id)
					WHERE jkwitansi_ref='".$ref_id."'
					LIMIT 1,1";
			}else{
				$sql="SELECT jkwitansi_id
						,kwitansi_no
						,jkwitansi_nilai
						,cust_nama
						,kwitansi_sisa
						,kwitansi_id
					FROM jual_kwitansi
					LEFT JOIN cetak_kwitansi ON(jkwitansi_master=kwitansi_id)
					LEFT JOIN customer ON(kwitansi_cust=cust_id)
					WHERE jkwitansi_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==3){
			if($nbrows==2){
				$sql="SELECT jkwitansi_id
						,kwitansi_no
						,jkwitansi_nilai
						,cust_nama
						,kwitansi_sisa
						,kwitansi_id
					FROM jual_kwitansi
					LEFT JOIN cetak_kwitansi ON(jkwitansi_master=kwitansi_id)
					LEFT JOIN customer ON(kwitansi_cust=cust_id)
					WHERE jkwitansi_ref='".$ref_id."'
					LIMIT 1,1";
			}elseif($nbrows==3){
				$sql="SELECT jkwitansi_id
						,kwitansi_no
						,jkwitansi_nilai
						,cust_nama
						,kwitansi_sisa
						,kwitansi_id
					FROM jual_kwitansi
					LEFT JOIN cetak_kwitansi ON(jkwitansi_master=kwitansi_id)
					LEFT JOIN customer ON(kwitansi_cust=cust_id)
					WHERE jkwitansi_ref='".$ref_id."'
					LIMIT 2,1";
			}else{
				$sql="SELECT jkwitansi_id
						,kwitansi_no
						,jkwitansi_nilai
						,cust_nama
						,kwitansi_sisa
						,kwitansi_id
					FROM jual_kwitansi
					LEFT JOIN cetak_kwitansi ON(jkwitansi_master=kwitansi_id)
					LEFT JOIN customer ON(kwitansi_cust=cust_id)
					WHERE jkwitansi_ref='".$ref_id."'";
			}
		}
		
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
	
	function get_card_by_ref($ref_id ,$cara_bayar_ke){
		$sql = "SELECT jcard_id FROM jual_card WHERE jcard_ref='".$ref_id."'";
		$rs = $this->db->query($sql);
		$nbrows = $rs->num_rows();
		
		if($cara_bayar_ke==''){
			$cara_bayar_ke = 1;
		}
		
		if($cara_bayar_ke==1){
			if($nbrows>1){
				$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."' LIMIT 0,1";
			}else{
				$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==2){
			if($nbrows>1){
				$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."' LIMIT 1,1";
			}else{
				$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==3){
			if($nbrows==2){
				$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."' LIMIT 1,1";
			}elseif($nbrows==3){
				$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."' LIMIT 2,1";
			}else{
				$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."'";
			}
		}
		//$sql="SELECT jcard_id,jcard_no,jcard_nama,jcard_edc,jcard_nilai FROM jual_card where jcard_ref='".$ref_id."'";
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
	
	function get_cek_by_ref($ref_id ,$cara_bayar_ke){
		$sql="SELECT jcek_id FROM jual_cek WHERE jcek_ref='".$ref_id."'";
		$rs = $this->db->query($sql);
		$nbrows = $rs->num_rows();
		
		if($cara_bayar_ke==''){
			$cara_bayar_ke = 1;
		}
		
		if($cara_bayar_ke==1){
			if($nbrows>1){
				$sql="SELECT jcek_id,jcek_nama,jcek_no,jcek_valid,jcek_bank,jcek_nilai FROM jual_cek WHERE jcek_ref='".$ref_id."' LIMIT 0,1";
			}else{
				$sql="SELECT jcek_id,jcek_nama,jcek_no,jcek_valid,jcek_bank,jcek_nilai FROM jual_cek WHERE jcek_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==2){
			if($nbrows>1){
				$sql="SELECT jcek_id,jcek_nama,jcek_no,jcek_valid,jcek_bank,jcek_nilai FROM jual_cek WHERE jcek_ref='".$ref_id."' LIMIT 1,1";
			}else{
				$sql="SELECT jcek_id,jcek_nama,jcek_no,jcek_valid,jcek_bank,jcek_nilai FROM jual_cek WHERE jcek_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==3){
			if($nbrows==2){
				$sql="SELECT jcek_id,jcek_nama,jcek_no,jcek_valid,jcek_bank,jcek_nilai FROM jual_cek WHERE jcek_ref='".$ref_id."' LIMIT 1,1";
			}elseif($nbrows==3){
				$sql="SELECT jcek_id,jcek_nama,jcek_no,jcek_valid,jcek_bank,jcek_nilai FROM jual_cek WHERE jcek_ref='".$ref_id."' LIMIT 2,1";
			}else{
				$sql="SELECT jcek_id,jcek_nama,jcek_no,jcek_valid,jcek_bank,jcek_nilai FROM jual_cek WHERE jcek_ref='".$ref_id."'";
			}
		}
		
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
	
	function get_transfer_by_ref($ref_id ,$cara_bayar_ke){
		$sql="SELECT jtransfer_id FROM jual_transfer WHERE jtransfer_ref='".$ref_id."'";
		$rs = $this->db->query($sql);
		$nbrows = $rs->num_rows();
		
		if($cara_bayar_ke==''){
			$cara_bayar_ke = 1;
		}
		
		if($cara_bayar_ke==1){
			if($nbrows>1){
				$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nama,jtransfer_nilai FROM jual_transfer WHERE jtransfer_ref='".$ref_id."' LIMIT 0,1";
			}else{
				$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nama,jtransfer_nilai FROM jual_transfer WHERE jtransfer_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==2){
			if($nbrows>1){
				$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nama,jtransfer_nilai FROM jual_transfer WHERE jtransfer_ref='".$ref_id."' LIMIT 1,1";
			}else{
				$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nama,jtransfer_nilai FROM jual_transfer WHERE jtransfer_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==3){
			if($nbrows==2){
				$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nama,jtransfer_nilai FROM jual_transfer WHERE jtransfer_ref='".$ref_id."' LIMIT 1,1";
			}elseif($nbrows==3){
				$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nama,jtransfer_nilai FROM jual_transfer WHERE jtransfer_ref='".$ref_id."' LIMIT 2,1";
			}else{
				$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nama,jtransfer_nilai FROM jual_transfer WHERE jtransfer_ref='".$ref_id."'";
			}
		}
		
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
	
	function get_tunai_by_ref($ref_id ,$cara_bayar_ke){
		$sql="SELECT jtunai_id FROM jual_tunai WHERE jtunai_ref='".$ref_id."'";
		$rs = $this->db->query($sql);
		$nbrows = $rs->num_rows();
		
		if($cara_bayar_ke==''){
			$cara_bayar_ke = 1;
		}
		
		if($cara_bayar_ke==1){
			if($nbrows>1){
				$sql="SELECT jtunai_id,jtunai_nilai FROM jual_tunai WHERE jtunai_ref='".$ref_id."' LIMIT 0,1";
			}else{
				$sql="SELECT jtunai_id,jtunai_nilai FROM jual_tunai WHERE jtunai_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==2){
			if($nbrows>1){
				$sql="SELECT jtunai_id,jtunai_nilai FROM jual_tunai WHERE jtunai_ref='".$ref_id."' LIMIT 1,1";
			}else{
				$sql="SELECT jtunai_id,jtunai_nilai FROM jual_tunai WHERE jtunai_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==3){
			if($nbrows==2){
				$sql="SELECT jtunai_id,jtunai_nilai FROM jual_tunai WHERE jtunai_ref='".$ref_id."' LIMIT 1,1";
			}elseif($nbrows==3){
				$sql="SELECT jtunai_id,jtunai_nilai FROM jual_tunai WHERE jtunai_ref='".$ref_id."' LIMIT 2,1";
			}else{
				$sql="SELECT jtunai_id,jtunai_nilai FROM jual_tunai WHERE jtunai_ref='".$ref_id."'";
			}
		}
		
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
	
	function get_voucher_by_ref($ref_id ,$cara_bayar_ke){
		$sql="SELECT tvoucher_id FROM voucher_terima WHERE tvoucher_ref='".$ref_id."'";
		$rs = $this->db->query($sql);
		$nbrows = $rs->num_rows();
		
		if($cara_bayar_ke==''){
			$cara_bayar_ke = 1;
		}
		
		if($cara_bayar_ke==1){
			if($nbrows>1){
				$sql="SELECT tvoucher_id,tvoucher_novoucher,tvoucher_nilai FROM voucher_terima WHERE tvoucher_ref='".$ref_id."' LIMIT 0,1";
			}else{
				$sql="SELECT tvoucher_id,tvoucher_novoucher,tvoucher_nilai FROM voucher_terima WHERE tvoucher_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==2){
			if($nbrows>1){
				$sql="SELECT tvoucher_id,tvoucher_novoucher,tvoucher_nilai FROM voucher_terima WHERE tvoucher_ref='".$ref_id."' LIMIT 1,1";
			}else{
				$sql="SELECT tvoucher_id,tvoucher_novoucher,tvoucher_nilai FROM voucher_terima WHERE tvoucher_ref='".$ref_id."'";
			}
		}elseif($cara_bayar_ke==3){
			if($nbrows==2){
				$sql="SELECT tvoucher_id,tvoucher_novoucher,tvoucher_nilai FROM voucher_terima WHERE tvoucher_ref='".$ref_id."' LIMIT 1,1";
			}elseif($nbrows==3){
				$sql="SELECT tvoucher_id,tvoucher_novoucher,tvoucher_nilai FROM voucher_terima WHERE tvoucher_ref='".$ref_id."' LIMIT 2,1";
			}else{
				$sql="SELECT tvoucher_id,tvoucher_novoucher,tvoucher_nilai FROM voucher_terima WHERE tvoucher_ref='".$ref_id."'";
			}
		}
		
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
	
	function get_transfer_paket_by_ref($ref_id){
		$sql="SELECT jtransfer_id,jtransfer_bank,jtransfer_nilai FROM jual_transfer where jtransfer_ref='".$ref_id."'";
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
	
		
	function get_harga_produk($produk_id) {
		
		$query = "SELECT produk_harga from produk where produk_id='".$produk_id."'";
		$result = $this->db->query($query);
		if($result->num_rows()){
			$data=$result->row();
			$produk_harga=$data->produk_harga;
			return $produk_harga;
		}else{
			return '0';
		}
	}
	
	function get_harga_rawat($rawat_id) {
		
		$query = "SELECT rawat_harga FROM perawatan WHERE rawat_id='".$rawat_id."'";
		$result = $this->db->query($query);
		if($result->num_rows()){
			$data=$result->row();
			$rawat_harga=$data->rawat_harga;
			return $rawat_harga;
		}else{
			return '0';
		}
	}
		
	function get_member_by_cust($member_cust){
		//$sql = "SELECT * from member where member_cust='".$member_cust."' and member_status!='tidak aktif' order by member_id desc limit 1";
		//$sql = "SELECT * from member where member_cust='".$member_cust."'";
		$sql = "SELECT * from vu_customer where cust_id='".$member_cust."'";
		/*$sql="SELECT member_id, member_membert, member_cust, date_format(member_valid,'%Y-%m-%d') as member_valid, member_no, member_register, member_point, member_jenis, member_nota_ref, member_status, member_tglserahterima,
				member_creator, member_kodecust, member_update, member_date_update, member_date_create
			from member where member_cust= '".$member_cust."' and member_valid > now() order by member_id desc limit 1 ";*/
		
		
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
	
	/*Auto Catatan Customer */
	function get_auto_catatan_customer($note_customer){

		$sql = "select * from customer_note where note_customer = ".$note_customer." and note_aktif ='Aktif' order by note_tanggal desc limit 1";
	                      
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
	
	
	function get_auto_karyawan_sip($karyawan_id){
		$sql = "SELECT karyawan_sip, karyawan_no from vu_karyawan where karyawan_id='".$karyawan_id."' and karyawan_aktif!='Tidak Aktif' order by karyawan_id desc limit 1";
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
	
	function get_auto_cust_no($cust_id){
		$sql = "SELECT cust_no from customer where cust_id='".$cust_id."' and cust_aktif!='Tidak Aktif' order by cust_id desc limit 1";
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
	
	
	function get_customer_list($query,$start,$end){
		$sql="SELECT cust_id,cust_no,cust_nama,cust_tgllahir,cust_alamat,cust_telprumah,cust_point,date_format(cust_tgllahir,'%Y-%m-%d') as cust_tgllahir FROM customer WHERE cust_aktif='Aktif'";
		if($query<>""){
			$sql=$sql." and (/*cust_id = '".$query."' or*/ cust_no like '%".$query."%' or cust_alamat like '%".$query."%' or cust_nama like '%".$query."%' or cust_telprumah like '%".$query."%' or cust_telprumah2 like '%".$query."%' or cust_telpkantor like '%".$query."%' or cust_hp like '%".$query."%' or cust_hp2 like '%".$query."%' or cust_hp3 like '%".$query."%') ";
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
	
	function get_kwitansi_list($query,$start=0,$end=10,$kwitansi_cust){
		/*$sql="SELECT kwitansi_id
				,kwitansi_no
				,kwitansi_nilai
				,cust_no
				,cust_nama
				,cust_tgllahir
				,cust_alamat
				,IF((kwitansi_nilai-(IF(sum(jkwitansi_nilai),sum(jkwitansi_nilai),0)))=0,
					kwitansi_nilai,
					(kwitansi_nilai-(IF(sum(jkwitansi_nilai),sum(jkwitansi_nilai),0)))) AS total_sisa
			FROM cetak_kwitansi
				LEFT JOIN jual_kwitansi ON(jkwitansi_master=kwitansi_id)
				LEFT JOIN customer ON(kwitansi_cust=cust_id)
			WHERE kwitansi_status<>'Batal'";*/
		$sql = "SELECT kwitansi_id
				,kwitansi_no
				,kwitansi_nilai
				,cust_no
				,cust_nama
				,cust_tgllahir
				,cust_alamat
				,kwitansi_sisa AS total_sisa
			FROM cetak_kwitansi
				LEFT JOIN jual_kwitansi ON(jkwitansi_master=kwitansi_id)
				LEFT JOIN customer ON(kwitansi_cust=cust_id)
			WHERE kwitansi_status = 'Tertutup'
				AND kwitansi_sisa>0";
		if($query<>""){
			$sql=$sql." and (cust_no like '%".$query."%' or cust_nama like '%".$query."%' or cust_alamat like '%".$query."%' or kwitansi_no like '%".$query."%')";
		}
		if($kwitansi_cust<>""){
			$sql=$sql." AND kwitansi_cust='$kwitansi_cust'";
		}
		$sql.=" GROUP BY kwitansi_id";
		
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
	
	function get_supplier_list($query,$start,$end){
		$sql="SELECT supplier_id,supplier_nama,supplier_alamat,supplier_kota,supplier_notelp FROM supplier where supplier_aktif='Aktif'";
		
		if($query<>""){
			$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql .= " (supplier_nama LIKE '%".addslashes($query)."%')";
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
	
	//Function utk mendapatkan list Kurs
	function get_kurs_list($query,$start,$end,$kurs_id){
		
		if($kurs_id<>''){
			$sql="SELECT * FROM kurs where kurs_aktif='Aktif' AND kurs_id = $kurs_id";	
		}
		else{
		$sql="SELECT * FROM kurs where kurs_aktif='Aktif'";
		}
	
		if($query<>""){
			$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql .= " (kurs_negara LIKE '%".addslashes($query)."%')";
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
	
	function get_cabang_list(){
		$sql="SELECT cabang_id, cabang_nama, cabang_kode FROM cabang where cabang_aktif='Aktif'";
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
	
	function get_nomor_member($table,$field,$pattern,$length){
		$date=date('y');
		$len_pattern=strlen($pattern);
		$len_lpad=$length-$len_pattern;
		//$sql="SELECT LPAD((RIGHT(MAX(SUBSTRING(".$field.",-4)),".$len_lpad.")+1),".$len_lpad.",0) AS max_key FROM ".$table." WHERE ".$field." LIKE '".$date."%'";
		//$sql="SELECT LPAD((RIGHT(MAX(SUBSTRING(".$field.",-4)),".$len_lpad.")+1),".$len_lpad.",0) AS max_key FROM ".$table." WHERE SUBSTRING(".$field.",1,6) LIKE '%".$date."'"; //ini yang benar
		$sql="SELECT LPAD((RIGHT(MAX(SUBSTRING(".$field.",-4)),".$len_lpad.")+1),".$len_lpad.",0) AS max_key FROM ".$table." WHERE SUBSTRING(".$field.",1,6) LIKE '%10' or SUBSTRING(".$field.",1,6) LIKE '%11' or SUBSTRING(".$field.",1,6) LIKE '%12' or SUBSTRING(".$field.",1,6) LIKE '%13' or SUBSTRING(".$field.",1,6) LIKE '%14' "; //sementara, supaya no_member bisa urut terus
		
		$query=$this->db->query($sql);
		if($query->num_rows()){
			$data=$query->row();
			$kode=$data->max_key;
			if(is_null($kode))
			{
				$pad="";
				for($i=1;$i<$len_lpad;$i++)
					$pad.="0";
				$kode=$pattern.$pad."1";
			}else{
				$kode=$pattern.$kode;
			}
			return $kode;
		}else{
			$pad="";
			for($i=1;$i<$len_lpad;$i++)
				$pad.="0";
			$kode=$pattern.$pad."1";
			return $kode;
		}
	}
	
	function get_custno_gen($table,$field,$pattern,$length){
		$len_pattern=strlen($pattern);
		$len_lpad=$length-$len_pattern;
		$sql="select concat(left(max(substring(".$field.",-6)),".$len_pattern."),LPAD((right(max(substring(".$field.",-6)),".$len_lpad.")+1),".$len_lpad.",0)) as max_key from ".$table." where ".$field." is not null and ".$field."<>'' and cust_aktif='Aktif'";
		/*hasilnya akan spt ini: 
		select 
			concat(left(max(substring(c.cust_no,-6)),0),LPAD((right(max(substring(c.cust_no,-6)),6)+1),6,0)) as max_key 
			from customer c
			where c.cust_no is not null and c.cust_no <>'' and cust_aktif='Aktif'
		*/
		
		$query=$this->db->query($sql);
		if($query->num_rows()){
			$data=$query->row();
			$kode=$data->max_key;
			if(is_null($kode))
			{
				$pad="";
				for($i=1;$i<$len_lpad;$i++)
					$pad.="0";
				$kode=$pattern.$pad."1";
			}
			return $kode;
		}else{
			$pad="";
			for($i=1;$i<$len_lpad;$i++)
				$pad.="0";
			$kode=$pattern.$pad."1";
			return $kode;
		}
	}
	
	function get_kode_pajak($pattern, $length){
		$len_pattern=strlen($pattern);
		$len_lpad=$length-$len_pattern;
		$sql="select concat(left(max(nobukti_pajak),".$len_pattern."),LPAD((right(max(nobukti_pajak),".$len_lpad.")+1),".$len_lpad.",0)) as max_key from 
					(
						select jproduk_nobukti_pajak AS nobukti_pajak from master_jual_produk
						union
						select dapaket_nobukti_pajak AS nobukti_pajak from detail_ambil_paket					
						union 					
						select jrawat_nobukti_pajak AS nobukti_pajak from master_jual_rawat
						union
						select jpaket_nobukti_pajak As nobukti_pajak from master_jual_paket
					) AS table_union
				where nobukti_pajak like '".$pattern."%' AND nobukti_pajak is not null";
				
		$query=$this->db->query($sql);
		if($query->num_rows()){
			$data=$query->row();
			$kode=$data->max_key;
			if(is_null($kode))
			{
				$pad="";
				for($i=1;$i<$len_lpad;$i++)
					$pad.="0";
				$kode=$pattern.$pad."1";
			}
			return $kode;
		}else{
			$pad="";
			for($i=1;$i<$len_lpad;$i++)
				$pad.="0";
			$kode=$pattern.$pad."1";
			return $kode;
		}
	}
	
	function get_kode_1($table,$field,$pattern,$length){
		$len_pattern=strlen($pattern);
		$len_lpad=$length-$len_pattern;
		$sql="select concat(left(max(".$field."),".$len_pattern."),LPAD((right(max(".$field."),".$len_lpad.")+1),".$len_lpad.",0)) as max_key from ".$table." where ".$field." like '".$pattern."%'";
		
		$query=$this->db->query($sql);
		if($query->num_rows()){
			$data=$query->row();
			$kode=$data->max_key;
			if(is_null($kode))
			{
				$pad="";
				for($i=1;$i<$len_lpad;$i++)
					$pad.="0";
				$kode=$pattern.$pad."1";
			}
			return $kode;
		}else{
			$pad="";
			for($i=1;$i<$len_lpad;$i++)
				$pad.="0";
			$kode=$pattern.$pad."1";
			return $kode;
		}
	}
	
	function get_kode_2($table,$field,$pattern_g,$pattern_j,$length){
		$len_pattern=strlen($pattern_g.$pattern_j);
		$len_lpad=$length-$len_pattern;
		$sql="select concat(left(max(".$field."),".$len_pattern."),LPAD((right(max(".$field."),".$len_lpad.")+1),".$len_lpad.",0)) as max_key from ".$table." where ".$field." like '".$pattern_g.$pattern_j."%'";
		$query=$this->db->query($sql);
		if($query->num_rows()){
			$data=$query->row();
			$kode=$data->max_key;
			if(is_null($kode))
			{
				$pad="";
				for($i=1;$i<$len_lpad;$i++)
					$pad.="0";
				$kode=$pattern_g.$pattern_j.$pad."1";
			}
			return $kode;
		}else{
			$pad="";
			for($i=1;$i<$len_lpad;$i++)
				$pad.="0";
			$kode=$pattern_g.$pattern_j.$pad."1";
			return $kode;
		}
	}
	
	function get_nik_karyawan($table,$field,$pattern,$length){
		$len_pattern=strlen($pattern);
		$len_lpad=$length-$len_pattern;
		$sql="select concat(left(max(".$field."),".$len_pattern."),LPAD((right(max(".$field."),".$len_lpad.")+1),".$len_lpad.",0)) as max_key from ".$table." where ".$field." like '".$pattern."%'";
		
		$query=$this->db->query($sql);
		if($query->num_rows()){
			$data=$query->row();
			$kode=$data->max_key;
			if(is_null($kode))
			{
				$pad="";
				for($i=1;$i<$len_lpad;$i++)
					$pad.="0";
				$kode=$pattern.$pad."1";
			}
			return $kode;
		}else{
			$pad="";
			for($i=1;$i<$len_lpad;$i++)
				$pad.="0";
			$kode=$pattern.$pad."1";
			return $kode;
		}
	}
	
	function get_resep_kode($table,$field,$pattern,$length){
		$len_pattern=strlen($pattern);
		$len_lpad=$length-$len_pattern;
		$sql="select concat(left(max(".$field."),".$len_pattern."),LPAD((right(max(".$field."),".$len_lpad.")+1),".$len_lpad.",0)) as max_key from ".$table." where ".$field." like '".$pattern."%'";
		$query=$this->db->query($sql);
		if($query->num_rows()){
			$data=$query->row();
			$kode=$data->max_key;
			if(is_null($kode))
			{
				$pad="";
				for($i=1;$i<$len_lpad;$i++)
					$pad.="0";
				$kode=$pattern.$pad."1";
			}
			return $kode;
		}else{
			$pad="";
			for($i=1;$i<$len_lpad;$i++)
				$pad.="0";
			$kode=$pattern.$pad."1";
			return $kode;
		}
	}
	
	
	
	function get_satuan_list(){
		$sql="SELECT satuan_id,satuan_kode,satuan_nama FROM satuan where satuan_aktif='Aktif'";
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
	
	function get_satuan_bydjproduk_list($djproduk_id){
		$sql="SELECT satuan_id,satuan_nama,konversi_nilai,satuan_kode,konversi_default,produk_harga FROM produk,satuan_konversi,satuan WHERE produk_id=konversi_produk AND konversi_satuan=satuan_id AND produk_id='$djproduk_id'";
		if($djproduk_id==0)
			$sql="SELECT satuan_id,satuan_nama,konversi_nilai,satuan_kode,konversi_default,produk_harga FROM produk,satuan_konversi,satuan WHERE produk_id=konversi_produk AND konversi_satuan=satuan_id";
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
	
	function get_kategori_produk_list(){
		$sql="SELECT kategori_id,kategori_nama FROM kategori where kategori_jenis='produk' and kategori_aktif='Aktif'";
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
	
	function get_kategori_paket_list(){
		$sql="SELECT kategori_id,kategori_nama FROM kategori where kategori_jenis='paket' and kategori_aktif='Aktif'";
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
	
	function get_kategori_perawatan_list(){
		$sql="SELECT kategori_id,kategori_nama FROM kategori WHERE kategori_jenis='perawatan' and kategori_aktif='Aktif'";
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
	
	function get_kategori_list(){
		$sql="SELECT kategori_id,kategori_nama,kategori_jenis FROM kategori where kategori_aktif='Aktif'";
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
	
	function get_rawat_list($query,$start,$end){
	/*$sql="SELECT produk_id,produk_kode,produk_nama,produk_kategori,produk_harga,produk_group,produk_du,produk_dm
				,kategori_nama, group_nama, satuan_kode, satuan_nama 
				FROM produk,satuan,kategori,produk_group where satuan_id=produk_satuan and kategori_id=produk_kategori
				and produk_group=group_id and produk_aktif='Aktif'";*/
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_drawat="SELECT distinct(drawat_rawat) FROM detail_jual_rawat WHERE drawat_master='$query'";
			$rs=$this->db->query($sql_drawat);
			$rs_rows=$rs->num_rows();
		}
		
		$sql="SELECT rawat_id,rawat_harga,rawat_kode,group_nama,kategori_nama,rawat_du,rawat_dm,rawat_nama FROM perawatan LEFT JOIN produk_group ON(rawat_group=group_id) LEFT JOIN kategori ON(rawat_kategori=kategori_id) WHERE rawat_aktif='Aktif'";//join dr tabel: perawatan,produk_group,kategori2,kategori,jenis,gudang
		if($query<>"" && is_numeric($query)==false){
			$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
			$sql.=" (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%' or kategori_nama like '%".$query."%') ";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("AND",$query)? " OR ":" AND ";
				foreach($rs->result() as $row_drawat){
					
					$filter.="OR rawat_id='".$row_drawat->drawat_rawat."' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
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
	
	/*function get_cbo_rawat_list($query,$start,$end){
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_drawat="SELECT distinct(drawat_rawat) FROM detail_jual_rawat WHERE drawat_master='$query'";
			$rs=$this->db->query($sql_drawat);
			$rs_rows=$rs->num_rows();
		}
		
		$sql="SELECT rawat_id,rawat_kode,rawat_nama FROM perawatan WHERE rawat_aktif='Aktif'";//join dr tabel: perawatan,produk_group,kategori2,kategori,jenis,gudang
		if($query<>"" && is_numeric($query)==false){
			$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
			$sql.=" (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%') ";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("AND",$query)? " OR ":" AND ";
				foreach($rs->result() as $row_drawat){
					
					$filter.="OR rawat_id='".$row_drawat->drawat_rawat."' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
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
	}*/
	
	function get_perawatan_list($query="",$start=0,$end=10){
		//$sql="SELECT rawat_id,rawat_kode,rawat_nama,rawat_kategori,rawat_harga,rawat_group,rawat_du,rawat_dm
		//		,kategori_nama, group_nama 
		//		FROM perawatan,kategori,produk_group where kategori_id=rawat_kategori
		//		and rawat_group=group_id and rawat_aktif='Aktif'";
				
		$sql="SELECT * FROM vu_perawatan";//join dr tabel: perawatan,produk_group,kategori2,kategori,jenis,gudang
		if($query<>""){
			$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
			$sql.=" (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%' or kategori_nama like '%".$query."%' or group_nama like '%".$query."%')";
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
	
	//Ambil Perawatan berdasarkan kategori MEDIS
	function get_rawat_medis_list($query,$start,$end){
		/*$sql="SELECT rawat_id,rawat_kode,rawat_nama,rawat_kategori,rawat_harga,rawat_group,rawat_du,rawat_dm,kategori_nama, group_nama 
		FROM perawatan,kategori,produk_group where rawat_kategori=kategori_id 
		AND rawat_group=group_id AND rawat_aktif='Aktif' AND kategori_nama='Medis'";*/
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_dapp="SELECT distinct(dapp_perawatan) FROM appointment_detail WHERE dapp_master='$query'";
			$rs=$this->db->query($sql_dapp);
			$rs_rows=$rs->num_rows();
		}
		
		$sql="SELECT * FROM vu_perawatan WHERE (kategori_nama='Medis' OR kategori_nama='Surgery' OR kategori_nama='Anti Aging') AND rawat_aktif='Aktif'";//join dr tabel: perawatan,produk_group,kategori2,kategori,jenis,gudang
		if($query<>"" && is_numeric($query)==false){
			$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%')";
			//$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%' or group_nama like '%".$query."%')";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("AND",$query)? " OR ":" AND ";
				foreach($rs->result() as $row_dapp){
					
					$filter.="OR rawat_id='".$row_dapp->dapp_perawatan."' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
		}
		$sql.=" ORDER BY rawat_nama ASC";
	
		$result = $this->db->query($sql);
		$nbrows = $result->num_rows();
		$limit = $sql." LIMIT ".$start.",".$end;			
		//echo $limit;
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
	/*function get_rawat_medis_list($query,$start,$end){
		return $this->get_perawatan_medis_list($query,$start,$end);
	}
	
	function get_perawatan_medis_list($query,$start,$end){
		$sql="SELECT rawat_id,rawat_kode,rawat_nama,rawat_kategori,rawat_harga,rawat_group,rawat_du,rawat_dm
				,kategori_nama, group_nama 
				FROM perawatan,kategori,produk_group where rawat_kategori=kategori_id
				and rawat_group=group_id and rawat_aktif='Tidak Aktif' AND kategori_nama='Medis'";
		if($query<>"")
			$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%' or kategori_nama like '%".$query."%' or group_nama like '%".$query."%')";
	
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
	}*/
	//END Ambil Perawatan berdasarkan kategori MEDIS
	
	function get_tindakan_medis_list($query,$start,$end){
		/*$sql="SELECT rawat_id,rawat_kode,rawat_nama,rawat_kategori,rawat_harga,rawat_group,rawat_du,rawat_dm,kategori_nama, group_nama 
		FROM perawatan,kategori,produk_group where rawat_kategori=kategori_id 
		AND rawat_group=group_id AND rawat_aktif='Aktif' AND kategori_nama='Medis'";*/
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_dapp="SELECT distinct(dtrawat_perawatan) FROM tindakan_detail WHERE dtrawat_master='$query'";
			$rs=$this->db->query($sql_dapp);
			$rs_rows=$rs->num_rows();
		}
		
		//$sql="SELECT * FROM vu_perawatan WHERE kategori_nama='Medis' AND rawat_aktif='Aktif'";//join dr tabel: perawatan,produk_group,kategori2,kategori,jenis,gudang
		$sql="SELECT rawat_id,rawat_harga,rawat_kode,group_nama,kategori_nama,rawat_du,rawat_dm,rawat_nama FROM perawatan LEFT JOIN produk_group ON(rawat_group=group_id) LEFT JOIN kategori ON(rawat_kategori=kategori_id) WHERE (kategori_nama='Medis' OR kategori_nama='Anti Aging' OR kategori_nama='Surgery') AND rawat_aktif='Aktif'";
		if($query<>"" && is_numeric($query)==false){
			$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%')";
			//$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%' or group_nama like '%".$query."%')";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("AND",$query)? " OR ":" AND ";
				foreach($rs->result() as $row_dapp){
					
					$filter.="OR rawat_id='".$row_dapp->dtrawat_perawatan."' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
		}
		$sql.=" ORDER BY rawat_nama ASC";
		//echo $sql;
	
		$result = $this->db->query($sql);
		$nbrows = $result->num_rows();
		$limit = $sql." LIMIT ".$start.",".$end;			
		//echo $limit;
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
	
	function get_tindakan_nonmedis_list($query,$start,$end){
		/*$sql="SELECT rawat_id,rawat_kode,rawat_nama,rawat_kategori,rawat_harga,rawat_group,rawat_du,rawat_dm,kategori_nama, group_nama 
		FROM perawatan,kategori,produk_group where rawat_kategori=kategori_id 
		AND rawat_group=group_id AND rawat_aktif='Aktif' AND kategori_nama='Medis'";*/
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_dapp="SELECT distinct(dtrawat_perawatan) FROM tindakan_detail WHERE dtrawat_master='$query'";
			$rs=$this->db->query($sql_dapp);
			$rs_rows=$rs->num_rows();
		}
		
		$sql="SELECT * FROM vu_perawatan WHERE kategori_nama='Non Medis' AND rawat_aktif='Aktif'";//join dr tabel: perawatan,produk_group,kategori2,kategori,jenis,gudang
		if($query<>"" && is_numeric($query)==false){
			$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%')";
			//$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%' or group_nama like '%".$query."%')";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("AND",$query)? " OR ":" AND ";
				foreach($rs->result() as $row_dapp){
					
					$filter.="OR rawat_id='".$row_dapp->dtrawat_perawatan."' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
		}
		$sql.=" ORDER BY rawat_nama ASC";
	
		$result = $this->db->query($sql);
		$nbrows = $result->num_rows();
		$limit = $sql." LIMIT ".$start.",".$end;			
		//echo $limit;
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
	
	
	function get_rawat_nonmedis_list($query,$start,$end){
		/*$sql="SELECT rawat_id,rawat_kode,rawat_nama,rawat_kategori,rawat_harga,rawat_group,rawat_du,rawat_dm,kategori_nama, group_nama 
		FROM perawatan,kategori,produk_group where rawat_kategori=kategori_id 
		AND rawat_group=group_id AND rawat_aktif='Aktif' AND kategori_nama='Non Medis'";*/
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_dapp="SELECT distinct(dapp_perawatan) FROM appointment_detail WHERE dapp_master='$query'";
			$rs=$this->db->query($sql_dapp);
			$rs_rows=$rs->num_rows();
		}
		
		$sql="SELECT * FROM vu_perawatan WHERE kategori_nama='Non Medis' AND rawat_aktif='Aktif'";//join dr tabel: perawatan,produk_group,kategori2,kategori,jenis,gudang
		if($query<>"" && is_numeric($query)==false){
			$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%')";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("AND",$query)? " OR ":" AND ";
				foreach($rs->result() as $row_dapp){
					
					$filter.="OR rawat_id='".$row_dapp->dapp_perawatan."' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
		}
		$sql.=" ORDER BY rawat_nama ASC";
	
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
	//Ambil Perawatan berdasarkan kategori NON-MEDIS
	/*function get_rawat_nonmedis_list($query="",$start=0,$end=10){
		return $this->get_perawatan_nonmedis_list($query="",$start=0,$end=10);
	}
	
	function get_perawatan_nonmedis_list($query="",$start=0,$end=10){
		$sql="SELECT rawat_id,rawat_kode,rawat_nama,rawat_kategori,rawat_harga,rawat_group,rawat_du,rawat_dm
				,kategori_nama, group_nama 
				FROM perawatan,kategori,produk_group where rawat_kategori=kategori_id
				AND rawat_group=group_id AND rawat_aktif='Aktif' AND kategori_nama='Non Medis'";
		if($query<>"")
			$sql.=" and (rawat_kode like '%".$query."%' or rawat_nama like '%".$query."%' or satuan_nama like '%".$query."%'
						 or kategori_nama like '%".$query."%' or group_nama like '%".$query."%')";
	
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
	}*/
	//END Ambil Perawatan berdasarkan kategori NON-MEDIS
	
	function get_karyawan_list($query="",$start=0,$end=10){
		$sql="select karyawan_id,karyawan_no,karyawan_nama,jabatan_nama from vu_karyawan,jabatan where jabatan_id=karyawan_jabatan and karyawan_aktif='Aktif'";
		if($query!=="")
			$sql.=" and (karyawan_id like '%".$query."%' or karyawan_no like '%".$query."%' or karyawan_nama like '%".$query."%'
						 or jabatan_nama like '%".$query."%')";
	
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
	
	function get_terapis_list($query="",$start=0,$end=30){
		$sql="select karyawan_id,karyawan_no,karyawan_nama,karyawan_username,jabatan_nama from vu_karyawan,jabatan where jabatan_id=karyawan_jabatan and karyawan_aktif='Aktif' and karyawan_jabatan='7'";
		if($query!=="")
			$sql.=" and (karyawan_id like '%".$query."%' or karyawan_no like '%".$query."%' or karyawan_nama like '%".$query."%'
						 or jabatan_nama like '%".$query."%')";
	
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
	
	function get_user_karyawan_nolist($query,$start,$end){
		$sql="SELECT karyawan_id,karyawan_no,karyawan_nama FROM vu_karyawan 
				WHERE karyawan_aktif='Aktif'
				AND karyawan_id NOT IN(select user_karyawan from users)";
		if($query!=="")
			$sql.=" and (karyawan_id like '%".$query."%' or karyawan_no like '%".$query."%' or karyawan_nama like '%".$query."%')";
	
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
	
	function get_user_karyawan_list($query,$start,$end){
		$sql="SELECT karyawan_id,karyawan_no,karyawan_nama,jabatan_nama FROM vu_karyawan,jabatan 
				WHERE jabatan_id=karyawan_jabatan AND karyawan_aktif='Aktif'
				AND karyawan_id IN(select user_karyawan from users)";
		if($query!=="")
			$sql.=" and (karyawan_id like '%".$query."%' or karyawan_no like '%".$query."%' or karyawan_nama like '%".$query."%'
						 or jabatan_nama like '%".$query."%')";
	
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
	
	function get_paket_list(){
		$sql="SELECT * FROM paket INNER JOIN produk_group ON paket.paket_group=produk_group.group_id INNER JOIN kategori ON produk_group.group_kelompok=kategori.kategori_id WHERE kategori.kategori_jenis='paket' AND paket_aktif='Aktif'";
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
	
	function get_produk_group_list(){
		$sql="SELECT produk_id,produk_nama, produk_kategori,produk_group FROM produk where produk_aktif='Aktif'";
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
	
	function get_produk_rawat_list($query="",$start=0,$end=10){
			$query = "SELECT produk_kode as produk_rawat_kode, produk_nama as produk_rawat_nama, 'produk' as produk_rawat_jenis 
					FROM produk where produk_aktif='Aktif'
					UNION
					SELECT rawat_kode as produk_rawat_kode, rawat_nama as produk_rawat_nama, 'perawatan' as produk_rawat_jenis 
					FROM perawatan where rawat_aktif='Aktif'";
		$result = $this->db->query($query);
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
		
	function get_produk_list($query,$start,$end){
		/*$sql="SELECT produk_id,produk_kode,produk_nama,produk_kategori,produk_harga,produk_group,produk_du,produk_dm
				,kategori_nama, group_nama, satuan_kode, satuan_nama 
				FROM produk,satuan,kategori,produk_group where satuan_id=produk_satuan and kategori_id=produk_kategori
				and produk_group=group_id and produk_aktif='Aktif'";*/
		$rs_rows=0;
		if(is_numeric($query)==true){
			$sql_dproduk="SELECT krawat_produk FROM perawatan_konsumsi WHERE krawat_master='$query'";
			$rs=$this->db->query($sql_dproduk);
			$rs_rows=$rs->num_rows();
		}
		
		$sql="select * from vu_produk WHERE produk_aktif='Aktif'";
		if($query<>"" && is_numeric($query)==false){
			$sql.=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql.=" (produk_kode like '%".$query."%' or produk_nama like '%".$query."%' or satuan_nama like '%".$query."%' or kategori_nama like '%".$query."%' or group_nama like '%".$query."%') ";
		}else{
			if($rs_rows){
				$filter="";
				$sql.=eregi("AND",$sql)? " OR ":" AND ";
				foreach($rs->result() as $row_dproduk){
					
					$filter.="OR produk_idsss='".$row_dproduk->krawat_produk."' ";
					//$filter.="OR produk_id='$query' ";
				}
				$sql=$sql."(".substr($filter,2,strlen($filter)).")";
			}
		}
		
		/*if($query<>"")
			$sql.=" WHERE (produk_kode like '%".$query."%' or produk_nama like '%".$query."%' or satuan_nama like '%".$query."%'
						 or kategori_nama like '%".$query."%' or group_nama like '%".$query."%') ";*/
		
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
	
	function get_trans_list($query="",$start=0,$end=10){
		/*$sql="SELECT produk_id,produk_kode,produk_nama,produk_kategori,produk_harga,produk_group,produk_du,produk_dm
				,kategori_nama, group_nama, satuan_kode, satuan_nama 
				FROM produk,satuan,kategori,produk_group where satuan_id=produk_satuan and kategori_id=produk_kategori
				and produk_group=group_id and produk_aktif='Aktif'";*/
		$sql="select * from vu_trans_union";
		if($query<>"")
			$sql.=" and (no_bukti like '%".$query."%' or cust_nama like '%".$query."%' or cust_no like '%".$query."%'
						 or cust_member like '%".$query."%') ";
						 
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
	
	function get_group_list(){
		$sql="SELECT group_id,group_nama,group_duproduk,group_dmproduk,group_durawat,group_dmrawat,group_dupaket,group_dmpaket FROM produk_group where group_aktif='Aktif'";
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
	
	function get_group_produk_list(){
		$sql="SELECT group_id,group_nama,group_duproduk,group_dmproduk,group_durawat,group_dmrawat,group_dupaket,group_dmpaket,group_dultah,group_dcard,group_dkolega,group_dkeluarga,group_downer,group_dgrooming,group_dwartawan, group_dstaffdokter, group_dstaffnondokter, kategori_nama,kategori_id 
				FROM produk_group,kategori 
				WHERE group_kelompok=kategori_id AND kategori_jenis='produk' AND group_aktif='Aktif' AND kategori_aktif='Aktif'";

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
	
	function get_kategori_jenis_produk_list(){
		$sql="SELECT kategori_id, kategori_nama FROM kategori where kategori_jenis = 'produk' AND kategori_aktif='Aktif'";
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
	
	function get_group_perawatan_list(){
		$sql="SELECT group_id,group_nama,
				group_duproduk,group_dmproduk,group_dultah, group_dcard, group_dkolega, group_dkeluarga, group_downer, group_dgrooming,group_dwartawan, group_dstaffdokter, group_dstaffnondokter,
				group_durawat,group_dmrawat,group_dupaket,group_dmpaket, kategori_nama,kategori_id 
				FROM produk_group,kategori 
				WHERE group_kelompok=kategori_id AND kategori_jenis='perawatan' AND group_aktif='Aktif' AND kategori_aktif='Aktif'";
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
	
	function get_group_paket_list(){
		$sql="SELECT group_id,group_nama,group_duproduk,group_dmproduk,group_durawat,group_dmrawat,group_dupaket,group_dmpaket,group_dultah, group_dcard, group_dkolega, group_dkeluarga, group_downer, group_dgrooming, group_dwartawan, group_dstaffdokter, group_dstaffnondokter
				kategori_nama,kategori_id 
				FROM produk_group,kategori 
				WHERE group_kelompok=kategori_id AND kategori_jenis='produk' AND group_aktif='Aktif' AND kategori_aktif='Aktif'";
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
	
	function get_group_by_id($group_id){
		$query = "SELECT * FROM produk_group WHERE group_id='".$group_id."' and group_aktif='Aktif'";
		
		$result = $this->db->query($query);
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
	
	function get_gudang_list(){
		$sql="SELECT gudang_id,gudang_nama FROM gudang WHERE gudang_aktif='Aktif'";
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
	
	function get_alat_list(){
		$sql="SELECT alat_id,alat_nama,alat_jumlah FROM alat WHERE alat_aktif='Aktif'";
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
	
	function pengecekan_dokumen($tanggal_pengecekan){
		
		$date = date('Y-m-d');
			//$date_1 = '01';
			//$date_2 = '02';
			$date_3 = '03';
			$month = substr($tanggal_pengecekan,5,2);
			$year = substr($tanggal_pengecekan,0,4);
			$begin=mktime(0,0,0,$month,1,$year);
			$nextmonth=strtotime("+12month",$begin);
			
			$month_next = substr(date("Y-m-d",$nextmonth),5,2);
			$year_next = substr(date("Y-m-d",$nextmonth),0,4);
			
			//$tanggal_1 = $year_next.'-'.$month_next.'-'.$date_1;
			//$tanggal_2 = $year_next.'-'.$month_next.'-'.$date_2;
			$tanggal_3 = $year_next.'-'.$month_next.'-'.$date_3;
            $datetime_now = date('Y-m-d H:i:s');
     
			if ($date <= $tanggal_3 || $tanggal_pengecekan == $date)
			{
				return '1';
			}
			else
			{
				return '0';
			}
		
		}
	
	function cara_bayar_tunai_insert($jtunai_nilai
									,$jtunai_ref
									,$jtunai_date_create
									,$jenis_transaksi
									,$cetak){
		$stat_dok = 'Terbuka';
		if($cetak==1 || $cetak==2){
			$stat_dok = 'Tertutup';
		}
		$data=array(
			"jtunai_nilai"=>$jtunai_nilai,
			"jtunai_ref"=>$jtunai_ref,
			"jtunai_transaksi"=>$jenis_transaksi,
			"jtunai_date_create"=>$jtunai_date_create,
			"jtunai_stat_dok"=>$stat_dok
			);
		$this->db->insert('jual_tunai', $data);
		if($this->db->affected_rows()){
			return 1;
		}else{
			return 0;
		}
	}
	
	function cara_bayar_transfer_insert($jtransfer_bank
										,$jtransfer_nama
										,$jtransfer_nilai
										,$jtransfer_ref
										,$jtransfer_date_create
										,$jenis_transaksi
										,$cetak){
		$stat_dok = 'Terbuka';
		if($cetak==1 || $cetak==2){
			$stat_dok = 'Tertutup';
		}
		$data=array(
			"jtransfer_bank"=>$jtransfer_bank,
			"jtransfer_nama"=>$jtransfer_nama,
			"jtransfer_nilai"=>$jtransfer_nilai,
			"jtransfer_ref"=>$jtransfer_ref,
			"jtransfer_transaksi"=>$jenis_transaksi,
			"jtransfer_date_create"=>$jtransfer_date_create,
			"jtransfer_stat_dok"=>$stat_dok
			);
		$this->db->insert('jual_transfer', $data);
		if($this->db->affected_rows()){
			return 1;
		}else{
			return 0;
		}
	}
	
	function cara_bayar_card_insert($jcard_nama
									,$jcard_edc
									,$jcard_no
									,$jcard_nilai
									,$jcard_ref
									,$jcard_date_create
									,$jenis_transaksi
									,$cetak){
		$stat_dok = 'Terbuka';
		if($cetak==1 || $cetak==2){
			$stat_dok = 'Tertutup';
		}
		$data=array(
			"jcard_nama"=>$jcard_nama,
			"jcard_edc"=>$jcard_edc,
			"jcard_no"=>$jcard_no,
			"jcard_nilai"=>$jcard_nilai,
			"jcard_ref"=>$jcard_ref,
			"jcard_transaksi"=>$jenis_transaksi,
			"jcard_date_create"=>$jcard_date_create,
			"jcard_stat_dok"=>$stat_dok
			);
		$this->db->insert('jual_card', $data);
		if($this->db->affected_rows()){
			return 1;
		}else{
			return 0;
		}
	}
	
	function cara_bayar_cek_insert($jcek_nama
									,$jcek_no
									,$jcek_valid
									,$jcek_bank
									,$jcek_nilai
									,$jcek_ref
									,$jcek_date_create
									,$jenis_transaksi
									,$cetak){
		$stat_dok = 'Terbuka';
		if($cetak==1 || $cetak==2){
			$stat_dok = 'Tertutup';
		}
		$data=array(
			"jcek_nama"=>$jcek_nama,
			"jcek_no"=>$jcek_no,
			"jcek_valid"=>$jcek_valid,
			"jcek_bank"=>$jcek_bank,
			"jcek_nilai"=>$jcek_nilai,
			"jcek_ref"=>$jcek_ref,
			"jcek_transaksi"=>$jenis_transaksi,
			"jcek_date_create"=>$jcek_date_create,
			"jcek_stat_dok"=>$stat_dok
			);
		$this->db->insert('jual_cek', $data);
		if($this->db->affected_rows()){
			return 1;
		}else{
			return 0;
		}
	}
	
	function cara_bayar_kwitansi_insert($jkwitansi_master
										,$jkwitansi_nilai
										,$jkwitansi_ref
										,$jkwitansi_date_create
										,$jenis_transaksi
										,$cetak
										,$jual_kwitansi_id){
		$stat_dok = 'Terbuka';
		if($cetak==1 || $cetak==2){
			$stat_dok = 'Tertutup';
		}
		$data=array(
			//"jkwitansi_master"=>$jkwitansi_master,
			"jkwitansi_nilai"=>$jkwitansi_nilai,
			"jkwitansi_ref"=>$jkwitansi_ref,
			"jkwitansi_transaksi"=>$jenis_transaksi,
			"jkwitansi_date_create"=>$jkwitansi_date_create,
			"jkwitansi_stat_dok"=>$stat_dok
		);
		
		/*Melakukan pengecekan, jika kwitansi_id ini ada, maka proses Insert/Create akan terisi dengan ID dari cetak_kwitansi, Jika tidak ada, maka tidak perlu diisikan*/
		$sql="SELECT kwitansi_id FROM cetak_kwitansi WHERE kwitansi_id='".$jkwitansi_master."'";
			$rs=$this->db->query($sql);
			if($rs->num_rows()) {
				$data["jkwitansi_master"]=$jkwitansi_master;
			}
		
		$sql_cek = "SELECT * FROM jual_kwitansi WHERE jkwitansi_id = '".$jual_kwitansi_id."' ";
		$rs_cek=$this->db->query($sql_cek);
			if($rs_cek->num_rows())
			{
				$this->db->where('jkwitansi_id', $jual_kwitansi_id);
				$this->db->update('jual_kwitansi', $data);
				//echo "iki update";
			}
			else
			{
				$this->db->insert('jual_kwitansi', $data);
				//echo "iki insert";
			}

			
		if($this->db->affected_rows()){
			//Jika Statusnya Tertutup, maka akan ada proses mengupdate Sisa Kwitansi (mengurangi sisa kwitansi dgn nominal kwitansi pd faktur)
			if($stat_dok=='Tertutup'){
				if(is_numeric($jkwitansi_master)==false)
				{
				$sql_kwitansi_id = "SELECT kwitansi_id from cetak_kwitansi WHERE kwitansi_no = '".$jkwitansi_master."'";
				$query_kw= $this->db->query($sql_kwitansi_id);
				$kw_id= $query_kw->row();
				$kwitansi_id= $kw_id->kwitansi_id;
				$jkwitansi_master = $kwitansi_id;
				
				}
				//masuk proses cetak faktur ==> untuk itu update db.cetak_kwitansi.kwitansi_sisa
				$sqlu = "UPDATE cetak_kwitansi,
							(SELECT sum(jkwitansi_nilai) AS total_kwitansi
								,jkwitansi_master 
							FROM jual_kwitansi
							WHERE jkwitansi_master<>0
								AND jkwitansi_stat_dok<>'Batal'
								AND jkwitansi_master='".$jkwitansi_master."'
							GROUP BY jkwitansi_master) AS vu_kw
						SET kwitansi_sisa=(kwitansi_nilai - vu_kw.total_kwitansi)
						WHERE vu_kw.jkwitansi_master=kwitansi_id
							AND kwitansi_id='".$jkwitansi_master."'";
				$this->db->query($sqlu);
				return 1;
			}
			//Jika Statusnya Batal, maka ada proses mengupdate Sisa Kwitansi (menambahkan/mengembalikan sisa dari kwitansi tersebut)
			else if($stat_dok=='Batal'){
			if(is_numeric($jkwitansi_master)==false)
				{
				$sql_kwitansi_id = "SELECT kwitansi_id from cetak_kwitansi WHERE kwitansi_no = '".$jkwitansi_master."'";
				$query_kw= $this->db->query($sql_kwitansi_id);
				$kw_id= $query_kw->row();
				$kwitansi_id= $kw_id->kwitansi_id;
				$jkwitansi_master = $kwitansi_id;
				
				}
				//masuk proses cetak faktur ==> untuk itu update db.cetak_kwitansi.kwitansi_sisa
				$sqlu = "UPDATE cetak_kwitansi,
							(SELECT sum(jkwitansi_nilai) AS total_kwitansi
								,jkwitansi_master 
							FROM jual_kwitansi
							WHERE jkwitansi_master<>0
								AND jkwitansi_stat_dok<>'Batal'
								AND jkwitansi_master='".$jkwitansi_master."'
							GROUP BY jkwitansi_master) AS vu_kw
						SET kwitansi_sisa=(kwitansi_nilai + vu_kw.total_kwitansi)
						WHERE vu_kw.jkwitansi_master=kwitansi_id
							AND kwitansi_id='".$jkwitansi_master."'";
				$this->db->query($sqlu);
				return 1;
			}
			
			else{
				return 1;
			}
			
		}else{
			return 0;
		}
	}
	
	function cara_bayar_voucher_insert($tvoucher_novoucher
									   ,$tvoucher_ref
									   ,$tvoucher_nilai
									   ,$tvoucher_date_create
									   ,$jenis_transaksi
									   ,$cetak){
		$stat_dok = 'Terbuka';
		if($cetak==1 || $cetak==2){
			$stat_dok = 'Tertutup';
		}
		$data=array(
			"tvoucher_novoucher"=>$tvoucher_novoucher,
			"tvoucher_ref"=>$tvoucher_ref,
			"tvoucher_nilai"=>$tvoucher_nilai,
			"tvoucher_transaksi"=>$jenis_transaksi,
			"tvoucher_date_create"=>$tvoucher_date_create,
			"tvoucher_stat_dok"=>$stat_dok
			);
		$this->db->insert('voucher_terima', $data);
		if($this->db->affected_rows()){
			return 1;
		}else{
			return 0;
		}
	}
	
	function cara_bayar_ftpkpr_insert($jrawat_nobukti ,$jrawat_cara ,$jrawat_kwitansi_no ,$jrawat_kwitansi_nilai
                               ,$jrawat_card_nama ,$jrawat_card_edc ,$jrawat_card_no ,$jrawat_card_nilai
                               ,$jrawat_cek_nama ,$jrawat_cek_no ,$jrawat_cek_valid ,$jrawat_cek_bank ,$jrawat_cek_nilai
                               ,$jrawat_transfer_bank ,$jrawat_transfer_nama ,$jrawat_transfer_nilai
                               ,$jrawat_tunai_nilai
                               ,$jrawat_voucher_no ,$jrawat_voucher_cashback
                               ,$jrawat_cara2
                               ,$jrawat_kwitansi_no2 ,$jrawat_kwitansi_nilai2
                               ,$jrawat_card_nama2 ,$jrawat_card_edc2 ,$jrawat_card_no2 ,$jrawat_card_nilai2
                               ,$jrawat_cek_nama2 ,$jrawat_cek_no2 ,$jrawat_cek_valid2 ,$jrawat_cek_bank2 ,$jrawat_cek_nilai2
                               ,$jrawat_transfer_bank2 ,$jrawat_transfer_nama2 ,$jrawat_transfer_nilai2
							   ,$jrawat_tunai_nilai2
                               ,$jrawat_voucher_no2 ,$jrawat_voucher_cashback2
                               ,$jrawat_cara3
                               ,$jrawat_kwitansi_no3 ,$jrawat_kwitansi_nilai3
                               ,$jrawat_card_nama3 ,$jrawat_card_edc3 ,$jrawat_card_no3 ,$jrawat_card_nilai3
                               ,$jrawat_cek_nama3 ,$jrawat_cek_no3 ,$jrawat_cek_valid3 ,$jrawat_cek_bank3 ,$jrawat_cek_nilai3
                               ,$jrawat_transfer_bank3 ,$jrawat_transfer_nama3 ,$jrawat_transfer_nilai3
							   ,$jrawat_tunai_nilai3
                               ,$jrawat_voucher_no3 ,$jrawat_voucher_cashback3
                               ,$bayar_date_create ,$jenis_transaksi ,$cetak, $jual_kwitansi_id, $jual_kwitansi_id2, $jual_kwitansi_id3){
        //delete all transaksi
        //$sql="delete from jual_kwitansi where jkwitansi_ref='".$jrawat_nobukti."'";
        //$this->db->query($sql);
        if($this->db->affected_rows()>-1){
            $sql="delete from jual_card where jcard_ref='".$jrawat_nobukti."'";
            $this->db->query($sql);
            if($this->db->affected_rows()>-1){
                $sql="delete from jual_cek where jcek_ref='".$jrawat_nobukti."'";
                $this->db->query($sql);
                if($this->db->affected_rows()>-1){
                    $sql="delete from jual_transfer where jtransfer_ref='".$jrawat_nobukti."'";
                    $this->db->query($sql);
                    if($this->db->affected_rows()>-1){
                        $sql="delete from jual_tunai where jtunai_ref='".$jrawat_nobukti."'";
                        $this->db->query($sql);
                        if($this->db->affected_rows()>-1){
                            $sql="delete from voucher_terima where tvoucher_ref='".$jrawat_nobukti."'";
                            $this->db->query($sql);
                            if($this->db->affected_rows()>-1){
                                if($jrawat_cara!=null || $jrawat_cara!=''){
                                    if($jrawat_kwitansi_nilai<>'' && $jrawat_kwitansi_nilai<>0){
                                        $result_bayar = $this->cara_bayar_kwitansi_insert($jrawat_kwitansi_no
                                                                                          ,$jrawat_kwitansi_nilai
                                                                                          ,$jrawat_nobukti
                                                                                          ,$bayar_date_create
                                                                                          ,$jenis_transaksi
                                                                                          ,$cetak
																						  ,$jual_kwitansi_id);
                                        
                                    }elseif($jrawat_card_nilai<>'' && $jrawat_card_nilai<>0){
                                        $result_bayar = $this->cara_bayar_card_insert($jrawat_card_nama
                                                                                      ,$jrawat_card_edc
                                                                                      ,$jrawat_card_no
                                                                                      ,$jrawat_card_nilai
                                                                                      ,$jrawat_nobukti
                                                                                      ,$bayar_date_create
                                                                                      ,$jenis_transaksi
                                                                                      ,$cetak);
                                    }elseif($jrawat_cek_nilai<>'' && $jrawat_cek_nilai<>0){
                                        $result_bayar = $this->cara_bayar_cek_insert($jrawat_cek_nama
                                                                                     ,$jrawat_cek_no
                                                                                     ,$jrawat_cek_valid
                                                                                     ,$jrawat_cek_bank
                                                                                     ,$jrawat_cek_nilai
                                                                                     ,$jrawat_nobukti
                                                                                     ,$bayar_date_create
                                                                                     ,$jenis_transaksi
                                                                                     ,$cetak);
                                    }elseif($jrawat_transfer_nilai<>'' && $jrawat_transfer_nilai<>0){
                                        $result_bayar = $this->cara_bayar_transfer_insert($jrawat_transfer_bank
                                                                                          ,$jrawat_transfer_nama
                                                                                          ,$jrawat_transfer_nilai
                                                                                          ,$jrawat_nobukti
                                                                                          ,$bayar_date_create
                                                                                          ,$jenis_transaksi
                                                                                          ,$cetak);
                                    }elseif($jrawat_tunai_nilai<>'' && $jrawat_tunai_nilai<>0){
                                        $result_bayar = $this->cara_bayar_tunai_insert($jrawat_tunai_nilai
                                                                                       ,$jrawat_nobukti
                                                                                       ,$bayar_date_create
                                                                                       ,$jenis_transaksi
                                                                                       ,$cetak);
                                    }elseif($jrawat_voucher_cashback<>'' && $jrawat_voucher_cashback<>0){
                                        $result_bayar = $this->cara_bayar_voucher_insert($jrawat_voucher_no
                                                                                         ,$jrawat_nobukti
                                                                                         ,$jrawat_voucher_cashback
                                                                                         ,$bayar_date_create
                                                                                         ,$jenis_transaksi
                                                                                         ,$cetak);
                                    }
                                }
                                if($jrawat_cara2!=null || $jrawat_cara2!=''){
                                    if($jrawat_kwitansi_nilai2<>'' && $jrawat_kwitansi_nilai2<>0){
                                        $result_bayar2 = $this->cara_bayar_kwitansi_insert($jrawat_kwitansi_no2
                                                                                          ,$jrawat_kwitansi_nilai2
                                                                                          ,$jrawat_nobukti
                                                                                          ,$bayar_date_create
                                                                                          ,$jenis_transaksi
                                                                                          ,$cetak
																						  ,$jual_kwitansi_id2);
                                        
                                    }elseif($jrawat_card_nilai2<>'' && $jrawat_card_nilai2<>0){
                                        $result_bayar2 = $this->cara_bayar_card_insert($jrawat_card_nama2
                                                                                      ,$jrawat_card_edc2
                                                                                      ,$jrawat_card_no2
                                                                                      ,$jrawat_card_nilai2
                                                                                      ,$jrawat_nobukti
                                                                                      ,$bayar_date_create
                                                                                      ,$jenis_transaksi
                                                                                      ,$cetak);
                                    }elseif($jrawat_cek_nilai2<>'' && $jrawat_cek_nilai2<>0){
                                        $result_bayar2 = $this->cara_bayar_cek_insert($jrawat_cek_nama2
                                                                                     ,$jrawat_cek_no2
                                                                                     ,$jrawat_cek_valid2
                                                                                     ,$jrawat_cek_bank2
                                                                                     ,$jrawat_cek_nilai2
                                                                                     ,$jrawat_nobukti
                                                                                     ,$bayar_date_create
                                                                                     ,$jenis_transaksi
                                                                                     ,$cetak);
                                    }elseif($jrawat_transfer_nilai2<>'' && $jrawat_transfer_nilai2<>0){
                                        $result_bayar2 = $this->cara_bayar_transfer_insert($jrawat_transfer_bank2
                                                                                          ,$jrawat_transfer_nama2
                                                                                          ,$jrawat_transfer_nilai2
                                                                                          ,$jrawat_nobukti
                                                                                          ,$bayar_date_create
                                                                                          ,$jenis_transaksi
                                                                                          ,$cetak);
                                    }elseif($jrawat_tunai_nilai2<>'' && $jrawat_tunai_nilai2<>0){
                                        $result_bayar2 = $this->cara_bayar_tunai_insert($jrawat_tunai_nilai2
                                                                                       ,$jrawat_nobukti
                                                                                       ,$bayar_date_create
                                                                                       ,$jenis_transaksi
                                                                                       ,$cetak);
                                    }elseif($jrawat_voucher_cashback2<>'' && $jrawat_voucher_cashback2<>0){
                                        $result_bayar2 = $this->cara_bayar_voucher_insert($jrawat_voucher_no2
                                                                                         ,$jrawat_nobukti
                                                                                         ,$jrawat_voucher_cashback2
                                                                                         ,$bayar_date_create
                                                                                         ,$jenis_transaksi
                                                                                         ,$cetak);
                                    }
                                }
                                if($jrawat_cara3!=null || $jrawat_cara3!=''){
                                    if($jrawat_kwitansi_nilai3<>'' && $jrawat_kwitansi_nilai3<>0){
                                        $result_bayar3 = $this->cara_bayar_kwitansi_insert($jrawat_kwitansi_no3
                                                                                          ,$jrawat_kwitansi_nilai3
                                                                                          ,$jrawat_nobukti
                                                                                          ,$bayar_date_create
                                                                                          ,$jenis_transaksi
                                                                                          ,$cetak
																						  ,$jual_kwitansi_id3);
                                        
                                    }elseif($jrawat_card_nilai3<>'' && $jrawat_card_nilai3<>0){
                                        $result_bayar3 = $this->cara_bayar_card_insert($jrawat_card_nama3
                                                                                      ,$jrawat_card_edc3
                                                                                      ,$jrawat_card_no3
                                                                                      ,$jrawat_card_nilai3
                                                                                      ,$jrawat_nobukti
                                                                                      ,$bayar_date_create
                                                                                      ,$jenis_transaksi
                                                                                      ,$cetak);
                                    }elseif($jrawat_cek_nilai3<>'' && $jrawat_cek_nilai3<>0){
                                        $result_bayar3 = $this->cara_bayar_cek_insert($jrawat_cek_nama3
                                                                                     ,$jrawat_cek_no3
                                                                                     ,$jrawat_cek_valid3
                                                                                     ,$jrawat_cek_bank3
                                                                                     ,$jrawat_cek_nilai3
                                                                                     ,$jrawat_nobukti
                                                                                     ,$bayar_date_create
                                                                                     ,$jenis_transaksi
                                                                                     ,$cetak);
                                    }elseif($jrawat_transfer_nilai3<>'' && $jrawat_transfer_nilai3<>0){
                                        $result_bayar3 = $this->cara_bayar_transfer_insert($jrawat_transfer_bank3
                                                                                          ,$jrawat_transfer_nama3
                                                                                          ,$jrawat_transfer_nilai3
                                                                                          ,$jrawat_nobukti
                                                                                          ,$bayar_date_create
                                                                                          ,$jenis_transaksi
                                                                                          ,$cetak);
                                    }elseif($jrawat_tunai_nilai3<>'' && $jrawat_tunai_nilai3<>0){
                                        $result_bayar3 = $this->cara_bayar_tunai_insert($jrawat_tunai_nilai3
                                                                                       ,$jrawat_nobukti
                                                                                       ,$bayar_date_create
                                                                                       ,$jenis_transaksi
                                                                                       ,$cetak);
                                    }elseif($jrawat_voucher_cashback3<>'' && $jrawat_voucher_cashback3<>0){
                                        $result_bayar3 = $this->cara_bayar_voucher_insert($jrawat_voucher_no3
                                                                                         ,$jrawat_nobukti
                                                                                         ,$jrawat_voucher_cashback3
                                                                                         ,$bayar_date_create
                                                                                         ,$jenis_transaksi
                                                                                         ,$cetak);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
	
	function get_akun_master_kasbank(){
		$sql="select A.* from akun A,akun_map M
				WHERE A.akun_kode not in (
				SELECT B.akun_parent_kode FROM akun B WHERE B.akun_parent_kode is NOT NULL)
				AND A.akun_kode LIKE concat('%', M.map_akun_kode,'%')";
				
	}
	
	function get_piutang_cust_list($query,$start,$end){
		$sql = "SELECT master_lunas_piutang.lpiutang_id
				,master_lunas_piutang.lpiutang_cust
				,cust_nama
				,cust_no
				,sum(lpiutang_total) AS lpiutang_total
				,(
sum(master_lunas_piutang.lpiutang_total)-
IFNULL((select sum(master_faktur_lunas_piutang.fpiutang_bayar)
from master_faktur_lunas_piutang
where master_faktur_lunas_piutang.fpiutang_stat_dok <> 'Batal'
and master_faktur_lunas_piutang.fpiutang_cust=master_lunas_piutang.lpiutang_cust
group by master_faktur_lunas_piutang.fpiutang_cust),0)-
(
IFNULL((select SUM(detail_retur_jual_produk.drproduk_jumlah *
detail_retur_jual_produk.drproduk_harga)
from detail_retur_jual_produk
left join master_retur_jual_produk
on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
where
master_retur_jual_produk.rproduk_stat_dok = 'Tertutup'
and master_retur_jual_produk.rproduk_cust=master_lunas_piutang.lpiutang_cust
and master_retur_jual_produk.rproduk_tanggal >= '2014-01-01'
group by master_retur_jual_produk.rproduk_cust),0)
)
) as lpiutang_sisa

				,sum(lpiutang_sisa) as lpiutang_sisa22
			FROM master_lunas_piutang 
	
				LEFT JOIN customer ON(cust_id=master_lunas_piutang.lpiutang_cust)
				WHERE master_lunas_piutang.lpiutang_stat_dok <> 'Batal' ";
		
		if($query<>""){
			$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql .= " (cust_nama LIKE '%".addslashes($query)."%' OR cust_no LIKE '%".addslashes($query)."%' )";
		}
		$sql.=" GROUP BY lpiutang_cust ";
		
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
	
	function get_hutang_list($query,$start,$end){
		/*$sql = "SELECT master_lunas_piutang.lpiutang_id
				,master_lunas_piutang.lpiutang_cust
				,cust_nama
				,cust_no
				,sum(lpiutang_total) AS lpiutang_total
				,sum(lpiutang_sisa) as lpiutang_sisa
			FROM master_lunas_piutang 
				LEFT JOIN vu_piutang_total_lunas ON(vu_piutang_total_lunas.dpiutang_master=master_lunas_piutang.lpiutang_id)
				LEFT JOIN customer ON(cust_id=master_lunas_piutang.lpiutang_cust)
				WHERE master_lunas_piutang.lpiutang_stat_dok = 'Terbuka' AND lpiutang_faktur_tanggal > '2010-07-20' ";
				*/
		
		$sql = "SELECT hutang.hutang_id
				,hutang.hutang_supplier
				,supplier_nama, supplier_alamat
				,sum(hutang_total) AS hutang_total
				/*,(sum(hutang_total)) - ifnull(sum(vu_piutang_total_lunas.total_pelunasan),0) AS lpiutang_sisa*/
				,sum(hutang_sisa) as hutang_sisa22
				,(
sum(hutang.hutang_total)-
IFNULL((select sum(master_lunas_hutang.fhutang_bayar)
from master_lunas_hutang
where master_lunas_hutang.fhutang_stat_dok <> 'Batal'
and master_lunas_hutang.fhutang_cust=hutang.hutang_supplier
group by master_lunas_hutang.fhutang_cust),0)-
(
IFNULL((select SUM(detail_retur_beli.drbeli_jumlah *
detail_retur_beli.drbeli_harga)
from detail_retur_beli
left join master_retur_beli
on master_retur_beli.rbeli_id = detail_retur_beli.drbeli_master
where
master_retur_beli.rbeli_status = 'Tertutup'
and master_retur_beli.rbeli_supplier=hutang.hutang_supplier
group by master_retur_beli.rbeli_supplier),0)
)
) as hutang_sisa


			FROM hutang 
				/*LEFT JOIN vu_piutang_total_lunas ON(vu_piutang_total_lunas.dpiutang_master=master_lunas_piutang.lpiutang_id)*/
				LEFT JOIN supplier ON(supplier.supplier_id=hutang.hutang_supplier)
				WHERE hutang_sisa <> 0 and hutang_status = 'Hutang'";
		
		if($query<>""){
			$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
			$sql .= " (supplier_nama LIKE '%".addslashes($query)."%' OR supplier_alamat LIKE '%".addslashes($query)."%' )";
		}
		$sql.=" GROUP BY hutang_supplier ";
		
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
	

	function customer_crm_generator_create_all($query, $crmvalue_id, $crmvalue_cust, $crmvalue_date, $crmvalue_frequency, $crmvalue_recency, $crmvalue_spending, $crmvalue_highmargin, $crmvalue_referal, $crmvalue_kerewelan, $crmvalue_disiplin, $crmvalue_treatment, $crmvalue_author,$cust_id ,$cust_no, $cust_no_awal ,$cust_no_akhir ,$cust_nama ,$cust_kelamin ,$cust_alamat ,$cust_alamat2 ,$cust_kota ,$cust_kodepos ,$cust_propinsi ,$cust_negara ,$cust_telprumah ,$cust_telprumah2 ,$cust_telpkantor ,$cust_hp ,$cust_hp2 ,$cust_hp3 ,$cust_email ,$cust_agama ,$cust_pendidikan ,$cust_profesi ,$cust_tgllahir ,$cust_tgllahirend,$cust_referensi ,$cust_keterangan ,$cust_member ,$cust_member2, $cust_terdaftar ,$cust_tglawaltrans, $cust_statusnikah , $cust_priority , $cust_jmlanak ,$cust_unit ,$cust_aktif, $sortby,$cust_fretfulness,$cust_creator ,$cust_date_create ,$cust_update ,$cust_date_update ,$cust_revised ,$option,$filter, $cust_umurstart, $cust_umurend, $cust_umur,$cust_tgl, $cust_bulan, $cust_bb, $cust_tgldaftarend, $cust_tglawaltransend,$cust_referensilain, $setcrm_frequency_count, $setcrm_frequency_days, $setcrm_frequency_value_lessthan, $setcrm_frequency_value_equal, $setcrm_frequency_value_morethan, $setcrm_recency_days, $setcrm_recency_value_lessthan, $setcrm_recency_value_morethan,	$setcrm_spending_days, $setcrm_spending_value_lessthan, $setcrm_spending_value_equal, $setcrm_spending_value_morethan, $setcrm_highmargin_treatment, $setcrm_highmargin_days, $setcrm_highmargin_value_morethan, $setcrm_highmargin_value_equal, $setcrm_highmargin_value_lessthan, $setcrm_referal_person, $setcrm_referal_days, $setcrm_referal_morethan, $setcrm_referal_equal, $setcrm_referal_lessthan, $setcrm_kerewelan_high, $setcrm_kerewelan_normal, $setcrm_kerewelan_low, $setcrm_disiplin_days, $setcrm_disiplin_persentase_pembatalan, $setcrm_disiplin_batal_value_morethan,$setcrm_disiplin_batal_value_lessthan, $setcrm_disiplin_persentase_telat, $setcrm_disiplin_menit_telat, $setcrm_disiplin_telat_value_morethan, $setcrm_disiplin_telat_value_lessthan, $setcrm_treatment_days, $setcrm_treatment_nonmedis, $setcrm_treatment_medis, $setcrm_treatment_morethan, $setcrm_treatment_equal, $setcrm_treatment_lessthan, $setcrm_result_nilai_atas, $setcrm_result_nilai_bawah){
	
	$datetime_now=date('Y-m-d H:i:s');
		
	$query="select cust_id from vu_customer";

			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (cust_id LIKE '%".addslashes($filter)."%' OR cust_no LIKE '%".addslashes($filter)."%' OR cust_nama LIKE '%".addslashes($filter)."%' OR cust_kelamin LIKE '%".addslashes($filter)."%' OR cust_alamat LIKE '%".addslashes($filter)."%' OR cust_alamat2 LIKE '%".addslashes($filter)."%' OR cust_kota LIKE '%".addslashes($filter)."%' OR cust_kodepos LIKE '%".addslashes($filter)."%' OR cust_propinsi LIKE '%".addslashes($filter)."%' OR cust_negara LIKE '%".addslashes($filter)."%' OR cust_telprumah LIKE '%".addslashes($filter)."%' OR cust_telprumah2 LIKE '%".addslashes($filter)."%' OR cust_telpkantor LIKE '%".addslashes($filter)."%' OR cust_hp LIKE '%".addslashes($filter)."%' OR cust_hp2 LIKE '%".addslashes($filter)."%' OR cust_hp3 LIKE '%".addslashes($filter)."%' OR cust_email LIKE '%".addslashes($filter)."%' OR cust_agama LIKE '%".addslashes($filter)."%' OR cust_pendidikan LIKE '%".addslashes($filter)."%' OR cust_profesi LIKE '%".addslashes($filter)."%' OR cust_tgllahir LIKE '%"./*addslashes($filter)."%' OR cust_hobi LIKE '%".*/addslashes($filter)."%' OR cust_referensi LIKE '%".addslashes($filter)."%' OR cust_keterangan LIKE '%".addslashes($filter)."%' OR cust_member LIKE '%".addslashes($filter)."%' OR cust_terdaftar LIKE '%".addslashes($filter)."%' OR cust_tglawaltrans LIKE '%".addslashes($filter)."%' OR cust_statusnikah LIKE '%"./*addslashes($filter)."%' OR cust_priority LIKE '%".*/addslashes($filter)."%' OR cust_jmlanak LIKE '%".addslashes($filter)."%'  OR cust_unit LIKE '%".addslashes($filter)."%' OR cust_aktif LIKE '%".addslashes($filter)."%' OR cust_creator LIKE '%".addslashes($filter)."%' OR cust_date_create LIKE '%".addslashes($filter)."%' OR cust_update LIKE '%".addslashes($filter)."%' OR cust_date_update LIKE '%".addslashes($filter)."%' OR cust_revised LIKE '%".addslashes($filter)."%')";
				//$result = $this->db->query($query);
			} 
			else if($option=='SEARCH'){
					if($cust_id!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_id LIKE '%".$cust_id."%'";
					};
					if($cust_no!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_no LIKE '%".$cust_no."%'";
					};
					if($cust_no_awal!=''){
							$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
							$query.= " right(cust_no,6) BETWEEN '".$cust_no_awal."' AND '".$cust_no_akhir."'";
						};
					if($cust_nama!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_nama LIKE '%".$cust_nama."%'";
					};
					if($cust_kelamin!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_kelamin LIKE '%".$cust_kelamin."%'";
					};
					if($cust_alamat!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_alamat LIKE '%".$cust_alamat."%' OR cust_alamat2 LIKE '%".$cust_alamat."%'";
					};
					if($cust_kota!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_kota LIKE '%".$cust_kota."%'";
					};
					if($cust_kodepos!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_kodepos LIKE '%".$cust_kodepos."%'";
					};
					if($cust_propinsi!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_propinsi LIKE '%".$cust_propinsi."%'";
					};
					if($cust_negara!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_negara LIKE '%".$cust_negara."%'";
					};
					if($cust_telprumah!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_telprumah LIKE '%".$cust_telprumah."%' OR cust_telprumah2 LIKE '%".$cust_telprumah."%' OR cust_telpkantor LIKE '%".$cust_telprumah."%' ";
					};
					if($cust_bb!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_bb LIKE '%".$cust_bb."%'";
					};
					if($cust_email!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_email LIKE '%".$cust_email."%'  OR cust_email2 LIKE '%".$cust_email."%'   ";
					};
					if($cust_agama!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_agama LIKE '%".$cust_agama."%'";
					};
					if($cust_pendidikan!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_pendidikan LIKE '%".$cust_pendidikan."%'";
					};
					if($cust_profesi!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_profesi LIKE '%".$cust_profesi."%'";
					};
					
					/*if($cust_tgllahir!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_tgllahir BETWEEN '".$cust_tgllahir."' AND '".$cust_tgllahirend."'";
					};	*/
					
					if($cust_tgllahir!='' or $cust_tgllahirend!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						
						if($cust_tgllahir!='' and $cust_tgllahirend!=''){
							$query.= " cust_tgllahir BETWEEN '".$cust_tgllahir."' AND '".$cust_tgllahirend."'";
						}else if ($cust_tgllahir!='' and $cust_tgllahirend==''){
							$query.= " cust_tgllahir BETWEEN '".$cust_tgllahir."' AND now()";
						}else if ($cust_tgllahir=='' and $cust_tgllahirend!=''){
							$query.= " cust_tgllahir < '".$cust_tgllahirend."'";
						}
					};
					
					if($cust_tgl!='' and $cust_bulan!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " day(cust_tgllahir)='".$cust_tgl."' AND month(cust_tgllahir)='".$cust_bulan."'";
					};
					
					if($cust_umurstart!='' or $cust_umurend!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						
						if($cust_umurstart!='' and $cust_umurend!=''){
							$query.= " (year(now())-year(cust_tgllahir)) BETWEEN '".$cust_umurstart."' AND '".$cust_umurend."'";
						}else if ($cust_umurstart!='' and $cust_umurend==''){
							$query.= " (year(now())-year(cust_tgllahir)) > '".$cust_umurstart."'";
						}else if ($cust_umurstart=='' and $cust_umurend!=''){
							$query.= " (year(now())-year(cust_tgllahir)) < '".$cust_umurend."'";
						}
					};	
					
					if($cust_referensi!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_referensi LIKE '%".$cust_referensi."%'";
					};
					if($cust_referensilain!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_referensilain LIKE '%".$cust_referensilain."%'";
					};
					if($cust_keterangan!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_keterangan LIKE '%".$cust_keterangan."%'";
					};
					if($cust_member!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_member LIKE '%".$cust_member."%'";
					};
					if($cust_member2!=''){
						$date_now = date('Y-m-d');
						if($cust_member2=='Semua'){
							$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
							$query.= " cust_member <> ''";
						} 
						else if($cust_member2=='Aktif'){
							$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
							$query.= " member_valid > '".$date_now."'";
						}
						else if($cust_member2=='Tidak Aktif'){
							$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
							$query.= " member_valid < '".$date_now."'";
						}
						else if($cust_member2=='Non Member'){
							$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
							$query.= " cust_member = ''";
						}				
					};
					/*if($cust_terdaftar!='' or $cust_tgldaftarend!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_terdaftar BETWEEN '".$cust_terdaftar."' AND '".$cust_tgldaftarend."'";						
					};*/
					
					if($cust_terdaftar!='' or $cust_tgldaftarend!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						
						if($cust_terdaftar!='' and $cust_tgldaftarend!=''){
							$query.= " cust_terdaftar BETWEEN '".$cust_terdaftar."' AND '".$cust_tgldaftarend."'";
						}else if ($cust_terdaftar!='' and $cust_tgldaftarend==''){
							$query.= " cust_terdaftar BETWEEN '".$cust_terdaftar."' AND now()";
						}else if ($cust_terdaftar=='' and $cust_tgldaftarend!=''){
							$query.= " cust_terdaftar < '".$cust_tgldaftarend."'";
						}
						
					};
					
					if($cust_tglawaltrans!='' or $cust_tglawaltransend!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						
						if($cust_tglawaltrans!='' and $cust_tglawaltransend!=''){
							$query.= " cust_tglawaltrans BETWEEN '".$cust_tglawaltrans."' AND '".$cust_tglawaltransend."'";
						}else if ($cust_tglawaltrans!='' and $cust_tglawaltransend==''){
							$query.= " cust_tglawaltrans BETWEEN '".$cust_tglawaltrans."' AND now()";
						}else if ($cust_tglawaltrans=='' and $cust_tglawaltransend!=''){
							$query.= " cust_tglawaltrans < '".$cust_tglawaltransend."'";
						}
						
					};
								
					if($cust_statusnikah!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_statusnikah='".$cust_statusnikah."'";
					};
					if($cust_priority!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_priority='".$cust_priority."'";
					};
					if($cust_jmlanak!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_jmlanak LIKE '%".$cust_jmlanak."%'";
					};
					if($cust_unit!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_unit LIKE '%".$cust_unit."%'";
					};
					if($cust_aktif!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_aktif LIKE '%".$cust_aktif."%'";
					};
					/*if($cust_fretfulness!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_fretfulness LIKE '%".$cust_fretfulness."%'";
					};*/
					if($sortby=='Nama'){
						$query.=eregi("WHERE",$query)?" ":" WHERE ";
						$query.= " ORDER BY cust_nama";
					};
					if($sortby=='No Cust'){
						$query.=eregi("WHERE",$query)?" ":" WHERE ";
						$query.= " ORDER BY cust_no";
					};
					if($sortby=='Alamat'){
						$query.=eregi("WHERE",$query)?" ":" WHERE ";
						$query.= " ORDER BY cust_alamat";
					};
					if($sortby=='Tgl Lahir'){
						$query.=eregi("WHERE",$query)?" ":" WHERE ";
						$query.= " ORDER BY cust_tgllahir";
					};
					if($sortby=='Telp Rmh'){
						$query.=eregi("WHERE",$query)?" ":" WHERE ";
						$query.= " ORDER BY cust_telprumah";
					};
					if($sortby=='Handphone'){
						$query.=eregi("WHERE",$query)?" ":" WHERE ";
						$query.= " ORDER BY cust_hp";
					};
					if($cust_creator!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_creator LIKE '%".$cust_creator."%'";
					};
					if($cust_date_create!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_date_create LIKE '%".$cust_date_create."%'";
					};
					if($cust_update!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_update LIKE '%".$cust_update."%'";
					};
					if($cust_date_update!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_date_update LIKE '%".$cust_date_update."%'";
					};
					if($cust_revised!=''){
						$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
						$query.= " cust_revised LIKE '%".$cust_revised."%'";
					};
					
					$query.= " order by cust_id desc ";
				}
			
				$rs = $this->db->query($query);
				$datetime_now = date('Y-m-d H:i:s');
				
				$ret = 0;
				$jum_baris = $rs->num_rows();
				
				//looping dimulai disini
				if ($jum_baris <= 100) {
					for ($i = 0; $i < $jum_baris; $i++) {
						$record = $rs->row($i);
						$data_arr= $record->cust_id;				
						$ret = 1;
						$this->customer_crm_generator_create($query, $crmvalue_id, $data_arr, $crmvalue_date, $crmvalue_frequency, $crmvalue_recency, $crmvalue_spending, $crmvalue_highmargin, $crmvalue_referal, $crmvalue_kerewelan, $crmvalue_disiplin, $crmvalue_treatment, $crmvalue_author, $setcrm_frequency_count, $setcrm_frequency_days, $setcrm_frequency_value_lessthan, $setcrm_frequency_value_equal, $setcrm_frequency_value_morethan, $setcrm_recency_days, $setcrm_recency_value_lessthan, $setcrm_recency_value_morethan,	$setcrm_spending_days, $setcrm_spending_value_lessthan, $setcrm_spending_value_equal, $setcrm_spending_value_morethan, $setcrm_highmargin_treatment, $setcrm_highmargin_days, $setcrm_highmargin_value_morethan, $setcrm_highmargin_value_equal, $setcrm_highmargin_value_lessthan, $setcrm_referal_person, $setcrm_referal_days, $setcrm_referal_morethan, $setcrm_referal_equal, $setcrm_referal_lessthan, $setcrm_kerewelan_high, $setcrm_kerewelan_normal, $setcrm_kerewelan_low, $setcrm_disiplin_days, $setcrm_disiplin_persentase_pembatalan, $setcrm_disiplin_batal_value_morethan,$setcrm_disiplin_batal_value_lessthan, $setcrm_disiplin_persentase_telat, $setcrm_disiplin_menit_telat, $setcrm_disiplin_telat_value_morethan, $setcrm_disiplin_telat_value_lessthan, $setcrm_treatment_days, $setcrm_treatment_nonmedis, $setcrm_treatment_medis, $setcrm_treatment_morethan, $setcrm_treatment_equal, $setcrm_treatment_lessthan, $setcrm_result_nilai_atas, $setcrm_result_nilai_bawah);
					}
					if ($ret == 1)
						return '1';
					else
						return '0';
				}
				else
					return 2;
	}
	
	//function for CRM_setup
		function CRM_setup(){
		
		//untuk mendapatkan parameter di crm_setup
			$sql_parameter = 
			   "select max(setcrm_id) as setcrm_id, 
					c.setcrm_frequency_count, c.setcrm_frequency_days, 
					c.setcrm_frequency_value_morethan, c.setcrm_frequency_value_equal, c.setcrm_frequency_value_lessthan,
					c.setcrm_recency_days, c.setcrm_recency_value_morethan, c.setcrm_recency_value_lessthan, 
					c.setcrm_spending_days, c.setcrm_spending_value_lessthan, c.setcrm_spending_value_equal, c.setcrm_spending_value_morethan,
					c.setcrm_highmargin_treatment, c.setcrm_highmargin_days, 
					c.setcrm_highmargin_value_morethan, c.setcrm_highmargin_value_equal, c.setcrm_highmargin_value_lessthan,
					c.setcrm_referal_person, c.setcrm_referal_days, c.setcrm_referal_morethan, c.setcrm_referal_equal, c.setcrm_referal_lessthan,
					c.setcrm_kerewelan_high, c.setcrm_kerewelan_normal, c.setcrm_kerewelan_low,
					c.setcrm_result_nilai_atas, c.setcrm_result_nilai_bawah,
					c.setcrm_disiplin_days, c.setcrm_disiplin_persentase_pembatalan, 
					c.setcrm_disiplin_batal_value_morethan, c.setcrm_disiplin_batal_value_lessthan,
					c.setcrm_disiplin_persentase_telat, c.setcrm_disiplin_menit_telat, c.setcrm_disiplin_telat_value_morethan, c.setcrm_disiplin_telat_value_lessthan,
					c.setcrm_treatment_days, c.setcrm_treatment_nonmedis, c.setcrm_treatment_medis, c.setcrm_treatment_morethan, c.setcrm_treatment_equal, c.setcrm_treatment_lessthan
				from crm_setup c";
			
			$query_parameter				= $this->db->query($sql_parameter);
			$data_parameter 				= $query_parameter->row();
			
			$hasil		= array($data_parameter->setcrm_frequency_count, $data_parameter->setcrm_frequency_days, $data_parameter->setcrm_frequency_value_lessthan, $data_parameter->setcrm_frequency_value_equal, $data_parameter->setcrm_frequency_value_morethan, $data_parameter->setcrm_recency_days, $data_parameter->setcrm_recency_value_lessthan, $data_parameter->setcrm_recency_value_morethan, $data_parameter->setcrm_spending_days, $data_parameter->setcrm_spending_value_lessthan, $data_parameter->setcrm_spending_value_equal, $data_parameter->setcrm_spending_value_morethan, $data_parameter->setcrm_highmargin_treatment, $data_parameter->setcrm_highmargin_days, $data_parameter->setcrm_highmargin_value_morethan, $data_parameter->setcrm_highmargin_value_equal, $data_parameter->setcrm_highmargin_value_lessthan, $data_parameter->setcrm_referal_person, $data_parameter->setcrm_referal_days, $data_parameter->setcrm_referal_morethan, $data_parameter->setcrm_referal_equal, $data_parameter->setcrm_referal_lessthan, $data_parameter->setcrm_kerewelan_high, $data_parameter->setcrm_kerewelan_normal, $data_parameter->setcrm_kerewelan_low, $data_parameter->setcrm_disiplin_days, $data_parameter->setcrm_disiplin_persentase_pembatalan, $data_parameter->setcrm_disiplin_batal_value_morethan, $data_parameter->setcrm_disiplin_batal_value_lessthan, $data_parameter->setcrm_disiplin_persentase_telat, $data_parameter->setcrm_disiplin_menit_telat, $data_parameter->setcrm_disiplin_telat_value_morethan, $data_parameter->setcrm_disiplin_telat_value_lessthan, $data_parameter->setcrm_treatment_days, $data_parameter->setcrm_treatment_nonmedis, $data_parameter->setcrm_treatment_medis, $data_parameter->setcrm_treatment_morethan, $data_parameter->setcrm_treatment_equal, $data_parameter->setcrm_treatment_lessthan, $data_parameter->setcrm_result_nilai_atas, $data_parameter->setcrm_result_nilai_bawah);
			
			return array($hasil);		

		}

	//function for generate value CRM
		function customer_crm_generator_create($query, $crmvalue_id, $crmvalue_cust, $crmvalue_date, $crmvalue_frequency, $crmvalue_recency, $crmvalue_spending, $crmvalue_highmargin, $crmvalue_referal, $crmvalue_kerewelan, $crmvalue_disiplin, $crmvalue_treatment, $crmvalue_author, $setcrm_frequency_count, $setcrm_frequency_days, $setcrm_frequency_value_lessthan, $setcrm_frequency_value_equal, $setcrm_frequency_value_morethan, $setcrm_recency_days, $setcrm_recency_value_lessthan, $setcrm_recency_value_morethan,	$setcrm_spending_days, $setcrm_spending_value_lessthan, $setcrm_spending_value_equal, $setcrm_spending_value_morethan, $setcrm_highmargin_treatment, $setcrm_highmargin_days, $setcrm_highmargin_value_morethan, $setcrm_highmargin_value_equal, $setcrm_highmargin_value_lessthan, $setcrm_referal_person, $setcrm_referal_days, $setcrm_referal_morethan, $setcrm_referal_equal, $setcrm_referal_lessthan, $setcrm_kerewelan_high, $setcrm_kerewelan_normal, $setcrm_kerewelan_low, $setcrm_disiplin_days, $setcrm_disiplin_persentase_pembatalan, $setcrm_disiplin_batal_value_morethan,$setcrm_disiplin_batal_value_lessthan, $setcrm_disiplin_persentase_telat, $setcrm_disiplin_menit_telat, $setcrm_disiplin_telat_value_morethan, $setcrm_disiplin_telat_value_lessthan, $setcrm_treatment_days, $setcrm_treatment_nonmedis, $setcrm_treatment_medis, $setcrm_treatment_morethan, $setcrm_treatment_equal, $setcrm_treatment_lessthan, $setcrm_result_nilai_atas, $setcrm_result_nilai_bawah){
			$datetime_now=date('Y-m-d H:i:s');
			
			//parameter di crm_setup didapatkan dari function CRM_setup
		
	
			//UNTUK MENGHITUNG FREQUENCY:
			//selalu sesuaikan query dengan query di m_lap_kunjungan.php
			
			$sql_value_kunjungan_by_freq =
			   "select 
					sum(jum_total) as tot_kunjungan_cust
				from
				   (select 
						count(distinct cust) as jum_total
					from
						(
						select 
							m.jrawat_cust as cust,
							m.jrawat_tanggal as tgl_tindakan
						from detail_jual_rawat d
						left join master_jual_rawat m on (d.drawat_master = m.jrawat_id)
						left join perawatan p on (d.drawat_rawat=p.rawat_id)
						where 
							m.jrawat_stat_dok = 'Tertutup' and m.jrawat_bayar <> 0  
							and (p.rawat_kategori = 2 or p.rawat_kategori = 3 or p.rawat_kategori = 4 or p.rawat_kategori = 16)
							and date_add(m.jrawat_tanggal, interval '$setcrm_frequency_days' day) >= date_format(now(), '%Y-%m-%d') and m.jrawat_cust = '$crmvalue_cust'
						
						union
						
						select 
							m.jproduk_cust as cust,
							m.jproduk_tanggal as tgl_tindakan
						from master_jual_produk m
						where 
							m.jproduk_stat_dok = 'Tertutup' and m.jproduk_bayar <> 0 
							and date_add(m.jproduk_tanggal, interval '$setcrm_frequency_days' day) >= date_format(now(), '%Y-%m-%d') and m.jproduk_cust = '$crmvalue_cust'
												
						union
						
						select 
							d.dapaket_cust as cust,
							d.dapaket_tgl_ambil as tgl_tindakan
						from detail_ambil_paket d
						left join perawatan p on (d.dapaket_item = p.rawat_id)
						where 
							(p.rawat_kategori = 2 or p.rawat_kategori = 3 or p.rawat_kategori = 4 or p.rawat_kategori = 16)
							and d.dapaket_stat_dok = 'Tertutup' 							
							and date_add(d.dapaket_tgl_ambil, interval '$setcrm_frequency_days' day) >= date_format(now(), '%Y-%m-%d') and d.dapaket_cust = '$crmvalue_cust'
						
						)
						as table_union2
						
					group by tgl_tindakan
					)
				as tabel
			";
			   

			$query_kunjungan_by_freq	= $this->db->query($sql_value_kunjungan_by_freq);
			$data_kunjungan_by_freq		= $query_kunjungan_by_freq->row();
			$tot_kunjungan_cust_by_freq	= $data_kunjungan_by_freq->tot_kunjungan_cust;
			
			if ($tot_kunjungan_cust_by_freq <= $setcrm_frequency_count){
				$crmvalue_frequency = $setcrm_frequency_value_lessthan;
			}
			else if ($tot_kunjungan_cust_by_freq == $setcrm_frequency_count){
				$crmvalue_frequency = $setcrm_frequency_value_equal;
			}
			else if ($tot_kunjungan_cust_by_freq >= $setcrm_frequency_count){
				$crmvalue_frequency = $setcrm_frequency_value_morethan;
			}
			
			
			//UNTUK MENGHITUNG RECENCY:
			
			$sql_value_recency = 
			   "select dapaket_id as id
				from detail_ambil_paket d
				where date_add(d.dapaket_tgl_ambil, interval '$setcrm_recency_days' day) >= date_format(now(), '%Y-%m-%d') and dapaket_cust = '$crmvalue_cust'
				
				union
				
				select d2.drawat_id as id
				from detail_jual_rawat d2
				left join master_jual_rawat m2 on m2.jrawat_id = d2.drawat_master
				where date_add(m2.jrawat_tanggal, interval '$setcrm_recency_days' day) >= date_format(now(), '%Y-%m-%d') and m2.jrawat_cust = '$crmvalue_cust'

				union
				
				select d3.dproduk_id as id
				from detail_jual_produk d3
				left join master_jual_produk m3 on m3.jproduk_id = d3.dproduk_master
				where date_add(m3.jproduk_tanggal, interval '$setcrm_recency_days' day) >= date_format(now(), '%Y-%m-%d') and m3.jproduk_cust = '$crmvalue_cust'
				";
			$query_recency	= $this->db->query($sql_value_recency);
			$recency_row 	= $query_recency->num_rows();
			
			if($recency_row==0){
				$crmvalue_recency = $setcrm_recency_value_lessthan;
			}
			else if($recency_row>=1){
				$crmvalue_recency = $setcrm_recency_value_morethan;
			}
			
			
			//UNTUK MENGHITUNG SPENDING:
			
			//menghitung Total Spending ALL-->(selalu sesuaikan dg m_report_rekap_penjualan)
			//Spending Produk:
			$sql_spending_produk_all = 
			   "SELECT 
					(SUM((d.dproduk_jumlah * d.dproduk_harga)-((d.dproduk_jumlah * d.dproduk_harga)*d.dproduk_diskon/100)) - 
					SUM((m.jproduk_diskon *((d.dproduk_jumlah * d.dproduk_harga)-((d.dproduk_jumlah * d.dproduk_harga)*d.dproduk_diskon/100))) /100)) -
					IFNULL 
					  ((SELECT SUM(dr.drproduk_jumlah*dr.drproduk_harga) 
						FROM detail_retur_jual_produk dr
						LEFT JOIN master_retur_jual_produk mr ON dr.drproduk_master = mr.rproduk_id
						WHERE 
							date_add(mr.rproduk_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND mr.rproduk_stat_dok = 'Tertutup' ),0) 
					AS tot_net
				FROM detail_jual_produk d
				LEFT JOIN master_jual_produk m ON d.dproduk_master = m.jproduk_id
				WHERE
					date_add(m.jproduk_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND m.jproduk_stat_dok = 'Tertutup'";
			
			$query_spending_produk_all	= $this->db->query($sql_spending_produk_all);
			$data_spending_produk_all	= $query_spending_produk_all->row();
			$jum_spending_produk_all	= $data_spending_produk_all->tot_net;
			
			//Spending Perawatan:
			$sql_spending_perawatan_all = 
			   "SELECT 
					(SUM((d.drawat_jumlah * d.drawat_harga)-((d.drawat_jumlah * d.drawat_harga)*d.drawat_diskon/100)) - 
						SUM((M.jrawat_diskon *((d.drawat_jumlah * d.drawat_harga)-((d.drawat_jumlah * d.drawat_harga)*d.drawat_diskon/100))) /100)) AS grand_total
				FROM detail_jual_rawat d
				LEFT JOIN master_jual_rawat M ON d.drawat_master = M.jrawat_id
				LEFT JOIN perawatan ON d.drawat_rawat = perawatan.rawat_id
				LEFT JOIN kategori ON perawatan.rawat_kategori = kategori.kategori_id
				WHERE
					date_add(m.jrawat_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND m.jrawat_stat_dok = 'Tertutup'";
			
			$query_spending_perawatan_all	= $this->db->query($sql_spending_perawatan_all);
			$data_spending_perawatan_all	= $query_spending_perawatan_all->row();
			$jum_spending_perawatan_all		= $data_spending_perawatan_all->grand_total;
			
			//Spending Pengambilan Paket:
			 $sql_spending_apaket_all =
			   "select 
					SUM(d.dapaket_jumlah * 
						(((((dj.dpaket_harga * (100 - dj.dpaket_diskon) / 100) * dj.dpaket_jumlah) - 
						(((m.jpaket_diskon * (dj.dpaket_harga * (100 - dj.dpaket_diskon) / 100)) * dj.dpaket_jumlah) / 100)) / dj.dpaket_jumlah) / v.isi_paket) -
						(((((m.jpaket_diskon * (dj.dpaket_harga * (100 - dj.dpaket_diskon) / 100)) * dj.dpaket_jumlah) / 100)) / dj.dpaket_jumlah) / v.isi_paket) AS tot_net
				from detail_ambil_paket d
				join master_jual_paket m on m.jpaket_id = d.dapaket_jpaket 
				left join vu_jumlah_isi_paket v on d.dapaket_paket = v.paket_id
				left join detail_jual_paket dj on d.dapaket_dpaket = dj.dpaket_id
				WHERE
					date_add(d.dapaket_tgl_ambil, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND d.dapaket_stat_dok = 'Tertutup'";
			
			$query_spending_apaket_all	= $this->db->query($sql_spending_apaket_all);
			$data_spending_apaket_all	= $query_spending_apaket_all->row();
			$jum_spending_apaket_all	= $data_spending_apaket_all->tot_net;
			
			//Total Spending (Produk + Perawatan + Pengambilan Paket):
			$tot_spending_all	= $jum_spending_produk_all + $jum_spending_perawatan_all + $jum_spending_apaket_all;
			
			//menghitung Total Kunjungan ALL
			//selalu sesuaikan query dengan query di m_lap_kunjungan.php
			$sql_value_kunjungan_all =
			   "select 
					sum(jum_total) as tot_kunjungan_all
				from
				   (select 
						count(distinct cust) as jum_total
					from
						(
						select 
							m.jrawat_cust as cust,
							m.jrawat_tanggal as tgl_tindakan
						from detail_jual_rawat d
						left join master_jual_rawat m on (d.drawat_master = m.jrawat_id)
						left join perawatan p on (d.drawat_rawat=p.rawat_id)
						where 
							m.jrawat_stat_dok = 'Tertutup' and m.jrawat_bayar <> 0  
							and (p.rawat_kategori = 2 or p.rawat_kategori = 3 or p.rawat_kategori = 4 or p.rawat_kategori = 16)
							and date_add(m.jrawat_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d')
						
						union
						
						select 
							m.jproduk_cust as cust,
							m.jproduk_tanggal as tgl_tindakan
						from master_jual_produk m
						where 
							m.jproduk_stat_dok = 'Tertutup' and m.jproduk_bayar <> 0 
							and date_add(m.jproduk_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d')
												
						union
						
						select 
							d.dapaket_cust as cust,
							d.dapaket_tgl_ambil as tgl_tindakan
						from detail_ambil_paket d
						left join perawatan p on (d.dapaket_item = p.rawat_id)
						where 
							(p.rawat_kategori = 2 or p.rawat_kategori = 3 or p.rawat_kategori = 4 or p.rawat_kategori = 16)
							and d.dapaket_stat_dok = 'Tertutup' 							
							and date_add(d.dapaket_tgl_ambil, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d')
						
						)
						as table_union2
						
					group by tgl_tindakan
			)
			as tabel
			";
			   
			$query_kunjungan_all	= $this->db->query($sql_value_kunjungan_all);
			$data_kunjungan_all		= $query_kunjungan_all->row();
			$tot_kunjungan_all		= $data_kunjungan_all->tot_kunjungan_all;
			
			if (($tot_kunjungan_all == 0) or ($tot_kunjungan_all == null)) {
				$tot_kunjungan_all	= 0;
				$spending_avg 		= 0;
			} 
			else {
				$spending_avg 		= $tot_spending_all / $tot_kunjungan_all;
			}
			
			
			
			//menghitung Total Spending CUST ybs-->(selalu sesuaikan dg m_report_rekap_penjualan)
			//Spending Produk:
			$sql_spending_produk_cust = 
			   "SELECT 
					(SUM((d.dproduk_jumlah * d.dproduk_harga)-((d.dproduk_jumlah * d.dproduk_harga)*d.dproduk_diskon/100)) - 
					SUM((m.jproduk_diskon *((d.dproduk_jumlah * d.dproduk_harga)-((d.dproduk_jumlah * d.dproduk_harga)*d.dproduk_diskon/100))) /100)) -
					IFNULL 
					  ((SELECT SUM(dr.drproduk_jumlah*dr.drproduk_harga) 
						FROM detail_retur_jual_produk dr
						LEFT JOIN master_retur_jual_produk mr ON dr.drproduk_master = mr.rproduk_id
						WHERE 
							date_add(mr.rproduk_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND mr.rproduk_cust = '$crmvalue_cust' AND
							mr.rproduk_stat_dok = 'Tertutup' ),0) 
					AS tot_net
				FROM detail_jual_produk d
				LEFT JOIN master_jual_produk m ON d.dproduk_master = m.jproduk_id
				WHERE
					date_add(m.jproduk_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND m.jproduk_cust = '$crmvalue_cust' AND
					m.jproduk_stat_dok = 'Tertutup'";
			
			$query_spending_produk_cust	= $this->db->query($sql_spending_produk_cust);
			$data_spending_produk_cust	= $query_spending_produk_cust->row();
			$jum_spending_produk_cust	= $data_spending_produk_cust->tot_net;
			
			//Spending Perawatan:
			$sql_spending_perawatan_cust = 
			   "SELECT 
					(SUM((d.drawat_jumlah * d.drawat_harga)-((d.drawat_jumlah * d.drawat_harga)*d.drawat_diskon/100)) - 
						SUM((M.jrawat_diskon *((d.drawat_jumlah * d.drawat_harga)-((d.drawat_jumlah * d.drawat_harga)*d.drawat_diskon/100))) /100)) AS grand_total
				FROM detail_jual_rawat d
				LEFT JOIN master_jual_rawat M ON d.drawat_master = M.jrawat_id
				LEFT JOIN perawatan ON d.drawat_rawat = perawatan.rawat_id
				LEFT JOIN kategori ON perawatan.rawat_kategori = kategori.kategori_id
				WHERE
					date_add(m.jrawat_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND m.jrawat_cust = '$crmvalue_cust' AND
					m.jrawat_stat_dok = 'Tertutup'";
			
			$query_spending_perawatan_cust	= $this->db->query($sql_spending_perawatan_cust);
			$data_spending_perawatan_cust	= $query_spending_perawatan_cust->row();
			$jum_spending_perawatan_cust	= $data_spending_perawatan_cust->grand_total;
			
			//Spending Pengambilan Paket:
			 $sql_spending_apaket_cust =
			   "select 
					SUM(d.dapaket_jumlah * 
						(((((dj.dpaket_harga * (100 - dj.dpaket_diskon) / 100) * dj.dpaket_jumlah) - 
						(((m.jpaket_diskon * (dj.dpaket_harga * (100 - dj.dpaket_diskon) / 100)) * dj.dpaket_jumlah) / 100)) / dj.dpaket_jumlah) / v.isi_paket) -
						(((((m.jpaket_diskon * (dj.dpaket_harga * (100 - dj.dpaket_diskon) / 100)) * dj.dpaket_jumlah) / 100)) / dj.dpaket_jumlah) / v.isi_paket) AS tot_net
				from detail_ambil_paket d
				join master_jual_paket m on m.jpaket_id = d.dapaket_jpaket 
				left join vu_jumlah_isi_paket v on d.dapaket_paket = v.paket_id
				left join detail_jual_paket dj on d.dapaket_dpaket = dj.dpaket_id
				WHERE
					date_add(d.dapaket_tgl_ambil, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') AND m.jpaket_cust = '$crmvalue_cust' AND
					d.dapaket_stat_dok = 'Tertutup'";
			
			$query_spending_apaket_cust	= $this->db->query($sql_spending_apaket_cust);
			$data_spending_apaket_cust	= $query_spending_apaket_cust->row();
			$jum_spending_apaket_cust	= $data_spending_apaket_cust->tot_net;
			
			//Total Spending (Produk + Perawatan + Pengambilan Paket):
			if ($jum_spending_produk_cust == null) {$jum_spending_produk_cust = 0;}
			if ($jum_spending_perawatan_cust == null) {$jum_spending_perawatan_cust = 0;}
			if ($jum_spending_apaket_cust == null) {$jum_spending_apaket_cust = 0;}
			
			$tot_spending_cust	= $jum_spending_produk_cust + $jum_spending_perawatan_cust + $jum_spending_apaket_cust;
			
			//menghitung Total Kunjungan CUST
			//selalu sesuaikan query dengan query di m_lap_kunjungan.php
			$sql_value_kunjungan_cust =
			   "select 
					sum(jum_total) as tot_kunjungan_cust
				from
				   (select 
						count(distinct cust) as jum_total
					from
						(
						select 
							m.jrawat_cust as cust,
							m.jrawat_tanggal as tgl_tindakan
						from detail_jual_rawat d
						left join master_jual_rawat m on (d.drawat_master = m.jrawat_id)
						left join perawatan p on (d.drawat_rawat=p.rawat_id)
						where 
							m.jrawat_stat_dok = 'Tertutup' and m.jrawat_bayar <> 0  
							and (p.rawat_kategori = 2 or p.rawat_kategori = 3 or p.rawat_kategori = 4 or p.rawat_kategori = 16)
							and date_add(m.jrawat_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') and m.jrawat_cust = '$crmvalue_cust'
						
						union
						
						select 
							m.jproduk_cust as cust,
							m.jproduk_tanggal as tgl_tindakan
						from master_jual_produk m
						where 
							m.jproduk_stat_dok = 'Tertutup' and m.jproduk_bayar <> 0 
							and date_add(m.jproduk_tanggal, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') and m.jproduk_cust = '$crmvalue_cust'
												
						union
						
						select 
							d.dapaket_cust as cust,
							d.dapaket_tgl_ambil as tgl_tindakan
						from detail_ambil_paket d
						left join perawatan p on (d.dapaket_item = p.rawat_id)
						where 
							(p.rawat_kategori = 2 or p.rawat_kategori = 3 or p.rawat_kategori = 4 or p.rawat_kategori = 16)
							and d.dapaket_stat_dok = 'Tertutup' 							
							and date_add(d.dapaket_tgl_ambil, interval '$setcrm_spending_days' day) >= date_format(now(), '%Y-%m-%d') and d.dapaket_cust = '$crmvalue_cust'
						
						)
						as table_union2
						
					group by tgl_tindakan
			)
			as tabel
			";
			   
			$query_kunjungan_cust	= $this->db->query($sql_value_kunjungan_cust);
			$data_kunjungan_cust	= $query_kunjungan_cust->row();
			$tot_kunjungan_cust		= $data_kunjungan_cust->tot_kunjungan_cust;
			
			if (($tot_kunjungan_cust == 0) or ($tot_kunjungan_cust == null)) {
				$tot_kunjungan_cust	= 0;
				$spending_avg_cust 	= 0;
			} 
			else {
				$spending_avg_cust	= $tot_spending_cust / $tot_kunjungan_cust;
			}

			if ($spending_avg_cust > $spending_avg) {
				$crmvalue_spending = $setcrm_spending_value_morethan;
			}
			else if ($spending_avg_cust == $spending_avg) {
				$crmvalue_spending = $setcrm_spending_value_equal;
			}
			else if ($spending_avg_cust < $spending_avg) {
				$crmvalue_spending = $setcrm_spending_value_lessthan;
			}
			
			
						//UNTUK MENENTUKAN HIGH MARGIN TX
			$sql_value_highmargin =
			   "select 
					sum(jum_total) as tot_highmargin
				from
				   (select 
						count(distinct cust) as jum_total
					from
						(
						select 
							m.jrawat_cust as cust,
							m.jrawat_tanggal as tgl_tindakan
						from detail_jual_rawat d
						left join master_jual_rawat m on (d.drawat_master = m.jrawat_id)
						left join perawatan p on (d.drawat_rawat=p.rawat_id)
						left join produk_group g1 on g1.group_id = p.rawat_group
						where 
							m.jrawat_stat_dok = 'Tertutup' and m.jrawat_bayar <> 0 and p.rawat_highmargin = 1 and
							date_add(m.jrawat_tanggal, interval '$setcrm_highmargin_days' day) >= date_format(now(), '%Y-%m-%d') and m.jrawat_cust = '$crmvalue_cust'
						
						union
												
						select 
							d.dapaket_cust as cust,
							d.dapaket_tgl_ambil as tgl_tindakan
						from detail_ambil_paket d
						left join perawatan p on (d.dapaket_item = p.rawat_id)
						left join produk_group g1 on g1.group_id = p.rawat_group
						where 
							d.dapaket_stat_dok = 'Tertutup' and p.rawat_highmargin = 1 and
							date_add(d.dapaket_tgl_ambil, interval '$setcrm_highmargin_days' day) >= date_format(now(), '%Y-%m-%d') and d.dapaket_cust = '$crmvalue_cust'
						
						)
						as table_union2
						
					group by tgl_tindakan
			)
			as tabel";
			

			$query_highmargin	= $this->db->query($sql_value_highmargin);
			$data_highmargin	= $query_highmargin->row();
			$tot_highmargin		= $data_highmargin->tot_highmargin;
			
			if ($tot_highmargin == null) {$tot_highmargin = 0;}
			
			if ($tot_highmargin > $setcrm_highmargin_treatment){
				$crmvalue_highmargin = $setcrm_highmargin_value_morethan;
			}
			else if ($tot_highmargin == $setcrm_highmargin_treatment){
				$crmvalue_highmargin = $setcrm_highmargin_value_equal;
			}
			else {
				$crmvalue_highmargin = $setcrm_highmargin_value_lessthan;
			}
			
			
			//UNTUK MENGHITUNG REFERAL RATE:
			
			$sql_value_referal = 
			   "select count(c.cust_id) as jum_referal
			    from customer c
				where date_add(c.cust_terdaftar, interval '$setcrm_referal_days' day) >= date_format(now(), '%Y-%m-%d') and c.cust_referensi = '$crmvalue_cust'
				";
			$query_referal	= $this->db->query($sql_value_referal);
			$data_referal	= $query_referal->row();
			$jum_referal	= $data_referal->jum_referal;
			
			
			if ($jum_referal > $setcrm_referal_person) {
				$crmvalue_referal = $setcrm_referal_morethan;
			}
			else if ($jum_referal == $setcrm_referal_person) {
				$crmvalue_referal = $setcrm_referal_equal;
			}
			else if ($jum_referal < $setcrm_referal_person) {
				$crmvalue_referal = $setcrm_referal_lessthan;
			}
			
			
			//UNTUK MENGHITUNG FRETFULNESSS
			
			$sql_value_fretfulness = 
			   "SELECT cust_fretfulness FROM customer WHERE cust_id = '$crmvalue_cust'";
			$query_fretfulness	= $this->db->query($sql_value_fretfulness);
			$data_fretfulness	= $query_fretfulness->row();
			$cust_fretfulness	= $data_fretfulness->cust_fretfulness;
			
			if ($cust_fretfulness == 'High') {
				$crmvalue_fretfulness = $setcrm_kerewelan_high;
			}
			else if (($cust_fretfulness == 'Medium') or ($cust_fretfulness == 'Undefined')) {
				$crmvalue_fretfulness = $setcrm_kerewelan_normal;
			}
			else if ($cust_fretfulness == 'Low') {
				$crmvalue_fretfulness = $setcrm_kerewelan_low;
			}
				
			
			//UNTUK MENGHITUNG DISIPLIN: BATAL
			
			$sql_value_app_batal = 
			   "SELECT count(d.dapp_id) as total_app_batal
				FROM appointment_detail d
				LEFT JOIN appointment a on a.app_id = d.dapp_master
				WHERE 
					d.dapp_status = 'batal' AND 
					date_add(a.app_tanggal, interval '$setcrm_disiplin_days' day) >= date_format(now(), '%Y-%m-%d') AND a.app_customer = '$crmvalue_cust'";
			$query_app_batal 	= $this->db->query($sql_value_app_batal);
			$data_app_batal		= $query_app_batal->row();
			$total_app_batal	= $data_app_batal->total_app_batal;

			$sql_value_app_all = 
			   "SELECT count(d.dapp_id) as total_app_all
				FROM appointment_detail d
				LEFT JOIN appointment a on a.app_id = d.dapp_master
				WHERE 
					date_add(a.app_tanggal, interval '$setcrm_disiplin_days' day) >= date_format(now(), '%Y-%m-%d') AND a.app_customer = '$crmvalue_cust'";
			$query_app_all 		= $this->db->query($sql_value_app_all);
			$data_app_all		= $query_app_all->row();
			$total_app_all		= $data_app_all->total_app_all;
			
			if ($total_app_all == 0) {	//menghindari division by zero
				$crmvalue_disiplin_batal = $setcrm_disiplin_batal_value_morethan;
			} else {
				if (($total_app_batal / $total_app_all) >= ($setcrm_disiplin_persentase_pembatalan / 100)) {
					$crmvalue_disiplin_batal = $setcrm_disiplin_batal_value_morethan;
				}
				else {
					$crmvalue_disiplin_batal = $setcrm_disiplin_batal_value_lessthan;
				}				
			}
						
			
			//UNTUK MENGHITUNG DISIPLIN: TELAT
			
			$sql_value_app_dtg_telat = 
			   "SELECT count(d.dapp_id) as total_app_dtg_telat
				FROM appointment_detail d
				LEFT JOIN appointment a on a.app_id = d.dapp_master
				WHERE 
					d.dapp_status = 'datang' AND time_to_sec(timediff(d.dapp_jamdatang, d.dapp_jamreservasi)) > ($setcrm_disiplin_menit_telat * 60) AND
					date_add(a.app_tanggal, interval '$setcrm_disiplin_days' day) >= date_format(now(), '%Y-%m-%d') AND a.app_customer = '$crmvalue_cust'";
			$query_app_dtg_telat 	= $this->db->query($sql_value_app_dtg_telat);
			$data_app_dtg_telat		= $query_app_dtg_telat->row();
			$total_app_dtg_telat	= $data_app_dtg_telat->total_app_dtg_telat;

			$sql_value_app_dtg_all = 
			   "SELECT count(d.dapp_id) as total_app_dtg_all
				FROM appointment_detail d
				LEFT JOIN appointment a on a.app_id = d.dapp_master
				WHERE 
					d.dapp_status = 'datang' AND
					date_add(a.app_tanggal, interval '$setcrm_disiplin_days' day) >= date_format(now(), '%Y-%m-%d') AND a.app_customer = '$crmvalue_cust'";
			$query_app_dtg_all 		= $this->db->query($sql_value_app_dtg_all);
			$data_app_dtg_all		= $query_app_dtg_all->row();
			$total_app_dtg_all		= $data_app_dtg_all->total_app_dtg_all;
			
			if ($total_app_dtg_all == 0) { //menghindari division by zero
				$crmvalue_disiplin_telat = $setcrm_disiplin_telat_value_morethan;
			} else {
				if (($total_app_dtg_telat / $total_app_dtg_all) >= ($setcrm_disiplin_persentase_telat / 100)) {
					$crmvalue_disiplin_telat = $setcrm_disiplin_telat_value_morethan;
				}
				else {
					$crmvalue_disiplin_telat = $setcrm_disiplin_telat_value_lessthan;
				}				
			}
			
			
			//UNTUK MENGHITUNG JUMLAH TREATMENT UTAMA
			//selalu sesuaikan query dengan query di m_lap_kunjungan.php
			
			//medis
			$sql_value_tx_utama_medis =
			   "select 
					sum(jum_total) as tot_tx_utama_medis
				from
				   (select 
						count(distinct cust) as jum_total
					from
						(
						select 
							m.jrawat_cust as cust,
							m.jrawat_tanggal as tgl_tindakan
						from detail_jual_rawat d
						left join master_jual_rawat m on (d.drawat_master = m.jrawat_id)
						left join perawatan p on (d.drawat_rawat=p.rawat_id)
						left join produk_group g1 on g1.group_id = p.rawat_group
						where 
							m.jrawat_stat_dok = 'Tertutup' and m.jrawat_bayar <> 0  and g1.group_treatment_utama = 1 and
							(p.rawat_kategori = 2 or p.rawat_kategori = 4 or p.rawat_kategori = 16) and
							date_add(m.jrawat_tanggal, interval '$setcrm_treatment_days' day) >= date_format(now(), '%Y-%m-%d') and m.jrawat_cust = '$crmvalue_cust'
						
						union
												
						select 
							d.dapaket_cust as cust,
							d.dapaket_tgl_ambil as tgl_tindakan
						from detail_ambil_paket d
						left join perawatan p on (d.dapaket_item = p.rawat_id)
						left join produk_group g1 on g1.group_id = p.rawat_group
						where 
							d.dapaket_stat_dok = 'Tertutup' and g1.group_treatment_utama = 1 and
							(p.rawat_kategori = 2 or p.rawat_kategori = 4 or p.rawat_kategori = 16) and
							date_add(d.dapaket_tgl_ambil, interval '$setcrm_treatment_days' day) >= date_format(now(), '%Y-%m-%d') and d.dapaket_cust = '$crmvalue_cust'
						
						)
						as table_union2
						
					group by tgl_tindakan
			)
			as tabel";
			
			$query_tx_utama_medis	= $this->db->query($sql_value_tx_utama_medis);
			$data_tx_utama_medis	= $query_tx_utama_medis->row();
			$tot_tx_utama_medis		= $data_tx_utama_medis->tot_tx_utama_medis;
			
			//medis
			$sql_value_tx_utama_non_medis =
			   "select 
					sum(jum_total) as tot_tx_utama_non_medis
				from
				   (select 
						count(distinct cust) as jum_total
					from
						(
						select 
							m.jrawat_cust as cust,
							m.jrawat_tanggal as tgl_tindakan
						from detail_jual_rawat d
						left join master_jual_rawat m on (d.drawat_master = m.jrawat_id)
						left join perawatan p on (d.drawat_rawat=p.rawat_id)
						left join produk_group g1 on g1.group_id = p.rawat_group
						where 
							m.jrawat_stat_dok = 'Tertutup' and m.jrawat_bayar <> 0  and g1.group_treatment_utama = 1 and
							p.rawat_kategori = 3 and
							date_add(m.jrawat_tanggal, interval '$setcrm_treatment_days' day) >= date_format(now(), '%Y-%m-%d') and m.jrawat_cust = '$crmvalue_cust'
						
						union
												
						select 
							d.dapaket_cust as cust,
							d.dapaket_tgl_ambil as tgl_tindakan
						from detail_ambil_paket d
						left join perawatan p on (d.dapaket_item = p.rawat_id)
						left join produk_group g1 on g1.group_id = p.rawat_group
						where 
							d.dapaket_stat_dok = 'Tertutup' and g1.group_treatment_utama = 1 and
							p.rawat_kategori = 3 and
							date_add(d.dapaket_tgl_ambil, interval '$setcrm_treatment_days' day) >= date_format(now(), '%Y-%m-%d') and d.dapaket_cust = '$crmvalue_cust'
						
						)
						as table_union2
						
					group by tgl_tindakan
			)
			as tabel";
			

			$query_tx_utama_non_medis	= $this->db->query($sql_value_tx_utama_non_medis);
			$data_tx_utama_non_medis	= $query_tx_utama_non_medis->row();
			$tot_tx_utama_non_medis		= $data_tx_utama_non_medis->tot_tx_utama_non_medis;
			
			if (($tot_tx_utama_non_medis > $setcrm_treatment_nonmedis) and ($tot_tx_utama_medis > $setcrm_treatment_medis)){
				$crmvalue_treatment = $setcrm_treatment_morethan;
			}
			else if (($tot_tx_utama_non_medis == $setcrm_treatment_nonmedis) and ($tot_tx_utama_medis == $setcrm_treatment_medis)){
				$crmvalue_treatment = $setcrm_treatment_equal;
			}
			else {
				$crmvalue_treatment = $setcrm_treatment_lessthan;
			}
			

			//UNTUK MENENTUKAN PRIORITY
			$crmvalue_total   = $crmvalue_frequency + $crmvalue_recency + $crmvalue_spending + $crmvalue_highmargin + $crmvalue_referal + $crmvalue_fretfulness +
								$crmvalue_disiplin_batal + $crmvalue_disiplin_telat + $crmvalue_treatment;
			
			if ($crmvalue_total > $setcrm_result_nilai_atas){
				$crmvalue_priority = 'Core';
			}
			else if (($crmvalue_total <= $setcrm_result_nilai_atas) and ($crmvalue_total >= $setcrm_result_nilai_bawah)){
				$crmvalue_priority = 'Medium';
			}
			else if ($crmvalue_total < $setcrm_result_nilai_bawah){
				$crmvalue_priority = 'Low';
			}
			
			if ($total_app_all == 0) {$crmvalue_disiplin_batal_real = 0;} else {$crmvalue_disiplin_batal_real = ($total_app_batal / $total_app_all);}
			if ($total_app_dtg_all == 0) {$crmvalue_disiplin_telat_real = 0;} else {$crmvalue_disiplin_telat_real = ($total_app_dtg_telat / $total_app_dtg_all);}					
			$data=array(
				"crmvalue_frequency"			=> $crmvalue_frequency,
				"crmvalue_frequency_real"		=> $tot_kunjungan_cust_by_freq,
				"crmvalue_recency"				=> $crmvalue_recency,
				"crmvalue_recency_real"			=> $recency_row,
				"crmvalue_spending"				=> $crmvalue_spending,	
				"crmvalue_spending_real_rp"		=> $tot_spending_cust,	
				"crmvalue_spending_real_kunj"	=> $tot_kunjungan_cust,		
				"crmvalue_highmargin"			=> $crmvalue_highmargin,
				"crmvalue_highmargin_real"		=> $tot_highmargin,
				"crmvalue_referal"				=> $crmvalue_referal,
				"crmvalue_referal_real"			=> $jum_referal,
				"crmvalue_kerewelan"			=> $crmvalue_fretfulness,
				"crmvalue_kerewelan_real"		=> $cust_fretfulness,
				"crmvalue_disiplin_batal"		=> $crmvalue_disiplin_batal,
				"crmvalue_disiplin_batal_real"	=> $crmvalue_disiplin_batal_real,
				"crmvalue_disiplin_telat"		=> $crmvalue_disiplin_telat,
				"crmvalue_disiplin_telat_real"	=> $crmvalue_disiplin_telat_real,
				"crmvalue_treatment"				=> $crmvalue_treatment,
				"crmvalue_treatment_medis_real"		=> $tot_tx_utama_medis,
				"crmvalue_treatment_non_medis_real"	=> $tot_tx_utama_non_medis,
				"crmvalue_priority"				=> $crmvalue_priority,
				"crmvalue_total"				=> $crmvalue_total,
				"crmvalue_cust"					=> $crmvalue_cust,	
				"crmvalue_date"					=> $crmvalue_date,
				"crmvalue_author"				=> $_SESSION[SESSION_USERID]
			);
			$this->db->insert('crm_value',$data);
			
			
			
			
			if($this->db->affected_rows()){
				$sql_cust_priority = 
				   "UPDATE customer c
					SET c.cust_crm_value = 
					   (select max(crm.crmvalue_id)
						from crm_value crm
						WHERE crm.crmvalue_cust = '$crmvalue_cust')
					WHERE cust_id = '$crmvalue_cust'
					";
				$query_cust_priority	= $this->db->query($sql_cust_priority);				
				return '1';
			}
			else
				return '0';
				
				
			//UPDATE PRIORITY KE CUSTOMER
			$sql_cust_priority = 
			   "UPDATE customer c
				SET c.cust_crm_value = 
				   (select max(crm.crmvalue_id)
					from crm_value crm
					WHERE crm.crmvalue_cust = '$crmvalue_cust')
				WHERE cust_id = '$crmvalue_cust'
				";
			$query_cust_priority	= $this->db->query($sql_cust_priority);
			
		}
	

	// Function utk mengetahui stok akhir ketika membuat OP 
	function generate_stok_akhir($produk_id, $tanggal_start){

			//if ($periode == 'bulan'){
				//$tanggal_start	= $tahun.'-'.$bulan.'-01';
				//$tanggal_end	= $tahun.'-'.$bulan.'-'.cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); //mengetahui jumlah hari dlm bulan itu
			//}
			
			//DELETE ALL REPORT
			$sql="DELETE FROM stok_mutasi
					WHERE date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
					AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
					AND gudang_id=2";
						
			$result=$this->db->query($sql);

			//if($opsi_satuan=='terkecil')
				//$sql="SELECT * FROM vu_produk_satuan_terkecil WHERE produk_aktif='Aktif'";
			//else
				$sql="SELECT * FROM vu_produk_satuan_default WHERE produk_aktif='Aktif'";

			//if($opsi_produk=='group1' & $group1_id!=="" & $group1_id!==0){
				//$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
				//$sql.="	produk_group='".$group1_id."' ";
			//}elseif($opsi_produk=='produk' & $produk_id!=="" & $produk_id!==0){
				$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
				$sql.="	produk_id='".$produk_id."' ";
			//}

			//$this->firephp->log($sql);

			$sql.=" GROUP BY produk_kode ASC";
			$result=$this->db->query($sql) or die("ERROR-0 : ".$sql);;

			//INSERT KE TEMPORARY STOK_MUTASI
			if($result->num_rows()){

				//if($opsi_satuan=='terkecil'){
					//$sqlinsert="INSERT INTO stok_mutasi(produk_id,satuan_id,gudang_id,tanggal_awal,tanggal_akhir)
								//SELECT 	produk_id,satuan_id,".$gudang.",date_format('".$tanggal_start."','%Y-%m-%d'),
									//	date_format('".$tanggal_end."','%Y-%m-%d')
							//	FROM vu_produk_satuan_terkecil WHERE produk_aktif='Aktif'";
				//}
				//else{
					$sqlinsert="INSERT INTO stok_mutasi(produk_id,satuan_id,gudang_id,tanggal_awal,tanggal_akhir)
								SELECT 	produk_id,satuan_id,2,date_format('2012-03-01','%Y-%m-%d'),
										date_format('".$tanggal_start."','%Y-%m-%d')
								FROM vu_produk_satuan_default WHERE produk_aktif='Aktif'";

				//}

				//if($opsi_produk=='group1' & $group1_id!=="" & $group1_id!==0){
					//$sqlinsert.=eregi("WHERE",$sql)?" AND ":" WHERE ";
					//$sqlinsert.="	produk_group='".$group1_id."' ";
				//}elseif($opsi_produk=='produk' & $produk_id!=="" & $produk_id!==0){
					$sqlinsert.=eregi("WHERE",$sql)?" AND ":" WHERE ";
					$sqlinsert.="	produk_id='".$produk_id."' ";
				//}

				//$this->firephp->log($sqlinsert);

				$sqlinsert.=" GROUP BY produk_kode ASC";
				$this->db->query($sqlinsert) or die("ERROR-1 : ".$sqlinsert);

			}


			foreach($result->result() as $rowproduk){

			//Stok Awal
			$sqlawal="SELECT 	produk_saldo_awal*konversi_nilai/".$rowproduk->konversi_nilai." as jumlah
						FROM 	produk, satuan_konversi
						WHERE 	konversi_produk=produk_id
						AND 	konversi_default=true
						AND		produk_id='".$rowproduk->produk_id."'";
			//$this->firephp->log($sqlawal);

			$rsawal=$this->db->query($sqlawal) or die("ERROR-1.1 : ".$sqlawal);
			if($rsawal->num_rows()){
				$row=$rsawal->row();
				$sqlupdate="UPDATE stok_mutasi SET stok_awal='".$row->jumlah."'
							WHERE produk_id='".$rowproduk->produk_id."'
							AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
							AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
							AND gudang_id='1'";

				//$this->firephp->log($sqlupdate);

				$this->db->query($sqlupdate) or die("ERROR-2 : ".$sqlupdate);
			}


			//STOK AWAL
			$sql_stok_awal="SELECT ifnull(
							sum(jml_mutasi_masuk*konversi_nilai/".$rowproduk->konversi_nilai.")
							+ sum(jml_koreksi_stok*konversi_nilai/".$rowproduk->konversi_nilai.")
							- sum(jml_mutasi_keluar*konversi_nilai/".$rowproduk->konversi_nilai.")
							+ sum(jml_terima_barang*konversi_nilai/".$rowproduk->konversi_nilai.")
							+ sum(jml_terima_bonus*konversi_nilai/".$rowproduk->konversi_nilai.")";

			//if($gudang==1){
				//GUDANG UTAMA
			 	//$sql_stok_awal.="- sum(jml_retur_beli*konversi_nilai/".$rowproduk->konversi_nilai.")";
			//}

			//elseif($gudang==2){
				//GUDANG RETAIL
				$sql_stok_awal.="-sum(jml_jual_produk*konversi_nilai/".$rowproduk->konversi_nilai.")
								-sum(jml_jual_grooming*konversi_nilai/".$rowproduk->konversi_nilai.")
								-sum(jml_retur_beli*konversi_nilai/".$rowproduk->konversi_nilai.")
								+sum(jml_retur_produk*konversi_nilai/".$rowproduk->konversi_nilai.")
								+sum(jml_retur_paket*konversi_nilai/".$rowproduk->konversi_nilai.")";
			//}

			$sql_stok_awal.=" ,0) AS jumlah_awal
							FROM (
							  SELECT `mmm`.`mutasi_tanggal` AS `tanggal`,
								   `mmm`.`mutasi_asal` AS `asal`,
								   `mmm`.`mutasi_tujuan` AS `tujuan`,
								   `mmm`.`mutasi_tujuan` AS `gudang`,
								   `mmm`.`mutasi_no` AS `no_bukti`,
								   _UTF8 'mutasi' AS `jenis_transaksi`,
								   `mmm`.`mutasi_status` AS `status`,
								   `dmm`.`dmutasi_produk` AS `produk`,
								   `dmm`.`dmutasi_satuan` AS `satuan`,
								   konversi_nilai,
								   0 AS `jml_terima_barang`,
								   0 AS `jml_terima_bonus`,
								   0 AS `jml_retur_beli`,
								   `dmm`.`dmutasi_jumlah` AS `jml_mutasi_masuk`,
								   0 AS `jml_mutasi_keluar`,
								   0 AS `jml_koreksi_stok`,
								   0 AS `jml_jual_produk`,
								   0 AS `jml_jual_grooming`,
								   0 AS `jml_retur_produk`,
								   0 AS `jml_retur_paket`,
								   0 AS `jml_pakai_cabin`,
								   _UTF8 'mutasi masuk' AS `keterangan`,
								   `dmm`.`dmutasi_id` AS `detail_id`
							  FROM `master_mutasi` `mmm`, `detail_mutasi` `dmm`, satuan_konversi
							 WHERE     `dmm`.`dmutasi_master` = `mmm`.`mutasi_id`
								   AND konversi_satuan = dmm.dmutasi_satuan
								   AND konversi_produk = dmm.dmutasi_produk
									AND date_format(mutasi_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
									AND dmutasi_produk='".$rowproduk->produk_id."'
									AND mutasi_tujuan=2
									AND mutasi_status='Tertutup'
							UNION
							SELECT `mmk`.`mutasi_tanggal` AS `tanggal`,
								   `mmk`.`mutasi_asal` AS `asal`,
								   `mmk`.`mutasi_tujuan` AS `tujuan`,
								   `mmk`.`mutasi_asal` AS `gudang`,
								   `mmk`.`mutasi_no` AS `no_bukti`,
								   _UTF8 'mutasi' AS `jenis_transaksi`,
								   `mmk`.`mutasi_status` AS `status`,
								   `dmk`.`dmutasi_produk` AS `produk`,
								   `dmk`.`dmutasi_satuan` AS `satuan`,
								   konversi_nilai,
								   0 AS `jml_terima_barang`,
								   0 AS `jml_terima_bonus`,
								   0 AS `jml_retur_beli`,
								   0 AS `jml_mutasi_masuk`,
								   `dmk`.`dmutasi_jumlah` AS `jml_mutasi_keluar`,
								   0 AS `jml_koreksi_stok`,
								   0 AS `jml_jual_produk`,
								   0 AS `jml_jual_grooming`,
								   0 AS `jml_retur_produk`,
								   0 AS `jml_retur_paket`,
								   0 AS `jml_pakai_cabin`,
								   _UTF8 'mutasi keluar' AS `keterangan`,
								   `dmk`.`dmutasi_id` AS `detail_id`
							  FROM `master_mutasi` `mmk`, `detail_mutasi` `dmk`, satuan_konversi
							 WHERE     `dmk`.`dmutasi_master` = `mmk`.`mutasi_id`
								   AND konversi_satuan = dmk.dmutasi_satuan
								   AND konversi_produk = dmk.dmutasi_produk
									AND date_format(mutasi_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
									AND dmutasi_produk='".$rowproduk->produk_id."'
									AND mutasi_asal=2
									AND mutasi_status='Tertutup'
							UNION
							SELECT `mk`.`koreksi_tanggal` AS `tanggal`,
								   `mk`.`koreksi_gudang` AS `asal`,
								   `mk`.`koreksi_gudang` AS `tujuan`,
								   `mk`.`koreksi_gudang` AS `gudang`,
								   `mk`.`koreksi_no` AS `no_bukti`,
								   _UTF8 'koreksi' AS `jenis_transaksi`,
								   `mk`.`koreksi_status` AS `status`,
								   `dk`.`dkoreksi_produk` AS `produk`,
								   `dk`.`dkoreksi_satuan` AS `satuan`,
								   konversi_nilai,
								   0 AS `jml_terima_barang`,
								   0 AS `jml_terima_bonus`,
								   0 AS `jml_retur_beli`,
								   0 AS `jml_mutasi_masuk`,
								   0 AS `jml_mutasi_keluar`,
								   `dk`.`dkoreksi_jmlkoreksi` AS `jml_koreksi_stok`,
								   0 AS `jml_jual_produk`,
								   0 AS `jml_jual_grooming`,
								   0 AS `jml_retur_produk`,
								   0 AS `jml_retur_paket`,
								   0 AS `jml_pakai_cabin`,
								   _UTF8 'koreksi' AS `keterangan`,
								   `dk`.`dkoreksi_id` AS `detail_id`
							  FROM `master_koreksi_stok` `mk`,
								   `detail_koreksi_stok` `dk`,
								   satuan_konversi
							 WHERE     `mk`.`koreksi_id` = `dk`.`dkoreksi_master`
								   AND konversi_satuan = dk.dkoreksi_satuan
								   AND konversi_produk = dk.dkoreksi_produk
									AND date_format(koreksi_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
									AND dkoreksi_produk='".$rowproduk->produk_id."'
									AND koreksi_gudang=2
									AND koreksi_status='Tertutup'
							UNION SELECT `mt`.`terima_tanggal` AS `tanggal`,
										   `mt`.`terima_supplier` AS `asal`,
										   1 AS `tujuan`,
										   `mt`.`terima_gudang_id` AS `gudang`,
										   `mt`.`terima_no` AS `no_bukti`,
										   _UTF8 'PB' AS `jenis_transaksi`,
										   `mt`.`terima_status` AS `status`,
										   `dt`.`dterima_produk` AS `produk`,
										   `dt`.`dterima_satuan` AS `satuan`,
										   konversi_nilai,
										   `dt`.`dterima_jumlah` AS `jml_terima_barang`,
										   0 AS `jml_terima_bonus`,
										   0 AS `jml_retur_beli`,
										   0 AS `jml_mutasi_masuk`,
										   0 AS `jml_mutasi_keluar`,
										   0 AS `jml_koreksi_stok`,
										   0 AS `jml_jual_produk`,
										   0 AS `jml_jual_grooming`,
										   0 AS `jml_retur_produk`,
										   0 AS `jml_retur_paket`,
										   0 AS `jml_pakai_cabin`,
										   _UTF8 'beli' AS `keterangan`,
										   `dt`.`dterima_id` AS `detail_id`
									  FROM `detail_terima_beli` `dt`, `master_terima_beli` `mt`, satuan_konversi
									 WHERE     `dt`.`dterima_master` = `mt`.`terima_id`
											AND konversi_satuan = dt.dterima_satuan
											AND konversi_produk = dt.dterima_produk
											AND date_format(terima_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
											AND dterima_produk='".$rowproduk->produk_id."'
											AND `mt`.`terima_gudang_id`=2
											AND terima_status='Tertutup'
									UNION
									SELECT `mt`.`terima_tanggal` AS `tanggal`,
										   `mt`.`terima_supplier` AS `asal`,
										   1 AS `tujuan`,
										   `mt`.`terima_gudang_id` AS `gudang`,
										   `mt`.`terima_no` AS `no_bukti`,
										   _UTF8 'PB' AS `jenis_transaksi`,
										   `mt`.`terima_status` AS `status`,
										   `db`.`dtbonus_produk` AS `produk`,
										   `db`.`dtbonus_satuan` AS `satuan`,
										   konversi_nilai,
										   0 AS `jml_terima_barang`,
										   `db`.`dtbonus_jumlah` AS `jml_terima_bonus`,
										   0 AS `jml_retur_beli`,
										   0 AS `jml_mutasi_masuk`,
										   0 AS `jml_mutasi_keluar`,
										   0 AS `jml_koreksi_stok`,
										   0 AS `jml_jual_produk`,
										   0 AS `jml_jual_grooming`,
										   0 AS `jml_retur_produk`,
										   0 AS `jml_retur_paket`,
										   0 AS `jml_pakai_cabin`,
										   _UTF8 'bonus' AS `keterangan`,
										   `db`.`dtbonus_id` AS `detail_id`
									  FROM `detail_terima_bonus` `db`, `master_terima_beli` `mt`, satuan_konversi
									 WHERE     `db`.`dtbonus_master` = `mt`.`terima_id`
										   AND konversi_satuan = db.dtbonus_satuan
										   AND konversi_produk = db.dtbonus_produk
										   AND date_format(terima_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
											AND dtbonus_produk='".$rowproduk->produk_id."'
											AND `mt`.`terima_gudang_id`=2
											AND terima_status='Tertutup'";
				//if($gudang==1){
					//GUDANG UTAMA
					/*
					$sql_stok_awal.="
									UNION
									SELECT `mr`.`rbeli_tanggal` AS `tanggal`,
										   `mr`.`rbeli_supplier` AS `asal`,
										   1 AS `tujuan`,
										   1 AS `gudang`,
										   `mr`.`rbeli_nobukti` AS `no_bukti`,
										   _UTF8 'RB' AS `jenis_transaksi`,
										   `mr`.`rbeli_status` AS `status`,
										   `dr`.`drbeli_produk` AS `produk`,
										   `dr`.`drbeli_satuan` AS `satuan`,
										   konversi_nilai,
										   0 AS `jml_terima_barang`,
										   0 AS `jml_terima_bonus`,
										   `dr`.`drbeli_jumlah` AS `jml_retur_beli`,
										   0 AS `jml_mutasi_masuk`,
										   0 AS `jml_mutasi_keluar`,
										   0 AS `jml_koreksi_stok`,
										   0 AS `jml_jual_produk`,
										   0 AS `jml_jual_grooming`,
										   0 AS `jml_retur_produk`,
										   0 AS `jml_retur_paket`,
										   0 AS `jml_pakai_cabin`,
										   _UTF8 'retur' AS `keterangan`,
										   `dr`.`drbeli_id` AS `detail_id`
									  FROM `detail_retur_beli` `dr`, `master_retur_beli` `mr`, satuan_konversi
									 WHERE     `dr`.`drbeli_master` = `mr`.`rbeli_id`
										   AND konversi_satuan = dr.drbeli_satuan
										   AND konversi_produk = dr.drbeli_produk
											AND date_format(rbeli_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m-%d')
											AND drbeli_produk='".$rowproduk->produk_id."'
											AND 1='".$gudang."'
											AND rbeli_status='Tertutup'";
*/
				//}

			//	elseif($gudang==2){
					//GUDANG RETAIL
					$sql_stok_awal.="UNION
											SELECT `mj`.`jproduk_tanggal` AS `tanggal`,
												   2 AS `asal`,
												   `mj`.`jproduk_cust` AS `tujuan`,
												   2 AS `gudang`,
												   `mj`.`jproduk_nobukti` AS `no_bukti`,
												   _UTF8 'jual produk' AS `jenis_traksaksi`,
												   `mj`.`jproduk_stat_dok` AS `status`,
												   `dj`.`dproduk_produk` AS `produk`,
												   `dj`.`dproduk_satuan` AS `satuan`,
												   konversi_nilai,
												   0 AS `jml_terima_barang`,
												   0 AS `jml_terima_bonus`,
												   0 AS `jml_retur_beli`,
												   0 AS `jml_mutasi_masuk`,
												   0 AS `jml_mutasi_keluar`,
												   0 AS `jml_koreksi_stok`,
												   `dj`.`dproduk_jumlah` AS `jml_jual_produk`,
												   0 AS `jml_jual_grooming`,
												   0 AS `jml_retur_produk`,
												   0 AS `jml_retur_paket`,
												   0 AS `jml_pakai_cabin`,
												   _UTF8 'customer' AS `keterangan`,
												   `dj`.`dproduk_id` AS `detail_id`
											  FROM `master_jual_produk` `mj`, `detail_jual_produk` `dj`, satuan_konversi
											 WHERE     `dj`.`dproduk_master` = `mj`.`jproduk_id`
												   AND konversi_satuan = dj.dproduk_satuan
												   AND konversi_produk = dj.dproduk_produk
													AND date_format(jproduk_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
													AND dproduk_produk='".$rowproduk->produk_id."'
													AND 2=2
													AND jproduk_stat_dok='Tertutup'

											UNION

											SELECT `mjg`.`jpgrooming_tanggal` AS `tanggal`,
												   2 AS `asal`,
												   `mjg`.`jpgrooming_karyawan` AS `tujuan`,
												   2 AS `gudang`,
												   `mjg`.`jpgrooming_nobukti` AS `no_bukti`,
												   _UTF8 'jual produk' AS `jenis_transaksi`,
												   _UTF8 'Tertutup' AS `status`,
												   `djg`.`dpgrooming_produk` AS `produk`,
												   `djg`.`dpgrooming_satuan` AS `satuan`,
												   konversi_nilai,
												   0 AS `jml_terima_barang`,
												   0 AS `jml_terima_bonus`,
												   0 AS `jml_retur_beli`,
												   0 AS `jml_mutasi_masuk`,
												   0 AS `jml_mutasi_keluar`,
												   0 AS `jml_koreksi_stok`,
												   0 AS `jml_jual_produk`,
												   `djg`.`dpgrooming_jumlah` AS `jml_jual_grooming`,
												   0 AS `jml_retur_produk`,
												   0 AS `jml_retur_paket`,
												   0 AS `jml_pakai_cabin`,
												   _UTF8 'grooming' AS `keterangan`,
												   `djg`.`dpgrooming_id` AS `detail_id`
											  FROM `master_jualproduk_grooming` `mjg`,
												   `detail_jualproduk_grooming` `djg`,
												   satuan_konversi
											 WHERE     `mjg`.`jpgrooming_id` = `djg`.`dpgrooming_master`
												   AND konversi_satuan = djg.dpgrooming_satuan
												   AND konversi_produk = djg.dpgrooming_produk
													AND date_format(jpgrooming_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
													AND dpgrooming_produk='".$rowproduk->produk_id."'
													AND 2=2
											UNION
											SELECT `mrj`.`rproduk_tanggal` AS `tanggal`,
												   `mrj`.`rproduk_cust` AS `asal`,
												   2 AS `tujuan`,
												   2 AS `gudang`,
												   `mrj`.`rproduk_nobukti` AS `no_bukti`,
												   _UTF8 'retur jual' AS `jenis_transaksi`,
												   `mrj`.`rproduk_stat_dok` AS `status`,
												   `drj`.`drproduk_produk` AS `produk`,
												   `drj`.`drproduk_satuan` AS `satuan`,
												   konversi_nilai,
												   0 AS `jml_terima_barang`,
												   0 AS `jml_terima_bonus`,
												   0 AS `jml_retur_beli`,
												   0 AS `jml_mutasi_masuk`,
												   0 AS `jml_mutasi_keluar`,
												   0 AS `jml_koreksi_stok`,
												   0 AS `jml_jual_produk`,
												   0 AS `jml_jual_grooming`,
												   `drj`.`drproduk_jumlah` AS `jml_retur_produk`,
												   0 AS `jml_retur_paket`,
												   0 AS `jml_pakai_cabin`,
												   _UTF8 'produk retur' AS `keterangan`,
												   `drj`.`drproduk_id` AS `detail_id`
											  FROM `master_retur_jual_produk` `mrj`,
												   `detail_retur_jual_produk` `drj`,
												   satuan_konversi
											 WHERE     `mrj`.`rproduk_id` = `drj`.`drproduk_master`
												   AND konversi_satuan = drj.drproduk_satuan
												   AND konversi_produk = drj.drproduk_produk
													AND date_format(rproduk_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
													AND drproduk_produk='".$rowproduk->produk_id."'
													AND 2=2
													AND rproduk_stat_dok='Tertutup'
											UNION
											SELECT `mrp`.`rpaket_tanggal` AS `tanggal`,
												   `mrp`.`rpaket_cust` AS `asal`,
												   2 AS `tujuan`,
												   2 AS `gudang`,
												   `mrp`.`rpaket_nobukti` AS `no_bukti`,
												   _UTF8 'retur jual' AS `jenis_transaksi`,
												   `mrp`.`rpaket_stat_dok` AS `status`,
												   `drp`.`drpaket_produk` AS `produk`,
												   `drp`.`drpaket_satuan` AS `satuan`,
												   konversi_nilai,
												   0 AS `jml_terima_barang`,
												   0 AS `jml_terima_bonus`,
												   0 AS `jml_retur_beli`,
												   0 AS `jml_mutasi_masuk`,
												   0 AS `jml_mutasi_keluar`,
												   0 AS `jml_koreksi_stok`,
												   0 AS `jml_jual_produk`,
												   0 AS `jml_jual_grooming`,
												   0 AS `jml_retur_produk`,
												   `drp`.`drpaket_jumlah` AS `jml_retur_paket`,
												   0 AS `jml_pakai_cabin`,
												   _UTF8 'paket retur' AS `keterangan`,
												   `drp`.`drpaket_id` AS `detail_id`
											  FROM `master_retur_jual_paket` `mrp`,
												   `detail_retur_paket_produk` `drp`,
												   satuan_konversi
											 WHERE     `mrp`.`rpaket_id` = `drp`.`drpaket_master`
												   AND konversi_satuan = drpaket_satuan
												   AND konversi_produk = drpaket_produk
													AND date_format(rpaket_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
													AND drpaket_produk='".$rowproduk->produk_id."'
													AND 2=2
													AND rpaket_stat_dok='Tertutup'
													
											UNION
									SELECT `mr`.`rbeli_tanggal` AS `tanggal`,
										   `mr`.`rbeli_supplier` AS `asal`,
										   2 AS `tujuan`,
										   2 AS `gudang`,
										   `mr`.`rbeli_nobukti` AS `no_bukti`,
										   _UTF8 'RB' AS `jenis_transaksi`,
										   `mr`.`rbeli_status` AS `status`,
										   `dr`.`drbeli_produk` AS `produk`,
										   `dr`.`drbeli_satuan` AS `satuan`,
										   konversi_nilai,
										   0 AS `jml_terima_barang`,
										   0 AS `jml_terima_bonus`,
										   `dr`.`drbeli_jumlah` AS `jml_retur_beli`,
										   0 AS `jml_mutasi_masuk`,
										   0 AS `jml_mutasi_keluar`,
										   0 AS `jml_koreksi_stok`,
										   0 AS `jml_jual_produk`,
										   0 AS `jml_jual_grooming`,
										   0 AS `jml_retur_produk`,
										   0 AS `jml_retur_paket`,
										   0 AS `jml_pakai_cabin`,
										   _UTF8 'retur' AS `keterangan`,
										   `dr`.`drbeli_id` AS `detail_id`
									  FROM `detail_retur_beli` `dr`, `master_retur_beli` `mr`, satuan_konversi
									 WHERE     `dr`.`drbeli_master` = `mr`.`rbeli_id`
										   AND konversi_satuan = dr.drbeli_satuan
										   AND konversi_produk = dr.drbeli_produk
											AND date_format(rbeli_tanggal,'%Y-%m-%d')<date_format('".$tanggal_start."','%Y-%m')
											AND drbeli_produk='".$rowproduk->produk_id."'
											AND 2=2
											AND rbeli_status='Tertutup'";
				//}

				$sql_stok_awal.= ") as mutasi
									GROUP BY mutasi.produk
									ORDER BY mutasi.produk ";
				//$this->firephp->log($sql_stok_awal);

				$rstawal=$this->db->query($sql_stok_awal) or die("ERROR-3 : ".$sql_stok_awal);;
				if($rstawal->num_rows()){
					$row=$rstawal->row();
					$sqlupdate="UPDATE stok_mutasi SET stok_awal=stok_awal+'".$row->jumlah_awal."'
								WHERE produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";
					//$this->firephp->log($sql_stok_awal);

					$this->db->query($sqlupdate) or die("ERROR-4 : ".$sql_stok_awal);
				}

				//MUTASINYA DI SINI

				//MUTASI MASUK
				$sqlupdate="UPDATE stok_mutasi M SET stok_masuk=stok_masuk+ifnull((

						SELECT 	sum(dt.dmutasi_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as masuk
						  FROM `detail_mutasi` `dt`, `master_mutasi` `mt`, satuan_konversi, gudang gd
						 WHERE  `dt`.`dmutasi_master` = `mt`.`mutasi_id`
								AND mt.mutasi_asal=gd.gudang_id
								AND konversi_satuan = dt.dmutasi_satuan
								AND konversi_produk = dt.dmutasi_produk
								AND date_format(mutasi_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(mutasi_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND dmutasi_produk='".$rowproduk->produk_id."'
								AND mutasi_status='Tertutup'
								AND mutasi_tujuan=2
								AND M.produk_id=dt.dmutasi_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";

				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-8 : ".$sqlupdate);

				//MUTASI KELUAR
				$sqlupdate="UPDATE stok_mutasi M SET stok_keluar=stok_keluar+ifnull((

						SELECT 	sum(dt.dmutasi_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as keluar
						  FROM `detail_mutasi` `dt`, `master_mutasi` `mt`, satuan_konversi, gudang gd
						 WHERE  `dt`.`dmutasi_master` = `mt`.`mutasi_id`
								AND mt.mutasi_tujuan=gd.gudang_id
								AND konversi_satuan = dt.dmutasi_satuan
								AND konversi_produk = dt.dmutasi_produk
								AND date_format(mutasi_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(mutasi_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND dmutasi_produk='".$rowproduk->produk_id."'
								AND mutasi_status='Tertutup'
								AND mutasi_asal=2
								AND M.produk_id=dt.dmutasi_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";
				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-9 : ".$sqlupdate);

				//KOREKSI MASUK
				$sqlupdate="UPDATE stok_mutasi M SET stok_masuk=stok_masuk+ifnull((

						SELECT 	sum(dt.dkoreksi_jmlkoreksi*konversi_nilai/".$rowproduk->konversi_nilai.") as masuk
						  FROM `detail_koreksi_stok` `dt`, `master_koreksi_stok` `mt`, satuan_konversi, gudang gd
						 WHERE     `dt`.`dkoreksi_master` = `mt`.`koreksi_id`
								AND mt.koreksi_gudang=gd.gudang_id
								AND konversi_satuan = dt.dkoreksi_satuan
								AND konversi_produk = dt.dkoreksi_produk
								AND date_format(koreksi_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m-%d')
								AND date_format(koreksi_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m-%d')
								AND dkoreksi_produk='".$rowproduk->produk_id."'
								AND koreksi_status='Tertutup'
								AND koreksi_gudang=2
								AND dt.dkoreksi_jmlkoreksi>0
								AND M.produk_id=dt.dkoreksi_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";

				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-10 : ".$sqlupdate);

				//KOREKSI KELUAR
				$sqlupdate="UPDATE stok_mutasi M SET stok_keluar=stok_keluar+ifnull((

						SELECT 	abs(sum(dt.dkoreksi_jmlkoreksi*konversi_nilai/".$rowproduk->konversi_nilai.")) as keluar
						  FROM `detail_koreksi_stok` `dt`, `master_koreksi_stok` `mt`, satuan_konversi, gudang gd
						 WHERE   `dt`.`dkoreksi_master` = `mt`.`koreksi_id`
								AND mt.koreksi_gudang=gd.gudang_id
								AND konversi_satuan = dt.dkoreksi_satuan
								AND konversi_produk = dt.dkoreksi_produk
								AND date_format(koreksi_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(koreksi_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND dkoreksi_produk='".$rowproduk->produk_id."'
								AND koreksi_status='Tertutup'
								AND koreksi_gudang=2
								AND dt.dkoreksi_jmlkoreksi<0
								AND M.produk_id=dt.dkoreksi_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";

				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-11 : ".$sqlupdate);

				//PENERIMAAN BARANG
				$sqlupdate="UPDATE stok_mutasi M SET stok_masuk=stok_masuk+ifnull((

						SELECT 	sum(dt.dterima_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as masuk
						  FROM `detail_terima_beli` `dt`, `master_terima_beli` `mt`, satuan_konversi
						 WHERE     `dt`.`dterima_master` = `mt`.`terima_id`
									AND konversi_satuan = dt.dterima_satuan
								AND konversi_produk = dt.dterima_produk
								AND date_format(terima_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(terima_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND dterima_produk='".$rowproduk->produk_id."'
								AND terima_status='Tertutup'
								AND `mt`.`terima_gudang_id`=2
								AND M.produk_id=dt.dterima_produk),0)
					WHERE produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";
				//$this->firephp->log($sqlupdate);
				$this->db->query($sqlupdate) or die("ERROR-5 : ".$sqlupdate);

				//PENERIMAAN BARANG BONUS
				$sqlupdate="UPDATE stok_mutasi M SET stok_masuk=stok_masuk+ifnull((

						SELECT 	sum(dt.dtbonus_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as masuk
						  FROM `detail_terima_bonus` `dt`, `master_terima_beli` `mt`, satuan_konversi
						 WHERE  `dt`.`dtbonus_master` = `mt`.`terima_id`
								AND konversi_satuan = dt.dtbonus_satuan
								AND konversi_produk = dt.dtbonus_produk
								AND date_format(terima_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(terima_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND dtbonus_produk='".$rowproduk->produk_id."'
								AND terima_status='Tertutup'
								AND `mt`.`terima_gudang_id`=2
								AND M.produk_id=dt.dtbonus_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";
				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-6 : ".$sqlupdate);

	//	if($gudang==1){

				//RETUR PEMBELIAN
				$sqlupdate="UPDATE stok_mutasi M SET stok_keluar=stok_keluar+ifnull((

						SELECT 	sum(dt.drbeli_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as keluar
						  FROM `detail_retur_beli` `dt`, `master_retur_beli` `mt`, satuan_konversi
						 WHERE  `dt`.`drbeli_master` = `mt`.`rbeli_id`
								AND konversi_satuan = dt.drbeli_satuan
								AND konversi_produk = dt.drbeli_produk
								AND date_format(rbeli_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(rbeli_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND drbeli_produk='".$rowproduk->produk_id."'
								AND rbeli_status='Tertutup'
								AND 2 =2
								AND M.produk_id=dt.drbeli_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";
				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-7 : ".$sqlupdate);
		//}

		//elseif($gudang==2){
				//PENJUALAN PRODUK
				$sqlupdate="UPDATE stok_mutasi M SET stok_keluar=stok_keluar+ifnull((

						 SELECT sum(dt.dproduk_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as keluar
						  FROM `detail_jual_produk` `dt`, `master_jual_produk` `mt`, satuan_konversi
						 WHERE  `dt`.`dproduk_master` = `mt`.`jproduk_id`
								AND konversi_satuan = dt.dproduk_satuan
								AND konversi_produk = dt.dproduk_produk
								AND date_format(jproduk_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(jproduk_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND dproduk_produk='".$rowproduk->produk_id."'
								AND jproduk_stat_dok='Tertutup'
								AND M.produk_id=dt.dproduk_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";

				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-12 : ".$sqlupdate);

				//RETUR PENJUALAN PRODUK
				$sqlupdate="UPDATE stok_mutasi M SET stok_masuk=stok_masuk+ifnull((

								SELECT 	sum(dt.drproduk_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as masuk
						  FROM `detail_retur_jual_produk` `dt`, `master_retur_jual_produk` `mt`, satuan_konversi
						 WHERE  `dt`.`drproduk_master` = `mt`.`rproduk_id`
								AND konversi_satuan = dt.drproduk_satuan
								AND konversi_produk = dt.drproduk_produk
								AND date_format(rproduk_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(rproduk_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND drproduk_produk='".$rowproduk->produk_id."'
								AND rproduk_stat_dok='Tertutup'
								AND M.produk_id=dt.drproduk_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";

				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-13 : ".$sqlupdate);

				//PENJUALAN PRODUK GROOMING
				$sqlupdate="UPDATE stok_mutasi M SET stok_keluar=stok_keluar+ifnull((

								SELECT 	sum(dt.dpgrooming_jumlah*konversi_nilai/".$rowproduk->konversi_nilai.") as keluar
						  FROM `detail_jualproduk_grooming` `dt`, `master_jualproduk_grooming` `mt`, satuan_konversi
						 WHERE  `dt`.`dpgrooming_master` = `mt`.`jpgrooming_id`
								AND konversi_satuan = dt.dpgrooming_satuan
								AND konversi_produk = dt.dpgrooming_produk
								AND date_format(jpgrooming_tanggal,'%Y-%m-%d')>=date_format('".$tanggal_start."','%Y-%m')
								AND date_format(jpgrooming_tanggal,'%Y-%m-%d')<=date_format('".$tanggal_start."','%Y-%m')
								AND dpgrooming_produk='".$rowproduk->produk_id."'
								AND M.produk_id=dt.dpgrooming_produk),0)
						WHERE 	produk_id='".$rowproduk->produk_id."'
								AND date_format(tanggal_awal,'%Y-%m')='".$tanggal_start."'
								AND date_format(tanggal_akhir,'%Y-%m')='".$tanggal_start."'
								AND gudang_id=2";

				//$this->firephp->log($sqlupdate);
				$result=$this->db->query($sqlupdate) or die("ERROR-14 : ".$sqlupdate);
		//}


				$sql_update="UPDATE stok_mutasi SET stok_akhir=stok_awal+stok_masuk-stok_keluar
							WHERE 	produk_id='".$rowproduk->produk_id."'
									AND date_format('2012-03-01','%Y-%m-%d')='".$tanggal_start."'
									AND date_format(tanggal_akhir,'%Y-%m-%d')='".$tanggal_start."'
									AND gudang_id=2";
				//$this->firephp->log($sqlupdate);

				$result=$this->db->query($sql_update) or die("ERROR-16 : ".$sql_update);

			}



			return '1';
		}
	

	
}
?>