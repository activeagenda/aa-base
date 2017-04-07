<?php
/**
 * Custom snippet and function to handle the sending of notifications
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

if(!$ntf_sent){
    if(!empty($_POST['Send']) && ntf_checkForRecipients()){
        //generate message
        $textContent = '';
        $HTMLContent = '';

        include_once APP_INCLUDE_PATH . '/ntfFormatNotification.php';
        list($textContent, $HTMLContent) = ntf_formatNotification($data['RelatedModuleID'], $data['RelatedRecordID']);
//print debug_r($textContent);
        $textContent = mysql_real_escape_string($textContent); //DB_common::escapeSimple($textContent);
        $HTMLContent = mysql_real_escape_string($HTMLContent); //DB_common::escapeSimple($HTMLContent);

        $SQL = "UPDATE ntf SET StatusID = 2, TextContent = '$textContent', HTMLContent='$HTMLContent', SentDate = NOW(), SenderID = '{$User->PersonID}', _ModBy = '{$User->PersonID}', _ModDate = NOW() WHERE NotificationID = '$recordID'";

        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        //update recipient messages too
        $SQL = "UPDATE ntfr SET StatusID = 2 WHERE _Deleted = 0 AND NotificationID = '$recordID'";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        //get the code
        $SQL = "SELECT cod.Description AS Status 
        FROM cod 
        WHERE 
            cod.CodeTypeID = '32'
            AND cod.CodeID = 2";
//print debug_r($data);
        $r = $dbh->getOne($SQL);
        dbErrorCheck($r);
        $data['StatusID'] = 2;
        $data['Status'] = $r;
        $data['Sender'] = $User->DisplayName;
//print debug_r($data);
        $ntf_sent = true;
    }
}

function ntf_checkForRecipients(){
    global $dbh;
    global $recordID;

    $SQL = "SELECT count(*) FROM ntfr WHERE NotificationID = '$recordID'";
    $r = $dbh->getOne($SQL);
    dbErrorCheck($r);
    if(intval($r) > 0){
        return true;
    } else {
        return false;
    }
}


?>