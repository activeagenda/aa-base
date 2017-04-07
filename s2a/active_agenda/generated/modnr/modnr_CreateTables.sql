CREATE TABLE `modnr` (
   NotificationRecipientID int unsigned auto_increment not null,
   RelatedModuleID varchar(5),
   OrganizationID int unsigned not null,
   RecipientID int unsigned,
   Details text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      NotificationRecipientID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `modnr_l` (
   _RecordID int unsigned not null auto_increment,
   NotificationRecipientID int unsigned  not null,
   RelatedModuleID varchar(5),
   OrganizationID int unsigned not null,
   RecipientID int unsigned,
   Details text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      NotificationRecipientID
   )
) TYPE=InnoDB;
