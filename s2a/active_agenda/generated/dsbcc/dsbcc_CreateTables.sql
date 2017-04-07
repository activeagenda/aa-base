CREATE TABLE `dsbcc` (
   ConditionID int auto_increment not null,
   ModuleID varchar(5) not null,
   UserID int not null,
   DashboardChartID int not null,
   ConditionField varchar(50) not null,
   ConditionValue varchar(50) not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      ConditionID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `dsbcc_l` (
   _RecordID int unsigned not null auto_increment,
   ConditionID int  not null,
   ModuleID varchar(5) not null,
   UserID int not null,
   DashboardChartID int not null,
   ConditionField varchar(50) not null,
   ConditionValue varchar(50) not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      ConditionID
   )
) TYPE=InnoDB;
