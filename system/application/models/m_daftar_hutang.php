<? /* 
		GIOV Solution - Keep IT Simple
	
*/

class m_daftar_hutang extends Model{
		
		//constructor
		function m_daftar_hutang() {
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
		
		function detail_list_hutang_list($master_id, $supplier_id , $query,$start,$end) {
			$query = "SELECT 
				detail_lunas_hutang.dhutang_id as dhutang_id, 
				detail_lunas_hutang.dhutang_nilai as dhutang_nilai, 
				detail_lunas_hutang.dhutang_keterangan as keterangan,
				date_format(hutang.hutang_tanggal, '%Y-%m-%d') as tanggal,
				hutang.hutang_status as status_dokumen,
				hutang.hutang_creator as creator,
				master_lunas_hutang.fhutang_cara as dhutang_cara,
				master_lunas_hutang.fhutang_nobukti as nobukti
					FROM detail_lunas_hutang 
					LEFT JOIN hutang ON (hutang.hutang_id = detail_lunas_hutang.dhutang_hutang)
					LEFT JOIN master_lunas_hutang ON (master_lunas_hutang.fhutang_id = detail_lunas_hutang.dhutang_master)
					WHERE hutang.hutang_supplier = '".$supplier_id."' and master_lunas_hutang.fhutang_stat_dok = 'Tertutup'

				UNION

					select 0 as dhutang_id, 
					(detail_retur_beli.drbeli_harga * detail_retur_beli.drbeli_jumlah) as dhutang_nilai,
					'retur' as keterangan, 
					master_retur_beli.rbeli_tanggal as tanggal,
					'Lunas' as status_dokumen, 
					master_retur_beli.rbeli_creator as creator,
					 'retur' as dhutang_cara,
					master_retur_beli.rbeli_nobukti as nobukti 
					from detail_retur_beli
					left join master_retur_beli on (master_retur_beli.rbeli_id = detail_retur_beli.drbeli_master)
					WHERE master_retur_beli.rbeli_supplier = ".$supplier_id."

			";

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
		function daftar_hutang_list($filter,$start,$end){
			$query = "SELECT supplier.supplier_id as supplier_id , supplier.supplier_nama as supplier_nama
						, hutang.hutang_id as hutang_id, hutang.hutang_op_id as hutang_op_id, hutang.hutang_faktur as hutang_faktur, hutang.hutang_tanggal as hutang_tanggal, hutang.hutang_keterangan as hutang_keterangan,
						hutang.hutang_status as hutang_status , sum(hutang.hutang_total) as hutang_total, sum(hutang.hutang_sisa) as hutang_sisa_temp ,

						(
sum(hutang.hutang_total)-
IFNULL((select sum(master_lunas_hutang.fhutang_bayar)
from master_lunas_hutang
where master_lunas_hutang.fhutang_stat_dok = 'Tertutup'
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
						LEFT JOIN supplier on (supplier.supplier_id = hutang.hutang_supplier)
						WHERE hutang_status <> ''
						";
			
			// For simple search
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (hutang_faktur LIKE '%".addslashes($filter)."%' OR supplier.supplier_nama LIKE '%".addslashes($filter)."%' OR hutang_status LIKE '%".addslashes($filter)."%')";
			}
			
			$query .= " GROUP BY hutang.hutang_supplier";


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
?>