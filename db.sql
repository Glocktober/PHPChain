-- MySQL dump 8.21
--
-- Host: localhost    Database: keychain
---------------------------------------------------------
-- Server version	3.23.48

--
-- Table structure for table 'cat'
--

CREATE TABLE cat (
  id int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  title varchar(32) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY userid (userid)
) ;

--
-- Table structure for table 'loginlog'
--

CREATE TABLE loginlog (
  name varchar(30) default NULL,
  ip varchar(16) default NULL,
  date datetime default NULL,
  outcome tinyint(4) default NULL,
  KEY name (name)
) ;

--
-- Table structure for table 'logins'
--

CREATE TABLE logins (
  id int(11) NOT NULL auto_increment,
  iv varchar(24) NOT NULL default '',
  userid int(11) NOT NULL default '0',
  catid int(11) NOT NULL default '0',
  login text,
  password text,
  site text,
  url text,
  PRIMARY KEY  (id),
  KEY userid (userid),
  KEY catid (catid)
) ;

--
-- Table structure for table 'user'
--

CREATE TABLE user (
  id int(11) NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  teststring text,
  iv varchar(24) default NULL,
  PRIMARY KEY  (id)
) ;

