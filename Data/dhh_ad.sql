/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : dhh

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-07-29 17:17:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `dhh_ad`
-- ----------------------------
DROP TABLE IF EXISTS `dhh_ad`;
CREATE TABLE `dhh_ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `img` varchar(120) CHARACTER SET sjis NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dhh_ad
-- ----------------------------
INSERT INTO `dhh_ad` VALUES ('2', 'Uploads/ad/2016-07-28/5799b88ad5641.jpg');
INSERT INTO `dhh_ad` VALUES ('3', 'Uploads/ad/2016-07-28/5799b8926cbd5.jpg');
INSERT INTO `dhh_ad` VALUES ('4', 'Uploads/ad/2016-07-28/5799b899ab219.jpg');
