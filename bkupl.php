<?php
$ftp_server = "54.37.85.165";
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$login = ftp_login($ftp_conn, 'pho187', 'X~23gw3d');

$dbs = glob('../bk/dbbk/*.gz');
foreach($dbs as $key => $db){
	//var_dump(basename($db));
	echo ftp_put($ftp_conn, basename($db), $db).'_'.basename($db);
}
if(!isset($_GET['db_only']))
{
	$dbs = glob('../bk/wholeSiteBackup/*.zip');
	foreach($dbs as $key => $db){
	//var_dump(basename($db));
	echo ftp_put($ftp_conn, basename($db), $db).'_'.basename($db);
}
}


ftp_close($ftp_conn);