<?php
/**
 * Include for command-line utilities.
 *
 * This script expects the variables $script_location (local path to host 
 * script) and $config (array that defines possible command-line arguments).
 &
 *
 * PHP version 5
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
 * @license        http://www.activeagenda.net/license  RPL 1.5
 * @version        SVN: $Revision: 1643 $
 * @last-modified  SVN: $Date: 2009-05-22 07:30:08 +0200 (Pt, 22 maj 2009) $
 */

if(!defined('PATH_SEPARATOR')){
    if(strtolower(substr(php_uname('s'), 0, 3)) == "win") {
        define('PATH_SEPARATOR', ';');
    } else {
        define('PATH_SEPARATOR', ':');
    }
}

//get PEAR class that handles command line arguments
set_include_path($script_location.'/pear' . PATH_SEPARATOR . get_include_path());
require_once 'Console/Getargs.php';

$config['help'] =
    array('short' => 'h',
        'max'   => 0,
        'desc'  => 'Show this help.'
    );

$args =& Console_Getargs::factory($config);
if (PEAR::isError($args)) {
    if ($args->getCode() === CONSOLE_GETARGS_ERROR_USER) {
        // User put illegal values on the command line.
        echo Console_Getargs::getHelp($config, null, $args->getMessage())."\n";
    } else if ($args->getCode() === CONSOLE_GETARGS_HELP) {
        // User needs help.
        echo Console_Getargs::getHelp($config)."\n";
    }
    exit;
}

if(isset($config['project'])){
    $Project = $args->getValue('project');
}
if(empty($Project)){
    $Project = 'active_agenda';
}

$site_folder = $script_location.'/'.$Project;
if(!file_exists($site_folder)){
    print "The project folder '$Project' does not exist.\n";
    exit;
}

//includes
$config_file = $site_folder . '/config.php';
if(!file_exists($config_file)){
    print "Config file not found at $config_file\n";
    exit;
}
$gen_config_file = $site_folder . '/gen-config.php';
if(!file_exists($gen_config_file)){
    print "Config file not found at $gen_config_file\n";
    exit;
}


//get settings
include_once $config_file;
include_once $gen_config_file;

//this include contains utility functions used by command-line scripts
include_once INCLUDE_PATH . '/parse_util.php';
include_once INCLUDE_PATH . '/general_util.php';

/**
 * Sets custom error handler
 */
set_error_handler('handleError');
