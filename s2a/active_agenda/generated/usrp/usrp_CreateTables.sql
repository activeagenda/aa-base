CREATE TABLE `usrp` (
   PermissionID int unsigned auto_increment not null,
   PersonID int unsigned not null,
   ModuleID varchar(5) not null,
   EditPermission tinyint unsigned not null,
   ViewPermission tinyint unsigned not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      PermissionID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `usrp_l` (
   _RecordID int unsigned not null auto_increment,
   PermissionID int unsigned  not null,
   PersonID int unsigned not null,
   ModuleID varchar(5) not null,
   EditPermission tinyint unsigned not null,
   ViewPermission tinyint unsigned not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      PermissionID
   )
) TYPE=InnoDB;
