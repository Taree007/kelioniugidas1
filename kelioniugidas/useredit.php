<?php 
// useredit.php 
// vartotojas gali pasikeisti slaptažodį ar email
// formos reikšmes tikrins procuseredit.php. Esant klaidų pakartotinai rodant formą rodomos ir klaidos

session_start();

// sesijos kontrole
if (!isset($_SESSION['prev']) || 
    !in_array($_SESSION['prev'], ["index", "procuseredit", "useredit", "generatorius", "forumas"]))
{
    header("Location: logout.php");
    exit;
}

include("include/nustatymai.php");

// Jei ateinama iš index.php - nustatome pradines reikšmes
//if ($_SESSION['prev'] == "index") {
    $_SESSION['mail_login'] = $_SESSION['umail'];
    $_SESSION['pass_login'] = "";
    $_SESSION['passn_login'] = "";
    $_SESSION['passn_error'] = "";
    $_SESSION['pass_error'] = "";
    $_SESSION['mail_error'] = "";
    $_SESSION['dailytime_error'] = "";
    $_SESSION['message'] = "";
}

$_SESSION['prev'] = "useredit"; 
?>

<html>
    <head>  
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"> 
        <title>Paskyros redagavimas</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css" >
    </head>
    <body>   
        <table class="center"><tr><td> 
            <center><img src="include/top1.png"></center>
        </td></tr><tr><td> 
            <table style="border-width: 2px; border-style: dotted;"><tr><td>
                Atgal į [<a href="index.php">Pradžia</a>] 
            </td></tr>
            </table>               
            
            <div align="center">   
                <?php if (!empty($_SESSION['message'])): ?>
                    <font size="4" color="#ff0000"><?php echo $_SESSION['message']; ?><br></font>
                    <?php $_SESSION['message'] = ""; // Išvalome pranešimą ?>
                <?php endif; ?>
                					
                <table bgcolor=#C3FDB8>
                    <tr><td>
                        <form action="procuseredit.php" method="POST" class="login">             
                            <center style="font-size:18pt;"><b>Paskyros redagavimas</b></center><br>
                            <center style="font-size:14pt;"><b>Vartotojas: <?php echo $_SESSION['user']; ?></b></center>
                            
                            <p style="text-align:left;">Dabartinis slaptažodis:<br>
                                <input class="s1" name="pass" type="password" value="<?php echo $_SESSION['pass_login'] ?? ''; ?>"><br>
                                <?php echo $_SESSION['pass_error'] ?? ''; ?>
                            </p>
                                
                            <p style="text-align:left;">Naujas slaptažodis:<br>
                                <input class="s1" name="passn" type="password" value="<?php echo $_SESSION['passn_login'] ?? ''; ?>"><br>
                                <?php echo $_SESSION['passn_error'] ?? ''; ?>
                            </p>	
                                
                            <p style="text-align:left;">E-paštas:<br>
                                <input class="s1" name="email" type="text" value="<?php echo $_SESSION['mail_login'] ?? $_SESSION['umail']; ?>"><br>
                                <?php echo $_SESSION['mail_error'] ?? ''; ?>
                            </p> 
                                
                            <p style="text-align:left;">Aktyvumo lygis (minutės per dieną kelionėms):<br>
                                <input class="s1" name="dailytime" type="number" min="1" max="1440" value="<?php echo $_SESSION['dailyminutes'] ?? $_SESSION['dailytime'] ?? ''; ?>"><br>
                                <?php echo $_SESSION['dailytime_error'] ?? ''; ?>
                            </p>
                            
                            <p style="text-align:left;">
                                <input type="submit" name="login" value="Atnaujinti"/>     
                            </p>  
                        </form>
                    </tr></td>
                </table>
            </div>
        </td></tr>
        </table>           
    </body>
</html>

