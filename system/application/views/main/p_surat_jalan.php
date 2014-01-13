<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cetak Penjualan Produk</title>
<style type="text/css">
html,body,table,tr,td{
	font-family:Geneva, Arial, Helvetica, sans-serif;
	font-size:12px;
}
.title{
	font-size:12px;
}
.pagebreak {
page-break-after: always;
}
</style>
</head>
<body onload="window.print();">
<? //window.close();?>
<?php
function myheader($jproduk_tanggal ,$cust_no ,$cust_nama ,$cust_alamat ,$cust_kota, $jproduk_nobukti, $jproduk_jam, $jproduk_karyawan, $jproduk_karyawan_no){
?>

<table width="1250px" height="110px" border="0" cellspacing="0" cellpadding="0">
  <td height="50px" width="300px" align="left"><font size=5><b>NATALIN<br><font size=2>SURABAYA</font></font></td>
  <td height="50px" width="600px" colspan="2" align="left"><font size=3><b>
  	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S U R A T  J A L A N<br><font size=3>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$jproduk_nobukti;?></font></td>
  <tr>
	<td width="100px" align="left" valign="bottom" >
		<table width="300px" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td align="left" colspan="2"><b>Kepada Yth, </td>
		  </tr>
		  <tr>
			<td align="right"></td>
			<td><b>&nbsp;&nbsp;
			  <?=$cust_no;?></td>
		  </tr>
		  <tr>
			<td align="right"></td>
			<td><b>&nbsp;&nbsp;
			  <?=$cust_nama;?>
			  <?
				$nama_karyawan=$jproduk_karyawan;
				if ($nama_karyawan <> 'NA')
				{
					?>(<?=$jproduk_karyawan;?>,<?=$jproduk_karyawan_no;?>)<? 
				}
			  
			  ?>
			  </td>
		  </tr>
		  <tr>
			<td align="right"></td>
			<td><b>&nbsp;&nbsp;
			  <?=$cust_alamat;?>
			</td>
		  </tr>			  
		  <tr>
			<td align="right"></td>
			<td><b>&nbsp;&nbsp;
				<?=$cust_kota;?>
				</td>
		  </tr>
		</table>
	</td>

	<td width="300px" align="center" valign="bottom" ><b></td>
    <td width="300px" align="right" valign="bottom">
	<table width="400px" border="0" cellspacing="0" cellpadding="0">
			 <tr>



				<td align="left" colspan="2"></td>
			 </tr>


			 <tr>

				<td width="150px" align="right">Tanggal & Jam Jual</td>
				<td width="200px">:&nbsp;&nbsp;
				  <?=$jproduk_tanggal;?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				  <?=$jproduk_jam;?></td>
			  </tr>
			  <tr>
				<td align="right"></td>
				<td></td>
			  </tr>
			  <tr>
				<td align="right"></td>
				<td></td>
			  </tr>	
			  <tr>
				<td align="right"></td>
				<td></td>
			  </tr>	
			</table>
		</td>
	  </tr>
</table>
<?php
}
?>
<?php
function content_header(){
?>
<table width="1250px" height="200px" border="0" cellspacing="0" cellpadding="0">
  <tr>
  	<td width="1250px" height="300px" valign="top">
		<table width="1250px" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan=9>
			<table width="1250px" border="1" cellspacing="0" cellpadding="0">
			<tr>
			<td>
				<table width="1250px" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="20px"><b>No.</td>
						<td width="75px" align="center"><b>Kode</td>
						<td width="275px" align="center"><b>Nama Barang</td>
						<td width="150px" align="center"><b>Quantitas</td>
						<td width="100px" align="center"><b>Isi Per Colly</td>
						<td width="100px" align="center"><b>Jumlah Colly</td>
					</tr>
				</table>
				</td>
				</tr>
			</table>
			</td>
		<tr>
<?php
}
?>

	  <?php
		/* data header */
		$f_jproduk_tanggal = $jproduk_tanggal;
		$f_cust_no = $cust_no;
		$f_cust_nama = $cust_nama;
		$f_cust_alamat = $cust_alamat;
		$f_cust_kota = $cust_kota;
		$f_cust_ekspedisi = $cust_ekspedisi;
		$f_jproduk_nobukti = $jproduk_nobukti;
		$f_jproduk_jam = $jproduk_jam;
		$f_jproduk_karyawan = $jproduk_karyawan;
		$f_jproduk_karyawan_no = $jproduk_karyawan_no;
	
		$i=0;
		
		$dcount = sizeof($detail_jproduk);
		$total_colly = 0;
		foreach($detail_jproduk as $list => $row){
			$i+=1;
			if(($i%15)==1){
				myheader($f_jproduk_tanggal ,$f_cust_no ,$f_cust_nama ,$f_cust_alamat ,$f_cust_kota, $f_jproduk_nobukti, $f_jproduk_jam, $f_jproduk_karyawan, $f_jproduk_karyawan_no);
				content_header();
			}
	  ?>
	  <?
		$total_colly = $total_colly+$row->dsurat_jalan_jumlah_colly;
	  ?>
	  
	  <tr>
		<td width="20px"><?=$i;?>.
		<td width="75px">&nbsp;&nbsp;&nbsp;<?=$row->produk_kode;?></td>
  	    <td width="235px">&nbsp;&nbsp;<?=$row->produk_nama;?></td>
  	    <td width="85px">&nbsp;</td>
  	    <td width="100px">&nbsp;
  	      <?=$row->dsurat_jalan_jumlah;?>
  	      <?=$row->satuan_nama;?></td>
  	    <td width="60px" align="right"><?=$row->dsurat_jalan_isi_colly;?></td>
  	    <td width="60px" align="right">&nbsp;</td>
  	    <td width="50px" align="right">&nbsp;
			<?
				$jml_colly = $row->dsurat_jalan_jumlah_colly;
				$jumlah = $row->dsurat_jalan_jumlah;
				$isi_colly = $row->dsurat_jalan_isi_colly;
				//echo $jumlah.' '.$isi_colly;
				if ($isi_colly <> 0) {
					if (($jumlah%$isi_colly)==0)
						echo $jml_colly;
				}

			?>
		</td>
		<td width="30px" align="right">&nbsp;</td>
	  </tr>
  	  <?php 
			
			if($i==$dcount){
				content_footer();
				myfooter($total_colly, $f_cust_ekspedisi);
			}elseif(($i>1) && ($i%15==0)){
				content_footer();
				echo "<div class='pagebreak'></div>";
			}
		}
	  ?>
<?php
function content_footer(){
?>
    </table></td>
  </tr>  
</table>
<?php
}
?>
<?php
function myfooter($total_colly, $f_cust_ekspedisi){
?>
<table width="1250px" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="6"><hr size=5 noshade></td>
	</tr>
	<tr>
		<td height="30px" colspan="6">Via Ekspedisi : 
		<? 
			if ($f_cust_ekspedisi=='')
				echo '..............................';
			else
				echo $f_cust_ekspedisi;
		
		?> <b>&nbsp;&nbsp;| Jumlah Colly : ..........<?// echo $total_colly;?> &nbsp; Colly</td>
	</tr>
	<tr>
		<td height="100px" align="center" valign="top"><b>Diterima</td>
		<td height="100px" align="center" valign="top"><b>Diketahui</td>
		<td height="100px" align="center" valign="top"><b>Hormat  Kami</td>
		
	</tr>
</table>
<?php
}
?>
</body>
</html>
