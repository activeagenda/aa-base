CREATE TABLE `dat` (
   DateID int unsigned auto_increment not null,
   SourceModuleID varchar(5),
   SourceRecordID int,
   DateDescriptorID int,
   RelatedDate datetime,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      DateID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `dat_l` (
   _RecordID int unsigned not null auto_increment,
   DateID int unsigned  not null,
   SourceModuleID varchar(5),
   SourceRecordID int,
   DateDescriptorID int,
   RelatedDate datetime,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      DateID
   )
) TYPE=InnoDB;
