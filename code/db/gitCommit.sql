CREATE TABLE radius.backups (
  id int(11) unsigned NOT NULL auto_increment,
  commit varchar(64) NOT NULL,
  datetime DATETIME NOT NULL,
  nas varchar(100)  NOT NULL,
  action varchar(50) NOT NULL ,
  users varchar(256) NOT NULL ,
  PRIMARY KEY  (id)
) ;
