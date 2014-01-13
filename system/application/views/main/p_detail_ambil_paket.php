<?php
/* 	These code was generated using phpCIGen v 0.1.b (24/06/2009)
	#zaqi 		zaqi.smart@gmail.com,http://zenzaqi.blogspot.com, 
	#CV. Trust Solution, jl. Saronojiwo 19 Surabaya, http://www.ts.co.id
	
	+ Module  		: Penjualan Print
	+ Description	: For Print View
	+ Filename 		: p_rekap_jual.php
 	+ Author  		: 
 	+ Created on 01/Feb/2010 14:30:05
	
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Detail Pengambilan Paket <?php echo $periode; ?> &nbsp;Group by No Faktur</title>
<link rel='stylesheet' type='text/css' href='../assets/modules/main/css/printstyle.css'/>
</head>
<body onload="window.print();">
<table summary='Detail Jual'>
	<caption>
	Laporan Detail Pengambilan Paket<br/><?php echo $periode; ?>&nbsp;Group By No Faktur</caption>
	<thead>
    	<tr>
        	<th scope='col'>No</th>
            <th scope='col'>Tanggal</th>           
            <th scope='col'>Customer</th>
            <th scope='col'>Pemakai</th>
          	<th scope='col'>Nama Paket</th>
            <th scope='col'>Nama Perawatan</th>
            <th scope='col'>Jumlah</th>
			<th scope='col'>Hrg Satuan</th>
			<th scope='col'>Total</th>
            <th scope='col'>Referal</th>
        </tr>
    </thead>
	<tbody><?php 	$i=0; 
				$nobukti=""; 
				$total_item=0;
				$j=0;	
		foreach($data_print as $printlist){
		
		if($nobukti!==$printlist->no_bukti){
		?><tr>
                <td><b><? $j++; echo $j; ?></b></td>
                <td colspan="8"><b><?php echo $printlist->no_bukti;?></b></td>
         </tr><?
				$i=0;
				$tot_medis = 0;
				$tot_non_medis = 0;
				$tot_aa = 0;
				$tot_surgery = 0;
				$tot_all = 0;
				$total = 0;
				$jum_all = 0;
				
		foreach($data_print as $print) {
		if ($print->kategori_nama == 'Medis') {
			$tot_medis = $tot_medis+$print->dapaket_jumlah*$print->harga_satuan;
		}
		if ($print->kategori_nama == 'Non Medis') {
			$tot_non_medis = $tot_non_medis+$print->dapaket_jumlah*$print->harga_satuan;
		}
		if ($print->kategori_nama == 'Anti Aging') {
			$tot_aa = $tot_aa+$print->dapaket_jumlah*$print->harga_satuan;
		}
		if ($print->kategori_nama == 'Surgery') {
			$tot_surgery = $tot_surgery+$print->dapaket_jumlah*$print->harga_satuan;
		}
		$tot_all = $tot_all + $print->harga_satuan;
		$jum_all = $jum_all + $print->dapaket_jumlah;
		$total = $total + $print->dapaket_jumlah*$print->harga_satuan;

				
				if($print->no_bukti==$printlist->no_bukti){ $i++;
		?><tr>
        	<td><? echo $i; ?></td>
            <td><?php echo $print->tanggal; ?></td>
            <td ><?php echo $print->cust_nama." (".$print->cust_no.")"; ?></td>
            <td ><?php echo $print->pemakai_nama; ?></td>
            <td ><?php echo $print->paket_nama; ?></td>
            <td ><?php echo $print->rawat_nama; ?></td>
            <td><?php echo $print->dapaket_jumlah; ?></td>
			<td class="numeric"><?php echo number_format($print->harga_satuan,0,",",","); ?></td>
			<td class="numeric"><?php echo number_format($print->dapaket_jumlah*$print->harga_satuan,0,",",","); ?></td>
            <td>&nbsp;<?php echo $print->referal; ?></td>
       </tr><?php } } ?><tr>

         </tr><?php } $nobukti=$printlist->no_bukti;  }	?></tbody>
    	<tfoot>
		<tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row' colspan='2'>Total Pengambilan Paket (Rp)</th>
            <td colspan='3'>&nbsp;</td> 
			<td><?php echo $jum_all; ?></td>
			<td> </td>
			<td align="right" class="numeric_bayar"><?php echo number_format($total,0,",",","); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row'>Medis</th>
            <td colspan='8'><?php echo number_format($tot_medis,0,",",","); ?></td>
        </tr>
		<tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row'>Non Medis</th>
            <td colspan='8'><?php echo number_format($tot_non_medis,0,",",","); ?></td>
        </tr>
				<tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row'>Anti Aging</th>
            <td colspan='8'><?php echo number_format($tot_aa,0,",",","); ?></td>
        </tr>
				<tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row'>Surgery</th>
            <td colspan='8'><?php echo number_format($tot_surgery,0,",",","); ?></td>
        </tr>
    </tfoot>
</table>
</body>
</html>