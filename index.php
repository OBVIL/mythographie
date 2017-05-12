<?php
// page demandée ?
// si rien, liste des livres
// si recherche, concordance
// si livre, vérifier qu'il existe
// si recherche concordance locale
// si pas de chapitre, donner page accueil du livre

ini_set('display_errors', '1');
error_reporting(-1);
$conf = include( dirname(__FILE__)."/conf.php" );
include( dirname(dirname(__FILE__))."/Htocc/Web.php" );
include( dirname(dirname(__FILE__))."/Htocc/Sqlite.php" );
$base = new Htocc_Sqlite( $conf['sqlite'] );
$path = Htocc_Web::pathinfo(); // document demandé
$basehref = Htocc_Web::basehref(); //
$teinte = $basehref."../Teinte/";

// chercher le doc dans la base
$docid = current( explode( '/', $path ) );
$q = $base->pdo->prepare("SELECT * FROM doc WHERE code = ?; ");
$q->execute( array( $docid ) );
$doc = $q->fetch();

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?php
if( $doc ) echo $doc['title'].' — ';
echo 'Mythographie, OBVIL';
    ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $teinte ?>tei2html.css" />
    <link rel="stylesheet" type="text/css" href="<?= $basehref ?>../theme/obvil.css"/>
  </head>
  <body>
    <div id="center">
      <header id="header">
        <h1>
          <?php
if ( !$path ) echo '<a href="//obvil.paris-sorbonne.fr/projets/autorites-en-partage">Projet : Autorités en partage</a>';
else echo '<a href="'.$basehref.'">Corpus : Mythographie</a>';
          ?>
        </h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/"><img class="logo" src="<?php echo $basehref; ?>../theme/img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <aside id="aside">
          <?php
// document
if ( $doc ) {
  // TODO, download
  // auteur, titre, date
  echo "\n".'<header>';
  if ($doc['byline']) echo "\n".'<div class="byline">'.$doc['byline'] .'</div>';
  echo "\n".'<a class="title" href="' . $basehref . $doc['code'] . '/">';
  if ($doc['date']) echo $doc['date'].', ';
  echo $doc['title'].'</a>';
  echo "\n".'</header>';
  // table des matières
  readfile("toc/".$doc['code'].".html");
}
// accueil ? formulaire de recherche général
else {
  /*
  echo'
    <form action="">
      <input name="q" class="text" placeholder="Rechercher" value="'.str_replace('"', '&quot;', $pot->q).'"/>
      <div><label>De <input placeholder="année" name="start" class="year" value="'.$pot->start.'"/></label> <label>à <input class="year" placeholder="année" name="end" value="'. $pot->end .'"/></label></div>
      '.$pot->bylist().'
      <button type="reset" onclick="return Form.reset(this.form)">Effacer</button>
      <button type="submit">Rechercher</button>
    </form>
  ';
  */
}
          ?>
        </aside>
        <div id="main">
          <div id="article">
            <?php
if ( $doc ) {
  readfile("article/".$doc['code'].".html");
}
// pas de livre demandé, montrer un rapport général
else {
  echo '<h1 style="padding-top:0">Mythographie 1800-1950</h1>';
  echo '<img src="accueil/mitologia.png"/>';
  echo '
<p>Le projet « Mythographie 1800-1950 » porte sur le corpus des dictionnaires, manuels et recueils de récits mythologiques dont la publication prend un essor considérable au XIXe siècle.</p>
<p>Une bibliographie des ouvrages publiés dans les langues française, anglaise, italienne, allemande, espagnole et grecque moderne est en cours d’élaboration. Les livres recensés font l’objet d’un travail collectif d’édition et d’interprétation dans le cadre d’une réflexion sur l’autorité littéraire.</p>
<p>L’équipe, dirigée par Véronique Gély avec le soutien de Diego Pellizzari, réunit pour ces travaux de bibliographie et d’édition Cécile Chapon, Elodie Coutier, Cyril Gendry, Marie-Pierre Harder, Georgios Meli, François Vassogne, des étudiants de Master et les ingénieurs de l’Obvil.</p>
';
  // catalogue
  $cols=array("no", "creator", "date", "title");
  $labels = array(
    "no"=>"N°",
    "publisher" => "Éditeur",
    "creator" => "Auteur",
    "date" => "Date",
    "title" => "Titre",
    "downloads" => "Téléchargements",
    "relation" => "Téléchargements",
  );
  echo '<table class="sortable">'."\n  <tr>\n";
  foreach ($cols as $code) {
    echo '    <th>'.$labels[$code]."</th>\n";
  }
  echo "  </tr>\n";
  $i = 1;
  foreach ($base->pdo->query("SELECT * FROM doc ORDER BY code") as $doc ) {
    echo "  <tr>\n";
    foreach ($cols as $code) {
      if (!isset($labels[$code])) continue;
      echo "    <td>";
      if ("no" == $code) {
        echo $i;
      }
      else if( "creator" == $code || "author" == $code || "byline" == $code ) {
        echo $doc['byline'];
      }
      else if( "date" == $code || "year" == $code ) {
        echo $doc['date'];
      }
      else if( "title" == $code ) {
        echo '<a href="'.$doc['code'].'">'.$doc['title']."</a>";
      }
      echo "</td>\n";
    }
    echo "  </tr>\n";
    $i++;
  }
  echo "\n</table>\n";
  /*
  // nombre de résultats
  echo $pot->report();
  // présentation chronologique des résultats
  echo $pot->chrono();
  // présentation bibliographique des résultats
  echo $pot->biblio(array('date', 'title', 'occs'));
  // concordance s’il y a recherche plein texte
  echo $pot->concByBook();
  */
}
            ?>
          </div>
        </div>
      </div></div>
      <?php
// footer
      ?>
    </div>
    <script type="text/javascript" src="<?= $teinte ?>Tree.js">//</script>
    <script type="text/javascript" src="<?= $teinte ?>Sortable.js">//</script>
  </body>
</html>
