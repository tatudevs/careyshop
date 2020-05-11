-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 2020-05-11 09:16:40
-- 服务器版本： 5.7.27-log
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `careyshop`
--

DELIMITER $$
--
-- 函数
--
CREATE DEFINER=`careyshop`@`%` FUNCTION `getRegionChildrenList` (`rootId` TEXT) RETURNS VARCHAR(4000) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci NO SQL
    COMMENT '根据父ID获取区域所有子级'
BEGIN
DECLARE sTemp VARCHAR(4000);
DECLARE sTempChd VARCHAR(4000);

SET sTemp = null;
SET sTempChd = cast(rootId as CHAR);

WHILE sTempChd is not null DO
IF (sTemp is not null) THEN
SET sTemp = concat(sTemp,',',sTempChd);
ELSE
SET sTemp = concat(sTempChd);
END IF;
SELECT group_concat(region_id) INTO sTempChd FROM cs_region where FIND_IN_SET(parent_id,sTempChd)>0;
END WHILE;
RETURN sTemp;
END$$

CREATE DEFINER=`careyshop`@`%` FUNCTION `getStorageChildrenList` (`rootId` TEXT) RETURNS VARCHAR(4000) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci NO SQL
    COMMENT '根据父ID获取资源管理器所有子级'
BEGIN
DECLARE sTemp VARCHAR(4000);
DECLARE sTempChd VARCHAR(4000);

SET sTemp = null;
SET sTempChd = cast(rootId as CHAR);

WHILE sTempChd is not null DO
IF (sTemp is not null) THEN
SET sTemp = concat(sTemp,',',sTempChd);
ELSE
SET sTemp = concat(sTempChd);
END IF;
SELECT group_concat(storage_id) INTO sTempChd FROM cs_storage where FIND_IN_SET(parent_id,sTempChd)>0;
END WHILE;
RETURN sTemp;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `cs_action_log`
--

CREATE TABLE `cs_action_log` (
  `action_log_id` int(11) UNSIGNED NOT NULL,
  `client_type` tinyint(1) DEFAULT '-1' COMMENT '-1=游客 0=顾客组 1=管理组',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '账号编号',
  `username` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '访问路径',
  `module` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '模型',
  `header` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求头部',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求参数',
  `result` longtext COLLATE utf8mb4_unicode_ci COMMENT '处理结果',
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=成功 1=错误',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志';

--
-- 插入之前先把表清空（truncate） `cs_action_log`
--

TRUNCATE TABLE `cs_action_log`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_admin`
--

CREATE TABLE `cs_admin` (
  `admin_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号',
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户组Id',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `head_pic` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `last_login` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录日期',
  `last_ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删 ',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理组账号';

--
-- 插入之前先把表清空（truncate） `cs_admin`
--

TRUNCATE TABLE `cs_admin`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_ads`
--

CREATE TABLE `cs_ads` (
  `ads_id` int(11) UNSIGNED NOT NULL,
  `ads_position_id` smallint(5) UNSIGNED NOT NULL COMMENT '对应ads_position表',
  `code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '编码',
  `platform` tinyint(3) NOT NULL COMMENT '对应ads_position表',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '链接',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '_self _blank',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容(图片,代码等)',
  `color` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff' COMMENT '背景色',
  `type` tinyint(1) NOT NULL COMMENT '0=图片 1=代码',
  `begin_time` int(11) NOT NULL DEFAULT '0' COMMENT '投放日期',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束日期',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告列表';

--
-- 插入之前先把表清空（truncate） `cs_ads`
--

TRUNCATE TABLE `cs_ads`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_ads_position`
--

CREATE TABLE `cs_ads_position` (
  `ads_position_id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '编码',
  `platform` tinyint(3) NOT NULL COMMENT '平台(自定义)',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '位置名称',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `width` smallint(5) NOT NULL DEFAULT '0' COMMENT '位置宽度',
  `height` smallint(5) NOT NULL DEFAULT '0' COMMENT '位置高度',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '默认内容(图片,代码等)',
  `color` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff' COMMENT '背景色',
  `type` tinyint(1) NOT NULL COMMENT '0=图片 1=代码',
  `display` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=多个 1=单个 2=随机多个 3=随机单个',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用 	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告位';

--
-- 插入之前先把表清空（truncate） `cs_ads_position`
--

TRUNCATE TABLE `cs_ads_position`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_app`
--

CREATE TABLE `cs_app` (
  `app_id` smallint(5) UNSIGNED NOT NULL,
  `app_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `app_key` int(8) UNSIGNED NOT NULL COMMENT '钥匙',
  `app_secret` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密钥',
  `captcha` tinyint(1) NOT NULL DEFAULT '0' COMMENT '启用验证码 0=否 1=是',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用 ',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='应用列表';

--
-- 插入之前先把表清空（truncate） `cs_app`
--

TRUNCATE TABLE `cs_app`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_app_install`
--

CREATE TABLE `cs_app_install` (
  `app_install_id` smallint(5) UNSIGNED NOT NULL,
  `user_agent` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '系统标识',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用名称',
  `ver` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本号',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '地址',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '访问次数',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='应用安装包';

--
-- 插入之前先把表清空（truncate） `cs_app_install`
--

TRUNCATE TABLE `cs_app_install`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_article`
--

CREATE TABLE `cs_article` (
  `article_id` int(11) UNSIGNED NOT NULL,
  `article_cat_id` smallint(5) UNSIGNED NOT NULL COMMENT '对应article_cat表',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `image` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '封面',
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `source` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '来源',
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '来源地址',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '外部链接',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '_self _blank',
  `page_views` int(11) NOT NULL DEFAULT '0' COMMENT '游览量',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶 0=否 1=是',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章管理';

--
-- 插入之前先把表清空（truncate） `cs_article`
--

TRUNCATE TABLE `cs_article`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_article_cat`
--

CREATE TABLE `cs_article_cat` (
  `article_cat_id` smallint(5) UNSIGNED NOT NULL,
  `parent_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `cat_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `cat_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型(自定义)',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类描述',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `is_navi` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航 0=否 1=是'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章分类';

--
-- 插入之前先把表清空（truncate） `cs_article_cat`
--

TRUNCATE TABLE `cs_article_cat`;
--
-- 转存表中的数据 `cs_article_cat`
--

INSERT INTO `cs_article_cat` (`article_cat_id`, `parent_id`, `cat_name`, `cat_type`, `keywords`, `description`, `sort`, `is_navi`) VALUES
(1, 0, '物流配送', 0, '', '', 50, 1),
(2, 1, '配送查询', 0, '', '', 50, 1),
(3, 1, '配送服务', 0, '', '', 50, 1),
(4, 1, '配送费用', 0, '', '', 50, 1),
(5, 1, '配送时效', 0, '', '', 50, 1),
(6, 1, '签收与验货', 0, '', '', 50, 1),
(7, 0, '支付与账户', 0, '', '', 50, 1),
(8, 7, '货到付款', 0, '', '', 50, 1),
(9, 7, '在线支付', 0, '', '', 50, 1),
(10, 7, '分期付款', 0, '', '', 50, 1),
(11, 7, '门店支付', 0, '', '', 50, 1),
(12, 7, '发票制度', 0, '', '', 50, 1),
(13, 0, '售后服务', 0, '', '', 50, 1),
(14, 13, '退换货服务', 0, '', '', 50, 1),
(15, 13, '退款说明', 0, '', '', 50, 1),
(16, 13, '专业维修', 0, '', '', 50, 1),
(17, 13, '延保服务', 0, '', '', 50, 1),
(18, 13, '家电回收', 0, '', '', 50, 1),
(19, 0, '会员专区', 0, '', '', 50, 1),
(20, 19, '会员介绍', 0, '', '', 50, 1),
(21, 19, '优惠券说明', 0, '', '', 50, 1),
(22, 19, '商品评价', 0, '', '', 50, 1),
(23, 0, '购物帮助', 0, '', '', 50, 1),
(24, 23, '购物保障', 0, '', '', 50, 1),
(25, 23, '购物流程', 0, '', '', 50, 1),
(26, 23, '促销优惠', 0, '', '', 50, 1),
(27, 23, '焦点问题', 0, '', '', 50, 1),
(28, 23, '联系我们', 0, '', '', 50, 1);

-- --------------------------------------------------------

--
-- 表的结构 `cs_ask`
--

CREATE TABLE `cs_ask` (
  `ask_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '对应user表',
  `ask_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=咨询 1=售后 2=投诉 3=求购',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=主题 1=提问 2=回答',
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '主题',
  `ask` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '提问内容',
  `answer` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回答内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=待回答 1=已回答',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='咨询问答';

--
-- 插入之前先把表清空（truncate） `cs_ask`
--

TRUNCATE TABLE `cs_ask`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_auth_group`
--

CREATE TABLE `cs_auth_group` (
  `group_id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '系统保留',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户组';

--
-- 插入之前先把表清空（truncate） `cs_auth_group`
--

TRUNCATE TABLE `cs_auth_group`;
--
-- 转存表中的数据 `cs_auth_group`
--

INSERT INTO `cs_auth_group` (`group_id`, `name`, `description`, `system`, `sort`, `status`) VALUES
(1, '超级管理员', '系统保留，拥有最高权限', 1, 50, 1),
(2, '普通管理员', '系统保留，拥有较高权限', 1, 50, 1),
(3, '普通顾客', '系统保留，前台普通顾客', 1, 50, 1),
(4, '游客', '系统保留，无需授权即可访问', 1, 50, 1),
(5, '后勤', '这是一段描述', 0, 50, 1),
(6, '售后', '这是一段描述', 0, 50, 1),
(7, '客服', '这是一段描述', 0, 50, 1),
(8, '财务', '这是一段描述', 0, 50, 1);

-- --------------------------------------------------------

--
-- 表的结构 `cs_auth_rule`
--

CREATE TABLE `cs_auth_rule` (
  `rule_id` mediumint(8) UNSIGNED NOT NULL,
  `module` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属模块',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户组Id',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则名称',
  `menu_auth` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '菜单权限',
  `log_auth` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '记录权限',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限规则';

--
-- 插入之前先把表清空（truncate） `cs_auth_rule`
--

TRUNCATE TABLE `cs_auth_rule`;
--
-- 转存表中的数据 `cs_auth_rule`
--

INSERT INTO `cs_auth_rule` (`rule_id`, `module`, `group_id`, `name`, `menu_auth`, `log_auth`, `sort`, `status`) VALUES
(1, 'api', 1, '超级管理员', '[1,2,3,4,5,6,7,8,9,10,11,1049,12,13,14,15,16,17,18,19,20,21,22,615,617,618,23,24,25,26,27,28,29,30,614,616,31,32,33,34,35,36,37,38,39,1010,1011,1013,1012,40,41,42,43,44,45,46,1009,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,619,66,67,69,70,71,72,73,74,75,76,77,78,620,79,80,81,82,83,84,85,86,621,87,88,89,90,91,92,93,94,95,96,622,97,98,99,100,101,102,103,106,107,110,130,131,132,133,134,968,135,136,137,138,141,142,144,145,146,147,150,151,152,153,154,155,156,157,158,159,160,623,161,162,163,164,165,166,169,171,172,774,168,173,174,175,176,177,178,179,180,777,181,182,183,184,185,186,187,188,189,966,190,191,192,193,194,195,196,197,624,199,200,201,202,203,204,205,206,207,208,209,210,211,212,215,216,217,957,958,959,967,232,233,234,235,236,237,238,239,240,241,242,626,246,247,249,250,251,252,253,254,255,258,259,260,261,262,265,266,267,268,269,270,271,272,273,274,218,219,220,221,222,223,224,225,226,227,228,908,229,230,231,625,965,414,415,416,417,418,419,907,962,420,421,631,281,282,283,284,734,285,286,287,288,289,290,291,292,293,294,295,296,627,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,628,320,321,322,323,324,325,326,327,328,329,332,333,334,335,336,337,338,341,342,343,344,345,346,347,1054,348,349,350,353,354,355,360,361,362,363,366,367,368,370,371,372,373,374,375,376,377,379,380,629,383,384,385,386,387,388,389,390,391,392,393,394,395,723,724,725,726,727,396,397,398,399,400,401,402,403,404,630,405,406,407,408,409,410,411,412,413,422,423,424,425,426,427,428,429,430,431,432,433,434,435,574,641,648,664,665,956,436,437,438,439,440,441,442,443,632,444,445,446,447,448,449,450,1038,1039,1040,451,452,454,455,456,457,459,461,462,463,464,465,466,467,468,469,840,470,993,475,476,477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,492,493,494,495,496,498,499,502,503,504,505,506,507,508,509,510,511,512,575,576,577,633,634,635,636,637,638,639,640,657,658,659,660,661,662,663,64,104,139,167,198,243,256,263,330,351,369,381,453,460,497]', '[4,5,6,7,8,9,12,13,14,72,73,74,75,77,78,80,81,82,83,85,86,107,200,201,203,204,205,206,207,208,215,217,283,284,286,287,288,289,293,294,295,377,464,465,466,467,468,487,488,502,503,504,507,508,509,1,71,79,104,198,281,285,369,460,483,497,506]', 1, 1),
(2, 'api', 2, '普通管理员', '[1,2,3,4,5,6,7,8,9,10,11,1049,12,13,14,15,16,17,18,19,20,21,22,615,617,618,23,24,25,26,27,28,29,30,614,616,31,32,33,34,35,36,37,38,39,1010,1011,1013,1012,40,41,42,43,44,45,46,1009,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,619,66,67,69,70,74,76,81,84,87,88,89,90,91,92,93,94,95,96,622,97,98,99,100,101,102,103,106,107,110,130,131,132,133,134,968,135,136,137,138,141,142,144,145,146,147,150,151,152,153,154,155,156,157,158,159,160,623,161,162,163,164,165,166,169,171,172,774,168,173,174,175,176,177,178,179,180,777,181,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,624,199,200,201,202,203,204,205,206,207,208,209,210,211,212,215,216,217,957,958,959,967,232,233,234,235,236,237,238,239,240,241,242,626,246,247,249,250,251,252,253,254,255,258,259,260,261,262,265,266,267,268,269,270,271,272,273,274,218,219,220,221,222,223,224,225,226,227,228,908,229,230,231,625,965,414,415,416,417,418,419,907,962,420,421,631,281,282,283,284,734,287,290,291,292,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,628,320,321,322,323,324,325,326,327,328,329,332,333,334,335,336,337,338,341,342,343,344,345,346,347,1054,348,349,350,353,354,355,360,361,362,363,366,367,368,372,373,374,377,379,380,383,384,385,386,387,388,389,390,391,392,393,394,395,723,724,725,726,727,396,397,398,399,400,401,402,403,404,630,406,422,423,424,425,426,427,428,429,430,431,432,433,434,435,574,641,648,664,665,956,436,437,438,439,440,441,442,443,632,444,445,446,447,448,449,450,1038,1039,1040,451,452,454,455,456,457,459,461,462,463,464,465,466,467,468,469,840,470,993,475,476,477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,492,493,494,495,496,498,499,502,503,504,505,506,507,508,509,510,511,512,575,576,577,633,634,635,636,637,638,639,640,657,658,659,660,661,662,663,64,71,79,104,139,167,182,198,243,256,263,285,330,351,369,381,405,453,460,497]', '[4,5,6,7,8,9,10,11,12,13,14,377,465,467,487,488,502,503,504,507,508,509,1,369,460,483,497,506]', 2, 1),
(3, 'api', 3, '普通顾客', '[617,616,1011,1013,1012,46,1009,51,52,59,60,61,65,66,68,69,70,93,94,95,105,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,138,143,144,145,146,148,149,156,157,171,172,774,180,777,189,195,202,209,210,211,213,214,216,957,958,959,237,238,239,244,245,248,249,253,254,255,257,261,262,264,266,418,419,907,275,276,277,278,279,280,282,734,291,292,296,304,305,306,307,308,309,310,316,331,335,336,337,338,339,340,341,342,343,346,347,1054,348,349,350,352,354,355,356,357,358,359,360,364,365,367,372,379,380,381,382,383,384,392,394,395,400,401,402,404,425,427,428,433,434,574,641,648,956,440,441,448,449,1038,1039,1040,451,452,455,456,457,461,462,463,464,465,467,469,840,471,472,473,474,475,476,477,478,479,480,481,482,489,490,491,492,493,494,495,496,498,499,500,501,505,506,507,508,509,510,511,512,637,638,660,663,15,23,31,40,47,55,64,87,104,130,139,150,167,173,182,190,198,232,243,256,263,414,281,285,297,311,330,351,369,385,393,396,422,436,444,453,460,497,633,657]', '[467,500,501,507,508,509,460,497,506]', 3, 1),
(4, 'api', 4, '游客', '[13,617,616,1011,1013,1012,46,1009,51,52,59,60,61,93,94,95,115,138,156,157,170,180,189,195,202,209,210,211,213,214,216,957,958,959,237,238,239,249,253,254,255,418,419,907,282,734,316,341,342,378,392,394,395,400,401,402,404,425,427,428,433,434,574,641,648,956,440,441,448,449,1038,1039,1040,458,461,462,463,464,472,473,474,491,492,493,494,495,496,637,660,663,1,15,23,31,40,47,55,87,113,130,150,167,173,182,190,198,232,243,414,281,311,330,369,385,393,396,422,436,444,453,460,633,657]', '[13,170,378,458,1,167,369,453]', 4, 1),
(5, 'admin', 1, '超级管理员', '[513,534,535,536,679,1080,514,871,872,960,873,940,941,948,947,952,949,942,944,945,946,950,954,955,1082,882,874,884,885,886,887,888,889,890,875,891,892,893,894,897,898,895,896,876,901,902,903,904,905,906,878,909,910,911,912,913,914,915,877,916,917,918,919,920,921,923,924,879,880,932,933,934,935,936,937,939,938,881,927,928,929,930,931,515,867,868,865,1055,1057,1058,1059,1063,1060,1061,1062,1064,1065,1066,1067,1068,1052,1053,866,1007,1008,869,870,1070,1071,1072,1073,1074,1075,1076,1077,1078,1079,1050,1051,516,854,857,858,969,970,971,972,973,974,859,975,976,977,978,979,980,855,860,992,994,995,996,997,998,999,1000,1001,861,1002,1003,1004,1005,856,862,981,982,983,984,987,985,986,864,988,991,989,990,517,819,812,813,828,829,830,831,832,833,834,835,836,846,847,848,849,837,841,842,843,844,845,838,839,1048,814,820,821,822,823,651,815,816,824,825,826,827,817,818,852,851,850,1041,1042,1043,1045,1047,1046,1044,518,519,520,735,539,540,686,687,688,689,690,1085,566,680,681,682,683,684,685,563,564,600,601,602,603,604,605,565,606,607,608,609,610,612,613,559,963,561,592,578,579,580,583,584,585,586,1083,560,591,567,568,569,570,1014,964,562,593,594,595,596,598,599,1084,571,572,667,668,669,670,671,672,674,673,675,676,677,678,573,642,643,644,645,646,647,652,653,691,692,696,693,697,694,695,759,804,805,654,699,700,701,702,703,704,705,655,708,728,729,730,731,521,740,523,524,587,541,542,543,544,545,546,538,525,526,588,547,548,549,550,551,552,528,589,529,530,531,532,533,527,590,553,554,555,556,557,558,712,713,1023,1015,1016,1017,1018,1019,1020,1022,1024,1025,1026,1027,1028,1029,1030,1031,1032,1033,1034,1035,1036,1037,733,732,741,742,743,744,745,746,747,802,806,807,808,809,810,811,710,748,751,752,753,754,755,756,650,709,757,764,765,766,767,768,758,769,770,771,772,773,760,778,779,780,781,785,782,786,787,788,789,790,783,784,711,656,706,714,715,716,717,718,719,698,707,720,721,722,1086,522]', '[]', 1, 1),
(6, 'admin', 2, '普通管理员', '[535,679,871,960,940,884,891,901,909,916,932,927,867,1055,1052,1053,1007,1070,1050,1051,854,969,975,992,1002,981,988,819,828,846,841,1048,820,651,824,852,1041,1042,1043,1045,1047,1046,1044,518,519,735,686,687,688,689,690,680,600,606,592,591,593,667,678,642,691,804,699,708,731,740,587,538,588,589,590,1015,1026,1027,733,741,806,751,650,764,769,778,786,706,707,1086,522,513,534,514,872,873,882,874,875,876,878,877,879,880,881,515,868,865,866,869,870,516,857,858,859,855,860,861,856,862,864,517,812,813,834,836,837,814,815,816,817,818,520,539,540,566,563,564,565,559,561,560,1014,562,571,572,573,652,653,759,654,655,521,523,524,525,526,528,527,712,713,1023,732,802,710,748,709,757,758,760,782,711,656,698]', '[]', 2, 1),
(7, 'home', 1, '超级管理员', '[]', '[]', 1, 0),
(8, 'home', 2, '普通管理员', '[]', '[]', 2, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cs_brand`
--

CREATE TABLE `cs_brand` (
  `brand_id` smallint(5) UNSIGNED NOT NULL,
  `goods_category_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应goods_category表',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌名称',
  `phonetic` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌首拼',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌描述',
  `logo` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌LOGO',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌url',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '_self _blank',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品品牌';

--
-- 插入之前先把表清空（truncate） `cs_brand`
--

TRUNCATE TABLE `cs_brand`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_card`
--

CREATE TABLE `cs_card` (
  `card_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '卡名',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `money` decimal(10,2) NOT NULL COMMENT '面额',
  `category` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '限制商品分类',
  `exclude_category` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '排除商品分类',
  `give_num` int(11) NOT NULL COMMENT '发放量',
  `active_num` int(11) NOT NULL DEFAULT '0' COMMENT '激活量',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '截止日期(有效期)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物卡';

--
-- 插入之前先把表清空（truncate） `cs_card`
--

TRUNCATE TABLE `cs_card`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_card_use`
--

CREATE TABLE `cs_card_use` (
  `card_use_id` int(11) UNSIGNED NOT NULL,
  `card_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应card表',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `number` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '卡号',
  `password` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '卡密',
  `money` decimal(10,2) NOT NULL COMMENT '可用余额',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否激活 0=否 1=是',
  `is_invalid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效 0=无效 1=有效',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `active_time` int(11) NOT NULL DEFAULT '0' COMMENT '激活日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物卡使用';

--
-- 插入之前先把表清空（truncate） `cs_card_use`
--

TRUNCATE TABLE `cs_card_use`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_cart`
--

CREATE TABLE `cs_cart` (
  `cart_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应goods表',
  `goods_num` smallint(5) UNSIGNED NOT NULL COMMENT '购买数量',
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格键名',
  `key_value` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格值',
  `is_selected` tinyint(1) NOT NULL DEFAULT '1' COMMENT '选中 0=否 1=是',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=隐藏 1=显示',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车';

--
-- 插入之前先把表清空（truncate） `cs_cart`
--

TRUNCATE TABLE `cs_cart`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_collect`
--

CREATE TABLE `cs_collect` (
  `collect_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应goods表',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶 0=否 1=是',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品收藏夹';

--
-- 插入之前先把表清空（truncate） `cs_collect`
--

TRUNCATE TABLE `cs_collect`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_coupon`
--

CREATE TABLE `cs_coupon` (
  `coupon_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '优惠劵名称',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `guide` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '引导地址',
  `type` tinyint(1) NOT NULL COMMENT '0=用户 1=生成 2=领取 3=赠送',
  `give_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '领取码',
  `money` decimal(10,2) NOT NULL COMMENT '优惠金额',
  `quota` decimal(10,2) NOT NULL COMMENT '使用门槛',
  `category` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '限制商品分类',
  `exclude_category` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '排除商品分类',
  `level` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '限制会员等级',
  `frequency` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '限制领取次数',
  `give_num` int(11) NOT NULL DEFAULT '0' COMMENT '发放量',
  `receive_num` int(11) NOT NULL DEFAULT '0' COMMENT '领取量',
  `use_num` int(11) NOT NULL DEFAULT '0' COMMENT '使用量',
  `give_begin_time` int(11) NOT NULL COMMENT '发放开始日期',
  `give_end_time` int(11) NOT NULL COMMENT '发放结束日期',
  `use_begin_time` int(11) NOT NULL COMMENT '使用开始日期',
  `use_end_time` int(11) NOT NULL COMMENT '使用截止日期',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `is_invalid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=正常 1=作废',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删 '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='优惠劵';

--
-- 插入之前先把表清空（truncate） `cs_coupon`
--

TRUNCATE TABLE `cs_coupon`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_coupon_give`
--

CREATE TABLE `cs_coupon_give` (
  `coupon_give_id` int(11) UNSIGNED NOT NULL,
  `coupon_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应coupon表',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应order表',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `exchange_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '兑换码',
  `use_time` int(11) NOT NULL DEFAULT '0' COMMENT '使用日期',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删 '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='优惠劵发放';

--
-- 插入之前先把表清空（truncate） `cs_coupon_give`
--

TRUNCATE TABLE `cs_coupon_give`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_delivery`
--

CREATE TABLE `cs_delivery` (
  `delivery_id` smallint(5) UNSIGNED NOT NULL,
  `delivery_item_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应delivery_item表',
  `alias` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `content` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `first_weight` decimal(10,2) NOT NULL COMMENT '首重',
  `first_weight_price` decimal(10,2) NOT NULL COMMENT '首重运费',
  `second_weight` decimal(10,2) NOT NULL COMMENT '续重',
  `second_weight_price` decimal(10,2) NOT NULL COMMENT '续重运费',
  `first_item` tinyint(3) UNSIGNED NOT NULL COMMENT '首件',
  `first_item_price` decimal(10,2) NOT NULL COMMENT '首件运费',
  `second_item` tinyint(3) UNSIGNED NOT NULL COMMENT '续件',
  `second_item_price` decimal(10,2) NOT NULL COMMENT '续件运费',
  `first_volume` decimal(10,2) NOT NULL COMMENT '首体积量',
  `first_volume_price` decimal(10,2) NOT NULL COMMENT '首体积运费',
  `second_volume` decimal(10,2) NOT NULL COMMENT '续体积量',
  `second_volume_price` decimal(10,2) NOT NULL COMMENT '续体积运费',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送方式';

--
-- 插入之前先把表清空（truncate） `cs_delivery`
--

TRUNCATE TABLE `cs_delivery`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_delivery_area`
--

CREATE TABLE `cs_delivery_area` (
  `delivery_area_id` smallint(5) UNSIGNED NOT NULL,
  `delivery_id` smallint(5) UNSIGNED NOT NULL COMMENT '对应delivery表',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '区域名称',
  `region` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所辖区域',
  `first_weight_price` decimal(10,2) NOT NULL COMMENT '首重运费',
  `second_weight_price` decimal(10,2) NOT NULL COMMENT '续重运费',
  `first_item_price` decimal(10,2) NOT NULL COMMENT '首件运费',
  `second_item_price` decimal(10,2) NOT NULL COMMENT '续件运费',
  `first_volume_price` decimal(10,2) NOT NULL COMMENT '首体积运费',
  `second_volume_price` decimal(10,2) NOT NULL COMMENT '续体积运费'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送区域';

--
-- 插入之前先把表清空（truncate） `cs_delivery_area`
--

TRUNCATE TABLE `cs_delivery_area`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_delivery_dist`
--

CREATE TABLE `cs_delivery_dist` (
  `delivery_dist_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '对应user表',
  `order_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '流水号',
  `delivery_item_id` smallint(5) UNSIGNED NOT NULL COMMENT '对应delivery_item表',
  `delivery_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '快递编码',
  `logistic_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '快递单号',
  `trace` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '轨迹',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0=无轨迹 1=已揽收 2=在途中 201=到达派件城市 3=签收 4=问题件',
  `is_sub` tinyint(1) NOT NULL COMMENT '是否订阅 0=否 1=是',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送轨迹';

--
-- 插入之前先把表清空（truncate） `cs_delivery_dist`
--

TRUNCATE TABLE `cs_delivery_dist`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_delivery_item`
--

CREATE TABLE `cs_delivery_item` (
  `delivery_item_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '快递公司',
  `phonetic` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '首拼',
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '快递编码',
  `type` tinyint(1) NOT NULL COMMENT '0=热门 1=国内 2=国外 3=转运',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='快递公司列表';

--
-- 插入之前先把表清空（truncate） `cs_delivery_item`
--

TRUNCATE TABLE `cs_delivery_item`;
--
-- 转存表中的数据 `cs_delivery_item`
--

INSERT INTO `cs_delivery_item` (`delivery_item_id`, `name`, `phonetic`, `code`, `type`, `is_delete`) VALUES
(1, '顺丰速运', 's', 'SF', 0, 0),
(2, '百世快递', 'b', 'HTKY', 0, 0),
(3, '中通快递', 'z', 'ZTO', 0, 0),
(4, '申通快递', 's', 'STO', 0, 0),
(5, '圆通速递', 'y', 'YTO', 0, 0),
(6, '韵达速递', 'y', 'YD', 0, 0),
(7, '邮政快递包裹', 'y', 'YZPY', 0, 0),
(8, 'EMS', 'e', 'EMS', 0, 0),
(9, '天天快递', 't', 'HHTT', 1, 0),
(10, '京东物流', 'j', 'JD', 0, 0),
(11, '优速快递', 'y', 'UC', 1, 0),
(12, '德邦', 'd', 'DBL', 1, 0),
(13, '快捷快递', 'k', 'FAST', 1, 0),
(14, '宅急送', 'z', 'ZJS', 1, 0),
(15, 'TNT快递', 't', 'TNT', 1, 0),
(16, 'UPS', 'u', 'UPS', 1, 0),
(17, 'DHL', 'd', 'DHL', 1, 0),
(18, 'FEDEX联邦(国内件）', 'f', 'FEDEX', 1, 0),
(19, 'FEDEX联邦(国际件）', 'f', 'FEDEX_GJ', 1, 0),
(20, '安捷快递', 'a', 'AJ', 1, 0),
(21, '阿里跨境电商物流', 'a', 'ALKJWL', 1, 0),
(22, '安讯物流', 'a', 'AXWL', 1, 0),
(23, '安邮美国', 'a', 'AYUS', 1, 0),
(24, '亚马逊物流', 'y', 'AMAZON', 1, 0),
(25, '澳门邮政', 'a', 'AOMENYZ', 1, 0),
(26, '安能物流', 'a', 'ANE', 1, 0),
(27, '澳多多', 'a', 'ADD', 1, 0),
(28, '澳邮专线', 'a', 'AYCA', 1, 0),
(29, '八达通  ', 'b', 'BDT', 1, 0),
(30, '百腾物流', 'b', 'BETWL', 1, 0),
(31, '北极星快运', 'b', 'BJXKY', 1, 0),
(32, '巴伦支快运', 'b', 'BLZ', 1, 0),
(33, '奔腾物流', 'b', 'BNTWL', 1, 0),
(34, '百福东方', 'b', 'BFDF', 1, 0),
(35, '贝海国际 ', 'b', 'BHGJ', 1, 0),
(36, '北青小红帽', 'b', 'BQXHM', 1, 0),
(37, '八方安运', 'b', 'BFAY', 1, 0),
(38, '百世快运', 'b', 'BTWL', 1, 0),
(39, 'CCES快递', 'c', 'CCES', 1, 0),
(40, '春风物流', 'c', 'CFWL', 1, 0),
(41, '诚通物流', 'c', 'CHTWL', 1, 0),
(42, '出口易', 'c', 'CKY', 1, 0),
(43, '传喜物流', 'c', 'CXHY', 1, 0),
(44, '程光   ', 'c', 'CG', 1, 0),
(45, '城市100', 'c', 'CITY100', 1, 0),
(46, '城际快递', 'c', 'CJKD', 1, 0),
(47, 'CNPEX中邮快递', 'c', 'CNPEX', 1, 0),
(48, 'COE东方快递', 'c', 'COE', 1, 0),
(49, '长沙创一', 'c', 'CSCY', 1, 0),
(50, '成都善途速运', 'c', 'CDSTKY', 1, 0),
(51, '联合运通', 'l', 'CTG', 1, 0),
(52, '疯狂快递', 'f', 'CRAZY', 1, 0),
(53, 'CBO钏博物流', 'c', 'CBO', 1, 0),
(54, 'D速物流', 'd', 'DSWL', 1, 0),
(55, '到了港', 'd', 'DLG ', 1, 0),
(56, '递四方速递', 'd', 'D4PX', 1, 0),
(57, '大田物流', 'd', 'DTWL', 1, 0),
(58, '东骏快捷物流', 'd', 'DJKJWL', 1, 0),
(59, '德坤', 'd', 'DEKUN', 1, 0),
(60, 'E特快', 'e', 'ETK', 1, 0),
(61, 'EWE', 'e', 'EWE', 1, 0),
(62, '快服务', 'k', 'KFW', 1, 0),
(63, '飞康达', 'f', 'FKD', 1, 0),
(64, '富腾达  ', 'f', 'FTD', 1, 0),
(65, '冠达   ', 'g', 'GD', 1, 0),
(66, '国通快递', 'g', 'GTO', 1, 0),
(67, '广东邮政', 'g', 'GDEMS', 1, 0),
(68, '共速达', 'g', 'GSD', 1, 0),
(69, '广通       ', 'g', 'GTONG', 1, 0),
(70, '迦递快递', 'g', 'GAI', 1, 0),
(71, '港快速递', 'g', 'GKSD', 1, 0),
(72, '高铁速递', 'g', 'GTSD', 1, 0),
(73, '汇丰物流', 'h', 'HFWL', 1, 0),
(74, '黑狗冷链', 'h', 'HGLL', 1, 0),
(75, '恒路物流', 'h', 'HLWL', 1, 0),
(76, '天地华宇', 't', 'HOAU', 1, 0),
(77, '鸿桥供应链', 'h', 'HOTSCM', 1, 0),
(78, '海派通物流公司', 'h', 'HPTEX', 1, 0),
(79, '华强物流', 'h', 'hq568', 1, 0),
(80, '环球速运  ', 'h', 'HQSY', 1, 0),
(81, '华夏龙物流', 'h', 'HXLWL', 1, 0),
(82, '豪翔物流 ', 'h', 'HXWL', 1, 0),
(83, '合肥汇文', 'h', 'HFHW', 1, 0),
(84, '华翰物流', 'h', 'HHWL', 1, 0),
(85, '辉隆物流', 'h', 'HLONGWL', 1, 0),
(86, '华企快递', 'h', 'HQKD', 1, 0),
(87, '韩润物流', 'h', 'HRWL', 1, 0),
(88, '青岛恒通快递', 'q', 'HTKD', 1, 0),
(89, '货运皇物流', 'h', 'HYH', 1, 0),
(90, '好来运快递', 'h', 'HYLSD', 1, 0),
(91, '捷安达  ', 'j', 'JAD', 1, 0),
(92, '京广速递', 'j', 'JGSD', 1, 0),
(93, '九曳供应链', 'j', 'JIUYE', 1, 0),
(94, '佳吉快运', 'j', 'JJKY', 1, 0),
(95, '嘉里物流', 'j', 'JLDT', 1, 0),
(96, '捷特快递', 'j', 'JTKD', 1, 0),
(97, '急先达', 'j', 'JXD', 1, 0),
(98, '晋越快递', 'j', 'JYKD', 1, 0),
(99, '加运美', 'j', 'JYM', 1, 0),
(100, '景光物流', 'j', 'JGWL', 1, 0),
(101, '佳怡物流', 'j', 'JYWL', 1, 0),
(102, '跨越速运', 'k', 'KYSY', 1, 0),
(103, '跨越物流', 'k', 'KYWL', 1, 0),
(104, '快速递物流', 'k', 'KSDWL', 1, 0),
(105, '龙邦快递', 'l', 'LB', 1, 0),
(106, '立即送', 'l', 'LJSKD', 1, 0),
(107, '联昊通速递', 'l', 'LHT', 1, 0),
(108, '民邦快递', 'm', 'MB', 1, 0),
(109, '民航快递', 'm', 'MHKD', 1, 0),
(110, '美快    ', 'm', 'MK', 1, 0),
(111, '门对门快递', 'm', 'MDM', 1, 0),
(112, '迈隆递运', 'm', 'MRDY', 1, 0),
(113, '明亮物流', 'm', 'MLWL', 1, 0),
(114, '南方', 'n', 'NF', 1, 0),
(115, '能达速递', 'n', 'NEDA', 1, 0),
(116, '平安达腾飞快递', 'p', 'PADTF', 1, 0),
(117, '泛捷快递', 'f', 'PANEX', 1, 0),
(118, '品骏', 'p', 'PJ', 1, 0),
(119, 'PCA Express', 'p', 'PCA', 1, 0),
(120, '全晨快递', 'q', 'QCKD', 1, 0),
(121, '全峰快递', 'q', 'QFKD', 1, 0),
(122, '全日通快递', 'q', 'QRT', 1, 0),
(123, '快客快递', 'k', 'QUICK', 1, 0),
(124, '全信通', 'q', 'QXT', 1, 0),
(125, '如风达', 'r', 'RFD', 1, 0),
(126, '日日顺物流', 'r', 'RRS', 1, 0),
(127, '瑞丰速递', 'r', 'RFEX', 1, 0),
(128, '赛澳递', 's', 'SAD', 1, 0),
(129, '苏宁物流', 's', 'SNWL', 1, 0),
(130, '圣安物流', 's', 'SAWL', 1, 0),
(131, '盛邦物流', 's', 'SBWL', 1, 0),
(132, '上大物流', 's', 'SDWL', 1, 0),
(133, '盛丰物流', 's', 'SFWL', 1, 0),
(134, '盛辉物流', 's', 'SHWL', 1, 0),
(135, '速通物流', 's', 'ST', 1, 0),
(136, '速腾快递', 's', 'STWL', 1, 0),
(137, '速必达物流', 's', 'SUBIDA', 1, 0),
(138, '速递e站', 's', 'SDEZ', 1, 0),
(139, '速呈宅配', 's', 'SCZPDS', 1, 0),
(140, '速尔快递', 's', 'SURE', 1, 0),
(141, '台湾邮政', 't', 'TAIWANYZ', 1, 0),
(142, '唐山申通', 't', 'TSSTO', 1, 0),
(143, '全一快递', 'q', 'UAPEX', 1, 0),
(144, '优联吉运', 'y', 'ULUCKEX', 1, 0),
(145, 'UEQ Express', 'u', 'UEQ', 1, 0),
(146, '万家康  ', 'w', 'WJK', 1, 0),
(147, '万家物流', 'w', 'WJWL', 1, 0),
(148, '万象物流', 'w', 'WXWL', 1, 0),
(149, '武汉同舟行', 'w', 'WHTZX', 1, 0),
(150, '维普恩', 'w', 'WPE', 1, 0),
(151, '微特派', 'w', 'WTP', 1, 0),
(152, '新邦物流', 'x', 'XBWL', 1, 0),
(153, '迅驰物流  ', 'x', 'XCWL', 1, 0),
(154, '信丰物流', 'x', 'XFEX', 1, 0),
(155, '希优特', 'x', 'XYT', 1, 0),
(156, '新杰物流', 'x', 'XJ', 1, 0),
(157, '源安达快递', 'y', 'YADEX', 1, 0),
(158, '远成物流', 'y', 'YCWL', 1, 0),
(159, '远成快运', 'y', 'YCSY', 1, 0),
(160, '义达国际物流', 'y', 'YDH', 1, 0),
(161, '易达通  ', 'y', 'YDT', 1, 0),
(162, '越丰物流', 'y', 'YFEX', 1, 0),
(163, '原飞航物流', 'y', 'YFHEX', 1, 0),
(164, '亚风快递', 'y', 'YFSD', 1, 0),
(165, '运通快递', 'y', 'YTKD', 1, 0),
(166, '亿翔快递', 'y', 'YXKD', 1, 0),
(167, '运东西', 'y', 'YUNDX', 1, 0),
(168, '壹米滴答', 'y', 'YMDD', 1, 0),
(169, '邮政国内标快', 'y', 'YZBK', 1, 0),
(170, '一站通速运', 'y', 'YZTSY', 1, 0),
(171, '驭丰速运', 'y', 'YFSUYUN', 1, 0),
(172, '增益快递', 'z', 'ZENY', 1, 0),
(173, '汇强快递', 'h', 'ZHQKD', 1, 0),
(174, '众通快递', 'z', 'ZTE', 1, 0),
(175, '中铁快运', 'z', 'ZTKY', 1, 0),
(176, '中铁物流', 'z', 'ZTWL', 1, 0),
(177, '中邮物流', 'z', 'ZYWL', 1, 0),
(178, '郑州速捷', 'z', 'SJ', 1, 0),
(179, '中通快运', 'z', 'ZTOKY', 1, 0),
(180, 'AAE全球专递', 'a', 'AAE', 2, 0),
(181, '阿里跨境电商物流', 'a', 'ALKJWL', 2, 0),
(182, 'ACS雅仕快递', 'a', 'ACS', 2, 0),
(183, 'ADP Express Tracking', 'a', 'ADP', 2, 0),
(184, '安圭拉邮政', 'a', 'ANGUILAYOU', 2, 0),
(185, 'APAC', 'a', 'APAC', 2, 0),
(186, 'Aramex', 'a', 'ARAMEX', 2, 0),
(187, '奥地利邮政', 'a', 'AT', 2, 0),
(188, 'Australia Post Tracking', 'a', 'AUSTRALIA', 2, 0),
(189, '比利时邮政', 'b', 'BEL', 2, 0),
(190, 'BHT快递', 'b', 'BHT', 2, 0),
(191, '秘鲁邮政', 'm', 'BILUYOUZHE', 2, 0),
(192, '巴西邮政', 'b', 'BR', 2, 0),
(193, '不丹邮政', 'b', 'BUDANYOUZH', 2, 0),
(194, 'CDEK', 'c', 'CDEK', 2, 0),
(195, '加拿大邮政', 'j', 'CA', 2, 0),
(196, '递必易国际物流', 'd', 'DBYWL', 2, 0),
(197, '大道物流', 'd', 'DDWL', 2, 0),
(198, '德国云快递', 'd', 'DGYKD', 2, 0),
(199, '到了港', 'd', 'DLG', 2, 0),
(200, '到乐国际', 'd', 'DLGJ', 2, 0),
(201, 'DHL德国', 'd', 'DHL_DE', 2, 0),
(202, 'DHL(英文版)', 'd', 'DHL_EN', 2, 0),
(203, 'DHL全球', 'd', 'DHL_GLB', 2, 0),
(204, 'DHL Global Mail', 'd', 'DHLGM', 2, 0),
(205, '丹麦邮政', 'd', 'DK', 2, 0),
(206, 'DPD', 'd', 'DPD', 2, 0),
(207, 'DPEX', 'd', 'DPEX', 2, 0),
(208, 'EMS国际', 'e', 'EMSGJ', 2, 0),
(209, 'E特快', 'e', 'ETK', 2, 0),
(210, '易客满', 'y', 'EKM', 2, 0),
(211, 'EPS (联众国际快运)', 'e', 'EPS', 2, 0),
(212, 'EShipper', 'e', 'ESHIPPER', 2, 0),
(213, '丰程物流', 'f', 'FCWL', 2, 0),
(214, '法翔速运', 'f', 'FX', 2, 0),
(215, 'FQ', 'f', 'FQ', 2, 0),
(216, '国际e邮宝', 'g', 'GJEYB', 2, 0),
(217, '国际邮政包裹', 'g', 'GJYZ', 2, 0),
(218, 'GE2D', 'g', 'GE2D', 2, 0),
(219, '冠泰', 'g', 'GT', 2, 0),
(220, 'GLS', 'g', 'GLS', 2, 0),
(221, '安的列斯群岛邮政', 'a', 'IADLSQDYZ', 2, 0),
(222, '欧洲专线(邮政)', 'o', 'IOZYZ', 2, 0),
(223, '澳大利亚邮政', 'a', 'IADLYYZ', 2, 0),
(224, '阿尔巴尼亚邮政', 'a', 'IAEBNYYZ', 2, 0),
(225, '阿尔及利亚邮政', 'a', 'IAEJLYYZ', 2, 0),
(226, '阿富汗邮政', 'a', 'IAFHYZ', 2, 0),
(227, '安哥拉邮政', 'a', 'IAGLYZ', 2, 0),
(228, '阿根廷邮政', 'a', 'IAGTYZ', 2, 0),
(229, '埃及邮政', 'a', 'IAJYZ', 2, 0),
(230, '阿鲁巴邮政', 'a', 'IALBYZ', 2, 0),
(231, '奥兰群岛邮政', 'a', 'IALQDYZ', 2, 0),
(232, '阿联酋邮政', 'a', 'IALYYZ', 2, 0),
(233, '阿曼邮政', 'a', 'IAMYZ', 2, 0),
(234, '阿塞拜疆邮政', 'a', 'IASBJYZ', 2, 0),
(235, '埃塞俄比亚邮政', 'a', 'IASEBYYZ', 2, 0),
(236, '爱沙尼亚邮政', 'a', 'IASNYYZ', 2, 0),
(237, '阿森松岛邮政', 'a', 'IASSDYZ', 2, 0),
(238, '博茨瓦纳邮政', 'b', 'IBCWNYZ', 2, 0),
(239, '波多黎各邮政', 'b', 'IBDLGYZ', 2, 0),
(240, '冰岛邮政', 'b', 'IBDYZ', 2, 0),
(241, '白俄罗斯邮政', 'b', 'IBELSYZ', 2, 0),
(242, '波黑邮政', 'b', 'IBHYZ', 2, 0),
(243, '保加利亚邮政', 'b', 'IBJLYYZ', 2, 0),
(244, '巴基斯坦邮政', 'b', 'IBJSTYZ', 2, 0),
(245, '黎巴嫩邮政', 'l', 'IBLNYZ', 2, 0),
(246, '便利速递', 'b', 'IBLSD', 2, 0),
(247, '玻利维亚邮政', 'b', 'IBLWYYZ', 2, 0),
(248, '巴林邮政', 'b', 'IBLYZ', 2, 0),
(249, '百慕达邮政', 'b', 'IBMDYZ', 2, 0),
(250, '波兰邮政', 'b', 'IBOLYZ', 2, 0),
(251, '宝通达', 'b', 'IBTD', 2, 0),
(252, '贝邮宝', 'b', 'IBYB', 2, 0),
(253, '出口易', 'c', 'ICKY', 2, 0),
(254, '达方物流', 'd', 'IDFWL', 2, 0),
(255, '德国邮政', 'd', 'IDGYZ', 2, 0),
(256, '爱尔兰邮政', 'a', 'IE', 2, 0),
(257, '厄瓜多尔邮政', 'e', 'IEGDEYZ', 2, 0),
(258, '俄罗斯邮政', 'e', 'IELSYZ', 2, 0),
(259, '厄立特里亚邮政', 'e', 'IELTLYYZ', 2, 0),
(260, '飞特物流', 'f', 'IFTWL', 2, 0),
(261, '瓜德罗普岛EMS', 'g', 'IGDLPDEMS', 2, 0),
(262, '瓜德罗普岛邮政', 'g', 'IGDLPDYZ', 2, 0),
(263, '俄速递', 'e', 'IGJESD', 2, 0),
(264, '哥伦比亚邮政', 'g', 'IGLBYYZ', 2, 0),
(265, '格陵兰邮政', 'g', 'IGLLYZ', 2, 0),
(266, '哥斯达黎加邮政', 'g', 'IGSDLJYZ', 2, 0),
(267, '韩国邮政', 'h', 'IHGYZ', 2, 0),
(268, '华翰物流', 'h', 'IHHWL', 2, 0),
(269, '互联易', 'h', 'IHLY', 2, 0),
(270, '哈萨克斯坦邮政', 'h', 'IHSKSTYZ', 2, 0),
(271, '黑山邮政', 'h', 'IHSYZ', 2, 0),
(272, '津巴布韦邮政', 'j', 'IJBBWYZ', 2, 0),
(273, '吉尔吉斯斯坦邮政', 'j', 'IJEJSSTYZ', 2, 0),
(274, '捷克邮政', 'j', 'IJKYZ', 2, 0),
(275, '加纳邮政', 'j', 'IJNYZ', 2, 0),
(276, '柬埔寨邮政', 'j', 'IJPZYZ', 2, 0),
(277, '克罗地亚邮政', 'k', 'IKNDYYZ', 2, 0),
(278, '肯尼亚邮政', 'k', 'IKNYYZ', 2, 0),
(279, '科特迪瓦EMS', 'k', 'IKTDWEMS', 2, 0),
(280, '科特迪瓦邮政', 'k', 'IKTDWYZ', 2, 0),
(281, '卡塔尔邮政', 'k', 'IKTEYZ', 2, 0),
(282, '利比亚邮政', 'l', 'ILBYYZ', 2, 0),
(283, '林克快递', 'l', 'ILKKD', 2, 0),
(284, '罗马尼亚邮政', 'l', 'ILMNYYZ', 2, 0),
(285, '卢森堡邮政', 'l', 'ILSBYZ', 2, 0),
(286, '拉脱维亚邮政', 'l', 'ILTWYYZ', 2, 0),
(287, '立陶宛邮政', 'l', 'ILTWYZ', 2, 0),
(288, '列支敦士登邮政', 'l', 'ILZDSDYZ', 2, 0),
(289, '马尔代夫邮政', 'm', 'IMEDFYZ', 2, 0),
(290, '摩尔多瓦邮政', 'm', 'IMEDWYZ', 2, 0),
(291, '马耳他邮政', 'm', 'IMETYZ', 2, 0),
(292, '孟加拉国EMS', 'm', 'IMJLGEMS', 2, 0),
(293, '摩洛哥邮政', 'm', 'IMLGYZ', 2, 0),
(294, '毛里求斯邮政', 'm', 'IMLQSYZ', 2, 0),
(295, '马来西亚EMS', 'm', 'IMLXYEMS', 2, 0),
(296, '马来西亚邮政', 'm', 'IMLXYYZ', 2, 0),
(297, '马其顿邮政', 'm', 'IMQDYZ', 2, 0),
(298, '马提尼克EMS', 'm', 'IMTNKEMS', 2, 0),
(299, '马提尼克邮政', 'm', 'IMTNKYZ', 2, 0),
(300, '墨西哥邮政', 'm', 'IMXGYZ', 2, 0),
(301, '南非邮政', 'n', 'INFYZ', 2, 0),
(302, '尼日利亚邮政', 'n', 'INRLYYZ', 2, 0),
(303, '挪威邮政', 'n', 'INWYZ', 2, 0),
(304, '葡萄牙邮政', 'p', 'IPTYYZ', 2, 0),
(305, '全球快递', 'q', 'IQQKD', 2, 0),
(306, '全通物流', 'q', 'IQTWL', 2, 0),
(307, '苏丹邮政', 's', 'ISDYZ', 2, 0),
(308, '萨尔瓦多邮政', 's', 'ISEWDYZ', 2, 0),
(309, '塞尔维亚邮政', 's', 'ISEWYYZ', 2, 0),
(310, '斯洛伐克邮政', 's', 'ISLFKYZ', 2, 0),
(311, '斯洛文尼亚邮政', 's', 'ISLWNYYZ', 2, 0),
(312, '塞内加尔邮政', 's', 'ISNJEYZ', 2, 0),
(313, '塞浦路斯邮政', 's', 'ISPLSYZ', 2, 0),
(314, '沙特阿拉伯邮政', 's', 'ISTALBYZ', 2, 0),
(315, '土耳其邮政', 't', 'ITEQYZ', 2, 0),
(316, '泰国邮政', 't', 'ITGYZ', 2, 0),
(317, '特立尼达和多巴哥EMS', 't', 'ITLNDHDBGE', 2, 0),
(318, '突尼斯邮政', 't', 'ITNSYZ', 2, 0),
(319, '坦桑尼亚邮政', 't', 'ITSNYYZ', 2, 0),
(320, '危地马拉邮政', 'w', 'IWDMLYZ', 2, 0),
(321, '乌干达邮政', 'w', 'IWGDYZ', 2, 0),
(322, '乌克兰EMS', 'w', 'IWKLEMS', 2, 0),
(323, '乌克兰邮政', 'w', 'IWKLYZ', 2, 0),
(324, '乌拉圭邮政', 'w', 'IWLGYZ', 2, 0),
(325, '文莱邮政', 'w', 'IWLYZ', 2, 0),
(326, '乌兹别克斯坦EMS', 'w', 'IWZBKSTEMS', 2, 0),
(327, '乌兹别克斯坦邮政', 'w', 'IWZBKSTYZ', 2, 0),
(328, '西班牙邮政', 'x', 'IXBYYZ', 2, 0),
(329, '小飞龙物流', 'x', 'IXFLWL', 2, 0),
(330, '新喀里多尼亚邮政', 'x', 'IXGLDNYYZ', 2, 0),
(331, '新加坡EMS', 'x', 'IXJPEMS', 2, 0),
(332, '新加坡邮政', 'x', 'IXJPYZ', 2, 0),
(333, '叙利亚邮政', 'x', 'IXLYYZ', 2, 0),
(334, '希腊邮政', 'x', 'IXLYZ', 2, 0),
(335, '夏浦世纪', 'x', 'IXPSJ', 2, 0),
(336, '夏浦物流', 'x', 'IXPWL', 2, 0),
(337, '新西兰邮政', 'x', 'IXXLYZ', 2, 0),
(338, '匈牙利邮政', 'x', 'IXYLYZ', 2, 0),
(339, '意大利邮政', 'y', 'IYDLYZ', 2, 0),
(340, '印度尼西亚邮政', 'y', 'IYDNXYYZ', 2, 0),
(341, '印度邮政', 'y', 'IYDYZ', 2, 0),
(342, '英国邮政', 'y', 'IYGYZ', 2, 0),
(343, '伊朗邮政', 'y', 'IYLYZ', 2, 0),
(344, '亚美尼亚邮政', 'y', 'IYMNYYZ', 2, 0),
(345, '也门邮政', 'y', 'IYMYZ', 2, 0),
(346, '越南邮政', 'y', 'IYNYZ', 2, 0),
(347, '以色列邮政', 'y', 'IYSLYZ', 2, 0),
(348, '易通关', 'y', 'IYTG', 2, 0),
(349, '燕文物流', 'y', 'IYWWL', 2, 0),
(350, '直布罗陀邮政', 'z', 'IZBLTYZ', 2, 0),
(351, '智利邮政', 'z', 'IZLYZ', 2, 0),
(352, '日本邮政', 'r', 'JP', 2, 0),
(353, '今枫国际', 'j', 'JFGJ', 2, 0),
(354, '极光转运', 'j', 'JGZY', 2, 0),
(355, '吉祥邮转运', 'j', 'JXYKD', 2, 0),
(356, '嘉里国际', 'j', 'JLDT', 2, 0),
(357, '绝配国际速递', 'j', 'JPKD', 2, 0),
(358, '佳惠尔', 'j', 'SYJHE', 2, 0),
(359, '联运通', 'l', 'LYT', 2, 0),
(360, '联合快递', 'l', 'LHKDS', 2, 0),
(361, '林道国际', 'l', 'SHLDHY', 2, 0),
(362, '荷兰邮政', 'h', 'NL', 2, 0),
(363, '新顺丰', 'x', 'NSF', 2, 0),
(364, 'ONTRAC', 'o', 'ONTRAC', 2, 0),
(365, 'OCS', 'o', 'OCS', 2, 0),
(366, '全球邮政', 'q', 'QQYZ', 2, 0),
(367, 'POSTEIBE', 'p', 'POSTEIBE', 2, 0),
(368, '啪啪供应链', 'p', 'PAPA', 2, 0),
(369, '秦远海运', 'q', 'QYHY', 2, 0),
(370, '启辰国际', 'q', 'VENUCIA', 2, 0),
(371, '瑞典邮政', 'r', 'RDSE', 2, 0),
(372, 'SKYPOST', 's', 'SKYPOST', 2, 0),
(373, '瑞士邮政', 'r', 'SWCH', 2, 0),
(374, '首达速运', 's', 'SDSY', 2, 0),
(375, '穗空物流', 's', 'SK', 2, 0),
(376, '首通快运', 's', 'STONG', 2, 0),
(377, '申通快递国际单', 's', 'STO_INTL', 2, 0),
(378, '上海久易国际', 's', 'JYSD', 2, 0),
(379, '泰国138', 't', 'TAILAND138', 2, 0),
(380, 'USPS美国邮政', 'u', 'USPS', 2, 0),
(381, '万国邮政', 'w', 'UPU', 2, 0),
(382, '星空国际', 'x', 'XKGJ', 2, 0),
(383, '迅达国际', 'x', 'XD', 2, 0),
(384, '香港邮政', 'x', 'XGYZ', 2, 0),
(385, '喜来快递', 'x', 'XLKD', 2, 0),
(386, '鑫世锐达', 'x', 'XSRD', 2, 0),
(387, '新元国际', 'x', 'XYGJ', 2, 0),
(388, 'ADLER雄鹰国际速递', 'a', 'XYGJSD', 2, 0),
(389, '日本大和运输(Yamato)', 'r', 'YAMA', 2, 0),
(390, 'YODEL', 'y', 'YODEL', 2, 0),
(391, '一号线', 'y', 'YHXGJSD', 2, 0),
(392, '约旦邮政', 'y', 'YUEDANYOUZ', 2, 0),
(393, '玥玛速运', 'y', 'YMSY', 2, 0),
(394, '鹰运', 'y', 'YYSD', 2, 0),
(395, '易境达', 'y', 'YJD', 2, 0),
(396, '洋包裹', 'y', 'YBG', 2, 0),
(397, 'AOL（澳通）', 'a', 'AOL', 3, 0),
(398, 'BCWELT   ', 'b', 'BCWELT', 3, 0),
(399, '笨鸟国际', 'b', 'BN', 3, 0),
(400, 'COE快递 ', 'c', 'COE', 3, 0),
(401, '优邦国际速运', 'y', 'UBONEX', 3, 0),
(402, 'UEX   ', 'u', 'UEX', 3, 0),
(403, '韵达国际', 'y', 'YDGJ', 3, 0),
(404, '爱购转运', 'a', 'ZY_AG', 3, 0),
(405, '爱欧洲', 'a', 'ZY_AOZ', 3, 0),
(406, '澳世速递', 'a', 'ZY_AUSE', 3, 0),
(407, 'AXO', 'a', 'ZY_AXO', 3, 0),
(408, '澳转运', 'a', 'ZY_AZY', 3, 0),
(409, '八达网', 'b', 'ZY_BDA', 3, 0),
(410, '蜜蜂速递', 'm', 'ZY_BEE', 3, 0),
(411, '贝海速递', 'b', 'ZY_BH', 3, 0),
(412, '百利快递', 'b', 'ZY_BL', 3, 0),
(413, '斑马物流', 'b', 'ZY_BM', 3, 0),
(414, '败欧洲', 'b', 'ZY_BOZ', 3, 0),
(415, '百通物流', 'b', 'ZY_BT', 3, 0),
(416, '贝易购', 'b', 'ZY_BYECO', 3, 0),
(417, '策马转运', 'c', 'ZY_CM', 3, 0),
(418, '赤兔马转运', 'c', 'ZY_CTM', 3, 0),
(419, 'CUL中美速递', 'c', 'ZY_CUL', 3, 0),
(420, '德国海淘之家', 'd', 'ZY_DGHT', 3, 0),
(421, '德运网', 'd', 'ZY_DYW', 3, 0),
(422, 'EFS POST', 'e', 'ZY_EFS', 3, 0),
(423, '宜送转运', 'y', 'ZY_ESONG', 3, 0),
(424, 'ETD', 'e', 'ZY_ETD', 3, 0),
(425, '飞碟快递', 'f', 'ZY_FD', 3, 0),
(426, '飞鸽快递', 'f', 'ZY_FG', 3, 0),
(427, '风雷速递', 'f', 'ZY_FLSD', 3, 0),
(428, '风行快递', 'f', 'ZY_FX', 3, 0),
(429, '风行速递', 'f', 'ZY_FXSD', 3, 0),
(430, '飞洋快递', 'f', 'ZY_FY', 3, 0),
(431, '皓晨快递', 'h', 'ZY_HC', 3, 0),
(432, '皓晨优递', 'h', 'ZY_HCYD', 3, 0),
(433, '海带宝', 'h', 'ZY_HDB', 3, 0),
(434, '汇丰美中速递', 'h', 'ZY_HFMZ', 3, 0),
(435, '豪杰速递', 'h', 'ZY_HJSD', 3, 0),
(436, '360hitao转运', 'h', 'ZY_HTAO', 3, 0),
(437, '海淘村', 'h', 'ZY_HTCUN', 3, 0),
(438, '365海淘客', 'h', 'ZY_HTKE', 3, 0),
(439, '华通快运', 'h', 'ZY_HTONG', 3, 0),
(440, '海星桥快递', 'h', 'ZY_HXKD', 3, 0),
(441, '华兴速运', 'h', 'ZY_HXSY', 3, 0),
(442, '海悦速递', 'h', 'ZY_HYSD', 3, 0),
(443, 'LogisticsY', 'l', 'ZY_IHERB', 3, 0),
(444, '君安快递', 'j', 'ZY_JA', 3, 0),
(445, '时代转运', 's', 'ZY_JD', 3, 0),
(446, '骏达快递', 'j', 'ZY_JDKD', 3, 0),
(447, '骏达转运', 'j', 'ZY_JDZY', 3, 0),
(448, '久禾快递', 'j', 'ZY_JH', 3, 0),
(449, '金海淘', 'j', 'ZY_JHT', 3, 0),
(450, '联邦转运FedRoad', 'l', 'ZY_LBZY', 3, 0),
(451, '领跑者快递', 'l', 'ZY_LPZ', 3, 0),
(452, '龙象快递', 'l', 'ZY_LX', 3, 0),
(453, '量子物流', 'l', 'ZY_LZWL', 3, 0),
(454, '明邦转运', 'm', 'ZY_MBZY', 3, 0),
(455, '美国转运', 'm', 'ZY_MGZY', 3, 0),
(456, '美嘉快递', 'm', 'ZY_MJ', 3, 0),
(457, '美速通', 'm', 'ZY_MST', 3, 0),
(458, '美西转运', 'm', 'ZY_MXZY', 3, 0),
(459, '168 美中快递', 'm', 'ZY_MZ', 3, 0),
(460, '欧e捷', 'o', 'ZY_OEJ', 3, 0),
(461, '欧洲疯', 'o', 'ZY_OZF', 3, 0),
(462, '欧洲GO', 'o', 'ZY_OZGO', 3, 0),
(463, '全美通', 'q', 'ZY_QMT', 3, 0),
(464, 'QQ-EX', 'q', 'ZY_QQEX', 3, 0),
(465, '润东国际快线', 'r', 'ZY_RDGJ', 3, 0),
(466, '瑞天快递', 'r', 'ZY_RT', 3, 0),
(467, '瑞天速递', 'r', 'ZY_RTSD', 3, 0),
(468, 'SCS国际物流', 's', 'ZY_SCS', 3, 0),
(469, '速达快递', 's', 'ZY_SDKD', 3, 0),
(470, '四方转运', 's', 'ZY_SFZY', 3, 0),
(471, 'SOHO苏豪国际', 's', 'ZY_SOHO', 3, 0),
(472, 'Sonic-Ex速递', 's', 'ZY_SONIC', 3, 0),
(473, '上腾快递', 's', 'ZY_ST', 3, 0),
(474, '通诚美中快递', 't', 'ZY_TCM', 3, 0),
(475, '天际快递', 't', 'ZY_TJ', 3, 0),
(476, '天马转运', 't', 'ZY_TM', 3, 0),
(477, '滕牛快递', 't', 'ZY_TN', 3, 0),
(478, 'TrakPak', 't', 'ZY_TPAK', 3, 0),
(479, '太平洋快递', 't', 'ZY_TPY', 3, 0),
(480, '唐三藏转运', 't', 'ZY_TSZ', 3, 0),
(481, '天天海淘', 't', 'ZY_TTHT', 3, 0),
(482, 'TWC转运世界', 't', 'ZY_TWC', 3, 0),
(483, '同心快递', 't', 'ZY_TX', 3, 0),
(484, '天翼快递', 't', 'ZY_TY', 3, 0),
(485, '同舟快递', 't', 'ZY_TZH', 3, 0),
(486, 'UCS合众快递', 'u', 'ZY_UCS', 3, 0),
(487, '文达国际DCS', 'w', 'ZY_WDCS', 3, 0),
(488, '星辰快递', 'x', 'ZY_XC', 3, 0),
(489, '迅达快递', 'x', 'ZY_XDKD', 3, 0),
(490, '信达速运', 'x', 'ZY_XDSY', 3, 0),
(491, '先锋快递', 'x', 'ZY_XF', 3, 0),
(492, '新干线快递', 'x', 'ZY_XGX', 3, 0),
(493, '西邮寄', 'x', 'ZY_XIYJ', 3, 0),
(494, '信捷转运', 'x', 'ZY_XJ', 3, 0),
(495, '优购快递', 'y', 'ZY_YGKD', 3, 0),
(496, '友家速递(UCS)', 'y', 'ZY_YJSD', 3, 0),
(497, '云畔网', 'y', 'ZY_YPW', 3, 0),
(498, '云骑快递', 'y', 'ZY_YQ', 3, 0),
(499, '一柒物流', 'y', 'ZY_YQWL', 3, 0),
(500, '优晟速递', 'y', 'ZY_YSSD', 3, 0),
(501, '易送网', 'y', 'ZY_YSW', 3, 0),
(502, '运淘美国', 'y', 'ZY_YTUSA', 3, 0),
(503, '至诚速递', 'z', 'ZY_ZCSD', 3, 0),
(504, '增速海淘', 'z', 'ZYZOOM', 3, 0),
(505, '中驰物流', 'z', 'ZH', 3, 0),
(506, '中欧快运', 'z', 'ZO', 3, 0),
(507, '准实快运', 'z', 'ZSKY', 3, 0),
(508, '中外速运', 'z', 'ZWSY', 3, 0),
(509, '郑州建华', 'z', 'ZZJH', 3, 0),
(510, '买家自提', 'm', 'ZT', 0, 0),
(511, '同城配送', 't', 'TC', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cs_discount`
--

CREATE TABLE `cs_discount` (
  `discount_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '折扣名称',
  `type` tinyint(1) NOT NULL COMMENT '0=打折 1=减价 2=固定价格 3=送优惠劵',
  `begin_time` int(11) NOT NULL COMMENT '开始日期',
  `end_time` int(11) NOT NULL COMMENT '结束日期',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品折扣';

--
-- 插入之前先把表清空（truncate） `cs_discount`
--

TRUNCATE TABLE `cs_discount`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_discount_goods`
--

CREATE TABLE `cs_discount_goods` (
  `discount_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应discount表',
  `goods_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods表',
  `discount` decimal(10,2) NOT NULL COMMENT '折扣额',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='折扣商品';

--
-- 插入之前先把表清空（truncate） `cs_discount_goods`
--

TRUNCATE TABLE `cs_discount_goods`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_friend_link`
--

CREATE TABLE `cs_friend_link` (
  `friend_link_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '链接名称',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '链接地址',
  `logo` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'logo',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '_self _blank',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='友情链接';

--
-- 插入之前先把表清空（truncate） `cs_friend_link`
--

TRUNCATE TABLE `cs_friend_link`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods`
--

CREATE TABLE `cs_goods` (
  `goods_id` int(11) UNSIGNED NOT NULL,
  `goods_category_id` smallint(5) UNSIGNED NOT NULL COMMENT '对应goods_category表',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称',
  `short_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '短名称',
  `product_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '促销语',
  `goods_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品货号',
  `goods_spu` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品SPU',
  `goods_sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品SKU',
  `bar_code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品条码',
  `brand_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应brand表',
  `store_qty` int(11) NOT NULL DEFAULT '0' COMMENT '库存数量',
  `comment_sum` int(11) NOT NULL DEFAULT '0' COMMENT '评价数量',
  `sales_sum` int(11) NOT NULL DEFAULT '0' COMMENT '销售数量',
  `measure` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品计量(重量、体积)',
  `measure_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=重量 1=计件 2=体积',
  `is_postage` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=收费 1=包邮',
  `market_price` decimal(10,2) NOT NULL COMMENT '市场价',
  `shop_price` decimal(10,2) NOT NULL COMMENT '本店价',
  `integral_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=按百分比 1=按固定值',
  `give_integral` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送积分',
  `is_integral` int(11) NOT NULL DEFAULT '0' COMMENT '积分可抵扣额',
  `least_sum` smallint(5) NOT NULL DEFAULT '0' COMMENT '最少起订',
  `purchase_sum` smallint(5) NOT NULL DEFAULT '0' COMMENT '限购数量 0=不限',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `attachment` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '附件',
  `video` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '短视频',
  `unit` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '计量单位',
  `is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=不推 1=推荐',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=不新 1=新品',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=普通 1=热卖',
  `goods_type_id` smallint(5) UNSIGNED NOT NULL COMMENT '对应goods_type表',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=下架 1=上架',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=正常 1=回收 2=软删除',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品表';

--
-- 插入之前先把表清空（truncate） `cs_goods`
--

TRUNCATE TABLE `cs_goods`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_attr`
--

CREATE TABLE `cs_goods_attr` (
  `goods_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods表',
  `goods_attribute_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods_attribute表',
  `parent_id` int(11) UNSIGNED NOT NULL COMMENT '父id',
  `is_important` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=普通 1=核心属性',
  `attr_value` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '属性值',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品属性列表';

--
-- 插入之前先把表清空（truncate） `cs_goods_attr`
--

TRUNCATE TABLE `cs_goods_attr`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_attribute`
--

CREATE TABLE `cs_goods_attribute` (
  `goods_attribute_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `attr_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '属性名称',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '属性描述',
  `icon` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `goods_type_id` smallint(5) UNSIGNED NOT NULL COMMENT '对应goods_type表',
  `attr_index` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=不检索 1=关键字 2=范围',
  `attr_input_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=手工填写 1=单选 2=多选',
  `attr_values` text COLLATE utf8mb4_unicode_ci COMMENT '可选值列表',
  `is_important` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=普通 1=核心属性',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品属性';

--
-- 插入之前先把表清空（truncate） `cs_goods_attribute`
--

TRUNCATE TABLE `cs_goods_attribute`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_attr_config`
--

CREATE TABLE `cs_goods_attr_config` (
  `goods_attr_config_id` int(11) UNSIGNED NOT NULL,
  `goods_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods表',
  `config_data` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置数据'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品属性配置';

--
-- 插入之前先把表清空（truncate） `cs_goods_attr_config`
--

TRUNCATE TABLE `cs_goods_attr_config`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_category`
--

CREATE TABLE `cs_goods_category` (
  `goods_category_id` smallint(5) UNSIGNED NOT NULL,
  `parent_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `name_phonetic` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称首拼',
  `alias` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `alias_phonetic` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名首拼',
  `category_pic` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片',
  `category_ico` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `category_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型(自定义)',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `is_navi` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航 0=否 1=是',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类';

--
-- 插入之前先把表清空（truncate） `cs_goods_category`
--

TRUNCATE TABLE `cs_goods_category`;
--
-- 转存表中的数据 `cs_goods_category`
--

INSERT INTO `cs_goods_category` (`goods_category_id`, `parent_id`, `name`, `name_phonetic`, `alias`, `alias_phonetic`, `category_pic`, `category_ico`, `keywords`, `description`, `category_type`, `sort`, `is_navi`, `status`) VALUES
(1, 0, '手机 、 数码 、 通信', 's', '数码产品', 's', '', '', '', '', 0, 50, 0, 1),
(2, 0, '家用电器', 'j', '家用电器', 'j', '', '', '', '', 0, 50, 0, 1),
(3, 0, '电脑、办公', 'd', '电脑', 'd', '', '', '', '', 0, 50, 0, 1),
(4, 0, '家居、家具、家装、厨具', 'j', '家具', 'j', '', '', '', '', 0, 50, 0, 1),
(5, 0, '男装、女装、童装、内衣', 'n', '服装', 'f', '', '', '', '', 0, 50, 0, 1),
(6, 0, '个人化妆', 'g', '个人化妆', 'g', '', '', '', '', 0, 50, 0, 1),
(7, 0, '鞋、箱包、珠宝、手表', 'x', '箱包', 'x', '', '', '', '', 0, 50, 0, 1),
(8, 0, '运动户外', 'y', '运动户外', 'y', '', '', '', '', 0, 50, 0, 1),
(9, 0, '汽车用品', 'q', '汽车用品', 'q', '', '', '', '', 0, 50, 0, 1),
(10, 0, '母婴用品、儿童玩具', 'm', '母婴用品', 'm', '', '', '', '', 0, 50, 0, 1),
(11, 0, '图书、音像、电子书', 't', '书籍影像', 't', '', '', '', '', 0, 50, 0, 1),
(12, 1, '手机配件', 's', '手机配件', 's', '', '', '', '', 0, 50, 0, 1),
(13, 1, '摄影摄像', 's', '摄影摄像', 's', '', '', '', '', 0, 50, 0, 1),
(14, 1, '运营商', 'y', '运营商', 'y', '', '', '', '', 0, 50, 0, 1),
(15, 1, '数码配件', 's', '数码配件', 's', '', '', '', '', 0, 50, 0, 1),
(16, 1, '娱乐影视', 'y', '娱乐影视', 'y', '', '', '', '', 0, 50, 0, 1),
(17, 1, '电子教育', 'd', '电子教育', 'd', '', '', '', '', 0, 50, 0, 1),
(18, 1, '手机通讯', 's', '手机通讯', 's', '', '', '', '', 0, 50, 0, 1),
(19, 2, '生活电器', 's', '生活电器', 's', '', '', '', '', 0, 50, 0, 1),
(20, 2, '大家电', 'd', '大家电', 'd', '', '', '', '', 0, 50, 0, 1),
(21, 2, '厨房电器', 'c', '厨房电器', 'c', '', '', '', '', 0, 50, 0, 1),
(22, 2, '个护健康', 'g', '个护健康', 'g', '', '', '', '', 0, 50, 0, 1),
(23, 2, '五金交电', 'w', '五金交电', 'w', '', '', '', '', 0, 50, 0, 1),
(24, 3, '网络产品', 'w', '网络产品', 'w', '', '', '', '', 0, 50, 0, 1),
(25, 3, '办公设备', 'b', '办公设备', 'b', '', '', '', '', 0, 50, 0, 1),
(26, 3, '文具耗材', 'w', '文具耗材', 'w', '', '', '', '', 0, 50, 0, 1),
(27, 3, '电脑整机', 'd', '电脑整机', 'd', '', '', '', '', 0, 50, 0, 1),
(28, 3, '电脑配件', 'd', '电脑配件', 'd', '', '', '', '', 0, 50, 0, 1),
(29, 3, '外设产品', 'w', '外设产品', 'w', '', '', '', '', 0, 50, 0, 1),
(30, 3, '游戏设备', 'y', '游戏设备', 'y', '', '', '', '', 0, 50, 0, 1),
(31, 4, '生活日用', 's', '生活日用', 's', '', '', '', '', 0, 50, 0, 1),
(32, 4, '家装软饰', 'j', '家装软饰', 'j', '', '', '', '', 0, 50, 0, 1),
(33, 4, '宠物生活', 'c', '宠物生活', 'c', '', '', '', '', 0, 50, 0, 1),
(34, 4, '厨具', 'c', '厨具', 'c', '', '', '', '', 0, 50, 0, 1),
(35, 4, '家装建材', 'j', '家装建材', 'j', '', '', '', '', 0, 50, 0, 1),
(36, 4, '家纺', 'j', '家纺', 'j', '', '', '', '', 0, 50, 0, 1),
(37, 4, '家具', 'j', '家具', 'j', '', '', '', '', 0, 50, 0, 1),
(38, 4, '灯具', 'd', '灯具', 'd', '', '', '', '', 0, 50, 0, 1),
(39, 5, '女装', 'n', '女装', 'n', '', '', '', '', 0, 50, 0, 1),
(40, 5, '男装', 'n', '男装', 'n', '', '', '', '', 0, 50, 0, 1),
(41, 5, '内衣', 'n', '内衣', 'n', '', '', '', '', 0, 50, 0, 1),
(42, 6, '身体护肤', 's', '身体护肤', 's', '', '', '', '', 0, 50, 0, 1),
(43, 6, '口腔护理', 'k', '口腔护理', 'k', '', '', '', '', 0, 50, 0, 1),
(44, 6, '女性护理', 'n', '女性护理', 'n', '', '', '', '', 0, 50, 0, 1),
(45, 6, '香水彩妆', 'x', '香水彩妆', 'x', '', '', '', '', 0, 50, 0, 1),
(46, 6, '清洁用品', 'q', '清洁用品', 'q', '', '', '', '', 0, 50, 0, 1),
(47, 6, '面部护肤', 'm', '面部护肤', 'm', '', '', '', '', 0, 50, 0, 1),
(48, 6, '洗发护发', 'x', '洗发护发', 'x', '', '', '', '', 0, 50, 0, 1),
(49, 7, '精品男包', 'j', '精品男包', 'j', '', '', '', '', 0, 50, 0, 1),
(50, 7, '功能箱包', 'g', '功能箱包', 'g', '', '', '', '', 0, 50, 0, 1),
(51, 7, '珠宝', 'z', '珠宝', 'z', '', '', '', '', 0, 50, 0, 1),
(52, 7, '钟表', 'z', '钟表', 'z', '', '', '', '', 0, 50, 0, 1),
(53, 7, '时尚女鞋', 's', '时尚女鞋', 's', '', '', '', '', 0, 50, 0, 1),
(54, 7, '流行男鞋', 'l', '流行男鞋', 'l', '', '', '', '', 0, 50, 0, 1),
(55, 7, '潮流女包', 'c', '潮流女包', 'c', '', '', '', '', 0, 50, 0, 1),
(56, 8, '体育用品', 't', '体育用品', 't', '', '', '', '', 0, 50, 0, 1),
(57, 8, '户外鞋服', 'h', '户外鞋服', 'h', '', '', '', '', 0, 50, 0, 1),
(58, 8, '户外装备', 'h', '户外装备', 'h', '', '', '', '', 0, 50, 0, 1),
(59, 8, '垂钓用品', 'c', '垂钓用品', 'c', '', '', '', '', 0, 50, 0, 1),
(60, 8, '运动鞋包', 'y', '运动鞋包', 'y', '', '', '', '', 0, 50, 0, 1),
(61, 8, '游泳用品', 'y', '游泳用品', 'y', '', '', '', '', 0, 50, 0, 1),
(62, 8, '运动服饰', 'y', '运动服饰', 'y', '', '', '', '', 0, 50, 0, 1),
(63, 8, '健身训练', 'j', '健身训练', 'j', '', '', '', '', 0, 50, 0, 1),
(64, 8, '骑行运动', 'q', '骑行运动', 'q', '', '', '', '', 0, 50, 0, 1),
(65, 9, '车载电器', 'c', '车载电器', 'c', '', '', '', '', 0, 50, 0, 1),
(66, 9, '美容清洗', 'm', '美容清洗', 'm', '', '', '', '', 0, 50, 0, 1),
(67, 9, '汽车装饰', 'q', '汽车装饰', 'q', '', '', '', '', 0, 50, 0, 1),
(68, 9, '安全自驾', 'a', '安全自驾', 'a', '', '', '', '', 0, 50, 0, 1),
(69, 9, '线下服务', 'x', '线下服务', 'x', '', '', '', '', 0, 50, 0, 1),
(70, 9, '汽车车型', 'q', '汽车车型', 'q', '', '', '', '', 0, 50, 0, 1),
(71, 9, '汽车品牌', 'q', '汽车品牌', 'q', '', '', '', '', 0, 50, 0, 1),
(72, 9, '维修保养', 'w', '维修保养', 'w', '', '', '', '', 0, 50, 0, 1),
(73, 10, '洗护用品', 'x', '洗护用品', 'x', '', '', '', '', 0, 50, 0, 1),
(74, 10, '喂养用品', 'w', '喂养用品', 'w', '', '', '', '', 0, 50, 0, 1),
(75, 10, '童车童床', 't', '童车童床', 't', '', '', '', '', 0, 50, 0, 1),
(76, 10, '安全座椅', 'a', '安全座椅', 'a', '', '', '', '', 0, 50, 0, 1),
(77, 10, '寝居服饰', 'q', '寝居服饰', 'q', '', '', '', '', 0, 50, 0, 1),
(78, 10, '奶粉', 'n', '奶粉', 'n', '', '', '', '', 0, 50, 0, 1),
(79, 10, '妈妈专区', 'm', '妈妈专区', 'm', '', '', '', '', 0, 50, 0, 1),
(80, 10, '营养辅食', 'y', '营养辅食', 'y', '', '', '', '', 0, 50, 0, 1),
(81, 10, '童装童鞋', 't', '童装童鞋', 't', '', '', '', '', 0, 50, 0, 1),
(82, 10, '尿裤湿巾', 'n', '尿裤湿巾', 'n', '', '', '', '', 0, 50, 0, 1),
(83, 10, '玩具乐器', 'w', '玩具乐器', 'w', '', '', '', '', 0, 50, 0, 1),
(84, 11, '音像', 'y', '音像', 'y', '', '', '', '', 0, 50, 0, 1),
(85, 11, '刊/原版', 'k', '刊/原版', 'k', '', '', '', '', 0, 50, 0, 1),
(86, 11, '少儿', 's', '少儿', 's', '', '', '', '', 0, 50, 0, 1),
(87, 11, '电子书', 'd', '电子书', 'd', '', '', '', '', 0, 50, 0, 1),
(88, 11, '教育', 'j', '教育', 'j', '', '', '', '', 0, 50, 0, 1),
(89, 11, '数字音乐', 's', '数字音乐', 's', '', '', '', '', 0, 50, 0, 1),
(90, 11, '文艺', 'w', '文艺', 'w', '', '', '', '', 0, 50, 0, 1),
(91, 11, '经管励志', 'j', '经管励志', 'j', '', '', '', '', 0, 50, 0, 1),
(92, 11, '人文社科', 'r', '人文社科', 'r', '', '', '', '', 0, 50, 0, 1),
(93, 11, '生活', 's', '生活', 's', '', '', '', '', 0, 50, 0, 1),
(94, 11, '科技', 'k', '科技', 'k', '', '', '', '', 0, 50, 0, 1),
(95, 46, '纸品湿巾', 'z', '纸品湿巾', 'z', '', '', '', '', 0, 50, 0, 1),
(96, 46, '衣物清洁', 'y', '衣物清洁', 'y', '', '', '', '', 0, 50, 0, 1),
(97, 46, '家庭清洁', 'j', '家庭清洁', 'j', '', '', '', '', 0, 50, 0, 1),
(98, 46, '一次性用品', 'y', '一次性用品', 'y', '', '', '', '', 0, 50, 0, 1),
(99, 46, '驱虫用品', 'q', '驱虫用品', 'q', '', '', '', '', 0, 50, 0, 1),
(100, 12, '电池 电源 充电器', 'd', '电池 电源 充电器', 'd', '', '', '', '', 0, 50, 0, 1),
(101, 12, '数据线,耳机', 's', '数据线,耳机', 's', '', '', '', '', 0, 50, 0, 1),
(102, 12, '贴膜,保护套', 't', '贴膜,保护套', 't', '', '', '', '', 0, 50, 0, 1),
(103, 13, '数码相机', 's', '数码相机', 's', '', '', '', '', 0, 50, 0, 1),
(104, 13, '单反相机', 'd', '单反相机', 'd', '', '', '', '', 0, 50, 0, 1),
(105, 13, '摄像机', 's', '摄像机', 's', '', '', '', '', 0, 50, 0, 1),
(106, 13, '镜头', 'j', '镜头', 'j', '', '', '', '', 0, 50, 0, 1),
(107, 13, '数码相框', 's', '数码相框', 's', '', '', '', '', 0, 50, 0, 1),
(108, 14, '选号码', 'x', '选号码', 'x', '', '', '', '', 0, 50, 0, 1),
(109, 14, '办套餐', 'b', '办套餐', 'b', '', '', '', '', 0, 50, 0, 1),
(110, 14, '合约机', 'h', '合约机', 'h', '', '', '', '', 0, 50, 0, 1),
(111, 14, '中国移动', 'z', '中国移动', 'z', '', '', '', '', 0, 50, 0, 1),
(112, 15, '充值卡', 'c', '充值卡', 'c', '', '', '', '', 0, 50, 0, 1),
(113, 15, '读卡器', 'd', '读卡器', 'd', '', '', '', '', 0, 50, 0, 1),
(114, 15, '支架', 'z', '支架', 'z', '', '', '', '', 0, 50, 0, 1),
(115, 15, '滤镜', 'l', '滤镜', 'l', '', '', '', '', 0, 50, 0, 1),
(116, 16, '音响麦克风', 'y', '音响麦克风', 'y', '', '', '', '', 0, 50, 0, 1),
(117, 16, '耳机/耳麦', 'e', '耳机/耳麦', 'e', '', '', '', '', 0, 50, 0, 1),
(118, 17, '学生平板', 'x', '学生平板', 'x', '', '', '', '', 0, 50, 0, 1),
(119, 17, '点读机', 'd', '点读机', 'd', '', '', '', '', 0, 50, 0, 1),
(120, 17, '电纸书', 'd', '电纸书', 'd', '', '', '', '', 0, 50, 0, 1),
(121, 17, '电子词典', 'd', '电子词典', 'd', '', '', '', '', 0, 50, 0, 1),
(122, 17, '复读机', 'f', '复读机', 'f', '', '', '', '', 0, 50, 0, 1),
(123, 1, '手机', 's', '手机', 's', '', '', '', '', 0, 50, 0, 1),
(124, 18, '对讲机', 'd', '对讲机', 'd', '', '', '', '', 0, 50, 0, 1),
(125, 19, '录音机', 'l', '录音机', 'l', '', '', '', '', 0, 50, 0, 1),
(126, 19, '饮水机', 'y', '饮水机', 'y', '', '', '', '', 0, 50, 0, 1),
(127, 19, '烫衣机', 't', '烫衣机', 't', '', '', '', '', 0, 50, 0, 1),
(128, 19, '电风扇', 'd', '电风扇', 'd', '', '', '', '', 0, 50, 0, 1),
(129, 19, '电话机', 'd', '电话机', 'd', '', '', '', '', 0, 50, 0, 1),
(130, 20, '电视', 'd', '电视', 'd', '', '', '', '', 0, 50, 0, 1),
(131, 20, '冰箱', 'b', '冰箱', 'b', '', '', '', '', 0, 50, 0, 1),
(132, 20, '空调', 'k', '空调', 'k', '', '', '', '', 0, 50, 0, 1),
(133, 20, '洗衣机', 'x', '洗衣机', 'x', '', '', '', '', 0, 50, 0, 1),
(134, 20, '热水器', 'r', '热水器', 'r', '', '', '', '', 0, 50, 0, 1),
(135, 21, '料理/榨汁机', 'l', '料理/榨汁机', 'l', '', '', '', '', 0, 50, 0, 1),
(136, 21, '电饭锅', 'd', '电饭锅', 'd', '', '', '', '', 0, 50, 0, 1),
(137, 21, '微波炉', 'w', '微波炉', 'w', '', '', '', '', 0, 50, 0, 1),
(138, 21, '豆浆机', 'd', '豆浆机', 'd', '', '', '', '', 0, 50, 0, 1),
(139, 22, '剃须刀', 't', '剃须刀', 't', '', '', '', '', 0, 50, 0, 1),
(140, 22, '吹风机', 'c', '吹风机', 'c', '', '', '', '', 0, 50, 0, 1),
(141, 22, '按摩器', 'a', '按摩器', 'a', '', '', '', '', 0, 50, 0, 1),
(142, 22, '足浴盆', 'z', '足浴盆', 'z', '', '', '', '', 0, 50, 0, 1),
(143, 22, '血压计', 'x', '血压计', 'x', '', '', '', '', 0, 50, 0, 1),
(144, 23, '厨卫五金', 'c', '厨卫五金', 'c', '', '', '', '', 0, 50, 0, 1),
(145, 23, '门铃门锁', 'm', '门铃门锁', 'm', '', '', '', '', 0, 50, 0, 1),
(146, 23, '开关插座', 'k', '开关插座', 'k', '', '', '', '', 0, 50, 0, 1),
(147, 23, '电动工具', 'd', '电动工具', 'd', '', '', '', '', 0, 50, 0, 1),
(148, 23, '监控安防', 'j', '监控安防', 'j', '', '', '', '', 0, 50, 0, 1),
(149, 23, '仪表仪器', 'y', '仪表仪器', 'y', '', '', '', '', 0, 50, 0, 1),
(150, 23, '电线线缆', 'd', '电线线缆', 'd', '', '', '', '', 0, 50, 0, 1),
(151, 23, '浴霸/排气扇', 'y', '浴霸/排气扇', 'y', '', '', '', '', 0, 50, 0, 1),
(152, 23, '灯具', 'd', '灯具', 'd', '', '', '', '', 0, 50, 0, 1),
(153, 23, '水龙头', 's', '水龙头', 's', '', '', '', '', 0, 50, 0, 1),
(154, 24, '网络配件', 'w', '网络配件', 'w', '', '', '', '', 0, 50, 0, 1),
(155, 24, '路由器', 'l', '路由器', 'l', '', '', '', '', 0, 50, 0, 1),
(156, 24, '网卡', 'w', '网卡', 'w', '', '', '', '', 0, 50, 0, 1),
(157, 24, '交换机', 'j', '交换机', 'j', '', '', '', '', 0, 50, 0, 1),
(158, 24, '网络存储', 'w', '网络存储', 'w', '', '', '', '', 0, 50, 0, 1),
(159, 24, '3G/4G/5G上网', '3', '3G/4G/5G上网', '3', '', '', '', '', 0, 50, 0, 1),
(160, 24, '网络盒子', 'w', '网络盒子', 'w', '', '', '', '', 0, 50, 0, 1),
(161, 25, '复合机', 'f', '复合机', 'f', '', '', '', '', 0, 50, 0, 1),
(162, 25, '办公家具', 'b', '办公家具', 'b', '', '', '', '', 0, 50, 0, 1),
(163, 25, '摄影机', 's', '摄影机', 's', '', '', '', '', 0, 50, 0, 1),
(164, 25, '碎纸机', 's', '碎纸机', 's', '', '', '', '', 0, 50, 0, 1),
(165, 25, '白板', 'b', '白板', 'b', '', '', '', '', 0, 50, 0, 1),
(166, 25, '投影配件', 't', '投影配件', 't', '', '', '', '', 0, 50, 0, 1),
(167, 25, '考勤机', 'k', '考勤机', 'k', '', '', '', '', 0, 50, 0, 1),
(168, 25, '多功能一体机', 'd', '多功能一体机', 'd', '', '', '', '', 0, 50, 0, 1),
(169, 25, '收款/POS机', 's', '收款/POS机', 's', '', '', '', '', 0, 50, 0, 1),
(170, 25, '打印机', 'd', '打印机', 'd', '', '', '', '', 0, 50, 0, 1),
(171, 25, '会员视频音频', 'h', '会员视频音频', 'h', '', '', '', '', 0, 50, 0, 1),
(172, 25, '传真设备', 'c', '传真设备', 'c', '', '', '', '', 0, 50, 0, 1),
(173, 25, '保险柜', 'b', '保险柜', 'b', '', '', '', '', 0, 50, 0, 1),
(174, 25, '验钞/点钞机', 'y', '验钞/点钞机', 'y', '', '', '', '', 0, 50, 0, 1),
(175, 25, '装订/封装机', 'z', '装订/封装机', 'z', '', '', '', '', 0, 50, 0, 1),
(176, 25, '扫描设备', 's', '扫描设备', 's', '', '', '', '', 0, 50, 0, 1),
(177, 25, '安防监控', 'a', '安防监控', 'a', '', '', '', '', 0, 50, 0, 1),
(178, 26, '文具管理', 'w', '文具管理', 'w', '', '', '', '', 0, 50, 0, 1),
(179, 26, '本册便签', 'b', '本册便签', 'b', '', '', '', '', 0, 50, 0, 1),
(180, 26, '硒鼓/墨粉', 'x', '硒鼓/墨粉', 'x', '', '', '', '', 0, 50, 0, 1),
(181, 26, '计算器', 'j', '计算器', 'j', '', '', '', '', 0, 50, 0, 1),
(182, 26, '墨盒', 'm', '墨盒', 'm', '', '', '', '', 0, 50, 0, 1),
(183, 26, '笔类', 'b', '笔类', 'b', '', '', '', '', 0, 50, 0, 1),
(184, 26, '色带', 's', '色带', 's', '', '', '', '', 0, 50, 0, 1),
(185, 26, '画具画材', 'h', '画具画材', 'h', '', '', '', '', 0, 50, 0, 1),
(186, 26, '纸类', 'z', '纸类', 'z', '', '', '', '', 0, 50, 0, 1),
(187, 26, '财会用品', 'c', '财会用品', 'c', '', '', '', '', 0, 50, 0, 1),
(188, 26, '办公文具', 'b', '办公文具', 'b', '', '', '', '', 0, 50, 0, 1),
(189, 26, '刻录碟片', 'k', '刻录碟片', 'k', '', '', '', '', 0, 50, 0, 1),
(190, 26, '学生文具', 'x', '学生文具', 'x', '', '', '', '', 0, 50, 0, 1),
(191, 27, '平板电脑', 'p', '平板电脑', 'p', '', '', '', '', 0, 50, 0, 1),
(192, 27, '台式机', 't', '台式机', 't', '', '', '', '', 0, 50, 0, 1),
(193, 27, '一体机', 'y', '一体机', 'y', '', '', '', '', 0, 50, 0, 1),
(194, 27, '笔记本', 'b', '笔记本', 'b', '', '', '', '', 0, 50, 0, 1),
(195, 27, '超极本', 'c', '超极本', 'c', '', '', '', '', 0, 50, 0, 1),
(196, 28, '内存', 'n', '内存', 'n', '', '', '', '', 0, 50, 0, 1),
(197, 28, '组装电脑', 'z', '组装电脑', 'z', '', '', '', '', 0, 50, 0, 1),
(198, 28, '机箱', 'j', '机箱', 'j', '', '', '', '', 0, 50, 0, 1),
(199, 28, '电源', 'd', '电源', 'd', '', '', '', '', 0, 50, 0, 1),
(200, 28, 'CPU', 'c', 'CPU', 'c', '', '', '', '', 0, 50, 0, 1),
(201, 28, '显示器', 'x', '显示器', 'x', '', '', '', '', 0, 50, 0, 1),
(202, 28, '主板', 'z', '主板', 'z', '', '', '', '', 0, 50, 0, 1),
(203, 28, '刻录机/光驱', 'k', '刻录机/光驱', 'k', '', '', '', '', 0, 50, 0, 1),
(204, 28, '显卡', 'x', '显卡', 'x', '', '', '', '', 0, 50, 0, 1),
(205, 28, '声卡/扩展卡', 's', '声卡/扩展卡', 's', '', '', '', '', 0, 50, 0, 1),
(206, 28, '硬盘', 'y', '硬盘', 'y', '', '', '', '', 0, 50, 0, 1),
(207, 28, '散热器', 's', '散热器', 's', '', '', '', '', 0, 50, 0, 1),
(208, 28, '固态硬盘', 'g', '固态硬盘', 'g', '', '', '', '', 0, 50, 0, 1),
(209, 28, '装机配件', 'z', '装机配件', 'z', '', '', '', '', 0, 50, 0, 1),
(210, 29, '线缆', 'x', '线缆', 'x', '', '', '', '', 0, 50, 0, 1),
(211, 29, '鼠标', 's', '鼠标', 's', '', '', '', '', 0, 50, 0, 1),
(212, 29, '手写板', 's', '手写板', 's', '', '', '', '', 0, 50, 0, 1),
(213, 29, '键盘', 'j', '键盘', 'j', '', '', '', '', 0, 50, 0, 1),
(214, 29, '电脑工具', 'd', '电脑工具', 'd', '', '', '', '', 0, 50, 0, 1),
(215, 29, '网络仪表仪器', 'w', '网络仪表仪器', 'w', '', '', '', '', 0, 50, 0, 1),
(216, 29, 'UPS', 'u', 'UPS', 'u', '', '', '', '', 0, 50, 0, 1),
(217, 29, 'U盘', 'u', 'U盘', 'u', '', '', '', '', 0, 50, 0, 1),
(218, 29, '插座', 'c', '插座', 'c', '', '', '', '', 0, 50, 0, 1),
(219, 29, '移动硬盘', 'y', '移动硬盘', 'y', '', '', '', '', 0, 50, 0, 1),
(220, 29, '鼠标垫', 's', '鼠标垫', 's', '', '', '', '', 0, 50, 0, 1),
(221, 29, '摄像头', 's', '摄像头', 's', '', '', '', '', 0, 50, 0, 1),
(222, 30, '游戏软件', 'y', '游戏软件', 'y', '', '', '', '', 0, 50, 0, 1),
(223, 30, '游戏周边', 'y', '游戏周边', 'y', '', '', '', '', 0, 50, 0, 1),
(224, 30, '游戏机', 'y', '游戏机', 'y', '', '', '', '', 0, 50, 0, 1),
(225, 30, '游戏耳机', 'y', '游戏耳机', 'y', '', '', '', '', 0, 50, 0, 1),
(226, 30, '手柄方向盘', 's', '手柄方向盘', 's', '', '', '', '', 0, 50, 0, 1),
(227, 31, '清洁工具', 'q', '清洁工具', 'q', '', '', '', '', 0, 50, 0, 1),
(228, 31, '收纳用品', 's', '收纳用品', 's', '', '', '', '', 0, 50, 0, 1),
(229, 31, '雨伞雨具', 'y', '雨伞雨具', 'y', '', '', '', '', 0, 50, 0, 1),
(230, 31, '浴室用品', 'y', '浴室用品', 'y', '', '', '', '', 0, 50, 0, 1),
(231, 31, '缝纫/针织用品', 'f', '缝纫/针织用品', 'f', '', '', '', '', 0, 50, 0, 1),
(232, 31, '洗晒/熨烫', 'x', '洗晒/熨烫', 'x', '', '', '', '', 0, 50, 0, 1),
(233, 31, '净化除味', 'j', '净化除味', 'j', '', '', '', '', 0, 50, 0, 1),
(234, 32, '节庆饰品', 'j', '节庆饰品', 'j', '', '', '', '', 0, 50, 0, 1),
(235, 32, '手工/十字绣', 's', '手工/十字绣', 's', '', '', '', '', 0, 50, 0, 1),
(236, 32, '桌布/罩件', 'z', '桌布/罩件', 'z', '', '', '', '', 0, 50, 0, 1),
(237, 32, '钟饰', 'z', '钟饰', 'z', '', '', '', '', 0, 50, 0, 1),
(238, 32, '地毯地垫', 'd', '地毯地垫', 'd', '', '', '', '', 0, 50, 0, 1),
(239, 32, '装饰摆件', 'z', '装饰摆件', 'z', '', '', '', '', 0, 50, 0, 1),
(240, 32, '沙发垫套/椅垫', 's', '沙发垫套/椅垫', 's', '', '', '', '', 0, 50, 0, 1),
(241, 32, '花瓶花艺', 'h', '花瓶花艺', 'h', '', '', '', '', 0, 50, 0, 1),
(242, 32, '帘艺隔断', 'l', '帘艺隔断', 'l', '', '', '', '', 0, 50, 0, 1),
(243, 32, '创意家居', 'c', '创意家居', 'c', '', '', '', '', 0, 50, 0, 1),
(244, 32, '相框/照片墙', 'x', '相框/照片墙', 'x', '', '', '', '', 0, 50, 0, 1),
(245, 32, '保暖防护', 'b', '保暖防护', 'b', '', '', '', '', 0, 50, 0, 1),
(246, 32, '装饰字画', 'z', '装饰字画', 'z', '', '', '', '', 0, 50, 0, 1),
(247, 32, '香薰蜡烛', 'x', '香薰蜡烛', 'x', '', '', '', '', 0, 50, 0, 1),
(248, 32, '墙贴/装饰贴', 'q', '墙贴/装饰贴', 'q', '', '', '', '', 0, 50, 0, 1),
(249, 33, '水族用品', 's', '水族用品', 's', '', '', '', '', 0, 50, 0, 1),
(250, 33, '宠物玩具', 'c', '宠物玩具', 'c', '', '', '', '', 0, 50, 0, 1),
(251, 33, '宠物主粮', 'c', '宠物主粮', 'c', '', '', '', '', 0, 50, 0, 1),
(252, 33, '宠物牵引', 'c', '宠物牵引', 'c', '', '', '', '', 0, 50, 0, 1),
(253, 33, '宠物零食', 'c', '宠物零食', 'c', '', '', '', '', 0, 50, 0, 1),
(254, 33, '宠物驱虫', 'c', '宠物驱虫', 'c', '', '', '', '', 0, 50, 0, 1),
(255, 33, '猫砂/尿布', 'm', '猫砂/尿布', 'm', '', '', '', '', 0, 50, 0, 1),
(256, 33, '洗护美容', 'x', '洗护美容', 'x', '', '', '', '', 0, 50, 0, 1),
(257, 33, '家居日用', 'j', '家居日用', 'j', '', '', '', '', 0, 50, 0, 1),
(258, 33, '医疗保健', 'y', '医疗保健', 'y', '', '', '', '', 0, 50, 0, 1),
(259, 33, '出行装备', 'c', '出行装备', 'c', '', '', '', '', 0, 50, 0, 1),
(260, 34, '剪刀菜饭', 'j', '剪刀菜饭', 'j', '', '', '', '', 0, 50, 0, 1),
(261, 34, '厨房配件', 'c', '厨房配件', 'c', '', '', '', '', 0, 50, 0, 1),
(262, 34, '水具酒具', 's', '水具酒具', 's', '', '', '', '', 0, 50, 0, 1),
(263, 34, '餐具', 'c', '餐具', 'c', '', '', '', '', 0, 50, 0, 1),
(264, 34, '茶具/咖啡具', 'c', '茶具/咖啡具', 'c', '', '', '', '', 0, 50, 0, 1),
(265, 34, '烹饪锅具', 'p', '烹饪锅具', 'p', '', '', '', '', 0, 50, 0, 1),
(266, 35, '电工电料', 'd', '电工电料', 'd', '', '', '', '', 0, 50, 0, 1),
(267, 35, '墙地材料', 'q', '墙地材料', 'q', '', '', '', '', 0, 50, 0, 1),
(268, 35, '装饰材料', 'z', '装饰材料', 'z', '', '', '', '', 0, 50, 0, 1),
(269, 35, '装修服务', 'z', '装修服务', 'z', '', '', '', '', 0, 50, 0, 1),
(270, 35, '沐浴花洒', 'y', '沐浴花洒', 'y', '', '', '', '', 0, 50, 0, 1),
(271, 35, '灯饰照明', 'd', '灯饰照明', 'd', '', '', '', '', 0, 50, 0, 1),
(272, 35, '开关插座', 'k', '开关插座', 'k', '', '', '', '', 0, 50, 0, 1),
(273, 35, '厨房卫浴', 'c', '厨房卫浴', 'c', '', '', '', '', 0, 50, 0, 1),
(274, 35, '油漆涂料', 'y', '油漆涂料', 'y', '', '', '', '', 0, 50, 0, 1),
(275, 35, '五金工具', 'w', '五金工具', 'w', '', '', '', '', 0, 50, 0, 1),
(276, 35, '龙头', 'l', '龙头', 'l', '', '', '', '', 0, 50, 0, 1),
(277, 36, '床品套件', 'c', '床品套件', 'c', '', '', '', '', 0, 50, 0, 1),
(278, 36, '抱枕靠垫', 'b', '抱枕靠垫', 'b', '', '', '', '', 0, 50, 0, 1),
(279, 36, '被子', 'b', '被子', 'b', '', '', '', '', 0, 50, 0, 1),
(280, 36, '布艺软饰', 'b', '布艺软饰', 'b', '', '', '', '', 0, 50, 0, 1),
(281, 36, '被芯', 'b', '被芯', 'b', '', '', '', '', 0, 50, 0, 1),
(282, 36, '窗帘窗纱', 'c', '窗帘窗纱', 'c', '', '', '', '', 0, 50, 0, 1),
(283, 36, '床单被罩', 'c', '床单被罩', 'c', '', '', '', '', 0, 50, 0, 1),
(284, 36, '蚊帐', 'w', '蚊帐', 'w', '', '', '', '', 0, 50, 0, 1),
(285, 36, '床垫床褥', 'c', '床垫床褥', 'c', '', '', '', '', 0, 50, 0, 1),
(286, 36, '凉席', 'l', '凉席', 'l', '', '', '', '', 0, 50, 0, 1),
(287, 36, '电地毯', 'd', '电地毯', 'd', '', '', '', '', 0, 50, 0, 1),
(288, 36, '毯子', 't', '毯子', 't', '', '', '', '', 0, 50, 0, 1),
(289, 36, '毛巾浴巾', 'm', '毛巾浴巾', 'm', '', '', '', '', 0, 50, 0, 1),
(290, 37, '餐厅家具', 'c', '餐厅家具', 'c', '', '', '', '', 0, 50, 0, 1),
(291, 37, '电脑椅', 'd', '电脑椅', 'd', '', '', '', '', 0, 50, 0, 1),
(292, 37, '书房家具', 's', '书房家具', 's', '', '', '', '', 0, 50, 0, 1),
(293, 37, '衣柜', 'y', '衣柜', 'y', '', '', '', '', 0, 50, 0, 1),
(294, 37, '储物家具', 'c', '储物家具', 'c', '', '', '', '', 0, 50, 0, 1),
(295, 37, '茶几', 'c', '茶几', 'c', '', '', '', '', 0, 50, 0, 1),
(296, 37, '阳台/户外', 'y', '阳台/户外', 'y', '', '', '', '', 0, 50, 0, 1),
(297, 37, '电视柜', 'd', '电视柜', 'd', '', '', '', '', 0, 50, 0, 1),
(298, 37, '商业办公', 's', '商业办公', 's', '', '', '', '', 0, 50, 0, 1),
(299, 37, '餐桌', 'c', '餐桌', 'c', '', '', '', '', 0, 50, 0, 1),
(300, 37, '卧室家具', 'w', '卧室家具', 'w', '', '', '', '', 0, 50, 0, 1),
(301, 37, '床', 'c', '床', 'c', '', '', '', '', 0, 50, 0, 1),
(302, 37, '电脑桌', 'd', '电脑桌', 'd', '', '', '', '', 0, 50, 0, 1),
(303, 37, '客厅家具', 'k', '客厅家具', 'k', '', '', '', '', 0, 50, 0, 1),
(304, 37, '床垫', 'c', '床垫', 'c', '', '', '', '', 0, 50, 0, 1),
(305, 37, '鞋架/衣帽架', 'x', '鞋架/衣帽架', 'x', '', '', '', '', 0, 50, 0, 1),
(306, 37, '客厅家具', 'k', '客厅家具', 'k', '', '', '', '', 0, 50, 0, 1),
(307, 37, '沙发', 's', '沙发', 's', '', '', '', '', 0, 50, 0, 1),
(308, 38, '吸顶灯', 'x', '吸顶灯', 'x', '', '', '', '', 0, 50, 0, 1),
(309, 38, '吊灯', 'd', '吊灯', 'd', '', '', '', '', 0, 50, 0, 1),
(310, 38, '筒灯射灯', 't', '筒灯射灯', 't', '', '', '', '', 0, 50, 0, 1),
(311, 38, '氛围照明', 'f', '氛围照明', 'f', '', '', '', '', 0, 50, 0, 1),
(312, 38, 'LED灯', 'l', 'LED灯', 'l', '', '', '', '', 0, 50, 0, 1),
(313, 38, '节能灯', 'j', '节能灯', 'j', '', '', '', '', 0, 50, 0, 1),
(314, 38, '落地灯', 'l', '落地灯', 'l', '', '', '', '', 0, 50, 0, 1),
(315, 38, '五金电器', 'w', '五金电器', 'w', '', '', '', '', 0, 50, 0, 1),
(316, 38, '应急灯/手电', 'y', '应急灯/手电', 'y', '', '', '', '', 0, 50, 0, 1),
(317, 38, '台灯', 't', '台灯', 't', '', '', '', '', 0, 50, 0, 1),
(318, 38, '装饰灯', 'z', '装饰灯', 'z', '', '', '', '', 0, 50, 0, 1),
(319, 39, '短外套', 'd', '短外套', 'd', '', '', '', '', 0, 50, 0, 1),
(320, 39, '羊毛衫', 'y', '羊毛衫', 'y', '', '', '', '', 0, 50, 0, 1),
(321, 39, '雪纺衫', 'x', '雪纺衫', 'x', '', '', '', '', 0, 50, 0, 1),
(322, 39, '礼服', 'l', '礼服', 'l', '', '', '', '', 0, 50, 0, 1),
(323, 39, '风衣', 'f', '风衣', 'f', '', '', '', '', 0, 50, 0, 1),
(324, 39, '羊绒衫', 'y', '羊绒衫', 'y', '', '', '', '', 0, 50, 0, 1),
(325, 39, '牛仔裤', 'n', '牛仔裤', 'n', '', '', '', '', 0, 50, 0, 1),
(326, 39, '马甲', 'm', '马甲', 'm', '', '', '', '', 0, 50, 0, 1),
(327, 39, '衬衫', 'c', '衬衫', 'c', '', '', '', '', 0, 50, 0, 1),
(328, 39, '半身裙', 'b', '半身裙', 'b', '', '', '', '', 0, 50, 0, 1),
(329, 39, '休闲裤', 'x', '休闲裤', 'x', '', '', '', '', 0, 50, 0, 1),
(330, 39, '吊带/背心', 'd', '吊带/背心', 'd', '', '', '', '', 0, 50, 0, 1),
(331, 39, '羽绒服', 'y', '羽绒服', 'y', '', '', '', '', 0, 50, 0, 1),
(332, 39, 'T恤', 't', 'T恤', 't', '', '', '', '', 0, 50, 0, 1),
(333, 39, '大码女装', 'd', '大码女装', 'd', '', '', '', '', 0, 50, 0, 1),
(334, 39, '正装裤', 'z', '正装裤', 'z', '', '', '', '', 0, 50, 0, 1),
(335, 39, '设计师/潮牌', 's', '设计师/潮牌', 's', '', '', '', '', 0, 50, 0, 1),
(336, 39, '毛呢大衣', 'm', '毛呢大衣', 'm', '', '', '', '', 0, 50, 0, 1),
(337, 39, '小西装', 'x', '小西装', 'x', '', '', '', '', 0, 50, 0, 1),
(338, 39, '中老年女装', 'z', '中老年女装', 'z', '', '', '', '', 0, 50, 0, 1),
(339, 39, '加绒裤', 'j', '加绒裤', 'j', '', '', '', '', 0, 50, 0, 1),
(340, 39, '棉服', 'm', '棉服', 'm', '', '', '', '', 0, 50, 0, 1),
(341, 39, '打底衫', 'd', '打底衫', 'd', '', '', '', '', 0, 50, 0, 1),
(342, 39, '皮草', 'p', '皮草', 'p', '', '', '', '', 0, 50, 0, 1),
(343, 39, '短裤', 'd', '短裤', 'd', '', '', '', '', 0, 50, 0, 1),
(344, 39, '连衣裙', 'l', '连衣裙', 'l', '', '', '', '', 0, 50, 0, 1),
(345, 39, '打底裤', 'd', '打底裤', 'd', '', '', '', '', 0, 50, 0, 1),
(346, 39, '真皮皮衣', 'z', '真皮皮衣', 'z', '', '', '', '', 0, 50, 0, 1),
(347, 39, '婚纱', 'h', '婚纱', 'h', '', '', '', '', 0, 50, 0, 1),
(348, 39, '卫衣', 'w', '卫衣', 'w', '', '', '', '', 0, 50, 0, 1),
(349, 39, '针织衫', 'z', '针织衫', 'z', '', '', '', '', 0, 50, 0, 1),
(350, 39, '仿皮皮衣', 'f', '仿皮皮衣', 'f', '', '', '', '', 0, 50, 0, 1),
(351, 39, '旗袍/唐装', 'q', '旗袍/唐装', 'q', '', '', '', '', 0, 50, 0, 1),
(352, 40, '羊毛衫', 'y', '羊毛衫', 'y', '', '', '', '', 0, 50, 0, 1),
(353, 40, '休闲裤', 'x', '休闲裤', 'x', '', '', '', '', 0, 50, 0, 1),
(354, 40, 'POLO衫', 'p', 'POLO衫', 'p', '', '', '', '', 0, 50, 0, 1),
(355, 40, '加绒裤', 'j', '加绒裤', 'j', '', '', '', '', 0, 50, 0, 1),
(356, 40, '衬衫', 'c', '衬衫', 'c', '', '', '', '', 0, 50, 0, 1),
(357, 40, '短裤', 'd', '短裤', 'd', '', '', '', '', 0, 50, 0, 1),
(358, 40, '真皮皮衣', 'z', '真皮皮衣', 'z', '', '', '', '', 0, 50, 0, 1),
(359, 40, '毛呢大衣', 'm', '毛呢大衣', 'm', '', '', '', '', 0, 50, 0, 1),
(360, 40, '中老年男装', 'z', '中老年男装', 'z', '', '', '', '', 0, 50, 0, 1),
(361, 40, '卫衣', 'w', '卫衣', 'w', '', '', '', '', 0, 50, 0, 1),
(362, 40, '风衣', 'f', '风衣', 'f', '', '', '', '', 0, 50, 0, 1),
(363, 40, '大码男装', 'd', '大码男装', 'd', '', '', '', '', 0, 50, 0, 1),
(364, 40, '羽绒服', 'y', '羽绒服', 'y', '', '', '', '', 0, 50, 0, 1),
(365, 40, 'T恤', 't', 'T恤', 't', '', '', '', '', 0, 50, 0, 1),
(366, 40, '仿皮皮衣', 'f', '仿皮皮衣', 'f', '', '', '', '', 0, 50, 0, 1),
(367, 40, '羊绒衫', 'y', '羊绒衫', 'y', '', '', '', '', 0, 50, 0, 1),
(368, 40, '棉服', 'm', '棉服', 'm', '', '', '', '', 0, 50, 0, 1),
(369, 40, '马甲/背心', 'm', '马甲/背心', 'm', '', '', '', '', 0, 50, 0, 1),
(370, 40, '西服', 'x', '西服', 'x', '', '', '', '', 0, 50, 0, 1),
(371, 40, '设计师/潮牌', 's', '设计师/潮牌', 's', '', '', '', '', 0, 50, 0, 1),
(372, 40, '针织衫', 'z', '针织衫', 'z', '', '', '', '', 0, 50, 0, 1),
(373, 40, '西服套装', 'x', '西服套装', 'x', '', '', '', '', 0, 50, 0, 1),
(374, 40, '西裤', 'x', '西裤', 'x', '', '', '', '', 0, 50, 0, 1),
(375, 40, '工装', 'g', '工装', 'g', '', '', '', '', 0, 50, 0, 1),
(376, 40, '夹克', 'j', '夹克', 'j', '', '', '', '', 0, 50, 0, 1),
(377, 40, '牛仔裤', 'n', '牛仔裤', 'n', '', '', '', '', 0, 50, 0, 1),
(378, 40, '卫裤/运动裤', 'w', '卫裤/运动裤', 'w', '', '', '', '', 0, 50, 0, 1),
(379, 40, '唐装/中山装', 't', '唐装/中山装', 't', '', '', '', '', 0, 50, 0, 1),
(380, 41, '保暖内衣', 'b', '保暖内衣', 'b', '', '', '', '', 0, 50, 0, 1),
(381, 41, '大码内衣', 'd', '大码内衣', 'd', '', '', '', '', 0, 50, 0, 1),
(382, 41, '吊带/背心', 'd', '吊带/背心', 'd', '', '', '', '', 0, 50, 0, 1),
(383, 41, '秋衣秋裤', 'q', '秋衣秋裤', 'q', '', '', '', '', 0, 50, 0, 1),
(384, 41, '文胸', 'w', '文胸', 'w', '', '', '', '', 0, 50, 0, 1),
(385, 41, '内衣配件', 'n', '内衣配件', 'n', '', '', '', '', 0, 50, 0, 1),
(386, 41, '睡衣/家居服', 's', '睡衣/家居服', 's', '', '', '', '', 0, 50, 0, 1),
(387, 41, '男式内裤', 'n', '男式内裤', 'n', '', '', '', '', 0, 50, 0, 1),
(388, 41, '泳衣', 'y', '泳衣', 'y', '', '', '', '', 0, 50, 0, 1),
(389, 41, '打底裤袜', 'd', '打底裤袜', 'd', '', '', '', '', 0, 50, 0, 1),
(390, 41, '女式内裤', 'n', '女式内裤', 'n', '', '', '', '', 0, 50, 0, 1),
(391, 41, '塑身美体', 's', '塑身美体', 's', '', '', '', '', 0, 50, 0, 1),
(392, 41, '休闲棉袜', 'x', '休闲棉袜', 'x', '', '', '', '', 0, 50, 0, 1),
(393, 41, '连裤袜/丝袜', 'l', '连裤袜/丝袜', 'l', '', '', '', '', 0, 50, 0, 1),
(394, 41, '美腿袜', 'm', '美腿袜', 'm', '', '', '', '', 0, 50, 0, 1),
(395, 41, '商务男袜', 's', '商务男袜', 's', '', '', '', '', 0, 50, 0, 1),
(396, 41, '打底衫', 'd', '打底衫', 'd', '', '', '', '', 0, 50, 0, 1),
(397, 41, '情趣内衣', 'q', '情趣内衣', 'q', '', '', '', '', 0, 50, 0, 1),
(398, 41, '情侣睡衣', 'q', '情侣睡衣', 'q', '', '', '', '', 0, 50, 0, 1),
(399, 41, '少女文胸', 's', '少女文胸', 's', '', '', '', '', 0, 50, 0, 1),
(400, 41, '文胸套装', 'w', '文胸套装', 'w', '', '', '', '', 0, 50, 0, 1),
(401, 41, '抹胸', 'm', '抹胸', 'm', '', '', '', '', 0, 50, 0, 1),
(402, 42, '沐浴', 'y', '沐浴', 'y', '', '', '', '', 0, 50, 0, 1),
(403, 42, '润肤', 'r', '润肤', 'r', '', '', '', '', 0, 50, 0, 1),
(404, 42, '颈部', 'j', '颈部', 'j', '', '', '', '', 0, 50, 0, 1),
(405, 42, '手足', 's', '手足', 's', '', '', '', '', 0, 50, 0, 1),
(406, 42, '纤体塑形', 'x', '纤体塑形', 'x', '', '', '', '', 0, 50, 0, 1),
(407, 42, '美胸', 'm', '美胸', 'm', '', '', '', '', 0, 50, 0, 1),
(408, 42, '套装', 't', '套装', 't', '', '', '', '', 0, 50, 0, 1),
(409, 43, '牙膏/牙粉', 'y', '牙膏/牙粉', 'y', '', '', '', '', 0, 50, 0, 1),
(410, 43, '牙刷/牙线', 'y', '牙刷/牙线', 'y', '', '', '', '', 0, 50, 0, 1),
(411, 43, '漱口水', 's', '漱口水', 's', '', '', '', '', 0, 50, 0, 1),
(412, 43, '套装', 't', '套装', 't', '', '', '', '', 0, 50, 0, 1),
(413, 44, '卫生巾', 'w', '卫生巾', 'w', '', '', '', '', 0, 50, 0, 1),
(414, 44, '卫生护垫', 'w', '卫生护垫', 'w', '', '', '', '', 0, 50, 0, 1),
(415, 44, '私密护理', 's', '私密护理', 's', '', '', '', '', 0, 50, 0, 1),
(416, 44, '脱毛膏', 't', '脱毛膏', 't', '', '', '', '', 0, 50, 0, 1),
(417, 45, '唇部', 'c', '唇部', 'c', '', '', '', '', 0, 50, 0, 1),
(418, 45, '美甲', 'm', '美甲', 'm', '', '', '', '', 0, 50, 0, 1),
(419, 45, '美容工具', 'm', '美容工具', 'm', '', '', '', '', 0, 50, 0, 1),
(420, 45, '套装', 't', '套装', 't', '', '', '', '', 0, 50, 0, 1),
(421, 45, '香水', 'x', '香水', 'x', '', '', '', '', 0, 50, 0, 1),
(422, 45, '底妆', 'd', '底妆', 'd', '', '', '', '', 0, 50, 0, 1),
(423, 45, '腮红', 's', '腮红', 's', '', '', '', '', 0, 50, 0, 1),
(424, 45, '眼部', 'y', '眼部', 'y', '', '', '', '', 0, 50, 0, 1),
(425, 47, '面膜', 'm', '面膜', 'm', '', '', '', '', 0, 50, 0, 1),
(426, 47, '剃须', 't', '剃须', 't', '', '', '', '', 0, 50, 0, 1),
(427, 47, '套装', 't', '套装', 't', '', '', '', '', 0, 50, 0, 1),
(428, 47, '清洁', 'q', '清洁', 'q', '', '', '', '', 0, 50, 0, 1),
(429, 47, '护肤', 'h', '护肤', 'h', '', '', '', '', 0, 50, 0, 1),
(430, 48, '套装', 't', '套装', 't', '', '', '', '', 0, 50, 0, 1),
(431, 48, '洗发', 'x', '洗发', 'x', '', '', '', '', 0, 50, 0, 1),
(432, 48, '护发', 'h', '护发', 'h', '', '', '', '', 0, 50, 0, 1),
(433, 48, '染发', 'r', '染发', 'r', '', '', '', '', 0, 50, 0, 1),
(434, 48, '造型', 'z', '造型', 'z', '', '', '', '', 0, 50, 0, 1),
(435, 48, '假发', 'j', '假发', 'j', '', '', '', '', 0, 50, 0, 1),
(436, 49, '商务公文包', 's', '商务公文包', 's', '', '', '', '', 0, 50, 0, 1),
(437, 49, '单肩/斜挎包', 'd', '单肩/斜挎包', 'd', '', '', '', '', 0, 50, 0, 1),
(438, 49, '男士手包', 'n', '男士手包', 'n', '', '', '', '', 0, 50, 0, 1),
(439, 49, '双肩包', 's', '双肩包', 's', '', '', '', '', 0, 50, 0, 1),
(440, 49, '钱包/卡包', 'q', '钱包/卡包', 'q', '', '', '', '', 0, 50, 0, 1),
(441, 49, '钥匙包', 'y', '钥匙包', 'y', '', '', '', '', 0, 50, 0, 1),
(442, 50, '旅行包', 'l', '旅行包', 'l', '', '', '', '', 0, 50, 0, 1),
(443, 50, '妈咪包', 'm', '妈咪包', 'm', '', '', '', '', 0, 50, 0, 1),
(444, 50, '电脑包', 'd', '电脑包', 'd', '', '', '', '', 0, 50, 0, 1),
(445, 50, '休闲运动包', 'x', '休闲运动包', 'x', '', '', '', '', 0, 50, 0, 1),
(446, 50, '相机包', 'x', '相机包', 'x', '', '', '', '', 0, 50, 0, 1),
(447, 50, '腰包/胸包', 'y', '腰包/胸包', 'y', '', '', '', '', 0, 50, 0, 1),
(448, 50, '登山包', 'd', '登山包', 'd', '', '', '', '', 0, 50, 0, 1),
(449, 50, '旅行配件', 'l', '旅行配件', 'l', '', '', '', '', 0, 50, 0, 1),
(450, 50, '拉杆箱/拉杆包', 'l', '拉杆箱/拉杆包', 'l', '', '', '', '', 0, 50, 0, 1),
(451, 50, '书包', 's', '书包', 's', '', '', '', '', 0, 50, 0, 1),
(452, 51, '彩宝', 'c', '彩宝', 'c', '', '', '', '', 0, 50, 0, 1),
(453, 51, '时尚饰品', 's', '时尚饰品', 's', '', '', '', '', 0, 50, 0, 1),
(454, 51, '铂金', 'b', '铂金', 'b', '', '', '', '', 0, 50, 0, 1),
(455, 51, '钻石', 'z', '钻石', 'z', '', '', '', '', 0, 50, 0, 1),
(456, 51, '天然木饰', 't', '天然木饰', 't', '', '', '', '', 0, 50, 0, 1),
(457, 51, '翡翠玉石', 'c', '翡翠玉石', 'c', '', '', '', '', 0, 50, 0, 1),
(458, 51, '珍珠', 'z', '珍珠', 'z', '', '', '', '', 0, 50, 0, 1),
(459, 51, '纯金K金饰品', 'c', '纯金K金饰品', 'c', '', '', '', '', 0, 50, 0, 1),
(460, 51, '金银投资', 'j', '金银投资', 'j', '', '', '', '', 0, 50, 0, 1),
(461, 51, '银饰', 'y', '银饰', 'y', '', '', '', '', 0, 50, 0, 1),
(462, 51, '水晶玛瑙', 's', '水晶玛瑙', 's', '', '', '', '', 0, 50, 0, 1),
(463, 52, '座钟挂钟', 'z', '座钟挂钟', 'z', '', '', '', '', 0, 50, 0, 1),
(464, 52, '男表', 'n', '男表', 'n', '', '', '', '', 0, 50, 0, 1),
(465, 52, '女表', 'n', '女表', 'n', '', '', '', '', 0, 50, 0, 1),
(466, 52, '儿童表', 'e', '儿童表', 'e', '', '', '', '', 0, 50, 0, 1),
(467, 52, '智能手表', 'z', '智能手表', 'z', '', '', '', '', 0, 50, 0, 1),
(468, 53, '女靴', 'n', '女靴', 'n', '', '', '', '', 0, 50, 0, 1),
(469, 53, '布鞋/绣花鞋', 'b', '布鞋/绣花鞋', 'b', '', '', '', '', 0, 50, 0, 1),
(470, 53, '鱼嘴鞋', 'y', '鱼嘴鞋', 'y', '', '', '', '', 0, 50, 0, 1),
(471, 53, '雪地靴', 'x', '雪地靴', 'x', '', '', '', '', 0, 50, 0, 1),
(472, 53, '凉鞋', 'l', '凉鞋', 'l', '', '', '', '', 0, 50, 0, 1),
(473, 53, '雨鞋/雨靴', 'y', '雨鞋/雨靴', 'y', '', '', '', '', 0, 50, 0, 1),
(474, 53, '单鞋', 'd', '单鞋', 'd', '', '', '', '', 0, 50, 0, 1),
(475, 53, '拖鞋/人字拖', 't', '拖鞋/人字拖', 't', '', '', '', '', 0, 50, 0, 1),
(476, 53, '鞋配件', 'x', '鞋配件', 'x', '', '', '', '', 0, 50, 0, 1),
(477, 53, '高跟鞋', 'g', '高跟鞋', 'g', '', '', '', '', 0, 50, 0, 1),
(478, 53, '马丁靴', 'm', '马丁靴', 'm', '', '', '', '', 0, 50, 0, 1),
(479, 53, '帆布鞋', 'f', '帆布鞋', 'f', '', '', '', '', 0, 50, 0, 1),
(480, 53, '休闲鞋', 'x', '休闲鞋', 'x', '', '', '', '', 0, 50, 0, 1),
(481, 53, '妈妈鞋', 'm', '妈妈鞋', 'm', '', '', '', '', 0, 50, 0, 1),
(482, 53, '踝靴', 'x', '踝靴', 'x', '', '', '', '', 0, 50, 0, 1),
(483, 53, '防水台', 'f', '防水台', 'f', '', '', '', '', 0, 50, 0, 1),
(484, 53, '内增高', 'n', '内增高', 'n', '', '', '', '', 0, 50, 0, 1),
(485, 53, '松糕鞋', 's', '松糕鞋', 's', '', '', '', '', 0, 50, 0, 1),
(486, 53, '坡跟鞋', 'p', '坡跟鞋', 'p', '', '', '', '', 0, 50, 0, 1),
(487, 54, '增高鞋', 'z', '增高鞋', 'z', '', '', '', '', 0, 50, 0, 1),
(488, 54, '鞋配件', 'x', '鞋配件', 'x', '', '', '', '', 0, 50, 0, 1),
(489, 54, '拖鞋/人字拖', 't', '拖鞋/人字拖', 't', '', '', '', '', 0, 50, 0, 1),
(490, 54, '凉鞋/沙滩鞋', 'l', '凉鞋/沙滩鞋', 'l', '', '', '', '', 0, 50, 0, 1),
(491, 54, '休闲鞋', 'x', '休闲鞋', 'x', '', '', '', '', 0, 50, 0, 1),
(492, 54, '雨鞋/雨靴', 'y', '雨鞋/雨靴', 'y', '', '', '', '', 0, 50, 0, 1),
(493, 54, '商务休闲鞋', 's', '商务休闲鞋', 's', '', '', '', '', 0, 50, 0, 1),
(494, 54, '定制鞋', 'd', '定制鞋', 'd', '', '', '', '', 0, 50, 0, 1),
(495, 54, '正装鞋', 'z', '正装鞋', 'z', '', '', '', '', 0, 50, 0, 1),
(496, 54, '男靴', 'n', '男靴', 'n', '', '', '', '', 0, 50, 0, 1),
(497, 54, '帆布鞋', 'f', '帆布鞋', 'f', '', '', '', '', 0, 50, 0, 1),
(498, 54, '传统布鞋', 'c', '传统布鞋', 'c', '', '', '', '', 0, 50, 0, 1),
(499, 54, '工装鞋', 'g', '工装鞋', 'g', '', '', '', '', 0, 50, 0, 1),
(500, 54, '功能鞋', 'g', '功能鞋', 'g', '', '', '', '', 0, 50, 0, 1),
(501, 55, '钥匙包', 'y', '钥匙包', 'y', '', '', '', '', 0, 50, 0, 1),
(502, 55, '单肩包', 'd', '单肩包', 'd', '', '', '', '', 0, 50, 0, 1),
(503, 55, '手提包', 's', '手提包', 's', '', '', '', '', 0, 50, 0, 1),
(504, 55, '斜挎包', 'x', '斜挎包', 'x', '', '', '', '', 0, 50, 0, 1),
(505, 55, '双肩包', 's', '双肩包', 's', '', '', '', '', 0, 50, 0, 1),
(506, 55, '钱包', 'q', '钱包', 'q', '', '', '', '', 0, 50, 0, 1),
(507, 55, '手拿包/晚宴包', 's', '手拿包/晚宴包', 's', '', '', '', '', 0, 50, 0, 1),
(508, 55, '卡包/零钱包', 'k', '卡包/零钱包', 'k', '', '', '', '', 0, 50, 0, 1),
(509, 56, '轮滑滑板', 'l', '轮滑滑板', 'l', '', '', '', '', 0, 50, 0, 1),
(510, 56, '网球', 'w', '网球', 'w', '', '', '', '', 0, 50, 0, 1),
(511, 56, '高尔夫', 'g', '高尔夫', 'g', '', '', '', '', 0, 50, 0, 1),
(512, 56, '台球', 't', '台球', 't', '', '', '', '', 0, 50, 0, 1),
(513, 56, '乒乓球', 'p', '乒乓球', 'p', '', '', '', '', 0, 50, 0, 1),
(514, 56, '排球', 'p', '排球', 'p', '', '', '', '', 0, 50, 0, 1),
(515, 56, '羽毛球', 'y', '羽毛球', 'y', '', '', '', '', 0, 50, 0, 1),
(516, 56, '棋牌麻将', 'q', '棋牌麻将', 'q', '', '', '', '', 0, 50, 0, 1),
(517, 56, '篮球', 'l', '篮球', 'l', '', '', '', '', 0, 50, 0, 1),
(518, 56, '其它', 'q', '其它', 'q', '', '', '', '', 0, 50, 0, 1),
(519, 56, '足球', 'z', '足球', 'z', '', '', '', '', 0, 50, 0, 1),
(520, 57, '速干衣裤', 's', '速干衣裤', 's', '', '', '', '', 0, 50, 0, 1),
(521, 57, '功能内衣', 'g', '功能内衣', 'g', '', '', '', '', 0, 50, 0, 1),
(522, 57, '溯溪鞋', 's', '溯溪鞋', 's', '', '', '', '', 0, 50, 0, 1),
(523, 57, '滑雪服', 'h', '滑雪服', 'h', '', '', '', '', 0, 50, 0, 1),
(524, 57, '军迷服饰', 'j', '军迷服饰', 'j', '', '', '', '', 0, 50, 0, 1),
(525, 57, '沙滩/凉拖', 's', '沙滩/凉拖', 's', '', '', '', '', 0, 50, 0, 1),
(526, 57, '羽绒服/棉服', 'y', '羽绒服/棉服', 'y', '', '', '', '', 0, 50, 0, 1),
(527, 57, '登山鞋', 'd', '登山鞋', 'd', '', '', '', '', 0, 50, 0, 1),
(528, 57, '户外袜', 'h', '户外袜', 'h', '', '', '', '', 0, 50, 0, 1),
(529, 57, '休闲衣裤', 'x', '休闲衣裤', 'x', '', '', '', '', 0, 50, 0, 1),
(530, 57, '徒步鞋', 't', '徒步鞋', 't', '', '', '', '', 0, 50, 0, 1),
(531, 57, '抓绒衣裤', 'z', '抓绒衣裤', 'z', '', '', '', '', 0, 50, 0, 1),
(532, 57, '越野跑鞋', 'y', '越野跑鞋', 'y', '', '', '', '', 0, 50, 0, 1),
(533, 57, '软壳衣裤', 'r', '软壳衣裤', 'r', '', '', '', '', 0, 50, 0, 1),
(534, 57, '休闲鞋', 'x', '休闲鞋', 'x', '', '', '', '', 0, 50, 0, 1),
(535, 57, 'T恤', 't', 'T恤', 't', '', '', '', '', 0, 50, 0, 1),
(536, 57, '雪地靴', 'x', '雪地靴', 'x', '', '', '', '', 0, 50, 0, 1),
(537, 57, '冲锋衣裤', 'c', '冲锋衣裤', 'c', '', '', '', '', 0, 50, 0, 1),
(538, 57, '户外风衣', 'h', '户外风衣', 'h', '', '', '', '', 0, 50, 0, 1),
(539, 57, '工装鞋', 'g', '工装鞋', 'g', '', '', '', '', 0, 50, 0, 1),
(540, 58, '野餐烧烤', 'y', '野餐烧烤', 'y', '', '', '', '', 0, 50, 0, 1),
(541, 58, '滑雪装备', 'h', '滑雪装备', 'h', '', '', '', '', 0, 50, 0, 1),
(542, 58, '便携桌椅床', 'b', '便携桌椅床', 'b', '', '', '', '', 0, 50, 0, 1),
(543, 58, '极限户外', 'j', '极限户外', 'j', '', '', '', '', 0, 50, 0, 1),
(544, 58, '户外工具', 'h', '户外工具', 'h', '', '', '', '', 0, 50, 0, 1),
(545, 58, '冲浪潜水', 'c', '冲浪潜水', 'c', '', '', '', '', 0, 50, 0, 1),
(546, 58, '背包', 'b', '背包', 'b', '', '', '', '', 0, 50, 0, 1),
(547, 58, '望远镜', 'w', '望远镜', 'w', '', '', '', '', 0, 50, 0, 1),
(548, 58, '户外配饰', 'h', '户外配饰', 'h', '', '', '', '', 0, 50, 0, 1),
(549, 58, '帐篷/垫子', 'z', '帐篷/垫子', 'z', '', '', '', '', 0, 50, 0, 1),
(550, 58, '户外仪表', 'h', '户外仪表', 'h', '', '', '', '', 0, 50, 0, 1),
(551, 58, '睡袋/吊床', 's', '睡袋/吊床', 's', '', '', '', '', 0, 50, 0, 1),
(552, 58, '旅游用品', 'l', '旅游用品', 'l', '', '', '', '', 0, 50, 0, 1),
(553, 58, '登山攀岩', 'd', '登山攀岩', 'd', '', '', '', '', 0, 50, 0, 1),
(554, 58, '军迷用品', 'j', '军迷用品', 'j', '', '', '', '', 0, 50, 0, 1),
(555, 58, '户外照明', 'h', '户外照明', 'h', '', '', '', '', 0, 50, 0, 1),
(556, 58, '救援装备', 'j', '救援装备', 'j', '', '', '', '', 0, 50, 0, 1),
(557, 59, '钓箱鱼包', 'd', '钓箱鱼包', 'd', '', '', '', '', 0, 50, 0, 1),
(558, 59, '其它', 'q', '其它', 'q', '', '', '', '', 0, 50, 0, 1),
(559, 59, '鱼竿鱼线', 'y', '鱼竿鱼线', 'y', '', '', '', '', 0, 50, 0, 1),
(560, 59, '浮漂鱼饵', 'f', '浮漂鱼饵', 'f', '', '', '', '', 0, 50, 0, 1),
(561, 59, '钓鱼桌椅', 'd', '钓鱼桌椅', 'd', '', '', '', '', 0, 50, 0, 1),
(562, 59, '钓鱼配件', 'd', '钓鱼配件', 'd', '', '', '', '', 0, 50, 0, 1),
(563, 60, '帆布鞋', 'f', '帆布鞋', 'f', '', '', '', '', 0, 50, 0, 1),
(564, 60, '乒羽网鞋', 'p', '乒羽网鞋', 'p', '', '', '', '', 0, 50, 0, 1),
(565, 60, '跑步鞋', 'p', '跑步鞋', 'p', '', '', '', '', 0, 50, 0, 1),
(566, 60, '训练鞋', 'x', '训练鞋', 'x', '', '', '', '', 0, 50, 0, 1),
(567, 60, '休闲鞋', 'x', '休闲鞋', 'x', '', '', '', '', 0, 50, 0, 1),
(568, 60, '专项运动鞋', 'z', '专项运动鞋', 'z', '', '', '', '', 0, 50, 0, 1),
(569, 60, '篮球鞋', 'l', '篮球鞋', 'l', '', '', '', '', 0, 50, 0, 1),
(570, 60, '拖鞋', 't', '拖鞋', 't', '', '', '', '', 0, 50, 0, 1),
(571, 60, '板鞋', 'b', '板鞋', 'b', '', '', '', '', 0, 50, 0, 1),
(572, 60, '运动包', 'y', '运动包', 'y', '', '', '', '', 0, 50, 0, 1),
(573, 60, '足球鞋', 'z', '足球鞋', 'z', '', '', '', '', 0, 50, 0, 1),
(574, 61, '其它', 'q', '其它', 'q', '', '', '', '', 0, 50, 0, 1),
(575, 61, '泳镜', 'y', '泳镜', 'y', '', '', '', '', 0, 50, 0, 1),
(576, 61, '泳帽', 'y', '泳帽', 'y', '', '', '', '', 0, 50, 0, 1),
(577, 61, '游泳包防水包', 'y', '游泳包防水包', 'y', '', '', '', '', 0, 50, 0, 1),
(578, 61, '男士泳衣', 'n', '男士泳衣', 'n', '', '', '', '', 0, 50, 0, 1),
(579, 61, '女士泳衣', 'n', '女士泳衣', 'n', '', '', '', '', 0, 50, 0, 1),
(580, 61, '比基尼', 'b', '比基尼', 'b', '', '', '', '', 0, 50, 0, 1),
(581, 62, 'T恤', 't', 'T恤', 't', '', '', '', '', 0, 50, 0, 1),
(582, 62, '棉服', 'm', '棉服', 'm', '', '', '', '', 0, 50, 0, 1),
(583, 62, '运动裤', 'y', '运动裤', 'y', '', '', '', '', 0, 50, 0, 1),
(584, 62, '夹克/风衣', 'j', '夹克/风衣', 'j', '', '', '', '', 0, 50, 0, 1),
(585, 62, '运动配饰', 'y', '运动配饰', 'y', '', '', '', '', 0, 50, 0, 1),
(586, 62, '运动背心', 'y', '运动背心', 'y', '', '', '', '', 0, 50, 0, 1),
(587, 62, '乒羽网服', 'p', '乒羽网服', 'p', '', '', '', '', 0, 50, 0, 1),
(588, 62, '运动套装', 'y', '运动套装', 'y', '', '', '', '', 0, 50, 0, 1),
(589, 62, '训练服', 'x', '训练服', 'x', '', '', '', '', 0, 50, 0, 1),
(590, 62, '羽绒服', 'y', '羽绒服', 'y', '', '', '', '', 0, 50, 0, 1),
(591, 62, '毛衫/线衫', 'm', '毛衫/线衫', 'm', '', '', '', '', 0, 50, 0, 1),
(592, 62, '卫衣/套头衫', 'w', '卫衣/套头衫', 'w', '', '', '', '', 0, 50, 0, 1),
(593, 63, '瑜伽舞蹈', 'w', '瑜伽舞蹈', 'w', '', '', '', '', 0, 50, 0, 1),
(594, 63, '跑步机', 'p', '跑步机', 'p', '', '', '', '', 0, 50, 0, 1),
(595, 63, '武术搏击', 'w', '武术搏击', 'w', '', '', '', '', 0, 50, 0, 1),
(596, 63, '健身车/动感单车', 'j', '健身车/动感单车', 'j', '', '', '', '', 0, 50, 0, 1),
(597, 63, '综合训练器', 'z', '综合训练器', 'z', '', '', '', '', 0, 50, 0, 1),
(598, 63, '哑铃', 'y', '哑铃', 'y', '', '', '', '', 0, 50, 0, 1),
(599, 63, '其他大型器械', 'q', '其他大型器械', 'q', '', '', '', '', 0, 50, 0, 1),
(600, 63, '仰卧板/收腹机', 'y', '仰卧板/收腹机', 'y', '', '', '', '', 0, 50, 0, 1),
(601, 63, '其他中小型器材', 'q', '其他中小型器材', 'q', '', '', '', '', 0, 50, 0, 1),
(602, 63, '甩脂机', 's', '甩脂机', 's', '', '', '', '', 0, 50, 0, 1),
(603, 63, '踏步机', 't', '踏步机', 't', '', '', '', '', 0, 50, 0, 1),
(604, 63, '运动护具', 'y', '运动护具', 'y', '', '', '', '', 0, 50, 0, 1),
(605, 64, '平衡车', 'p', '平衡车', 'p', '', '', '', '', 0, 50, 0, 1),
(606, 64, '其他整车', 'q', '其他整车', 'q', '', '', '', '', 0, 50, 0, 1),
(607, 64, '骑行装备', 'q', '骑行装备', 'q', '', '', '', '', 0, 50, 0, 1),
(608, 64, '骑行服', 'q', '骑行服', 'q', '', '', '', '', 0, 50, 0, 1),
(609, 64, '山地车/公路车', 's', '山地车/公路车', 's', '', '', '', '', 0, 50, 0, 1),
(610, 64, '折叠车', 'z', '折叠车', 'z', '', '', '', '', 0, 50, 0, 1),
(611, 64, '电动车', 'd', '电动车', 'd', '', '', '', '', 0, 50, 0, 1),
(612, 65, '电源', 'd', '电源', 'd', '', '', '', '', 0, 50, 0, 1),
(613, 65, '导航仪', 'd', '导航仪', 'd', '', '', '', '', 0, 50, 0, 1),
(614, 65, '智能驾驶', 'z', '智能驾驶', 'z', '', '', '', '', 0, 50, 0, 1),
(615, 65, '安全预警仪', 'a', '安全预警仪', 'a', '', '', '', '', 0, 50, 0, 1),
(616, 65, '车载电台', 'c', '车载电台', 'c', '', '', '', '', 0, 50, 0, 1),
(617, 65, '行车记录仪', 'x', '行车记录仪', 'x', '', '', '', '', 0, 50, 0, 1),
(618, 65, '吸尘器', 'x', '吸尘器', 'x', '', '', '', '', 0, 50, 0, 1),
(619, 65, '倒车雷达', 'd', '倒车雷达', 'd', '', '', '', '', 0, 50, 0, 1),
(620, 65, '冰箱', 'b', '冰箱', 'b', '', '', '', '', 0, 50, 0, 1),
(621, 65, '蓝牙设备', 'l', '蓝牙设备', 'l', '', '', '', '', 0, 50, 0, 1),
(622, 65, '时尚影音', 's', '时尚影音', 's', '', '', '', '', 0, 50, 0, 1),
(623, 65, '净化器', 'j', '净化器', 'j', '', '', '', '', 0, 50, 0, 1),
(624, 66, '清洁剂', 'q', '清洁剂', 'q', '', '', '', '', 0, 50, 0, 1),
(625, 66, '洗车工具', 'x', '洗车工具', 'x', '', '', '', '', 0, 50, 0, 1),
(626, 66, '洗车配件', 'x', '洗车配件', 'x', '', '', '', '', 0, 50, 0, 1),
(627, 66, '车蜡', 'c', '车蜡', 'c', '', '', '', '', 0, 50, 0, 1),
(628, 66, '补漆笔', 'b', '补漆笔', 'b', '', '', '', '', 0, 50, 0, 1),
(629, 66, '玻璃水', 'b', '玻璃水', 'b', '', '', '', '', 0, 50, 0, 1),
(630, 67, '香水', 'x', '香水', 'x', '', '', '', '', 0, 50, 0, 1),
(631, 67, '空气净化', 'k', '空气净化', 'k', '', '', '', '', 0, 50, 0, 1),
(632, 67, '车内饰品', 'c', '车内饰品', 'c', '', '', '', '', 0, 50, 0, 1),
(633, 67, '脚垫', 'j', '脚垫', 'j', '', '', '', '', 0, 50, 0, 1),
(634, 67, '功能小件', 'g', '功能小件', 'g', '', '', '', '', 0, 50, 0, 1),
(635, 67, '座垫', 'z', '座垫', 'z', '', '', '', '', 0, 50, 0, 1),
(636, 67, '车身装饰件', 'c', '车身装饰件', 'c', '', '', '', '', 0, 50, 0, 1),
(637, 67, '座套', 'z', '座套', 'z', '', '', '', '', 0, 50, 0, 1),
(638, 67, '车衣', 'c', '车衣', 'c', '', '', '', '', 0, 50, 0, 1),
(639, 67, '后备箱垫', 'h', '后备箱垫', 'h', '', '', '', '', 0, 50, 0, 1),
(640, 67, '头枕腰靠', 't', '头枕腰靠', 't', '', '', '', '', 0, 50, 0, 1),
(641, 68, '充气泵', 'c', '充气泵', 'c', '', '', '', '', 0, 50, 0, 1),
(642, 68, '防盗设备', 'f', '防盗设备', 'f', '', '', '', '', 0, 50, 0, 1),
(643, 68, '应急救援', 'y', '应急救援', 'y', '', '', '', '', 0, 50, 0, 1),
(644, 68, '保温箱', 'b', '保温箱', 'b', '', '', '', '', 0, 50, 0, 1),
(645, 68, '储物箱', 'c', '储物箱', 'c', '', '', '', '', 0, 50, 0, 1),
(646, 68, '自驾野营', 'z', '自驾野营', 'z', '', '', '', '', 0, 50, 0, 1),
(647, 68, '安全座椅', 'a', '安全座椅', 'a', '', '', '', '', 0, 50, 0, 1),
(648, 68, '摩托车装备', 'm', '摩托车装备', 'm', '', '', '', '', 0, 50, 0, 1),
(649, 68, '胎压监测', 't', '胎压监测', 't', '', '', '', '', 0, 50, 0, 1),
(650, 69, '功能升级服务', 'g', '功能升级服务', 'g', '', '', '', '', 0, 50, 0, 1),
(651, 69, '保养维修服务', 'b', '保养维修服务', 'b', '', '', '', '', 0, 50, 0, 1),
(652, 69, '清洗美容服务', 'q', '清洗美容服务', 'q', '', '', '', '', 0, 50, 0, 1),
(653, 70, '跑车', 'p', '跑车', 'p', '', '', '', '', 0, 50, 0, 1),
(654, 70, '微型车', 'w', '微型车', 'w', '', '', '', '', 0, 50, 0, 1),
(655, 70, '小型车', 'x', '小型车', 'x', '', '', '', '', 0, 50, 0, 1),
(656, 70, '紧凑型车', 'j', '紧凑型车', 'j', '', '', '', '', 0, 50, 0, 1),
(657, 70, '中型车', 'z', '中型车', 'z', '', '', '', '', 0, 50, 0, 1),
(658, 70, '中大型车', 'z', '中大型车', 'z', '', '', '', '', 0, 50, 0, 1),
(659, 70, '豪华车', 'h', '豪华车', 'h', '', '', '', '', 0, 50, 0, 1),
(660, 70, 'MPV', 'm', 'MPV', 'm', '', '', '', '', 0, 50, 0, 1),
(661, 70, 'SUV', 's', 'SUV', 's', '', '', '', '', 0, 50, 0, 1),
(662, 71, '上海大众', 's', '上海大众', 's', '', '', '', '', 0, 50, 0, 1),
(663, 71, '斯柯达', 's', '斯柯达', 's', '', '', '', '', 0, 50, 0, 1),
(664, 71, '东风雪铁龙', 'd', '东风雪铁龙', 'd', '', '', '', '', 0, 50, 0, 1),
(665, 71, '一汽奔腾', 'y', '一汽奔腾', 'y', '', '', '', '', 0, 50, 0, 1),
(666, 71, '北汽新能源', 'b', '北汽新能源', 'b', '', '', '', '', 0, 50, 0, 1),
(667, 71, '陆风', 'l', '陆风', 'l', '', '', '', '', 0, 50, 0, 1),
(668, 71, '海马', 'h', '海马', 'h', '', '', '', '', 0, 50, 0, 1),
(669, 72, '润滑油', 'r', '润滑油', 'r', '', '', '', '', 0, 50, 0, 1),
(670, 72, '轮胎', 'l', '轮胎', 'l', '', '', '', '', 0, 50, 0, 1),
(671, 72, '改装配件', 'g', '改装配件', 'g', '', '', '', '', 0, 50, 0, 1),
(672, 72, '添加剂', 't', '添加剂', 't', '', '', '', '', 0, 50, 0, 1),
(673, 72, '轮毂', 'l', '轮毂', 'l', '', '', '', '', 0, 50, 0, 1),
(674, 72, '防冻液', 'f', '防冻液', 'f', '', '', '', '', 0, 50, 0, 1),
(675, 72, '刹车片/盘', 's', '刹车片/盘', 's', '', '', '', '', 0, 50, 0, 1),
(676, 72, '滤清器', 'l', '滤清器', 'l', '', '', '', '', 0, 50, 0, 1),
(677, 72, '维修配件', 'w', '维修配件', 'w', '', '', '', '', 0, 50, 0, 1),
(678, 72, '火花塞', 'h', '火花塞', 'h', '', '', '', '', 0, 50, 0, 1),
(679, 72, '蓄电池', 'x', '蓄电池', 'x', '', '', '', '', 0, 50, 0, 1),
(680, 72, '雨刷', 'y', '雨刷', 'y', '', '', '', '', 0, 50, 0, 1),
(681, 72, '底盘装甲/护板', 'd', '底盘装甲/护板', 'd', '', '', '', '', 0, 50, 0, 1),
(682, 72, '车灯', 'c', '车灯', 'c', '', '', '', '', 0, 50, 0, 1),
(683, 72, '贴膜', 't', '贴膜', 't', '', '', '', '', 0, 50, 0, 1),
(684, 72, '后视镜', 'h', '后视镜', 'h', '', '', '', '', 0, 50, 0, 1),
(685, 72, '汽修工具', 'q', '汽修工具', 'q', '', '', '', '', 0, 50, 0, 1),
(686, 73, '宝宝护肤', 'b', '宝宝护肤', 'b', '', '', '', '', 0, 50, 0, 1),
(687, 73, '宝宝洗浴', 'b', '宝宝洗浴', 'b', '', '', '', '', 0, 50, 0, 1),
(688, 73, '理发器', 'l', '理发器', 'l', '', '', '', '', 0, 50, 0, 1),
(689, 73, '洗衣液/皂', 'x', '洗衣液/皂', 'x', '', '', '', '', 0, 50, 0, 1),
(690, 73, '奶瓶清洗', 'n', '奶瓶清洗', 'n', '', '', '', '', 0, 50, 0, 1),
(691, 73, '日常护理', 'r', '日常护理', 'r', '', '', '', '', 0, 50, 0, 1),
(692, 73, '座便器', 'z', '座便器', 'z', '', '', '', '', 0, 50, 0, 1),
(693, 73, '驱蚊防蚊', 'q', '驱蚊防蚊', 'q', '', '', '', '', 0, 50, 0, 1),
(694, 74, '奶瓶奶嘴', 'n', '奶瓶奶嘴', 'n', '', '', '', '', 0, 50, 0, 1),
(695, 74, '吸奶器', 'x', '吸奶器', 'x', '', '', '', '', 0, 50, 0, 1),
(696, 74, '牙胶安抚', 'y', '牙胶安抚', 'y', '', '', '', '', 0, 50, 0, 1),
(697, 74, '暖奶消毒', 'n', '暖奶消毒', 'n', '', '', '', '', 0, 50, 0, 1),
(698, 74, '辅食料理机', 'f', '辅食料理机', 'f', '', '', '', '', 0, 50, 0, 1),
(699, 74, '碗盘叉勺', 'w', '碗盘叉勺', 'w', '', '', '', '', 0, 50, 0, 1),
(700, 74, '水壶/水杯', 's', '水壶/水杯', 's', '', '', '', '', 0, 50, 0, 1),
(701, 75, '婴儿推车', 'y', '婴儿推车', 'y', '', '', '', '', 0, 50, 0, 1),
(702, 75, '餐椅摇椅', 'c', '餐椅摇椅', 'c', '', '', '', '', 0, 50, 0, 1),
(703, 75, '学步车', 'x', '学步车', 'x', '', '', '', '', 0, 50, 0, 1),
(704, 75, '三轮车', 's', '三轮车', 's', '', '', '', '', 0, 50, 0, 1),
(705, 75, '自行车', 'z', '自行车', 'z', '', '', '', '', 0, 50, 0, 1),
(706, 75, '扭扭车', 'n', '扭扭车', 'n', '', '', '', '', 0, 50, 0, 1),
(707, 75, '滑板车', 'h', '滑板车', 'h', '', '', '', '', 0, 50, 0, 1),
(708, 75, '婴儿床', 'y', '婴儿床', 'y', '', '', '', '', 0, 50, 0, 1),
(709, 75, '电动车', 'd', '电动车', 'd', '', '', '', '', 0, 50, 0, 1),
(710, 76, '提篮式', 't', '提篮式', 't', '', '', '', '', 0, 50, 0, 1),
(711, 76, '安全座椅', 'a', '安全座椅', 'a', '', '', '', '', 0, 50, 0, 1),
(712, 76, '增高垫', 'z', '增高垫', 'z', '', '', '', '', 0, 50, 0, 1),
(713, 77, '安全防护', 'a', '安全防护', 'a', '', '', '', '', 0, 50, 0, 1),
(714, 77, '婴儿外出服', 'y', '婴儿外出服', 'y', '', '', '', '', 0, 50, 0, 1),
(715, 77, '婴儿内衣', 'y', '婴儿内衣', 'y', '', '', '', '', 0, 50, 0, 1),
(716, 77, '婴儿礼盒', 'y', '婴儿礼盒', 'y', '', '', '', '', 0, 50, 0, 1),
(717, 77, '婴儿鞋帽袜', 'y', '婴儿鞋帽袜', 'y', '', '', '', '', 0, 50, 0, 1),
(718, 77, '家居床品', 'j', '家居床品', 'j', '', '', '', '', 0, 50, 0, 1),
(719, 78, '婴幼奶粉', 'y', '婴幼奶粉', 'y', '', '', '', '', 0, 50, 0, 1),
(720, 78, '成人奶粉', 'c', '成人奶粉', 'c', '', '', '', '', 0, 50, 0, 1),
(721, 79, '待产/新生', 'd', '待产/新生', 'd', '', '', '', '', 0, 50, 0, 1),
(722, 79, '孕妇装', 'y', '孕妇装', 'y', '', '', '', '', 0, 50, 0, 1),
(723, 79, '孕期营养', 'y', '孕期营养', 'y', '', '', '', '', 0, 50, 0, 1),
(724, 79, '防辐射服', 'f', '防辐射服', 'f', '', '', '', '', 0, 50, 0, 1),
(725, 79, '妈咪包/背婴带', 'm', '妈咪包/背婴带', 'm', '', '', '', '', 0, 50, 0, 1),
(726, 79, '产后塑身', 'c', '产后塑身', 'c', '', '', '', '', 0, 50, 0, 1),
(727, 79, '孕妈美容', 'y', '孕妈美容', 'y', '', '', '', '', 0, 50, 0, 1),
(728, 79, '文胸/内裤', 'w', '文胸/内裤', 'w', '', '', '', '', 0, 50, 0, 1),
(729, 79, '月子装', 'y', '月子装', 'y', '', '', '', '', 0, 50, 0, 1),
(730, 80, '米粉/菜粉', 'm', '米粉/菜粉', 'm', '', '', '', '', 0, 50, 0, 1),
(731, 80, '果泥/果汁', 'g', '果泥/果汁', 'g', '', '', '', '', 0, 50, 0, 1),
(732, 80, '面条/粥', 'm', '面条/粥', 'm', '', '', '', '', 0, 50, 0, 1),
(733, 80, '宝宝零食', 'b', '宝宝零食', 'b', '', '', '', '', 0, 50, 0, 1),
(734, 80, 'DHA', 'd', 'DHA', 'd', '', '', '', '', 0, 50, 0, 1),
(735, 80, '钙铁锌/维生素', 'g', '钙铁锌/维生素', 'g', '', '', '', '', 0, 50, 0, 1),
(736, 80, '益生菌/初乳', 'y', '益生菌/初乳', 'y', '', '', '', '', 0, 50, 0, 1),
(737, 80, '清火/开胃', 'q', '清火/开胃', 'q', '', '', '', '', 0, 50, 0, 1),
(738, 81, '配饰', 'p', '配饰', 'p', '', '', '', '', 0, 50, 0, 1),
(739, 81, '亲子装', 'q', '亲子装', 'q', '', '', '', '', 0, 50, 0, 1),
(740, 81, '羽绒服/棉服', 'y', '羽绒服/棉服', 'y', '', '', '', '', 0, 50, 0, 1),
(741, 81, '套装', 't', '套装', 't', '', '', '', '', 0, 50, 0, 1),
(742, 81, '运动服', 'y', '运动服', 'y', '', '', '', '', 0, 50, 0, 1),
(743, 81, '上衣', 's', '上衣', 's', '', '', '', '', 0, 50, 0, 1),
(744, 81, '靴子', 'x', '靴子', 'x', '', '', '', '', 0, 50, 0, 1),
(745, 81, '运动鞋', 'y', '运动鞋', 'y', '', '', '', '', 0, 50, 0, 1),
(746, 81, '演出服', 'y', '演出服', 'y', '', '', '', '', 0, 50, 0, 1),
(747, 81, '裙子', 'q', '裙子', 'q', '', '', '', '', 0, 50, 0, 1),
(748, 81, '裤子', 'k', '裤子', 'k', '', '', '', '', 0, 50, 0, 1),
(749, 81, '功能鞋', 'g', '功能鞋', 'g', '', '', '', '', 0, 50, 0, 1),
(750, 81, '内衣', 'n', '内衣', 'n', '', '', '', '', 0, 50, 0, 1),
(751, 81, '凉鞋', 'l', '凉鞋', 'l', '', '', '', '', 0, 50, 0, 1),
(752, 81, '皮鞋/帆布鞋', 'p', '皮鞋/帆布鞋', 'p', '', '', '', '', 0, 50, 0, 1),
(753, 82, '婴儿尿裤', 'y', '婴儿尿裤', 'y', '', '', '', '', 0, 50, 0, 1),
(754, 82, '拉拉裤', 'l', '拉拉裤', 'l', '', '', '', '', 0, 50, 0, 1),
(755, 82, '成人尿裤', 'c', '成人尿裤', 'c', '', '', '', '', 0, 50, 0, 1),
(756, 82, '湿巾', 's', '湿巾', 's', '', '', '', '', 0, 50, 0, 1),
(757, 83, '健身玩具', 'j', '健身玩具', 'j', '', '', '', '', 0, 50, 0, 1),
(758, 83, '适用年龄', 's', '适用年龄', 's', '', '', '', '', 0, 50, 0, 1),
(759, 83, '娃娃玩具', 'w', '娃娃玩具', 'w', '', '', '', '', 0, 50, 0, 1),
(760, 83, '遥控/电动', 'y', '遥控/电动', 'y', '', '', '', '', 0, 50, 0, 1),
(761, 83, 'DIY玩具', 'd', 'DIY玩具', 'd', '', '', '', '', 0, 50, 0, 1),
(762, 83, '益智玩具', 'y', '益智玩具', 'y', '', '', '', '', 0, 50, 0, 1),
(763, 83, '创意减压', 'c', '创意减压', 'c', '', '', '', '', 0, 50, 0, 1),
(764, 83, '积木拼插', 'j', '积木拼插', 'j', '', '', '', '', 0, 50, 0, 1),
(765, 83, '乐器相关', 'l', '乐器相关', 'l', '', '', '', '', 0, 50, 0, 1),
(766, 83, '动漫玩具', 'd', '动漫玩具', 'd', '', '', '', '', 0, 50, 0, 1),
(767, 83, '毛绒布艺', 'm', '毛绒布艺', 'm', '', '', '', '', 0, 50, 0, 1),
(768, 83, '模型玩具', 'm', '模型玩具', 'm', '', '', '', '', 0, 50, 0, 1),
(769, 84, '游戏', 'y', '游戏', 'y', '', '', '', '', 0, 50, 0, 1),
(770, 84, '影视/动漫周边', 'y', '影视/动漫周边', 'y', '', '', '', '', 0, 50, 0, 1),
(771, 84, '音乐', 'y', '音乐', 'y', '', '', '', '', 0, 50, 0, 1),
(772, 84, '影视', 'y', '影视', 'y', '', '', '', '', 0, 50, 0, 1),
(773, 84, '教育音像', 'j', '教育音像', 'j', '', '', '', '', 0, 50, 0, 1),
(774, 85, '港台图书', 'g', '港台图书', 'g', '', '', '', '', 0, 50, 0, 1),
(775, 85, '杂志/期刊', 'z', '杂志/期刊', 'z', '', '', '', '', 0, 50, 0, 1),
(776, 85, '英文原版书', 'y', '英文原版书', 'y', '', '', '', '', 0, 50, 0, 1),
(777, 86, '科普', 'k', '科普', 'k', '', '', '', '', 0, 50, 0, 1),
(778, 86, '幼儿启蒙', 'y', '幼儿启蒙', 'y', '', '', '', '', 0, 50, 0, 1),
(779, 86, '0-2岁', '0', '0-2岁', '0', '', '', '', '', 0, 50, 0, 1),
(780, 86, '手工游戏', 's', '手工游戏', 's', '', '', '', '', 0, 50, 0, 1),
(781, 86, '3-6岁', '3', '3-6岁', '3', '', '', '', '', 0, 50, 0, 1),
(782, 86, '智力开发', 'z', '智力开发', 'z', '', '', '', '', 0, 50, 0, 1),
(783, 86, '7-10岁', '7', '7-10岁', '7', '', '', '', '', 0, 50, 0, 1),
(784, 86, '11-14岁', '1', '11-14岁', '1', '', '', '', '', 0, 50, 0, 1),
(785, 86, '儿童文学', 'e', '儿童文学', 'e', '', '', '', '', 0, 50, 0, 1),
(786, 86, '绘本', 'h', '绘本', 'h', '', '', '', '', 0, 50, 0, 1),
(787, 87, '外文原版', 'w', '外文原版', 'w', '', '', '', '', 0, 50, 0, 1),
(788, 87, '畅读VIP', 'c', '畅读VIP', 'c', '', '', '', '', 0, 50, 0, 1),
(789, 87, '免费', 'm', '免费', 'm', '', '', '', '', 0, 50, 0, 1),
(790, 87, '小说', 'x', '小说', 'x', '', '', '', '', 0, 50, 0, 1);
INSERT INTO `cs_goods_category` (`goods_category_id`, `parent_id`, `name`, `name_phonetic`, `alias`, `alias_phonetic`, `category_pic`, `category_ico`, `keywords`, `description`, `category_type`, `sort`, `is_navi`, `status`) VALUES
(791, 87, '励志与成功', 'l', '励志与成功', 'l', '', '', '', '', 0, 50, 0, 1),
(792, 87, '经济金融', 'j', '经济金融', 'j', '', '', '', '', 0, 50, 0, 1),
(793, 87, '文学', 'w', '文学', 'w', '', '', '', '', 0, 50, 0, 1),
(794, 87, '社科', 's', '社科', 's', '', '', '', '', 0, 50, 0, 1),
(795, 87, '婚恋两性', 'h', '婚恋两性', 'h', '', '', '', '', 0, 50, 0, 1),
(796, 88, '字典词典', 'z', '字典词典', 'z', '', '', '', '', 0, 50, 0, 1),
(797, 88, '教材', 'j', '教材', 'j', '', '', '', '', 0, 50, 0, 1),
(798, 88, '中小学教辅', 'z', '中小学教辅', 'z', '', '', '', '', 0, 50, 0, 1),
(799, 88, '考试', 'k', '考试', 'k', '', '', '', '', 0, 50, 0, 1),
(800, 88, '外语学习', 'w', '外语学习', 'w', '', '', '', '', 0, 50, 0, 1),
(801, 89, '通俗流行', 't', '通俗流行', 't', '', '', '', '', 0, 50, 0, 1),
(802, 89, '古典音乐', 'g', '古典音乐', 'g', '', '', '', '', 0, 50, 0, 1),
(803, 89, '摇滚说唱', 'y', '摇滚说唱', 'y', '', '', '', '', 0, 50, 0, 1),
(804, 89, '爵士蓝调', 'j', '爵士蓝调', 'j', '', '', '', '', 0, 50, 0, 1),
(805, 89, '乡村民谣', 'x', '乡村民谣', 'x', '', '', '', '', 0, 50, 0, 1),
(806, 89, '有声读物', 'y', '有声读物', 'y', '', '', '', '', 0, 50, 0, 1),
(807, 90, '小说', 'x', '小说', 'x', '', '', '', '', 0, 50, 0, 1),
(808, 90, '文学', 'w', '文学', 'w', '', '', '', '', 0, 50, 0, 1),
(809, 90, '青春文学', 'q', '青春文学', 'q', '', '', '', '', 0, 50, 0, 1),
(810, 90, '传记', 'c', '传记', 'c', '', '', '', '', 0, 50, 0, 1),
(811, 90, '动漫', 'd', '动漫', 'd', '', '', '', '', 0, 50, 0, 1),
(812, 90, '艺术', 'y', '艺术', 'y', '', '', '', '', 0, 50, 0, 1),
(813, 90, '摄影', 's', '摄影', 's', '', '', '', '', 0, 50, 0, 1),
(814, 91, '管理', 'g', '管理', 'g', '', '', '', '', 0, 50, 0, 1),
(815, 91, '金融与投资', 'j', '金融与投资', 'j', '', '', '', '', 0, 50, 0, 1),
(816, 91, '经济', 'j', '经济', 'j', '', '', '', '', 0, 50, 0, 1),
(817, 91, '励志与成功', 'l', '励志与成功', 'l', '', '', '', '', 0, 50, 0, 1),
(818, 92, '哲学/宗教', 'z', '哲学/宗教', 'z', '', '', '', '', 0, 50, 0, 1),
(819, 92, '社会科学', 's', '社会科学', 's', '', '', '', '', 0, 50, 0, 1),
(820, 92, '法律', 'f', '法律', 'f', '', '', '', '', 0, 50, 0, 1),
(821, 92, '文化', 'w', '文化', 'w', '', '', '', '', 0, 50, 0, 1),
(822, 92, '历史', 'l', '历史', 'l', '', '', '', '', 0, 50, 0, 1),
(823, 92, '心理学', 'x', '心理学', 'x', '', '', '', '', 0, 50, 0, 1),
(824, 92, '政治/军事', 'z', '政治/军事', 'z', '', '', '', '', 0, 50, 0, 1),
(825, 92, '国学/古籍', 'g', '国学/古籍', 'g', '', '', '', '', 0, 50, 0, 1),
(826, 93, '美食', 'm', '美食', 'm', '', '', '', '', 0, 50, 0, 1),
(827, 93, '时尚美妆', 's', '时尚美妆', 's', '', '', '', '', 0, 50, 0, 1),
(828, 93, '家居', 'j', '家居', 'j', '', '', '', '', 0, 50, 0, 1),
(829, 93, '手工DIY', 's', '手工DIY', 's', '', '', '', '', 0, 50, 0, 1),
(830, 93, '家教与育儿', 'j', '家教与育儿', 'j', '', '', '', '', 0, 50, 0, 1),
(831, 93, '两性', 'l', '两性', 'l', '', '', '', '', 0, 50, 0, 1),
(832, 93, '孕产', 'y', '孕产', 'y', '', '', '', '', 0, 50, 0, 1),
(833, 93, '体育', 't', '体育', 't', '', '', '', '', 0, 50, 0, 1),
(834, 93, '健身保健', 'j', '健身保健', 'j', '', '', '', '', 0, 50, 0, 1),
(835, 93, '旅游/地图', 'l', '旅游/地图', 'l', '', '', '', '', 0, 50, 0, 1),
(836, 94, '建筑', 'j', '建筑', 'j', '', '', '', '', 0, 50, 0, 1),
(837, 94, '工业技术', 'g', '工业技术', 'g', '', '', '', '', 0, 50, 0, 1),
(838, 94, '电子/通信', 'd', '电子/通信', 'd', '', '', '', '', 0, 50, 0, 1),
(839, 94, '医学', 'y', '医学', 'y', '', '', '', '', 0, 50, 0, 1),
(840, 94, '科学与自然', 'k', '科学与自然', 'k', '', '', '', '', 0, 50, 0, 1),
(841, 94, '农林', 'n', '农林', 'n', '', '', '', '', 0, 50, 0, 1),
(842, 94, '计算机与互联网', 'j', '计算机与互联网', 'j', '', '', '', '', 0, 50, 0, 1),
(843, 94, '科普', 'k', '科普', 'k', '', '', '', '', 0, 50, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_comment`
--

CREATE TABLE `cs_goods_comment` (
  `goods_comment_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id(回复) ',
  `goods_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods表',
  `order_goods_id` int(11) UNSIGNED NOT NULL COMMENT '对应order_goods表',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '对应order表',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '对应user表',
  `is_anon` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否匿名 0=否 1=是',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=主评 1=主回 2=追评 3=追回',
  `content` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '晒图',
  `is_image` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=无图 1=有图',
  `is_append` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=无追评 1=有追评',
  `score` tinyint(1) NOT NULL DEFAULT '1' COMMENT '评分 1~5',
  `praise` smallint(5) NOT NULL DEFAULT '0' COMMENT '点赞',
  `reply_count` smallint(5) NOT NULL DEFAULT '0' COMMENT '回复数',
  `ip_address` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ip地址',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=否 1=是',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶 0=否 1=是',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未读 1=已读',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品评价';

--
-- 插入之前先把表清空（truncate） `cs_goods_comment`
--

TRUNCATE TABLE `cs_goods_comment`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_consult`
--

CREATE TABLE `cs_goods_consult` (
  `goods_consult_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id(回复)',
  `goods_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods表',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表 0=游客',
  `is_anon` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否匿名 0=否 1=是',
  `type` tinyint(3) NOT NULL COMMENT '类型(自定义)',
  `content` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示 0=否 1=是',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=待回复 1=已回复',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品咨询';

--
-- 插入之前先把表清空（truncate） `cs_goods_consult`
--

TRUNCATE TABLE `cs_goods_consult`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_reply`
--

CREATE TABLE `cs_goods_reply` (
  `goods_reply_id` int(11) UNSIGNED NOT NULL,
  `goods_comment_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods_comment表',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '对应user表',
  `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回复者昵称',
  `to_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回复谁',
  `content` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品评价回复';

--
-- 插入之前先把表清空（truncate） `cs_goods_reply`
--

TRUNCATE TABLE `cs_goods_reply`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_goods_type`
--

CREATE TABLE `cs_goods_type` (
  `goods_type_id` smallint(5) UNSIGNED NOT NULL,
  `type_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品模型';

--
-- 插入之前先把表清空（truncate） `cs_goods_type`
--

TRUNCATE TABLE `cs_goods_type`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_help`
--

CREATE TABLE `cs_help` (
  `help_id` int(11) UNSIGNED NOT NULL,
  `router` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路由',
  `ver` char(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本号',
  `module` char(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属模块',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '外部链接'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `cs_help`
--

TRUNCATE TABLE `cs_help`;
--
-- 转存表中的数据 `cs_help`
--

INSERT INTO `cs_help` (`help_id`, `router`, `ver`, `module`, `content`, `url`) VALUES
(1, '/marketing/card/list', '1.0.1', 'admin', '<span>1、购物卡&ldquo;禁用&rdquo;后，未绑定但已生成的购物卡将无法绑定</span></br>\r\n<span>2、购物卡&ldquo;禁用&rdquo;后，已绑定的购物卡不影响消费使用</span></br>\r\n<span>3、删除购物卡后未绑定的将无法绑定，但已绑定的不影响消费</span></br>\r\n<span>4、删除购物卡将失去对已生成卡的管理</span>', ''),
(2, '/marketing/card/use', '1.0.1', 'admin', '<p>购物卡生成，客户绑定后以下几种情况将影响消费：</p>\r\n<span>1、生成的购物卡被禁用</span></br>\r\n<span>2、可用余额不足</span></br>\r\n<span>3、截至日期已到期</span></br>\r\n<span>4、生成的购物卡未激活</span></br>\r\n<span>5、卡密默认不可见，可在\"<a href=\"./#/setting/setting/system\">系统管理</a>\"中添加可见名单</span>', ''),
(3, '/system/auth/menu', '1.0.1', 'admin', '<span>任何操作完成后涉及到的账号都需要注销，重新登录才会生效</span>', ''),
(4, '/system/auth/rule', '1.0.1', 'admin', '<span>任何操作完成后涉及到的账号都需要注销，重新登录才会生效</span>', ''),
(5, '/setting/logistics/delivery', '1.0.1', 'admin', '<span>&ldquo;费用设置&rdquo;将作为 <strong>配送区域 </strong>中的基础费用，如果 <strong>配送区域 </strong>中的区域未单独设置运费，那么此设置将作为计算值。</span>', ''),
(6, '/member/withdraw/list', '1.0.1', 'admin', '<span>&ldquo;提现金额&rdquo;为实际需打款给对方的金额</span>', ''),
(7, '/goods/setting/brand', '1.0.1', 'admin', '<span>1、不同的分类下允许品牌名称重复</span></br>\r\n<span>2、分类搜索品牌时，如果子分类下的品牌条件符合也会出现结果</span>', ''),
(9, '/marketing/coupon/list', '1.0.1', 'admin', '<span>1、优惠劵&ldquo;禁用(或删除)&rdquo;后，将不再发放优惠劵</span></br> <span>2、优惠劵&ldquo;禁用(或删除)&rdquo;后，已发放的不影响使用</span></br> <span>3、优惠劵&ldquo;作废&rdquo;后，将不再发放优惠劵</span></br> <span>4、优惠劵&ldquo;作废&rdquo;后，已发放的也将不可使用</span></br> <span>5、删除优惠劵将失去对已发放优惠劵的管理</span></br> <span>6、提示发放数不足时可通过编辑&ldquo;发放数&rdquo;数量进行修改</span>', ''),
(10, '/marketing/coupon/give', '1.0.1', 'admin', '<span>1、管理员删除优惠劵时对&ldquo;已发放未使用&rdquo;的优惠劵会永久删除</span></br> <span>2、管理员删除优惠劵时对&ldquo;已使用&rdquo;的优惠劵会放入&ldquo;回收站&rdquo;</span></br> <span>3、顾客删除优惠劵时无论是否使用都会放入&ldquo;回收站&rdquo;</span></br> <span>4、回收站里的优惠劵都可以&ldquo;恢复&rdquo;</span></br></br> <b>以下几种情况将视优惠劵无效：</b></br> <span>1、优惠劵已作废</span></br> <span>2、使用截至日期已到期</span>', '');

-- --------------------------------------------------------

--
-- 表的结构 `cs_history`
--

CREATE TABLE `cs_history` (
  `history_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应goods表',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='浏览历史';

--
-- 插入之前先把表清空（truncate） `cs_history`
--

TRUNCATE TABLE `cs_history`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_menu`
--

CREATE TABLE `cs_menu` (
  `menu_id` smallint(5) UNSIGNED NOT NULL,
  `parent_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `alias` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `icon` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `module` char(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属模块',
  `type` tinyint(1) NOT NULL COMMENT '0=模块 1=外链',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '参数',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '_self _blank',
  `is_navi` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航 0=否 1=是',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='菜单管理';

--
-- 插入之前先把表清空（truncate） `cs_menu`
--

TRUNCATE TABLE `cs_menu`;
--
-- 转存表中的数据 `cs_menu`
--

INSERT INTO `cs_menu` (`menu_id`, `parent_id`, `name`, `alias`, `icon`, `remark`, `module`, `type`, `url`, `params`, `target`, `is_navi`, `sort`, `status`) VALUES
(1, 0, '管理组账户', '', '', '', 'api', 0, '', '', '_self', 0, 1, 1),
(2, 1, '验证账号是否合法', '', '', '', 'api', 0, 'api/v1/admin/check.admin.username', '', '_self', 0, 1, 1),
(3, 1, '验证账号昵称是否合法', '', '', '', 'api', 0, 'api/v1/admin/check.admin.nickname', '', '_self', 0, 2, 1),
(4, 1, '添加一个账号', '', '', '', 'api', 0, 'api/v1/admin/add.admin.item', '', '_self', 0, 3, 1),
(5, 1, '编辑一个账号', '', '', '', 'api', 0, 'api/v1/admin/set.admin.item', '', '_self', 0, 4, 1),
(6, 1, '批量设置账号状态', '', '', '', 'api', 0, 'api/v1/admin/set.admin.status', '', '_self', 0, 5, 1),
(7, 1, '修改一个账号密码', '', '', '', 'api', 0, 'api/v1/admin/set.admin.password', '', '_self', 0, 6, 1),
(8, 1, '重置一个账号密码', '', '', '', 'api', 0, 'api/v1/admin/reset.admin.item', '', '_self', 0, 7, 1),
(9, 1, '批量删除账号', '', '', '', 'api', 0, 'api/v1/admin/del.admin.list', '', '_self', 0, 8, 1),
(10, 1, '获取一个账号', '', '', '', 'api', 0, 'api/v1/admin/get.admin.item', '', '_self', 0, 9, 1),
(11, 1, '获取账号列表', '', '', '', 'api', 0, 'api/v1/admin/get.admin.list', '', '_self', 0, 10, 1),
(12, 1, '注销账号', '', '', '', 'api', 0, 'api/v1/admin/logout.admin.user', '', '_self', 0, 12, 1),
(13, 1, '登录账号', '', '', '', 'api', 0, 'api/v1/admin/login.admin.user', '', '_self', 0, 13, 1),
(14, 1, '刷新Token', '', '', '', 'api', 0, 'api/v1/admin/refresh.admin.token', '', '_self', 0, 14, 1),
(15, 0, '广告管理', '', '', '', 'api', 0, '', '', '_self', 0, 2, 1),
(16, 15, '添加一个广告', '', '', '', 'api', 0, 'api/v1/ads/add.ads.item', '', '_self', 0, 50, 1),
(17, 15, '编辑一个广告', '', '', '', 'api', 0, 'api/v1/ads/set.ads.item', '', '_self', 0, 50, 1),
(18, 15, '批量删除广告', '', '', '', 'api', 0, 'api/v1/ads/del.ads.list', '', '_self', 0, 50, 1),
(19, 15, '设置广告排序', '', '', '', 'api', 0, 'api/v1/ads/set.ads.sort', '', '_self', 0, 50, 1),
(20, 15, '批量设置广告是否显示', '', '', '', 'api', 0, 'api/v1/ads/set.ads.status', '', '_self', 0, 50, 1),
(21, 15, '获取一个广告', '', '', '', 'api', 0, 'api/v1/ads/get.ads.item', '', '_self', 0, 50, 1),
(22, 15, '获取广告列表', '', '', '', 'api', 0, 'api/v1/ads/get.ads.list', '', '_self', 0, 50, 1),
(23, 0, '广告位置', '', '', '', 'api', 0, '', '', '_self', 0, 3, 1),
(24, 23, '添加一个广告位置', '', '', '', 'api', 0, 'api/v1/ads_position/add.ads.position.item', '', '_self', 0, 50, 1),
(25, 23, '编辑一个广告位置', '', '', '', 'api', 0, 'api/v1/ads_position/set.ads.position.item', '', '_self', 0, 50, 1),
(26, 23, '批量删除广告位置', '', '', '', 'api', 0, 'api/v1/ads_position/del.ads.position.list', '', '_self', 0, 50, 1),
(27, 23, '验证广告位置名称是否唯一', '', '', '', 'api', 0, 'api/v1/ads_position/unique.ads.position.name', '', '_self', 0, 50, 1),
(28, 23, '获取一个广告位置', '', '', '', 'api', 0, 'api/v1/ads_position/get.ads.position.item', '', '_self', 0, 50, 1),
(29, 23, '获取广告位置列表', '', '', '', 'api', 0, 'api/v1/ads_position/get.ads.position.list', '', '_self', 0, 50, 1),
(30, 23, '批量设置广告位置状态', '', '', '', 'api', 0, 'api/v1/ads_position/set.ads.position.status', '', '_self', 0, 50, 1),
(31, 0, '应用管理', '', '', '', 'api', 0, '', '', '_self', 0, 4, 1),
(32, 31, '添加一个应用', '', '', '', 'api', 0, 'api/v1/app/add.app.item', '', '_self', 0, 1, 1),
(33, 31, '编辑一个应用', '', '', '', 'api', 0, 'api/v1/app/set.app.item', '', '_self', 0, 2, 1),
(34, 31, '获取一个应用', '', '', '', 'api', 0, 'api/v1/app/get.app.item', '', '_self', 0, 3, 1),
(35, 31, '获取应用列表', '', '', '', 'api', 0, 'api/v1/app/get.app.list', '', '_self', 0, 4, 1),
(36, 31, '批量删除应用', '', '', '', 'api', 0, 'api/v1/app/del.app.list', '', '_self', 0, 5, 1),
(37, 31, '查询应用名称是否已存在', '', '', '', 'api', 0, 'api/v1/app/unique.app.name', '', '_self', 0, 6, 1),
(38, 31, '更换应用Secret', '', '', '', 'api', 0, 'api/v1/app/replace.app.secret', '', '_self', 0, 7, 1),
(39, 31, '批量设置应用状态', '', '', '', 'api', 0, 'api/v1/app/set.app.status', '', '_self', 0, 8, 1),
(40, 0, '应用安装包', '', '', '', 'api', 0, '', '', '_self', 0, 5, 1),
(41, 40, '添加一个应用安装包', '', '', '', 'api', 0, 'api/v1/app_install/add.app.install.item', '', '_self', 0, 50, 1),
(42, 40, '编辑一个应用安装包', '', '', '', 'api', 0, 'api/v1/app_install/set.app.install.item', '', '_self', 0, 50, 1),
(43, 40, '获取一个应用安装包', '', '', '', 'api', 0, 'api/v1/app_install/get.app.install.item', '', '_self', 0, 50, 1),
(44, 40, '批量删除应用安装包', '', '', '', 'api', 0, 'api/v1/app_install/del.app.install.list', '', '_self', 0, 50, 1),
(45, 40, '获取应用安装包列表', '', '', '', 'api', 0, 'api/v1/app_install/get.app.install.list', '', '_self', 0, 50, 1),
(46, 40, '根据请求获取一个应用安装包', '', '', '', 'api', 0, 'api/v1/app_install/request.app.install.item', '', '_self', 0, 50, 1),
(47, 0, '文章管理', '', '', '', 'api', 0, '', '', '_self', 0, 6, 1),
(48, 47, '添加一篇文章', '', '', '', 'api', 0, 'api/v1/article/add.article.item', '', '_self', 0, 50, 1),
(49, 47, '编辑一篇文章', '', '', '', 'api', 0, 'api/v1/article/set.article.item', '', '_self', 0, 50, 1),
(50, 47, '批量删除文章', '', '', '', 'api', 0, 'api/v1/article/del.article.list', '', '_self', 0, 50, 1),
(51, 47, '获取一篇文章', '', '', '', 'api', 0, 'api/v1/article/get.article.item', '', '_self', 0, 50, 1),
(52, 47, '获取文章列表', '', '', '', 'api', 0, 'api/v1/article/get.article.list', '', '_self', 0, 50, 1),
(53, 47, '批量设置文章置顶', '', '', '', 'api', 0, 'api/v1/article/set.article.top', '', '_self', 0, 50, 1),
(54, 47, '批量设置文章是否显示', '', '', '', 'api', 0, 'api/v1/article/set.article.status', '', '_self', 0, 50, 1),
(55, 0, '文章分类', '', '', '', 'api', 0, '', '', '_self', 0, 7, 1),
(56, 55, '添加一个文章分类', '', '', '', 'api', 0, 'api/v1/article_cat/add.article.cat.item', '', '_self', 0, 50, 1),
(57, 55, '编辑一个文章分类', '', '', '', 'api', 0, 'api/v1/article_cat/set.article.cat.item', '', '_self', 0, 50, 1),
(58, 55, '批量删除文章分类', '', '', '', 'api', 0, 'api/v1/article_cat/del.article.cat.list', '', '_self', 0, 50, 1),
(59, 55, '根据主Id获取文章分类', '', '', '', 'api', 0, 'api/v1/article_cat/get.article.cat.item', '', '_self', 0, 50, 1),
(60, 55, '获取分类所有列表', '', '', '', 'api', 0, 'api/v1/article_cat/get.article.cat.list', '', '_self', 0, 50, 1),
(61, 55, '根据分类Id生成导航数据', '', '', '', 'api', 0, 'api/v1/article_cat/get.article.cat.navi', '', '_self', 0, 50, 1),
(62, 55, '设置文章分类排序', '', '', '', 'api', 0, 'api/v1/article_cat/set.article.cat.sort', '', '_self', 0, 50, 1),
(63, 55, '批量设置文章分类是否导航', '', '', '', 'api', 0, 'api/v1/article_cat/set.article.cat.navi', '', '_self', 0, 50, 1),
(64, 0, '问答管理', '', '', '', 'api', 0, '', '', '_self', 0, 8, 1),
(65, 64, '添加一个新的咨询', '', '', '', 'api', 0, 'api/v1/ask/add.ask.item', '', '_self', 0, 50, 1),
(66, 64, '删除一条记录', '', '', '', 'api', 0, 'api/v1/ask/del.ask.item', '', '_self', 0, 50, 1),
(67, 64, '回复一个咨询', '', '', '', 'api', 0, 'api/v1/ask/reply.ask.item', '', '_self', 0, 50, 1),
(68, 64, '在主题上继续提交咨询', '', '', '', 'api', 0, 'api/v1/ask/continue.ask.item', '', '_self', 0, 50, 1),
(69, 64, '根据主题获取一个问答明细', '', '', '', 'api', 0, 'api/v1/ask/get.ask.item', '', '_self', 0, 50, 1),
(70, 64, '获取咨询主题列表', '', '', '', 'api', 0, 'api/v1/ask/get.ask.list', '', '_self', 0, 50, 1),
(71, 0, '用户组', '', '', '', 'api', 0, '', '', '_self', 0, 9, 1),
(72, 71, '添加一个用户组', '', '', '', 'api', 0, 'api/v1/auth_group/add.auth.group.item', '', '_self', 0, 50, 1),
(73, 71, '编辑一个用户组', '', '', '', 'api', 0, 'api/v1/auth_group/set.auth.group.item', '', '_self', 0, 50, 1),
(74, 71, '获取一个用户组', '', '', '', 'api', 0, 'api/v1/auth_group/get.auth.group.item', '', '_self', 0, 50, 1),
(75, 71, '删除一个用户组', '', '', '', 'api', 0, 'api/v1/auth_group/del.auth.group.item', '', '_self', 0, 50, 1),
(76, 71, '获取用户组列表', '', '', '', 'api', 0, 'api/v1/auth_group/get.auth.group.list', '', '_self', 0, 50, 1),
(77, 71, '批量设置用户组状态', '', '', '', 'api', 0, 'api/v1/auth_group/set.auth.group.status', '', '_self', 0, 50, 1),
(78, 71, '设置用户组排序', '', '', '', 'api', 0, 'api/v1/auth_group/set.auth.group.sort', '', '_self', 0, 50, 1),
(79, 0, '权限规则', '', '', '', 'api', 0, '', '', '_self', 0, 10, 1),
(80, 79, '添加一条规则', '', '', '', 'api', 0, 'api/v1/auth_rule/add.auth.rule.item', '', '_self', 0, 50, 1),
(81, 79, '获取一条规则', '', '', '', 'api', 0, 'api/v1/auth_rule/get.auth.rule.item', '', '_self', 0, 50, 1),
(82, 79, '编辑一条规则', '', '', '', 'api', 0, 'api/v1/auth_rule/set.auth.rule.item', '', '_self', 0, 50, 1),
(83, 79, '批量删除规则', '', '', '', 'api', 0, 'api/v1/auth_rule/del.auth.rule.list', '', '_self', 0, 50, 1),
(84, 79, '获取规则列表', '', '', '', 'api', 0, 'api/v1/auth_rule/get.auth.rule.list', '', '_self', 0, 50, 1),
(85, 79, '批量设置规则状态', '', '', '', 'api', 0, 'api/v1/auth_rule/set.auth.rule.status', '', '_self', 0, 50, 1),
(86, 79, '设置规则排序', '', '', '', 'api', 0, 'api/v1/auth_rule/set.auth.rule.sort', '', '_self', 0, 50, 1),
(87, 0, '品牌管理', '', '', '', 'api', 0, '', '', '_self', 0, 11, 1),
(88, 87, '添加一个品牌', '', '', '', 'api', 0, 'api/v1/brand/add.brand.item', '', '_self', 0, 50, 1),
(89, 87, '编辑一个品牌', '', '', '', 'api', 0, 'api/v1/brand/set.brand.item', '', '_self', 0, 50, 1),
(90, 87, '批量删除品牌', '', '', '', 'api', 0, 'api/v1/brand/del.brand.list', '', '_self', 0, 50, 1),
(91, 87, '批量设置品牌是否显示', '', '', '', 'api', 0, 'api/v1/brand/set.brand.status', '', '_self', 0, 50, 1),
(92, 87, '验证品牌名称是否唯一', '', '', '', 'api', 0, 'api/v1/brand/unique.brand.name', '', '_self', 0, 50, 1),
(93, 87, '获取一个品牌', '', '', '', 'api', 0, 'api/v1/brand/get.brand.item', '', '_self', 0, 50, 1),
(94, 87, '获取品牌列表', '', '', '', 'api', 0, 'api/v1/brand/get.brand.list', '', '_self', 0, 50, 1),
(95, 87, '获取品牌选择列表', '', '', '', 'api', 0, 'api/v1/brand/get.brand.select', '', '_self', 0, 50, 1),
(96, 87, '设置品牌排序', '', '', '', 'api', 0, 'api/v1/brand/set.brand.sort', '', '_self', 0, 50, 1),
(97, 0, '购物卡', '', '', '', 'api', 0, '', '', '_self', 0, 12, 1),
(98, 97, '添加一条购物卡', '', '', '', 'api', 0, 'api/v1/card/add.card.item', '', '_self', 0, 50, 1),
(99, 97, '编辑一条购物卡', '', '', '', 'api', 0, 'api/v1/card/set.card.item', '', '_self', 0, 50, 1),
(100, 97, '获取一条购物卡', '', '', '', 'api', 0, 'api/v1/card/get.card.item', '', '_self', 0, 50, 1),
(101, 97, '批量设置购物卡状态', '', '', '', 'api', 0, 'api/v1/card/set.card.status', '', '_self', 0, 50, 1),
(102, 97, '批量删除购物卡', '', '', '', 'api', 0, 'api/v1/card/del.card.list', '', '_self', 0, 50, 1),
(103, 97, '获取购物卡列表', '', '', '', 'api', 0, 'api/v1/card/get.card.list', '', '_self', 0, 50, 1),
(104, 0, '购物卡使用', '', '', '', 'api', 0, '', '', '_self', 0, 13, 1),
(105, 104, '绑定购物卡', '', '', '', 'api', 0, 'api/v1/card_use/bind.card.use.item', '', '_self', 0, 50, 1),
(106, 104, '批量设置购物卡是否有效', '', '', '', 'api', 0, 'api/v1/card_use/set.card.use.invalid', '', '_self', 0, 50, 1),
(107, 104, '导出生成的购物卡', '', '', '', 'api', 0, 'api/v1/card_use/get.card.use.export', '', '_self', 0, 50, 1),
(108, 104, '获取可合并的购物卡列表', '', '', '', 'api', 0, 'api/v1/card_use/get.card.use.merge', '', '_self', 0, 50, 1),
(109, 104, '相同购物卡进行余额合并', '', '', '', 'api', 0, 'api/v1/card_use/set.card.use.merge', '', '_self', 0, 50, 1),
(110, 104, '获取已绑定的购物卡', '', '', '', 'api', 0, 'api/v1/card_use/get.card.use.list', '', '_self', 0, 50, 1),
(111, 104, '根据商品Id列出可使用的购物卡', '', '', '', 'api', 0, 'api/v1/card_use/get.card.use.select', '', '_self', 0, 50, 1),
(112, 104, '验证购物卡是否可使用', '', '', '', 'api', 0, 'api/v1/card_use/get.card.use.check', '', '_self', 0, 50, 1),
(113, 0, '购物车', '', '', '', 'api', 0, '', '', '_self', 0, 14, 1),
(114, 113, '添加或编辑购物车商品', '', '', '', 'api', 0, 'api/v1/cart/set.cart.item', '', '_self', 0, 50, 1),
(115, 113, '验证是否允许添加或编辑购物车', '', '', '', 'api', 0, 'api/v1/cart/check.cart.goods', '', '_self', 0, 50, 1),
(116, 113, '批量添加商品到购物车', '', '', '', 'api', 0, 'api/v1/cart/add.cart.list', '', '_self', 0, 50, 1),
(117, 113, '获取购物车列表', '', '', '', 'api', 0, 'api/v1/cart/get.cart.list', '', '_self', 0, 50, 1),
(118, 113, '获取购物车商品数量', '', '', '', 'api', 0, 'api/v1/cart/get.cart.count', '', '_self', 0, 50, 1),
(119, 113, '设置购物车商品是否选中', '', '', '', 'api', 0, 'api/v1/cart/set.cart.select', '', '_self', 0, 50, 1),
(120, 113, '批量删除购物车商品', '', '', '', 'api', 0, 'api/v1/cart/del.cart.list', '', '_self', 0, 50, 1),
(121, 113, '清空购物车', '', '', '', 'api', 0, 'api/v1/cart/clear.cart.list', '', '_self', 0, 50, 1),
(122, 113, '请求商品立即购买', '', '', '', 'api', 0, 'api/v1/cart/create.cart.buynow', '', '_self', 0, 50, 1),
(123, 0, '收藏夹', '', '', '', 'api', 0, '', '', '_self', 0, 15, 1),
(124, 123, '添加一个商品收藏', '', '', '', 'api', 0, 'api/v1/collect/add.collect.item', '', '_self', 0, 50, 1),
(125, 123, '批量删除商品收藏', '', '', '', 'api', 0, 'api/v1/collect/del.collect.list', '', '_self', 0, 50, 1),
(126, 123, '清空商品收藏夹', '', '', '', 'api', 0, 'api/v1/collect/clear.collect.list', '', '_self', 0, 50, 1),
(127, 123, '设置收藏商品是否置顶', '', '', '', 'api', 0, 'api/v1/collect/set.collect.top', '', '_self', 0, 50, 1),
(128, 123, '获取商品收藏列表', '', '', '', 'api', 0, 'api/v1/collect/get.collect.list', '', '_self', 0, 50, 1),
(129, 123, '获取商品收藏数量', '', '', '', 'api', 0, 'api/v1/collect/get.collect.count', '', '_self', 0, 50, 1),
(130, 0, '优惠劵', '', '', '', 'api', 0, '', '', '_self', 0, 16, 1),
(131, 130, '添加一张优惠劵', '', '', '', 'api', 0, 'api/v1/coupon/add.coupon.item', '', '_self', 0, 1, 1),
(132, 130, '编辑一张优惠劵', '', '', '', 'api', 0, 'api/v1/coupon/set.coupon.item', '', '_self', 0, 2, 1),
(133, 130, '获取一张优惠劵', '', '', '', 'api', 0, 'api/v1/coupon/get.coupon.item', '', '_self', 0, 3, 1),
(134, 130, '获取优惠劵列表', '', '', '', 'api', 0, 'api/v1/coupon/get.coupon.list', '', '_self', 0, 4, 1),
(135, 130, '批量删除优惠劵', '', '', '', 'api', 0, 'api/v1/coupon/del.coupon.list', '', '_self', 0, 6, 1),
(136, 130, '批量设置优惠劵状态', '', '', '', 'api', 0, 'api/v1/coupon/set.coupon.status', '', '_self', 0, 7, 1),
(137, 130, '批量设置优惠劵是否失效', '', '', '', 'api', 0, 'api/v1/coupon/set.coupon.invalid', '', '_self', 0, 8, 1),
(138, 130, '获取当前可领取的优惠劵列表', '', '', '', 'api', 0, 'api/v1/coupon/get.coupon.active', '', '_self', 0, 9, 1),
(139, 0, '优惠劵发放', '', '', '', 'api', 0, '', '', '_self', 0, 17, 1),
(140, 139, '使用优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/use.coupon.item', '', '_self', 0, 50, 1),
(141, 139, '指定用户发放优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/give.coupon.user', '', '_self', 0, 50, 1),
(142, 139, '生成线下优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/give.coupon.live', '', '_self', 0, 50, 1),
(143, 139, '领取码领取优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/give.coupon.code', '', '_self', 0, 50, 1),
(144, 139, '获取已领取优惠劵列表', '', '', '', 'api', 0, 'api/v1/coupon_give/get.coupon.give.list', '', '_self', 0, 50, 1),
(145, 139, '批量删除已领取优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/del.coupon.give.list', '', '_self', 0, 50, 1),
(146, 139, '批量恢复已删优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/rec.coupon.give.list', '', '_self', 0, 50, 1),
(147, 139, '导出线下生成的优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/get.coupon.give.export', '', '_self', 0, 50, 1),
(148, 139, '根据商品Id列出可使用的优惠劵', '', '', '', 'api', 0, 'api/v1/coupon_give/get.coupon.give.select', '', '_self', 0, 50, 1),
(149, 139, '验证优惠劵是否可使用', '', '', '', 'api', 0, 'api/v1/coupon_give/get.coupon.give.check', '', '_self', 0, 50, 1),
(150, 0, '配送方式', '', '', '', 'api', 0, '', '', '_self', 0, 18, 1),
(151, 150, '添加一个配送方式', '', '', '', 'api', 0, 'api/v1/delivery/add.delivery.item', '', '_self', 0, 50, 1),
(152, 150, '编辑一个配送方式', '', '', '', 'api', 0, 'api/v1/delivery/set.delivery.item', '', '_self', 0, 50, 1),
(153, 150, '批量删除配送方式', '', '', '', 'api', 0, 'api/v1/delivery/del.delivery.list', '', '_self', 0, 50, 1),
(154, 150, '获取一个配送方式', '', '', '', 'api', 0, 'api/v1/delivery/get.delivery.item', '', '_self', 0, 50, 1),
(155, 150, '获取配送方式列表', '', '', '', 'api', 0, 'api/v1/delivery/get.delivery.list', '', '_self', 0, 50, 1),
(156, 150, '获取配送方式选择列表', '', '', '', 'api', 0, 'api/v1/delivery/get.delivery.select', '', '_self', 0, 50, 1),
(157, 150, '根据配送方式获取运费', '', '', '', 'api', 0, 'api/v1/delivery/get.delivery.freight', '', '_self', 0, 50, 1),
(158, 150, '批量设置配送方式状态', '', '', '', 'api', 0, 'api/v1/delivery/set.delivery.status', '', '_self', 0, 50, 1),
(159, 150, '验证快递公司编号是否唯一', '', '', '', 'api', 0, 'api/v1/delivery/unique.delivery.item.id', '', '_self', 0, 50, 1),
(160, 150, '设置配送方式排序', '', '', '', 'api', 0, 'api/v1/delivery/set.delivery.sort', '', '_self', 0, 50, 1),
(161, 0, '配送区域', '', '', '', 'api', 0, '', '', '_self', 0, 19, 1),
(162, 161, '添加一个配送区域', '', '', '', 'api', 0, 'api/v1/delivery_area/add.delivery.area.item', '', '_self', 0, 50, 1),
(163, 161, '编辑一个配送区域', '', '', '', 'api', 0, 'api/v1/delivery_area/set.delivery.area.item', '', '_self', 0, 50, 1),
(164, 161, '批量删除配送区域', '', '', '', 'api', 0, 'api/v1/delivery_area/del.delivery.area.list', '', '_self', 0, 50, 1),
(165, 161, '获取一个配送区域', '', '', '', 'api', 0, 'api/v1/delivery_area/get.delivery.area.item', '', '_self', 0, 50, 1),
(166, 161, '获取配送区域列表', '', '', '', 'api', 0, 'api/v1/delivery_area/get.delivery.area.list', '', '_self', 0, 50, 1),
(167, 0, '配送轨迹', '', '', '', 'api', 0, '', '', '_self', 0, 20, 1),
(168, 167, '获取配送回调URL接口', '', '', '', 'api', 0, 'api/v1/delivery_dist/get.delivery.dist.callback', '', '_self', 0, 7, 1),
(169, 167, '添加一条配送轨迹', '', '', '', 'api', 0, 'api/v1/delivery_dist/add.delivery.dist.item', '', '_self', 0, 1, 1),
(170, 167, '接收推送过来的配送轨迹', '', '', '', 'api', 0, 'api/v1/delivery_dist/put.delivery.dist.data', '', '_self', 0, 2, 1),
(171, 167, '根据流水号获取配送轨迹', '', '', '', 'api', 0, 'api/v1/delivery_dist/get.delivery.dist.code', '', '_self', 0, 3, 1),
(172, 167, '获取配送轨迹列表', '', '', '', 'api', 0, 'api/v1/delivery_dist/get.delivery.dist.list', '', '_self', 0, 4, 1),
(173, 0, '快递公司', '', '', '', 'api', 0, '', '', '_self', 0, 21, 1),
(174, 173, '添加一个快递公司', '', '', '', 'api', 0, 'api/v1/delivery_item/add.delivery.company.item', '', '_self', 0, 1, 1),
(175, 173, '编辑一个快递公司', '', '', '', 'api', 0, 'api/v1/delivery_item/set.delivery.company.item', '', '_self', 0, 2, 1),
(176, 173, '批量删除快递公司', '', '', '', 'api', 0, 'api/v1/delivery_item/del.delivery.company.list', '', '_self', 0, 3, 1),
(177, 173, '获取一个快递公司', '', '', '', 'api', 0, 'api/v1/delivery_item/get.delivery.company.item', '', '_self', 0, 4, 1),
(178, 173, '查询快递公司编码是否已存在', '', '', '', 'api', 0, 'api/v1/delivery_item/unique.delivery.company.code', '', '_self', 0, 5, 1),
(179, 173, '获取快递公司列表', '', '', '', 'api', 0, 'api/v1/delivery_item/get.delivery.company.list', '', '_self', 0, 6, 1),
(180, 173, '获取快递公司选择列表', '', '', '', 'api', 0, 'api/v1/delivery_item/get.delivery.company.select', '', '_self', 0, 7, 1),
(181, 173, '复制一个快递公司为\"热门类型\"', '', '', '', 'api', 0, 'api/v1/delivery_item/copy.delivery.company.hot', '', '_self', 0, 9, 1),
(182, 0, '商品折扣', '', '', '', 'api', 0, '', '', '_self', 0, 22, 1),
(183, 182, '添加一个商品折扣', '', '', '', 'api', 0, 'api/v1/discount/add.discount.item', '', '_self', 0, 50, 1),
(184, 182, '编辑一个商品折扣', '', '', '', 'api', 0, 'api/v1/discount/set.discount.item', '', '_self', 0, 50, 1),
(185, 182, '获取一个商品折扣', '', '', '', 'api', 0, 'api/v1/discount/get.discount.item', '', '_self', 0, 50, 1),
(186, 182, '批量删除商品折扣', '', '', '', 'api', 0, 'api/v1/discount/del.discount.list', '', '_self', 0, 50, 1),
(187, 182, '批量设置商品折扣状态', '', '', '', 'api', 0, 'api/v1/discount/set.discount.status', '', '_self', 0, 50, 1),
(188, 182, '获取商品折扣列表', '', '', '', 'api', 0, 'api/v1/discount/get.discount.list', '', '_self', 0, 50, 1),
(189, 182, '根据商品编号获取折扣信息', '', '', '', 'api', 0, 'api/v1/discount/get.discount.goods.info', '', '_self', 0, 50, 1),
(190, 0, '友情链接', '', '', '', 'api', 0, '', '', '_self', 0, 23, 1),
(191, 190, '添加一个友情链接', '', '', '', 'api', 0, 'api/v1/friend_link/add.friendlink.item', '', '_self', 0, 50, 1),
(192, 190, '编辑一个友情链接', '', '', '', 'api', 0, 'api/v1/friend_link/set.friendlink.item', '', '_self', 0, 50, 1),
(193, 190, '批量删除友情链接', '', '', '', 'api', 0, 'api/v1/friend_link/del.friendlink.list', '', '_self', 0, 50, 1),
(194, 190, '获取一个友情链接', '', '', '', 'api', 0, 'api/v1/friend_link/get.friendlink.item', '', '_self', 0, 50, 1),
(195, 190, '获取友情链接列表', '', '', '', 'api', 0, 'api/v1/friend_link/get.friendlink.list', '', '_self', 0, 50, 1),
(196, 190, '批量设置友情链接状态', '', '', '', 'api', 0, 'api/v1/friend_link/set.friendlink.status', '', '_self', 0, 50, 1),
(197, 190, '设置友情链接排序', '', '', '', 'api', 0, 'api/v1/friend_link/set.friendlink.sort', '', '_self', 0, 50, 1),
(198, 0, '商品管理', '', '', '', 'api', 0, '', '', '_self', 0, 24, 1),
(199, 198, '检测商品货号是否唯一', '', '', '', 'api', 0, 'api/v1/goods/unique.goods.code', '', '_self', 0, 50, 1),
(200, 198, '添加一个商品', '', '', '', 'api', 0, 'api/v1/goods/add.goods.item', '', '_self', 0, 50, 1),
(201, 198, '编辑一个商品', '', '', '', 'api', 0, 'api/v1/goods/set.goods.item', '', '_self', 0, 50, 1),
(202, 198, '获取一个商品', '', '', '', 'api', 0, 'api/v1/goods/get.goods.item', '', '_self', 0, 50, 1),
(203, 198, '批量删除或恢复商品(回收站)', '', '', '', 'api', 0, 'api/v1/goods/del.goods.list', '', '_self', 0, 50, 1),
(204, 198, '批量开启或关闭商品可积分抵扣', '', '', '', 'api', 0, 'api/v1/goods/set.integral.goods.list', '', '_self', 0, 50, 1),
(205, 198, '批量设置商品是否推荐', '', '', '', 'api', 0, 'api/v1/goods/set.recommend.goods.list', '', '_self', 0, 50, 1),
(206, 198, '批量设置商品是否为新品', '', '', '', 'api', 0, 'api/v1/goods/set.new.goods.list', '', '_self', 0, 50, 1),
(207, 198, '批量设置商品是否为热卖', '', '', '', 'api', 0, 'api/v1/goods/set.hot.goods.list', '', '_self', 0, 50, 1),
(208, 198, '批量设置商品是否上下架', '', '', '', 'api', 0, 'api/v1/goods/set.shelves.goods.list', '', '_self', 0, 50, 1),
(209, 198, '获取指定商品的属性列表', '', '', '', 'api', 0, 'api/v1/goods/get.goods.attr.list', '', '_self', 0, 50, 1),
(210, 198, '获取指定商品的规格组合列表', '', '', '', 'api', 0, 'api/v1/goods/get.goods.spec.list', '', '_self', 0, 50, 1),
(211, 198, '获取指定商品的规格图', '', '', '', 'api', 0, 'api/v1/goods/get.goods.spec.image', '', '_self', 0, 50, 1),
(212, 198, '获取管理后台商品列表', '', '', '', 'api', 0, 'api/v1/goods/get.goods.admin.list', '', '_self', 0, 50, 1),
(213, 198, '根据商品分类获取指定类型的商品', '', '', '', 'api', 0, 'api/v1/goods/get.goods.index.type', '', '_self', 0, 50, 1),
(214, 198, '根据商品分类获取前台商品列表页', '', '', '', 'api', 0, 'api/v1/goods/get.goods.index.list', '', '_self', 0, 50, 1),
(215, 198, '设置商品排序', '', '', '', 'api', 0, 'api/v1/goods/set.goods.sort', '', '_self', 0, 50, 1),
(216, 198, '获取商品关键词联想词', '', '', '', 'api', 0, 'api/v1/goods/get.goods.keywords.suggest', '', '_self', 0, 50, 1),
(217, 198, '复制一个商品', '', '', '', 'api', 0, 'api/v1/goods/copy.goods.item', '', '_self', 0, 50, 1),
(218, 0, '商品属性', '', '', '', 'api', 0, '', '', '_self', 0, 30, 1),
(219, 218, '添加一个商品属性主体', '', '', '', 'api', 0, 'api/v1/goods_attribute/add.goods.attribute.body.item', '', '_self', 0, 1, 1),
(220, 218, '编辑一个商品属性主体', '', '', '', 'api', 0, 'api/v1/goods_attribute/set.goods.attribute.body.item', '', '_self', 0, 2, 1),
(221, 218, '获取一个商品属性主体', '', '', '', 'api', 0, 'api/v1/goods_attribute/get.goods.attribute.body.item', '', '_self', 0, 3, 1),
(222, 218, '获取商品属性主体列表', '', '', '', 'api', 0, 'api/v1/goods_attribute/get.goods.attribute.body.list', '', '_self', 0, 4, 1),
(223, 218, '设置商品属性主体排序', '', '', '', 'api', 0, 'api/v1/goods_attribute/set.goods.attribute.body.sort', '', '_self', 0, 5, 1),
(224, 218, '添加一个商品属性', '', '', '', 'api', 0, 'api/v1/goods_attribute/add.goods.attribute.item', '', '_self', 0, 6, 1),
(225, 218, '编辑一个商品属性', '', '', '', 'api', 0, 'api/v1/goods_attribute/set.goods.attribute.item', '', '_self', 0, 7, 1),
(226, 218, '批量删除商品属性', '', '', '', 'api', 0, 'api/v1/goods_attribute/del.goods.attribute.list', '', '_self', 0, 8, 1),
(227, 218, '获取一个商品属性', '', '', '', 'api', 0, 'api/v1/goods_attribute/get.goods.attribute.item', '', '_self', 0, 9, 1),
(228, 218, '获取商品属性列表', '', '', '', 'api', 0, 'api/v1/goods_attribute/get.goods.attribute.list', '', '_self', 0, 10, 1),
(229, 218, '批量设置商品属性检索', '', '', '', 'api', 0, 'api/v1/goods_attribute/set.goods.attribute.key', '', '_self', 0, 12, 1),
(230, 218, '批量设置商品属性是否核心', '', '', '', 'api', 0, 'api/v1/goods_attribute/set.goods.attribute.important', '', '_self', 0, 13, 1),
(231, 218, '设置商品属性排序', '', '', '', 'api', 0, 'api/v1/goods_attribute/set.goods.attribute.sort', '', '_self', 0, 14, 1),
(232, 0, '商品分类', '', '', '', 'api', 0, '', '', '_self', 0, 25, 1),
(233, 232, '添加一个商品分类', '', '', '', 'api', 0, 'api/v1/goods_category/add.goods.category.item', '', '_self', 0, 50, 1),
(234, 232, '编辑一个商品分类', '', '', '', 'api', 0, 'api/v1/goods_category/set.goods.category.item', '', '_self', 0, 50, 1),
(235, 232, '批量删除商品分类', '', '', '', 'api', 0, 'api/v1/goods_category/del.goods.category.list', '', '_self', 0, 50, 1),
(236, 232, '根据主Id获取商品分类', '', '', '', 'api', 0, 'api/v1/goods_category/get.goods.category.item', '', '_self', 0, 50, 1),
(237, 232, '获取所有商品分类', '', '', '', 'api', 0, 'api/v1/goods_category/get.goods.category.list', '', '_self', 0, 50, 1),
(238, 232, '根据主Id集合获取所有子级', '', '', '', 'api', 0, 'api/v1/goods_category/get.goods.category.son', '', '_self', 0, 50, 1),
(239, 232, '根据分类Id生成导航数据', '', '', '', 'api', 0, 'api/v1/goods_category/get.goods.category.navi', '', '_self', 0, 50, 1),
(240, 232, '批量设置商品分类是否显示', '', '', '', 'api', 0, 'api/v1/goods_category/set.goods.category.status', '', '_self', 0, 50, 1),
(241, 232, '设置商品分类排序', '', '', '', 'api', 0, 'api/v1/goods_category/set.goods.category.sort', '', '_self', 0, 50, 1),
(242, 232, '批量设置商品分类是否导航', '', '', '', 'api', 0, 'api/v1/goods_category/set.goods.category.navi', '', '_self', 0, 50, 1),
(243, 0, '商品评价', '', '', '', 'api', 0, '', '', '_self', 0, 26, 1),
(244, 243, '添加一条新的商品评价', '', '', '', 'api', 0, 'api/v1/goods_comment/add.goods.comment.item', '', '_self', 0, 50, 1),
(245, 243, '追加一条商品评价', '', '', '', 'api', 0, 'api/v1/goods_comment/add.goods.addition.item', '', '_self', 0, 50, 1),
(246, 243, '回复一条商品评价或追加评价', '', '', '', 'api', 0, 'api/v1/goods_comment/reply.goods.comment.item', '', '_self', 0, 50, 1),
(247, 243, '删除商品评价任意内容', '', '', '', 'api', 0, 'api/v1/goods_comment/del.goods.comment.item', '', '_self', 0, 50, 1),
(248, 243, '点赞一条商品评价', '', '', '', 'api', 0, 'api/v1/goods_comment/add.goods.praise.item', '', '_self', 0, 50, 1),
(249, 243, '获取一个商品评价得分', '', '', '', 'api', 0, 'api/v1/goods_comment/get.goods.comment.score', '', '_self', 0, 50, 1),
(250, 243, '批量设置是否前台显示', '', '', '', 'api', 0, 'api/v1/goods_comment/set.goods.comment.show', '', '_self', 0, 50, 1),
(251, 243, '批量设置是否置顶', '', '', '', 'api', 0, 'api/v1/goods_comment/set.goods.comment.top', '', '_self', 0, 50, 1),
(252, 243, '批量设置商品评价是否已读', '', '', '', 'api', 0, 'api/v1/goods_comment/set.goods.comment.status', '', '_self', 0, 50, 1),
(253, 243, '获取一个商品评价数量', '', '', '', 'api', 0, 'api/v1/goods_comment/get.goods.comment.count', '', '_self', 0, 50, 1),
(254, 243, '获取某个主评价的明细', '', '', '', 'api', 0, 'api/v1/goods_comment/get.goods.comment.item', '', '_self', 0, 50, 1),
(255, 243, '获取评价列表', '', '', '', 'api', 0, 'api/v1/goods_comment/get.goods.comment.list', '', '_self', 0, 50, 1),
(256, 0, '商品咨询', '', '', '', 'api', 0, '', '', '_self', 0, 27, 1),
(257, 256, '添加一个新的商品咨询', '', '', '', 'api', 0, 'api/v1/goods_consult/add.goods.consult.item', '', '_self', 0, 50, 1),
(258, 256, '批量删除商品咨询', '', '', '', 'api', 0, 'api/v1/goods_consult/del.goods.consult.list', '', '_self', 0, 50, 1),
(259, 256, '批量设置是否前台显示', '', '', '', 'api', 0, 'api/v1/goods_consult/set.goods.consult.show', '', '_self', 0, 50, 1),
(260, 256, '回复一个商品咨询', '', '', '', 'api', 0, 'api/v1/goods_consult/reply.goods.consult.item', '', '_self', 0, 50, 1),
(261, 256, '根据主Id获取一个问答明细', '', '', '', 'api', 0, 'api/v1/goods_consult/get.goods.consult.item', '', '_self', 0, 50, 1),
(262, 256, '获取商品咨询列表', '', '', '', 'api', 0, 'api/v1/goods_consult/get.goods.consult.list', '', '_self', 0, 50, 1),
(263, 0, '商品评价回复', '', '', '', 'api', 0, '', '', '_self', 0, 28, 1),
(264, 263, '对商品评价添加一个回复', '', '', '', 'api', 0, 'api/v1/goods_reply/add.goods.reply.item', '', '_self', 0, 50, 1),
(265, 263, '批量删除商品评价的回复', '', '', '', 'api', 0, 'api/v1/goods_reply/del.goods.reply.list', '', '_self', 0, 50, 1),
(266, 263, '获取商品评价回复列表', '', '', '', 'api', 0, 'api/v1/goods_reply/get.goods.reply.list', '', '_self', 0, 50, 1),
(267, 0, '商品模型', '', '', '', 'api', 0, '', '', '_self', 0, 29, 1),
(268, 267, '添加一个商品模型', '', '', '', 'api', 0, 'api/v1/goods_type/add.goods.type.item', '', '_self', 0, 50, 1),
(269, 267, '编辑一个商品模型', '', '', '', 'api', 0, 'api/v1/goods_type/set.goods.type.item', '', '_self', 0, 50, 1),
(270, 267, '批量删除商品模型', '', '', '', 'api', 0, 'api/v1/goods_type/del.goods.type.list', '', '_self', 0, 50, 1),
(271, 267, '查询商品模型名称是否已存在', '', '', '', 'api', 0, 'api/v1/goods_type/unique.goods.type.name', '', '_self', 0, 50, 1),
(272, 267, '获取一个商品模型', '', '', '', 'api', 0, 'api/v1/goods_type/get.goods.type.item', '', '_self', 0, 50, 1),
(273, 267, '获取商品模型列表', '', '', '', 'api', 0, 'api/v1/goods_type/get.goods.type.list', '', '_self', 0, 50, 1),
(274, 267, '获取商品模型选择列表', '', '', '', 'api', 0, 'api/v1/goods_type/get.goods.type.select', '', '_self', 0, 50, 1),
(275, 0, '我的足迹', '', '', '', 'api', 0, '', '', '_self', 0, 32, 1),
(276, 275, '添加一条我的足迹', '', '', '', 'api', 0, 'api/v1/history/add.history.item', '', '_self', 0, 50, 1),
(277, 275, '批量删除我的足迹', '', '', '', 'api', 0, 'api/v1/history/del.history.list', '', '_self', 0, 50, 1),
(278, 275, '清空我的足迹', '', '', '', 'api', 0, 'api/v1/history/clear.history.list', '', '_self', 0, 50, 1),
(279, 275, '获取我的足迹数量', '', '', '', 'api', 0, 'api/v1/history/get.history.count', '', '_self', 0, 50, 1),
(280, 275, '获取我的足迹列表', '', '', '', 'api', 0, 'api/v1/history/get.history.list', '', '_self', 0, 50, 1),
(281, 0, 'API访问测试', '', '', '', 'api', 0, '', '', '_self', 0, 33, 1),
(282, 281, 'API访问测试接口', '', '', '', 'api', 0, 'api/v1/index/get.index.host', '', '_self', 0, 50, 1),
(283, 281, '清空缓存', '', '', '', 'api', 0, 'api/v1/index/clear.cache.all', '', '_self', 0, 50, 1),
(284, 281, '正式环境下调整为最优状态', '', '', '', 'api', 0, 'api/v1/index/set.system.optimize', '', '_self', 0, 50, 1),
(285, 0, '菜单管理', '', '', '', 'api', 0, '', '', '_self', 0, 34, 1),
(286, 285, '添加一个菜单', '', '', '', 'api', 0, 'api/v1/menu/add.menu.item', '', '_self', 0, 50, 1),
(287, 285, '获取一个菜单', '', '', '', 'api', 0, 'api/v1/menu/get.menu.item', '', '_self', 0, 50, 1),
(288, 285, '编辑一个菜单', '', '', '', 'api', 0, 'api/v1/menu/set.menu.item', '', '_self', 0, 50, 1),
(289, 285, '删除一个菜单', '', '', '', 'api', 0, 'api/v1/menu/del.menu.item', '', '_self', 0, 50, 1),
(290, 285, '获取菜单列表', '', '', '', 'api', 0, 'api/v1/menu/get.menu.list', '', '_self', 0, 50, 1),
(291, 285, '根据菜单Id生成导航数据', '', '', '', 'api', 0, 'api/v1/menu/get.menu.id.navi', '', '_self', 0, 50, 1),
(292, 285, '根据菜单url生成导航数据', '', '', '', 'api', 0, 'api/v1/menu/get.menu.url.navi', '', '_self', 0, 50, 1),
(293, 285, '批量设置是否属于导航菜单', '', '', '', 'api', 0, 'api/v1/menu/set.menu.navi', '', '_self', 0, 50, 1),
(294, 285, '设置菜单排序', '', '', '', 'api', 0, 'api/v1/menu/set.menu.sort', '', '_self', 0, 50, 1),
(295, 285, '设置菜单状态', '', '', '', 'api', 0, 'api/v1/menu/set.menu.status', '', '_self', 0, 50, 1),
(296, 285, '根据权限获取菜单列表', '', '', '', 'api', 0, 'api/v1/menu/get.menu.auth.list', '', '_self', 0, 50, 1),
(297, 0, '消息', '', '', '', 'api', 0, '', '', '_self', 0, 35, 1),
(298, 297, '添加一条消息', '', '', '', 'api', 0, 'api/v1/message/add.message.item', '', '_self', 0, 50, 1),
(299, 297, '编辑一条消息', '', '', '', 'api', 0, 'api/v1/message/set.message.item', '', '_self', 0, 50, 1),
(300, 297, '批量删除消息', '', '', '', 'api', 0, 'api/v1/message/del.message.list', '', '_self', 0, 50, 1),
(301, 297, '批量正式发布消息', '', '', '', 'api', 0, 'api/v1/message/set.message.status', '', '_self', 0, 50, 1),
(302, 297, '获取一条消息(后台)', '', '', '', 'api', 0, 'api/v1/message/get.message.item', '', '_self', 0, 50, 1),
(303, 297, '获取消息列表(后台)', '', '', '', 'api', 0, 'api/v1/message/get.message.list', '', '_self', 0, 50, 1),
(304, 297, '用户获取一条消息', '', '', '', 'api', 0, 'api/v1/message/get.message.user.item', '', '_self', 0, 50, 1),
(305, 297, '用户获取消息列表', '', '', '', 'api', 0, 'api/v1/message/get.message.user.list', '', '_self', 0, 50, 1),
(306, 297, '用户获取未读消息数', '', '', '', 'api', 0, 'api/v1/message/get.message.user.unread', '', '_self', 0, 50, 1),
(307, 297, '用户批量设置消息已读', '', '', '', 'api', 0, 'api/v1/message/set.message.user.read', '', '_self', 0, 50, 1),
(308, 297, '用户设置消息全部已读', '', '', '', 'api', 0, 'api/v1/message/set.message.user.allread', '', '_self', 0, 50, 1),
(309, 297, '用户批量删除消息', '', '', '', 'api', 0, 'api/v1/message/del.message.user.list', '', '_self', 0, 50, 1),
(310, 297, '用户删除全部消息', '', '', '', 'api', 0, 'api/v1/message/del.message.user.all', '', '_self', 0, 50, 1),
(311, 0, '导航', '', '', '', 'api', 0, '', '', '_self', 0, 36, 1),
(312, 311, '添加一个导航', '', '', '', 'api', 0, 'api/v1/navigation/add.navigation.item', '', '_self', 0, 50, 1),
(313, 311, '编辑一个导航', '', '', '', 'api', 0, 'api/v1/navigation/set.navigation.item', '', '_self', 0, 50, 1),
(314, 311, '批量删除导航', '', '', '', 'api', 0, 'api/v1/navigation/del.navigation.list', '', '_self', 0, 50, 1),
(315, 311, '获取一个导航', '', '', '', 'api', 0, 'api/v1/navigation/get.navigation.item', '', '_self', 0, 50, 1),
(316, 311, '获取导航列表', '', '', '', 'api', 0, 'api/v1/navigation/get.navigation.list', '', '_self', 0, 50, 1),
(317, 311, '批量设置是否新开窗口', '', '', '', 'api', 0, 'api/v1/navigation/set.navigation.target', '', '_self', 0, 50, 1),
(318, 311, '批量设置是否启用', '', '', '', 'api', 0, 'api/v1/navigation/set.navigation.status', '', '_self', 0, 50, 1),
(319, 311, '设置导航排序', '', '', '', 'api', 0, 'api/v1/navigation/set.navigation.sort', '', '_self', 0, 50, 1),
(320, 0, '通知系统', '', '', '', 'api', 0, '', '', '_self', 0, 37, 1),
(321, 320, '获取一个通知系统', '', '', '', 'api', 0, 'api/v1/notice/get.notice.item', '', '_self', 0, 50, 1),
(322, 320, '获取通知系统列表', '', '', '', 'api', 0, 'api/v1/notice/get.notice.list', '', '_self', 0, 50, 1),
(323, 320, '批量设置通知系统是否启用', '', '', '', 'api', 0, 'api/v1/notice/set.notice.status', '', '_self', 0, 50, 1),
(324, 320, '设置一个通知系统', '', '', '', 'api', 0, 'api/v1/notice/set.notice.item', '', '_self', 0, 50, 1),
(325, 0, '通知系统模板', '', '', '', 'api', 0, '', '', '_self', 0, 38, 1),
(326, 325, '获取一个通知系统模板', '', '', '', 'api', 0, 'api/v1/notice_tpl/get.notice.tpl.item', '', '_self', 0, 50, 1),
(327, 325, '获取通知系统模板列表', '', '', '', 'api', 0, 'api/v1/notice_tpl/get.notice.tpl.list', '', '_self', 0, 50, 1),
(328, 325, '编辑一个通知系统模板', '', '', '', 'api', 0, 'api/v1/notice_tpl/set.notice.tpl.item', '', '_self', 0, 50, 1),
(329, 325, '批量设置通知系统模板是否启用', '', '', '', 'api', 0, 'api/v1/notice_tpl/set.notice.tpl.status', '', '_self', 0, 50, 1),
(330, 0, '订单管理', '', '', '', 'api', 0, '', '', '_self', 0, 39, 1),
(331, 330, '获取订单确认或提交订单', '', '', '', 'api', 0, 'api/v1/order/confirm.order.list', '', '_self', 0, 50, 1),
(332, 330, '调整订单应付金额', '', '', '', 'api', 0, 'api/v1/order/change.price.order.item', '', '_self', 0, 50, 1),
(333, 330, '添加或编辑卖家备注', '', '', '', 'api', 0, 'api/v1/order/remark.order.item', '', '_self', 0, 50, 1),
(334, 330, '编辑一个订单', '', '', '', 'api', 0, 'api/v1/order/set.order.item', '', '_self', 0, 50, 1),
(335, 330, '将订单放入回收站、还原或删除', '', '', '', 'api', 0, 'api/v1/order/recycle.order.item', '', '_self', 0, 50, 1),
(336, 330, '获取一个订单', '', '', '', 'api', 0, 'api/v1/order/get.order.item', '', '_self', 0, 50, 1),
(337, 330, '获取订单列表', '', '', '', 'api', 0, 'api/v1/order/get.order.list', '', '_self', 0, 50, 1),
(338, 330, '获取订单各个状态合计数', '', '', '', 'api', 0, 'api/v1/order/get.order.status.total', '', '_self', 0, 50, 1),
(339, 330, '再次购买与订单相同的商品', '', '', '', 'api', 0, 'api/v1/order/buyagain.order.goods', '', '_self', 0, 50, 1),
(340, 330, '获取可评价或可追评的订单商品列表', '', '', '', 'api', 0, 'api/v1/order/get.order.goods.comment', '', '_self', 0, 50, 1),
(341, 330, '未付款订单超时自动取消', '', '', '', 'api', 0, 'api/v1/order/timeout.order.cancel', '', '_self', 0, 50, 1),
(342, 330, '未确认收货订单超时自动完成', '', '', '', 'api', 0, 'api/v1/order/timeout.order.complete', '', '_self', 0, 50, 1),
(343, 330, '取消一个订单', '', '', '', 'api', 0, 'api/v1/order/cancel.order.item', '', '_self', 0, 50, 1),
(344, 330, '订单批量设为配货状态', '', '', '', 'api', 0, 'api/v1/order/picking.order.list', '', '_self', 0, 50, 1),
(345, 330, '订单设为发货状态', '', '', '', 'api', 0, 'api/v1/order/delivery.order.item', '', '_self', 0, 50, 1),
(346, 330, '订单批量确认收货', '', '', '', 'api', 0, 'api/v1/order/complete.order.list', '', '_self', 0, 50, 1),
(347, 330, '获取一个订单商品明细', '', '', '', 'api', 0, 'api/v1/order/get.order.goods.item', '', '_self', 0, 50, 1),
(348, 0, '订单退款', '', '', '', 'api', 0, '', '', '_self', 0, 40, 1),
(349, 348, '查询一笔退款记录', '', '', '', 'api', 0, 'api/v1/order_refund/query.refund.item', '', '_self', 0, 50, 1),
(350, 348, '获取退款记录列表', '', '', '', 'api', 0, 'api/v1/order_refund/get.refund.list', '', '_self', 0, 50, 1),
(351, 0, '售后服务', '', '', '', 'api', 0, '', '', '_self', 0, 41, 1),
(352, 351, '获取订单商品可申请的售后服务', '', '', '', 'api', 0, 'api/v1/order_service/get.order.service.goods', '', '_self', 0, 50, 1),
(353, 351, '客服对售后服务单添加备注(顾客不可见)', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.remark', '', '_self', 0, 50, 1),
(354, 351, '获取一个售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/get.order.service.item', '', '_self', 0, 50, 1),
(355, 351, '获取售后服务单列表', '', '', '', 'api', 0, 'api/v1/order_service/get.order.service.list', '', '_self', 0, 50, 1),
(356, 351, '添加一个维修售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/add.order.service.maintain', '', '_self', 0, 50, 1),
(357, 351, '添加一个换货售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/add.order.service.exchange', '', '_self', 0, 50, 1),
(358, 351, '添加一个仅退款售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/add.order.service.refund', '', '_self', 0, 50, 1),
(359, 351, '添加一个退款退货售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/add.order.service.refunds', '', '_self', 0, 50, 1),
(360, 351, '添加一条售后服务单留言', '', '', '', 'api', 0, 'api/v1/order_service/add.order.service.message', '', '_self', 0, 50, 1),
(361, 351, '同意(接收)一个售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.agree', '', '_self', 0, 50, 1),
(362, 351, '拒绝一个售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.refused', '', '_self', 0, 50, 1),
(363, 351, '设置退换货、维修商品是否寄还商家', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.sendback', '', '_self', 0, 50, 1),
(364, 351, '买家上报换货、维修后的快递单号,并填写商家寄回时需要的信息', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.buyer', '', '_self', 0, 50, 1),
(365, 351, '买家上报退款退货后的快递单号', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.logistic', '', '_self', 0, 50, 1),
(366, 351, '设置一个售后服务单状态(售后中)', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.after', '', '_self', 0, 50, 1),
(367, 351, '撤销一个售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.cancel', '', '_self', 0, 50, 1),
(368, 351, '完成一个售后服务单', '', '', '', 'api', 0, 'api/v1/order_service/set.order.service.complete', '', '_self', 0, 50, 1),
(369, 0, '支付管理', '', '', '', 'api', 0, '', '', '_self', 0, 42, 1),
(370, 369, '编辑一个支付配置', '', '', '', 'api', 0, 'api/v1/payment/set.payment.item', '', '_self', 0, 50, 1),
(371, 369, '获取一个支付配置', '', '', '', 'api', 0, 'api/v1/payment/get.payment.item', '', '_self', 0, 50, 1),
(372, 369, '获取支付配置列表', '', '', '', 'api', 0, 'api/v1/payment/get.payment.list', '', '_self', 0, 50, 1),
(373, 369, '获取支付异步URL接口', '', '', '', 'api', 0, 'api/v1/payment/get.payment.notify', '', '_self', 0, 50, 1),
(374, 369, '获取支付同步URL接口', '', '', '', 'api', 0, 'api/v1/payment/get.payment.return', '', '_self', 0, 50, 1),
(375, 369, '设置支付配置排序', '', '', '', 'api', 0, 'api/v1/payment/set.payment.sort', '', '_self', 0, 50, 1),
(376, 369, '批量设置支付配置状态', '', '', '', 'api', 0, 'api/v1/payment/set.payment.status', '', '_self', 0, 50, 1),
(377, 369, '财务对账号进行资金调整', '', '', '', 'api', 0, 'api/v1/payment/set.payment.finance', '', '_self', 0, 50, 1),
(378, 369, '接收支付返回内容', '', '', '', 'api', 0, 'api/v1/payment/put.payment.data', '', '_self', 0, 50, 1),
(379, 369, '账号在线充值余额', '', '', '', 'api', 0, 'api/v1/payment/user.payment.pay', '', '_self', 0, 50, 1),
(380, 369, '订单付款在线支付', '', '', '', 'api', 0, 'api/v1/payment/order.payment.pay', '', '_self', 0, 50, 1),
(381, 0, '支付日志', '', '', '', 'api', 0, '', '', '_self', 0, 43, 1),
(382, 381, '关闭一笔充值记录', '', '', '', 'api', 0, 'api/v1/payment_log/close.payment.log.item', '', '_self', 0, 50, 1),
(383, 381, '获取一笔充值记录', '', '', '', 'api', 0, 'api/v1/payment_log/get.payment.log.item', '', '_self', 0, 50, 1),
(384, 381, '获取充值记录列表', '', '', '', 'api', 0, 'api/v1/payment_log/get.payment.log.list', '', '_self', 0, 50, 1),
(385, 0, '订单促销', '', '', '', 'api', 0, '', '', '_self', 0, 44, 1),
(386, 385, '添加一个订单促销', '', '', '', 'api', 0, 'api/v1/promotion/add.promotion.item', '', '_self', 0, 50, 1),
(387, 385, '编辑一个订单促销', '', '', '', 'api', 0, 'api/v1/promotion/set.promotion.item', '', '_self', 0, 50, 1),
(388, 385, '获取一个订单促销', '', '', '', 'api', 0, 'api/v1/promotion/get.promotion.item', '', '_self', 0, 50, 1),
(389, 385, '批量设置订单促销状态', '', '', '', 'api', 0, 'api/v1/promotion/set.promotion.status', '', '_self', 0, 50, 1),
(390, 385, '批量删除订单促销', '', '', '', 'api', 0, 'api/v1/promotion/del.promotion.list', '', '_self', 0, 50, 1),
(391, 385, '获取订单促销列表', '', '', '', 'api', 0, 'api/v1/promotion/get.promotion.list', '', '_self', 0, 50, 1),
(392, 385, '获取正在进行的促销列表', '', '', '', 'api', 0, 'api/v1/promotion/get.promotion.active', '', '_self', 0, 50, 1),
(393, 0, '二维码', '', '', '', 'api', 0, '', '', '_self', 0, 45, 1),
(394, 393, '动态生成一个二维码', '', '', '', 'api', 0, 'api/v1/qrcode/get.qrcode.item', '', '_self', 0, 50, 1),
(395, 393, '获取二维码调用地址', '', '', '', 'api', 0, 'api/v1/qrcode/get.qrcode.callurl', '', '_self', 0, 50, 1),
(396, 0, '区域', '', '', '', 'api', 0, '', '', '_self', 0, 46, 1),
(397, 396, '添加一个区域', '', '', '', 'api', 0, 'api/v1/region/add.region.item', '', '_self', 0, 50, 1),
(398, 396, '编辑一个区域', '', '', '', 'api', 0, 'api/v1/region/set.region.item', '', '_self', 0, 50, 1),
(399, 396, '批量删除区域', '', '', '', 'api', 0, 'api/v1/region/del.region.list', '', '_self', 0, 50, 1),
(400, 396, '获取指定区域', '', '', '', 'api', 0, 'api/v1/region/get.region.item', '', '_self', 0, 50, 1),
(401, 396, '获取指定Id下的子节点(不包含本身)', '', '', '', 'api', 0, 'api/v1/region/get.region.list', '', '_self', 0, 50, 1),
(402, 396, '获取指定Id下的所有子节点(包含本身)', '', '', '', 'api', 0, 'api/v1/region/get.region.son.list', '', '_self', 0, 50, 1),
(403, 396, '设置区域排序', '', '', '', 'api', 0, 'api/v1/region/set.region.sort', '', '_self', 0, 50, 1),
(404, 396, '根据区域编号获取区域名称', '', '', '', 'api', 0, 'api/v1/region/get.region.name', '', '_self', 0, 50, 1),
(405, 0, '系统配置', '', '', '', 'api', 0, '', '', '_self', 0, 47, 1),
(406, 405, '获取某个模块的设置', '', '', '', 'api', 0, 'api/v1/setting/get.setting.list', '', '_self', 0, 50, 1),
(407, 405, '设置配送轨迹', '', '', '', 'api', 0, 'api/v1/setting/set.delivery.dist.list', '', '_self', 0, 50, 1),
(408, 405, '设置支付完成提示页', '', '', '', 'api', 0, 'api/v1/setting/set.payment.list', '', '_self', 0, 50, 1),
(409, 405, '设置配送优惠', '', '', '', 'api', 0, 'api/v1/setting/set.delivery.list', '', '_self', 0, 50, 1),
(410, 405, '设置购物系统', '', '', '', 'api', 0, 'api/v1/setting/set.shopping.list', '', '_self', 0, 50, 1),
(411, 405, '设置售后服务', '', '', '', 'api', 0, 'api/v1/setting/set.service.list', '', '_self', 0, 50, 1),
(412, 405, '设置系统配置', '', '', '', 'api', 0, 'api/v1/setting/set.system.list', '', '_self', 0, 50, 1),
(413, 405, '设置上传配置', '', '', '', 'api', 0, 'api/v1/setting/set.upload.list', '', '_self', 0, 50, 1),
(414, 0, '商品规格', '', '', '', 'api', 0, '', '', '_self', 0, 31, 1),
(415, 414, '添加一个商品规格', '', '', '', 'api', 0, 'api/v1/spec/add.goods.spec.item', '', '_self', 0, 1, 1),
(416, 414, '编辑一个商品规格', '', '', '', 'api', 0, 'api/v1/spec/set.goods.spec.item', '', '_self', 0, 2, 1),
(417, 414, '批量删除商品规格', '', '', '', 'api', 0, 'api/v1/spec/del.goods.spec.list', '', '_self', 0, 3, 1),
(418, 414, '获取一条商品规格', '', '', '', 'api', 0, 'api/v1/spec/get.goods.spec.item', '', '_self', 0, 4, 1),
(419, 414, '获取商品规格列表', '', '', '', 'api', 0, 'api/v1/spec/get.goods.spec.list', '', '_self', 0, 5, 1),
(420, 414, '批量设置商品规格检索', '', '', '', 'api', 0, 'api/v1/spec/set.goods.spec.key', '', '_self', 0, 8, 1),
(421, 414, '设置商品规格排序', '', '', '', 'api', 0, 'api/v1/spec/set.goods.spec.sort', '', '_self', 0, 9, 1),
(422, 0, '资源管理', '', '', '', 'api', 0, '', '', '_self', 0, 48, 1),
(423, 422, '添加一个资源目录', '', '', '', 'api', 0, 'api/v1/storage/add.storage.directory.item', '', '_self', 0, 50, 1),
(424, 422, '编辑一个资源目录', '', '', '', 'api', 0, 'api/v1/storage/set.storage.directory.item', '', '_self', 0, 50, 1),
(425, 422, '获取资源目录选择列表', '', '', '', 'api', 0, 'api/v1/storage/get.storage.directory.select', '', '_self', 0, 50, 1),
(426, 422, '将资源目录标记为默认选中', '', '', '', 'api', 0, 'api/v1/storage/set.storage.directory.default', '', '_self', 0, 50, 1),
(427, 422, '获取一个资源或资源目录', '', '', '', 'api', 0, 'api/v1/storage/get.storage.item', '', '_self', 0, 50, 1),
(428, 422, '获取资源列表', '', '', '', 'api', 0, 'api/v1/storage/get.storage.list', '', '_self', 0, 50, 1),
(429, 422, '重命名一个资源', '', '', '', 'api', 0, 'api/v1/storage/rename.storage.item', '', '_self', 0, 50, 1),
(430, 422, '将图片资源设为目录封面', '', '', '', 'api', 0, 'api/v1/storage/set.storage.cover', '', '_self', 0, 50, 1),
(431, 422, '验证资源是否允许移动到指定目录', '', '', '', 'api', 0, 'api/v1/storage/check.storage.move', '', '_self', 0, 50, 1),
(432, 422, '批量移动资源到指定目录', '', '', '', 'api', 0, 'api/v1/storage/move.storage.list', '', '_self', 0, 50, 1),
(433, 422, '获取资源缩略图', '', '', '', 'api', 0, 'api/v1/storage/get.storage.thumb', '', '_self', 0, 50, 1),
(434, 422, '获取资源缩略图实际路径', '', '', '', 'api', 0, 'api/v1/storage/get.storage.thumb.url', '', '_self', 0, 50, 1),
(435, 422, '批量删除资源', '', '', '', 'api', 0, 'api/v1/storage/del.storage.list', '', '_self', 0, 50, 1),
(436, 0, '客服', '', '', '', 'api', 0, '', '', '_self', 0, 49, 1),
(437, 436, '添加一名客服', '', '', '', 'api', 0, 'api/v1/support/add.support.item', '', '_self', 0, 50, 1),
(438, 436, '编辑一名客服', '', '', '', 'api', 0, 'api/v1/support/set.support.item', '', '_self', 0, 50, 1),
(439, 436, '批量删除客服', '', '', '', 'api', 0, 'api/v1/support/del.support.list', '', '_self', 0, 50, 1),
(440, 436, '获取一名客服', '', '', '', 'api', 0, 'api/v1/support/get.support.item', '', '_self', 0, 50, 1),
(441, 436, '获取客服列表', '', '', '', 'api', 0, 'api/v1/support/get.support.list', '', '_self', 0, 50, 1),
(442, 436, '批量设置客服状态', '', '', '', 'api', 0, 'api/v1/support/set.support.status', '', '_self', 0, 50, 1),
(443, 436, '设置客服排序', '', '', '', 'api', 0, 'api/v1/support/set.support.sort', '', '_self', 0, 50, 1),
(444, 0, '专题', '', '', '', 'api', 0, '', '', '_self', 0, 50, 1),
(445, 444, '添加一个专题', '', '', '', 'api', 0, 'api/v1/topic/add.topic.item', '', '_self', 0, 50, 1),
(446, 444, '编辑一个专题', '', '', '', 'api', 0, 'api/v1/topic/set.topic.item', '', '_self', 0, 50, 1),
(447, 444, '批量删除专题', '', '', '', 'api', 0, 'api/v1/topic/del.topic.list', '', '_self', 0, 50, 1),
(448, 444, '获取一个专题', '', '', '', 'api', 0, 'api/v1/topic/get.topic.item', '', '_self', 0, 50, 1),
(449, 444, '获取专题列表', '', '', '', 'api', 0, 'api/v1/topic/get.topic.list', '', '_self', 0, 50, 1),
(450, 444, '批量设置专题是否显示', '', '', '', 'api', 0, 'api/v1/topic/set.topic.status', '', '_self', 0, 50, 1),
(451, 0, '交易结算', '', '', '', 'api', 0, '', '', '_self', 0, 51, 1),
(452, 451, '获取交易结算列表', '', '', '', 'api', 0, 'api/v1/transaction/get.transaction.list', '', '_self', 0, 50, 1),
(453, 0, '资源上传', '', '', '', 'api', 0, '', '', '_self', 0, 52, 1),
(454, 453, '获取上传模块列表', '', '', '', 'api', 0, 'api/v1/upload/get.upload.module', '', '_self', 0, 50, 1),
(455, 453, '获取上传地址', '', '', '', 'api', 0, 'api/v1/upload/get.upload.url', '', '_self', 0, 50, 1),
(456, 453, '获取上传Token', '', '', '', 'api', 0, 'api/v1/upload/get.upload.token', '', '_self', 0, 50, 1),
(457, 453, '资源上传请求', '', '', '', 'api', 0, 'api/v1/upload/add.upload.list', '', '_self', 0, 50, 1),
(458, 453, '接收第三方推送数据', '', '', '', 'api', 0, 'api/v1/upload/put.upload.data', '', '_self', 0, 50, 1),
(459, 453, '替换上传资源', '', '', '', 'api', 0, 'api/v1/upload/replace.upload.item', '', '_self', 0, 50, 1),
(460, 0, '账号管理', '', '', '', 'api', 0, '', '', '_self', 0, 53, 1),
(461, 460, '验证账号是否合法', '', '', '', 'api', 0, 'api/v1/user/check.user.username', '', '_self', 0, 1, 1),
(462, 460, '验证账号手机是否合法', '', '', '', 'api', 0, 'api/v1/user/check.user.mobile', '', '_self', 0, 2, 1),
(463, 460, '验证账号昵称是否合法', '', '', '', 'api', 0, 'api/v1/user/check.user.nickname', '', '_self', 0, 3, 1),
(464, 460, '注册一个新账号', '', '', '', 'api', 0, 'api/v1/user/add.user.item', '', '_self', 0, 4, 1),
(465, 460, '编辑一个账号', '', '', '', 'api', 0, 'api/v1/user/set.user.item', '', '_self', 0, 5, 1),
(466, 460, '批量设置账号状态', '', '', '', 'api', 0, 'api/v1/user/set.user.status', '', '_self', 0, 6, 1),
(467, 460, '修改一个账号密码', '', '', '', 'api', 0, 'api/v1/user/set.user.password', '', '_self', 0, 7, 1),
(468, 460, '批量删除账号', '', '', '', 'api', 0, 'api/v1/user/del.user.list', '', '_self', 0, 8, 1),
(469, 460, '获取一个账号', '', '', '', 'api', 0, 'api/v1/user/get.user.item', '', '_self', 0, 9, 1),
(470, 460, '获取账号列表', '', '', '', 'api', 0, 'api/v1/user/get.user.list', '', '_self', 0, 11, 1),
(471, 460, '注销账号', '', '', '', 'api', 0, 'api/v1/user/logout.user.user', '', '_self', 0, 13, 1),
(472, 460, '登录账号', '', '', '', 'api', 0, 'api/v1/user/login.user.user', '', '_self', 0, 14, 1),
(473, 460, '刷新Token', '', '', '', 'api', 0, 'api/v1/user/refresh.user.token', '', '_self', 0, 15, 1),
(474, 460, '忘记密码', '', '', '', 'api', 0, 'api/v1/user/find.user.password', '', '_self', 0, 16, 1),
(475, 0, '收货地址', '', '', '', 'api', 0, '', '', '_self', 0, 54, 1),
(476, 475, '获取指定账号的收货地址列表', '', '', '', 'api', 0, 'api/v1/user_address/get.user.address.list', '', '_self', 0, 50, 1),
(477, 475, '获取指定账号的一个收货地址', '', '', '', 'api', 0, 'api/v1/user_address/get.user.address.item', '', '_self', 0, 50, 1),
(478, 475, '添加一个收货地址', '', '', '', 'api', 0, 'api/v1/user_address/add.user.address.item', '', '_self', 0, 50, 1),
(479, 475, '编辑一个收货地址', '', '', '', 'api', 0, 'api/v1/user_address/set.user.address.item', '', '_self', 0, 50, 1),
(480, 475, '批量删除收货地址', '', '', '', 'api', 0, 'api/v1/user_address/del.user.address.list', '', '_self', 0, 50, 1),
(481, 475, '设置一个收货地址为默认', '', '', '', 'api', 0, 'api/v1/user_address/set.user.address.default', '', '_self', 0, 50, 1),
(482, 475, '检测是否超出最大添加数量', '', '', '', 'api', 0, 'api/v1/user_address/is.user.address.maximum', '', '_self', 0, 50, 1),
(483, 0, '账号等级', '', '', '', 'api', 0, '', '', '_self', 0, 55, 1),
(484, 483, '获取一个账号等级', '', '', '', 'api', 0, 'api/v1/user_level/get.user.level.item', '', '_self', 0, 50, 1),
(485, 483, '获取账号等级列表', '', '', '', 'api', 0, 'api/v1/user_level/get.user.level.list', '', '_self', 0, 50, 1),
(486, 483, '添加一个账号等级', '', '', '', 'api', 0, 'api/v1/user_level/add.user.level.item', '', '_self', 0, 50, 1),
(487, 483, '编辑一个账号等级', '', '', '', 'api', 0, 'api/v1/user_level/set.user.level.item', '', '_self', 0, 50, 1),
(488, 483, '批量删除账号等级', '', '', '', 'api', 0, 'api/v1/user_level/del.user.level.list', '', '_self', 0, 50, 1),
(489, 0, '账号资金', '', '', '', 'api', 0, '', '', '_self', 0, 56, 1),
(490, 489, '获取指定账号资金信息', '', '', '', 'api', 0, 'api/v1/user_money/get.user.money.info', '', '_self', 0, 50, 1),
(491, 0, '验证码', '', '', '', 'api', 0, '', '', '_self', 0, 57, 1),
(492, 491, '使用验证码', '', '', '', 'api', 0, 'api/v1/verification/use.verification.item', '', '_self', 0, 50, 1),
(493, 491, '发送短信验证码', '', '', '', 'api', 0, 'api/v1/verification/send.verification.sms', '', '_self', 0, 50, 1);
INSERT INTO `cs_menu` (`menu_id`, `parent_id`, `name`, `alias`, `icon`, `remark`, `module`, `type`, `url`, `params`, `target`, `is_navi`, `sort`, `status`) VALUES
(494, 491, '发送邮件验证码', '', '', '', 'api', 0, 'api/v1/verification/send.verification.email', '', '_self', 0, 50, 1),
(495, 491, '验证短信验证码', '', '', '', 'api', 0, 'api/v1/verification/ver.verification.sms', '', '_self', 0, 50, 1),
(496, 491, '验证邮件验证码', '', '', '', 'api', 0, 'api/v1/verification/ver.verification.email', '', '_self', 0, 50, 1),
(497, 0, '提现', '', '', '', 'api', 0, '', '', '_self', 0, 58, 1),
(498, 497, '获取一个提现请求', '', '', '', 'api', 0, 'api/v1/withdraw/get.withdraw.item', '', '_self', 0, 50, 1),
(499, 497, '获取提现请求列表', '', '', '', 'api', 0, 'api/v1/withdraw/get.withdraw.list', '', '_self', 0, 50, 1),
(500, 497, '申请一个提现请求', '', '', '', 'api', 0, 'api/v1/withdraw/add.withdraw.item', '', '_self', 0, 50, 1),
(501, 497, '取消一个提现请求', '', '', '', 'api', 0, 'api/v1/withdraw/cancel.withdraw.item', '', '_self', 0, 50, 1),
(502, 497, '处理一个提现请求', '', '', '', 'api', 0, 'api/v1/withdraw/process.withdraw.item', '', '_self', 0, 50, 1),
(503, 497, '完成一个提现请求', '', '', '', 'api', 0, 'api/v1/withdraw/complete.withdraw.item', '', '_self', 0, 50, 1),
(504, 497, '拒绝一个提现请求', '', '', '', 'api', 0, 'api/v1/withdraw/refuse.withdraw.item', '', '_self', 0, 50, 1),
(505, 497, '获取提现手续费', '', '', '', 'api', 0, 'api/v1/withdraw/get.withdraw.fee', '', '_self', 0, 50, 1),
(506, 0, '提现账号', '', '', '', 'api', 0, '', '', '_self', 0, 59, 1),
(507, 506, '添加一个提现账号', '', '', '', 'api', 0, 'api/v1/withdraw_user/add.withdraw.user.item', '', '_self', 0, 50, 1),
(508, 506, '编辑一个提现账号', '', '', '', 'api', 0, 'api/v1/withdraw_user/set.withdraw.user.item', '', '_self', 0, 50, 1),
(509, 506, '批量删除提现账号', '', '', '', 'api', 0, 'api/v1/withdraw_user/del.withdraw.user.list', '', '_self', 0, 50, 1),
(510, 506, '获取指定账号的一个提现账号', '', '', '', 'api', 0, 'api/v1/withdraw_user/get.withdraw.user.item', '', '_self', 0, 50, 1),
(511, 506, '获取指定账号的提现账号列表', '', '', '', 'api', 0, 'api/v1/withdraw_user/get.withdraw.user.list', '', '_self', 0, 50, 1),
(512, 506, '检测是否超出最大添加数量', '', '', '', 'api', 0, 'api/v1/withdraw_user/is.withdraw.user.maximum', '', '_self', 0, 50, 1),
(513, 0, '首页', '', 'shouye', '', 'admin', 0, '/index', '', '_self', 1, 1, 1),
(514, 0, '商品', '', 'gouwu', '', 'admin', 0, '/goods', '', '_self', 1, 2, 1),
(515, 0, '订单', '', 'yemianliu', '', 'admin', 0, '/order', '', '_self', 1, 3, 1),
(516, 0, '营销', '', 'liwu', '', 'admin', 0, '/marketing', '', '_self', 1, 4, 1),
(517, 0, '会员', '', 'CPhezuo', '', 'admin', 0, '/member', '', '_self', 1, 5, 1),
(518, 0, '数据', '', 'zhuzhuangtu', '/data', 'admin', 0, '', '', '_self', 1, 7, 0),
(519, 0, '小程序', '', 'yingyongAPP', '/app', 'admin', 0, '', '', '_self', 1, 8, 0),
(520, 0, '店铺', '', 'baoguo_shounahe', '', 'admin', 0, '/system', '', '_self', 1, 9, 1),
(521, 0, '设置', '', 'kongzhizhongxin', '', 'admin', 0, '/setting', '', '_self', 1, 10, 1),
(522, 0, '云端', '', 'yun', '/cloud', 'admin', 0, 'https://www.careyshop.cn/', '', '_blank', 1, 11, 1),
(523, 521, '运营人员', '', 'guanliyuansousuo_o', '', 'admin', 0, '/setting/admin', '', '_self', 1, 2, 1),
(524, 523, '管理人员', '', 'jingliren_o', '对管理组成员账号进行管理', 'admin', 0, '/setting/admin/member', '', '_self', 1, 1, 1),
(525, 521, '权限分配', '', 'RectangleCopy173', '', 'admin', 0, '/setting/auth', '', '_self', 1, 3, 1),
(526, 525, '用户组', '', 'qunzu_o', '为系统分配前后台用户组', 'admin', 0, '/setting/auth/group', '', '_self', 1, 1, 1),
(527, 525, '权限规则', '', 'RectangleCopy176', '将菜单访问权限分配给不同的用户组', 'admin', 0, '/setting/auth/rule', '', '_self', 1, 3, 1),
(528, 525, '菜单管理', '', 'RectangleCopy51', '为系统配置前后台导航菜单', 'admin', 0, '/setting/auth/menu', '', '_self', 1, 2, 1),
(529, 528, '新增菜单', '', '', '', 'admin', 0, '/setting/auth/menu/add', '', '_self', 0, 2, 1),
(530, 528, '删除菜单', '', '', '', 'admin', 0, '/setting/auth/menu/del', '', '_self', 0, 3, 1),
(531, 528, '编辑菜单', '', '', '', 'admin', 0, '/setting/auth/menu/set', '', '_self', 0, 4, 1),
(532, 528, '修改状态', '', '', '', 'admin', 0, '/setting/auth/menu/status', '', '_self', 0, 5, 1),
(533, 528, '移动排序', '', '', '', 'admin', 0, '/setting/auth/menu/move', '', '_self', 0, 6, 1),
(534, 513, '辅助', '', 'linggan_o', '', 'admin', 0, '/index/help', '', '_self', 0, 50, 1),
(535, 534, '清空缓存', '', '', '', 'admin', 0, '/index/help/cache', '', '_self', 0, 50, 1),
(536, 534, '优化缓存', '', '', '', 'admin', 0, '/index/help/optimize', '', '_self', 0, 50, 1),
(538, 523, '操作日志', '', 'daibanrenwu_o', '访问者的行踪轨迹记录', 'admin', 0, '/setting/admin/action', '', '_self', 1, 2, 1),
(539, 520, '消息管理', '', 'RectangleCopy3', '', 'admin', 0, '/system/message', '', '_self', 1, 4, 1),
(540, 539, '我的消息', '', 'youjian_o', '接收系统发送过来的消息', 'admin', 0, '/system/message/user', '', '_self', 1, 50, 1),
(541, 524, '新增用户', '', '', '', 'admin', 0, '/setting/admin/member/add', '', '_self', 0, 2, 1),
(542, 524, '删除用户', '', '', '', 'admin', 0, '/setting/admin/member/del', '', '_self', 0, 3, 1),
(543, 524, '编辑用户', '', '', '', 'admin', 0, '/setting/admin/member/set', '', '_self', 0, 4, 1),
(544, 524, '设为启用', '', '', '', 'admin', 0, '/setting/admin/member/enable', '', '_self', 0, 5, 1),
(545, 524, '设为禁用', '', '', '', 'admin', 0, '/setting/admin/member/disable', '', '_self', 0, 6, 1),
(546, 524, '重置密码', '', '', '', 'admin', 0, '/setting/admin/member/reset', '', '_self', 0, 7, 1),
(547, 526, '新增用户组', '', '', '', 'admin', 0, '/setting/auth/group/add', '', '_self', 0, 2, 1),
(548, 526, '删除用户组', '', '', '', 'admin', 0, '/setting/auth/group/del', '', '_self', 0, 3, 1),
(549, 526, '编辑用户组', '', '', '', 'admin', 0, '/setting/auth/group/set', '', '_self', 0, 4, 1),
(550, 526, '设为启用', '', '', '', 'admin', 0, '/setting/auth/group/enable', '', '_self', 0, 5, 1),
(551, 526, '设为禁用', '', '', '', 'admin', 0, '/setting/auth/group/disable', '', '_self', 0, 6, 1),
(552, 526, '设置排序', '', '', '', 'admin', 0, '/setting/auth/group/sort', '', '_self', 0, 7, 1),
(553, 527, '新增权限', '', '', '', 'admin', 0, '/setting/auth/rule/add', '', '_self', 0, 2, 1),
(554, 527, '删除权限', '', '', '', 'admin', 0, '/setting/auth/rule/del', '', '_self', 0, 3, 1),
(555, 527, '编辑权限', '', '', '', 'admin', 0, '/setting/auth/rule/set', '', '_self', 0, 4, 1),
(556, 527, '设为启用', '', '', '', 'admin', 0, '/setting/auth/rule/enable', '', '_self', 0, 5, 1),
(557, 527, '设为禁用', '', '', '', 'admin', 0, '/setting/auth/rule/disable', '', '_self', 0, 6, 1),
(558, 527, '移动排序', '', '', '', 'admin', 0, '/setting/auth/rule/move', '', '_self', 0, 7, 1),
(559, 520, '文章发布', '', 'xinwen_o', '', 'admin', 0, '/system/article', '', '_self', 1, 6, 1),
(560, 559, '文章分类', '', 'shuzhuangtu_o', '为文章分配可归类的分类', 'admin', 0, '/system/article/cat', '', '_self', 1, 3, 1),
(561, 559, '文章管理', '', 'tuwen_o', '管理文章的增、删、改等', 'admin', 0, '/system/article/admin', '', '_self', 1, 2, 1),
(562, 1014, '专题管理', '', 'jieshaoxinxi_o', '管理专题的增、删、改等', 'admin', 0, '/system/topic/admin', '', '_self', 1, 2, 1),
(563, 520, '广告发布', '', 'yemianliu_o', '', 'admin', 0, '/system/ads', '', '_self', 1, 5, 1),
(564, 563, '广告位置', '', 'jibao_o', '广告位置是广告列表的母版与集合', 'admin', 0, '/system/ads/position', '', '_self', 1, 1, 1),
(565, 563, '广告列表', '', 'sandengfen_o', '可单独发布或归类到广告位置', 'admin', 0, '/system/ads/ads', '', '_self', 1, 2, 1),
(566, 539, '发布消息', '', 'duanxinqunfa_o', '对外发布消息', 'admin', 0, '/system/message/send', '', '_self', 1, 50, 1),
(567, 560, '新增分类', '', '', '', 'admin', 0, '/system/article/cat/add', '', '_self', 0, 2, 1),
(568, 560, '编辑分类', '', '', '', 'admin', 0, '/system/article/cat/set', '', '_self', 0, 3, 1),
(569, 560, '删除分类', '', '', '', 'admin', 0, '/system/article/cat/del', '', '_self', 0, 4, 1),
(570, 560, '移动排序', '', '', '', 'admin', 0, '/system/article/cat/move', '', '_self', 0, 5, 1),
(571, 520, '附件资源', '', 'tianjiafujian_o', '', 'admin', 0, '/system/storage', '', '_self', 1, 8, 1),
(572, 571, '资源管理', '', 'RectangleCopy256', '对已上传的资源进行管理', 'admin', 0, '/system/storage/storage', '', '_self', 1, 50, 1),
(573, 571, '资源样式', '', 'chizi_o', '动态分配、管理资源样式', 'admin', 0, '/system/storage/style', '', '_self', 1, 50, 1),
(574, 422, '获取资源下载链接', '', '', '', 'api', 0, 'api/v1/storage/get.storage.download', '', '_self', 0, 50, 1),
(575, 0, '操作日志', '', '', '', 'api', 0, '', '', '_self', 0, 60, 1),
(576, 575, '获取一条操作日志', '', '', '', 'api', 0, 'api/v1/action_log/get.action.log.item', '', '_self', 0, 50, 1),
(577, 575, '获取操作日志列表', '', '', '', 'api', 0, 'api/v1/action_log/get.action.log.list', '', '_self', 0, 50, 1),
(578, 561, '新增文章', '', '', '', 'admin', 0, '/system/article/admin/add', '', '_self', 0, 2, 1),
(579, 561, '删除文章', '', '', '', 'admin', 0, '/system/article/admin/del', '', '_self', 0, 3, 1),
(580, 561, '编辑文章', '', '', '', 'admin', 0, '/system/article/update', '', '_self', 0, 4, 1),
(583, 561, '设为置顶', '', '', '', 'admin', 0, '/system/article/admin/top', '', '_self', 0, 7, 1),
(584, 561, '取消置顶', '', '', '', 'admin', 0, '/system/article/admin/remove_top', '', '_self', 0, 8, 1),
(585, 561, '设为启用', '', '', '', 'admin', 0, '/system/article/admin/enable', '', '_self', 0, 9, 1),
(586, 561, '设为禁用', '', '', '', 'admin', 0, '/system/article/admin/disable', '', '_self', 0, 10, 1),
(587, 524, '查询列表', '', '', '', 'admin', 0, '/setting/admin/member/list', '', '_self', 0, 1, 1),
(588, 526, '查询列表', '', '', '', 'admin', 0, '/setting/auth/group/list', '', '_self', 0, 1, 1),
(589, 528, '查询列表', '', '', '', 'admin', 0, '/setting/auth/menu/list', '', '_self', 0, 1, 1),
(590, 527, '查询列表', '', '', '', 'admin', 0, '/setting/auth/rule/list', '', '_self', 0, 1, 1),
(591, 560, '查询列表', '', '', '', 'admin', 0, '/system/article/cat/list', '', '_self', 0, 1, 1),
(592, 561, '查询列表', '', '', '', 'admin', 0, '/system/article/admin/list', '', '_self', 0, 1, 1),
(593, 562, '查询列表', '', '', '', 'admin', 0, '/system/topic/admin/list', '', '_self', 0, 50, 1),
(594, 562, '新增专题', '', '', '', 'admin', 0, '/system/topic/admin/add', '', '_self', 0, 50, 1),
(595, 562, '删除专题', '', '', '', 'admin', 0, '/system/topic/admin/del', '', '_self', 0, 50, 1),
(596, 562, '编辑专题', '', '', '', 'admin', 0, '/system/topic/update', '', '_self', 0, 50, 1),
(598, 562, '设为启用', '', '', '', 'admin', 0, '/system/topic/admin/enable', '', '_self', 0, 50, 1),
(599, 562, '设为禁用', '', '', '', 'admin', 0, '/system/topic/admin/disable', '', '_self', 0, 50, 1),
(600, 564, '查询列表', '', '', '', 'admin', 0, '/system/ads/position/list', '', '_self', 0, 50, 1),
(601, 564, '新增位置', '', '', '', 'admin', 0, '/system/ads/position/add', '', '_self', 0, 50, 1),
(602, 564, '编辑位置', '', '', '', 'admin', 0, '/system/ads/position/set', '', '_self', 0, 50, 1),
(603, 564, '删除位置', '', '', '', 'admin', 0, '/system/ads/position/del', '', '_self', 0, 50, 1),
(604, 564, '设为启用', '', '', '', 'admin', 0, '/system/ads/position/enable', '', '_self', 0, 50, 1),
(605, 564, '设为禁用', '', '', '', 'admin', 0, '/system/ads/position/disable', '', '_self', 0, 50, 1),
(606, 565, '查询列表', '', '', '', 'admin', 0, '/system/ads/ads/list', '', '_self', 0, 50, 1),
(607, 565, '新增广告', '', '', '', 'admin', 0, '/system/ads/ads/add', '', '_self', 0, 50, 1),
(608, 565, '编辑广告', '', '', '', 'admin', 0, '/system/ads/ads/set', '', '_self', 0, 50, 1),
(609, 565, '删除广告', '', '', '', 'admin', 0, '/system/ads/ads/del', '', '_self', 0, 50, 1),
(610, 565, '排序广告', '', '', '', 'admin', 0, '/system/ads/ads/sort', '', '_self', 0, 50, 1),
(612, 565, '设为启用', '', '', '', 'admin', 0, '/system/ads/ads/enable', '', '_self', 0, 50, 1),
(613, 565, '设为禁用', '', '', '', 'admin', 0, '/system/ads/ads/disable', '', '_self', 0, 50, 1),
(614, 23, '获取广告位置选择列表', '', '', '', 'api', 0, 'api/v1/ads_position/get.ads.position.select', '', '_self', 0, 50, 1),
(615, 15, '验证广告编码是否唯一', '', '', '', 'api', 0, 'api/v1/ads/unique.ads.code', '', '_self', 0, 50, 1),
(616, 23, '根据广告位置编码获取广告列表', '', '', '', 'api', 0, 'api/v1/ads_position/get.ads.position.code', '', '_self', 0, 50, 1),
(617, 15, '根据编码获取一个广告', '', '', '', 'api', 0, 'api/v1/ads/get.ads.code', '', '_self', 0, 50, 1),
(618, 15, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/ads/set.ads.index', '', '_self', 0, 50, 1),
(619, 55, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/article_cat/set.article.cat.index', '', '_self', 0, 50, 1),
(620, 71, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/auth_group/set.auth.group.index', '', '_self', 0, 50, 1),
(621, 79, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/auth_rule/set.auth.rule.index', '', '_self', 0, 50, 1),
(622, 87, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/brand/set.brand.index', '', '_self', 0, 50, 1),
(623, 150, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/delivery/set.delivery.index', '', '_self', 0, 50, 1),
(624, 190, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/friend_link/set.friendlink.index', '', '_self', 0, 50, 1),
(625, 218, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/goods_attribute/set.goods.attribute.index', '', '_self', 0, 15, 1),
(626, 232, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/goods_category/set.goods.category.index', '', '_self', 0, 50, 1),
(627, 285, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/menu/set.menu.index', '', '_self', 0, 50, 1),
(628, 311, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/navigation/set.navigation.index', '', '_self', 0, 50, 1),
(629, 369, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/payment/set.payment.index', '', '_self', 0, 50, 1),
(630, 396, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/region/set.region.index', '', '_self', 0, 50, 1),
(631, 414, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/spec/set.goods.spec.index', '', '_self', 0, 10, 1),
(632, 436, '根据编号自动排序', '', '', '', 'api', 0, 'api/v1/support/set.support.index', '', '_self', 0, 50, 1),
(633, 0, '资源样式', '', '', '', 'api', 0, '', '', '_self', 0, 61, 1),
(634, 633, '验证资源样式编码是否唯一', '', '', '', 'api', 0, 'api/v1/storage_style/unique.storage.style.code', '', '_self', 0, 50, 1),
(635, 633, '添加一个资源样式', '', '', '', 'api', 0, 'api/v1/storage_style/add.storage.style.item', '', '_self', 0, 50, 1),
(636, 633, '编辑一个资源样式', '', '', '', 'api', 0, 'api/v1/storage_style/set.storage.style.item', '', '_self', 0, 50, 1),
(637, 633, '获取一个资源样式', '', '', '', 'api', 0, 'api/v1/storage_style/get.storage.style.item', '', '_self', 0, 50, 1),
(638, 633, '获取资源样式列表', '', '', '', 'api', 0, 'api/v1/storage_style/get.storage.style.list', '', '_self', 0, 50, 1),
(639, 633, '批量删除资源样式', '', '', '', 'api', 0, 'api/v1/storage_style/del.storage.style.list', '', '_self', 0, 50, 1),
(640, 633, '批量设置资源样式状态', '', '', '', 'api', 0, 'api/v1/storage_style/set.storage.style.status', '', '_self', 0, 50, 1),
(641, 422, '获取资源缩略图信息', '', '', '', 'api', 0, 'api/v1/storage/get.storage.thumb.info', '', '_self', 0, 50, 1),
(642, 573, '查询列表', '', '', '', 'admin', 0, '/system/storage/style/list', '', '_self', 0, 50, 1),
(643, 573, '新增样式', '', '', '', 'admin', 0, '/system/storage/style/add', '', '_self', 0, 50, 1),
(644, 573, '编辑样式', '', '', '', 'admin', 0, '/system/storage/style/set', '', '_self', 0, 50, 1),
(645, 573, '删除样式', '', '', '', 'admin', 0, '/system/storage/style/del', '', '_self', 0, 50, 1),
(646, 573, '设为启用', '', '', '', 'admin', 0, '/system/storage/style/enable', '', '_self', 0, 50, 1),
(647, 573, '设为禁用', '', '', '', 'admin', 0, '/system/storage/style/disable', '', '_self', 0, 50, 1),
(648, 422, '获取导航数据', '', '', '', 'api', 0, 'api/v1/storage/get.storage.navi', '', '_self', 0, 50, 1),
(650, 710, '支付日志', '', 'huobiliu_o', '支付行为、结果的流水账', 'admin', 0, '/setting/payment/log', '', '_self', 1, 50, 1),
(651, 812, '交易结算', '', 'RectangleCopy218', '账户资金支出、收入的流水账', 'admin', 0, '/member/user/transaction', '', '_self', 1, 4, 1),
(652, 520, '辅助管理', '', 'zhongkong_o', '', 'admin', 0, '/system/aided', '', '_self', 1, 9, 1),
(653, 652, '客服人员', '', 'kefu_o', '添加自定义代码，供前端调用', 'admin', 0, '/system/aided/support', '', '_self', 1, 1, 1),
(654, 652, '友情链接', '', 'lianjie_o', '友情链接管理', 'admin', 0, '/system/aided/friendlink', '', '_self', 1, 3, 1),
(655, 652, '二维码', '', 'erweima_o', '可作为物料的发布与管理', 'admin', 0, '/system/aided/qrcode', '', '_self', 1, 4, 1),
(656, 711, 'App应用', '', 'yingyongAPP_o', '移动、PC端都属于一个独立的APP', 'admin', 0, '/setting/app/app', '', '_self', 1, 7, 1),
(657, 0, '帮助文档', '', '', '', 'api', 0, '', '', '_self', 0, 62, 1),
(658, 657, '添加一条帮助文档', '', '', '', 'api', 0, 'api/v1/help/add.help.item', '', '_self', 0, 50, 1),
(659, 657, '编辑一条帮助文档', '', '', '', 'api', 0, 'api/v1/help/set.help.item', '', '_self', 0, 50, 1),
(660, 657, '获取一条帮助文档', '', '', '', 'api', 0, 'api/v1/help/get.help.item', '', '_self', 0, 50, 1),
(661, 657, '验证帮助文档是否唯一', '', '', '', 'api', 0, 'api/v1/help/unique.help.item', '', '_self', 0, 50, 1),
(662, 657, '获取帮助文档列表', '', '', '', 'api', 0, 'api/v1/help/get.help.list', '', '_self', 0, 50, 1),
(663, 657, '根据路由获取帮助文档', '', '', '', 'api', 0, 'api/v1/help/get.help.router', '', '_self', 0, 50, 1),
(664, 422, '清除图片资源缓存', '', '', '', 'api', 0, 'api/v1/storage/clear.storage.thumb', '', '_self', 0, 50, 1),
(665, 422, '清除目录资源的封面', '', '', '', 'api', 0, 'api/v1/storage/clear.storage.cover', '', '_self', 0, 50, 1),
(667, 572, '查询列表', '', '', '', 'admin', 0, '/system/storage/storage/list', '', '_self', 0, 1, 1),
(668, 572, '新增目录', '', '', '', 'admin', 0, '/system/storage/storage/add', '', '_self', 0, 2, 1),
(669, 572, '上传资源', '', '', '', 'admin', 0, '/system/storage/storage/upload', '', '_self', 0, 3, 1),
(670, 572, '重命名', '', '', '', 'admin', 0, '/system/storage/storage/rename', '', '_self', 0, 4, 1),
(671, 572, '替换上传', '', '', '', 'admin', 0, '/system/storage/storage/replace', '', '_self', 0, 5, 1),
(672, 572, '设为封面', '', '', '', 'admin', 0, '/system/storage/storage/cover', '', '_self', 0, 6, 1),
(673, 572, '设为(取消)默认', '', '', '', 'admin', 0, '/system/storage/storage/default', '', '_self', 0, 8, 1),
(674, 572, '取消封面', '', '', '', 'admin', 0, '/system/storage/storage/clear_cover', '', '_self', 0, 7, 1),
(675, 572, '转移目录', '', '', '', 'admin', 0, '/system/storage/storage/move', '', '_self', 0, 9, 1),
(676, 572, '删除资源', '', '', '', 'admin', 0, '/system/storage/storage/del', '', '_self', 0, 10, 1),
(677, 572, '清除缓存', '', '', '', 'admin', 0, '/system/storage/storage/refresh', '', '_self', 0, 11, 1),
(678, 572, '复制外链', '', '', '', 'admin', 0, '/system/storage/storage/link', '', '_self', 0, 12, 1),
(679, 534, '未读消息', '', '', '', 'admin', 0, '/system/message/unread', '', '_self', 0, 50, 1),
(680, 566, '查询列表', '', '', '', 'admin', 0, '/system/message/send/list', '', '_self', 0, 50, 1),
(681, 566, '新增消息', '', '', '', 'admin', 0, '/system/message/send/add', '', '_self', 0, 50, 1),
(682, 566, '编辑消息', '', '', '', 'admin', 0, '/system/message/send/set', '', '_self', 0, 50, 1),
(683, 566, '删除消息', '', '', '', 'admin', 0, '/system/message/send/del', '', '_self', 0, 50, 1),
(684, 566, '正式发布', '', '', '', 'admin', 0, '/system/message/send/status', '', '_self', 0, 50, 1),
(685, 566, '消息预览', '', '', '', 'admin', 0, '/system/message/send/view', '', '_self', 0, 50, 1),
(686, 540, '查询列表', '', '', '', 'admin', 0, '/system/message/user/list', '', '_self', 0, 50, 1),
(687, 540, '标记已读', '', '', '', 'admin', 0, '/system/message/user/read', '', '_self', 0, 50, 1),
(688, 540, '全部已读', '', '', '', 'admin', 0, '/system/message/user/read_all', '', '_self', 0, 50, 1),
(689, 540, '批量删除', '', '', '', 'admin', 0, '/system/message/user/del', '', '_self', 0, 50, 1),
(690, 540, '全部删除', '', '', '', 'admin', 0, '/system/message/user/del_all', '', '_self', 0, 50, 1),
(691, 653, '查询列表', '', '', '', 'admin', 0, '/system/aided/support/list', '', '_self', 0, 1, 1),
(692, 653, '新增客服', '', '', '', 'admin', 0, '/system/aided/support/add', '', '_self', 0, 2, 1),
(693, 653, '删除客服', '', '', '', 'admin', 0, '/system/aided/support/del', '', '_self', 0, 4, 1),
(694, 653, '设为启用', '', '', '', 'admin', 0, '/system/aided/support/enable', '', '_self', 0, 6, 1),
(695, 653, '设为禁用', '', '', '', 'admin', 0, '/system/aided/support/disable', '', '_self', 0, 7, 1),
(696, 653, '编辑客服', '', '', '', 'admin', 0, '/system/aided/support/set', '', '_self', 0, 3, 1),
(697, 653, '排序客服', '', '', '', 'admin', 0, '/system/aided/support/sort', '', '_self', 0, 5, 1),
(698, 711, 'App安装包', '', 'shitujuzhen_o', 'APP安装包的发布与管理', 'admin', 0, '/setting/app/app_install', '', '_self', 1, 7, 1),
(699, 654, '查询列表', '', '', '', 'admin', 0, '/system/aided/friendlink/list', '', '_self', 0, 50, 1),
(700, 654, '新增链接', '', '', '', 'admin', 0, '/system/aided/friendlink/add', '', '_self', 0, 50, 1),
(701, 654, '编辑链接', '', '', '', 'admin', 0, '/system/aided/friendlink/set', '', '_self', 0, 50, 1),
(702, 654, '删除链接', '', '', '', 'admin', 0, '/system/aided/friendlink/del', '', '_self', 0, 50, 1),
(703, 654, '排序链接', '', '', '', 'admin', 0, '/system/aided/friendlink/sort', '', '_self', 0, 50, 1),
(704, 654, '设为启用', '', '', '', 'admin', 0, '/system/aided/friendlink/enable', '', '_self', 0, 50, 1),
(705, 654, '设为禁用', '', '', '', 'admin', 0, '/system/aided/friendlink/disable', '', '_self', 0, 50, 1),
(706, 656, '查询列表', '', '', '', 'admin', 0, '/setting/app/app/list', '', '_self', 0, 50, 1),
(707, 698, '查询列表', '', '', '', 'admin', 0, '/setting/app/app_install/list', '', '_self', 0, 50, 1),
(708, 655, '查询列表', '', '', '', 'admin', 0, '/system/aided/qrcode/list', '', '_self', 0, 50, 1),
(709, 521, '物流管控', '', 'che_o', '', 'admin', 0, '/setting/logistics', '', '_self', 1, 6, 1),
(710, 521, '支付系统', '', 'jinbi_o', '', 'admin', 0, '/setting/payment', '', '_self', 1, 5, 1),
(711, 521, '应用管理', '', 'RectangleCopy16', '', 'admin', 0, '/setting/app', '', '_self', 1, 8, 1),
(712, 521, '店铺设置', '', 'kongzhizhongxin_o', '', 'admin', 0, '/setting/setting', '', '_self', 1, 4, 1),
(713, 712, '系统管理', '', 'quanjushezhi_o', '系统基础的参数设置', 'admin', 0, '/setting/setting/system', '', '_self', 1, 1, 1),
(714, 656, '新增应用', '', '', '', 'admin', 0, '/setting/app/app/add', '', '_self', 0, 50, 1),
(715, 656, '编辑应用', '', '', '', 'admin', 0, '/setting/app/app/set', '', '_self', 0, 50, 1),
(716, 656, '删除应用', '', '', '', 'admin', 0, '/setting/app/app/del', '', '_self', 0, 50, 1),
(717, 656, '重置密钥', '', '', '', 'admin', 0, '/setting/app/app/replace', '', '_self', 0, 50, 1),
(718, 656, '设为启用', '', '', '', 'admin', 0, '/setting/app/app/enable', '', '_self', 0, 50, 1),
(719, 656, '设为禁用', '', '', '', 'admin', 0, '/setting/app/app/disable', '', '_self', 0, 50, 1),
(720, 698, '新增安装包', '', '', '', 'admin', 0, '/setting/app/app_install/add', '', '_self', 0, 50, 1),
(721, 698, '编辑安装包', '', '', '', 'admin', 0, '/setting/app/app_install/set', '', '_self', 0, 50, 1),
(722, 698, '删除安装包', '', '', '', 'admin', 0, '/setting/app/app_install/del', '', '_self', 0, 50, 1),
(723, 393, '添加一个二维码', '', '', '', 'api', 0, 'api/v1/qrcode/add.qrcode.item', '', '_self', 0, 50, 1),
(724, 393, '编辑一个二维码', '', '', '', 'api', 0, 'api/v1/qrcode/set.qrcode.item', '', '_self', 0, 50, 1),
(725, 393, '获取一个二维码', '', '', '', 'api', 0, 'api/v1/qrcode/get.qrcode.config', '', '_self', 0, 50, 1),
(726, 393, '获取二维码列表', '', '', '', 'api', 0, 'api/v1/qrcode/get.qrcode.list', '', '_self', 0, 50, 1),
(727, 393, '批量删除二维码', '', '', '', 'api', 0, 'api/v1/qrcode/del.qrcode.list', '', '_self', 0, 50, 1),
(728, 655, '新增二维码', '', '', '', 'admin', 0, '/system/aided/qrcode/add', '', '_self', 0, 50, 1),
(729, 655, '编辑二维码', '', '', '', 'admin', 0, '/system/aided/qrcode/set', '', '_self', 0, 50, 1),
(730, 655, '删除二维码', '', '', '', 'admin', 0, '/system/aided/qrcode/del', '', '_self', 0, 50, 1),
(731, 655, '预览二维码', '', '', '', 'admin', 0, '/system/aided/qrcode/view', '', '_self', 0, 50, 1),
(732, 712, '前台导航', '', 'daohang_o', '前台主导航栏的管理', 'admin', 0, '/setting/setting/navi', '', '_self', 1, 3, 1),
(733, 712, '系统信息', '', 'guanyu_o', '系统常用信息的显示', 'admin', 0, '/setting/setting/info', '', '_self', 1, 2, 1),
(734, 281, '获取系统版本号', '', '', '', 'api', 0, 'api/v1/index/get.system.version', '', '_self', 0, 50, 1),
(735, 520, '系统首页', '', 'shouye_o', '', 'admin', 0, '/system/index', '', '_self', 1, 1, 1),
(740, 521, '设置首页', '', 'shouye_o', '', 'admin', 0, '/setting/index', '', '_self', 1, 1, 1),
(741, 732, '查询列表', '', '', '', 'admin', 0, '/setting/setting/navi/list', '', '_self', 0, 50, 1),
(742, 732, '新增导航', '', '', '', 'admin', 0, '/setting/setting/navi/add', '', '_self', 0, 50, 1),
(743, 732, '编辑导航', '', '', '', 'admin', 0, '/setting/setting/navi/set', '', '_self', 0, 50, 1),
(744, 732, '删除导航', '', '', '', 'admin', 0, '/setting/setting/navi/del', '', '_self', 0, 50, 1),
(745, 732, '排序导航', '', '', '', 'admin', 0, '/setting/setting/navi/sort', '', '_self', 0, 50, 1),
(746, 732, '设为启用', '', '', '', 'admin', 0, '/setting/setting/navi/enable', '', '_self', 0, 50, 1),
(747, 732, '设为禁用', '', '', '', 'admin', 0, '/setting/setting/navi/disable', '', '_self', 0, 50, 1),
(748, 710, '支付配置', '', 'jinrongguanli_o', '各个支付平台参数的配置', 'admin', 0, '/setting/payment/config', '', '_self', 1, 1, 1),
(751, 748, '查询列表', '', '', '', 'admin', 0, '/setting/payment/config/list', '', '_self', 0, 50, 1),
(752, 748, '编辑支付', '', '', '', 'admin', 0, '/setting/payment/config/set', '', '_self', 0, 50, 1),
(753, 748, '配置参数', '', '', '', 'admin', 0, '/setting/payment/config/setting', '', '_self', 0, 50, 1),
(754, 748, '排序支付', '', '', '', 'admin', 0, '/setting/payment/config/sort', '', '_self', 0, 50, 1),
(755, 748, '设为启用', '', '', '', 'admin', 0, '/setting/payment/config/enable', '', '_self', 0, 50, 1),
(756, 748, '设为禁用', '', '', '', 'admin', 0, '/setting/payment/config/disable', '', '_self', 0, 50, 1),
(757, 709, '区域管理', '', 'ditu_diqiu_o', '对国家、省、市、区等地域进行管理', 'admin', 0, '/setting/logistics/region', '', '_self', 1, 1, 1),
(758, 709, '快递公司', '', 'jianzhu_o', '管理常见的快递公司，供前台调用', 'admin', 0, '/setting/logistics/company', '', '_self', 1, 2, 1),
(759, 652, '配送轨迹', '', 'didiandingwei_o', '物流轨迹流水记录操作', 'admin', 0, '/system/aided/dist', '', '_self', 1, 2, 1),
(760, 709, '配送方式', '', 'paijian_o', '配送区域费用，配送方式等管理', 'admin', 0, '/setting/logistics/delivery', '', '_self', 1, 3, 1),
(764, 757, '查询列表', '', '', '', 'admin', 0, '/setting/logistics/region/list', '', '_self', 0, 50, 1),
(765, 757, '新增区域', '', '', '', 'admin', 0, '/setting/logistics/region/add', '', '_self', 0, 50, 1),
(766, 757, '编辑区域', '', '', '', 'admin', 0, '/setting/logistics/region/set', '', '_self', 0, 50, 1),
(767, 757, '删除区域', '', '', '', 'admin', 0, '/setting/logistics/region/del', '', '_self', 0, 50, 1),
(768, 757, '移动区域', '', '', '', 'admin', 0, '/setting/logistics/region/move', '', '_self', 0, 50, 1),
(769, 758, '查询列表', '', '', '', 'admin', 0, '/setting/logistics/company/list', '', '_self', 0, 50, 1),
(770, 758, '新增公司', '', '', '', 'admin', 0, '/setting/logistics/company/add', '', '_self', 0, 50, 1),
(771, 758, '编辑公司', '', '', '', 'admin', 0, '/setting/logistics/company/set', '', '_self', 0, 50, 1),
(772, 758, '删除公司', '', '', '', 'admin', 0, '/setting/logistics/company/del', '', '_self', 0, 50, 1),
(773, 758, '复制为热门', '', '', '', 'admin', 0, '/setting/logistics/company/copy', '', '_self', 0, 50, 1),
(774, 167, '根据快递单号即时查询配送轨迹', '', '', '', 'api', 0, 'api/v1/delivery_dist/get.delivery.dist.trace', '', '_self', 0, 5, 1),
(777, 173, '根据快递单号识别快递公司', '', '', '', 'api', 0, 'api/v1/delivery_item/get.delivery.company.recognise', '', '_self', 0, 8, 1),
(778, 760, '查询列表', '', '', '', 'admin', 0, '/setting/logistics/delivery/list', '', '_self', 0, 1, 1),
(779, 760, '新增方式', '', '', '', 'admin', 0, '/setting/logistics/delivery/add', '', '_self', 0, 2, 1),
(780, 760, '编辑方式', '', '', '', 'admin', 0, '/setting/logistics/delivery/set', '', '_self', 0, 3, 1),
(781, 760, '删除方式', '', '', '', 'admin', 0, '/setting/logistics/delivery/del', '', '_self', 0, 4, 1),
(782, 760, '配送区域', '', '', '', 'admin', 0, '/setting/logistics/delivery/area', '', '_self', 0, 6, 1),
(783, 760, '设为启用', '', '', '', 'admin', 0, '/setting/logistics/delivery/enable', '', '_self', 0, 7, 1),
(784, 760, '设为禁用', '', '', '', 'admin', 0, '/setting/logistics/delivery/disable', '', '_self', 0, 8, 1),
(785, 760, '排序方式', '', '', '', 'admin', 0, '/setting/logistics/delivery/sort', '', '_self', 0, 5, 1),
(786, 782, '查询列表', '', '', '', 'admin', 0, '/setting/logistics/delivery/area/list', '', '_self', 0, 50, 1),
(787, 782, '新增区域', '', '', '', 'admin', 0, '/setting/logistics/delivery/area/add', '', '_self', 0, 50, 1),
(788, 782, '编辑区域', '', '', '', 'admin', 0, '/setting/logistics/delivery/area/set', '', '_self', 0, 50, 1),
(789, 782, '删除区域', '', '', '', 'admin', 0, '/setting/logistics/delivery/area/del', '', '_self', 0, 50, 1),
(790, 782, '所辖区域', '', '', '', 'admin', 0, '/setting/logistics/delivery/area/region', '', '_self', 0, 50, 1),
(802, 712, '消息模板', '', 'daochu_o', '预设计消息推送内容模板', 'admin', 0, '/setting/setting/template', '', '_self', 1, 50, 1),
(804, 759, '查询列表', '', '', '', 'admin', 0, '/system/aided/dist/list', '', '_self', 0, 50, 1),
(805, 759, '即时查询', '', '', '', 'admin', 0, '/system/aided/dist/trace', '', '_self', 0, 50, 1),
(806, 802, '查询列表', '', '', '', 'admin', 0, '/setting/setting/template/list', '', '_self', 0, 50, 1),
(807, 802, '短信配置', '', '', '', 'admin', 0, '/setting/setting/template/sms_setting', '', '_self', 0, 50, 1),
(808, 802, '邮件配置', '', '', '', 'admin', 0, '/setting/setting/template/email_setting', '', '_self', 0, 50, 1),
(809, 802, '设为启用', '', '', '', 'admin', 0, '/setting/setting/template/enable', '', '_self', 0, 50, 1),
(810, 802, '设为禁用', '', '', '', 'admin', 0, '/setting/setting/template/disable', '', '_self', 0, 50, 1),
(811, 802, '编辑模板', '', '', '', 'admin', 0, '/setting/setting/template/tpl', '', '_self', 0, 50, 1),
(812, 517, '账号管理', '', 'CPhezuo_o', '', 'admin', 0, '/member/user', '', '_self', 1, 2, 1),
(813, 812, '会员账号', '', 'gerentouxiang_o', '对顾客组成员账号进行管理', 'admin', 0, '/member/user/client', '', '_self', 1, 1, 1),
(814, 812, '账号等级', '', 'chengchang_o', '会员等级、折扣额度的管理', 'admin', 0, '/member/user/level', '', '_self', 1, 3, 1),
(815, 517, '提现审批', '', 'jinbi_o', '', 'admin', 0, '/member/withdraw', '', '_self', 1, 4, 1),
(816, 815, '提现列表', '', 'jiekuan_o', '提现申请的审批、拒绝、查询', 'admin', 0, '/member/withdraw/list', '', '_self', 1, 50, 1),
(817, 517, '咨询问答', '', 'RectangleCopy46', '', 'admin', 0, '/member/ask', '', '_self', 1, 5, 1),
(818, 817, '问答列表', '', 'RectangleCopy223', '咨询、售后、投诉、求购问答管理', 'admin', 0, '/member/ask/list', '', '_self', 1, 50, 1),
(819, 517, '会员首页', '', 'shouye_o', '', 'admin', 0, '/member/index', '', '_self', 1, 1, 1),
(820, 814, '查询列表', '', '', '', 'admin', 0, '/member/user/level/list', '', '_self', 0, 50, 1),
(821, 814, '新增等级', '', '', '', 'admin', 0, '/member/user/level/add', '', '_self', 0, 50, 1),
(822, 814, '编辑等级', '', '', '', 'admin', 0, '/member/user/level/set', '', '_self', 0, 50, 1),
(823, 814, '删除等级', '', '', '', 'admin', 0, '/member/user/level/del', '', '_self', 0, 50, 1),
(824, 816, '查询列表', '', '', '', 'admin', 0, '/member/withdraw/list/list', '', '_self', 0, 50, 1),
(825, 816, '处理提现', '', '', '', 'admin', 0, '/member/withdraw/list/process', '', '_self', 0, 50, 1),
(826, 816, '完成提现', '', '', '', 'admin', 0, '/member/withdraw/list/complete', '', '_self', 0, 50, 1),
(827, 816, '拒绝提现', '', '', '', 'admin', 0, '/member/withdraw/list/refuse', '', '_self', 0, 50, 1),
(828, 813, '查询列表', '', '', '', 'admin', 0, '/member/user/client/list', '', '_self', 0, 50, 1),
(829, 813, '新增账号', '', '', '', 'admin', 0, '/member/user/client/add', '', '_self', 0, 50, 1),
(830, 813, '编辑账号', '', '', '', 'admin', 0, '/member/user/client/set', '', '_self', 0, 50, 1),
(831, 813, '删除账号', '', '', '', 'admin', 0, '/member/user/client/del', '', '_self', 0, 50, 1),
(832, 813, '设为启用', '', '', '', 'admin', 0, '/member/user/client/enable', '', '_self', 0, 50, 1),
(833, 813, '设为禁用', '', '', '', 'admin', 0, '/member/user/client/disable', '', '_self', 0, 50, 1),
(834, 813, '更多操作', '', '', '', 'admin', 0, '/member/user/client/more', '', '_self', 0, 50, 1),
(835, 834, '重置密码', '', '', '', 'admin', 0, '/member/user/client/reset', '', '_self', 0, 50, 1),
(836, 834, '提现账户', '', '', '', 'admin', 0, '/member/user/client/withdraw', '', '_self', 0, 50, 1),
(837, 834, '收货地址', '', '', '', 'admin', 0, '/member/user/client/address', '', '_self', 0, 50, 1),
(838, 834, '账户资金', '', '', '', 'admin', 0, '/member/user/client/money', '', '_self', 0, 50, 1),
(839, 834, '调整资金', '', '', '', 'admin', 0, '/member/user/client/finance', '', '_self', 0, 50, 1),
(840, 460, '获取一个账号的简易信息', '', '', '', 'api', 0, 'api/v1/user/get.user.info', '', '_self', 0, 10, 1),
(841, 837, '查询列表', '', '', '', 'admin', 0, '/member/user/address', '', '_self', 0, 50, 1),
(842, 837, '新增地址', '', '', '', 'admin', 0, '/member/user/address/add', '', '_self', 0, 50, 1),
(843, 837, '编辑地址', '', '', '', 'admin', 0, '/member/user/address/set', '', '_self', 0, 50, 1),
(844, 837, '删除地址', '', '', '', 'admin', 0, '/member/user/address/del', '', '_self', 0, 50, 1),
(845, 837, '设为默认', '', '', '', 'admin', 0, '/member/user/address/default', '', '_self', 0, 50, 1),
(846, 836, '查询列表', '', '', '', 'admin', 0, '/member/user/withdraw', '', '_self', 0, 50, 1),
(847, 836, '新增账户', '', '', '', 'admin', 0, '/member/user/withdraw/add', '', '_self', 0, 50, 1),
(848, 836, '编辑账户', '', '', '', 'admin', 0, '/member/user/withdraw/set', '', '_self', 0, 50, 1),
(849, 836, '删除账户', '', '', '', 'admin', 0, '/member/user/withdraw/del', '', '_self', 0, 50, 1),
(850, 818, '问答明细', '', '', '', 'admin', 0, '/member/ask/detail', '', '_self', 0, 3, 1),
(851, 818, '删除问答', '', '', '', 'admin', 0, '/member/ask/list/del', '', '_self', 0, 2, 1),
(852, 818, '查询列表', '', '', '', 'admin', 0, '/member/ask/list/list', '', '_self', 0, 1, 1),
(854, 516, '营销首页', '', 'shouye_o', '', 'admin', 0, '/marketing/index', '', '_self', 1, 1, 1),
(855, 516, '优惠劵', '', 'RectangleCopy42', '', 'admin', 0, '/marketing/coupon', '', '_self', 1, 3, 1),
(856, 516, '购物卡', '', 'yinhangqia_o', '', 'admin', 0, '/marketing/card', '', '_self', 1, 4, 1),
(857, 516, '营销中心', '', 'liwu_o', '', 'admin', 0, '/marketing/marketing', '', '_self', 1, 2, 1),
(858, 857, '商品折扣', '', 'RectangleCopy72', '下单前对商品的折扣优惠', 'admin', 0, '/marketing/marketing/discount', '', '_self', 1, 50, 1),
(859, 857, '订单促销', '', 'RectangleCopy154', '下单后对订单的促销优惠', 'admin', 0, '/marketing/marketing/promotion', '', '_self', 1, 50, 1),
(860, 855, '优惠劵', '', 'RectangleCopy42', '优惠劵的发放与管理', 'admin', 0, '/marketing/coupon/list', '', '_self', 1, 50, 1),
(861, 855, '优惠劵发放', '', 'zhibi_renminbi_o', '对生成的优惠劵进行发放、导出', 'admin', 0, '/marketing/coupon/give', '', '_self', 1, 50, 1),
(862, 856, '购物卡', '', 'yinhangqia_o', '购物卡的生成与管理', 'admin', 0, '/marketing/card/list', '', '_self', 1, 50, 1),
(864, 856, '购物卡使用', '', 'qianbao_o', '对已绑定的购物卡进行管理', 'admin', 0, '/marketing/card/use', '', '_self', 1, 50, 1),
(865, 868, '订单列表', '', 'RectangleCopy153', '订单的常规管理', 'admin', 0, '/order/admin/list', '', '_self', 1, 1, 1),
(866, 868, '退款日志', '', 'cunkuan_o', '订单生成退款后的日志', 'admin', 0, '/order/admin/refund', '', '_self', 1, 4, 1),
(867, 515, '订单首页', '', 'shouye_o', '', 'admin', 0, '/order/index', '', '_self', 1, 1, 1),
(868, 515, '订单管理', '', 'yemianliu_o', '', 'admin', 0, '/order/admin', '', '_self', 1, 2, 1),
(869, 515, '售后中心', '', 'RectangleCopy254', '', 'admin', 0, '/order/service', '', '_self', 1, 50, 1),
(870, 869, '售后列表', '', 'fuwu_o', '买家申请售后服务', 'admin', 0, '/order/service/list', '', '_self', 1, 1, 1),
(871, 514, '商品首页', '', 'shouye_o', '', 'admin', 0, '/goods/index', '', '_self', 1, 1, 1),
(872, 514, '商品管理', '', 'gouwu_o', '', 'admin', 0, '/goods/admin', '', '_self', 1, 2, 1),
(873, 872, '商品列表', '', 'RectangleCopy75', '商品的常规管理', 'admin', 0, '/goods/admin/list', '', '_self', 1, 2, 1),
(874, 882, '商品分类', '', 'cengji_o', '商品分类的常规管理', 'admin', 0, '/goods/setting/category', '', '_self', 1, 1, 1),
(875, 882, '商品品牌', '', 'RectangleCopy175', '商品品牌的常规管理', 'admin', 0, '/goods/setting/brand', '', '_self', 1, 2, 1),
(876, 882, '商品模型', '', 'RectangleCopy233', '商品模型的设置', 'admin', 0, '/goods/setting/type', '', '_self', 1, 3, 1),
(877, 882, '商品属性', '', 'fuzhi_o', '商品属性的设置', 'admin', 0, '/goods/setting/attribute', '', '_self', 1, 5, 1),
(878, 882, '商品规格', '', 'liangliangduibi_o', '商品规格的设置', 'admin', 0, '/goods/setting/spec', '', '_self', 1, 4, 1),
(879, 514, '评价咨询', '', 'jianpan_o', '', 'admin', 0, '/goods/opinion', '', '_self', 1, 6, 1),
(880, 879, '商品评价', '', 'RectangleCopy240', '买家收货后对商品的评价', 'admin', 0, '/goods/opinion/comment', '', '_self', 1, 50, 1),
(881, 879, '商品咨询', '', 'wangwang_o', '买家购买前对商品的咨询', 'admin', 0, '/goods/opinion/consult', '', '_self', 1, 50, 1),
(882, 514, '商品配置', '', 'shebeidadian_o', '', 'admin', 0, '/goods/setting', '', '_self', 1, 5, 1),
(884, 874, '查询列表', '', '', '', 'admin', 0, '/goods/setting/category/list', '', '_self', 0, 50, 1),
(885, 874, '新增分类', '', '', '', 'admin', 0, '/goods/setting/category/add', '', '_self', 0, 50, 1),
(886, 874, '编辑分类', '', '', '', 'admin', 0, '/goods/setting/category/set', '', '_self', 0, 50, 1),
(887, 874, '删除分类', '', '', '', 'admin', 0, '/goods/setting/category/del', '', '_self', 0, 50, 1),
(888, 874, '启用分类', '', '', '', 'admin', 0, '/goods/setting/category/enable', '', '_self', 0, 50, 1),
(889, 874, '禁用分类', '', '', '', 'admin', 0, '/goods/setting/category/disable', '', '_self', 0, 50, 1),
(890, 874, '移动分类', '', '', '', 'admin', 0, '/goods/setting/category/move', '', '_self', 0, 50, 1),
(891, 875, '查询列表', '', '', '', 'admin', 0, '/goods/setting/brand/list', '', '_self', 0, 1, 1),
(892, 875, '新增品牌', '', '', '', 'admin', 0, '/goods/setting/brand/add', '', '_self', 0, 2, 1),
(893, 875, '编辑品牌', '', '', '', 'admin', 0, '/goods/setting/brand/set', '', '_self', 0, 3, 1),
(894, 875, '删除品牌', '', '', '', 'admin', 0, '/goods/setting/brand/del', '', '_self', 0, 4, 1),
(895, 875, '设为启用', '', '', '', 'admin', 0, '/goods/setting/brand/enable', '', '_self', 0, 7, 1),
(896, 875, '设为禁用', '', '', '', 'admin', 0, '/goods/setting/brand/disable', '', '_self', 0, 8, 1),
(897, 875, '排序品牌', '', '', '', 'admin', 0, '/goods/setting/brand/sort', '', '_self', 0, 5, 1),
(898, 875, '打开链接', '', '', '', 'admin', 0, '/goods/setting/brand/url', '', '_self', 0, 6, 1),
(901, 876, '查询列表', '', '', '', 'admin', 0, '/goods/setting/type/list', '', '_self', 0, 50, 1),
(902, 876, '新增模型', '', '', '', 'admin', 0, '/goods/setting/type/add', '', '_self', 0, 50, 1),
(903, 876, '编辑模型', '', '', '', 'admin', 0, '/goods/setting/type/set', '', '_self', 0, 50, 1),
(904, 876, '删除模型', '', '', '', 'admin', 0, '/goods/setting/type/del', '', '_self', 0, 50, 1),
(905, 876, '商品属性', '', '', '', 'admin', 0, '/goods/setting/type/attribute', '', '_self', 0, 50, 1),
(906, 876, '商品规格', '', '', '', 'admin', 0, '/goods/setting/type/spec', '', '_self', 0, 50, 1),
(907, 414, '获取商品规格列表(可翻页)', '', '', '', 'api', 0, 'api/v1/spec/get.goods.spec.page', '', '_self', 0, 6, 1),
(908, 218, '获取商品属性列表(可翻页)', '', '', '', 'api', 0, 'api/v1/goods_attribute/get.goods.attribute.page', '', '_self', 0, 11, 1),
(909, 878, '查询列表', '', '', '', 'admin', 0, '/goods/setting/spec/list', '', '_self', 0, 50, 1),
(910, 878, '新增规则', '', '', '', 'admin', 0, '/goods/setting/spec/add', '', '_self', 0, 50, 1),
(911, 878, '编辑规则', '', '', '', 'admin', 0, '/goods/setting/spec/set', '', '_self', 0, 50, 1),
(912, 878, '删除规则', '', '', '', 'admin', 0, '/goods/setting/spec/del', '', '_self', 0, 50, 1),
(913, 878, '排序规则', '', '', '', 'admin', 0, '/goods/setting/spec/sort', '', '_self', 0, 50, 1),
(914, 878, '设为检索', '', '', '', 'admin', 0, '/goods/setting/spec/index', '', '_self', 0, 50, 1),
(915, 878, '取消检索', '', '', '', 'admin', 0, '/goods/setting/spec/close', '', '_self', 0, 50, 1),
(916, 877, '查询列表', '', '', '', 'admin', 0, '/goods/setting/attribute/list', '', '_self', 0, 50, 1),
(917, 877, '新增主属性', '', '', '', 'admin', 0, '/goods/setting/attribute/add', '', '_self', 0, 50, 1),
(918, 877, '新增子属性', '', '', '', 'admin', 0, '/goods/setting/attribute/add_son', '', '_self', 0, 50, 1),
(919, 877, '编辑属性', '', '', '', 'admin', 0, '/goods/setting/attribute/set', '', '_self', 0, 50, 1),
(920, 877, '删除属性', '', '', '', 'admin', 0, '/goods/setting/attribute/del', '', '_self', 0, 50, 1),
(921, 877, '设置核心', '', '', '', 'admin', 0, '/goods/setting/attribute/important', '', '_self', 0, 50, 1),
(923, 877, '排序属性', '', '', '', 'admin', 0, '/goods/setting/attribute/sort', '', '_self', 0, 50, 1),
(924, 877, '索引方式', '', '', '', 'admin', 0, '/goods/setting/attribute/search', '', '_self', 0, 50, 1),
(927, 881, '查询列表', '', '', '', 'admin', 0, '/goods/opinion/consult/list', '', '_self', 0, 50, 1),
(928, 881, '显示咨询', '', '', '', 'admin', 0, '/goods/opinion/consult/show', '', '_self', 0, 50, 1),
(929, 881, '隐藏咨询', '', '', '', 'admin', 0, '/goods/opinion/consult/hide', '', '_self', 0, 50, 1),
(930, 881, '删除咨询', '', '', '', 'admin', 0, '/goods/opinion/consult/del', '', '_self', 0, 50, 1),
(931, 881, '咨询明细', '', '', '', 'admin', 0, '/goods/opinion/consult/detail', '', '_self', 0, 50, 1),
(932, 880, '查询列表', '', '', '', 'admin', 0, '/goods/opinion/comment/list', '', '_self', 0, 1, 1),
(933, 880, '忽略评论', '', '', '', 'admin', 0, '/goods/opinion/comment/ignore', '', '_self', 0, 2, 1),
(934, 880, '显示评论', '', '', '', 'admin', 0, '/goods/opinion/comment/show', '', '_self', 0, 3, 1),
(935, 880, '隐藏评论', '', '', '', 'admin', 0, '/goods/opinion/comment/hide', '', '_self', 0, 4, 1),
(936, 880, '设为置顶', '', '', '', 'admin', 0, '/goods/opinion/comment/top', '', '_self', 0, 5, 1),
(937, 880, '取消置顶', '', '', '', 'admin', 0, '/goods/opinion/comment/remove_top', '', '_self', 0, 6, 1),
(938, 880, '评论明细', '', '', '', 'admin', 0, '/goods/opinion/comment/detail', '', '_self', 0, 8, 1),
(939, 880, '删除评论', '', '', '', 'admin', 0, '/goods/opinion/comment/del', '', '_self', 0, 7, 1),
(940, 873, '查询列表', '', '', '', 'admin', 0, '/goods/admin/list/list', '', '_self', 0, 1, 1),
(941, 873, '新增商品', '', '', '', 'admin', 0, '/goods/admin/list/add', '', '_self', 0, 2, 1),
(942, 873, '商品上下架', '', '', '', 'admin', 0, '/goods/admin/list/shelves', '', '_self', 0, 8, 1),
(944, 873, '是否推荐', '', '', '', 'admin', 0, '/goods/admin/list/recommend', '', '_self', 0, 10, 1),
(945, 873, '是否新品', '', '', '', 'admin', 0, '/goods/admin/list/new', '', '_self', 0, 11, 1),
(946, 873, '是否热卖', '', '', '', 'admin', 0, '/goods/admin/list/hot', '', '_self', 0, 12, 1),
(947, 873, '删除商品', '', '', '', 'admin', 0, '/goods/admin/list/del', '', '_self', 0, 4, 1),
(948, 873, '编辑商品', '', '', '', 'admin', 0, '/goods/admin/update', '', '_self', 0, 3, 1),
(949, 873, '复制商品', '', '', '', 'admin', 0, '/goods/admin/list/copy', '', '_self', 0, 7, 1),
(950, 873, '排序商品', '', '', '', 'admin', 0, '/goods/admin/list/sort', '', '_self', 0, 13, 1),
(952, 873, '恢复删除', '', '', '', 'admin', 0, '/goods/admin/list/restore', '', '_self', 0, 5, 1),
(954, 873, '快捷改价', '', '', '', 'admin', 0, '/goods/admin/list/price', '', '_self', 0, 50, 1),
(955, 873, '快捷库存', '', '', '', 'admin', 0, '/goods/admin/list/store', '', '_self', 0, 50, 1),
(956, 422, '根据资源编号获取集合', '', '', '', 'api', 0, 'api/v1/storage/get.storage.collection', '', '_self', 0, 50, 1),
(957, 198, '获取指定商品的属性配置数据', '', '', '', 'api', 0, 'api/v1/goods/get.goods.attr.config', '', '_self', 0, 50, 1),
(958, 198, '获取指定商品的规格配置数据', '', '', '', 'api', 0, 'api/v1/goods/get.goods.spec.config', '', '_self', 0, 50, 1),
(959, 198, '获取指定商品的规格菜单数据', '', '', '', 'api', 0, 'api/v1/goods/get.goods.spec.menu', '', '_self', 0, 50, 1),
(960, 872, '新增商品', '', 'RectangleCopy76', '添加新的商品', 'admin', 0, '/goods/admin/create', '', '_self', 1, 1, 1),
(962, 414, '获取所有商品规格及规格项', '', '', '', 'api', 0, 'api/v1/spec/get.goods.spec.all', '', '_self', 0, 7, 1),
(963, 559, '新增文章', '', 'RectangleCopy54', '添加新的文章', 'admin', 0, '/system/article/create', '', '_self', 1, 1, 1),
(964, 1014, '新增专题', '', 'RectangleCopy227', '添加新的专题', 'admin', 0, '/system/topic/create', '', '_self', 1, 1, 1),
(965, 218, '获取基础数据索引列表', '', '', '', 'api', 0, 'api/v1/goods_attribute/get.goods.attribute.data', '', '_self', 0, 50, 1),
(966, 182, '根据编号获取折扣商品明细', '', '', '', 'api', 0, 'api/v1/discount/get.discount.goods.list', '', '_self', 0, 50, 1),
(967, 198, '获取指定编号商品的基础数据', '', '', '', 'api', 0, 'api/v1/goods/get.goods.select', '', '_self', 0, 50, 1),
(968, 130, '获取优惠劵选择列表', '', '', '', 'api', 0, 'api/v1/coupon/get.coupon.select', '', '_self', 0, 5, 1),
(969, 858, '查询列表', '', '', '', 'admin', 0, '/marketing/marketing/discount/list', '', '_self', 0, 50, 1),
(970, 858, '新增折扣', '', '', '', 'admin', 0, '/marketing/marketing/discount/add', '', '_self', 0, 50, 1),
(971, 858, '编辑折扣', '', '', '', 'admin', 0, '/marketing/marketing/discount/set', '', '_self', 0, 50, 1),
(972, 858, '删除折扣', '', '', '', 'admin', 0, '/marketing/marketing/discount/del', '', '_self', 0, 50, 1),
(973, 858, '设为启用', '', '', '', 'admin', 0, '/marketing/marketing/discount/enable', '', '_self', 0, 50, 1),
(974, 858, '设为禁用', '', '', '', 'admin', 0, '/marketing/marketing/discount/disable', '', '_self', 0, 50, 1),
(975, 859, '查询列表', '', '', '', 'admin', 0, '/marketing/marketing/promotion/list', '', '_self', 0, 50, 1),
(976, 859, '新增促销', '', '', '', 'admin', 0, '/marketing/marketing/promotion/add', '', '_self', 0, 50, 1),
(977, 859, '编辑促销', '', '', '', 'admin', 0, '/marketing/marketing/promotion/set', '', '_self', 0, 50, 1),
(978, 859, '删除促销', '', '', '', 'admin', 0, '/marketing/marketing/promotion/del', '', '_self', 0, 50, 1),
(979, 859, '设为启用', '', '', '', 'admin', 0, '/marketing/marketing/promotion/enable', '', '_self', 0, 50, 1),
(980, 859, '设为禁用', '', '', '', 'admin', 0, '/marketing/marketing/promotion/disable', '', '_self', 0, 50, 1),
(981, 862, '查询列表', '', '', '', 'admin', 0, '/marketing/card/list/list', '', '_self', 0, 1, 1),
(982, 862, '新增购物卡', '', '', '', 'admin', 0, '/marketing/card/list/add', '', '_self', 0, 2, 1),
(983, 862, '编辑购物卡', '', '', '', 'admin', 0, '/marketing/card/list/set', '', '_self', 0, 3, 1),
(984, 862, '删除购物卡', '', '', '', 'admin', 0, '/marketing/card/list/del', '', '_self', 0, 4, 1),
(985, 862, '设为启用', '', '', '', 'admin', 0, '/marketing/card/list/enable', '', '_self', 0, 6, 1),
(986, 862, '设为禁用', '', '', '', 'admin', 0, '/marketing/card/list/disable', '', '_self', 0, 7, 1),
(987, 862, '导出购物卡', '', '', '', 'admin', 0, '/marketing/card/list/export', '', '_self', 0, 5, 1),
(988, 864, '查询列表', '', '', '', 'admin', 0, '/marketing/card/use/list', '', '_self', 0, 1, 1),
(989, 864, '设为启用', '', '', '', 'admin', 0, '/marketing/card/use/enable', '', '_self', 0, 3, 1),
(990, 864, '设为禁用', '', '', '', 'admin', 0, '/marketing/card/use/disable', '', '_self', 0, 4, 1),
(991, 864, '购物卡信息', '', '', '', 'admin', 0, '/marketing/card/use/info', '', '_self', 0, 2, 1),
(992, 860, '查询列表', '', '', '', 'admin', 0, '/marketing/coupon/list/list', '', '_self', 0, 50, 1),
(993, 460, '获取指定账号的基础数据', '', '', '', 'api', 0, 'api/v1/user/get.user.select', '', '_self', 0, 12, 1),
(994, 860, '新增优惠劵', '', '', '', 'admin', 0, '/marketing/coupon/list/add', '', '_self', 0, 50, 1),
(995, 860, '编辑优惠劵', '', '', '', 'admin', 0, '/marketing/coupon/list/set', '', '_self', 0, 50, 1),
(996, 860, '删除优惠劵', '', '', '', 'admin', 0, '/marketing/coupon/list/del', '', '_self', 0, 50, 1),
(997, 860, '发放优惠劵', '', '', '', 'admin', 0, '/marketing/coupon/list/give', '', '_self', 0, 50, 1),
(998, 860, '设为启用', '', '', '', 'admin', 0, '/marketing/coupon/list/enable', '', '_self', 0, 50, 1),
(999, 860, '设为禁用', '', '', '', 'admin', 0, '/marketing/coupon/list/disable', '', '_self', 0, 50, 1),
(1000, 860, '设为正常', '', '', '', 'admin', 0, '/marketing/coupon/list/normal', '', '_self', 0, 50, 1),
(1001, 860, '设为作废', '', '', '', 'admin', 0, '/marketing/coupon/list/invalid', '', '_self', 0, 50, 1),
(1002, 861, '查询列表', '', '', '', 'admin', 0, '/marketing/coupon/give/list', '', '_self', 0, 50, 1),
(1003, 861, '优惠劵信息', '', '', '', 'admin', 0, '/marketing/coupon/give/info', '', '_self', 0, 50, 1),
(1004, 861, '删除优惠劵', '', '', '', 'admin', 0, '/marketing/coupon/give/del', '', '_self', 0, 50, 1),
(1005, 861, '恢复优惠劵', '', '', '', 'admin', 0, '/marketing/coupon/give/rec', '', '_self', 0, 50, 1),
(1007, 866, '查询列表', '', '', '', 'admin', 0, '/order/admin/refund/list', '', '_self', 0, 50, 1),
(1008, 866, '退款信息', '', '', '', 'admin', 0, '/order/admin/refund/query', '', '_self', 0, 50, 1),
(1009, 40, '根据条件查询是否有更新', '', '', '', 'api', 0, 'api/v1/app_install/query.app.install.updated', '', '_self', 0, 50, 1),
(1010, 31, '批量设置应用验证码', '', '', '', 'api', 0, 'api/v1/app/set.app.captcha', '', '_self', 0, 9, 1),
(1011, 31, '查询应用验证码状态', '', '', '', 'api', 0, 'api/v1/app/get.app.captcha', '', '_self', 0, 10, 1),
(1012, 31, '获取应用验证码', '', '', '', 'api', 0, 'api/v1/app/image.app.captcha', '', '_self', 0, 12, 1),
(1013, 31, '获取应用验证码调用地址', '', '', '', 'api', 0, 'api/v1/app/get.app.captcha.callurl', '', '_self', 0, 11, 1),
(1014, 520, '专题发布', '', 'baoguo_dabao_o', '', 'admin', 0, '/system/topic', '', '_self', 1, 7, 1),
(1015, 1023, '基础设置', '', '', '', 'admin', 0, '/setting/setting/system/info/basis', '', '_self', 0, 1, 1),
(1016, 1023, '备案许可', '', '', '', 'admin', 0, '/setting/setting/system/info/record', '', '_self', 0, 2, 1),
(1017, 1023, '状态设置', '', '', '', 'admin', 0, '/setting/setting/system/info/status', '', '_self', 0, 3, 1),
(1018, 1023, '定义平台', '', '', '', 'admin', 0, '/setting/setting/system/info/platform', '', '_self', 0, 4, 1),
(1019, 1023, '跨域访问', '', '', '', 'admin', 0, '/setting/setting/system/info/cors', '', '_self', 0, 5, 1),
(1020, 1023, '其他设置', '', '', '', 'admin', 0, '/setting/setting/system/info/other', '', '_self', 0, 6, 1),
(1022, 713, '购物系统', '', '', '', 'admin', 0, '/setting/setting/system/shopping', '', '_self', 0, 2, 1),
(1023, 713, '系统配置', '', '', '', 'admin', 0, '/setting/setting/system/info', '', '_self', 0, 1, 1),
(1024, 1022, '基础设置', '', '', '', 'admin', 0, '/setting/setting/system/shopping/basis', '', '_self', 0, 50, 1),
(1025, 1022, '订单来源', '', '', '', 'admin', 0, '/setting/setting/system/shopping/source', '', '_self', 0, 50, 1),
(1026, 713, '售后服务', '', '', '', 'admin', 0, '/setting/setting/system/service', '', '_self', 0, 50, 1),
(1027, 713, '支付页面', '', '', '', 'admin', 0, '/setting/setting/system/payment', '', '_self', 0, 50, 1),
(1028, 713, '配送优惠', '', '', '', 'admin', 0, '/setting/setting/system/delivery', '', '_self', 0, 50, 1),
(1029, 1028, '满额设置', '', '', '', 'admin', 0, '/setting/setting/system/delivery/money', '', '_self', 0, 50, 1),
(1030, 1028, '满件设置', '', '', '', 'admin', 0, '/setting/setting/system/delivery/amount', '', '_self', 0, 50, 1),
(1031, 1028, '满额减设置', '', '', '', 'admin', 0, '/setting/setting/system/delivery/lower', '', '_self', 0, 50, 1),
(1032, 713, '配送轨迹', '', '', '', 'admin', 0, '/setting/setting/system/dist', '', '_self', 0, 50, 1),
(1033, 713, '上传配置', '', '', '', 'admin', 0, '/setting/setting/system/upload', '', '_self', 0, 50, 1),
(1034, 1033, '基础设置', '', '', '', 'admin', 0, '/setting/setting/system/upload/basis', '', '_self', 0, 50, 1),
(1035, 1033, 'CareyShop(本地上传)', '', '', '', 'admin', 0, '/setting/setting/system/upload/careyshop', '', '_self', 0, 50, 1),
(1036, 1033, '七牛云 KODO', '', '', '', 'admin', 0, '/setting/setting/system/upload/qiniu', '', '_self', 0, 50, 1),
(1037, 1033, '阿里云 OSS', '', '', '', 'admin', 0, '/setting/setting/system/upload/aliyun', '', '_self', 0, 50, 1),
(1038, 0, '条形码', '', '', '', 'api', 0, '', '', '_self', 0, 50, 1),
(1039, 1038, '获取条形码调用地址', '', '', '', 'api', 0, 'api/v1/barcode/get.barcode.callurl', '', '_self', 0, 50, 1),
(1040, 1038, '动态生成一个二维码', '', '', '', 'api', 0, 'api/v1/barcode/get.barcode.item', '', '_self', 0, 50, 1),
(1041, 0, '财务', '', 'jinbi', '', 'admin', 0, '/finance', '', '_self', 1, 6, 1);
INSERT INTO `cs_menu` (`menu_id`, `parent_id`, `name`, `alias`, `icon`, `remark`, `module`, `type`, `url`, `params`, `target`, `is_navi`, `sort`, `status`) VALUES
(1042, 1041, '财务首页', '', 'shouye_o', '', 'admin', 0, '/finance/index', '', '_self', 1, 50, 1),
(1043, 1041, '快捷向导', '', 'ding_o', '', 'admin', 0, '/finance/navi', '', '_self', 1, 50, 1),
(1044, 1043, '退款日志', '', 'cunkuan_o', '订单生成退款后的日志', 'admin', 1, '/order/admin/refund', '', '_self', 1, 4, 1),
(1045, 1043, '交易结算', '', 'RectangleCopy218', '账户资金支出、收入的流水账', 'admin', 1, '/member/user/transaction', '', '_self', 1, 1, 1),
(1046, 1043, '提现申请', '', 'jiekuan_o', '提现申请的审批、拒绝、查询', 'admin', 1, '/member/withdraw/list', '', '_self', 1, 3, 1),
(1047, 1043, '支付日志', '', 'huobiliu_o', '支付行为、结果的流水账', 'admin', 1, '/setting/payment/log', '', '_self', 1, 2, 1),
(1048, 812, '访问日志', '', 'daibanrenwu_o', '访问者的行踪轨迹记录', 'admin', 1, '/setting/admin/action', '', '_self', 1, 2, 1),
(1049, 1, '获取指定账号的基础数据', '', '', '', 'api', 0, 'api/v1/admin/get.admin.select', '', '_self', 0, 11, 1),
(1050, 869, '商品评价', '', 'RectangleCopy240', '买家收货后对商品的评价', 'admin', 1, '/goods/opinion/comment', '', '_self', 1, 2, 1),
(1051, 869, '配送轨迹', '', 'didiandingwei_o', '物流轨迹流水记录操作', 'admin', 1, '/system/aided/dist', '', '_self', 1, 5, 1),
(1052, 868, '交易结算', '', 'RectangleCopy218', '账户资金支出、收入的流水账', 'admin', 1, '/member/user/transaction', '', '_self', 1, 2, 1),
(1053, 868, '支付日志', '', 'huobiliu_o', '支付行为、结果的流水账', 'admin', 1, '/setting/payment/log', '', '_self', 1, 3, 1),
(1054, 330, '获取一个订单操作日志', '', '', '', 'api', 0, 'api/v1/order/get.order.log', '', '_self', 0, 50, 1),
(1055, 865, '订单详情', '', '', '', 'admin', 0, '/order/admin/info', '', '_self', 0, 1, 1),
(1057, 865, '订单打印', '', '', '', 'admin', 0, '/order/admin/print', '', '_self', 0, 3, 1),
(1058, 865, '设为配货', '', '', '', 'admin', 0, '/order/admin/list/start_picking', '', '_self', 0, 4, 1),
(1059, 865, '取消配货', '', '', '', 'admin', 0, '/order/admin/list/cancel_picking', '', '_self', 0, 5, 1),
(1060, 865, '确认收货', '', '', '', 'admin', 0, '/order/admin/list/complete', '', '_self', 0, 7, 1),
(1061, 865, '修改金额', '', '', '', 'admin', 0, '/order/admin/list/price', '', '_self', 0, 8, 1),
(1062, 865, '修改订单', '', '', '', 'admin', 0, '/order/admin/list/set', '', '_self', 0, 9, 1),
(1063, 865, '确定发货', '', '', '', 'admin', 0, '/order/admin/list/delivery', '', '_self', 0, 6, 1),
(1064, 865, '物流信息', '', '', '', 'admin', 0, '/order/admin/list/dist', '', '_self', 0, 50, 1),
(1065, 865, '取消订单', '', '', '', 'admin', 0, '/order/admin/list/cancel', '', '_self', 0, 50, 1),
(1066, 865, '删除订单', '', '', '', 'admin', 0, '/order/admin/list/del', '', '_self', 0, 50, 1),
(1067, 865, '恢复订单', '', '', '', 'admin', 0, '/order/admin/list/restore', '', '_self', 0, 50, 1),
(1068, 865, '设置备注', '', '', '', 'admin', 0, '/order/admin/list/remark', '', '_self', 0, 50, 1),
(1070, 870, '售后详情', '', '', '', 'admin', 0, '/order/service/info', '', '_self', 0, 50, 1),
(1071, 870, '添加留言', '', '', '', 'admin', 0, '/order/service/list/message', '', '_self', 0, 50, 1),
(1072, 870, '同意售后', '', '', '', 'admin', 0, '/order/service/list/agree', '', '_self', 0, 50, 1),
(1073, 870, '拒绝售后', '', '', '', 'admin', 0, '/order/service/list/refused', '', '_self', 0, 50, 1),
(1074, 870, '要求寄回', '', '', '', 'admin', 0, '/order/service/list/sendback', '', '_self', 0, 50, 1),
(1075, 870, '设为售后中', '', '', '', 'admin', 0, '/order/service/list/after', '', '_self', 0, 50, 1),
(1076, 870, '撤销售后', '', '', '', 'admin', 0, '/order/service/list/cancel', '', '_self', 0, 50, 1),
(1077, 870, '售后完成', '', '', '', 'admin', 0, '/order/service/list/complete', '', '_self', 0, 50, 1),
(1078, 870, '设置备注', '', '', '', 'admin', 0, '/order/service/list/remark', '', '_self', 0, 50, 1),
(1079, 870, '物流信息', '', '', '', 'admin', 0, '/order/service/list/dist', '', '_self', 0, 50, 1),
(1080, 534, '调试日志', '', '', '', 'admin', 0, '/log', '', '_self', 0, 50, 1),
(1082, 873, '商品预览', '', '', '', 'admin', 0, '/goods/admin/view', '', '_self', 0, 50, 1),
(1083, 561, '文章预览', '', '', '', 'admin', 0, '/system/article/view', '', '_self', 0, 50, 1),
(1084, 562, '专题预览', '', '', '', 'admin', 0, '/system/topic/view', '', '_self', 0, 50, 1),
(1085, 540, '消息预览', '', '', '', 'admin', 0, '/system/message/user/view', '', '_self', 0, 50, 1),
(1086, 711, '商业授权', '', 'careyshop', '商业授权信息查询', 'admin', 0, '/setting/app/authorize', '', '_self', 1, 50, 1);

-- --------------------------------------------------------

--
-- 表的结构 `cs_message`
--

CREATE TABLE `cs_message` (
  `message_id` int(11) UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '消息类型(自定义)',
  `member` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=私有函 1=顾客组 2=管理组',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '外部链接',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '_self _blank',
  `page_views` int(11) NOT NULL DEFAULT '0' COMMENT '游览量',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶 0=否 1=是',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=禁用 1=启用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息管理';

--
-- 插入之前先把表清空（truncate） `cs_message`
--

TRUNCATE TABLE `cs_message`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_message_user`
--

CREATE TABLE `cs_message_user` (
  `message_user_id` int(11) UNSIGNED NOT NULL,
  `message_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应message表',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT '对应user表',
  `admin_id` int(11) UNSIGNED DEFAULT NULL COMMENT '对应admin表',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未读 1=已读',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户消息通知';

--
-- 插入之前先把表清空（truncate） `cs_message_user`
--

TRUNCATE TABLE `cs_message_user`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_navigation`
--

CREATE TABLE `cs_navigation` (
  `navigation_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '链接',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '_self _blank',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='导航';

--
-- 插入之前先把表清空（truncate） `cs_navigation`
--

TRUNCATE TABLE `cs_navigation`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_notice_item`
--

CREATE TABLE `cs_notice_item` (
  `notice_item_id` smallint(5) UNSIGNED NOT NULL,
  `item_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `replace_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '替换变量',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '通知类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='通知系统可用变量';

--
-- 插入之前先把表清空（truncate） `cs_notice_item`
--

TRUNCATE TABLE `cs_notice_item`;
--
-- 转存表中的数据 `cs_notice_item`
--

INSERT INTO `cs_notice_item` (`notice_item_id`, `item_name`, `replace_name`, `type`) VALUES
(1, '{验证码}', 'number', 0),
(2, '{商城名称}', 'shop_name', 1),
(3, '{用户账号}', 'user_name', 1),
(4, '{用户昵称}', 'nick_name', 1),
(5, '{商城名称}', 'shop_name', 2),
(6, '{用户账号}', 'user_name', 2),
(7, '{用户昵称}', 'nick_name', 2),
(8, '{充值金额}', 'recharge_money', 2),
(9, '{商城名称}', 'shop_name', 3),
(10, '{用户账号}', 'user_name', 3),
(11, '{用户昵称}', 'nick_name', 3),
(12, '{主订单号}', 'order_no', 3),
(13, '{订单金额}', 'order_money', 3),
(14, '{商城名称}', 'shop_name', 4),
(15, '{用户账号}', 'user_name', 4),
(16, '{用户昵称}', 'nick_name', 4),
(17, '{主订单号}', 'order_no', 4),
(18, '{订单金额}', 'order_money', 4),
(19, '{商品金额}', 'goods_money', 4),
(20, '{商城名称}', 'shop_name', 5),
(21, '{用户账号}', 'user_name', 5),
(22, '{用户昵称}', 'nick_name', 5),
(23, '{主订单号}', 'order_no', 5),
(24, '{订单金额}', 'order_money', 5),
(25, '{商品金额}', 'goods_money', 5),
(26, '{商城名称}', 'shop_name', 6),
(27, '{用户账号}', 'user_name', 6),
(28, '{用户昵称}', 'nick_name', 6),
(29, '{主订单号}', 'order_no', 6),
(30, '{订单金额}', 'order_money', 6),
(31, '{商品金额}', 'goods_money', 6),
(32, '{商品名称}', 'goods_name', 6),
(33, '{快递公司}', 'delivery_name', 6),
(34, '{快递单号}', 'logistic_code', 6);

-- --------------------------------------------------------

--
-- 表的结构 `cs_notice_tpl`
--

CREATE TABLE `cs_notice_tpl` (
  `notice_tpl_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板名称',
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '通知系统编码',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '通知类型',
  `sms_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '阿里云短信模板编号',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题或签名',
  `template` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='通知系统模板';

--
-- 插入之前先把表清空（truncate） `cs_notice_tpl`
--

TRUNCATE TABLE `cs_notice_tpl`;
--
-- 转存表中的数据 `cs_notice_tpl`
--

INSERT INTO `cs_notice_tpl` (`notice_tpl_id`, `name`, `code`, `type`, `sms_code`, `title`, `template`, `status`) VALUES
(1, '通用验证', 'sms', 0, '', '', '', 1),
(2, '注册成功', 'sms', 1, '', '', '', 1),
(3, '充值成功', 'sms', 2, '', '', '', 1),
(4, '确认订单', 'sms', 3, '', '', '', 1),
(5, '付款成功', 'sms', 4, '', '', '', 1),
(6, '下单成功', 'sms', 5, '', '', '', 1),
(7, '订单发货', 'sms', 6, '', '', '', 1),
(8, '通用验证', 'email', 0, '', '', '', 1),
(9, '注册成功', 'email', 1, '', '', '', 1),
(10, '充值成功', 'email', 2, '', '', '', 1),
(11, '确认订单', 'email', 3, '', '', '', 1),
(12, '付款成功', 'email', 4, '', '', '', 1),
(13, '下单成功', 'email', 5, '', '', '', 1),
(14, '订单发货', 'email', 6, '', '', '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `cs_order`
--

CREATE TABLE `cs_order` (
  `order_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父订单Id',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '对应user表',
  `source` tinyint(3) NOT NULL COMMENT '订单来源(自定义)',
  `pay_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品金额',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额(含运费)',
  `use_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额抵扣',
  `use_level` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员抵扣',
  `use_integral` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分抵扣',
  `use_coupon` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠劵抵扣',
  `use_discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品折扣抵扣',
  `use_promotion` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单促销抵扣',
  `use_card` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '购物卡抵扣',
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `payment_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付单号(交易流水号)',
  `payment_code` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付编码',
  `card_number` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '购物卡号',
  `delivery_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '配送方式 对应delivery表',
  `consignee` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `country` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '国家',
  `region_list` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '区域列表',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `complete_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '完整地址',
  `zipcode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮编',
  `tel` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号码',
  `buyer_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '买家备注',
  `invoice_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否开票 0=否 1=个人 2=企业',
  `invoice_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '发票抬头',
  `tax_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '纳税人识别号',
  `invoice_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '开票费用',
  `trade_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '交易状态 0=待处理 1=配货中 2=已发货 3=已完成 4=已取消',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配送状态 0=未发 1=已发 2=部分发',
  `payment_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态 0=未付 1=已付',
  `create_user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单创建者(管理组)',
  `is_give` tinyint(1) NOT NULL DEFAULT '1' COMMENT '收货增 0=否 1=是',
  `sellers_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '卖家备注',
  `adjustment` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '调整价格',
  `integral_pct` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分换算比例',
  `give_integral` int(11) NOT NULL DEFAULT '0' COMMENT '赠送积分',
  `give_coupon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '赠送优惠劵',
  `payment_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付日期',
  `picking_time` int(11) NOT NULL DEFAULT '0' COMMENT '配货日期',
  `delivery_time` int(11) NOT NULL DEFAULT '0' COMMENT '发货日期',
  `finished_time` int(11) NOT NULL DEFAULT '0' COMMENT '完成日期',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=回收站 2=删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单管理';

--
-- 插入之前先把表清空（truncate） `cs_order`
--

TRUNCATE TABLE `cs_order`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_order_goods`
--

CREATE TABLE `cs_order_goods` (
  `order_goods_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应order表',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '对应user表',
  `goods_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应goods表',
  `goods_image` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品封面',
  `goods_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品货号',
  `goods_sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品SKU',
  `bar_code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品条码',
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规格键名',
  `key_value` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规格值',
  `market_price` decimal(10,2) NOT NULL COMMENT '市场价',
  `shop_price` decimal(10,2) NOT NULL COMMENT '本店价',
  `qty` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '购买数量',
  `is_comment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未评 1=已评 2=追评',
  `is_service` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=可申请 1=售后中 2=已售后 3=不可申',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未发 1=已发 2=收货 3=取消'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单商品';

--
-- 插入之前先把表清空（truncate） `cs_order_goods`
--

TRUNCATE TABLE `cs_order_goods`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_order_log`
--

CREATE TABLE `cs_order_log` (
  `order_log_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应order表',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `trade_status` tinyint(1) NOT NULL COMMENT '交易状态',
  `delivery_status` tinyint(1) NOT NULL COMMENT '配送状态',
  `payment_status` tinyint(1) NOT NULL COMMENT '支付状态',
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作者',
  `client_type` tinyint(1) NOT NULL COMMENT '-1=游客 0=顾客 1=管理组',
  `comment` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '备注',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单日志';

--
-- 插入之前先把表清空（truncate） `cs_order_log`
--

TRUNCATE TABLE `cs_order_log`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_order_refund`
--

CREATE TABLE `cs_order_refund` (
  `order_refund_id` int(11) UNSIGNED NOT NULL,
  `refund_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退款单号(流水号)',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `out_trade_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '退款交易号',
  `out_trade_msg` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '退款返回信息',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `total_amount` decimal(10,2) NOT NULL COMMENT '订单支付总额',
  `amount` decimal(10,2) NOT NULL COMMENT '退款金额',
  `payment_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付单号(交易流水号)',
  `to_payment` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付方式',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=待处理 1=已处理 2=失败 3=撤销'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单退款';

--
-- 插入之前先把表清空（truncate） `cs_order_refund`
--

TRUNCATE TABLE `cs_order_refund`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_order_service`
--

CREATE TABLE `cs_order_service` (
  `order_service_id` int(11) UNSIGNED NOT NULL,
  `service_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '售后单号(流水号)',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `order_goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应order_goods表',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应admin表',
  `qty` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '数量',
  `type` tinyint(1) NOT NULL COMMENT '0=仅退款 1=退货退款 2=换货 3=维修',
  `reason` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '原因',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '说明',
  `goods_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '货物状态 0=未选择 1=未收到货 2=已收到货',
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '凭证(照片)',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=待处理 1=已同意 2=已拒绝 3=已寄件 4=售后中 5=已撤销 6=已完成',
  `is_return` tinyint(1) NOT NULL DEFAULT '0' COMMENT '寄回到商家 0=否 1=是',
  `result` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '处理结果',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '客服备注',
  `refund_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `refund_detail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '退款结构',
  `refund_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '退款单号',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '返件地址',
  `consignee` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '委托人',
  `zipcode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮编',
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号码',
  `logistic_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '快递单号',
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `admin_event` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有新事件',
  `user_event` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有新事件',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='售后服务';

--
-- 插入之前先把表清空（truncate） `cs_order_service`
--

TRUNCATE TABLE `cs_order_service`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_payment`
--

CREATE TABLE `cs_payment` (
  `payment_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付名称',
  `code` tinyint(1) NOT NULL COMMENT '支付编码',
  `image` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片',
  `is_deposit` tinyint(1) NOT NULL COMMENT '财务充值 0=否 1=是',
  `is_inpour` tinyint(1) NOT NULL COMMENT '账号充值 0=否 1=是',
  `is_payment` tinyint(1) NOT NULL COMMENT '订单支付 0=否 1=是',
  `is_refund` tinyint(1) NOT NULL COMMENT '原路返回 0=否 1=是',
  `setting` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置',
  `model` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '对应模型',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付配置';

--
-- 插入之前先把表清空（truncate） `cs_payment`
--

TRUNCATE TABLE `cs_payment`;
--
-- 转存表中的数据 `cs_payment`
--

INSERT INTO `cs_payment` (`payment_id`, `name`, `code`, `image`, `is_deposit`, `is_inpour`, `is_payment`, `is_refund`, `setting`, `model`, `sort`, `status`) VALUES
(1, '账户资金', 0, 'aliyun.oss.careyshop.cn/支付图标/yezf.gif?type=aliyun', 1, 0, 0, 0, '[]', '', 50, 1),
(2, '货到付款', 1, 'aliyun.oss.careyshop.cn/支付图标/hdfk.gif?type=aliyun', 0, 0, 1, 0, '[]', 'cod', 50, 1),
(3, '支付宝', 2, 'aliyun.oss.careyshop.cn/支付图标/zhifubao.png?type=aliyun', 1, 1, 1, 1, '{\"appId\":{\"name\":\"APPID\",\"value\":\"\",\"remark\":\"支付宝应用中的 <span style=\\\"color:#F56C6C;\\\">APPID<\\/span>\"},\"merchantPrivateKey\":{\"name\":\"商户私钥\",\"value\":\"\",\"remark\":\"支付宝应用中的 <span style=\\\"color:#F56C6C;\\\">商户私钥<\\/span>\"},\"alipayPublicKey\":{\"name\":\"支付宝公钥\",\"value\":\"\",\"remark\":\"支付宝应用中的 <span style=\\\"color:#F56C6C;\\\">支付宝公钥<\\/span>\"},\"signType\":{\"name\":\"签名方式\",\"value\":\"\",\"remark\":\"可使用RSA2、SHA256等签名方式，推荐使用 <span style=\\\"color:#F56C6C;\\\">RSA2<\\/span>\"},\"httpMethod\":{\"name\":\"页面接口方式\",\"value\":\"\",\"remark\":\"推荐使用 <span style=\\\"color:#F56C6C;\\\">post<\\/span>\"}}', 'alipay', 50, 1),
(4, '微信支付', 3, 'aliyun.oss.careyshop.cn/支付图标/weixin.png?type=aliyun', 1, 1, 1, 1, '{\"appid\":{\"name\":\"APPID\",\"value\":\"\",\"remark\":\"微信支付APPID，对应 <span style=\\\"color:#F56C6C;\\\">appid<\\/span>\"},\"mchid\":{\"name\":\"商户号\",\"value\":\"\",\"remark\":\"微信支付商户号，对应 <span style=\\\"color:#F56C6C;\\\">mchid<\\/span>\"},\"key\":{\"name\":\"商户支付密钥\",\"value\":\"\",\"remark\":\"微信支付商户支付密钥，对应 <span style=\\\"color:#F56C6C;\\\">key<\\/span>\"},\"appsecret\":{\"name\":\"公众帐号Secert\",\"value\":\"\",\"remark\":\"微信支付公众帐号Secert，对应 <span style=\\\"color:#F56C6C;\\\">appsecret<\\/span>\"},\"sslcert\":{\"name\":\"apiclient_cert\",\"value\":\"\",\"remark\":\"<span style=\\\"color:#F56C6C;\\\">apiclient_cert.pem<\\/span> 文件的绝对路径\"},\"sslkey\":{\"name\":\"apiclient_key\",\"value\":\"\",\"remark\":\"<span style=\\\"color:#F56C6C;\\\">apiclient_key.pem<\\/span> 文件的绝对路径\"}}', 'weixin', 50, 1),
(5, '银行转账', 4, 'aliyun.oss.careyshop.cn/支付图标/yinlian.png?type=aliyun', 1, 0, 0, 0, '[]', '', 50, 1),
(6, '购物卡', 5, '', 0, 0, 0, 0, '[]', '', 50, 1),
(7, '其他', 6, '', 1, 0, 0, 0, '[]', '', 50, 1);

-- --------------------------------------------------------

--
-- 表的结构 `cs_payment_log`
--

CREATE TABLE `cs_payment_log` (
  `payment_log_id` int(11) UNSIGNED NOT NULL,
  `payment_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '流水号',
  `out_trade_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '交易号',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `amount` decimal(10,2) NOT NULL COMMENT '支付金额',
  `payment_time` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付日期',
  `to_payment` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付方式',
  `type` tinyint(1) NOT NULL COMMENT '0=充值 1=订单',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=待付款 1=已完成 2=已关闭'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付日志';

--
-- 插入之前先把表清空（truncate） `cs_payment_log`
--

TRUNCATE TABLE `cs_payment_log`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_praise`
--

CREATE TABLE `cs_praise` (
  `praise_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '对应user表',
  `goods_comment_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods_comment表'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='点赞记录';

--
-- 插入之前先把表清空（truncate） `cs_praise`
--

TRUNCATE TABLE `cs_praise`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_promotion`
--

CREATE TABLE `cs_promotion` (
  `promotion_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '促销名称',
  `begin_time` int(11) NOT NULL DEFAULT '0' COMMENT '开始日期',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束日期',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单促销';

--
-- 插入之前先把表清空（truncate） `cs_promotion`
--

TRUNCATE TABLE `cs_promotion`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_promotion_item`
--

CREATE TABLE `cs_promotion_item` (
  `promotion_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应promotion表',
  `quota` decimal(10,2) NOT NULL COMMENT '限额',
  `settings` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0=减价 1=打折 2=免邮 3=送积分 4=送优惠劵',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单促销项';

--
-- 插入之前先把表清空（truncate） `cs_promotion_item`
--

TRUNCATE TABLE `cs_promotion_item`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_qrcode`
--

CREATE TABLE `cs_qrcode` (
  `qrcode_id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `size` smallint(5) UNSIGNED NOT NULL DEFAULT '75' COMMENT '大小',
  `suffix` enum('png','jpg','gif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'png' COMMENT '后缀',
  `logo` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'logo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='二维码管理';

--
-- 插入之前先把表清空（truncate） `cs_qrcode`
--

TRUNCATE TABLE `cs_qrcode`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_region`
--

CREATE TABLE `cs_region` (
  `region_id` smallint(5) UNSIGNED NOT NULL,
  `parent_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父节点id',
  `region_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '区域名称',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='区域';

--
-- 插入之前先把表清空（truncate） `cs_region`
--

TRUNCATE TABLE `cs_region`;
--
-- 转存表中的数据 `cs_region`
--

INSERT INTO `cs_region` (`region_id`, `parent_id`, `region_name`, `sort`, `is_delete`) VALUES
(1, 0, '中国', 50, 0),
(2, 1, '北京', 1, 0),
(3, 1, '天津', 3, 0),
(4, 1, '河北', 5, 0),
(5, 1, '山西', 6, 0),
(6, 1, '内蒙古', 11, 0),
(7, 1, '辽宁', 8, 0),
(8, 1, '吉林', 9, 0),
(9, 1, '黑龙江', 10, 0),
(10, 1, '上海', 2, 0),
(11, 1, '江苏', 12, 0),
(12, 1, '浙江', 15, 0),
(13, 1, '安徽', 14, 0),
(14, 1, '福建', 16, 0),
(15, 1, '江西', 21, 0),
(16, 1, '山东', 13, 0),
(17, 1, '河南', 7, 0),
(18, 1, '湖北', 17, 0),
(19, 1, '湖南', 18, 0),
(20, 1, '广东', 19, 0),
(21, 1, '广西', 20, 0),
(22, 1, '海南', 23, 0),
(23, 1, '重庆', 4, 0),
(24, 1, '四川', 22, 0),
(25, 1, '贵州', 24, 0),
(26, 1, '云南', 25, 0),
(27, 1, '西藏', 26, 0),
(28, 1, '陕西', 27, 0),
(29, 1, '甘肃', 28, 0),
(30, 1, '青海', 29, 0),
(31, 1, '宁夏', 30, 0),
(32, 1, '新疆', 31, 0),
(33, 1, '台湾', 34, 0),
(34, 1, '香港', 32, 0),
(35, 1, '澳门', 33, 0),
(37, 2, '北京市', 50, 0),
(38, 3, '天津市', 50, 0),
(39, 4, '石家庄市', 50, 0),
(40, 4, '唐山市', 50, 0),
(41, 4, '秦皇岛市', 50, 0),
(42, 4, '邯郸市', 50, 0),
(43, 4, '邢台市', 50, 0),
(44, 4, '保定市', 50, 0),
(45, 4, '张家口市', 50, 0),
(46, 4, '承德市', 50, 0),
(47, 4, '沧州市', 50, 0),
(48, 4, '廊坊市', 50, 0),
(49, 4, '衡水市', 50, 0),
(50, 5, '太原市', 50, 0),
(51, 5, '大同市', 50, 0),
(52, 5, '阳泉市', 50, 0),
(53, 5, '长治市', 50, 0),
(54, 5, '晋城市', 50, 0),
(55, 5, '朔州市', 50, 0),
(56, 5, '晋中市', 50, 0),
(57, 5, '运城市', 50, 0),
(58, 5, '忻州市', 50, 0),
(59, 5, '临汾市', 50, 0),
(60, 5, '吕梁市', 50, 0),
(61, 6, '呼和浩特市', 50, 0),
(62, 6, '包头市', 50, 0),
(63, 6, '乌海市', 50, 0),
(64, 6, '赤峰市', 50, 0),
(65, 6, '通辽市', 50, 0),
(66, 6, '鄂尔多斯市', 50, 0),
(67, 6, '呼伦贝尔市', 50, 0),
(68, 6, '巴彦淖尔市', 50, 0),
(69, 6, '乌兰察布市', 50, 0),
(70, 6, '兴安盟', 50, 0),
(71, 6, '锡林郭勒盟', 50, 0),
(72, 6, '阿拉善盟', 50, 0),
(73, 7, '沈阳市', 50, 0),
(74, 7, '大连市', 50, 0),
(75, 7, '鞍山市', 50, 0),
(76, 7, '抚顺市', 50, 0),
(77, 7, '本溪市', 50, 0),
(78, 7, '丹东市', 50, 0),
(79, 7, '锦州市', 50, 0),
(80, 7, '营口市', 50, 0),
(81, 7, '阜新市', 50, 0),
(82, 7, '辽阳市', 50, 0),
(83, 7, '盘锦市', 50, 0),
(84, 7, '铁岭市', 50, 0),
(85, 7, '朝阳市', 50, 0),
(86, 7, '葫芦岛市', 50, 0),
(87, 8, '长春市', 50, 0),
(88, 8, '吉林市', 50, 0),
(89, 8, '四平市', 50, 0),
(90, 8, '辽源市', 50, 0),
(91, 8, '通化市', 50, 0),
(92, 8, '白山市', 50, 0),
(93, 8, '松原市', 50, 0),
(94, 8, '白城市', 50, 0),
(95, 8, '延边朝鲜族自治州', 50, 0),
(96, 9, '哈尔滨市', 50, 0),
(97, 9, '齐齐哈尔市', 50, 0),
(98, 9, '鸡西市', 50, 0),
(99, 9, '鹤岗市', 50, 0),
(100, 9, '双鸭山市', 50, 0),
(101, 9, '大庆市', 50, 0),
(102, 9, '伊春市', 50, 0),
(103, 9, '佳木斯市', 50, 0),
(104, 9, '七台河市', 50, 0),
(105, 9, '牡丹江市', 50, 0),
(106, 9, '黑河市', 50, 0),
(107, 9, '绥化市', 50, 0),
(108, 9, '大兴安岭地区', 50, 0),
(109, 10, '上海市', 50, 0),
(110, 11, '南京市', 50, 0),
(111, 11, '无锡市', 50, 0),
(112, 11, '徐州市', 50, 0),
(113, 11, '常州市', 50, 0),
(114, 11, '苏州市', 50, 0),
(115, 11, '南通市', 50, 0),
(116, 11, '连云港市', 50, 0),
(117, 11, '淮安市', 50, 0),
(118, 11, '盐城市', 50, 0),
(119, 11, '扬州市', 50, 0),
(120, 11, '镇江市', 50, 0),
(121, 11, '泰州市', 50, 0),
(122, 11, '宿迁市', 50, 0),
(123, 12, '杭州市', 50, 0),
(124, 12, '宁波市', 50, 0),
(125, 12, '温州市', 50, 0),
(126, 12, '嘉兴市', 50, 0),
(127, 12, '湖州市', 50, 0),
(128, 12, '绍兴市', 50, 0),
(129, 12, '金华市', 50, 0),
(130, 12, '衢州市', 50, 0),
(131, 12, '舟山市', 50, 0),
(132, 12, '台州市', 50, 0),
(133, 12, '丽水市', 50, 0),
(134, 13, '合肥市', 50, 0),
(135, 13, '芜湖市', 50, 0),
(136, 13, '蚌埠市', 50, 0),
(137, 13, '淮南市', 50, 0),
(138, 13, '马鞍山市', 50, 0),
(139, 13, '淮北市', 50, 0),
(140, 13, '铜陵市', 50, 0),
(141, 13, '安庆市', 50, 0),
(142, 13, '黄山市', 50, 0),
(143, 13, '滁州市', 50, 0),
(144, 13, '阜阳市', 50, 0),
(145, 13, '宿州市', 50, 0),
(146, 13, '六安市', 50, 0),
(147, 13, '亳州市', 50, 0),
(148, 13, '池州市', 50, 0),
(149, 13, '宣城市', 50, 0),
(150, 14, '福州市', 50, 0),
(151, 14, '厦门市', 50, 0),
(152, 14, '莆田市', 50, 0),
(153, 14, '三明市', 50, 0),
(154, 14, '泉州市', 50, 0),
(155, 14, '漳州市', 50, 0),
(156, 14, '南平市', 50, 0),
(157, 14, '龙岩市', 50, 0),
(158, 14, '宁德市', 50, 0),
(159, 15, '南昌市', 50, 0),
(160, 15, '景德镇市', 50, 0),
(161, 15, '萍乡市', 50, 0),
(162, 15, '九江市', 50, 0),
(163, 15, '新余市', 50, 0),
(164, 15, '鹰潭市', 50, 0),
(165, 15, '赣州市', 50, 0),
(166, 15, '吉安市', 50, 0),
(167, 15, '宜春市', 50, 0),
(168, 15, '抚州市', 50, 0),
(169, 15, '上饶市', 50, 0),
(170, 16, '济南市', 50, 0),
(171, 16, '青岛市', 50, 0),
(172, 16, '淄博市', 50, 0),
(173, 16, '枣庄市', 50, 0),
(174, 16, '东营市', 50, 0),
(175, 16, '烟台市', 50, 0),
(176, 16, '潍坊市', 50, 0),
(177, 16, '济宁市', 50, 0),
(178, 16, '泰安市', 50, 0),
(179, 16, '威海市', 50, 0),
(180, 16, '日照市', 50, 0),
(181, 16, '莱芜市', 50, 0),
(182, 16, '临沂市', 50, 0),
(183, 16, '德州市', 50, 0),
(184, 16, '聊城市', 50, 0),
(185, 16, '滨州市', 50, 0),
(186, 16, '菏泽市', 50, 0),
(187, 17, '郑州市', 50, 0),
(188, 17, '开封市', 50, 0),
(189, 17, '洛阳市', 50, 0),
(190, 17, '平顶山市', 50, 0),
(191, 17, '安阳市', 50, 0),
(192, 17, '鹤壁市', 50, 0),
(193, 17, '新乡市', 50, 0),
(194, 17, '焦作市', 50, 0),
(195, 17, '濮阳市', 50, 0),
(196, 17, '许昌市', 50, 0),
(197, 17, '漯河市', 50, 0),
(198, 17, '三门峡市', 50, 0),
(199, 17, '南阳市', 50, 0),
(200, 17, '商丘市', 50, 0),
(201, 17, '信阳市', 50, 0),
(202, 17, '周口市', 50, 0),
(203, 17, '驻马店市', 50, 0),
(204, 18, '武汉市', 50, 0),
(205, 18, '黄石市', 50, 0),
(206, 18, '十堰市', 50, 0),
(207, 18, '宜昌市', 50, 0),
(208, 18, '襄阳市', 50, 0),
(209, 18, '鄂州市', 50, 0),
(210, 18, '荆门市', 50, 0),
(211, 18, '孝感市', 50, 0),
(212, 18, '荆州市', 50, 0),
(213, 18, '黄冈市', 50, 0),
(214, 18, '咸宁市', 50, 0),
(215, 18, '随州市', 50, 0),
(216, 18, '恩施土家族苗族自治州', 50, 0),
(217, 19, '长沙市', 50, 0),
(218, 19, '株洲市', 50, 0),
(219, 19, '湘潭市', 50, 0),
(220, 19, '衡阳市', 50, 0),
(221, 19, '邵阳市', 50, 0),
(222, 19, '岳阳市', 50, 0),
(223, 19, '常德市', 50, 0),
(224, 19, '张家界市', 50, 0),
(225, 19, '益阳市', 50, 0),
(226, 19, '郴州市', 50, 0),
(227, 19, '永州市', 50, 0),
(228, 19, '怀化市', 50, 0),
(229, 19, '娄底市', 50, 0),
(230, 19, '湘西土家族苗族自治州', 50, 0),
(231, 20, '广州市', 50, 0),
(232, 20, '韶关市', 50, 0),
(233, 20, '深圳市', 50, 0),
(234, 20, '珠海市', 50, 0),
(235, 20, '汕头市', 50, 0),
(236, 20, '佛山市', 50, 0),
(237, 20, '江门市', 50, 0),
(238, 20, '湛江市', 50, 0),
(239, 20, '茂名市', 50, 0),
(240, 20, '肇庆市', 50, 0),
(241, 20, '惠州市', 50, 0),
(242, 20, '梅州市', 50, 0),
(243, 20, '汕尾市', 50, 0),
(244, 20, '河源市', 50, 0),
(245, 20, '阳江市', 50, 0),
(246, 20, '清远市', 50, 0),
(247, 20, '东莞市', 50, 0),
(248, 20, '中山市', 50, 0),
(249, 20, '东沙群岛', 50, 0),
(250, 20, '潮州市', 50, 0),
(251, 20, '揭阳市', 50, 0),
(252, 20, '云浮市', 50, 0),
(253, 21, '南宁市', 50, 0),
(254, 21, '柳州市', 50, 0),
(255, 21, '桂林市', 50, 0),
(256, 21, '梧州市', 50, 0),
(257, 21, '北海市', 50, 0),
(258, 21, '防城港市', 50, 0),
(259, 21, '钦州市', 50, 0),
(260, 21, '贵港市', 50, 0),
(261, 21, '玉林市', 50, 0),
(262, 21, '百色市', 50, 0),
(263, 21, '贺州市', 50, 0),
(264, 21, '河池市', 50, 0),
(265, 21, '来宾市', 50, 0),
(266, 21, '崇左市', 50, 0),
(267, 22, '海口市', 50, 0),
(268, 22, '三亚市', 50, 0),
(269, 22, '三沙市', 50, 0),
(270, 23, '重庆市', 50, 0),
(271, 24, '成都市', 50, 0),
(272, 24, '自贡市', 50, 0),
(273, 24, '攀枝花市', 50, 0),
(274, 24, '泸州市', 50, 0),
(275, 24, '德阳市', 50, 0),
(276, 24, '绵阳市', 50, 0),
(277, 24, '广元市', 50, 0),
(278, 24, '遂宁市', 50, 0),
(279, 24, '内江市', 50, 0),
(280, 24, '乐山市', 50, 0),
(281, 24, '南充市', 50, 0),
(282, 24, '眉山市', 50, 0),
(283, 24, '宜宾市', 50, 0),
(284, 24, '广安市', 50, 0),
(285, 24, '达州市', 50, 0),
(286, 24, '雅安市', 50, 0),
(287, 24, '巴中市', 50, 0),
(288, 24, '资阳市', 50, 0),
(289, 24, '阿坝藏族羌族自治州', 50, 0),
(290, 24, '甘孜藏族自治州', 50, 0),
(291, 24, '凉山彝族自治州', 50, 0),
(292, 25, '贵阳市', 50, 0),
(293, 25, '六盘水市', 50, 0),
(294, 25, '遵义市', 50, 0),
(295, 25, '安顺市', 50, 0),
(296, 25, '铜仁市', 50, 0),
(297, 25, '黔西南布依族苗族自治州', 50, 0),
(298, 25, '毕节市', 50, 0),
(299, 25, '黔东南苗族侗族自治州', 50, 0),
(300, 25, '黔南布依族苗族自治州', 50, 0),
(301, 26, '昆明市', 50, 0),
(302, 26, '曲靖市', 50, 0),
(303, 26, '玉溪市', 50, 0),
(304, 26, '保山市', 50, 0),
(305, 26, '昭通市', 50, 0),
(306, 26, '丽江市', 50, 0),
(307, 26, '普洱市', 50, 0),
(308, 26, '临沧市', 50, 0),
(309, 26, '楚雄彝族自治州', 50, 0),
(310, 26, '红河哈尼族彝族自治州', 50, 0),
(311, 26, '文山壮族苗族自治州', 50, 0),
(312, 26, '西双版纳傣族自治州', 50, 0),
(313, 26, '大理白族自治州', 50, 0),
(314, 26, '德宏傣族景颇族自治州', 50, 0),
(315, 26, '怒江傈僳族自治州', 50, 0),
(316, 26, '迪庆藏族自治州', 50, 0),
(317, 27, '拉萨市', 50, 0),
(318, 27, '昌都市', 50, 0),
(319, 27, '山南地区', 50, 0),
(320, 27, '日喀则市', 50, 0),
(321, 27, '那曲地区', 50, 0),
(322, 27, '阿里地区', 50, 0),
(323, 27, '林芝市', 50, 0),
(324, 28, '西安市', 50, 0),
(325, 28, '铜川市', 50, 0),
(326, 28, '宝鸡市', 50, 0),
(327, 28, '咸阳市', 50, 0),
(328, 28, '渭南市', 50, 0),
(329, 28, '延安市', 50, 0),
(330, 28, '汉中市', 50, 0),
(331, 28, '榆林市', 50, 0),
(332, 28, '安康市', 50, 0),
(333, 28, '商洛市', 50, 0),
(334, 29, '兰州市', 50, 0),
(335, 29, '嘉峪关市', 50, 0),
(336, 29, '金昌市', 50, 0),
(337, 29, '白银市', 50, 0),
(338, 29, '天水市', 50, 0),
(339, 29, '武威市', 50, 0),
(340, 29, '张掖市', 50, 0),
(341, 29, '平凉市', 50, 0),
(342, 29, '酒泉市', 50, 0),
(343, 29, '庆阳市', 50, 0),
(344, 29, '定西市', 50, 0),
(345, 29, '陇南市', 50, 0),
(346, 29, '临夏回族自治州', 50, 0),
(347, 29, '甘南藏族自治州', 50, 0),
(348, 30, '西宁市', 50, 0),
(349, 30, '海东市', 50, 0),
(350, 30, '海北藏族自治州', 50, 0),
(351, 30, '黄南藏族自治州', 50, 0),
(352, 30, '海南藏族自治州', 50, 0),
(353, 30, '果洛藏族自治州', 50, 0),
(354, 30, '玉树藏族自治州', 50, 0),
(355, 30, '海西蒙古族藏族自治州', 50, 0),
(356, 31, '银川市', 50, 0),
(357, 31, '石嘴山市', 50, 0),
(358, 31, '吴忠市', 50, 0),
(359, 31, '固原市', 50, 0),
(360, 31, '中卫市', 50, 0),
(361, 32, '乌鲁木齐市', 50, 0),
(362, 32, '克拉玛依市', 50, 0),
(363, 32, '吐鲁番市', 50, 0),
(364, 32, '哈密地区', 50, 0),
(365, 32, '昌吉回族自治州', 50, 0),
(366, 32, '博尔塔拉蒙古自治州', 50, 0),
(367, 32, '巴音郭楞蒙古自治州', 50, 0),
(368, 32, '阿克苏地区', 50, 0),
(369, 32, '克孜勒苏柯尔克孜自治州', 50, 0),
(370, 32, '喀什地区', 50, 0),
(371, 32, '和田地区', 50, 0),
(372, 32, '伊犁哈萨克自治州', 50, 0),
(373, 32, '塔城地区', 50, 0),
(374, 32, '阿勒泰地区', 50, 0),
(375, 33, '台北市', 50, 0),
(376, 33, '高雄市', 50, 0),
(377, 33, '台南市', 50, 0),
(378, 33, '台中市', 50, 0),
(379, 33, '金门县', 50, 0),
(380, 33, '南投县', 50, 0),
(381, 33, '基隆市', 50, 0),
(382, 33, '新竹市', 50, 0),
(383, 33, '嘉义市', 50, 0),
(384, 33, '新北市', 50, 0),
(385, 33, '宜兰县', 50, 0),
(386, 33, '新竹县', 50, 0),
(387, 33, '桃园县', 50, 0),
(388, 33, '苗栗县', 50, 0),
(389, 33, '彰化县', 50, 0),
(390, 33, '嘉义县', 50, 0),
(391, 33, '云林县', 50, 0),
(392, 33, '屏东县', 50, 0),
(393, 33, '台东县', 50, 0),
(394, 33, '花莲县', 50, 0),
(395, 33, '澎湖县', 50, 0),
(396, 33, '连江县', 50, 0),
(397, 34, '香港岛', 50, 0),
(398, 34, '九龙', 50, 0),
(399, 34, '新界', 50, 0),
(400, 35, '澳门半岛', 50, 0),
(401, 35, '离岛', 50, 0),
(402, 36, '海外', 50, 0),
(403, 37, '东城区', 50, 0),
(404, 37, '西城区', 50, 0),
(405, 37, '崇文区', 50, 0),
(406, 37, '宣武区', 50, 0),
(407, 37, '朝阳区', 50, 0),
(408, 37, '丰台区', 50, 0),
(409, 37, '石景山区', 50, 0),
(410, 37, '海淀区', 50, 0),
(411, 37, '门头沟区', 50, 0),
(412, 37, '房山区', 50, 0),
(413, 37, '通州区', 50, 0),
(414, 37, '顺义区', 50, 0),
(415, 37, '昌平区', 50, 0),
(416, 37, '大兴区', 50, 0),
(417, 37, '怀柔区', 50, 0),
(418, 37, '平谷区', 50, 0),
(419, 37, '密云县', 50, 0),
(420, 37, '延庆县', 50, 0),
(421, 37, '其它区', 60, 0),
(422, 38, '和平区', 50, 0),
(423, 38, '河东区', 50, 0),
(424, 38, '河西区', 50, 0),
(425, 38, '南开区', 50, 0),
(426, 38, '河北区', 50, 0),
(427, 38, '红桥区', 50, 0),
(428, 38, '塘沽区', 50, 0),
(429, 38, '汉沽区', 50, 0),
(430, 38, '大港区', 50, 0),
(431, 38, '东丽区', 50, 0),
(432, 38, '西青区', 50, 0),
(433, 38, '津南区', 50, 0),
(434, 38, '北辰区', 50, 0),
(435, 38, '武清区', 50, 0),
(436, 38, '宝坻区', 50, 0),
(437, 38, '滨海新区', 50, 0),
(438, 38, '宁河县', 50, 0),
(439, 38, '静海县', 50, 0),
(440, 38, '蓟县', 50, 0),
(441, 38, '其它区', 60, 0),
(442, 39, '长安区', 50, 0),
(443, 39, '桥东区', 50, 0),
(444, 39, '桥西区', 50, 0),
(445, 39, '新华区', 50, 0),
(446, 39, '井陉矿区', 50, 0),
(447, 39, '裕华区', 50, 0),
(448, 39, '井陉县', 50, 0),
(449, 39, '正定县', 50, 0),
(450, 39, '栾城区', 50, 0),
(451, 39, '行唐县', 50, 0),
(452, 39, '灵寿县', 50, 0),
(453, 39, '高邑县', 50, 0),
(454, 39, '深泽县', 50, 0),
(455, 39, '赞皇县', 50, 0),
(456, 39, '无极县', 50, 0),
(457, 39, '平山县', 50, 0),
(458, 39, '元氏县', 50, 0),
(459, 39, '赵县', 50, 0),
(460, 39, '辛集市', 50, 0),
(461, 39, '藁城区', 50, 0),
(462, 39, '晋州市', 50, 0),
(463, 39, '新乐市', 50, 0),
(464, 39, '鹿泉区', 50, 0),
(465, 39, '其它区', 60, 0),
(466, 40, '路南区', 50, 0),
(467, 40, '路北区', 50, 0),
(468, 40, '古冶区', 50, 0),
(469, 40, '开平区', 50, 0),
(470, 40, '丰南区', 50, 0),
(471, 40, '丰润区', 50, 0),
(472, 40, '滦县', 50, 0),
(473, 40, '滦南县', 50, 0),
(474, 40, '乐亭县', 50, 0),
(475, 40, '迁西县', 50, 0),
(476, 40, '玉田县', 50, 0),
(477, 40, '曹妃甸区', 50, 0),
(478, 40, '遵化市', 50, 0),
(479, 40, '迁安市', 50, 0),
(480, 40, '其它区', 60, 0),
(481, 41, '海港区', 50, 0),
(482, 41, '山海关区', 50, 0),
(483, 41, '北戴河区', 50, 0),
(484, 41, '青龙满族自治县', 50, 0),
(485, 41, '昌黎县', 50, 0),
(486, 41, '抚宁县', 50, 0),
(487, 41, '卢龙县', 50, 0),
(488, 41, '其它区', 60, 0),
(489, 41, '经济技术开发区', 50, 0),
(490, 42, '邯山区', 50, 0),
(491, 42, '丛台区', 50, 0),
(492, 42, '复兴区', 50, 0),
(493, 42, '峰峰矿区', 50, 0),
(494, 42, '邯郸县', 50, 0),
(495, 42, '临漳县', 50, 0),
(496, 42, '成安县', 50, 0),
(497, 42, '大名县', 50, 0),
(498, 42, '涉县', 50, 0),
(499, 42, '磁县', 50, 0),
(500, 42, '肥乡县', 50, 0),
(501, 42, '永年县', 50, 0),
(502, 42, '邱县', 50, 0),
(503, 42, '鸡泽县', 50, 0),
(504, 42, '广平县', 50, 0),
(505, 42, '馆陶县', 50, 0),
(506, 42, '魏县', 50, 0),
(507, 42, '曲周县', 50, 0),
(508, 42, '武安市', 50, 0),
(509, 42, '其它区', 60, 0),
(510, 43, '桥东区', 50, 0),
(511, 43, '桥西区', 50, 0),
(512, 43, '邢台县', 50, 0),
(513, 43, '临城县', 50, 0),
(514, 43, '内丘县', 50, 0),
(515, 43, '柏乡县', 50, 0),
(516, 43, '隆尧县', 50, 0),
(517, 43, '任县', 50, 0),
(518, 43, '南和县', 50, 0),
(519, 43, '宁晋县', 50, 0),
(520, 43, '巨鹿县', 50, 0),
(521, 43, '新河县', 50, 0),
(522, 43, '广宗县', 50, 0),
(523, 43, '平乡县', 50, 0),
(524, 43, '威县', 50, 0),
(525, 43, '清河县', 50, 0),
(526, 43, '临西县', 50, 0),
(527, 43, '南宫市', 50, 0),
(528, 43, '沙河市', 50, 0),
(529, 43, '其它区', 60, 0),
(530, 44, '新市区', 50, 0),
(531, 44, '北市区', 50, 0),
(532, 44, '南市区', 50, 0),
(533, 44, '满城县', 50, 0),
(534, 44, '清苑县', 50, 0),
(535, 44, '涞水县', 50, 0),
(536, 44, '阜平县', 50, 0),
(537, 44, '徐水县', 50, 0),
(538, 44, '定兴县', 50, 0),
(539, 44, '唐县', 50, 0),
(540, 44, '高阳县', 50, 0),
(541, 44, '容城县', 50, 0),
(542, 44, '涞源县', 50, 0),
(543, 44, '望都县', 50, 0),
(544, 44, '安新县', 50, 0),
(545, 44, '易县', 50, 0),
(546, 44, '曲阳县', 50, 0),
(547, 44, '蠡县', 50, 0),
(548, 44, '顺平县', 50, 0),
(549, 44, '博野县', 50, 0),
(550, 44, '雄县', 50, 0),
(551, 44, '涿州市', 50, 0),
(552, 44, '定州市', 50, 0),
(553, 44, '安国市', 50, 0),
(554, 44, '高碑店市', 50, 0),
(555, 44, '高开区', 50, 0),
(556, 44, '其它区', 60, 0),
(557, 45, '桥东区', 50, 0),
(558, 45, '桥西区', 50, 0),
(559, 45, '宣化区', 50, 0),
(560, 45, '下花园区', 50, 0),
(561, 45, '宣化县', 50, 0),
(562, 45, '张北县', 50, 0),
(563, 45, '康保县', 50, 0),
(564, 45, '沽源县', 50, 0),
(565, 45, '尚义县', 50, 0),
(566, 45, '蔚县', 50, 0),
(567, 45, '阳原县', 50, 0),
(568, 45, '怀安县', 50, 0),
(569, 45, '万全县', 50, 0),
(570, 45, '怀来县', 50, 0),
(571, 45, '涿鹿县', 50, 0),
(572, 45, '赤城县', 50, 0),
(573, 45, '崇礼县', 50, 0),
(574, 45, '其它区', 60, 0),
(575, 46, '双桥区', 50, 0),
(576, 46, '双滦区', 50, 0),
(577, 46, '鹰手营子矿区', 50, 0),
(578, 46, '承德县', 50, 0),
(579, 46, '兴隆县', 50, 0),
(580, 46, '平泉县', 50, 0),
(581, 46, '滦平县', 50, 0),
(582, 46, '隆化县', 50, 0),
(583, 46, '丰宁满族自治县', 50, 0),
(584, 46, '宽城满族自治县', 50, 0),
(585, 46, '围场满族蒙古族自治县', 50, 0),
(586, 46, '其它区', 60, 0),
(587, 47, '新华区', 50, 0),
(588, 47, '运河区', 50, 0),
(589, 47, '沧县', 50, 0),
(590, 47, '青县', 50, 0),
(591, 47, '东光县', 50, 0),
(592, 47, '海兴县', 50, 0),
(593, 47, '盐山县', 50, 0),
(594, 47, '肃宁县', 50, 0),
(595, 47, '南皮县', 50, 0),
(596, 47, '吴桥县', 50, 0),
(597, 47, '献县', 50, 0),
(598, 47, '孟村回族自治县', 50, 0),
(599, 47, '泊头市', 50, 0),
(600, 47, '任丘市', 50, 0),
(601, 47, '黄骅市', 50, 0),
(602, 47, '河间市', 50, 0),
(603, 47, '其它区', 60, 0),
(604, 48, '安次区', 50, 0),
(605, 48, '广阳区', 50, 0),
(606, 48, '固安县', 50, 0),
(607, 48, '永清县', 50, 0),
(608, 48, '香河县', 50, 0),
(609, 48, '大城县', 50, 0),
(610, 48, '文安县', 50, 0),
(611, 48, '大厂回族自治县', 50, 0),
(612, 48, '开发区', 50, 0),
(613, 48, '燕郊经济技术开发区', 50, 0),
(614, 48, '霸州市', 50, 0),
(615, 48, '三河市', 50, 0),
(616, 48, '其它区', 60, 0),
(617, 49, '桃城区', 50, 0),
(618, 49, '枣强县', 50, 0),
(619, 49, '武邑县', 50, 0),
(620, 49, '武强县', 50, 0),
(621, 49, '饶阳县', 50, 0),
(622, 49, '安平县', 50, 0),
(623, 49, '故城县', 50, 0),
(624, 49, '景县', 50, 0),
(625, 49, '阜城县', 50, 0),
(626, 49, '冀州市', 50, 0),
(627, 49, '深州市', 50, 0),
(628, 49, '其它区', 60, 0),
(629, 50, '小店区', 50, 0),
(630, 50, '迎泽区', 50, 0),
(631, 50, '杏花岭区', 50, 0),
(632, 50, '尖草坪区', 50, 0),
(633, 50, '万柏林区', 50, 0),
(634, 50, '晋源区', 50, 0),
(635, 50, '清徐县', 50, 0),
(636, 50, '阳曲县', 50, 0),
(637, 50, '娄烦县', 50, 0),
(638, 50, '古交市', 50, 0),
(639, 50, '其它区', 60, 0),
(640, 51, '城区', 50, 0),
(641, 51, '矿区', 50, 0),
(642, 51, '南郊区', 50, 0),
(643, 51, '新荣区', 50, 0),
(644, 51, '阳高县', 50, 0),
(645, 51, '天镇县', 50, 0),
(646, 51, '广灵县', 50, 0),
(647, 51, '灵丘县', 50, 0),
(648, 51, '浑源县', 50, 0),
(649, 51, '左云县', 50, 0),
(650, 51, '大同县', 50, 0),
(651, 51, '其它区', 60, 0),
(652, 52, '城区', 50, 0),
(653, 52, '矿区', 50, 0),
(654, 52, '郊区', 50, 0),
(655, 52, '平定县', 50, 0),
(656, 52, '盂县', 50, 0),
(657, 52, '其它区', 60, 0),
(658, 53, '长治县', 50, 0),
(659, 53, '襄垣县', 50, 0),
(660, 53, '屯留县', 50, 0),
(661, 53, '平顺县', 50, 0),
(662, 53, '黎城县', 50, 0),
(663, 53, '壶关县', 50, 0),
(664, 53, '长子县', 50, 0),
(665, 53, '武乡县', 50, 0),
(666, 53, '沁县', 50, 0),
(667, 53, '沁源县', 50, 0),
(668, 53, '潞城市', 50, 0),
(669, 53, '城区', 50, 0),
(670, 53, '郊区', 50, 0),
(671, 53, '高新区', 50, 0),
(672, 53, '其它区', 60, 0),
(673, 54, '城区', 50, 0),
(674, 54, '沁水县', 50, 0),
(675, 54, '阳城县', 50, 0),
(676, 54, '陵川县', 50, 0),
(677, 54, '泽州县', 50, 0),
(678, 54, '高平市', 50, 0),
(679, 54, '其它区', 60, 0),
(680, 55, '朔城区', 50, 0),
(681, 55, '平鲁区', 50, 0),
(682, 55, '山阴县', 50, 0),
(683, 55, '应县', 50, 0),
(684, 55, '右玉县', 50, 0),
(685, 55, '怀仁县', 50, 0),
(686, 55, '其它区', 60, 0),
(687, 56, '榆次区', 50, 0),
(688, 56, '榆社县', 50, 0),
(689, 56, '左权县', 50, 0),
(690, 56, '和顺县', 50, 0),
(691, 56, '昔阳县', 50, 0),
(692, 56, '寿阳县', 50, 0),
(693, 56, '太谷县', 50, 0),
(694, 56, '祁县', 50, 0),
(695, 56, '平遥县', 50, 0),
(696, 56, '灵石县', 50, 0),
(697, 56, '介休市', 50, 0),
(698, 56, '其它区', 60, 0),
(699, 57, '盐湖区', 50, 0),
(700, 57, '临猗县', 50, 0),
(701, 57, '万荣县', 50, 0),
(702, 57, '闻喜县', 50, 0),
(703, 57, '稷山县', 50, 0),
(704, 57, '新绛县', 50, 0),
(705, 57, '绛县', 50, 0),
(706, 57, '垣曲县', 50, 0),
(707, 57, '夏县', 50, 0),
(708, 57, '平陆县', 50, 0),
(709, 57, '芮城县', 50, 0),
(710, 57, '永济市', 50, 0),
(711, 57, '河津市', 50, 0),
(712, 57, '其它区', 60, 0),
(713, 58, '忻府区', 50, 0),
(714, 58, '定襄县', 50, 0),
(715, 58, '五台县', 50, 0),
(716, 58, '代县', 50, 0),
(717, 58, '繁峙县', 50, 0),
(718, 58, '宁武县', 50, 0),
(719, 58, '静乐县', 50, 0),
(720, 58, '神池县', 50, 0),
(721, 58, '五寨县', 50, 0),
(722, 58, '岢岚县', 50, 0),
(723, 58, '河曲县', 50, 0),
(724, 58, '保德县', 50, 0),
(725, 58, '偏关县', 50, 0),
(726, 58, '原平市', 50, 0),
(727, 58, '其它区', 60, 0),
(728, 59, '尧都区', 50, 0),
(729, 59, '曲沃县', 50, 0),
(730, 59, '翼城县', 50, 0),
(731, 59, '襄汾县', 50, 0),
(732, 59, '洪洞县', 50, 0),
(733, 59, '古县', 50, 0),
(734, 59, '安泽县', 50, 0),
(735, 59, '浮山县', 50, 0),
(736, 59, '吉县', 50, 0),
(737, 59, '乡宁县', 50, 0),
(738, 59, '大宁县', 50, 0),
(739, 59, '隰县', 50, 0),
(740, 59, '永和县', 50, 0),
(741, 59, '蒲县', 50, 0),
(742, 59, '汾西县', 50, 0),
(743, 59, '侯马市', 50, 0),
(744, 59, '霍州市', 50, 0),
(745, 59, '其它区', 60, 0),
(746, 60, '离石区', 50, 0),
(747, 60, '文水县', 50, 0),
(748, 60, '交城县', 50, 0),
(749, 60, '兴县', 50, 0),
(750, 60, '临县', 50, 0),
(751, 60, '柳林县', 50, 0),
(752, 60, '石楼县', 50, 0),
(753, 60, '岚县', 50, 0),
(754, 60, '方山县', 50, 0),
(755, 60, '中阳县', 50, 0),
(756, 60, '交口县', 50, 0),
(757, 60, '孝义市', 50, 0),
(758, 60, '汾阳市', 50, 0),
(759, 60, '其它区', 60, 0),
(760, 61, '新城区', 50, 0),
(761, 61, '回民区', 50, 0),
(762, 61, '玉泉区', 50, 0),
(763, 61, '赛罕区', 50, 0),
(764, 61, '土默特左旗', 50, 0),
(765, 61, '托克托县', 50, 0),
(766, 61, '和林格尔县', 50, 0),
(767, 61, '清水河县', 50, 0),
(768, 61, '武川县', 50, 0),
(769, 61, '其它区', 60, 0),
(770, 62, '东河区', 50, 0),
(771, 62, '昆都仑区', 50, 0),
(772, 62, '青山区', 50, 0),
(773, 62, '石拐区', 50, 0),
(774, 62, '白云鄂博矿区', 50, 0),
(775, 62, '九原区', 50, 0),
(776, 62, '土默特右旗', 50, 0),
(777, 62, '固阳县', 50, 0),
(778, 62, '达尔罕茂明安联合旗', 50, 0),
(779, 62, '其它区', 60, 0),
(780, 63, '海勃湾区', 50, 0),
(781, 63, '海南区', 50, 0),
(782, 63, '乌达区', 50, 0),
(783, 63, '其它区', 60, 0),
(784, 64, '红山区', 50, 0),
(785, 64, '元宝山区', 50, 0),
(786, 64, '松山区', 50, 0),
(787, 64, '阿鲁科尔沁旗', 50, 0),
(788, 64, '巴林左旗', 50, 0),
(789, 64, '巴林右旗', 50, 0),
(790, 64, '林西县', 50, 0),
(791, 64, '克什克腾旗', 50, 0),
(792, 64, '翁牛特旗', 50, 0),
(793, 64, '喀喇沁旗', 50, 0),
(794, 64, '宁城县', 50, 0),
(795, 64, '敖汉旗', 50, 0),
(796, 64, '其它区', 60, 0),
(797, 65, '科尔沁区', 50, 0),
(798, 65, '科尔沁左翼中旗', 50, 0),
(799, 65, '科尔沁左翼后旗', 50, 0),
(800, 65, '开鲁县', 50, 0),
(801, 65, '库伦旗', 50, 0),
(802, 65, '奈曼旗', 50, 0),
(803, 65, '扎鲁特旗', 50, 0),
(804, 65, '霍林郭勒市', 50, 0),
(805, 65, '其它区', 60, 0),
(806, 66, '东胜区', 50, 0),
(807, 66, '达拉特旗', 50, 0),
(808, 66, '准格尔旗', 50, 0),
(809, 66, '鄂托克前旗', 50, 0),
(810, 66, '鄂托克旗', 50, 0),
(811, 66, '杭锦旗', 50, 0),
(812, 66, '乌审旗', 50, 0),
(813, 66, '伊金霍洛旗', 50, 0),
(814, 66, '其它区', 60, 0),
(815, 67, '海拉尔区', 50, 0),
(816, 67, '扎赉诺尔区', 50, 0),
(817, 67, '阿荣旗', 50, 0),
(818, 67, '莫力达瓦达斡尔族自治旗', 50, 0),
(819, 67, '鄂伦春自治旗', 50, 0),
(820, 67, '鄂温克族自治旗', 50, 0),
(821, 67, '陈巴尔虎旗', 50, 0),
(822, 67, '新巴尔虎左旗', 50, 0),
(823, 67, '新巴尔虎右旗', 50, 0),
(824, 67, '满洲里市', 50, 0),
(825, 67, '牙克石市', 50, 0),
(826, 67, '扎兰屯市', 50, 0),
(827, 67, '额尔古纳市', 50, 0),
(828, 67, '根河市', 50, 0),
(829, 67, '其它区', 60, 0),
(830, 68, '临河区', 50, 0),
(831, 68, '五原县', 50, 0),
(832, 68, '磴口县', 50, 0),
(833, 68, '乌拉特前旗', 50, 0),
(834, 68, '乌拉特中旗', 50, 0),
(835, 68, '乌拉特后旗', 50, 0),
(836, 68, '杭锦后旗', 50, 0),
(837, 68, '其它区', 60, 0),
(838, 69, '集宁区', 50, 0),
(839, 69, '卓资县', 50, 0),
(840, 69, '化德县', 50, 0),
(841, 69, '商都县', 50, 0),
(842, 69, '兴和县', 50, 0),
(843, 69, '凉城县', 50, 0),
(844, 69, '察哈尔右翼前旗', 50, 0),
(845, 69, '察哈尔右翼中旗', 50, 0),
(846, 69, '察哈尔右翼后旗', 50, 0),
(847, 69, '四子王旗', 50, 0),
(848, 69, '丰镇市', 50, 0),
(849, 69, '其它区', 60, 0),
(850, 70, '乌兰浩特市', 50, 0),
(851, 70, '阿尔山市', 50, 0),
(852, 70, '科尔沁右翼前旗', 50, 0),
(853, 70, '科尔沁右翼中旗', 50, 0),
(854, 70, '扎赉特旗', 50, 0),
(855, 70, '突泉县', 50, 0),
(856, 70, '其它区', 60, 0),
(857, 71, '二连浩特市', 50, 0),
(858, 71, '锡林浩特市', 50, 0),
(859, 71, '阿巴嘎旗', 50, 0),
(860, 71, '苏尼特左旗', 50, 0),
(861, 71, '苏尼特右旗', 50, 0),
(862, 71, '东乌珠穆沁旗', 50, 0),
(863, 71, '西乌珠穆沁旗', 50, 0),
(864, 71, '太仆寺旗', 50, 0),
(865, 71, '镶黄旗', 50, 0),
(866, 71, '正镶白旗', 50, 0),
(867, 71, '正蓝旗', 50, 0),
(868, 71, '多伦县', 50, 0),
(869, 71, '其它区', 60, 0),
(870, 72, '阿拉善左旗', 50, 0),
(871, 72, '阿拉善右旗', 50, 0),
(872, 72, '额济纳旗', 50, 0),
(873, 72, '其它区', 60, 0),
(874, 73, '和平区', 50, 0),
(875, 73, '沈河区', 50, 0),
(876, 73, '大东区', 50, 0),
(877, 73, '皇姑区', 50, 0),
(878, 73, '铁西区', 50, 0),
(879, 73, '苏家屯区', 50, 0),
(880, 73, '浑南区', 50, 0),
(881, 73, '新城子区', 50, 0),
(882, 73, '于洪区', 50, 0),
(883, 73, '辽中县', 50, 0),
(884, 73, '康平县', 50, 0),
(885, 73, '法库县', 50, 0),
(886, 73, '新民市', 50, 0),
(887, 73, '浑南新区', 50, 0),
(888, 73, '张士开发区', 50, 0),
(889, 73, '沈北新区', 50, 0),
(890, 73, '其它区', 60, 0),
(891, 74, '中山区', 50, 0),
(892, 74, '西岗区', 50, 0),
(893, 74, '沙河口区', 50, 0),
(894, 74, '甘井子区', 50, 0),
(895, 74, '旅顺口区', 50, 0),
(896, 74, '金州区', 50, 0),
(897, 74, '长海县', 50, 0),
(898, 74, '开发区', 50, 0),
(899, 74, '瓦房店市', 50, 0),
(900, 74, '普兰店市', 50, 0),
(901, 74, '庄河市', 50, 0),
(902, 74, '岭前区', 50, 0),
(903, 74, '其它区', 60, 0),
(904, 75, '铁东区', 50, 0),
(905, 75, '铁西区', 50, 0),
(906, 75, '立山区', 50, 0),
(907, 75, '千山区', 50, 0),
(908, 75, '台安县', 50, 0),
(909, 75, '岫岩满族自治县', 50, 0),
(910, 75, '高新区', 50, 0),
(911, 75, '海城市', 50, 0),
(912, 75, '其它区', 60, 0),
(913, 76, '新抚区', 50, 0),
(914, 76, '东洲区', 50, 0),
(915, 76, '望花区', 50, 0),
(916, 76, '顺城区', 50, 0),
(917, 76, '抚顺县', 50, 0),
(918, 76, '新宾满族自治县', 50, 0),
(919, 76, '清原满族自治县', 50, 0),
(920, 76, '其它区', 60, 0),
(921, 77, '平山区', 50, 0),
(922, 77, '溪湖区', 50, 0),
(923, 77, '明山区', 50, 0),
(924, 77, '南芬区', 50, 0),
(925, 77, '本溪满族自治县', 50, 0),
(926, 77, '桓仁满族自治县', 50, 0),
(927, 77, '其它区', 60, 0),
(928, 78, '元宝区', 50, 0),
(929, 78, '振兴区', 50, 0),
(930, 78, '振安区', 50, 0),
(931, 78, '宽甸满族自治县', 50, 0),
(932, 78, '东港市', 50, 0),
(933, 78, '凤城市', 50, 0),
(934, 78, '其它区', 60, 0),
(935, 79, '古塔区', 50, 0),
(936, 79, '凌河区', 50, 0),
(937, 79, '太和区', 50, 0),
(938, 79, '黑山县', 50, 0),
(939, 79, '义县', 50, 0),
(940, 79, '凌海市', 50, 0),
(941, 79, '北镇市', 50, 0),
(942, 79, '其它区', 60, 0),
(943, 80, '站前区', 50, 0),
(944, 80, '西市区', 50, 0),
(945, 80, '鲅鱼圈区', 50, 0),
(946, 80, '老边区', 50, 0),
(947, 80, '盖州市', 50, 0),
(948, 80, '大石桥市', 50, 0),
(949, 80, '其它区', 60, 0),
(950, 81, '海州区', 50, 0),
(951, 81, '新邱区', 50, 0),
(952, 81, '太平区', 50, 0),
(953, 81, '清河门区', 50, 0),
(954, 81, '细河区', 50, 0),
(955, 81, '阜新蒙古族自治县', 50, 0),
(956, 81, '彰武县', 50, 0),
(957, 81, '其它区', 60, 0),
(958, 82, '白塔区', 50, 0),
(959, 82, '文圣区', 50, 0),
(960, 82, '宏伟区', 50, 0),
(961, 82, '弓长岭区', 50, 0),
(962, 82, '太子河区', 50, 0),
(963, 82, '辽阳县', 50, 0),
(964, 82, '灯塔市', 50, 0),
(965, 82, '其它区', 60, 0),
(966, 83, '双台子区', 50, 0),
(967, 83, '兴隆台区', 50, 0),
(968, 83, '大洼县', 50, 0),
(969, 83, '盘山县', 50, 0),
(970, 83, '其它区', 60, 0),
(971, 84, '银州区', 50, 0),
(972, 84, '清河区', 50, 0),
(973, 84, '铁岭县', 50, 0),
(974, 84, '西丰县', 50, 0),
(975, 84, '昌图县', 50, 0),
(976, 84, '调兵山市', 50, 0),
(977, 84, '开原市', 50, 0),
(978, 84, '其它区', 60, 0),
(979, 85, '双塔区', 50, 0),
(980, 85, '龙城区', 50, 0),
(981, 85, '朝阳县', 50, 0),
(982, 85, '建平县', 50, 0),
(983, 85, '喀喇沁左翼蒙古族自治县', 50, 0),
(984, 85, '北票市', 50, 0),
(985, 85, '凌源市', 50, 0),
(986, 85, '其它区', 60, 0),
(987, 86, '连山区', 50, 0),
(988, 86, '龙港区', 50, 0),
(989, 86, '南票区', 50, 0),
(990, 86, '绥中县', 50, 0),
(991, 86, '建昌县', 50, 0),
(992, 86, '兴城市', 50, 0),
(993, 86, '其它区', 60, 0),
(994, 87, '南关区', 50, 0),
(995, 87, '宽城区', 50, 0),
(996, 87, '朝阳区', 50, 0),
(997, 87, '二道区', 50, 0),
(998, 87, '绿园区', 50, 0),
(999, 87, '双阳区', 50, 0),
(1000, 87, '农安县', 50, 0),
(1001, 87, '九台区', 50, 0),
(1002, 87, '榆树市', 50, 0),
(1003, 87, '德惠市', 50, 0),
(1004, 87, '高新技术产业开发区', 50, 0),
(1005, 87, '汽车产业开发区', 50, 0),
(1006, 87, '经济技术开发区', 50, 0),
(1007, 87, '净月旅游开发区', 50, 0),
(1008, 87, '其它区', 60, 0),
(1009, 88, '昌邑区', 50, 0),
(1010, 88, '龙潭区', 50, 0),
(1011, 88, '船营区', 50, 0),
(1012, 88, '丰满区', 50, 0),
(1013, 88, '永吉县', 50, 0),
(1014, 88, '蛟河市', 50, 0),
(1015, 88, '桦甸市', 50, 0),
(1016, 88, '舒兰市', 50, 0),
(1017, 88, '磐石市', 50, 0),
(1018, 88, '其它区', 60, 0),
(1019, 89, '铁西区', 50, 0),
(1020, 89, '铁东区', 50, 0),
(1021, 89, '梨树县', 50, 0),
(1022, 89, '伊通满族自治县', 50, 0),
(1023, 89, '公主岭市', 50, 0),
(1024, 89, '双辽市', 50, 0),
(1025, 89, '其它区', 60, 0),
(1026, 90, '龙山区', 50, 0),
(1027, 90, '西安区', 50, 0),
(1028, 90, '东丰县', 50, 0),
(1029, 90, '东辽县', 50, 0),
(1030, 90, '其它区', 60, 0),
(1031, 91, '东昌区', 50, 0),
(1032, 91, '二道江区', 50, 0),
(1033, 91, '通化县', 50, 0),
(1034, 91, '辉南县', 50, 0),
(1035, 91, '柳河县', 50, 0),
(1036, 91, '梅河口市', 50, 0),
(1037, 91, '集安市', 50, 0),
(1038, 91, '其它区', 60, 0),
(1039, 92, '浑江区', 50, 0),
(1040, 92, '抚松县', 50, 0),
(1041, 92, '靖宇县', 50, 0),
(1042, 92, '长白朝鲜族自治县', 50, 0),
(1043, 92, '江源区', 50, 0),
(1044, 92, '临江市', 50, 0),
(1045, 92, '其它区', 60, 0),
(1046, 93, '宁江区', 50, 0),
(1047, 93, '前郭尔罗斯蒙古族自治县', 50, 0),
(1048, 93, '长岭县', 50, 0),
(1049, 93, '乾安县', 50, 0),
(1050, 93, '扶余市', 50, 0),
(1051, 93, '其它区', 60, 0),
(1052, 94, '洮北区', 50, 0),
(1053, 94, '镇赉县', 50, 0),
(1054, 94, '通榆县', 50, 0),
(1055, 94, '洮南市', 50, 0),
(1056, 94, '大安市', 50, 0),
(1057, 94, '其它区', 60, 0),
(1058, 95, '延吉市', 50, 0),
(1059, 95, '图们市', 50, 0),
(1060, 95, '敦化市', 50, 0),
(1061, 95, '珲春市', 50, 0),
(1062, 95, '龙井市', 50, 0),
(1063, 95, '和龙市', 50, 0),
(1064, 95, '汪清县', 50, 0),
(1065, 95, '安图县', 50, 0),
(1066, 95, '其它区', 60, 0),
(1067, 96, '道里区', 50, 0),
(1068, 96, '南岗区', 50, 0),
(1069, 96, '道外区', 50, 0),
(1070, 96, '香坊区', 50, 0),
(1071, 96, '动力区', 50, 0),
(1072, 96, '平房区', 50, 0),
(1073, 96, '松北区', 50, 0),
(1074, 96, '呼兰区', 50, 0),
(1075, 96, '依兰县', 50, 0),
(1076, 96, '方正县', 50, 0),
(1077, 96, '宾县', 50, 0),
(1078, 96, '巴彦县', 50, 0),
(1079, 96, '木兰县', 50, 0),
(1080, 96, '通河县', 50, 0),
(1081, 96, '延寿县', 50, 0),
(1082, 96, '阿城区', 50, 0),
(1083, 96, '双城区', 50, 0),
(1084, 96, '尚志市', 50, 0),
(1085, 96, '五常市', 50, 0),
(1086, 96, '阿城市', 50, 0),
(1087, 96, '其它区', 60, 0),
(1088, 97, '龙沙区', 50, 0),
(1089, 97, '建华区', 50, 0),
(1090, 97, '铁锋区', 50, 0),
(1091, 97, '昂昂溪区', 50, 0),
(1092, 97, '富拉尔基区', 50, 0),
(1093, 97, '碾子山区', 50, 0),
(1094, 97, '梅里斯达斡尔族区', 50, 0),
(1095, 97, '龙江县', 50, 0),
(1096, 97, '依安县', 50, 0),
(1097, 97, '泰来县', 50, 0),
(1098, 97, '甘南县', 50, 0),
(1099, 97, '富裕县', 50, 0),
(1100, 97, '克山县', 50, 0),
(1101, 97, '克东县', 50, 0),
(1102, 97, '拜泉县', 50, 0),
(1103, 97, '讷河市', 50, 0),
(1104, 97, '其它区', 60, 0),
(1105, 98, '鸡冠区', 50, 0),
(1106, 98, '恒山区', 50, 0),
(1107, 98, '滴道区', 50, 0),
(1108, 98, '梨树区', 50, 0),
(1109, 98, '城子河区', 50, 0),
(1110, 98, '麻山区', 50, 0),
(1111, 98, '鸡东县', 50, 0),
(1112, 98, '虎林市', 50, 0),
(1113, 98, '密山市', 50, 0),
(1114, 98, '其它区', 60, 0),
(1115, 99, '向阳区', 50, 0),
(1116, 99, '工农区', 50, 0),
(1117, 99, '南山区', 50, 0),
(1118, 99, '兴安区', 50, 0),
(1119, 99, '东山区', 50, 0),
(1120, 99, '兴山区', 50, 0),
(1121, 99, '萝北县', 50, 0),
(1122, 99, '绥滨县', 50, 0),
(1123, 99, '其它区', 60, 0),
(1124, 100, '尖山区', 50, 0),
(1125, 100, '岭东区', 50, 0),
(1126, 100, '四方台区', 50, 0),
(1127, 100, '宝山区', 50, 0),
(1128, 100, '集贤县', 50, 0),
(1129, 100, '友谊县', 50, 0),
(1130, 100, '宝清县', 50, 0),
(1131, 100, '饶河县', 50, 0),
(1132, 100, '其它区', 60, 0),
(1133, 101, '萨尔图区', 50, 0),
(1134, 101, '龙凤区', 50, 0),
(1135, 101, '让胡路区', 50, 0),
(1136, 101, '红岗区', 50, 0),
(1137, 101, '大同区', 50, 0),
(1138, 101, '肇州县', 50, 0),
(1139, 101, '肇源县', 50, 0),
(1140, 101, '林甸县', 50, 0),
(1141, 101, '杜尔伯特蒙古族自治县', 50, 0),
(1142, 101, '其它区', 60, 0),
(1143, 102, '伊春区', 50, 0),
(1144, 102, '南岔区', 50, 0),
(1145, 102, '友好区', 50, 0),
(1146, 102, '西林区', 50, 0),
(1147, 102, '翠峦区', 50, 0),
(1148, 102, '新青区', 50, 0),
(1149, 102, '美溪区', 50, 0),
(1150, 102, '金山屯区', 50, 0),
(1151, 102, '五营区', 50, 0),
(1152, 102, '乌马河区', 50, 0),
(1153, 102, '汤旺河区', 50, 0),
(1154, 102, '带岭区', 50, 0),
(1155, 102, '乌伊岭区', 50, 0),
(1156, 102, '红星区', 50, 0),
(1157, 102, '上甘岭区', 50, 0),
(1158, 102, '嘉荫县', 50, 0),
(1159, 102, '铁力市', 50, 0),
(1160, 102, '其它区', 60, 0),
(1161, 103, '永红区', 50, 0),
(1162, 103, '向阳区', 50, 0),
(1163, 103, '前进区', 50, 0),
(1164, 103, '东风区', 50, 0),
(1165, 103, '郊区', 50, 0),
(1166, 103, '桦南县', 50, 0),
(1167, 103, '桦川县', 50, 0),
(1168, 103, '汤原县', 50, 0),
(1169, 103, '抚远县', 50, 0),
(1170, 103, '同江市', 50, 0),
(1171, 103, '富锦市', 50, 0),
(1172, 103, '其它区', 60, 0),
(1173, 104, '新兴区', 50, 0),
(1174, 104, '桃山区', 50, 0),
(1175, 104, '茄子河区', 50, 0),
(1176, 104, '勃利县', 50, 0),
(1177, 104, '其它区', 60, 0),
(1178, 105, '东安区', 50, 0),
(1179, 105, '阳明区', 50, 0),
(1180, 105, '爱民区', 50, 0),
(1181, 105, '西安区', 50, 0),
(1182, 105, '东宁县', 50, 0),
(1183, 105, '林口县', 50, 0),
(1184, 105, '绥芬河市', 50, 0),
(1185, 105, '海林市', 50, 0),
(1186, 105, '宁安市', 50, 0),
(1187, 105, '穆棱市', 50, 0),
(1188, 105, '其它区', 60, 0),
(1189, 106, '爱辉区', 50, 0),
(1190, 106, '嫩江县', 50, 0),
(1191, 106, '逊克县', 50, 0),
(1192, 106, '孙吴县', 50, 0),
(1193, 106, '北安市', 50, 0),
(1194, 106, '五大连池市', 50, 0),
(1195, 106, '其它区', 60, 0),
(1196, 107, '北林区', 50, 0),
(1197, 107, '望奎县', 50, 0),
(1198, 107, '兰西县', 50, 0),
(1199, 107, '青冈县', 50, 0),
(1200, 107, '庆安县', 50, 0),
(1201, 107, '明水县', 50, 0),
(1202, 107, '绥棱县', 50, 0),
(1203, 107, '安达市', 50, 0),
(1204, 107, '肇东市', 50, 0),
(1205, 107, '海伦市', 50, 0),
(1206, 107, '其它区', 60, 0),
(1207, 108, '松岭区', 50, 0),
(1208, 108, '新林区', 50, 0),
(1209, 108, '呼中区', 50, 0),
(1210, 108, '呼玛县', 50, 0),
(1211, 108, '塔河县', 50, 0),
(1212, 108, '漠河县', 50, 0),
(1213, 108, '加格达奇区', 50, 0),
(1214, 108, '其它区', 60, 0),
(1215, 109, '黄浦区', 50, 0),
(1216, 109, '卢湾区', 50, 0),
(1217, 109, '徐汇区', 50, 0),
(1218, 109, '长宁区', 50, 0),
(1219, 109, '静安区', 50, 0),
(1220, 109, '普陀区', 50, 0),
(1221, 109, '闸北区', 50, 0),
(1222, 109, '虹口区', 50, 0),
(1223, 109, '杨浦区', 50, 0),
(1224, 109, '闵行区', 50, 0),
(1225, 109, '宝山区', 50, 0),
(1226, 109, '嘉定区', 50, 0),
(1227, 109, '浦东新区', 50, 0),
(1228, 109, '金山区', 50, 0),
(1229, 109, '松江区', 50, 0),
(1230, 109, '青浦区', 50, 0),
(1231, 109, '南汇区', 50, 0),
(1232, 109, '奉贤区', 50, 0),
(1233, 109, '川沙区', 50, 0),
(1234, 109, '崇明县', 50, 0),
(1235, 109, '其它区', 60, 0),
(1236, 110, '玄武区', 50, 0),
(1237, 110, '白下区', 50, 0),
(1238, 110, '秦淮区', 50, 0),
(1239, 110, '建邺区', 50, 0),
(1240, 110, '鼓楼区', 50, 0),
(1241, 110, '下关区', 50, 0),
(1242, 110, '浦口区', 50, 0),
(1243, 110, '栖霞区', 50, 0),
(1244, 110, '雨花台区', 50, 0),
(1245, 110, '江宁区', 50, 0),
(1246, 110, '六合区', 50, 0),
(1247, 110, '溧水区', 50, 0),
(1248, 110, '高淳区', 50, 0),
(1249, 110, '其它区', 60, 0),
(1250, 111, '崇安区', 50, 0),
(1251, 111, '南长区', 50, 0),
(1252, 111, '北塘区', 50, 0),
(1253, 111, '锡山区', 50, 0),
(1254, 111, '惠山区', 50, 0),
(1255, 111, '滨湖区', 50, 0),
(1256, 111, '江阴市', 50, 0),
(1257, 111, '宜兴市', 50, 0),
(1258, 111, '新区', 50, 0),
(1259, 111, '其它区', 60, 0),
(1260, 112, '鼓楼区', 50, 0),
(1261, 112, '云龙区', 50, 0),
(1262, 112, '九里区', 50, 0),
(1263, 112, '贾汪区', 50, 0),
(1264, 112, '泉山区', 50, 0),
(1265, 112, '丰县', 50, 0),
(1266, 112, '沛县', 50, 0),
(1267, 112, '铜山区', 50, 0),
(1268, 112, '睢宁县', 50, 0),
(1269, 112, '新沂市', 50, 0),
(1270, 112, '邳州市', 50, 0),
(1271, 112, '其它区', 60, 0),
(1272, 113, '天宁区', 50, 0),
(1273, 113, '钟楼区', 50, 0),
(1274, 113, '戚墅堰区', 50, 0),
(1275, 113, '新北区', 50, 0),
(1276, 113, '武进区', 50, 0),
(1277, 113, '溧阳市', 50, 0),
(1278, 113, '金坛市', 50, 0),
(1279, 113, '其它区', 60, 0),
(1280, 114, '沧浪区', 50, 0),
(1281, 114, '平江区', 50, 0),
(1282, 114, '金阊区', 50, 0),
(1283, 114, '虎丘区', 50, 0),
(1284, 114, '吴中区', 50, 0),
(1285, 114, '相城区', 50, 0),
(1286, 114, '姑苏区', 50, 0),
(1287, 114, '常熟市', 50, 0),
(1288, 114, '张家港市', 50, 0),
(1289, 114, '昆山市', 50, 0),
(1290, 114, '吴江区', 50, 0),
(1291, 114, '太仓市', 50, 0),
(1292, 114, '新区', 50, 0),
(1293, 114, '园区', 50, 0),
(1294, 114, '其它区', 60, 0),
(1295, 115, '崇川区', 50, 0),
(1296, 115, '港闸区', 50, 0),
(1297, 115, '通州区', 50, 0),
(1298, 115, '海安县', 50, 0),
(1299, 115, '如东县', 50, 0),
(1300, 115, '启东市', 50, 0),
(1301, 115, '如皋市', 50, 0),
(1302, 115, '通州市', 50, 0),
(1303, 115, '海门市', 50, 0),
(1304, 115, '开发区', 50, 0),
(1305, 115, '其它区', 60, 0),
(1306, 116, '连云区', 50, 0),
(1307, 116, '新浦区', 50, 0),
(1308, 116, '海州区', 50, 0),
(1309, 116, '赣榆区', 50, 0),
(1310, 116, '东海县', 50, 0),
(1311, 116, '灌云县', 50, 0),
(1312, 116, '灌南县', 50, 0),
(1313, 116, '其它区', 60, 0),
(1314, 117, '清河区', 50, 0),
(1315, 117, '淮安区', 50, 0),
(1316, 117, '淮阴区', 50, 0),
(1317, 117, '清浦区', 50, 0),
(1318, 117, '涟水县', 50, 0),
(1319, 117, '洪泽县', 50, 0),
(1320, 117, '盱眙县', 50, 0),
(1321, 117, '金湖县', 50, 0),
(1322, 117, '其它区', 60, 0),
(1323, 118, '亭湖区', 50, 0),
(1324, 118, '盐都区', 50, 0),
(1325, 118, '响水县', 50, 0),
(1326, 118, '滨海县', 50, 0),
(1327, 118, '阜宁县', 50, 0),
(1328, 118, '射阳县', 50, 0),
(1329, 118, '建湖县', 50, 0),
(1330, 118, '东台市', 50, 0),
(1331, 118, '大丰市', 50, 0),
(1332, 118, '其它区', 60, 0),
(1333, 119, '广陵区', 50, 0),
(1334, 119, '邗江区', 50, 0),
(1335, 119, '维扬区', 50, 0),
(1336, 119, '宝应县', 50, 0),
(1337, 119, '仪征市', 50, 0),
(1338, 119, '高邮市', 50, 0),
(1339, 119, '江都区', 50, 0),
(1340, 119, '经济开发区', 50, 0),
(1341, 119, '其它区', 60, 0),
(1342, 120, '京口区', 50, 0),
(1343, 120, '润州区', 50, 0),
(1344, 120, '丹徒区', 50, 0),
(1345, 120, '丹阳市', 50, 0),
(1346, 120, '扬中市', 50, 0),
(1347, 120, '句容市', 50, 0),
(1348, 120, '其它区', 60, 0),
(1349, 121, '海陵区', 50, 0),
(1350, 121, '高港区', 50, 0),
(1351, 121, '兴化市', 50, 0),
(1352, 121, '靖江市', 50, 0),
(1353, 121, '泰兴市', 50, 0),
(1354, 121, '姜堰区', 50, 0),
(1355, 121, '其它区', 60, 0),
(1356, 122, '宿城区', 50, 0),
(1357, 122, '宿豫区', 50, 0),
(1358, 122, '沭阳县', 50, 0),
(1359, 122, '泗阳县', 50, 0),
(1360, 122, '泗洪县', 50, 0),
(1361, 122, '其它区', 60, 0),
(1362, 123, '上城区', 50, 0),
(1363, 123, '下城区', 50, 0),
(1364, 123, '江干区', 50, 0),
(1365, 123, '拱墅区', 50, 0),
(1366, 123, '西湖区', 50, 0),
(1367, 123, '滨江区', 50, 0),
(1368, 123, '萧山区', 50, 0),
(1369, 123, '余杭区', 50, 0),
(1370, 123, '桐庐县', 50, 0),
(1371, 123, '淳安县', 50, 0),
(1372, 123, '建德市', 50, 0),
(1373, 123, '富阳区', 50, 0),
(1374, 123, '临安市', 50, 0),
(1375, 123, '其它区', 60, 0),
(1376, 124, '海曙区', 47, 0),
(1377, 124, '江东区', 48, 0),
(1378, 4044, '基于CareyShop商城框架系统', 50, 1),
(1379, 124, '北仑区', 50, 0),
(1380, 124, '镇海区', 50, 0),
(1381, 124, '鄞州区', 50, 0),
(1382, 124, '象山县', 50, 0),
(1383, 124, '宁海县', 50, 0),
(1384, 124, '余姚市', 50, 0),
(1385, 124, '慈溪市', 50, 0),
(1386, 124, '奉化市', 50, 0),
(1387, 124, '其它区', 60, 0),
(1388, 125, '鹿城区', 50, 0),
(1389, 125, '龙湾区', 50, 0),
(1390, 125, '瓯海区', 50, 0),
(1391, 125, '洞头县', 50, 0),
(1392, 125, '永嘉县', 50, 0),
(1393, 125, '平阳县', 50, 0),
(1394, 125, '苍南县', 50, 0),
(1395, 125, '文成县', 50, 0),
(1396, 125, '泰顺县', 50, 0),
(1397, 125, '瑞安市', 50, 0),
(1398, 125, '乐清市', 50, 0),
(1399, 125, '其它区', 60, 0),
(1400, 126, '南湖区', 50, 0),
(1401, 126, '秀洲区', 50, 0),
(1402, 126, '嘉善县', 50, 0),
(1403, 126, '海盐县', 50, 0),
(1404, 126, '海宁市', 50, 0),
(1405, 126, '平湖市', 50, 0),
(1406, 126, '桐乡市', 50, 0),
(1407, 126, '其它区', 60, 0),
(1408, 127, '吴兴区', 50, 0),
(1409, 127, '南浔区', 50, 0),
(1410, 127, '德清县', 50, 0),
(1411, 127, '长兴县', 50, 0),
(1412, 127, '安吉县', 50, 0),
(1413, 127, '其它区', 60, 0),
(1414, 128, '越城区', 50, 0),
(1415, 128, '柯桥区', 50, 0),
(1416, 128, '新昌县', 50, 0),
(1417, 128, '诸暨市', 50, 0),
(1418, 128, '上虞区', 50, 0),
(1419, 128, '嵊州市', 50, 0),
(1420, 128, '其它区', 60, 0),
(1421, 129, '婺城区', 50, 0),
(1422, 129, '金东区', 50, 0),
(1423, 129, '武义县', 50, 0),
(1424, 129, '浦江县', 50, 0),
(1425, 129, '磐安县', 50, 0),
(1426, 129, '兰溪市', 50, 0),
(1427, 129, '义乌市', 50, 0),
(1428, 129, '东阳市', 50, 0),
(1429, 129, '永康市', 50, 0),
(1430, 129, '其它区', 60, 0),
(1431, 130, '柯城区', 50, 0),
(1432, 130, '衢江区', 50, 0),
(1433, 130, '常山县', 50, 0),
(1434, 130, '开化县', 50, 0),
(1435, 130, '龙游县', 50, 0),
(1436, 130, '江山市', 50, 0),
(1437, 130, '其它区', 60, 0),
(1438, 131, '定海区', 50, 0),
(1439, 131, '普陀区', 50, 0),
(1440, 131, '岱山县', 50, 0),
(1441, 131, '嵊泗县', 50, 0),
(1442, 131, '其它区', 60, 0),
(1443, 132, '椒江区', 50, 0),
(1444, 132, '黄岩区', 50, 0),
(1445, 132, '路桥区', 50, 0),
(1446, 132, '玉环县', 50, 0),
(1447, 132, '三门县', 50, 0),
(1448, 132, '天台县', 50, 0),
(1449, 132, '仙居县', 50, 0),
(1450, 132, '温岭市', 50, 0),
(1451, 132, '临海市', 50, 0),
(1452, 132, '其它区', 60, 0),
(1453, 133, '莲都区', 50, 0),
(1454, 133, '青田县', 50, 0),
(1455, 133, '缙云县', 50, 0),
(1456, 133, '遂昌县', 50, 0),
(1457, 133, '松阳县', 50, 0),
(1458, 133, '云和县', 50, 0),
(1459, 133, '庆元县', 50, 0),
(1460, 133, '景宁畲族自治县', 50, 0),
(1461, 133, '龙泉市', 50, 0),
(1462, 133, '其它区', 60, 0),
(1463, 134, '瑶海区', 50, 0),
(1464, 134, '庐阳区', 50, 0),
(1465, 134, '蜀山区', 50, 0),
(1466, 134, '包河区', 50, 0),
(1467, 134, '长丰县', 50, 0),
(1468, 134, '肥东县', 50, 0),
(1469, 134, '肥西县', 50, 0),
(1470, 134, '高新区', 50, 0),
(1471, 134, '中区', 50, 0),
(1472, 134, '其它区', 60, 0),
(1473, 135, '镜湖区', 50, 0),
(1474, 135, '弋江区', 50, 0),
(1475, 135, '鸠江区', 50, 0),
(1476, 135, '三山区', 50, 0),
(1477, 135, '芜湖县', 50, 0),
(1478, 135, '繁昌县', 50, 0),
(1479, 135, '南陵县', 50, 0),
(1480, 135, '其它区', 60, 0),
(1481, 136, '龙子湖区', 50, 0),
(1482, 136, '蚌山区', 50, 0),
(1483, 136, '禹会区', 50, 0),
(1484, 136, '淮上区', 50, 0),
(1485, 136, '怀远县', 50, 0),
(1486, 136, '五河县', 50, 0),
(1487, 136, '固镇县', 50, 0),
(1488, 136, '其它区', 60, 0),
(1489, 137, '大通区', 50, 0),
(1490, 137, '田家庵区', 50, 0),
(1491, 137, '谢家集区', 50, 0),
(1492, 137, '八公山区', 50, 0),
(1493, 137, '潘集区', 50, 0),
(1494, 137, '凤台县', 50, 0),
(1495, 137, '其它区', 60, 0),
(1496, 138, '金家庄区', 50, 0),
(1497, 138, '花山区', 50, 0),
(1498, 138, '雨山区', 50, 0),
(1499, 138, '博望区', 50, 0),
(1500, 138, '当涂县', 50, 0),
(1501, 138, '其它区', 60, 0),
(1502, 139, '杜集区', 50, 0),
(1503, 139, '相山区', 50, 0),
(1504, 139, '烈山区', 50, 0),
(1505, 139, '濉溪县', 50, 0),
(1506, 139, '其它区', 60, 0),
(1507, 140, '铜官山区', 50, 0),
(1508, 140, '狮子山区', 50, 0),
(1509, 140, '郊区', 50, 0),
(1510, 140, '铜陵县', 50, 0),
(1511, 140, '其它区', 60, 0),
(1512, 141, '迎江区', 50, 0),
(1513, 141, '大观区', 50, 0),
(1514, 141, '宜秀区', 50, 0),
(1515, 141, '怀宁县', 50, 0),
(1516, 141, '枞阳县', 50, 0),
(1517, 141, '潜山县', 50, 0),
(1518, 141, '太湖县', 50, 0),
(1519, 141, '宿松县', 50, 0),
(1520, 141, '望江县', 50, 0),
(1521, 141, '岳西县', 50, 0),
(1522, 141, '桐城市', 50, 0),
(1523, 141, '其它区', 60, 0),
(1524, 142, '屯溪区', 50, 0),
(1525, 142, '黄山区', 50, 0),
(1526, 142, '徽州区', 50, 0),
(1527, 142, '歙县', 50, 0),
(1528, 142, '休宁县', 50, 0),
(1529, 142, '黟县', 50, 0),
(1530, 142, '祁门县', 50, 0),
(1531, 142, '其它区', 60, 0),
(1532, 143, '琅琊区', 50, 0),
(1533, 143, '南谯区', 50, 0),
(1534, 143, '来安县', 50, 0),
(1535, 143, '全椒县', 50, 0),
(1536, 143, '定远县', 50, 0),
(1537, 143, '凤阳县', 50, 0),
(1538, 143, '天长市', 50, 0),
(1539, 143, '明光市', 50, 0),
(1540, 143, '其它区', 60, 0),
(1541, 144, '颍州区', 50, 0),
(1542, 144, '颍东区', 50, 0),
(1543, 144, '颍泉区', 50, 0),
(1544, 144, '临泉县', 50, 0),
(1545, 144, '太和县', 50, 0),
(1546, 144, '阜南县', 50, 0),
(1547, 144, '颍上县', 50, 0),
(1548, 144, '界首市', 50, 0),
(1549, 144, '其它区', 60, 0),
(1550, 145, '埇桥区', 50, 0),
(1551, 145, '砀山县', 50, 0),
(1552, 145, '萧县', 50, 0),
(1553, 145, '灵璧县', 50, 0),
(1554, 145, '泗县', 50, 0),
(1555, 145, '其它区', 60, 0),
(1556, 134, '巢湖市', 50, 0),
(1557, 134, '居巢区', 50, 0),
(1558, 134, '庐江县', 50, 0),
(1559, 135, '无为县', 50, 0),
(1560, 138, '含山县', 50, 0),
(1561, 138, '和县', 50, 0),
(1562, 146, '金安区', 50, 0),
(1563, 146, '裕安区', 50, 0),
(1564, 146, '寿县', 50, 0),
(1565, 146, '霍邱县', 50, 0),
(1566, 146, '舒城县', 50, 0),
(1567, 146, '金寨县', 50, 0),
(1568, 146, '霍山县', 50, 0),
(1569, 146, '其它区', 60, 0),
(1570, 147, '谯城区', 50, 0),
(1571, 147, '涡阳县', 50, 0),
(1572, 147, '蒙城县', 50, 0),
(1573, 147, '利辛县', 50, 0),
(1574, 147, '其它区', 60, 0),
(1575, 148, '贵池区', 50, 0),
(1576, 148, '东至县', 50, 0),
(1577, 148, '石台县', 50, 0),
(1578, 148, '青阳县', 50, 0),
(1579, 148, '其它区', 60, 0),
(1580, 149, '宣州区', 50, 0),
(1581, 149, '郎溪县', 50, 0),
(1582, 149, '广德县', 50, 0),
(1583, 149, '泾县', 50, 0),
(1584, 149, '绩溪县', 50, 0),
(1585, 149, '旌德县', 50, 0),
(1586, 149, '宁国市', 50, 0),
(1587, 149, '其它区', 60, 0),
(1588, 150, '鼓楼区', 50, 0),
(1589, 150, '台江区', 50, 0),
(1590, 150, '仓山区', 50, 0),
(1591, 150, '马尾区', 50, 0),
(1592, 150, '晋安区', 50, 0),
(1593, 150, '闽侯县', 50, 0),
(1594, 150, '连江县', 50, 0),
(1595, 150, '罗源县', 50, 0),
(1596, 150, '闽清县', 50, 0),
(1597, 150, '永泰县', 50, 0),
(1598, 150, '平潭县', 50, 0),
(1599, 150, '福清市', 50, 0),
(1600, 150, '长乐市', 50, 0),
(1601, 150, '其它区', 60, 0),
(1602, 151, '思明区', 50, 0),
(1603, 151, '海沧区', 50, 0),
(1604, 151, '湖里区', 50, 0),
(1605, 151, '集美区', 50, 0),
(1606, 151, '同安区', 50, 0),
(1607, 151, '翔安区', 50, 0),
(1608, 151, '其它区', 60, 0),
(1609, 152, '城厢区', 50, 0),
(1610, 152, '涵江区', 50, 0),
(1611, 152, '荔城区', 50, 0),
(1612, 152, '秀屿区', 50, 0),
(1613, 152, '仙游县', 50, 0),
(1614, 152, '其它区', 60, 0),
(1615, 153, '梅列区', 50, 0),
(1616, 153, '三元区', 50, 0),
(1617, 153, '明溪县', 50, 0),
(1618, 153, '清流县', 50, 0),
(1619, 153, '宁化县', 50, 0),
(1620, 153, '大田县', 50, 0),
(1621, 153, '尤溪县', 50, 0),
(1622, 153, '沙县', 50, 0),
(1623, 153, '将乐县', 50, 0),
(1624, 153, '泰宁县', 50, 0),
(1625, 153, '建宁县', 50, 0),
(1626, 153, '永安市', 50, 0),
(1627, 153, '其它区', 60, 0),
(1628, 154, '鲤城区', 50, 0),
(1629, 154, '丰泽区', 50, 0),
(1630, 154, '洛江区', 50, 0),
(1631, 154, '泉港区', 50, 0),
(1632, 154, '惠安县', 50, 0),
(1633, 154, '安溪县', 50, 0),
(1634, 154, '永春县', 50, 0),
(1635, 154, '德化县', 50, 0),
(1636, 154, '金门县', 50, 0),
(1637, 154, '石狮市', 50, 0),
(1638, 154, '晋江市', 50, 0),
(1639, 154, '南安市', 50, 0),
(1640, 154, '其它区', 60, 0),
(1641, 155, '芗城区', 50, 0),
(1642, 155, '龙文区', 50, 0),
(1643, 155, '云霄县', 50, 0),
(1644, 155, '漳浦县', 50, 0),
(1645, 155, '诏安县', 50, 0),
(1646, 155, '长泰县', 50, 0),
(1647, 155, '东山县', 50, 0),
(1648, 155, '南靖县', 50, 0),
(1649, 155, '平和县', 50, 0),
(1650, 155, '华安县', 50, 0),
(1651, 155, '龙海市', 50, 0),
(1652, 155, '其它区', 60, 0),
(1653, 156, '延平区', 50, 0),
(1654, 156, '顺昌县', 50, 0),
(1655, 156, '浦城县', 50, 0),
(1656, 156, '光泽县', 50, 0),
(1657, 156, '松溪县', 50, 0),
(1658, 156, '政和县', 50, 0),
(1659, 156, '邵武市', 50, 0),
(1660, 156, '武夷山市', 50, 0),
(1661, 156, '建瓯市', 50, 0),
(1662, 156, '建阳区', 50, 0),
(1663, 156, '其它区', 60, 0),
(1664, 157, '新罗区', 50, 0),
(1665, 157, '长汀县', 50, 0),
(1666, 157, '永定区', 50, 0),
(1667, 157, '上杭县', 50, 0),
(1668, 157, '武平县', 50, 0),
(1669, 157, '连城县', 50, 0),
(1670, 157, '漳平市', 50, 0),
(1671, 157, '其它区', 60, 0),
(1672, 158, '蕉城区', 50, 0),
(1673, 158, '霞浦县', 50, 0),
(1674, 158, '古田县', 50, 0),
(1675, 158, '屏南县', 50, 0),
(1676, 158, '寿宁县', 50, 0),
(1677, 158, '周宁县', 50, 0),
(1678, 158, '柘荣县', 50, 0),
(1679, 158, '福安市', 50, 0),
(1680, 158, '福鼎市', 50, 0),
(1681, 158, '其它区', 60, 0),
(1682, 159, '东湖区', 50, 0),
(1683, 159, '西湖区', 50, 0),
(1684, 159, '青云谱区', 50, 0),
(1685, 159, '湾里区', 50, 0),
(1686, 159, '青山湖区', 50, 0),
(1687, 159, '南昌县', 50, 0),
(1688, 159, '新建县', 50, 0),
(1689, 159, '安义县', 50, 0),
(1690, 159, '进贤县', 50, 0),
(1691, 159, '红谷滩新区', 50, 0),
(1692, 159, '经济技术开发区', 50, 0),
(1693, 159, '昌北区', 50, 0),
(1694, 159, '其它区', 60, 0),
(1695, 160, '昌江区', 50, 0),
(1696, 160, '珠山区', 50, 0),
(1697, 160, '浮梁县', 50, 0),
(1698, 160, '乐平市', 50, 0),
(1699, 160, '其它区', 60, 0),
(1700, 161, '安源区', 50, 0),
(1701, 161, '湘东区', 50, 0),
(1702, 161, '莲花县', 50, 0),
(1703, 161, '上栗县', 50, 0),
(1704, 161, '芦溪县', 50, 0),
(1705, 161, '其它区', 60, 0),
(1706, 162, '庐山区', 50, 0),
(1707, 162, '浔阳区', 50, 0),
(1708, 162, '九江县', 50, 0),
(1709, 162, '武宁县', 50, 0),
(1710, 162, '修水县', 50, 0),
(1711, 162, '永修县', 50, 0),
(1712, 162, '德安县', 50, 0),
(1713, 162, '星子县', 50, 0),
(1714, 162, '都昌县', 50, 0),
(1715, 162, '湖口县', 50, 0),
(1716, 162, '彭泽县', 50, 0),
(1717, 162, '瑞昌市', 50, 0),
(1718, 162, '其它区', 60, 0),
(1719, 162, '共青城市', 50, 0),
(1720, 163, '渝水区', 50, 0),
(1721, 163, '分宜县', 50, 0),
(1722, 163, '其它区', 60, 0),
(1723, 164, '月湖区', 50, 0),
(1724, 164, '余江县', 50, 0),
(1725, 164, '贵溪市', 50, 0),
(1726, 164, '其它区', 60, 0),
(1727, 165, '章贡区', 50, 0),
(1728, 165, '赣县', 50, 0),
(1729, 165, '信丰县', 50, 0),
(1730, 165, '大余县', 50, 0),
(1731, 165, '上犹县', 50, 0),
(1732, 165, '崇义县', 50, 0),
(1733, 165, '安远县', 50, 0),
(1734, 165, '龙南县', 50, 0),
(1735, 165, '定南县', 50, 0),
(1736, 165, '全南县', 50, 0),
(1737, 165, '宁都县', 50, 0),
(1738, 165, '于都县', 50, 0),
(1739, 165, '兴国县', 50, 0),
(1740, 165, '会昌县', 50, 0),
(1741, 165, '寻乌县', 50, 0),
(1742, 165, '石城县', 50, 0),
(1743, 165, '黄金区', 50, 0),
(1744, 165, '瑞金市', 50, 0),
(1745, 165, '南康区', 50, 0),
(1746, 165, '其它区', 60, 0),
(1747, 166, '吉州区', 50, 0),
(1748, 166, '青原区', 50, 0),
(1749, 166, '吉安县', 50, 0),
(1750, 166, '吉水县', 50, 0),
(1751, 166, '峡江县', 50, 0),
(1752, 166, '新干县', 50, 0),
(1753, 166, '永丰县', 50, 0),
(1754, 166, '泰和县', 50, 0),
(1755, 166, '遂川县', 50, 0),
(1756, 166, '万安县', 50, 0),
(1757, 166, '安福县', 50, 0),
(1758, 166, '永新县', 50, 0),
(1759, 166, '井冈山市', 50, 0),
(1760, 166, '其它区', 60, 0),
(1761, 167, '袁州区', 50, 0),
(1762, 167, '奉新县', 50, 0),
(1763, 167, '万载县', 50, 0),
(1764, 167, '上高县', 50, 0),
(1765, 167, '宜丰县', 50, 0),
(1766, 167, '靖安县', 50, 0),
(1767, 167, '铜鼓县', 50, 0),
(1768, 167, '丰城市', 50, 0),
(1769, 167, '樟树市', 50, 0),
(1770, 167, '高安市', 50, 0),
(1771, 167, '其它区', 60, 0),
(1772, 168, '临川区', 50, 0),
(1773, 168, '南城县', 50, 0),
(1774, 168, '黎川县', 50, 0),
(1775, 168, '南丰县', 50, 0),
(1776, 168, '崇仁县', 50, 0),
(1777, 168, '乐安县', 50, 0),
(1778, 168, '宜黄县', 50, 0),
(1779, 168, '金溪县', 50, 0),
(1780, 168, '资溪县', 50, 0),
(1781, 168, '东乡县', 50, 0),
(1782, 168, '广昌县', 50, 0),
(1783, 168, '其它区', 60, 0),
(1784, 169, '信州区', 50, 0),
(1785, 169, '上饶县', 50, 0),
(1786, 169, '广丰区', 50, 0),
(1787, 169, '玉山县', 50, 0),
(1788, 169, '铅山县', 50, 0),
(1789, 169, '横峰县', 50, 0),
(1790, 169, '弋阳县', 50, 0),
(1791, 169, '余干县', 50, 0),
(1792, 169, '鄱阳县', 50, 0),
(1793, 169, '万年县', 50, 0),
(1794, 169, '婺源县', 50, 0),
(1795, 169, '德兴市', 50, 0),
(1796, 169, '其它区', 60, 0),
(1797, 170, '历下区', 50, 0),
(1798, 170, '市中区', 50, 0),
(1799, 170, '槐荫区', 50, 0),
(1800, 170, '天桥区', 50, 0),
(1801, 170, '历城区', 50, 0),
(1802, 170, '长清区', 50, 0),
(1803, 170, '平阴县', 50, 0),
(1804, 170, '济阳县', 50, 0),
(1805, 170, '商河县', 50, 0),
(1806, 170, '章丘市', 50, 0),
(1807, 170, '其它区', 60, 0),
(1808, 171, '市南区', 50, 0),
(1809, 171, '市北区', 50, 0),
(1810, 171, '四方区', 50, 0),
(1811, 171, '黄岛区', 50, 0),
(1812, 171, '崂山区', 50, 0),
(1813, 171, '李沧区', 50, 0),
(1814, 171, '城阳区', 50, 0),
(1815, 171, '开发区', 50, 0),
(1816, 171, '胶州市', 50, 0),
(1817, 171, '即墨市', 50, 0),
(1818, 171, '平度市', 50, 0),
(1819, 171, '胶南市', 50, 0),
(1820, 171, '莱西市', 50, 0),
(1821, 171, '其它区', 60, 0),
(1822, 172, '淄川区', 50, 0),
(1823, 172, '张店区', 50, 0),
(1824, 172, '博山区', 50, 0),
(1825, 172, '临淄区', 50, 0),
(1826, 172, '周村区', 50, 0),
(1827, 172, '桓台县', 50, 0),
(1828, 172, '高青县', 50, 0),
(1829, 172, '沂源县', 50, 0),
(1830, 172, '其它区', 60, 0),
(1831, 173, '市中区', 50, 0),
(1832, 173, '薛城区', 50, 0),
(1833, 173, '峄城区', 50, 0),
(1834, 173, '台儿庄区', 50, 0),
(1835, 173, '山亭区', 50, 0),
(1836, 173, '滕州市', 50, 0),
(1837, 173, '其它区', 60, 0),
(1838, 174, '东营区', 50, 0),
(1839, 174, '河口区', 50, 0),
(1840, 174, '垦利县', 50, 0),
(1841, 174, '利津县', 50, 0),
(1842, 174, '广饶县', 50, 0),
(1843, 174, '西城区', 50, 0),
(1844, 174, '东城区', 50, 0),
(1845, 174, '其它区', 60, 0),
(1846, 175, '芝罘区', 50, 0),
(1847, 175, '福山区', 50, 0),
(1848, 175, '牟平区', 50, 0),
(1849, 175, '莱山区', 50, 0),
(1850, 175, '长岛县', 50, 0),
(1851, 175, '龙口市', 50, 0),
(1852, 175, '莱阳市', 50, 0),
(1853, 175, '莱州市', 50, 0),
(1854, 175, '蓬莱市', 50, 0),
(1855, 175, '招远市', 50, 0),
(1856, 175, '栖霞市', 50, 0),
(1857, 175, '海阳市', 50, 0),
(1858, 175, '其它区', 60, 0),
(1859, 176, '潍城区', 50, 0),
(1860, 176, '寒亭区', 50, 0),
(1861, 176, '坊子区', 50, 0),
(1862, 176, '奎文区', 50, 0),
(1863, 176, '临朐县', 50, 0),
(1864, 176, '昌乐县', 50, 0),
(1865, 176, '开发区', 50, 0),
(1866, 176, '青州市', 50, 0),
(1867, 176, '诸城市', 50, 0),
(1868, 176, '寿光市', 50, 0),
(1869, 176, '安丘市', 50, 0),
(1870, 176, '高密市', 50, 0),
(1871, 176, '昌邑市', 50, 0),
(1872, 176, '其它区', 60, 0),
(1873, 177, '市中区', 50, 0),
(1874, 177, '任城区', 50, 0),
(1875, 177, '微山县', 50, 0),
(1876, 177, '鱼台县', 50, 0),
(1877, 177, '金乡县', 50, 0),
(1878, 177, '嘉祥县', 50, 0),
(1879, 177, '汶上县', 50, 0),
(1880, 177, '泗水县', 50, 0),
(1881, 177, '梁山县', 50, 0),
(1882, 177, '曲阜市', 50, 0),
(1883, 177, '兖州区', 50, 0),
(1884, 177, '邹城市', 50, 0),
(1885, 177, '其它区', 60, 0),
(1886, 178, '泰山区', 50, 0),
(1887, 178, '岱岳区', 50, 0),
(1888, 178, '宁阳县', 50, 0),
(1889, 178, '东平县', 50, 0),
(1890, 178, '新泰市', 50, 0),
(1891, 178, '肥城市', 50, 0),
(1892, 178, '其它区', 60, 0),
(1893, 179, '环翠区', 50, 0),
(1894, 179, '文登区', 50, 0),
(1895, 179, '荣成市', 50, 0),
(1896, 179, '乳山市', 50, 0),
(1897, 179, '其它区', 60, 0),
(1898, 180, '东港区', 50, 0),
(1899, 180, '岚山区', 50, 0),
(1900, 180, '五莲县', 50, 0),
(1901, 180, '莒县', 50, 0),
(1902, 180, '其它区', 60, 0),
(1903, 181, '莱城区', 50, 0),
(1904, 181, '钢城区', 50, 0),
(1905, 181, '其它区', 60, 0),
(1906, 182, '兰山区', 50, 0),
(1907, 182, '罗庄区', 50, 0),
(1908, 182, '河东区', 50, 0),
(1909, 182, '沂南县', 50, 0),
(1910, 182, '郯城县', 50, 0),
(1911, 182, '沂水县', 50, 0),
(1912, 182, '兰陵县', 50, 0),
(1913, 182, '费县', 50, 0),
(1914, 182, '平邑县', 50, 0),
(1915, 182, '莒南县', 50, 0),
(1916, 182, '蒙阴县', 50, 0),
(1917, 182, '临沭县', 50, 0),
(1918, 182, '其它区', 60, 0),
(1919, 183, '德城区', 50, 0),
(1920, 183, '陵城区', 50, 0),
(1921, 183, '宁津县', 50, 0),
(1922, 183, '庆云县', 50, 0),
(1923, 183, '临邑县', 50, 0),
(1924, 183, '齐河县', 50, 0),
(1925, 183, '平原县', 50, 0),
(1926, 183, '夏津县', 50, 0),
(1927, 183, '武城县', 50, 0),
(1928, 183, '开发区', 50, 0),
(1929, 183, '乐陵市', 50, 0),
(1930, 183, '禹城市', 50, 0),
(1931, 183, '其它区', 60, 0),
(1932, 184, '东昌府区', 50, 0),
(1933, 184, '阳谷县', 50, 0),
(1934, 184, '莘县', 50, 0),
(1935, 184, '茌平县', 50, 0),
(1936, 184, '东阿县', 50, 0),
(1937, 184, '冠县', 50, 0),
(1938, 184, '高唐县', 50, 0),
(1939, 184, '临清市', 50, 0),
(1940, 184, '其它区', 60, 0),
(1941, 185, '滨城区', 50, 0),
(1942, 185, '惠民县', 50, 0),
(1943, 185, '阳信县', 50, 0),
(1944, 185, '无棣县', 50, 0),
(1945, 185, '沾化区', 50, 0),
(1946, 185, '博兴县', 50, 0),
(1947, 185, '邹平县', 50, 0),
(1948, 185, '其它区', 60, 0),
(1949, 186, '牡丹区', 50, 0),
(1950, 186, '曹县', 50, 0),
(1951, 186, '单县', 50, 0),
(1952, 186, '成武县', 50, 0),
(1953, 186, '巨野县', 50, 0),
(1954, 186, '郓城县', 50, 0),
(1955, 186, '鄄城县', 50, 0),
(1956, 186, '定陶县', 50, 0),
(1957, 186, '东明县', 50, 0),
(1958, 186, '其它区', 60, 0),
(1959, 187, '中原区', 50, 0),
(1960, 187, '二七区', 50, 0),
(1961, 187, '管城回族区', 50, 0),
(1962, 187, '金水区', 50, 0),
(1963, 187, '上街区', 50, 0),
(1964, 187, '惠济区', 50, 0),
(1965, 187, '中牟县', 50, 0),
(1966, 187, '巩义市', 50, 0),
(1967, 187, '荥阳市', 50, 0),
(1968, 187, '新密市', 50, 0),
(1969, 187, '新郑市', 50, 0),
(1970, 187, '登封市', 50, 0),
(1971, 187, '郑东新区', 50, 0),
(1972, 187, '高新区', 50, 0),
(1973, 187, '其它区', 60, 0),
(1974, 188, '龙亭区', 50, 0),
(1975, 188, '顺河回族区', 50, 0),
(1976, 188, '鼓楼区', 50, 0),
(1977, 188, '禹王台区', 50, 0),
(1978, 188, '金明区', 50, 0),
(1979, 188, '杞县', 50, 0),
(1980, 188, '通许县', 50, 0),
(1981, 188, '尉氏县', 50, 0),
(1982, 188, '祥符区', 50, 0),
(1983, 188, '兰考县', 50, 0),
(1984, 188, '其它区', 60, 0),
(1985, 189, '老城区', 50, 0),
(1986, 189, '西工区', 50, 0),
(1987, 189, '瀍河回族区', 50, 0),
(1988, 189, '涧西区', 50, 0),
(1989, 189, '吉利区', 50, 0),
(1990, 189, '洛龙区', 50, 0),
(1991, 189, '孟津县', 50, 0),
(1992, 189, '新安县', 50, 0),
(1993, 189, '栾川县', 50, 0),
(1994, 189, '嵩县', 50, 0),
(1995, 189, '汝阳县', 50, 0),
(1996, 189, '宜阳县', 50, 0),
(1997, 189, '洛宁县', 50, 0),
(1998, 189, '伊川县', 50, 0),
(1999, 189, '偃师市', 50, 0),
(2000, 190, '新华区', 50, 0),
(2001, 190, '卫东区', 50, 0),
(2002, 190, '石龙区', 50, 0),
(2003, 190, '湛河区', 50, 0),
(2004, 190, '宝丰县', 50, 0),
(2005, 190, '叶县', 50, 0),
(2006, 190, '鲁山县', 50, 0),
(2007, 190, '郏县', 50, 0),
(2008, 190, '舞钢市', 50, 0),
(2009, 190, '汝州市', 50, 0),
(2010, 190, '其它区', 60, 0),
(2011, 191, '文峰区', 50, 0),
(2012, 191, '北关区', 50, 0),
(2013, 191, '殷都区', 50, 0),
(2014, 191, '龙安区', 50, 0),
(2015, 191, '安阳县', 50, 0),
(2016, 191, '汤阴县', 50, 0),
(2017, 191, '滑县', 50, 0),
(2018, 191, '内黄县', 50, 0),
(2019, 191, '林州市', 50, 0),
(2020, 191, '其它区', 60, 0),
(2021, 192, '鹤山区', 50, 0),
(2022, 192, '山城区', 50, 0),
(2023, 192, '淇滨区', 50, 0),
(2024, 192, '浚县', 50, 0),
(2025, 192, '淇县', 50, 0),
(2026, 192, '其它区', 60, 0),
(2027, 193, '红旗区', 50, 0),
(2028, 193, '卫滨区', 50, 0),
(2029, 193, '凤泉区', 50, 0),
(2030, 193, '牧野区', 50, 0),
(2031, 193, '新乡县', 50, 0),
(2032, 193, '获嘉县', 50, 0),
(2033, 193, '原阳县', 50, 0),
(2034, 193, '延津县', 50, 0),
(2035, 193, '封丘县', 50, 0),
(2036, 193, '长垣县', 50, 0),
(2037, 193, '卫辉市', 50, 0),
(2038, 193, '辉县市', 50, 0),
(2039, 193, '其它区', 60, 0),
(2040, 194, '解放区', 50, 0),
(2041, 194, '中站区', 50, 0),
(2042, 194, '马村区', 50, 0),
(2043, 194, '山阳区', 50, 0),
(2044, 194, '修武县', 50, 0),
(2045, 194, '博爱县', 50, 0),
(2046, 194, '武陟县', 50, 0),
(2047, 194, '温县', 50, 0),
(2048, 194, '沁阳市', 50, 0),
(2049, 194, '孟州市', 50, 0),
(2050, 194, '其它区', 60, 0),
(2051, 195, '华龙区', 50, 0),
(2052, 195, '清丰县', 50, 0),
(2053, 195, '南乐县', 50, 0),
(2054, 195, '范县', 50, 0),
(2055, 195, '台前县', 50, 0),
(2056, 195, '濮阳县', 50, 0),
(2057, 195, '其它区', 60, 0),
(2058, 196, '魏都区', 50, 0),
(2059, 196, '许昌县', 50, 0),
(2060, 196, '鄢陵县', 50, 0),
(2061, 196, '襄城县', 50, 0),
(2062, 196, '禹州市', 50, 0),
(2063, 196, '长葛市', 50, 0),
(2064, 196, '其它区', 60, 0),
(2065, 197, '源汇区', 50, 0),
(2066, 197, '郾城区', 50, 0),
(2067, 197, '召陵区', 50, 0),
(2068, 197, '舞阳县', 50, 0),
(2069, 197, '临颍县', 50, 0),
(2070, 197, '其它区', 60, 0),
(2071, 198, '湖滨区', 50, 0),
(2072, 198, '渑池县', 50, 0);
INSERT INTO `cs_region` (`region_id`, `parent_id`, `region_name`, `sort`, `is_delete`) VALUES
(2073, 198, '陕州区', 50, 0),
(2074, 198, '卢氏县', 50, 0),
(2075, 198, '义马市', 50, 0),
(2076, 198, '灵宝市', 50, 0),
(2077, 198, '其它区', 60, 0),
(2078, 199, '宛城区', 50, 0),
(2079, 199, '卧龙区', 50, 0),
(2080, 199, '南召县', 50, 0),
(2081, 199, '方城县', 50, 0),
(2082, 199, '西峡县', 50, 0),
(2083, 199, '镇平县', 50, 0),
(2084, 199, '内乡县', 50, 0),
(2085, 199, '淅川县', 50, 0),
(2086, 199, '社旗县', 50, 0),
(2087, 199, '唐河县', 50, 0),
(2088, 199, '新野县', 50, 0),
(2089, 199, '桐柏县', 50, 0),
(2090, 199, '邓州市', 50, 0),
(2091, 199, '其它区', 60, 0),
(2092, 200, '梁园区', 50, 0),
(2093, 200, '睢阳区', 50, 0),
(2094, 200, '民权县', 50, 0),
(2095, 200, '睢县', 50, 0),
(2096, 200, '宁陵县', 50, 0),
(2097, 200, '柘城县', 50, 0),
(2098, 200, '虞城县', 50, 0),
(2099, 200, '夏邑县', 50, 0),
(2100, 200, '永城市', 50, 0),
(2101, 200, '其它区', 60, 0),
(2102, 201, '浉河区', 50, 0),
(2103, 201, '平桥区', 50, 0),
(2104, 201, '罗山县', 50, 0),
(2105, 201, '光山县', 50, 0),
(2106, 201, '新县', 50, 0),
(2107, 201, '商城县', 50, 0),
(2108, 201, '固始县', 50, 0),
(2109, 201, '潢川县', 50, 0),
(2110, 201, '淮滨县', 50, 0),
(2111, 201, '息县', 50, 0),
(2112, 201, '其它区', 60, 0),
(2113, 202, '川汇区', 50, 0),
(2114, 202, '扶沟县', 50, 0),
(2115, 202, '西华县', 50, 0),
(2116, 202, '商水县', 50, 0),
(2117, 202, '沈丘县', 50, 0),
(2118, 202, '郸城县', 50, 0),
(2119, 202, '淮阳县', 50, 0),
(2120, 202, '太康县', 50, 0),
(2121, 202, '鹿邑县', 50, 0),
(2122, 202, '项城市', 50, 0),
(2123, 202, '其它区', 60, 0),
(2124, 203, '驿城区', 50, 0),
(2125, 203, '西平县', 50, 0),
(2126, 203, '上蔡县', 50, 0),
(2127, 203, '平舆县', 50, 0),
(2128, 203, '正阳县', 50, 0),
(2129, 203, '确山县', 50, 0),
(2130, 203, '泌阳县', 50, 0),
(2131, 203, '汝南县', 50, 0),
(2132, 203, '遂平县', 50, 0),
(2133, 203, '新蔡县', 50, 0),
(2134, 203, '其它区', 60, 0),
(2135, 204, '江岸区', 50, 0),
(2136, 204, '江汉区', 50, 0),
(2137, 204, '硚口区', 50, 0),
(2138, 204, '汉阳区', 50, 0),
(2139, 204, '武昌区', 50, 0),
(2140, 204, '青山区', 50, 0),
(2141, 204, '洪山区', 50, 0),
(2142, 204, '东西湖区', 50, 0),
(2143, 204, '汉南区', 50, 0),
(2144, 204, '蔡甸区', 50, 0),
(2145, 204, '江夏区', 50, 0),
(2146, 204, '黄陂区', 50, 0),
(2147, 204, '新洲区', 50, 0),
(2148, 204, '其它区', 60, 0),
(2149, 205, '黄石港区', 50, 0),
(2150, 205, '西塞山区', 50, 0),
(2151, 205, '下陆区', 50, 0),
(2152, 205, '铁山区', 50, 0),
(2153, 205, '阳新县', 50, 0),
(2154, 205, '大冶市', 50, 0),
(2155, 205, '其它区', 60, 0),
(2156, 206, '茅箭区', 50, 0),
(2157, 206, '张湾区', 50, 0),
(2158, 206, '郧阳区', 50, 0),
(2159, 206, '郧西县', 50, 0),
(2160, 206, '竹山县', 50, 0),
(2161, 206, '竹溪县', 50, 0),
(2162, 206, '房县', 50, 0),
(2163, 206, '丹江口市', 50, 0),
(2164, 206, '城区', 50, 0),
(2165, 206, '其它区', 60, 0),
(2166, 207, '西陵区', 50, 0),
(2167, 207, '伍家岗区', 50, 0),
(2168, 207, '点军区', 50, 0),
(2169, 207, '猇亭区', 50, 0),
(2170, 207, '夷陵区', 50, 0),
(2171, 207, '远安县', 50, 0),
(2172, 207, '兴山县', 50, 0),
(2173, 207, '秭归县', 50, 0),
(2174, 207, '长阳土家族自治县', 50, 0),
(2175, 207, '五峰土家族自治县', 50, 0),
(2176, 207, '葛洲坝区', 50, 0),
(2177, 207, '开发区', 50, 0),
(2178, 207, '宜都市', 50, 0),
(2179, 207, '当阳市', 50, 0),
(2180, 207, '枝江市', 50, 0),
(2181, 207, '其它区', 60, 0),
(2182, 208, '襄城区', 50, 0),
(2183, 208, '樊城区', 50, 0),
(2184, 208, '襄州区', 50, 0),
(2185, 208, '南漳县', 50, 0),
(2186, 208, '谷城县', 50, 0),
(2187, 208, '保康县', 50, 0),
(2188, 208, '老河口市', 50, 0),
(2189, 208, '枣阳市', 50, 0),
(2190, 208, '宜城市', 50, 0),
(2191, 208, '其它区', 60, 0),
(2192, 209, '梁子湖区', 50, 0),
(2193, 209, '华容区', 50, 0),
(2194, 209, '鄂城区', 50, 0),
(2195, 209, '其它区', 60, 0),
(2196, 210, '东宝区', 50, 0),
(2197, 210, '掇刀区', 50, 0),
(2198, 210, '京山县', 50, 0),
(2199, 210, '沙洋县', 50, 0),
(2200, 210, '钟祥市', 50, 0),
(2201, 210, '其它区', 60, 0),
(2202, 211, '孝南区', 50, 0),
(2203, 211, '孝昌县', 50, 0),
(2204, 211, '大悟县', 50, 0),
(2205, 211, '云梦县', 50, 0),
(2206, 211, '应城市', 50, 0),
(2207, 211, '安陆市', 50, 0),
(2208, 211, '汉川市', 50, 0),
(2209, 211, '其它区', 60, 0),
(2210, 212, '沙市区', 50, 0),
(2211, 212, '荆州区', 50, 0),
(2212, 212, '公安县', 50, 0),
(2213, 212, '监利县', 50, 0),
(2214, 212, '江陵县', 50, 0),
(2215, 212, '石首市', 50, 0),
(2216, 212, '洪湖市', 50, 0),
(2217, 212, '松滋市', 50, 0),
(2218, 212, '其它区', 60, 0),
(2219, 213, '黄州区', 50, 0),
(2220, 213, '团风县', 50, 0),
(2221, 213, '红安县', 50, 0),
(2222, 213, '罗田县', 50, 0),
(2223, 213, '英山县', 50, 0),
(2224, 213, '浠水县', 50, 0),
(2225, 213, '蕲春县', 50, 0),
(2226, 213, '黄梅县', 50, 0),
(2227, 213, '麻城市', 50, 0),
(2228, 213, '武穴市', 50, 0),
(2229, 213, '其它区', 60, 0),
(2230, 214, '咸安区', 50, 0),
(2231, 214, '嘉鱼县', 50, 0),
(2232, 214, '通城县', 50, 0),
(2233, 214, '崇阳县', 50, 0),
(2234, 214, '通山县', 50, 0),
(2235, 214, '赤壁市', 50, 0),
(2236, 214, '温泉城区', 50, 0),
(2237, 214, '其它区', 60, 0),
(2238, 215, '曾都区', 50, 0),
(2239, 215, '随县', 50, 0),
(2240, 215, '广水市', 50, 0),
(2241, 215, '其它区', 60, 0),
(2242, 216, '恩施市', 50, 0),
(2243, 216, '利川市', 50, 0),
(2244, 216, '建始县', 50, 0),
(2245, 216, '巴东县', 50, 0),
(2246, 216, '宣恩县', 50, 0),
(2247, 216, '咸丰县', 50, 0),
(2248, 216, '来凤县', 50, 0),
(2249, 216, '鹤峰县', 50, 0),
(2250, 216, '其它区', 60, 0),
(2251, 217, '芙蓉区', 50, 0),
(2252, 217, '天心区', 50, 0),
(2253, 217, '岳麓区', 50, 0),
(2254, 217, '开福区', 50, 0),
(2255, 217, '雨花区', 50, 0),
(2256, 217, '长沙县', 50, 0),
(2257, 217, '望城区', 50, 0),
(2258, 217, '宁乡县', 50, 0),
(2259, 217, '浏阳市', 50, 0),
(2260, 217, '其它区', 60, 0),
(2261, 218, '荷塘区', 50, 0),
(2262, 218, '芦淞区', 50, 0),
(2263, 218, '石峰区', 50, 0),
(2264, 218, '天元区', 50, 0),
(2265, 218, '株洲县', 50, 0),
(2266, 218, '攸县', 50, 0),
(2267, 218, '茶陵县', 50, 0),
(2268, 218, '炎陵县', 50, 0),
(2269, 218, '醴陵市', 50, 0),
(2270, 218, '其它区', 60, 0),
(2271, 219, '雨湖区', 50, 0),
(2272, 219, '岳塘区', 50, 0),
(2273, 219, '湘潭县', 50, 0),
(2274, 219, '湘乡市', 50, 0),
(2275, 219, '韶山市', 50, 0),
(2276, 219, '其它区', 60, 0),
(2277, 220, '珠晖区', 50, 0),
(2278, 220, '雁峰区', 50, 0),
(2279, 220, '石鼓区', 50, 0),
(2280, 220, '蒸湘区', 50, 0),
(2281, 220, '南岳区', 50, 0),
(2282, 220, '衡阳县', 50, 0),
(2283, 220, '衡南县', 50, 0),
(2284, 220, '衡山县', 50, 0),
(2285, 220, '衡东县', 50, 0),
(2286, 220, '祁东县', 50, 0),
(2287, 220, '耒阳市', 50, 0),
(2288, 220, '常宁市', 50, 0),
(2289, 220, '其它区', 60, 0),
(2290, 221, '双清区', 50, 0),
(2291, 221, '大祥区', 50, 0),
(2292, 221, '北塔区', 50, 0),
(2293, 221, '邵东县', 50, 0),
(2294, 221, '新邵县', 50, 0),
(2295, 221, '邵阳县', 50, 0),
(2296, 221, '隆回县', 50, 0),
(2297, 221, '洞口县', 50, 0),
(2298, 221, '绥宁县', 50, 0),
(2299, 221, '新宁县', 50, 0),
(2300, 221, '城步苗族自治县', 50, 0),
(2301, 221, '武冈市', 50, 0),
(2302, 221, '其它区', 60, 0),
(2303, 222, '岳阳楼区', 50, 0),
(2304, 222, '云溪区', 50, 0),
(2305, 222, '君山区', 50, 0),
(2306, 222, '岳阳县', 50, 0),
(2307, 222, '华容县', 50, 0),
(2308, 222, '湘阴县', 50, 0),
(2309, 222, '平江县', 50, 0),
(2310, 222, '汨罗市', 50, 0),
(2311, 222, '临湘市', 50, 0),
(2312, 222, '其它区', 60, 0),
(2313, 223, '武陵区', 50, 0),
(2314, 223, '鼎城区', 50, 0),
(2315, 223, '安乡县', 50, 0),
(2316, 223, '汉寿县', 50, 0),
(2317, 223, '澧县', 50, 0),
(2318, 223, '临澧县', 50, 0),
(2319, 223, '桃源县', 50, 0),
(2320, 223, '石门县', 50, 0),
(2321, 223, '津市市', 50, 0),
(2322, 223, '其它区', 60, 0),
(2323, 224, '永定区', 50, 0),
(2324, 224, '武陵源区', 50, 0),
(2325, 224, '慈利县', 50, 0),
(2326, 224, '桑植县', 50, 0),
(2327, 224, '其它区', 60, 0),
(2328, 225, '资阳区', 50, 0),
(2329, 225, '赫山区', 50, 0),
(2330, 225, '南县', 50, 0),
(2331, 225, '桃江县', 50, 0),
(2332, 225, '安化县', 50, 0),
(2333, 225, '沅江市', 50, 0),
(2334, 225, '其它区', 60, 0),
(2335, 226, '北湖区', 50, 0),
(2336, 226, '苏仙区', 50, 0),
(2337, 226, '桂阳县', 50, 0),
(2338, 226, '宜章县', 50, 0),
(2339, 226, '永兴县', 50, 0),
(2340, 226, '嘉禾县', 50, 0),
(2341, 226, '临武县', 50, 0),
(2342, 226, '汝城县', 50, 0),
(2343, 226, '桂东县', 50, 0),
(2344, 226, '安仁县', 50, 0),
(2345, 226, '资兴市', 50, 0),
(2346, 226, '其它区', 60, 0),
(2347, 227, '零陵区', 50, 0),
(2348, 227, '冷水滩区', 50, 0),
(2349, 227, '祁阳县', 50, 0),
(2350, 227, '东安县', 50, 0),
(2351, 227, '双牌县', 50, 0),
(2352, 227, '道县', 50, 0),
(2353, 227, '江永县', 50, 0),
(2354, 227, '宁远县', 50, 0),
(2355, 227, '蓝山县', 50, 0),
(2356, 227, '新田县', 50, 0),
(2357, 227, '江华瑶族自治县', 50, 0),
(2358, 227, '其它区', 60, 0),
(2359, 228, '鹤城区', 50, 0),
(2360, 228, '中方县', 50, 0),
(2361, 228, '沅陵县', 50, 0),
(2362, 228, '辰溪县', 50, 0),
(2363, 228, '溆浦县', 50, 0),
(2364, 228, '会同县', 50, 0),
(2365, 228, '麻阳苗族自治县', 50, 0),
(2366, 228, '新晃侗族自治县', 50, 0),
(2367, 228, '芷江侗族自治县', 50, 0),
(2368, 228, '靖州苗族侗族自治县', 50, 0),
(2369, 228, '通道侗族自治县', 50, 0),
(2370, 228, '洪江市', 50, 0),
(2371, 228, '其它区', 60, 0),
(2372, 229, '娄星区', 50, 0),
(2373, 229, '双峰县', 50, 0),
(2374, 229, '新化县', 50, 0),
(2375, 229, '冷水江市', 50, 0),
(2376, 229, '涟源市', 50, 0),
(2377, 229, '其它区', 60, 0),
(2378, 230, '吉首市', 50, 0),
(2379, 230, '泸溪县', 50, 0),
(2380, 230, '凤凰县', 50, 0),
(2381, 230, '花垣县', 50, 0),
(2382, 230, '保靖县', 50, 0),
(2383, 230, '古丈县', 50, 0),
(2384, 230, '永顺县', 50, 0),
(2385, 230, '龙山县', 50, 0),
(2386, 230, '其它区', 60, 0),
(2387, 231, '荔湾区', 50, 0),
(2388, 231, '越秀区', 50, 0),
(2389, 231, '海珠区', 50, 0),
(2390, 231, '天河区', 50, 0),
(2391, 231, '白云区', 50, 0),
(2392, 231, '黄埔区', 50, 0),
(2393, 231, '番禺区', 50, 0),
(2394, 231, '花都区', 50, 0),
(2395, 231, '南沙区', 50, 0),
(2396, 231, '萝岗区', 50, 0),
(2397, 231, '增城区', 50, 0),
(2398, 231, '从化区', 50, 0),
(2399, 231, '东山区', 50, 0),
(2400, 231, '其它区', 60, 0),
(2401, 232, '武江区', 50, 0),
(2402, 232, '浈江区', 50, 0),
(2403, 232, '曲江区', 50, 0),
(2404, 232, '始兴县', 50, 0),
(2405, 232, '仁化县', 50, 0),
(2406, 232, '翁源县', 50, 0),
(2407, 232, '乳源瑶族自治县', 50, 0),
(2408, 232, '新丰县', 50, 0),
(2409, 232, '乐昌市', 50, 0),
(2410, 232, '南雄市', 50, 0),
(2411, 232, '其它区', 60, 0),
(2412, 233, '罗湖区', 50, 0),
(2413, 233, '福田区', 50, 0),
(2414, 233, '南山区', 50, 0),
(2415, 233, '宝安区', 50, 0),
(2416, 233, '龙岗区', 50, 0),
(2417, 233, '盐田区', 50, 0),
(2418, 233, '其它区', 60, 0),
(2419, 233, '光明新区', 50, 0),
(2420, 233, '坪山新区', 50, 0),
(2421, 233, '大鹏新区', 50, 0),
(2422, 233, '龙华新区', 50, 0),
(2423, 234, '香洲区', 50, 0),
(2424, 234, '斗门区', 50, 0),
(2425, 234, '金湾区', 50, 0),
(2426, 234, '金唐区', 50, 0),
(2427, 234, '南湾区', 50, 0),
(2428, 234, '其它区', 60, 0),
(2429, 235, '龙湖区', 50, 0),
(2430, 235, '金平区', 50, 0),
(2431, 235, '濠江区', 50, 0),
(2432, 235, '潮阳区', 50, 0),
(2433, 235, '潮南区', 50, 0),
(2434, 235, '澄海区', 50, 0),
(2435, 235, '南澳县', 50, 0),
(2436, 235, '其它区', 60, 0),
(2437, 236, '禅城区', 50, 0),
(2438, 236, '南海区', 50, 0),
(2439, 236, '顺德区', 50, 0),
(2440, 236, '三水区', 50, 0),
(2441, 236, '高明区', 50, 0),
(2442, 236, '其它区', 60, 0),
(2443, 237, '蓬江区', 50, 0),
(2444, 237, '江海区', 50, 0),
(2445, 237, '新会区', 50, 0),
(2446, 237, '台山市', 50, 0),
(2447, 237, '开平市', 50, 0),
(2448, 237, '鹤山市', 50, 0),
(2449, 237, '恩平市', 50, 0),
(2450, 237, '其它区', 60, 0),
(2451, 238, '赤坎区', 50, 0),
(2452, 238, '霞山区', 50, 0),
(2453, 238, '坡头区', 50, 0),
(2454, 238, '麻章区', 50, 0),
(2455, 238, '遂溪县', 50, 0),
(2456, 238, '徐闻县', 50, 0),
(2457, 238, '廉江市', 50, 0),
(2458, 238, '雷州市', 50, 0),
(2459, 238, '吴川市', 50, 0),
(2460, 238, '其它区', 60, 0),
(2461, 239, '茂南区', 50, 0),
(2462, 239, '电白区', 50, 0),
(2463, 239, '电白县', 50, 0),
(2464, 239, '高州市', 50, 0),
(2465, 239, '化州市', 50, 0),
(2466, 239, '信宜市', 50, 0),
(2467, 239, '其它区', 60, 0),
(2468, 240, '端州区', 50, 0),
(2469, 240, '鼎湖区', 50, 0),
(2470, 240, '广宁县', 50, 0),
(2471, 240, '怀集县', 50, 0),
(2472, 240, '封开县', 50, 0),
(2473, 240, '德庆县', 50, 0),
(2474, 240, '高要市', 50, 0),
(2475, 240, '四会市', 50, 0),
(2476, 240, '其它区', 60, 0),
(2477, 241, '惠城区', 50, 0),
(2478, 241, '惠阳区', 50, 0),
(2479, 241, '博罗县', 50, 0),
(2480, 241, '惠东县', 50, 0),
(2481, 241, '龙门县', 50, 0),
(2482, 241, '其它区', 60, 0),
(2483, 242, '梅江区', 50, 0),
(2484, 242, '梅县区', 50, 0),
(2485, 242, '大埔县', 50, 0),
(2486, 242, '丰顺县', 50, 0),
(2487, 242, '五华县', 50, 0),
(2488, 242, '平远县', 50, 0),
(2489, 242, '蕉岭县', 50, 0),
(2490, 242, '兴宁市', 50, 0),
(2491, 242, '其它区', 60, 0),
(2492, 243, '城区', 50, 0),
(2493, 243, '海丰县', 50, 0),
(2494, 243, '陆河县', 50, 0),
(2495, 243, '陆丰市', 50, 0),
(2496, 243, '其它区', 60, 0),
(2497, 244, '源城区', 50, 0),
(2498, 244, '紫金县', 50, 0),
(2499, 244, '龙川县', 50, 0),
(2500, 244, '连平县', 50, 0),
(2501, 244, '和平县', 50, 0),
(2502, 244, '东源县', 50, 0),
(2503, 244, '其它区', 60, 0),
(2504, 245, '江城区', 50, 0),
(2505, 245, '阳西县', 50, 0),
(2506, 245, '阳东区', 50, 0),
(2507, 245, '阳春市', 50, 0),
(2508, 245, '其它区', 60, 0),
(2509, 246, '清城区', 50, 0),
(2510, 246, '佛冈县', 50, 0),
(2511, 246, '阳山县', 50, 0),
(2512, 246, '连山壮族瑶族自治县', 50, 0),
(2513, 246, '连南瑶族自治县', 50, 0),
(2514, 246, '清新区', 50, 0),
(2515, 246, '英德市', 50, 0),
(2516, 246, '连州市', 50, 0),
(2517, 246, '其它区', 60, 0),
(2518, 250, '湘桥区', 50, 0),
(2519, 250, '潮安区', 50, 0),
(2520, 250, '饶平县', 50, 0),
(2521, 250, '枫溪区', 50, 0),
(2522, 250, '其它区', 60, 0),
(2523, 251, '榕城区', 50, 0),
(2524, 251, '揭东区', 50, 0),
(2525, 251, '揭西县', 50, 0),
(2526, 251, '惠来县', 50, 0),
(2527, 251, '普宁市', 50, 0),
(2528, 251, '东山区', 50, 0),
(2529, 251, '其它区', 60, 0),
(2530, 252, '云城区', 50, 0),
(2531, 252, '新兴县', 50, 0),
(2532, 252, '郁南县', 50, 0),
(2533, 252, '云安区', 50, 0),
(2534, 252, '罗定市', 50, 0),
(2535, 252, '其它区', 60, 0),
(2536, 253, '兴宁区', 50, 0),
(2537, 253, '青秀区', 50, 0),
(2538, 253, '江南区', 50, 0),
(2539, 253, '西乡塘区', 50, 0),
(2540, 253, '良庆区', 50, 0),
(2541, 253, '邕宁区', 50, 0),
(2542, 253, '武鸣区', 50, 0),
(2543, 253, '隆安县', 50, 0),
(2544, 253, '马山县', 50, 0),
(2545, 253, '上林县', 50, 0),
(2546, 253, '宾阳县', 50, 0),
(2547, 253, '横县', 50, 0),
(2548, 253, '其它区', 60, 0),
(2549, 254, '城中区', 50, 0),
(2550, 254, '鱼峰区', 50, 0),
(2551, 254, '柳南区', 50, 0),
(2552, 254, '柳北区', 50, 0),
(2553, 254, '柳江县', 50, 0),
(2554, 254, '柳城县', 50, 0),
(2555, 254, '鹿寨县', 50, 0),
(2556, 254, '融安县', 50, 0),
(2557, 254, '融水苗族自治县', 50, 0),
(2558, 254, '三江侗族自治县', 50, 0),
(2559, 254, '其它区', 60, 0),
(2560, 255, '秀峰区', 50, 0),
(2561, 255, '叠彩区', 50, 0),
(2562, 255, '象山区', 50, 0),
(2563, 255, '七星区', 50, 0),
(2564, 255, '雁山区', 50, 0),
(2565, 255, '阳朔县', 50, 0),
(2566, 255, '临桂区', 50, 0),
(2567, 255, '灵川县', 50, 0),
(2568, 255, '全州县', 50, 0),
(2569, 255, '兴安县', 50, 0),
(2570, 255, '永福县', 50, 0),
(2571, 255, '灌阳县', 50, 0),
(2572, 255, '龙胜各族自治县', 50, 0),
(2573, 255, '资源县', 50, 0),
(2574, 255, '平乐县', 50, 0),
(2575, 255, '荔浦县', 50, 0),
(2576, 255, '恭城瑶族自治县', 50, 0),
(2577, 255, '其它区', 60, 0),
(2578, 256, '万秀区', 50, 0),
(2579, 256, '蝶山区', 50, 0),
(2580, 256, '长洲区', 50, 0),
(2581, 256, '龙圩区', 50, 0),
(2582, 256, '苍梧县', 50, 0),
(2583, 256, '藤县', 50, 0),
(2584, 256, '蒙山县', 50, 0),
(2585, 256, '岑溪市', 50, 0),
(2586, 256, '其它区', 60, 0),
(2587, 257, '海城区', 50, 0),
(2588, 257, '银海区', 50, 0),
(2589, 257, '铁山港区', 50, 0),
(2590, 257, '合浦县', 50, 0),
(2591, 257, '其它区', 60, 0),
(2592, 258, '港口区', 50, 0),
(2593, 258, '防城区', 50, 0),
(2594, 258, '上思县', 50, 0),
(2595, 258, '东兴市', 50, 0),
(2596, 258, '其它区', 60, 0),
(2597, 259, '钦南区', 50, 0),
(2598, 259, '钦北区', 50, 0),
(2599, 259, '灵山县', 50, 0),
(2600, 259, '浦北县', 50, 0),
(2601, 259, '其它区', 60, 0),
(2602, 260, '港北区', 50, 0),
(2603, 260, '港南区', 50, 0),
(2604, 260, '覃塘区', 50, 0),
(2605, 260, '平南县', 50, 0),
(2606, 260, '桂平市', 50, 0),
(2607, 260, '其它区', 60, 0),
(2608, 261, '玉州区', 50, 0),
(2609, 261, '福绵区', 50, 0),
(2610, 261, '容县', 50, 0),
(2611, 261, '陆川县', 50, 0),
(2612, 261, '博白县', 50, 0),
(2613, 261, '兴业县', 50, 0),
(2614, 261, '北流市', 50, 0),
(2615, 261, '其它区', 60, 0),
(2616, 262, '右江区', 50, 0),
(2617, 262, '田阳县', 50, 0),
(2618, 262, '田东县', 50, 0),
(2619, 262, '平果县', 50, 0),
(2620, 262, '德保县', 50, 0),
(2621, 262, '靖西县', 50, 0),
(2622, 262, '那坡县', 50, 0),
(2623, 262, '凌云县', 50, 0),
(2624, 262, '乐业县', 50, 0),
(2625, 262, '田林县', 50, 0),
(2626, 262, '西林县', 50, 0),
(2627, 262, '隆林各族自治县', 50, 0),
(2628, 262, '其它区', 60, 0),
(2629, 263, '八步区', 50, 0),
(2630, 263, '平桂管理区', 50, 0),
(2631, 263, '昭平县', 50, 0),
(2632, 263, '钟山县', 50, 0),
(2633, 263, '富川瑶族自治县', 50, 0),
(2634, 263, '其它区', 60, 0),
(2635, 264, '金城江区', 50, 0),
(2636, 264, '南丹县', 50, 0),
(2637, 264, '天峨县', 50, 0),
(2638, 264, '凤山县', 50, 0),
(2639, 264, '东兰县', 50, 0),
(2640, 264, '罗城仫佬族自治县', 50, 0),
(2641, 264, '环江毛南族自治县', 50, 0),
(2642, 264, '巴马瑶族自治县', 50, 0),
(2643, 264, '都安瑶族自治县', 50, 0),
(2644, 264, '大化瑶族自治县', 50, 0),
(2645, 264, '宜州市', 50, 0),
(2646, 264, '其它区', 60, 0),
(2647, 265, '兴宾区', 50, 0),
(2648, 265, '忻城县', 50, 0),
(2649, 265, '象州县', 50, 0),
(2650, 265, '武宣县', 50, 0),
(2651, 265, '金秀瑶族自治县', 50, 0),
(2652, 265, '合山市', 50, 0),
(2653, 265, '其它区', 60, 0),
(2654, 266, '江州区', 50, 0),
(2655, 266, '扶绥县', 50, 0),
(2656, 266, '宁明县', 50, 0),
(2657, 266, '龙州县', 50, 0),
(2658, 266, '大新县', 50, 0),
(2659, 266, '天等县', 50, 0),
(2660, 266, '凭祥市', 50, 0),
(2661, 266, '其它区', 60, 0),
(2662, 267, '秀英区', 50, 0),
(2663, 267, '龙华区', 50, 0),
(2664, 267, '琼山区', 50, 0),
(2665, 267, '美兰区', 50, 0),
(2666, 267, '其它区', 60, 0),
(2667, 269, '西沙群岛', 50, 0),
(2668, 269, '南沙群岛', 50, 0),
(2669, 269, '中沙群岛的岛礁及其海域', 50, 0),
(2670, 189, '高新区', 50, 0),
(2671, 189, '其它区', 60, 0),
(2672, 270, '万州区', 50, 0),
(2673, 270, '涪陵区', 50, 0),
(2674, 270, '渝中区', 50, 0),
(2675, 270, '大渡口区', 50, 0),
(2676, 270, '江北区', 50, 0),
(2677, 270, '沙坪坝区', 50, 0),
(2678, 270, '九龙坡区', 50, 0),
(2679, 270, '南岸区', 50, 0),
(2680, 270, '北碚区', 50, 0),
(2681, 270, '万盛区', 50, 0),
(2682, 270, '双桥区', 50, 0),
(2683, 270, '渝北区', 50, 0),
(2684, 270, '巴南区', 50, 0),
(2685, 270, '黔江区', 50, 0),
(2686, 270, '长寿区', 50, 0),
(2687, 270, '綦江区', 50, 0),
(2688, 270, '潼南县', 50, 0),
(2689, 270, '铜梁区', 50, 0),
(2690, 270, '大足区', 50, 0),
(2691, 270, '荣昌县', 50, 0),
(2692, 270, '璧山区', 50, 0),
(2693, 270, '梁平县', 50, 0),
(2694, 270, '城口县', 50, 0),
(2695, 270, '丰都县', 50, 0),
(2696, 270, '垫江县', 50, 0),
(2697, 270, '武隆县', 50, 0),
(2698, 270, '忠县', 50, 0),
(2699, 270, '开县', 50, 0),
(2700, 270, '云阳县', 50, 0),
(2701, 270, '奉节县', 50, 0),
(2702, 270, '巫山县', 50, 0),
(2703, 270, '巫溪县', 50, 0),
(2704, 270, '石柱土家族自治县', 50, 0),
(2705, 270, '秀山土家族苗族自治县', 50, 0),
(2706, 270, '酉阳土家族苗族自治县', 50, 0),
(2707, 270, '彭水苗族土家族自治县', 50, 0),
(2708, 270, '江津区', 50, 0),
(2709, 270, '合川区', 50, 0),
(2710, 270, '永川区', 50, 0),
(2711, 270, '南川区', 50, 0),
(2712, 270, '其它区', 60, 0),
(2713, 271, '锦江区', 50, 0),
(2714, 271, '青羊区', 50, 0),
(2715, 271, '金牛区', 50, 0),
(2716, 271, '武侯区', 50, 0),
(2717, 271, '成华区', 50, 0),
(2718, 271, '龙泉驿区', 50, 0),
(2719, 271, '青白江区', 50, 0),
(2720, 271, '新都区', 50, 0),
(2721, 271, '温江区', 50, 0),
(2722, 271, '金堂县', 50, 0),
(2723, 271, '双流县', 50, 0),
(2724, 271, '郫县', 50, 0),
(2725, 271, '大邑县', 50, 0),
(2726, 271, '蒲江县', 50, 0),
(2727, 271, '新津县', 50, 0),
(2728, 271, '都江堰市', 50, 0),
(2729, 271, '彭州市', 50, 0),
(2730, 271, '邛崃市', 50, 0),
(2731, 271, '崇州市', 50, 0),
(2732, 271, '其它区', 60, 0),
(2733, 272, '自流井区', 50, 0),
(2734, 272, '贡井区', 50, 0),
(2735, 272, '大安区', 50, 0),
(2736, 272, '沿滩区', 50, 0),
(2737, 272, '荣县', 50, 0),
(2738, 272, '富顺县', 50, 0),
(2739, 272, '其它区', 60, 0),
(2740, 273, '东区', 50, 0),
(2741, 273, '西区', 50, 0),
(2742, 273, '仁和区', 50, 0),
(2743, 273, '米易县', 50, 0),
(2744, 273, '盐边县', 50, 0),
(2745, 273, '其它区', 60, 0),
(2746, 274, '江阳区', 50, 0),
(2747, 274, '纳溪区', 50, 0),
(2748, 274, '龙马潭区', 50, 0),
(2749, 274, '泸县', 50, 0),
(2750, 274, '合江县', 50, 0),
(2751, 274, '叙永县', 50, 0),
(2752, 274, '古蔺县', 50, 0),
(2753, 274, '其它区', 60, 0),
(2754, 275, '旌阳区', 50, 0),
(2755, 275, '中江县', 50, 0),
(2756, 275, '罗江县', 50, 0),
(2757, 275, '广汉市', 50, 0),
(2758, 275, '什邡市', 50, 0),
(2759, 275, '绵竹市', 50, 0),
(2760, 275, '其它区', 60, 0),
(2761, 276, '涪城区', 50, 0),
(2762, 276, '游仙区', 50, 0),
(2763, 276, '三台县', 50, 0),
(2764, 276, '盐亭县', 50, 0),
(2765, 276, '安县', 50, 0),
(2766, 276, '梓潼县', 50, 0),
(2767, 276, '北川羌族自治县', 50, 0),
(2768, 276, '平武县', 50, 0),
(2769, 276, '高新区', 50, 0),
(2770, 276, '江油市', 50, 0),
(2771, 276, '其它区', 60, 0),
(2772, 277, '利州区', 50, 0),
(2773, 277, '昭化区', 50, 0),
(2774, 277, '朝天区', 50, 0),
(2775, 277, '旺苍县', 50, 0),
(2776, 277, '青川县', 50, 0),
(2777, 277, '剑阁县', 50, 0),
(2778, 277, '苍溪县', 50, 0),
(2779, 277, '其它区', 60, 0),
(2780, 278, '船山区', 50, 0),
(2781, 278, '安居区', 50, 0),
(2782, 278, '蓬溪县', 50, 0),
(2783, 278, '射洪县', 50, 0),
(2784, 278, '大英县', 50, 0),
(2785, 278, '其它区', 60, 0),
(2786, 279, '市中区', 50, 0),
(2787, 279, '东兴区', 50, 0),
(2788, 279, '威远县', 50, 0),
(2789, 279, '资中县', 50, 0),
(2790, 279, '隆昌县', 50, 0),
(2791, 279, '其它区', 60, 0),
(2792, 280, '市中区', 50, 0),
(2793, 280, '沙湾区', 50, 0),
(2794, 280, '五通桥区', 50, 0),
(2795, 280, '金口河区', 50, 0),
(2796, 280, '犍为县', 50, 0),
(2797, 280, '井研县', 50, 0),
(2798, 280, '夹江县', 50, 0),
(2799, 280, '沐川县', 50, 0),
(2800, 280, '峨边彝族自治县', 50, 0),
(2801, 280, '马边彝族自治县', 50, 0),
(2802, 280, '峨眉山市', 50, 0),
(2803, 280, '其它区', 60, 0),
(2804, 281, '顺庆区', 50, 0),
(2805, 281, '高坪区', 50, 0),
(2806, 281, '嘉陵区', 50, 0),
(2807, 281, '南部县', 50, 0),
(2808, 281, '营山县', 50, 0),
(2809, 281, '蓬安县', 50, 0),
(2810, 281, '仪陇县', 50, 0),
(2811, 281, '西充县', 50, 0),
(2812, 281, '阆中市', 50, 0),
(2813, 281, '其它区', 60, 0),
(2814, 282, '东坡区', 50, 0),
(2815, 282, '仁寿县', 50, 0),
(2816, 282, '彭山区', 50, 0),
(2817, 282, '洪雅县', 50, 0),
(2818, 282, '丹棱县', 50, 0),
(2819, 282, '青神县', 50, 0),
(2820, 282, '其它区', 60, 0),
(2821, 283, '翠屏区', 50, 0),
(2822, 283, '宜宾县', 50, 0),
(2823, 283, '南溪区', 50, 0),
(2824, 283, '江安县', 50, 0),
(2825, 283, '长宁县', 50, 0),
(2826, 283, '高县', 50, 0),
(2827, 283, '珙县', 50, 0),
(2828, 283, '筠连县', 50, 0),
(2829, 283, '兴文县', 50, 0),
(2830, 283, '屏山县', 50, 0),
(2831, 283, '其它区', 60, 0),
(2832, 284, '广安区', 50, 0),
(2833, 284, '前锋区', 50, 0),
(2834, 284, '岳池县', 50, 0),
(2835, 284, '武胜县', 50, 0),
(2836, 284, '邻水县', 50, 0),
(2837, 284, '华蓥市', 50, 0),
(2838, 284, '市辖区', 50, 0),
(2839, 284, '其它区', 60, 0),
(2840, 285, '通川区', 50, 0),
(2841, 285, '达川区', 50, 0),
(2842, 285, '宣汉县', 50, 0),
(2843, 285, '开江县', 50, 0),
(2844, 285, '大竹县', 50, 0),
(2845, 285, '渠县', 50, 0),
(2846, 285, '万源市', 50, 0),
(2847, 285, '其它区', 60, 0),
(2848, 286, '雨城区', 50, 0),
(2849, 286, '名山区', 50, 0),
(2850, 286, '荥经县', 50, 0),
(2851, 286, '汉源县', 50, 0),
(2852, 286, '石棉县', 50, 0),
(2853, 286, '天全县', 50, 0),
(2854, 286, '芦山县', 50, 0),
(2855, 286, '宝兴县', 50, 0),
(2856, 286, '其它区', 60, 0),
(2857, 287, '巴州区', 50, 0),
(2858, 287, '恩阳区', 50, 0),
(2859, 287, '通江县', 50, 0),
(2860, 287, '南江县', 50, 0),
(2861, 287, '平昌县', 50, 0),
(2862, 287, '其它区', 60, 0),
(2863, 288, '雁江区', 50, 0),
(2864, 288, '安岳县', 50, 0),
(2865, 288, '乐至县', 50, 0),
(2866, 288, '简阳市', 50, 0),
(2867, 288, '其它区', 60, 0),
(2868, 289, '汶川县', 50, 0),
(2869, 289, '理县', 50, 0),
(2870, 289, '茂县', 50, 0),
(2871, 289, '松潘县', 50, 0),
(2872, 289, '九寨沟县', 50, 0),
(2873, 289, '金川县', 50, 0),
(2874, 289, '小金县', 50, 0),
(2875, 289, '黑水县', 50, 0),
(2876, 289, '马尔康县', 50, 0),
(2877, 289, '壤塘县', 50, 0),
(2878, 289, '阿坝县', 50, 0),
(2879, 289, '若尔盖县', 50, 0),
(2880, 289, '红原县', 50, 0),
(2881, 289, '其它区', 60, 0),
(2882, 290, '康定市', 50, 0),
(2883, 290, '泸定县', 50, 0),
(2884, 290, '丹巴县', 50, 0),
(2885, 290, '九龙县', 50, 0),
(2886, 290, '雅江县', 50, 0),
(2887, 290, '道孚县', 50, 0),
(2888, 290, '炉霍县', 50, 0),
(2889, 290, '甘孜县', 50, 0),
(2890, 290, '新龙县', 50, 0),
(2891, 290, '德格县', 50, 0),
(2892, 290, '白玉县', 50, 0),
(2893, 290, '石渠县', 50, 0),
(2894, 290, '色达县', 50, 0),
(2895, 290, '理塘县', 50, 0),
(2896, 290, '巴塘县', 50, 0),
(2897, 290, '乡城县', 50, 0),
(2898, 290, '稻城县', 50, 0),
(2899, 290, '得荣县', 50, 0),
(2900, 290, '其它区', 60, 0),
(2901, 291, '西昌市', 50, 0),
(2902, 291, '木里藏族自治县', 50, 0),
(2903, 291, '盐源县', 50, 0),
(2904, 291, '德昌县', 50, 0),
(2905, 291, '会理县', 50, 0),
(2906, 291, '会东县', 50, 0),
(2907, 291, '宁南县', 50, 0),
(2908, 291, '普格县', 50, 0),
(2909, 291, '布拖县', 50, 0),
(2910, 291, '金阳县', 50, 0),
(2911, 291, '昭觉县', 50, 0),
(2912, 291, '喜德县', 50, 0),
(2913, 291, '冕宁县', 50, 0),
(2914, 291, '越西县', 50, 0),
(2915, 291, '甘洛县', 50, 0),
(2916, 291, '美姑县', 50, 0),
(2917, 291, '雷波县', 50, 0),
(2918, 291, '其它区', 60, 0),
(2919, 292, '南明区', 50, 0),
(2920, 292, '云岩区', 50, 0),
(2921, 292, '花溪区', 50, 0),
(2922, 292, '乌当区', 50, 0),
(2923, 292, '白云区', 50, 0),
(2924, 292, '小河区', 50, 0),
(2925, 292, '开阳县', 50, 0),
(2926, 292, '息烽县', 50, 0),
(2927, 292, '修文县', 50, 0),
(2928, 292, '观山湖区', 50, 0),
(2929, 292, '清镇市', 50, 0),
(2930, 292, '其它区', 60, 0),
(2931, 293, '钟山区', 50, 0),
(2932, 293, '六枝特区', 50, 0),
(2933, 293, '水城县', 50, 0),
(2934, 293, '盘县', 50, 0),
(2935, 293, '其它区', 60, 0),
(2936, 294, '红花岗区', 50, 0),
(2937, 294, '汇川区', 50, 0),
(2938, 294, '遵义县', 50, 0),
(2939, 294, '桐梓县', 50, 0),
(2940, 294, '绥阳县', 50, 0),
(2941, 294, '正安县', 50, 0),
(2942, 294, '道真仡佬族苗族自治县', 50, 0),
(2943, 294, '务川仡佬族苗族自治县', 50, 0),
(2944, 294, '凤冈县', 50, 0),
(2945, 294, '湄潭县', 50, 0),
(2946, 294, '余庆县', 50, 0),
(2947, 294, '习水县', 50, 0),
(2948, 294, '赤水市', 50, 0),
(2949, 294, '仁怀市', 50, 0),
(2950, 294, '其它区', 60, 0),
(2951, 295, '西秀区', 50, 0),
(2952, 295, '平坝区', 50, 0),
(2953, 295, '普定县', 50, 0),
(2954, 295, '镇宁布依族苗族自治县', 50, 0),
(2955, 295, '关岭布依族苗族自治县', 50, 0),
(2956, 295, '紫云苗族布依族自治县', 50, 0),
(2957, 295, '其它区', 60, 0),
(2958, 296, '碧江区', 50, 0),
(2959, 296, '江口县', 50, 0),
(2960, 296, '玉屏侗族自治县', 50, 0),
(2961, 296, '石阡县', 50, 0),
(2962, 296, '思南县', 50, 0),
(2963, 296, '印江土家族苗族自治县', 50, 0),
(2964, 296, '德江县', 50, 0),
(2965, 296, '沿河土家族自治县', 50, 0),
(2966, 296, '松桃苗族自治县', 50, 0),
(2967, 296, '万山区', 50, 0),
(2968, 296, '其它区', 60, 0),
(2969, 297, '兴义市', 50, 0),
(2970, 297, '兴仁县', 50, 0),
(2971, 297, '普安县', 50, 0),
(2972, 297, '晴隆县', 50, 0),
(2973, 297, '贞丰县', 50, 0),
(2974, 297, '望谟县', 50, 0),
(2975, 297, '册亨县', 50, 0),
(2976, 297, '安龙县', 50, 0),
(2977, 297, '其它区', 60, 0),
(2978, 298, '七星关区', 50, 0),
(2979, 298, '大方县', 50, 0),
(2980, 298, '黔西县', 50, 0),
(2981, 298, '金沙县', 50, 0),
(2982, 298, '织金县', 50, 0),
(2983, 298, '纳雍县', 50, 0),
(2984, 298, '威宁彝族回族苗族自治县', 50, 0),
(2985, 298, '赫章县', 50, 0),
(2986, 298, '其它区', 60, 0),
(2987, 299, '凯里市', 50, 0),
(2988, 299, '黄平县', 50, 0),
(2989, 299, '施秉县', 50, 0),
(2990, 299, '三穗县', 50, 0),
(2991, 299, '镇远县', 50, 0),
(2992, 299, '岑巩县', 50, 0),
(2993, 299, '天柱县', 50, 0),
(2994, 299, '锦屏县', 50, 0),
(2995, 299, '剑河县', 50, 0),
(2996, 299, '台江县', 50, 0),
(2997, 299, '黎平县', 50, 0),
(2998, 299, '榕江县', 50, 0),
(2999, 299, '从江县', 50, 0),
(3000, 299, '雷山县', 50, 0),
(3001, 299, '麻江县', 50, 0),
(3002, 299, '丹寨县', 50, 0),
(3003, 299, '其它区', 60, 0),
(3004, 300, '都匀市', 50, 0),
(3005, 300, '福泉市', 50, 0),
(3006, 300, '荔波县', 50, 0),
(3007, 300, '贵定县', 50, 0),
(3008, 300, '瓮安县', 50, 0),
(3009, 300, '独山县', 50, 0),
(3010, 300, '平塘县', 50, 0),
(3011, 300, '罗甸县', 50, 0),
(3012, 300, '长顺县', 50, 0),
(3013, 300, '龙里县', 50, 0),
(3014, 300, '惠水县', 50, 0),
(3015, 300, '三都水族自治县', 50, 0),
(3016, 300, '其它区', 60, 0),
(3017, 301, '五华区', 50, 0),
(3018, 301, '盘龙区', 50, 0),
(3019, 301, '官渡区', 50, 0),
(3020, 301, '西山区', 50, 0),
(3021, 301, '东川区', 50, 0),
(3022, 301, '呈贡区', 50, 0),
(3023, 301, '晋宁县', 50, 0),
(3024, 301, '富民县', 50, 0),
(3025, 301, '宜良县', 50, 0),
(3026, 301, '石林彝族自治县', 50, 0),
(3027, 301, '嵩明县', 50, 0),
(3028, 301, '禄劝彝族苗族自治县', 50, 0),
(3029, 301, '寻甸回族彝族自治县', 50, 0),
(3030, 301, '安宁市', 50, 0),
(3031, 301, '其它区', 60, 0),
(3032, 302, '麒麟区', 50, 0),
(3033, 302, '马龙县', 50, 0),
(3034, 302, '陆良县', 50, 0),
(3035, 302, '师宗县', 50, 0),
(3036, 302, '罗平县', 50, 0),
(3037, 302, '富源县', 50, 0),
(3038, 302, '会泽县', 50, 0),
(3039, 302, '沾益县', 50, 0),
(3040, 302, '宣威市', 50, 0),
(3041, 302, '其它区', 60, 0),
(3042, 303, '红塔区', 50, 0),
(3043, 303, '江川县', 50, 0),
(3044, 303, '澄江县', 50, 0),
(3045, 303, '通海县', 50, 0),
(3046, 303, '华宁县', 50, 0),
(3047, 303, '易门县', 50, 0),
(3048, 303, '峨山彝族自治县', 50, 0),
(3049, 303, '新平彝族傣族自治县', 50, 0),
(3050, 303, '元江哈尼族彝族傣族自治县', 50, 0),
(3051, 303, '其它区', 60, 0),
(3052, 304, '隆阳区', 50, 0),
(3053, 304, '施甸县', 50, 0),
(3054, 304, '腾冲县', 50, 0),
(3055, 304, '龙陵县', 50, 0),
(3056, 304, '昌宁县', 50, 0),
(3057, 304, '其它区', 60, 0),
(3058, 305, '昭阳区', 50, 0),
(3059, 305, '鲁甸县', 50, 0),
(3060, 305, '巧家县', 50, 0),
(3061, 305, '盐津县', 50, 0),
(3062, 305, '大关县', 50, 0),
(3063, 305, '永善县', 50, 0),
(3064, 305, '绥江县', 50, 0),
(3065, 305, '镇雄县', 50, 0),
(3066, 305, '彝良县', 50, 0),
(3067, 305, '威信县', 50, 0),
(3068, 305, '水富县', 50, 0),
(3069, 305, '其它区', 60, 0),
(3070, 306, '古城区', 50, 0),
(3071, 306, '玉龙纳西族自治县', 50, 0),
(3072, 306, '永胜县', 50, 0),
(3073, 306, '华坪县', 50, 0),
(3074, 306, '宁蒗彝族自治县', 50, 0),
(3075, 306, '其它区', 60, 0),
(3076, 307, '思茅区', 50, 0),
(3077, 307, '宁洱哈尼族彝族自治县', 50, 0),
(3078, 307, '墨江哈尼族自治县', 50, 0),
(3079, 307, '景东彝族自治县', 50, 0),
(3080, 307, '景谷傣族彝族自治县', 50, 0),
(3081, 307, '镇沅彝族哈尼族拉祜族自治县', 50, 0),
(3082, 307, '江城哈尼族彝族自治县', 50, 0),
(3083, 307, '孟连傣族拉祜族佤族自治县', 50, 0),
(3084, 307, '澜沧拉祜族自治县', 50, 0),
(3085, 307, '西盟佤族自治县', 50, 0),
(3086, 307, '其它区', 60, 0),
(3087, 308, '临翔区', 50, 0),
(3088, 308, '凤庆县', 50, 0),
(3089, 308, '云县', 50, 0),
(3090, 308, '永德县', 50, 0),
(3091, 308, '镇康县', 50, 0),
(3092, 308, '双江拉祜族佤族布朗族傣族自治县', 50, 0),
(3093, 308, '耿马傣族佤族自治县', 50, 0),
(3094, 308, '沧源佤族自治县', 50, 0),
(3095, 308, '其它区', 60, 0),
(3096, 309, '楚雄市', 50, 0),
(3097, 309, '双柏县', 50, 0),
(3098, 309, '牟定县', 50, 0),
(3099, 309, '南华县', 50, 0),
(3100, 309, '姚安县', 50, 0),
(3101, 309, '大姚县', 50, 0),
(3102, 309, '永仁县', 50, 0),
(3103, 309, '元谋县', 50, 0),
(3104, 309, '武定县', 50, 0),
(3105, 309, '禄丰县', 50, 0),
(3106, 309, '其它区', 60, 0),
(3107, 310, '个旧市', 50, 0),
(3108, 310, '开远市', 50, 0),
(3109, 310, '蒙自市', 50, 0),
(3110, 310, '屏边苗族自治县', 50, 0),
(3111, 310, '建水县', 50, 0),
(3112, 310, '石屏县', 50, 0),
(3113, 310, '弥勒市', 50, 0),
(3114, 310, '泸西县', 50, 0),
(3115, 310, '元阳县', 50, 0),
(3116, 310, '红河县', 50, 0),
(3117, 310, '金平苗族瑶族傣族自治县', 50, 0),
(3118, 310, '绿春县', 50, 0),
(3119, 310, '河口瑶族自治县', 50, 0),
(3120, 310, '其它区', 60, 0),
(3121, 311, '文山市', 50, 0),
(3122, 311, '砚山县', 50, 0),
(3123, 311, '西畴县', 50, 0),
(3124, 311, '麻栗坡县', 50, 0),
(3125, 311, '马关县', 50, 0),
(3126, 311, '丘北县', 50, 0),
(3127, 311, '广南县', 50, 0),
(3128, 311, '富宁县', 50, 0),
(3129, 311, '其它区', 60, 0),
(3130, 312, '景洪市', 50, 0),
(3131, 312, '勐海县', 50, 0),
(3132, 312, '勐腊县', 50, 0),
(3133, 312, '其它区', 60, 0),
(3134, 313, '大理市', 50, 0),
(3135, 313, '漾濞彝族自治县', 50, 0),
(3136, 313, '祥云县', 50, 0),
(3137, 313, '宾川县', 50, 0),
(3138, 313, '弥渡县', 50, 0),
(3139, 313, '南涧彝族自治县', 50, 0),
(3140, 313, '巍山彝族回族自治县', 50, 0),
(3141, 313, '永平县', 50, 0),
(3142, 313, '云龙县', 50, 0),
(3143, 313, '洱源县', 50, 0),
(3144, 313, '剑川县', 50, 0),
(3145, 313, '鹤庆县', 50, 0),
(3146, 313, '其它区', 60, 0),
(3147, 314, '瑞丽市', 50, 0),
(3148, 314, '芒市', 50, 0),
(3149, 314, '梁河县', 50, 0),
(3150, 314, '盈江县', 50, 0),
(3151, 314, '陇川县', 50, 0),
(3152, 314, '其它区', 60, 0),
(3153, 315, '泸水县', 50, 0),
(3154, 315, '福贡县', 50, 0),
(3155, 315, '贡山独龙族怒族自治县', 50, 0),
(3156, 315, '兰坪白族普米族自治县', 50, 0),
(3157, 315, '其它区', 60, 0),
(3158, 316, '香格里拉市', 50, 0),
(3159, 316, '德钦县', 50, 0),
(3160, 316, '维西傈僳族自治县', 50, 0),
(3161, 316, '其它区', 60, 0),
(3162, 317, '城关区', 50, 0),
(3163, 317, '林周县', 50, 0),
(3164, 317, '当雄县', 50, 0),
(3165, 317, '尼木县', 50, 0),
(3166, 317, '曲水县', 50, 0),
(3167, 317, '堆龙德庆县', 50, 0),
(3168, 317, '达孜县', 50, 0),
(3169, 317, '墨竹工卡县', 50, 0),
(3170, 317, '其它区', 60, 0),
(3171, 318, '卡若区', 50, 0),
(3172, 318, '江达县', 50, 0),
(3173, 318, '贡觉县', 50, 0),
(3174, 318, '类乌齐县', 50, 0),
(3175, 318, '丁青县', 50, 0),
(3176, 318, '察雅县', 50, 0),
(3177, 318, '八宿县', 50, 0),
(3178, 318, '左贡县', 50, 0),
(3179, 318, '芒康县', 50, 0),
(3180, 318, '洛隆县', 50, 0),
(3181, 318, '边坝县', 50, 0),
(3182, 318, '其它区', 60, 0),
(3183, 319, '乃东县', 50, 0),
(3184, 319, '扎囊县', 50, 0),
(3185, 319, '贡嘎县', 50, 0),
(3186, 319, '桑日县', 50, 0),
(3187, 319, '琼结县', 50, 0),
(3188, 319, '曲松县', 50, 0),
(3189, 319, '措美县', 50, 0),
(3190, 319, '洛扎县', 50, 0),
(3191, 319, '加查县', 50, 0),
(3192, 319, '隆子县', 50, 0),
(3193, 319, '错那县', 50, 0),
(3194, 319, '浪卡子县', 50, 0),
(3195, 319, '其它区', 60, 0),
(3196, 320, '桑珠孜区', 50, 0),
(3197, 320, '南木林县', 50, 0),
(3198, 320, '江孜县', 50, 0),
(3199, 320, '定日县', 50, 0),
(3200, 320, '萨迦县', 50, 0),
(3201, 320, '拉孜县', 50, 0),
(3202, 320, '昂仁县', 50, 0),
(3203, 320, '谢通门县', 50, 0),
(3204, 320, '白朗县', 50, 0),
(3205, 320, '仁布县', 50, 0),
(3206, 320, '康马县', 50, 0),
(3207, 320, '定结县', 50, 0),
(3208, 320, '仲巴县', 50, 0),
(3209, 320, '亚东县', 50, 0),
(3210, 320, '吉隆县', 50, 0),
(3211, 320, '聂拉木县', 50, 0),
(3212, 320, '萨嘎县', 50, 0),
(3213, 320, '岗巴县', 50, 0),
(3214, 320, '其它区', 60, 0),
(3215, 321, '那曲县', 50, 0),
(3216, 321, '嘉黎县', 50, 0),
(3217, 321, '比如县', 50, 0),
(3218, 321, '聂荣县', 50, 0),
(3219, 321, '安多县', 50, 0),
(3220, 321, '申扎县', 50, 0),
(3221, 321, '索县', 50, 0),
(3222, 321, '班戈县', 50, 0),
(3223, 321, '巴青县', 50, 0),
(3224, 321, '尼玛县', 50, 0),
(3225, 321, '其它区', 60, 0),
(3226, 321, '双湖县', 50, 0),
(3227, 322, '普兰县', 50, 0),
(3228, 322, '札达县', 50, 0),
(3229, 322, '噶尔县', 50, 0),
(3230, 322, '日土县', 50, 0),
(3231, 322, '革吉县', 50, 0),
(3232, 322, '改则县', 50, 0),
(3233, 322, '措勤县', 50, 0),
(3234, 322, '其它区', 60, 0),
(3235, 323, '巴宜区', 50, 0),
(3236, 323, '工布江达县', 50, 0),
(3237, 323, '米林县', 50, 0),
(3238, 323, '墨脱县', 50, 0),
(3239, 323, '波密县', 50, 0),
(3240, 323, '察隅县', 50, 0),
(3241, 323, '朗县', 50, 0),
(3242, 323, '其它区', 60, 0),
(3243, 324, '新城区', 50, 0),
(3244, 324, '碑林区', 50, 0),
(3245, 324, '莲湖区', 50, 0),
(3246, 324, '灞桥区', 50, 0),
(3247, 324, '未央区', 50, 0),
(3248, 324, '雁塔区', 50, 0),
(3249, 324, '阎良区', 50, 0),
(3250, 324, '临潼区', 50, 0),
(3251, 324, '长安区', 50, 0),
(3252, 324, '蓝田县', 50, 0),
(3253, 324, '周至县', 50, 0),
(3254, 324, '户县', 50, 0),
(3255, 324, '高陵区', 50, 0),
(3256, 324, '其它区', 60, 0),
(3257, 325, '王益区', 50, 0),
(3258, 325, '印台区', 50, 0),
(3259, 325, '耀州区', 50, 0),
(3260, 325, '宜君县', 50, 0),
(3261, 325, '其它区', 60, 0),
(3262, 326, '渭滨区', 50, 0),
(3263, 326, '金台区', 50, 0),
(3264, 326, '陈仓区', 50, 0),
(3265, 326, '凤翔县', 50, 0),
(3266, 326, '岐山县', 50, 0),
(3267, 326, '扶风县', 50, 0),
(3268, 326, '眉县', 50, 0),
(3269, 326, '陇县', 50, 0),
(3270, 326, '千阳县', 50, 0),
(3271, 326, '麟游县', 50, 0),
(3272, 326, '凤县', 50, 0),
(3273, 326, '太白县', 50, 0),
(3274, 326, '其它区', 60, 0),
(3275, 327, '秦都区', 50, 0),
(3276, 327, '杨陵区', 50, 0),
(3277, 327, '渭城区', 50, 0),
(3278, 327, '三原县', 50, 0),
(3279, 327, '泾阳县', 50, 0),
(3280, 327, '乾县', 50, 0),
(3281, 327, '礼泉县', 50, 0),
(3282, 327, '永寿县', 50, 0),
(3283, 327, '彬县', 50, 0),
(3284, 327, '长武县', 50, 0),
(3285, 327, '旬邑县', 50, 0),
(3286, 327, '淳化县', 50, 0),
(3287, 327, '武功县', 50, 0),
(3288, 327, '兴平市', 50, 0),
(3289, 327, '其它区', 60, 0),
(3290, 328, '临渭区', 50, 0),
(3291, 328, '华县', 50, 0),
(3292, 328, '潼关县', 50, 0),
(3293, 328, '大荔县', 50, 0),
(3294, 328, '合阳县', 50, 0),
(3295, 328, '澄城县', 50, 0),
(3296, 328, '蒲城县', 50, 0),
(3297, 328, '白水县', 50, 0),
(3298, 328, '富平县', 50, 0),
(3299, 328, '韩城市', 50, 0),
(3300, 328, '华阴市', 50, 0),
(3301, 328, '其它区', 60, 0),
(3302, 329, '宝塔区', 50, 0),
(3303, 329, '延长县', 50, 0),
(3304, 329, '延川县', 50, 0),
(3305, 329, '子长县', 50, 0),
(3306, 329, '安塞县', 50, 0),
(3307, 329, '志丹县', 50, 0),
(3308, 329, '吴起县', 50, 0),
(3309, 329, '甘泉县', 50, 0),
(3310, 329, '富县', 50, 0),
(3311, 329, '洛川县', 50, 0),
(3312, 329, '宜川县', 50, 0),
(3313, 329, '黄龙县', 50, 0),
(3314, 329, '黄陵县', 50, 0),
(3315, 329, '其它区', 60, 0),
(3316, 330, '汉台区', 50, 0),
(3317, 330, '南郑县', 50, 0),
(3318, 330, '城固县', 50, 0),
(3319, 330, '洋县', 50, 0),
(3320, 330, '西乡县', 50, 0),
(3321, 330, '勉县', 50, 0),
(3322, 330, '宁强县', 50, 0),
(3323, 330, '略阳县', 50, 0),
(3324, 330, '镇巴县', 50, 0),
(3325, 330, '留坝县', 50, 0),
(3326, 330, '佛坪县', 50, 0),
(3327, 330, '其它区', 60, 0),
(3328, 331, '榆阳区', 50, 0),
(3329, 331, '神木县', 50, 0),
(3330, 331, '府谷县', 50, 0),
(3331, 331, '横山县', 50, 0),
(3332, 331, '靖边县', 50, 0),
(3333, 331, '定边县', 50, 0),
(3334, 331, '绥德县', 50, 0),
(3335, 331, '米脂县', 50, 0),
(3336, 331, '佳县', 50, 0),
(3337, 331, '吴堡县', 50, 0),
(3338, 331, '清涧县', 50, 0),
(3339, 331, '子洲县', 50, 0),
(3340, 331, '其它区', 60, 0),
(3341, 332, '汉滨区', 50, 0),
(3342, 332, '汉阴县', 50, 0),
(3343, 332, '石泉县', 50, 0),
(3344, 332, '宁陕县', 50, 0),
(3345, 332, '紫阳县', 50, 0),
(3346, 332, '岚皋县', 50, 0),
(3347, 332, '平利县', 50, 0),
(3348, 332, '镇坪县', 50, 0),
(3349, 332, '旬阳县', 50, 0),
(3350, 332, '白河县', 50, 0),
(3351, 332, '其它区', 60, 0),
(3352, 333, '商州区', 50, 0),
(3353, 333, '洛南县', 50, 0),
(3354, 333, '丹凤县', 50, 0),
(3355, 333, '商南县', 50, 0),
(3356, 333, '山阳县', 50, 0),
(3357, 333, '镇安县', 50, 0),
(3358, 333, '柞水县', 50, 0),
(3359, 333, '其它区', 60, 0),
(3360, 334, '城关区', 50, 0),
(3361, 334, '七里河区', 50, 0),
(3362, 334, '西固区', 50, 0),
(3363, 334, '安宁区', 50, 0),
(3364, 334, '红古区', 50, 0),
(3365, 334, '永登县', 50, 0),
(3366, 334, '皋兰县', 50, 0),
(3367, 334, '榆中县', 50, 0),
(3368, 334, '其它区', 60, 0),
(3369, 336, '金川区', 50, 0),
(3370, 336, '永昌县', 50, 0),
(3371, 336, '其它区', 60, 0),
(3372, 337, '白银区', 50, 0),
(3373, 337, '平川区', 50, 0),
(3374, 337, '靖远县', 50, 0),
(3375, 337, '会宁县', 50, 0),
(3376, 337, '景泰县', 50, 0),
(3377, 337, '其它区', 60, 0),
(3378, 338, '秦州区', 50, 0),
(3379, 338, '麦积区', 50, 0),
(3380, 338, '清水县', 50, 0),
(3381, 338, '秦安县', 50, 0),
(3382, 338, '甘谷县', 50, 0),
(3383, 338, '武山县', 50, 0),
(3384, 338, '张家川回族自治县', 50, 0),
(3385, 338, '其它区', 60, 0),
(3386, 339, '凉州区', 50, 0),
(3387, 339, '民勤县', 50, 0),
(3388, 339, '古浪县', 50, 0),
(3389, 339, '天祝藏族自治县', 50, 0),
(3390, 339, '其它区', 60, 0),
(3391, 340, '甘州区', 50, 0),
(3392, 340, '肃南裕固族自治县', 50, 0),
(3393, 340, '民乐县', 50, 0),
(3394, 340, '临泽县', 50, 0),
(3395, 340, '高台县', 50, 0),
(3396, 340, '山丹县', 50, 0),
(3397, 340, '其它区', 60, 0),
(3398, 341, '崆峒区', 50, 0),
(3399, 341, '泾川县', 50, 0),
(3400, 341, '灵台县', 50, 0),
(3401, 341, '崇信县', 50, 0),
(3402, 341, '华亭县', 50, 0),
(3403, 341, '庄浪县', 50, 0),
(3404, 341, '静宁县', 50, 0),
(3405, 341, '其它区', 60, 0),
(3406, 342, '肃州区', 50, 0),
(3407, 342, '金塔县', 50, 0),
(3408, 342, '瓜州县', 50, 0),
(3409, 342, '肃北蒙古族自治县', 50, 0),
(3410, 342, '阿克塞哈萨克族自治县', 50, 0),
(3411, 342, '玉门市', 50, 0),
(3412, 342, '敦煌市', 50, 0),
(3413, 342, '其它区', 60, 0),
(3414, 343, '西峰区', 50, 0),
(3415, 343, '庆城县', 50, 0),
(3416, 343, '环县', 50, 0),
(3417, 343, '华池县', 50, 0),
(3418, 343, '合水县', 50, 0),
(3419, 343, '正宁县', 50, 0),
(3420, 343, '宁县', 50, 0),
(3421, 343, '镇原县', 50, 0),
(3422, 343, '其它区', 60, 0),
(3423, 344, '安定区', 50, 0),
(3424, 344, '通渭县', 50, 0),
(3425, 344, '陇西县', 50, 0),
(3426, 344, '渭源县', 50, 0),
(3427, 344, '临洮县', 50, 0),
(3428, 344, '漳县', 50, 0),
(3429, 344, '岷县', 50, 0),
(3430, 344, '其它区', 60, 0),
(3431, 345, '武都区', 50, 0),
(3432, 345, '成县', 50, 0),
(3433, 345, '文县', 50, 0),
(3434, 345, '宕昌县', 50, 0),
(3435, 345, '康县', 50, 0),
(3436, 345, '西和县', 50, 0),
(3437, 345, '礼县', 50, 0),
(3438, 345, '徽县', 50, 0),
(3439, 345, '两当县', 50, 0),
(3440, 345, '其它区', 60, 0),
(3441, 346, '临夏市', 50, 0),
(3442, 346, '临夏县', 50, 0),
(3443, 346, '康乐县', 50, 0),
(3444, 346, '永靖县', 50, 0),
(3445, 346, '广河县', 50, 0),
(3446, 346, '和政县', 50, 0),
(3447, 346, '东乡族自治县', 50, 0),
(3448, 346, '积石山保安族东乡族撒拉族自治县', 50, 0),
(3449, 346, '其它区', 60, 0),
(3450, 347, '合作市', 50, 0),
(3451, 347, '临潭县', 50, 0),
(3452, 347, '卓尼县', 50, 0),
(3453, 347, '舟曲县', 50, 0),
(3454, 347, '迭部县', 50, 0),
(3455, 347, '玛曲县', 50, 0),
(3456, 347, '碌曲县', 50, 0),
(3457, 347, '夏河县', 50, 0),
(3458, 347, '其它区', 60, 0),
(3459, 348, '城东区', 50, 0),
(3460, 348, '城中区', 50, 0),
(3461, 348, '城西区', 50, 0),
(3462, 348, '城北区', 50, 0),
(3463, 348, '大通回族土族自治县', 50, 0),
(3464, 348, '湟中县', 50, 0),
(3465, 348, '湟源县', 50, 0),
(3466, 348, '其它区', 60, 0),
(3467, 349, '平安区', 50, 0),
(3468, 349, '民和回族土族自治县', 50, 0),
(3469, 349, '乐都区', 50, 0),
(3470, 349, '互助土族自治县', 50, 0),
(3471, 349, '化隆回族自治县', 50, 0),
(3472, 349, '循化撒拉族自治县', 50, 0),
(3473, 349, '其它区', 60, 0),
(3474, 350, '门源回族自治县', 50, 0),
(3475, 350, '祁连县', 50, 0),
(3476, 350, '海晏县', 50, 0),
(3477, 350, '刚察县', 50, 0),
(3478, 350, '其它区', 60, 0),
(3479, 351, '同仁县', 50, 0),
(3480, 351, '尖扎县', 50, 0),
(3481, 351, '泽库县', 50, 0),
(3482, 351, '河南蒙古族自治县', 50, 0),
(3483, 351, '其它区', 60, 0),
(3484, 352, '共和县', 50, 0),
(3485, 352, '同德县', 50, 0),
(3486, 352, '贵德县', 50, 0),
(3487, 352, '兴海县', 50, 0),
(3488, 352, '贵南县', 50, 0),
(3489, 352, '其它区', 60, 0),
(3490, 353, '玛沁县', 50, 0),
(3491, 353, '班玛县', 50, 0),
(3492, 353, '甘德县', 50, 0),
(3493, 353, '达日县', 50, 0),
(3494, 353, '久治县', 50, 0),
(3495, 353, '玛多县', 50, 0),
(3496, 353, '其它区', 60, 0),
(3497, 354, '玉树市', 50, 0),
(3498, 354, '杂多县', 50, 0),
(3499, 354, '称多县', 50, 0),
(3500, 354, '治多县', 50, 0),
(3501, 354, '囊谦县', 50, 0),
(3502, 354, '曲麻莱县', 50, 0),
(3503, 354, '其它区', 60, 0),
(3504, 355, '格尔木市', 50, 0),
(3505, 355, '德令哈市', 50, 0),
(3506, 355, '乌兰县', 50, 0),
(3507, 355, '都兰县', 50, 0),
(3508, 355, '天峻县', 50, 0),
(3509, 355, '其它区', 60, 0),
(3510, 356, '兴庆区', 50, 0),
(3511, 356, '西夏区', 50, 0),
(3512, 356, '金凤区', 50, 0),
(3513, 356, '永宁县', 50, 0),
(3514, 356, '贺兰县', 50, 0),
(3515, 356, '灵武市', 50, 0),
(3516, 356, '其它区', 60, 0),
(3517, 357, '大武口区', 50, 0),
(3518, 357, '惠农区', 50, 0),
(3519, 357, '平罗县', 50, 0),
(3520, 357, '其它区', 60, 0),
(3521, 358, '利通区', 50, 0),
(3522, 358, '红寺堡区', 50, 0),
(3523, 358, '盐池县', 50, 0),
(3524, 358, '同心县', 50, 0),
(3525, 358, '青铜峡市', 50, 0),
(3526, 358, '其它区', 60, 0),
(3527, 359, '原州区', 50, 0),
(3528, 359, '西吉县', 50, 0),
(3529, 359, '隆德县', 50, 0),
(3530, 359, '泾源县', 50, 0),
(3531, 359, '彭阳县', 50, 0),
(3532, 359, '其它区', 60, 0),
(3533, 360, '沙坡头区', 50, 0),
(3534, 360, '中宁县', 50, 0),
(3535, 360, '海原县', 50, 0),
(3536, 360, '其它区', 60, 0),
(3537, 361, '天山区', 50, 0),
(3538, 361, '沙依巴克区', 50, 0),
(3539, 361, '新市区', 50, 0),
(3540, 361, '水磨沟区', 50, 0),
(3541, 361, '头屯河区', 50, 0),
(3542, 361, '达坂城区', 50, 0),
(3543, 361, '东山区', 50, 0),
(3544, 361, '米东区', 50, 0),
(3545, 361, '乌鲁木齐县', 50, 0),
(3546, 361, '其它区', 60, 0),
(3547, 362, '独山子区', 50, 0),
(3548, 362, '克拉玛依区', 50, 0),
(3549, 362, '白碱滩区', 50, 0),
(3550, 362, '乌尔禾区', 50, 0),
(3551, 362, '其它区', 60, 0),
(3552, 363, '高昌区', 50, 0),
(3553, 363, '鄯善县', 50, 0),
(3554, 363, '托克逊县', 50, 0),
(3555, 363, '其它区', 60, 0),
(3556, 364, '哈密市', 50, 0),
(3557, 364, '巴里坤哈萨克自治县', 50, 0),
(3558, 364, '伊吾县', 50, 0),
(3559, 364, '其它区', 60, 0),
(3560, 365, '昌吉市', 50, 0),
(3561, 365, '阜康市', 50, 0),
(3562, 365, '米泉市', 50, 0),
(3563, 365, '呼图壁县', 50, 0),
(3564, 365, '玛纳斯县', 50, 0),
(3565, 365, '奇台县', 50, 0),
(3566, 365, '吉木萨尔县', 50, 0),
(3567, 365, '木垒哈萨克自治县', 50, 0),
(3568, 365, '其它区', 60, 0),
(3569, 366, '博乐市', 50, 0),
(3570, 366, '阿拉山口市', 50, 0),
(3571, 366, '精河县', 50, 0),
(3572, 366, '温泉县', 50, 0),
(3573, 366, '其它区', 60, 0),
(3574, 367, '库尔勒市', 50, 0),
(3575, 367, '轮台县', 50, 0),
(3576, 367, '尉犁县', 50, 0),
(3577, 367, '若羌县', 50, 0),
(3578, 367, '且末县', 50, 0),
(3579, 367, '焉耆回族自治县', 50, 0),
(3580, 367, '和静县', 50, 0),
(3581, 367, '和硕县', 50, 0),
(3582, 367, '博湖县', 50, 0),
(3583, 367, '其它区', 60, 0),
(3584, 368, '阿克苏市', 50, 0),
(3585, 368, '温宿县', 50, 0),
(3586, 368, '库车县', 50, 0),
(3587, 368, '沙雅县', 50, 0),
(3588, 368, '新和县', 50, 0),
(3589, 368, '拜城县', 50, 0),
(3590, 368, '乌什县', 50, 0),
(3591, 368, '阿瓦提县', 50, 0),
(3592, 368, '柯坪县', 50, 0),
(3593, 368, '其它区', 60, 0),
(3594, 369, '阿图什市', 50, 0),
(3595, 369, '阿克陶县', 50, 0),
(3596, 369, '阿合奇县', 50, 0),
(3597, 369, '乌恰县', 50, 0),
(3598, 369, '其它区', 60, 0),
(3599, 370, '喀什市', 50, 0),
(3600, 370, '疏附县', 50, 0),
(3601, 370, '疏勒县', 50, 0),
(3602, 370, '英吉沙县', 50, 0),
(3603, 370, '泽普县', 50, 0),
(3604, 370, '莎车县', 50, 0),
(3605, 370, '叶城县', 50, 0),
(3606, 370, '麦盖提县', 50, 0),
(3607, 370, '岳普湖县', 50, 0),
(3608, 370, '伽师县', 50, 0),
(3609, 370, '巴楚县', 50, 0),
(3610, 370, '塔什库尔干塔吉克自治县', 50, 0),
(3611, 370, '其它区', 60, 0),
(3612, 371, '和田市', 50, 0),
(3613, 371, '和田县', 50, 0),
(3614, 371, '墨玉县', 50, 0),
(3615, 371, '皮山县', 50, 0),
(3616, 371, '洛浦县', 50, 0),
(3617, 371, '策勒县', 50, 0),
(3618, 371, '于田县', 50, 0),
(3619, 371, '民丰县', 50, 0),
(3620, 371, '其它区', 60, 0),
(3621, 372, '伊宁市', 50, 0),
(3622, 372, '奎屯市', 50, 0),
(3623, 372, '伊宁县', 50, 0),
(3624, 372, '察布查尔锡伯自治县', 50, 0),
(3625, 372, '霍城县', 50, 0),
(3626, 372, '巩留县', 50, 0),
(3627, 372, '新源县', 50, 0),
(3628, 372, '昭苏县', 50, 0),
(3629, 372, '特克斯县', 50, 0),
(3630, 372, '尼勒克县', 50, 0),
(3631, 372, '其它区', 60, 0),
(3632, 373, '塔城市', 50, 0),
(3633, 373, '乌苏市', 50, 0),
(3634, 373, '额敏县', 50, 0),
(3635, 373, '沙湾县', 50, 0),
(3636, 373, '托里县', 50, 0),
(3637, 373, '裕民县', 50, 0),
(3638, 373, '和布克赛尔蒙古自治县', 50, 0),
(3639, 373, '其它区', 60, 0),
(3640, 374, '阿勒泰市', 50, 0),
(3641, 374, '布尔津县', 50, 0),
(3642, 374, '富蕴县', 50, 0),
(3643, 374, '福海县', 50, 0),
(3644, 374, '哈巴河县', 50, 0),
(3645, 374, '青河县', 50, 0),
(3646, 374, '吉木乃县', 50, 0),
(3647, 374, '其它区', 60, 0),
(3648, 375, '中正区', 50, 0),
(3649, 375, '大同区', 50, 0),
(3650, 375, '中山区', 50, 0),
(3651, 375, '松山区', 50, 0),
(3652, 375, '大安区', 50, 0),
(3653, 375, '万华区', 50, 0),
(3654, 375, '信义区', 50, 0),
(3655, 375, '士林区', 50, 0),
(3656, 375, '北投区', 50, 0),
(3657, 375, '内湖区', 50, 0),
(3658, 375, '南港区', 50, 0),
(3659, 375, '文山区', 50, 0),
(3660, 375, '其它区', 60, 0),
(3661, 376, '新兴区', 50, 0),
(3662, 376, '前金区', 50, 0),
(3663, 376, '芩雅区', 50, 0),
(3664, 376, '盐埕区', 50, 0),
(3665, 376, '鼓山区', 50, 0),
(3666, 376, '旗津区', 50, 0),
(3667, 376, '前镇区', 50, 0),
(3668, 376, '三民区', 50, 0),
(3669, 376, '左营区', 50, 0),
(3670, 376, '楠梓区', 50, 0),
(3671, 376, '小港区', 50, 0),
(3672, 376, '其它区', 60, 0),
(3673, 376, '苓雅区', 50, 0),
(3674, 376, '仁武区', 50, 0),
(3675, 376, '大社区', 50, 0),
(3676, 376, '冈山区', 50, 0),
(3677, 376, '路竹区', 50, 0),
(3678, 376, '阿莲区', 50, 0),
(3679, 376, '田寮区', 50, 0),
(3680, 376, '燕巢区', 50, 0),
(3681, 376, '桥头区', 50, 0),
(3682, 376, '梓官区', 50, 0),
(3683, 376, '弥陀区', 50, 0),
(3684, 376, '永安区', 50, 0),
(3685, 376, '湖内区', 50, 0),
(3686, 376, '凤山区', 50, 0),
(3687, 376, '大寮区', 50, 0),
(3688, 376, '林园区', 50, 0),
(3689, 376, '鸟松区', 50, 0),
(3690, 376, '大树区', 50, 0),
(3691, 376, '旗山区', 50, 0),
(3692, 376, '美浓区', 50, 0),
(3693, 376, '六龟区', 50, 0),
(3694, 376, '内门区', 50, 0),
(3695, 376, '杉林区', 50, 0),
(3696, 376, '甲仙区', 50, 0),
(3697, 376, '桃源区', 50, 0),
(3698, 376, '那玛夏区', 50, 0),
(3699, 376, '茂林区', 50, 0),
(3700, 376, '茄萣区', 50, 0),
(3701, 377, '中西区', 50, 0),
(3702, 377, '东区', 50, 0),
(3703, 377, '南区', 50, 0),
(3704, 377, '北区', 50, 0),
(3705, 377, '安平区', 50, 0),
(3706, 377, '安南区', 50, 0),
(3707, 377, '其它区', 60, 0),
(3708, 377, '永康区', 50, 0),
(3709, 377, '归仁区', 50, 0),
(3710, 377, '新化区', 50, 0),
(3711, 377, '左镇区', 50, 0),
(3712, 377, '玉井区', 50, 0),
(3713, 377, '楠西区', 50, 0),
(3714, 377, '南化区', 50, 0),
(3715, 377, '仁德区', 50, 0),
(3716, 377, '关庙区', 50, 0),
(3717, 377, '龙崎区', 50, 0),
(3718, 377, '官田区', 50, 0),
(3719, 377, '麻豆区', 50, 0),
(3720, 377, '佳里区', 50, 0),
(3721, 377, '西港区', 50, 0),
(3722, 377, '七股区', 50, 0),
(3723, 377, '将军区', 50, 0),
(3724, 377, '学甲区', 50, 0),
(3725, 377, '北门区', 50, 0),
(3726, 377, '新营区', 50, 0),
(3727, 377, '后壁区', 50, 0),
(3728, 377, '白河区', 50, 0),
(3729, 377, '东山区', 50, 0),
(3730, 377, '六甲区', 50, 0),
(3731, 377, '下营区', 50, 0),
(3732, 377, '柳营区', 50, 0),
(3733, 377, '盐水区', 50, 0),
(3734, 377, '善化区', 50, 0),
(3735, 377, '大内区', 50, 0),
(3736, 377, '山上区', 50, 0),
(3737, 377, '新市区', 50, 0),
(3738, 377, '安定区', 50, 0),
(3739, 378, '中区', 50, 0),
(3740, 378, '东区', 50, 0),
(3741, 378, '南区', 50, 0),
(3742, 378, '西区', 50, 0),
(3743, 378, '北区', 50, 0),
(3744, 378, '北屯区', 50, 0),
(3745, 378, '西屯区', 50, 0),
(3746, 378, '南屯区', 50, 0),
(3747, 378, '其它区', 60, 0),
(3748, 378, '太平区', 50, 0),
(3749, 378, '大里区', 50, 0),
(3750, 378, '雾峰区', 50, 0),
(3751, 378, '乌日区', 50, 0),
(3752, 378, '丰原区', 50, 0),
(3753, 378, '后里区', 50, 0),
(3754, 378, '石冈区', 50, 0),
(3755, 378, '东势区', 50, 0),
(3756, 378, '和平区', 50, 0),
(3757, 378, '新社区', 50, 0),
(3758, 378, '潭子区', 50, 0),
(3759, 378, '大雅区', 50, 0),
(3760, 378, '神冈区', 50, 0),
(3761, 378, '大肚区', 50, 0),
(3762, 378, '沙鹿区', 50, 0),
(3763, 378, '龙井区', 50, 0),
(3764, 378, '梧栖区', 50, 0),
(3765, 378, '清水区', 50, 0),
(3766, 378, '大甲区', 50, 0),
(3767, 378, '外埔区', 50, 0),
(3768, 378, '大安区', 50, 0),
(3769, 379, '金沙镇', 50, 0),
(3770, 379, '金湖镇', 50, 0),
(3771, 379, '金宁乡', 50, 0),
(3772, 379, '金城镇', 50, 0),
(3773, 379, '烈屿乡', 50, 0),
(3774, 379, '乌坵乡', 50, 0),
(3775, 380, '南投市', 50, 0),
(3776, 380, '中寮乡', 50, 0),
(3777, 380, '草屯镇', 50, 0),
(3778, 380, '国姓乡', 50, 0),
(3779, 380, '埔里镇', 50, 0),
(3780, 380, '仁爱乡', 50, 0),
(3781, 380, '名间乡', 50, 0),
(3782, 380, '集集镇', 50, 0),
(3783, 380, '水里乡', 50, 0),
(3784, 380, '鱼池乡', 50, 0),
(3785, 380, '信义乡', 50, 0),
(3786, 380, '竹山镇', 50, 0),
(3787, 380, '鹿谷乡', 50, 0),
(3788, 381, '仁爱区', 50, 0),
(3789, 381, '信义区', 50, 0),
(3790, 381, '中正区', 50, 0),
(3791, 381, '中山区', 50, 0),
(3792, 381, '安乐区', 50, 0),
(3793, 381, '暖暖区', 50, 0),
(3794, 381, '七堵区', 50, 0),
(3795, 381, '其它区', 60, 0),
(3796, 382, '东区', 50, 0),
(3797, 382, '北区', 50, 0),
(3798, 382, '香山区', 50, 0),
(3799, 382, '其它区', 60, 0),
(3800, 383, '东区', 50, 0),
(3801, 383, '西区', 50, 0),
(3802, 383, '其它区', 60, 0),
(3803, 384, '万里区', 50, 0),
(3804, 384, '金山区', 50, 0),
(3805, 384, '板桥区', 50, 0),
(3806, 384, '汐止区', 50, 0),
(3807, 384, '深坑区', 50, 0),
(3808, 384, '石碇区', 50, 0),
(3809, 384, '瑞芳区', 50, 0),
(3810, 384, '平溪区', 50, 0),
(3811, 384, '双溪区', 50, 0),
(3812, 384, '贡寮区', 50, 0),
(3813, 384, '新店区', 50, 0),
(3814, 384, '坪林区', 50, 0),
(3815, 384, '乌来区', 50, 0),
(3816, 384, '永和区', 50, 0),
(3817, 384, '中和区', 50, 0),
(3818, 384, '土城区', 50, 0),
(3819, 384, '三峡区', 50, 0),
(3820, 384, '树林区', 50, 0),
(3821, 384, '莺歌区', 50, 0),
(3822, 384, '三重区', 50, 0),
(3823, 384, '新庄区', 50, 0),
(3824, 384, '泰山区', 50, 0),
(3825, 384, '林口区', 50, 0),
(3826, 384, '芦洲区', 50, 0),
(3827, 384, '五股区', 50, 0),
(3828, 384, '八里区', 50, 0),
(3829, 384, '淡水区', 50, 0),
(3830, 384, '三芝区', 50, 0),
(3831, 384, '石门区', 50, 0),
(3832, 385, '宜兰市', 50, 0),
(3833, 385, '头城镇', 50, 0),
(3834, 385, '礁溪乡', 50, 0),
(3835, 385, '壮围乡', 50, 0),
(3836, 385, '员山乡', 50, 0),
(3837, 385, '罗东镇', 50, 0),
(3838, 385, '三星乡', 50, 0),
(3839, 385, '大同乡', 50, 0),
(3840, 385, '五结乡', 50, 0),
(3841, 385, '冬山乡', 50, 0),
(3842, 385, '苏澳镇', 50, 0),
(3843, 385, '南澳乡', 50, 0),
(3844, 385, '钓鱼台', 50, 0),
(3845, 386, '竹北市', 50, 0),
(3846, 386, '湖口乡', 50, 0),
(3847, 386, '新丰乡', 50, 0),
(3848, 386, '新埔镇', 50, 0),
(3849, 386, '关西镇', 50, 0),
(3850, 386, '芎林乡', 50, 0),
(3851, 386, '宝山乡', 50, 0),
(3852, 386, '竹东镇', 50, 0),
(3853, 386, '五峰乡', 50, 0),
(3854, 386, '横山乡', 50, 0),
(3855, 386, '尖石乡', 50, 0),
(3856, 386, '北埔乡', 50, 0),
(3857, 386, '峨眉乡', 50, 0),
(3858, 387, '中坜市', 50, 0),
(3859, 387, '平镇市', 50, 0),
(3860, 387, '龙潭乡', 50, 0),
(3861, 387, '杨梅市', 50, 0),
(3862, 387, '新屋乡', 50, 0),
(3863, 387, '观音乡', 50, 0),
(3864, 387, '桃园市', 50, 0),
(3865, 387, '龟山乡', 50, 0),
(3866, 387, '八德市', 50, 0),
(3867, 387, '大溪镇', 50, 0),
(3868, 387, '复兴乡', 50, 0),
(3869, 387, '大园乡', 50, 0),
(3870, 387, '芦竹乡', 50, 0),
(3871, 388, '竹南镇', 50, 0),
(3872, 388, '头份镇', 50, 0),
(3873, 388, '三湾乡', 50, 0),
(3874, 388, '南庄乡', 50, 0),
(3875, 388, '狮潭乡', 50, 0),
(3876, 388, '后龙镇', 50, 0),
(3877, 388, '通霄镇', 50, 0),
(3878, 388, '苑里镇', 50, 0),
(3879, 388, '苗栗市', 50, 0),
(3880, 388, '造桥乡', 50, 0),
(3881, 388, '头屋乡', 50, 0),
(3882, 388, '公馆乡', 50, 0),
(3883, 388, '大湖乡', 50, 0),
(3884, 388, '泰安乡', 50, 0),
(3885, 388, '铜锣乡', 50, 0),
(3886, 388, '三义乡', 50, 0),
(3887, 388, '西湖乡', 50, 0),
(3888, 388, '卓兰镇', 50, 0),
(3889, 389, '彰化市', 50, 0),
(3890, 389, '芬园乡', 50, 0),
(3891, 389, '花坛乡', 50, 0),
(3892, 389, '秀水乡', 50, 0),
(3893, 389, '鹿港镇', 50, 0),
(3894, 389, '福兴乡', 50, 0),
(3895, 389, '线西乡', 50, 0),
(3896, 389, '和美镇', 50, 0),
(3897, 389, '伸港乡', 50, 0),
(3898, 389, '员林镇', 50, 0),
(3899, 389, '社头乡', 50, 0),
(3900, 389, '永靖乡', 50, 0),
(3901, 389, '埔心乡', 50, 0),
(3902, 389, '溪湖镇', 50, 0),
(3903, 389, '大村乡', 50, 0),
(3904, 389, '埔盐乡', 50, 0),
(3905, 389, '田中镇', 50, 0),
(3906, 389, '北斗镇', 50, 0),
(3907, 389, '田尾乡', 50, 0),
(3908, 389, '埤头乡', 50, 0),
(3909, 389, '溪州乡', 50, 0),
(3910, 389, '竹塘乡', 50, 0),
(3911, 389, '二林镇', 50, 0),
(3912, 389, '大城乡', 50, 0),
(3913, 389, '芳苑乡', 50, 0),
(3914, 389, '二水乡', 50, 0),
(3915, 390, '番路乡', 50, 0),
(3916, 390, '梅山乡', 50, 0),
(3917, 390, '竹崎乡', 50, 0),
(3918, 390, '阿里山乡', 50, 0),
(3919, 390, '中埔乡', 50, 0),
(3920, 390, '大埔乡', 50, 0),
(3921, 390, '水上乡', 50, 0),
(3922, 390, '鹿草乡', 50, 0),
(3923, 390, '太保市', 50, 0),
(3924, 390, '朴子市', 50, 0),
(3925, 390, '东石乡', 50, 0),
(3926, 390, '六脚乡', 50, 0),
(3927, 390, '新港乡', 50, 0),
(3928, 390, '民雄乡', 50, 0),
(3929, 390, '大林镇', 50, 0),
(3930, 390, '溪口乡', 50, 0),
(3931, 390, '义竹乡', 50, 0),
(3932, 390, '布袋镇', 50, 0),
(3933, 391, '斗南镇', 50, 0),
(3934, 391, '大埤乡', 50, 0),
(3935, 391, '虎尾镇', 50, 0),
(3936, 391, '土库镇', 50, 0),
(3937, 391, '褒忠乡', 50, 0),
(3938, 391, '东势乡', 50, 0),
(3939, 391, '台西乡', 50, 0),
(3940, 391, '仑背乡', 50, 0),
(3941, 391, '麦寮乡', 50, 0),
(3942, 391, '斗六市', 50, 0),
(3943, 391, '林内乡', 50, 0),
(3944, 391, '古坑乡', 50, 0),
(3945, 391, '莿桐乡', 50, 0),
(3946, 391, '西螺镇', 50, 0),
(3947, 391, '二仑乡', 50, 0),
(3948, 391, '北港镇', 50, 0),
(3949, 391, '水林乡', 50, 0),
(3950, 391, '口湖乡', 50, 0),
(3951, 391, '四湖乡', 50, 0),
(3952, 391, '元长乡', 50, 0),
(3953, 392, '屏东市', 50, 0),
(3954, 392, '三地门乡', 50, 0),
(3955, 392, '雾台乡', 50, 0),
(3956, 392, '玛家乡', 50, 0),
(3957, 392, '九如乡', 50, 0),
(3958, 392, '里港乡', 50, 0),
(3959, 392, '高树乡', 50, 0),
(3960, 392, '盐埔乡', 50, 0),
(3961, 392, '长治乡', 50, 0),
(3962, 392, '麟洛乡', 50, 0),
(3963, 392, '竹田乡', 50, 0),
(3964, 392, '内埔乡', 50, 0),
(3965, 392, '万丹乡', 50, 0),
(3966, 392, '潮州镇', 50, 0),
(3967, 392, '泰武乡', 50, 0),
(3968, 392, '来义乡', 50, 0),
(3969, 392, '万峦乡', 50, 0),
(3970, 392, '崁顶乡', 50, 0),
(3971, 392, '新埤乡', 50, 0),
(3972, 392, '南州乡', 50, 0),
(3973, 392, '林边乡', 50, 0),
(3974, 392, '东港镇', 50, 0),
(3975, 392, '琉球乡', 50, 0),
(3976, 392, '佳冬乡', 50, 0),
(3977, 392, '新园乡', 50, 0),
(3978, 392, '枋寮乡', 50, 0),
(3979, 392, '枋山乡', 50, 0),
(3980, 392, '春日乡', 50, 0),
(3981, 392, '狮子乡', 50, 0),
(3982, 392, '车城乡', 50, 0),
(3983, 392, '牡丹乡', 50, 0),
(3984, 392, '恒春镇', 50, 0),
(3985, 392, '满州乡', 50, 0),
(3986, 393, '台东市', 50, 0),
(3987, 393, '绿岛乡', 50, 0),
(3988, 393, '兰屿乡', 50, 0),
(3989, 393, '延平乡', 50, 0),
(3990, 393, '卑南乡', 50, 0),
(3991, 393, '鹿野乡', 50, 0),
(3992, 393, '关山镇', 50, 0),
(3993, 393, '海端乡', 50, 0),
(3994, 393, '池上乡', 50, 0),
(3995, 393, '东河乡', 50, 0),
(3996, 393, '成功镇', 50, 0),
(3997, 393, '长滨乡', 50, 0),
(3998, 393, '金峰乡', 50, 0),
(3999, 393, '大武乡', 50, 0),
(4000, 393, '达仁乡', 50, 0),
(4001, 393, '太麻里乡', 50, 0),
(4002, 394, '花莲市', 50, 0),
(4003, 394, '新城乡', 50, 0),
(4004, 394, '太鲁阁', 50, 0),
(4005, 394, '秀林乡', 50, 0),
(4006, 394, '吉安乡', 50, 0),
(4007, 394, '寿丰乡', 50, 0),
(4008, 394, '凤林镇', 50, 0),
(4009, 394, '光复乡', 50, 0),
(4010, 394, '丰滨乡', 50, 0),
(4011, 394, '瑞穗乡', 50, 0),
(4012, 394, '万荣乡', 50, 0),
(4013, 394, '玉里镇', 50, 0),
(4014, 394, '卓溪乡', 50, 0),
(4015, 394, '富里乡', 50, 0),
(4016, 395, '马公市', 50, 0),
(4017, 395, '西屿乡', 50, 0),
(4018, 395, '望安乡', 50, 0),
(4019, 395, '七美乡', 50, 0),
(4020, 395, '白沙乡', 50, 0),
(4021, 395, '湖西乡', 50, 0),
(4022, 396, '南竿乡', 50, 0),
(4023, 396, '北竿乡', 50, 0),
(4024, 396, '莒光乡', 50, 0),
(4025, 396, '东引乡', 50, 0),
(4026, 397, '中西区', 50, 0),
(4027, 397, '湾仔', 50, 0),
(4028, 397, '东区', 50, 0),
(4029, 397, '南区', 50, 0),
(4030, 398, '九龙城区', 50, 0),
(4031, 398, '油尖旺区', 50, 0),
(4032, 398, '深水埗区', 50, 0),
(4033, 398, '黄大仙区', 50, 0),
(4034, 398, '观塘区', 50, 0),
(4035, 399, '北区', 50, 0),
(4036, 399, '大埔区', 50, 0),
(4037, 399, '沙田区', 50, 0),
(4038, 399, '西贡区', 50, 0),
(4039, 399, '元朗区', 50, 0),
(4040, 399, '屯门区', 50, 0),
(4041, 399, '荃湾区', 50, 0),
(4042, 399, '葵青区', 50, 0),
(4043, 399, '离岛区', 50, 0),
(4044, 124, '江北区', 49, 0),
(4080, 1, '钓鱼岛', 35, 0),
(4081, 4080, '钓鱼岛', 50, 0);
INSERT INTO `cs_region` (`region_id`, `parent_id`, `region_name`, `sort`, `is_delete`) VALUES
(4083, 1, '海外', 36, 0),
(4084, 4083, '海外', 50, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cs_service_log`
--

CREATE TABLE `cs_service_log` (
  `service_log_id` int(11) UNSIGNED NOT NULL,
  `order_service_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应order_service表',
  `service_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '售后单号',
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作者',
  `client_type` tinyint(1) NOT NULL COMMENT '-1=游客 0=顾客 1=管理组',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '具体说明',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='售后日志';

--
-- 插入之前先把表清空（truncate） `cs_service_log`
--

TRUNCATE TABLE `cs_service_log`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_setting`
--

CREATE TABLE `cs_setting` (
  `setting_id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量名',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量值',
  `module` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模块(作用域)',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `help_text` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '帮助'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置';

--
-- 插入之前先把表清空（truncate） `cs_setting`
--

TRUNCATE TABLE `cs_setting`;
--
-- 转存表中的数据 `cs_setting`
--

INSERT INTO `cs_setting` (`setting_id`, `code`, `value`, `module`, `description`, `help_text`) VALUES
(1, 'api_id', '', 'delivery_dist', '快递鸟商户ID', '填写快递鸟 <span style=\"color:#F56C6C;\">商户ID</span>'),
(2, 'api_key', '', 'delivery_dist', '快递鸟ApiKey', '填写快递鸟 <span style=\"color:#F56C6C;\">API key</span>'),
(3, 'is_sub', '1', 'delivery_dist', '是否启用订阅', '开启订阅后快递鸟会主动将配送轨迹推送到系统'),
(4, 'success', 'http://www.careyshop.cn/', 'payment', '支付成功提示页', '支付成功后返回到的页面'),
(5, 'error', 'http://www.careyshop.cn/', 'payment', '支付失败提示页', '支付失败后返回到的页面'),
(6, 'sms', '{\"key_id\":{\"name\":\"Access Key ID\",\"value\":\"\"},\"key_secret\":{\"name\":\"Access Key Secret\",\"value\":\"\"},\"status\":{\"name\":\"启用状态\",\"value\":\"1\"}}', 'notice', '短信通知', '短信通知参数配置'),
(7, 'email', '{\"email_host\":{\"name\":\"SMTP服务器\",\"value\":\"\"},\"email_port\":{\"name\":\"SMTP端口\",\"value\":\"\"},\"email_addr\":{\"name\":\"发信人邮箱地址\",\"value\":\"\"},\"email_id\":{\"name\":\"SMTP身份验证用户名\",\"value\":\"\"},\"email_pass\":{\"name\":\"SMTP身份验证码\",\"value\":\"\"},\"email_ssl\":{\"name\":\"是否使用安全链接\",\"value\":\"0\"},\"status\":{\"name\":\"启用状态\",\"value\":\"0\"}}', 'notice', '邮件通知', '邮件通知参数配置'),
(8, 'money', '0', 'delivery', '满额包邮', '满多少金额启用全场包邮'),
(9, 'money_status', '0', 'delivery', '满额是否启用', '满额是否启用'),
(10, 'money_exclude', '[]', 'delivery', '满额不包邮区域', '满额不包邮区域'),
(11, 'number', '0', 'delivery', '满件包邮', '满多少件商品启用全场包邮'),
(12, 'number_status', '0', 'delivery', '满件是否启用', '满件是否启用'),
(13, 'number_exclude', '[]', 'delivery', '满件不包邮区域', '满件不包邮区域'),
(14, 'quota', '0', 'delivery', '满额减运费', '满多少金额启用全场减运费'),
(15, 'dec_money', '0', 'delivery', '满额减多少运费', '满额减多少运费'),
(16, 'dec_status', '0', 'delivery', '满额减是否启用', '满额减是否启用'),
(17, 'dec_exclude', '[]', 'delivery', '满额减排除区域', '满额减排除区域'),
(18, 'withdraw_fee', '6.5', 'system_shopping', '提现手续费(%)', '申请提现时系统收取的手续费，按总金额的百分比换算'),
(19, 'integral', '100', 'system_shopping', '积分换算比例(1:x)', '积分可抵扣金额按多少比例换算，例如 1:100'),
(20, 'timeout', '30', 'system_shopping', '下单后未付款自动取消(分)', '订单下单后超过多少分钟自动取消订单，单位：分'),
(21, 'complete', '10', 'system_shopping', '发货几天后自动确认收货(天)', '订单发货多少天后自动确认收货，单位：天'),
(22, 'is_country', '0', 'system_shopping', '完整地址是否包含国籍', '生成完整地址时是否包含国籍'),
(23, 'spacer', ' ', 'system_shopping', '完整地址分隔符', '生成完整地址时省市区之间的分隔符，例如 浙江省<span style=\"color:#F56C6C;\">-</span>宁波市'),
(24, 'invoice', '3', 'system_shopping', '开票收取多少税率(%)', '开票收取多少税率，按票面总金额的百分比换算'),
(25, 'source', '{\"0\":{\"name\":\"电脑端\",\"icon\":\"diannao_o\"},\"1\":{\"name\":\"移动端\",\"icon\":\"shouji1_o\"},\"2\":{\"name\":\"小程序\",\"icon\":\"xiaochengxu_o\"},\"3\":{\"name\":\"微信\",\"icon\":\"weixin_o\"}}', 'system_shopping', '订单来源自定义', ''),
(26, 'days', '15', 'service', '有效维权期天数(天)', '订单完成后多少天内允许申请售后服务，单位：天'),
(27, 'address', '', 'service', '退换货地址', '售后服务退换货的详细地址'),
(28, 'consignee', '', 'service', '退换货收件人', '售后服务退换货的收件人姓名'),
(29, 'zipcode', '', 'service', '退换货邮编', '售后服务退换货地址的邮编'),
(30, 'mobile', '', 'service', '退换货联系电话', '售后服务退换货收件人的联系电话'),
(31, 'platform', '{\"0\":\"all\",\"1\":\"pc\",\"2\":\"mobile\",\"3\":\"ios\",\"4\":\"android\"}', 'system_info', '平台自定义值', ''),
(32, 'open_index', '0', 'system_info', '是否开启首页', ''),
(33, 'open_api', '1', 'system_info', '是否开启API接口', ''),
(34, 'open_mobile', '0', 'system_info', '是否开启移动页', ''),
(35, 'close_reason', '系统维护中，请稍后访问！', 'system_info', 'API接口关闭原因', ''),
(36, 'allow_origin', '[\"*\"]', 'system_info', '允许跨域访问的域名', ''),
(37, 'name', 'CarayShop商城', 'system_info', '商城名称', '商城名称，将显示在前台顶部欢迎信息等位置'),
(38, 'title', 'CarayShop商城框架系统', 'system_info', '商城标题', '商城标题，将显示在前台顶部欢迎信息等位置'),
(39, 'keywords', '开源新零售，开源小程序，开源微商城，开源商城，商城系统，免费商城', 'system_info', '商城关键词', '商城关键词，有利于对整站的SEO优化'),
(40, 'description', 'CareyShop（简称CS）是一套基于ThinkPHP5框架开发的高性能商城框架系统，秉承简洁、快速、极致的开发理念，对内使用面向对象模块化调用，多终端、跨平台采用REST API构架来面向移动网络趋势，可直接对接PC、移动设备、小程序、云部署，构建Android、IOS的APP。', 'system_info', '商城描述', '商城描述，将显示在前台顶部欢迎信息等位置'),
(41, 'logo', 'aliyun.oss.careyshop.cn/uploads/files/20200330/4dd3960b-486c-4dfb-8c86-0e424ac61c32.png?type=aliyun', 'system_info', '商城LOGO', '默认商城LOGO，通用头部显示，最佳显示尺寸为240*60像素'),
(42, 'square_logo', 'aliyun.oss.careyshop.cn/uploads/files/20200403/8b85fe59-7481-48dc-82ef-592d281b7d4b.png?type=aliyun', 'system_info', '方形LOGO', '方形商城LOGO，通用移动端居多，最佳显示尺寸为80*80像素'),
(43, 'information', '联系电话：400-XXXXXXXX\n联系地址：xxx xxx xxx xxxxx\n如果您需要将商品寄回，请将本单据一同附上。', 'system_info', '发货信息', '打印发货单时预留的信息'),
(44, 'card_auth', '[]', 'system_info', '购物卡权限', '设置允许查看购物卡卡密的管理组账号'),
(45, 'third_count', '<script>\r\nvar _hmt = _hmt || [];\r\n(function() {\r\n  var hm = document.createElement(\"script\");\r\n  hm.src = \"https://hm.baidu.com/hm.js?e325e60ca4cd358f2b424f5aecb8021a\";\r\n  var s = document.getElementsByTagName(\"script\")[0]; \r\n  s.parentNode.insertBefore(hm, s);\r\n})();</script>', 'system_info', '第三方统计代码', '第三方统计的脚本代码'),
(46, 'miitbeian', '', 'system_info', 'ICP备案许可证号', 'ICP备案许可证号，将显示在前台底部等位置'),
(47, 'miitbeian_url', 'http://www.beian.miit.gov.cn', 'system_info', 'ICP备案链接地址', 'ICP备案链接地址，点击后将引导到该网站'),
(48, 'miitbeian_ico', '', 'system_info', 'ICP备案图标', 'ICP备案图标，将显示在前台底部等位置'),
(49, 'beian', '', 'system_info', '公安机关备案号', '公安机关备案号，将显示在前台底部等位置'),
(50, 'beian_url', 'http://www.beian.gov.cn', 'system_info', '公安机关备案链接', '公安机关备案链接，点击后将引导到该网站'),
(51, 'beian_ico', '', 'system_info', '公安机关备案图标', '公安机关备案图标，将显示在前台底部等位置'),
(52, 'weixin_url', 'http://www.careyshop.cn/', 'system_info', '移动中间页地址', '例如微信中无法访问实际地址，可通过该地址进行跳转'),
(53, 'qrcode_logo', 'static/api/images/qrcode_logo.png', 'system_info', '二维码LOGO', '生成二维码时默认的LOGO，可使用 <span style=\"color:#F56C6C;\">路径</span> 或 <span style=\"color:#F56C6C;\">网址</span>'),
(54, 'default', 'careyshop', 'upload', '默认资源上传模块', '在不指定上传模块时，系统默认启用的上传模块'),
(55, 'oss', 'careyshop.cn/oss?url=', 'upload', '资源获取短地址', '可启用短地址获取资源，避免原地址冗长'),
(56, 'image_ext', 'jpg,png,svg,gif,bmp,tiff,webp', 'upload', '允许上传的图片后缀', '设置的后缀对 <strong>上传模块</strong> <span style=\"color:#F56C6C;\">不一定支持生成缩略图</span>，需要实际测试'),
(57, 'file_ext', 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip,gz,bz2,7z,pem,mp4,ogg,webm,ico', 'upload', '允许上传的文件后缀', ''),
(58, 'file_size', '5M', 'upload', '附件上传大小限制', ''),
(59, 'token_expires', '300', 'upload', '上传令牌有效时长(秒)', '获得上传令牌后多少秒后视为无效'),
(60, 'careyshop_url', '', 'upload', '资源绑定域名别名', 'CareyShop(本地上传)模块绑定资源目录域名，资源Host路径将变为该值'),
(61, 'qiniu_access_key', '', 'upload', 'AccessKey(AK)', '七牛云密钥管理创建的 <span style=\"color:#F56C6C;\">AK</span>'),
(62, 'qiniu_secret_key', '', 'upload', 'SecretKey(SK)', '七牛云密钥管理创建的 <span style=\"color:#F56C6C;\">SK</span>'),
(63, 'qiniu_bucket', '', 'upload', '存储空间名称', '选择一个存储空间，请保证访问控制为 <span style=\"color:#F56C6C;\">公开空间</span>'),
(64, 'qiniu_url', '', 'upload', '外链域名', '存储空间 <span style=\"color:#F56C6C;\">对外访问域名</span>，也支持填写 <span style=\"color:#F56C6C;\">自定义域名</span>'),
(65, 'aliyun_access_key', '', 'upload', 'AccessKey ID', '阿里云RAM子用户创建的 <span style=\"color:#F56C6C;\">AccessKey ID</span> 值'),
(66, 'aliyun_secret_key', '', 'upload', 'AccessKey Secret', '阿里云RAM子用户创建的 <span style=\"color:#F56C6C;\">AccessKey Secret</span> 值'),
(67, 'aliyun_bucket', '', 'upload', 'Bucket 名称', '选择一个Bucket，请保证读写权限为 <span style=\"color:#F56C6C;\">公共读</span>'),
(68, 'aliyun_url', '', 'upload', 'Bucket 域名', '外网访问 <span style=\"color:#F56C6C;\">Bucket域名</span>，也支持填写 <span style=\"color:#F56C6C;\">用户域名</span>'),
(69, 'aliyun_endpoint', '', 'upload', 'EndPoint', '外网访问 <span style=\"color:#F56C6C;\">EndPoint (地域节点)</span>'),
(70, 'aliyun_rolearn', '', 'upload', 'RoleArn', '阿里云RAM角色创建的 <span style=\"color:#F56C6C;\">ARN</span>');

-- --------------------------------------------------------

--
-- 表的结构 `cs_spec`
--

CREATE TABLE `cs_spec` (
  `spec_id` int(11) UNSIGNED NOT NULL,
  `goods_type_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应goods_type表 0=自定义',
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规格名称',
  `spec_index` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否检索 0=否 1=是',
  `spec_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=文字 1=图片 2=颜色',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品规格';

--
-- 插入之前先把表清空（truncate） `cs_spec`
--

TRUNCATE TABLE `cs_spec`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_spec_config`
--

CREATE TABLE `cs_spec_config` (
  `spec_config_id` int(11) UNSIGNED NOT NULL,
  `goods_id` int(11) UNSIGNED NOT NULL COMMENT '对应goods表',
  `config_data` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置数据'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品规格配置';

--
-- 插入之前先把表清空（truncate） `cs_spec_config`
--

TRUNCATE TABLE `cs_spec_config`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_spec_goods`
--

CREATE TABLE `cs_spec_goods` (
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应goods表',
  `key_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规格键名',
  `key_value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规格值',
  `price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `store_qty` int(11) NOT NULL DEFAULT '0' COMMENT '库存数量',
  `bar_code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品条码',
  `goods_sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品SKU'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品规格列表';

--
-- 插入之前先把表清空（truncate） `cs_spec_goods`
--

TRUNCATE TABLE `cs_spec_goods`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_spec_image`
--

CREATE TABLE `cs_spec_image` (
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应goods表',
  `spec_item_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应spec_item表',
  `spec_type` tinyint(1) UNSIGNED NOT NULL COMMENT '1=图片 2=颜色',
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品规格图片',
  `color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品规格颜色'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品规格图片';

--
-- 插入之前先把表清空（truncate） `cs_spec_image`
--

TRUNCATE TABLE `cs_spec_image`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_spec_item`
--

CREATE TABLE `cs_spec_item` (
  `spec_item_id` int(11) UNSIGNED NOT NULL,
  `spec_id` int(11) UNSIGNED NOT NULL COMMENT '对应spec表',
  `item_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '项名',
  `is_contact` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关联 0=否 1=是',
  `sort` tinyint(3) NOT NULL DEFAULT '50' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品规格项';

--
-- 插入之前先把表清空（truncate） `cs_spec_item`
--

TRUNCATE TABLE `cs_spec_item`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_storage`
--

CREATE TABLE `cs_storage` (
  `storage_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `mime` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'mime',
  `ext` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '后缀',
  `size` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '大小',
  `pixel` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '像素',
  `hash` char(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '哈希值',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '外链',
  `protocol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '协议',
  `type` tinyint(1) NOT NULL COMMENT '0=图片 1=附件 2=目录 3=视频',
  `priority` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优先权 0>1',
  `cover` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '封面',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认目录 0=否 1=是',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资源管理器';

--
-- 插入之前先把表清空（truncate） `cs_storage`
--

TRUNCATE TABLE `cs_storage`;
--
-- 转存表中的数据 `cs_storage`
--

INSERT INTO `cs_storage` (`storage_id`, `parent_id`, `name`, `mime`, `ext`, `size`, `pixel`, `hash`, `path`, `url`, `protocol`, `type`, `priority`, `cover`, `sort`, `is_default`, `create_time`, `update_time`) VALUES
(1, 0, '会员等级', '', '', 0, '', '', '', '', '', 2, 0, '', 50, 0, 1588922794, 1588926835),
(2, 0, '支付图标', '', '', 0, '', '', '', '', '', 2, 0, '', 50, 0, 1588922800, 1588926835),
(3, 1, 'level1.png', 'image/png', 'png', 417, '{\"width\":16,\"height\":16}', 'F41831546A34BE42C34343FD92492C67', '会员等级/level1.png', 'aliyun.oss.careyshop.cn/会员等级/level1.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923087, 1588923087),
(4, 1, 'level2.png', 'image/png', 'png', 408, '{\"width\":16,\"height\":16}', 'E61AF90BB05B24B06514F8300674BD96', '会员等级/level2.png', 'aliyun.oss.careyshop.cn/会员等级/level2.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923087, 1588923087),
(5, 1, 'level3.png', 'image/png', 'png', 406, '{\"width\":16,\"height\":16}', '578C74719EF4D67B7426A2AB3B481173', '会员等级/level3.png', 'aliyun.oss.careyshop.cn/会员等级/level3.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923087, 1588923087),
(6, 1, 'level4.png', 'image/png', 'png', 401, '{\"width\":16,\"height\":16}', '8C1D64E3514293AA87E713727965A965', '会员等级/level4.png', 'aliyun.oss.careyshop.cn/会员等级/level4.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923087, 1588923087),
(7, 1, 'level5.png', 'image/png', 'png', 686, '{\"width\":16,\"height\":16}', '670A286772BB8571218CE91A881DB004', '会员等级/level5.png', 'aliyun.oss.careyshop.cn/会员等级/level5.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923087, 1588923087),
(8, 1, 'level6.png', 'image/png', 'png', 1118, '{\"width\":52,\"height\":16}', '94E46D065664D0FCAC16C36609FD4B5A', '会员等级/level6.png', 'aliyun.oss.careyshop.cn/会员等级/level6.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923087, 1588923087),
(9, 2, 'alipay.gif', 'image/gif', 'gif', 3021, '{\"width\":130,\"height\":40}', '09E09591677CDF421EC853CFCAA6B9C3', '支付图标/alipay.gif', 'aliyun.oss.careyshop.cn/支付图标/alipay.gif?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(10, 2, 'hdfk.gif', 'image/gif', 'gif', 2120, '{\"width\":130,\"height\":40}', '41993EF1E4FF5CE64F60BD66714A53D3', '支付图标/hdfk.gif', 'aliyun.oss.careyshop.cn/支付图标/hdfk.gif?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(11, 2, 'jd.png', 'image/png', 'png', 2113, '{\"width\":86,\"height\":50}', 'B8C109758B5334BDDCDB4FCE71E73554', '支付图标/jd.png', 'aliyun.oss.careyshop.cn/支付图标/jd.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(12, 2, 'baidu.png', 'image/png', 'png', 1789, '{\"width\":86,\"height\":50}', 'CC35A174F72A0BDD4C41170194C44426', '支付图标/baidu.png', 'aliyun.oss.careyshop.cn/支付图标/baidu.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(13, 2, 'paypal.jpg', 'image/jpeg', 'jpg', 15141, '{\"width\":130,\"height\":37}', '85952C58A935D24EE51CF91429BD5DF3', '支付图标/paypal.jpg', 'aliyun.oss.careyshop.cn/支付图标/paypal.jpg?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(14, 2, 'malipay.gif', 'image/gif', 'gif', 14727, '{\"width\":130,\"height\":40}', '66233678DF31634B26F04491A2CE5850', '支付图标/malipay.gif', 'aliyun.oss.careyshop.cn/支付图标/malipay.gif?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(15, 2, 'qq.png', 'image/png', 'png', 3627, '{\"width\":86,\"height\":50}', '7B6E41A80BC2E80A9B1BD3A2388EE7ED', '支付图标/qq.png', 'aliyun.oss.careyshop.cn/支付图标/qq.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(16, 2, 'weixin.png', 'image/png', 'png', 1712, '{\"width\":86,\"height\":50}', '752F1A30D6A86FF83D329FB031DDC8CC', '支付图标/weixin.png', 'aliyun.oss.careyshop.cn/支付图标/weixin.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(17, 2, 'wxpay.png', 'image/png', 'png', 52342, '{\"width\":130,\"height\":35}', 'DD81FAE66BA72F31847260BB00AD3C9C', '支付图标/wxpay.png', 'aliyun.oss.careyshop.cn/支付图标/wxpay.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(18, 2, 'xxzf.gif', 'image/gif', 'gif', 2347, '{\"width\":130,\"height\":40}', '3096FBFAF4FD5BA3CAF99C16ED9D17A4', '支付图标/xxzf.gif', 'aliyun.oss.careyshop.cn/支付图标/xxzf.gif?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(19, 2, 'wxmpay.png', 'image/png', 'png', 53092, '{\"width\":130,\"height\":35}', '8A4C4B15AE3C3906A58BED143D8B5018', '支付图标/wxmpay.png', 'aliyun.oss.careyshop.cn/支付图标/wxmpay.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(20, 2, 'wxh5pay.png', 'image/png', 'png', 53092, '{\"width\":130,\"height\":35}', '8A4C4B15AE3C3906A58BED143D8B5018', '支付图标/wxh5pay.png', 'aliyun.oss.careyshop.cn/支付图标/wxh5pay.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(21, 2, 'yezf.gif', 'image/gif', 'gif', 1128, '{\"width\":130,\"height\":40}', 'CE6885A00CB0CD1172E14CEAE2A2854E', '支付图标/yezf.gif', 'aliyun.oss.careyshop.cn/支付图标/yezf.gif?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(22, 2, 'yinlian.png', 'image/png', 'png', 3263, '{\"width\":86,\"height\":50}', 'EA74308D5EA06E0B7AB9A68DD06522A6', '支付图标/yinlian.png', 'aliyun.oss.careyshop.cn/支付图标/yinlian.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112),
(23, 2, 'zhifubao.png', 'image/png', 'png', 1751, '{\"width\":86,\"height\":50}', '444B8960B1A5205DD3A3F10C7CE96621', '支付图标/zhifubao.png', 'aliyun.oss.careyshop.cn/支付图标/zhifubao.png?type=aliyun', 'aliyun', 0, 1, '', 50, 0, 1588923112, 1588923112);

-- --------------------------------------------------------

--
-- 表的结构 `cs_storage_style`
--

CREATE TABLE `cs_storage_style` (
  `storage_style_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '编码',
  `platform` tinyint(3) NOT NULL COMMENT '平台(自定义)',
  `scale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '缩放规格',
  `resize` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '缩放方式',
  `quality` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片质量',
  `suffix` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '输出格式',
  `style` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '第三方样式',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资源样式';

--
-- 插入之前先把表清空（truncate） `cs_storage_style`
--

TRUNCATE TABLE `cs_storage_style`;
--
-- 转存表中的数据 `cs_storage_style`
--

INSERT INTO `cs_storage_style` (`storage_style_id`, `name`, `code`, `platform`, `scale`, `resize`, `quality`, `suffix`, `style`, `status`) VALUES
(1, '正文内容图片 790*0', 'inside_content', 0, '{\"pc\":{\"size\":[790,0],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[480,0],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(2, '文章列表封面 150*0', 'article_lists', 0, '{\"pc\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(3, '资源管理列表 158*158', 'storage_lists', 0, '{\"pc\":{\"size\":[158,158],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[158,158],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(4, '友情链接图片 150*0', 'link_image', 0, '{\"pc\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(5, '用户头像上传 150*0', 'head_pic', 0, '{\"pc\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(6, '商品分类图片 150*0', 'goods_category', 0, '{\"pc\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(7, '商品品牌图片 150*0', 'goods_brand', 0, '{\"pc\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(8, '商品属性图片 150*0', 'goods_attribute', 0, '{\"pc\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[150,0],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(9, '评价晒照片 40*40', 'comment_thumb_x40', 0, '{\"pc\":{\"size\":[40,40],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[40,40],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(11, '商品缩略图 80*80', 'goods_image_x80', 0, '{\"pc\":{\"size\":[80,80],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[80,80],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(12, '商品缩略图 480*480', 'goods_image_x480', 0, '{\"pc\":{\"size\":[480,480],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[480,480],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1),
(13, '商品缩略图 800*800', 'goods_image_x800', 0, '{\"pc\":{\"size\":[800,800],\"crop\":[0,0],\"slider\":0,\"order\":true},\"mobile\":{\"size\":[800,800],\"crop\":[0,0],\"slider\":0,\"order\":true}}', 'scaling', 100, '', '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `cs_support`
--

CREATE TABLE `cs_support` (
  `support_id` smallint(5) UNSIGNED NOT NULL,
  `type_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '客服组名称',
  `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `code` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '联系方式',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客服';

--
-- 插入之前先把表清空（truncate） `cs_support`
--

TRUNCATE TABLE `cs_support`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_token`
--

CREATE TABLE `cs_token` (
  `token_id` int(11) UNSIGNED NOT NULL,
  `client_id` int(11) UNSIGNED NOT NULL COMMENT '编号',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户组Id',
  `username` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号',
  `client_type` tinyint(1) UNSIGNED NOT NULL COMMENT '0=顾客 1=管理组',
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '来源终端',
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '随机密钥',
  `token` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '授权码',
  `token_expires` int(11) NOT NULL COMMENT '授权码过期时间',
  `refresh` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '刷新授权码',
  `refresh_expires` int(11) NOT NULL COMMENT '刷新授权码过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='token';

--
-- 插入之前先把表清空（truncate） `cs_token`
--

TRUNCATE TABLE `cs_token`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_topic`
--

CREATE TABLE `cs_topic` (
  `topic_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `alias` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='专题';

--
-- 插入之前先把表清空（truncate） `cs_topic`
--

TRUNCATE TABLE `cs_topic`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_transaction`
--

CREATE TABLE `cs_transaction` (
  `transaction_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT '0' COMMENT '对应user表',
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作者',
  `type` tinyint(1) NOT NULL COMMENT '0=收入 1=支出',
  `amount` decimal(10,2) NOT NULL COMMENT '交易金额',
  `balance` decimal(10,2) NOT NULL COMMENT '剩余余额',
  `source_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '来源订单号',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `cause` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '原因',
  `module` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'points=积分 money=余额 card=购物卡',
  `to_payment` tinyint(1) NOT NULL COMMENT '支付方式',
  `card_number` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '购物卡卡号',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='交易结算日志';

--
-- 插入之前先把表清空（truncate） `cs_transaction`
--

TRUNCATE TABLE `cs_transaction`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_user`
--

CREATE TABLE `cs_user` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号',
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机',
  `is_mobile` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否验证 0=否 1=是',
  `email` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `is_email` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否验证 0=否 1=是',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `head_pic` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=保密 1=男 2=女',
  `birthday` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '生日',
  `level_icon` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '等级图标',
  `user_level_id` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '对应user_level表',
  `user_address_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user_address表',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户组Id',
  `last_login` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录日期',
  `last_ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='顾客组账号';

--
-- 插入之前先把表清空（truncate） `cs_user`
--

TRUNCATE TABLE `cs_user`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_user_address`
--

CREATE TABLE `cs_user_address` (
  `user_address_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `consignee` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `country` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '国家',
  `region_list` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '区域列表',
  `region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所在地区',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '详细地址',
  `zipcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '邮编',
  `tel` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机号码',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='账号收货地址';

--
-- 插入之前先把表清空（truncate） `cs_user_address`
--

TRUNCATE TABLE `cs_user_address`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_user_level`
--

CREATE TABLE `cs_user_level` (
  `user_level_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '等级名称',
  `icon` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `amount` decimal(10,2) NOT NULL COMMENT '消费金额',
  `discount` tinyint(3) NOT NULL COMMENT '折扣',
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '等级描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='账号等级';

--
-- 插入之前先把表清空（truncate） `cs_user_level`
--

TRUNCATE TABLE `cs_user_level`;
--
-- 转存表中的数据 `cs_user_level`
--

INSERT INTO `cs_user_level` (`user_level_id`, `name`, `icon`, `amount`, `discount`, `description`) VALUES
(1, '青铜会员', 'http://aliyun.oss.careyshop.cn/会员等级/level1.png?type=aliyun', '0.00', 100, '青铜会员'),
(2, '白银会员', 'http://aliyun.oss.careyshop.cn/会员等级/level2.png?type=aliyun', '10000.00', 98, '白银会员累计消费满10000，全场享9.8折优惠'),
(3, '黄金会员', 'http://aliyun.oss.careyshop.cn/会员等级/level3.png?type=aliyun', '30000.00', 95, '黄金会员累计消费满30000，全场享9.5折优惠'),
(4, '铂金会员', 'http://aliyun.oss.careyshop.cn/会员等级/level4.png?type=aliyun', '50000.00', 92, '铂金会员累计消费满50000，全场享9.2折优惠'),
(5, '钻石会员', 'http://aliyun.oss.careyshop.cn/会员等级/level5.png?type=aliyun', '100000.00', 90, '钻石会员累计消费满100000，全场享9折优惠'),
(6, '至尊 VIP', 'http://aliyun.oss.careyshop.cn/会员等级/level6.png?type=aliyun', '200000.00', 88, '至尊VIP累计消费满200000，全场享8.8折优惠');

-- --------------------------------------------------------

--
-- 表的结构 `cs_user_money`
--

CREATE TABLE `cs_user_money` (
  `user_money_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `total_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '累计消费',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用余额',
  `lock_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '锁定余额',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '账号积分',
  `lock_points` int(11) NOT NULL DEFAULT '0' COMMENT '锁定积分'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='账号资金表';

--
-- 插入之前先把表清空（truncate） `cs_user_money`
--

TRUNCATE TABLE `cs_user_money`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_verification`
--

CREATE TABLE `cs_verification` (
  `verification_id` int(11) UNSIGNED NOT NULL,
  `number` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '号码',
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '验证码',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=无效 1=有效',
  `type` enum('sms','email') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='验证码';

--
-- 插入之前先把表清空（truncate） `cs_verification`
--

TRUNCATE TABLE `cs_verification`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_withdraw`
--

CREATE TABLE `cs_withdraw` (
  `withdraw_id` int(11) UNSIGNED NOT NULL,
  `withdraw_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '提现单号',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款人姓名',
  `mobile` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款人手机',
  `bank_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款账户',
  `account` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款账号',
  `money` decimal(10,2) NOT NULL COMMENT '提现金额',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费(百分比)',
  `amount` decimal(10,2) NOT NULL COMMENT '合计金额',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=待处理 1=处理中 2=已取消 3=已完成 4=已拒绝',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='提现申请';

--
-- 插入之前先把表清空（truncate） `cs_withdraw`
--

TRUNCATE TABLE `cs_withdraw`;
-- --------------------------------------------------------

--
-- 表的结构 `cs_withdraw_user`
--

CREATE TABLE `cs_withdraw_user` (
  `withdraw_user_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应user表',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款人姓名',
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款人手机',
  `bank_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款账户',
  `account` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款账号',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未删 1=已删'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='提现账号';

--
-- 插入之前先把表清空（truncate） `cs_withdraw_user`
--

TRUNCATE TABLE `cs_withdraw_user`;
--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_action_log`
--
ALTER TABLE `cs_action_log`
  ADD PRIMARY KEY (`action_log_id`),
  ADD KEY `client_type` (`client_type`),
  ADD KEY `path` (`path`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_admin`
--
ALTER TABLE `cs_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `username` (`username`),
  ADD KEY `status` (`status`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `nickname` (`nickname`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `cs_ads`
--
ALTER TABLE `cs_ads`
  ADD PRIMARY KEY (`ads_id`),
  ADD KEY `ads_position_id` (`ads_position_id`),
  ADD KEY `start_time` (`begin_time`),
  ADD KEY `end_time` (`end_time`),
  ADD KEY `sort` (`sort`),
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `code` (`code`),
  ADD KEY `platform` (`platform`);

--
-- Indexes for table `cs_ads_position`
--
ALTER TABLE `cs_ads_position`
  ADD PRIMARY KEY (`ads_position_id`),
  ADD KEY `status` (`status`),
  ADD KEY `name` (`name`) USING BTREE,
  ADD KEY `code` (`code`),
  ADD KEY `platform` (`platform`);

--
-- Indexes for table `cs_app`
--
ALTER TABLE `cs_app`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `app_key` (`app_key`),
  ADD KEY `status` (`status`),
  ADD KEY `is_delete` (`is_delete`);

--
-- Indexes for table `cs_app_install`
--
ALTER TABLE `cs_app_install`
  ADD PRIMARY KEY (`app_install_id`),
  ADD KEY `user_agent` (`user_agent`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `cs_article`
--
ALTER TABLE `cs_article`
  ADD PRIMARY KEY (`article_id`),
  ADD KEY `article_cat_id` (`article_cat_id`),
  ADD KEY `is_top` (`is_top`),
  ADD KEY `status` (`status`) USING BTREE;

--
-- Indexes for table `cs_article_cat`
--
ALTER TABLE `cs_article_cat`
  ADD PRIMARY KEY (`article_cat_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `cat_type` (`cat_type`),
  ADD KEY `sort` (`sort`),
  ADD KEY `is_navi` (`is_navi`) USING BTREE;

--
-- Indexes for table `cs_ask`
--
ALTER TABLE `cs_ask`
  ADD PRIMARY KEY (`ask_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `ask_type` (`ask_type`) USING BTREE,
  ADD KEY `is_delete` (`is_delete`);

--
-- Indexes for table `cs_auth_group`
--
ALTER TABLE `cs_auth_group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `sort` (`sort`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_auth_rule`
--
ALTER TABLE `cs_auth_rule`
  ADD PRIMARY KEY (`rule_id`),
  ADD KEY `module` (`module`),
  ADD KEY `status` (`status`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `cs_brand`
--
ALTER TABLE `cs_brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD KEY `is_show` (`status`),
  ADD KEY `name` (`name`),
  ADD KEY `goods_category_id` (`goods_category_id`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `cs_card`
--
ALTER TABLE `cs_card`
  ADD PRIMARY KEY (`card_id`),
  ADD KEY `name` (`name`),
  ADD KEY `status` (`status`),
  ADD KEY `is_delete` (`is_delete`);

--
-- Indexes for table `cs_card_use`
--
ALTER TABLE `cs_card_use`
  ADD PRIMARY KEY (`card_use_id`) USING BTREE,
  ADD KEY `card_id` (`card_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `number` (`number`),
  ADD KEY `money` (`money`),
  ADD KEY `is_invalid` (`is_invalid`) USING BTREE;

--
-- Indexes for table `cs_cart`
--
ALTER TABLE `cs_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `key_name` (`key_name`),
  ADD KEY `update_time` (`update_time`),
  ADD KEY `is_show` (`is_show`);

--
-- Indexes for table `cs_collect`
--
ALTER TABLE `cs_collect`
  ADD PRIMARY KEY (`collect_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_top` (`is_top`),
  ADD KEY `goods_id` (`goods_id`);

--
-- Indexes for table `cs_coupon`
--
ALTER TABLE `cs_coupon`
  ADD PRIMARY KEY (`coupon_id`),
  ADD KEY `name` (`name`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `type` (`type`),
  ADD KEY `status` (`status`),
  ADD KEY `is_invalid` (`is_invalid`),
  ADD KEY `use_end_time` (`use_end_time`),
  ADD KEY `give_code` (`give_code`) USING BTREE;

--
-- Indexes for table `cs_coupon_give`
--
ALTER TABLE `cs_coupon_give`
  ADD PRIMARY KEY (`coupon_give_id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `use_time` (`use_time`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `exchange_code` (`exchange_code`) USING BTREE;

--
-- Indexes for table `cs_delivery`
--
ALTER TABLE `cs_delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `status` (`status`),
  ADD KEY `sort` (`sort`),
  ADD KEY `delivery_item_id` (`delivery_item_id`);

--
-- Indexes for table `cs_delivery_area`
--
ALTER TABLE `cs_delivery_area`
  ADD PRIMARY KEY (`delivery_area_id`),
  ADD KEY `delivery_id` (`delivery_id`);

--
-- Indexes for table `cs_delivery_dist`
--
ALTER TABLE `cs_delivery_dist`
  ADD PRIMARY KEY (`delivery_dist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `state` (`state`),
  ADD KEY `delivery_code` (`delivery_code`),
  ADD KEY `logistic_code` (`logistic_code`),
  ADD KEY `order_code` (`order_code`) USING BTREE,
  ADD KEY `is_sub` (`is_sub`);

--
-- Indexes for table `cs_delivery_item`
--
ALTER TABLE `cs_delivery_item`
  ADD PRIMARY KEY (`delivery_item_id`),
  ADD KEY `type` (`type`),
  ADD KEY `is_delete` (`is_delete`) USING BTREE;

--
-- Indexes for table `cs_discount`
--
ALTER TABLE `cs_discount`
  ADD PRIMARY KEY (`discount_id`),
  ADD KEY `begin_time` (`begin_time`),
  ADD KEY `end_time` (`end_time`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_discount_goods`
--
ALTER TABLE `cs_discount_goods`
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `goods_id` (`goods_id`);

--
-- Indexes for table `cs_friend_link`
--
ALTER TABLE `cs_friend_link`
  ADD PRIMARY KEY (`friend_link_id`),
  ADD KEY `is_show` (`status`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `cs_goods`
--
ALTER TABLE `cs_goods`
  ADD PRIMARY KEY (`goods_id`),
  ADD KEY `goods_code` (`goods_code`),
  ADD KEY `name` (`name`(191)),
  ADD KEY `goods_category_id` (`goods_category_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `is_integral` (`is_integral`),
  ADD KEY `is_recommend` (`is_recommend`),
  ADD KEY `is_new` (`is_new`),
  ADD KEY `is_hot` (`is_hot`),
  ADD KEY `sort` (`sort`),
  ADD KEY `status` (`status`),
  ADD KEY `shop_price` (`shop_price`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `store_qty` (`store_qty`),
  ADD KEY `is_postage` (`is_postage`),
  ADD KEY `bar_code` (`bar_code`);

--
-- Indexes for table `cs_goods_attr`
--
ALTER TABLE `cs_goods_attr`
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `is_important` (`is_important`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `attr_value` (`attr_value`),
  ADD KEY `goods_attribute_id` (`goods_attribute_id`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `cs_goods_attribute`
--
ALTER TABLE `cs_goods_attribute`
  ADD PRIMARY KEY (`goods_attribute_id`),
  ADD KEY `goods_type_id` (`goods_type_id`),
  ADD KEY `sort` (`sort`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `attr_index` (`attr_index`);

--
-- Indexes for table `cs_goods_attr_config`
--
ALTER TABLE `cs_goods_attr_config`
  ADD PRIMARY KEY (`goods_attr_config_id`),
  ADD KEY `goods_id` (`goods_id`);

--
-- Indexes for table `cs_goods_category`
--
ALTER TABLE `cs_goods_category`
  ADD PRIMARY KEY (`goods_category_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `category_type` (`category_type`),
  ADD KEY `status` (`status`),
  ADD KEY `sort` (`sort`),
  ADD KEY `is_navi` (`is_navi`) USING BTREE;

--
-- Indexes for table `cs_goods_comment`
--
ALTER TABLE `cs_goods_comment`
  ADD PRIMARY KEY (`goods_comment_id`),
  ADD KEY `type` (`type`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `is_top` (`is_top`),
  ADD KEY `score` (`score`),
  ADD KEY `is_show` (`is_show`),
  ADD KEY `is_image` (`is_image`),
  ADD KEY `order_no` (`order_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_goods_id` (`order_goods_id`),
  ADD KEY `is_append` (`is_append`);

--
-- Indexes for table `cs_goods_consult`
--
ALTER TABLE `cs_goods_consult`
  ADD PRIMARY KEY (`goods_consult_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `is_show` (`is_show`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_goods_reply`
--
ALTER TABLE `cs_goods_reply`
  ADD PRIMARY KEY (`goods_reply_id`),
  ADD KEY `goods_comment_id` (`goods_comment_id`);

--
-- Indexes for table `cs_goods_type`
--
ALTER TABLE `cs_goods_type`
  ADD PRIMARY KEY (`goods_type_id`);

--
-- Indexes for table `cs_help`
--
ALTER TABLE `cs_help`
  ADD PRIMARY KEY (`help_id`),
  ADD KEY `router` (`router`),
  ADD KEY `module` (`module`);

--
-- Indexes for table `cs_history`
--
ALTER TABLE `cs_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `update_time` (`update_time`);

--
-- Indexes for table `cs_menu`
--
ALTER TABLE `cs_menu`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `sort` (`sort`),
  ADD KEY `status` (`status`),
  ADD KEY `module` (`module`) USING BTREE,
  ADD KEY `is_navi` (`is_navi`);

--
-- Indexes for table `cs_message`
--
ALTER TABLE `cs_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `type` (`type`),
  ADD KEY `is_top` (`is_top`),
  ADD KEY `status` (`status`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `member` (`member`) USING BTREE,
  ADD KEY `create_time` (`create_time`);

--
-- Indexes for table `cs_message_user`
--
ALTER TABLE `cs_message_user`
  ADD PRIMARY KEY (`message_user_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `cs_navigation`
--
ALTER TABLE `cs_navigation`
  ADD PRIMARY KEY (`navigation_id`);

--
-- Indexes for table `cs_notice_item`
--
ALTER TABLE `cs_notice_item`
  ADD PRIMARY KEY (`notice_item_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `cs_notice_tpl`
--
ALTER TABLE `cs_notice_tpl`
  ADD PRIMARY KEY (`notice_tpl_id`),
  ADD KEY `status` (`status`),
  ADD KEY `code` (`code`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `cs_order`
--
ALTER TABLE `cs_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `order_no` (`order_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trade_status` (`trade_status`),
  ADD KEY `delivery_status` (`delivery_status`),
  ADD KEY `payment_status` (`payment_status`),
  ADD KEY `create_user_id` (`create_user_id`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `create_time` (`create_time`),
  ADD KEY `delivery_time` (`delivery_time`);

--
-- Indexes for table `cs_order_goods`
--
ALTER TABLE `cs_order_goods`
  ADD PRIMARY KEY (`order_goods_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_no` (`order_no`),
  ADD KEY `goods_name` (`goods_name`),
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `key_name` (`key_name`),
  ADD KEY `is_comment` (`is_comment`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_service` (`is_service`);

--
-- Indexes for table `cs_order_log`
--
ALTER TABLE `cs_order_log`
  ADD PRIMARY KEY (`order_log_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_no` (`order_no`);

--
-- Indexes for table `cs_order_refund`
--
ALTER TABLE `cs_order_refund`
  ADD PRIMARY KEY (`order_refund_id`),
  ADD KEY `refund_no` (`refund_no`),
  ADD KEY `order_no` (`order_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_order_service`
--
ALTER TABLE `cs_order_service`
  ADD PRIMARY KEY (`order_service_id`),
  ADD KEY `service_no` (`service_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_no` (`order_no`),
  ADD KEY `status` (`status`),
  ADD KEY `type` (`type`),
  ADD KEY `order_goods_id` (`order_goods_id`),
  ADD KEY `admin_event` (`admin_event`) USING BTREE,
  ADD KEY `user_event` (`user_event`) USING BTREE,
  ADD KEY `admin_id` (`admin_id`) USING BTREE;

--
-- Indexes for table `cs_payment`
--
ALTER TABLE `cs_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `code` (`code`),
  ADD KEY `sort` (`sort`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_payment_log`
--
ALTER TABLE `cs_payment_log`
  ADD PRIMARY KEY (`payment_log_id`),
  ADD KEY `payment_no` (`payment_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_no` (`order_no`),
  ADD KEY `type` (`type`),
  ADD KEY `status` (`status`),
  ADD KEY `trade_no` (`out_trade_no`);

--
-- Indexes for table `cs_praise`
--
ALTER TABLE `cs_praise`
  ADD PRIMARY KEY (`praise_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `goods_comment_id` (`goods_comment_id`);

--
-- Indexes for table `cs_promotion`
--
ALTER TABLE `cs_promotion`
  ADD PRIMARY KEY (`promotion_id`),
  ADD KEY `begin_time` (`begin_time`),
  ADD KEY `end_time` (`end_time`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_promotion_item`
--
ALTER TABLE `cs_promotion_item`
  ADD KEY `promotion_id` (`promotion_id`),
  ADD KEY `quota` (`quota`);

--
-- Indexes for table `cs_qrcode`
--
ALTER TABLE `cs_qrcode`
  ADD PRIMARY KEY (`qrcode_id`);

--
-- Indexes for table `cs_region`
--
ALTER TABLE `cs_region`
  ADD PRIMARY KEY (`region_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `is_delete` (`is_delete`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `cs_service_log`
--
ALTER TABLE `cs_service_log`
  ADD PRIMARY KEY (`service_log_id`),
  ADD KEY `order_service_id` (`order_service_id`),
  ADD KEY `service_no` (`service_no`);

--
-- Indexes for table `cs_setting`
--
ALTER TABLE `cs_setting`
  ADD PRIMARY KEY (`setting_id`) USING BTREE,
  ADD KEY `code` (`code`),
  ADD KEY `module` (`module`);

--
-- Indexes for table `cs_spec`
--
ALTER TABLE `cs_spec`
  ADD PRIMARY KEY (`spec_id`) USING BTREE,
  ADD KEY `goods_type_id` (`goods_type_id`),
  ADD KEY `sort` (`sort`),
  ADD KEY `spec_index` (`spec_index`);

--
-- Indexes for table `cs_spec_config`
--
ALTER TABLE `cs_spec_config`
  ADD PRIMARY KEY (`spec_config_id`),
  ADD KEY `goods_id` (`goods_id`);

--
-- Indexes for table `cs_spec_goods`
--
ALTER TABLE `cs_spec_goods`
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `key_name` (`key_name`);

--
-- Indexes for table `cs_spec_image`
--
ALTER TABLE `cs_spec_image`
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `spec_item_id` (`spec_item_id`);

--
-- Indexes for table `cs_spec_item`
--
ALTER TABLE `cs_spec_item`
  ADD PRIMARY KEY (`spec_item_id`),
  ADD KEY `spec_id` (`spec_id`),
  ADD KEY `is_contact` (`is_contact`) USING BTREE,
  ADD KEY `sort` (`sort`) USING BTREE;

--
-- Indexes for table `cs_storage`
--
ALTER TABLE `cs_storage`
  ADD PRIMARY KEY (`storage_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `type` (`type`),
  ADD KEY `sort` (`sort`),
  ADD KEY `hash` (`hash`),
  ADD KEY `path` (`path`),
  ADD KEY `name` (`name`),
  ADD KEY `protocol` (`protocol`),
  ADD KEY `priority` (`priority`);

--
-- Indexes for table `cs_storage_style`
--
ALTER TABLE `cs_storage_style`
  ADD PRIMARY KEY (`storage_style_id`) USING BTREE,
  ADD KEY `code` (`code`),
  ADD KEY `platform` (`platform`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cs_support`
--
ALTER TABLE `cs_support`
  ADD PRIMARY KEY (`support_id`),
  ADD KEY `status` (`status`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `cs_token`
--
ALTER TABLE `cs_token`
  ADD PRIMARY KEY (`token_id`),
  ADD KEY `user_id` (`client_id`),
  ADD KEY `admin_id` (`platform`),
  ADD KEY `client_type` (`client_type`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `cs_topic`
--
ALTER TABLE `cs_topic`
  ADD PRIMARY KEY (`topic_id`),
  ADD KEY `is_show` (`status`);

--
-- Indexes for table `cs_transaction`
--
ALTER TABLE `cs_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `order_no` (`source_no`),
  ADD KEY `module` (`module`),
  ADD KEY `create_time` (`create_time`),
  ADD KEY `card_number` (`card_number`);

--
-- Indexes for table `cs_user`
--
ALTER TABLE `cs_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `username` (`username`),
  ADD KEY `mobile` (`mobile`),
  ADD KEY `email` (`email`),
  ADD KEY `nickname` (`nickname`),
  ADD KEY `user_level_id` (`user_level_id`),
  ADD KEY `status` (`status`),
  ADD KEY `is_delete` (`is_delete`);

--
-- Indexes for table `cs_user_address`
--
ALTER TABLE `cs_user_address`
  ADD PRIMARY KEY (`user_address_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_delete` (`is_delete`);

--
-- Indexes for table `cs_user_level`
--
ALTER TABLE `cs_user_level`
  ADD PRIMARY KEY (`user_level_id`),
  ADD KEY `amount` (`amount`);

--
-- Indexes for table `cs_user_money`
--
ALTER TABLE `cs_user_money`
  ADD PRIMARY KEY (`user_money_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cs_verification`
--
ALTER TABLE `cs_verification`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `number` (`number`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `cs_withdraw`
--
ALTER TABLE `cs_withdraw`
  ADD PRIMARY KEY (`withdraw_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `withdraw_no` (`withdraw_no`),
  ADD KEY `create_time` (`create_time`);

--
-- Indexes for table `cs_withdraw_user`
--
ALTER TABLE `cs_withdraw_user`
  ADD PRIMARY KEY (`withdraw_user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_delete` (`is_delete`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cs_action_log`
--
ALTER TABLE `cs_action_log`
  MODIFY `action_log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_admin`
--
ALTER TABLE `cs_admin`
  MODIFY `admin_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `cs_ads`
--
ALTER TABLE `cs_ads`
  MODIFY `ads_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_ads_position`
--
ALTER TABLE `cs_ads_position`
  MODIFY `ads_position_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_app`
--
ALTER TABLE `cs_app`
  MODIFY `app_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `cs_app_install`
--
ALTER TABLE `cs_app_install`
  MODIFY `app_install_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_article`
--
ALTER TABLE `cs_article`
  MODIFY `article_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_article_cat`
--
ALTER TABLE `cs_article_cat`
  MODIFY `article_cat_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- 使用表AUTO_INCREMENT `cs_ask`
--
ALTER TABLE `cs_ask`
  MODIFY `ask_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_auth_group`
--
ALTER TABLE `cs_auth_group`
  MODIFY `group_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- 使用表AUTO_INCREMENT `cs_auth_rule`
--
ALTER TABLE `cs_auth_rule`
  MODIFY `rule_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- 使用表AUTO_INCREMENT `cs_brand`
--
ALTER TABLE `cs_brand`
  MODIFY `brand_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_card`
--
ALTER TABLE `cs_card`
  MODIFY `card_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_card_use`
--
ALTER TABLE `cs_card_use`
  MODIFY `card_use_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_cart`
--
ALTER TABLE `cs_cart`
  MODIFY `cart_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_collect`
--
ALTER TABLE `cs_collect`
  MODIFY `collect_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_coupon`
--
ALTER TABLE `cs_coupon`
  MODIFY `coupon_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_coupon_give`
--
ALTER TABLE `cs_coupon_give`
  MODIFY `coupon_give_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_delivery`
--
ALTER TABLE `cs_delivery`
  MODIFY `delivery_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_delivery_area`
--
ALTER TABLE `cs_delivery_area`
  MODIFY `delivery_area_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_delivery_dist`
--
ALTER TABLE `cs_delivery_dist`
  MODIFY `delivery_dist_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_delivery_item`
--
ALTER TABLE `cs_delivery_item`
  MODIFY `delivery_item_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=512;
--
-- 使用表AUTO_INCREMENT `cs_discount`
--
ALTER TABLE `cs_discount`
  MODIFY `discount_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_friend_link`
--
ALTER TABLE `cs_friend_link`
  MODIFY `friend_link_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_goods`
--
ALTER TABLE `cs_goods`
  MODIFY `goods_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_goods_attribute`
--
ALTER TABLE `cs_goods_attribute`
  MODIFY `goods_attribute_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_goods_attr_config`
--
ALTER TABLE `cs_goods_attr_config`
  MODIFY `goods_attr_config_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_goods_category`
--
ALTER TABLE `cs_goods_category`
  MODIFY `goods_category_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=844;
--
-- 使用表AUTO_INCREMENT `cs_goods_comment`
--
ALTER TABLE `cs_goods_comment`
  MODIFY `goods_comment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_goods_consult`
--
ALTER TABLE `cs_goods_consult`
  MODIFY `goods_consult_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_goods_reply`
--
ALTER TABLE `cs_goods_reply`
  MODIFY `goods_reply_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_goods_type`
--
ALTER TABLE `cs_goods_type`
  MODIFY `goods_type_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_help`
--
ALTER TABLE `cs_help`
  MODIFY `help_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- 使用表AUTO_INCREMENT `cs_history`
--
ALTER TABLE `cs_history`
  MODIFY `history_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_menu`
--
ALTER TABLE `cs_menu`
  MODIFY `menu_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1087;
--
-- 使用表AUTO_INCREMENT `cs_message`
--
ALTER TABLE `cs_message`
  MODIFY `message_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_message_user`
--
ALTER TABLE `cs_message_user`
  MODIFY `message_user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_navigation`
--
ALTER TABLE `cs_navigation`
  MODIFY `navigation_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_notice_item`
--
ALTER TABLE `cs_notice_item`
  MODIFY `notice_item_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- 使用表AUTO_INCREMENT `cs_notice_tpl`
--
ALTER TABLE `cs_notice_tpl`
  MODIFY `notice_tpl_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- 使用表AUTO_INCREMENT `cs_order`
--
ALTER TABLE `cs_order`
  MODIFY `order_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_order_goods`
--
ALTER TABLE `cs_order_goods`
  MODIFY `order_goods_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_order_log`
--
ALTER TABLE `cs_order_log`
  MODIFY `order_log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_order_refund`
--
ALTER TABLE `cs_order_refund`
  MODIFY `order_refund_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_order_service`
--
ALTER TABLE `cs_order_service`
  MODIFY `order_service_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_payment`
--
ALTER TABLE `cs_payment`
  MODIFY `payment_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- 使用表AUTO_INCREMENT `cs_payment_log`
--
ALTER TABLE `cs_payment_log`
  MODIFY `payment_log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_praise`
--
ALTER TABLE `cs_praise`
  MODIFY `praise_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_promotion`
--
ALTER TABLE `cs_promotion`
  MODIFY `promotion_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_qrcode`
--
ALTER TABLE `cs_qrcode`
  MODIFY `qrcode_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_region`
--
ALTER TABLE `cs_region`
  MODIFY `region_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4085;
--
-- 使用表AUTO_INCREMENT `cs_service_log`
--
ALTER TABLE `cs_service_log`
  MODIFY `service_log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_setting`
--
ALTER TABLE `cs_setting`
  MODIFY `setting_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
--
-- 使用表AUTO_INCREMENT `cs_spec`
--
ALTER TABLE `cs_spec`
  MODIFY `spec_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_spec_config`
--
ALTER TABLE `cs_spec_config`
  MODIFY `spec_config_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_spec_item`
--
ALTER TABLE `cs_spec_item`
  MODIFY `spec_item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_storage`
--
ALTER TABLE `cs_storage`
  MODIFY `storage_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- 使用表AUTO_INCREMENT `cs_storage_style`
--
ALTER TABLE `cs_storage_style`
  MODIFY `storage_style_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- 使用表AUTO_INCREMENT `cs_support`
--
ALTER TABLE `cs_support`
  MODIFY `support_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_token`
--
ALTER TABLE `cs_token`
  MODIFY `token_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `cs_topic`
--
ALTER TABLE `cs_topic`
  MODIFY `topic_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_transaction`
--
ALTER TABLE `cs_transaction`
  MODIFY `transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_user`
--
ALTER TABLE `cs_user`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_user_address`
--
ALTER TABLE `cs_user_address`
  MODIFY `user_address_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_user_level`
--
ALTER TABLE `cs_user_level`
  MODIFY `user_level_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- 使用表AUTO_INCREMENT `cs_user_money`
--
ALTER TABLE `cs_user_money`
  MODIFY `user_money_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_verification`
--
ALTER TABLE `cs_verification`
  MODIFY `verification_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_withdraw`
--
ALTER TABLE `cs_withdraw`
  MODIFY `withdraw_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `cs_withdraw_user`
--
ALTER TABLE `cs_withdraw_user`
  MODIFY `withdraw_user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
