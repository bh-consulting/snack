#
# Table structure for table 'raduser'
#
CREATE TABLE raduser (
    id int(11) unsigned NOT NULL auto_increment,
    username varchar(64) NOT NULL default '',
    admin boolean default '0',
    cert_path varchar(255),
    comment text,
    is_cisco boolean default '0',
    is_loginpass boolean default '0',
    is_cert boolean default '0',
    is_mac boolean default '0',
    PRIMARY KEY (id),
    KEY username (username(32))
);

#
# Table structure for table 'radgroup'
#
CREATE TABLE radgroup (
    id int(11) unsigned NOT NULL auto_increment,
    groupname varchar(64) NOT NULL default '',
    cert_path varchar(255),
    comment text,
    is_cisco boolean default '0',
    is_loginpass boolean default '0',
    is_cert boolean default '0',
    is_mac boolean default '0',
    PRIMARY KEY (id),
    KEY groupname (groupname(32))
);

#
# Table structure for table 'logs'
#
CREATE TABLE logs (
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

