CREATE TABLE `modch` (
   ModuleChartID int unsigned auto_increment not null,
   ModuleID varchar(5),
   Name varchar(10),
   Title varchar(128),
   Type varchar(10),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      ModuleChartID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `modch_l` (
   _RecordID int unsigned not null auto_increment,
   ModuleChartID int unsigned  not null,
   ModuleID varchar(5),
   Name varchar(10),
   Title varchar(128),
   Type varchar(10),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      ModuleChartID
   )
) TYPE=InnoDB;
