<?php

// Jei jau įtrauktas - nedaryk nieko
if (defined('DB_SERVER')) {
    return;
}

// Tikriname, ar esame Docker aplinkoje
if (getenv('DOCKER') === 'true') {
    $db_server = 'db';
} else {
    $db_server = 'localhost';
}


//nustatymai.php
define("DB_SERVER", "db" );
define("DB_USER", "stud");
define("DB_PASS", "stud");
define("DB_NAME", "kelioniugidas1");
define("TBL_USERS", "users");
define("TBL_OBJECTS", "objektai");  //  objektų lentelė
define("TBL_TYPES", "objektu_tipai");       //  tipų lentelė


// Vartotojų profiliai
$user_roles=array(      // vartotojų rolių vardai ir  atitinkamos userlevel reikšmės
	"Administratorius"=>"9",
	"Keliautojas"=>"4",
	"Gidas"=>"5",);   
// automatiškai galioja ir vartotojas "guest",rolė "Svečias",  userlevel=0
//   jam irgi galima nurodyti leidžiamas operacijas

define("DEFAULT_LEVEL","Keliautojas");  // kokia rolė priskiriama kai registruojasi
define("ADMIN_LEVEL","Administratorius");  // jis turi vartotojų valdymo teisę per "Administratoriaus sąsaja"
define("GIDAS_LEVEL","Gidas");  // jis turi vartotojų valdymo teisę per "Administratoriaus sąsaja"
define("UZBLOKUOTAS","255");      // vartotojas negali prisijungti kol administratorius nepakeis rolės
$uregister="both";  // kaip registruojami vartotojai:
					// self - pats registruojasi, admin - tik ADMIN_LEVEL, both - abu atvejai

// Operacijų meniu
// Automatiškai rodomi punktai "Redaguoti paskyrą" ir "Atsijungti", 
//  							o Administratoriui dar "Administratoriaus sąsaja"
// Kitų operacijų meniu aprašomas kintamuoju $usermenu:
// operacijos pavadinimas
// kokioms rolėms rodoma
// operacijos modulis

$usermenu=array(
	["Marsrutu paieska",[0,4,5,9],"generatorius.php"],
	["Objekto pridejimas",[5,9],"NaujasObjektas.php"],
	["Forumas",[4,5,9],"forumas.php"],
			  ); 

// karkaso vaizdavimą paredaguokite savo stiliumi keisdami top.png (pradinis yra 1027x122, read teisė visiems)
// ir styles.css.
// top.png pageidautina matyti sistemos pavadinimą ir autorių.

