CREATE TABLE `trxr` (
   TransactionRecordID bigint unsigned auto_increment not null,
   TransactionID bigint unsigned not null default 0,
   RelatedModuleID varchar(5),
   RelatedRecordID int,
   Indirect bool default 0,
   ActionTypeID tinyint unsigned not null default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      TransactionRecordID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `trxr_l` (
   _RecordID int unsigned not null auto_increment,
   TransactionRecordID bigint unsigned  not null,
   TransactionID bigint unsigned not null default 0,
   RelatedModuleID varchar(5),
   RelatedRecordID int,
   Indirect bool default 0,
   ActionTypeID tinyint unsigned not null default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      TransactionRecordID
   )
) TYPE=InnoDB;
