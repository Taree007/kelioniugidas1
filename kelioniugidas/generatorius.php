<?php
// generatorius.php - 2 MARŠRUTAI PAGAL KOORDINATES SU APRAŠYMAIS
session_start();
include("include/nustatymai.php");
include("include/functions.php");

if (!isset($_SESSION['prev']) || $_SESSION['ulevel'] == 0) {
    header("Location: index.php");
    exit;
}
$_SESSION['prev'] = "generatorius";
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Funkcija atstumo skaičiavimui
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $latDiff = $lat2 - $lat1;
    $lonDiff = $lon2 - $lon1;
    $distance = sqrt(($latDiff * $latDiff) + ($lonDiff * $lonDiff));
    $distance_km = $distance * 111;
    return max($distance_km, 0.1);
}

// Funkcija kelionės laiko skaičiavimui
function calculateTravelTime($distance_km) {
    if ($distance_km < 0.2) {
        return 3;
    } elseif ($distance_km <= 5) {
        return round($distance_km * 60 / 25);
    } else {
        return round($distance_km * 60 / 60);
    }
}

function isInDirections($current_lat, $current_lon, $target_lat, $target_lon, $directions = array(), $tolerance_km = 20) {
    if (empty($directions)) return true;
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
    return false;
}

// Artimiausių objektų paieška
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
        $row['distance_km'] = round($distance, 2);
        $row['travel_time'] = round(calculateTravelTime($distance),2);
        $objects[] = $row;
    }
    
    usort($objects, function($a, $b) {
        return $a['distance_km'] <=> $b['distance_km'];
    });
    
    return array_slice($objects, 0, $limit);
}

// Nakvynės paieška
function findNearestAccommodation($db, $current_lat, $current_lon, $selected_accommodation_types = array()) {
    $where_condition = "ot.ar_nakyvne = 1";
    
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
    
    usort($accommodations, function($a, $b) {
        return $a['distance_km'] <=> $b['distance_km'];
    });
    
    return !empty($accommodations) ? $accommodations[0] : null;
}

// Kelionės aprašymo generavimas
function generateDescription($route_data, $dienų_skaicius) {
    $output = "<table class=\"center\" border=\"1\" cellpadding=\"5\" cellspacing=\"0\">";
    $output .= "<tr><td><b>Detalus kelionės aprašymas</b></td></tr>";
    $output .= "<tr><td>";
    
    for ($diena = 1; $diena <= $dienų_skaicius; $diena++) {
        $dienos_objektai = array_filter($route_data, function($item) use ($diena) {
            return $item['day'] == $diena && $item['type'] == 'object';
        });
        $nakvyne = array_filter($route_data, function($item) use ($diena) {
            return $item['day'] == $diena && $item['type'] == 'accommodation';
        });
        
        if (!empty($dienos_objektai)) {
            $output .= "<b>Diena $diena aplankysime:</b><br>";
            
            $objektu_sarasas = array_values($dienos_objektai);
            for ($i = 0; $i < count($objektu_sarasas); $i++) {
                $obj = $objektu_sarasas[$i];
                $output .= "<b>" . $obj['pavadinimas'] . "</b> (" . $obj['tipo_pavadinimas'] . ")<br>";
                if (!empty($obj['aprasymas'])) {
                    $output .= $obj['aprasymas'] . "<br>";
                }
                
                if ($i < count($objektu_sarasas) - 1) {
                    $output .= "<i>Toliau keliausime prie</i><br>";
                }
            }
            
            if (!empty($nakvyne)) {
                $acc = array_values($nakvyne)[0];
                $output .= "<br><b>Dienos gale nakvosim:</b><br>";
                $output .= "<b>" . $acc['pavadinimas'] . "</b> (" . $acc['tipo_pavadinimas'] . ")<br>";
                if (!empty($acc['aprasymas'])) {
                    $output .= $acc['aprasymas'] . "<br>";
                }
            }
            $output .= "<br>";
        }
    }
    
    $output .= "</td></tr></table><br>";
    return $output;
}

// Maršruto generavimas
function generateRoute($db, $pradinis_miestas, $datos_nuo, $datos_iki, $tipai_str, $route_number, $accommodation_types = array()) {

    $time_row = mysqli_fetch_assoc(mysqli_query($db, "SELECT dailyminutes FROM users WHERE userid = '{$_SESSION['userid']}'"));
    $max_laikas_dienai = $time_row['dailyminutes'];

    $dienų_skaicius = (strtotime($datos_iki) - strtotime($datos_nuo)) / 86400 + 1;
    
    $offset = $route_number;

    // Pradinio objekto paieška
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
    
    if (empty($city_objs)) {
        return "<p style='color: red;'>Mieste $pradinis_miestas nėra tinkamų objektų.</p>";
    }
    
    $pradinis_obj = isset($city_objs[$offset]) ? $city_objs[$offset] : $city_objs[0];
    
    $current_lat = $pradinis_obj['koordinateX'];
    $current_lon = $pradinis_obj['koordinateY'];
    $panaudoti_objektai = array($pradinis_obj['objekto_id']);
    
    $route_data = array();
    $bendras_laikas = 0;
    $bendras_atstumas = 0;
    
    $output = "<h2>Maršrutas ". ($route_number + 1) . "</h2>";
     
    $output .= "<table border='1' cellpadding='5' cellspacing='0' class=\"center\">";
    $output .= "<tr style='background-color: #f0f0f0;'>";
    $output .= "<th>Diena</th><th>Objektas</th><th>Tipas</th><th>Miestas</th>";
    $output .= "<th>Atstumas</th><th>Kelionės laikas</th><th>Lankymo laikas</th>";
    $output .= "</tr>";
    
    // Pradinis objektas
    $lankymo_laikas = $pradinis_obj['rekomenduojamas_laikas'] ?: 60;
    $output .= "<tr>";
    $output .= "<td><b>1</b></td>";
    $output .= "<td>" . $pradinis_obj['pavadinimas'] . "</td>";
    $output .= "<td>" . $pradinis_obj['tipo_pavadinimas'] . "</td>";
    $output .= "<td>" . $pradinis_obj['miestas'] . "</td>";
    $output .= "<td>0 km</td>";
    $output .= "<td>0 min</td>";
    $output .= "<td>" . $lankymo_laikas . " min</td>";
    $output .= "</tr>";
    
    $route_data[] = array(
        'type' => 'object',
        'day' => 1,
        'pavadinimas' => $pradinis_obj['pavadinimas'],
        'tipo_pavadinimas' => $pradinis_obj['tipo_pavadinimas'],
        'aprasymas' => $pradinis_obj['aprasymas'] ?? ''
    );
    
    $dienos_laikas = $lankymo_laikas;
    $bendras_laikas += $lankymo_laikas;
    
    for ($diena = 1; $diena <= $dienų_skaicius; $diena++) {
        if ($diena > 1) {
            $dienos_laikas = 0;
        }
        
        while ($dienos_laikas < $max_laikas_dienai) {
            $nearest_objects = findNearestObjects($db, $current_lat, $current_lon, $tipai_str, $panaudoti_objektai, 3);
            
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
            
            $route_data[] = array(
                'type' => 'object',
                'day' => $diena,
                'pavadinimas' => $obj['pavadinimas'],
                'tipo_pavadinimas' => $obj['tipo_pavadinimas'],
                'aprasymas' => $obj['aprasymas'] ?? ''
            );
            
            $bendras_laikas += $lankymo_laikas + $keliones_laikas;
            $bendras_atstumas += $obj['distance_km'];
            $dienos_laikas = $naujas_laikas;
            
            $current_lat = $obj['koordinateX'];
            $current_lon = $obj['koordinateY'];
            $panaudoti_objektai[] = $obj['objekto_id'];
        }
        
        // Nakvynė
        if ($diena < $dienų_skaicius) {
            $accommodation = findNearestAccommodation($db, $current_lat, $current_lon, $accommodation_types);
            
            if ($accommodation) {
                $output .= "<tr style='background-color: #fffacd;'>";
                $output .= "<td>Nakvynė</td>";
                $output .= "<td>" . $accommodation['pavadinimas'] . "</td>";
                $output .= "<td>" . $accommodation['tipo_pavadinimas'] . "</td>";
                $output .= "<td>" . $accommodation['miestas'] . "</td>";
                $output .= "<td>" . $accommodation['distance_km'] . " km</td>";
                $output .= "<td>" . $accommodation['travel_time'] . " min</td>";
                $output .= "<td>-</td>";
                $output .= "</tr>";
                
                $route_data[] = array(
                    'type' => 'accommodation',
                    'day' => $diena,
                    'pavadinimas' => $accommodation['pavadinimas'],
                    'tipo_pavadinimas' => $accommodation['tipo_pavadinimas'],
                    'aprasymas' => $accommodation['aprasymas'] ?? ''
                );
                
                $bendras_atstumas += $accommodation['distance_km'];
                $current_lat = $accommodation['koordinateX'];
                $current_lon = $accommodation['koordinateY'];
            }
        }
        
        // Dienos suvestinė
        $output .= "<tr style='background-color: #e8f5e9; font-weight: bold;'>";
        $output .= "<td colspan='5' style='text-align: right;'>Dienos $diena suvestinė:</td>";
        $output .= "<td>" . round($dienos_laikas / 60, 1) . " val</td>";
        $output .= "<td></td>";
        $output .= "</tr>";
    }
    
    $output .= "</table>";
    
    $output .= "<p>";
    $output .= "<b>Bendras atstumas:</b> " . round($bendras_atstumas, 1) . " km<br>";
    $output .= "<b>Bendras laikas:</b> " . round($bendras_laikas / 60, 1) . " val<br>";
    $output .= "<b>Objektų skaičius:</b> " . count($panaudoti_objektai);
    $output .= "</p>";
    
    // Pridedame aprašymą
    $output .= generateDescription($route_data, $dienų_skaicius);
    
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
            <p style="text-align: center; color: #666;">Generuoja 2 skirtingus maršruto variantus su aprašymais</p>
            
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
        
        echo "<h2>Sugeneruoti maršrutai</h2>";
        echo "<p><b>Pradinis miestas:</b> $pradinis_miestas | <b>Trukmė:</b> $dienų_skaicius d.</p>";
        
        if (!empty($nakvynes_tipai)) {
            $nakv_names = array();
            foreach ($nakvynes_tipai as $tipo_id) {
                $result = mysqli_query($db, "SELECT pavadinimas FROM objektu_tipai WHERE tipo_id = $tipo_id");
                if ($row = mysqli_fetch_assoc($result)) {
                    $nakv_names[] = $row['pavadinimas'];
                }
            }
            echo "<p><b>Nakvynės:</b> " . implode(', ', $nakv_names) . "</p>";
        }
        
        echo "<hr>";
        
        echo "<table class=\"center\"><tr>";
        echo "<td style=\"width:50%\">" . generateRoute($db, $pradinis_miestas, $datos_nuo, $datos_iki, $tipai_str, 0, $nakvynes_tipai) . "</td>";
        echo "<td style=\"width:50%\">" . generateRoute($db, $pradinis_miestas, $datos_nuo, $datos_iki, $tipai_str, 1, $nakvynes_tipai) . "</td>";
        echo "</tr></table>";
        
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
