CREATE TABLE `rdc` (
   ModuleID varchar(5) not null,
   RecordID int not null,
   OrganizationID int,
   Value text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      ModuleID,
      RecordID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `rdc_l` (
   _RecordID int unsigned not null auto_increment,
   ModuleID varchar(5) not null,
   RecordID int not null,
   OrganizationID int,
   Value text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      ModuleID,
      RecordID
   )
) TYPE=InnoDB;
