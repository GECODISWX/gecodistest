<?php
$url = "https://www.googleapis.com/calendar/v3/calendars/gecodiswx@gmail.com/events?key=AIzaSyCKh_waBUMtNQQnKd-T3OXOmMyHUmkCI98";
$timeMin = date("Y-m-d")."T00:00:00Z";
$d = date();
$timeMax = date('Y-m-d', strtotime( $d . " +1 days"))."T00:00:00Z";
$url .= "&timeMin=$timeMin&timeMax=$timeMax";
$tmp = file_get_contents($url);
$json = json_decode($tmp);
//var_dump($url);

$subject = "Log de travail WEI Xiao ".date("Y-m-d");
$msg = "";
$itmes=[];
foreach ($json->items as $key => $l) {
  $t1 = strtotime($l->start->dateTime);
  $int = $t1 - strtotime($timeMin);
  if ($int>0) {
    $items[$l->start->dateTime]=$l;
  }

}
ksort($items);

foreach ($items as $key => $l) {
  //var_dump($l);
  $t1 =  explode("T",$l->start->dateTime);
  $t1 = explode("+",$t1[1]);
  $t2 =  explode("T",$l->end->dateTime);
  $t2 = explode("+",$t2[1]);
  $msg .= $t1[0]." - ".$t2[0]."\r\n";
  $msg .= $l->summary."\r\n";
  if (isset($l->description)){
    $msg .= $l->description."\r\n";
  }
  $msg .="\r\n";
}

//mail("gecodiswx@gmail.com",$subject,$msg);
