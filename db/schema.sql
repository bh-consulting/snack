###########################################################################
# $Id$                 #
#                                                                         #
#  schema.sql                       rlm_sql - FreeRADIUS SQL Module       #
#                                                                         #
#     Database schema for MySQL rlm_sql module                            #
#                                                                         #
#     To load:                                                            #
#         mysql -uroot -prootpass radius < schema.sql                     #
#                                                                         #
#                                   Mike Machado <mike@innercite.com>     #
###########################################################################

GRANT ALL ON radius.* to 'radius'@'localhost';

#
# Table structure for table 'radacct'
#
CREATE TABLE radius.radacct (
  radacctid bigint(21) NOT NULL auto_increment,
  acctsessionid varchar(64) NOT NULL default '',
  acctuniqueid varchar(32) NOT NULL default '',
  username varchar(64) NOT NULL default '',
  groupname varchar(64) NOT NULL default '',
  realm varchar(64) default '',
  nasipaddress varchar(15) NOT NULL default '',
  nasportid varchar(15) default NULL,
  nasporttype varchar(32) default NULL,
  acctstarttime datetime NULL default NULL,
  acctstoptime datetime NULL default NULL,
  acctsessiontime int(12) default NULL,
  acctauthentic varchar(32) default NULL,
  connectinfo_start varchar(50) default NULL,
  connectinfo_stop varchar(50) default NULL,
  acctinputoctets bigint(20) default NULL,
  acctoutputoctets bigint(20) default NULL,
  calledstationid varchar(50) NOT NULL default '',
  callingstationid varchar(50) NOT NULL default '',
  acctterminatecause varchar(32) NOT NULL default '',
  servicetype varchar(32) default NULL,
  framedprotocol varchar(32) default NULL,
  framedipaddress varchar(15) NOT NULL default '',
  acctstartdelay int(12) default NULL,
  acctstopdelay int(12) default NULL,
  xascendsessionsvrkey varchar(10) default NULL,
  PRIMARY KEY  (radacctid),
  UNIQUE KEY acctuniqueid (acctuniqueid),
  KEY username (username),
  KEY framedipaddress (framedipaddress),
  KEY acctsessionid (acctsessionid),
  KEY acctsessiontime (acctsessiontime),
  KEY acctstarttime (acctstarttime),
  KEY acctstoptime (acctstoptime),
  KEY nasipaddress (nasipaddress)
) ENGINE = INNODB;

#
# Table structure for table 'radcheck'
#

CREATE TABLE radius.radcheck (
  id int(11) unsigned NOT NULL auto_increment,
  username varchar(64) NOT NULL default '',
  attribute varchar(64)  NOT NULL default '',
  op char(2) NOT NULL DEFAULT '==',
  value varchar(253) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY username (username(32))
) ;

#
# Table structure for table 'radgroupcheck'
#

CREATE TABLE radius.radgroupcheck (
  id int(11) unsigned NOT NULL auto_increment,
  groupname varchar(64) NOT NULL default '',
  attribute varchar(64)  NOT NULL default '',
  op char(2) NOT NULL DEFAULT '==',
  value varchar(253)  NOT NULL default '',
  PRIMARY KEY  (id),
  KEY groupname (groupname(32))
) ;

#
# Table structure for table 'radgroupreply'
#

CREATE TABLE radius.radgroupreply (
  id int(11) unsigned NOT NULL auto_increment,
  groupname varchar(64) NOT NULL default '',
  attribute varchar(64)  NOT NULL default '',
  op char(2) NOT NULL DEFAULT '=',
  value varchar(253)  NOT NULL default '',
  PRIMARY KEY  (id),
  KEY groupname (groupname(32))
) ;

#
# Table structure for table 'radreply'
#

CREATE TABLE radius.radreply (
  id int(11) unsigned NOT NULL auto_increment,
  username varchar(64) NOT NULL default '',
  attribute varchar(64) NOT NULL default '',
  op char(2) NOT NULL DEFAULT '=',
  value varchar(253) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY username (username(32))
) ;


#
# Table structure for table 'radusergroup'
#

CREATE TABLE radius.radusergroup (
  username varchar(64) NOT NULL default '',
  groupname varchar(64) NOT NULL default '',
  priority int(11) NOT NULL default '1',
  KEY username (username(32))
) ;

#
# Table structure for table 'radpostauth'
#

CREATE TABLE radius.radpostauth (
  id int(11) NOT NULL auto_increment,
  username varchar(64) NOT NULL default '',
  pass varchar(64) NOT NULL default '',
  reply varchar(32) NOT NULL default '',
  authdate timestamp NOT NULL,
  PRIMARY KEY  (id)
) ENGINE = INNODB;

#
# Table structure for table 'nas'
#
CREATE TABLE radius.nas (
  id int(10) NOT NULL auto_increment,
  nasname varchar(128) NOT NULL,
  shortname varchar(32),
  type varchar(30) DEFAULT 'other',
  ports int(5),
  secret varchar(60) DEFAULT 'secret' NOT NULL,
  server varchar(64),
  community varchar(50),
  description varchar(200) DEFAULT 'RADIUS Client',
  PRIMARY KEY (id),
  KEY nasname (nasname)
);

#
# Table structure for table 'raduser'
#
CREATE TABLE radius.raduser (
    id int(11) unsigned NOT NULL auto_increment,
    username varchar(64) NOT NULL default '',
    role varchar(64) default 'user',
    comment text,
    is_cisco boolean default '0',
    is_loginpass boolean default '0',
    is_phone boolean default '0',
    is_cert boolean default '0',
    is_mac boolean default '0',
    PRIMARY KEY (id),
    KEY username (username(32))
);

#
# Table structure for table 'radgroup'
#
CREATE TABLE radius.radgroup (
    id int(11) unsigned NOT NULL auto_increment,
    groupname varchar(64) NOT NULL default '',
    comment text,
    PRIMARY KEY (id),
    KEY groupname (groupname(32))
);

#
# 'backup_freeradius.sql'
#
CREATE USER 'logsfreeradius'@'localhost' IDENTIFIED BY 'logsfreeradius';

CREATE TABLE radius.logs (
	id bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	host varchar(128) default NULL,
	facility varchar(10) default NULL,
	priority varchar(10) default NULL,
	level varchar(10) default NULL,
	tag varchar(10) default NULL,
	datetime datetime default NULL,
	program varchar(15) default NULL,
	msg text
);

GRANT ALL ON radius.logs TO 'logsfreeradius'@'localhost';


#
# 'gitCommit.sql' 
#
CREATE TABLE radius.backups (
  id int(11) unsigned NOT NULL auto_increment,
  commit varchar(64) DEFAULT NULL,
  datetime DATETIME NOT NULL,
  nas varchar(100)  NOT NULL,
  action varchar(50) NOT NULL ,
  users varchar(256) NOT NULL ,
  restore varchar(64) DEFAULT NULL,
  PRIMARY KEY  (id)
) ;
