<?php

/**
 * Locates info on the related meeting assignment.
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

if(empty($_POST)){
    if(0 == $recordID){
        $assignedMeetingID = intval($_GET['MasterAssignID']);
        if(0 != $assignedMeetingID){
            include_once(GENERATED_PATH.'/mtg/mtg_CustomSQL.gen'); //returns $custom_mtgSQL
            $custom_mtgSQL =  str_replace('[*MasterAssignID*]', $assignedMeetingID, $custom_mtgSQL);
            $custom_mtgSQL = TranslateLocalDateSQLFormats($custom_mtgSQL);
            global $dbh;
            $mtgma_data = $dbh->getRow($custom_mtgSQL, DB_FETCHMODE_ASSOC);
            dbErrorCheck($mtgma_data);

            if(count($mtgma_data) > 0){
                $data = array_merge($data, $mtgma_data);
            }
        }
    }
}
?>