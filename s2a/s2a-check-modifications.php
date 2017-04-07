<?php
/**
 * Utility to check whether any files have been modified locally.
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

$checksum_files = glob("active*_checksums.txt");
switch(count($checksum_files)){
case 0:
    die(gettext("No checksum file found")."\n");
    break;
case 1:
    $checksum_file = $checksum_files[0];
    break;
default:
    include './lib/includes/parse_util.php';
    print gettext("Several checksum files found:")."\n";
    print_r($checksum_files);
    $checksum_file_ix = textPrompt(gettext("Select a checksum file by typing the number associated with the correct file listed above:"));
    if(empty($checksum_files[$checksum_file_ix])){
        while(prompt(sprintf(gettext("'%s' is an invalid choice. Would you like to try again?"), $checksum_file_ix))){
            $checksum_file_ix = textPrompt(gettext("Select a checksum file by typing the number associated with the correct file listed above:"));
            if(!empty($checksum_files[$checksum_file_ix])){
                $checksum_file = $checksum_files[$checksum_file_ix];
                break 2;
            }
        }
    } else {
        $checksum_file = $checksum_files[$checksum_file_ix];
    }
    break;
}

print sprintf(gettext("Reading checksum file %s"), $checksum_file)."\n";
$checksums_content = file_get_contents($checksum_file);
$checksums = explode("\n", $checksums_content);
print sprintf(gettext("There are %s files to check"), count($checksums)) . "\n";

$start = false;
$modified_files = array();
$removed_files = array();
$new_files = array();
foreach($checksums as $checksum_ix => $checksum_line){
    //print "line: $checksum_line\n";
    if($checksum_ix == 2){ //file list starts on the third (index 2) line
        $start = true;
        if(false !== strpos($checksum_line, '0.8.1')){
            $long_filenames = true;
        } else {
            $long_filenames = false;
        }
    }
    if($start && !empty($checksum_line)) {
        list($file_name, $file_size, $file_checksum) = explode("\t", $checksum_line);
        $file_checksum = trim($file_checksum);
        //print "$file_name, $file_size, $file_checksum\n";
        if($long_filenames){
            $file_name = substr($file_name, strpos($file_name, '/s2a/')+5);
        }
        if(file_exists($file_name)){
            $current_checksum = md5_file($file_name);
            if($file_checksum != $current_checksum){
                $current_size = filesize($file_name);
                print "\n";
                print sprintf(gettext("Found a modification on line %s.\n"),$checksum_ix);
                print "File: $file_name\n (old checksum: '$file_checksum' current checksum: '$current_checksum')\n";
                $modified_files[] = $file_name;
            }
        } else {
            $removed_files[] = $file_name;
        }
    }
    //indicates reading progress by printing a dot for each 100 files checked.
    if(($checksum_ix % 100) == 0){
        print '.';
    }
}
print "\n";
if(count($modified_files) > 0){
    print gettext("Found the following modified files:")."\n";
    print_r($modified_files);
} else {
    print gettext("Found no modified files.")."\n";
}
if(count($removed_files) > 0){
    print gettext("The following files were removed:")."\n";
    print_r($removed_files);
} else {
    print gettext("No files were removed.")."\n";
}
print gettext("All done!")."\n";
?>