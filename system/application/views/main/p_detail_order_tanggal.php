<?php
/* 	
	GIOV Solution - Keep IT Simple
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Detail Order Pembelian <?php echo $periode; ?> Group By Tanggal</title>
<link rel='stylesheet' type='text/css' href='../assets/modules/main/css/printstyle.css'/>
</head>
<body onload="window.print();">
<table summary='Detail Order Pembelian'>
	<caption>Laporan Order Pembelian<br/><?php echo $periode; ?><br/>Group By Tanggal</caption>
	<thead>
    	<tr>
        	<th scope='col'>No</th>
            <th scope='col'>Supplier</th>
            <th scope='col'>No Faktur</th>
            <th scope='col'>Nama Barang/Jasa</th>
            <th scope='col'>Satuan</th>
            <th scope='col'>Jumlah</th>
            <th scope='col'></th>
            <th scope='col'>Harga</th>
            <th scope='col'>Diskon1(%)</th>
            <th scope='col'>Diskon1(Rp)</th> 
            <th scope='col'>Diskon2(%)</th>
            <th scope='col'>Diskon2(Rp)</th>
            <th scope='col'>Total Nilai (Rp)</th>
        </tr>
    </thead>
	<tbody>
		        
        <?php 	$i=0; $j=0; $tanggal=""; 
				$total_item=0;
				$total_terima=0;
                $total_diskon=0;
				$total_diskon2=0;
				$total_nilai=0;
				foreach($data_print as $print) { 
			?>
			<?php if($tanggal!==$print->tanggal) { ?>
           <tr>
                <td><b><? $j++; echo $j; ?></b></td>
                <td colspan="12"><b><?php echo $print->tanggal;?></b></td>
           </tr>
           <?php 	$sub_cashback=0;
					$sub_total=0;
                    $sub_diskon=0;
					$sub_diskon2=0;
					$sub_jumlah_barang=0;
					// $sub_jumlah_terima=0;
					$i=0; 
			?>
           <?php foreach($data_print as $print_list) {  ?>
           <?php if($print_list->tanggal==$print->tanggal){ $i++;
		   			$sub_jumlah_barang+=$print_list->jumlah_barang;
					// $sub_jumlah_terima+=$print_list->jumlah_terima;
                    $sub_diskon+=$print_list->diskon_nilai;
					$sub_diskon2+=$print_list->diskon_nilai2;
					$sub_total+=$print_list->subtotal;
					//-----------------
					$total_item+=$print_list->jumlah_barang;
					// $total_terima+=$print_list->jumlah_terima;
                    $total_diskon+=$print_list->diskon_nilai;
					$total_diskon2+=$print_list->diskon_nilai2;
					$total_nilai+=$print_list->subtotal;
		   ?>
            <tr>
                <td><? echo $i; ?></td>
                <td><?php echo $print_list->supplier_nama."(".$print_list->supplier_akun.")"; ?></td>
                <td><?php echo $print_list->no_bukti; ?></td>
                <td><?php echo $print_list->produk_nama."( ".$print_list->produk_kode.")"; ?></td>
                <td><?php echo $print_list->satuan_nama; ?></td>
                <td class="numeric"><?php echo number_format($print_list->jumlah_barang,0,",","."); ?></td>
                <td class="numeric"><?php echo number_format("",0,",","."); ?></td>
                <td class="numeric"><?php echo number_format($print_list->harga_satuan,2,",","."); ?></td>
                <td class="numeric"><?php echo number_format($print_list->diskon,0,",","."); ?></td>
                <td class="numeric"><?php echo number_format($print_list->diskon_nilai,2,",","."); ?></td> 
                <td class="numeric"><?php echo number_format($print_list->diskon2,0,",","."); ?></td>
                <td class="numeric"><?php echo number_format($print_list->diskon_nilai2,2,",","."); ?></td>
                <td class="numeric"><?php echo number_format($print_list->subtotal,2,",","."); ?></td>
           </tr>
           <?php } ?>
           <?php } ?>
           <tr>
                <td colspan="5">&nbsp;</td>
                <td align="right" class="numeric"><b><?php echo number_format($sub_jumlah_barang,0,",","."); ?></b></td>
                <td align="right" class="numeric"><b><?php echo number_format("",0,",","."); ?></b></td>
                <td align="right" class="numeric">&nbsp;</td>
                <td align="right" class="numeric">&nbsp;</td>
                <td align="right" class="numeric"><b><?php echo number_format($sub_diskon,2,",","."); ?></b></td>
                <td align="right" class="numeric">&nbsp;</td>
                <td align="right" class="numeric"><b><?php echo number_format($sub_diskon2,2,",","."); ?></b></td>
                <td align="right" class="numeric"><b><?php echo number_format($sub_total,2,",","."); ?></b></td>
           </tr>
           <?php }
		   		
		   		$tanggal=$print->tanggal; ?>
		<?php } ?>
        
	</tbody>
    	<tfoot>
    	<tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row' nowrap="nowrap">Total</th>
            <td colspan='11'><?php echo count($data_print); ?> data</td>
        </tr>
        <tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row' colspan="12">Summary</th>
        </tr>
        <tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row' nowrap="nowrap">Total Item</th>
            <td class="numeric clear" nowrap="nowrap"><?php echo number_format($total_item,0,",","."); ?></td>
            <td colspan="10" class="clear">&nbsp;</td>
        </tr>
        <tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row' nowrap="nowrap"></th>
            <td class="numeric clear" nowrap="nowrap"><?php echo number_format($total_terima,0,",","."); ?></td>
            <td colspan="10" class="clear">&nbsp;</td>
        </tr>
        <tr>
            <td class="clear">&nbsp;</td>
            <th scope='row' nowrap="nowrap">Total Diskon 1(Rp)</th>
            <td class="numeric clear" nowrap="nowrap" ><?php echo number_format($total_diskon,2,",","."); ?></td>
            <td colspan="10" class="clear">&nbsp;</td>
        </tr> 
        <tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row' nowrap="nowrap">Total Diskon 2(Rp)</th>
            <td class="numeric clear" nowrap="nowrap" ><?php echo number_format($total_diskon2,2,",","."); ?></td>
            <td colspan="10" class="clear">&nbsp;</td>
        </tr>
        <tr>
        	<td class="clear">&nbsp;</td>
        	<th scope='row' nowrap="nowrap">Total Nilai (Rp)</th>
            <td class="numeric clear" nowrap="nowrap"><?php echo number_format($total_nilai,2,",","."); ?></td>
            <td colspan="10" class="clear">&nbsp;</td>
        </tr>
	</tfoot>
</table>
</body>
</html>