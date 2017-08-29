-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526C4B89032C` (`post_id`),
  CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `comment` (`id`, `post_id`, `content`, `status`, `create_time`, `author`, `email`, `url`) VALUES
(24,	56,	'fasdfadf',	2,	'2017-08-19 22:59:37',	'Esgras',	'esgras@mail.com',	NULL),
(26,	56,	'fasdfsadf',	2,	'2017-08-19 23:00:57',	'Esgras',	'esgras@mail.com',	NULL),
(27,	56,	'fasdfdaf',	1,	'2017-08-19 23:01:15',	'Esgras',	'esgras@mail.com',	NULL),
(28,	56,	'Here is some context...',	1,	'2017-08-20 18:58:47',	'Vayn',	'vayn@mail.com',	NULL),
(29,	56,	'Some Content',	1,	'2017-08-20 19:18:13',	'AuthorX',	'some@mail.com',	NULL);

DROP TABLE IF EXISTS `lookup`;
CREATE TABLE `lookup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `lookup` (`id`, `name`, `code`, `type`, `position`) VALUES
(1,	'Draft',	1,	'PostStatus',	1),
(2,	'Published',	2,	'PostStatus',	2),
(3,	'Archived',	3,	'PostStatus',	3),
(4,	'Pending Approval',	1,	'CommentStatus',	1),
(5,	'Approved',	2,	'CommentStatus',	2);

DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `migration_versions` (`version`) VALUES
('20170829042430');

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A8A6C8DF675F31B` (`author_id`),
  CONSTRAINT `FK_5A8A6C8DF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `post` (`id`, `author_id`, `title`, `content`, `tags`, `status`, `create_time`, `update_time`) VALUES
(52,	1,	'Title New One',	'Content',	'yii, symfony, blog, posts, cool, cold',	3,	'2017-08-18 21:15:29',	'2017-08-18 21:15:29'),
(53,	1,	'Some Title',	'Content',	'yii, symfony, blog, cool, cold',	3,	'2017-08-18 21:15:42',	'2017-08-18 21:15:42'),
(54,	1,	'Hello, World',	'Content',	'yii, symfony, blog, posts, cool, cold',	3,	'2017-08-18 21:15:57',	'2017-08-18 21:15:57'),
(55,	1,	'Title New',	'Content',	'tag, blog, yii, error, test, symfony',	1,	'2017-08-18 21:16:09',	'2017-08-18 21:16:09'),
(56,	1,	'Title New One',	'Some Content',	'yii, symfony, blog, posts, cool, cold',	3,	'2017-08-18 21:16:21',	'2017-08-18 21:16:21'),
(57,	1,	'New Post',	'Here is some content',	'yii, post, cool, comment',	1,	'2017-08-20 20:14:23',	'2017-08-20 20:14:23');

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `frequency` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `tag` (`id`, `name`, `frequency`) VALUES
(16,	'symfony',	5),
(17,	'blog',	5),
(18,	'cool',	5),
(19,	'yii',	6),
(22,	'posts',	3),
(23,	'cold',	4),
(24,	'tag',	1),
(25,	'error',	1),
(26,	'test',	1),
(27,	'post',	1),
(28,	'comment',	1);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user` (`id`, `username`, `password`, `email`, `hash`) VALUES
(1,	'admin',	'$2y$13$DErj9HQImfTDjF6/TzAJXugypg3MSvDDRJEllZ9WwHE4QbQmtqCmG',	'',	NULL),
(3,	'Esgras',	'$2y$13$RSxG0qGEFcCo6qcLfE96H.KeBQNkx8aPRWSifUwtVQtOrzhN4B1we',	'esgras@ukr.net',	'e49e8102d1b4043ecf5ea6d5f33add09'),
(5,	'Vayn',	'$2y$13$yd2Wcx8313Rhbxm8VsXzqeMXaIVUtgTaCPlH9vXKnwryqPinZEf0y',	'vaynnorg@gmail.com',	NULL);

-- 2017-08-29 04:59:21
