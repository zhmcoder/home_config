-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 08, 2021 at 11:11 PM
-- Server version: 5.7.30-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mall_e2s`
--

-- --------------------------------------------------------

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES
(16, 0, 1, '应用首页', 'el-icon-milk-tea', 'app_home', '[]', '2021-10-21 00:33:09', '2021-10-21 00:33:09'),
(17, 16, 1, '应用管理', 'el-icon-potato-strips', 'app/info', '[]', '2021-10-21 00:34:31', '2021-11-07 10:02:05'),
(18, 16, 1, '搜索管理', 'el-icon-ice-tea', 'search/list', '[]', '2021-10-21 00:39:40', '2021-11-03 01:46:40'),
(19, 16, 1, '首页配置', 'el-icon-cherry', 'home/config', '[]', '2021-10-21 03:19:57', '2021-11-07 10:01:55'),
(22, 11, 1, '供货商管理', 'el-icon-milk-tea', 'supplier', '[]', '2021-10-22 07:36:09', '2021-10-22 07:36:54'),
(23, 16, 1, '货架管理', 'el-icon-ice-cream-square', 'home/shelf', '[]', '2021-11-02 14:22:58', '2021-11-06 05:18:41'),
(24, 16, 1, '跳转类型', 'el-icon-goblet-square-full', 'home/jump', '[]', '2021-11-03 15:11:39', '2021-11-06 03:16:16');


--
-- Table structure for table `home_configs`
--

CREATE TABLE `home_configs` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '栏目名称',
  `shelf_id` int(11) NOT NULL DEFAULT '3' COMMENT '货架类型',
  `show_name` int(10) NOT NULL DEFAULT '0' COMMENT '显示名称',
  `show_more` int(10) NOT NULL DEFAULT '0' COMMENT '显示更多',
  `shelf_on` int(10) NOT NULL DEFAULT '0' COMMENT '是否上架',
  `show_num` int(11) NOT NULL DEFAULT '0' COMMENT '显示个数',
  `show_app` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '[]' COMMENT '展示App',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `column_count` int(11) NOT NULL DEFAULT '0' COMMENT '专栏数量',
  `publish_up` datetime DEFAULT NULL COMMENT '上架时间',
  `publish_down` datetime DEFAULT NULL COMMENT '下架时间',
  `image` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '栏目图标'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_configs`
--

INSERT INTO `home_configs` (`id`, `created_at`, `updated_at`, `deleted_at`, `name`, `shelf_id`, `show_name`, `show_more`, `shelf_on`, `show_num`, `show_app`, `sort`, `column_count`, `publish_up`, `publish_down`) VALUES
(6, '2020-06-15 05:48:21', '2021-11-01 15:01:19', '2021-11-01 15:01:19', '轮播图111', 1, 0, 1, 1, 17, '[\"deep.mall\"]', 1, 0, NULL, NULL),
(11, '2020-06-19 07:36:34', '2021-11-06 05:34:55', NULL, '免费专区', 4, 1, 1, 1, 4, '[\"deep.mall\"]', 3, 0, NULL, NULL),
(20, '2020-12-24 07:53:12', '2021-11-06 05:34:34', NULL, '商品推荐', 3, 1, 1, 1, 6, '[\"deep.mall\"]', 12, 0, NULL, NULL),
(33, '2021-04-12 09:04:29', '2021-11-06 05:34:06', NULL, '金刚圈', 1, 1, 1, 1, 4, '[\"deep.mall\"]', 15, 0, NULL, NULL),
(34, '2021-04-12 15:48:24', '2021-11-06 10:43:47', NULL, '轮播图', 2, 1, 1, 1, 2, '[\"deep.mall\"]', 100, 0, NULL, NULL),
(35, '2021-11-06 00:23:11', '2021-11-06 05:33:45', NULL, '金刚圈-测试', 1, 0, 0, 0, 0, '[\"deep.mall\"]', 100, 0, NULL, NULL),
(36, '2021-11-08 14:53:42', '2021-11-08 14:53:42', NULL, '你好你好', 2, 1, 1, 1, 1, '[\"1212\",\"123\"]', 99, 0, '2021-11-08 00:00:00', '2021-11-22 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `home_config_ids`
--

CREATE TABLE `home_config_ids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `config_id` int(11) NOT NULL DEFAULT '0' COMMENT '首页货架ID',
  `third_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `sort` int(11) DEFAULT '100' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '关联名称',
  `jump_id` int(11) DEFAULT '0' COMMENT '跳转ID',
  `data_type` int(11) DEFAULT '1' COMMENT '跳转数据类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_config_ids`
--

INSERT INTO `home_config_ids` (`id`, `config_id`, `third_id`, `sort`, `created_at`, `updated_at`, `deleted_at`, `name`, `jump_id`, `data_type`) VALUES
(15, 11, 10, 100, '2021-11-06 14:48:50', '2021-11-06 14:48:50', NULL, 'asdfasdf', 2, 2),
(16, 11, 19, 100, '2021-11-06 14:51:07', '2021-11-06 14:51:07', NULL, '测试1212', 2, 2),
(17, 11, 2, 100, '2021-11-06 14:51:14', '2021-11-06 14:51:14', NULL, '店铺12', 4, 2),
(18, 11, 62, 100, '2021-11-08 10:23:43', '2021-11-08 10:23:43', NULL, '测试H5链接', 1, 1),
(20, 36, 1, 100, '2021-11-08 14:54:14', '2021-11-08 14:54:14', NULL, '店铺11', 4, 2),
(21, 36, 18, 100, '2021-11-08 14:54:20', '2021-11-08 14:54:20', NULL, '测试12', 2, 2),
(23, 34, 62, 100, '2021-11-08 15:01:16', '2021-11-08 15:01:16', NULL, '测试H5链接', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `home_items`
--

CREATE TABLE `home_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `image` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '轮播图片',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `sub_name` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '副标题',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `tag` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签',
  `is_show` int(11) DEFAULT '1' COMMENT '是否显示（1显示、0不显示）',
  `h5_url` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'H5链接',
  `order` int(11) DEFAULT '0' COMMENT '排序',
  `relation_id` int(11) DEFAULT '0' COMMENT '关联id',
  `config_id` int(11) DEFAULT '0' COMMENT '配置货架'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_items`
--

INSERT INTO `home_items` (`id`, `deleted_at`, `created_at`, `updated_at`, `name`, `image`, `content`, `sub_name`, `sort`, `tag`, `is_show`, `h5_url`, `order`, `relation_id`, `config_id`) VALUES
(62, NULL, '2021-11-06 14:47:20', '2021-11-06 14:47:20', '测试H5链接', 'images/WechatIMG2916.jpeg', '2342342', '', 0, '', 1, '', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `home_jumps`
--

CREATE TABLE `home_jumps` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型名称',
  `form_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型表单',
  `table_info` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '关联表',
  `data_type` int(11) DEFAULT '1' COMMENT '数据关联类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_jumps`
--

INSERT INTO `home_jumps` (`id`, `deleted_at`, `created_at`, `updated_at`, `name`, `form_type`, `table_info`, `data_type`) VALUES
(1, NULL, '2021-11-04 11:09:00', '2021-11-06 06:44:01', 'H5链接', 'input', NULL, 1),
(2, NULL, '2021-11-04 11:12:22', '2021-11-06 06:48:20', '商品', 'selectTable', 'goods\nid,name', 2),
(3, NULL, '2021-11-04 11:12:32', '2021-11-06 06:48:27', '分类', 'selectTable', 'goods_classes\nid,name', 2),
(4, NULL, '2021-11-04 11:15:49', '2021-11-06 06:48:34', '店铺', 'selectTable', 'shops\nid,name', 2),
(9, NULL, '2021-11-08 14:58:26', '2021-11-08 14:58:26', '外部链接', 'textArea', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `home_shelves`
--

CREATE TABLE `home_shelves` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '货架名称',
  `image` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '货架UI图'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_shelves`
--

INSERT INTO `home_shelves` (`id`, `deleted_at`, `created_at`, `updated_at`, `name`, `image`) VALUES
(1, NULL, '2021-11-06 04:18:01', '2021-11-08 14:11:06', '金刚圈', 'images/ico01.gif'),
(2, NULL, '2021-11-06 04:18:15', '2021-11-08 14:11:26', '轮播图', 'images/ico05.gif'),
(3, NULL, '2021-11-06 05:22:37', '2021-11-08 14:11:21', '列表展示', 'images/ico03.gif'),
(4, NULL, '2021-11-06 05:22:55', '2021-11-08 14:11:15', '方格展示', 'images/ico02.gif'),
(5, NULL, '2021-11-08 14:56:32', '2021-11-08 14:56:41', '货架1号', 'images/u=310660914,2361219137&fm=26&gp=0.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `home_configs`
--
ALTER TABLE `home_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_config_ids`
--
ALTER TABLE `home_config_ids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_items`
--
ALTER TABLE `home_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bmh_banner_name_unique` (`name`);

--
-- Indexes for table `home_jumps`
--
ALTER TABLE `home_jumps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_types_name_unique` (`name`);

--
-- Indexes for table `home_shelves`
--
ALTER TABLE `home_shelves`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `home_configs`
--
ALTER TABLE `home_configs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `home_config_ids`
--
ALTER TABLE `home_config_ids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `home_items`
--
ALTER TABLE `home_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `home_jumps`
--
ALTER TABLE `home_jumps`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `home_shelves`
--
ALTER TABLE `home_shelves`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
