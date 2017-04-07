CREATE TABLE `usrgm` (
   MembershipID int unsigned not null auto_increment,
   UserGroupID int unsigned not null,
   PersonID int unsigned not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      MembershipID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `usrgm_l` (
   _RecordID int unsigned not null auto_increment,
   MembershipID int unsigned not null ,
   UserGroupID int unsigned not null,
   PersonID int unsigned not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      MembershipID
   )
) TYPE=InnoDB;
