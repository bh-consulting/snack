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
