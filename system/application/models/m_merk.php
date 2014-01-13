<? /* 
	+ Module  		: merk Model
	+ Description	: For record model process back-end
	+ Filename 		: c_merk.php
 	+ Author  		: Isaac, Freddy
 	+ Created on 01/Apr/2012 22:46:58
	
*/

class m_merk extends Model{
		
		//constructor
		function m_merk() {
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
		
		//function for get list record
		function merk_list($filter,$start,$end){
			$query = "SELECT * FROM merek ";
			
			// For simple search
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (merek_nama LIKE '%".addslashes($filter)."%' OR merek_keterangan LIKE '%".addslashes($filter)."%' OR merek_aktif LIKE '%".addslashes($filter)."%')";
			}
			
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
		function merk_update($merk_id , $merk_nama , $merk_keterangan ,$merk_aktif){
			
			if ($merk_aktif=="")
				$merk_aktif = "Aktif";
			$data = array(
				"merek_id"=>$merk_id,	
				"merek_nama"=>$merk_nama,			
				"merek_keterangan"=>$merk_keterangan,
				"merek_aktif"=>$merk_aktif,			
				"merek_update"=>$_SESSION[SESSION_USERID],			
				"merek_date_update"=>date('Y-m-d H:i:s')			
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
			
			$this->db->where('merek_id', $merk_id);
			$this->db->update('merek', $data);
			if($this->db->affected_rows()){
				//echo "masuk";
				$sql="UPDATE merek set merek_revised=(merek_revised+1) WHERE merek_id='".$merk_id."'";
				$this->db->query($sql);
				return '1';
			}
			
		}
		
		//function for create new record
		function merk_create($merk_nama, $merk_keterangan ,$merk_aktif ,$merk_creator ,$merk_date_create ,$merk_update ,$merk_date_update ,$merk_revised ){
			if ($merk_aktif=="")
				$merk_aktif = "Aktif";
			$data = array(
				"merek_nama"=>$merk_nama,	
				"merek_keterangan"=>$merk_keterangan,	
				"merek_aktif"=>$merk_aktif,	
				"merek_creator"=>$_SESSION[SESSION_USERID],	
				"merek_date_create"=>date('Y-m-d H:i:s'),	
				"merek_update"=>$merk_update,	
				"merek_date_update"=>$merk_date_update,	
				"merek_revised"=>'0'	
			);
			$this->db->insert('merek', $data); 
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
		function bank_print($bank_id ,$bank_kode ,$bank_nama ,$bank_norek ,$bank_atasnama ,$bank_saldo ,$bank_keterangan ,$bank_aktif ,$bank_creator ,$bank_date_create ,$bank_update ,$bank_date_update ,$bank_revised ,$option,$filter){
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
		function bank_export_excel($bank_id ,$bank_kode ,$bank_nama ,$bank_norek ,$bank_atasnama ,$bank_saldo ,$bank_keterangan ,$bank_aktif ,$bank_creator ,$bank_date_create ,$bank_update ,$bank_date_update ,$bank_revised ,$option,$filter){
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