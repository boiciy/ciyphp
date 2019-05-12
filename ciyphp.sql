SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for d_test
-- ----------------------------
DROP TABLE IF EXISTS `d_test`;
CREATE TABLE `d_test`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '上传图片',
  `truename` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '姓名',
  `scores` int(11) NOT NULL COMMENT '分数',
  `fxk` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '复选框',
  `dxk` int(11) NOT NULL COMMENT '单选框',
  `lbk` int(11) NOT NULL COMMENT '下拉列表框',
  `kg` int(11) NOT NULL COMMENT '开关',
  `dh` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '多行文本框',
  `activetime` bigint(20) NOT NULL COMMENT '日期时间',
  `addtimes` bigint(20) NOT NULL COMMENT '新建时间',
  `ip` int(11) NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '增删改查Demo表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for d_test_bak
-- ----------------------------
DROP TABLE IF EXISTS `d_test_bak`;
CREATE TABLE `d_test_bak`  (
  `id` bigint(20) NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '上传图片',
  `truename` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '姓名',
  `scores` int(11) NOT NULL COMMENT '分数',
  `fxk` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '复选框',
  `dxk` int(11) NOT NULL COMMENT '单选框',
  `lbk` int(11) NOT NULL COMMENT '下拉列表框',
  `kg` int(11) NOT NULL COMMENT '开关',
  `dh` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '多行文本框',
  `activetime` bigint(20) NOT NULL COMMENT '日期时间',
  `addtimes` bigint(20) NOT NULL COMMENT '新建时间',
  `ip` int(11) NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '增删改查Demo备份表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for p_admin
-- ----------------------------
DROP TABLE IF EXISTS `p_admin`;
CREATE TABLE `p_admin`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `upid` bigint(20) NOT NULL COMMENT '领导用户ID',
  `status` int(11) NOT NULL COMMENT '状态,1.禁用,10.正常',
  `trytime` int(11) NOT NULL COMMENT '连续错误次数',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登录手机号',
  `password` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `truename` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '真实姓名',
  `power` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '扩展权限',
  `sex` int(11) NOT NULL COMMENT '性别,1.男,2.女',
  `leader` int(11) NOT NULL COMMENT '负责人,1.是,2.否',
  `departid` int(11) NOT NULL COMMENT '部门ID',
  `depart` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在部门',
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户照片',
  `activetime` bigint(20) NOT NULL COMMENT '活跃时间',
  `addtimes` bigint(20) NOT NULL COMMENT '注册日期',
  `ip` int(20) NOT NULL COMMENT '登录IP',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `mobile`(`mobile`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员用户表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_admin
-- ----------------------------
INSERT INTO `p_admin` VALUES (10, 0, 10, 0, '10000000000', '67d7efa04a58f5c3a2640e593ae81ccf', '默认管理员', '.admin.', 1, 1, 3, '职能中心', '', 1557684322, 1547695138, 0);

-- ----------------------------
-- Table structure for p_admindepart
-- ----------------------------
DROP TABLE IF EXISTS `p_admindepart`;
CREATE TABLE `p_admindepart`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upid` int(11) NOT NULL COMMENT '上级部门',
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '部门名称',
  `power` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '默认权限',
  `powerleader` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '领导权限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理部门表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_admindepart
-- ----------------------------
INSERT INTO `p_admindepart` VALUES (1, 0, '运营中心', '..', '..');
INSERT INTO `p_admindepart` VALUES (2, 0, '技术中心', '..', '..');
INSERT INTO `p_admindepart` VALUES (3, 0, '职能中心', '.admin.', '..');
INSERT INTO `p_admindepart` VALUES (4, 1, '市场部门', '..', '..');
INSERT INTO `p_admindepart` VALUES (5, 1, '销售部门', '..', '..');
INSERT INTO `p_admindepart` VALUES (6, 2, '开发部门', '..', '..');
INSERT INTO `p_admindepart` VALUES (7, 2, '运维部门', '..', '..');

-- ----------------------------
-- Table structure for p_adminonline
-- ----------------------------
DROP TABLE IF EXISTS `p_adminonline`;
CREATE TABLE `p_adminonline`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '登录用户ID',
  `target` int(11) NOT NULL COMMENT '登录平台,10.中台登录,11.代登录',
  `sid` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '授权码',
  `exptime` bigint(20) NOT NULL COMMENT '授权到期日',
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员登录状态表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for p_cata
-- ----------------------------
DROP TABLE IF EXISTS `p_cata`;
CREATE TABLE `p_cata`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upid` int(11) NOT NULL COMMENT '上级代码',
  `nums` int(11) NOT NULL COMMENT '排序',
  `types` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类',
  `codeid` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值',
  `title` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `codeid`(`types`, `codeid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '代码表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_cata
-- ----------------------------
INSERT INTO `p_cata` VALUES (1, 0, 0, 'user.sex', '0', '未知');
INSERT INTO `p_cata` VALUES (2, 0, 0, 'user.sex', '1', '男');
INSERT INTO `p_cata` VALUES (3, 0, 0, 'user.sex', '2', '女');
INSERT INTO `p_cata` VALUES (4, 0, 0, 'user.power', 'admin', '管理员');
INSERT INTO `p_cata` VALUES (5, 0, 0, 'user.power', 'p1', '权限1');
INSERT INTO `p_cata` VALUES (6, 0, 0, 'user.power', 'p2', '权限2');
INSERT INTO `p_cata` VALUES (7, 0, 0, 'user.level', '1', '注册用户');
INSERT INTO `p_cata` VALUES (8, 0, 0, 'user.level', '10', '认证用户');
INSERT INTO `p_cata` VALUES (9, 0, 0, 'user.wxstatus', '1', '授权');
INSERT INTO `p_cata` VALUES (10, 0, 0, 'user.wxstatus', '9', '取消关注');
INSERT INTO `p_cata` VALUES (11, 0, 0, 'user.wxstatus', '10', '已关注');
INSERT INTO `p_cata` VALUES (12, 0, 0, 'user.wxstatus', '0', '无');

-- ----------------------------
-- Table structure for p_config
-- ----------------------------
DROP TABLE IF EXISTS `p_config`;
CREATE TABLE `p_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '参数标题',
  `types` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '参数代码',
  `params` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '参数含义',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '快捷配置表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_config
-- ----------------------------
INSERT INTO `p_config` VALUES (1, '配置例子', 'demo', 'demo param');

-- ----------------------------
-- Table structure for p_log
-- ----------------------------
DROP TABLE IF EXISTS `p_log`;
CREATE TABLE `p_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '操作人ID',
  `readid` int(11) NOT NULL COMMENT '处理人ID',
  `types` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'LOG分类',
  `status` int(11) NOT NULL COMMENT '状态,0.未处理,1.已处理,2.锁定',
  `logs` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '详情',
  `addtimes` bigint(20) NOT NULL COMMENT '时间',
  `ip` int(10) UNSIGNED NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '日志表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for p_system
-- ----------------------------
DROP TABLE IF EXISTS `p_system`;
CREATE TABLE `p_system`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '任务名称',
  `runfunc` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '入口函数',
  `runrequire` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '引用文件',
  `runparam` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '执行参数',
  `runcnt` int(11) NOT NULL COMMENT '执行次数',
  `nexttime` bigint(20) UNSIGNED NOT NULL COMMENT '下一次执行时间',
  `nextsec` int(11) NOT NULL COMMENT '时间间隔',
  `exptime` bigint(20) UNSIGNED NOT NULL COMMENT '超时时间',
  `status` int(11) NOT NULL COMMENT '状态,0.待执行,1.正在执行,10.禁用',
  `errmsg` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '执行信息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '自动化任务表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_system
-- ----------------------------
INSERT INTO `p_system` VALUES (1, '每天执行', 'day', 'timer.php', '', 0, 1514736000, 86400, 1514736000, 0, '');
INSERT INTO `p_system` VALUES (2, '每小时执行', 'hour', 'timer.php', '', 0, 1514736000, 3600, 1514736000, 0, '');
INSERT INTO `p_system` VALUES (4, '每月执行', 'month', 'timer.php', '', 0, 1514736000, 1, 1514736000, 0, '');
INSERT INTO `p_system` VALUES (7, '每分钟执行', 'minute', 'timer.php', '', 0, 1514736000, 60, 1514736000, 0, '');
INSERT INTO `p_system` VALUES (8, '每年执行', 'year', 'timer.php', '', 0, 1514736000, 12, 1514736000, 0, '');
INSERT INTO `p_system` VALUES (9, '每周执行', 'week', 'timer.php', '', 0, 1514736000, 604800, 1514736000, 0, '');
INSERT INTO `p_system` VALUES (10, '区块链同步', 'sync', 'blocktc.php', '', 0, 1514736000, 60, 1514736000, 10, '');

-- ----------------------------
-- Table structure for p_user
-- ----------------------------
DROP TABLE IF EXISTS `p_user`;
CREATE TABLE `p_user`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `upid` bigint(20) NOT NULL COMMENT '推荐人ID',
  `money` int(11) NOT NULL COMMENT '余额(分)',
  `level` int(11) NOT NULL COMMENT '用户级别,1.已注册,10.已认证',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '昵称',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号',
  `pass` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `sid` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Token',
  `headimg` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '头像',
  `wxstatus` int(11) NOT NULL COMMENT '关注公众号,1.授权,9.取消关注,10.已关注',
  `wxunionid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信UnionID',
  `wxopenid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信OpenID',
  `wxminaid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '小程序OpenID',
  `wxminakey` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '小程序Key',
  `logintime` bigint(20) UNSIGNED NOT NULL COMMENT '登陆时间',
  `addtimes` bigint(20) UNSIGNED NOT NULL COMMENT '注册时间',
  `lng` double(10, 6) NOT NULL COMMENT '经度',
  `lat` double(10, 6) NOT NULL COMMENT '纬度',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_user
-- ----------------------------
INSERT INTO `p_user` VALUES (1, 0, 0, 10, '众产国际', '13120599999', '', '5c755c5a2f63c', '', 0, '', '', '', '', 1514736000, 1514736000, 120.000000, 30.000000);

-- ----------------------------
-- Table structure for p_user_ext
-- ----------------------------
DROP TABLE IF EXISTS `p_user_ext`;
CREATE TABLE `p_user_ext`  (
  `id` bigint(20) NOT NULL,
  `sex` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '性别',
  `dist` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '区县',
  `city` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '城市',
  `country` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '国家',
  `province` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '省份',
  `ip` int(10) UNSIGNED NOT NULL COMMENT '注册IP',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户扩展表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_user_ext
-- ----------------------------
INSERT INTO `p_user_ext` VALUES (1, '1', '', '徐汇', '中国', '上海', 0);

-- ----------------------------
-- Table structure for p_vcode
-- ----------------------------
DROP TABLE IF EXISTS `p_vcode`;
CREATE TABLE `p_vcode`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL COMMENT '用户ID',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号',
  `code` int(11) NOT NULL COMMENT '验证码',
  `addtimes` bigint(20) NOT NULL COMMENT '添加时间',
  `ip` int(11) UNSIGNED NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '验证码表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for p_weixin
-- ----------------------------
DROP TABLE IF EXISTS `p_weixin`;
CREATE TABLE `p_weixin`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '绑定企业',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登录名',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '显示名',
  `baseid` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '原始ID',
  `appid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'APIID',
  `appsecret` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'APIsecret',
  `mchid` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付ID',
  `mchkey` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付Key',
  `accesstoken` varchar(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'AccessToken',
  `jsticket` varchar(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'JSTicket',
  `exptime` bigint(20) NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信授权表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of p_weixin
-- ----------------------------
INSERT INTO `p_weixin` VALUES (1, 0, '10000@qq.com', 'ciy', 'gh_*', 'wx*', '', '', '', '', '', 0);

SET FOREIGN_KEY_CHECKS = 1;
