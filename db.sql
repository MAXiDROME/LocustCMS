SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Структура таблицы `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `text1` text NOT NULL,
  `text2` text NOT NULL,
  `order` int(11) NOT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `href` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `banners`
--

INSERT INTO `banners` (`id`, `name`, `filename`, `text1`, `text2`, `order`, `visible`, `href`) VALUES
(1, 'Тестовый баннер 1-', '', '1234567890', 'английский текст', 0, 1, ''),
(2, 'Тестовый баннер 2', '', '', '', 1, 1, '/about-us/');

-- --------------------------------------------------------

--
-- Структура таблицы `carousel`
--

CREATE TABLE IF NOT EXISTS `carousel` (
`id` int(11) NOT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='карусель на главной';

--
-- Дамп данных таблицы `carousel`
--

INSERT INTO `carousel` (`id`, `visible`, `order`, `title`, `content`) VALUES
(1, 1, 0, 'Buying property is easy with us', 'We shall help to make a choice and observe all legal formalities.'),
(2, 1, 1, 'Selling property is profitable with us', 'We focus on results for each and every property.'),
(3, 1, 2, 'Rental is reliable with us', 'We have been in property management business since 2011.');

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

CREATE TABLE IF NOT EXISTS `config` (
`id` int(11) NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `firm_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(12) NOT NULL DEFAULT '',
  `pagesize` tinyint(4) NOT NULL DEFAULT '10',
  `counters` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `maintenance` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`id`, `regdate`, `firm_name`, `email`, `phone`, `login`, `password`, `pagesize`, `counters`, `url`, `meta_title`, `meta_keywords`, `meta_description`, `maintenance`) VALUES
(1, '2012-11-30 18:00:01', 'firm name', 'email@email', 'phone number', 'login', 'password', 20, '', 'site_url', '', '', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
`id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE IF NOT EXISTS `news` (
`id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `adddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `photo` varchar(255) NOT NULL,
  `rewrite` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `info` text NOT NULL,
  `content` longtext NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
`id` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `rewrite` varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `template` varchar(255) NOT NULL DEFAULT 'blank',
  `order` int(11) NOT NULL DEFAULT '0',
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `protected` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `pages`
--

INSERT INTO `pages` (`id`, `pid`, `title`, `content`, `rewrite`, `meta_title`, `meta_keywords`, `meta_description`, `template`, `order`, `visible`, `published`, `protected`) VALUES
(2, 0, 'Home', '', '/', '', '', '', 'main', 1, 1, 1, 1),
(1, 0, 'Not found (404)', '', '404/', '', '', '', 'blank', 0, 0, 1, 1),
(4, 0, 'News', '', 'news/', '', '', '', 'news', 2, 1, 1, 0),
(5, 0, 'Contacts', '', 'contacts/', '', '', '', 'blank', 3, 1, 1, 0),
(6, 0, 'About Us', '', 'about-us/', 'about-us', '', '', 'blank', 4, 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `blocked` tinyint(4) NOT NULL DEFAULT '0',
  `email_checked` tinyint(4) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `comment` text NOT NULL,
  `admin_comment` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `lastlogin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `blocks` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `content` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `admin_users` (`id`, `login`, `password`, `role`) VALUES
  (1, 'admin', '1', '');
ALTER TABLE `admin_users` ADD `name` VARCHAR(255) NOT NULL , ADD `comment` TEXT NOT NULL ;
ALTER TABLE `admin_users` ADD `lastlogin` TIMESTAMP NOT NULL ;
ALTER TABLE `admin_users` CHANGE `lastlogin` `lastlogin` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `banners`
--
ALTER TABLE `banners`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `carousel`
--
ALTER TABLE `carousel`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `config`
--
ALTER TABLE `config`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `emails`
--
ALTER TABLE `emails`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pages`
--
ALTER TABLE `pages`
 ADD PRIMARY KEY (`id`), ADD KEY `pid` (`pid`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);


ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `banners`
--
ALTER TABLE `banners`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `carousel`
--
ALTER TABLE `carousel`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `config`
--
ALTER TABLE `config`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `emails`
--
ALTER TABLE `emails`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `pages`
--
ALTER TABLE `pages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

