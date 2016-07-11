/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : dev_dhh

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-01-12 18:54:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `dhh_article`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_article`;
CREATE TABLE `dhh_article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_title` varchar(150) DEFAULT NULL,
  `article_content` longtext,
  `article_author` varchar(30) DEFAULT NULL,
  `article_open` tinyint(1) DEFAULT NULL,
  `article_keywork` varchar(30) DEFAULT NULL,
  `article_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_article
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_car_wire`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_car_wire`;
CREATE TABLE `dhh_car_wire` (
  `wire_id` int(11) NOT NULL AUTO_INCREMENT,
  `drivers_id` int(11) DEFAULT NULL,
  `logistics_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `wire_state` varchar(60) DEFAULT NULL,
  `wire_end` varchar(60) DEFAULT NULL,
  `wire_effect` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`wire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_car_wire
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_comment`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_comment`;
CREATE TABLE `dhh_comment` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `wire_id` int(11) DEFAULT NULL,
  `comment_content` varchar(255) DEFAULT NULL,
  `comment_rank` tinyint(1) DEFAULT NULL,
  `comment_status` tinyint(1) DEFAULT NULL,
  `comment_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_config`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_config`;
CREATE TABLE `dhh_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `type` tinyint(3) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `status` varchar(4) DEFAULT NULL,
  `value` text,
  `sort` smallint(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_config
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_drivers`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_drivers`;
CREATE TABLE `dhh_drivers` (
  `driver_id` int(11) NOT NULL AUTO_INCREMENT,
  `logistics_id` int(11) DEFAULT NULL,
  `driver_name` varchar(30) DEFAULT NULL,
  `driver_sex` tinyint(1) DEFAULT NULL,
  `driver_age` tinyint(3) DEFAULT NULL,
  `driver_phone` varchar(20) DEFAULT NULL,
  `driver_img` int(10) DEFAULT NULL,
  `driver_charter` int(10) DEFAULT NULL,
  `driver_register` int(10) DEFAULT NULL,
  `driver_allow` int(10) DEFAULT NULL,
  PRIMARY KEY (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_drivers
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_logistics`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_logistics`;
CREATE TABLE `dhh_logistics` (
  `logistics_id` int(11) NOT NULL AUTO_INCREMENT,
  `logistics_name` varchar(60) DEFAULT NULL,
  `logistics_address` varchar(150) DEFAULT NULL,
  `logistics_phome` varchar(60) DEFAULT NULL,
  `logistics_brief` varchar(255) DEFAULT NULL,
  `logistics_img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`logistics_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_logistics
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_message`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_message`;
CREATE TABLE `dhh_message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `message_content` varchar(255) DEFAULT NULL,
  `message_time` int(10) DEFAULT NULL,
  `message_status` tinyint(3) DEFAULT NULL COMMENT '1：系统消息；2：意见反馈；3：订单消息；4：建议消息',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_message
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_orders`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_orders`;
CREATE TABLE `dhh_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` int(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `wire_id` int(11) DEFAULT NULL,
  `Logistics_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `order_status` int(3) DEFAULT NULL,
  `order_depart` varchar(255) DEFAULT NULL,
  `order_destination` varchar(255) DEFAULT NULL,
  `order_time` varchar(60) DEFAULT NULL,
  `order_type` varchar(60) DEFAULT NULL,
  `order_weight` int(60) DEFAULT NULL,
  `order_bulk` int(60) DEFAULT NULL,
  `order_car_type` varchar(60) DEFAULT NULL,
  `order_car_length` varchar(20) DEFAULT NULL,
  `order_user_phone` int(60) DEFAULT NULL,
  `order_remark` varchar(255) DEFAULT NULL,
  `order_cash` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_orders
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_region`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_region`;
CREATE TABLE `dhh_region` (
  `region_id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `region_name` varchar(120) DEFAULT NULL,
  `region_type` tinyint(1) DEFAULT NULL COMMENT '0:国家;1:省;2:市;3;区',
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_region
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_track`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_track`;
CREATE TABLE `dhh_track` (
  `track_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `wire_id` int(11) DEFAULT NULL,
  `track_name` varchar(60) DEFAULT NULL,
  `track_time` int(10) DEFAULT NULL,
  `track_content` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`track_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_track
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_users`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_users`;
CREATE TABLE `dhh_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_name` varchar(60) NOT NULL,
  `user_password` char(10) DEFAULT NULL,
  `user_age` tinyint(3) DEFAULT NULL,
  `user_phone` varchar(60) NOT NULL,
  `user_img` varchar(255) DEFAULT NULL,
  `user_sex` tinyint(1) DEFAULT NULL,
  `user_inform` tinyint(1) DEFAULT NULL COMMENT '0:关闭；1开启',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_users
-- ----------------------------

-- ----------------------------
-- Table structure for `dhh_vehicle`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_vehicle`;
CREATE TABLE `dhh_vehicle` (
  `vehicle_id` int(11) NOT NULL AUTO_INCREMENT,
  `logistics_id` int(11) DEFAULT NULL,
  `vehicle_type` varchar(30) DEFAULT NULL,
  `vehicle_licence` varchar(30) DEFAULT NULL,
  `vehicle_long` tinyint(3) DEFAULT NULL,
  `vehicle_weight` tinyint(3) DEFAULT NULL,
  `vehicle_age` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_vehicle
-- ----------------------------
