CREATE TABLE raduser (
    username varchar(64) NOT NULL default '',
    admin boolean default '0',
    cert_path varchar(255),
    PRIMARY KEY (username)
);
