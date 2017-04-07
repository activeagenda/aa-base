CREATE TABLE `usr` (
   PersonID int unsigned not null,
   Username varchar(25),
   Password varchar(50),
   IsAdmin bool,
   RequireNewPassword bool default 1,
   LangID varchar(5),
   DefaultOrganizationID int,
   SessionTimeout int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      PersonID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `usr_l` (
   _RecordID int unsigned not null auto_increment,
   PersonID int unsigned not null,
   Username varchar(25),
   Password varchar(50),
   IsAdmin bool,
   RequireNewPassword bool default 1,
   LangID varchar(5),
   DefaultOrganizationID int,
   SessionTimeout int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      PersonID
   )
) TYPE=InnoDB;
