<?php
//ntfSendForm.php
/**
 * Custom snippet for displying the "Send" button in the Notifications Send screen
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

if($ntf_sent){
    $content .= gettext("This notification has been sent.");
} else {
    if(ntf_checkForRecipients()){
        $sendFormTemplate = '<div class="searchFilter"><p>%s</p>%s</div>';
        $sendPhrase = gettext("To send this notification to the recipients listed below, press the button.");
        $sendForm = '<form action="'.$targetlink.'" method="post">
    <input class="btn" type="submit" name="Send" value="'.gettext("Send this Notification").'"/>
</form>';
        $content .= sprintf(
            $sendFormTemplate, 
            $sendPhrase,
            $sendForm
            );
    } else {
        $content .= gettext("There are no recipients for this notification. To send it, first go to the Recipients screen and add some.");
    }
}
?>