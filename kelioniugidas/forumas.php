<?php
// forumas.php - forumo sistema
session_start();

// Sesijos kontrolė
if (!isset($_SESSION['user']) || $_SESSION['ulevel'] == 0) {
    header("Location: logout.php");
    exit;
}

include("include/nustatymai.php");
include("include/functions.php");
$_SESSION['prev'] = "forumas";

// Veiksmų apdorojimas
$veiksmas = $_GET['veiksmas'] ?? $_POST['veiksmas'] ?? '';
$pranesimo_id = intval($_GET['id'] ?? $_POST['pranesimo_id'] ?? 0);
$komentaro_id = intval($_GET['komentaro_id'] ?? 0);

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// POST duomenų apdorojimas
if ($_SERVER['REQUEST_METHOD'] == 'POST' || (isset($_GET['veiksmas']) && in_array($_GET['veiksmas'], ['trinti_tema', 'trinti_komentaras']))) {
    $userid = $_SESSION['userid'];
    $user_level = $_SESSION['ulevel'];
    
    if ($veiksmas == 'nauja_tema') {
        $tema = trim($_POST['tema'] ?? '');
        $tekstas = trim($_POST['tekstas'] ?? '');
        
        if (!empty($tema) && !empty($tekstas)) {
            $tema = mysqli_real_escape_string($db, $tema);
            $tekstas = mysqli_real_escape_string($db, $tekstas);
            $userid = mysqli_real_escape_string($db, $userid);
            
            $sql = "INSERT INTO forumo_pranesimal (tema, tekstas, autorius_userid) 
                    VALUES ('$tema', '$tekstas', '$userid')";
            
            if (mysqli_query($db, $sql)) {
                $_SESSION['message'] = "Tema sėkmingai sukurta!";
                $veiksmas = '';
            } else {
                $_SESSION['message'] = "Klaida kuriant temą: " . mysqli_error($db);
            }
        } else {
            $_SESSION['message'] = "Visi laukai yra privalomi!";
        }
        
    } elseif ($veiksmas == 'komentaras') {
        $komentaras = trim($_POST['komentaras'] ?? '');
        
        if (!empty($komentaras) && $pranesimo_id > 0) {
            $komentaras = mysqli_real_escape_string($db, $komentaras);
            $userid = mysqli_real_escape_string($db, $userid);
            
            $sql = "INSERT INTO komentaras (pranesimo_id, tekstas, userid) 
                    VALUES ($pranesimo_id, '$komentaras', '$userid')";
            
            if (mysqli_query($db, $sql)) {
                $_SESSION['message'] = "Komentaras pridėtas!";
            } else {
                $_SESSION['message'] = "Klaida pridedant komentarą: " . mysqli_error($db);
            }
        } else {
            $_SESSION['message'] = "Komentaras negali būti tuščias!";
        }
        $veiksmas = 'tema';
        
    } elseif ($veiksmas == 'trinti_tema' && $user_level == 9) {
        if ($pranesimo_id > 0) {
            // Pirmiausia ištriname visus komentarus prie šios temos
            $sql1 = "DELETE FROM komentaras WHERE pranesimo_id = $pranesimo_id";
            mysqli_query($db, $sql1);
            
            // Tada ištriname pačią temą
            $sql2 = "DELETE FROM forumo_pranesimal WHERE pranesimo_id = $pranesimo_id";
            if (mysqli_query($db, $sql2)) {
                $_SESSION['message'] = "Tema ir visi jos komentarai ištrinti!";
            } else {
                $_SESSION['message'] = "Klaida trinant temą: " . mysqli_error($db);
            }
        }
        $veiksmas = '';
        
    } elseif ($veiksmas == 'trinti_komentaras' && $user_level == 9) {
        if ($komentaro_id > 0) {
            $sql = "DELETE FROM komentaras WHERE komentaro_id = $komentaro_id";
            if (mysqli_query($db, $sql)) {
                $_SESSION['message'] = "Komentaras ištrintas!";
            } else {
                $_SESSION['message'] = "Klaida trinant komentarą: " . mysqli_error($db);
            }
        }
        $veiksmas = 'tema';
    }
}
?>

<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Forumas</title>
    <link href="include/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
    <table class="center">
        <tr><td>
            <center><img src="include/top1.png"></center>
        </td></tr>
        <tr><td>
            <?php include("include/meniu.php"); ?>
        </td></tr>
        <tr><td>
            <center><font size="5"><b>Bendruomenės forumas</b></font></center><br>
            
            <?php if (!empty($_SESSION['message'])): ?>
                <center><font color="red"><b><?php echo $_SESSION['message']; $_SESSION['message'] = ""; ?></b></font></center><br>
            <?php endif; ?>

            <?php
            if ($veiksmas == 'tema' && $pranesimo_id > 0) {
                // Rodome konkrečią temą su komentarais
                echo "<table class=\"center\" style=\"width:80%;\"><tr><td>";
                echo "<a href='forumas.php'>&larr; Atgal į forumo sąrašą</a><br><br>";
                rodykTemaSuKomentarais($db, $pranesimo_id);
                echo "</td></tr></table>";
                
            } elseif ($veiksmas == 'nauja_tema') {
                // Najos temos forma
                echo "<table class=\"center\" style=\"width:60%;\"><tr><td>";
                rodykNaujosTemosForma();
                echo "</td></tr></table>";
                
            } else {
                // Rodome visų temų sąrašą
                echo "<table class=\"center\" style=\"width:80%;\"><tr><td>";
                echo "<div align='right'><a href='forumas.php?veiksmas=nauja_tema'><b>[Nauja tema]</b></a></div><br>";
                rodykVisasForumoTemas($db);
                echo "</td></tr></table>";
            }
            ?>
            
        </td></tr>
    </table>
</body>
</html>

<?php
// FUNKCIJOS

function rodykVisasForumoTemas($db) {
    $sql = "SELECT fp.*, u.username 
            FROM forumo_pranesimal fp 
            LEFT JOIN " . TBL_USERS . " u ON fp.autorius_userid = u.userid 
            ORDER BY fp.data DESC";
    $result = mysqli_query($db, $sql);
    
    if (!$result || mysqli_num_rows($result) < 1) {
        echo "<center>Dar nėra pranešimų forume. <a href='forumas.php?veiksmas=nauja_tema'>Sukurkite pirmąjį!</a></center>";
        return;
    }
    
    $is_admin = ($_SESSION['ulevel'] == 9);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $autorius = $row['username'] ?? 'Nežinomas';
        $data = date("Y-m-d H:i", strtotime($row['data']));
        
        // Skaičiuojame komentarų kiekį
        $koment_sql = "SELECT COUNT(*) as kiekis FROM komentaras WHERE pranesimo_id = " . $row['pranesimo_id'];
        $koment_result = mysqli_query($db, $koment_sql);
        $koment_kiekis = 0;
        if ($koment_result) {
            $koment_row = mysqli_fetch_assoc($koment_result);
            $koment_kiekis = $koment_row['kiekis'];
        }
        
        echo "<table class=\"center\" style=\"width:100%; margin-bottom:15px; background-color:#F8E9FC; border:2px solid #ccc;\"><tr><td>";
        
        // Administratoriaus trynimo mygtukas
        if ($is_admin) {
            echo "<div style=\"float:right;\">";
            echo "<a href=\"forumas.php?veiksmas=trinti_tema&id=" . $row['pranesimo_id'] . "\" ";
            echo "onclick=\"return confirm('Ar tikrai norite ištrinti šią temą ir visus jos komentarus?')\" ";
            echo "style=\"color:red; text-decoration:none; font-weight:bold;\">[X]</a>";
            echo "</div>";
        }
        
        echo "<h3><a href='forumas.php?veiksmas=tema&id=" . $row['pranesimo_id'] . "'>" . htmlspecialchars($row['tema']) . "</a></h3>";
        echo "<p>" . nl2br(htmlspecialchars(substr($row['tekstas'], 0, 200))) . (strlen($row['tekstas']) > 200 ? "..." : "") . "</p>";
        echo "<small><b>Autorius:</b> $autorius | <b>Data:</b> $data | <b>Komentarų:</b> $koment_kiekis</small>";
        echo "</td></tr></table>";
    }
}

function rodykTemaSuKomentarais($db, $pranesimo_id) {
    // Paimame temą
    $sql = "SELECT fp.*, u.username 
            FROM forumo_pranesimal fp 
            LEFT JOIN " . TBL_USERS . " u ON fp.autorius_userid = u.userid 
            WHERE fp.pranesimo_id = $pranesimo_id";
    $result = mysqli_query($db, $sql);
    
    if (!$result || mysqli_num_rows($result) != 1) {
        echo "<center>Tema nerasta!</center>";
        return;
    }
    
    $tema = mysqli_fetch_assoc($result);
    $autorius = $tema['username'] ?? 'Nežinomas';
    $data = date("Y-m-d H:i", strtotime($tema['data']));
    $is_admin = ($_SESSION['ulevel'] == 9);
    
    // Rodome temą
    echo "<table class=\"center\" style=\"width:100%; background-color:#F8E9FC; border:3px dashed black;\"><tr><td>";
    
    // Administratoriaus trynimo mygtukas
    if ($is_admin) {
        echo "<div style=\"float:right;\">";
        echo "<a href=\"forumas.php?veiksmas=trinti_tema&id=" . $pranesimo_id . "\" ";
        echo "onclick=\"return confirm('Ar tikrai norite ištrinti šią temą ir visus jos komentarus?')\" ";
        echo "style=\"color:red; text-decoration:none; font-weight:bold; font-size:16px;\">[Trinti temą]</a>";
        echo "</div>";
    }
    
    echo "<h2>" . htmlspecialchars($tema['tema']) . "</h2>";
    echo "<p>" . nl2br(htmlspecialchars($tema['tekstas'])) . "</p>";
    echo "<small><b>Autorius:</b> $autorius | <b>Data:</b> $data</small>";
    echo "</td></tr></table><br>";
    
    // Rodome komentarus
    $koment_sql = "SELECT k.*, u.username 
                   FROM komentaras k 
                   LEFT JOIN " . TBL_USERS . " u ON k.userid = u.userid 
                   WHERE k.pranesimo_id = $pranesimo_id 
                   ORDER BY k.data ASC";
    $koment_result = mysqli_query($db, $koment_sql);
    
    echo "<h3>Komentarai:</h3>";
    
    if ($koment_result && mysqli_num_rows($koment_result) > 0) {
        while ($koment = mysqli_fetch_assoc($koment_result)) {
            $koment_autorius = $koment['username'] ?? 'Nežinomas';
            $koment_data = date("Y-m-d H:i", strtotime($koment['data']));
            
            echo "<table class=\"center\" style=\"width:95%; margin-left:20px; margin-bottom:10px; background-color:#FFF7B7; border:1px solid #ccc;\"><tr><td>";
            
            // Administratoriaus trynimo mygtukas komentarui
            if ($is_admin) {
                echo "<div style=\"float:right;\">";
                echo "<a href=\"forumas.php?veiksmas=trinti_komentaras&komentaro_id=" . $koment['komentaro_id'] . "&id=$pranesimo_id\" ";
                echo "onclick=\"return confirm('Ar tikrai norite ištrinti šį komentarą?')\" ";
                echo "style=\"color:red; text-decoration:none; font-weight:bold;\">[x]</a>";
                echo "</div>";
            }
            
            echo "<p>" . nl2br(htmlspecialchars($koment['tekstas'])) . "</p>";
            echo "<small><b>Komentavo:</b> $koment_autorius | $koment_data</small>";
            echo "</td></tr></table>";
        }
    } else {
        echo "<p><i>Dar nėra komentarų.</i></p>";
    }
    
    // Komentaro pridėjimo forma
    echo "<br><table class=\"center\" style=\"width:100%; background-color:#FFF7B7; border:2px dashed grey;\"><tr><td>";
    echo "<h4>Pridėti komentarą:</h4>";
    echo "<form method='POST' action='forumas.php' class='login'>";
    echo "<input type='hidden' name='veiksmas' value='komentaras'>";
    echo "<input type='hidden' name='pranesimo_id' value='$pranesimo_id'>";
    echo "<textarea name='komentaras' rows='4' style='width:600px; resize:none;' placeholder='Įveskite savo komentarą...' required></textarea><br><br>";
    echo "<input type='submit' value='Pridėti komentarą'>";
    echo "</form>";
    echo "</td></tr></table>";
}

function rodykNaujosTemosForma() {
    echo "<h3>Nauja forumų tema</h3>";
    echo "<form method='POST' action='forumas.php' class='login'>";
    echo "<input type='hidden' name='veiksmas' value='nauja_tema'>";
    echo "<p style='text-align:left;'>Temos pavadinimas:<br>";
    echo "<input type='text' name='tema' style='width:600px;' maxlength='255' placeholder='Įveskite temos pavadinimą...' required></p>";
    echo "<p style='text-align:left;'>Temos tekstas:<br>";
    echo "<textarea name='tekstas' rows='6' style='width:600px; resize:none;' placeholder='Įveskite temos turinį...' required></textarea></p>";
    echo "<p style='text-align:left;'>";
    echo "<input type='submit' value='Sukurti temą'> ";
    echo "<input type='button' value='Atšaukti' onclick='window.location.href=\"forumas.php\"'>";
    echo "</p></form>";
}
?>
