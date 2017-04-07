CREATE TABLE `inv` (
   InvolvementID int unsigned auto_increment not null,
   SourceModuleID varchar(5),
   SourceRecordID int,
   PersonInvolvedID int,
   InvolvementDescriptorID int,
   Details text,
   InvolvementStatusID int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      InvolvementID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `inv_l` (
   _RecordID int unsigned not null auto_increment,
   InvolvementID int unsigned  not null,
   SourceModuleID varchar(5),
   SourceRecordID int,
   PersonInvolvedID int,
   InvolvementDescriptorID int,
   Details text,
   InvolvementStatusID int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      InvolvementID
   )
) TYPE=InnoDB;
