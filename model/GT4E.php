<?php // Goto4ever.com
function GT4Elog($log) //créé un fichier logs dans le dossier root et y ajoute le contenu en parramètre.
{
  $file = "logs.log";
  $fileopen=(fopen("$file",'a'));
  fwrite($fileopen, $today = date("Y-m-d H:i:s")." -> ".$log."\r\n"); // date-heure + log
  fclose($fileopen);
}
?>
