CREATE TABLE `ntfr` (
   NotificationRecipientID int unsigned not null auto_increment,
   NotificationID int unsigned not null,
   RecipientID int not null,
   StatusID int default 1,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      NotificationRecipientID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `ntfr_l` (
   _RecordID int unsigned not null auto_increment,
   NotificationRecipientID int unsigned not null ,
   NotificationID int unsigned not null,
   RecipientID int not null,
   StatusID int default 1,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      NotificationRecipientID
   )
) TYPE=InnoDB;
