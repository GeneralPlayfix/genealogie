<?php
$title = "Ajouter un membre";
require_once "./component/header.php";
require_once "./functions/sql.php";
require_once "./functions/util.php";
require_once "./component/bdd.php";
if (isset($_POST['send'])) {
    if (!empty($_POST['firstname']) || !empty($_POST['lastname'])) {
        if (!empty($_POST['generationNumber'])) {
            $explodedLastName = explode(" ", trim($_POST['lastname']));
            $lastName = "";
            foreach ($explodedLastName as $tempLastName) {
                $result = mbUcfirst($tempLastName);
                if ($lastName == "") {
                    $lastName = $result;
                } else {
                    $lastName .= " " . $result;
                }
            }
            $explodedFirstName = explode(" ", trim($_POST['firstname']));
            $firstName = "";
            foreach ($explodedFirstName as $tempFirstName) {
                $result = mbUcfirst($tempFirstName);
                if ($firstName == "") {
                    $firstName = $result;
                } else {
                    $firstName .= " " . $result;
                }
            }
            $birthDate = htmlspecialchars(trim($_POST['birthDate']));
            $birthPlace = htmlspecialchars(trim($_POST['birthPlace']));
            $weddingDate = htmlspecialchars(trim($_POST['weddingDate']));
            $weddingPlace = htmlspecialchars(trim($_POST['weddingPlace']));
            $generationNumber = htmlspecialchars(trim(intval($_POST['generationNumber'])));
            $tempContactDetails = htmlspecialchars(trim($_POST['contactDetails']));
            $deathDate = htmlspecialchars(trim($_POST['deathDate']));
            $deathPlace = htmlspecialchars(trim($_POST['deathPlace']));
            if (!empty($tempContactDetails)) {
                $tempContactDetails = explode(" ", $tempContactDetails);
                $cityName = $tempContactDetails[0];
                $cityZipCode = $tempContactDetails[1];
                if (is_numeric($cityZipCode)) {
                    $townSearchResult = getCityByNameAndZipCode($dbh, $cityName, $cityZipCode);
                    if (!empty($townSearchResult)) {
                        $cityId = $townSearchResult['id'];
                        $lat = $townSearchResult['gps_lat'];
                        $lng = $townSearchResult['gps_lng'];
                        $contactDetails = $lat.", ".$lng;
                        $isOk = getMemberByAllInformations($dbh, $firstName, $lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $deathDate, $deathPlace);
                        if ($isOk == 0) {
                            if (!empty($_POST['gender'])) {
                                $gender = htmlspecialchars($_POST['gender']);
                                $remarks = htmlspecialchars(trim($_POST["remarks"]));
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
                                 addMembers($dbh, $firstName, $lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $newContactDetails, $deathDate, $deathPlace, $gender, $remarks, $cityId);
                                 $msg = "Le membre $firstName $lastName à bien été ajouté";
                                }else{
                                    addMembers($dbh, $firstName, $lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace, $gender, $remarks, $cityId);
                                    $msg = "Le membre $firstName $lastName à bien été ajouté";
                                }                                
                            }else{
                                $error = "Le genre de la personne doit être impérativement renseigné";
                            }
                        } else {
                            $error = "Le membre a déjà été ajouté !";
                        }
                    }else{
                        $err = "Veuillez rentrer un nom de ville valide";
                    }
                } else {
                    $error = "Vous devez impérativement rentrer un nom de ville et son code postal (Villeneuve-lès-Avignon 30400)";
                }
            } else {
                $error = "Vous devez renseigner la ville pour que le programme puisse récupérer des coordonées";
            }
        } else {
            $error = "Vous devez imperativement renseigner le numéro de la génération !";
        }
    } else {
        $error = "Vous devez au minimum remplir le champ nom ou prénom !";
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
<form class="row g-3" id="addMembersForm" method="post">
    <?php
    if (isset($error) || isset($msg)) {
        if (isset($error)) {
    ?>
            <div class="col-md-12">
                <p class="error"> <?php echo $error ?></p>
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
            header('Refresh: 5; URL=add.php');
        }
    }
    ?>
    <div class="col-md-4">
        <label for="lastname" class="form-label">Nom de famille (s) :</label>
        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo (isset($_POST['lastname']) and !empty($_POST['lastname'])) ? $_POST['lastname'] : "" ?>" placeholder="nom1 nom2">
    </div>

    <div class="col-md-4">
        <label for="firstname" class="form-label">Prénom (s) : </label>
        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo (isset($_POST['firstname']) and !empty($_POST['firstname'])) ? $_POST['firstname'] : "" ?>" placeholder="prénom1 prénom2">
    </div>
    <div class="col-md-4">
        <label for="gender" class="form-label">Genre :</label>
        <select id="gender" name="gender" class="form-select">
            <option value="Homme" <?php echo (isset($_POST['gender']) and !empty($_POST['gender']) and $_POST['gender'] == "Homme") ? "selected" : "" ?>>Homme</option>
            <option value="Femme" <?php echo (isset($_POST['gender']) and !empty($_POST['gender']) and $_POST['gender'] == "Femme") ? "selected" : "" ?>>Femme</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="birthDate" class="form-label">Date de naissance :</label>
        <input type="text" class="form-control" id="birthDate" name="birthDate" value="<?php echo (isset($_POST['birthDate']) and !empty($_POST['birthDate'])) ? $_POST['birthDate'] : "" ?>" placeholder="12/03/1863">
    </div>

    <div class="col-md-6">
        <label for="birthPlace" class="form-label">Lieu de naissance : </label>
        <input type="text" class="form-control" id="birthPlace" name="birthPlace" value="<?php echo (isset($_POST['birthPlace']) and !empty($_POST['birthPlace'])) ? $_POST['birthPlace'] : "" ?>">
    </div>

    <!-- <div class="col-md-4">
        <label for="parents" class="form-label">Parent(s) : </label>
        <input type="text" class="form-control" id="parents" name="parents" value="<?php echo (isset($_POST['parents']) and !empty($_POST['parents'])) ? $_POST['parents'] : "" ?>" placeholder="nom prénom/nom prénom">
    </div> -->
    <div class="col-md-6">
        <label for="weddingDate" class="form-label">Date de mariage :</label>
        <input type="text" class="form-control" id="weddingDate" name="weddingDate" value="<?php echo (isset($_POST['weddingDate']) and !empty($_POST['weddingDate'])) ? $_POST['weddingDate'] : "" ?>" placeholder="21/08/1892">
    </div>

    <div class="col-md-6">
        <label for="weddingPlace" class="form-label">Lieu de mariage : </label>
        <input type="text" class="form-control" id="weddingPlace" name="weddingPlace" value="<?php echo (isset($_POST['weddingPlace']) and !empty($_POST['weddingPlace'])) ? $_POST['weddingPlace'] : "" ?>">
    </div>
    <!-- <div class="col-md-6">
        <label for="weddingPartner" class="form-label">Femme / Mari : </label>
        <input type="text" class="form-control" id="weddingPartner" name="weddingPartner" value="<?php echo (isset($_POST['weddingPartner']) and !empty($_POST['weddingPartner'])) ? $_POST['weddingPartner'] : "" ?>" placeholder="charles delavalière">
    </div> -->

    <!-- <div class="col-md-6">
        <label for="children" class="form-label">Enfant(s) : </label>
        <input type="text" class="form-control" id="children" name="children" value="<?php echo (isset($_POST['children']) and !empty($_POST['children'])) ? $_POST['children'] : "" ?>" placeholder="nom prénom/nom prénom/nom prénom">
    </div> -->
    <div class="col-md-6">
        <label for="generationNumber" class="form-label">Numéro de la génération :</label>
        <input type="text" class="form-control" id="generationNumber" name="generationNumber" value="<?php echo (isset($_POST['generationNumber']) and !empty($_POST['generationNumber'])) ? $_POST['generationNumber'] : "" ?>" placeholder="10">
    </div>
    <div class="col-md-6" id="searchDiv">
        <label for="search" class="form-label">Rechercher une ville : </label>
        <input type="text" class="form-control" id="search" name="contactDetails" value="<?php echo (isset($_POST['contactDetails']) and !empty($_POST['contactDetails'])) ? $_POST['contactDetails'] : "" ?>" placeholder="Rechercher une ville">
        <div id="content" style="width: 100%;">
        </div>
    </div>

    <div class="col-md-6">
        <label for="deathDate" class="form-label">Date de décès : </label>
        <input type="text" class="form-control" id="deathDate" name="deathDate" value="<?php echo (isset($_POST['deathDate']) and !empty($_POST['deathDate'])) ? $_POST['deathDate'] : "" ?>" placeholder="12/06/1906">
    </div>

    <div class="col-md-6">
        <label for="deathPlace" class="form-label">Lieu de décés :</label>
        <input type="text" class="form-control" id="deathPlace" name="deathPlace" value="<?php echo (isset($_POST['deathPlace']) and !empty($_POST['deathPlace'])) ? $_POST['deathPlace'] : "" ?>">
    </div>


    <div class="mb-3">
        <label for="remarks" class="form-label">Remarques : </label>
        <textarea class="form-control" id="remarks" name="remarks"><?php echo (isset($_POST['remarks']) and !empty($_POST['remarks'])) ? $_POST['remarks'] : ""  ?></textarea>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary" name="send">Ajouter</button>
    </div>

</form>
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