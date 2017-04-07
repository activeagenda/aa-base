CREATE TABLE `usrpo` (
   PermitOrganizationID int unsigned auto_increment not null,
   PersonID int unsigned not null,
   OrganizationID int unsigned not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      PermitOrganizationID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `usrpo_l` (
   _RecordID int unsigned not null auto_increment,
   PermitOrganizationID int unsigned  not null,
   PersonID int unsigned not null,
   OrganizationID int unsigned not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      PermitOrganizationID
   )
) TYPE=InnoDB;
