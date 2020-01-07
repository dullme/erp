# ************************************************************
# Sequel Pro SQL dump
# Version (null)
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 8.0.16)
# Database: erp
# Generation Time: 2020-01-07 14:11:18 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table admin_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_config`;

CREATE TABLE `admin_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_config_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_config` WRITE;
/*!40000 ALTER TABLE `admin_config` DISABLE KEYS */;

INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`)
VALUES
	(1,'hq','60','必须为整数','2020-01-07 18:22:27','2020-01-07 18:22:27'),
	(2,'in','0.3937008','1厘米 = 多少英寸','2020-01-07 20:15:04','2020-01-07 20:15:04'),
	(3,'cuft','35.3147248','1立方米 = 多少立方英尺','2020-01-07 20:30:03','2020-01-07 20:34:29');

/*!40000 ALTER TABLE `admin_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_menu`;

CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`)
VALUES
	(1,0,1,'仪表盘','fa-bar-chart','/',NULL,NULL,'2020-01-05 13:23:28'),
	(2,0,14,'Admin','fa-tasks','',NULL,NULL,'2020-01-05 21:10:19'),
	(3,2,16,'Users','fa-users','auth/users',NULL,NULL,'2020-01-07 18:21:57'),
	(4,2,17,'Roles','fa-user','auth/roles',NULL,NULL,'2020-01-07 18:21:57'),
	(5,2,18,'Permission','fa-ban','auth/permissions',NULL,NULL,'2020-01-07 18:21:57'),
	(6,2,19,'Menu','fa-bars','auth/menu',NULL,NULL,'2020-01-07 18:21:57'),
	(7,2,20,'Operation log','fa-history','auth/logs',NULL,NULL,'2020-01-07 18:21:57'),
	(8,9,11,'单品管理','fa-cube','products',NULL,'2019-12-17 13:35:47','2020-01-07 21:12:01'),
	(9,0,10,'产品管理','fa-product-hunt',NULL,NULL,'2019-12-17 14:01:54','2020-01-05 21:10:19'),
	(10,9,12,'组合管理','fa-cubes','composes',NULL,'2019-12-17 14:30:34','2020-01-07 21:11:48'),
	(11,0,13,'文件管理','fa-file','media',NULL,'2019-12-17 14:47:55','2020-01-05 21:10:19'),
	(12,16,4,'订购入库','fa-bars','orders',NULL,'2019-12-21 17:24:01','2020-01-05 21:02:35'),
	(13,0,2,'库存状况','fa-leaf','warehouses',NULL,'2019-12-21 20:53:10','2020-01-05 21:02:35'),
	(14,17,7,'供应商','fa-bars','suppliers',NULL,'2020-01-03 14:56:33','2020-01-05 21:02:35'),
	(15,17,8,'货代公司','fa-bars','forwarding-companies',NULL,'2020-01-05 13:16:15','2020-01-05 21:02:35'),
	(16,0,3,'货物管理','fa-dropbox',NULL,NULL,'2020-01-05 13:20:46','2020-01-05 21:02:35'),
	(17,0,6,'公司','fa-black-tie',NULL,NULL,'2020-01-05 13:22:33','2020-01-05 21:02:35'),
	(18,16,5,'装箱运送','fa-truck','packages',NULL,'2020-01-05 13:27:39','2020-01-05 21:02:35'),
	(19,17,9,'仓储公司','fa-inbox','warehouse-companies',NULL,'2020-01-05 21:10:09','2020-01-05 21:10:19'),
	(20,2,15,'配置','fa-toggle-on','config',NULL,'2020-01-07 18:21:30','2020-01-07 18:21:57');

/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_operation_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_operation_log`;

CREATE TABLE `admin_operation_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_operation_log_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table admin_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_permissions`;

CREATE TABLE `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_name_unique` (`name`),
  UNIQUE KEY `admin_permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`)
VALUES
	(1,'All permission','*','','*',NULL,NULL),
	(2,'Dashboard','dashboard','GET','/',NULL,NULL),
	(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),
	(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),
	(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL),
	(6,'Media manager','ext.media-manager','','/media*','2019-12-17 14:47:55','2019-12-17 14:47:55'),
	(7,'Admin Config','ext.config','','/config*','2020-01-07 18:21:30','2020-01-07 18:21:30');

/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role_menu`;

CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`)
VALUES
	(1,2,NULL,NULL);

/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role_permissions`;

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
VALUES
	(1,1,NULL,NULL);

/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role_users`;

CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;

INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`)
VALUES
	(1,1,NULL,NULL);

/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_roles`;

CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_roles_name_unique` (`name`),
  UNIQUE KEY `admin_roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`)
VALUES
	(1,'Administrator','administrator','2019-12-12 17:21:37','2019-12-12 17:21:37');

/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_user_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_user_permissions`;

CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table admin_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`)
VALUES
	(1,'admin','$2y$10$50p6/2vDrQjKXlshnEjyAu5WE/WKE7p7xDZhBgP/sdKrQSzQP71uW','Administrator',NULL,'Tdxc6PwqBrnvjqP9KbQz23ZaHaquKdFaXEFaQAHoJqj8RUCwL8pnWYWnzOQY','2019-12-12 17:21:37','2019-12-12 17:21:37');

/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table compose_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `compose_products`;

CREATE TABLE `compose_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `compose_id` int(10) unsigned NOT NULL COMMENT '所属组合',
  `product_id` int(10) unsigned NOT NULL COMMENT '所属产品',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `compose_products` WRITE;
/*!40000 ALTER TABLE `compose_products` DISABLE KEYS */;

INSERT INTO `compose_products` (`id`, `compose_id`, `product_id`, `quantity`, `created_at`, `updated_at`)
VALUES
	(37,34,2,1,'2019-12-20 15:53:18','2019-12-20 15:53:18'),
	(38,34,4,1,'2019-12-20 15:53:18','2019-12-20 15:53:18'),
	(39,34,5,1,'2019-12-20 15:53:18','2019-12-20 15:53:18'),
	(40,35,6,1,'2019-12-25 13:35:38','2019-12-25 13:35:38'),
	(41,35,3,2,'2019-12-25 13:35:38','2019-12-25 13:35:38'),
	(42,35,5,1,'2019-12-25 13:35:38','2019-12-25 13:35:38'),
	(43,36,1,1,'2019-12-25 13:43:51','2019-12-25 13:43:51'),
	(44,37,7,1,'2019-12-26 09:49:32','2019-12-26 09:49:32'),
	(45,37,8,1,'2019-12-26 09:49:32','2019-12-26 09:49:32'),
	(46,37,9,2,'2019-12-26 09:49:32','2019-12-26 09:49:32'),
	(47,37,10,1,'2019-12-26 09:49:32','2019-12-26 09:49:32'),
	(48,37,11,1,'2019-12-26 09:49:32','2019-12-26 09:49:32');

/*!40000 ALTER TABLE `compose_products` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table composes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `composes`;

CREATE TABLE `composes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `asin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hq` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图片',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `composes_name_unique` (`name`),
  UNIQUE KEY `composes_asin_unique` (`asin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `composes` WRITE;
/*!40000 ALTER TABLE `composes` DISABLE KEYS */;

INSERT INTO `composes` (`id`, `name`, `asin`, `hq`, `image`, `content`, `created_at`, `updated_at`)
VALUES
	(34,'2单人+2脚蹬+1双人+1长几','K76A',NULL,'[\"compose\\/1576828398767180.png\"]',NULL,'2019-12-20 15:53:18','2019-12-20 15:53:18'),
	(37,'A1系列六件套','A169',NULL,'[\"compose\\/1577324972811341.jpg\"]',NULL,'2019-12-26 09:49:32','2019-12-26 09:49:32');

/*!40000 ALTER TABLE `composes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table failed_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forwarding_companies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forwarding_companies`;

CREATE TABLE `forwarding_companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '电话',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '地址',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forwarding_companies_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `forwarding_companies` WRITE;
/*!40000 ALTER TABLE `forwarding_companies` DISABLE KEYS */;

INSERT INTO `forwarding_companies` (`id`, `name`, `mobile`, `address`, `deleted_at`, `created_at`, `updated_at`)
VALUES
	(1,'货代公司','12312312312','22312312321',NULL,'2020-01-07 15:53:01','2020-01-07 15:53:01');

/*!40000 ALTER TABLE `forwarding_companies` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'2014_10_12_000000_create_users_table',1),
	(2,'2014_10_12_100000_create_password_resets_table',1),
	(3,'2016_01_04_173148_create_admin_tables',1),
	(4,'2019_08_19_000000_create_failed_jobs_table',1),
	(6,'2019_12_17_131454_create_products_table',2),
	(11,'2019_12_17_140616_create_composes_table',3),
	(12,'2019_12_17_142613_create_compose_products_table',3),
	(18,'2019_12_21_150109_create_orders_table',4),
	(19,'2019_12_21_162910_create_warehouses_table',4),
	(26,'2020_01_03_145335_create_suppliers_table',5),
	(27,'2020_01_05_123727_create_packages_table',6),
	(28,'2020_01_05_124101_create_forwarding_companies_table',6),
	(29,'2020_01_05_210745_create_warehouse_companies_table',6),
	(30,'2017_07_17_040159_create_config_table',7);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table orders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL COMMENT '供应商',
  `no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单编号',
  `batch` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前批次',
  `product` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品',
  `product_batch` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '入库批次记录',
  `signing_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '签订日',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_no_unique` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table packages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `packages`;

CREATE TABLE `packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forwarding_company_id` int(10) unsigned NOT NULL COMMENT '货代',
  `lading_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '提单号',
  `container_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '集装箱号',
  `seal_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '铅封号',
  `product` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品',
  `packaged_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '装箱日',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `packages_lading_number_unique` (`lading_number`),
  UNIQUE KEY `packages_container_number_unique` (`container_number`),
  UNIQUE KEY `packages_seal_number_unique` (`seal_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table password_resets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `length` int(11) NOT NULL COMMENT '长',
  `width` int(11) NOT NULL COMMENT '宽',
  `height` int(11) NOT NULL COMMENT '高',
  `weight` decimal(10,2) NOT NULL COMMENT '重量',
  `ddp` int(11) NOT NULL COMMENT '价格',
  `hq` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'hq',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图片',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '单位',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '详情',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;

INSERT INTO `products` (`id`, `sku`, `length`, `width`, `height`, `weight`, `ddp`, `hq`, `image`, `unit`, `description`, `content`, `created_at`, `updated_at`)
VALUES
	(1,'7A',90,70,75,24.50,0,NULL,'images/图片 1.png',NULL,'1xKD三人坐背垫+1x长茶几','<table><tbody><tr class=\"firstRow\"><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">1</td><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">2</td><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">3</td><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">4</td><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">5</td><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">6</td><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">7</td></tr><tr><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">2</td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td></tr><tr><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">3</td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td></tr><tr><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">4</td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td></tr><tr><td width=\"87\" valign=\"top\" style=\"word-break: break-all;\">5</td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td><td width=\"87\" valign=\"top\"><br/></td></tr></tbody></table><p style=\"text-align: left;\"><br/></p>','2019-12-17 13:42:00','2019-12-25 21:35:24'),
	(2,'7B',90,66,45,24.50,126,NULL,NULL,NULL,'2x单人坐背垫',NULL,'2019-12-17 13:48:14','2019-12-17 13:48:14'),
	(3,'7C',81,41,70,26.00,87,NULL,NULL,NULL,'1x摇转单人坐背垫',NULL,'2019-12-17 13:55:35','2019-12-17 13:55:35'),
	(4,'7D',54,48,42,6.70,60,NULL,NULL,NULL,'2x单人脚蹬坐垫',NULL,'2019-12-20 15:51:55','2019-12-20 16:03:23'),
	(5,'7E',120,25,79,27.00,123,NULL,NULL,NULL,'1x双人坐背垫+1x长茶几',NULL,'2019-12-20 15:52:31','2019-12-20 16:03:37'),
	(6,'97M',53,11,52,5.70,19,NULL,'images/1850ec45fc2eb2a1afef9fad893bd4e4.png',NULL,'1x小茶几',NULL,'2019-12-25 13:32:37','2019-12-25 13:32:37'),
	(7,'A101',68,15,32,70.00,131,NULL,'images/A101-2.JPG',NULL,'三人沙发','<table cellpadding=\"0\" cellspacing=\"0\" width=\"443\"><colgroup><col width=\"443\" style=\";width:443px\"/></colgroup><tbody><tr height=\"20\" style=\";height:20px\" class=\"firstRow\"><td height=\"20\" dir=\"LTR\" width=\"443\">Product &nbsp; name: <span style=\"\">BANER GARDEN</span><span style=\"\"><sup>R</sup></span><span style=\"\">&nbsp; Outdoor furniture sofa</span></td></tr><tr height=\"20\" style=\";height:20px\"><td height=\"20\" dir=\"LTR\">Model no<span style=\"\">.:&nbsp; A101</span><span style=\"\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; Barcode: 789185889715</span></td></tr><tr height=\"20\" style=\";height:20px\"><td height=\"20\" dir=\"LTR\">Main &nbsp; specification:&nbsp;</td></tr><tr height=\"20\" style=\";height:20px\"><td height=\"20\" dir=\"LTR\">Three seater sofa: &nbsp; 71-1/2*28*29&quot;&nbsp;&nbsp; Weight: 55 &nbsp; lbs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr height=\"20\" style=\";height:20px\"><td height=\"20\" dir=\"LTR\">Including seat &nbsp; cushion and back cushion: Thickness 3&quot;</td></tr><tr height=\"20\" style=\";height:20px\"><td height=\"20\" dir=\"LTR\">Galvanized steel &nbsp; frame, powdered coating,&nbsp;</td></tr><tr height=\"20\" style=\";height:20px\"><td height=\"20\" dir=\"LTR\">PE rattan,UV &nbsp; protection and weather resistant</td></tr><tr height=\"20\" style=\";height:20px\"><td height=\"20\" dir=\"LTR\">ISTA A3 mail order &nbsp; packing&nbsp;</td></tr></tbody></table><br/><p>&nbsp;</p>','2019-12-26 09:06:39','2019-12-26 09:54:16'),
	(8,'A102',47,15,32,57.00,98,NULL,'images/A102-2.jpg',NULL,'双人沙发','<table width=\"443\" cellspacing=\"0\" cellpadding=\"0\"><colgroup><col width=\"443\" style=\"width: 443px;\"/></colgroup><tbody><tr height=\"20\" class=\"firstRow\" style=\"height: 20px;\"><td width=\"443\" height=\"20\" style=\"border-width: 1px 1px 0px; border-style: solid solid none; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Product &nbsp; name: </strong>BANER GARDEN<sup>R</sup>&nbsp; Outdoor furniture sofa</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Model no</strong>.:&nbsp; A102 <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Barcode: &nbsp; 789185889722</strong></span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><strong><span style=\"font-size: 12px;\">Main specification:</span></strong></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Two seater sofa: &nbsp; 49-1/2*28*29&quot;&nbsp;&nbsp; Weight: 42 &nbsp; lbs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Including seat &nbsp; cushion and back cushion: Thickness 3&quot;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Galvanized steel &nbsp; frame, powdered coating,&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">PE rattan,UV &nbsp; protection and weather resistant</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">ISTA A3 mail order &nbsp; packing&nbsp;</span></td></tr></tbody></table><p></p>','2019-12-26 09:08:53','2019-12-26 09:08:53'),
	(9,'A103',31,15,28,42.00,67,NULL,'images/A103-2.jpg',NULL,'单人沙发','<table width=\"443\" cellspacing=\"0\" cellpadding=\"0\"><colgroup><col width=\"443\" style=\"width: 443px;\"/></colgroup><tbody><tr height=\"20\" class=\"firstRow\" style=\"height: 20px;\"><td width=\"443\" height=\"20\" style=\"border-width: 1px 1px 0px; border-style: solid solid none; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Product &nbsp; name: </strong>BANER GARDEN<sup>R</sup>&nbsp; Outdoor furniture sofa</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Model no</strong>.:&nbsp; A103 <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Barcode: &nbsp; 789185889739</strong></span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><strong><span style=\"font-size: 12px;\">Main specification:</span></strong></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Single seater sofa: &nbsp; 28*28*29&quot;&nbsp;&nbsp; Weight: 29 lbs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Including seat &nbsp; cushion and back cushion: Thickness 3&quot;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Galvanized steel &nbsp; frame, powdered coating,&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">PE rattan,UV &nbsp; protection and weather resistant</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">ISTA A3 mail order &nbsp; packing&nbsp;</span></td></tr></tbody></table><p></p>','2019-12-26 09:10:53','2019-12-26 09:10:53'),
	(10,'A104',51,7,28,38.00,59,NULL,'images/A104-4.jpg',NULL,'储物功能长茶几','<table width=\"443\" cellspacing=\"0\" cellpadding=\"0\"><colgroup><col width=\"443\" style=\"width: 443px;\"/></colgroup><tbody><tr height=\"20\" class=\"firstRow\" style=\"height: 20px;\"><td width=\"443\" height=\"20\" style=\"border-width: 1px 1px 0px; border-style: solid solid none; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Product &nbsp; name: </strong>BANER GARDEN<sup>R</sup>&nbsp; Outdoor furniture table &nbsp; or Storage box</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Model no</strong>.:&nbsp; A104 <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Barcode: &nbsp; 789185889746</strong></span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><strong><span style=\"font-size: 12px;\">Main specification:</span></strong></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Single seater sofa: &nbsp; 49*25*16&quot;&nbsp;&nbsp; Weight: 30 lbs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Galvanized steel &nbsp; frame, powdered coating,&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">PE rattan,UV &nbsp; protection and weather resistant</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">ISTA A3 mail order &nbsp; packing&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px 1px; border-style: none solid solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"></td></tr></tbody></table><p></p>','2019-12-26 09:14:41','2019-12-26 09:14:41'),
	(11,'A105',27,7,28,31.00,48,NULL,'images/A105-4.jpg',NULL,'储物功能方茶几','<table width=\"443\" cellspacing=\"0\" cellpadding=\"0\"><colgroup><col width=\"443\" style=\"width: 443px;\"/></colgroup><tbody><tr height=\"20\" class=\"firstRow\" style=\"height: 20px;\"><td width=\"443\" height=\"20\" style=\"border-width: 1px 1px 0px; border-style: solid solid none; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Product &nbsp; name: </strong>BANER GARDEN<sup>R</sup>&nbsp; Outdoor furniture table &nbsp; or Storage box</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Model no</strong>.:&nbsp; A105 <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Barcode: &nbsp; 789185889753</strong></span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><strong><span style=\"font-size: 12px;\">Main specification:</span></strong></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Single seater sofa: &nbsp; 25*25*25&quot;&nbsp;&nbsp; Weight: 24 lbs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Galvanized steel &nbsp; frame, powdered coating,&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">PE rattan,UV &nbsp; protection and weather resistant</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">ISTA A3 mail order &nbsp; packing&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px 1px; border-style: none solid solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><br/></td></tr></tbody></table><p>&nbsp;</p>','2019-12-26 09:16:41','2019-12-26 09:17:01'),
	(12,'A106',56,4,35,30.00,72,NULL,'images/A106-3.jpg',NULL,'长高茶几','<table width=\"443\" cellspacing=\"0\" cellpadding=\"0\"><colgroup><col width=\"443\" style=\"width: 443px;\"/></colgroup><tbody><tr height=\"20\" class=\"firstRow\" style=\"height: 20px;\"><td width=\"443\" height=\"20\" style=\"border-width: 1px 1px 0px; border-style: solid solid none; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Product &nbsp; name: </strong>BANER GARDEN<sup>R</sup>&nbsp; Outdoor furniture table</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\"><strong>Model no</strong>.:&nbsp; A106 <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Barcode: &nbsp; 789185889760</strong></span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><strong><span style=\"font-size: 12px;\">Main specification:</span></strong></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Single seater sofa: &nbsp; 55*31-1/2*25-1/2&quot;&nbsp;&nbsp; Weight: 22 &nbsp; lbs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">Galvanized steel &nbsp; frame, powdered coating,&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">PE rattan,UV &nbsp; protection and weather resistant</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px; border-style: none solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"><span style=\"font-size: 12px;\">ISTA A3 mail order &nbsp; packing&nbsp;</span></td></tr><tr height=\"20\" style=\"height: 20px;\"><td height=\"20\" style=\"border-width: 0px 1px 1px; border-style: none solid solid; border-color: windowtext; background-color: white;\" dir=\"LTR\"></td></tr></tbody></table><p></p>','2019-12-26 09:18:55','2019-12-26 09:18:55');

/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table suppliers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `suppliers`;

CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '电话',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '地址',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;

INSERT INTO `suppliers` (`id`, `name`, `mobile`, `address`, `deleted_at`, `created_at`, `updated_at`)
VALUES
	(1,'供应商名称','13867676767','地址很长','2020-01-07 15:54:13','2020-01-07 15:51:29','2020-01-07 15:54:13');

/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table warehouse_companies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `warehouse_companies`;

CREATE TABLE `warehouse_companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '电话',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '地址',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouse_companies_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `warehouse_companies` WRITE;
/*!40000 ALTER TABLE `warehouse_companies` DISABLE KEYS */;

INSERT INTO `warehouse_companies` (`id`, `name`, `mobile`, `address`, `deleted_at`, `created_at`, `updated_at`)
VALUES
	(1,'仓储公司','12312312312','123123123123123',NULL,'2020-01-07 15:53:49','2020-01-07 15:53:49');

/*!40000 ALTER TABLE `warehouse_companies` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table warehouses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `warehouses`;

CREATE TABLE `warehouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `package_id` int(10) unsigned DEFAULT NULL COMMENT '集装箱ID',
  `warehouse_company_id` int(10) unsigned DEFAULT NULL COMMENT '仓储公司ID',
  `order_batch` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '订单批次',
  `batch_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '批次号',
  `product_id` int(10) unsigned NOT NULL COMMENT '所属产品',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '所属仓库:1中国仓库；2海运中；3美国备用仓库；4美国线上',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `entry_at` timestamp NULL DEFAULT NULL COMMENT '入库时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
