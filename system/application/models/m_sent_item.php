<? /* 	These code was generated using phpCIGen v 0.1.a (21/04/2009)
	#zaqi 		zaqi.smart@gmail.com,http://zenzaqi.blogspot.com, 
    #songbee	mukhlisona@gmail.com
	#CV. Trust Solution, jl. Saronojiwo 19 Surabaya, http://www.ts.co.id
	
	+ Module  		: sent_item Model
	+ Description	: For record model process back-end
	+ Filename 		: M_sent_item.php
 	+ Author  		: Natalie
 	 	+ Created on 20/Apr/2011 14:17
	
*/

class M_sent_item extends Model{
		
		//constructor
		function M_sent_item() {
			parent::Model();
		}
		
		//function for get list record
		function sent_item_list($filter,$start,$end){
			$query =   "SELECT * FROM sentitems";
			
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (DestinationNumber LIKE '%".addslashes($filter)."%' OR 
							 TextDecoded LIKE '%".addslashes($filter)."%')";
			}

			$query .= " ORDER BY SendingDateTime DESC ";
			
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
		
		//function for get list record
		/*function sent_item_status_sent($filter,$start,$end){
			$query =   "SELECT 
							count(sentitems_status) as status_sent
						FROM sentitems 
						WHERE sentitems_status = 'sent'";
			
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (DestinationNumber LIKE '%".addslashes($filter)."%' OR TextDecoded LIKE '%".addslashes($filter)."%' )";
			}

			//$query .= "";
			
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
		
		//function for get list record
		function sent_item_status_unsent($filter,$start,$end){
			$query =   "SELECT 
							count(sentitems_status) as status_unsent
						FROM sentitems 
						WHERE sentitems_status = 'unsent'";
			
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (DestinationNumber LIKE '%".addslashes($filter)."%' OR TextDecoded LIKE '%".addslashes($filter)."%' )";
			}

			//$query .= "";
			
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
		
		//function for get list record
		function sent_item_status_failed($filter,$start,$end){
			$query =   "SELECT 
							count(sentitems_status) as status_failed
						FROM sentitems 
						WHERE sentitems_status = 'failed'";
			
			if ($filter<>""){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (DestinationNumber LIKE '%".addslashes($filter)."%' OR TextDecoded LIKE '%".addslashes($filter)."%' )";
			}

			//$query .= "";
			
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
		} */
		
		
		//fcuntion for delete record
		function sent_item_delete($pkid){
			// You could do some checkups here and return '0' or other error consts.
			// Make a single query to delete all of the sent_items at the same time :
			if(sizeof($pkid)<1){
				return '0';
			} else if (sizeof($pkid) == 1){
				$query = "DELETE FROM sentitems WHERE ID = ".$pkid[0];
				$this->db->query($query);
			} else {
				$query = "DELETE FROM sentitems WHERE ";
				for($i = 0; $i < sizeof($pkid); $i++){
					$query = $query . "ID= ".$pkid[$i];
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

		function sent_item_delete_all(){
			// You could do some checkups here and return '0' or other error consts.
			// Make a single query to delete all of the sent_items at the same time :
			$query = "DELETE FROM sentitems ";
			$this->db->query($query);
			 
			
			if($this->db->affected_rows()>0)
				return '1';
			else
				return '0';
		}
		
		//function for advanced search record
		function sent_item_search($ID ,$DestinationNumber ,$TextDecoded ,$SendingDateTime,$start,$end){
			//full query
			$query =   "SELECT * FROM sentitems";
			
			
			if($DestinationNumber!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " DestinationNumber LIKE '%".$DestinationNumber."%'";
			};
			if($TextDecoded!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " TextDecoded LIKE '%".$TextDecoded."%'";
			};
			
			if($SendingDateTime!=''){
				$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
				$query.= " SendingDateTime LIKE '%".$SendingDateTime."%'";
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
		function sent_item_print($ID ,$DestinationNumber ,$TextDecoded ,$SendingDateTime ,$option,$filter){
			//full query
			$query =   "SELECT * FROM sentitems ";
			
			if($option=='LIST'){
				$query .=eregi("WHERE",$sql)? " AND ":" WHERE ";
				$query .= " (DestinationNumber LIKE '%".addslashes($filter)."%' OR 
							 TextDecoded LIKE '%".addslashes($filter)."%' )";
			} else if($option=='SEARCH'){
				if($DestinationNumber!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " DestinationNumber LIKE '%".$DestinationNumber."%'";
				};
				if($TextDecoded!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " TextDecoded LIKE '%".$TextDecoded."%'";
				};
				if($SendingDateTime!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " SendingDateTime LIKE '%".$SendingDateTime."%'";
				};
				
			}
			$query = $this->db->query($query);
			
			return $query->result();
		}
		
		//function  for export to excel
		function sent_item_export_excel($ID ,$DestinationNumber ,$TextDecoded ,$SendingDateTime, $option,$filter){
			//full query
			$query =   "SELECT SendingDateTime as 'Tanggal Kirim', TextDecoded as 'Isi Pesan', DestinationNumber as 'No HP'
						FROM sentitems";
			
			if($option=='LIST'){
				$query .=eregi("WHERE",$query)? " AND ":" WHERE ";
				$query .= " (DestinationNumber LIKE '%".addslashes($filter)."%' OR 
							 TextDecoded LIKE '%".addslashes($filter)."%' )";
			} else if($option=='SEARCH'){
				if($DestinationNumber!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " DestinationNumber LIKE '%".$DestinationNumber."%'";
				};
				if($TextDecoded!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " TextDecoded LIKE '%".$TextDecoded."%'";
				};
				if($SendingDateTime!=''){
					$query.=eregi("WHERE",$query)?" AND ":" WHERE ";
					$query.= " SendingDateTime LIKE '%".$SendingDateTime."%'";
				};
			
			}
			$query = $this->db->query($query);
			return $query;
		}
		
}
?>