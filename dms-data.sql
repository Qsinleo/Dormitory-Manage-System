-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-09-23 22:07:53
-- 服务器版本： 5.7.40-log
-- PHP 版本： 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `dms-data`
--

-- --------------------------------------------------------

--
-- 表的结构 `checkios`
--

CREATE TABLE `checkios` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `requestid` int(11) NOT NULL COMMENT '请求人ID',
  `roomid` int(11) NOT NULL COMMENT '房间ID',
  `fromdate` date NOT NULL COMMENT '起始日期',
  `todate` date NOT NULL COMMENT '终止日期',
  `reason` text NOT NULL COMMENT '原因'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `requestid` int(11) NOT NULL COMMENT '请求人ID',
  `type` varchar(30) NOT NULL COMMENT '种类',
  `param` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '参数',
  `time` datetime NOT NULL COMMENT '申请时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL COMMENT '房间ID',
  `number` int(11) NOT NULL COMMENT '房间号',
  `status` enum('normal','cleaning','repairing','stop') NOT NULL COMMENT '房间状态',
  `updatetime` datetime NOT NULL COMMENT '更改时间',
  `addtime` datetime NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `rooms`
--

INSERT INTO `rooms` (`id`, `number`, `status`, `updatetime`, `addtime`) VALUES
(1, 1, 'empty', '2023-09-19 20:54:18', '2023-09-19 20:54:18');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT '用户ID',
  `mail` varchar(50) NOT NULL COMMENT '用户邮箱',
  `realname` varchar(10) NOT NULL COMMENT '真名',
  `password` tinyblob NOT NULL COMMENT '密码（加密）',
  `workid` int(11) DEFAULT '10000' COMMENT '工号',
  `department` varchar(50) NOT NULL DEFAULT '[未定义]' COMMENT '部门',
  `accessment` enum('staff','admin','system-admin') NOT NULL COMMENT '权限',
  `managepartid` text COMMENT '管理区域',
  `actived` int(11) NOT NULL COMMENT '是否被批准',
  `header` mediumblob COMMENT '头像',
  `registertime` datetime NOT NULL COMMENT '注册时间',
  `logintime` datetime NOT NULL COMMENT '上次登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `mail`, `realname`, `password`, `workid`, `department`, `accessment`, `managepartid`, `actived`, `header`, `registertime`, `logintime`) VALUES
(1, 'lylsnrc@126.com', '李雨乐', 0x37633232326662323932376438323861663232663539323133346538393332343830363337633064, 1145, 'SYSTEM-ADMIN', 'system-admin', NULL, 1, NULL, '2023-09-19 20:40:19', '2023-09-19 20:40:19'),
(3, 'temp@unknown.com', '需好困', 0x37633232326662323932376438323861663232663539323133346538393332343830363337633064, NULL, 'ADMIN', 'admin', NULL, 1, NULL, '2023-09-19 21:16:23', '2023-09-19 21:16:23');

--
-- 转储表的索引
--

--
-- 表的索引 `checkios`
--
ALTER TABLE `checkios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requestid` (`requestid`);

--
-- 表的索引 `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requestid` (`requestid`);

--
-- 表的索引 `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `mail` (`mail`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `checkios`
--
ALTER TABLE `checkios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '房间ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID', AUTO_INCREMENT=4;

--
-- 限制导出的表
--

--
-- 限制表 `checkios`
--
ALTER TABLE `checkios`
  ADD CONSTRAINT `checkios_ibfk_1` FOREIGN KEY (`requestid`) REFERENCES `users` (`id`);

--
-- 限制表 `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`requestid`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
