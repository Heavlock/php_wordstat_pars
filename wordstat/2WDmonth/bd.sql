-- --------------------------------------------------------

--
-- Структура таблицы `temp`
--

CREATE TABLE IF NOT EXISTS `temp` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `key` text,
  `count` text,
  `region` int(4) NOT NULL,
  `month` int(11) NOT NULL,
  `year` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `temp`
--

INSERT INTO `temp` (`id`, `key`, `count`, `region`, `month`, `year`) VALUES
(1, 'купить1с', '6', 2, 2, '2010'),
(2, 'золото', '55314', 2, 2, '2010'),
(3, 'купить1с', '35', 0, 2, '2010'),
(4, 'золото', '651928', 0, 2, '2010'),
(5, 'купить1с', '30', 225, 2, '2010'),
(6, 'золото', '566068', 225, 2, '2010');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `login` text,
  `email` text,
  `pass` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `email`, `pass`) VALUES
(1, 'admin', 'gregory.pelipenko@gmail.com', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Структура таблицы `variables`
--

CREATE TABLE IF NOT EXISTS `variables` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Дамп данных таблицы `variables`
--

INSERT INTO `variables` (`id`, `name`, `value`) VALUES
(27, 'wordstat', '0'),
(30, 'needlimit', '1'),
(15, 'month', '2'),
(29, 'mincount', ''),
(28, 'adstat', '0'),
(18, 'region1', '2'),
(19, 'region2', '0'),
(20, 'region3', '225'),
(21, 'region4', '65535'),
(22, 'region_name1', 'Санкт-Петербург'),
(23, 'region_name2', 'Москва'),
(24, 'region_name3', 'Россия'),
(25, 'region_name4', 'Не выбран'),
(26, 'year', '2010');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
