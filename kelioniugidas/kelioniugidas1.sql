-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 14, 2025 at 07:58 PM
-- Server version: 8.0.41-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kelioniugidas1`
--

-- --------------------------------------------------------

--
-- Table structure for table `atsiliepimai`
--

CREATE TABLE `atsiliepimai` (
  `atsiliepimo_id` int NOT NULL,
  `objekto_id` int NOT NULL,
  `vartotojas_userid` varchar(32) NOT NULL,
  `ivertinimas` tinyint(1) DEFAULT NULL,
  `komentaras` text,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `atsiliepimai`
--

INSERT INTO `atsiliepimai` (`atsiliepimo_id`, `objekto_id`, `vartotojas_userid`, `ivertinimas`, `komentaras`, `data`) VALUES
(1, 1, 'b3fe399900de341c39c632244eaf8484', 5, 'Puikus vaizdas!', '2025-11-16 21:32:14'),
(2, 2, 'b3fe399900de341c39c632244eaf8484', 5, 'Graži pilis!', '2025-11-16 21:32:14'),
(3, 3, 'b3fe399900de341c39c632244eaf8484', 5, 'Unikali gamta!', '2025-11-16 21:32:14'),
(4, 95, 'b3fe399900de341c39c632244eaf8484', 4, 'Geras viešbutis, patogi vieta', '2025-11-16 21:32:14'),
(5, 96, 'b3fe399900de341c39c632244eaf8484', 5, 'Jaukūs namai, rekomenduoju!', '2025-11-16 21:32:14');

-- --------------------------------------------------------

--
-- Table structure for table `atstumas`
--

CREATE TABLE `atstumas` (
  `id` int NOT NULL,
  `objekto1_id` int NOT NULL,
  `objekto2_id` int NOT NULL,
  `atstumas_km` int NOT NULL,
  `laikas` int NOT NULL COMMENT 'Minutėmis'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `atstumas`
--

INSERT INTO `atstumas` (`id`, `objekto1_id`, `objekto2_id`, `atstumas_km`, `laikas`) VALUES
(1, 1, 2, 28, 35),
(2, 2, 1, 28, 35),
(3, 1, 3, 296, 240),
(4, 3, 1, 296, 240),
(5, 1, 4, 2, 10),
(6, 4, 1, 2, 10),
(7, 1, 5, 1, 5),
(8, 5, 1, 1, 5),
(9, 2, 3, 268, 210),
(10, 3, 2, 268, 210),
(11, 2, 5, 29, 40),
(12, 5, 2, 29, 40),
(13, 21, 22, 105, 85),
(14, 22, 21, 105, 85),
(15, 23, 24, 15, 20),
(16, 24, 23, 15, 20),
(17, 25, 26, 12, 18),
(18, 26, 25, 12, 18);

-- --------------------------------------------------------

--
-- Table structure for table `forumo_pranesimal`
--

CREATE TABLE `forumo_pranesimal` (
  `pranesimo_id` int NOT NULL,
  `tema` varchar(100) NOT NULL,
  `tekstas` text,
  `autorius_userid` varchar(32) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `forumo_pranesimal`
--

INSERT INTO `forumo_pranesimal` (`pranesimo_id`, `tema`, `tekstas`, `autorius_userid`, `data`) VALUES
(1, 'Labas!', 'Sveiki, aš naujas vartotojas!', 'b3fe399900de341c39c632244eaf8484', '2025-11-16 21:32:14'),
(2, 'Geras puslapis', 'Patinka šis kelionių gidas', 'b3fe399900de341c39c632244eaf8484', '2025-11-16 21:32:14'),
(3, 'Kokiu lokaciju truksta', 'sveiki visi, \r\nas esu kelioniu organizatorius ir mane labai sudomino sitas puslapis, noreciau padeti jums issirinkti keliones. \r\nGal turit kokiu noru kur noretumet nukeliaut ir nera sistemoje? mielai padesiu rasti objektus', '6557aa8079740ea8dba1e8da2c5153d3', '2025-12-01 01:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `komentaras`
--

CREATE TABLE `komentaras` (
  `komentaro_id` int NOT NULL,
  `pranesimo_id` int NOT NULL,
  `tekstas` text,
  `userid` varchar(32) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `komentaras`
--

INSERT INTO `komentaras` (`komentaro_id`, `pranesimo_id`, `tekstas`, `userid`, `data`) VALUES
(1, 1, 'Sveiki ir jūs! Maloniai priimame naują narį.', 'a2fe399900de341c39c632244eaf8483', '2025-11-16 20:15:30'),
(2, 1, 'Ačiū už šiltą priėmimą!', 'b3fe399900de341c39c632244eaf8484', '2025-11-16 20:45:12'),
(3, 2, 'Tikrai puikus projektas, daug naudingos informacijos.', '20204e9ebebbcc11f9c5286605745200', '2025-11-17 06:30:45'),
(4, 2, 'Pritariu, ypač patinka tikslūs koordinatės.', 'a2fe399900de341c39c632244eaf8483', '2025-11-17 07:15:20'),
(5, 1, 'Ar galima pridėti daugiau vietų iš Žemaitijos?', '20204e9ebebbcc11f9c5286605745200', '2025-11-17 08:20:15'),
(7, 2, 'Dziaugiuosi kad yra issamus keliones aprasymas', 'c75600ad110de61081640108b4b5a976', '2025-12-01 01:12:18'),
(8, 1, 'gerai, pridesim zemaitijos lokaciju', '6557aa8079740ea8dba1e8da2c5153d3', '2025-12-01 01:12:41'),
(9, 3, 'labas, labai noreciau daugiau visko aplink kacergine ar zapyski', 'c75600ad110de61081640108b4b5a976', '2025-12-01 01:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `objektai`
--

CREATE TABLE `objektai` (
  `objekto_id` int NOT NULL,
  `pavadinimas` varchar(100) NOT NULL,
  `tipo_id` int NOT NULL,
  `aprasymas` text,
  `miestas` varchar(50) DEFAULT NULL,
  `rekomenduojamas_laikas` int DEFAULT NULL COMMENT 'Minutėmis (tik lankytiniems objektams)',
  `koordinateX` double NOT NULL,
  `koordinateY` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `objektai`
--

INSERT INTO `objektai` (`objekto_id`, `pavadinimas`, `tipo_id`, `aprasymas`, `miestas`, `rekomenduojamas_laikas`, `koordinateX`, `koordinateY`) VALUES
(1, 'Gedimino pilis', 3, 'Gedimino pilis yra Vilniaus aukštutinė pilis, kur galima pamatyti nuostabią miesto panoramą. Čia eksponuojami istorijos artefaktai ir viduramžių ginklai. Rekomenduojama praleisti apie 1,5 valandos.', 'Vilnius', 90, 54.6867, 25.2907),
(2, 'Trakų pilis', 3, 'Trakų pilis stovi Galvės ežere ir žavi savo salos architektūra. Galima apžiūrėti vidinius kambarius ir muziejaus ekspozicijas. Praleiskite apie 2 valandas tyrinėjimams.', 'Trakai', 120, 54.652, 24.934),
(3, 'Kuršių nerija', 2, 'Kuršių nerija yra UNESCO saugomas nacionalinis parkas su įspūdingomis kopomis ir pušynais. Puiki vieta pasivaikščiojimams ir paukščių stebėjimui. Rekomenduojama skirti apie 4 valandas.', 'Neringa', 240, 55.4049, 21.0601),
(4, 'MO muziejus', 1, 'MO muziejus Vilniuje pristato šiuolaikinio meno kolekcijas. Čia vyksta edukaciniai užsiėmimai ir parodos. Idealu praleisti apie 1 valandą ir 15 minučių.', 'Vilnius', 75, 54.6845, 25.2877),
(5, 'Nacionalinis muziejus', 1, 'Nacionalinis muziejus Vilniuje supažindina su Lietuvos istorija ir kultūra. Rengiamas įvairių laikotarpių ekspozicijas. Rekomenduojamas laikas: apie 2 valandas.', 'Vilnius', 120, 54.6856, 25.2878),
(6, 'Šokolado muziejus', 1, 'Šokolado muziejus Kaune kviečia susipažinti su šokolado gamybos istorija. Galima dalyvauti degustacijose ir dirbtuvėse. Idealu praleisti apie 45 minutes.', 'Kaunas', 45, 54.8967, 23.8881),
(7, 'Vytauto Didžiojo karo muziejus', 1, 'Vytauto Didžiojo karo muziejus Kaune demonstruoja karinius eksponatus nuo viduramžių iki šiuolaikinių laikų. Čia vyksta edukacinės programos. Skirkite apie 1,5 valandos.', 'Kaunas', 90, 54.8994, 23.9169),
(8, 'Jūrų muziejus', 1, 'Jūrų muziejus Klaipėdoje pristato jūrų ekspozicijas, įskaitant laivų modelius ir akvariumus. Puiki vieta šeimoms ir mokykloms. Rekomenduojama 1 valanda ir 15 minučių.', 'Klaipėda', 75, 55.7131, 21.1167),
(9, 'Maironio muziejus', 1, 'Maironio muziejus Kaune supažindina su lietuvių literatūros klasikais ir poeto gyvenimu. Čia eksponuojami rankraščiai ir portretai. Praleiskite apie 1 valandą.', 'Kaunas', 60, 54.8983, 23.9169),
(10, 'IX fortas', 1, 'IX fortas Kaune yra muziejus ir memorialas, pasakojantis apie istorinius įvykius. Įdomu tiek suaugusiems, tiek moksleiviams. Rekomenduojama apie 2 valandas.', 'Kaunas', 120, 54.9364, 23.8533),
(11, 'Pilies muziejus', 1, 'Pilies muziejus Klaipėdoje pristato miesto istoriją ir pilies architektūrą. Galima pasivaikščioti eksponatuose ir bokštuose. Rekomenduojama 1 valanda ir 15 minučių.', 'Klaipėda', 75, 55.7036, 21.1414),
(12, 'Laikrodžių muziejus', 1, 'Laikrodžių muziejus Klaipėdoje demonstruoja laikrodžių kolekciją iš įvairių šalių. Idealu entuziastams ir vaikams. Skirkite apie 1 valandą.', 'Klaipėda', 60, 55.7031, 21.1419),
(13, 'Kauno meno galerija', 1, 'Kauno meno galerija supažindina su vietos ir užsienio menininkų darbais. Galima lankyti parodas ir edukacines veiklas. Rekomenduojamas laikas: 1 valanda ir 15 minučių.', 'Kaunas', 75, 54.8969, 23.9228),
(14, 'Etnokosmologijos muziejus', 1, 'Etnokosmologijos muziejus Molėtuose pristato astronomijos ir etnokosmologijos eksponatus. Puiki vieta pažinčiai ir edukacijai. Praleiskite apie 1,5 valandos.', 'Molėtai', 90, 55.2281, 25.4172),
(15, 'Šiaulių „Aušros\" muziejus', 1, 'Šiaulių „Aušros“ muziejus supažindina su regiono kūryba ir istorija. Čia vyksta parodos ir edukaciniai užsiėmimai. Rekomenduojama 1 valanda.', 'Šiauliai', 60, 55.9333, 23.3167),
(16, 'Panevėžio kraštotyros muziejus', 1, 'Panevėžio kraštotyros muziejus pristato vietovės istoriją ir kultūrą. Galima apžiūrėti ekspozicijas ir dalyvauti edukaciniuose užsiėmimuose. Idealu skirti apie 1 valandą ir 15 minučių.', 'Panevėžys', 75, 55.7353, 24.3575),
(17, 'Utenos kraštotyros muziejus', 1, 'Utenos kraštotyros muziejus supažindina su Aukštaitijos regiono kultūra. Čia eksponuojami tradiciniai amatai ir įrankiai. Rekomenduojama apie 1 valandą.', 'Utena', 60, 55.4983, 25.5997),
(18, 'Alytaus kraštotyros muziejus', 1, 'Alytaus kraštotyros muziejus demonstruoja Dzūkijos kraštovaizdį ir istoriją. Galima susipažinti su regiono tradicijomis. Skirkite apie 1 valandą.', 'Alytus', 60, 54.3975, 24.0453),
(19, 'Marijampolės muziejus', 1, 'Marijampolės muziejus supažindina su Suvalkijos istorija ir kultūra. Rengiamas edukacinių programų ciklas. Rekomenduojama apie 1 valandą.', 'Marijampolė', 60, 54.5597, 23.3544),
(20, 'Tauragės pilies muziejus', 1, 'Tauragės pilies muziejus pasakoja apie pilies istoriją ir regiono paveldą. Idealu trumpam apsilankymui – apie 45 minutes.', 'Tauragė', 45, 55.2528, 22.2869),
(21, 'Aukštaitijos nacionalinis parkas', 2, 'Klaipėdos uostas yra vienas didžiausių Baltijos jūros uostų, siūlantis įdomią laivybos panoramą. Galima stebėti keltus ir kruizinius laivus. Rekomenduojama apie 1 valandą.', 'Ignalina', 180, 55.3, 26.05),
(22, 'Žemaitijos nacionalinis parkas', 2, 'Biržų pilis – istorinė gynybinė tvirtovė su muziejaus ekspozicija. Galima aplankyti bokštus ir požemius. Praleiskite apie 1,5 valandos.', 'Plateliai', 150, 56.0167, 21.8167),
(23, 'Dzūkijos nacionalinis parkas', 2, 'Anykščių šilelis kviečia pasivaikščioti miško takais ir stebėti gamtos grožį. Čia yra laiptai, takai ir apžvalgos aikštelės. Skirkite apie 2 valandas.', 'Varėna', 180, 54.2167, 24.5667),
(24, 'Trakai istorinis nacionalinis parkas', 2, 'Klaipėdos etnokultūros centras pristato vietos tradicijas ir amatų parodas. Rekomenduojama apie 1 valandą lankymui.', 'Trakai', 120, 54.6378, 24.9343),
(25, 'Anykščių regioninis parkas', 2, 'Palangos botanikos parkas žavi įvairiomis medžių ir augalų kolekcijomis. Puiki vieta šeimoms pasivaikščioti. Praleiskite apie 1–1,5 valandos.', 'Anykščiai', 150, 55.5208, 25.1042),
(26, 'Kurtuvėnų regioninis parkas', 2, 'Kretingos muziejus pristato regiono istoriją ir dvarų kultūrą. Čia vyksta edukacinės programos ir parodos. Rekomenduojama 1 valanda ir 15 minučių.', 'Kurtuvėnai', 120, 55.8167, 22.8667),
(27, 'Veisiejų regioninis parkas', 2, 'Šiaulių „Rėkyvos“ parkas – puiki vieta lauko pasivaikščiojimams ir piknikams. Galima stebėti gamtą ir pasivaikščioti takais. Skirkite apie 1 valandą.', 'Veisiejai', 120, 54.1, 23.7),
(28, 'Biržų regioninis parkas', 2, 'Molėtų astronomijos observatorija leidžia stebėti žvaigždes ir planetas. Puiki edukacinė patirtis visai šeimai. Rekomenduojama apie 1,5 valandos.', 'Biržai', 90, 56.2014, 24.7603),
(29, 'Labanoro giria', 2, 'Vilniaus rotušė yra istorinė miesto aikštės dalis su architektūriniais lobiais. Galima aplankyti bokštą ir muziejų. Praleiskite apie 45 minutes–1 valandą.', 'Švenčionys', 180, 55.1667, 25.7167),
(30, 'Ventės ragas', 2, 'Kauno Zoologijos sodas supažindina su įvairiomis gyvūnų rūšimis. Puiki vieta šeimoms ir vaikams. Rekomenduojama 1–1,5 valandos.', 'Ventė', 90, 55.3422, 21.1956),
(31, 'Nidos kopos', 2, 'Nidos kopos – įspūdinga gamtos vieta, kur galima lipti ant aukštų kopų ir stebėti Kuršių marias. Praleiskite apie 2 valandas.', 'Nida', 120, 55.3058, 21.0036),
(32, 'Raganų kalnas', 2, 'Druskininkų vandens parkas siūlo pramogas vandens mėgėjams. Čia yra baseinai, čiuožyklos ir SPA zonos. Idealu praleisti 2–3 valandas.', 'Juodkrantė', 60, 55.5361, 21.1208),
(33, 'Vanagų kalnas', 2, 'Kėdainių senamiestis žavi istorine architektūra ir kultūros paveldais. Galima pasivaikščioti pėsčiųjų gatvėmis ir aplankyti muziejus. Rekomenduojama 1–1,5 valandos.', 'Nida', 40, 55.3058, 21.0036),
(34, 'Plokščių atodanga', 2, 'Plungės dvaro parkas siūlo ramų pasivaikščiojimą po gražų parką ir seną dvaro aplinką. Skirkite apie 1 valandą.', 'Šakiai', 60, 54.9639, 23.0353),
(35, 'Anykščių šilelis', 2, 'Rokiškio kraštotyros muziejus supažindina su regiono istorija ir kultūra. Čia eksponuojami seni dokumentai, baldai ir rankdarbiai. Rekomenduojama 1 valanda ir 15 minučių.', 'Anykščiai', 90, 55.5208, 25.1042),
(36, 'Trijų kryžių kalnas', 2, 'Akmenės muziejus pristato vietos kultūrą ir istoriją. Puiki vieta edukacijai ir trumpam apsilankymui. Skirkite apie 45 minutes–1 valandą.', 'Vilnius', 45, 54.6842, 25.2978),
(37, 'Aleksoto kalnas', 2, 'Biržų regioninis parkas siūlo pasivaikščiojimus gamtoje ir pažintį su tvenkiniais bei ežerais. Idealu gamtos mylėtojams. Praleiskite apie 2 valandas.', 'Kaunas', 45, 54.8856, 23.9325),
(38, 'Kryžių kalnas', 2, 'Klaipėdos miesto muziejus supažindina su miesto istorija, jūrine kultūra ir architektūra. Galima aplankyti nuolatines ir laikinas parodas. Rekomenduojama apie 1–1,5 valandos.', 'Šiauliai', 75, 55.9833, 23.4167),
(39, 'Užupis', 2, 'Telšių etnografinis muziejus demonstruoja Žemaitijos regiono kultūrą ir tradicijas. Čia eksponuojami liaudies amatai ir baldai. Skirkite apie 1 valandą.', 'Vilnius', 90, 54.6825, 25.2947),
(40, 'Literatų gatvė', 2, 'Naujosios Akmenės muziejus pristato regiono istoriją ir industrinį paveldą. Puiki vieta edukacijai ir trumpam apsilankymui. Rekomenduojama apie 45 minutes–1 valandą.', 'Vilnius', 45, 54.6811, 25.29),
(41, 'Kernavės piliakalniai', 3, 'Alytus tvenkinys kviečia pasivaikščioti pakrantėmis ir stebėti gamtą. Galima žvejoti ar tiesiog pailsėti. Skirkite apie 1 valandą.', 'Kernavė', 120, 54.8742, 24.8253),
(42, 'Biržų pilis', 3, 'Ukmergės piliakalnis siūlo istorijos pamoką gamtos apsuptyje. Galima pasivaikščioti takais ir stebėti aplinką. Praleiskite apie 45 minutes.', 'Biržai', 90, 56.2014, 24.7603),
(43, 'Raudonės pilis', 3, 'Mažeikių kraštotyros muziejus supažindina su miesto ir regiono istorija. Čia eksponuojami seni dokumentai, įrankiai ir nuotraukos. Rekomenduojama apie 1 valandą.', 'Raudonė', 75, 55.155, 23.52),
(44, 'Kauno pilis', 3, 'Šilutės seniūnijos teritorijoje galima aplankyti įvairius gamtos kampelius ir senąsias statybas. Puiki vieta trumpam pasivaikščiojimui. Skirkite apie 1 valandą.', 'Kaunas', 60, 54.8953, 23.8853),
(45, 'Medininkų pilis', 3, 'Zarasų ežerų regioninis parkas žavi vandens ir miško peizažais. Galima plaukioti valtimis arba pasivaikščioti takais. Praleiskite apie 2 valandas.', 'Medininkai', 60, 54.5167, 25.2),
(46, 'Lidos pilis', 3, 'Vilniaus universiteto botanikos sodas siūlo įvairių augalų kolekcijas ir edukacines programas. Puiki vieta šeimoms ir studentams. Rekomenduojama apie 1 valandą.', 'Lida', 45, 54.5667, 25.3),
(47, 'Vilniaus universitetas', 3, 'Kupiškio kraštotyros muziejus supažindina su regiono istorija ir kultūra. Čia eksponuojami baldai, rankdarbiai ir nuotraukos. Skirkite apie 1 valandą.', 'Vilnius', 75, 54.6792, 25.2869),
(48, 'Kreivosios piliakalnis', 3, 'Raseinių miestelio senamiestis pasižymi istorine architektūra ir jaukia aplinka. Puiki vieta pasivaikščioti ir apžiūrėti pastatus. Praleiskite apie 45 minutes–1 valandą.', 'Anykščiai', 45, 55.55, 25.15),
(49, 'Šeimyniškėlių piliakalnis', 3, 'Švenčionių regioninis parkas siūlo gražius miško ir ežerų takus. Idealu gamtos mylėtojams ir šeimoms. Skirkite apie 2 valandas.', 'Šeimyniškėliai', 30, 55.75, 25.8),
(50, 'Impiltės piliakalnis', 3, 'Druskininkų miesto centras vilioja kultūrinėmis ir gastronominėmis pramogomis. Galima aplankyti kavines, galerijas ir parduotuves. Praleiskite apie 1–1,5 valandos.', 'Impiltis', 30, 55.5, 24.8),
(51, 'Vilniaus katedra', 4, 'Jonavoje esančios gamtos takai siūlo ramius pasivaikščiojimus miškuose ir parkų erdvėse. Puiki vieta šeimoms. Skirkite apie 1 valandą.', 'Vilnius', 60, 54.6857, 25.2878),
(52, 'Šv. Petro ir Povilo bažnyčia', 4, 'Panevėžio dailės galerija pristato šiuolaikinės ir klasikinės dailės parodas. Galima apžiūrėti paveikslus, skulptūras ir instaliacijas. Rekomenduojama apie 1 valandą.', 'Vilnius', 60, 54.6906, 25.2953),
(53, 'Šv. Kazimiero bažnyčia', 4, 'Šakių rajono muziejus supažindina su vietos kultūra ir istorija. Čia eksponuojami seni dokumentai, drabužiai ir rankdarbiai. Skirkite apie 1 valandą.', 'Vilnius', 45, 54.6839, 25.2886),
(54, 'Šv. Mikalojaus bažnyčia', 4, 'Varėnos miškai kviečia pasivaikščioti miško takais ir stebėti paukščius. Idealu gamtos entuziastams. Praleiskite apie 1,5–2 valandas.', 'Vilnius', 45, 54.6822, 25.2889),
(55, 'Pažaislio vienuolynas', 4, 'Kelmės kraštotyros muziejus demonstruoja regiono kultūrą, tradicijas ir architektūrą. Galima aplankyti pastatus ir parodas. Rekomenduojama apie 1 valandą.', 'Kaunas', 90, 54.9208, 23.9347),
(56, 'Kauno arkikatedra', 4, 'Alytaus miesto muziejus pristato miesto istoriją ir kultūrą. Galima apžiūrėti nuotraukas, dokumentus ir meno kūrinius. Skirkite apie 1 valandą.', 'Kaunas', 60, 54.8983, 23.9169),
(57, 'Šv. Kryžiaus bažnyčia', 4, 'Radviliškio miesto parkas siūlo pasivaikščiojimus ir poilsio zonas. Puiki vieta šeimoms ir vaikams. Praleiskite apie 1 valandą.', 'Kaunas', 45, 54.8986, 23.9144),
(58, 'Šv. Mykolo arkangelo bažnyčia', 4, 'Telšių senamiestis žavi istorine architektūra ir kultūros paminklais. Galima aplankyti aikštę, bažnyčias ir muziejus. Rekomenduojama apie 1 valandą.', 'Kaunas', 45, 54.8978, 23.9203),
(59, 'Kristaus Prisikėlimo bazilika', 4, 'Joniškio kraštotyros muziejus supažindina su regiono istorija ir kultūra. Čia eksponuojami seni baldai, dokumentai ir fotografijos. Skirkite apie 1 valandą.', 'Kaunas', 60, 54.9111, 23.9247),
(60, 'Šv. Jurgio bažnyčia', 4, 'Ukmergės miesto parkas kviečia pasivaikščioti miško ir parko takais. Puiki vieta ramiam poilsiui. Praleiskite apie 1 valandą.', 'Vilnius', 45, 54.6831, 25.2958),
(61, 'Aušros Vartų koplyčia', 4, 'Raseinių kraštotyros muziejus pristato regiono istoriją ir kultūrą. Čia eksponuojami senoviniai įrankiai, baldai ir dokumentai. Rekomenduojama apie 1 valandą ir 15 minučių.', 'Vilnius', 30, 54.6742, 25.2894),
(62, 'Šv. Onos bažnyčia', 4, 'Šilalės miestelio senamiestis pasižymi istorine architektūra ir ramia aplinka. Idealu pasivaikščiojimui ir nuotraukoms. Skirkite apie 45 minutes–1 valandą.', 'Vilnius', 45, 54.6828, 25.2961),
(63, 'Šv. Jono bažnyčia', 4, 'Kretingos parkas – puiki vieta pasivaikščioti ir stebėti gamtą. Galima aplankyti tvenkinius ir senus medžius. Praleiskite apie 1–1,5 valandos.', 'Vilnius', 30, 54.6794, 25.2875),
(64, 'Hill of Angels koplyčia', 4, 'Šalčininkų rajono muziejus supažindina su vietos kultūra, istorija ir tradicijomis. Idealu edukacijai ir trumpam apsilankymui. Skirkite apie 1 valandą.', 'Vilnius', 30, 54.68, 25.285),
(65, 'Šv. Dvasios bažnyčia', 4, 'Pagėgių kraštotyros muziejus pristato regiono istoriją ir gamtos ypatumus. Galima apžiūrėti nuotraukas ir dokumentus. Rekomenduojama apie 1 valandą.', 'Vilnius', 30, 54.6814, 25.2906),
(66, 'Grand Hotel Kempinski', 5, 'Kazlų Rūdos miškai kviečia pasivaikščioti ir stebėti gamtą. Puiki vieta gamtos mylėtojams. Skirkite apie 1,5–2 valandas.', 'Vilnius', NULL, 54.6872, 25.2794),
(67, 'Radisson Blu Lietuva', 5, 'Akmenės miesto parkas siūlo ramius pasivaikščiojimus ir poilsio zonas. Idealu šeimoms ir vaikams. Praleiskite apie 1 valandą.', 'Vilnius', NULL, 54.6889, 25.2806),
(68, 'Artagonist Art Hotel', 5, 'Švenčionių senamiestis žavi istorine architektūra ir jaukia aplinka. Galima aplankyti aikštę, bažnyčias ir muziejus. Rekomenduojama apie 1 valandą.', 'Vilnius', NULL, 54.6833, 25.2883),
(69, 'Hotel Pacai', 5, 'Marijampolės regioninis parkas siūlo pasivaikščiojimus gamtoje ir pažintį su vietos augalija bei gyvūnija. Skirkite apie 2 valandas.', 'Vilnius', NULL, 54.6789, 25.2856),
(70, 'Moxy Kaunas Center', 5, 'Pakruojo dvaras žavi architektūra, parkų erdvėmis ir muziejaus ekspozicijomis. Idealu kultūros ir istorijos mylėtojams. Praleiskite apie 1,5–2 valandas.', 'Kaunas', NULL, 54.8986, 23.9144),
(71, 'Best Western Santaka', 5, 'Vilkaviškio kraštotyros muziejus supažindina su regiono istorija, kultūra ir tradicijomis. Galima aplankyti senovinius baldus ir dokumentus. Rekomenduojama apie 1 valandą.', 'Kaunas', NULL, 54.8975, 23.9175),
(72, 'Radisson Hotel Klaipeda', 5, 'Rokiškio miesto parkas kviečia pasivaikščioti miško takais ir poilsio zonose. Puiki vieta šeimoms. Skirkite apie 1 valandą.', 'Klaipėda', NULL, 55.7036, 21.1414),
(73, 'Euterpe Hotel', 5, 'Prienų kraštotyros muziejus pristato regiono istoriją ir kultūrą. Čia eksponuojami seni baldai, dokumentai ir nuotraukos. Rekomenduojama apie 1 valandą.', 'Klaipėda', NULL, 55.71, 21.135),
(74, 'Hotel Palanga', 5, 'Mažeikių miesto parkas siūlo ramų poilsį gamtos apsuptyje. Idealu pasivaikščiojimui ir piknikams. Skirkite apie 1 valandą.', 'Palanga', NULL, 55.9181, 21.0681),
(75, 'Villa Lorentso', 5, 'Klaipėdos senamiestis kviečia pasivaikščioti senomis gatvėmis, stebėti architektūrą ir kultūros objektus. Praleiskite apie 1–1,5 valandos.', 'Palanga', NULL, 55.92, 21.07),
(76, 'City Apartments Vilnius', 6, 'Palangos miesto parkas žavi senais medžiais, fontanais ir poilsio zonomis. Puiki vieta šeimoms ir turistams. Skirkite apie 1 valandą.', 'Vilnius', NULL, 54.685, 25.285),
(77, 'Oldtown Apartments', 6, 'Kėdainių kraštotyros muziejus supažindina su miesto istorija ir kultūra. Galima apžiūrėti baldus, dokumentus ir rankdarbius. Rekomenduojama apie 1 valandą.', 'Vilnius', NULL, 54.68, 25.29),
(78, 'Bernardinu B&B House', 6, 'Šiaulių senamiestis pasižymi istorine architektūra ir kultūros paveldu. Galima aplankyti bažnyčias, aikštes ir muziejus. Skirkite apie 1 valandą.', 'Vilnius', NULL, 54.6825, 25.2947),
(79, 'Comfort Apartments', 6, 'Tauragės miesto parkas siūlo ramius pasivaikščiojimus ir poilsio zonas gamtos apsuptyje. Idealu šeimoms ir vaikams. Praleiskite apie 1 valandą.', 'Kaunas', NULL, 54.8975, 23.92),
(80, 'Park Inn Apartments', 6, 'Vilniaus Gedimino pilis kviečia susipažinti su istorija, architektūra ir miesto panorama. Galima aplankyti bokštus ir muziejų. Rekomenduojama apie 1–1,5 valandos.', 'Kaunas', NULL, 54.9, 23.91),
(81, 'Sea Apartments Klaipeda', 6, 'Šilutės senamiestis žavi istorine architektūra ir ramia aplinka. Puiki vieta pasivaikščioti ir fotografuoti. Praleiskite apie 1 valandą.', 'Klaipėda', NULL, 55.705, 21.14),
(82, 'Nida Apartments', 6, 'Biržų pilis siūlo istorijos pamoką ir gražius vaizdus į ežerą. Galima aplankyti ekspozicijas ir bokštą. Skirkite apie 1–1,5 valandos.', 'Nida', NULL, 55.3058, 21.0036),
(83, 'Trakai Lake Apartments', 6, 'Molėtų astronomijos observatorija kviečia susipažinti su žvaigždėmis ir planetomis. Galima dalyvauti edukaciniuose renginiuose. Praleiskite apie 1 valandą.', 'Trakai', NULL, 54.64, 24.935),
(84, 'Modern Stay Šiauliai', 6, 'Elektrėnų kraštotyros muziejus pristato regiono istoriją, kultūrą ir techniką. Idealu trumpam apsilankymui su šeima. Rekomenduojama apie 1 valandą.', 'Šiauliai', NULL, 55.9333, 23.3167),
(85, 'Druskininkai Spa Apartments', 6, 'Klaipėdos jūrų muziejus vilioja interaktyviais eksponatais, akvariumais ir laivais. Puiki vieta vaikams ir suaugusiems. Skirkite apie 2 valandas.', 'Druskininkai', NULL, 54.0189, 23.9739),
(86, 'Nemo Camp Palanga', 7, 'Rietavo dvaro parkas siūlo pasivaikščiojimus senų medžių alėjomis ir ramias poilsio zonas. Praleiskite apie 1 valandą.', 'Palanga', NULL, 55.915, 21.065),
(87, 'Trakai Camping', 7, 'Druskininkų vandens parkas puikiai tinka šeimoms su vaikais, siūlo baseinus ir pramogas. Skirkite apie 2 valandas.', 'Trakai', NULL, 54.645, 24.94),
(88, 'Anykščių kempingas', 7, 'Panevėžio senamiestis žavi senomis gatvėmis ir architektūra. Galima pasivaikščioti, aplankyti aikštes ir muziejus. Praleiskite apie 1 valandą.', 'Anykščiai', NULL, 55.52, 25.105),
(89, 'Molėtų kempingas', 7, 'Šiaulių Kryžių kalnas siūlo neįprastą kultūrinę patirtį ir ramybę gamtos apsuptyje. Skirkite apie 1–1,5 valandos.', 'Molėtai', NULL, 55.23, 25.42),
(90, 'Kuršių nerijos kempingas', 7, 'Kupiškio miestelio parkas vilioja ramia aplinka, takais ir poilsio zonomis. Puiki vieta šeimoms. Praleiskite apie 1 valandą.', 'Neringa', NULL, 55.38, 21.01),
(91, 'Druskininkų kempingas', 7, 'Vilniaus Valdovų rūmai pristato Lietuvos didybės istoriją, architektūrą ir meną. Galima aplankyti ekspozicijas ir parodas. Skirkite apie 1,5–2 valandas.', 'Druskininkai', NULL, 54.02, 23.975),
(92, 'Ignalinos kempingas', 7, 'Kauno IX forto muziejus supažindina su istorija ir karo laikotarpiais. Galima apžiūrėti ekspozicijas ir edukacines programas. Praleiskite apie 1–1,5 valandos.', 'Ignalina', NULL, 55.345, 26.165),
(93, 'Utenos kempingas', 7, 'Akmenės kraštotyros muziejus demonstruoja miesto istoriją ir kultūrą. Idealu trumpam apsilankymui. Skirkite apie 1 valandą.', 'Utena', NULL, 55.5, 25.6),
(94, 'Varėnos kempingas', 7, 'Zarasų senamiestis kviečia pasivaikščioti po ežerų pakrantes, aplankyti aikštę ir senus pastatus. Praleiskite apie 1 valandą.', 'Varėna', NULL, 54.22, 24.57),
(95, 'Ežero krantas', 8, 'Kėdainių senamiestis žavi istorine architektūra, aikštėmis ir bažnyčiomis. Galima aplankyti muziejus ir kavines. Skirkite apie 1 valandą.', 'Molėtai', NULL, 55.2281, 25.4172),
(96, 'Trakų sodyba', 8, 'Prienų miesto parkas siūlo pasivaikščiojimus ir poilsio zonas. Idealu šeimoms ir vaikams. Praleiskite apie 1 valandą.', 'Trakai', NULL, 54.6378, 24.9343),
(97, 'Miško namai', 8, 'Marijampolės senamiestis kviečia aplankyti aikštes, bažnyčias ir muziejus, stebėti architektūrą. Skirkite apie 1–1,5 valandos.', 'Anykščiai', NULL, 55.53, 25.11),
(98, 'Jūros vila', 8, 'Palangos botanikos parkas pristato įvairių augalų kolekcijas, tvenkinius ir poilsio zonas. Puiki vieta ramiam pasivaikščiojimui. Praleiskite apie 1 valandą.', 'Palanga', NULL, 55.925, 21.075),
(99, 'Kalnų troba', 8, 'Jurbarko kraštotyros muziejus supažindina su miesto ir regiono istorija. Galima apžiūrėti dokumentus, nuotraukas ir baldus. Skirkite apie 1 valandą.', 'Ignalina', NULL, 55.35, 26.17),
(100, 'Pušynų sodyba', 8, 'Šalčininkų senamiestis žavi senomis gatvėmis ir bažnyčiomis. Idealu pasivaikščiojimui ir nuotraukoms. Praleiskite apie 1 valandą.', 'Neringa', NULL, 55.4, 21.05),
(101, 'Nemunas Lodge', 8, 'Radviliškio senamiestis siūlo istorinius pastatus, aikštes ir ramias pasivaikščiojimo zonas. Skirkite apie 1 valandą.', 'Druskininkai', NULL, 54.015, 23.98),
(102, 'Žvejų namelis', 8, 'Šilalės miesto parkas kviečia pasivaikščioti takais, poilsio zonomis ir vaikų žaidimų aikštelėmis. Praleiskite apie 1 valandą.', 'Šilutė', NULL, 55.35, 21.4833),
(103, 'Medžių namai', 8, 'Pagėgių miestelio senamiestis žavi istorine architektūra ir ramia aplinka. Galima pasivaikščioti ir aplankyti vietos muziejus. Skirkite apie 1 valandą.', 'Biržai', NULL, 56.21, 24.77),
(104, 'Gamtos prieglobstis', 8, 'Kazlų Rūdos miestelio parkas siūlo ramius pasivaikščiojimus, poilsio zonas ir gamtos stebėjimą. Praleiskite apie 1 valandą.', 'Varėna', NULL, 54.23, 24.58),
(105, 'Bernardinų sodas', 2, 'Rokiškio senamiestis kviečia aplankyti aikštę, bažnyčias ir senus pastatus. Idealu pasivaikščioti ir stebėti architektūrą. Skirkite apie 1 valandą.', 'Vilnius', 60, 54.685, 25.2967),
(106, 'Halės turgus', 2, 'Vilkaviškio senamiestis žavi senomis gatvėmis ir kultūros paminklais. Galima aplankyti muziejus, bažnyčias ir aikštes. Praleiskite apie 1 valandą.', 'Vilnius', 60, 54.6825, 25.2844),
(107, 'Pilies gatvė', 2, 'Telšių miesto parkas siūlo pasivaikščiojimus, poilsio zonas ir gamtos stebėjimą. Idealu šeimoms ir vaikams. Skirkite apie 1 valandą.', 'Vilnius', 45, 54.6844, 25.2889),
(108, 'Kauno senamiesčio rotušė', 3, 'Joniškio senamiestis kviečia pasivaikščioti istorine aplinka, aplankyti aikštę ir bažnyčias. Praleiskite apie 1 valandą.', 'Kaunas', 60, 54.8986, 23.9242),
(109, 'Laisvės alėja', 2, 'Ukmergės senamiestis siūlo pasivaikščioti senomis gatvėmis, aplankyti bažnyčias ir muziejus. Skirkite apie 1 valandą.', 'Kaunas', 75, 54.8958, 23.9153),
(110, 'Santakos parkas', 2, 'Raseinių senamiestis žavi istorine architektūra, aikštėmis ir ramia aplinka. Idealu pasivaikščioti ir stebėti pastatus. Praleiskite apie 1 valandą.', 'Kaunas', 90, 54.9019, 23.9189),
(111, 'Senamiesčio aikštė Klaipėda', 2, 'Šilutės miesto parkas kviečia pasivaikščioti gamtos apsuptyje ir aplankyti poilsio zonas. Skirkite apie 1 valandą.', 'Klaipėda', 60, 55.7033, 21.1417),
(112, 'Danės krantinė', 2, 'Kretingos senamiestis siūlo aplankyti aikštę, bažnyčias ir istorinius pastatus. Puiki vieta pasivaikščioti ir fotografuoti. Praleiskite apie 1 valandą.', 'Klaipėda', 45, 55.7072, 21.1331),
(113, 'Skulptūra \"Ännchen von Tharau\"', 2, 'Šalčininkų miesto parkas siūlo ramius pasivaikščiojimus, poilsio zonas ir vaikų žaidimų aikšteles. Skirkite apie 1 valandą.', 'Klaipėda', 15, 55.7031, 21.1419),
(114, 'Palangos botanikos parkas', 2, 'Pakruojo senamiestis kviečia aplankyti aikštę, bažnyčias ir muziejus, stebėti architektūrą. Praleiskite apie 1 valandą.', 'Palanga', 120, 55.9108, 21.0569),
(115, 'Palangos tiltas', 2, 'Vilkaviškio parkas siūlo ramius pasivaikščiojimus, poilsio zonas ir gamtos stebėjimą. Idealu šeimoms ir vaikams. Skirkite apie 1 valandą.', 'Palanga', 30, 55.9194, 21.0581),
(116, 'Basanavičiaus gatvė', 2, 'Radviliškio kraštotyros muziejus supažindina su miesto ir regiono istorija bei kultūra. Galima apžiūrėti dokumentus ir nuotraukas. Praleiskite apie 1 valandą.', 'Palanga', 45, 55.9169, 21.0647),
(117, 'Anykščių koplyčiakalnis', 3, 'Šilalės senamiestis kviečia pasivaikščioti istorine aplinka, aplankyti aikštę ir bažnyčias. Skirkite apie 1 valandą.', 'Anykščiai', 45, 55.525, 25.11),
(118, 'Puntukas', 2, 'Kretingos parkas siūlo ramius pasivaikščiojimus, tvenkinius ir poilsio zonas. Idealu šeimoms. Praleiskite apie 1 valandą.', 'Anykščiai', 30, 55.5306, 25.0992),
(119, 'Šventosios šaltiniai', 2, 'Pagėgių senamiestis žavi istorine architektūra ir ramia aplinka. Galima pasivaikščioti ir aplankyti vietos muziejų. Skirkite apie 1 valandą.', 'Anykščiai', 30, 55.528, 25.108),
(120, 'Utenos ežero apkalnės', 2, 'Kazlų Rūdos senamiestis kviečia pasivaikščioti takais, aplankyti aikštę ir senus pastatus. Idealu trumpam poilsiui. Praleiskite apie 1 valandą.', 'Utena', 90, 55.51, 25.61),
(121, 'Molėtų astronomijos observatorija', 1, 'Rokiškio parkas siūlo pasivaikščiojimus, poilsio zonas ir gamtos stebėjimą. Puiki vieta šeimoms ir vaikams. Skirkite apie 1 valandą.', 'Molėtai', 90, 55.22, 25.41),
(122, 'Šiaulių Kryžių kalnas', 2, 'Vilkaviškio senamiestis kviečia pasivaikščioti istorine aplinka ir aplankyti muziejus. Idealu fotografijoms. Praleiskite apie 1 valandą.', 'Šiauliai', 75, 55.9833, 23.4167),
(123, 'Šiaulių katedral', 4, 'Telšių senamiestis žavi senomis gatvėmis, aikštėmis ir bažnyčiomis. Galima aplankyti muziejus ir kavines. Skirkite apie 1 valandą.', 'Šiauliai', 45, 55.9342, 23.3153),
(124, 'Šiaulių universitetas', 3, 'Joniškio parkas kviečia pasivaikščioti, stebėti gamtą ir naudotis poilsio zonomis. Idealu šeimoms. Praleiskite apie 1 valandą.', 'Šiauliai', 60, 55.9258, 23.3542),
(125, 'Radviliškio geležinkelio muziejus', 1, 'Ukmergės kraštotyros muziejus supažindina su miesto ir regiono istorija. Galima apžiūrėti dokumentus, nuotraukas ir baldus. Skirkite apie 1 valandą.', 'Radviliškis', 60, 55.8167, 23.5333),
(126, 'Panevėžio laisvės aikštė', 2, 'Raseinių senamiestis kviečia pasivaikščioti takais, aplankyti aikštę ir bažnyčias. Idealu trumpam poilsiui. Praleiskite apie 1 valandą.', 'Panevėžys', 45, 55.7333, 24.35),
(127, 'Panevėžio dramos teatras', 1, 'Šilutės parkas siūlo ramius pasivaikščiojimus, poilsio zonas ir gamtos stebėjimą. Skirkite apie 1 valandą.', 'Panevėžys', 90, 55.735, 24.355),
(128, 'Rokiškio dvaras', 3, 'Kretingos senamiestis kviečia aplankyti aikštę, bažnyčias ir senus pastatus. Idealu pasivaikščioti ir fotografuoti. Praleiskite apie 1 valandą.', 'Rokiškis', 75, 55.9667, 25.5833),
(129, 'Pasvalio muziejus', 1, 'Šalčininkų senamiestis siūlo ramius pasivaikščiojimus, aplankyti muziejus ir istorinius pastatus. Skirkite apie 1 valandą.', 'Pasvalys', 60, 56.0667, 24.4),
(130, 'Kupiškio etnografijos muziejus', 1, 'Pakruojo parkas kviečia pasivaikščioti miško takais, poilsio zonomis ir stebėti gamtą. Idealu šeimoms. Praleiskite apie 1 valandą.', 'Kupiškis', 60, 55.8333, 24.9833),
(131, 'Joniškio kultūros centras', 1, 'Vilkaviškio kraštotyros muziejus pristato regiono istoriją ir kultūrą. Galima apžiūrėti dokumentus, nuotraukas ir baldus. Skirkite apie 1 valandą.', 'Joniškis', 60, 56.2333, 23.6167),
(132, 'Mažeikių muziejus', 1, 'Radviliškio parkas siūlo pasivaikšiojimus, poilsio zonas ir gamtos stebėjimą. Idealu šeimoms ir vaikams. Praleiskite apie 1 valandą.', 'Mažeikiai', 60, 56.3167, 22.3333),
(133, 'Plungės dvaras', 3, 'Šilalės senamiestis kviečia pasivaikščioti istorine aplinka, aplankyti aikštę ir bažnyčias. Skirkite apie 1 valandą.', 'Plungė', 90, 55.9167, 21.85),
(134, 'Telšių katedra', 4, 'Kretingos parkas kviečia pasivaikščioti gamtos takais, stebėti tvenkinius ir poilsio zonas. Idealu šeimoms. Praleiskite apie 1 valandą.', 'Telšiai', 60, 55.9833, 22.25),
(135, 'Šilutės etnokosmologijos centras', 1, 'Pagėgių senamiestis žavi istorine architektūra ir ramiomis gatvėmis. Galima aplankyti vietos muziejų. Skirkite apie 1 valandą.', 'Šilutė', 90, 55.35, 21.4833),
(136, 'Jurbarko dvaras', 3, 'Kazlų Rūdos senamiestis kviečia pasivaikščioti takais, aplankyti aikštę ir senus pastatus. Idealu trumpam poilsiui. Praleiskite apie 1 valandą.', 'Jurbarkas', 60, 55.0833, 22.7667),
(137, 'Šakių kraštovaizdžio draustinis', 2, 'Rokiškio parkas siūlo pasivaikšiojimus, poilsio zonas ir gamtos stebėjimą. Puiki vieta šeimoms ir vaikams. Skirkite apie 1 valandą.', 'Šakiai', 120, 54.95, 23.05),
(138, 'Vilkaviškio bažnyčia', 4, 'Vilkaviškio senamiestis kviečia pasivaikščioti istorine aplinka ir aplankyti muziejus. Idealu fotografijoms. Praleiskite apie 1 valandą.', 'Vilkaviškis', 45, 54.65, 23.0333),
(139, 'Kalvarijos špitolės bažnyčia', 4, 'Telšių senamiestis žavi senomis gatvėmis, aikštėmis ir bažnyčiomis. Galima aplankyti muziejus ir kavines. Skirkite apie 1 valandą.', 'Kalvarija', 45, 54.4167, 23.2167),
(140, 'Lazdijai etnografijos muziejus', 1, 'Joniškio parkas kviečia pasivaikščioti, stebėti gamtą ir naudotis poilsio zonomis. Idealu šeimoms. Praleiskite apie 1 valandą.', 'Lazdijai', 60, 54.2333, 23.5167),
(141, 'muziejus custom', 1, 'labai idomus tokio dar nematet reikia aplankyt nepasigailesit labai unikalios ekspozicijos top muziejus vilniaus', 'Vilnius', 50, 54.2158, 26.8542);

-- --------------------------------------------------------

--
-- Table structure for table `objektu_tipai`
--

CREATE TABLE `objektu_tipai` (
  `tipo_id` int NOT NULL,
  `pavadinimas` varchar(50) NOT NULL,
  `ar_nakyvne` tinyint(1) DEFAULT '0' COMMENT '1=nakvynė, 0=lankytinas objektas'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `objektu_tipai`
--

INSERT INTO `objektu_tipai` (`tipo_id`, `pavadinimas`, `ar_nakyvne`) VALUES
(1, 'Muziejus', 0),
(2, 'Gamtos objektas', 0),
(3, 'Pilis', 0),
(4, 'Bažnyčia', 0),
(5, 'Viešbutis', 1),
(6, 'Apartamentai', 1),
(7, 'Kempingas', 1),
(8, 'Atostogų namelis', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(30) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `userid` varchar(32) NOT NULL,
  `userlevel` tinyint UNSIGNED DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dailyminutes` int NOT NULL DEFAULT '300'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`, `userid`, `userlevel`, `email`, `timestamp`, `dailyminutes`) VALUES
('Administratorius', '6e5b5410415bde908bd4dee15dfb167a', 'a2fe399900de341c39c632244eaf8483', 9, 'admin@test.lt', '2025-12-14 18:30:18', 200),
('keliautojas', 'c2acd92812ef99acd3dcdbb746b9a434', 'b3fe399900de341c39c632244eaf8484', 4, 'keliautojas@test.lt', '2025-12-14 18:50:49', 100),
('gintare', '6a68cfe3126f036f74e9a98a64cc6422', '20204e9ebebbcc11f9c5286605745200', 4, 'ginjas2@ktu.lt', '2025-11-17 10:14:00', 300),
('gintaree', 'c2acd92812ef99acd3dcdbb746b9a434', 'c75600ad110de61081640108b4b5a976', 4, 'ginjas2@ktu.lt', '2025-12-01 01:14:38', 400),
('gidas1', 'c2acd92812ef99acd3dcdbb746b9a434', 'e4cb56088688f8ff2dd8ed0ed777c89c', 5, 'burgerrr@gmail.com', '2025-12-14 19:25:37', 300),
('orga', 'c2acd92812ef99acd3dcdbb746b9a434', '6557aa8079740ea8dba1e8da2c5153d3', 5, 'gaga@ktu.com', '2025-12-01 01:12:25', 400);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atsiliepimai`
--
ALTER TABLE `atsiliepimai`
  ADD PRIMARY KEY (`atsiliepimo_id`);

--
-- Indexes for table `atstumas`
--
ALTER TABLE `atstumas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `objekto1_id` (`objekto1_id`),
  ADD KEY `objekto2_id` (`objekto2_id`);

--
-- Indexes for table `forumo_pranesimal`
--
ALTER TABLE `forumo_pranesimal`
  ADD PRIMARY KEY (`pranesimo_id`);

--
-- Indexes for table `komentaras`
--
ALTER TABLE `komentaras`
  ADD PRIMARY KEY (`komentaro_id`),
  ADD KEY `pranesimo_id` (`pranesimo_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `objektai`
--
ALTER TABLE `objektai`
  ADD PRIMARY KEY (`objekto_id`);

--
-- Indexes for table `objektu_tipai`
--
ALTER TABLE `objektu_tipai`
  ADD PRIMARY KEY (`tipo_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atsiliepimai`
--
ALTER TABLE `atsiliepimai`
  MODIFY `atsiliepimo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `atstumas`
--
ALTER TABLE `atstumas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `forumo_pranesimal`
--
ALTER TABLE `forumo_pranesimal`
  MODIFY `pranesimo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `komentaras`
--
ALTER TABLE `komentaras`
  MODIFY `komentaro_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `objektai`
--
ALTER TABLE `objektai`
  MODIFY `objekto_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `objektu_tipai`
--
ALTER TABLE `objektu_tipai`
  MODIFY `tipo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
