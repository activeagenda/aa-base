CREATE TABLE `trx` (
   TransactionID bigint unsigned auto_increment not null,
   TransactionDate datetime not null,
   UserID int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      TransactionID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `trx_l` (
   _RecordID int unsigned not null auto_increment,
   TransactionID bigint unsigned  not null,
   TransactionDate datetime not null,
   UserID int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      TransactionID
   )
) TYPE=InnoDB;
