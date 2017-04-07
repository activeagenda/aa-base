CREATE TABLE `tut` (
   RecordID int unsigned auto_increment not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      RecordID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `tut_l` (
   _RecordID int unsigned not null auto_increment,
   RecordID int unsigned  not null,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      RecordID
   )
) TYPE=InnoDB;
