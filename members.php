<?php
$title = "Tous les membres";
require_once "./component/header.php";
require_once "./component/bdd.php";
require_once "./functions/sql.php";
require_once "./functions/util.php";
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
asort($allGenerationNumbers, SORT_REGULAR);
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
//deuxième tri
if (!empty($_GET['genre'])) {
    $genre = htmlspecialchars($_GET['genre']);
    foreach ($firstSort as $member) {
        if ($member['gender'] == $genre) {
            array_push($secondSort, $member);
        }
    }
} else {
    $secondSort = $firstSort;
}
//troisième tri
if (!empty($_GET['birthDate'])) {
    $birthDate = htmlspecialchars($_GET['birthDate']);
    foreach ($secondSort as $member) {
        if (strpos($member['birthdate'], $birthDate) !== FALSE) {
            array_push($thirdSort, $member);
        }
    }
} else {
    $thirdSort = $secondSort;
}
//quatrième tri
if (!empty($_GET['weddingDate'])) {
    $weddingDate = htmlspecialchars($_GET['weddingDate']);
    foreach ($thirdSort as $member) {
        if (strpos($member['weddingdate'], $weddingDate) !== FALSE) {
            array_push($fourthSort, $member);
        }
    }
} else {
    $fourthSort = $thirdSort;
}
//cinquième tri
if (!empty($_GET['deathDate'])) {
    $deathDate = htmlspecialchars($_GET['deathDate']);
    foreach ($fourthSort as $member) {
        if (strpos($member['deathdate'], $deathDate) !== FALSE) {
            array_push($fifthSort, $member);
        }
    }
} else {
    $fifthSort = $fourthSort;
}
//sixième tri
if (!empty($_GET['generationNumber'])) {
    $generationNumber2 = intval($_GET['generationNumber']);
    foreach ($firstSort as $member) {
        if ($member['generationnumber'] == $generationNumber2) {
            array_push($sixthSort, $member);
        }
    }
} else {
    $sixthSort = $fifthSort;
}
//dernier tri
if (!empty($_GET['sorted'])) {
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
} else {
    $allMembers = $sixthSort;
}
if (isset($_POST['removeFromFinalList']) && !empty($_POST['removeFromFinalList'])) {
    $tempMember = getMemberById($dbh, $_POST['id']);
    updateMemberNewInformations($dbh, $tempMember['id'], $tempMember['parents'], $tempMember['weddingpartner'], $tempMember['children'], 0);
}

if (isset($_POST['modify']) && !empty($_POST['modify'])) {
    $firstName = htmlspecialchars(trim($_POST['firstname']));
    $lastName = htmlspecialchars(trim($_POST['lastname']));
    $birthDate = htmlspecialchars(trim($_POST['birthdate']));
    $birthPlace = htmlspecialchars(trim($_POST['birthplace']));
    $weddingDate = htmlspecialchars(trim($_POST['weddingdate']));
    $weddingPlace = htmlspecialchars(trim($_POST['weddingplace']));
    $generationNumber = htmlspecialchars(trim(intval($_POST['generationnumber'])));
    $tempContactDetails = htmlspecialchars(trim($_POST['contactDetails']));
    $deathDate = htmlspecialchars(trim($_POST['deathdate']));
    $deathPlace = htmlspecialchars(trim($_POST['deathplace']));
    $gender = htmlspecialchars($_POST['gender']);
    $remarks = htmlspecialchars(trim($_POST["remarks"]));
    if (!empty($generationNumber)) {
        $memberToModify = getMemberById($dbh, $_POST['id']);
        if (!empty($firstName)) {
            if (!empty($lastName)) {
                if (!empty($tempContactDetails)) {
                    $tempContactDetails = explode(" ", $tempContactDetails);
                    $cityName = $tempContactDetails[0];
                    $cityZipCode = $tempContactDetails[1];
                    if (is_numeric($cityZipCode)) {
                        $townSearchResult = getCityByNameAndZipCode($dbh, $cityName, $cityZipCode);
                        if (!empty($townSearchResult)) {
                            $cityId = $townSearchResult['id'];
                            $contactDetails = "";
                            if($cityId == $memberToModify['townId']){
                                $contactDetails = $memberToModify['contactdetails'];
                            }else{
                                $lat = $townSearchResult['gps_lat'];
                                $lng = $townSearchResult['gps_lng'];
                                $contactDetails = $lat.", ".$lng;
                                $membersByCity = getMembersByCityId($dbh, $cityId);
                                if(!empty($membersByCity)){
                                    $newContactDetails = "";
                                    $latBooleanPlus = Random();
                                    $longBooleanPlus = Random();
                                    $latValueToAdd = float_rand(0.002, 0.009);
                                    $longValueToAdd = float_rand(0.002, 0.009);
                                     do{
                                        if($latBooleanPlus){
                                            $lat += $latValueToAdd; 
                                        }else{
                                            $lat -= $latValueToAdd;
                                        }
                                        if($longBooleanPlus){
                                            $lng += $longValueToAdd; 
                                        }else{
                                            $lng -= $longValueToAdd; 
                                        }
                                        $newContactDetails = $lat.", ".$lng;
                                        $memberByContactDetails = getMemberByContactDetails($dbh, $newContactDetails);
                                 }while($memberByContactDetails != 0);
                                 $contactDetails = $newContactDetails;
                                }     
                            }
                            echo $lastName;
                            echo $firstName;
                            updateMember($dbh, $firstName, $lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace, $gender, $remarks, $cityId, $memberToModify['id']);
                            $msg = "Le membre $firstName $lastName à bien été modifié";
                        }else{
                            $err = "La ville rentrée n'existe pas !";
                        }
                    } else {
                        $error = "Vous devez impérativement rentrer un nom de ville et son code postal (Villeneuve-lès-Avignon 30400)";
                    }
                } else {
                    $err = "Vous devez impérativement rentrer le nom d'une ville";
                }
            } else {
                $err = "Vous devez impérativement remplir le nom";
            }
        } else {
            $err = "Vous devez impérativement remplir le prénom";
        }
    } else {
        $err = "Vous devez impérativement renseigné le numéro de la génération";
    }
}
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
<?php
    if (isset($err) || isset($msg)) {
        if (isset($err)) {
    ?>
            <div class="col-md-12">
                <p class="error"> <?php echo $err ?></p>
            </div>
        <?php
        } else if (isset($msg)) {
        ?>

            <div class="col-md-12">
                <p class="success"> <?php echo $msg ?>... Vous allez être redirigé dans <b>
                        <script>
                            count(5)
                        </script>
                    </b> secondes
                </p>
            </div>

    <?php
            header('Refresh: 5; URL=members.php');
        }
    }
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
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
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
                    <td><?php echo ($member['lastname'] !== "") ? $member['lastname'] : "N/A" ?></td>
                    <td><?php echo ($member['firstname'] !== "") ? $member['firstname'] : "N/A" ?></td>
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
                            <br>
                            <form method="post">
                                <?php
                                $cityArray = getCityById($dbh, $member['townId']);
                                $city = $cityArray['name'] . " " . $cityArray['zip_code'];
                                $children = "Aucun";
                                if (!empty($member["children"])) {
                                    $childrenArray = explode("/", $member['children']);
                                    foreach ($childrenArray as $childId) {
                                        $child = getMemberById($dbh, $childId);
                                        if (!empty($child)) {
                                            if ($children == "Aucun") {
                                                $children = strtoupper($child['lastname']) . " " . $child['firstname'];
                                            } else {
                                                $children .= " / " . strtoupper($child['lastname']) . " " . $child['firstname'];
                                            }
                                        }
                                    }
                                }
                                $weddingPartner = "inconnu/NA";
                                if (!empty($member['weddingpartner'])) {
                                    if (is_numeric($member['weddingpartner'])) {
                                        $weddingPartner = getMemberById($dbh, $member['weddingpartner']);
                                        $weddingPartner = strtoupper($weddingPartner['lastname']) . " " . mbUcfirst($weddingPartner['firstname']);
                                    }
                                }
                                $parent = false;
                                $father = "Non renseigné";
                                $mother = "Non renseigné";
                                if (!empty($member['parents'])) {
                                    $parent = true;
                                    if (strpos($member['parents'], "/") !== false) {
                                        $parentArray = explode("/", $member['parents']);
                                        foreach ($parentArray as $parent) {
                                            if (is_numeric($parent)) {
                                                $temp = getMemberById($dbh, $parent);
                                                if ($temp['gender'] == "Homme") {
                                                    $father = strtoupper($temp['lastname'] . " " . $temp['firstName']);
                                                } else if ($temp['gender'] == "Femme") {
                                                    $mother = strtoupper($temp['lastname'] . " " . $temp['firstName']);
                                                }
                                            }
                                        }
                                    }
                                }

                                ?>
                                <input type="hidden" value="<?= $member['id'] ?>" name="id">
                                <div class="row g-3">
                                    <div class="col-sm-4">
                                        <label for="lastname" class="form-label">Nom de famille : </label>

                                        <input type="text" class="form-control" id="lastname" placeholder="Nom de famille" value="<?= $member['lastname'] ?>" aria-label="Nom de famille" name="lastname">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="firstname" class="form-label">Prénom : </label>

                                        <input type="text" class="form-control" id="firstname" placeholder="Prénom" value="<?= $member['firstname'] ?>" aria-label="Prénom" name="firstname">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="inputState" class="form-label">Genre : </label>

                                        <select id="inputState" class="form-select" name="gender">
                                            <option value="Homme" <?php echo ($member['gender'] == "Homme") ? "selected" : "" ?>>Homme</option>
                                            <option value="Femme" <?php echo ($member['gender'] == "Femme") ? "selected" : "" ?>>Femme</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="birthdate" class="form-label">Date de naissance : </label>

                                        <input type="text" class="form-control" id="birthdate" placeholder="Date de naissance" value="<?= $member['birthdate'] ?>" aria-label="Date de naissance" name="birthdate">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="birthplace" class="form-label">Lieu de naissance : </label>

                                        <input type="text" class="form-control" id="birthplace" placeholder="Lieu de naissance" value="<?= $member['birthplace'] ?>" aria-label="Lieu de naissance" name="birthplace">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="mother" class="form-label">Mère : </label>

                                        <input type="text" readonly class="form-control" id="mother" placeholder="Mère" value="<?= $mother ?>" aria-label="Mère" name="mother">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="father" class="form-label">Père : </label>
                                        <input type="text" readonly class="form-control" id="father" placeholder="Père" value="<?= $father ?>" aria-label="Père" name="father">
                                    </div>


                                    <div class="col-sm-4">
                                        <label for="weddingpartner" class="form-label">Partenaire de mariage : </label>
                                        <input type="text" class="form-control" id="weddingpartner" placeholder="Partenaire de mariage" value="<?= $weddingPartner ?>" aria-label="Partenaire de mariage" readonly name="weddingpartner">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="weddingdate" class="form-label">Date de mariage : </label>
                                        <input type="text" class="form-control" id="weddingdate" placeholder="Date de mariage" value="<?= $member['weddingdate'] ?>" aria-label="Date de mariage" name="weddingdate">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="weddingplace" class="form-label">Lieu de mariage : </label>

                                        <input type="text" class="form-control" id="weddingplace" placeholder="Lieu de mariage" value="<?= $member['weddingplace'] ?>" aria-label="Lieu de mariage" name="weddingplace">
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="children" class="form-label">Enfants : </label>

                                        <input type="text" class="form-control" id="children" placeholder="Enfants" value="<?= $children ?>" aria-label="Enfants" readonly name="children">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="generationnumber" class="form-label">Numéro de génération : </label>

                                        <input type="text" class="form-control" id="generationnumber" placeholder="Numéro de génération" value="<?= $member['generationnumber'] ?>" aria-label="Numéro de génération" name="generationnumber">
                                    </div>
                                    <div class="col-md-6" id="searchDiv">
                                        <label for="search" class="form-label">Ville : </label>
                                        <input type="text" class="form-control" id="search" name="contactDetails" value="<?php echo (isset($_POST['contactDetails']) and !empty($_POST['contactDetails'])) ? $_POST['contactDetails'] : $city ?>" placeholder="Rechercher une ville">
                                        <div id="content" style="width: 100%;">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="deathdate" class="form-label">Date de décès : </label>

                                        <input type="text" class="form-control" id="deathdate" placeholder="Date de décès" value="<?= $member['deathdate'] ?>" arifinna-label="Date de décès" name="deathdate">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="deathplace" class="form-label">Date de décès : </label>
                                        <input type="text" class="form-control" id="deathplace" placeholder="Date de décès" value="<?= $member['deathplace'] ?>" arifinna-label="Date de décès" name="deathplace">
                                    </div>

                                    <div class="col-sm-12">
                                        <label for="remarks" class="form-label">Remarques : </label>
                                        <textarea class="form-control" id="remarks" name="remarks"><?= $member['remarks'] ?></textarea>
                                    </div>
                                    <input class="btn btn-primary" type="submit" value="Modifier" name="modify">

                                    <?php
                                    if ($member['final']) {
                                    ?>
                                        <input class="btn btn-success" type="submit" value="Activer le changement des relations" name="removeFromFinalList">

                                    <?php
                                    }
                                    ?>
                                </div>

                        </div>
                        </form>
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
<style>
    #searchDiv {
        position: relative;
    }

    #content {
        background-color: whitesmoke;
        position: absolute;
        z-index: 999;
        width: 50px;
        box-shadow: 4px 4px 8px;

    }

    #content p {
        margin: 2px;
        height: 25px;
        vertical-align: center;
    }

    #content p:hover {
        vertical-align: text-top;
        background-color: #0d6efd;
        color: white;
        cursor: pointer;
    }

    #content p:hover span {
        margin: none;
        padding: none;
        width: 5px;
        height: 100%;
        display: inline-block;
        background-color: red;
    }
</style>
<script src="js/main.js"></script>
</body>

</html>