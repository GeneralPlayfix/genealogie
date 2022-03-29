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

function addMembers($dbh, $firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace, $gender, $remarks, $cityId, $final = 0){
    $sql = $dbh->prepare("INSERT INTO membres (firstname, lastname, birthdate, birthplace, weddingdate, weddingplace, generationnumber, contactdetails, deathdate, deathplace, gender, remarks, townId, final) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql ->execute(array($firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $contactDetails, $deathDate, $deathPlace, $gender, $remarks, $cityId, $final));
}
function getMemberByAllInformations($dbh, $firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $deathDate, $deathPlace){
    $sql = $dbh->prepare("SELECT * FROM membres WHERE firstname = ? AND lastname = ? AND birthdate = ? AND birthplace = ? AND weddingdate = ? AND  weddingplace = ? AND generationnumber = ? AND deathdate = ? AND deathplace = ? ");
    $sql ->execute(array($firstName,$lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $deathDate, $deathPlace));
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
    $query = $dbh->prepare("SELECT * FROM membres WHERE final = ?");
    $query->execute(array(0));
    return $result = $query->fetchAll();
}
function getMemberById($dbh, $id){
    $query = $dbh->prepare("SELECT * FROM membres WHERE id = ?");
    $query->execute(array($id));
    return $result = $query->fetch();
}

function updateMemberNewInformations($dbh, $id, $parents, $weddingPartner, $children, $final = 0){
    $sql = $dbh->prepare("UPDATE membres SET parents = ?, weddingpartner = ?, children = ?, final = ? WHERE id = ?");
    $sql -> execute(array($parents, $weddingPartner, $children,$final,$id));
}
function getAllMembersExceptThisId($dbh, $id){
    $query = $dbh->prepare("SELECT * FROM membres WHERE id != ?");
    $query->execute(array($id));
    return $result = $query->fetchAll();
}
function getInformationsByWords($dbh, $search){
    $query = $dbh->prepare("SELECT * FROM membres WHERE UPPER(id) LIKE UPPER(?) OR UPPER(firstname) LIKE UPPER(?) OR UPPER(lastname) LIKE UPPER(?) OR UPPER(birthplace) LIKE UPPER(?) OR UPPER(weddingplace) LIKE UPPER(?) OR UPPER(deathplace) LIKE UPPER(?) OR UPPER(weddingpartner) LIKE UPPER(?) OR UPPER(children) LIKE UPPER(?) OR UPPER(parents) LIKE UPPER(?) OR UPPER(remarks) LIKE UPPER(?)");
	$query -> execute(array($search, $search , $search, $search, $search, $search, $search, $search, $search, $search));
	return $members = $query->fetchAll();
}

function getCityByNameAndZipCode($dbh, $cityName, $cityZipCode){
    $query = $dbh->prepare("SELECT * FROM villes WHERE name LIKE(?) and zip_code LIKE (?)");
	$query -> execute(array($cityName, $cityZipCode));
	return $city = $query->fetch();
}
function getMembersByCityId($dbh, $id){
    $query = $dbh->prepare("SELECT * FROM membres WHERE townId = ?");
	$query -> execute(array($id));
	return $members = $query->fetchAll();
}
function getCityById($dbh, $id){
    $query = $dbh->prepare("SELECT * FROM villes WHERE id = ?");
	$query -> execute(array($id));
	return $members = $query->fetch();
}
function updateMember($dbh, $firstName, $lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $newContactDetails, $deathDate, $deathPlace, $gender, $remarks, $cityId, $id){
    $sql = $dbh->prepare("UPDATE membres SET firstname = ?, lastname = ? ,  birthdate = ? ,  birthplace = ? ,  weddingdate = ? ,   weddingplace = ? ,  generationnumber = ? ,  contactdetails = ? ,  deathdate = ? ,  deathplace = ? ,  gender = ? ,  remarks = ?, townId = ? WHERE id = ?");
    $sql -> execute(array($firstName, $lastName, $birthDate, $birthPlace, $weddingDate, $weddingPlace, $generationNumber, $newContactDetails, $deathDate, $deathPlace, $gender, $remarks, $cityId, $id));
}
?>