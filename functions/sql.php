<?php

function getAllMembers($dbh){
    $query = $dbh->prepare("SELECT * FROM membres ORDER BY id DESC");
    $query ->execute();
    return $members = $query->fetchAll();
}
function getMemberByContactDetails($dbh, $contactDetails){
    $sql = $dbh->prepare("SELECT * FROM membres WHERE contactdetails = ?");
    $sql ->execute(array($contactDetails));
    return $result = $sql->rowCount();
}

function addMembers($dbh, $firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace, $gender, $remarks){
    $sql = $dbh->prepare("INSERT INTO membres (firstname, lastname, birthdate, birthplace, weddingdate, weddingplace, generationnumber, contactdetails, deathdate, deathplace, gender, remarks) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql ->execute(array($firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace, $gender, $remarks));
}
function getMemberByAllInformations($dbh, $firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace){
    $sql = $dbh->prepare("SELECT * FROM membres WHERE firstname = ? AND lastname = ? AND birthdate = ? AND birthplace = ? AND weddingdate = ? AND  weddingplace = ? AND generationnumber = ? AND contactdetails = ? AND deathdate = ? AND deathplace = ? ");
    $sql ->execute(array($firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace));
    return $result = $sql->rowCount();
}
function getAllMembersWithContactDetails($dbh){
    $query = $dbh->prepare("SELECT * FROM membres WHERE contactdetails != ?");
    $query ->execute(array(""));
    return $members = $query->fetchAll();
}
function deleteMember($dbh, $id){
$statement = $dbh->prepare('DELETE FROM membres WHERE id = ?'); 
$statement -> execute(array($id));
}
function getMemberByName($dbh, $lastname, $firstname){
    $lastName = "%"+ $lastname+"%";
    $firstName = "%"+ $firstname+"%";
    $sql = $dbh->prepare("SELECT * FROM membres WHERE UPPER(firstname) LIKE UPPER(?) AND UPPER(lastname) LIKE UPPER(?)");
    $sql ->execute(array($lastName, $firstName));
    return $result = $sql->fetch();
}
function getMemberWithoutRelationshipInformations($dbh){
    $query = $dbh->prepare("SELECT * FROM membres WHERE weddingpartner IS NULL OR parents IS NULL OR children IS NULL OR weddingpartner = ? OR parents = ? OR children = ? ");
    $query->execute(array("","",""));
    return $result = $query->fetchAll();
}
function getMemberById($dbh, $id){
    $query = $dbh->prepare("SELECT * FROM membres WHERE id = ?");
    $query->execute(array($id));
    return $result = $query->fetch();
}
function updateMemberNewInformations($dbh, $id, $parents, $weddingPartner, $children){
    $sql = $dbh->prepare("UPDATE membres SET id = ?, weddingpartner = ?, parents = ?, children = ? WHERE id = ?");
    $sql -> execute(array($id, $weddingPartner, $parents , $children, $id));
}
function getAllMembersExceptThisId($dbh, $id){
    $query = $dbh->prepare("SELECT * FROM membres WHERE id != ?");
    $query->execute(array($id));
    return $result = $query->fetchAll();
}
function getInformationsByWords($dbh, $search){
    $query = $dbh->prepare("SELECT * FROM membres WHERE UPPER(firstname) LIKE UPPER(?) OR UPPER(lastname) LIKE UPPER(?) OR UPPER(birthplace) LIKE UPPER(?) OR UPPER(weddingplace) LIKE UPPER(?) OR UPPER(deathplace) LIKE UPPER(?) OR UPPER(weddingpartner) LIKE UPPER(?) OR UPPER(children) LIKE UPPER(?) OR UPPER(parents) LIKE UPPER(?) OR UPPER(remarks) LIKE UPPER(?)");
	$query -> execute(array($search, $search, $search, $search, $search, $search, $search, $search, $search));
	return $members = $query->fetchAll();
}
?>