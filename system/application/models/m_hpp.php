<? /* 	These code was generated using phpCIGen v 0.1.a (21/04/2009)
	#zaqi 		zaqi.smart@gmail.com,http://zenzaqi.blogspot.com, 
    #songbee	mukhlisona@gmail.com
	#CV. Trust Solution, jl. Saronojiwo 19 Surabaya, http://www.ts.co.id
	
	+ Module  		: hpp Model
	+ Description	: For record model process back-end
	+ Filename 		: c_hpp.php
 	+ creator 		: 
 	+ Created on 09/Apr/2010 10:47:15
	
*/

class M_hpp extends Model{
		
		//constructor
		function M_hpp() {
			parent::Model();
		}
		
		function get_produk_list($filter,$start,$end,$satuan){
			if($satuan=='default')
				$sql="select distinct * from vu_produk_satuan_default WHERE produk_aktif='Aktif'";
			else
				$sql="select distinct * from vu_produk_satuan_terkecil WHERE produk_aktif='Aktif'";
			//echo $sql;
			
			if($filter<>""){
				$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
				$sql.="( produk_kode LIKE '%".addslashes($filter)."%' OR 
						 produk_nama LIKE '%".addslashes($filter)."%' OR 
						 satuan_kode LIKE '%".addslashes($filter)."%' OR 
						 satuan_nama LIKE '%".addslashes($filter)."%')";
			}
			
			$sql.=" ORDER BY produk_kode ASC ";
			
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
		
		function get_periode(){
			$sql="SELECT distinct substr(min(tanggal),1,7) as min_date, substr(max(tanggal),1,7) as max_date from vu_hpp_tanggal";
			$query=$this->db->query($sql);
			if($query->num_rows()>0){
				$ds=$query->row();
				$min_date=$ds->min_date;
				$max_date=$ds->max_date;
				$min_year=substr($min_date,0,4);
				$max_year=substr($max_date,0,4);
				$range_year=$max_year-$min_year;
				
				$min_month=substr($min_date,5,2);
				$max_month=substr($max_date,5,2);
				
				//echo $min_year." s/d ".$max_year;
				//echo $min_month."-".$max_month;
				//min year
				$j=0;
				for($i=12;$i>=(int)$min_month;$i--){
					$data[$j]["periode_tanggal"]=$min_year."-".(strlen($i)==1?"0".$i:$i);
					$j++;
				}
				//range year
				for($i=(int)$min_year;$i<(int)$max_year;$i++){
					for($k=1;$k<=12;$k++){
						$data[$j]["periode_tanggal"]=$i."-".(strlen($k)==1?"0".$k:$k);
						$j++;
					}
				}
				
				//max year
				for($i=1;$i<=$max_month;$i++){
					$data[$j]["periode_tanggal"]=$max_year."-".(strlen($i)==1?"0".$i:$i);
					$j++;
				}
				
				return $data;
			}
			else
				return "";
				
		}
		
		function get_jumlah_beli($periode_start, $periode_end, $produk_id){
			$sql="SELECT sum(jumlah_konversi) as jumlah_beli from vu_hpp_beli_terima 
					where DATE_FORMAT(tanggal,'%Y-%m-%d') >= '".$periode_start."' 
					AND DATE_FORMAT(tanggal,'%Y-%m-%d') <= '".$periode_end."' 
					AND produk_id='".$produk_id."'
					AND terima_status='Tertutup'";
			//echo $sql;
			
			$query_nbeli=$this->db->query($sql);
			if($query_nbeli->num_rows()){
				$nbeli=$query_nbeli->row();
				return $nbeli->jumlah_beli;
			}else
				return 0;
		}
		
		function get_harga_beli($opsi,$periode_start, $periode_end, $produk_id){
			
			if($opsi=='range'){
				$sql="SELECT ifnull(avg(harga_beli),0) as harga_beli from vu_hpp_beli_terima 
						WHERE DATE_FORMAT(tanggal,'%Y-%m-%d') >= '".$periode_start."' 
						AND DATE_FORMAT(tanggal,'%Y-%m-%d') <= '".$periode_end."' 
						AND produk_id='".$produk_id."'
						AND terima_status='Tertutup'";
			}else{
				$sql="SELECT ifnull(avg(harga_beli),0) as harga_beli from vu_hpp_beli_terima 
						WHERE DATE_FORMAT(tanggal,'%Y-%m-%d')< '".$periode_start."' 
						AND produk_id='".$produk_id."'
						AND terima_status='Tertutup'";

			}
			$query_vbeli=$this->db->query($sql);
			//$this->firephp->log($sql);
			
			if($query_vbeli->num_rows()){
				$row_vbeli=$query_vbeli->row();
				return $row_vbeli->harga_beli;
				if($row_vbeli->harga_beli>0)
					return $row_vbeli->harga_beli;
				else{
					//$sql="SELECT produk_harga,konversi_nilai FROM vu_produk_satuan_default WHERE produk_id='".$produk_id."'";
					$sql="SELECT harga_beli from vu_hpp_beli_terima 
						WHERE produk_id='".$produk_id."'
						AND DATE_FORMAT(tanggal,'%Y-%m-%d') =
							(SELECT min(DATE_FORMAT(tanggal,'%Y-%m-%d')
								FROM vu_hpp_beli_terima 
								WHERE produk_id='".$produk_id."')
						AND status='Tertutup'";
					
					$query_v2beli=$this->db->query($sql);
					if($query_v2beli->num_rows()){
						$row_v2beli=$query_v2beli->row();
						return $row_v2beli->harga_beli;
					}else{
						return 0;					
					}
				}
			}else{
				return 0;
			}
		}
		
		function get_nilai_persediaan($opsi,$periode_start,$periode_end,$produk_id){
			$persediaan=$this->get_harga_beli($opsi,$periode_start, $periode_end, $produk_id)*$this->get_stok_saldo($periode_end, $produk_id);
			return $persediaan;
		}
		
		function get_stok_saldo($tanggal_start, $produk_id){
			/*$sql="SELECT sum(jumlah_saldo) as stok_saldo from vu_stok_new_produk 
					where DATE_FORMAT(tanggal,'%Y-%m-%d') <= '".$periode."'
					AND produk_id='".$produk_id."'";
			
*/			/*$sql="SELECT 	sum(jml_terima_barang*konversi_nilai)
											+sum(jml_terima_bonus*konversi_nilai)
											-sum(jml_retur_beli*konversi_nilai)
											-sum(jml_mutasi_keluar*konversi_nilai)
											+sum(jml_mutasi_masuk*konversi_nilai)
											+sum(jml_koreksi_stok*konversi_nilai)
											-sum(jml_jual_produk*konversi_nilai)
											-sum(jml_jual_grooming*konversi_nilai)
											+sum(jml_retur_produk*konversi_nilai)
											+sum(jml_retur_paket*konversi_nilai)
											-sum(jml_pakai_cabin*konversi_nilai)
											as stok_saldo
									FROM	vu_stok_new_produk
									WHERE   date_format(tanggal,'%Y-%m-%d')<'".$periode."'
											AND produk_id='".$produk_id."'
											AND status<>'Batal'
									GROUP BY produk_id";*/
			$sql="";
			
			$sql="SELECT 	sum(stok.jml_terima_barang)
						+sum(stok.jml_terima_bonus)
						-sum(stok.jml_retur_beli)
						+sum(stok.jml_koreksi_stok)
						-sum(stok.jml_jual_produk)
						-sum(stok.jml_jual_grooming)
						+sum(stok.jml_retur_produk)
						+sum(stok.jml_retur_paket)
						-sum(stok.jml_pakai_cabin)
						as stok_saldo
				FROM
								
					(SELECT `mt`.`terima_tanggal` AS `tanggal`,
					          `mt`.`terima_supplier` AS `asal`,
					          1 AS `tujuan`,
					          1 AS `gudang`,
					          `mt`.`terima_no` AS `no_bukti`,
					          _UTF8 'PB' AS `jenis_transaksi`,
					          `mt`.`terima_status` AS `status`,
					          `dt`.`dterima_produk` AS `produk`,
					          `dt`.`dterima_satuan` AS `satuan`,
					          `dt`.`dterima_jumlah`*sk.konversi_nilai AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'beli' AS `keterangan`,
					          `dt`.`dterima_id` AS `detail_id`
					     FROM `detail_terima_beli` `dt`,`master_terima_beli` `mt`,
					           satuan_konversi sk					              
					    WHERE `dt`.`dterima_master` = `mt`.`terima_id`
					    AND  sk.konversi_satuan=dt.dterima_satuan
					    AND  sk.konversi_produk=dt.dterima_produk
					    AND  dt.dterima_produk='".$produk_id."'
					    AND  date_format(mt.terima_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    AND  mt.terima_status='Tertutup'
					    
					    UNION ALL
					    SELECT `mt`.`terima_tanggal` AS `tanggal`,
					          `mt`.`terima_supplier` AS `asal`,
					          1 AS `tujuan`,
					          1 AS `gudang`,
					          `mt`.`terima_no` AS `no_bukti`,
					          _UTF8 'PB' AS `jenis_transaksi`,
					          `mt`.`terima_status` AS `status`,
					          `db`.`dtbonus_produk` AS `produk`,
					          `db`.`dtbonus_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          `db`.`dtbonus_jumlah`*sk.konversi_nilai AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'bonus' AS `keterangan`,
					          `db`.`dtbonus_id` AS `detail_id`
					     FROM `detail_terima_bonus` `db`,`master_terima_beli` `mt`,
					     		satuan_konversi sk
					    WHERE `db`.`dtbonus_master` = `mt`.`terima_id`
					    AND  sk.konversi_satuan=db.dtbonus_satuan
					    AND  sk.konversi_produk=db.dtbonus_produk
					    AND  db.dtbonus_produk='".$produk_id."'
					    AND  date_format(mt.terima_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    AND  mt.terima_status='Tertutup'
					    
					   UNION ALL
					   SELECT `mr`.`rbeli_tanggal` AS `tanggal`,
					          `mr`.`rbeli_supplier` AS `asal`,
					          1 AS `tujuan`,
					          1 AS `gudang`,
					          `mr`.`rbeli_nobukti` AS `no_bukti`,
					          _UTF8 'RB' AS `jenis_transaksi`,
					          `mr`.`rbeli_status` AS `status`,
					          `dr`.`drbeli_produk` AS `produk`,
					          `dr`.`drbeli_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          `dr`.`drbeli_jumlah`*sk.konversi_nilai AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'retur' AS `keterangan`,
					          `dr`.`drbeli_id` AS `detail_id`
					     FROM `detail_retur_beli` `dr`,`master_retur_beli` `mr`, satuan_konversi sk
					    WHERE `dr`.`drbeli_master` = `mr`.`rbeli_id`
					    AND  sk.konversi_satuan=dr.drbeli_satuan
					    AND  dr.drbeli_produk='".$produk_id."'
					    AND  date_format(mr.rbeli_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    AND  mr.rbeli_status='Tertutup'
					    
					   UNION ALL
					   SELECT `mk`.`koreksi_tanggal` AS `tanggal`,
					          `mk`.`koreksi_gudang` AS `asal`,
					          `mk`.`koreksi_gudang` AS `tujuan`,
					          `mk`.`koreksi_gudang` AS `gudang`,
					          `mk`.`koreksi_no` AS `no_bukti`,
					          _UTF8 'koreksi' AS `jenis_transaksi`,
					          `mk`.`koreksi_status` AS `status`,
					          `dk`.`dkoreksi_produk` AS `produk`,
					          `dk`.`dkoreksi_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          `dk`.`dkoreksi_jmlkoreksi`*sk.konversi_nilai AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'koreksi' AS `keterangan`,
					          `dk`.`dkoreksi_id` AS `detail_id`
					     FROM `master_koreksi_stok` `mk`,`detail_koreksi_stok` `dk`, satuan_konversi sk
					    WHERE (`mk`.`koreksi_id` = `dk`.`dkoreksi_master`)
					    AND sk.konversi_satuan=dk.dkoreksi_satuan
					    AND  sk.konversi_produk=dk.dkoreksi_produk
					    AND  dk.dkoreksi_produk='".$produk_id."'
					    AND  date_format(mk.koreksi_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    AND  mk.koreksi_status='Tertutup'
					    
					   UNION ALL
					   SELECT `mj`.`jproduk_tanggal` AS `tanggal`,
					          2 AS `asal`,
					          `mj`.`jproduk_cust` AS `tujuan`,
					          2 AS `gudang`,
					          `mj`.`jproduk_nobukti` AS `no_bukti`,
					          _UTF8 'jual produk' AS `jenis_traksaksi`,
					          `mj`.`jproduk_stat_dok` AS `status`,
					          `dj`.`dproduk_produk` AS `produk`,
					          `dj`.`dproduk_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          `dj`.`dproduk_jumlah`*sk.konversi_nilai AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'customer' AS `keterangan`,
					          `dj`.`dproduk_id` AS `detail_id`
					     FROM  `master_jual_produk` `mj`,`detail_jual_produk` `dj`, satuan_konversi sk
					    WHERE (`dj`.`dproduk_master` = `mj`.`jproduk_id`)
					    AND  dj.dproduk_satuan=sk.konversi_satuan
					    AND  sk.konversi_produk=dj.dproduk_produk
					    AND  dj.dproduk_produk='".$produk_id."'
					    AND  date_format(mj.jproduk_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    AND  mj.jproduk_stat_dok='Tertutup'
					    
					   UNION ALL
					   SELECT `mjg`.`jpgrooming_tanggal` AS `tanggal`,
					          2 AS `asal`,
					          `mjg`.`jpgrooming_karyawan` AS `tujuan`,
					          2 AS `gudang`,
					          `mjg`.`jpgrooming_nobukti` AS `no_bukti`,
					          _UTF8 'jual produk' AS `jenis_transaksi`,
					          _UTF8 'Tertutup' AS `status`,
					          `djg`.`dpgrooming_produk` AS `produk`,
					          `djg`.`dpgrooming_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          `djg`.`dpgrooming_jumlah`*sk.konversi_nilai AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'grooming' AS `keterangan`,
					          `djg`.`dpgrooming_id` AS `detail_id`
					     FROM `master_jualproduk_grooming` `mjg`,`detail_jualproduk_grooming` `djg`,
					          satuan_konversi sk
					    WHERE (`mjg`.`jpgrooming_id` = `djg`.`dpgrooming_master`)
					    AND  sk.konversi_satuan=djg.dpgrooming_satuan
					    AND  sk.konversi_produk=djg.dpgrooming_produk
					    AND  djg.dpgrooming_produk='".$produk_id."'
					    AND  date_format(mjg.jpgrooming_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    
					   UNION ALL
					   SELECT `mrj`.`rproduk_tanggal` AS `tanggal`,
					          `mrj`.`rproduk_cust` AS `asal`,
					          2 AS `tujuan`,
					          2 AS `gudang`,
					          `mrj`.`rproduk_nobukti` AS `no_bukti`,
					          _UTF8 'retur jual' AS `jenis_transaksi`,
					          `mrj`.`rproduk_stat_dok` AS `status`,
					          `drj`.`drproduk_produk` AS `produk`,
					          `drj`.`drproduk_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          `drj`.`drproduk_jumlah`*sk.konversi_nilai AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'produk retur' AS `keterangan`,
					          `drj`.`drproduk_id` AS `detail_id`
					     FROM `master_retur_jual_produk` `mrj`,`detail_retur_jual_produk` `drj`,
					           satuan_konversi sk
					    WHERE (`mrj`.`rproduk_id` = `drj`.`drproduk_master`)
					    AND  sk.konversi_satuan=drj.drproduk_satuan
					    AND  sk.konversi_produk=drj.drproduk_produk
					    AND  drj.drproduk_produk='".$produk_id."'
					    AND  date_format(mrj.rproduk_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    AND  mrj.rproduk_stat_dok='Tertutup'
					    
					   UNION ALL
					   SELECT `mrp`.`rpaket_tanggal` AS `tanggal`,
					          `mrp`.`rpaket_cust` AS `asal`,
					          2 AS `tujuan`,
					          2 AS `gudang`,
					          `mrp`.`rpaket_nobukti` AS `no_bukti`,
					          _UTF8 'retur jual' AS `jenis_transaksi`,
					          `mrp`.`rpaket_stat_dok` AS `status`,
					          `drp`.`drpaket_produk` AS `produk`,
					          `drp`.`drpaket_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          `drp`.`drpaket_jumlah`*sk.konversi_nilai AS `jml_retur_paket`,
					          0 AS `jml_pakai_cabin`,
					          _UTF8 'paket retur' AS `keterangan`,
					          `drp`.`drpaket_id` AS `detail_id`
					     FROM `master_retur_jual_paket` `mrp`,
					           `detail_retur_paket_produk` `drp`,
					           satuan_konversi  sk
					    WHERE (`mrp`.`rpaket_id` = `drp`.`drpaket_master`)
					    AND	 sk.konversi_satuan=drp.drpaket_satuan
					    AND  sk.konversi_produk=drp.drpaket_produk
					    AND  drp.drpaket_produk='".$produk_id."'
					    AND  date_format(mrp.rpaket_tanggal,'%Y-%m-%d')<'".$tanggal_start."'
					    AND  mrp.rpaket_stat_dok='Tertutup'
					    
					   UNION ALL
					   SELECT `cb`.`cabin_date_create` AS `tanggal`,
					          `cb`.`cabin_gudang` AS `asal`,
					          `cb`.`cabin_cust` AS `tujuan`,
					          `cb`.`cabin_gudang` AS `gudang`,
					          `cb`.`cabin_bukti` AS `no_bukti`,
					          _UTF8 'pakai cabin' AS `jenis_transaksi`,
					          _UTF8 'Tertutup' AS `status`,
					          `cb`.`cabin_produk` AS `produk`,
					          `cb`.`cabin_satuan` AS `satuan`,
					          0 AS `jml_terima_barang`,
					          0 AS `jml_terima_bonus`,
					          0 AS `jml_retur_beli`,
					          0 AS `jml_koreksi_stok`,
					          0 AS `jml_jual_produk`,
					          0 AS `jml_jual_grooming`,
					          0 AS `jml_retur_produk`,
					          0 AS `jml_retur_paket`,
					          `cb`.`cabin_jumlah`*sk.konversi_nilai AS `jml_pakai_cabin`,
					          _UTF8 'pakai cabin' AS `keterangan`,
					          `cb`.`cabin_dtrawat` AS `detail_id`
					     FROM `detail_pakai_cabin` `cb`, satuan_konversi sk
					    WHERE  cb.cabin_produk='".$produk_id."'
					    AND sk.konversi_satuan=cb.cabin_satuan
					    AND  sk.konversi_produk=cb.cabin_produk
					    AND  date_format(cb.cabin_date_create,'%Y-%m-%d')<'".$tanggal_start."'
					) as stok GROUP by stok.produk";
									
			$query=$this->db->query($sql);
			//$this->firephp->log($sql);
			if($query->num_rows()>0){
				$row=$query->row();
				return $row->stok_saldo;
			}else
				return 0;
		}
		
		//add periode bulan
		function add_periode($periode,$nilai){
			$tahun=substr($periode,1,4);
			$bulan=substr($periode,5,7);
			if($bulan+$nilai>12){
				$bulan=($bulan+$nilai) % 12;
				$tahun+=round(($bulan+$nilai)/12);
			}elseif($bulan+$nilai<1){
				$bulan=12+($bulan+$nilai)%12;
				$tahun+=round(($bulan+$nilai)/12);
			}
			$periode=$tahun."-".$bulan;
		}
		
		function hpp_list($produk_id, $tanggal_start, $tanggal_end, $filter,$start,$end){
			$i=0;$j=0;
			$sql="select distinct * from vu_produk_satuan_default WHERE produk_aktif='Aktif' ";
			if($produk_id!=="" && $produk_id!=NULL){
				$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
				$sql.=" produk_id='".$produk_id."'";
			}
			
			if($tanggal_start=="" || $tanggal_start==""){
				$nbrows=0;
			}else{
				$query=$this->db->query($sql);
				$nbrows=$query->num_rows();
				
				$limit=$sql." LIMIT ".$start.",".$end;
				$query=$this->db->query($limit);
				//$this->firephp->log($limit);
				//cari awal tanggal
				$trans_first="";
				$sql_tgl="SELECT min(firstdate.tanggal) as min_date FROM (
						SELECT `terima_tanggal` AS `tanggal`
      					FROM `master_terima_beli`
      					WHERE terima_status='Tertutup'
      							
      					UNION ALL
						SELECT `master_jual_produk`.`jproduk_tanggal` AS `tanggal`
  						FROM `master_jual_produk`
  						WHERE jproduk_stat_dok='Tertutup') as firstdate";
				
				///$sql_tgl="SELECT distinct min(tanggal) as min_date from vu_hpp_tanggal WHERE status<>'Batal'";
				$query_tgl=$this->db->query($sql_tgl);
				if($query_tgl->num_rows()){
					$row_tgl=$query_tgl->row();
					$trans_first=$row_tgl->min_date;
				}else
					$trans_first="";
				
				foreach($query->result() as $row){
					$jumlah_beli=0;
					$persediaan_sebelum=0;
					$stok_sebelum=0;
					$harga_beli_sebelum=0;
					$stok_sekarang=0;
					$harga_beli_sekarang=0;
					$persediaan_sekarang=0;
					$data[$i]["konversi_nilai"]=1/$row->konversi_nilai;
					
					$persediaan_sebelum=$this->get_nilai_persediaan('sebelum',$tanggal_start,$tanggal_end,$row->produk_id)*$data[$i]["konversi_nilai"];
					$stok_sebelum=$this->get_stok_saldo($tanggal_start,$row->produk_id)*$data[$i]["konversi_nilai"];
					$harga_beli_sebelum=$this->get_harga_beli('sebelum',$tanggal_start, $tanggal_end, $row->produk_id)*$data[$i]["konversi_nilai"];
					
					$stok_sekarang=$this->get_stok_saldo($tanggal_end, $row->produk_id)*$data[$i]["konversi_nilai"];
					$harga_beli_antara=$this->get_harga_beli('range',$tanggal_start, $tanggal_end, $row->produk_id)*$data[$i]["konversi_nilai"];
					$harga_beli_sekarang=$harga_beli_antara+$harga_beli_sebelum;
					$harga_beli_sekarang=($harga_beli_sebelum>0?($harga_beli_sekarang/2):$harga_beli_sekarang);
					
					$persediaan_sekarang=$harga_beli_sekarang*$stok_sekarang;
					
					$jumlah_beli=$this->get_jumlah_beli($tanggal_start, $tanggal_end, $row->produk_id)*$data[$i]["konversi_nilai"];
					
					
					/*$data[$i]["produk_id"]=$row->produk_id;
					$data[$i]["produk_kode"]=$row->produk_kode;
					$data[$i]["produk_nama"]=$row->produk_nama;
					$data[$i]["satuan_id"]=0;
					$data[$i]["satuan_kode"]=0;
					$data[$i]["satuan_nama"]=0;
					$data[$i]["stok_awal"]=0;
					$data[$i]["persediaan_awal"]=0;
					$data[$i]["stok_saldo"]=0;
					$data[$i]["persediaan_akhir"]=0;
					$data[$i]["jumlah_beli"]=0;
					$data[$i]["pembelian"]=0;
					$data[$i]["barang_jual"]=0;
					$data[$i]["hpp"]=0;
					$data[$i]["harga_satuan"]=0;*/
					
					$data[$i]["produk_id"]		= $row->produk_id;
					$data[$i]["produk_kode"]	= $row->produk_kode;
					$data[$i]["produk_nama"]	= $row->produk_nama;
					$data[$i]["satuan_id"]		= $row->satuan_id;
					$data[$i]["satuan_kode"]	= $row->satuan_kode;
					$data[$i]["satuan_nama"]	= $row->satuan_nama;
					$data[$i]["stok_awal"]		= $stok_sebelum;
					$data[$i]["persediaan_awal"]= $persediaan_sebelum;
					$data[$i]["stok_saldo"]		= $stok_sekarang;
					$data[$i]["persediaan_akhir"]= $persediaan_sekarang;
					$data[$i]["jumlah_beli"]	= $jumlah_beli;
					$data[$i]["pembelian"]		= $harga_beli_sekarang*$jumlah_beli;
					$data[$i]["barang_jual"]	= ($stok_sebelum+$jumlah_beli)-$stok_sekarang;
					$data[$i]["hpp"]			= $data[$i]["persediaan_awal"] + $data[$i]["pembelian"] - $data[$i]["persediaan_akhir"];

/*					$data[$i]["hpp"]			= $data[$i]["persediaan_awal"] + $data[$i]["pembelian"] - 
												  $data[$i]["barang_jual"] * $harga_beli_sekarang - $data[$i]["persediaan_akhir"];
*/					$data[$i]["harga_satuan"]	= $harga_beli_sekarang;
					$i++;
				}
			}
			
			if($nbrows>0){
				$jsonresult = json_encode($data);
				return '({"total":"'.$nbrows.'","results":'.$jsonresult.'})';
			} else {
				return '({"total":"0", "results":""})';
			}
		}
				
		//function for advanced search record
		function hpp_search($produk_id ,$produk_nama ,$satuan_id ,$satuan_nama ,$stok_saldo ,$start,$end){
			//full query
			$query="select * from hpp";
			
			if($produk_id!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " produk_id LIKE '%".$produk_id."%'";
			};
			if($produk_nama!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " produk_nama LIKE '%".$produk_nama."%'";
			};
			if($satuan_id!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " satuan_id LIKE '%".$satuan_id."%'";
			};
			if($satuan_nama!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " satuan_nama LIKE '%".$satuan_nama."%'";
			};
			if($stok_saldo!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " stok_saldo LIKE '%".$stok_saldo."%'";
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
		function hpp_print($produk_id ,$produk_nama ,$satuan_id ,$satuan_nama ,$stok_saldo ,$option,$filter){
			//full query
			$sql="select * from hpp";
			if($option=='LIST'){
				$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
				$sql .= " (produk_id LIKE '%".addslashes($filter)."%' OR produk_nama LIKE '%".addslashes($filter)."%' OR satuan_id LIKE '%".addslashes($filter)."%' OR satuan_nama LIKE '%".addslashes($filter)."%' OR stok_saldo LIKE '%".addslashes($filter)."%' )";
				$query = $this->db->query($sql);
			} else if($option=='SEARCH'){
				if($produk_id!=''){
					$sql.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$sql.= " produk_id LIKE '%".$produk_id."%'";
				};
				if($produk_nama!=''){
					$sql.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$sql.= " produk_nama LIKE '%".$produk_nama."%'";
				};
				if($satuan_id!=''){
					$sql.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$sql.= " satuan_id LIKE '%".$satuan_id."%'";
				};
				if($satuan_nama!=''){
					$sql.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$sql.= " satuan_nama LIKE '%".$satuan_nama."%'";
				};
				if($stok_saldo!=''){
					$sql.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$sql.= " stok_saldo LIKE '%".$stok_saldo."%'";
				};
				$query = $this->db->query($sql);
			}
			return $query->result();
		}
		
		//function  for export to excel
		function hpp_export_excel($produk_id ,$produk_nama ,$satuan_id ,$satuan_nama ,$stok_saldo ,$option,$filter){
			//full query
			$sql="select * from hpp";
			if($option=='LIST'){
				$sql .=eregi("WHERE",$sql)? " AND ":" WHERE ";
				$sql .= " (produk_id LIKE '%".addslashes($filter)."%' OR produk_nama LIKE '%".addslashes($filter)."%' OR satuan_id LIKE '%".addslashes($filter)."%' OR satuan_nama LIKE '%".addslashes($filter)."%' OR stok_saldo LIKE '%".addslashes($filter)."%' )";
				$query = $this->db->query($sql);
			} else if($option=='SEARCH'){
				if($produk_id!=''){
					$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
					$sql.= " produk_id LIKE '%".$produk_id."%'";
				};
				if($produk_nama!=''){
					$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
					$sql.= " produk_nama LIKE '%".$produk_nama."%'";
				};
				if($satuan_id!=''){
					$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
					$sql.= " satuan_id LIKE '%".$satuan_id."%'";
				};
				if($satuan_nama!=''){
					$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
					$sql.= " satuan_nama LIKE '%".$satuan_nama."%'";
				};
				if($stok_saldo!=''){
					$sql.=eregi("WHERE",$sql)?" AND ":" WHERE ";
					$sql.= " stok_saldo LIKE '%".$stok_saldo."%'";
				};
				$query = $this->db->query($sql);
			}
			return $query;
		}
		
}
?>