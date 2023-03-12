<?php
    require 'vendor/autoload.php';

    $id = $_GET['id'];

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
        SELECT ?f ?id ?judul ?deskripsi ?genre1 ?genre2 ?genre3 ?mrating ?rating ?poster ?trailer ?link
        WHERE {
            ?f a film:movie;
                film:id ?id;
                rdfs:label ?judul;
                film:desc ?deskripsi;
                film:genreone ?genre1;
                film:genretwo ?genre2;
                film:genrethree ?genre3;
                film:movierating ?mrating;
                film:rating ?rating;
                film:poster ?poster;
                film:trailer ?trailer;
                foaf:homepage ?link.
                FILTER (?id = "'.$id.'")
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
    <link rel="stylesheet" href="css/desc.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/bea7ede4c1.js" crossorigin="anonymous"></script>
    <title>Movie</title>
</head>
<body>
    <!-- Begin navbar -->
    <?php include "navbar.php"; ?>
    <!-- End navbar -->
    
    <!-- Begin main content -->
    <div class="container-content">
        <div class="main-content">
            <?php
                foreach($result as $row){
                    $imdb = \EasyRdf\Graph::newAndLoad($row->link);

                    $link_dbp = $row->f;
                    $cover = $imdb->image;
            ?>
            <!-- Begin Uppersource -->
            <div class="movie-uppersource">
                <!-- Thumbnail image -->
                <div class="movie-thumbnail">
                    <img src="<?= $row->poster?>" alt="">
                </div>
                <!-- Thumbnail title -->
                <div class="movie-title">
                    <h1><?=$row->judul?> (<?=$row->mrating?>)</h1>
                </div>
            </div>
            <!-- End Uppersource -->

            <!-- Begin lowersource -->
            <div class="movie-lowersource">
                <!-- Begin cover -->
                <div class="movie-cover">
                    <img src="<?=$cover?>" alt="">
                </div>
                <!-- End cover -->
                
                <?php 
                    $sparql_query1 = '
                    SELECT ?aktor ?nama WHERE {
                        <'.$link_dbp.'> dbo:starring ?aktor.
                        ?aktor foaf:name ?nama.
                    } ORDER BY ASC(?nama)
                    ';

                    $sparql_query2 = '
                    SELECT ?director ?nama WHERE {
                        <'.$link_dbp.'> dbo:director ?director.
                        ?director foaf:name ?nama.
                    } 
                    ';

                    $sparql_query3 = '
                    SELECT ?produser ?nama WHERE {
                        <'.$link_dbp.'> dbo:producer ?produser.
                        ?produser foaf:name ?nama.
                    } ORDER BY ASC(?nama)                      
                    ';

                    $result1 = $dbpedia_endpoint->query($sparql_query1);
                    $result2 = $dbpedia_endpoint->query($sparql_query2);
                    $result3 = $dbpedia_endpoint->query($sparql_query3);
                ?>

                <!-- Begin desc -->
                <div class="movie-desc">
                    <p> <?=$row->deskripsi ?> </p>
                    <p> Genre : <?=$row->genre1?> , <?=$row->genre2?> , <?=$row->genre3?></p>
                    <p> Starring : 
                        <?php 
                            $x = count($result1);
                            foreach($result1 as $row1){
                                if($x != 1){ $x--;
                        ?>
                            <a href="actorprofile.php?p=<?=$row1->aktor?>"><?=$row1->nama?></a> , 
                        <?php }else{ ?>
                            <a href="actorprofile.php?p=<?=$row1->aktor?>"><?=$row1->nama?></a>
                        <?php }} ?>
                    </p>
                    <p> Directed By : 
                        <?php 
                            foreach($result2 as $row2){
                        ?>
                            <a href="directorprofile.php?p=<?=$row2->director ?>">
                                <?= $row2->nama?>
                            </a>
                        <?php } ?>
                    </p>
                    <p> Producer :
                        <?php 
                            $y = count($result3);
                            foreach($result3 as $row3){
                                if($y != 1){ $y--;
                        ?>
                            <a href="producerprofile.php?p=<?=$row3->produser ?>">
                                <?= $row3->nama?>
                            </a> ,
                        <?php }else{ ?>
                            <a href="producerprofile.php?p=<?=$row3->produser ?>">
                                <?= $row3->nama?>
                            </a>
                        <?php }} ?>
                    </p>
                    <iframe src="<?=$row->trailer?>" width="500" height="300" frameborder="0"></iframe>
                </div>
                <!-- End desc -->
                
                <!-- Begin rate -->
                <div class="movie-rate">
                    <div>
                        <span>
                            <i class="fas fa-star"></i>
                        </span>
                    </div>
                    <div>
                        <span>
                            <p><?=$row->rating?></p>
                        </span>
                    </div>
                </div>
                <!-- End rate -->
            </div>
            <!-- End Lowersource -->
            <?php } ?>
        </div>
    </div>
    <!-- End main content -->
    
    <script src="js/bootstrap.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
</body>
</html>