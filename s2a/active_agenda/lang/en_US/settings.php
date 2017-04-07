<?php

/**
 *  Localization settings for a locale
 */

$dateFormats = array(

    //generic date format
    'date' => 'MM/DD/YYYY',

    //MySQL date format
    'dateDB' => '%m/%d/%Y',

    //PHP date string: see http://php.net/strftime for valid symbols
    'datePHP'=> '%m/%d/%Y',

    //MySQL datetime format
    'dateTimeDB' => '%m/%d/%Y %h:%i %p',

    //PHP date+time string: see http://php.net/strftime for valid symbols
    'dateTimePHP'=> '%m/%d/%Y %I:%M %p',

    //the following are jsCalendar-specific date strings
    //same as datePHP
    'dateCal'=> '%m/%d/%Y',

    //same as dateTimePHP
    'dateTimeCal'=> '%m/%d/%Y %I:%M %p',

    //either '24' (24-hour format) or '12' (for AM/PM time format)
    'timeFormat'=> '12',

    //PHP time format
    'timePHP'    => '%I:%M %p',

    //MySQL time format
    'timeDB' => '%h:%i %p',

    //whether Monday is the first day of the week: if false, Sunday will be first
    'mondayFirst'=> false,

    //whether to display ISO week numbers
    'weekNumbers'=> false
);

//page formats, with most preferred format first. US formats are 'letter' and 'legal'
$pageFormats = array('letter', 'legal');

?>