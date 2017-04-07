CREATE TABLE `cti` (
   CountyID int unsigned auto_increment not null,
   StateID int unsigned,
   CountyName varchar(50),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   _GlobalID varchar(20) default null,
   PRIMARY KEY(
      CountyID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `cti_l` (
   _RecordID int unsigned not null auto_increment,
   CountyID int unsigned  not null,
   StateID int unsigned,
   CountyName varchar(50),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   _GlobalID varchar(20) default null,
   PRIMARY KEY(
      _RecordID,
      CountyID
   )
) TYPE=InnoDB;
