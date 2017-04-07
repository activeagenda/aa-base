<?php
/**
 * Utility functions for file uploads
 *
 *
 * PHP version 5
 *
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
 * @author         Mattias Thorslund <mthorslund@activeagenda.net>
 * @copyright      2003-2009 Active Agenda Inc.
 * @license        http://www.activeagenda.net/license  RPL 1.5
 * @version        SVN: $Revision: 1406 $
 * @last-modified  SVN: $Date: 2009-01-27 07:56:18 +0100 (Wt, 27 sty 2009) $
 */

function CheckFileUploads($fileField){
    switch($_FILES[$fileField]['error']){
    case UPLOAD_ERR_OK:

        if(is_uploaded_file($_FILES[$fileField]['tmp_name'])){
            //print "file upload went well<br>\n";
            return TRUE;
        } else {
            print "what's the matter?<br>\n";
        }
        break;
    case UPLOAD_ERR_INI_SIZE:
        print "file upload: file is larger than allowed for this server<br>\n";
        break;
    case UPLOAD_ERR_FORM_SIZE:
        print "file upload: file is larger than allowed for this form<br>\n";
        break;
    case UPLOAD_ERR_PARTIAL:
        print "file upload: file was not completely uploaded<br>\n";
        break;
    case UPLOAD_ERR_NO_FILE:
        print "file upload: no file<br>\n";
        break;
    default:
        print "unknown file upload status<br>\n";
        print_r($_FILES);
    }
    return FALSE;
}


function HandleFileUploads($fileField, $attachmentID){

    //move the file to the right folder

    $destination = UPLOAD_PATH . '/' . $_FILES[$fileField]['name'];
    move_uploaded_file($_FILES[$fileField]['tmp_name'], $destination);

}

?>