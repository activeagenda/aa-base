<?php
/**
 *  Allows the user to change their preferences.
 *
 *  PHP version 5
 *
 *
 *  LICENSE NOTE:
 *
 *  Copyright  2003-2009 Active Agenda Inc., All Rights Reserved.
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
 *
 * @author         Mattias Thorslund <mthorslund@activeagenda.net>
 * @copyright      2003-2009 Active Agenda Inc.
 * @license        http://www.activeagenda.net/license  RPL 1.5
 * @version        SVN: $Revision: 1406 $
 * @last-modified  SVN: $Date: 2009-01-27 07:56:18 +0100 (Wt, 27 sty 2009) $
 */

//general settings
require_once '../config.php';
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//main include file - performs all general application setup
require_once INCLUDE_PATH . '/page_startup.php';

include_once CLASSES_PATH . '/components.php';
include_once $theme .'/component_html.php';


$user_id = $User->PersonID;
$title = gettext("Change Your Preferences");

$include_file_path = GENERATED_PATH . '/usr/usr_ChangePreferences.gen';
if(!file_exists($include_file_path)){
    trigger_error(gettext("Could not find the file:").$include_file_path, E_USER_ERROR);
}
include_once($include_file_path); //returns $fields, $get_sql

$get_sql = str_replace('/**RecordID**/', $User->PersonID,$get_sql);
$get_sql = TranslateLocalDateSQLFormats($get_sql);
$data = $dbh->getRow($get_sql, DB_FETCHMODE_ASSOC);
dbErrorCheck($data);

$messages = array();
if(!empty($_POST['Save'])){
    foreach($data as $fieldName => $fieldValue){
        if(!empty($_POST[$fieldName])){
            $data[$fieldName] = dbQuote($_POST[$fieldName], 'int');
        }
    }
    $SQL = "UPDATE `usr` SET DefaultOrganizationID = '{$data['DefaultOrganizationID']}', LangID = '{$data['LangID']}' WHERE PersonID = {$User->PersonID}";

    $r = $dbh->query($SQL);
    dbErrorCheck($r);

    $messages = array();
    $messages[] = array('m', gettext("Your settings were saved successfully. They will become effective next time you log in."));
}




$content = '';
foreach($fields as $key => $field){
    if (!$field->isSubField()){
        $content .= $field->render($data, $phrases);
    }
}

$intro = '';
$content = $intro . renderForm($content, $_SERVER['REQUEST_URI'], '', '', '', '', 'usr', true);

include_once $theme . '/no-tabs.template.php';
?>