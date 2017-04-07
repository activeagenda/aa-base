CREATE TABLE `usrdi` (
   RecordID int auto_increment not null,
   PersonID int,
   ModuleID varchar(5),
   Dismiss bool,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      RecordID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `usrdi_l` (
   _RecordID int unsigned not null auto_increment,
   RecordID int  not null,
   PersonID int,
   ModuleID varchar(5),
   Dismiss bool,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      RecordID
   )
) TYPE=InnoDB;
