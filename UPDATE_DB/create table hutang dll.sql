CREATE TABLE IF NOT EXISTS `hutang` (
  `hutang_id` int(10) NOT NULL AUTO_INCREMENT,
  `hutang_faktur` varchar(10) DEFAULT NULL,
  `hutang_supplier` int(11) DEFAULT NULL,
  `hutang_tanggal` date DEFAULT NULL,
  `hutang_keterangan` varchar(150) DEFAULT NULL,
  `hutang_status` enum('Lunas','Hutang') DEFAULT NULL,
  `hutang_total` double DEFAULT NULL,
  `hutang_bayar` double DEFAULT NULL,
  `hutang_sisa` double DEFAULT NULL,
  `hutang_stat_dok` enum('Terbuka','Terutup','Batal') DEFAULT NULL,
  `hutang_creator` varchar(20) DEFAULT NULL,
  `hutang_date_create` datetime DEFAULT NULL,
  `hutang_update` varchar(20) DEFAULT NULL,
  `hutang_date_update` datetime DEFAULT NULL,
  `hutang_revised` int(11) DEFAULT NULL,
  PRIMARY KEY (`hutang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='menampung daftar hutang';

CREATE TABLE IF NOT EXISTS `master_lunas_hutang` (
  `lhutang_id` int(10) NOT NULL AUTO_INCREMENT,
  `lhutang_nobukti` varchar(10) DEFAULT NULL,
  `lhutang_cust` int(11) DEFAULT NULL,
  `lhutang_tanggal` date DEFAULT NULL,
  `lhutang_cara` enum('tunai','kwitansi','card','cek/giro','transfer') DEFAULT NULL,
  `lhutang_bayar` double DEFAULT NULL,
  `lhutang_keterangan` varchar(150) DEFAULT NULL,
  `lhutang_stat_dok` enum('Terbuka','Tertutup','Batal') DEFAULT NULL,
  `lhutang_creator` varchar(20) DEFAULT NULL,
  `lhutang_date_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lhutang_update` varchar(20) DEFAULT NULL,
  `lhutang_date_update` datetime DEFAULT NULL,
  `lhutang_revised` int(11) DEFAULT NULL,
  PRIMARY KEY (`lhutang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detail_lunas_hutang` (
  `dhutang_id` int(10) NOT NULL AUTO_INCREMENT,
  `dhutang_master` int(10) DEFAULT '0',
  `dhutang_hutang` int(10) DEFAULT '0',
  `dhutang_nilai` double DEFAULT '0',
  `dhutang_keterangan` varchar(150) DEFAULT '0',
  PRIMARY KEY (`dhutang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

