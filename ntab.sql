DROP TABLE IF EXISTS nlogins;
CREATE TABLE nlogins (
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
) ENGINE=MyISAM;