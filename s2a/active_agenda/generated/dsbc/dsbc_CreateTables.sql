CREATE TABLE `dsbc` (
   DashboardChartID int auto_increment not null,
   UserID int not null,
   ModuleID varchar(5) not null,
   ChartName varchar(10) not null,
   SortOrder int,
   ConditionPhrases text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      DashboardChartID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `dsbc_l` (
   _RecordID int unsigned not null auto_increment,
   DashboardChartID int  not null,
   UserID int not null,
   ModuleID varchar(5) not null,
   ChartName varchar(10) not null,
   SortOrder int,
   ConditionPhrases text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      DashboardChartID
   )
) TYPE=InnoDB;
