<?php
    require 'vendor/autoload.php';

    $title = $_GET['judul'];

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
        SELECT *
        WHERE {
            ?f a film:movie;
                rdfs:label ?judul;
                film:id ?id;
                film:desc ?deskripsi;
                film:rating ?rating;
                film:poster ?poster;
            FILTER REGEX(?judul , "^'.$title.'", "i")
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
    <link rel="stylesheet" href="css/searchedmovie.css">
    <title>Movie</title>
</head>
<body>
    
    <!-- Begin navbar -->
    <?php include "navbar.php";?>
    <!-- End navbar -->

    <!-- Begin main content -->
    <div class="container-content">
        <div class="main-content">
            <h1>You're looking for '<?=$title ?>'</h1>
            <div class="movie-list">
                <?php 
                    $x = count($result);

                    if($x == 0)
                        {print "<div class='result-empty'>Sorry, there is no result for '$title' </div>";
                    }else{
                        foreach($result as $row){
                            $link_dbp = $row->f;
                ?>
                <div class="movie-card">
                    <!-- Movie rate and image -->
                    <div class="upper-card">
                        <span><?= $row->rating ?></span>
                        <a href="desc.php?id=<?=$row->id?>">
                            <img src="<?= $row->poster?>" alt="">
                        </a>
                    </div>
                <!-- Movie description -->
                    <div class="lower-card">
                        <!-- Movie title -->
                        <h5><?=$row->judul?></h5>
                        <!-- Movie synopsis -->
                        <p><?=$row->deskripsi?></p>
                        <!-- Movie director -->
                        <?php 
                            $sparql_query1 = '
                            SELECT ?director ?nama WHERE{
                                <'.$link_dbp.'> dbo:director ?director.
                                ?director foaf:name ?nama.
                            }
                            ';

                            $result1 = $dbpedia_endpoint->query($sparql_query1);
                        ?>
                        <div class="director-card">
                            <p>directed by: </p>
                            <?php foreach($result1 as $row1){ ?>
                            <a href="directorprofile.php?p=<?=$row1->director?>">
                                <p><?=$row1->nama?></p>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php }} ?>
            </div>
        </div>
    </div>
    <!-- End main content -->
    <script src="js/bootstrap.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
</body>
</html>