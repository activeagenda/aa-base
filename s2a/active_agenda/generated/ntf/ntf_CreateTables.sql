CREATE TABLE `ntf` (
   NotificationID int unsigned auto_increment not null,
   RelatedModuleID varchar(5) not null,
   RelatedRecordID int not null,
   Subject varchar(50) not null,
   Message text,
   XMLAttached bool,
   StatusID int,
   SenderID int,
   SentDate datetime,
   TextContent text,
   HTMLContent text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      NotificationID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `ntf_l` (
   _RecordID int unsigned not null auto_increment,
   NotificationID int unsigned  not null,
   RelatedModuleID varchar(5) not null,
   RelatedRecordID int not null,
   Subject varchar(50) not null,
   Message text,
   XMLAttached bool,
   StatusID int,
   SenderID int,
   SentDate datetime,
   TextContent text,
   HTMLContent text,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      NotificationID
   )
) TYPE=InnoDB;
