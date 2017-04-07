CREATE TABLE `cod` (
   RecordID int unsigned not null auto_increment,
   CodeID int not null,
   CodeTypeID int not null,
   SortOrder int,
   Value varchar(25),
   Description varchar(128),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   _GlobalID varchar(20) default null,
   PRIMARY KEY(
      RecordID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `cod_l` (
   _RecordID int unsigned not null auto_increment,
   RecordID int unsigned not null ,
   CodeID int not null,
   CodeTypeID int not null,
   SortOrder int,
   Value varchar(25),
   Description varchar(128),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   _GlobalID varchar(20) default null,
   PRIMARY KEY(
      _RecordID,
      RecordID
   )
) TYPE=InnoDB;
