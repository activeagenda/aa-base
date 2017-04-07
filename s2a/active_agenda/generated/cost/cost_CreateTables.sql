CREATE TABLE `cost` (
   CostTypeID int auto_increment not null,
   CostCategoryID int,
   PersonAccountableID int,
   CostTitle varchar(128),
   CostTypeDesc text,
   Expenditure bool default 1,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      CostTypeID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `cost_l` (
   _RecordID int unsigned not null auto_increment,
   CostTypeID int  not null,
   CostCategoryID int,
   PersonAccountableID int,
   CostTitle varchar(128),
   CostTypeDesc text,
   Expenditure bool default 1,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      CostTypeID
   )
) TYPE=InnoDB;
