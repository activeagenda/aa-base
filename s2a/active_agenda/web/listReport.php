<?php
/**
 * Generates a PDF report based on the List screen fields and search conditions
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
require_once '../config.php';
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//this include contains the search class
include_once CLASSES_PATH . '/search.class.php';

//contains the list data class
include_once CLASSES_PATH . '/lists.php';
require_once PEAR_PATH . '/File/PDF.php';


class ListReportPDF extends File_PDF
{


function header()
{
    global $title;
    global $search;
    global $orderBys;
    global $fieldHeaders;
    global $workSheet;

    $this->setFont('Arial', '', 24);
    $this->multiCell(0, 36, $title);
    $this->newLine();

    if(!$workSheet){
        if($search->hasConditions()){
            $this->setFont('Arial', 'B', 8);
            $this->cell(0, 12, gettext("Search conditions:"));
            $this->newLine();
            $this->setFont('Arial', '', 8);
            foreach($search->phrases as $phrase){
                $this->cell(0, 12, $phrase);
                $this->newLine();
            }
            $this->newLine();
        }

        if(count($orderBys) > 0){
            $this->setFont('Arial', 'B', 8);
            $this->cell(0, 12, gettext("Ordering:"));
            $this->newLine();
            $this->setFont('Arial', '', 8);

            $orderByPhrases = array(
                0 => gettext("ascending"),
                1 => gettext("descending")
            );
            foreach($orderBys as $orderByField => $orderByDirection){
                $this->cell(0, 12, $fieldHeaders[$orderByField] .' ('.$orderByPhrases[intval($orderByDirection)].')');
                $this->newLine();
            }
            $this->newLine();
        }
    }

    $this->printHeaders($fieldHeaders);
    $this->newLine();
}


function footer()
{
    global $data;
    if(count($data) > 0){
        $this->drawVerticalLines();
    }
    global $theme;
    $this->image($theme .'/img/logo-pdfinclude-noalpha.png', 24,-60,126);

    // Go to 36 pt from bottom
    $this->setY(-48);
    // Select Arial italic 8
    $this->setFont('Arial', 'I', 8);
    // Print centered
    $this->cell(0, 10, 'www.ActiveAgenda.net', 0, 0, 'C', 0, 'http://www.activeagenda.net');
    $this->newLine();
    $this->cell(0, 10, '"Controlling Loss, Not Minds, Methods, or Markets"', 0, 0, 'C');
    $this->newLine();
    //page number
    $this->cell(0, 10, 'Page ' . $this->getPageNo() . ' of {nb}', 0, 0, 'R');
}


function checkPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->getY() + $h > $this->_page_break_trigger){
        $this->addPage();
    }
}


//estimates how many lines a multiCell field would generate (not exact but hopefully good enough)
function getNumberOfLines($width, $text)
{
    if(strlen($text) > 0){
        $textWidth = $this->getStringWidth($text);
        $wrapLines = ceil($textWidth / $width); //number of lines because of line wrapping (approx)
        $hardLines = substr_count($text, "\n");
        $nLines = $wrapLines + $hardLines;
    } else {
        $nLines = 1; //even if the text is empty, there will be a line (ceil function above would return 0)
    }

    return $nLines;
}


function printEmpty($fieldHeaders)
{
    $this->setFont('Arial', 'B', 16);
    $this->multiCell(0, 24, gettext("There is no matching data."));
}


function printHeaders($fieldHeaders)
{
    global $fieldWidths;
    global $dataTextSize;
    global $fieldAlign;
    $dataLineHeight = $dataTextSize * 1.5;
    $this->setFont('Arial', 'B', $dataTextSize);


    foreach($fieldHeaders as $fieldName => $fieldHeader){
        if('hide' != $fieldAlign[$fieldName]){
            $headerWidth = $fieldWidths[$fieldName];
            $this->cell($headerWidth, $dataLineHeight, $fieldHeader, 0, 0, 'C');
        }
    }

    //start the row size here again
    $this->setFont('Arial', '', $dataTextSize);

    $y=$this->GetY() + $dataLineHeight;
    $this->headerY = $y;
    $this->drawHorizontalLine($y, false);

}


function printRows($data, $fieldHeaders, $fieldTypes)
{
    global $dataTextSize;
    global $fieldAlign;

    $this->setFont('Arial', '', $dataTextSize);
    $dataLineHeight = $dataTextSize * 1.5;

    $alignTranslation = array(
        'left' => 'L',
        'center' => 'C',
        'right' => 'R'
    );

    global $fieldWidths; //ugly? oh yes!
    foreach($data as $row_ix => $row){
        //determine row height
        $rowHeight = 0;
        foreach($row as $fieldName => $fieldVal){
            if('hide' != $fieldAlign[$fieldName]){
                $fieldHeight = 1.5 * $this->_font_size * $this->getNumberOfLines($fieldWidths[$fieldName], $fieldVal);
                if($rowHeight < $fieldHeight){
                    $rowHeight = $fieldHeight;
                }
            }
        }

        //check whether we need a new page
        $this->checkPageBreak($rowHeight);
        $this->setFont('Arial', '', $dataTextSize);//re-set it

        //write cells
        foreach($row as $fieldName => $fieldVal){
            if('hide' != $fieldAlign[$fieldName]){
                $fieldWidth = $fieldWidths[$fieldName];
                $align = $alignTranslation[$fieldAlign[$fieldName]];
                if(empty($align)){
                    $align = 'L';
                }

                //Save the current position
                $x=$this->GetX();
                $y=$this->GetY();

                $this->multiCell($fieldWidth, $dataLineHeight, $fieldVal, 0, $align);

                //corrects the row height if it was wrong
                if($this->GetY() - $y > $rowHeight){
                    $rowHeight = $this->GetY() - $y;
                }

                $this->SetXY($x+$fieldWidth,$y);
            }
        }

        //could possibly draw the borders AFTER adding row content (actual row height is known better)

        $this->newLine($rowHeight);
        $y=$this->GetY();
        $this->drawHorizontalLine($y);
    }
}


function printWorkSheet($fieldHeaders)
{
    $lineHeight = 24;
    do {
        $this->newLine($lineHeight);
        $y=$this->GetY();
        $this->drawHorizontalLine($y);
    } while($this->getY() + $lineHeight < $this->_page_break_trigger);
}


function drawHorizontalLine($y, $useColor = true)
{
    if($useColor){
        $this->setDrawColor('rgb', 0.8, 0.8, 0.8);
    } else {
        $this->setDrawColor('rgb', 0.0, 0.0, 0.0);
    }
    $this->line($this->_left_margin, $y, $this->fw - $this->_right_margin, $y);
}


function drawVerticalLines()
{
    $bottom = $this->getY();
    $top = $this->headerY+1;//keeps the header line intact
    $left = $this->_left_margin;

    global $fieldWidths;
    $drawWidths = $fieldWidths;
    array_pop($drawWidths);

    $this->setDrawColor('rgb', 0.8, 0.8, 0.8);
    $this->line($left, $top, $left, $bottom);

    $right = $this->fw - $this->_right_margin;
    $this->line($right, $top, $right, $bottom);

    foreach($drawWidths as $drawWidth){
        $left = $left + $drawWidth;
        $this->line($left, $top, $left, $bottom);
    }
}
} //end class ListReportPDF



//main include file - performs all general application setup
require_once INCLUDE_PATH . '/page_startup.php';

//check if user has permission to view or edit record
$allowEdit = $User->CheckListScreenPermission(); //edit permission is irrelevant, but it will redirect if no permission at all

$listFieldsFileName = GENERATED_PATH . "/{$ModuleID}/{$ModuleID}_ListFields.gen";

//check for cached page for this module
if (!file_exists($listFieldsFileName)){
    trigger_error("Could not find list fields file '$listFieldsFileName'.", E_USER_ERROR);
}

include_once $listFieldsFileName; //returns $fieldHeaders, $fieldTypes, $listFields, $linkFields, $fieldAlign

//remove IsBestPractice
unset($fieldHeaders['IsBestPractice']);
unset($fieldTypes['IsBestPractice']);
unset($linkFields['IsBestPractice']);
unset($fieldAlign['IsBestPractice']);


$moduleInfo = GetModuleInfo($ModuleID);

$useBestPractices = false;
$listFilterSQL = $User->getListFilterSQL($ModuleID);

if(isset($_SESSION['Search_'.$ModuleID])){
    $search = $_SESSION['Search_'.$ModuleID];
} else {
    //create an empty Search object
    $search = GetNewSearch($ModuleID);

    if('1' != $_GET['clear']){
        $search->loadUserDefault($User->PersonID);
    }
    $_SESSION['Search_'.$ModuleID] = $search;
}

if(!empty($_SESSION['ListOrder_'.$ModuleID.'_list'])){
    $orderBys = $_SESSION['ListOrder_'.$ModuleID.'_list'];
}
if(!empty($_GET['ob'])){
    $inputOBs = split(',', $_GET['ob']);
    $orderBys = array();
    foreach($inputOBs as $inputOB){
        if('-' == $inputOB[0]){
            $desc = true;
            $inputOB = substr($inputOB, 1);
        } else {
            $desc = false;
        }
        if(in_array($inputOB, $listFields)){
            $orderBys[$inputOB] = $desc;
        }
    }

}

//set $fieldTypes

if(empty($_GET['worksheet'])){
    $listData =& new ListData($ModuleID, $search->getListSQL(null, true), null, $fieldTypes, false);
    $nRows = $listData->getCount();

    $data = $listData->getData($orderBys);
    $workSheet = false;
} else {
    $data = array();
    $workSheet = true;
}

//print debug_r($listFields);
//print debug_r($linkFields);
//print debug_r($listData);

//print debug_r($fieldHeaders);
//print debug_r($fieldTypes);

//print debug_r($data);

//find largest size of fields (approximate)
$fieldSizes = array();
$totalHeaderWidth = 0;
foreach($fieldHeaders as $fieldName => $fieldHeader){
    if('hide' != $fieldAlign[$fieldName]){
        $fieldHeader = ShortPhrase($fieldHeader);
        $fieldHeaders[$fieldName] = $fieldHeader;
        $fieldWidth = strlen($fieldHeader);
        $fieldSizes[$fieldName] = $fieldWidth;
        $totalHeaderWidth += $fieldWidth;
    }
}
if(count($data) > 0){
    foreach($data as $dataRowIX => $dataRow){
        foreach($dataRow as $fieldName => $fieldValue){
            if('hide' != $fieldAlign[$fieldName]){
                $fieldValue = fldFormat($fieldTypes[$fieldName], $fieldValue);
                $dataLength = strlen($fieldValue);
                if($fieldSizes[$fieldName] < $dataLength){
                    $fieldSizes[$fieldName] = $dataLength;
                }
                $dataRow[$fieldName] = $fieldValue;
            }
        }
        $data[$dataRowIX] = $dataRow;
    }
}
//print "totalHeaderWidth = $totalHeaderWidth<br />\n";
//print debug_r($fieldSizes);
$maxFieldSize = 50;
$minFieldSize = 10;

//massage field widths
foreach($fieldSizes as $fieldName => $fieldSize){
    if($fieldSize > $maxFieldSize){
        $fieldSize = $maxFieldSize; //causes wrapping but prevents disproportionate column widths
    } elseif($fieldSize < $minFieldSize) {
        $fieldSize = $minFieldSize;
    }
    $fieldSizes[$fieldName] = $fieldSize;
}




if($workSheet){
    $title = gettext("Worksheet for the %s module");
    $dataTextSize = 8;
} else {
    $title = gettext("Records in the %s module");
    $dataTextSize = 6;
}
$moduleInfo = GetModuleInfo($ModuleID);
$title = sprintf($title, $moduleInfo->getProperty('moduleName'));

$pageFormat = reset($User->pageFormats);

$pdf = &ListReportPDF::factory(array('orientation' => 'P','unit' => 'pt','format' => $pageFormat), 'ListReportPDF');
$pdf->aliasNbPages();


$pdf->open();
$pdf->setMargins(24, 24);
$pdf->setAutoPageBreak(false, 60);

$availableWidth = $pdf->fw - $pdf->_left_margin - $pdf->_right_margin;

//print "availableWidth = $availableWidth<br/>\n";

$totalRelWidth = 0;
foreach($fieldSizes as $fieldSize){
    $totalRelWidth += $fieldSize;
}

//make absolute field widths
$widthConverter = $availableWidth/$totalRelWidth;
$fieldWidths = array();
foreach($fieldSizes as $fieldName => $fieldSize){
    $fieldWidths[$fieldName] = round($fieldSize * $widthConverter);
}

$pdf->addPage('P');

if($workSheet){
    $pdf->printWorkSheet($fieldHeaders);
} else {
    if(count($data) > 0){
        $pdf->printRows($data, $fieldHeaders, $fieldTypes);
    } else {
        $pdf->printEmpty($fieldHeaders);
    }
}

$pdf->close();

if($User->browserInfo['is_IE']){
    $inline = true;
} else {
    $inline = false;
}

if($workSheet){
    $pdf->output($ModuleID.'_workSheet.pdf', $inline);
} else {
    $pdf->output($ModuleID.'_listReport.pdf', $inline);
}




?>