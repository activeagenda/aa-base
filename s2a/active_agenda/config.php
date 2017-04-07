<?php
/**
 * Example configurations for S2A/Active Agenda
 *
 * Copy and modify this file to fit your specific server/site settings. Save it under
 * the name 'config.php'.
 *
 *
 * LICENSE NOTE:
 *
 * Copyright  2003-2009 Active Agenda Inc., All Rights Reserved.
 *
 * Unless explicitly acquired and licensed from Licensor under another license, the
 * contents of this file are subject to the Reciprocal Public License ("RPL")
 * Version 1.5, or subsequent versions as allowed by the RPL, and You may not copy
 * or use this file in either source code or executable form, except in compliance
 * with the terms and conditions of the RPL. You may obtain a copy of the RPL from
 * Active Agenda Inc. at http://www.activeagenda.net/license.
 *
 * All software distributed under the RPL is provided strictly on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, AND LICENSOR HEREBY
 * DISCLAIMS ALL SUCH WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT, OR
 * NON-INFRINGEMENT. See the RPL for specific language governing rights and
 * limitations under the RPL.
 *
 * @author         Mattias Thorslund <mthorslund@activeagenda.net>
 * @contributor    Mike O'Meara <momeara@orqa.org>
 * @copyright      2003-2009 Active Agenda Inc.
 * @license        http://www.activeagenda.net/license
 **/


/**
 *  The title of this Active Agenda site.
 *
 *  This is what's displayed on the login/logout screens, and in the title bar of the browser.
 */

define('SERVER_EXT_ADRR', 'www.example.com'); 
 
define('FOOTER_BAR', 'Customer or my address');
 
define('SITE_NAME', 'Active Agenda');


/**
 *  A short acronym or title for the site.
 *
 *  This will be used to prepend subject lines in email messages. Alpha-numeric 
 *  characters only. No spaces, commas, periods, etc.
 */
define('SITE_SHORTNAME', 'AA');


/**
 *  Name of the session ID cookie
 *
 *  This will be used for the session cookie name. Alpha-numeric
 *  characters only. No spaces, commas, periods, etc.
 */
define('SITE_SESSIONNAME', SITE_SHORTNAME . 'SESSID');


/**
 *  Default session timeout.
 *
 *  The amount of time (in minutes) before an inactive user session expires. Be
 *  careful NOT to extend this too much without understanding the potential
 *  consequences. The default 20 minutes should be acceptable for most uses.
 *  Integers only.
 */
define('SESSION_DEFAULT_TIMEOUT', 20);


/**
 *  Whether to enable user (and group) based session timeouts.
 *
 *  When set to true, the session timeouts of individual users may be defined
 *  through user settings, and user group membership.
 *  True or false only.
 */
define('SESSION_USER_TIMEOUT_ENABLED', false);


/**
 *  The root installation folder.
 *
 *  This should be located *outside* the web root. It should not be accessible from the
 *  web.  It should (theoretically) be possible for mutiple sites to share the s2a, lib and 
 *  pear files, since the more site-specific files and templates are reserved for the
 *  APP_FOLDER location.  On single-site Linux servers, we've used /var/www/s2a.
 */
define('S2A_FOLDER', '/var/www/s2a');


/**
 *  Classes used when generating and running Active Agenda
 */
define('CLASSES_PATH', S2A_FOLDER . '/lib/classes');


/**
 *  Utility functions used when generating and running Active Agenda
 */
define('INCLUDE_PATH', S2A_FOLDER . '/lib/includes');


/**
 *  Third-party utility classes from the PHP PEAR project
 */
define('PEAR_PATH', S2A_FOLDER . '/pear');


/**
 *  Templates for files generated from the XML definitions.
 */
define('MODELS_PATH', S2A_FOLDER . "/lib/templates");


/**
 *  Site-specific files
 *
 *  This folder contains folders that contain more site specific data, such as the themes, the
 *  web folder, the xml defintions folder, the generated fiels and the upload folder. This
 *  folder should also be outside the site's web root. This configuration file should be
 *  located here.
 */
define('APP_FOLDER', S2A_FOLDER . '/active_agenda');  //on Linux/Unix, maybe /var/www/s2a/active_agenda


/**
 *  Web root folder
 *
 *  Configured your web server settings so that HTTP requests to the the site are directed to
 *  files in this folder. This could be done easily with a "soft link" to this folder from the
 *  server's pre-configured web root, or you can configure a Directory and Alias in Apache's
 *  configuration file to point here, or you can set up the site as a virtual host...
 */
define('WEB_PATH', APP_FOLDER . '/web');


/**
 *  Third party javascript libraries: Menu, calendar, and more.
 */
define('THIRD_PARTY_PATH', APP_FOLDER . '/web/3rdparty');


/**
 *  Module include files for specific modules.
 *
 *  These includes are in this folder rather than in INCLUDE_PATH because they were thought
 *  to be more likely to be customized by end-users.
 */
define('APP_INCLUDE_PATH', APP_FOLDER . '/includes');


/**
 *  Root folder for themes
 *
 *  (Changed in 0.8.1)
 */
define('THEME_PATH', WEB_PATH . '/themes');


/**
 *  Web folder for themes
 *
 *  (New in 0.8.1)
 */
define('THEME_WEB_PATH', 'themes'); //note: no preceding "/"!


/**
 *  Point this path to the preferred default theme.
 */
define('DEFAULT_THEME', 'aa_theme');


/**
 *  Home of thousands of generated files.
 *
 *  When "generating" a module, the resulting files are saved here automatically.
 */
define('GENERATED_PATH', APP_FOLDER . '/generated');


/**
 *  Root folder for file uploads.
 *
 *  This is one of only two locations where the web server needs write permissions. Configure
 *  file permissions so that other users on the system can't read or write here.
 */
define('UPLOAD_PATH', APP_FOLDER . '/uploads');


/**
 *  Root folder for gettext library .po files.
 *
 *  This should contain all translation files, and be accessible by the gettext utility
 */
define('LOCALE_PATH', APP_FOLDER . '/lang');


/**
 *  Location of XML module definitions.
 *
 *  Web server does not need access to this folder. These files are used only when 
 *  (re)generating a module.
 */
define('XML_PATH', APP_FOLDER . '/xml');


/**
 *  Location of custom XML module definitions.
 *
 *  When defined, any matching files in this folder will override those in XML_PATH.
 */
define('CUSTOM_XML_PATH', APP_FOLDER . '/xml/custom');


/**
 *  Location for PDF report forms
 */
 //DEV: nowa wersja formularzy pdf
define('PDF_PATH', 'http://'.$_SERVER['SERVER_ADDR'].'/pdf');


/**
 *  Location for Video tutorials
 */
 //DEV: nowa wersja formularzy pdf
define('VIDEO_PATH', 'http://'.$_SERVER['SERVER_ADDR'].'/videos');

// kawalki szablonow dla xml Excel 2003
define('MSXML_PATH', APP_FOLDER . '/msxml');

/**
 *  Location for error and debug log files
 *
 *  This is the second location where the web server will need write access. Run-time
 *  error messages are appended to the errors.log file by the web server.  If the
 *  TRACE_RUNTIME option (not for production, only debugging) is enabled, the trace
 *  files will be saved here. The module generator (s2a.php and s2a-generate-module.php)
 *  also save log files here.
 */
define('GEN_LOG_PATH', APP_FOLDER . '/s2alog');

// Charts settings:
// gd or cairo display libarary
define('CHART_IMAGE_DRIVER', 'cairo');
// only if cairo libarary used
define('CHART_IMAGE_PIE_3D', true);
// tweaked colors installed by overwritting s2a/pear/ezc/Graph/palette/tango.php
define('CHART_PALETTE', 'custom');


/**
 *  Locale string, used by gettext.
 *
 *  Make sure this is a valid locale string on your system: Windows may use 'english-usa'
 */
define('DEFAULT_LOCALE', 'pl_PL');

/**
 *  Locale string, used by tiny_MCE
 *
  */
define('TINY_MCE_LANG', 'pl');

/**
 *  Master currency
 *
 *  This is the currency symbol that will be displayed in money fields. It will also be used
 *  when we implement multi-currency functionality.
 */
define('MASTER_CURRENCY', 'PLN');

//Decimal separartor dot or coma
define('DECIMAL_SEPARATOR', ',');

// Regular expresion for condition separation in validation
define('CONDITION_SEPARATOR_REGEX', '/\s+\|\s+/');

/**
 *  Seed value for enrypting user passwords in the database
 *
 *  Definitely change this when setting up the server.  Changing it when there already are 
 *  some users in the database would invalidate their passwords, which would need to be reset.
 */
define('CRYPT_SEED', 'default');


/**
 *  Database server type identifier.
 *
 *  Currently, only MySQL (v. 4.1, 5) is supported. This constant is meant to control the
 *  behavior of the SQL functionality, should the need arise to support other database
 *  servers.
 */
define('DB_TYPE', 'MySQL');


/**
 *  Database connection string
 *
 *  Change this to reflect your settings. Refer to the PEAR DB documentation here:
 *  http://pear.php.net/manual/en/package.database.db.intro-dsn.php
 *  The database installation script creates this MySQL user - change the name
 *  and password.
 *
 *  Choose the database name well. Underscores can be a source of trouble.
 */
define('DB_NAME', 'activeagenda');
define('DB_USER', 'theuser');
define('DB_PASS', 'thepassword');
define('DB_HOST', 'localhost');
define('DB_DSN',  'mysql://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.'/'.DB_NAME);


//If display Tutorial on the upper bar
define('TUTORIAL', true );

//If display Glossary on the upper bar
define('GLOSSARY', true );

//If display Forum on the upper bar
// URL for the "Forum" icon 
define('GLOBAL_FORUM', true );
define('FORUM_LINK_GLOBAL', 'http://www.example.com/');

//If display Forum on the upper bar
// URL for the "Report bug" icon (optional)
define('BUG_REPORT', true );
define('BUG_REPORT_LINK', 'mailto:bug@example.com');

/**
 *  URL to Active Agenda's global discussion forum.
 *
 *  This contains the URL to access a specific topic (there's one per forum), when combined
 *  with the unique value for the topic's ID, stored in the Module module (`mod`).
 */
define('DISCUSSION_LINK_GLOBAL', 'http://www.activeagenda.net/discussions/viewforum.php?f=');

/**
 *  URL to an organization-internal discussion forum.
 *
 *  To implement the link from the Active Agenda interface, some tweaking of the
 *  theme template files would be necessary (adding an extra icon).
 */
define('DISCUSSION_LINK_LOCAL', 'http://www.example.com/discussions/viewforum.php?f=');

// Who is Active Agenda Logo on this site
define('PROVIDER_LINK', 'http://www.example.com/');


/**
 *  An identifier for the type of navigation menu to use.
 *
 *  Currently, the only supported value is 'G5'
 */
define('MENU_TYPE', 'G5');


/**
 *  Optional custom navigation menu file.
 *
 *  To enable using a customized navigation menu file (without modifying the
 *  default Navigation.xml file), enable this setting by removing the leading
 *  two slashes.
 */
//define('CUSTOM_NAVIGATION_FILE', APP_FOLDER . '/customNavigation.xml');


/**
 *  Whether the application should do a DNS lookup when validating email addresses
 *
 *  When saving forms in the application that include fields validated as "Email",
 *  the default behavior is to only verify the format. When this setting is true,
 *  the application will also do a DNS lookup to verify that domain name part
 *  of the address is a valid, active domain.
 */
define('VALIDATE_EMAIL_DOMAINS', false);


/**
 *  Email address used for the Return-Path header in emails.
 *
 *  Specify an email address that should receive notification in case an email
 *  sent from the application could not be delivered because the recipient's
 *  address is no longer valid, or their inbox is full, or otherwise unreachable.
 */
define('BOUNCE_EMAIL_ADDRESS', 'root@localhost');


/**
 *  Maximum allowed file size, in bytes.
 *
 *  Note that there is a php.ini directive as well: upload_max_filesize.
 *  The ini-file directive is the limit that is enforced. This setting
 *  is sent to the browser as an advisory only.
 */
define('UPLOAD_MAX_FILE_SIZE', 2097152); //default 2.0 MiB


/**
 *  Whether to log EVERY page access in the usrl table
 *
 *  Login/logout and permission errors are logged to the table in either case.
 */
define('USER_LOG_PAGE_ACCESS', false);


/**
 *  Whether to send error messages by email.
 *
 *  When set to true, an email notification will be sent to the address specified
 * in the EMAIL_ERROR_ADDRESS setting.
 */
define('EMAIL_ERRORS', false);


/**
 *  Email address of an administrator to be contacted in case of errors.
 */
define('EMAIL_ERROR_ADDRESS', 'errors@example.com');


/**
 *  Whether to log errors to a log file.
 *
 *  The error log file is called errors.log, located in the GEN_LOG_PATH folder.
 */
define('LOG_ERRORS', true);


/**
 *  Whether to save a debug "trace".
 *
 *  Not for production use. When set to true, this setting will save a debug
 *  output file for each request to the server where the code contains any
 *  calls to the trace() function. This is for debugging purposes only, since
 *  it causes slower performance.
 */
define('TRACE_RUNTIME', false);

//Adding time and memory usage as comment at the end of the script
define('SCRIPT_PROFILING', 'false');

/**
 * Anonymous Account Username and Password.
 *
 * Anonymous data entry credentials for use via anonedit.php.
 * To use, create a user account (within the Active Agenda application) for
 * the purpose of anonymous data entry, and assign permissions for the desired
 * modules. Only modules with a defined AnonEditScreen support anonymous data
 * entry. Provide the anonymous account's user credentials below, but protect
 * them from being known elsewhere.
 */
//define('ANON_USER', 'anonymous');
//define('ANON_PASS', 'change_me');


/**
 *  Big brothers can choose to be emailed any time someone logs in :-)
 */
define('NOTIFY_LOGINS_EMAIL', false);


/**
 *  Email address where login noitifications should be sent if NOTIFY_LOGINS_EMAIL is enabled.
 */
define('EMAIL_LOGIN_NOTIFICATION_ADDRESS', 'logins@example.com');


/**
 *  Email address used as the "from" address for system messages such as errors or login notifications.
 */
define('EMAIL_SYSTEM_FROM_ADDRESS', 'root@localhost');


/**
 *  Defines PATH_SEPARATOR depending on operating system.
 */
if(!defined('PATH_SEPARATOR')){
        //You may simplify this according to your operating system
        if(strtolower(substr(php_uname('s'), 0, 3)) == "win") {
                define('PATH_SEPARATOR', ';');  //Windows
        } else {
                define('PATH_SEPARATOR', ':');  //non-Windows
        }
}


/**
 *  Defines whether the displayed items in the navigation menu will be limited to those modules defined in the Implementation Roadmap module (specifically, irmml).
 *
 *  Set this to true if the navigation menu should be limited by modules selected in the Implementation Roadmap.
 */
define('MENU_FILTER_IMPLEMENTED_MODULES', false);


/**
 *  Mailing method: none, mail, smtp, sendmail
 *
 *  The value 'none' will disable emailing functionality entirely,
 *  The values 'mail', 'smtp', and 'sendmail' correspond with the
 *  documentation for the PEAR Mail package. See MAILING_PARAMS
 *  below.
 */
define('MAILING_METHOD', 'sendmail');


/**
 *  Connection parameters for the email backend
 *
 *  The defined value should be an array. The relevant settings depend
 *  on the MAILING_METHOD defined above. See the notes at:
 *  http://pear.php.net/manual/en/package.mail.mail.factory.php
 *
 *  Parameters must be defined in a single string with the following format
 *  (example):
 *
 *  define('MAILING_PARAMS',
 *    'host = smtp.example.com
 *     username = my_username
 *     password = my_password
 *     auth = true
 *     debug = true');
 *
 *  I.e. parameter names and values are separated with an equals sign,
 *  and definition pairs are separated with line breaks.
 */
define('MAILING_PARAMS', 'sendmail_path = /usr/sbin/sendmail');

/**
 * Email parameters for sending messages by cron-notifications.php, cron-reminders.php
 * (the scripts don't run in Apache context-> IP can be extracted only vian DNS name)
 */
define( 'EMAIL_HTTP_HOST', 'http://www.example.com' );
define( 'SENDER_EMAIL', 'pu3.sygnity@gmail.com' );
define( 'SENDER_DISPLAY_NAME', 'Active Agenda' );



// Display relations box on the list where dashboard icon is placed
define('DISPLAY_RELATIONS', false );

/**
 *  A folder where charts exported by the cron-export-charts.php are saved.
 *
 *  Documentation about this functionality:
 *  http://www.activeagenda.net/documentation/index.php?title=Public_Charts
 *
 *  If you are not using this functionality, you may safely ignore or disable
 *  (by adding "//" at the beginning of the line) this setting.
 *
 *  NOTE: The default setting does not point to an existing folder. You must
 *  create this and give the user that executes the cron job permissions to 
 *  write files to it.
 */
define('CHART_EXPORT_LOCATION', WEB_PATH.'/public');


/**
 *  A template file for the exported charts.
 *
 *  If you change the code in this file, you may want to use a new filename and
 *  change this setting accordingly. This would prevent losing your changes
 *  if your theme is re-built.
 */
define('CHART_EXPORT_TEMPLATE', THEME_PATH.'/'.DEFAULT_THEME.'/export_html.php');



?>