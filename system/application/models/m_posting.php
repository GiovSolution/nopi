<? /* 	These code was generated using phpCIGen v 0.1.a (21/04/2009)
	#zaqi 		zaqi.smart@gmail.com,http://zenzaqi.blogspot.com,
    #songbee	mukhlisona@gmail.com
	#CV. Trust Solution, jl. Saronojiwo 19 Surabaya, http://www.ts.co.id

	+ Module  		: posting Model
	+ Description	: For record model process back-end
	+ Filename 		: c_posting.php
 	+ creator 		:
 	+ Created on 12/Mar/2010 10:42:59

*/

class M_posting extends Model{

		//constructor
		function M_posting() {
			parent::Model();
		}

		function post_transaksi($tgl_awal,$tgl_akhir,$bulan,$tahun,$periode){

			$tanggal=date('Y-m-d H:i:s');
			if($periode=='tanggal'){
				//POSTING KE BUKU BESAR
				/*$sql="INSERT INTO buku_besar(buku_tanggal,
                             buku_ref,
                             buku_akun,
                             buku_akun_kode,
                             buku_debet,
                             buku_kredit,
							 buku_author)
					   SELECT tanggal,
							  no_jurnal,
							  akun,
							  akun_kode,
							  debet,
							  kredit,
							  '".@$_SESSION[SESSION_USERID]."'
						 FROM vu_jurnal_harian
						WHERE post <> 'Y' AND
							  date_format(tanggal,'%Y-%m-%d')>='".$tgl_awal."'  AND
						      date_format(tanggal,'%Y-%m-%d')<='".$tgl_akhir."'";
				//$this->firephp->log($sql);
				*/
				
				$sql="INSERT INTO buku_besar(buku_tanggal,
                             buku_ref,
                             buku_akun,
                             buku_akun_kode,
                             buku_keterangan,
                             buku_debet,
                             buku_kredit,
							 buku_author)
					   SELECT N.tanggal,
							  N.no_jurnal,
							  N.akun,
							  N.akun_kode,
							  N.keterangan,
							  N.debet,
							  N.kredit,
							   '".@$_SESSION[SESSION_USERID]."'
							  FROM (";
				 $sql.= "SELECT
				 			 concat('JU-',J.djurnal_id) as no_id,
				 			 J.`jurnal_no` AS `no_jurnal`,
							  date_format(J.`jurnal_tanggal`,'%Y-%m-%d') AS `tanggal`,
							  J.`djurnal_akun` AS `akun`,
							  J.`akun_kode` AS `akun_kode`,
							  J.`akun_nama` AS `akun_nama`,
							  J.`djurnal_detail` AS `keterangan`,
							  J.`djurnal_debet` AS `debet`,
							  J.`djurnal_kredit` AS `kredit`,
							  J.`jurnal_post` AS `post`,
							  J.`jurnal_date_post` AS `post_date`
						 FROM `vu_jurnal` J
						 WHERE (J.jurnal_post<>'Y' OR J.jurnal_post IS NULL)
						 AND date_format(J.jurnal_tanggal,'%Y-%m-%d')>=date_format('".$tgl_awal."','%Y-%m-%d')
						 AND date_format(J.jurnal_tanggal,'%Y-%m-%d')<=date_format('".$tgl_akhir."','%Y-%m-%d')";

					//JURNAL KASBANK
				   $sql.=" UNION ";
				   $sql.=" SELECT
				          concat('JK-',K.kasbank_detid) as no_id,
				   		  K.`no_jurnal` AS `no_jurnal`,
						  date_format(K.`tanggal`,'%Y-%m-%d') AS `tanggal`,
						  K.`akun` AS `akun`,
						  K.`akun_kode` AS `akun_kode`,
						  K.`akun_nama` AS `akun_nama`,
						  K.`keterangan` AS `keterangan`,
						  K.`debet` AS `debet`,
						  K.`kredit` AS `kredit`,
						  K.`post` AS `post`,
						  K.`post_date` AS `post_date`
					 FROM `vu_jurnal_bank` K
					 WHERE (K.post<>'Y' OR K.post is NULL)
					 AND date_format(K.tanggal,'%Y-%m-%d')>=date_format('".$tgl_awal."','%Y-%m-%d')
					 AND date_format(K.tanggal,'%Y-%m-%d')<=date_format('".$tgl_akhir."','%Y-%m-%d')";
					$sql.=") as N";
					
				$result=$this->db->query($sql);

				//UPDATE STATUS POSTING
				$sql_post_umum="UPDATE 	jurnal
								SET 	jurnal_post='Y',
										jurnal_date_post='".$tanggal."'
								WHERE 	date_format(jurnal_tanggal,'%Y-%m-%d')>='".$tgl_awal."'
										AND date_format(jurnal_tanggal,'%Y-%m-%d')<='".$tgl_akhir."'
										AND jurnal_post<>'Y'";
				$this->db->query($sql_post_umum);

				$sql_post_bank="UPDATE 	kasbank
								SET 	kasbank_post='Y',
										kasbank_date_post='".$tanggal."'
								WHERE 	date_format(kasbank_tanggal,'%Y-%m-%d')>='".$tgl_awal."'
										AND date_format(kasbank_tanggal,'%Y-%m-%d')<='".$tgl_akhir."'
										AND kasbank_post<>'Y'";
				$this->db->query($sql_post_bank);


			}else if($periode=='bulan'){


				//POSTING KE BUKU BESAR
				/*$sql="INSERT INTO buku_besar(buku_tanggal,
                             buku_ref,
                             buku_akun,
                             buku_akun_kode,
                             buku_debet,
                             buku_kredit)
					   SELECT tanggal,
							  no_jurnal,
							  akun,
							  akun_kode,
							  debet,
							  kredit
						 FROM vu_jurnal_harian
						WHERE post <> 'Y' AND
							  date_format(tanggal,'%Y-%m')='".$tahun."-".$bulan."'";
				*/
				
				$sql="INSERT INTO buku_besar(buku_tanggal,
                             buku_ref,
                             buku_akun,
                             buku_akun_kode,
                             buku_keterangan,
                             buku_debet,
                             buku_kredit,
							 buku_author)
					   SELECT N.tanggal,
							  N.no_jurnal,
							  N.akun,
							  N.akun_kode,
							  N.keterangan,
							  N.debet,
							  N.kredit,
							   '".@$_SESSION[SESSION_USERID]."'
							  FROM (";
				 $sql.= "SELECT concat('JU-',J.djurnal_id) as no_id,
				 			 J.`jurnal_no` AS `no_jurnal`,
							  date_format(J.`jurnal_tanggal`,'%Y-%m-%d') AS `tanggal`,
							  J.`djurnal_akun` AS `akun`,
							  J.`akun_kode` AS `akun_kode`,
							  J.`akun_nama` AS `akun_nama`,
							  J.`djurnal_detail` AS `keterangan`,
							  J.`djurnal_debet` AS `debet`,
							  J.`djurnal_kredit` AS `kredit`,
							  J.`jurnal_post` AS `post`,
							  J.`jurnal_date_post` AS `post_date`
						 FROM `vu_jurnal` J
						 WHERE (J.jurnal_post<>'Y' OR J.jurnal_post IS NULL)
						 AND date_format(J.jurnal_tanggal,'%Y-%m')='".$tahun."-".$bulan."'";

					//JURNAL KASBANK
				   $sql.=" UNION ";
				   $sql.=" SELECT
				   		  concat('JK-',K.kasbank_detid) as no_id,
				   		  K.`no_jurnal` AS `no_jurnal`,
						  date_format(K.`tanggal`,'%Y-%m-%d') AS `tanggal`,
						  K.`akun` AS `akun`,
						  K.`akun_kode` AS `akun_kode`,
						  K.`akun_nama` AS `akun_nama`,
						  K.`keterangan` AS `keterangan`,
						  K.`debet` AS `debet`,
						  K.`kredit` AS `kredit`,
						  K.`post` AS `post`,
						  K.`post_date` AS `post_date`
					 FROM `vu_jurnal_bank` K
					 WHERE (K.post<>'Y' OR K.post is NULL)
					 AND date_format(K.tanggal,'%Y-%m')='".$tahun."-".$bulan."'";

					$sql.=") as N";
					
				$result=$this->db->query($sql);
				//$this->firephp->log($sql);

				//UPDATE STATUS POSTING
				$sql_post_umum="UPDATE 	jurnal
								SET 	jurnal_post='Y',
										jurnal_date_post='".$tanggal."'
								WHERE 	date_format(jurnal_tanggal,'%Y-%m')='".$tahun."-".$bulan."'
										AND jurnal_post<>'Y'";
				$this->db->query($sql_post_umum);
				//$this->firephp->log($sql);

				$sql_post_bank="UPDATE 	kasbank
								SET 	kasbank_post='Y',
										kasbank_date_post='".$tanggal."'
								WHERE 	date_format(kasbank_tanggal,'%Y-%m')='".$tahun."-".$bulan."'
										AND kasbank_post<>'Y'";
				$this->db->query($sql_post_bank);

			}

			return '1';
		}
}
?>