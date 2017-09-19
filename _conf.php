<?php
return array(
  "srcdir" => dirname( __FILE__ ),
  "destdir" => ".",
  "cmdup" => "git pull",
  "pass" => "VéroniqueGély",
  // "srclist" => "obvil.csv",
  "srcglob" => array( "xml/*_*.xml" ), // pour mise à jour de la polémique
  "sqlite" => "mythographie.sqlite",
  "formats" => "article, toc, epub, kindle",
);
?>
