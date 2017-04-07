<?php
/**
 * Example configurations for generating modules with s2a. 
 *
 * Copy and modify this file to fit your specific server/site settings. Save it under
 * the name 'gen-config.php'.
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
 * @copyright      2003-2009 Active Agenda Inc.
 * @license        http://www.activeagenda.net/license
 **/


/**
 *  Requires the main configurations
 */
require_once 'config.php';


/**
 * MySQL credentials for a more privileged MySQL user than the normal one specified in config.php
 *
 * Generating modules requires more MySQL privileges than the running application needs.
 * Specify a MySQL user that has "all" privileges to the database here. When installing,
 * this user can be created by the database install script.
 */
define('GEN_DB_USER', 'romek');
define('GEN_DB_PASS', 'swords');
define('GEN_DB_DSN', 'mysql://'.GEN_DB_USER.':'.GEN_DB_PASS.'@'. DB_HOST.'/'.DB_NAME);


/**
 *  These settings control the output of the module generating process
 */
define('DEBUG_LOG_FORMAT', 'unix'); //either 'win' (default) or 'unix'
define('DEBUG_STYLE_BACKTRACE', false);
define('DEBUG_STYLE_INDENT', true);
define('DEBUG_STYLE_PRINT_LEVEL', false);

/**
 *  The command to invoke PHP scripts from the command line.
 *
 *  This is optional, but useful if the s2a.php script (and others) keep asking
 *  for this location.  Use it by un-commenting (remove the leading "//") and
 *  specifying the command that is correct on your system.
 */
//define('GEN_PHP_EXEC', 'php');
?>
