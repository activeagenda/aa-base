CREATE TABLE `acc` (
   AccountabilityID int unsigned auto_increment not null,
   SourceModuleID varchar(5),
   SourceRecordID int unsigned not null,
   PersonAccountableID int unsigned,
   AccountabilityDescriptorID int unsigned,
   Details text,
   AccountabilityStatusID int unsigned not null default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      AccountabilityID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `acc_l` (
   _RecordID int unsigned not null auto_increment,
   AccountabilityID int unsigned  not null,
   SourceModuleID varchar(5),
   SourceRecordID int unsigned not null,
   PersonAccountableID int unsigned,
   AccountabilityDescriptorID int unsigned,
   Details text,
   AccountabilityStatusID int unsigned not null default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      AccountabilityID
   )
) TYPE=InnoDB;
