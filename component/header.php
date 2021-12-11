<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/select.css">
  <link rel="stylesheet" href="./css/demo.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Fournier</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="map.php" <?php if (isset($title) and $title == "Map") { ?> class="nav-link active" <?php } else { ?> class="nav-link " <?php } ?> href="#">Map</a>
          </li>
          <li class="nav-item">
            <a href="add.php" <?php if (isset($title) and $title == "Ajouter un membre") { ?> class="nav-link active" <?php } else { ?> class="nav-link " <?php } ?>>Ajouter un membre</a>
          </li>
          <li class="nav-item">
            <a href="members.php" <?php if (isset($title) and $title == "Tous les membres") { ?> class="nav-link active" <?php } else { ?> class="nav-link " <?php } ?>>Tous les membres</a>
          </li>
          <li class="nav-item">
            <a href="addMoreInformations.php" <?php if (isset($title) and $title == "Ajout des relations") { ?> class="nav-link active" <?php } else { ?> class="nav-link " <?php } ?>>Ajout des relations</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>