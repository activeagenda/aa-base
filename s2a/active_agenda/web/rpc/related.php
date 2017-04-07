<?php
/**
 * Displays relationships with other modules and records
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
require_once '../../config.php';
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//causes session timeouts to return a catchable response
//define('IS_RPC', true);

//main include file - performs all general application setup
require_once(INCLUDE_PATH . '/page_startup.php');

$SQL = "SELECT 
    `modd`.DependencyID AS ParentModuleID,
    `mod`.Name AS ParentModule,
    `modd`.SubModDependency,
    `modd`.ForeignDependency,
    `modd`.RemoteDependency
FROM
    `modd`
    INNER JOIN `mod`
    ON (`modd`.DependencyID = `mod`.ModuleID
    )
WHERE `modd`.ModuleID = '$ModuleID'
ORDER BY ParentModule";
$r = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
dbErrorCheck($r);
//$content .= debug_r($r);

$parents = array();
$foreigns = array();
$remotes = array();
if(count($r) > 0){
    foreach($r as $row){
        if(1 == $row['SubModDependency']){
            $parents[$row['ParentModuleID']] = $row['ParentModule'];
        }
        if(1 == $row['ForeignDependency']){
            $rev_foreigns[$row['ParentModuleID']] = $row['ParentModule'];
        }
        if(1 == $row['RemoteDependency']){
            $rev_remotes[$row['ParentModuleID']] = $row['ParentModule'];
        }
    }
}


$SQL = "SELECT 
    `modd`.ModuleID AS SubModuleID,
    `mod`.Name AS SubModule,
    `modd`.SubModDependency,
    `modd`.ForeignDependency,
    `modd`.RemoteDependency
FROM
    `modd`
    INNER JOIN `mod`
    ON (`modd`.ModuleID = `mod`.ModuleID
    )
WHERE `modd`.DependencyID = '$ModuleID'
ORDER BY SubModule";
$r = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
dbErrorCheck($r);


$subs = array();
$foreigns = array();
$remotes = array();
if(count($r) > 0){
    foreach($r as $row){
        if(1 == $row['SubModDependency']){
            $subs[$row['SubModuleID']] = $row['SubModule'];
        }
        if(1 == $row['ForeignDependency']){
            $foreigns[$row['SubModuleID']] = $row['SubModule'];
        }
        if(1 == $row['RemoteDependency']){
            $remotes[$row['SubModuleID']] = $row['SubModule'];
        }
    }
}



$content .= RenderSection(
    $parents,
    gettext("Parent Modules"),
    gettext("Modules where this module is a submodule")
);

$content .= RenderSection(
    $subs,
    gettext("Submodules"),
    gettext("Submodules of this module")
);

$content .= RenderSection(
    $foreigns,
    gettext("Foreign Modules"),
    gettext("This module displays data from the following modules")
);

$content .= RenderSection(
    $remotes,
    gettext("Remote Modules"),
    gettext("This module displays data from and saves data into the following modules")
);

$content .= RenderSection(
    $rev_foreigns,
    gettext("Reverse Foreign Modules"),
    gettext("Modules that display data from this module")
);

$content .= RenderSection(
    $rev_remotes,
    gettext("Reverse Remote Modules"),
    gettext("Modules that display data from this module and save data into this module")
);



echo $content;

function RenderSection($items, $title, $explanation)
{
    static $section_id = 0;
    $section_id++;

    $content = '';
    $item_count = count($items);
    if($item_count > 0){
        $content .= "<h3>$title</h3>\n";
        $content .= "<i>($explanation)</i>\n";
        if($item_count > 10){
            $content .= "<br />\n";
            $content .= sprintf(gettext("There are %s items in this list"), $item_count);
            $content .= ' [<a href="javascript:toggleRelSection(\'relSection'.$section_id.'\', this, \''.gettext("Show Items").'\', \''.gettext("Hide Items").'\')">'.gettext("Show Items").'</a>]';
            $content .= "<br />\n";
            $content .= "<ul id=\"relSection$section_id\" style=\"display:none\">\n";
        } else {
            $content .= "<ul id=\"relSection$section_id\">\n";
        }
        foreach($items as $item_id => $item_name){
            $content .= '<li>';
            $content .= '<a href="list.php?mdl='.$item_id.'">'.gettext($item_name).'</a>';
            $content .= '</li>';
        }
        $content .= "</ul>\n";
        $content .= "<br />\n";
    }
    return $content;
}

?>