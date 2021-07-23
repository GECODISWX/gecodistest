<?php
// $to = "xiao.wei@habitatetjardin.fr";
// $subject = "[DDO #2021042413] # - VM17/19 ne répond plus - Nouveau suivi";
//
// $message = "
// <span style='font-size:10pt;''>=-=-=-= Pour répondre par courriel, écrivez au dessus de cette ligne =-=-=-=<br>
//
// <br>
//
// Bonjour,<br>
//
// <br>
//
// De nouvelles informations ont été apportées au ticket 2021040604 :<br>
//
// <br>
//
// Bien cordialement,<br>
//
// <br>
//
// Le support DDO Organisation<br>
//
// <br>
//
// Statut : En cours (Attribué)<br>
//
// <br>
//
// +--------------------------------------------------------------------------------------------+<br>
//
// <br>
//
// Historique :<br>
//
// <br>
//
// 06-04-2021 13:53 - Laurent Lefevre<br>
//
// <br>
//
// Bonjour Xiao<br>
//
// <br>
//
// A priori le disque de 20Go n'était pas intégré dans l'espace total , je viens<br>
//
// de le configurer et la connexion doit maintenant être possible<br>
//
// <br>
//
// Bonne journée<br>
//
// <br>
//
// Cdlt<br>
//
// ---<br>
//
// Laurent Lefevre<br>
//
// DDO Organisation<br>
//
// Toulouse/Minimes 05 34 60 49 00<br>
//
// Toulouse/Croix de Pierre 05 61 75 41 68<br>
//
// </span>
// ";
//
// // Always set content-type when sending HTML email
// $headers = "MIME-Version: 1.0" . "\r\n";
// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
// $headers = "From: support@ddo.net" . "\r\n";
//
// mail($to,$subject,$message,$headers);
//


$to = "xiao.wei@habitatetjardin.fr";
$subject = "[DDO #2021042413] # - VM17/19 ne répond plus - Nouveau suivi";
$txt = "Hello world!";
$headers = "From: xiao.wei@habitatetjardin.fr" . "\r\n";

mail($to,$subject,$txt,$headers);
?>
