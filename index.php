<?php
ini_set('display_errors', '1');
error_reporting(-1);
$conf = include( dirname(__FILE__)."/conf.php" );
include( dirname(dirname(__FILE__))."/Teinte/Web.php" );
include( dirname(dirname(__FILE__))."/Teinte/Base.php" );
$base = new Teinte_Base( $conf['sqlite'] );
$path = Teinte_Web::pathinfo(); // document demandé
$basehref = Teinte_Web::basehref(); //
$teinte = $basehref."../Teinte/";

// chercher le doc dans la base
$docid = current( explode( '/', $path ) );
$query = $base->pdo->prepare("SELECT * FROM doc WHERE code = ?; ");
$query->execute( array( $docid ) );
$doc = $query->fetch();

$q = null;
if ( isset($_REQUEST['q']) ) $q=$_REQUEST['q'];


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
if ( !$path ) echo '<a href="//132.227.201.10:8086/projets/autorites-en-partage">Projet : Autorités en partage</a>';
else echo '<a href="'.$basehref.'">Corpus : Mythographie</a>';
          ?>
        </h1>
        <a class="logo" href="http://132.227.201.10:8086/"><img class="logo" src="<?php echo $basehref; ?>../theme/img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <aside id="aside">
          <?php
// document
if ( $doc ) {
  // formats alternatifs à télécharger
  echo '
<nav id="download"><small>Télécharger :</small>
  <a target="_blank" href="http://obvil.github.io/mythographie/xml/'.$doc['code'].'.xml" title="Source XML/TEI">tei</a>,
  <a type="application/epub+zip" href="epub/'.$doc['code'].'.epub" title="Livre électronique">epub</a>,
  <a type="application/x-mobipocket-ebook" href="kindle/'.$doc['code'].'.mobi" title="Mobi, format propriétaire Amazon">kindle</a>,
  <a target="_blank" href="markdown/'.$doc['code'].'.md" title="Markdown">texte brut</a>,
  <a target="_blank" href="iramuteq/'.$doc['code'].'.txt">iramuteq</a>,
  <a target="_blank" href="html/'.$doc['code'].'.html" title="Page complète sans interface">html</a>.
</nav>';


  echo "\n".'<header>';
  if ($doc['byline']) echo "\n".'<div class="byline">'.$doc['byline'] .'</div>';
  echo "\n".'<a class="title" href="' . $basehref . $doc['code'] . '/">';
  if ($doc['date']) echo $doc['date'].', ';
  echo $doc['title'].'</a>';
  echo "\n".'</header>';

  echo '
<form action="#mark1">
  <a title="Retour aux résultats" href="'.$basehref.'?'.$_COOKIE['lastsearch'].'"><img src="'.$basehref.'../theme/img/fleche-retour-corpus.png" alt="←"/></a>
  <input name="q" value="'.str_replace( '"', '&quot;', $base->p['q'] ).'"/><button type="submit">🔎</button>
</form>
';
  // table des matières
  if ( file_exists( $f="toc/".$doc['code']."_toc.html" ) ) readfile( $f );
}
// accueil, formulaire de recherche général
else {

  echo '
  <nav id="download"><small>Télécharger :</small>
    <a target="_blank" href="https://github.com/OBVIL/mythographie" title="Source XML/TEI">tei</a>,
    <a target="_blank" href="epub/" title="Livre électronique">epub</a>,
    <a target="_blank" href="kindle/" title="Mobi, format propriétaire Amazon">kindle</a>,
    <a target="_blank" href="markdown/" title="Markdown">texte brut</a>,
    <a target="_blank" href="iramuteq/">iramuteq</a>,
    <a target="_blank" href="html/">html</a>.
  </nav>';
  echo '<p> </p>';

  echo'
<form action="">
  <input style="width: 100%;" name="q" class="text" placeholder="Rechercher de mots" value="'.str_replace( '"', '&quot;', $base->p['q'] ).'"/>
  <div><label>De <input placeholder="année" name="start" class="year" value="'.$base->p['start'].'"/></label> <label>à <input class="year" placeholder="année" name="end" value="'.$base->p['end'].'"/></label></div>
  <button type="reset" onclick="Form.reset(this.form); this.form.submit(); ">Effacer</button>
  <button type="submit" style="float: right; ">Rechercher</button>
</form>
  ';
}
          ?>
        </aside>
        <div id="main">
          <div id="article">
            <?php
if ( $doc ) {
  $html = file_get_contents( "article/".$doc['code']."_art.html" );
  if ( $q ) echo $base->hilite( $doc['id'], $q, $html );
  else echo $html;
}
else if ( $base->search ) {
  $base->biblio( array( "no", "date", "author", "title", "occs" ), "SEARCH" );
}
// pas de livre demandé, montrer un rapport général
else {
  echo '<h1 style="padding-top:0">Mythographie 1800-1950</h1>';
  echo '<img src="images/mitologia.png"/>';
  echo '
<p>Le projet « Mythographie 1800-1950 » porte sur le corpus des dictionnaires, manuels et recueils de récits mythologiques dont la publication prend un essor considérable au XIXe siècle.</p>
<p>Une bibliographie des ouvrages publiés dans les langues française, anglaise, italienne, allemande, espagnole et grecque moderne est en cours d’élaboration. Les livres recensés font l’objet d’un travail collectif d’édition et d’interprétation dans le cadre d’une réflexion sur l’autorité littéraire.</p>
<p>L’équipe, dirigée par Véronique Gély avec le soutien de Diego Pellizzari, réunit pour ces travaux de bibliographie et d’édition Cécile Chapon, Elodie Coutier, Cyril Gendry, Marie-Pierre Harder, Georgios Meli, François Vassogne, des étudiants de Master et les ingénieurs de l’Obvil.</p>
';
  $base->biblio( array("no", "creator", "date", "title") );
}
            ?>
            <a id="gotop" href="#top">▲</a>
          </div>
        </div>
    </div>
    <script type="text/javascript" src="<?= $teinte ?>Tree.js">//</script>
    <script type="text/javascript" src="<?= $teinte ?>Sortable.js">//</script>
    <script type="text/javascript" src="<?= $teinte ?>Teinte.js">//</script>
  </body>
</html>
