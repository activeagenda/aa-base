#!/usr/bin/php
<?php
/**
 * Utility to make a database update script
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




define('PARSE_TIME', true);

$new_location = $_SERVER[argv][1];  //folder location
$old_location = $_SERVER[argv][2];  //folder location
$old_version_name = $_SERVER[argv][3];  //like '0.8.0'
$new_version_name = $_SERVER[argv][4];  //like '0.8.1'

$project = 'active_agenda';


print "Utility to generate upgrade script for migrating an Active Agenda database to another version.\n\n";

if(empty($new_version_name)){
    print 'USAGE: s2a-generate-db-upgrade.php new_location old_location project
    new_location: s2a folder of new version to be released
    old_location: s2a folder of old version that the upgrade
    old_version: version number of previous release
    new_version: version number of currrent release
    project: defaults to \'active_agenda\'';
    exit;
}

//assumes we're in the 's2a' folder 
$site_folder = $new_location;
$site_folder .= '/'.$project;

//includes
$config_file = $site_folder . '/config.php';
if(!file_exists($config_file)){
    print "Config file not found at $config_file\n";
    exit;
}
$gen_config_file = $site_folder . '/gen-config.php';
if(!file_exists($gen_config_file)){
    print "Config file not found at $gen_config_file\n";
    exit;
}

//get settings
include_once $config_file;
include_once $gen_config_file;
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());
require_once PEAR_PATH . '/DB.php' ;  //PEAR DB class
include_once INCLUDE_PATH . '/general_util.php';
include_once INCLUDE_PATH . '/parse_util.php';
include_once INCLUDE_PATH . '/web_util.php'; //need dbQuote()

$new_glob = GENERATED_PATH . '/*/*ModuleFields.gen';
//print "$new_glob\n";
$new_modulefields_files = glob($new_glob);

$remove_modules = array();

if(prompt("Are there any modules that should be DROPPED entirely in the new version? ")){
    do {
        $str_remove_modules = textPrompt("Please enter the modules to be dropped (comma or space-separated):");
        $str_remove_modules = str_replace(',', ' ', $str_remove_modules);
        while(false !== strpos($str_remove_modules, '  ')){
            $str_remove_modules = str_replace('  ', ' ', $str_remove_modules);
        }
        $remove_modules = explode(' ', $str_remove_modules);
        print "Will remove these modules in the new package:\n";
        print_r($remove_modules);
    } while(!prompt("Is this correct?"));
}

if(prompt("Are there any modules that should be REPLACED (good heavens, hope not!) entirely in the new version? ")){
    do {
        $str_replace_modules = textPrompt("Please enter the modules to be replaced (comma or space-separated):");
        $str_replace_modules = str_replace(',', ' ', $str_replace_modules);
        while(false !== strpos($str_replace_modules, '  ')){
            $str_replace_modules = str_replace('  ', ' ', $str_replace_modules);
        }
        $replace_modules = explode(' ', $str_replace_modules);

        print "Will replace these modules in the new package:\n";
        print_r($replace_modules);

        $remove_modules = array_merge($remove_modules, $replace_modules); //include replaced modules here
        foreach($replace_modules as $replace_module_id){
            $module_changes['new_modules'][] = $replace_module_id;
        }

    } while(!prompt("Is this correct?"));
}
//print_r($new_modulefields_files);

$module_changes = array();

foreach($new_modulefields_files as $file_path){

    $missing_fields = array(); //array for dropped or missing fields
    $new_fields = array();     //array for new fields or new field names

    $drop_fields = array();    //array of fields to drop
    $rename_fields = array();  //array of fields to rename (old => new)
    $alter_fields = array();   //array of fields to be altered

    $remote_new = array();     //fields that were changed from tablefield to remotefield
    $remote_old = array();     //fields that were changed from remotefield to tablefield

    $file_name = basename($file_path);
    $moduleID = substr($file_name, 0, strpos($file_name, '_'));

    print "Now comparing fields in $moduleID \n";
    include($file_path);
    $new_modulefields = unserialize($modulefields);


    $old_modulefields_file = $old_location . '/active_agenda/.generated/'.$moduleID.'/'.$moduleID.'_ModuleFields.gen';
    if(!file_exists($old_modulefields_file)){
        $old_modulefields_file = $old_location . '/active_agenda/.generated/'.$moduleID.'_ModuleFields.gen';
    }
    print $old_modulefields_file ."\n";
    if((!in_array($moduleID, $replace_modules)) && file_exists($old_modulefields_file)){
        include($old_modulefields_file);
        $old_modulefields = unserialize($modulefields);
        //print_r(array_keys($old_modulefields));

        foreach($old_modulefields as $old_modulefield_name => $old_modulefield){
            if(in_array(strtolower(get_class($old_modulefield)), array('tablefield', 'remotefield'))){
                //detect dropped/re-named fields
                if(!in_array($old_modulefield_name, array_keys($new_modulefields)) ){
                    $missing_fields[] = $old_modulefield_name;
                }
            }
        }

        foreach($new_modulefields as $new_modulefield_name => $new_modulefield){
            if('tablefield' == strtolower(get_class($new_modulefield))){
                //detect added/re-named fields
                if(!in_array($new_modulefield_name, array_keys($old_modulefields)) ){
                    $new_fields[] = $new_modulefield_name;
                    print "field $new_modulefield_name was added or renamed\n";
                }
            }
        }

        if(count($missing_fields) > 0){
            foreach($missing_fields as $missing_field){

                if(count($new_fields) > 0){
                    print "The field $missing_field was dropped or renamed. New fields in $moduleID:\n";
                    foreach($new_fields as $ix => $new_field_name){
                        print "$ix: $new_field_name ({$new_modulefields[$new_field_name]->dataType})\n";
                    }
                    print "\n";
                    if(prompt("Map the missing field $missing_field ({$old_modulefields[$missing_field]->dataType}) to one of the above names?")){
                        $choice = textPrompt("Enter the number associated with that name:");
                        $new_name = $new_fields[$choice];
                        print "You chose $choice: {$new_name}\n";

                        //map old an new names, remove new name from $new_fields
                        unset($new_fields[$choice]);
                        $rename_fields[$missing_field] = $new_name;

                        //die('test');
                    }
                } else {
                    $drop_fields[] = $missing_field;
                }
            }
        }

        //check for changes
        foreach($old_modulefields as $old_modulefield_name => $old_modulefield){
            if(!in_array($old_modulefield_name, $drop_fields)){
                //find corresponding new field:
                if(isset($rename_fields[$old_modulefield_name])){
                    $new_modulefield_name = $rename_fields[$old_modulefield_name];
                } else {
                    $new_modulefield_name = $old_modulefield_name;
                }

                if(in_array(strtolower(get_class($old_modulefield)), array('tablefield', 'remotefield'))){

                    $new_modulefield = $new_modulefields[$new_modulefield_name];

                    if('tablefield' == strtolower(get_class($old_modulefield))){
                        if('tablefield' == strtolower(get_class($new_modulefield))){
                            if($old_modulefield->dataType !== $new_modulefield->dataType ||
                                $old_modulefield->dbFlags !== $new_modulefield->dbFlags){
                                $alter_fields[] = $new_modulefield_name; 
                            }

                        } elseif('remotefield' == strtolower(get_class($new_modulefield))) {
                            //changed from tablefield to remotefield
                            $remote_new[$old_modulefield_name] = $new_modulefield_name;
                            $drop_fields[] = $old_modulefield_name;
                        }
                    } else {
                        if('tablefield' == strtolower(get_class($new_modulefield))){
                            //changed from remotefield to tablefield
                            $remote_old[$old_modulefield_name] = $new_modulefield_name;
                        }
                    }
                }
            }
        }

        if(count($new_fields) > 0){
            print "Fields to add:\n";
            print_r($new_fields);
            $module_changes['add'][$moduleID] = $new_fields;
        }

        //copy from old tablefield to new remotefield (use data handler)
        if(count($remote_new) > 0){
            print "Field changed from tablefield to remotefield:\n";
            print_r($remote_new);
            $module_changes['remote_new'][$moduleID] = $remote_new;
        }

        if(count($rename_fields) > 0){
            print "Fields to rename:\n";
            print_r($rename_fields);
            $module_changes['rename'][$moduleID] = $rename_fields;
        }
        if(count($alter_fields) > 0){
            print "Fields to alter:\n";
            print_r($alter_fields);
            $module_changes['alter'][$moduleID] = $alter_fields;
            //"MODIFY COLUMN {$new_modulefield_name} {$dataTypes[$new_modulefield->dataType]} {$new_modulefield->dbFlags}\n";
        }

        //copy from old remotefield to new tablefield (use UPDATE stmt)
        if(count($remote_old) > 0){
            print "Field changed from remotefield to tablefield:\n";
            print_r($remote_old);
            $module_changes['remote_old'][$moduleID] = $remote_old;
        }

        if(count($drop_fields) > 0){
            print "Fields to drop:\n";
            print_r($drop_fields);
            $module_changes['drop'][$moduleID] = $drop_fields;
        }

        //determine whether the module is a master module
        $module_info = GetModuleInfo($moduleID);
        $is_master_data = $module_info->getProperty('isMasterData');
        if($is_master_data){
            $module_changes['master_modules'][] = $moduleID;
        }

    } else {
        print "Module $moduleID does not exist in old version.\n";

        $module_changes['new_modules'][] = $moduleID;
    }

}

if(count($remove_modules)){
    foreach($remove_modules as $remove_moduleID){
        $module_changes['remove_modules'][] = $remove_moduleID;
    }
}

$module_changes['old_version'] = $old_version_name;
$module_changes['new_version'] = $new_version_name;

print_r($module_changes);
$modelFileName = 'CustomModel.php';
$createFileName = 'db_upgrade_'.$old_version_name.'_'.$new_version_name.'.gen';
$codeArray['/**custom**/'] = '$module_changes = unserialize(\''.escapeSerialize($module_changes) .'\')';
SaveGeneratedFile($modelFileName, $createFileName, $codeArray);
rename(GENERATED_PATH.'/'.$createFileName,S2A_FOLDER.'/install/'.$createFileName);


?>