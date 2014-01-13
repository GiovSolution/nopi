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
function myheader($jproduk_tanggal ,$cust_no ,$cust_nama ,$cust_alamat ,$jproduk_nobukti, $jproduk_jam, $jproduk_karyawan, $jproduk_karyawan_no){
?>

<table width="1240px" height="110px" border="0" cellspacing="0" cellpadding="0">
  <td height="50px"><font size=5><b>NATALIN SURABAYA</font></td>
  <tr>
    <td width="700px" align="center" valign="bottom" ><b>N O T A  P E N J U A L A N</td>
    <td width="540px" valign="bottom">
		<table width="540px" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="100px" align="right">Tanggal & Jam</td>
			<td width="480px">:&nbsp;&nbsp;
			  <?=$jproduk_tanggal;?> 
			  <?=$jproduk_jam;?></td>
		  </tr>
		  <tr>
			<td align="right">Nomor</td>
			<td>:&nbsp;&nbsp;
			  <?=$cust_no;?></td>
		  </tr>
		  <tr>
			<td align="right">Nama</td>
			<td>:&nbsp;&nbsp;
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
			<td align="right">Alamat</td>
			<td>:&nbsp;&nbsp;
			  <?=$cust_alamat;?>
			  </td>
		  </tr>	
		  <tr>
			<td align="right">&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
		  </tr>
		</table></td>
  </tr>
</table>
<table width="1240px" height="10px" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="700px" align="center"><?=$jproduk_nobukti;?></td>
    <td width="540px" valign="bottom"></td>
  </tr>
</table>
<table width="1240px" height="30px" border="0" cellspacing="0" cellpadding="0">
  <tr>
  	<td>&nbsp;</td>
  </tr>
</table>
<?php
}
?>
<?php
function content_header(){
?>
<table width="1240px" height="200px" border="1" cellspacing="0" cellpadding="0">
  <tr>
  	<td width="1240px" height="200px" valign="top"><table width="1240px" border="1" cellspacing="0" cellpadding="0">
	<tr>
  	    <td width="490px" align="Center"><b>Nama Barang</td>
  	    <td width="150px"><b>Jumlah   Satuan</td>
  	    <td width="160px" align="Center"><b>Harga</td>
  	    <td width="170px" align="Center"><b>Disk</td>
  	    <td width="270px" align="Center"><b>Subtotal</td>
	    </tr>
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
		$f_jproduk_nobukti = $jproduk_nobukti;
		$f_jproduk_jam = $jproduk_jam;
		$f_jproduk_karyawan = $jproduk_karyawan;
		$f_jproduk_karyawan_no = $jproduk_karyawan_no;

		
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
		
		$i=0;
		$total=0;
		$subtotal=0;
		$total_diskon_tamb_tamb=0;
		$total_voucher=0;
		
		$dcount = sizeof($detail_jproduk);
		foreach($detail_jproduk as $list => $row){
			$i+=1;
			if(($i%15)==1){
				myheader($f_jproduk_tanggal ,$f_cust_no ,$f_cust_nama ,$f_cust_alamat ,$f_jproduk_nobukti, $f_jproduk_jam, $f_jproduk_karyawan, $f_jproduk_karyawan_no);
				content_header();
			}
	  ?>
  	  <tr>
  	    <td width="490px">&nbsp;
  	      <?=$i;?>
  	      .&nbsp;
  	      <?=$row->produk_nama;?></td>
  	    <td width="150px">&nbsp;
  	      <?=$row->dproduk_jumlah;?>
  	      <?=$row->satuan_nama;?></td>
  	    <td width="160px" align="right">&nbsp;
  	      <?=rupiah(($row->dproduk_harga));?></td>
  	    <td width="170px" align="right">&nbsp;
  	      <?=$row->dproduk_diskon;?></td>
  	    <td width="270px" align="right">&nbsp;
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
						 ,$total_voucher ,$total_diskon_tamb, $f_iklantoday_keterangan, $f_mbank_nama, $f_mbank_rekening, $f_mbank_atasnama);
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
				  ,$total_voucher ,$total_diskon_tamb, $f_iklantoday_keterangan, $mbank_nama, $mbank_rekening, $mbank_atasnama){
?>
<table width="1240px" border="0" cellspacing="0" cellpadding="0">
   <tr>
    <? /*<td height="30px" colspan="5"><?=$f_iklantoday_keterangan;?> </td>*/	?>
	<td height="30px" colspan="5"></td>
  </tr>
  <tr>
    <td width="160px">&nbsp;</td>
    <? /*<td width="280px"><?=$_SESSION[SESSION_USERID];?></td> */ ?>
	<td width="280px">Pembayaran harap ditransfer melalui rekening :<br> <b><?=$mbank_nama;?></td>
    <td width="420px">&nbsp;</td>
    <td width="180px">&nbsp;</td>
    <td width="200px" align="right"><?=rupiah($subtotal);?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>No. Rek : <b><?=$mbank_rekening;?></td>
    <td>* Komplain barang dan harga hanya 7 hari setelah barang diterima</td>
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
	<td>Atas Nama : <b><?=$mbank_atasnama;?></td>
    <td>* Pembayaran dengan Cek/Giro dianggap lunas setelah diuangkan</td>
    <td>&nbsp;</td>
    <td align="right"><?php if($total_voucher<>0){?>
      <?=rupiah($total_voucher);?>
      <?php }?>
      <?php if($total_diskon_tamb<>0){?>
      <?=rupiah($total_diskon_tamb);?>
      <?php }?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><?php if($cara_bayar2<>''){?>
      <?=$cara_bayar2;?>
      &nbsp;:&nbsp;
      <?=rupiah($nilai_bayar2);?>
      <?php }?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <!--<td align="right"><//?=rupiah($jumlah_bayar);?></td>-->
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><?php if($cara_bayar3<>''){?>
      <?=$cara_bayar3;?>
      &nbsp;:&nbsp;
      <?=rupiah($nilai_bayar3);?>
      <?php }?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><?=rupiah($total);?></td>
  </tr>
</table>
<?php
}
?>
</body>
</html>
