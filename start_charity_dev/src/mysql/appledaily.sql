-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- 主機: localhost:8889
-- 產生時間： 2016 年 04 月 07 日 05:13
-- 伺服器版本: 5.5.38
-- PHP 版本： 5.6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `appledaily`
--

-- --------------------------------------------------------

--
-- 資料表結構 `article`
--

CREATE TABLE `article` (
`id` int(11) NOT NULL,
  `aid` varchar(20) NOT NULL,
  `article` varchar(288) NOT NULL,
  `cover` varchar(288) NOT NULL,
  `title` varchar(288) NOT NULL,
  `url` varchar(288) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `fb_category`
--

CREATE TABLE `fb_category` (
`id` int(11) NOT NULL,
  `c_name` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `fb_category_list`
--

CREATE TABLE `fb_category_list` (
`id` int(11) NOT NULL,
  `cl_name` varchar(150) NOT NULL,
  `cl_id` varchar(30) NOT NULL,
  `c_name` varchar(150) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `fb_favorite`
--

CREATE TABLE `fb_favorite` (
`id` int(11) NOT NULL,
  `fav_id` varchar(30) NOT NULL,
  `fav_name` varchar(200) NOT NULL,
  `fav_type` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `fb_gender`
--

CREATE TABLE `fb_gender` (
`id` int(11) NOT NULL,
  `gender_id` int(11) NOT NULL,
  `gender_name` varchar(288) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `fb_id`
--

CREATE TABLE `fb_id` (
`id` int(11) NOT NULL,
  `fb_id` varchar(100) NOT NULL,
  `email` varchar(288) NOT NULL,
  `subscribe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `fb_like`
--

CREATE TABLE `fb_like` (
`id` int(11) NOT NULL,
  `like_id` varchar(30) NOT NULL,
  `like_name` varchar(288) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `subscribe`
--

CREATE TABLE `subscribe` (
`id` int(11) NOT NULL,
  `subscribe_value` int(11) NOT NULL,
  `subscribe_name` varchar(288) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 資料表結構 `uniq_id`
--

CREATE TABLE `uniq_id` (
`id` int(11) NOT NULL,
  `uniq_id` varchar(30) NOT NULL,
  `fb_id` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
`id` int(10) NOT NULL COMMENT '流水號',
  `user_key` varchar(50) NOT NULL COMMENT '欄位名稱',
  `user_definition` varchar(288) NOT NULL COMMENT '對應意義'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `article`
--
ALTER TABLE `article`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `aid` (`aid`);

--
-- 資料表索引 `fb_category`
--
ALTER TABLE `fb_category`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `key` (`c_name`);

--
-- 資料表索引 `fb_category_list`
--
ALTER TABLE `fb_category_list`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `cl_name` (`cl_name`,`cl_id`,`c_name`);

--
-- 資料表索引 `fb_favorite`
--
ALTER TABLE `fb_favorite`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `fav_id` (`fav_id`,`fav_name`,`fav_type`);

--
-- 資料表索引 `fb_gender`
--
ALTER TABLE `fb_gender`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `key` (`gender_id`);

--
-- 資料表索引 `fb_id`
--
ALTER TABLE `fb_id`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `key` (`fb_id`);

--
-- 資料表索引 `fb_like`
--
ALTER TABLE `fb_like`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `key` (`like_id`);

--
-- 資料表索引 `subscribe`
--
ALTER TABLE `subscribe`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `key` (`subscribe_value`);

--
-- 資料表索引 `uniq_id`
--
ALTER TABLE `uniq_id`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `uniq_id` (`uniq_id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `user_key` (`user_key`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `article`
--
ALTER TABLE `article`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `fb_category`
--
ALTER TABLE `fb_category`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `fb_category_list`
--
ALTER TABLE `fb_category_list`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `fb_favorite`
--
ALTER TABLE `fb_favorite`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `fb_gender`
--
ALTER TABLE `fb_gender`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `fb_id`
--
ALTER TABLE `fb_id`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `fb_like`
--
ALTER TABLE `fb_like`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `subscribe`
--
ALTER TABLE `subscribe`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `uniq_id`
--
ALTER TABLE `uniq_id`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `user`
--
ALTER TABLE `user`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '流水號';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
