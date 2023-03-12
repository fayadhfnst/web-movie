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

    $sparql_query = '
        SELECT ?f ?id ?judul ?deskripsi ?cover ?link
        WHERE {
            ?f a film:movie;
                film:id ?id;
                rdfs:label ?judul;
                film:desc ?deskripsi;
                film:poster ?cover;
                foaf:homepage ?link.
                BIND(RAND() as ?rand)
        } ORDER BY ?rand LIMIT 1
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
    <link rel="stylesheet" href="css/index.css">
    <title>Movie</title>
</head>
<body>
    <!-- Begin navbar -->
    <?php include "navbar.php";?>
    <!-- End navbar -->
    <div class="container-content">
        <!-- Begin popular movie -->
        <?php
            foreach ($result as $row){
                $imdb = \EasyRdf\Graph::newAndLoad($row->link);

                $poster = $imdb->image; 
        ?>
        <div class="popular-movie">
                <img src="<?= $row->cover?>" alt="">
            <div class="movie-info">
                <div class="movie-cover">
                    <a href="desc.php?id=<?=$row->id?>">
                        <img src="<?= $poster ?>">
                    </a>
                </div>
                <div class="movie-synopsis">
                    <h5 style="color: white"><?= $row->deskripsi?></h5>
                </div>
            </div>
        </div>
        <?php } ?>
        <!-- End popular movie -->
        <div class="current-movie">
            <div class="current-movie-info">
                <a href="">
                    <h3>Another movie is here...</h3>
                </a>
            </div>
            <?php 
                $sparql_query1 = '
                    SELECT ?f ?id ?judul ?deskripsi ?cover ?link
                    WHERE {
                        ?f a film:movie;
                            film:id ?id;
                            rdfs:label ?judul;
                            film:desc ?deskripsi;
                            film:poster ?cover;
                            foaf:homepage ?link.
                    } ORDER BY rand() LIMIT 12
                ';

                $result1 = $jena_endpoint->query($sparql_query1);
                // echo $result1;
            ?>
            <div class="current-movie-details">
                <!-- Begin movie list -->
                <?php
                    foreach($result1 as $row1){
                        $imdb = \EasyRdf\Graph::newAndLoad($row1->link);
                        $poster = $imdb->image;
                ?>
                <div class="movie-card">
                    <a href="desc.php?id=<?=$row1->id?>">
                        <img src="<?= $poster ?>" alt="">
                    </a>
                </div>

                <?php } ?>
                <!-- End movie list -->
            </div>
        </div>
    </div>

    <script src="js/bootstrap.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
</body>
</html>