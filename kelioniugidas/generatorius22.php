<?php
// generatorius.php - SU 3 MARŠRUTŲ VARIANTAIS
session_start();
include("include/nustatymai.php");
include("include/functions.php");

if (!isset($_SESSION['prev']) || $_SESSION['ulevel'] == 0) {
    header("Location: index.php");
    exit;
}
$_SESSION['prev'] = "generatorius";
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Maršruto generatorius</title>
    <link href="include/styles.css" rel="stylesheet" type="text/css">
    <style>
        .variant-box {
            border: 2px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            background-color: #fafafa;
        }
        .variant-box:hover {
            border-color: #4CAF50;
            background-color: #f0f8f0;
        }
        .variant-header {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
        }
        .nakvyne-row {
            background-color: #fff9e6;
            font-style: italic;
        }
    </style>
</head>
<body>
    <table class="center">
        <tr><td>
            <center><img src="include/top1.png"></center>
        </td></tr>
        <tr><td>
<?php
include("include/meniu.php");
?>
            <h1>Automatinis maršruto generatorius</h1>
            <p>Sistema pasiūlys 3 maršrutų variantus - pasirinkite labiausiai tinkantį!</p>


            <div style="max-width: 500px; margin: 20px auto; background-color: white; padding: 20px; border: 1px solid #ddd;">
                <form method="POST" action="generatorius.php">
                    <h3>Kelionės parametrai</h3>
                    
                    <label>Pradžios data:</label>
                    <input type="date" name="datos_nuo" required value="<?php echo date('Y-m-d'); ?>">
                    
                    <label>Pabaigos data:</label>
                    <input type="date" name="datos_iki" required value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
                    
                    <h3>Objektų tipai</h3>
                    <p style="font-size: 12px; color: #666;">(Nakvynės pridedamos automatiškai)</p>
<?php
$tipai_result = mysqli_query($db, "SELECT * FROM objektu_tipai WHERE ar_nakyvne = 0 ORDER BY pavadinimas");
while ($tipas = mysqli_fetch_assoc($tipai_result)) {
    echo "<label>";
    echo "<input type='checkbox' name='tipai[]' value='" . $tipas['tipo_id'] . "'> ";
    echo $tipas['pavadinimas'];
    echo "</label><br>";
}
?>
                    
                    <p style="text-align: center; margin-top: 20px;">
                        <input type="submit" name="generuoti" value="Generuoti variantus">
                    </p>
                </form>
            </div>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generuoti'])) {
    $datos_nuo = $_POST['datos_nuo'];
    $datos_iki = $_POST['datos_iki'];
    $pasirinkti_tipai = isset($_POST['tipai']) ? $_POST['tipai'] : array();
    
    $dienų_skaicius = (strtotime($datos_iki) - strtotime($datos_nuo)) / 86400 + 1;
    
    if (!empty($pasirinkti_tipai)) {
        $tipai_str = implode(',', array_map('intval', $pasirinkti_tipai));
        
        // GAUNAME OBJEKTUS
        $sql_objektai = "SELECT o.*, ot.pavadinimas as tipo_pavadinimas,
                AVG(a.ivertinimas) as vid_ivertinimas
                FROM objektai o
                LEFT JOIN objektu_tipai ot ON o.tipo_id = ot.tipo_id
                LEFT JOIN atsiliepimai a ON o.objekto_id = a.objekto_id
                WHERE o.tipo_id IN ($tipai_str) 
                AND ot.ar_nakyvne = 0
                GROUP BY o.objekto_id
                ORDER BY vid_ivertinimas DESC";
        
        $result_objektai = mysqli_query($db, $sql_objektai);
        
        if ($result_objektai && mysqli_num_rows($result_objektai) > 0) {
            $visi_objektai = array();
            while ($obj = mysqli_fetch_assoc($result_objektai)) {
                $visi_objektai[] = $obj;
            }
            
            // 3 VARIANTAI
            $variantai = array(
                array('pavadinimas' => 'Trumpas maršrutas', 'per_diena' => 2, 'aprasymas' => 'Atsipalaidavęs tempas - 2 objektai per dieną'),
                array('pavadinimas' => 'Vidutinis maršrutas', 'per_diena' => 3, 'aprasymas' => 'Subalansuotas - 3 objektai per dieną'),
                array('pavadinimas' => 'Intensyvus maršrutas', 'per_diena' => 4, 'aprasymas' => 'Pilna programa - 4 objektai per dieną')
            );
            
            echo "<div style='margin: 20px;'>";
            echo "<h2>Pasirinkite maršruto variantą:</h2>";
            echo "<p><b>Kelionės trukmė:</b> $dienų_skaicius d. | <b>Pasirinkti tipai:</b> ";
            
            $tipai_pavadinimai = array();
            foreach ($pasirinkti_tipai as $tipo_id) {
                $r = mysqli_query($db, "SELECT pavadinimas FROM objektu_tipai WHERE tipo_id = $tipo_id");
                if ($row = mysqli_fetch_assoc($r)) {
                    $tipai_pavadinimai[] = $row['pavadinimas'];
                }
            }
            echo implode(', ', $tipai_pavadinimai) . "</p>";
            
            // GENERUOJAME 3 VARIANTUS
            foreach ($variantai as $idx => $variantas) {
                $objektu_per_diena = $variantas['per_diena'];
                $bendras_objektu = $objektu_per_diena * $dienų_skaicius;
                
                // Apribojame objektų kiekį
                $marsruto_objektai = array_slice($visi_objektai, 0, min($bendras_objektu, count($visi_objektai)));
                
                if (count($marsruto_objektai) == 0) continue;
                
                echo "<div class='variant-box'>";
                echo "<div class='variant-header'>";
                echo "VARIANTAS " . ($idx + 1) . ": " . $variantas['pavadinimas'];
                echo "</div>";
                echo "<p>" . $variantas['aprasymas'] . "</p>";
                
                echo "<table border='1' cellspacing='0' cellpadding='8' width='100%'>";
                echo "<tr style='background-color: #f0f0f0;'>";
                echo "<th>Diena</th><th>Objektas</th><th>Tipas</th><th>Miestas</th><th>Laikas</th><th>★</th>";
                echo "</tr>";
                
                $bendras_laikas = 0;
                $obj_idx = 0;
                
                for ($diena = 1; $diena <= $dienų_skaicius; $diena++) {
                    $paskutinis_miestas = '';
                    
                    // OBJEKTAI ŠIAI DIENAI
                    for ($i = 0; $i < $objektu_per_diena && $obj_idx < count($marsruto_objektai); $i++) {
                        $obj = $marsruto_objektai[$obj_idx++];
                        
                        echo "<tr>";
                        echo "<td style='text-align: center;'><b>$diena</b></td>";
                        echo "<td>" . $obj['pavadinimas'] . "</td>";
                        echo "<td>" . $obj['tipo_pavadinimas'] . "</td>";
                        echo "<td>" . $obj['miestas'] . "</td>";
                        echo "<td>" . $obj['rekomenduojamas_laikas'] . " min</td>";
                        
                        $rating = $obj['vid_ivertinimas'] ? number_format($obj['vid_ivertinimas'], 1) : "-";
                        echo "<td>" . $rating . "</td>";
                        echo "</tr>";
                        
                        $bendras_laikas += $obj['rekomenduojamas_laikas'];
                        $paskutinis_miestas = $obj['miestas'];
                    }
                    
                    // NAKVYNĖ
                    if ($diena < $dienų_skaicius && $paskutinis_miestas) {
                        $sql_nakv = "SELECT o.*, AVG(a.ivertinimas) as vid_ivertinimas
                                    FROM objektai o
                                    LEFT JOIN atsiliepimai a ON o.objekto_id = a.objekto_id
                                    WHERE o.tipo_id = 5
                                    AND o.miestas = '" . mysqli_real_escape_string($db, $paskutinis_miestas) . "'
                                    GROUP BY o.objekto_id
                                    ORDER BY vid_ivertinimas DESC
                                    LIMIT 1";
                        
                        $result_nakv = mysqli_query($db, $sql_nakv);
                        
                        if ($result_nakv && mysqli_num_rows($result_nakv) > 0) {
                            $nakvyne = mysqli_fetch_assoc($result_nakv);
                            
                            echo "<tr class='nakvyne-row'>";
                            echo "<td style='text-align: center;'>🌙</td>";
                            echo "<td>🏨 " . $nakvyne['pavadinimas'] . "</td>";
                            echo "<td>Nakvynė</td>";
                            echo "<td>" . $nakvyne['miestas'] . "</td>";
                            echo "<td>-</td>";
                            $nakv_rating = $nakvyne['vid_ivertinimas'] ? number_format($nakvyne['vid_ivertinimas'], 1) : "-";
                            echo "<td>" . $nakv_rating . "</td>";
                            echo "</tr>";
                        }
                    }
                }
                
                echo "</table>";
                
                echo "<p style='margin-top: 10px;'>";
                echo "<b>Bendras laikas objektuose:</b> " . round($bendras_laikas / 60, 1) . " val.";
                echo " | <b>Objektų:</b> " . count($marsruto_objektai);
                echo "</p>";
                
                echo "</div>";
            }
            
            echo "</div>";
            
        } else {
            echo "<p class='message error'>Nerasta objektų pagal pasirinktus tipus!</p>";
        }
    } else {
        echo "<p class='message error'>Pasirinkite bent vieną objekto tipą!</p>";
    }
}
?>
        </td></tr>
    </table>
</body>
</html>
<?php mysqli_close($db); ?>
