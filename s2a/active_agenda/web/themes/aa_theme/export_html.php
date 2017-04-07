<?php
/**
 * HTML snippets for exported dashboard charts (for use on external sites)
 *
 * You may wish to tweak the contents of this file to match your public web
 * site.
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
 * @version        SVN: $Revision: 765 $
 * @last-modified  SVN: $Date: 2007-05-14 11:20:19 -0700 (Mon, 14 May 2007) $
 * @last-author    SVN: $Author: code_g $
 **/


/**
 *  A title for the exported charts page.
 *
 *  Change it to whatever you like.
 */
define('CHART_EXPORT_TITLE', 'Public Charts from '.SITE_NAME);


/**
 *  The imediate area surrounding a chart.
 *
 *  parameters: chart ID, title, condition phrases, chart image
 */
define('CHART_EXPORT_TRIM',
'<div class="export_charttrim" id="export_chart%1$s">
<div class="export_chartarea">
<div class="export_chartbar">
<h3>%2$s</h3>
</div>
<p class="export_chartphrases">%3$s</p>
   <img src="%4$s" alt="%2$s"/>
</div>
</div>');


/**
 *  For embedding in external page.
 *
 *  parameters: title, content
 */
define('CHART_EXPORT_EMBED',
'<style>
 /**insert style directives here**/
</style>
<div class="export_chart_container">
    <h1>%1$s</h1>
    %2$s
</div>
');


/**
 *  Defines a full HTML page (together with CHART_EXPORT_LAYOUT) for use as an external page.
 *
 *  parameters: title, content
 */
define('CHART_EXPORT_PAGE',
'<html>
    <head>
        <title>%1$s</title>
    </head>
    <body>
        %2$s
    </body>
</html>');
?>
