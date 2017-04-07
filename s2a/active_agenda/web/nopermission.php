<?php
/**
 * Displays an error message to the user, and logs a failed access attempt
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

//main include file - performs all general application setup
require_once INCLUDE_PATH . '/page_startup.php';

$SQL = "SELECT Count(*) FROM usrl WHERE EventTypeID = 10 AND PersonID = '{$User->PersonID}' AND TIMEDIFF(NOW(), _ModDate) <= '00:30:00'";

$attempts = $dbh->getOne($SQL);

header('HTTP/1.0 401 Unauthorized');
$content =  "\n";


if(intval($attempts) < 3){
    $content .= '<p>'.gettext("You are seeing this page because you don't have permission to the record you were trying to access.");
    $content .= "<p/>\n<p>";
    $content .= gettext("If you feel you require access permissions to this module, please contact your local Active Agenda Administrator.  Please note that new permissions aren't available until the next time you log in.");
    $content .= "<p/>\n";
} else {
    $content .= gettext("Alright. That's enough tries for now."); 
    $content .= "<br />\n";
    $content .= gettext("You have been locked out from the application for 30 minutes. Here's some interesting reading while you wait:");
    $content .= "<br />\n";
    $content .= "<br />\n";
    $poem = gettext("<blockquote><strong>The Guy in the Glass</strong>
<em>by Dale Wimbrow, &#169; 1934</em>

When you get what you want in your struggle for pelf,
And the world makes you King for a day,
Then go to the mirror and look at yourself,
And see what that guy has to say.

For it isn't your Father, or Mother, or Wife,
Who judgement upon you must pass.
The feller whose verdict counts most in your life
Is the guy staring back from the glass.

He's the feller to please, never mind all the rest,
For he's with you clear up to the end,
And you've passed your most dangerous, difficult test
If the guy in the glass is your friend.

You may be like Jack Horner and \"chisel\" a plum,
And think you're a wonderful guy,
But the man in the glass says you're only a bum
If you can't look him straight in the eye.

You can fool the whole world down the pathway of years,
And get pats on the back as you pass,
But your final reward will be heartaches and tears
If you've cheated the guy in the glass.</blockquote>");
    $content .= nl2br($poem);
    $User->Block(); //lock out user for thirty minutes
    session_destroy();
}

//if there are a number of logged permission denials, display the "Man in the Mirror" poem the last time, then shut the user out (log out and block connection for, say 30 minutes)

$tabs["Noaccess"] = Array('', gettext("No Access"));

$title = gettext("Permission Denied");
$screenPhrase = gettext("Permission Denied");
include $theme . '/nopermission.template.php';
?>