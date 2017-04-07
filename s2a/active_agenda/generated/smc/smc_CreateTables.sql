CREATE TABLE `smc` (
   CacheID int unsigned auto_increment not null,
   ModuleID varchar(5) not null,
   RecordID int not null,
   SubModuleID varchar(5) not null,
   SubRecordID int not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      CacheID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `smc_l` (
   _RecordID int unsigned not null auto_increment,
   CacheID int unsigned  not null,
   ModuleID varchar(5) not null,
   RecordID int not null,
   SubModuleID varchar(5) not null,
   SubRecordID int not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      CacheID
   )
) TYPE=InnoDB;
