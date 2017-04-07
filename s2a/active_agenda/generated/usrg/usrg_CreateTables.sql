CREATE TABLE `usrg` (
   UserGroupID int unsigned not null auto_increment,
   Name varchar(25) not null,
   Description text,
   SessionTimeout int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      UserGroupID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `usrg_l` (
   _RecordID int unsigned not null auto_increment,
   UserGroupID int unsigned not null ,
   Name varchar(25) not null,
   Description text,
   SessionTimeout int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      UserGroupID
   )
) TYPE=InnoDB;
