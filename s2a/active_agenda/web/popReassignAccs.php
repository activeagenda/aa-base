<?php
/**
 * Custom popup screen to enable reassigning accountabilities in bulk
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
 * author         Mattias Thorslund <mthorslund@activeagenda.net>
 * copyright      2003-2009 Active Agenda Inc.
 * license        http://www.activeagenda.net/license
 **/

//general settings
require_once '../config.php';
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//this include contains the search class
include_once(CLASSES_PATH . '/search.class.php');

include_once(CLASSES_PATH . '/components.php');

//this causes session timeouts to display a message instead of redirecting to the login screen
DEFINE('IS_POPUP', true);

//main include file - performs all general application setup
require_once(INCLUDE_PATH . '/page_startup.php');
include_once $theme .'/component_html.php';


$GlobalModuleID = 'acc';


$search = $_SESSION['Search_acc'];

if(is_object($search) && $search->hasConditions()){

    //if the form was posted
    if(isset($_POST['Save'])){

        //check that there's a person selected
        $personSelected = intval($_POST['PersonAccountableID']);

        if(!empty($personSelected)){
            //print debug_r($search);
            $SQL = "UPDATE acc\n";
            //include FROMS
            foreach($search->froms as $alias => $def){
                $SQL .= "$def\n";
            }

            $SQL .= "SET acc.PersonAccountableID = $personSelected,\n";
            $SQL .= " acc._ModDate = NOW(),\n";
            $SQL .= " acc._ModBy = {$User->PersonID}\n";
            $SQL .= "WHERE\n";
            $SQL .= "acc._Deleted = 0";
            foreach($search->wheres as $fields){
                foreach($fields as $fieldname => $def){
                    $SQL .= "\nAND $def";
                }
            }

            //execute SQL statement
            $r = $dbh->query($SQL);
            dbErrorCheck($r);

            $messages[] = array('m', gettext("Successfully changed accountabilities"));

            print "<script language=\"JavaScript\">\n";

            //refresh List screen with JavaScript
            print "opener.location.reload();\n";
            print "opener.focus();\n";
            //close this window
            print "self.close();\n";
            print "</script>\n";
        } else {
            $messages[] = array('e', gettext("No person selected."));
        }
    }

ob_start(); //eliminate all the debug prints that this generates
    $personField =& MakeObject(
        'acc',
        'PersonAccountableID',
        'PersonComboField',
        array(
            'name' => 'PersonAccountableID',
            'validate' => 'notEmpty',
            'formName' => 'mainForm',
            'findMode' => 'text'
        )
    );
    //$debugContent = ob_get_contents();
ob_end_clean();
/*
    
        $personField = new PersonComboField(
            'PersonAccountableID', //$pName, 
            '', //$pListCondition, 
            '', //$pOrgListCondition, 
            '', //$pSQL
            '',//$pConditionField, 
            '',//$pConditionValue, 
            'notEmpty', //$pValidate
            '', //$pDefaultValue
            'acc',//$pModuleID, 
            NULL, //$pGrid
            'mainForm', //$formName
            'text' //$findMode
        );

        $debugContent = ob_get_contents();
    ob_end_clean();
*/
    $content = '';

    $data = array();
    $phrases = array('PersonAccountableID' => gettext("New Person Accountable|Select the person that the currently filtered accountabilities should be reassigned to."));

    $fieldHTML = $personField->render($data, $phrases);

    $targetlink = "popReassignAccs.php";
    $cancellink = "javascript:opener.focus();self.close();";

    $content .= renderForm($fieldHTML, $targetlink, $deletelink, $cancellink, $nextScreen, $form_enctype, $GlobalModuleID);

    $content .= "<div class=\"searchFilter\"><b>".gettext("Filter Conditions").":</b><br />\n";
    $content .= $search->getPhrases();
    $content .= "<br />\n"; 
    $content .= "</div><br />\n";

} else {
    $content = gettext("To reassign accountabilities, you must first perform a search. Please close this screen and search for records that should be reassigned").".<br>\n";
}

$title = gettext("Reassign Accountabilities");
$subtitle = gettext("Select a person below:");
//$user_info;
$screenPhrase = ShortPhrase($screenPhrase);
$moduleID = $ModuleID;
//$recordID;
//$messages; //any error messages, acknowledgements etc.
//$content;

include_once $theme . '/popup.template.php';
?>