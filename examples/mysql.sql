SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `d_test`
-- ----------------------------
DROP TABLE IF EXISTS `d_test`;
CREATE TABLE `d_test` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) NOT NULL,
  `truename` varchar(20) NOT NULL,
  `scores` int(11) NOT NULL,
  `addtimes` datetime NOT NULL,
  `ip` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
-- ----------------------------
-- Table structure for `d_test_bak`
-- ----------------------------
DROP TABLE IF EXISTS `d_test_bak`;
CREATE TABLE `d_test_bak` (
  `id` bigint(20) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `truename` varchar(20) NOT NULL,
  `scores` int(11) NOT NULL,
  `addtimes` datetime NOT NULL,
  `ip` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
