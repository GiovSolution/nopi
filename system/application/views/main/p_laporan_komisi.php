<?php
/* 	These code was generated using phpCIGen v 0.1.b (24/06/2009) 
	#GIOV Solution
	
	+ Module  		: Laporan Komisi
	+ Description	: For Print View
	+ Filename 		: p_laporan_komisi.php
 	+ Author  		: Isaac
 	+ Created on 06/May/2013 13:38:00
	
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Komisi Sales <?php echo @$produk_nama; ?>, di <?php echo @$gudang_nama; ?>, Periode <?php echo @$periode; ?></title>
<link rel='stylesheet' type='text/css' href='../assets/modules/main/css/printstyle.css'/>
</head>
<body onload="window.print();">
<table summary='Rekap Jual'>
	<caption>Laporan Komisi Sales <?php echo @$produk_nama; ?><br/><?php echo @$gudang_nama; ?><br/> Periode <?php echo @$periode; ?></caption>
	<thead>
    	<tr>
        	<th scope='col'>Nama Karyawan</th>
            <th scope='col'>Total Penjualan</th>           
            <th scope='col'>Total Retur</th>
            <th scope='col'>Total Nett Penjualan</th>
            <th scope='col'>Poin</th>
			<th scope='col'>Total Komisi</th>
        </tr>
    </thead>
	<tbody>
		<?php 
			$i=0; 
			$tanggal=""; 
			$total_penjualan=0; 
			$total_retur=0; 
			$total_nett_penjualan=0; 
			$total_komisi=0; 
			foreach($data_print as $print) { $i++; ?>		
			<tr>
				<td><? echo $print->karyawan_nama; ?></td>
				<td align="right" class="numeric" width="100"><?php echo number_format($print->total_biaya,0); ?></td>
				<td align="right" class="numeric" width="100"><?php echo number_format($print->retur,0); ?></td>
				<td align="right" class="numeric" width="100"><?php echo number_format($print->total,0); ?></td>
				<td align="right" class="numeric"><?php echo $print->poin; ?></td>
				<td align="right" class="numeric" width="100"><?php echo number_format($print->komisi,0); ?></td>
		   </tr>
		<?php 
		$total_komisi+=($print->komisi);
		$total_penjualan+=($print->total_biaya);
		$total_retur+=($print->retur);
		$total_nett_penjualan+=($print->total);
		//$total_keluar+= ($print->keluar);
		
		} ?>
	</tbody>
	<tfoot>
			<tr>
				<td><b>T O T A L</td>
				<td align="right" class="numeric"><b><?php echo number_format($total_penjualan,0); ?></td>
				<td align="right" class="numeric"><b><?php echo number_format($total_retur,0); ?></td>
				<td align="right" class="numeric"><b><?php echo number_format($total_nett_penjualan,0); ?></td>
				<td align="right" class="numeric">&nbsp;</td>
				<td align="right" class="numeric"><b><?php echo number_format($total_komisi,0); ?></td>
		   </tr>
	</tfoot>	
</table>
</body>
</html>