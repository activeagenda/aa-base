<?php

/**
 *  Localization settings for a locale
 */

$dateFormats = array(

    //generic date format
    'date' => 'YYYY-MM-DD',

    //MySQL date format
    'dateDB' => '%Y-%m-%d',

    //PHP date string: see http://php.net/date for valid symbols
    'datePHP'=> '%Y-%m-%d',

    //MySQL datetime format
    'dateTimeDB' => '%Y-%m-%d %H:%i',

    //PHP date+time string: see http://php.net/date for valid symbols
    'dateTimePHP'=> '%Y-%m-%d %H:%M',

    //the following are jsCalendar-specific date strings
    //same as datePHP
    'dateCal'=> '%Y-%m-%d',

    //same as dateTimePHP
    'dateTimeCal'=> '%Y-%m-%d %H:%M',

    //either '24' (24-hour format) or '12' (for AM/PM time format)
    'timeFormat'=> '24',

        //PHP time format
    'timePHP' => '%H.%M',

    //MySQL time format
    'timeDB' => '%H:%i',

    //whether Monday is the first day of the week: if false, Sunday will be first
    'mondayFirst'=> true,

    //whether to display ISO week numbers
    'weekNumbers'=> true
);

//page formats, with most preferred format first. US formats are 'letter' and 'legal'
$pageFormats = array('A4', 'A3');

?>