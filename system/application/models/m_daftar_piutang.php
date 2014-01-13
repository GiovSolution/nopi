<? 

class m_daftar_piutang extends Model{
		
		//constructor
		function m_daftar_piutang() {
			parent::Model();
		}
		
		function get_akun_list(){
			$sql="SELECT akun_id,akun_nama FROM akun";
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
		

	function detail_list_piutang_list($master_id, $cust_id, $query,$start,$end) {
			$query = "SELECT detail_lunas_piutang.dpiutang_id as dpiutang_id, detail_lunas_piutang.dpiutang_nobukti as nobukti, detail_lunas_piutang.dpiutang_nilai as dpiutang_nilai, detail_lunas_piutang.dpiutang_cara as dpiutang_cara, detail_lunas_piutang.dpiutang_keterangan as keterangan,
						date_format(master_faktur_lunas_piutang.fpiutang_tanggal, '%Y-%m-%d') as tanggal,
						master_faktur_lunas_piutang.fpiutang_stat_dok as status_dokumen,
						master_faktur_lunas_piutang.fpiutang_creator as creator
						FROM detail_lunas_piutang 
						LEFT JOIN master_faktur_lunas_piutang ON (master_faktur_lunas_piutang.fpiutang_id = detail_lunas_piutang.dpiutang_id_faktur_lp)
						WHERE master_faktur_lunas_piutang.fpiutang_cust = '".$cust_id."' and master_faktur_lunas_piutang.fpiutang_stat_dok = 'Tertutup'
					UNION

					select 0 as dpiutang_id, master_retur_jual_produk.rproduk_nobukti as nobukti, 
					(detail_retur_jual_produk.drproduk_harga * detail_retur_jual_produk.drproduk_jumlah) as dpiutang_nilai, 'retur' as dpiutang_cara, 'retur' as keterangan, 
					master_retur_jual_produk.rproduk_tanggal as tanggal, master_retur_jual_produk.rproduk_stat_dok as status_dokumen, 0 as creator
					from detail_retur_jual_produk
					left join master_retur_jual_produk on (master_retur_jual_produk.rproduk_id = detail_retur_jual_produk.drproduk_master)
					WHERE master_retur_jual_produk.rproduk_cust = ".$cust_id."
			"
			;

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

		//function for get list record
		function daftar_piutang_list($filter,$start,$end){
			$query = "SELECT master_lunas_piutang.lpiutang_id , master_lunas_piutang.lpiutang_faktur, master_lunas_piutang.lpiutang_id_faktur, master_lunas_piutang.lpiutang_cust, master_lunas_piutang.lpiutang_faktur_tanggal,
							master_lunas_piutang.lpiutang_keterangan, master_lunas_piutang.lpiutang_status, master_lunas_piutang.lpiutang_jenis_transaksi,
							master_lunas_piutang.lpiutang_stat_dok, master_lunas_piutang.lpiutang_kode_cust, sum(master_lunas_piutang.lpiutang_total) as lpiutang_total, sum(master_lunas_piutang.lpiutang_sisa) as lpiutang_sisa_temp
							,customer.* , 

							(
sum(master_lunas_piutang.lpiutang_total)-
IFNULL((select sum(master_faktur_lunas_piutang.fpiutang_bayar)
from master_faktur_lunas_piutang
where master_faktur_lunas_piutang.fpiutang_stat_dok = 'Tertutup'
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
group by master_retur_jual_produk.rproduk_cust),0)
)
) as lpiutang_sisa

						FROM master_lunas_piutang
						LEFT JOIN customer on (customer.cust_id = master_lunas_piutang.lpiutang_cust)
						WHERE master_lunas_piutang.lpiutang_stat_dok <> 'Batal'

						";
			
			// For simple search
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (lpiutang_faktur LIKE '%".addslashes($filter)."%' OR customer.cust_nama LIKE '%".addslashes($filter)."%' OR lpiutang_keterangan LIKE '%".addslashes($filter)."%' OR lpiutang_stat_dok LIKE '%".addslashes($filter)."%')";
			}
			

			$query .= " GROUP BY customer.cust_id";

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
		function ship_update($ship_id , $ship_nama , $ship_keterangan ,$ship_aktif){
			
			if ($ship_aktif=="")
				$ship_aktif = "Aktif";
			$data = array(
				"ship_id"=>$ship_id,	
				"ship_nama"=>$ship_nama,			
				"ship_keterangan"=>$ship_keterangan,
				"ship_aktif"=>$ship_aktif,			
				"ship_update"=>$_SESSION[SESSION_USERID],			
				"ship_date_update"=>date('Y-m-d H:i:s')			
			);
			
			// pengecekan update jika tidak ada perubahan
			/*
			$sql="SELECT mbank_id FROM bank_master WHERE mbank_nama='".$bank_nama."'";
			$rs=$this->db->query($sql);
			if($rs->num_rows()==1){
				foreach($rs->result() as $ngr){
					$data['bank_nama']= $ngr->mbank_id;
				}
			}
			else {$data['bank_nama']=$bank_nama;}
			*/
			// eof pengecekan update
			
			$this->db->where('ship_id', $ship_id);
			$this->db->update('ship', $data);
			if($this->db->affected_rows()){
				//echo "masuk";
				$sql="UPDATE ship set ship_revised=(ship_revised+1) WHERE ship_id='".$ship_id."'";
				$this->db->query($sql);
				return '1';
			}
			
		}
		
		//function for create new record
		function ship_create($ship_nama, $ship_keterangan ,$ship_aktif ,$ship_creator ,$ship_date_create ,$ship_update ,$ship_date_update ,$ship_revised ){
			if ($ship_aktif=="")
				$ship_aktif = "Aktif";
			$data = array(
				"ship_nama"=>$ship_nama,	
				"ship_keterangan"=>$ship_keterangan,	
				"ship_aktif"=>$ship_aktif,	
				"ship_creator"=>$_SESSION[SESSION_USERID],	
				"ship_date_create"=>date('Y-m-d H:i:s'),	
				"ship_update"=>$ship_update,	
				"ship_date_update"=>$ship_date_update,	
				"ship_revised"=>'0'	
			);
			$this->db->insert('ship', $data); 
			if($this->db->affected_rows())
				return '1';
			else
				return '0';
		}
		
		//fcuntion for delete record
		function bank_delete($pkid){
			// You could do some checkups here and return '0' or other error consts.
			// Make a single query to delete all of the banks at the same time :
			if(sizeof($pkid)<1){
				return '0';
			} else if (sizeof($pkid) == 1){
				$query = "DELETE FROM bank WHERE bank_id = ".$pkid[0];
				$this->db->query($query);
			} else {
				$query = "DELETE FROM bank WHERE ";
				for($i = 0; $i < sizeof($pkid); $i++){
					$query = $query . "bank_id= ".$pkid[$i];
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
		function bank_search($bank_id ,$bank_kode ,$bank_nama ,$bank_norek ,$bank_atasnama ,$bank_saldo ,$bank_keterangan ,$bank_aktif ,$bank_creator ,$bank_date_create ,$bank_update ,$bank_date_update ,$bank_revised ,$start,$end){
			if ($bank_aktif=="")
				$bank_aktif = "Aktif";
			//full query
			$query="select * from bank,akun,bank_master WHERE bank_kode=akun_id AND bank_nama=mbank_id";
			
			if($bank_id!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_id LIKE '%".$bank_id."%'";
			};
			if($bank_kode!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_kode LIKE '%".$bank_kode."%'";
			};
			if($bank_nama!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_nama LIKE '%".$bank_nama."%'";
			};
			if($bank_norek!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_norek LIKE '%".$bank_norek."%'";
			};
			if($bank_atasnama!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_atasnama LIKE '%".$bank_atasnama."%'";
			};
			if($bank_saldo!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_saldo LIKE '%".$bank_saldo."%'";
			};
			if($bank_keterangan!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_keterangan LIKE '%".$bank_keterangan."%'";
			};
			if($bank_aktif!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_aktif LIKE '%".$bank_aktif."%'";
			};
			if($bank_creator!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_creator LIKE '%".$bank_creator."%'";
			};
			if($bank_date_create!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_date_create LIKE '%".$bank_date_create."%'";
			};
			if($bank_update!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_update LIKE '%".$bank_update."%'";
			};
			if($bank_date_update!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_date_update LIKE '%".$bank_date_update."%'";
			};
			if($bank_revised!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " bank_revised LIKE '%".$bank_revised."%'";
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
		function listpiutang_print($bank_id ,$bank_kode ,$bank_nama ,$bank_norek ,$bank_atasnama ,$bank_saldo ,$bank_keterangan ,$bank_aktif ,$bank_creator ,$bank_date_create ,$bank_update ,$bank_date_update ,$bank_revised ,$option,$filter){
			//full query
			$query="SELECT * FROM bank,bank_master WHERE  bank_nama=mbank_id";
			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (bank_id LIKE '%".addslashes($filter)."%' OR bank_kode LIKE '%".addslashes($filter)."%' OR bank_nama LIKE '%".addslashes($filter)."%' OR bank_norek LIKE '%".addslashes($filter)."%' OR bank_atasnama LIKE '%".addslashes($filter)."%' OR bank_saldo LIKE '%".addslashes($filter)."%' OR bank_keterangan LIKE '%".addslashes($filter)."%' OR bank_aktif LIKE '%".addslashes($filter)."%' OR bank_creator LIKE '%".addslashes($filter)."%' OR bank_date_create LIKE '%".addslashes($filter)."%' OR bank_update LIKE '%".addslashes($filter)."%' OR bank_date_update LIKE '%".addslashes($filter)."%' OR bank_revised LIKE '%".addslashes($filter)."%' )";
				$result = $this->db->query($query);
			} else if($option=='SEARCH'){
				if($bank_id!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_id LIKE '%".$bank_id."%'";
				};
				if($bank_kode!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_kode LIKE '%".$bank_kode."%'";
				};
				if($bank_nama!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_nama LIKE '%".$bank_nama."%'";
				};
				if($bank_norek!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_norek LIKE '%".$bank_norek."%'";
				};
				if($bank_atasnama!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_atasnama LIKE '%".$bank_atasnama."%'";
				};
				if($bank_saldo!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_saldo LIKE '%".$bank_saldo."%'";
				};
				if($bank_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_keterangan LIKE '%".$bank_keterangan."%'";
				};
				if($bank_aktif!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_aktif LIKE '%".$bank_aktif."%'";
				};
				if($bank_creator!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_creator LIKE '%".$bank_creator."%'";
				};
				if($bank_date_create!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_date_create LIKE '%".$bank_date_create."%'";
				};
				if($bank_update!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_update LIKE '%".$bank_update."%'";
				};
				if($bank_date_update!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_date_update LIKE '%".$bank_date_update."%'";
				};
				if($bank_revised!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_revised LIKE '%".$bank_revised."%'";
				};
				$result = $this->db->query($query);
			}
			return $result;
		}
		
		//function  for export to excel
		function listpiutang_export_excel($bank_id ,$bank_kode ,$bank_nama ,$bank_norek ,$bank_atasnama ,$bank_saldo ,$bank_keterangan ,$bank_aktif ,$bank_creator ,$bank_date_create ,$bank_update ,$bank_date_update ,$bank_revised ,$option,$filter){
			//full query
			$query="select bank_master.mbank_nama AS nama_bank,
							bank.bank_norek AS no_rekening,
							bank.bank_atasnama AS atas_nama,
							bank.bank_saldo AS 'Saldo_(Rp)',
							bank.bank_aktif AS aktif
						FROM bank
						Inner Join bank_master ON bank.bank_nama = bank_master.mbank_id";
			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (bank_id LIKE '%".addslashes($filter)."%' OR bank_kode LIKE '%".addslashes($filter)."%' OR bank_nama LIKE '%".addslashes($filter)."%' OR bank_norek LIKE '%".addslashes($filter)."%' OR bank_atasnama LIKE '%".addslashes($filter)."%' OR bank_saldo LIKE '%".addslashes($filter)."%' OR bank_keterangan LIKE '%".addslashes($filter)."%' OR bank_aktif LIKE '%".addslashes($filter)."%' OR bank_creator LIKE '%".addslashes($filter)."%' OR bank_date_create LIKE '%".addslashes($filter)."%' OR bank_update LIKE '%".addslashes($filter)."%' OR bank_date_update LIKE '%".addslashes($filter)."%' OR bank_revised LIKE '%".addslashes($filter)."%' )";
				$result = $this->db->query($query);
			} else if($option=='SEARCH'){
				if($bank_id!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_id LIKE '%".$bank_id."%'";
				};
				if($bank_kode!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_kode LIKE '%".$bank_kode."%'";
				};
				if($bank_nama!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_nama LIKE '%".$bank_nama."%'";
				};
				if($bank_norek!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_norek LIKE '%".$bank_norek."%'";
				};
				if($bank_atasnama!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_atasnama LIKE '%".$bank_atasnama."%'";
				};
				if($bank_saldo!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_saldo LIKE '%".$bank_saldo."%'";
				};
				if($bank_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_keterangan LIKE '%".$bank_keterangan."%'";
				};
				if($bank_aktif!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_aktif LIKE '%".$bank_aktif."%'";
				};
				if($bank_creator!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_creator LIKE '%".$bank_creator."%'";
				};
				if($bank_date_create!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_date_create LIKE '%".$bank_date_create."%'";
				};
				if($bank_update!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_update LIKE '%".$bank_update."%'";
				};
				if($bank_date_update!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_date_update LIKE '%".$bank_date_update."%'";
				};
				if($bank_revised!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " bank_revised LIKE '%".$bank_revised."%'";
				};
				$result = $this->db->query($query);
			}
			return $result;
		}
}

/* 
	created by : GIOV Solution - Keep IT Simple
*/
?>