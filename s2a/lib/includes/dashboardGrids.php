<?php
/**
 * Supports displaying the dashboard grids in the dashboard screen.
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
 * @version        SVN: $Revision: 1662 $
 * @last-modified  SVN: $Date: 2009-05-27 19:26:23 +0200 (Śr, 27 maj 2009) $
 */


include_once(CLASSES_PATH . '/grids.php');


class DashboardGrid extends ViewGrid {

function prepareCountSQL()
{
    global $User;
    $countSQL = parent::prepareCountSQL();
    $countSQL = str_replace('/**UserID**/', $User->PersonID, $countSQL);

    return $countSQL;
}

function prepareListSQL()
{
    global $User;
    $listSQL = parent::prepareListSQL();
    $listSQL = str_replace('/**UserID**/', $User->PersonID, $listSQL);
    return $listSQL;
}

} //end class DashboadGrid



class ActionsDashboardGrid extends DashboardGrid {
function ActionsDashboardGrid (){
    //all properties are pre-set here, so no XML needed
    $this->moduleID = 'act';
    $this->phrase = 'Action List';
    $this->formatOptions['suppressTitle'] = true;
    $this->formatOptions['suppressPaging'] = true;
    $this->orderByFields = array('DueDate'=>0);

    $module = GetModule($this->moduleID);

    //easier to define the fields in "element" format
    $field_elements = array(
        0 => new Element(
            'ActionID', 
            'InvisibleGridField',
            array(
                'name' => 'ActionID'
            )
        ),
        1 => new Element(
            'Title', 
            'ViewGridField', 
            array(
                'name' => 'Title',
                'phrase' => 'Title|A concise title that describes the action'
            )
        ),
        2 => new Element(
            'DueDate',
            'ViewGridField',
            array(
                'name' => 'DueDate',
                'formatField' => 'DueDateFormat',
                'phrase' => 'Due Date|Date the action is projected to be completed'
            )
        ),
        3 => new Element(
            'ActionStatus', 
            'ViewGridField', 
            array(
                'name' => 'ActionStatus',
                'phrase' => 'Status|Status of the assigned action'
            )
        )

    );

    foreach($field_elements as $field_element){
        $field_object = $field_element->createObject($this->moduleID, $field_element->type);

        if(empty($sub_element->attributes['phrase'])){
            $field_object->phrase = $module->ModuleFields[$field_object->name]->phrase;
        }
        $this->AddField($field_object);
    }

    $this->setUpFieldTypes($module->ModuleFields);

    $this->conditions['PersonAccountableID'] = '/**UserID**/';
    $this->conditions['ActionStatusID'] = '1|2';

    $this->listSQL = $module->generateListSQL($this);
    $this->countSQL = $module->generateListCountSQL($this);

} //end constructor ActionsDashboardGrid

/*function appendSQLConditions($conditions){
    parent::appendSQLConditions($conditions);
    $this->ParentRowSQL .= "\n AND act.ActionStatusID IN (1,2)";
}*/

function render($page, $qsArgs)
{
    $content = parent::render($page, $qsArgs);
    $content .= gettext("\"Complete\" or \"Deferred\" actions are not shown.");

    return $content;
}

} //end class ActionsDashboardGrid


class AccountabilityDashboardGrid extends DashboardGrid {
function AccountabilityDashboardGrid (){
    //all properties are pre-set here, so no XML needed
    $this->moduleID = 'acc';
    $this->phrase = 'Accountabilities';
    $this->formatOptions['suppressTitle'] = true;
    $this->formatOptions['suppressRecordIcons'] = true;
    $this->formatOptions['suppressPaging'] = true;

    $module = GetModule($this->moduleID);

    //easier to define the fields in "element" format
    $field_elements = array(
        0 => new Element(
            'AccountabilityDescriptorID',
            'InvisibleGridField',
            array(
                'name' => 'AccountabilityDescriptorID',
                'phrase' => 'Specific Accountability|A word or phrase describing the specific accountability of the person. The specific accountability is automatically generated by the system based on the assignment which occurred within a related module'
            )
        ),
        1 => new Element(
            'AccountabilityDescriptor',
            'ViewGridField',
            array(
                'name' => 'AccountabilityDescriptor',
                'link' => 'DashboardGridLink',
                'phrase' => 'Specific Accountability|A word or phrase describing the specific accountability of the person. The specific accountability is automatically generated by the system based on the assignment which occurred within a related module'
            )
        ),
        2 => new Element(
            'Total', 
            'ViewGridField',
            array(
                'name' => 'Total',
                'phrase' => 'Total'
            )
        ),
        3 => new Element(
            'New',
            'ViewGridField',
            array(
                'name' => 'New',
                'phrase' => 'New'
            )
        )

    );

    foreach($field_elements as $field_element){
        $field_object = $field_element->createObject($this->moduleID, $field_element->type);
        if(empty($sub_element->attributes['phrase'])){
            $field_object->phrase = $module->ModuleFields[$field_object->name]->phrase;
        }
        $this->AddField($field_object);
    }


    //$this->listSQL = $module->generateListSQL($this);
    $this->listSQL = "SELECT
    acc.AccountabilityDescriptorID,
    cod1.Description AS AccountabilityDescriptor,
    CONCAT(
        'internal:list.php?mdl=acc&filter=1&PersonAccountableID=',
        `acc`.PersonAccountableID,
        '&AccountabilityDescriptorID=',
        IFNULL(`acc`.AccountabilityDescriptorID,'')
    ) AS DashboardGridLink,
    COUNT(AccountabilityID) AS Total,
    SUM(CASE WHEN acc._ModDate > '/**UserPreviousVisit**/' THEN 1 ELSE 0 END) AS `New`
FROM acc
    LEFT OUTER JOIN `ppl` AS ppl1
    ON (`acc`.PersonAccountableID = `ppl1`.PersonID )
    LEFT OUTER JOIN cod AS cod1 ON acc.AccountabilityDescriptorID = cod1.CodeID AND cod1.CodeTypeID = 260
WHERE
    acc._Deleted = 0
    AND acc.PersonAccountableID = '/**UserID**/'
GROUP BY AccountabilityDescriptor
HAVING `New` > 0
ORDER BY `New` DESC, AccountabilityDescriptor";

    $this->countSQL = "SELECT
    count(*)
FROM acc
    LEFT OUTER JOIN `ppl` AS ppl1
    ON (`acc`.PersonAccountableID = `ppl1`.PersonID )
WHERE
    acc._Deleted = 0
    AND acc._ModDate > '/**UserPreviousVisit**/'
    AND acc.PersonAccountableID = '/**UserID**/'";

    //$this->appendSQLConditions();

} //end constructor AccountabilityDashboardGrid 



function prepareListSQL(){
    global $User;
    $previousVisit = $User->previousVisit;
    if(empty($previousVisit)){
        $previousVisit = '1/1/2000';
    }
    //$listSQL = parent::prepareListSQL();
    $listSQL = $this->listSQL;
    $listSQL = str_replace('/**UserPreviousVisit**/', $previousVisit, $listSQL);
    $listSQL = str_replace('/**UserID**/', $User->PersonID, $listSQL);
//print debug_r($listSQL);
    return $listSQL;
}

function prepareCountSQL()
{
    global $User;
    $previousVisit = $User->previousVisit;
    if(empty($previousVisit)){
        $previousVisit = '1/1/2000';
    }
    $countSQL = $this->countSQL;

    if(!empty($this->ParentRowSQL)){
        if(FALSE === strpos($countSQL, 'WHERE')){
            $countSQL .= ' WHERE ';
        } else {
            $countSQL .= ' AND ';
        }
        $countSQL .= $this->ParentRowSQL;
    }

    //no need to filter out records where user has no permission (users must be able to see their own)
    //$countSQL .= $User->getListFilterSQL($this->moduleID, true);
    $countSQL = str_replace('/**UserID**/', $User->PersonID, $countSQL);
    $countSQL = str_replace('/**UserPreviousVisit**/', $previousVisit, $countSQL);
    return $countSQL;
}

function render($page, $qsArgs)
{
    global $User;
    $allMyAccsLink = 'list.php?mdl=acc&filter=1&PersonAccountableID='.$User->PersonID;
    $content = '<p><b><a href="'.$allMyAccsLink.'">'.gettext("View All Your Acountabilities").'</a></b></p>';
    $count = $this->getRecordCount();
    if(intval($count) == 0){
        $content .= gettext("No new accountabilities have been assigned to you since your last login.");
    } else {
        $content .= parent::render($page, $qsArgs);
        $content .= gettext("\"New\" accountabilities were assigned today,<br /> or since your last login a previous day.");
    }
    return $content;
}

} //end class AccountabilityDashboardGrid 


class ShortcutDashboardGrid extends DashboardGrid {

function ShortcutDashboardGrid()
{
//all properties are pre-set here, so no XML needed
    $this->moduleID = 'usrds';
    $this->phrase = 'Shortcuts';
    $this->orderByFields = array('Type'=>0, 'Title'=>0);
    $this->formatOptions['suppressTitle'] = true;
    $this->formatOptions['suppressRecordIcons'] = true;
    $this->formatOptions['suppressPaging'] = true;

    $module = GetModule($this->moduleID);

    //easier to define the fields in "element" format
    $field_elements = array(
        0 => new Element(
            'RecordID',
            'InvisibleGridField',
            array(
                'name' => 'RecordID'
            )
        ),
        1 => new Element(
            'Type',
            'ViewGridField',
            array(
                'name' => 'Type',
                'phrase' => 'Type|The type of link'
            )
        ),
        2 => new Element(
            'Title',
            'ViewGridField',
            array(
                'name' => 'Title',
                'link' => 'InternalLink',
                'phrase' => 'Title|The title'
            )
        )
    );

    foreach($field_elements as $field_element){
        $field_object = $field_element->createObject($this->moduleID, $field_element->type);

        if(empty($sub_element->attributes['phrase'])){
            $field_object->phrase = $module->ModuleFields[$field_object->name]->phrase;
        }
        $this->AddField($field_object);
    }

    $this->conditions['PersonID'] = '/**UserID**/';
    $this->listSQL = $module->generateListSQL($this);
    $this->countSQL = $module->generateListCountSQL($this);
}




} //end class ShortcutDashboardGrid

?>