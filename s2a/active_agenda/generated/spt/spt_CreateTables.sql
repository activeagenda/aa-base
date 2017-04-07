CREATE TABLE `spt` (
   SupportDocumentID int unsigned auto_increment not null,
   ModuleID varchar(5),
   LocalDocumentationStatusID int,
   WikiArticle varchar(128),
   WikiArticleStatusID int,
   WikiGuide varchar(128),
   WikiGuideStatusID int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      SupportDocumentID
   )
) TYPE=InnoDB;

-- statement separator --
CREATE TABLE `spt_l` (
   _RecordID int unsigned not null auto_increment,
   SupportDocumentID int unsigned  not null,
   ModuleID varchar(5),
   LocalDocumentationStatusID int,
   WikiArticle varchar(128),
   WikiArticleStatusID int,
   WikiGuide varchar(128),
   WikiGuideStatusID int,
   _ModDate datetime not null,
   _ModBy int unsigned not null default 0,
   _Deleted bool not null default 0,
   _TransactionID bigint unsigned not null default 0,
   PRIMARY KEY(
      _RecordID,
      SupportDocumentID
   )
) TYPE=InnoDB;
