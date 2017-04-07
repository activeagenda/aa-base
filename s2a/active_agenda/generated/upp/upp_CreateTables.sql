CREATE TABLE `upp` (
   RecordID int unsigned auto_increment not null,
   PatchName varchar(25),
   Description text,
   ReleaseVersion varchar(10),
   AppliedStatusID int default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      RecordID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `upp_l` (
   _RecordID int unsigned not null auto_increment,
   RecordID int unsigned  not null,
   PatchName varchar(25),
   Description text,
   ReleaseVersion varchar(10),
   AppliedStatusID int default 0,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      RecordID
   )
) TYPE=InnoDB;
