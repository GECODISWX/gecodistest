<?php
$d = array (
    'database_host' => '127.0.0.1',
    'database_port' => '',
    'database_name' => 'hetj_prod',
    'database_user' => 'hetj_prod',
    'database_password' => 'S9$pr1r0'
  );

$currentDir = dirname(__FILE__);

$file_path = $currentDir."/../bk/dbbk/";
if (!file_exists($file_path)) {
    mkdir($file_path, 0755, true);
}

$file_name = "db_v".date("YmdHi").".gz";


//$sh_str = "/usr/bin/mysqldump --opt -u ".$d['database_user']." -p".$d['database_password']." ".$d['database_name']." | gzip > ".$file_path.$file_name;
$sh_str = "mysqldump --defaults-file=.my.cnf --opt -u ".$d['database_user']." ".$d['database_name']." | gzip > ".$file_path.$file_name;

$sh_str_remove = "find ".$file_path."* -type f -mtime +14 -exec rm {} \;";

exec($sh_str_remove);
echo exec($sh_str);

?>