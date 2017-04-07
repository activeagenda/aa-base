<?php
/**
 * Functions that enable the cistom features of the pple (Employees) module
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
 * @version        SVN: $Revision: 1406 $
 * @last-modified  SVN: $Date: 2009-01-27 07:56:18 +0100 (Wt, 27 sty 2009) $
 */


include_once(GENERATED_PATH . '/pple/pple_CustomSQLs.gen');

define('EMPLOYEE_STATUS_NO_RECORD', 0);
define('EMPLOYEE_STATUS_DELETED', -1);
define('EMPLOYEE_STATUS_CURRENT', 1);

/**
 * checks the status of the Employee record
 *
 * returns:
 *  EMPLOYEE_STATUS_DELETED (-1) if employee record is deleted (i.e. it exists but _Deleted is 1
 *  EMPLOYEE_STATUS_CURRENT (1) if employee record exists and is not deleted
 *  EMPLOYEE_STATUS_NO_RECORD (0) if the employee record doesn't exist at all
 */
function verifyEmployeeRecord(){
    global $dbh;
    global $recordID;
    
    $SQL = "SELECT IF(_Deleted = 0, 1, -1) FROM pple WHERE PersonID = $recordID";
    $r = $dbh->getOne($SQL);
    dbErrorCheck($r);
    
    $returnValue = intval($r);
    if(0 === $returnValue){
        global $existing;
        $existing = false;
    }
    
    //print "verifyEmployeeRecord returns '$returnValue'<br />\n";
    return intval($returnValue);
}



/**
 * adds ppl record to $data
 */
function loadPersonRecord($screen_name){
    global $dbh;
    global $data;
    global $custom_pplSQLs;
    global $recordID;

    $SQL = str_replace('/**RecordID**/', $recordID, $custom_pplSQLs[$screen_name]['get']);
    $SQL = TranslateLocalDateSQLFormats($SQL);
    $r = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
    dbErrorCheck($r);
    $data['PersonID'] = $recordID;

    //populate $data ($_POST takes precenence
    if(count($r) > 0){
        foreach($r[0] as $fieldName=>$dbValue){
            //(checking for gridnum avoids interference with any posted edit grid)
            if (empty($_POST['gridnum']) && isset($_POST[$fieldName])){
                $data[$fieldName] = $_POST[$fieldName];
            } else {
                $data[$fieldName] = $dbValue;
            }
        }
    }

    //print debug_r($SQL);
    //print debug_r($data, 'loadPersonRecord');

}


?>