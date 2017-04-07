<?php
/**
 *  Utility class for caching of key module info
 *
 *  This file contains the definition for the ModuleInfo class, which is an 
 *  attempt at reducing the number of repetitive look-ups of module properties.
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
 * @version        SVN: $Revision: 1583 $
 * @last-modified  SVN: $Date: 2009-04-28 07:11:50 +0200 (Wt, 28 kwi 2009) $
 */

class ModuleInfo
{
var $moduleID;
var $fileLoaded = false;
var $tableLoaded = false;
var $dbProperties = array('globalDiscussionAddress', 'localDiscussionAddress');
var $properties = array();
var $isDataView = false;

function ModuleInfo($moduleID)
{
    if(empty($moduleID)){
        trigger_error("moduleID cannot be empty", E_USER_ERROR);
    }
    $this->moduleID = $moduleID;
    if(false !== strpos($this->moduleID, '_')){
        $this->isDataView = true;
    }
}

function _loadFile()
{
    if($this->isDataView){
        return true;
    }
    $genFileName = GENERATED_PATH . "/{$this->moduleID}/{$this->moduleID}_ModuleInfo.gen";
    if(!file_exists($genFileName)){
        if(defined('EXEC_STATE') && EXEC_STATE == 4){
            $this->makeGeneratedFile();
        } else {
            trigger_error("Could not find file $genFileName", E_USER_ERROR);
        }
    }

    include($genFileName); //imports the variables used below

    $this->properties['moduleName']             = $moduleName;
    $this->properties['parentModuleID']         = $parentModuleID;
    $this->properties['ownerField']             = $ownerField;
    $this->properties['recordDescriptionField'] = $recordDescriptionField;
    $this->properties['recordLabelField']       = $recordLabelField;
    $this->properties['recordIDField']          = $recordIDField;
    $this->properties['primaryKeys']            = $primaryKeys;
    $this->properties['ownerFieldFilter']       = $ownerFieldFilter;
    $this->properties['firstEditScreen']        = $firstEditScreen;
    $this->properties['autoIncrement']          = $autoIncrement;
    $this->properties['isMasterData']           = $isMasterData;
    $this->properties['uniquenessIndexes']      = $uniquenessIndexes;

    if(!isset($isTypeModule)){
        $isTypeModule = false;
    }
    $this->properties['isTypeModule']           = $isTypeModule;

    $this->fileLoaded = true;
}

function _loadTable()
{
    global $dbh;
    $SQL = "SELECT GlobalDiscussionAddress, LocalDiscussionAddress FROM `mod` WHERE ModuleID = '{$this->moduleID}'\n";

    $row = $dbh->getRow($SQL, DB_FETCHMODE_ASSOC);
    dbErrorCheck($row);

    $this->properties['globalDiscussionAddress'] = $row['GlobalDiscussionAddress'];
    $this->properties['localDiscussionAddress']  = $row['LocalDiscussionAddress'];

    $this->tableLoaded = true;
}

function getProperty($propName)
{
    if(!empty($this->properties[$propName])){
        return $this->properties[$propName];
    } else {
        if(in_array($propName, $this->dbProperties)){
            if(!$this->tableLoaded){
                $this->_loadTable();
            }
        } else {
            if(!$this->fileLoaded){
                $this->_loadFile();
            }
        }
        return $this->properties[$propName];
    }
}

function getPKField()
{
    //special solution to a difficult chicken-or-egg problem
    if(defined('EXEC_STATE') && EXEC_STATE == 4){
        if(!$this->fileLoaded){
            $module = GetModule($this->moduleID);
            return end($module->PKFields);
        }
    }
    return $this->getProperty('recordIDField');
}

function getPermissionModuleID()
{
    $parentModuleID = $this->getProperty('parentModuleID');
    if(empty($parentModuleID)){
        return $this->moduleID;
    } else {
        return $parentModuleID;
    }
}

function makeGeneratedFile(){
    if(!defined('EXEC_STATE') || EXEC_STATE != 4){
        trigger_error('Called a parse-time method during run time.', E_USER_ERROR);
    }
    $module = GetModule($this->moduleID);
    $codeArray = array();

    $codeArray['/**moduleName**/'] = $module->Name;
    $codeArray['/**parentModuleID**/'] = $module->permissionParentModuleID;
    $codeArray['/**ownerField**/'] = $module->OwnerField;
    $codeArray['/**recordDescriptionField**/'] = $module->recordDescriptionField;
    $codeArray['/**recordLabelField**/'] = $module->recordLabelField;

    $codeArray['/**ownerFieldFilter**/'] = $module->getOwnerFieldFilter();
    $codeArray['/**primaryKeys**/'] = escapeSerialize($module->PKFields);
    $recordIDField = end($module->PKFields);
    $codeArray['/**recordIDField**/'] = $recordIDField;
    $codeArray['/**firstEditScreen**/'] = $module->getFirstEditScreen();
    $codeArray['/**autoIncrement**/'] = $module->usesAutoIncrement();
    $codeArray['/**isMasterData**/'] = $module->isMasterData;
    $codeArray['/**isTypeModule**/'] = $module->isTypeModule;
    $codeArray['/**uniquenessIndexes**/'] = escapeSerialize($module->uniquenessIndexes);

    $modelFileName = "ModuleInfoModel.php";
    $CreateFileName = "{$this->moduleID}_ModuleInfo.gen";

    SaveGeneratedFile($modelFileName, $CreateFileName, $codeArray, $this->moduleID);
}

}
?>