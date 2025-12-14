<?php
session_start();
include("include/nustatymai.php");
include("include/functions.php");

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) die("Klaida jungiantis prie DB: " . mysqli_connect_error());

// Paimame tipus iš DB
$tipai = mysqli_query($db, "SELECT tipo_id, pavadinimas FROM objektu_tipai WHERE ar_nakyvne IN (0,1) ORDER BY tipo_id ASC");

// Jei formos duomenys nusiųsti
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pavadinimas = trim($_POST['pavadinimas'] ?? '');
    $tipo_id = intval($_POST['tipo_id'] ?? 0);
    $aprasymas = trim($_POST['aprasymas'] ?? '');
    $miestas = trim($_POST['miestas'] ?? '');
    $rekomenduojamas_laikas = intval($_POST['rekomenduojamas_laikas'] ?? 0);
    $koordinateX = trim($_POST['koordinateX'] ?? '');
    $koordinateY = trim($_POST['koordinateY'] ?? '');
    
    $errors = [];

    if ($pavadinimas === '') $errors[] = "Pavadinimas privalomas.";
    if ($tipo_id <= 0) $errors[] = "Pasirinkite tipą.";
    if ($miestas === '') $errors[] = "Miestas privalomas.";
    if ($rekomenduojamas_laikas <= 0) $errors[] = "Rekomenduojamas laikas privalomas.";
    if ($koordinateX === '' || !is_numeric($koordinateX)) $errors[] = "Koordinate X privaloma ir turi būti skaičius.";
    if ($koordinateY === '' || !is_numeric($koordinateY)) $errors[] = "Koordinate Y privaloma ir turi būti skaičius.";

    if (empty($errors)) {
        $stmt = mysqli_prepare($db, "INSERT INTO objektai 
            (pavadinimas, tipo_id, aprasymas, miestas, rekomenduojamas_laikas, koordinateX, koordinateY) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sissidd", $pavadinimas, $tipo_id, $aprasymas, $miestas, $rekomenduojamas_laikas, $koordinateX, $koordinateY);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['object_message'] = "Objektas sėkmingai pridėtas.";
        } else {
            $errors[] = "DB klaida: " . mysqli_error($db);
        }
        mysqli_stmt_close($stmt);
    }

    if (!empty($errors)) {
        $_SESSION['object_errors'] = implode("<br>", $errors);
        $_SESSION['object_values'] = $_POST; // kad išliktų įvesti duomenys
    }

    header("Location:NaujasObjektas.php");
    exit;
}
?>

<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"> 
    <title>Naujas objektas</title>
    <link href="include/styles.css" rel="stylesheet" type="text/css" >
</head>
<body>
<table class="center"><tr><td><img src="include/top1.png"></td></tr><tr><td>
    <table style="border-width: 2px; border-style: dotted;"><tr><td>
       Atgal į [<a href="index.php">Pradžia</a>]</td></tr>
    </table>   
    <div align="center">
        <table>
            <tr><td>
                <?php
                if (!empty($_SESSION['object_message'])) {
                    echo "<p style='color:green'>".$_SESSION['object_message']."</p>";
                    unset($_SESSION['object_message']);
                }
                if (!empty($_SESSION['object_errors'])) {
                    echo "<p style='color:red'>".$_SESSION['object_errors']."</p>";
                    unset($_SESSION['object_errors']);
                }
                $vals = $_SESSION['object_values'] ?? [];
                ?>
                <form action="" method="POST" class="login">
                    <center style="font-size:18pt;"><b>Naujas objektas</b></center><br>


                <p style="text-align:left;">Pavadinimas:<br>
                    <input class="s1" name="pavadinimas" type="text" value="<?php echo htmlspecialchars($vals['pavadinimas'] ?? ''); ?>"><br>
                </p>

                <p style="text-align:left;">Tipas:<br>
                    <select name="tipo_id" class="s1">
                    <?php while($row = mysqli_fetch_assoc($tipai)) {
                        $selected = (isset($vals['tipo_id']) && $vals['tipo_id'] == $row['tipo_id']) ? 'selected' : '';
                        echo '<option value="'.$row['tipo_id'].'" '.$selected.'>'.$row['pavadinimas'].'</option>';
                    } ?>
                    </select>
                </p>

                <p style="text-align:left;">Aprašymas:<br>
                    <textarea class="s1" name="aprasymas" rows="4" cols="50"><?php echo htmlspecialchars($vals['aprasymas'] ?? ''); ?></textarea><br>
                </p>

                <p style="text-align:left;">Miestas:<br>
                    <input class="s1" name="miestas" type="text" value="<?php echo htmlspecialchars($vals['miestas'] ?? ''); ?>"><br>
                </p>

                <p style="text-align:left;">Rekomenduojamas laikas (minutėmis):<br>
                    <input class="s1" name="rekomenduojamas_laikas" type="number" min="1" max="1440" value="<?php echo htmlspecialchars($vals['rekomenduojamas_laikas'] ?? ''); ?>"><br>
                </p>

                <p style="text-align:left;">Koordinate X:<br>
                    <input class="s1" name="koordinateX" type="text" value="<?php echo htmlspecialchars($vals['koordinateX'] ?? ''); ?>"><br>
                </p>

                <p style="text-align:left;">Koordinate Y:<br>
                    <input class="s1" name="koordinateY" type="text" value="<?php echo htmlspecialchars($vals['koordinateY'] ?? ''); ?>"><br>
                </p>

                <p style="text-align:left;">
                    <input type="submit" value="Pridėti objektą">
                </p>
            </form>
        </td></tr>
    </table>
</div>


</td></tr>
</table>
</body>
</html>

