-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2024-07-21 16:38:05
-- 服务器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

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
-- 表的结构 `areas`
--

CREATE TABLE `areas` (
  `name` varchar(60) NOT NULL COMMENT '区域名称',
  `includes` text NOT NULL COMMENT '区域包含的房间号码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `checkios`
--

CREATE TABLE `checkios` (
  `requestid` int(11) NOT NULL COMMENT '请求人ID',
  `room` varchar(20) NOT NULL COMMENT '房间号',
  `fromdate` date NOT NULL COMMENT '起始日期',
  `todate` date NOT NULL COMMENT '终止日期',
  `reason` text NOT NULL COMMENT '原因'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `requestid` int(11) NOT NULL COMMENT '请求人ID',
  `type` varchar(30) NOT NULL COMMENT '种类',
  `param` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '参数',
  `time` datetime NOT NULL COMMENT '申请时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `rooms`
--

CREATE TABLE `rooms` (
  `number` varchar(20) NOT NULL COMMENT '房间号(id)',
  `status` enum('normal','cleaning','repairing','stop') NOT NULL COMMENT '房间状态',
  `location` varchar(100) NOT NULL COMMENT '地址',
  `max` int(11) NOT NULL COMMENT '最大居住人数',
  `updatetime` datetime NOT NULL COMMENT '更改时间',
  `addtime` datetime NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT '用户ID',
  `email` varchar(50) NOT NULL COMMENT '用户邮箱',
  `realname` varchar(10) NOT NULL COMMENT '真名',
  `password` tinyblob NOT NULL COMMENT '密码（加密）',
  `workid` varchar(20) DEFAULT 'NEWUSER' COMMENT '工号',
  `department` varchar(50) NOT NULL DEFAULT '[未定义]' COMMENT '部门',
  `accessment` enum('staff','admin','system-admin') NOT NULL COMMENT '权限',
  `managepart` varchar(30) DEFAULT NULL COMMENT '管理区域范围ID（见areas）',
  `actived` int(11) NOT NULL COMMENT '是否被批准',
  `header` mediumblob DEFAULT NULL COMMENT '头像',
  `registertime` datetime NOT NULL COMMENT '注册时间',
  `logintime` datetime NOT NULL COMMENT '上次登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`name`);

--
-- 表的索引 `checkios`
--
ALTER TABLE `checkios`
  ADD PRIMARY KEY (`requestid`),
  ADD KEY `roomid` (`room`);

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
  ADD PRIMARY KEY (`number`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `managepart` (`managepart`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID', AUTO_INCREMENT=6;

--
-- 限制导出的表
--

--
-- 限制表 `checkios`
--
ALTER TABLE `checkios`
  ADD CONSTRAINT `checkios_ibfk_1` FOREIGN KEY (`requestid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `checkios_ibfk_2` FOREIGN KEY (`room`) REFERENCES `rooms` (`number`);

--
-- 限制表 `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`requestid`) REFERENCES `users` (`id`);

--
-- 限制表 `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`managepart`) REFERENCES `areas` (`name`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
