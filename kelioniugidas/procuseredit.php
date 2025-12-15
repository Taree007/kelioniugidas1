<?php
// procuseredit.php tikrina paskyros keitimo reikšmes
// įvestas reikšmes išsaugo $_SESSION['xxxx_login']
// jei randa klaidų jas sužymi $_SESSION['xxxx_error']
// jei naujas slaptažodis ir email tinka, pataiso DB, nukreipia į index.php prisijungimui iš naujo
// po klaidų- vel i useredit.php 

session_start(); 

// cia sesijos kontrole - pridėtas "generatorius"
if (!isset($_SESSION['prev']) || 
    !in_array($_SESSION['prev'], ["useredit", "generatorius", "forumas"])) {
    header("Location: logout.php");
    exit;
}

include("include/nustatymai.php");
include("include/functions.php");

$_SESSION['prev'] = "procuseredit";

// Išvalome klaidų pranešimus
$_SESSION['pass_error'] = "";
$_SESSION['mail_error'] = "";
$_SESSION['passn_error'] = "";
$_SESSION['dailytime_error'] = "";

$user = $_SESSION['user'];
$pass = $_POST['pass'] ?? '';
$passn = $_POST['passn'] ?? '';
$mail = $_SESSION['email'] ?? '';
$dailyminutes = intval($_POST['dailytime'] ?? 0);

// Išsaugome įvestas reikšmes formai
$_SESSION['pass_login'] = $pass;
$_SESSION['passn_login'] = $passn;
$_SESSION['mail_login'] = $mail;
$_SESSION['dailyminutes'] = $dailyminutes;

$changes_made = false;
$errors = false;

// 1. AKTYVUMO LYGIO ATNAUJINIMAS - nepriklausomai nuo kitų laukų
if ($dailyminutes > 0) {
    if ($dailyminutes < 1 || $dailyminutes > 1440) {
        $_SESSION['dailytime_error'] = "<font size=\"2\" color=\"#ff0000\">* Įveskite teisingą min. skaičių nuo 1 iki 1440!</font>";
        $errors = true;
    } else {
        $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        $sql = "UPDATE " . TBL_USERS . " SET dailyminutes=$dailyminutes WHERE username='$user'";
        if (!mysqli_query($db, $sql)) {
            echo "DB klaida keičiant dailyminutes: " . mysqli_error($db);
            exit;
        }
        $changes_made = true;
        $_SESSION['dailytime'] = $dailyminutes; // Atnaujiname sesijoje
    }
}

// 2. SLAPTAŽODŽIO IR EMAIL ATNAUJINIMAS - tik jei kas nors įvesta
$need_password_check = false;

// Tikriname ar reikia slaptažodžio tikrinimo
if (!empty($pass) || !empty($passn) || (!empty($mail) && $mail != $_SESSION['umail'])) {
    $need_password_check = true;
}

if ($need_password_check) {
    // Paimame esamą slaptažodį iš DB
    list(, $dbpass) = checkdb($user);
    if (!$dbpass) {
        echo "DB klaida nuskaitant slaptazodi vartotojui " . $user;
        exit;
    }

    // Jei įvestas dabartinis slaptažodis - tikriname jį
    $password_check_passed = false;
    if (!empty($pass)) {
        if (checkpass($pass, $dbpass)) {
            $password_check_passed = true;
        } else {
            // Neteisingas dabartinis slaptažodis
            $_SESSION['pass_error'] = "<font size=\"2\" color=\"#ff0000\">* Neteisingas slaptažodis</font>";
            $errors = true;
        }
    } else {
        // Jei slaptažodis neįvestas, bet bandoma keisti email ar naują slaptažodį
        if (!empty($passn) || (!empty($mail) && $mail != $_SESSION['umail'])) {
            $_SESSION['pass_error'] = "<font size=\"2\" color=\"#ff0000\">* Įveskite dabartinį slaptažodį</font>";
            $errors = true;
        }
    }

    // Jei slaptažodis teisingas, tikriname naują slaptažodį ir email
    if ($password_check_passed && !$errors) {
        $password_valid = true;
        $email_valid = true;
        
        // Tikriname naują slaptažodį (jei įvestas)
        if (!empty($passn)) {
            if (!checkpass($passn, substr(hash('sha256', $passn), 5, 32))) {
                $password_valid = false;
                $_SESSION['passn_error'] = $_SESSION['pass_error'];
                $_SESSION['pass_error'] = "";
                $errors = true;
            }
        }
        
        // Tikriname email (jei įvestas ir skiriasi)
        if (!empty($mail) && $mail != $_SESSION['umail']) {
            if (!checkmail($mail)) {
                $email_valid = false;
                $errors = true;
            }
        }
        
        // Jei visi duomenys teisingi - atnaujiname DB
        if ($password_valid && $email_valid && !$errors) {
            $need_update = false;
            $update_fields = array();
            
            // Tikriname ar keičiamas slaptažodis
            if (!empty($passn) && $pass != $passn) {
                $new_dbpass = substr(hash('sha256', $passn), 5, 32);
                $update_fields[] = "password='$new_dbpass'";
                $need_update = true;
            }
            
            // Tikriname ar keičiamas email
            if (!empty($mail) && $mail != $_SESSION['umail']) {
                $update_fields[] = "email='$mail'";
                $need_update = true;
                $_SESSION['umail'] = $mail; // Atnaujiname sesijoje
            }
            
            // Vykdome atnaujinimą jei reikia
            if ($need_update) {
                $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
                $sql = "UPDATE " . TBL_USERS . " SET " . implode(', ', $update_fields) . " WHERE username='$user'";
                
                if (!mysqli_query($db, $sql)) {
                    echo "DB klaida keičiant paskyros duomenis: " . $sql . "<br>" . mysqli_error($db);
                    exit;
                }
                $changes_made = true;
            }
        }
    }
}

// Nustatome pranešimą
if (!$errors) {
    if ($changes_made) {
        $_SESSION['message'] = "Paskyros duomenys sėkmingai atnaujinti";
        // Jei slaptažodis pakeistas, nukreipiame prisijungti iš naujo
        if (!empty($passn) && $pass != $passn) {
            $_SESSION['user'] = "";
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Nieko nekeičėte";
    }
    
    // Išvalome formos laukus po sėkmingo atnaujinimo
    $_SESSION['pass_login'] = "";
    $_SESSION['passn_login'] = "";
} else {
    $_SESSION['message'] = "Yra klaidų - pataisykite ir bandykite dar kartą";
}

// Grįžtame į redagavimo formą
header("Location: useredit.php");
exit;
?>
