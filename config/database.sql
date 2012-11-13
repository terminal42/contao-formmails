-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_form`
-- 

CREATE TABLE `tl_form` (
  `cmail` char(1) NOT NULL default '',
  `cmail_templates` blob NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- 
-- Table `tl_form_mails`
-- 

CREATE TABLE `tl_form_mails` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `cmailSender` varchar(255) NOT NULL default '',
  `cmailRecipient` varchar(255) NOT NULL default '',
  `cmailSubject` varchar(255) NOT NULL default '',
  `cmailBcc` varchar(255) NOT NULL default '',
  `cmailMessage` text NULL,
  `form_post` blob NULL,
  `form_files` blob NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

