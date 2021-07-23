<?php
$currentDir = dirname(__FILE__);
$site_name = "hetj.fr";

$file_path = $currentDir."/../bk/wholeSiteBackup/";
if (!file_exists($file_path)) {
    mkdir($file_path, 0755, true);
}
$file_name = $site_name."_v".date("YmdHi").".zip";

$sh_str = "zip -r ".$file_path.$file_name." ".$currentDir."/*";

$sh_str_remove = "find ".$file_path."* -type f -mtime +7 -exec rm {} \;";
//$sh_str_remove = "find ".$file_path."* -type f -exec rm {} \;";
exec($sh_str_remove);
exec($sh_str);

?>