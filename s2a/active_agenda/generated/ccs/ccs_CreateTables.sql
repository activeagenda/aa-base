CREATE TABLE `ccs` (
   StateID int unsigned auto_increment not null,
   ModuleID varchar(5) not null,
   RecordID int not null,
   Inconsistent bool default null,
   Triggers varchar(128) not null,
   Targets varchar(128) not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      StateID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `ccs_l` (
   _RecordID int unsigned not null auto_increment,
   StateID int unsigned  not null,
   ModuleID varchar(5) not null,
   RecordID int not null,
   Inconsistent bool default null,
   Triggers varchar(128) not null,
   Targets varchar(128) not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      StateID
   )
) TYPE=InnoDB;
