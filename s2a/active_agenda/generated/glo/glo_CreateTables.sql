CREATE TABLE `glo` (
   GlossaryID int unsigned auto_increment not null,
   OrganizationID int,
   PersonAccountableID int,
   GlossaryItem varchar(128),
   Definition text,
   GlossaryURL varchar(128),
   Protected bool,
   Display bool,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      GlossaryID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `glo_l` (
   _RecordID int unsigned not null auto_increment,
   GlossaryID int unsigned  not null,
   OrganizationID int,
   PersonAccountableID int,
   GlossaryItem varchar(128),
   Definition text,
   GlossaryURL varchar(128),
   Protected bool,
   Display bool,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      GlossaryID
   )
) TYPE=InnoDB;
