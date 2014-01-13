<? /* 	
	GIOV Solution - Keep IT Simple
*/

class M_laporan_komisi_sales extends Model{
		
		//constructor
		function M_laporan_komisi_sales() {
			parent::Model();
		}
		
		
		function komisi_sales_list1($tanggal_start,$tanggal_end,$start,$end){
		
			//full query
				$query = "
				select
					karyawan.karyawan_nama as karyawan_nama,
					detail_jual_produk.dproduk_karyawan as sales,
					sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					) as total_biaya,
					
					IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and 
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0) as retur,
					
					sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					)-IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where 
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0) as total,	
					
					(sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					)-IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where 
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0))*
					karyawan.karyawan_poin/100 as komisi,
					
					karyawan.karyawan_poin as poin
				from detail_jual_produk
				left join master_jual_produk on (master_jual_produk.jproduk_id = detail_jual_produk.dproduk_master)
				left join karyawan on (karyawan.karyawan_id = detail_jual_produk.dproduk_karyawan)
				";
			
				if($tanggal_start!=''){
					$query.= " WHERE master_jual_produk.jproduk_stat_dok = 'Tertutup' and 
	master_jual_produk.jproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."'";
				};
			
				$query.=" group by detail_jual_produk.dproduk_karyawan";
			
			
			//$start = 0;
			//$end = $top_jumlah;
			$result = $this->db->query($query);
			$nbrows = $result->num_rows();
			
			//$limit = $query." LIMIT ".$start.",".$end;		
			//$result = $this->db->query($limit);    
			
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
		
		// FUNCTION SUBTOTAL
		function komisi_sales_list2($supplier_id, $tanggal_start,$tanggal_end,$jenis,$query,$start,$end){
		
			//full query
				$query = "
				select
					karyawan.karyawan_nama as karyawan_nama,
					detail_jual_produk.dproduk_karyawan as sales,
					sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					) as total_biaya,
					
					IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and 
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0) as retur,
					
					sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					)-IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where 
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0) as total,	
					
					(sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					)-IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where 
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0))*
					karyawan.karyawan_poin/100 as komisi,
					
					karyawan.karyawan_poin as poin
				from detail_jual_produk
				left join master_jual_produk on (master_jual_produk.jproduk_id = detail_jual_produk.dproduk_master)
				left join karyawan on (karyawan.karyawan_id = detail_jual_produk.dproduk_karyawan)
				";
			
				if($tanggal_start!=''){
					$query.= " WHERE master_jual_produk.jproduk_stat_dok = 'Tertutup' and 
	master_jual_produk.jproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."'";
				};
			
				//$query.=" group by detail_jual_produk.dproduk_karyawan";
			
			
			//$start = 0;
			//$end = $top_jumlah;
			$result = $this->db->query($query);
			$nbrows = $result->num_rows();
			
			//$limit = $query." LIMIT ".$start.",".$end;		
			//$result = $this->db->query($limit);    
			
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
		function kartu_stok_print($tanggal_start,$tanggal_end,$start,$end){
		//full query
				$sql = "
				select
					karyawan.karyawan_nama as karyawan_nama,
					detail_jual_produk.dproduk_karyawan as sales,
					sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					) as total_biaya,
					
					IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and 
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0) as retur,
					
					sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					)-IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where 
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0) as total,	
					
					(sum(detail_jual_produk.dproduk_jumlah * detail_jual_produk.dproduk_harga *
					((100-detail_jual_produk.dproduk_diskon)/100) * ((100-detail_jual_produk.dproduk_diskon2)/100)
					)-IFNULL((select 
						SUM(detail_retur_jual_produk.drproduk_jumlah * detail_retur_jual_produk.drproduk_harga)
					from detail_retur_jual_produk
					left join master_retur_jual_produk on master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master
					where 
					master_retur_jual_produk.rproduk_stat_dok = 'Tertutup' and
					master_retur_jual_produk.rproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."' and
					detail_retur_jual_produk.drproduk_sales_id = detail_jual_produk.dproduk_karyawan),0))*
					karyawan.karyawan_poin/100 as komisi,
					
					karyawan.karyawan_poin as poin
				from detail_jual_produk
				left join master_jual_produk on (master_jual_produk.jproduk_id = detail_jual_produk.dproduk_master)
				left join karyawan on (karyawan.karyawan_id = detail_jual_produk.dproduk_karyawan)
				";
			
				if($tanggal_start!=''){
					$sql.= " WHERE master_jual_produk.jproduk_stat_dok = 'Tertutup' and 
	master_jual_produk.jproduk_tanggal BETWEEN '".$tanggal_start."' AND '".$tanggal_end."'";
				};
			
				$sql.=" group by detail_jual_produk.dproduk_karyawan ORDER BY karyawan_nama";
				$query = $this->db->query($sql);
			return $query->result();
		}
		
		//function  for export to excel
		function kartu_stok_export_excel($produk_id ,$produk_nama ,$satuan_id ,$satuan_nama ,$stok_saldo ,$option,$filter){
			//full query
			$sql="select * from kartu_stok";
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