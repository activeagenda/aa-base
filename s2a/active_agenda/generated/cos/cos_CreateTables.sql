CREATE TABLE `cos` (
   CostID int unsigned auto_increment not null,
   RelatedModuleID varchar(5),
   RelatedRecordID int,
   OrganizationID int,
   PersonAccountableID int,
   CostTypeID int,
   Incurred decimal(12,4),
   Title varchar(128),
   CostDesc text,
   PONumber varchar(50),
   BudgetConsideration bool default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      CostID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `cos_l` (
   _RecordID int unsigned not null auto_increment,
   CostID int unsigned  not null,
   RelatedModuleID varchar(5),
   RelatedRecordID int,
   OrganizationID int,
   PersonAccountableID int,
   CostTypeID int,
   Incurred decimal(12,4),
   Title varchar(128),
   CostDesc text,
   PONumber varchar(50),
   BudgetConsideration bool default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      CostID
   )
) TYPE=InnoDB;
