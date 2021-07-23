<?php

 $login = 'GECODIS'; // à remplacer par l'identifiant de l'accès Mon espace
 $secretKey = '5c857f25d8ee473f88dec71ed3505618'; // à remplacer par l'api key générée pour utiliser la fonction zoom

 $uri = 'https://espace-client.geodis.com/services/';
 $service = 'api/zoomclient/recherche-envois';
 //$service = 'api/zoomclient/suivi-envois';
 $lang = 'fr';
$date_depart_debut = date("Y-m-d", strtotime("-3 day"));
$date_depart_fin = date("Y-m-d");
 $body = array(
   'dateDepart' => '',
   'dateDepartDebut' => $date_depart_debut,
   'dateDepartFin' => '',
   'noRecepisse' => '',
     'reference1' => '',
     'noSuivi' => '',
     'cabColis' => '',
     'codeSa' => '',
     'codeClient' => '',
     'codeProduit' => '',
     'typePrestation' => '',
     'dateLivraison' => '',
     'refDest' => '',
     'nomDest' => '',
     'codePostalDest' => '',
     'natureMarchandise' => '',
 );
 $inlineBody = json_encode($body);
  $timestamp = (time() * 1000);
  $message = $secretKey.';'.$login.';'.$timestamp.';'.$lang.';'.$service.';'.$inlineBody;
  $hash = hash('sha256', $message);
  $serviceRequestHeader = $login.';'.$timestamp.';'.$lang.';'.$hash;
  $headers = array(
      'X-GEODIS-Service: '.$serviceRequestHeader,
      'Content-Type: application/json; charset=utf-8',
      'Content-Length: '.strlen($inlineBody),
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $uri.$service);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $inlineBody);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 5000);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);

  $rawResult = curl_exec($ch);
  if (curl_error($ch)) {
      $error_msg = curl_error($ch);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_errno= curl_errno($ch);
      echo $error_msg."\n" ;
      echo $http_status."\n";
      echo $curl_errno."\n";

      throw new Exception($error_msg);
  }

  $results = json_decode($rawResult, true);

$out = '';
if (count($results['contenu'])>0) {
  foreach ($results['contenu'] as $key => $line) {
    $t = $line['codeProduit'].';';
	$t .= $line['dateDepartFrs'].';';
	$t .= $line['noRecepisse'].';';
	$t .= $line['reference1'].';';
	$t .= $line['reference2'].';';
	$t .= $line['urlSuiviDestinataire'].';';

    $out.= $t.PHP_EOL;
  }
}


echo $out;
