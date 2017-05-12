<?php
// charger la liste des graphies dans un tableau
$graphs = array();
$handle = fopen( dirname(__FILE__)."/author_graphs.csv", "r");
fgetcsv($handle, 0, "\n"); // passer la première ligne
while ( ($row = fgetcsv($handle, 0, "\t")) !== FALSE) { // lire toutes les autres lignes
  if ( count( $row ) < 2 ) continue; // ligne vide, incomplète ou non reconnue
  $graphs[$row[0]] = $row[1]; // remplir le tableau de graphies, avec pour clé la première cellule, et pour valeur la deuxième
}
// pour vérifier que c'est bien chargé
// print_r( $graphs );
// dossier où sont les fichiers XML, parent du parent de ce fichier
$dir = dirname(dirname(__FILE__))."/";
// boucler sur tous les fichiers XML, et les traiter
foreach ( glob( $dir."*.xml" ) as $file ) {
  echo "\n ———— ".basename($file)."\n";
  // charger le fichier comme un document xml
  $doc = new DOMDocument();
  $doc->load( $file );
  $nsuri = $doc->lookupNamespaceURI ( null ); // récupérer l’URI d'espace de noms par défaut, TEI
  $xpath = new DOMXpath($doc); // initialiser un processeur XPath
  $xpath->registerNamespace ( "tei", $nsuri ); // Déclarer le namespace avec un préfixe
  $nodelist = $xpath->query( "//tei:author" );
  foreach ( $nodelist as $node ) {
    if ( $node->hasAttribute("key") ) continue; // déjà renseigné, on passe
    $value = trim( $node->textContent);
    if ( !isset( $graphs[$value] ) ) { // graphie non répertoriée dans le tableau
      echo $value."\n"; // afficher la graphie pour plus tard
      continue;
    }
    $node->setAttribute( "key", $graphs[$value] ); // ajouter un attribut @key à l’élément <author>
  }
  $doc->save( $file ); // sauvegarder
}

?>
