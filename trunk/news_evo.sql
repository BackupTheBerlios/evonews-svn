-- phpMyAdmin SQL Dump
-- version 2.6.3-rc1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 06, 2005 at 10:15 PM
-- Server version: 4.1.12
-- PHP Version: 5.0.4
-- 
-- Database: `news_evo`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `ne_categories`
-- 

CREATE TABLE `ne_categories` (
  `cid` int(4) unsigned NOT NULL auto_increment,
  `ctitle` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='News Categories' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `ne_categories`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ne_comments`
-- 

CREATE TABLE `ne_comments` (
  `comid` int(10) unsigned NOT NULL auto_increment,
  `nid` int(10) unsigned NOT NULL default '0',
  `date` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `comment` text NOT NULL,
  `moderated` enum('false','true') NOT NULL default 'true',
  `deleted` enum('false','true') NOT NULL default 'false',
  PRIMARY KEY  (`comid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Comments' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `ne_comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ne_info`
-- 

CREATE TABLE `ne_info` (
  `display` smallint(2) unsigned NOT NULL default '15',
  `comment_html` enum('false','true') NOT NULL default 'false',
  `comment_mod` enum('false','true') NOT NULL default 'true',
  `comment_on` enum('false','true') NOT NULL default 'true',
  `max_comment_length` int(10) unsigned NOT NULL default '2140000',
  `max_article_length` int(10) unsigned NOT NULL default '2140000',
  `max_title_length` smallint(3) unsigned NOT NULL default '100',
  `min_comment_length` int(10) unsigned NOT NULL default '15',
  `pagelinks` smallint(2) unsigned NOT NULL default '3'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Configeration Settings for $info';

-- 
-- Dumping data for table `ne_info`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ne_news`
-- 

CREATE TABLE `ne_news` (
  `nid` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL default '0',
  `cid` int(4) unsigned NOT NULL default '0',
  `deleted` enum('false','true') NOT NULL default 'false',
  `title` varchar(50) NOT NULL default '',
  `short` text NOT NULL,
  `full` text,
  `date` int(10) unsigned NOT NULL default '0',
  `ip_address` varchar(15) NOT NULL default '',
  `comments` enum('false','true') NOT NULL default 'false',
  PRIMARY KEY  (`nid`),
  KEY `uid` (`uid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='News' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `ne_news`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ne_sessions`
-- 

CREATE TABLE `ne_sessions` (
  `hash` varchar(32) NOT NULL default '',
  `uid` int(10) NOT NULL default '0',
  `time` int(10) NOT NULL default '0',
  `ip_address` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`hash`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Login Sessions';

-- 
-- Dumping data for table `ne_sessions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ne_users`
-- 

CREATE TABLE `ne_users` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `utitle` varchar(50) default NULL,
  `password` varchar(32) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `permissions` text NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Users' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `ne_users`
-- 

