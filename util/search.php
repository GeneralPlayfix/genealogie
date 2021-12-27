<?php
require_once "../component/bdd.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $search = "%".strtoupper($_POST['search'])."%";

    $sql = $dbh->prepare("SELECT * FROM villes WHERE UPPER(name) LIKE ? OR UPPER(slug) LIKE ? LIMIT 5");
    $sql->execute(array($search, $search));

        $results = $sql->fetchAll();
    if(count($results) > 0){
        foreach($results as $city){
            echo '<p>'.$city['name']." ".$city['zip_code'].'</p>';
        }
    }else{
        echo '<p>La ville est introuvable</p>';
    }
}
