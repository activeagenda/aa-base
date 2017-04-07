<?php
/**
 * Custom snippet for initializing the notifications screens
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


//pre-populates some field values
if(empty($data['RelatedModule'])){
    $RelatedModuleID = substr($_GET['relm'], 0, 5);
    $RelatedRecordID = intval($_GET['relr']);

    $SQL = 
"SELECT `mod`.Name as RelatedModule, rdc.Value as RelatedRecord
FROM `mod`
    LEFT OUTER JOIN `rdc`
    ON (`mod`.ModuleID = `rdc`.ModuleID)
WHERE
    `mod`.ModuleID = '$RelatedModuleID'
    AND `rdc`.RecordID = $RelatedRecordID";

    $r = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
    dbErrorCheck($r);

    $data['RelatedModule'] = $r[0]['RelatedModule'];
    $data['RelatedRecord'] = $r[0]['RelatedRecord'];

    //adding to $_POST so that they 
    $_POST['RelatedModule'] = $r[0]['RelatedModule'];
    $_POST['RelatedRecord'] = $r[0]['RelatedRecord'];
}

?>