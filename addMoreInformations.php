<?php
$title = "Ajout des relations";
require_once "./component/header.php";
require_once "./component/bdd.php";
require_once "./functions/sql.php";
$members = getMemberWithoutRelationshipInformations($dbh);
if (isset($_POST['moreInformations'])) {
    if (!empty($_POST['parents']) or !empty($_POST['children']) or !empty($_POST['weddingPartner'])) {
        $memberId = $_POST['hiddenId'];
        $memberInformations = getMemberById($dbh, $memberId);
        $parents = "";
        $oneParent = false;
        if (!empty($_POST['parents'])) {
            $tempParents = $_POST['parents'];
            if(count($tempParents) == 1){
                $oneParent = true;
            }
            if(count($tempParents) > 2){
                return "error";
            }
            foreach ($tempParents as $temp) {
                if($oneParent){
                    $parents = $temp . "/" . "NA";
                }else{
                    if ($parents == "") {
                        $parents = $temp;
                    } else {
                        $parents .= "/" . $temp;
                    }
                }
            }
        } else {
            if ($memberInformations['parents'] != "") {
                $parents = $memberInformations['parents'];
            }
        }

        $children = "";
        if (!empty($_POST['children'])) {
            $tempChildren = $_POST['children'];
            foreach ($tempChildren as $temp) {
                if ($children == "") {
                    $children = $temp;
                } else {
                    $children .= "/" . $temp;
                }
            }
        } else {
            if ($memberInformations['children'] != "") {
                $children = $memberInformations['children'];
            }
        }
        $weddingPartner = "";
        if (!empty($_POST['weddingPartner'])) {
            $weddingPartner = $_POST['weddingPartner'];
        } else {
            if ($memberInformations['weddingpartner'] != "") {
                $weddingPartner = $memberInformations['weddingpartner'];
            }
        }
        echo $parents, $weddingPartner, $children;
        $final = 0; 
        if(!empty($parents) && !empty($weddingPartner)&& !empty($children)){
            $final = 1;
        }
        updateMemberNewInformations($dbh, $memberId, $parents, $weddingPartner, $children, $final);
        header("Location:addMoreInformations.php");
    }
}
if(isset($_POST['moreInformationsNone'])){
    $memberId = $_POST['hiddenId'];
    $member = getMemberById($dbh, $memberId);
    $final = 1;
    updateMemberNewInformations($dbh, $memberId, $member['parents'], $member['weddingpartner'], $member['children'], $final);
    header("Location:addMoreInformations.php");
}
?>
<h2>Il y a ci-dessous, l'ensemble des membres pour lesquels il manque les relations (mariage, parent, enfant)</h2>
<table class="table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nom</th>
            <th scope="col">Prénom</th>
            <th scope="col">Date naissance</th>
            <th scope="col">Ajouter</th>
        </tr>
    </thead>
    <tbody>

        <?php
        foreach ($members as $member) {
        ?>
            <tr>
                <th scope="row"><?= $member['id'] ?></th>
                <td><?php echo ($member['lastname'] !== "") ? $member['lastname'] : "N/A" ?></td>
                <td><?php echo ($member['firstname'] !== "") ? $member['firstname'] : "N/A" ?></td>
                <td><?php echo ($member['birthdate'] !== "") ? $member['birthdate'] : "N/A" ?></td>
                <td class="modify"><a class="btn btn-outline-primary button" data-modal="modal<?= $member['id'] ?>"><i class="fas fa-plus-circle"></i></a></td>
            </tr>
            <div id="modal<?= $member['id'] ?>" class="modal">
                <div class="modal-content">
                    <div class="contact-form">
                        <a class="close">&times;</a>
                        <h4>Ajouter les relations du membre : <?= $member['id'] ?>, <?php echo !empty($member['lastname']) ? strtoupper($member['lastname']) : "N/A" ?> <?php echo !empty($member['firstname']) ? $member['firstname'] : "N/A" ?></h4><br>
                        <?php

                        $id = $member['id'];
                        $memberInformation = getMemberById($dbh, $id);
                        $emptyValue = "";
                        if ($memberInformation['parents'] == "" or $memberInformation['parents'] == null) {
                            $emptyValue = "Parents";
                        }
                        if ($memberInformation['weddingpartner'] == ""  or $memberInformation['weddingpartner'] == null) {
                            if ($emptyValue == "") {
                                $emptyValue = "Partenaire de mariage";
                            } else {
                                $emptyValue .= "/" . "Partenaire de mariage";
                            }
                        }
                        if ($memberInformation['children'] == ""  or $memberInformation['children'] == null) {
                            if ($emptyValue == "") {
                                $emptyValue = "Enfant(s)";
                            } else {
                                $emptyValue .= "/" . "Enfants";
                            }
                        }
                        ?>
                        <p>Champs qui n'ont pas encore été remplis : <?= $emptyValue ?></p>
                        <form action="" method="post">
                            <?php
                                $members2 = getAllMembersExceptThisId($dbh, $member['id']);
                            ?>
                            <label>Parents : </label>
                            <select multiple data-select name="parents[]">
                                <?php foreach ($members2 as $member2) { ?>
                                    <option value="<?= $member2['id'] ?>"><?= strtoupper($member2['lastname']) . " " . $member2['firstname'] . " naissance :" . $member2['birthdate'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <br>
                            <label> Partenaire de mariage : </label>
                            <select data-select name="weddingPartner">
                                <option value=""></option>
                                <?php foreach ($members2 as $member2) { ?>
                                    <option value="<?= $member2['id'] ?>"><?= strtoupper($member2['lastname']) . " " . $member2['firstname'] . " naissance :" . $member2['birthdate'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <br>
                            <label> Enfant (s)</label>
                            <select multiple data-select name="children[]">
                                <?php foreach ($members2 as $member2) { ?>
                                    <option value="<?= $member2['id'] ?>"><?= strtoupper($member2['lastname']) . " " . $member2['firstname'] . " naissance :" . $member2['birthdate'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <br>
                            <div class="col-12">
                                <input type="hidden" name="hiddenId" value="<?= $member['id'] ?>">
                                <button type="submit" name="moreInformations" class="btn btn-outline-primary" title="Ajouter les champs en base de données">Ajouter</button>
                                <button type="submit" name="moreInformationsNone" class="btn btn-outline-danger" title="Remplacer les champs vide par N/A">Finir définitivement</button>
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
<script src="js/select.js" type="module"></script>