CREATE TABLE `spts` (
   SupportDocumentSectionID int unsigned auto_increment not null,
   SupportDocumentID int unsigned not null,
   Title varchar(50) not null,
   SectionText text,
   SortOrder int,
   Protected bool,
   Display bool,
   SectionID varchar(25),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      SupportDocumentSectionID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `spts_l` (
   _RecordID int unsigned not null auto_increment,
   SupportDocumentSectionID int unsigned  not null,
   SupportDocumentID int unsigned not null,
   Title varchar(50) not null,
   SectionText text,
   SortOrder int,
   Protected bool,
   Display bool,
   SectionID varchar(25),
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      SupportDocumentSectionID
   )
) TYPE=InnoDB;
