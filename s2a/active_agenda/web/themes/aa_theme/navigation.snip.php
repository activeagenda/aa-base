<?php
/**
 * HTML/PHP layout template for the Navigation frame
 *
 * NOTE that this template is different in that it only customizes the <BODY> part 
 * of the document (excluding the BODY tag itself. <HEADER> is declared in 
 * web/navigation.php
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

if(!defined('EXEC_STATE') || EXEC_STATE != 1){
    print gettext("This file should not be accessed directly.");
    trigger_error("This file should not be accessed directly.", E_USER_ERROR);
    exit;
}
?>

<table id="nav_table" cellspacing="0">
<tr>

<td id="nav_menu">
    <img src="<?php echo $theme_web; ?>/img/nav_starthere.gif" alt="<?php echo gettext("menu"); ?>"/><br />
    <?php echo gettext("Main Menu"); ?>
</td>

<td class="navicon">
    <a href="#" onclick="parent.frames[1].location = 'home.php'" title="<?php echo gettext("Home - Dashboard"); ?>">
        <img src="<?php echo $theme_web; ?>/img/nav_dashboard.gif" alt="<?php echo gettext("dashboard"); ?>"/><br />
        <?php echo gettext("Dashboard") ?>
    </a>
</td>

<td id="nav_sitename">
    <h1><?php echo SITE_NAME; ?></h1>
    <?php echo $user_info;?>
</td>

<td class="navicon" id="nav_guide">
    <a href="#" target="_blank" title="<?php echo gettext("Quick Start Guide"); ?>" onclick="open('supportDocView.php?mdl=tut', 'documentation', 'toolbar=1,resizable=1,menubar=1,location=1,scrollbars=1,width=800,height=600');">
        <img src="<?php echo $theme_web; ?>/img/nav_quick_guide.gif" alt="<?php echo gettext("quickstart"); ?>"/><br />
        <?php echo gettext("Quick Guide"); ?>
    </a>
</td>

<td class="navicon">
    <a href="#" title="<?php echo gettext("Look up a term in the Glossary"); ?>" onclick="open('glossary.php', 'glossary', 'toolbar=0,scrollbars=1,width=600,height=600');">
        <img src="<?php echo $theme_web; ?>/img/nav_glossary.gif" alt="<?php echo gettext("glossary"); ?>"/><br />
        <?php echo gettext("Glossary"); ?>
    </a>
</td>


<td class="navicon">
    <?php
        if(defined('CONTACT_LINK')){
            $contact_link = CONTACT_LINK;
        } else {
            $contact_link = 'http://www.activeagenda.net/contactform/';
        }
    ?>
    <a href="<?php echo $contact_link ?>" target="_blank" title="<?php echo gettext("Email Us!"); ?>">
        <img src="<?php echo $theme_web; ?>/img/nav_email.gif" alt="<?php echo gettext("email"); ?>"/><br />
        <?php echo gettext("Email Us!"); ?>
    </a>
</td>

<td class="navicon">
    <a href="http://www.activeagenda.net/discussions/" target="_blank" title="<?php echo gettext("Dicusssion Forums"); ?>">
        <img src="<?php echo $theme_web; ?>/img/nav_discussions.gif" alt="<?php echo gettext("discussion forums"); ?>"/><br />
        <?php echo gettext("Forums"); ?>
    </a>
</td>

<td class="navicon" id="nav_bugreport">
    <a href="http://www.activeagenda.net/bugs/" target="_blank" title="<?php echo gettext("File a Bug Report or Feature Request"); ?>">
        <img src="<?php echo $theme_web; ?>/img/nav_bugreport.gif" alt="<?php echo gettext("report bug"); ?>"/><br />
        <?php echo gettext("Report Bug"); ?>
    </a>
</td>

<td class="navicon">
    <a href="#" title="<?php echo gettext("Look up a term in the Glossary"); ?>" onclick="open('glossary.php', 'glossary', 'toolbar=0,scrollbars=1,width=600,height=600');">
        <img src="<?php echo $theme_web; ?>/img/nav_glossary.gif" alt="<?php echo gettext("glossary"); ?>"/><br />
        <?php echo gettext("Glossary"); ?>
    </a>
</td>

<td class="navicon">
    <a href="#" onclick="parent.frames[1].location.reload()">
        <img src="<?php echo $theme_web; ?>/img/nav_reload_page.gif" title="<?php echo gettext("Reload the page below"); ?>" alt="<?php echo gettext("reload"); ?>"/><br />
        <?php echo gettext("Reload"); ?>
    </a>
</td>

<td class="navicon">
    <a href="logout.php">
        <img src="<?php echo $theme_web; ?>/img/nav_exit.gif" title="<?php echo gettext("Log out"); ?>" alt="<?php echo gettext("logout"); ?>"/><br />
        <?php echo gettext("Log Out"); ?>
    </a>
</td>
</tr>
</table>