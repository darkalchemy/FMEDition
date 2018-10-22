-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2018 at 04:14 AM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xblade`
--

-- --------------------------------------------------------

--
-- Table structure for table `avps`
--

CREATE TABLE `avps` (
  `arg` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value_s` text COLLATE utf8_unicode_ci NOT NULL,
  `value_i` int(11) NOT NULL DEFAULT '0',
  `value_u` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `avps`
--

INSERT INTO `avps` (`arg`, `value_s`, `value_i`, `value_u`) VALUES
('lastcleantime', '', 0, 1293321999),
('seeders', '', 0, 0),
('leechers', '', 0, 0),
('last24', '', 0, 0),
('loadlimit', '12.5-1246045258', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` int(11) NOT NULL,
  `addedby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first` int(11) DEFAULT NULL,
  `last` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE `blocks` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `blockid` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bonus`
--

CREATE TABLE `bonus` (
  `id` int(5) NOT NULL,
  `bonusname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `points` decimal(10,1) NOT NULL DEFAULT '0.0',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `art` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'traffic',
  `menge` bigint(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bonus`
--

INSERT INTO `bonus` (`id`, `bonusname`, `points`, `description`, `art`, `menge`) VALUES
(1, '1.0GB Uploaded', '75.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1073741824),
(2, '2.5GB Uploaded', '150.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 2684354560),
(3, '5GB Uploaded', '250.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 5368709120),
(4, '3 Invites', '20.0', 'With enough bonus points acquired, you are able to exchange them for a few invites. The points are then removed from your Bonus Bank and the invitations are added to your invites amount.', 'invite', 3),
(5, 'Custom Title!', '50.0', 'For only 50.0 Karma Bonus Points you can buy yourself a custom title. the only restrictions are no foul or offensive language or userclass can be entered. The points are then removed from your Bonus Bank and your special title is changed to the title of your choice', 'title', 1),
(6, 'VIP Status', '500.0', 'With enough bonus points acquired, you can buy yourself VIP status for one month. The points are then removed from your Bonus Bank and your status is changed.', 'class', 1),
(7, 'Give A Karma Gift', '100.0', 'Well perhaps you don\'t need the upload credit, but you know somebody that could use the Karma boost! You are now able to give your Karma credits as a gift! The points are then removed from your Bonus Bank and added to the account of a user of your choice!\r\n\r\nAnd they recieve a PM with all the info as well as who it came from...', 'gift_1', 1073741824),
(8, 'Custom Smilies', '300.0', 'With enough bonus points acquired, you can buy yourself a set of custom smilies for one month! The points are then removed from your Bonus Bank and with a click of a link, your new smilies are available whenever you post or comment!', 'smile', 1),
(9, 'Remove Warning', '1000.0', 'With enough bonus points acquired... So you\'ve been naughty... tsk tsk Yep now for the Low Low price of only 1000 points you can have that warning taken away lol.!', 'warning', 1),
(10, 'Ratio Fix', '500.0', 'With enough bonus points acquired, you can bring the ratio of one torrent to a 1 to 1 ratio! The points are then removed from your Bonus Bank and your status is changed.', 'ratio', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cat_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No Description'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `cat_desc`) VALUES
(1, 'Appz/PC ISO', 'cat_apps.png', 'No Description'),
(2, 'Appz/misc', 'cat_apps.png', 'No Description'),
(3, 'Anime', 'cat_anime.png', 'No Description'),
(4, 'BluRay/480p', 'cat_480p.png', 'No Description'),
(5, 'BluRay/X264', 'cat_x256.png', 'No Description'),
(6, 'BluRay/Full', 'cat_full.png', 'No Description'),
(7, 'BluRay/720p', 'cat_720p.png', 'No Description'),
(8, 'Movies/DvdR', 'cat_mdvdr.png', 'No Description'),
(9, 'Movies/DvdRip', 'cat_rips.png', 'No Description'),
(10, 'Movies/Mp4', 'cat_mp4.png', 'No Description'),
(11, 'Music/Videos', 'cat_vids.png', 'No Description'),
(12, 'Music/Mp3', 'cat_mp3.png', 'No Description'),
(13, 'Music/Flac', 'cat_flack.png', 'No Description'),
(14, 'Episodes/BluRay', 'cat_tvb.png', 'No Description'),
(15, 'Episodes/DvdR', 'cat_dvdre.png', 'No Description'),
(16, 'Episodes/DvdRip', 'cat_erip.png', 'No Description'),
(17, 'Episodes/X264', 'cat_x256e.png', 'No Description'),
(18, 'Episodes/Packs', 'cat_ep.png', 'No Description'),
(19, 'Episodes/SDx264', 'cat_sd.png', 'No Description'),
(20, 'Adult/XxX', 'cat_x.png', 'No Description'),
(21, 'Games/Games', 'cat_ors.png', 'No Description');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `ori_text` text COLLATE utf8_unicode_ci NOT NULL,
  `editedby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `editedat` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flagpic` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `flagpic`) VALUES
(1, 'Sweden', 'sweden.gif'),
(2, 'United States of America', 'usa.gif'),
(3, 'Russia', 'russia.gif'),
(4, 'Finland', 'finland.gif'),
(5, 'Canada', 'canada.gif'),
(6, 'France', 'france.gif'),
(7, 'Germany', 'germany.gif'),
(8, 'China', 'china.gif'),
(9, 'Italy', 'italy.gif'),
(10, 'Denmark', 'denmark.gif'),
(11, 'Norway', 'norway.gif'),
(12, 'United Kingdom', 'uk.gif'),
(13, 'Ireland', 'ireland.gif'),
(14, 'Poland', 'poland.gif'),
(15, 'Netherlands', 'netherlands.gif'),
(16, 'Belgium', 'belgium.gif'),
(17, 'Japan', 'japan.gif'),
(18, 'Brazil', 'brazil.gif'),
(19, 'Argentina', 'argentina.gif'),
(20, 'Australia', 'australia.gif'),
(21, 'New Zealand', 'newzealand.gif'),
(22, 'Spain', 'spain.gif'),
(23, 'Portugal', 'portugal.gif'),
(24, 'Mexico', 'mexico.gif'),
(25, 'Singapore', 'singapore.gif'),
(67, 'India', 'india.gif'),
(62, 'Albania', 'albania.gif'),
(26, 'South Africa', 'southafrica.gif'),
(27, 'South Korea', 'southkorea.gif'),
(28, 'Jamaica', 'jamaica.gif'),
(29, 'Luxembourg', 'luxembourg.gif'),
(30, 'Hong Kong', 'hongkong.gif'),
(31, 'Belize', 'belize.gif'),
(32, 'Algeria', 'algeria.gif'),
(33, 'Angola', 'angola.gif'),
(34, 'Austria', 'austria.gif'),
(35, 'Yugoslavia', 'yugoslavia.gif'),
(36, 'Western Samoa', 'westernsamoa.gif'),
(37, 'Malaysia', 'malaysia.gif'),
(38, 'Dominican Republic', 'dominicanrep.gif'),
(39, 'Greece', 'greece.gif'),
(40, 'Guatemala', 'guatemala.gif'),
(41, 'Israel', 'israel.gif'),
(42, 'Pakistan', 'pakistan.gif'),
(43, 'Czech Republic', 'czechrep.gif'),
(44, 'Serbia', 'serbia.gif'),
(45, 'Seychelles', 'seychelles.gif'),
(46, 'Taiwan', 'taiwan.gif'),
(47, 'Puerto Rico', 'puertorico.gif'),
(48, 'Chile', 'chile.gif'),
(49, 'Cuba', 'cuba.gif'),
(50, 'Congo', 'congo.gif'),
(51, 'Afghanistan', 'afghanistan.gif'),
(52, 'Turkey', 'turkey.gif'),
(53, 'Uzbekistan', 'uzbekistan.gif'),
(54, 'Switzerland', 'switzerland.gif'),
(55, 'Kiribati', 'kiribati.gif'),
(56, 'Philippines', 'philippines.gif'),
(57, 'Burkina Faso', 'burkinafaso.gif'),
(58, 'Nigeria', 'nigeria.gif'),
(59, 'Iceland', 'iceland.gif'),
(60, 'Nauru', 'nauru.gif'),
(61, 'Slovenia', 'slovenia.gif'),
(63, 'Turkmenistan', 'turkmenistan.gif'),
(64, 'Bosnia Herzegovina', 'bosniaherzegovina.gif'),
(65, 'Andorra', 'andorra.gif'),
(66, 'Lithuania', 'lithuania.gif'),
(68, 'Netherlands Antilles', 'nethantilles.gif'),
(69, 'Ukraine', 'ukraine.gif'),
(70, 'Venezuela', 'venezuela.gif'),
(71, 'Hungary', 'hungary.gif'),
(72, 'Romania', 'romania.gif'),
(73, 'Vanuatu', 'vanuatu.gif'),
(74, 'Vietnam', 'vietnam.gif'),
(75, 'Trinidad & Tobago', 'trinidadandtobago.gif'),
(76, 'Honduras', 'honduras.gif'),
(77, 'Kyrgyzstan', 'kyrgyzstan.gif'),
(78, 'Ecuador', 'ecuador.gif'),
(79, 'Bahamas', 'bahamas.gif'),
(80, 'Peru', 'peru.gif'),
(81, 'Cambodia', 'cambodia.gif'),
(82, 'Barbados', 'barbados.gif'),
(83, 'Bangladesh', 'bangladesh.gif'),
(84, 'Laos', 'laos.gif'),
(85, 'Uruguay', 'uruguay.gif'),
(86, 'Antigua Barbuda', 'antiguabarbuda.gif'),
(87, 'Paraguay', 'paraguay.gif'),
(89, 'Thailand', 'thailand.gif'),
(88, 'Union of Soviet Socialist Republics', 'ussr.gif'),
(90, 'Senegal', 'senegal.gif'),
(91, 'Togo', 'togo.gif'),
(92, 'North Korea', 'northkorea.gif'),
(93, 'Croatia', 'croatia.gif'),
(94, 'Estonia', 'estonia.gif'),
(95, 'Colombia', 'colombia.gif'),
(96, 'Lebanon', 'lebanon.gif'),
(97, 'Latvia', 'latvia.gif'),
(98, 'Costa Rica', 'costarica.gif'),
(99, 'Egypt', 'egypt.gif'),
(100, 'Bulgaria', 'bulgaria.gif'),
(101, 'Scotland', 'scotland.gif'),
(102, 'United Arab Emirates', 'uae.gif');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(10) UNSIGNED NOT NULL,
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE `forums` (
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `minclassread` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `minclasswrite` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `postcount` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `topiccount` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `minclasscreate` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `friendid` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funds`
--

CREATE TABLE `funds` (
  `id` int(10) UNSIGNED NOT NULL,
  `cash` decimal(8,2) NOT NULL DEFAULT '0.00',
  `user` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invite_codes`
--

CREATE TABLE `invite_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `receiver` varchar(32) NOT NULL DEFAULT '0',
  `code` varchar(32) NOT NULL DEFAULT '',
  `invite_added` int(10) NOT NULL,
  `status` enum('Pending','Confirmed') NOT NULL DEFAULT 'Pending'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `receiver` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `subject` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No Subject',
  `msg` text COLLATE utf8_unicode_ci,
  `unread` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `poster` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `location` smallint(6) NOT NULL DEFAULT '1',
  `saved` enum('no','yes') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `headline` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'News'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `openclose`
--

CREATE TABLE `openclose` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `openclose`
--

INSERT INTO `openclose` (`name`, `value`) VALUES
('signup', '1');

-- --------------------------------------------------------

--
-- Table structure for table `peers`
--

CREATE TABLE `peers` (
  `id` int(10) UNSIGNED NOT NULL,
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `peer_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `port` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `to_go` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `seeder` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `started` int(11) NOT NULL,
  `last_action` int(11) NOT NULL,
  `connectable` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `agent` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `finishedat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `downloadoffset` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `uploadoffset` bigint(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pmboxes`
--

CREATE TABLE `pmboxes` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `boxnumber` tinyint(4) NOT NULL DEFAULT '2',
  `name` varchar(15) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `topicid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `editedby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `editedat` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `readposts`
--

CREATE TABLE `readposts` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `topicid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastpostread` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reputation`
--

CREATE TABLE `reputation` (
  `reputationid` int(11) UNSIGNED NOT NULL,
  `reputation` int(10) NOT NULL DEFAULT '0',
  `whoadded` int(10) NOT NULL DEFAULT '0',
  `reason` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateadd` int(10) NOT NULL DEFAULT '0',
  `postid` int(10) NOT NULL DEFAULT '0',
  `userid` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reputationlevel`
--

CREATE TABLE `reputationlevel` (
  `reputationlevelid` int(11) UNSIGNED NOT NULL,
  `minimumreputation` int(10) NOT NULL DEFAULT '0',
  `level` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `searchcloud`
--

CREATE TABLE `searchcloud` (
  `id` int(10) UNSIGNED NOT NULL,
  `searchedfor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `howmuch` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `searchcloud`
--

INSERT INTO `searchcloud` (`id`, `searchedfor`, `howmuch`) VALUES
(1, 'bob', 3),
(2, 'testing', 6),
(3, 'blackadder', 3),
(4, '24', 4),
(5, 'FMEDition xblade', 5);

-- --------------------------------------------------------

--
-- Table structure for table `shoutbox`
--

CREATE TABLE `shoutbox` (
  `id` bigint(10) NOT NULL,
  `userid` bigint(6) NOT NULL DEFAULT '0',
  `to_user` int(10) NOT NULL DEFAULT '0',
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `text_parsed` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sitelog`
--

CREATE TABLE `sitelog` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` int(11) NOT NULL,
  `txt` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `snatched`
--

CREATE TABLE `snatched` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `torrentid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `port` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `connectable` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `agent` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `peer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `upspeed` bigint(20) NOT NULL DEFAULT '0',
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `downspeed` bigint(20) NOT NULL DEFAULT '0',
  `to_go` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `seeder` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `seedtime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `leechtime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `start_date` int(10) NOT NULL,
  `last_action` int(10) NOT NULL,
  `complete_date` int(10) NOT NULL,
  `timesann` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `hit_and_run` int(11) NOT NULL,
  `mark_of_cain` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `finished` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `class` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stylesheets`
--

CREATE TABLE `stylesheets` (
  `id` int(10) UNSIGNED NOT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `stylesheets`
--

INSERT INTO `stylesheets` (`id`, `uri`, `name`) VALUES
(1, '1.css', '(default)');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locked` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `forumid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastpost` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sticky` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `torrents`
--

CREATE TABLE `torrents` (
  `id` int(10) UNSIGNED NOT NULL,
  `info_hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `save_as` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `search_text` text COLLATE utf8_unicode_ci NOT NULL,
  `descr` text COLLATE utf8_unicode_ci NOT NULL,
  `ori_descr` text COLLATE utf8_unicode_ci NOT NULL,
  `category` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `size` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `type` enum('single','multi') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'single',
  `numfiles` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `comments` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `times_completed` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `leechers` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `seeders` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_action` int(11) NOT NULL,
  `visible` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `banned` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `owner` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `numratings` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ratingsum` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `nfo` text COLLATE utf8_unicode_ci NOT NULL,
  `poster` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pic/noposter.jpg',
  `sticky` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `tube` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `vip` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `free` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `box` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `client_created_by` char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `passhash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('pending','confirmed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `added` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  `last_access` int(11) NOT NULL,
  `editsecret` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `privacy` enum('strong','normal','low') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `stylesheet` int(10) DEFAULT '1',
  `info` text COLLATE utf8_unicode_ci,
  `acceptpms` enum('yes','friends','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `class` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `language` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `support` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `supportfor` text COLLATE utf8_unicode_ci NOT NULL,
  `donoruntil` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `donated` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total_donated` decimal(8,2) NOT NULL DEFAULT '0.00',
  `vipclass_before` int(10) NOT NULL DEFAULT '0',
  `parked` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `avatar` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `av_w` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
  `av_h` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sig_w` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sig_h` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
  `signatures` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `signature` varchar(225) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `invites` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `invitedby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invite_rights` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `invitedate` int(11) NOT NULL DEFAULT '0',
  `invitees` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `invite_on` enum('yes','no') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'yes',
  `anonymous` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploadpos` int(11) NOT NULL DEFAULT '1',
  `forumpost` int(11) NOT NULL DEFAULT '1',
  `downloadpos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `vip_added` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `vip_until` int(10) NOT NULL DEFAULT '0',
  `seedbonus` decimal(10,1) NOT NULL DEFAULT '200.0',
  `bonuscomment` text COLLATE utf8_unicode_ci,
  `show_shout` enum('yes','no') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'yes',
  `shoutboxbg` enum('1','2','3') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '1',
  `chatpost` int(11) NOT NULL DEFAULT '1',
  `gender` enum('Male','Female','N/A') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `country` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `notifs` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `modcomment` text COLLATE utf8_unicode_ci NOT NULL,
  `enabled` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `avatars` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `donor` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `warned` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `warneduntil` int(11) NOT NULL DEFAULT '0',
  `torrentsperpage` int(3) UNSIGNED NOT NULL DEFAULT '0',
  `topicsperpage` int(3) UNSIGNED NOT NULL DEFAULT '0',
  `postsperpage` int(3) UNSIGNED NOT NULL DEFAULT '0',
  `deletepms` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `savepms` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `reputation` int(10) NOT NULL DEFAULT '10',
  `time_offset` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `dst_in_use` tinyint(1) NOT NULL DEFAULT '0',
  `auto_correct_dst` tinyint(1) NOT NULL DEFAULT '1',
  `clear_new_tag_manually` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `last_browse` int(11) NOT NULL DEFAULT '0',
  `immunity` int(11) NOT NULL DEFAULT '0',
  `hnrwarn` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `hit_and_run_total` int(9) DEFAULT '0',
  `birthday` date DEFAULT '0000-00-00',
  `forum_access` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `avps`
--
ALTER TABLE `avps`
  ADD PRIMARY KEY (`arg`);

--
-- Indexes for table `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `first_last` (`first`,`last`);

--
-- Indexes for table `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userfriend` (`userid`,`blockid`);

--
-- Indexes for table `bonus`
--
ALTER TABLE `bonus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `torrent` (`torrent`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `torrent` (`torrent`);
ALTER TABLE `files` ADD FULLTEXT KEY `filename` (`filename`);

--
-- Indexes for table `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userfriend` (`userid`,`friendid`);

--
-- Indexes for table `funds`
--
ALTER TABLE `funds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invite_codes`
--
ALTER TABLE `invite_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender` (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver` (`receiver`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added` (`added`);

--
-- Indexes for table `openclose`
--
ALTER TABLE `openclose`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `peers`
--
ALTER TABLE `peers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  ADD KEY `torrent` (`torrent`),
  ADD KEY `torrent_seeder` (`torrent`,`seeder`),
  ADD KEY `last_action` (`last_action`),
  ADD KEY `connectable` (`connectable`),
  ADD KEY `userid` (`userid`),
  ADD KEY `passkey` (`passkey`),
  ADD KEY `torrent_connect` (`torrent`,`connectable`);

--
-- Indexes for table `pmboxes`
--
ALTER TABLE `pmboxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topicid` (`topicid`),
  ADD KEY `userid` (`userid`);
ALTER TABLE `posts` ADD FULLTEXT KEY `body` (`body`);

--
-- Indexes for table `readposts`
--
ALTER TABLE `readposts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topicid` (`topicid`);

--
-- Indexes for table `reputation`
--
ALTER TABLE `reputation`
  ADD PRIMARY KEY (`reputationid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `whoadded` (`whoadded`),
  ADD KEY `multi` (`postid`,`userid`),
  ADD KEY `dateadd` (`dateadd`);

--
-- Indexes for table `reputationlevel`
--
ALTER TABLE `reputationlevel`
  ADD PRIMARY KEY (`reputationlevelid`),
  ADD KEY `reputationlevel` (`minimumreputation`);

--
-- Indexes for table `searchcloud`
--
ALTER TABLE `searchcloud`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `searchedfor` (`searchedfor`);

--
-- Indexes for table `shoutbox`
--
ALTER TABLE `shoutbox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `for` (`to_user`);

--
-- Indexes for table `sitelog`
--
ALTER TABLE `sitelog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added` (`added`);

--
-- Indexes for table `snatched`
--
ALTER TABLE `snatched`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tr_usr` (`torrentid`,`userid`);

--
-- Indexes for table `stylesheets`
--
ALTER TABLE `stylesheets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `subject` (`subject`),
  ADD KEY `lastpost` (`lastpost`),
  ADD KEY `locked_sticky` (`locked`,`sticky`),
  ADD KEY `forumid` (`forumid`);

--
-- Indexes for table `torrents`
--
ALTER TABLE `torrents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `info_hash` (`info_hash`),
  ADD KEY `owner` (`owner`),
  ADD KEY `visible` (`visible`),
  ADD KEY `category_visible` (`category`,`visible`);
ALTER TABLE `torrents` ADD FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `ip` (`ip`),
  ADD KEY `uploaded` (`uploaded`),
  ADD KEY `downloaded` (`downloaded`),
  ADD KEY `country` (`country`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `enabled` (`enabled`),
  ADD KEY `warned` (`warned`),
  ADD KEY `pkey` (`passkey`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bans`
--
ALTER TABLE `bans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blocks`
--
ALTER TABLE `blocks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bonus`
--
ALTER TABLE `bonus`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forums`
--
ALTER TABLE `forums`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `funds`
--
ALTER TABLE `funds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invite_codes`
--
ALTER TABLE `invite_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peers`
--
ALTER TABLE `peers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pmboxes`
--
ALTER TABLE `pmboxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `readposts`
--
ALTER TABLE `readposts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reputation`
--
ALTER TABLE `reputation`
  MODIFY `reputationid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reputationlevel`
--
ALTER TABLE `reputationlevel`
  MODIFY `reputationlevelid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `searchcloud`
--
ALTER TABLE `searchcloud`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shoutbox`
--
ALTER TABLE `shoutbox`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sitelog`
--
ALTER TABLE `sitelog`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `snatched`
--
ALTER TABLE `snatched`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stylesheets`
--
ALTER TABLE `stylesheets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `torrents`
--
ALTER TABLE `torrents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
