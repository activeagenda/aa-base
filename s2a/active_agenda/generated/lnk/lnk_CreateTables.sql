CREATE TABLE `lnk` (
   LinkID int unsigned auto_increment not null,
   RelatedModuleID varchar(5),
   RelatedRecordID int,
   LinkAddress varchar(128),
   LinkTitle varchar(128),
   LinkDescription text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      LinkID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `lnk_l` (
   _RecordID int unsigned not null auto_increment,
   LinkID int unsigned  not null,
   RelatedModuleID varchar(5),
   RelatedRecordID int,
   LinkAddress varchar(128),
   LinkTitle varchar(128),
   LinkDescription text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      LinkID
   )
) TYPE=InnoDB;
