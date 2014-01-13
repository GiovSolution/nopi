<? /* 	These code was generated using phpCIGen v 0.1.a (21/04/2009)
	#zaqi 		zaqi.smart@gmail.com,http://zenzaqi.blogspot.com, 
    #songbee	mukhlisona@gmail.com
	#CV. Trust Solution, jl. Saronojiwo 19 Surabaya, http://www.ts.co.id
	
	+ Module  		: tutup Model
	+ Description	: For record model process back-end
	+ Filename 		: c_tutup.php
 	+ creator 		: 
 	+ Created on 12/Mar/2010 10:42:59
	
*/

class M_tutup extends Model{
		
		//constructor
		function M_tutup() {
			parent::Model();
		}
		
		function tutup_transaksi(){
			
			$sql="SELECT if(akun_saldo = 'Debet',
				  sum(buku_debet) - sum(buku_kredit),
				  sum(buku_kredit) - sum(buku_debet))
				  AS saldo_akhir,
				  akun_id, akun_kode,akun_nama
			  FROM vu_buku_besar
				  GROUP BY akun_id,akun_kode,akun_nama";
			
			$result=$this->db->query($sql);
			foreach($result->result() as $row){
				$updatesql="UPDATE akun SET akun_debet=akun_debet+".$row->saldo_akhir."
							WHERE akun_saldo='Debet' AND akun_id='".$row->akun_id."'";
				$this->db->query($updatesql);
				//$this->firephp->log($updatesql);
				
				$updatesql="UPDATE akun SET akun_kredit=akun_kredit+".$row->saldo_akhir."
							WHERE akun_saldo='Kredit' AND akun_id='".$row->akun_id."'";
				$this->db->query($updatesql);

			}
			
			//ARSIPKAN
			$sql="UPDATE buku_besar SET buku_arsip='Y'";
			$this->db->query($sql);
			
			$sql="UPDATE kasbank SET kasbank_arsip='Y'";
			$this->db->query($sql);
			
			$sql="UPDATE jurnal SET jurnal_arsip='Y'";
			$this->db->query($sql);
			
			return '1';
		}
}
?>