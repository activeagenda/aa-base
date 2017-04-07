<?php
/**
 *  Class for parsing report definition XML
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
 * @author         Mattias Thorslund <mthorslund@activeagenda.net>
 * @copyright      2003-2009 Active Agenda Inc.
 * @license        http://www.activeagenda.net/license  RPL 1.5
 * @version        SVN: $Revision: 1406 $
 * @last-modified  SVN: $Date: 2009-01-27 07:56:18 +0100 (Wt, 27 sty 2009) $
 */

include_once CLASSES_PATH . '/module_map.class.php';


/**
* XML Map class specific to parsing ReportDef files
*/
class ReportMap extends XMLMap
{
var $moduleID;
var $name;
var $rootElement = 'ReportGroup';

/**
* Constructor
*/
function ReportMap($fileName)
{
    $this->XMLFileName = $fileName;
    $this->parseXMLFile();
    list($moduleID,,$name)  = explode('_', basename($fileName));

    $this->name = substr($name, 0, strpos($name, '.'));
    $this->moduleID = $moduleID;

    foreach($this->c as $id => $content){
        if('Report' == $content->type){
            $content->attributes['moduleID'] = $moduleID;
            $this->c[$id] = $content;
        }
    }
}

function &generateReports()
{
    $reports = array();
    foreach($this->c as $id => $content){
        if('Report' == $content->type){
            $reports[] = $content->createObject($this->moduleID);
        }
    }
    return $reports;
}

} //end class ReportMap
?>