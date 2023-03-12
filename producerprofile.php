<?php
    require 'vendor/autoload.php';

    $p = $_GET['p'];

    \EasyRdf\RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('owl', 'http://www.w3.org/2002/07/owl#');
    \EasyRdf\RdfNamespace::set('film', 'https://example.org/schema/film');
    \EasyRdf\RdfNamespace::setDefault('og');

    $dbpedia_endpoint = new \EasyRdf\Sparql\Client('https://dbpedia.org/sparql');

    $sparql_query = '
    SELECT DISTINCT * WHERE{
        <'.$p.'> dbo:abstract ?abstract;
                foaf:name ?nama;
                dbo:thumbnail ?foto.
        FILTER (lang(?abstract) = "en" && lang(?nama) = "en")
    }
    ';

    $result = $dbpedia_endpoint->query($sparql_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="css/directorprofile.css">
    <title>Movie</title>
</head>
<body>
    <!-- Begin navbar -->
    <?php include "navbar.php";?>
    <!-- End navbar -->

    <!-- Begin main content -->
    <div class="container-content">
        <h2>Producer personal file</h2>
        <div class="main-content">
            <!-- Director image -->
            <?php 
                foreach($result as $row){
            ?>
            <div class="director-image">
                <img src="<?=$row->foto?>" alt="">
            </div>
            <div class="director-profiles">
                <div class="profile-description">
                    <!-- Director desription -->
                    <h3>
                        <?=$row->nama ?>
                    </h3>
                    <p><?=$row->abstract?></p>
                </div>
                <div class="profile-personal">
                    <!-- Director data -->
                    <h3>Personal data</h3>
                    <ul>
                    <li>Date of Birth : 
                        <?php 
                                $sparql_query4 = '
                                    SELECT * WHERE{
                                        <'.$p.'> dbo:birthDate ?tgl_lahir.
                                    }
                                ';

                                $result4 = $dbpedia_endpoint->query($sparql_query4);
                                $x = count($result4);
                                if($x != 0){
                                    foreach($result4 as $row4){
                            ?>    
                                    <?=$row4->tgl_lahir ?></li>
                                <?php }}else{ ?>
                                    No data </li>
                            <?php } ?>
                        <li>Birth Place : <?php 
                                $sparql_query3 = '
                                    SELECT * WHERE{
                                        <'.$p.'> dbo:birthPlace ?birth.
                                                 ?birth foaf:name ?n_birth.
                                        
                                    }
                                ';

                                $result3 = $dbpedia_endpoint->query($sparql_query3);
                                $x = count($result3);
                                if($x != 0){
                                    foreach($result3 as $row3){
                                        if($x != 1){
                            ?>    
                                        <?=$row3->n_birth ?>,
                                <?php }else{ ?>
                                    <?=$row3->n_birth ?></li>
                                <?php }
                                    $x--;}}else{ ?>
                                    No data </li>
                            <?php } ?>
                    </ul>
                </div>
                <div class="profile-movie">
                    <?php 
                        $sparql_query1 = '
                        SELECT * WHERE{
                            ?film dbo:producer <'.$p.'>.
                            ?film foaf:name ?nama_film.
                        } ORDER BY ASC(?nama_film)
                        ';

                        $result1 = $dbpedia_endpoint->query($sparql_query1);
                    ?>
                    <!-- Director movies -->
                    <h3>Produce Movie</h3>
                    <ul>
                        <?php
                            foreach($result1 as $row1){
                        ?>
                        <li>
                            <?=$row1->nama_film?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php 
                    $sparql_query2 = '
                    SELECT DISTINCT * WHERE{
                        <'.$p.'> dbo:birthPlace ?b_place.
                        OPTIONAL {?b_place geo:lat ?lat;
                                            geo:long ?long.
                        }
                    } LIMIT 1
                    ';

                    $result2 = $dbpedia_endpoint->query($sparql_query2);
                    foreach($result2 as $row2){
                ?>

                <div class="open-street-map">
                    <h3>Map</h3>
                    <div id="map" style="width:680px; height: 300px;"></div>
                        <script>
                        var map = L.map('map').setView([<?=$row2->lat?>, <?=$row2->long?>], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        L.marker([<?=$row2->lat?>, <?=$row2->long?>]).addTo(map)
                            .bindPopup('<?=$row->nama ?> Birth Place')
                            .openPopup();             
                        </script>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <!-- End main content -->

    <script src="js/bootstrap.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
</body>
</html>