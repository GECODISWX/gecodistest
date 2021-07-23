<?php

function calcWhitePercentage($file){
  $file_path = 'tmp.jpg';
  $file = trim($file);
  file_put_contents($file_path, file_get_contents($file));
  $img_size = getimagesize($file_path);
  $im =imagecreatefromjpeg($file_path);
  $white_count=0;
  $count=0;
  for ($y=0; $y <$img_size[1] ; $y++) {
    for ($x=0; $x < $img_size[0]; $x++) {
    if ($y==0 || $y == ($img_size[1]-1) || ($x==0) || ($x == ($img_size[0]-1))) {
        $count++;
        $rgb = imagecolorat($im,$x,$y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r == 255 && $g == 255 && $b == 255) {
          $white_count++;
        }

      }
    }
  }
  return round($white_count/$count*100);
}
$file = 'ambiance.csv';
$str = file_get_contents($file);

$lines = explode(PHP_EOL,$str);
$lines_r_str ='';

foreach ($lines as $key_line => $line) {

  if ($key_line<100 || $key_line>999) {
    continue;
  }

  $cols = explode(',',$line);
  $line_r_str = $cols[0].';'.$cols[1].';';
  for ($i=2; $i <6 ; $i++) {
    if (trim($cols[$i]) != '') {
      $white_perc = calcWhitePercentage($cols[$i]);
      $line_r_str .= $cols[$i].';'.$white_perc.';';
    }
    else {
      $line_r_str .=';'.';';
    }
  }
  $lines_r_str .= $line_r_str.PHP_EOL.'<br>';



}
echo $lines_r_str;



 ?>
