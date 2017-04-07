CREATE TABLE `modrp` (
   ModuleReportID int unsigned auto_increment not null,
   ModuleID varchar(5) not null,
   Name varchar(50),
   Title varchar(50),
   LocationLevel varchar(10),
   LocationGroup varchar(20),
   Format varchar(15),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      ModuleReportID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `modrp_l` (
   _RecordID int unsigned not null auto_increment,
   ModuleReportID int unsigned  not null,
   ModuleID varchar(5) not null,
   Name varchar(50),
   Title varchar(50),
   LocationLevel varchar(10),
   LocationGroup varchar(20),
   Format varchar(15),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      ModuleReportID
   )
) TYPE=InnoDB;
