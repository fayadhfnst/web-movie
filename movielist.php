<?php
    require 'vendor/autoload.php';

    \EasyRdf\RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('owl', 'http://www.w3.org/2002/07/owl#');
    \EasyRdf\RdfNamespace::set('film', 'https://example.org/schema/film');
    \EasyRdf\RdfNamespace::setDefault('og');

    $jena_endpoint = new \EasyRdf\Sparql\Client('http://localhost:3030/film/sparql');
    $dbpedia_endpoint = new \EasyRdf\Sparql\Client('https://dbpedia.org/sparql');

    $sparql_query = '
        SELECT ?f ?id ?judul ?deskripsi ?durasi ?tahun ?link
        WHERE {
            ?f a film:movie;
                film:id ?id;
                rdfs:label ?judul;
                film:desc ?deskripsi;
                film:duration ?durasi;
                film:year ?tahun;
                foaf:homepage ?link.
        } ORDER BY ASC(?judul)
    ';

    $result = $jena_endpoint->query($sparql_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="css/movielist.css">
    <title>Movie</title>
</head>
<body>
    <!-- Begin navbar -->
    <?php include "navbar.php";?>
    <!-- End navbar -->

    <!-- Begin main content -->
    <div class="container-content">
        <div class="main-content">
            <h1>Movie lists</h1>
            <?php 
                foreach($result as $row){
                    $imdb = \EasyRdf\Graph::newAndLoad($row->link);
                    $poster = $imdb->image;
                    $link_dbp = $row->f;
            ?>
            <div class="movie-info">
                <div class="movie-info-cover">
                    <!-- Cover image -->
                    <a href="desc.php?id=<?=$row->id?>">
                        <img src="<?=$poster?>" alt="">
                    </a>
                </div>
                <div class="movie-info-desc">
                    <div class="info-desc-title">
                        <!-- Title -->
                        <h3><?=$row->judul?></h3>
                    </div>
                    <div class="info-desc-synopsis">
                        <!-- Synopsis -->
                        <?=$row->deskripsi?>
                    </div>
                    <div class="info-desc-dirr">
                        <!-- Directed by, year -->
                        <?php
                            $sparql_query1 = '
                                SELECT ?director ?nama WHERE{
                                    <'.$link_dbp.'> dbo:director ?director.
                                    ?director foaf:name ?nama.
                                }
                            ';         
                            
                            $result1 = $dbpedia_endpoint->query($sparql_query1);
                        ?>
                        <div class="director">
                            <h6>
                                directed by:
                                <?php 
                                    foreach($result1 as $row1){
                                ?>
                                    <a href="directorprofile.php?p=<?=$row1->director?>">
                                        <?=$row1->nama ?>
                                    </a>
                                <?php } ?>
                            </h6>
                            <h6>
                                Year: <?=$row->tahun?>
                            </h6>
                            <h6>
                                Duration: <?=$row->durasi?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <!-- End main content -->

    <script src="js/bootstrap.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
</body>
</html>