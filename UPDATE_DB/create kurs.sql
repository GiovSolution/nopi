-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 15, 2012 at 06:37 PM
-- Server version: 5.1.33
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `nopi`
--

-- --------------------------------------------------------

--
-- Table structure for table `kurs`
--

CREATE TABLE IF NOT EXISTS `kurs` (
  `kurs_id` int(10) NOT NULL AUTO_INCREMENT,
  `kurs_negara` varchar(50) DEFAULT NULL,
  `kurs_initial` varchar(10) DEFAULT NULL,
  `kurs_nilai` double DEFAULT '0',
  `kurs_tanggal` datetime DEFAULT NULL,
  `kurs_keterangan` varchar(500) DEFAULT NULL,
  `kurs_aktif` enum('Aktif','Tidak Aktif') DEFAULT NULL,
  `kurs_creator` varchar(30) DEFAULT NULL,
  `kurs_date_create` datetime DEFAULT NULL,
  `kurs_update` varchar(30) DEFAULT NULL,
  `kurs_date_update` datetime DEFAULT NULL,
  `kurs_revised` int(11) DEFAULT NULL,
  PRIMARY KEY (`kurs_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
