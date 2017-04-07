<?php
/**
 * Utility to reset the password of a given user.
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
 * @version        SVN: $Revision: 1376 $
 * @last-modified  SVN: $Date: 2008-12-19 15:40:22 -0800 (Fri, 19 Dec 2008) $
 */


if(!defined('PATH_SEPARATOR')){
    if(strtolower(substr(php_uname('s'), 0, 3)) == "win") {
        define('PATH_SEPARATOR', ';');
    } else {
        define('PATH_SEPARATOR', ':');
    }
}

//get PEAR class that handles command line arguments
//since this is needed before we include the config.php file, this is sort-of hard coded.
set_include_path('./pear' . PATH_SEPARATOR . get_include_path());
require_once 'pear/Console/Getargs.php';

$config = array();
$config['project'] =
    array('short' => 'p',
        'min'   => 0,
        'max'   => 1,
        'desc'  => 'The s2a project name. Must be a folder under the s2a folder.',
        'default' => 'active_agenda'
    );
$config['username'] =
    array('short'   => 'u',
        'min'     => 1,
        'max'     => 1,
        'desc'    => 'The username of the user to reset the password for. Required if person-id is not passed.'
    );
$config['person-id'] =
    array('short'   => 'i',
        'min'     => 1,
        'max'     => 1,
        'desc'    => 'The person ID of the user to reset the password for. Required if username is not passed.'
    );
$config['no-require-new'] =
    array('short'   => 'nr',
        'min'     => 0,
        'max'     => 0,
        'desc'    => 'When passed, the user is not required to enter a new password when logging in.'
    );


$args =& Console_Getargs::factory($config);
if (PEAR::isError($args)) {
    if ($args->getCode() === CONSOLE_GETARGS_ERROR_USER) {
        // User put illegal values on the command line.
        echo Console_Getargs::getHelp($config, NULL, $args->getMessage())."\n";
    } else if ($args->getCode() === CONSOLE_GETARGS_HELP) {
        // User needs help.
        echo Console_Getargs::getHelp($config)."\n";
    }
    exit;
}


//getting the passed parameters
$Project                = $args->getValue('project');
$Username               = $args->getValue('username');
$PersonID               = $args->getValue('person-id');
$tmp                    = $args->getValue('no-require-new');
$requireNewPassword     = empty($tmp);

if(empty($Project)){
    $Project = 'active_agenda';
}

if(empty($Username) && empty($PersonID)){
    echo Console_Getargs::getHelp($config)."\n";
    exit;
}

//assumes we're in the 's2a' folder 
$site_folder = realpath(dirname(__FILE__).'');
$site_folder .= '/'.$Project;

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

//this include contains utility functions
include_once INCLUDE_PATH . '/parse_util.php';
include_once INCLUDE_PATH . '/general_util.php';
include_once INCLUDE_PATH . '/usrFunctions.php'; //need encryptPassword()


/**
 * Sets custom error handler
 */
set_error_handler('handleError');



/**
 * Defines execution state as 'non-generating command line'.  Several classes and
 * functions behave differently because of this flag.
 */
DEFINE('EXEC_STATE', 2);

$mdb2 = GetMDB2();

//verify person record
if(empty($PersonID)){
    $SQL = "SELECT PersonID, _Deleted FROM `usr` WHERE Username = '$Username'";
    $result = $mdb2->queryRow($SQL);
    mdb2ErrorCheck($result);
    $PersonID = $result['PersonID'];
    $user_deleted = $result['_Deleted'];
} elseif(empty($Username)){
    $SQL = "SELECT Username, _Deleted FROM `usr` WHERE PersonID = '$PersonID'";
    $result = $mdb2->queryRow($SQL);
    mdb2ErrorCheck($result);
    $Username = $result['Username'];
    $user_deleted = $result['_Deleted'];
}

if(empty($result)){
    print "No existing user was found. No changes made. Exiting.\n";
    exit;
}

if($user_deleted){
    if(!prompt("The user '$Username' with ID $PersonID was previously marked as deleted. Continuing to reset the password will undelete this user account. Continue?")){
        print "No changes made. Exiting.\n";
        exit;
    }
}

if($requireNewPassword){
    print "The user will be prompted to enter a new password when logging in.\n";
} else {
    print "The user will NOT be prompted to enter a new password when logging in.\n";
}


$password = textPrompt("Please type the new password for user $Username (ID $PersonID) Leave blank to cancel.");
if('' == $password){
    print "No password found. No changes made. Exiting.\n";
    exit;
}

ob_start();
$dh = GetDataHandler('usr');
if(!$dh->saveRow(
    array(
        'Password' => encryptPassword($password),
        'RequireNewPassword' => $requireNewPassword
    ), $PersonID
)){
    die("$debug_prefix Something about saving the user record went wrong.\n");
}
ob_end_clean();

print "Updated password.\n";
?>