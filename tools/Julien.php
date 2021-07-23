<?php


function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

function getErrors1($file){
  $pathinfo = pathinfo($file);
  $c = file_get_contents($file);
  $lines = explode(PHP_EOL,$c);
  $error_col_n = 0;
  $sku_col_n = 0;
  $r =[];
  foreach ($lines as $key => $l) {
    // if ($key!=3) {
    //   continue;
    // }
    $tmps = explode(";",$l);
    if ($key==0) {
      foreach ($tmps as $key2 => $tmp) {
        $tmp = trim($tmp,'"');
        if ($tmp == 'errors') {
          $error_col_n = $key2;
        }
        if ($tmp == 'shopSku' || $tmp == 'shop_sku') {
          $sku_col_n = $key2;
        }
      }
    }
    else {
      $error = trim($tmps[$error_col_n],'"');
      if (isset( $tmps[$error_col_n+1])) {
          $error .= ' '.  $error = trim($tmps[$error_col_n+1],'"');
      }
      $r[] = ['file'=>getMPFolderName($file),'type'=>'"produit-integration-product-import-errors"','sku'=>$tmps[$sku_col_n],'error'=>'"'.$error.'"'];
    }
  }
  return $r;

}

function getMPFolderName($file){
  $tmp = explode("tools/j/",$file);
  $tmp2 = explode(basename($file),$tmp[1]);
  return trim($tmp2[0],"/");
}

function displayAsCsv($r){
  foreach ($r as $key => $l) {
    foreach ($l as $key => $c) {
      echo ''.$c.';';
    }
    echo "<br>";
  }
}

function run(){
  $files = getDirContents("./j/");
  $allowed_ext = ['csv','xlsx'];
  $r = [];
  foreach ($files as $key => $file) {
    $pathinfo = pathinfo($file);
    if (strpos($file,"0-Synthese")!==false || strpos($file,"__MACOSX")!==false) {
      continue;
    }
    if (!in_array($pathinfo['extension'],$allowed_ext)) {
      continue;
    }
    if ($file != "/var/www/vhosts/habitat-et-jardin.fr/httpdocs/tools/j/Conforama/2021-06-21-mirakl-produit-integration-product-import-errors-file-20210624130018 (1).csv") {
      continue;
    }
    if (strpos($file,"produit-integration-product-import-errors")) {
      $r1 = getErrors1($file);
      $r = array_merge($r,$r1);
    }

  }
  displayAsCsv($r);

}


run();
 ?>
