<?php

/**
 * Adds a popup link for the Reassign Accountabilities screen to the $content variable.
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

$content .= "<br />\n";
$content .= "<br />\n";
$content .= '<div class="dl_icon" style="clear:left">';
$content .= '<a href="#" onclick="open(\'popReassignAccs.php\', \'reassign\', \'toolbar=0,width=600,height=600\');" title="'.gettext("Reassign these accountabilities to a different person").'"><img src="'.$theme_web.'/img/reassign-accs.png"/><br />';
$content .= gettext("Reassign<br>Accountabilities");
$content .= '</a></div>';
?>