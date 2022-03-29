<?php
require_once "./component/bdd.php";
require_once "./functions/sql.php";
if(isset($_POST['weddingpartner'])){
    $id = $_POST['weddingpartner'];
    $member = getMemberById($dbh, $id);
    echo json_encode($member);
}
if(isset($_POST['parents'])){
    $getParent = explode("/", $_POST['parents']);
    $arrayOfParents = array();
    foreach($getParent as $parentId){
        if($parentId == "NA") continue;
        $member = getMemberById($dbh, $parentId);
        array_push($arrayOfParents, $member);
    }
    echo json_encode($arrayOfParents);
}

if(isset($_POST['children'])){
    $getChildren = explode("/", $_POST['children']);
    $arrayOfChildren = array();
    foreach($getChildren as $childId){
        if($childId == "NA") continue;
        $member = getMemberById($dbh, $childId);
        array_push($arrayOfChildren, $member);
    }
    echo json_encode($arrayOfChildren);
}