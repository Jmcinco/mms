-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 03, 2026 at 05:17 AM
-- Server version: 9.7.1
-- PHP Version: 8.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mms_db`
--
CREATE DATABASE IF NOT EXISTS `mms_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mms_db`;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-08-24-120000', 'App\\Database\\Migrations\\AddArticleLockColumns', 'default', 'App', 1787568863, 1),
(2, '2026-08-24-120000', 'App\\Database\\Migrations\\AddLockingToArticles', 'default', 'App', 1787570579, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tblarticle`
--

CREATE TABLE `tblarticle` (
  `id` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `news_date` date NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `entry_start` timestamp NULL DEFAULT NULL,
  `entry_end` timestamp NOT NULL,
  `editing_start` timestamp NOT NULL,
  `editing_end` timestamp NOT NULL,
  `summary` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sub_category` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `gov_offices` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `slant` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `medium` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `station` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `program` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reporter` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alert` enum('Yes','No') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'No',
  `status` enum('draft','submitted','editing','completed','archived') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `locked_by` int UNSIGNED DEFAULT NULL,
  `locked_at` datetime DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `archived_at` datetime NOT NULL,
  `archived_by` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblarticle`
--

INSERT INTO `tblarticle` (`id`, `news_date`, `content`, `entry_start`, `entry_end`, `editing_start`, `editing_end`, `summary`, `category`, `sub_category`, `gov_offices`, `remarks`, `slant`, `type`, `medium`, `station`, `program`, `reporter`, `alert`, `status`, `locked_by`, `locked_at`, `created_by`, `created_at`, `updated_at`, `archived_at`, `archived_by`) VALUES
('PMU-1787815328941', '2026-08-27', '<p>dasdasdasd</p>', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'dasdasdasd', 'Agrarian Reform', '[\"Abu Sayyaf Group (ASG)\",\"Academic Freedom, Policies\"]', '[\"Bangko Sentral ng Pilipinas\",\"BPI\"]', 'dsadsad', '+', 'Commentary', 'Online', 'Abante Tonite', '24 Oras', '[\"Atom Araullo\",\"Sample Reporter 1\"]', 'No', 'submitted', NULL, NULL, 2, '2026-08-27 07:22:08', '2026-08-27 07:22:08', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblarticle_archive`
--

CREATE TABLE `tblarticle_archive` (
  `id` bigint UNSIGNED NOT NULL,
  `article_id` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `archived_by` int UNSIGNED NOT NULL,
  `archived_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archive_remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblarticle_edit_history`
--

CREATE TABLE `tblarticle_edit_history` (
  `id` bigint UNSIGNED NOT NULL,
  `article_id` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `editor_id` int UNSIGNED NOT NULL,
  `editing_start` datetime NOT NULL,
  `editing_end` datetime DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `cat_id` int NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`cat_id`, `category_name`) VALUES
(4, 'Agrarian Reform'),
(5, 'Agriculture And Food'),
(6, 'Arts, Culture, History, and Entertainment'),
(7, 'Business and Economy'),
(8, 'Cabinet / Appointee'),
(9, 'Central Visayas Situation'),
(10, 'Church / Religion'),
(11, 'Communications'),
(12, 'Education'),
(13, 'Elections'),
(14, 'Energy'),
(15, 'Environment'),
(16, 'Foreign Affairs'),
(17, 'GOCC'),
(18, 'Good Governance'),
(19, 'Government Fund'),
(20, 'Health'),
(21, 'Holidays (National and Local)'),
(22, 'Housing'),
(23, 'Infrastructure and Public Works'),
(24, 'Judiciary'),
(25, 'Justice'),
(26, 'Labor And Employment'),
(27, 'Legislature'),
(28, 'Local Government / LGUs'),
(29, 'Media'),
(30, 'Mindanao'),
(31, 'Natural Hazards / Disasters'),
(32, 'Obituary'),
(33, 'Peace and Order'),
(34, 'People And Events'),
(35, 'Pinoy\'s Abroad'),
(36, 'Politics'),
(37, 'Population, Census'),
(38, 'Presidency'),
(39, 'Science and Technology'),
(40, 'Social Welfare and Development'),
(41, 'Sports and Games'),
(42, 'Tourism'),
(43, 'Transportation'),
(44, 'Vice Presidency'),
(45, 'Water'),
(46, 'Weather'),
(47, 'World news'),
(48, 'Games and Amusement');

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartment`
--

CREATE TABLE `tbldepartment` (
  `department_id` int NOT NULL,
  `department_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbldepartment`
--

INSERT INTO `tbldepartment` (`department_id`, `department_name`) VALUES
(1, 'Free Department'),
(2, 'Bangko Sentral ng Pilipinas'),
(3, 'BPI'),
(4, 'BDO');

-- --------------------------------------------------------

--
-- Table structure for table `tblmedium`
--

CREATE TABLE `tblmedium` (
  `medium_id` int NOT NULL,
  `medium_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblmedium`
--

INSERT INTO `tblmedium` (`medium_id`, `medium_name`) VALUES
(1, 'Online'),
(2, 'Print'),
(3, 'Radio'),
(4, 'TV'),
(5, 'SMS');

-- --------------------------------------------------------

--
-- Table structure for table `tblprogram`
--

CREATE TABLE `tblprogram` (
  `program_id` int NOT NULL,
  `from_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblprogram`
--

INSERT INTO `tblprogram` (`program_id`, `from_name`, `program_name`) VALUES
(1, 'GMA', '24 Oras'),
(2, 'DZBB', '24 Oras sa DZBB');

-- --------------------------------------------------------

--
-- Table structure for table `tblreporter`
--

CREATE TABLE `tblreporter` (
  `reporter_id` int NOT NULL,
  `reporter_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblreporter`
--

INSERT INTO `tblreporter` (`reporter_id`, `reporter_name`) VALUES
(1, 'Atom Araullo'),
(2, 'Sample Reporter 1'),
(3, 'Sample Reporter 2');

-- --------------------------------------------------------

--
-- Table structure for table `tblslant`
--

CREATE TABLE `tblslant` (
  `slant_id` int NOT NULL,
  `slant_name` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblslant`
--

INSERT INTO `tblslant` (`slant_id`, `slant_name`) VALUES
(1, '+'),
(2, '-'),
(3, '0');

-- --------------------------------------------------------

--
-- Table structure for table `tblstation`
--

CREATE TABLE `tblstation` (
  `station_id` int NOT NULL,
  `station_from` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `station_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstation`
--

INSERT INTO `tblstation` (`station_id`, `station_from`, `station_name`) VALUES
(1, 'Print', 'Abante'),
(2, 'Print', 'Abante Tonite'),
(3, 'TV', 'ABS - CBN'),
(4, 'TV', 'ANC'),
(5, 'Online', 'Bilyonaryo.com'),
(6, 'Print', 'Bulgar'),
(7, 'Print', 'Business Mirror');

-- --------------------------------------------------------

--
-- Table structure for table `tblsub_category`
--

CREATE TABLE `tblsub_category` (
  `sub_id` int NOT NULL,
  `sub_category` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsub_category`
--

INSERT INTO `tblsub_category` (`sub_id`, `sub_category`) VALUES
(1, 'Free Slot'),
(2, 'Debt management, Treasury/Retail bonds'),
(3, 'Plane crash'),
(4, 'Seafarer\'s welfare'),
(5, 'Athlete Welfare, Incentives, and Awards'),
(6, 'International Criminal Court (ICC)'),
(7, 'Shari'),
(8, 'Reclamation'),
(9, 'Medical welfare/assistance (financial/healthcare subsidies, Medical missions)'),
(10, 'Healthcare system (medical worker\'s welfare, hospital facilities, medical equipment)'),
(11, 'Pinoy Emigrant/Imigrant (for reports involving Pinoys overseas but not specifically OFW)'),
(12, 'Flooding and damages'),
(13, 'Abu Sayyaf Group (ASG)'),
(14, 'Academic Freedom, Policies'),
(15, 'Accident (Drowning, Electrocution, etc.)'),
(16, 'Accidents, Attacks, Cases Abroad'),
(17, 'AFP Modernization*'),
(18, 'Alternative Transpo (Ferry, Cable Car)'),
(19, 'Animal Cruelty, Rights, Extinction'),
(20, 'Anti-Terrorism Law'),
(21, 'Anti-Wiretapping Law'),
(22, 'APEC Summit'),
(23, 'Appointment, Nomination, Promotion, Retirement,  Resignation, Dismissal'),
(24, 'Araw ng Kagitingan'),
(25, 'Armed Forces/Military'),
(26, 'Arroyos'),
(27, 'Artificial intelligence (AI)'),
(28, 'ASEAN'),
(29, 'Asian Games'),
(30, 'Astronomy/Space (rocket launch, debris, impact)'),
(31, 'Aviation'),
(32, 'Bagong Pilipinas'),
(33, 'Baklas Billboard'),
(34, 'Balik Probinsya program'),
(35, 'Bangsamoro (Law, Region)'),
(36, 'Bangsamoro Islamic Freedom Fighters (BIFF)'),
(37, 'Banking and Finance (Counter, Digital)'),
(38, 'Bar Examinations'),
(39, 'BARMM Elections'),
(40, 'Benham Rise'),
(41, 'Bills/Legislation/Resolution (Senate, House, LEDAC)'),
(42, 'Bird Flu Virus'),
(43, 'Bitcoins/Cryptocurrency'),
(44, 'Board Examinations'),
(45, 'Bombs scare/threat/explosion (vintage bombs)'),
(46, 'Bullet-planting/Tanim-Bala Scheme'),
(47, 'Bullying'),
(48, 'Burst Water Pipe/Water Tank'),
(49, 'Business,  Investment, Trade, Loan deals'),
(50, 'Cancelled, Diverted Flights'),
(51, 'Cancer (Brain, Breast, Liver, Testicular, Etc)'),
(52, 'Cash Transfer Programs, Subsidies/Assistance (4Ps, Tupad, AICS)'),
(53, 'Catholic/CBCP'),
(54, 'Census, Statistics'),
(55, 'Charter Change, Federalism'),
(56, 'Christmas Season (Activities, Celebrations, Exodus)'),
(57, 'Circumcision'),
(58, 'Civil Service'),
(59, 'Clash Refugees, Displaced Residents'),
(60, 'Class, Work Suspensions'),
(61, 'Cleanup Drive, Rehab (Estero, Drainage, Coastal, River)'),
(62, 'Climate change, Global warming'),
(63, 'Coco Levy Funds'),
(64, 'Commemoration rites'),
(65, 'Communist Party (CPP-NDF), NPA, Red-tagging'),
(66, 'Commuters safety/rights'),
(67, 'Corruption, Anomaly, Misconduct'),
(68, 'Coup, Destabilization, Ouster'),
(69, 'Crime Prevention'),
(70, 'Crimes (Killing, Extrajudicial, Ambush, Robbery, EJK, Etc)'),
(71, 'Crisis Abroad'),
(72, 'Curfew'),
(73, 'Currency, Stamps (Commemorative Coins, Demonetization)'),
(74, 'Curriculum (School subjects, lectures, module errors, school activities)'),
(75, 'Cybercrime, Cybersecurity'),
(76, 'Dam Updates'),
(77, 'Davao Death Squad (DDS)'),
(78, 'Death Penalty'),
(79, 'Demolition'),
(80, 'Dengue'),
(81, 'DILG concerns'),
(82, 'Disappearances, Hostage, Kidnapping/Abduction'),
(83, 'Disaster Preparedness and Response (Rescue, Relief, Aids)'),
(84, 'Diseases (Outbreak, Mysterious Illness, Pandemic)'),
(85, 'Disqualification Cases (DC)'),
(86, 'Divorce Bill, Annulment'),
(87, 'Earthquake (bulletin, Tsunami warnings)'),
(88, 'Economic Development'),
(89, 'EDSA People Power'),
(90, 'El Ni'),
(91, 'Election Ban (Guns, Liquor, Appointment)'),
(92, 'Election Protests (EP)'),
(93, 'Election-related Crimes (EC)'),
(94, 'Elections (Preparations, Candidates, Campaign, Spending, Canvassing/proclamation)'),
(95, 'Electric vehicles (EV)'),
(96, 'Electricity (Price, Supply, Reserve, Solar, Nuclear, Etc)'),
(97, 'Employment (Contractualization, Regularization, Tenure, Layoff)'),
(98, 'Energy Emergency'),
(99, 'Enhanced Defense Cooperation Agreement (EDCA)'),
(100, 'Entrance Exams, Assessment Tests, School rankings and administration'),
(101, 'Environment concerns/issues'),
(102, 'Executive Clemency/Pardon/Amnesty'),
(103, 'Extortion, Bribery'),
(104, 'Fare/Toll'),
(105, 'Feast Of Black Nazarene'),
(106, 'Fertilizer Scam'),
(107, 'FIBA*'),
(108, 'Field Trips'),
(109, 'Films/Movies and Television (Regulation, MMFF, etc)'),
(110, 'Fines, Policies, Penalties, Ban'),
(111, 'Fire (Incidents, Prevention, Safety)'),
(112, 'Firearms (Loose firearms, illegal possession)'),
(113, 'First Family'),
(114, 'Fisheries And Aquatic Resources'),
(115, 'Food Security, Farming, Poultry, Livestock'),
(116, 'Foreign Policy, Foreign Relations'),
(117, 'Former President Benigno Aquino'),
(118, 'Former President Fidel Ramos'),
(119, 'Former President Gloria Arroyo'),
(120, 'Former President Joseph \"Erap\" Estrada'),
(121, 'Former President Rodrigo Duterte'),
(122, 'Former Vice President Leni Robredo'),
(123, 'Fraternity (Frat War, Hazing)'),
(124, 'Freedom Of Information (FOI)'),
(125, 'Premium policies/contributions'),
(126, 'Graduation'),
(127, 'Grassroots Participatory Budgeting (GPB)*'),
(128, 'Guinness World Records, International Recognitions'),
(129, 'Hacienda Luisita'),
(130, 'Heatstroke, Heatwave'),
(131, 'Heritage Site, Historic Place, Historical Treasure'),
(132, 'Heroes Day, National Heroes'),
(133, 'HIV/AIDS, Distribution Of Condom'),
(134, 'Hog Cholera, African Swine Fever (ASF)'),
(135, 'Holy Week (Observance, Exodus)'),
(136, 'Healthcare system (medical worker\'s welfare, hospital facilities/modernization)'),
(137, 'House Of Representatives, Speakership, Chairmanships'),
(138, 'Housing Projects'),
(139, 'Housing Scam, Rental Issues'),
(140, 'Human Rights, Freedom'),
(141, 'Human Trafficking'),
(142, 'Hunger And Poverty'),
(143, 'Iglesia Ni Cristo (INC)'),
(144, 'Illegal Drugs, Oplan Tokhang'),
(145, 'Illegal gambling'),
(146, 'Illegal Logging'),
(147, 'Pinoy Emigrant/Immigrants (for reports not involving OFWs)'),
(148, 'Immigration And Deportation'),
(149, 'Impeachment/Resignation Calls'),
(150, 'Independence Day'),
(151, 'Inflation, Interest, Rates, Policy'),
(152, 'Informal Settlers'),
(153, 'Infrastructure'),
(154, 'Insurance, Pre-need Plans'),
(155, 'International Contests'),
(156, 'Internet, Social Media, Websites, Digitization'),
(157, 'Jail Break, Riot'),
(158, 'Jail Management, GCTA'),
(159, 'Job Opportunities, Training'),
(160, 'Joint military drills/exercises'),
(161, 'Judicial matters'),
(162, 'Juvenile Justice, Child Rights, Abuse, Molestation, Pornography'),
(163, 'K-12 Program'),
(164, 'Kasambahay Law'),
(165, 'Labor Day'),
(166, 'Labor issues (Worker\'s welfare, Working arrangement, Dispute/Settlement)'),
(167, 'Land Reform, Distribution, Reclamation, Land Use'),
(168, 'Landslide, Rockslide, Trashslide'),
(169, 'Learning Modality (Face-to-face, blended, online, alternative)'),
(170, 'Leptospirosis'),
(171, 'Licensure Exam'),
(172, 'Literature'),
(173, 'Local government units (LGUs)'),
(174, 'Lottery, Sweepstakes, Casinos'),
(175, 'LPA/Cyclone (Approaching, Updates, Aftermath)'),
(176, 'Maguindanao Massacre'),
(177, 'Maharlika Investment Fund'),
(178, 'Malampaya Fund'),
(179, 'Mamasapano Clash'),
(180, 'Manila Bay Rehabilitation, Policies'),
(181, 'Marawi (Siege, Rehabilitation, Etc)'),
(182, 'Marcoses Ill-gotten Wealth/Marcos Burial'),
(183, 'Maritime (Collision/Sinking, Security etc.)'),
(184, 'Martial Law'),
(185, 'Measles'),
(186, 'Media (Attacks, Killings, Vilification, Press Freedom, Franchise/operation)'),
(187, 'Medical Malpractice'),
(188, 'Medical Marijuana'),
(189, 'Medicines, Medical Mission, Healthcare'),
(190, 'Mendiola Massacre'),
(191, 'Meningococcemia'),
(192, 'Mental Health, Anxiety, Depression'),
(193, 'Middle East Respiratory Syndrome Coronavirus (MERS-CoV)'),
(194, 'MIL campaign, Fake News, Disinformation, Misinformation'),
(195, 'Mining, Quarrying'),
(196, 'Missing Persons'),
(197, 'Monkeypox/Mpox (cases, treatment)'),
(198, 'Monsoon Rains, Flooding And Damage'),
(199, 'Moro Islamic Liberation Front (MILF)'),
(200, 'Moro National Liberation Front (MNLF)'),
(201, 'Mosquito Bites (Chikungunya Virus, Malaria, Dengue, Filariasis, Zika)'),
(202, 'National budget, confidential funds'),
(203, 'National ID System'),
(204, 'National Police/PNP'),
(205, 'Natural Resources'),
(206, 'New Year Revelry/Iwas Paputok'),
(207, 'Ninoy And Cory Aquino'),
(208, 'North Korea Missiles/Korean War'),
(209, 'Not Applicable (N/A)'),
(210, 'Nursing (Exam, plight)'),
(211, 'Nutrition, Malnutrition, Deworming, Obesity'),
(212, 'Obituary'),
(213, 'Offshore, Online Gaming, POGOs'),
(214, 'OFW Remittances'),
(215, 'OFWs (Abused, Killed, Deployment, Repatriation)'),
(216, 'Oil (Oil spill, Supply, Prices, Exploration)'),
(217, 'Olympics/Paralympics (Athletes life, benefits, awards)'),
(218, 'Overseas Voters Registration, Overseas Absentee Voting'),
(219, 'Pageant (Miss Philippines, Miss World, Miss Universe, Etc)'),
(220, 'Palarong Pambansa'),
(221, 'Papal Visit/Santo Papa'),
(222, 'Party-list System'),
(223, 'Passports, Visa'),
(224, 'PBBM - President Ferdinand Bongbong Marcos Jr'),
(225, 'People Awards and Recognition, National Artist, National Scientist'),
(226, 'Persons With Disabilities (PWD)'),
(227, 'Pests (Black Bug, Others)'),
(228, 'Philippine Peso'),
(229, 'Philippine Tourism (World/Local ranking, Tourism spots, tourism stats)'),
(230, 'Pinoy Drug Couriers'),
(231, 'Pinoys On Death Row'),
(232, 'Piracy/Imitation'),
(233, 'PNP Academy'),
(234, 'PNP/AFP/PCG Modernization'),
(235, 'Poaching, Illegal Sea Activities'),
(236, 'Poisoning (Food, Chemicals)'),
(237, 'Political Dynasty (Anti and Pro)'),
(238, 'Political Party'),
(239, 'Pollution (Air quality, plastic, etc.)'),
(240, 'Pork Barrel Scam'),
(241, 'Postal Services'),
(242, 'Pregnancy/Maternity (Teenage, Unwanted, Family Planning, Abortion)'),
(243, 'Presidential Elections'),
(244, 'Presidential Sisters'),
(245, 'Presidential Visit (Local)'),
(246, 'Price (Freeze, Manipulation, Overpricing, Hoarding)'),
(247, 'Private Schools'),
(248, 'Protest/rally'),
(249, 'Public-Private Partnership (PPP)'),
(250, 'Rabies'),
(251, 'Racism (Asian hate, Black lives matter, etc)'),
(252, 'Rainy And Summer Seasons'),
(253, 'Ramadan, Islamic festivities/culture'),
(254, 'Ratings (Trust, Approval, Performance)'),
(255, 'Red Tide Toxins'),
(256, 'Reproductive Health (RH) Law'),
(257, 'Revolutionary Government (RevGov)'),
(258, 'Rice (Supply, Price, Smuggling)'),
(259, 'Road, Building Constructions, Rehabilitation, Repairs'),
(260, 'ROTC, Mandatory military service'),
(261, 'Sabah (Standoff, Claim)'),
(262, 'Same Sex, LGBT Rights, SOGIE Bill, Pride Month'),
(263, 'Saudization'),
(264, 'Scam (ATM, Investment, Text, Money Laundering)'),
(265, 'School Calendar,  Opening/Registration, Brigada Eskwela'),
(266, 'School infrastructures/facilities, Classrooms'),
(267, 'SEA Games'),
(268, 'Senate (Senate Presidency, Chairmanships, Internal rules/issues))'),
(269, 'Senior Citizen rights, discounts'),
(270, 'Sexual Assault (Rape, molestation, harassment)'),
(271, 'Sidewalk Vendors'),
(272, 'Sin Tax Law (Cigarette, Alcohol)'),
(273, 'Sink Holes'),
(274, 'Simulation drills'),
(275, 'Youth empowerment'),
(276, 'Smoking (Mortality, Statistics, Surveys, Impact, Disease, Vape)'),
(277, 'Smuggled items, counterfeit items'),
(278, 'Solid Waste Management, Waste Segregation'),
(279, 'Special Allotment Release Order (SARO)'),
(280, 'Special Days, Festivals'),
(281, 'Sports Tournaments (Basketball, Volleyball, Tennis, Weightlifting, Gymnastics etc.)'),
(282, 'SRP'),
(283, 'State Of Calamity'),
(284, 'State Of Emergency'),
(285, 'State Of Lawlessness'),
(286, 'State Of The Nation Address (SONA)'),
(287, 'State Visit (International)'),
(288, 'Statement Of Assets, Liabilities, And Net Worth (SALN)'),
(289, 'Stock Exchange, Trading'),
(290, 'Stranded Passengers, Vessels'),
(291, 'Super Typhoon Lawin (Threats, Updates, Aftermath)*'),
(292, 'Supertyphoon Yolanda (Effects, Rehabilitation, Recovery)'),
(293, 'Surveys and Researches'),
(294, 'Tax (Increase, Evasion, Liability, Exemption, TRAIN Law)'),
(295, 'Telcos, SIM registration'),
(296, 'Temperature (Heat Index, Drought, Frost)'),
(297, 'Terrorism/Islamic State'),
(298, 'Tornado, Gustwind'),
(299, 'Tourist Destinations, Tourism Slogan'),
(300, 'Trains and Railways (LRT, MRT, PNR, Subway)'),
(301, 'Transport Modernization (Jeepney, Bus, Ship, Taxi)'),
(302, 'Transport policies (Regulations, traffic, violations, coding)'),
(303, 'Transportation Network Vehicle System (TNVS, Grab, Uber, Angkas)'),
(304, 'Travel Advisories'),
(305, 'Tribes (Indigenous Peoples, Lumad, Manobo, etc)'),
(306, 'UN Peacekeepers'),
(307, 'Undas'),
(308, 'Vaccinations (COVID-19, Polio, Measles, Etc)'),
(309, 'Vehicular Accidents'),
(310, 'Veterans'),
(311, 'Vice President Sara Duterte'),
(312, 'Vice Presidential Elections (VPE)'),
(313, 'Visiting Forces Agreement (Balikatan, Mutual Defense Treatu, SOVFA, RAA)'),
(314, 'Volcano (Alert, Eruption, Recovery)'),
(315, 'Vote-buying'),
(316, 'Voters Registration (VR)'),
(317, 'Wage (Hike, Back Pay, Benefits, Etc)'),
(318, 'Water Concessionaires (Rate Adjustments, Policy)'),
(319, 'Water Supply'),
(320, 'Weather Updates (Thunderstorms, Rainfall Advisories, Monsoon Rains)'),
(321, 'Welfare of teachers (Wage, compensation/benefits)'),
(322, 'West Philippine Sea'),
(323, 'Womens Rights');

-- --------------------------------------------------------

--
-- Table structure for table `tbltype`
--

CREATE TABLE `tbltype` (
  `type_id` int NOT NULL,
  `type_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltype`
--

INSERT INTO `tbltype` (`type_id`, `type_name`) VALUES
(1, 'Commentary'),
(2, 'Interview'),
(3, 'News'),
(4, 'Public Opinion');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `user_id` int NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('ADMIN','EDITOR','WRITER') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`user_id`, `first_name`, `last_name`, `username`, `password`, `role`, `status`) VALUES
(1, 'John Michael', 'Cinco', 'jmcinco', '$2y$12$OR66sP7DM83huN.LS9/CEeTGShMuFZ.zOk8fOzjUA7GTPBuT3hy82', 'ADMIN', 'ACTIVE'),
(2, 'John Michael', 'Cinco', 'jmcincowriter', '$2y$12$Qk7Hmy.Rqmw8g2.lEqujF.AV3e0G65OtUtDcbPx0ftDunJ/nnlzrK', 'WRITER', 'ACTIVE'),
(3, 'John Michael', 'Cinco', 'jmcincoeditor', '$2y$12$xeuO96ZcE/v2lX7ykUgHBev1iJRipIiocJeLsxJ.MdyopkjUq1H/W', 'EDITOR', 'ACTIVE'),
(4, 'John Michael', 'Cinco', 'jmcinco2', '$2y$12$c02l7tuUnhAEZZs4sPhDMesJ65WtkUSKqbHAOH.Ed0HQiAnVre3sm', 'WRITER', 'INACTIVE'),
(5, 'Sample', 'Writer', 'samplewriter', '$2y$12$M6eV78Pp1sS3bS017Y1E3u0j8uq9ZjgGOG8AO41.80iLZum0igx1W', 'WRITER', 'ACTIVE'),
(6, 'Sample', 'editor', 'sampleeditor', '$2y$12$3POHxMJCc0Q5BlIbljMCs.4kojC.6QP6SK5vvvgq.JKMgZ5e6vdEy', 'EDITOR', 'ACTIVE');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblarticle`
--
ALTER TABLE `tblarticle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_article_status` (`status`),
  ADD KEY `idx_article_created_by` (`created_by`),
  ADD KEY `idx_article_locked_by` (`locked_by`),
  ADD KEY `idx_article_archived_by` (`archived_by`);

--
-- Indexes for table `tblarticle_archive`
--
ALTER TABLE `tblarticle_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_archive_article` (`article_id`),
  ADD KEY `idx_archive_user` (`archived_by`),
  ADD KEY `idx_archive_date` (`archived_at`);

--
-- Indexes for table `tblarticle_edit_history`
--
ALTER TABLE `tblarticle_edit_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_edit_history_article` (`article_id`),
  ADD KEY `idx_edit_history_editor` (`editor_id`),
  ADD KEY `idx_edit_history_active` (`article_id`,`editor_id`,`editing_end`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `tbldepartment`
--
ALTER TABLE `tbldepartment`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `tblmedium`
--
ALTER TABLE `tblmedium`
  ADD PRIMARY KEY (`medium_id`);

--
-- Indexes for table `tblprogram`
--
ALTER TABLE `tblprogram`
  ADD PRIMARY KEY (`program_id`);

--
-- Indexes for table `tblreporter`
--
ALTER TABLE `tblreporter`
  ADD PRIMARY KEY (`reporter_id`);

--
-- Indexes for table `tblslant`
--
ALTER TABLE `tblslant`
  ADD PRIMARY KEY (`slant_id`);

--
-- Indexes for table `tblstation`
--
ALTER TABLE `tblstation`
  ADD PRIMARY KEY (`station_id`);

--
-- Indexes for table `tblsub_category`
--
ALTER TABLE `tblsub_category`
  ADD PRIMARY KEY (`sub_id`);

--
-- Indexes for table `tbltype`
--
ALTER TABLE `tbltype`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblarticle_archive`
--
ALTER TABLE `tblarticle_archive`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblarticle_edit_history`
--
ALTER TABLE `tblarticle_edit_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tbldepartment`
--
ALTER TABLE `tbldepartment`
  MODIFY `department_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblmedium`
--
ALTER TABLE `tblmedium`
  MODIFY `medium_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblprogram`
--
ALTER TABLE `tblprogram`
  MODIFY `program_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblreporter`
--
ALTER TABLE `tblreporter`
  MODIFY `reporter_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblslant`
--
ALTER TABLE `tblslant`
  MODIFY `slant_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblstation`
--
ALTER TABLE `tblstation`
  MODIFY `station_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblsub_category`
--
ALTER TABLE `tblsub_category`
  MODIFY `sub_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT for table `tbltype`
--
ALTER TABLE `tbltype`
  MODIFY `type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblarticle`
--
ALTER TABLE `tblarticle`
  ADD CONSTRAINT `tblarticle_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `tblusers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblarticle_archive`
--
ALTER TABLE `tblarticle_archive`
  ADD CONSTRAINT `fk_archive_article` FOREIGN KEY (`article_id`) REFERENCES `tblarticle` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tblarticle_edit_history`
--
ALTER TABLE `tblarticle_edit_history`
  ADD CONSTRAINT `fk_edit_history_article` FOREIGN KEY (`article_id`) REFERENCES `tblarticle` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
