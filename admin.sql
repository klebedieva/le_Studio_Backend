-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 14, 2025 at 02:48 PM
-- Server version: 11.5.2-MariaDB
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

DROP TABLE IF EXISTS `blog`;
CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `views` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `title`, `content`, `created_at`, `updated_at`, `author_id`, `is_published`, `views`) VALUES
(36, 'Cardio & corde : un entraînement qui fait la différence', '<p data-start=\"333\" data-end=\"495\">Zoom sur notre <strong data-start=\"348\" data-end=\"375\">atelier de conditioning</strong> avec les <em data-start=\"385\" data-end=\"399\">battle ropes</em>, une discipline redoutablement efficace qui allie <strong data-start=\"450\" data-end=\"459\">force</strong>, <em data-start=\"461\" data-end=\"474\">explosivit&eacute;</em> et <strong data-start=\"478\" data-end=\"491\">endurance</strong>.</p>\r\n<p data-start=\"497\" data-end=\"838\">Les mouvements rapides, rythm&eacute;s et puissants activent l&rsquo;ensemble du corps, en particulier les <strong data-start=\"591\" data-end=\"602\">&eacute;paules</strong>, les <strong data-start=\"608\" data-end=\"616\">bras</strong> et la <strong data-start=\"623\" data-end=\"646\">ceinture abdominale</strong>. <em data-start=\"648\" data-end=\"675\">D&egrave;s les premi&egrave;res minutes</em>, la sueur commence &agrave; couler, les muscles br&ucirc;lent, et le rythme cardiaque s&rsquo;envole. C&rsquo;est l\'entra&icirc;nement parfait pour celles et ceux qui aiment relever des d&eacute;fis !</p>\r\n<p data-start=\"840\" data-end=\"1055\">🔥 <em data-start=\"843\" data-end=\"865\">Pourquoi &ccedil;a marche ?</em> Parce que les battle ropes sollicitent &agrave; la fois le syst&egrave;me cardiovasculaire et musculaire. En peu de temps, vous am&eacute;liorez votre condition physique globale tout en renfor&ccedil;ant votre mental.</p>\r\n<p data-start=\"1057\" data-end=\"1252\">🎯 Encadr&eacute;s par nos <strong data-start=\"1077\" data-end=\"1104\">coach&middot;es exp&eacute;riment&eacute;&middot;es</strong>, vous apprendrez &agrave; ma&icirc;triser les diff&eacute;rentes techniques &mdash; vagues, slams, cercles &mdash; pour tirer <strong data-start=\"1199\" data-end=\"1232\">le meilleur de chaque session</strong>, en toute s&eacute;curit&eacute;.</p>\r\n<p data-start=\"1254\" data-end=\"1399\">💪 <em data-start=\"1257\" data-end=\"1310\">Une s&eacute;ance courte, intense et terriblement efficace</em>, id&eacute;ale pour booster son m&eacute;tabolisme et br&ucirc;ler des calories&hellip; m&ecirc;me apr&egrave;s l&rsquo;entra&icirc;nement !</p>\r\n<p data-start=\"1401\" data-end=\"1574\">👉 D&eacute;couvrez toutes nos sessions &laquo; cardio &amp; corde &raquo; dans la rubrique <a class=\"\" href=\"#\" rel=\"noopener\" data-start=\"1470\" data-end=\"1493\">Prochaines s&eacute;ances</a> et r&eacute;servez votre place d&egrave;s maintenant via notre <a class=\"\" href=\"registration.php\" rel=\"noopener\" data-start=\"1442\" data-end=\"1486\">formulaire d\'inscription</a> !</p>', '2025-07-10 12:56:00', '2025-07-10 14:37:12', 10, 1, 0),
(35, 'Séance de groupe : l’entraînement avec médecine ball', '<p data-start=\"237\" data-end=\"581\">Notre derni&egrave;re <strong data-start=\"252\" data-end=\"272\">s&eacute;ance de groupe</strong> avec <em data-start=\"278\" data-end=\"293\">m&eacute;decine ball</em> a &eacute;t&eacute; un v&eacute;ritable concentr&eacute; d&rsquo;&eacute;nergie, de sueur et de bonne humeur. Pendant pr&egrave;s d\'une heure, les participants ont encha&icirc;n&eacute; des exercices dynamiques, m&ecirc;lant <strong data-start=\"452\" data-end=\"461\">force</strong>, <em data-start=\"463\" data-end=\"477\">coordination</em> et <strong data-start=\"481\" data-end=\"496\">explosivit&eacute;</strong>. L&rsquo;objectif ? Travailler <em data-start=\"522\" data-end=\"543\">l&rsquo;ensemble du corps</em> tout en renfor&ccedil;ant l&rsquo;esprit d&rsquo;&eacute;quipe.</p>\r\n<p data-start=\"583\" data-end=\"823\"><em data-start=\"586\" data-end=\"604\">Le m&eacute;decine ball</em>, souvent sous-estim&eacute;, est pourtant un outil redoutablement efficace. Gr&acirc;ce &agrave; lui, chaque mouvement devient plus intense : lancers, squats, rotations, pompes avec charge... tout y passe ! Ce type d&rsquo;entra&icirc;nement permet :</p>\r\n<ul data-start=\"825\" data-end=\"971\">\r\n<li data-start=\"825\" data-end=\"871\">\r\n<p data-start=\"827\" data-end=\"871\">de d&eacute;velopper la <strong data-start=\"844\" data-end=\"868\">puissance musculaire</strong>,</p>\r\n</li>\r\n<li data-start=\"872\" data-end=\"923\">\r\n<p data-start=\"874\" data-end=\"923\">d&rsquo;am&eacute;liorer la <strong data-start=\"889\" data-end=\"920\">r&eacute;sistance cardiovasculaire</strong>,</p>\r\n</li>\r\n<li data-start=\"924\" data-end=\"971\">\r\n<p data-start=\"926\" data-end=\"971\">et d&rsquo;augmenter la <strong data-start=\"944\" data-end=\"970\">mobilit&eacute; fonctionnelle</strong>.</p>\r\n</li>\r\n</ul>\r\n<p data-start=\"973\" data-end=\"1215\">Mais ce n&rsquo;est pas tout. L&rsquo;un des grands avantages de l&rsquo;entra&icirc;nement avec m&eacute;decine ball, c&rsquo;est sa <strong data-start=\"1070\" data-end=\"1105\">dimension ludique et collective</strong>. On s&rsquo;encourage, on se d&eacute;passe, <em data-start=\"1138\" data-end=\"1161\">on progresse ensemble</em>. L&rsquo;&eacute;nergie du groupe devient un moteur de motivation.</p>\r\n<p data-start=\"1217\" data-end=\"1446\">📸 <em data-start=\"1220\" data-end=\"1259\">Petit aper&ccedil;u de la session en image ?</em> Retrouvez quelques moments forts sur notre <a href=\"https://instagram.com\" target=\"_new\" data-start=\"1303\" data-end=\"1345\">galerie Instagram</a> ou dans la rubrique <a href=\"#\" data-start=\"1366\" data-end=\"1386\">Galerie du site</a>. Vous y verrez des sourires&hellip; et beaucoup de concentration !</p>\r\n<p data-start=\"1448\" data-end=\"1624\">👉 Envie de rejoindre notre prochaine s&eacute;ance ? Consultez le planning dans la section <a href=\"#\" data-start=\"1533\" data-end=\"1556\">Prochaines s&eacute;ances</a> ou contactez-nous directement via le <a href=\"registration.php\" data-start=\"1442\" data-end=\"1486\">formulaire d\'inscription</a>.</p>\r\n<p data-start=\"1626\" data-end=\"1875\"><strong data-start=\"1626\" data-end=\"1642\">Conclusion :</strong><br data-start=\"1642\" data-end=\"1645\">Une s&eacute;ance intense, motivante et id&eacute;ale pour repousser ses limites&hellip; <em data-start=\"1713\" data-end=\"1723\">ensemble</em>. Venez vivre l&rsquo;exp&eacute;rience par vous-m&ecirc;me et d&eacute;couvrir tout ce que le <strong data-start=\"1792\" data-end=\"1809\">m&eacute;decine ball</strong> peut apporter &agrave; votre forme physique et &agrave; votre esprit d&rsquo;&eacute;quipe !</p>', '2025-07-10 12:11:50', '2025-07-14 11:17:00', 10, 1, 0),
(38, 'Courez vers le progrès : le plaisir du running', '<p data-start=\"98\" data-end=\"249\"><em data-start=\"104\" data-end=\"120\">Zoom sur notre</em> <strong data-start=\"121\" data-end=\"140\">atelier running</strong>, une discipline <strong data-start=\"157\" data-end=\"171\">accessible</strong>, <strong data-start=\"173\" data-end=\"184\">motiv&eacute;e</strong> et <strong data-start=\"188\" data-end=\"200\">efficace</strong> pour booster la forme, le mental et l&rsquo;endurance.</p>\r\n<p data-start=\"251\" data-end=\"518\">Pas besoin de machines sophistiqu&eacute;es : une bonne paire de baskets suffit pour vous lancer sur la piste ou en plein air. D&egrave;s les premi&egrave;res foul&eacute;es, <em data-start=\"398\" data-end=\"458\">le corps s&rsquo;&eacute;veille, le souffle se cale, l&rsquo;esprit se lib&egrave;re</em>.</p>\r\n<p data-start=\"251\" data-end=\"518\"><img src=\"../le_Studio_Backend/uploads/articles/img_6874c852992f8.jpg\" alt=\"\" width=\"512\" height=\"341\"> &nbsp;<img src=\"../le_Studio_Backend/uploads/articles/img_6874c8620c49c.jpg\" alt=\"\" width=\"517\" height=\"343\"></p>\r\n<p data-start=\"251\" data-end=\"518\">C&rsquo;est bien plus qu&rsquo;un sport &mdash;&nbsp;<strong data-start=\"492\" data-end=\"517\">c&rsquo;est un moment &agrave; soi</strong>.</p>\r\n<p data-start=\"520\" data-end=\"747\">🔥 <strong data-start=\"523\" data-end=\"547\">Pourquoi &ccedil;a marche ?</strong> Parce que le running sollicite <strong data-start=\"579\" data-end=\"596\">tout le corps</strong> tout en stimulant <strong data-start=\"615\" data-end=\"628\">le mental</strong>. On am&eacute;liore son endurance, on br&ucirc;le des calories, on renforce son c&oelig;ur &mdash; <em data-start=\"703\" data-end=\"746\">et surtout, on se d&eacute;passe &agrave; chaque sortie</em>.</p>\r\n<p data-start=\"749\" data-end=\"941\">🎯 <em data-start=\"752\" data-end=\"773\">Encadr&eacute;&middot;e&middot;s par nos</em> <strong data-start=\"774\" data-end=\"801\">coach&middot;es exp&eacute;riment&eacute;&middot;es</strong>, vous b&eacute;n&eacute;ficiez de conseils personnalis&eacute;s &mdash; &eacute;chauffement, posture, respiration, gestion de l&rsquo;effort &mdash; pour progresser <em data-start=\"921\" data-end=\"940\">en toute s&eacute;curit&eacute;</em>.</p>\r\n<p data-start=\"943\" data-end=\"1108\">💪 Une s&eacute;ance <strong data-start=\"957\" data-end=\"970\">adaptable</strong>, <strong data-start=\"972\" data-end=\"985\">&eacute;volutive</strong> et <em data-start=\"989\" data-end=\"1014\">hautement satisfaisante</em>, id&eacute;ale pour tous les niveaux&hellip; et parfaite pour lib&eacute;rer l&rsquo;esprit tout en sculptant le corps !</p>\r\n<p data-start=\"1110\" data-end=\"1291\">📅 D&eacute;couvrez toutes nos sessions &laquo; running &raquo; dans la rubrique <strong data-start=\"1172\" data-end=\"1194\">Prochaines s&eacute;ances</strong> et r&eacute;servez votre place d&egrave;s maintenant via notre&nbsp;<a href=\"registration.php\" data-start=\"1247\" data-end=\"1291\">formulaire d\'inscription</a></p>', '2025-07-10 13:11:52', '2025-07-14 11:13:14', 31, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `blog_images`
--

DROP TABLE IF EXISTS `blog_images`;
CREATE TABLE IF NOT EXISTS `blog_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `blog_images`
--

INSERT INTO `blog_images` (`id`, `blog_id`, `filename`) VALUES
(40, 36, 'img_686fa810b96d1.jpg'),
(39, 36, 'img_686fa7da345a8.jpg'),
(37, 36, '1752144960_news2.jpg'),
(38, 38, '1752145912_news3.jpg'),
(36, 35, '1752142310_news1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `blog_tags`
--

DROP TABLE IF EXISTS `blog_tags`;
CREATE TABLE IF NOT EXISTS `blog_tags` (
  `blog_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`blog_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `blog_tags`
--

INSERT INTO `blog_tags` (`blog_id`, `tag_id`) VALUES
(35, 1),
(35, 2),
(35, 3),
(36, 2),
(36, 3),
(38, 1),
(38, 3),
(38, 8);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id_contact` int(11) NOT NULL AUTO_INCREMENT,
  `name_contact` varchar(255) DEFAULT NULL,
  `surname_contact` varchar(255) DEFAULT NULL,
  `email_contact` varchar(255) DEFAULT NULL,
  `subject_contact` varchar(255) DEFAULT NULL,
  `creation_date_contact` date DEFAULT NULL,
  `status_contact` enum('Nouveau','Lu','Répondu') DEFAULT NULL,
  `message_contact` text DEFAULT NULL,
  `phone_contact` varchar(20) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_contact`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id_contact`, `name_contact`, `surname_contact`, `email_contact`, `subject_contact`, `creation_date_contact`, `status_contact`, `message_contact`, `phone_contact`, `id_user`) VALUES
(1, 'Jean', 'Dupont', 'jean.dupont@email.com', 'Demande d\'information produit', '2025-06-23', 'Répondu', 'Bonjour, je souhaite des infos sur votre produit.', '0600000001', 2),
(3, 'Pierre', 'Martin', 'pierre.martin@email.com', 'Réclamation commande', '2025-06-21', 'Nouveau', 'Je souhaite faire une réclamation sur ma commande.', '0600000003', 4),
(4, 'Sophie', 'Bernard', 'sophie.bernard@email.com', 'Partenariat commercial', '2025-06-20', 'Lu', 'Je suis intéressée par un partenariat.', '0600000004', 5),
(5, 'Antoine', 'Legrand', 'antoine.legrand@email.com', 'Question facturation', '2025-06-19', 'Nouveau', 'Je souhaite des précisions sur la facture.', '0600000005', NULL),
(6, 'Gaga', 'Lady', 'ladygaga@test.com', 'Hello Test', '2025-06-30', 'Lu', 'Test Test Test', '0111111111', NULL),
(25, 'Manon', 'Dubois', 'manon.dubois@test.com', 'Test 01', '2025-07-04', 'Nouveau', 'Test 01', NULL, 7);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id_session` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `session_type` varchar(100) NOT NULL,
  `trainer_name` varchar(100) DEFAULT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_session`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id_session`, `id_user`, `session_type`, `trainer_name`, `session_date`, `session_time`, `duration`, `created_at`) VALUES
(1, 7, 'HIIT', 'Coach Pierre', '2025-07-05', '10:00:00', '60 min', '2025-07-04 08:16:31'),
(2, 7, 'Yoga', 'Coach Sarah', '2025-07-06', '18:30:00', '45 min', '2025-07-04 08:16:31'),
(3, 7, 'CrossFit', 'Coach Marc', '2025-07-08', '19:00:00', '30 min', '2025-07-04 08:16:31'),
(4, NULL, 'Pilates', 'Coach Julie', '2025-07-04', '09:00:00', '45 min', '2025-07-04 08:28:59'),
(5, NULL, 'Yoga', 'Coach Sarah', '2025-07-04', '10:30:00', '60 min', '2025-07-04 08:28:59'),
(6, NULL, 'CrossFit', 'Coach Marc', '2025-07-04', '12:00:00', '30 min', '2025-07-04 08:28:59'),
(7, NULL, 'BoxFit', 'Coach Emma', '2025-07-04', '17:30:00', '45 min', '2025-07-04 08:28:59'),
(8, NULL, 'Cycling', 'Coach Tom', '2025-07-04', '19:00:00', '40 min', '2025-07-04 08:28:59');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_type` varchar(100) DEFAULT NULL,
  `subscription_start` date DEFAULT NULL,
  `subscription_end` date DEFAULT NULL,
  `weekly_sessions` int(11) DEFAULT NULL,
  `monthly_price` decimal(8,2) DEFAULT NULL,
  `status` enum('Actif','Expiré','Annulé') DEFAULT 'Actif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `subscription_type`, `subscription_start`, `subscription_end`, `weekly_sessions`, `monthly_price`, `status`) VALUES
(1, 'Essential Plan 6 months', '2025-06-12', '2025-12-12', 4, 59.99, 'Actif'),
(2, 'Formule Premium 12 mois', '2025-07-01', '2026-06-30', 7, 89.99, 'Actif'),
(3, 'Formule Basique 3 mois', '2025-06-15', '2025-09-15', 2, 39.99, 'Actif');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id_tag` int(11) NOT NULL AUTO_INCREMENT,
  `name_tag` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `name_tag` (`name_tag`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id_tag`, `name_tag`) VALUES
(1, 'CrossFit'),
(2, 'Fitness'),
(3, 'Entraînement'),
(4, 'Cardio'),
(5, 'Yoga'),
(6, 'HIIT'),
(7, 'Nutrition'),
(8, 'Bien-être');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `name_user` varchar(255) DEFAULT NULL,
  `surname_user` varchar(255) DEFAULT NULL,
  `email_user` varchar(255) DEFAULT NULL,
  `password_user` varchar(255) NOT NULL,
  `subscription_date_user` date DEFAULT NULL,
  `role_user` enum('Administrateur','Utilisateur','Modérateur') NOT NULL DEFAULT 'Utilisateur',
  `status_user` enum('Actif','Inactif','Suspendu') NOT NULL DEFAULT 'Actif',
  `subscription_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  KEY `subscription_id` (`subscription_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `name_user`, `surname_user`, `email_user`, `password_user`, `subscription_date_user`, `role_user`, `status_user`, `subscription_id`) VALUES
(2, 'Jean', 'Dupont', 'jean.dupont@email.com', '', '2025-03-10', 'Modérateur', 'Actif', NULL),
(4, 'Pierre', 'Martin', 'pierre.martin@email.com', '', '2025-04-05', 'Utilisateur', 'Inactif', NULL),
(5, 'Sophie', 'Bernard', 'sophie.bernard@email.com', '', '2025-05-18', 'Utilisateur', 'Actif', NULL),
(7, 'Manon', 'Dubois', 'manon.dubois@test.com', '$2y$10$LRDKehkpVdvdfiyE7ZzqDOfF7v8E6vnAOfY.76dhtbs3.HkHucVHK', '2025-06-30', 'Utilisateur', 'Actif', 1),
(10, 'Super', 'Admin', 'admin.test@test.com', '$2y$10$hxidHiipuZydm2oWmWAEBuXDvxfyC1v2AVsR5zPkWgRRoeTFcMP1y', '2025-06-30', 'Administrateur', 'Actif', NULL),
(31, 'Julie', 'Roux', 'julie.roux@example.com', '$2y$10$2qBowSP2Qnm7o3VO8jseIOP3fXu5zdmqhO.kveZNrDCT/qSNXJ3dW', '2025-07-08', 'Modérateur', 'Actif', NULL),
(32, 'Antoine', 'Fournier', 'antoine.fournier@example.com', '$2y$10$rJBu5at8XJiDBQdXJHhs9O17OiUhzKi4eWBHcKiFDJDufWD8eAxhW', '2025-07-08', 'Modérateur', 'Actif', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON UPDATE SET NULL;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `subscription_id` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
