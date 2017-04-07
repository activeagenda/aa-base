<?php
/**
 * HTML snippets for dashboard elements
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
 * @version        SVN: $Revision: 1406 $
 * @last-modified  SVN: $Date: 2009-01-27 07:56:18 +0100 (Wt, 27 sty 2009) $
 * @last-author    SVN: $Author: code_g $
 **/

//chart ID, title, condition phrases, chart
define('CHART_TRIM',
'<div class="charttrim" id="chart%1$s">
<div class="chartarea">
<div class="chartbar">
<div class="charticons">
    <a onclick="moveChartUp(%1$s)"><img src="'.$theme_web.'/img/chart_trsp_left.png" title="'.gettext("move chart left/up").'" alt="'.gettext("move chart left/up").'" onmouseover="imgOver(this, \''.$theme_web.'/img/chart_trsp_left_o.png\')" onmouseout="imgOut(this)"/></a><a onclick="moveChartDown(%1$s)"><img src="'.$theme_web.'/img/chart_trsp_right.png" title="'.gettext("move chart right/down").'" alt="'.gettext("move chart right/down").'" onmouseover="imgOver(this, \''.$theme_web.'/img/chart_trsp_right_o.png\')" onmouseout="imgOut(this)"/></a><a onclick="removeChart(%1$s)"><img src="'.$theme_web.'/img/chart_trsp_remove.png" title="'.gettext("remove this chart from the desktop").'" alt="'.gettext("remove this chart from the desktop").'" onmouseover="imgOver(this, \''.$theme_web.'/img/chart_trsp_remove_o.png\')" onmouseout="imgOut(this)"/></a>
</div>
<h3>%2$s</h3>
</div>

<p class="chartphrases">%3$s</p>
%4$s
</div>
</div>');

?>