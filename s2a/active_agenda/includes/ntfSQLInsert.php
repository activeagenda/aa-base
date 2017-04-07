<?php
/**
 * Custom snippet for Notifications
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

//ntfSQLInsert.php

//print "executing ntfSQLInsert.php<br />\n";
//updates newly inserted row with RelatedModuleID and RelatedRecordID

$SQL = "UPDATE `ntf` SET RelatedModuleID = '$RelatedModuleID', RelatedRecordID = '$RelatedRecordID', StatusID = 1 WHERE NotificationID=$recordID";

$r = $dbh->query($SQL);
dbErrorCheck($r);


//detect whether an OrganizationID field applies, and its value for the current record.

//select notification list
$SQL = "SELECT RecipientID FROM modnr WHERE RelatedModuleID = '$RelatedModuleID'";

$moduleInfo = GetModuleInfo($RelatedModuleID);
$ownerOrgField = $moduleInfo->getProperty('ownerField');
$pkField = $moduleInfo->getPKField();
if(!empty($ownerOrgField)){
    //add filter by Organization ID
    $ownerMF = GetModuleField($RelatedModuleID, $ownerOrgField);
    switch(strtolower(get_class($ownerMF))){
    case 'tablefield':
        $SQL .= " AND OrganizationID = (SELECT $ownerOrgField FROM `$RelatedModuleID` WHERE $pkField = '$RelatedRecordID')";
        break;
    case 'dynamicforeignfield':
        $SQL .= " AND OrganizationID = (SELECT OrganizationID FROM rdc WHERE ModuleID = '$RelatedModuleID' AND  RecordID = '$RelatedRecordID')";
        break;
    case 'foreignfield': //would need to generate a join
    case 'remotefield':  //would need to generate a join
    default:
        print debug_r($ownerMF);
        die("ntfSQLInsert dos not handle Owner Organization field of type ". get_class($ownerMF));
    }
}

$InitialRecipents = $dbh->getCol($SQL);
dbErrorCheck($InitialRecipents);

if(count($InitialRecipents) > 0){

    $SQL = "INSERT INTO ntfr (NotificationID, RecipientID, StatusID, _ModDate, _ModBy)
VALUES ($recordID, ?, 1, NOW(), {$User->PersonID})";
    //print debug_r($SQL);
    $prh = $dbh->prepare($SQL);
    foreach($InitialRecipents as $InitialRecipientID){
        $dbh->execute($prh, array($InitialRecipientID));
    }

}

?>