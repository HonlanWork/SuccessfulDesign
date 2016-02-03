/*
 Navicat Premium Data Transfer

 Source Server         : MyMac
 Source Server Type    : MySQL
 Source Server Version : 50542
 Source Host           : localhost
 Source Database       : successfuldesign

 Target Server Type    : MySQL
 Target Server Version : 50542
 File Encoding         : utf-8

 Date: 02/03/2016 11:56:00 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `contest`
-- ----------------------------
DROP TABLE IF EXISTS `contest`;
CREATE TABLE `contest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `year` int(4) NOT NULL,
  `fee` int(11) NOT NULL,
  `early_bird_time` varchar(255) NOT NULL,
  `early_bird_discount` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `contest`
-- ----------------------------
BEGIN;
INSERT INTO `contest` VALUES ('1', '2016成功设计大赛', '2016', '2000', '1459440000', '0.9');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
