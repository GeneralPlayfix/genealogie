<?php
$title = "Map";
require_once "./component/header.php";
require_once "./component/bdd.php";
require_once "./functions/sql.php";
$members = getAllMembersWithContactDetails($dbh);
$columns = array_column($members, 'generationnumber');
array_multisort($columns, SORT_ASC, $members);
?>
<div id="map"></div>
<?php
$json = file_get_contents("test.json");
$jsonArray = json_decode($json, true);
   
?>
<script>
    var json = {
    "1":"blue",
    "2":"cyan",
    "3":"green",
    "4":"pink",
    "5":"orange",
    "6":"red",
    "7":"grey",
    "8":"darkblue",
    "9":"lightcoral",
    "10":"purple",
    "11":"black",
    "12":"salmon",
    "13":"darkgreen",
    "14":"darkred",
    "15":"lightblue",
    "16":"darkpink",
    "17":"lightgreen",
    "18":"lightgrey",
    "19":"white"
}
    var map = L.map("map").setView([48.833, 2.333], 5);


    L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {}).addTo(map);
    var geojson = <?php echo json_encode($members, JSON_HEX_TAG); ?>;
   
     

    var cats = [];
    let tempCats = [];
    var cats = [];
    for (var i = 0; i < geojson.length; i++) {
        let i2 = i;
        var user = geojson[i2];
        var cat = getCat(cats, user.generationnumber);
        if (cat === undefined) {
            cat = {
                "people": [],
                "id": "cat" + i2,
                "label": "Génération : " + user.generationnumber,
            }
            cats.push(cat);
        }
        cat.people.push(user)
    }
    // for (user of geojson) {
    //     var cat;
    //     let generationNumber = user.generationnumber
    //     console.log(`La taille de cats ${cats.length}`)
    //     if (tempCats.length == 0) {
    //         cat = {
    //             "people": [],
    //             "id": "cat" + generationNumber,
    //             "label": "Génération : " + generationNumber
    //         }
    //         cats.push(cat)
    //         tempCats.push(generationNumber)
    //     }
    //     if (!tempCats.includes(generationNumber)) {
    //         cat = {
    //             "people": [],
    //             "id": "cat" + generationNumber,
    //             "label": "Génération : " + generationNumber
    //         }
    //         cats.push(cat)
    //         tempCats.push(generationNumber)
    //     }
    // }


    function getCat(cats, cat) {
        for (var i = 0; i < cats.length; i++) {
            if (cats[i]["label"] == "Génération : " + cat) {
                return cats[i];
            }
        }
        return;
    }
    // for (var i = 0; i < geojson.length; i++) {

    //     var cat = getCat(cats, geojson[i].generationnumber);
    //     console.log(i)
    //     if (cat === undefined) {
    //         cat = {
    //             "people": createUser(),
    //             "id": "cat" + i,
    //             "label": "Generation " + geojson[i].generationnumber
    //         }
    //         cats.push(cat);
    //     }
    //     // cat["people"].addData(geojson[i]);
    // }

    // function getCat(cats, generationnumber) {
    //     for (cat of cats) {
    //         console.log(cat.label)
    //         if (cat.label != "Generation " + generationnumber){
    //         return;
    //         }else{
    //             return 1;
    //         };
    //     }
    // }
    function createUser(geojson) {

        var generationNumber = geojson.generationnumber;
        //delete require.cache[require.resolve(`./test.json`)];

        var html = '<div class="globalMapMarkerPopup">';
        //Ceci sont les remarques</span>

        if (geojson.lastname) {
            html += '<span class="rightInfo"><b> Nom :</b>' + geojson.lastname + '</span>';
        }
        if (geojson.firstname) {
            html += '<span class="leftInfo"><b>Prénom :</b>' + geojson.firstname + '</span><br/>';
        }
        if (geojson.birthplace) {
            html += '<span class="allLineInfo"><b>Lieu de naissance :</b> ' + geojson.birthplace + '</span><br/>';
        }
        if (geojson.birthplace) {
            html += '<span class="allLineInfo"><b>Date de naissance :</b> ' + geojson.birthdate + '</span><br/>';
        }
        if (geojson.parents) {
            html += '<span class="rightInfo"><b> Mère :</b> mère </span>';
        }
        if (geojson.parents) {
            html += '<span class="leftInfo"><b> Mère :</b> père </span>';
        }
        if (geojson.gender) {
            html += '<span class="rightInfo"><b>Genre : </b>' + geojson.gender + '</span>';
        }
        if (geojson.generationnumber) {
            html += '<span class="leftInfo"><b>Génération : </b> ' + geojson.generationnumber + '</span><br/>';
        }
        if (geojson.weddingpartner) {
            html += '<span class="rightInfo"><b>Mari/Femme :</b>' + geojson.weddingpartner + '</span>';
        }
        if (geojson.weddingdate) {
            html += '<span class="leftInfo"><b>Date mariage : </b> ' + geojson.weddingdate + '</span><br/>';
        }
        if (geojson.weddingplace) {
            html += '<span class="allLineInfo"><b>Lieu mariage : </b>' + geojson.weddingplace + '</span><br/>';
        }
        if (geojson.children) {
            html += '<span class="allLineInfo"><b>Enfants : </b> ' + geojson.children + '</span><br/>';
        }
        if (geojson.deathdate) {
            html += '<span class="rightInfo"><b>Date mort : </b> ' + geojson.deathdate + '</span>';
        }
        if (geojson.deathplace) {
            html += '<span class="leftInfo"><b>Lieu mort : </b>' + geojson.deathplace + '</span><br/>';
        }
        if (geojson.remarks) {
            html += '<span class="allLineInfo"><b>Remarques : </b>' + geojson.remarks + '</span>';
        }
        html += "</div>"
        return html;
    }


    // var stamen = new L.StamenTileLayer("toner-lite");

    // var map = new L.Map("map", {
    //     center: new L.LatLng(48.825, 2.27),
    //     zoom: 15,
    //     layers: [stamen],
    // });

    var command = L.control({
        position: 'topright'
    });
    command.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'command');
        div.innerHTML += '<div style="text-align:center;"><span style="font-size:18px;">Numéro de génération</span><br /></div>';
        for (var i = 0; i < cats.length; i++) {
            div.innerHTML += `<form class="catForm"><input id="${cats[i]["id"]}" type="checkbox"/><label for="${cats[i]["id"]}">${cats[i]["label"]}</label> </form>`;
        }
        return div;
    };
    command.addTo(map);
    for (var i = 0; i < cats.length; i++) {
        document.getElementById(cats[i]["id"]).addEventListener("click", handleCommand, false);
    }
    var arrayOfMarker = [];

    function handleCommand() {
        var selectedCat;
        for (var i = 0; i < cats.length; i++) {
            if (cats[i]["id"] === this.id) {
                selectedCat = cats[i];
                break;
            }
        }
       
        
        if (this.checked) {
            for (let user of selectedCat.people) {
                var finalIcon = L.icon({
                    iconUrl: `images/${json[user.generationnumber]}.png`,
                    //shadowUrl: 'icon-shadow.png',
                    iconSize: [25, 35],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [45, 25],
                    shadowAnchor: [2, 50],
                });
                let html = createUser(user);
                let contactDetails = user.contactdetails;
                let lat = parseFloat(contactDetails.split(",")[0]);
                let long = parseFloat(contactDetails.split(",")[1])
                let marker = L.marker([lat, long], { icon : finalIcon }).addTo(map).bindPopup(html,{maxWidth: "auto"});
                arrayOfMarker.push({"user":user,"marker": marker})
            }
        } else { 
            for (marker of arrayOfMarker) {
                console.log(selectedCat.label.split("Génération : ")[1]);
                console.log(marker.user.generationnumber)
                if(marker.user.generationnumber == selectedCat.label.split("Génération : ")[1]){
                    marker.marker.remove();     
                    
                }
            }
        }
    }
    
</script>
<style>
    .command {
        padding: 4px 6px;
        background: white;
        font: 14px/16px Arial, Helvetica, sans-serif;
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        min-width: 200px;
    }
    .catForm{
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 2px;
    }
    .catForm input{
        width: auto;
    }
    .catForm label{
        margin-left: 1%;
    }
    </style>
</body>

</html>