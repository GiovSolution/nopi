<? /* 	
	+ Module  		: kurs Model
	+ Description	: For record model process back-end
	+ Filename 		: c_kurs.php
 	+ Author  		: Isaac & Freddy
 	+ Created on 14/Mar/2012 22:48:00
	
*/

class M_kurs extends Model{
		
		//constructor
		function M_kurs() {
			parent::Model();
		}
		
		//function for get list record
		function kurs_list($filter,$start,$end){
			$query = "SELECT * FROM kurs";
			
			// For simple search
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (jenis_kode LIKE '%".addslashes($filter)."%' OR jenis_nama LIKE '%".addslashes($filter)."%' OR jenis_kelompok LIKE '%".addslashes($filter)."%' )";
			}
			
			//$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
			//$query .= " (jenis_aktif = 'Aktif') ";
			
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
		function kurs_update($kurs_id, $kurs_tanggal ,$kurs_negara ,$kurs_initial ,$kurs_nilai ,$kurs_keterangan, $kurs_aktif){
			if ($kurs_aktif=="")
				$kurs_aktif = "Aktif";
			$data = array(
				"kurs_tanggal"=>$kurs_tanggal, 
				"kurs_negara"=>$kurs_negara, 
				"kurs_initial"=>$kurs_initial, 
				"kurs_nilai"=>$kurs_nilai, 
				"kurs_keterangan"=>$kurs_keterangan, 
				"kurs_aktif"=>$kurs_aktif,
				"kurs_update"=>$_SESSION[SESSION_USERID],			
				"kurs_date_update"=>date('Y-m-d H:i:s'),		
				"kurs_revised"=>'0'		
			);
			$this->db->where('kurs_id', $kurs_id);
			$this->db->update('kurs', $data);
			
			if($this->db->affected_rows()){
				$sql="UPDATE kurs set kurs_revised=(kurs_revised+1) WHERE kurs_id='".$kurs_id."'";
				$this->db->query($sql);
			}
			return '1';
		}
		
		//function for create new record
		function kurs_create($kurs_tanggal ,$kurs_negara ,$kurs_initial ,$kurs_nilai ,$kurs_keterangan, $kurs_aktif){
			if ($kurs_aktif=="")
				$kurs_aktif = "Aktif";
			if ($kurs_tanggal=="")
				$kurs_tanggal = date('Y-m-d H:i:s');
			$data = array(
				"kurs_tanggal"=>$kurs_tanggal, 
				"kurs_negara"=>$kurs_negara, 
				"kurs_initial"=>$kurs_initial, 
				"kurs_nilai"=>$kurs_nilai, 
				"kurs_keterangan"=>$kurs_keterangan, 
				"kurs_aktif"=>$kurs_aktif,
				"kurs_creator"=>$_SESSION[SESSION_USERID],			
				"kurs_date_create"=>date('Y-m-d H:i:s'),		
				"kurs_revised"=>'0'		
			);
			$this->db->insert('kurs', $data); 
			if($this->db->affected_rows())
				return '1';
			else
				return '0';
		}
		
		//fcuntion for delete record
		function jenis_delete($pkid){
			// You could do some checkups here and return '0' or other error consts.
			// Make a single query to delete all of the jeniss at the same time :
			if(sizeof($pkid)<1){
				return '0';
			} else if (sizeof($pkid) == 1){
				$query = "DELETE FROM jenis WHERE jenis_id = ".$pkid[0];
				$this->db->query($query);
			} else {
				$query = "DELETE FROM jenis WHERE ";
				for($i = 0; $i < sizeof($pkid); $i++){
					$query = $query . "jenis_id= ".$pkid[$i];
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
		function jenis_search($jenis_id ,$jenis_kode ,$jenis_nama ,$jenis_kelompok ,$jenis_keterangan ,$jenis_aktif ,$start,$end){
			if ($jenis_aktif=="")
				$jenis_aktif = "Aktif";
			//full query
			$query="select * from jenis";
			
			if($jenis_id!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " jenis_id LIKE '%".$jenis_id."%'";
			};
			if($jenis_kode!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " jenis_kode LIKE '%".$jenis_kode."%'";
			};
			if($jenis_nama!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " jenis_nama LIKE '%".$jenis_nama."%'";
			};
			if($jenis_kelompok!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " jenis_kelompok LIKE '%".$jenis_kelompok."%'";
			};
			if($jenis_keterangan!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " jenis_keterangan LIKE '%".$jenis_keterangan."%'";
			};
			if($jenis_aktif!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " jenis_aktif = '".$jenis_aktif."'";
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
		function jenis_print($jenis_id ,$jenis_kode ,$jenis_nama ,$jenis_kelompok ,$jenis_keterangan ,$jenis_aktif ,$option,$filter){
			//full query
			$query="select * from jenis";
			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (jenis_id LIKE '%".addslashes($filter)."%' OR jenis_kode LIKE '%".addslashes($filter)."%' OR jenis_nama LIKE '%".addslashes($filter)."%' OR jenis_kelompok LIKE '%".addslashes($filter)."%' OR jenis_keterangan LIKE '%".addslashes($filter)."%' OR jenis_aktif LIKE '%".addslashes($filter)."%' )";
				$result = $this->db->query($query);
			} else if($option=='SEARCH'){
				if($jenis_id!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_id LIKE '%".$jenis_id."%'";
				};
				if($jenis_kode!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_kode LIKE '%".$jenis_kode."%'";
				};
				if($jenis_nama!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_nama LIKE '%".$jenis_nama."%'";
				};
				if($jenis_kelompok!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_kelompok LIKE '%".$jenis_kelompok."%'";
				};
				if($jenis_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_keterangan LIKE '%".$jenis_keterangan."%'";
				};
				if($jenis_aktif!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_aktif LIKE '%".$jenis_aktif."%'";
				};
				$result = $this->db->query($query);
			}
			return $result;
		}
		
		//function  for export to excel
		function jenis_export_excel($jenis_id ,$jenis_kode ,$jenis_nama ,$jenis_kelompok ,$jenis_keterangan ,$jenis_aktif ,$option,$filter){
			//full query
			$query="SELECT
					jenis_kode AS kode,
					jenis_nama AS nama,
					jenis_kelompok AS kelompok,
					jenis_keterangan AS keterangan,
					jenis_aktif AS aktif
					from jenis";
			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (jenis_id LIKE '%".addslashes($filter)."%' OR jenis_kode LIKE '%".addslashes($filter)."%' OR jenis_nama LIKE '%".addslashes($filter)."%' OR jenis_kelompok LIKE '%".addslashes($filter)."%' OR jenis_keterangan LIKE '%".addslashes($filter)."%' OR jenis_aktif LIKE '%".addslashes($filter)."%' )";
				$result = $this->db->query($query);
			} else if($option=='SEARCH'){
				if($jenis_id!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_id LIKE '%".$jenis_id."%'";
				};
				if($jenis_kode!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_kode LIKE '%".$jenis_kode."%'";
				};
				if($jenis_nama!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_nama LIKE '%".$jenis_nama."%'";
				};
				if($jenis_kelompok!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_kelompok LIKE '%".$jenis_kelompok."%'";
				};
				if($jenis_keterangan!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_keterangan LIKE '%".$jenis_keterangan."%'";
				};
				if($jenis_aktif!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " jenis_aktif LIKE '%".$jenis_aktif."%'";
				};
				$result = $this->db->query($query);
			}
			return $result;
		}
		
}
?>