<?php
/**
 * Custom function to format a notification into text and HTML
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

function ntf_formatNotification($relModuleID, $relRecordID){
    global $User;
    global $dbh;
    global $recordID;

    $textContent = '';
    $HTMLContent = '';


    //retrieve Message, etc from Notification
    $SQL = "SELECT Message, XMLAttached FROM ntf WHERE NotificationID = '$recordID'";
    $row = $dbh->getRow($SQL, DB_FETCHMODE_ASSOC);
    dbErrorCheck($row);

    $message = $row['Message'];
    $attachXML = $row['XMLAttached'];


    //these could become template snips or something
    $HTMLTemplate = '<html>
<head><title>%s</title></head>
<style>%s</style>
<body><div id="aa_body"><div id="aa_content"><br />%s</div></div></body>
</html>';
    $HTMLTableTemplate = '   <table class="aa_fields">
        %s
    </table>'; //for section fields
    $HTMLGridTemplate = '<div class="aa_grid">
    <div class="aa_gridtitle">%1$s</div>
    <table class="aa_grid" cellpadding="2" cellspacing="1">%2$s</table>
</div>';

    $filename = GENERATED_PATH . "/{$relModuleID}/{$relModuleID}_ViewSer.gen";

    //check for cached page for this module
    if (!file_exists($filename)){
        die(gettext("Could not find file:") . "'$filename'.");
    }

    //the included file sets $phrases and $sections
    include $filename;
//print debug_r($sections);
    /*adding global modules as a separate section*/
    $globalModules = array('act','att','cos','lnk','nts');
    $globalSection = array();
    $globalSection['phrase'] = gettext("Global");
    $globalGrids = array();

    foreach($globalModules as $gmID){
        include_once GENERATED_PATH . "/{$gmID}/{$gmID}_GlobalViewGrid.gen";

        if(isset($grid)){
            $grid->listSQL = str_replace('/**DynamicModuleID**/', $relModuleID, $grid->listSQL);
            $grid->countSQL = str_replace('/**DynamicModuleID**/', $relModuleID, $grid->countSQL);
            $globalGrids[] =& $grid;
            unset($grid);
        }
    }
    $globalSection['grids'] =& $globalGrids;
    $sections[] =& $globalSection;

    /*retrive data and format Text and HTML*/
    $data = array();

    foreach($sections as $sectionID => $section){
        if(!empty($section['phrase'])){
            $phrase = $section['phrase'].':';
            $textContent .= $phrase."\n";
            $textContent .= str_pad('', strlen($phrase), '=')."\n";

            $HTMLContent .= '<h1 class="aa">'.$phrase.'</h1>';
        }
        //$textContent .= "\n";
        if(count($section['fields']) > 0){
            $textArray = array();
            $HTMLFieldContent = '';

            $SQL = $section['sql'];
            $SQL = str_replace('/**RecordID**/', $relRecordID, $SQL);
            $SQL = TranslateLocalDateSQLFormats($SQL);

            $row = $dbh->getRow($SQL, DB_FETCHMODE_ASSOC);
            dbErrorCheck($row);
            $data = array_merge($data, $row);
//print debug_r($data);
            foreach($section['fields'] as $screenFieldName => $screenField){
                $textArray[] = array(
                    shortPhrase($phrases[$sectionID][$screenFieldName]),
                    wordwrap(strip_tags($screenField->viewRender($data)), 60)
                );

                $HTMLFieldContent .= $screenField->render($data, $phrases[$sectionID]);
            }

            $textTable =& new TextTable($textArray);
            $textContent .= $textTable->render() . "\n";

            $HTMLContent .= sprintf($HTMLTableTemplate, $HTMLFieldContent);
        }
        if(count($section['grids']) > 0){
            foreach($section['grids'] as $grid){
                $textContent .= $grid->renderText($relRecordID) . "\n";
                $HTMLContent .= $grid->renderEmail($relRecordID);
                if($attachXML){
                    //XML content should be created with the XML report functionality.
                }
            }
        }
    }


    //put together HTML and Text content
    $HTMLMessage = nl2br($message);
    $theme = GetThemeLocation();
    $styles = file_get_contents ($theme . '/email.css');
    $HTMLContent = $HTMLMessage . $HTMLContent; //prepend message
    $HTMLContent =  sprintf($HTMLTemplate, 'title', $styles, $HTMLContent);
    $textContent =  $message . "\n\n" . $textContent;

    return array($textContent, $HTMLContent);
}
?>