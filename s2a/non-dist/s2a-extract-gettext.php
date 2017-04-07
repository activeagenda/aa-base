<?
/**
 * Utility to rearrange the gettext catalog into several subcatalogs.
 *
 * This is a work in progress.
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
 * @version        SVN: $Revision: 1367 $
 * @last-modified  SVN: $Date: 2008-12-18 09:39:45 -0800 (Thu, 18 Dec 2008) $
 */

print "extracting messages for main catalog\n";
$catalog_name = 'activeagenda';
$command = "find . -name '*.php' | xgettext -f - -d $catalog_name -L PHP";
$result = exec($command);
print $result."\n";

if(!file_exists($catalog_name.'.po')){
    die("could not find $catalog_name.po\n");
}
rename($catalog_name.'.po', 'active_agenda/lang/templates/'.$catalog_name.'.pot');

$module_folders = glob('active_agenda/.generated/*');
foreach($module_folders as $module_folder){
    if(is_dir($module_folder)){
        $module_id = basename($module_folder);
        print "extracting messages for ".$module_id." ";
        $command = "find $module_folder -name '*.gen' | xgettext -f - -d $module_id -L PHP";
        $result = exec($command);
        print $result."\n";
        if(file_exists($module_id.'.po')){
            rename($module_id.'.po', 'active_agenda/lang/templates/'.$module_id.'.pot');
        }
    }
}
print "done\n";
?>