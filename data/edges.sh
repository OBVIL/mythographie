DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )"/../ && pwd )"
echo $DIR
echo "Source	Target	lang	xml	role	@resp" >  $DIR/data/author_edges.tsv
xsltproc $DIR/data/edges.xsl $DIR/*.xml >> $DIR/data/author_edges.tsv
