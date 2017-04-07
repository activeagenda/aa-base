CREATE TABLE `codtd` (
   CodeTypeDependencyID int unsigned auto_increment not null,
   CodeTypeID int not null,
   DependencyID varchar(5),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      CodeTypeDependencyID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `codtd_l` (
   _RecordID int unsigned not null auto_increment,
   CodeTypeDependencyID int unsigned  not null,
   CodeTypeID int not null,
   DependencyID varchar(5),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      CodeTypeDependencyID
   )
) TYPE=InnoDB;
