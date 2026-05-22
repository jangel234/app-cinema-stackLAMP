/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db    Database: cine
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `asientos`
--

DROP TABLE IF EXISTS `asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `asientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sala_id` int NOT NULL,
  `fila` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int NOT NULL,
  `tipo` enum('normal','preferencial','discapacitado') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sala_id` (`sala_id`,`fila`,`numero`),
  CONSTRAINT `asientos_ibfk_1` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=649 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asientos`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `asientos` WRITE;
/*!40000 ALTER TABLE `asientos` DISABLE KEYS */;
INSERT INTO `asientos` VALUES
(1,1,'A',9,'normal'),
(2,1,'B',9,'normal'),
(3,1,'C',9,'normal'),
(4,1,'D',9,'normal'),
(5,1,'E',9,'normal'),
(6,1,'F',9,'normal'),
(7,1,'G',9,'normal'),
(8,1,'H',9,'normal'),
(9,1,'I',9,'normal'),
(10,1,'A',8,'normal'),
(11,1,'B',8,'normal'),
(12,1,'C',8,'normal'),
(13,1,'D',8,'normal'),
(14,1,'E',8,'normal'),
(15,1,'F',8,'normal'),
(16,1,'G',8,'normal'),
(17,1,'H',8,'normal'),
(18,1,'I',8,'normal'),
(19,1,'A',7,'normal'),
(20,1,'B',7,'normal'),
(21,1,'C',7,'normal'),
(22,1,'D',7,'normal'),
(23,1,'E',7,'normal'),
(24,1,'F',7,'normal'),
(25,1,'G',7,'normal'),
(26,1,'H',7,'normal'),
(27,1,'I',7,'normal'),
(28,1,'A',6,'normal'),
(29,1,'B',6,'normal'),
(30,1,'C',6,'normal'),
(31,1,'D',6,'normal'),
(32,1,'E',6,'normal'),
(33,1,'F',6,'normal'),
(34,1,'G',6,'normal'),
(35,1,'H',6,'normal'),
(36,1,'I',6,'normal'),
(37,1,'A',5,'normal'),
(38,1,'B',5,'normal'),
(39,1,'C',5,'normal'),
(40,1,'D',5,'normal'),
(41,1,'E',5,'normal'),
(42,1,'F',5,'normal'),
(43,1,'G',5,'normal'),
(44,1,'H',5,'normal'),
(45,1,'I',5,'normal'),
(46,1,'A',4,'normal'),
(47,1,'B',4,'normal'),
(48,1,'C',4,'normal'),
(49,1,'D',4,'normal'),
(50,1,'E',4,'normal'),
(51,1,'F',4,'normal'),
(52,1,'G',4,'normal'),
(53,1,'H',4,'normal'),
(54,1,'I',4,'normal'),
(55,1,'A',3,'normal'),
(56,1,'B',3,'normal'),
(57,1,'C',3,'normal'),
(58,1,'D',3,'normal'),
(59,1,'E',3,'normal'),
(60,1,'F',3,'normal'),
(61,1,'G',3,'normal'),
(62,1,'H',3,'normal'),
(63,1,'I',3,'normal'),
(64,1,'A',2,'normal'),
(65,1,'B',2,'normal'),
(66,1,'C',2,'normal'),
(67,1,'D',2,'normal'),
(68,1,'E',2,'normal'),
(69,1,'F',2,'normal'),
(70,1,'G',2,'normal'),
(71,1,'H',2,'normal'),
(72,1,'I',2,'normal'),
(73,1,'A',1,'normal'),
(74,1,'B',1,'normal'),
(75,1,'C',1,'normal'),
(76,1,'D',1,'normal'),
(77,1,'E',1,'normal'),
(78,1,'F',1,'normal'),
(79,1,'G',1,'normal'),
(80,1,'H',1,'normal'),
(81,1,'I',1,'normal'),
(82,2,'A',9,'normal'),
(83,2,'B',9,'normal'),
(84,2,'C',9,'normal'),
(85,2,'D',9,'normal'),
(86,2,'E',9,'normal'),
(87,2,'F',9,'normal'),
(88,2,'G',9,'normal'),
(89,2,'H',9,'normal'),
(90,2,'I',9,'normal'),
(91,2,'A',8,'normal'),
(92,2,'B',8,'normal'),
(93,2,'C',8,'normal'),
(94,2,'D',8,'normal'),
(95,2,'E',8,'normal'),
(96,2,'F',8,'normal'),
(97,2,'G',8,'normal'),
(98,2,'H',8,'normal'),
(99,2,'I',8,'normal'),
(100,2,'A',7,'normal'),
(101,2,'B',7,'normal'),
(102,2,'C',7,'normal'),
(103,2,'D',7,'normal'),
(104,2,'E',7,'normal'),
(105,2,'F',7,'normal'),
(106,2,'G',7,'normal'),
(107,2,'H',7,'normal'),
(108,2,'I',7,'normal'),
(109,2,'A',6,'normal'),
(110,2,'B',6,'normal'),
(111,2,'C',6,'normal'),
(112,2,'D',6,'normal'),
(113,2,'E',6,'normal'),
(114,2,'F',6,'normal'),
(115,2,'G',6,'normal'),
(116,2,'H',6,'normal'),
(117,2,'I',6,'normal'),
(118,2,'A',5,'normal'),
(119,2,'B',5,'normal'),
(120,2,'C',5,'normal'),
(121,2,'D',5,'normal'),
(122,2,'E',5,'normal'),
(123,2,'F',5,'normal'),
(124,2,'G',5,'normal'),
(125,2,'H',5,'normal'),
(126,2,'I',5,'normal'),
(127,2,'A',4,'normal'),
(128,2,'B',4,'normal'),
(129,2,'C',4,'normal'),
(130,2,'D',4,'normal'),
(131,2,'E',4,'normal'),
(132,2,'F',4,'normal'),
(133,2,'G',4,'normal'),
(134,2,'H',4,'normal'),
(135,2,'I',4,'normal'),
(136,2,'A',3,'normal'),
(137,2,'B',3,'normal'),
(138,2,'C',3,'normal'),
(139,2,'D',3,'normal'),
(140,2,'E',3,'normal'),
(141,2,'F',3,'normal'),
(142,2,'G',3,'normal'),
(143,2,'H',3,'normal'),
(144,2,'I',3,'normal'),
(145,2,'A',2,'normal'),
(146,2,'B',2,'normal'),
(147,2,'C',2,'normal'),
(148,2,'D',2,'normal'),
(149,2,'E',2,'normal'),
(150,2,'F',2,'normal'),
(151,2,'G',2,'normal'),
(152,2,'H',2,'normal'),
(153,2,'I',2,'normal'),
(154,2,'A',1,'normal'),
(155,2,'B',1,'normal'),
(156,2,'C',1,'normal'),
(157,2,'D',1,'normal'),
(158,2,'E',1,'normal'),
(159,2,'F',1,'normal'),
(160,2,'G',1,'normal'),
(161,2,'H',1,'normal'),
(162,2,'I',1,'normal'),
(163,3,'A',9,'normal'),
(164,3,'B',9,'normal'),
(165,3,'C',9,'normal'),
(166,3,'D',9,'normal'),
(167,3,'E',9,'normal'),
(168,3,'F',9,'normal'),
(169,3,'G',9,'normal'),
(170,3,'H',9,'normal'),
(171,3,'I',9,'normal'),
(172,3,'A',8,'normal'),
(173,3,'B',8,'normal'),
(174,3,'C',8,'normal'),
(175,3,'D',8,'normal'),
(176,3,'E',8,'normal'),
(177,3,'F',8,'normal'),
(178,3,'G',8,'normal'),
(179,3,'H',8,'normal'),
(180,3,'I',8,'normal'),
(181,3,'A',7,'normal'),
(182,3,'B',7,'normal'),
(183,3,'C',7,'normal'),
(184,3,'D',7,'normal'),
(185,3,'E',7,'normal'),
(186,3,'F',7,'normal'),
(187,3,'G',7,'normal'),
(188,3,'H',7,'normal'),
(189,3,'I',7,'normal'),
(190,3,'A',6,'normal'),
(191,3,'B',6,'normal'),
(192,3,'C',6,'normal'),
(193,3,'D',6,'normal'),
(194,3,'E',6,'normal'),
(195,3,'F',6,'normal'),
(196,3,'G',6,'normal'),
(197,3,'H',6,'normal'),
(198,3,'I',6,'normal'),
(199,3,'A',5,'normal'),
(200,3,'B',5,'normal'),
(201,3,'C',5,'normal'),
(202,3,'D',5,'normal'),
(203,3,'E',5,'normal'),
(204,3,'F',5,'normal'),
(205,3,'G',5,'normal'),
(206,3,'H',5,'normal'),
(207,3,'I',5,'normal'),
(208,3,'A',4,'normal'),
(209,3,'B',4,'normal'),
(210,3,'C',4,'normal'),
(211,3,'D',4,'normal'),
(212,3,'E',4,'normal'),
(213,3,'F',4,'normal'),
(214,3,'G',4,'normal'),
(215,3,'H',4,'normal'),
(216,3,'I',4,'normal'),
(217,3,'A',3,'normal'),
(218,3,'B',3,'normal'),
(219,3,'C',3,'normal'),
(220,3,'D',3,'normal'),
(221,3,'E',3,'normal'),
(222,3,'F',3,'normal'),
(223,3,'G',3,'normal'),
(224,3,'H',3,'normal'),
(225,3,'I',3,'normal'),
(226,3,'A',2,'normal'),
(227,3,'B',2,'normal'),
(228,3,'C',2,'normal'),
(229,3,'D',2,'normal'),
(230,3,'E',2,'normal'),
(231,3,'F',2,'normal'),
(232,3,'G',2,'normal'),
(233,3,'H',2,'normal'),
(234,3,'I',2,'normal'),
(235,3,'A',1,'normal'),
(236,3,'B',1,'normal'),
(237,3,'C',1,'normal'),
(238,3,'D',1,'normal'),
(239,3,'E',1,'normal'),
(240,3,'F',1,'normal'),
(241,3,'G',1,'normal'),
(242,3,'H',1,'normal'),
(243,3,'I',1,'normal'),
(244,4,'A',9,'normal'),
(245,4,'B',9,'normal'),
(246,4,'C',9,'normal'),
(247,4,'D',9,'normal'),
(248,4,'E',9,'normal'),
(249,4,'F',9,'normal'),
(250,4,'G',9,'normal'),
(251,4,'H',9,'normal'),
(252,4,'I',9,'normal'),
(253,4,'A',8,'normal'),
(254,4,'B',8,'normal'),
(255,4,'C',8,'normal'),
(256,4,'D',8,'normal'),
(257,4,'E',8,'normal'),
(258,4,'F',8,'normal'),
(259,4,'G',8,'normal'),
(260,4,'H',8,'normal'),
(261,4,'I',8,'normal'),
(262,4,'A',7,'normal'),
(263,4,'B',7,'normal'),
(264,4,'C',7,'normal'),
(265,4,'D',7,'normal'),
(266,4,'E',7,'normal'),
(267,4,'F',7,'normal'),
(268,4,'G',7,'normal'),
(269,4,'H',7,'normal'),
(270,4,'I',7,'normal'),
(271,4,'A',6,'normal'),
(272,4,'B',6,'normal'),
(273,4,'C',6,'normal'),
(274,4,'D',6,'normal'),
(275,4,'E',6,'normal'),
(276,4,'F',6,'normal'),
(277,4,'G',6,'normal'),
(278,4,'H',6,'normal'),
(279,4,'I',6,'normal'),
(280,4,'A',5,'normal'),
(281,4,'B',5,'normal'),
(282,4,'C',5,'normal'),
(283,4,'D',5,'normal'),
(284,4,'E',5,'normal'),
(285,4,'F',5,'normal'),
(286,4,'G',5,'normal'),
(287,4,'H',5,'normal'),
(288,4,'I',5,'normal'),
(289,4,'A',4,'normal'),
(290,4,'B',4,'normal'),
(291,4,'C',4,'normal'),
(292,4,'D',4,'normal'),
(293,4,'E',4,'normal'),
(294,4,'F',4,'normal'),
(295,4,'G',4,'normal'),
(296,4,'H',4,'normal'),
(297,4,'I',4,'normal'),
(298,4,'A',3,'normal'),
(299,4,'B',3,'normal'),
(300,4,'C',3,'normal'),
(301,4,'D',3,'normal'),
(302,4,'E',3,'normal'),
(303,4,'F',3,'normal'),
(304,4,'G',3,'normal'),
(305,4,'H',3,'normal'),
(306,4,'I',3,'normal'),
(307,4,'A',2,'normal'),
(308,4,'B',2,'normal'),
(309,4,'C',2,'normal'),
(310,4,'D',2,'normal'),
(311,4,'E',2,'normal'),
(312,4,'F',2,'normal'),
(313,4,'G',2,'normal'),
(314,4,'H',2,'normal'),
(315,4,'I',2,'normal'),
(316,4,'A',1,'normal'),
(317,4,'B',1,'normal'),
(318,4,'C',1,'normal'),
(319,4,'D',1,'normal'),
(320,4,'E',1,'normal'),
(321,4,'F',1,'normal'),
(322,4,'G',1,'normal'),
(323,4,'H',1,'normal'),
(324,4,'I',1,'normal'),
(325,5,'A',9,'normal'),
(326,5,'B',9,'normal'),
(327,5,'C',9,'normal'),
(328,5,'D',9,'normal'),
(329,5,'E',9,'normal'),
(330,5,'F',9,'normal'),
(331,5,'G',9,'normal'),
(332,5,'H',9,'normal'),
(333,5,'I',9,'normal'),
(334,5,'A',8,'normal'),
(335,5,'B',8,'normal'),
(336,5,'C',8,'normal'),
(337,5,'D',8,'normal'),
(338,5,'E',8,'normal'),
(339,5,'F',8,'normal'),
(340,5,'G',8,'normal'),
(341,5,'H',8,'normal'),
(342,5,'I',8,'normal'),
(343,5,'A',7,'normal'),
(344,5,'B',7,'normal'),
(345,5,'C',7,'normal'),
(346,5,'D',7,'normal'),
(347,5,'E',7,'normal'),
(348,5,'F',7,'normal'),
(349,5,'G',7,'normal'),
(350,5,'H',7,'normal'),
(351,5,'I',7,'normal'),
(352,5,'A',6,'normal'),
(353,5,'B',6,'normal'),
(354,5,'C',6,'normal'),
(355,5,'D',6,'normal'),
(356,5,'E',6,'normal'),
(357,5,'F',6,'normal'),
(358,5,'G',6,'normal'),
(359,5,'H',6,'normal'),
(360,5,'I',6,'normal'),
(361,5,'A',5,'normal'),
(362,5,'B',5,'normal'),
(363,5,'C',5,'normal'),
(364,5,'D',5,'normal'),
(365,5,'E',5,'normal'),
(366,5,'F',5,'normal'),
(367,5,'G',5,'normal'),
(368,5,'H',5,'normal'),
(369,5,'I',5,'normal'),
(370,5,'A',4,'normal'),
(371,5,'B',4,'normal'),
(372,5,'C',4,'normal'),
(373,5,'D',4,'normal'),
(374,5,'E',4,'normal'),
(375,5,'F',4,'normal'),
(376,5,'G',4,'normal'),
(377,5,'H',4,'normal'),
(378,5,'I',4,'normal'),
(379,5,'A',3,'normal'),
(380,5,'B',3,'normal'),
(381,5,'C',3,'normal'),
(382,5,'D',3,'normal'),
(383,5,'E',3,'normal'),
(384,5,'F',3,'normal'),
(385,5,'G',3,'normal'),
(386,5,'H',3,'normal'),
(387,5,'I',3,'normal'),
(388,5,'A',2,'normal'),
(389,5,'B',2,'normal'),
(390,5,'C',2,'normal'),
(391,5,'D',2,'normal'),
(392,5,'E',2,'normal'),
(393,5,'F',2,'normal'),
(394,5,'G',2,'normal'),
(395,5,'H',2,'normal'),
(396,5,'I',2,'normal'),
(397,5,'A',1,'normal'),
(398,5,'B',1,'normal'),
(399,5,'C',1,'normal'),
(400,5,'D',1,'normal'),
(401,5,'E',1,'normal'),
(402,5,'F',1,'normal'),
(403,5,'G',1,'normal'),
(404,5,'H',1,'normal'),
(405,5,'I',1,'normal'),
(406,6,'A',9,'normal'),
(407,6,'B',9,'normal'),
(408,6,'C',9,'normal'),
(409,6,'D',9,'normal'),
(410,6,'E',9,'normal'),
(411,6,'F',9,'normal'),
(412,6,'G',9,'normal'),
(413,6,'H',9,'normal'),
(414,6,'I',9,'normal'),
(415,6,'A',8,'normal'),
(416,6,'B',8,'normal'),
(417,6,'C',8,'normal'),
(418,6,'D',8,'normal'),
(419,6,'E',8,'normal'),
(420,6,'F',8,'normal'),
(421,6,'G',8,'normal'),
(422,6,'H',8,'normal'),
(423,6,'I',8,'normal'),
(424,6,'A',7,'normal'),
(425,6,'B',7,'normal'),
(426,6,'C',7,'normal'),
(427,6,'D',7,'normal'),
(428,6,'E',7,'normal'),
(429,6,'F',7,'normal'),
(430,6,'G',7,'normal'),
(431,6,'H',7,'normal'),
(432,6,'I',7,'normal'),
(433,6,'A',6,'normal'),
(434,6,'B',6,'normal'),
(435,6,'C',6,'normal'),
(436,6,'D',6,'normal'),
(437,6,'E',6,'normal'),
(438,6,'F',6,'normal'),
(439,6,'G',6,'normal'),
(440,6,'H',6,'normal'),
(441,6,'I',6,'normal'),
(442,6,'A',5,'normal'),
(443,6,'B',5,'normal'),
(444,6,'C',5,'normal'),
(445,6,'D',5,'normal'),
(446,6,'E',5,'normal'),
(447,6,'F',5,'normal'),
(448,6,'G',5,'normal'),
(449,6,'H',5,'normal'),
(450,6,'I',5,'normal'),
(451,6,'A',4,'normal'),
(452,6,'B',4,'normal'),
(453,6,'C',4,'normal'),
(454,6,'D',4,'normal'),
(455,6,'E',4,'normal'),
(456,6,'F',4,'normal'),
(457,6,'G',4,'normal'),
(458,6,'H',4,'normal'),
(459,6,'I',4,'normal'),
(460,6,'A',3,'normal'),
(461,6,'B',3,'normal'),
(462,6,'C',3,'normal'),
(463,6,'D',3,'normal'),
(464,6,'E',3,'normal'),
(465,6,'F',3,'normal'),
(466,6,'G',3,'normal'),
(467,6,'H',3,'normal'),
(468,6,'I',3,'normal'),
(469,6,'A',2,'normal'),
(470,6,'B',2,'normal'),
(471,6,'C',2,'normal'),
(472,6,'D',2,'normal'),
(473,6,'E',2,'normal'),
(474,6,'F',2,'normal'),
(475,6,'G',2,'normal'),
(476,6,'H',2,'normal'),
(477,6,'I',2,'normal'),
(478,6,'A',1,'normal'),
(479,6,'B',1,'normal'),
(480,6,'C',1,'normal'),
(481,6,'D',1,'normal'),
(482,6,'E',1,'normal'),
(483,6,'F',1,'normal'),
(484,6,'G',1,'normal'),
(485,6,'H',1,'normal'),
(486,6,'I',1,'normal'),
(487,7,'A',9,'normal'),
(488,7,'B',9,'normal'),
(489,7,'C',9,'normal'),
(490,7,'D',9,'normal'),
(491,7,'E',9,'normal'),
(492,7,'F',9,'normal'),
(493,7,'G',9,'normal'),
(494,7,'H',9,'normal'),
(495,7,'I',9,'normal'),
(496,7,'A',8,'normal'),
(497,7,'B',8,'normal'),
(498,7,'C',8,'normal'),
(499,7,'D',8,'normal'),
(500,7,'E',8,'normal'),
(501,7,'F',8,'normal'),
(502,7,'G',8,'normal'),
(503,7,'H',8,'normal'),
(504,7,'I',8,'normal'),
(505,7,'A',7,'normal'),
(506,7,'B',7,'normal'),
(507,7,'C',7,'normal'),
(508,7,'D',7,'normal'),
(509,7,'E',7,'normal'),
(510,7,'F',7,'normal'),
(511,7,'G',7,'normal'),
(512,7,'H',7,'normal'),
(513,7,'I',7,'normal'),
(514,7,'A',6,'normal'),
(515,7,'B',6,'normal'),
(516,7,'C',6,'normal'),
(517,7,'D',6,'normal'),
(518,7,'E',6,'normal'),
(519,7,'F',6,'normal'),
(520,7,'G',6,'normal'),
(521,7,'H',6,'normal'),
(522,7,'I',6,'normal'),
(523,7,'A',5,'normal'),
(524,7,'B',5,'normal'),
(525,7,'C',5,'normal'),
(526,7,'D',5,'normal'),
(527,7,'E',5,'normal'),
(528,7,'F',5,'normal'),
(529,7,'G',5,'normal'),
(530,7,'H',5,'normal'),
(531,7,'I',5,'normal'),
(532,7,'A',4,'normal'),
(533,7,'B',4,'normal'),
(534,7,'C',4,'normal'),
(535,7,'D',4,'normal'),
(536,7,'E',4,'normal'),
(537,7,'F',4,'normal'),
(538,7,'G',4,'normal'),
(539,7,'H',4,'normal'),
(540,7,'I',4,'normal'),
(541,7,'A',3,'normal'),
(542,7,'B',3,'normal'),
(543,7,'C',3,'normal'),
(544,7,'D',3,'normal'),
(545,7,'E',3,'normal'),
(546,7,'F',3,'normal'),
(547,7,'G',3,'normal'),
(548,7,'H',3,'normal'),
(549,7,'I',3,'normal'),
(550,7,'A',2,'normal'),
(551,7,'B',2,'normal'),
(552,7,'C',2,'normal'),
(553,7,'D',2,'normal'),
(554,7,'E',2,'normal'),
(555,7,'F',2,'normal'),
(556,7,'G',2,'normal'),
(557,7,'H',2,'normal'),
(558,7,'I',2,'normal'),
(559,7,'A',1,'normal'),
(560,7,'B',1,'normal'),
(561,7,'C',1,'normal'),
(562,7,'D',1,'normal'),
(563,7,'E',1,'normal'),
(564,7,'F',1,'normal'),
(565,7,'G',1,'normal'),
(566,7,'H',1,'normal'),
(567,7,'I',1,'normal'),
(568,8,'A',9,'normal'),
(569,8,'B',9,'normal'),
(570,8,'C',9,'normal'),
(571,8,'D',9,'normal'),
(572,8,'E',9,'normal'),
(573,8,'F',9,'normal'),
(574,8,'G',9,'normal'),
(575,8,'H',9,'normal'),
(576,8,'I',9,'normal'),
(577,8,'A',8,'normal'),
(578,8,'B',8,'normal'),
(579,8,'C',8,'normal'),
(580,8,'D',8,'normal'),
(581,8,'E',8,'normal'),
(582,8,'F',8,'normal'),
(583,8,'G',8,'normal'),
(584,8,'H',8,'normal'),
(585,8,'I',8,'normal'),
(586,8,'A',7,'normal'),
(587,8,'B',7,'normal'),
(588,8,'C',7,'normal'),
(589,8,'D',7,'normal'),
(590,8,'E',7,'normal'),
(591,8,'F',7,'normal'),
(592,8,'G',7,'normal'),
(593,8,'H',7,'normal'),
(594,8,'I',7,'normal'),
(595,8,'A',6,'normal'),
(596,8,'B',6,'normal'),
(597,8,'C',6,'normal'),
(598,8,'D',6,'normal'),
(599,8,'E',6,'normal'),
(600,8,'F',6,'normal'),
(601,8,'G',6,'normal'),
(602,8,'H',6,'normal'),
(603,8,'I',6,'normal'),
(604,8,'A',5,'normal'),
(605,8,'B',5,'normal'),
(606,8,'C',5,'normal'),
(607,8,'D',5,'normal'),
(608,8,'E',5,'normal'),
(609,8,'F',5,'normal'),
(610,8,'G',5,'normal'),
(611,8,'H',5,'normal'),
(612,8,'I',5,'normal'),
(613,8,'A',4,'normal'),
(614,8,'B',4,'normal'),
(615,8,'C',4,'normal'),
(616,8,'D',4,'normal'),
(617,8,'E',4,'normal'),
(618,8,'F',4,'normal'),
(619,8,'G',4,'normal'),
(620,8,'H',4,'normal'),
(621,8,'I',4,'normal'),
(622,8,'A',3,'normal'),
(623,8,'B',3,'normal'),
(624,8,'C',3,'normal'),
(625,8,'D',3,'normal'),
(626,8,'E',3,'normal'),
(627,8,'F',3,'normal'),
(628,8,'G',3,'normal'),
(629,8,'H',3,'normal'),
(630,8,'I',3,'normal'),
(631,8,'A',2,'normal'),
(632,8,'B',2,'normal'),
(633,8,'C',2,'normal'),
(634,8,'D',2,'normal'),
(635,8,'E',2,'normal'),
(636,8,'F',2,'normal'),
(637,8,'G',2,'normal'),
(638,8,'H',2,'normal'),
(639,8,'I',2,'normal'),
(640,8,'A',1,'normal'),
(641,8,'B',1,'normal'),
(642,8,'C',1,'normal'),
(643,8,'D',1,'normal'),
(644,8,'E',1,'normal'),
(645,8,'F',1,'normal'),
(646,8,'G',1,'normal'),
(647,8,'H',1,'normal'),
(648,8,'I',1,'normal');
/*!40000 ALTER TABLE `asientos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `boletos`
--

DROP TABLE IF EXISTS `boletos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `boletos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compra_id` int NOT NULL,
  `funcion_id` int NOT NULL,
  `asiento_id` int NOT NULL,
  `precio_pagado` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `funcion_id` (`funcion_id`,`asiento_id`) COMMENT 'Evita que un asiento se venda dos veces para la misma función',
  KEY `compra_id` (`compra_id`),
  KEY `asiento_id` (`asiento_id`),
  CONSTRAINT `boletos_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boletos_ibfk_2` FOREIGN KEY (`funcion_id`) REFERENCES `funciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boletos_ibfk_3` FOREIGN KEY (`asiento_id`) REFERENCES `asientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boletos`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `boletos` WRITE;
/*!40000 ALTER TABLE `boletos` DISABLE KEYS */;
INSERT INTO `boletos` VALUES
(1,7,1,550,120.00),
(2,7,1,541,120.00),
(3,7,1,532,120.00),
(4,7,1,523,120.00),
(5,7,1,514,120.00),
(6,8,1,499,120.00),
(7,8,1,490,120.00),
(8,9,1,495,120.00),
(9,9,1,504,120.00),
(10,10,15,103,80.00),
(11,10,15,94,80.00),
(12,10,15,85,80.00),
(13,10,15,158,80.00),
(14,11,19,167,80.00),
(15,11,19,240,80.00),
(16,12,41,410,150.00),
(17,12,41,483,150.00),
(18,13,41,484,150.00),
(19,13,41,475,150.00),
(20,14,41,432,150.00),
(21,14,41,423,150.00),
(22,14,41,441,150.00),
(23,15,11,17,80.00),
(24,15,11,8,80.00),
(25,16,14,402,150.00),
(26,16,14,393,150.00),
(27,17,2,375,150.00),
(28,17,2,366,150.00),
(29,18,8,188,80.00),
(30,18,8,197,80.00),
(31,19,20,304,95.00),
(32,19,20,313,95.00),
(33,20,43,378,150.00),
(34,20,43,369,150.00),
(35,21,9,265,95.00),
(36,21,9,256,95.00),
(37,21,9,247,95.00),
(38,22,5,319,95.00),
(39,22,5,310,95.00),
(40,23,22,507,120.00),
(41,23,22,511,120.00),
(42,23,22,530,120.00),
(43,23,22,538,120.00),
(44,23,22,547,120.00),
(45,23,22,516,120.00),
(46,23,22,500,120.00),
(47,23,22,491,120.00),
(48,24,44,274,95.00),
(49,24,44,265,95.00),
(50,24,44,305,95.00),
(51,24,44,314,95.00),
(52,24,44,311,95.00),
(53,24,44,300,95.00),
(54,24,44,273,95.00),
(55,24,44,286,95.00),
(56,24,44,277,95.00),
(57,24,44,270,95.00),
(58,25,44,284,95.00),
(59,25,44,267,95.00),
(60,25,44,250,95.00),
(61,25,44,259,95.00),
(62,25,44,297,95.00),
(63,25,44,288,95.00),
(64,25,44,324,95.00),
(65,25,44,315,95.00),
(66,25,44,321,95.00),
(67,25,44,312,95.00),
(68,26,44,316,95.00),
(69,26,44,307,95.00),
(70,26,44,289,95.00),
(71,26,44,280,95.00),
(72,26,44,271,95.00),
(73,26,44,253,95.00),
(74,26,44,244,95.00),
(75,26,44,272,95.00),
(76,26,44,281,95.00),
(77,26,44,290,95.00),
(78,27,44,246,95.00),
(79,27,44,247,95.00),
(80,27,44,248,95.00),
(81,27,44,257,95.00),
(82,27,44,255,95.00),
(83,27,44,285,95.00),
(84,27,44,294,95.00),
(85,27,44,304,95.00),
(86,27,44,313,95.00),
(87,27,44,322,95.00),
(88,28,44,317,95.00),
(89,28,44,308,95.00),
(90,28,44,319,95.00),
(91,28,44,310,95.00),
(92,28,44,318,95.00),
(93,28,44,292,95.00),
(94,28,44,283,95.00),
(95,28,44,254,95.00),
(96,28,44,245,95.00),
(97,28,44,278,95.00),
(98,29,44,260,95.00),
(99,29,44,251,95.00),
(100,29,44,252,95.00),
(101,29,44,249,95.00),
(102,29,44,275,95.00);
/*!40000 ALTER TABLE `boletos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES
(7,1,600.00,'2026-05-21 19:27:36'),
(8,1,240.00,'2026-05-21 19:50:17'),
(9,1,240.00,'2026-05-22 14:06:42'),
(10,1,320.00,'2026-05-22 15:06:47'),
(11,1,160.00,'2026-05-22 15:17:26'),
(12,1,300.00,'2026-05-22 15:19:45'),
(13,1,300.00,'2026-05-22 15:25:12'),
(14,1,450.00,'2026-05-22 15:26:54'),
(15,1,160.00,'2026-05-22 15:29:18'),
(16,1,300.00,'2026-05-22 15:34:03'),
(17,1,300.00,'2026-05-22 15:40:39'),
(18,1,160.00,'2026-05-22 15:44:55'),
(19,1,190.00,'2026-05-22 15:49:35'),
(20,1,300.00,'2026-05-22 15:52:54'),
(21,1,285.00,'2026-05-22 15:55:44'),
(22,1,190.00,'2026-05-22 15:57:45'),
(23,1,960.00,'2026-05-22 16:00:37'),
(24,1,950.00,'2026-05-22 16:04:56'),
(25,1,950.00,'2026-05-22 16:05:20'),
(26,1,950.00,'2026-05-22 16:05:40'),
(27,1,950.00,'2026-05-22 16:05:59'),
(28,1,950.00,'2026-05-22 16:06:13'),
(29,1,475.00,'2026-05-22 16:06:32');
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `funciones`
--

DROP TABLE IF EXISTS `funciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `funciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pelicula_id` int NOT NULL,
  `sala_id` int NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pelicula_id` (`pelicula_id`),
  KEY `sala_id` (`sala_id`),
  CONSTRAINT `funciones_ibfk_1` FOREIGN KEY (`pelicula_id`) REFERENCES `peliculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `funciones_ibfk_2` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funciones`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `funciones` WRITE;
/*!40000 ALTER TABLE `funciones` DISABLE KEYS */;
INSERT INTO `funciones` VALUES
(1,1,7,'2026-05-26 16:00:00',120.00),
(2,2,5,'2026-05-26 18:00:00',150.00),
(3,5,2,'2026-05-26 18:00:00',80.00),
(4,3,1,'2026-05-26 20:00:00',80.00),
(5,4,4,'2026-05-26 22:00:00',95.00),
(6,6,8,'2026-05-27 16:00:00',120.00),
(7,9,2,'2026-05-27 16:00:00',80.00),
(8,8,3,'2026-05-27 18:00:00',80.00),
(9,10,4,'2026-05-27 18:00:00',95.00),
(10,7,6,'2026-05-27 20:00:00',150.00),
(11,13,1,'2026-05-28 18:00:00',80.00),
(12,15,8,'2026-05-28 18:00:00',120.00),
(13,11,7,'2026-05-28 20:00:00',120.00),
(14,12,5,'2026-05-28 22:00:00',150.00),
(15,14,2,'2026-05-28 22:00:00',80.00),
(16,3,6,'2026-05-29 16:00:00',150.00),
(17,7,1,'2026-05-29 16:00:00',80.00),
(18,11,2,'2026-05-29 18:00:00',80.00),
(19,1,3,'2026-05-29 20:00:00',80.00),
(20,9,4,'2026-05-29 20:00:00',95.00),
(21,5,8,'2026-05-29 22:00:00',120.00),
(22,2,7,'2026-05-30 16:00:00',120.00),
(23,12,4,'2026-05-30 16:00:00',95.00),
(24,6,3,'2026-05-30 18:00:00',80.00),
(25,4,5,'2026-05-30 20:00:00',150.00),
(26,8,8,'2026-05-30 22:00:00',120.00),
(27,10,1,'2026-05-30 22:00:00',80.00),
(28,5,1,'2026-05-31 16:00:00',80.00),
(29,15,2,'2026-05-31 16:00:00',80.00),
(30,14,4,'2026-05-31 18:00:00',95.00),
(31,13,6,'2026-05-31 20:00:00',150.00),
(32,2,3,'2026-05-31 22:00:00',80.00),
(33,7,7,'2026-05-31 22:00:00',120.00),
(34,8,5,'2026-06-01 16:00:00',150.00),
(35,4,1,'2026-06-01 18:00:00',80.00),
(36,9,7,'2026-06-01 18:00:00',120.00),
(37,3,8,'2026-06-01 20:00:00',120.00),
(38,12,2,'2026-06-01 20:00:00',80.00),
(39,10,6,'2026-06-01 22:00:00',150.00),
(40,13,7,'2026-06-02 16:00:00',120.00),
(41,1,6,'2026-06-02 18:00:00',150.00),
(42,14,3,'2026-06-02 20:00:00',80.00),
(43,6,5,'2026-06-02 22:00:00',150.00),
(44,11,4,'2026-06-02 22:00:00',95.00),
(45,15,8,'2026-06-02 22:00:00',120.00);
/*!40000 ALTER TABLE `funciones` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `peliculas`
--

DROP TABLE IF EXISTS `peliculas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `peliculas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sinopsis` text COLLATE utf8mb4_unicode_ci,
  `duracion` int DEFAULT NULL COMMENT 'Duración en minutos',
  `clasificacion` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genero` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Imagen vertical para la cuadrícula',
  `banner_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Imagen horizontal para el carrusel',
  `estado` enum('cartelera','proximamente') COLLATE utf8mb4_unicode_ci DEFAULT 'cartelera',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peliculas`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `peliculas` WRITE;
/*!40000 ALTER TABLE `peliculas` DISABLE KEYS */;
INSERT INTO `peliculas` VALUES
(1,'Interestelar','Un grupo de exploradores hace uso de un agujero de gusano para superar las limitaciones de los viajes espaciales humanos.',169,'B','Ciencia Ficción','img/posters/interestelar.jpg','img/banners/interestelar_banner.jpg','cartelera'),
(2,'The Avengers','Los héroes más poderosos de la Tierra deben unirse para detener a Loki.',143,'B','Acción','img/posters/avengers.jpg','img/banners/avengers_banner.jpg','cartelera'),
(3,'Super Mario Bros. La Película','Mario y Luigi viajan por un laberinto subterráneo para rescatar a la Princesa Peach.',92,'A','Animación','img/posters/mario.jpg','img/banners/mario_banner.jpg','cartelera'),
(4,'Oppenheimer','La historia del científico estadounidense J. Robert Oppenheimer y su papel en el desarrollo de la bomba atómica.',180,'B15','Drama','img/posters/oppenheimer.jpg','img/banners/oppenheimer_banner.jpg','cartelera'),
(5,'Barbie','Barbie sufre una crisis que la lleva a cuestionar su mundo y su existencia.',114,'A','Comedia','img/posters/barbie.jpg','img/banners/barbie_banner.jpg','cartelera'),
(6,'Spider-Man: No Way Home','La identidad de Spider-Man es revelada, trayendo consecuencias multiversales.',148,'B','Acción','img/posters/spiderman.jpg','img/banners/spiderman_banner.jpg','cartelera'),
(7,'El Caballero de la Noche','Batman se enfrenta a su mayor reto físico y psicológico: El Guasón.',152,'B15','Acción','img/posters/batman.jpg','img/banners/batman_banner.jpg','cartelera'),
(8,'Toy Story','Un muñeco vaquero se siente amenazado cuando un nuevo juguete espacial llega al cuarto de Andy.',81,'A','Animación','img/posters/toystory.jpg','img/banners/toystory_banner.jpg','cartelera'),
(9,'Jurassic Park','Un parque temático de dinosaurios clonados se sale de control.',127,'B','Ciencia Ficción','img/posters/jurassic.jpg','img/banners/jurassic_banner.jpg','cartelera'),
(10,'Avatar','Un marine parapléjico es enviado a la luna Pandora en una misión única.',162,'B','Ciencia Ficción','img/posters/avatar.jpg','img/banners/avatar_banner.jpg','cartelera'),
(11,'El Rey León','El joven león Simba debe enfrentar su destino para convertirse en rey.',88,'A','Animación','img/posters/reyleon.jpg','img/banners/reyleon_banner.jpg','cartelera'),
(12,'Matrix','Un hacker descubre la verdadera naturaleza de su realidad.',136,'B15','Ciencia Ficción','img/posters/matrix.jpg','img/banners/matrix_banner.jpg','cartelera'),
(13,'Deadpool & Wolverine','Deadpool y Wolverine se unen en una aventura a través del multiverso.',127,'C','Acción','img/posters/deadpool.jpg','img/banners/deadpool_banner.jpg','cartelera'),
(14,'Shrek','Un ogro gruñón emprende un viaje para rescatar a una princesa.',90,'A','Animación','img/posters/shrek.jpg','img/banners/shrek_banner.jpg','cartelera'),
(15,'Volver al Futuro','Un joven es enviado accidentalmente 30 años en el pasado.',116,'A','Ciencia Ficción','img/posters/bttf.jpg','img/banners/bttf_banner.jpg','cartelera');
/*!40000 ALTER TABLE `peliculas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `promociones`
--

DROP TABLE IF EXISTS `promociones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_descuento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
INSERT INTO `promociones` VALUES
(1,'Martes 2x1','Disfruta de todas las películas al 2x1 todos los martes.','img/promos/martes2x1.jpg','MARTES2X1','2024-01-01','2024-12-31'),
(2,'Combo Nachos','Compra un boleto IMAX y llévate unos nachos a mitad de precio.','img/promos/nachos.jpg',NULL,'2024-06-01','2024-08-31');
/*!40000 ALTER TABLE `promociones` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `salas`
--

DROP TABLE IF EXISTS `salas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `salas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int NOT NULL,
  `tipo` enum('Tradicional','VIP','3D','IMAX') COLLATE utf8mb4_unicode_ci DEFAULT 'Tradicional',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salas`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `salas` WRITE;
/*!40000 ALTER TABLE `salas` DISABLE KEYS */;
INSERT INTO `salas` VALUES
(1,'Sala 1',100,'Tradicional'),
(2,'Sala 2',100,'Tradicional'),
(3,'Sala 3',100,'Tradicional'),
(4,'Sala 4',100,'3D'),
(5,'Sala 5 VIP',40,'VIP'),
(6,'Sala 6 VIP',40,'VIP'),
(7,'Sala IMAX',150,'IMAX'),
(8,'Sala IMAX',150,'IMAX');
/*!40000 ALTER TABLE `salas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('cliente','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'cliente',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES
(1,'paco','jaguilar57@ucol.mx','$2y$10$QRAnSb0YbwYIHypew3ClTeq1HGgZ3LjKQPk0wFWv.8lmlrqw3p9OO','cliente','2026-05-21 19:27:25');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-05-22 16:06:32
