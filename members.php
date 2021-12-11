<?php
$title = "Tous les membres";
require_once "./component/header.php";
require_once "./component/bdd.php";
require_once "./functions/sql.php";
$unsortedMembers = getAllMembers($dbh);
$allGenerationNumbers = array();
foreach ($unsortedMembers as $members) {
    if (empty($allGenerationNumbers)) {
        array_push($allGenerationNumbers, $members["generationnumber"]);
    } else {
        if (in_array($members['generationnumber'], $allGenerationNumbers)) {
        } else {
            array_push($allGenerationNumbers, $members['generationnumber']);
        }
    }
}
//Organiser dans un ordre croissant un tableau à un niveau
asort($allGenerationNumbers, SORT_REGULAR );
?>
<?php
if (isset($_POST['oui'])) {
    $idForDelete = $_POST['idForDelete'];
    deleteMember($dbh, $idForDelete);
    $msg = "Le membre à bien été supprimé";
    // header("Refresh:0");
    if (isset($msg) and !empty($msg)) {
?>
        <script>
            function count(n, el) {
                n = (typeof + n == "number" && n > -1) ? parseInt(n) : 60;
                parentScript = [].slice.call(document.getElementsByTagName('script')).pop().parentNode;
                el = (typeof el == "object") ? el : ((typeof parentScript != undefined) ? parentScript : {});
                el.innerText = n--;
                if (n > -1) setTimeout(function() {
                    count(n, el);
                }, 1000);
            }
        </script>
        <div class="col-md-12">
            <p class="success"> <?php echo $msg ?>... Vous allez être redirigé dans <b>
                    <script>
                        count(3)
                    </script>
                </b> secondes
            </p>

        </div>
<?php
        header('Refresh: 3; URL=members.php');
    }
}
$allMembers = array();

    $firstSort = array();
    $secondSort = array();
    $thirdSort = array();
    $fourthSort = array();
    $fifthSort = array();
    $sixthSort = array();
    // if (isset($_GET['search']) AND !empty($_GET['search']) || isset($_GET['genre']) AND !empty($_GET['genre']) || isset($_GET['birthDate']) AND !empty($_GET['birthDate']) || isset($_GET['weddingDate']) AND !empty($_GET['weddingDate']) || isset($_GET['deathDate']) AND !empty($_GET['deathDate']) || isset($_GET['generationNumber']) AND !empty($_GET['generationNumber']) || isset($_GET['sorted']) AND !empty($_GET['sorted'])) {
    //     echo "hey";
        if (!empty($_GET['search'])) {
            $finalSearch = array();
            $tempSearch = htmlspecialchars(trim($_GET['search']));
            $searchWords = explode(" ", $tempSearch);
            foreach ($searchWords as $tempWords) {
                $words = trim($tempWords);
                $search = "%" . $words . "%";
                $SearchArrayResult = getInformationsByWords($dbh, $search);
                foreach ($SearchArrayResult as $result) {
                    array_push($finalSearch, $result["id"]);
                }
            }
            $countWords = count($searchWords);
            $countId = array_count_values($finalSearch);
            foreach ($unsortedMembers as $member) {
                $id = $member['id'];
                if (in_array($id, $finalSearch)) {
                    if ($countId[$id] === $countWords) {
                        array_push($firstSort, $member);
                    }
                }
            }
        } else {
            $firstSort = $unsortedMembers;
        }
        // var_dump($firstSort);
        //deuxième tri
        if (!empty($_GET['genre'])) {
            $genre = htmlspecialchars($_GET['genre']);
            foreach($firstSort as $member){
                if($member['gender'] == $genre){
                    array_push($secondSort, $member);
                }
            }

        }else{
            $secondSort = $firstSort;
        }
        //troisième tri
        if(!empty($_GET['birthDate'])){
            $birthDate = htmlspecialchars($_GET['birthDate']);
            foreach($secondSort as $member){
                if (strpos($member['birthdate'], $birthDate) !== FALSE) {
                    array_push($thirdSort, $member);
                }
            }
        }else{
            $thirdSort = $secondSort;
        }
        //quatrième tri
        if(!empty($_GET['weddingDate'])){
            $weddingDate = htmlspecialchars($_GET['weddingDate']);
            foreach($thirdSort as $member){
                if (strpos($member['weddingdate'], $weddingDate) !== FALSE) {
                    array_push($fourthSort, $member);
                }
            }
        }else{
            $fourthSort = $thirdSort;
        }
        //cinquième tri
        if(!empty($_GET['deathDate'])){
            $deathDate = htmlspecialchars($_GET['deathDate']);
            foreach($fourthSort as $member){
                if (strpos($member['deathdate'], $deathDate) !== FALSE) {
                    array_push($fifthSort, $member);
                }
            }
        }else{
            $fifthSort = $fourthSort;
        }
        //sixième tri
        if(!empty($_GET['generationNumber'])){
            $generationNumber2 = intval($_GET['generationNumber']);
            foreach($firstSort as $member){
                if($member['generationnumber'] == $generationNumber2){
                    array_push($sixthSort, $member);
                }
            } 
        }else{
            $sixthSort = $fifthSort;
        }
        //dernier tri
        if(!empty($_GET['sorted'])){
            $sorted = htmlspecialchars($_GET['sorted']);
            switch ($sorted) {
                case "generationCroissant":
                    $columns = array_column($sixthSort, 'generationnumber');
                    array_multisort($columns, SORT_ASC, $sixthSort);
                    break;
                case "generationDecroissant":
                    $columns = array_column($sixthSort, 'generationnumber');
                    array_multisort($columns, SORT_DESC, $sixthSort);
                    break;
                case "ajoutCroissant":
                    $columns = array_column($sixthSort, 'id');
                    array_multisort($columns, SORT_ASC, $sixthSort);
                    break;
                case "ajoutDecroissant":
                    $columns = array_column($sixthSort, 'id');
                    array_multisort($columns, SORT_DESC, $sixthSort);
                    break;
            }
            $allMembers = $sixthSort;
        }else{
            $allMembers = $sixthSort;
        }
    // $columns = array_column($firstSort, 'id');
    // array_multisort($columns, SORT_ASC, $firstSort);
    // var_dump($firstSort);   
?>
<script src="js/dropdown.js"></script>
<br>
<div class="searchDiv">
    <div class="searchTop"><i class="fas fa-search"></i> Recherche </div>
    <form action="" class="searchForm" method="get">
        <div class="searchGroup">
            <input type="search" value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : "" ?>" placeholder="Rechercher..." name="search">
            <button type="submit" name="searchButton" class="searchBarButton"><i class="fa fa-search"></i></button>
            <div class="advancedSearch"><i class="fas fa-filter"></i></div>
            <div class="advanced">
                <div class="sub-advanced">
                    <div class="group">
                        <div>
                            <select name="genre" id="">
                                <option value="">Genre : </option>
                                <option value="Homme" <?php echo (isset($_GET['genre']) and $_GET['genre'] == "Homme") ? "selected" : "" ?>>Homme</option>
                                <option value="Femme" <?php echo (isset($_GET['genre']) and $_GET['genre'] == "Femme") ? "selected" : "" ?>>Femme</option>
                            </select>
                        </div>

                        <div>
                            <input type="text" name="birthDate" value="<?php echo (isset($_GET['birthDate'])) ? $_GET['birthDate'] : "" ?>" placeholder="Date de naissance">
                        </div>
                        <div>
                            <input type="text" name="weddingDate" value="<?php echo (isset($_GET['weddingDate'])) ? $_GET['weddingDate'] : "" ?>" placeholder="Date de mariage">
                        </div>

                    </div>
                    <div class="group">
                        <div class="groupDiv">
                            <input type="text" name="deathDate" value="<?php echo (isset($_GET['deathDate'])) ? $_GET['deathDate'] : "" ?>" placeholder="Date de mort">
                        </div>
                        <div class="groupDiv">
                            <select name="generationNumber" id="">
                                <option value="">Numéro de la génération</option>
                                <?php
                                if (!empty($allGenerationNumbers)) {
                                    foreach ($allGenerationNumbers as $generationNumber) {
                                ?>
                                        <option value="<?= $generationNumber ?>" <?php echo (isset($_GET['generationNumber']) and $_GET['generationNumber'] == $generationNumber) ? "selected" : "" ?>><?= $generationNumber ?></option>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <option value="">Pas de numéro de génération</option>
                                <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="generationCroissant" name="sorted" id="generationCroissant" <?php echo (isset($_GET['sorted']) and $_GET['sorted'] == "generationCroissant") ? "checked" : "" ?>>
                            <label class="form-check-label" for="generationCroissant">
                                Numéro de génération croissant
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="generationDecroissant" name="sorted" id="generationDecroissant" <?php echo (isset($_GET['sorted']) and $_GET['sorted'] == "generationDecroissant") ? "checked" : "" ?>>
                            <label class="form-check-label" for="generationDecroissant">
                                Numéro de génération décroissant
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="ajoutCroissant" name="sorted" id="ajoutCroissant" <?php echo (isset($_GET['sorted']) and $_GET['sorted'] == "ajoutCroissant") ? "checked" : "" ?>>
                            <label class="form-check-label" for="ajoutCroissant">
                                Date d'ajout de la plus récente à la plus ancienne
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="ajoutDecroissant" name="sorted" id="ajoutDecroissant" <?php echo (isset($_GET['sorted']) and $_GET['sorted'] == "ajoutDecroissant") ? "checked" : "" ?>>
                            <label class="form-check-label" for="ajoutDecroissant">
                                Date d'ajout de la plus anciene à la plus recente
                            </label>
                        </div>
                    </div>
                    <div style="display: flex;justify-content: center;">
                        <a href="members.php" type="button" class="btn btn-outline-danger">Réinitialiser la recherche</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="changeMode">
    <a class="test" href="#" onclick="show('Section1');"><i class="fas fa-list-ul"></i></a>
    <a class="test" href="#" onclick="show('Section2');"><i class="fas fa-th-large"></i></a>
</div>
<?php
$json = file_get_contents("test.json");
$jsonArray = json_decode($json, true);
?>
<div id="Section1">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">génération</th>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Coordonées</th>
                <th scope="col">Modifier</th>
                <th scope="col">Supprimer</th>
            </tr>
        </thead>
        <tbody>

            <?php
            foreach ($allMembers as $member) {
            ?>
                <tr>
                    <th scope="row"><?= $member['id'] ?></th>
                    <td><?= $member['generationnumber'] ?></td>
                    <td><?php echo ($member['lastname'] !== "") ? $member['lastname'] : "N/A" ?></td>
                    <td><?php echo ($member['firstname'] !== "") ? $member['firstname'] : "N/A" ?></td>
                    <td><?php echo ($member['contactdetails'] !== "") ? $member['contactdetails'] : "N/A" ?></td>
                    <td class="modify"><a class="btn btn-outline-primary button" data-modal="modal<?= $member['id'] ?>"><i class="fas fa-pen"></i></a></td>
                    <td><a class="btn btn-outline-danger button supp" data-modal="supp<?= $member['id'] ?>"><i class="fas fa-trash"></i></a></td>
                </tr>
                <div id="supp<?= $member['id'] ?>" class="modal">
                    <div class="modal-content">
                        <div class="contact-form">
                            <a class="close">&times;</a>
                            <h4>Êtes-vous sûr de vouloir supprimer le membre <b><span style="text-transform: uppercase;"><?= $member['lastname'] ?></span> <?= $member['firstname'] ?></b> ? Cette action est irreversible. </h4><br>
                            <form action="" method="post">
                                <div class="d-grid gap-2 col-6 mx-auto">
                                    <input type="hidden" name="idForDelete" value="<?= $member['id'] ?>">
                                    <button type="submit" name="oui" class="btn btn-outline-danger">Oui</button>
                                    <button type="submit" name="non" class="btn btn-outline-success">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="modal<?= $member['id'] ?>" class="modal">
                    <div class="modal-content">
                        <div class="contact-form">
                            <a class="close">&times;</a>
                            <h2>Ceci est le test du membre numéro : <?= $member['id'] ?></h2><br>
                            <p>Prénom : <?php echo $member['firstname']; ?> | Nom : <?php echo $member['lastname']; ?></p>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </tbody>
    </table>

</div>
<div id="Section2" style="display: none;">
    <div class="global">
        <?php
        foreach ($allMembers as $member) {
            $color = "#" . substr(md5(rand()), 0, 6);
            $color = $jsonArray[$member['generationnumber']];
        ?>
            <div class="member" style="background-color: <?= $color ?>;">
                <p><?= $member['id'] ?></p>
            </div>
        <?php
        }
        ?>

    </div>
</div>
<script type="text/javascript">
    var divs = ["Section1", "Section2"];
    var visibleId = null;

    function show(id) {
        if (visibleId !== id) {
            visibleId = id;
        }
        hide();
    }

    function hide() {
        var div, i, id;
        for (i = 0; i < divs.length; i++) {
            id = divs[i];
            div = document.getElementById(id);
            if (visibleId === id) {
                div.style.display = "block";
            } else {
                div.style.display = "none";
            }
        }
    }
</script>
<script>
    var modalBtns = [...document.querySelectorAll(".button")];
    modalBtns.forEach(function(btn) {
        btn.onclick = function() {
            var modal = btn.getAttribute('data-modal');
            document.getElementById(modal).style.display = "block";
        }
    });

    var closeBtns = [...document.querySelectorAll(".close")];
    closeBtns.forEach(function(btn) {
        btn.onclick = function() {
            var modal = btn.closest('.modal');
            modal.style.display = "none";
        }
    });

    window.onclick = function(event) {
        if (event.target.className === "modal") {
            event.target.style.display = "none";
        }
    }
</script>

</body>

</html>