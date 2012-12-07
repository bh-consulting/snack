CREATE TABLE raduser (
    id int(11) unsigned not null auto_increment,
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

CREATE TABLE radgroup (
    id int(11) unsigned not null auto_increment,
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
