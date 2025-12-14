<?php
// generatorius.php - 2 MARŠRUTAI PAGAL KOORDINATES
session_start();
include("include/nustatymai.php");
include("include/functions.php");

if (!isset($_SESSION['prev']) || $_SESSION['ulevel'] == 0) {
    header("Location: index.php");
    exit;
}
$_SESSION['prev'] = "generatorius";
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Funkcija atstumo skaičiavimui tarp dviejų koordinačių taškų (Pitagoras)
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $latDiff = $lat2 - $lat1;
    $lonDiff = $lon2 - $lon1;
    
    // Pitagoro teorema: a² + b² = c²
    $distance = sqrt(($latDiff * $latDiff) + ($lonDiff * $lonDiff));
    
    // Konvertuoti iš laipsnių į kilometrus (1 laipsnis ≈ 111 km)
    $distance_km = $distance * 111;
    
    // Jei atstumas per mažas, grąžink minimalų atstumą
    return max($distance_km, 0.1);
}
//
///
//
// Funkcija kelionės laiko skaičiavimui (apytikslis)
function calculateTravelTime($distance_km) {
    if ($distance_km < 0.2) {
        return 3; // Minimalus laikas - 1 minutė pėsčiomis
    } elseif ($distance_km <= 5) {
        $minutes = round($distance_km * 60 / 25); // Mieste ~25 km/h
        return ($minutes); // Mažiausiai 2 minutės
    } else {
        return round($distance_km * 60 / 60); // Tarp miestų ~60 km/h
    }
}
//
//
//
//
function isInDirections($current_lat, $current_lon, $target_lat, $target_lon, $directions = array(), $tolerance_km = 20) {
    if (empty($directions)) return true; // jei kryptis nepasirinkta, visi tinka

    // Atstumo limitas kitai koordinatai
    //$km_per_degree_lat = 111; // maždaug 1 laipsnis latitude ≈ 111 km
    //$km_per_degree_lon = 111 * cos(deg2rad($current_lat)); // 1 laipsnis longitude priklauso nuo platumos

    foreach($directions as $dir) {
        switch($dir) {
            case 'N':
                if ($target_lat >= $current_lat && abs($target_lon - $current_lon)*111 <= $tolerance_km) return true;
                break;
            case 'S':
                if ($target_lat <= $current_lat && abs($target_lon - $current_lon)*111 <= $tolerance_km) return true;
                break;
            case 'E':
                if ($target_lon >= $current_lon && abs($target_lat - $current_lat)*111 <= $tolerance_km) return true;
                break;
            case 'W':
                if ($target_lon <= $current_lon && abs($target_lat - $current_lat)*111 <= $tolerance_km) return true;
                break;
        }
    }
    return false; // jei neatitinka nė vienos krypties
}


//
//
//
// Funkcija artimiausių objektų paieškai
function findNearestObjects($db, $current_lat, $current_lon, $tipai_str, $used_objects, $limit = 5, $directions = array()) {
    $used_str = empty($used_objects) ? '0' : implode(',', $used_objects);
    
    $sql = "SELECT o.*, ot.pavadinimas as tipo_pavadinimas
            FROM objektai o
            JOIN objektu_tipai ot ON o.tipo_id = ot.tipo_id
            WHERE o.tipo_id IN ($tipai_str)
            AND ot.ar_nakyvne = 0
            AND o.objekto_id NOT IN ($used_str)
            AND o.koordinateX != 0 AND o.koordinateY != 0";
    
    $result = mysqli_query($db, $sql);
    $objects = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        if (!isInDirections($current_lat, $current_lon, $row['koordinateX'], $row['koordinateY'], $directions)) {
            continue;
        }
        $distance = calculateDistance($current_lat, $current_lon, $row['koordinateX'], $row['koordinateY']);
        $row['distance_km'] = round($distance, 2); // Tiksliau - 2 skaičiai po kablelio
        $row['travel_time'] = round(calculateTravelTime($distance),2);
        $objects[] = $row;
    }
    
    // Sortuoti pagal atstumą
    usort($objects, function($a, $b) {
        return $a['distance_km'] <=> $b['distance_km'];
    });
    
    return array_slice($objects, 0, $limit);
}
//
//
//
// Funkcija nakvynės paieškai pagal pasirinktus tipus
function findNearestAccommodation($db, $current_lat, $current_lon, $selected_accommodation_types = array()) {
    $where_condition = "ot.ar_nakyvne = 1";
    
    // Jei vartotojas pasirinko specifinių nakvynės tipus
    if (!empty($selected_accommodation_types)) {
        $types_str = implode(',', array_map('intval', $selected_accommodation_types));
        $where_condition .= " AND o.tipo_id IN ($types_str)";
    }
    
    $sql = "SELECT o.*, ot.pavadinimas as tipo_pavadinimas
            FROM objektai o
            JOIN objektu_tipai ot ON o.tipo_id = ot.tipo_id
            WHERE $where_condition
            AND o.koordinateX != 0 AND o.koordinateY != 0";
    
    $result = mysqli_query($db, $sql);
    $accommodations = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $distance = calculateDistance($current_lat, $current_lon, $row['koordinateX'], $row['koordinateY']);
        $row['distance_km'] = round($distance, 2);
        $row['travel_time'] = calculateTravelTime($distance);
        $accommodations[] = $row;
    }
    
    // Sortuoti pagal atstumą
    usort($accommodations, function($a, $b) {
        return $a['distance_km'] <=> $b['distance_km'];
    });
    
    return !empty($accommodations) ? $accommodations[0] : null;
}
//
// Funkcija maršruto generavimui
//
//

function generateRoute($db, $pradinis_miestas, $datos_nuo, $datos_iki, $tipai_str, $route_number, $accommodation_types = array()) {

    $time_row = mysqli_fetch_assoc(mysqli_query($db, "SELECT dailyminutes FROM users WHERE userid = '{$_SESSION['userid']}'"));
    $max_laikas_dienai = $time_row['dailyminutes'];


    $dienų_skaicius = (strtotime($datos_iki) - strtotime($datos_nuo)) / 86400 + 1;
    

$offset = $route_number; // Pirmas maršrutas: 0, antras: 2

// Pirmiausia pabandome paimti VISUS atitinkančius objektus tame pačiame mieste
$city_sql = "SELECT o.*, ot.pavadinimas as tipo_pavadinimas
             FROM objektai o
             JOIN objektu_tipai ot ON o.tipo_id = ot.tipo_id
             WHERE o.tipo_id IN ($tipai_str)
             AND o.miestas LIKE '%" . mysqli_real_escape_string($db, $pradinis_miestas) . "%'
             AND o.koordinateX != 0 AND o.koordinateY != 0";
$city_res = mysqli_query($db, $city_sql);

$city_objs = [];
if ($city_res) {
    while ($r = mysqli_fetch_assoc($city_res)) {
        $city_objs[] = $r;
    }
}

// Jeigu pradiniame mieste nėra nė vieno tinkamo objekto – MESTI KLAIDĄ
if (empty($city_objs)) {
    $miestas_display = $pradinis_miestas ?? '(nežinomas miestas)';
   return "<p style='color: red;'>Pasirinktame pradiniame mieste „$miestas_display “ nėra tinkamų objektų maršrutui $route_number.</p>";

}

// Yra objektų – parenkame pagal offset
if (isset($city_objs[$offset])) {
    $pradinis_obj = $city_objs[$offset];
} else {
    // Jeigu objektų mažiau nei reikia offsetui – imame pirmą
    $pradinis_obj = $city_objs[0];
}
    
    //$pradinis_obj = mysqli_fetch_assoc($result_pradinis);
    $current_lat = $pradinis_obj['koordinateX'];
    $current_lon = $pradinis_obj['koordinateY'];
    $dabartine_vieta_id = $pradinis_obj['objekto_id'];
    $panaudoti_objektai = array($dabartine_vieta_id);

 // Duomenų masyvai lentelei ir aprašymui
    $route_data = array(); // Saugosime duomenis aprašymui
    $bendras_laikas = 0;
    $bendras_atstumas = 0;

    
    $output = "<div style='margin: 20px;'>";
   $output .= "<h2>Maršrutas ". ( $route_number + 1) . "</h2>";
     
    $output .= "<p><strong>Pradžia:</strong> " . $pradinis_obj['pavadinimas'] . " (" . $pradinis_obj['miestas'] . ")</p>";
    
    $output .= "<table border='1' cellspacing='0' cellpadding='8' width='100%'>";
    $output .= "<tr style='background-color: #f0f0f0;'>";
    $output .= "<th>Diena</th><th>Objektas</th><th>Tipas</th><th>Miestas</th><th>Atstumas</th><th>Vykst.</th><th>Lank.</th>";
    $output .= "</tr>";
    
    $bendras_laikas = 0;
    $bendras_atstumas = 0;
    
    // Pradinis objektas
    $output .= "<tr>";
    $output .= "<td><b>1</b></td>";
    $output .= "<td>" . $pradinis_obj['pavadinimas'] . "</td>";
    $output .= "<td>" . $pradinis_obj['tipo_pavadinimas'] . "</td>";
    $output .= "<td>" . $pradinis_obj['miestas'] . "</td>";
    $output .= "<td>START</td>";
    $output .= "<td>-</td>";
    $output .= "<td>" . ($pradinis_obj['rekomenduojamas_laikas'] ?: 60) . " min</td>";
    $output .= "</tr>";
    
    $bendras_laikas = $pradinis_obj['rekomenduojamas_laikas'] ?: 60;
    
    for ($diena = 1; $diena <= $dienų_skaicius; $diena++) {
        $dienos_laikas = ($diena == 1) ? $bendras_laikas : 0;
        
        while ($dienos_laikas < $max_laikas_dienai) {

            $directions = isset($_POST['kryptys']) ? $_POST['kryptys'] : array();
            $nearest_objects = findNearestObjects($db, $current_lat, $current_lon, $tipai_str, $panaudoti_objektai, 3, $directions);

            //$nearest_objects = findNearestObjects($db, $current_lat, $current_lon, $tipai_str, $panaudoti_objektai, 3);
            
            if (empty($nearest_objects)) {
                break;
            }
            
            $obj = $nearest_objects[0]; // Imsime artimiausią
            
            $keliones_laikas = $obj['travel_time'];
            $lankymo_laikas = $obj['rekomenduojamas_laikas'] ?: 60;
            $naujas_laikas = $dienos_laikas + $lankymo_laikas + $keliones_laikas;
            
            if ($naujas_laikas > $max_laikas_dienai && $dienos_laikas > 60) {
                break;
            }
            
            $output .= "<tr>";
            $output .= "<td><b>$diena</b></td>";
            $output .= "<td>" . $obj['pavadinimas'] . "</td>";
            $output .= "<td>" . $obj['tipo_pavadinimas'] . "</td>";
            $output .= "<td>" . $obj['miestas'] . "</td>";
            $output .= "<td>" . $obj['distance_km'] . " km</td>";
            $output .= "<td>" . $keliones_laikas . " min</td>";
            $output .= "<td>" . $lankymo_laikas . " min</td>";
            $output .= "</tr>";
            
            $bendras_laikas += $lankymo_laikas + $keliones_laikas;
            $bendras_atstumas += $obj['distance_km'];
            $dienos_laikas = $naujas_laikas;
            
            $current_lat = $obj['koordinateX'];
            $current_lon = $obj['koordinateY'];
            $dabartine_vieta_id = $obj['objekto_id'];
            $panaudoti_objektai[] = $obj['objekto_id'];
        }
        
        // NAKVYNĖ
        if ($diena < $dienų_skaicius) {
            $accommodation = findNearestAccommodation($db, $current_lat, $current_lon, $accommodation_types);
            
            if ($accommodation) {
                $output .= "<tr style='background-color: #fffacd;'>";
                $output .= "<td>🌙</td>";
                $output .= "<td>" . $accommodation['pavadinimas'] . "</td>";
                $output .= "<td>" . $accommodation['tipo_pavadinimas'] . "</td>"; // Tikras tipas!
                $output .= "<td>" . $accommodation['miestas'] . "</td>";
                $output .= "<td>" . $accommodation['distance_km'] . " km</td>";
                $output .= "<td>" . $accommodation['travel_time'] . " min</td>";
                $output .= "<td>-</td>";
                $output .= "</tr>";
                
                $bendras_atstumas += $accommodation['distance_km'];
                $current_lat = $accommodation['koordinateX'];
                $current_lon = $accommodation['koordinateY'];
            }
        }
        
        // DIENOS SUVESTINĖ
        $output .= "<tr style='background-color: #e8f5e9; font-weight: bold;'>";
        $output .= "<td colspan='5' style='text-align: right;'>Dienos $diena suvestinė:</td>";
        $output .= "<td>" . round($dienos_laikas / 60, 1) . " val</td>";
        $output .= "<td></td>";
        $output .= "</tr>";
    }
    
    $output .= "</table>";
    
    $output .= "<p style='margin-top: 10px; font-size: 14px;'>";
    $output .= "<b>Bendras atstumas:</b> " . round($bendras_atstumas, 1) . " km<br>";
    $output .= "<b>Bendras laikas:</b> " . round($bendras_laikas / 60, 1) . " val<br>";
    $output .= "<b>Objektų skaičius:</b> " . count($panaudoti_objektai);
    $output .= "</p>";
    
    $output .= "</div>";
    
    return $output;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Maršruto generatorius</title>
    <link href="include/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
    <table class="center" style="width:80%">
        <tr><td>
            <center><img src="include/top1.png"></center>
        </td></tr>
        <tr><td>
<?php include("include/meniu.php"); ?>
            <h1>Automatinis maršruto generatorius</h1>
            <p style="text-align: center; color: #666;">Generuoja 2 skirtingus maršruto variantus naudodamas tikslias GPS koordinates</p>
            
           <div style="max-width: 500px; margin: 20px auto;">
                <form method="POST">
                    <h3>Kelionės parametrai</h3>
                    
                    <p>
                        <label>Pradinis miestas:</label><br>
                        <input type="text" name="pradinis_miestas" required placeholder="pvz: Vilnius" value="<?php echo isset($_POST['pradinis_miestas']) ? htmlspecialchars($_POST['pradinis_miestas']) : ''; ?>">
                    </p>
                    
                    <p>
                        <label>Pradžia:</label> <label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Pabaiga:</label> <br>
                        <input type="date" name="datos_nuo" required value="<?php echo isset($_POST['datos_nuo']) ? $_POST['datos_nuo'] : date('Y-m-d'); ?>">

                        &nbsp;
                        <input type="date" name="datos_iki" required value="<?php echo isset($_POST['datos_iki']) ? $_POST['datos_iki'] : date('Y-m-d', strtotime('+2 days')); ?>">
                    </p>

                    <table> <td>
                    <h3>Objektų tipai</h3>
<?php
$tipai_result = mysqli_query($db, "SELECT * FROM objektu_tipai WHERE ar_nakyvne = 0");
while ($tipas = mysqli_fetch_assoc($tipai_result)) {
    $checked = (isset($_POST['tipai']) && in_array($tipas['tipo_id'], $_POST['tipai'])) ? 'checked' : '';
    echo "<label><input type='checkbox' name='tipai[]' value='" . $tipas['tipo_id'] . "' $checked> " . $tipas['pavadinimas'] . "</label><br>";
}
?>
                    </td> 
                    <td><h3>Nakvynės tipai</h3>
<?php
$nakv_result = mysqli_query($db, "SELECT * FROM objektu_tipai WHERE ar_nakyvne = 1");
while ($nakv_tipas = mysqli_fetch_assoc($nakv_result)) {
    $checked = (isset($_POST['nakvynes']) && in_array($nakv_tipas['tipo_id'], $_POST['nakvynes'])) ? 'checked' : ''; 
    echo "<label><input type='checkbox' name='nakvynes[]' value='" . $nakv_tipas['tipo_id'] . "' $checked> " . $nakv_tipas['pavadinimas'] . "</label><br>";
}
?>
                   </td> 
<td><h3>Kelionės kryptys</h3>
<label><input type="checkbox" name="kryptys[]" value="N"> Šiaurė</label><br>
<label><input type="checkbox" name="kryptys[]" value="S"> Pietūs</label><br>
<label><input type="checkbox" name="kryptys[]" value="E"> Rytai</label><br>
<label><input type="checkbox" name="kryptys[]" value="W"> Vakarai</label><br></td> </table>

                    <p style="text-align: center;">
                        <input type="submit" name="generuoti" value="Generuoti">
                    </p>
                </form>
            </div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generuoti'])) {
    $pradinis_miestas = trim($_POST['pradinis_miestas']);
    $datos_nuo = $_POST['datos_nuo'];
    $datos_iki = $_POST['datos_iki'];
    $pasirinkti_tipai = isset($_POST['tipai']) ? $_POST['tipai'] : array();
    
    $dienų_skaicius = (strtotime($datos_iki) - strtotime($datos_nuo)) / 86400 + 1;
    
    if (empty($pradinis_miestas)) {
        echo "<p style='color: red;'>Įveskite pradinį miestą!</p>";
    } 
    elseif (!empty($pasirinkti_tipai)) {
        $tipai_str = implode(',', array_map('intval', $pasirinkti_tipai));
        $nakvynes_tipai = isset($_POST['nakvynes']) ? $_POST['nakvynes'] : array();
        
        echo "<div style='margin: 20px;'>";
        echo "<h2>Sugeneruoti maršrutai</h2>";
        echo "<p><b>Pradinis miestas:</b> $pradinis_miestas | <b>Trukmė:</b> $dienų_skaicius d. | <b>Apskaičiuota pagal GPS koordinates</b></p>";
        
        // Rodyti pasirinktus nakvynės tipus
        if (!empty($nakvynes_tipai)) {
            $nakv_names = array();
            foreach ($nakvynes_tipai as $tipo_id) {
                $result = mysqli_query($db, "SELECT pavadinimas FROM objektu_tipai WHERE tipo_id = $tipo_id");
                if ($row = mysqli_fetch_assoc($result)) {
                    $nakv_names[] = $row['pavadinimas'];
                }
            }
            echo "<p><b>Priimtinos nakvynės:</b> " . implode(', ', $nakv_names) . "</p>";
        }
        
        echo "<hr>";
        
        // Generuoti du skirtingus maršrutus
        echo "<div style='display: flex; gap: 20px;'>";
        echo "<div style='flex: 1;'>";
        echo generateRoute($db, $pradinis_miestas, $datos_nuo, $datos_iki, $tipai_str, 0, $nakvynes_tipai);
        echo "</div>";
        echo "<div style='flex: 1;'>";
        echo generateRoute($db, $pradinis_miestas, $datos_nuo, $datos_iki, $tipai_str, 1, $nakvynes_tipai);
        echo "</div>";
        echo "</div>";
        
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>Pasirinkite tipus!</p>";
    }
}
?>

         </td></tr>
    </table>
</body>
</html>
<?php mysqli_close($db); ?>
