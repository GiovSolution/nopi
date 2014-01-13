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
function myheader($jproduk_tanggal ,$cust_no ,$cust_nama ,$cust_alamat, $cust_kota ,$jproduk_nobukti, $jproduk_jam, $jproduk_karyawan, $jproduk_karyawan_no, $jproduk_jth_tempo, $selisih, $jproduk_sales){
?>

<table width="1250px" height="110px" border="0" cellspacing="0" cellpadding="0">
  <td height="50px" width="300px" align="left"><font size=5><b>NATALIN</font> <br><font size=2>SURABAYA</font></td>
  <td height="50px" width="600px" colspan="2" align="left"><font size=3><b>N O T A  P E N J U A L A N<br><font size=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$jproduk_nobukti;?></font></td>
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
			<td width="200px" align="right">Tanggal :&nbsp;</td>
			<td width="200px">
			  <?=$jproduk_tanggal;?></td>
		  </tr>
		  <tr>
			<td align="right">J. Tempo :&nbsp;</td>
			<td> <?=$jproduk_jth_tempo;?> </td>
		  </tr>
		  <tr>
			<td align="right">Status :&nbsp;</td>
			<td>KREDIT&nbsp;<?=$selisih;?> &nbsp;hari</td>
		  </tr>	
		  <tr>
			<td align="right">Sales :&nbsp;</td>
			<td><?=$jproduk_sales;?></td>
		  </tr>	
		  <tr>
		  <td align="right">Operator :&nbsp;</td>
			<td><?=$_SESSION[SESSION_USERID];?></td>
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
	<td width="1250px" height="15px" valign="top">
		<table width="1250px" border="1" cellspacing="0" cellpadding="0">
			<tr>
			<td>
			<table width="1250px" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="10px" height="15px"><font size=2><b>No.</td>
					<td width="75px" align="center"><font size=2><b>Kode</td>
					<td width="275px" align="center"><font size=2><b>Nama Barang
					<td width="100px" align="center"><font size=2><b>Quantitas</td>
					<td width="10px" align="center"><font size=2>&nbsp;</td>
					<td width="90px" align="center"><font size=2><b>Harga</td>
					<td width="50px" align="center"><font size=2><b>Disk 1</td>
					<td width="50px" align="center"><font size=2><b>Disk 2</td>
					<td width="10px" align="center"><font size=2><b>&nbsp;</td>
					<td width="90px" align="center"><font size=2><b>Subtotal</td>
				</tr>
			</table>
			</td>
			</tr>
		</table>
	</td>
	</tr>
  	<td width="1250px" height="300px" valign="top"><table width="1250px" border="0" cellspacing="0" cellpadding="0">
<?php
}
?>
  	  <?php
		/* data header */
		$f_jproduk_tanggal = $jproduk_tanggal;
		$f_iklantoday_keterangan = $iklantoday_keterangan;
		$f_cust_no = $cust_no;
		$f_cust_nama = $cust_nama;
		$f_cust_alamat = $cust_alamat;
		$f_cust_kota = $cust_kota;
		$f_jproduk_nobukti = $jproduk_nobukti;
		$f_jproduk_jam = $jproduk_jam;
		$f_jproduk_jth_tempo = $jproduk_jth_tempo;
		$f_jproduk_karyawan = $jproduk_karyawan;
		$f_jproduk_karyawan_no = $jproduk_karyawan_no;
		$f_selisih = $selisih;
		$f_sales = $sales;

		
		/* data footer */
		$f_cara_bayar1 = $cara_bayar1;
		$f_nilai_bayar1 = $nilai_bayar1;
		$f_cara_bayar2 = $cara_bayar2;
		$f_nilai_bayar2 = $nilai_bayar2;
		$f_cara_bayar3 = $cara_bayar3;
		$f_nilai_bayar3 = $nilai_bayar3;
		$f_mbank_nama =  $mbank_nama;
		$f_mbank_rekening = $mbank_rekening;
		$f_mbank_atasnama = $mbank_atasnama;
		$f_terbilang =  $total_biaya_terbilang;
		
		$i=0;
		$total=0;
		$subtotal=0;
		$total_diskon_tamb_tamb=0;
		$total_voucher=0;
		
		$dcount = sizeof($detail_jproduk);
		foreach($detail_jproduk as $list => $row){
			$i+=1;
			if(($i%15)==1){
				myheader($f_jproduk_tanggal ,$f_cust_no ,$f_cust_nama ,$f_cust_alamat, $f_cust_kota ,$f_jproduk_nobukti, $f_jproduk_jam, $f_jproduk_karyawan, $f_jproduk_karyawan_no, $f_jproduk_jth_tempo, $f_selisih, $f_sales);
				content_header();
			}
	  ?>
  	  <tr>
		<td width="10px" height="20px">&nbsp;
  	      <?=$i;?>.&nbsp;</td>
		<td width="75px">&nbsp;&nbsp;&nbsp;
  	      <?=$row->produk_kode;?></td>
  	    <td width="255px">&nbsp;&nbsp;
  	      <?=$row->produk_nama;?></td>
  	    <td width="130px" align="right">&nbsp;
  	      <?=$row->dproduk_jumlah;?>
  	      <?=$row->satuan_kode;?></td>
  	    <td width="40px" align="right">&nbsp;</td>
  	    <td width="10px" align="right">Rp.</td>
		<td width="60px" align="right">&nbsp;
			<?=rupiah(($row->dproduk_harga));?></td>
  	    <td width="50px" align="right">&nbsp;
  	      <?=$row->dproduk_diskon;?>&nbsp; %</td>
		<td width="50px" align="right">&nbsp;
  	      <?=$row->dproduk_diskon2;?>&nbsp; %</td>
		<td width="20px" align="right">&nbsp;</td>
		<td width="10px" align="right">Rp.</td>
  	    <td width="50px" align="right">&nbsp;
  	      <?=rupiah(($row->dproduk_jumlah)*($row->jumlah_subtotal));?></td>
	    </tr>
  	  <?php 
			$subtotal+=(($row->dproduk_jumlah)*($row->jumlah_subtotal));
			
			if($i==$dcount){
				$total=($subtotal*((100-$jproduk_diskon)/100)-$jproduk_cashback);
				$total_diskon_tamb=($subtotal*($jproduk_diskon/100));
				$total_voucher= $jproduk_cashback;
				
				content_footer();
				myfooter($subtotal ,$total ,$f_cara_bayar1 ,$f_nilai_bayar1 ,$f_cara_bayar2 ,$f_nilai_bayar2 ,$f_cara_bayar3 ,$f_nilai_bayar3
						 ,$total_voucher ,$total_diskon_tamb, $f_iklantoday_keterangan, $f_mbank_nama, $f_mbank_rekening, $f_mbank_atasnama, $f_terbilang);
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
function myfooter($subtotal ,$total ,$cara_bayar1 ,$nilai_bayar1 ,$cara_bayar2 ,$nilai_bayar2 ,$cara_bayar3 ,$nilai_bayar3
				  ,$total_voucher ,$total_diskon_tamb, $f_iklantoday_keterangan, $mbank_nama, $mbank_rekening, $mbank_atasnama, $terbilang){
?>
<table width="1250px" border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td colspan="6"><hr size=5 noshade></td>
  </tr>
  <tr>
    <? /*<td height="30px" colspan="5"><?=$f_iklantoday_keterangan;?> </td>*/	?>
	<td colspan="3"><b>Terbilang : # <?=$terbilang;?> #</td>
	<td colspan="3"align="right"><b>Jumlah : Rp. &nbsp;<?=rupiah($subtotal);?></td>
  </tr>
  <tr>
    <? /*<td height="30px" colspan="5"><?=$f_iklantoday_keterangan;?> </td>*/	?>
	<td colspan="6">&nbsp;</td>
  </tr>
  <tr>
    <td width="10px">&nbsp;</td>
    <? /*<td width="280px"><?=$_SESSION[SESSION_USERID];?></td> */ ?>
	<td width="700px"><? if ($mbank_nama<>'No Bank') { ?>Pembayaran harap ditransfer melalui rekening :<br> <b><?=$mbank_nama;?> <? } ?></td>
    <td width="150px" align="center">Diterima</td>
    <td width="150px" align="right">Hormat Kami</td>
    <td align="right"></td>
	<td width="50">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><? if ($mbank_nama<>'No Bank') { ?>No. Rek : <b><?=$mbank_rekening;?> <? } ?></td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <? /*<td><?php if($cara_bayar1<>''){?>
      <?=$cara_bayar1;?>
      &nbsp;:&nbsp;
      <?=rupiah($nilai_bayar1);?>
      <?php }?></td> */ ?>
	<td><? if ($mbank_nama<>'No Bank') { ?>Atas Nama : <b><?=$mbank_atasnama;?><? } ?></td>
    <td></td>
    <td>&nbsp;</td>
    <td align="right"><?php if($total_voucher<>0){?>
      <?=rupiah($total_voucher);?>
      <?php }?>
      <?php if($total_diskon_tamb<>0){?>
      <?=rupiah($total_diskon_tamb);?>
      <?php }?></td>
	  <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>* Komplain barang dan harga hanya 7 hari setelah barang diterima</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
    <!--<td align="right"><//?=rupiah($jumlah_bayar);?></td>-->
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>* Pembayaran dengan Cek/Giro dianggap lunas setelah diuangkan</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"></td>
	<td>&nbsp;</td>
  </tr>
</table>
<?php
}
?>
</body>
</html>
