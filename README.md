# test

1)	Данные для подключения к БД меняются в /php/dbClass.php

2)	В базе создать таблицу 'users', sql создания в конце этого файла

3)	При первом запросе страницы, появится форма из 3-х полей, первое необязательное поле - имя учетной записи приложения; второе - Логин gmail exempl@gmail.com; три - Пароль gmail;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_bin DEFAULT 'user',
  `mail` varchar(128) COLLATE utf8_bin NOT NULL,
  `pass` varchar(64) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



