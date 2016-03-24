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

 Date: 03/24/2016 08:55:30 AM
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
  `fee` varchar(255) NOT NULL,
  `early_bird_time` varchar(255) NOT NULL,
  `early_bird_fee` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `contest`
-- ----------------------------
BEGIN;
INSERT INTO `contest` VALUES ('1', '2016成功设计大赛', '2016', '2000', '1459440000', '1800');
COMMIT;

-- ----------------------------
--  Table structure for `email`
-- ----------------------------
DROP TABLE IF EXISTS `email`;
CREATE TABLE `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `email`
-- ----------------------------
BEGIN;
INSERT INTO `email` VALUES ('1', '激活邮箱', '您的帐号已创建，请激活。', '尊敬的用户，您好：\n您的成功设计帐号已创建成功，请激活。激活地址：\n^^^\n\n如果上面的链接无法点击，您也可以复制链接，粘贴到您浏览器的地址栏内，然后按“回车”完成激活操作。\n成功设计帐号问题，请您发送邮件至award@designsuccess.cn。\n如果您有其他产品问题，请联系我们：award@designsuccess.cn。\n如果您没有进行过设置注册帐号的操作，请不要点击上述链接，并删除此邮件。\n\n此邮件为系统自动发送，请勿回复。 \n谢谢！'), ('2', '重置密码', '您正在申请重置密码。', '尊敬的用户，您好：\n您正在申请重置密码，点击以下链接完成密码重置：\n^^^\n\n如果上面的链接无法点击，您也可以复制链接，粘贴到您浏览器的地址栏内，然后按“回车”完成激活操作。\n成功设计帐号问题，请您发送邮件至award@designsuccess.cn。\n如果您有其他产品问题，请联系我们：award@designsuccess.cn。\n如果您没有进行过设置注册帐号的操作，请不要点击上述链接，并删除此邮件。\n\n此邮件为系统自动发送，请勿回复。 \n谢谢！'), ('3', '完成支付', '有新的用户完成支付', '系统管理员，您好：\n有新的用户完成支付\n^^^\n\n请确认并登录后台将其标记为已支付'), ('4', '确认支付', '您已经成功支付。', '尊敬的用户，您好：\r\n您已经成功支付，请点击以下链接进入作品详情编辑页面，也可以通过右上角头像－我的作品回到所需编辑的作品页面，进行修改和完善作品信息：\r\n^^^\r\n\r\n如果上面的链接无法点击，您也可以复制链接，粘贴到您浏览器的地址栏内，然后按“回车”完成激活操作。\r\n成功设计帐号问题，请您发送邮件至award@designsuccess.cn。\r\n如果您有其他产品问题，请联系我们：award@designsuccess.cn。\r\n如果您没有进行过设置注册帐号的操作，请不要点击上述链接，并删除此邮件。\r\n\r\n此邮件为系统自动发送，请勿回复。 \r\n谢谢！');
COMMIT;

-- ----------------------------
--  Table structure for `submission`
-- ----------------------------
DROP TABLE IF EXISTS `submission`;
CREATE TABLE `submission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `titlec` varchar(255) NOT NULL,
  `titlee` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `companyc` varchar(255) NOT NULL,
  `companye` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `companyp` varchar(255) NOT NULL,
  `cellphone` varchar(255) NOT NULL,
  `addts` varchar(255) NOT NULL,
  `introductionc` text NOT NULL,
  `introductione` text NOT NULL,
  `demandc` text NOT NULL,
  `demande` text NOT NULL,
  `challengec` text NOT NULL,
  `challengee` text NOT NULL,
  `costc` text NOT NULL,
  `coste` text NOT NULL,
  `solutionc` text NOT NULL,
  `solutione` text NOT NULL,
  `conclusionc` text NOT NULL,
  `conclusione` text NOT NULL,
  `completeness` float NOT NULL,
  `image` varchar(255) NOT NULL,
  `image1` varchar(255) NOT NULL,
  `image2` varchar(255) NOT NULL,
  `image3` varchar(255) NOT NULL,
  `image4` varchar(255) NOT NULL,
  `image5` varchar(255) NOT NULL,
  `image6` varchar(255) NOT NULL,
  `video` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `modifyts` varchar(255) NOT NULL,
  `submitts` varchar(255) NOT NULL,
  `iscomplete` int(1) NOT NULL DEFAULT '0',
  `ispaied` int(1) NOT NULL DEFAULT '0',
  `pay_confirm` int(1) NOT NULL DEFAULT '0',
  `issubmitted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `translation`
-- ----------------------------
DROP TABLE IF EXISTS `translation`;
CREATE TABLE `translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ch` varchar(1024) NOT NULL,
  `en` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `translation`
-- ----------------------------
BEGIN;
INSERT INTO `translation` VALUES ('1', 'English', '中文'), ('2', '大赛', 'Awards'), ('3', '成功设计平台', 'Successful Design'), ('4', '个人资料', 'My Account'), ('5', '我的作品', 'My Registrations'), ('6', '退出登录', 'Logout'), ('7', '登录', 'Login'), ('8', '注册', 'Sign Up'), ('9', '注册步骤', 'Registration Process'), ('25', '添加新作品', 'Add Project'), ('26', '作品名（英文）', 'Entry Name (English)'), ('27', '作品名（中文）', 'Entry Name (Chinese)'), ('28', '产品', 'Product'), ('29', '空间', 'Space'), ('30', '营销', 'Marketing'), ('31', '数字', 'Digital'), ('32', '视觉', 'Visual'), ('33', '商业模式', 'Business Model'), ('34', '社会创新', 'Social Innovation'), ('35', '众创空间', 'Creative Space'), ('39', '填写<span>中文</span>和<span>英文作品名</span>，选择<span>作品类别</span>，点击<span>添加</span>，以添加一个属于您的独一无二的新作品', 'Fill in <span>Chinese</span> and <span>English entry name</span>, choose <span>entry category</span> and click <span>Add</span> to create your own unique new entry'), ('40', '请注意，以上三项都是<span>必填项</span>', 'Attention, all the above three fields are <span>required</span>'), ('41', '作品类别', 'Entry Category'), ('42', '添加', 'Add'), ('44', '在作品<span>未提交</span>的情况下，<span>任何时刻</span>都可以通过点击右上角头像中<span>我的作品</span>回到所需编辑的作品页面，进行修改和完善作品信息', 'When project is <span>unsubmitted</span>, you can return to the editing page for re-editing and improvement <span>at any time</span>, by clicking <span>My Registrations</span> from your portrait at the top right corner.'), ('45', '请注意，一旦点击最后的<span>提交</span>，作品进入初审系统，<span>不能再进行修改</span>', 'Please note: when <span>submitted</span>, your project will be sent for the initial approval and <span>cannot be editted anymore</span>.'), ('46', '填写详情', 'Fill in Details'), ('47', '个人信息', 'Personal Information'), ('48', '如果以下信息在个人资料中已经填写完整，此处将自动补全', 'The following fields will be filled in automatically if they are entered in personal profile'), ('49', '公司名称（中文）', 'Company Name (Chinese)'), ('50', '公司名称（英文）', 'Company Name (English)'), ('51', '公司电话', 'Company Phone'), ('52', '邮箱', 'Email'), ('53', '手机', 'Cellphone'), ('54', '职位', 'Position'), ('55', '基本信息', 'Basic Information'), ('56', '请注意，您所填写的作品名称将用于今后大赛相关的所有宣传材料中', 'Please note that we will use this across all communications relating to the entry'), ('57', '详细信息', 'Detail Information'), ('58', '设计作品需在2016年10月前在中国市场上市或已发布', 'The design registration is restricted to products developed during 2015 and/or launched by October 2016'), ('59', '发布日期', 'Launching date'), ('60', '将设计背景，细节，以及您作品的与众不同之处进行阐述（不超过300字）', 'Include the background, details of your entry, and highlight what makes your entry stand out. No more than 300 words'), ('61', '简述（英文）', 'General Description (English)'), ('62', '简述（中文）', 'General Description (Chinese)'), ('63', '项目需求（英文）', 'Design Request (English)'), ('64', '项目需求（中文）', 'Design Request (Chinese)'), ('65', '面临挑战（英文）', 'Challenges (English)'), ('66', '面临挑战（中文）', 'Challenges (Chinese)'), ('67', '预算评估（英文）', 'Budget (English)'), ('68', '预算评估（中文）', 'Budget (Chinese)'), ('69', '设计解决方案（英文）', 'Design Solution (English)'), ('70', '设计解决方案（中文）', 'Design Solution (Chinese)'), ('71', '请用图表和数据来表现此项目的设计所取得的成效，如: 增进市场业绩，降低生产成本 ,用户使用态度和行为的提升, 内部管理的提升,对环境、社会或其他因素带来的影响', 'Any diagrams or numbers should be included in this section. Detail all the results achieved by design, such as: increase in market performance, reductions in production costs, improvements in consumer attitudes or behavior, improvements in internal management, environmental impact, social impact, and other influential facts'), ('72', '项目成效总结（英文）', 'Summary of results (English)'), ('73', '项目成效总结（中文）', 'Summary of results (Chinese)'), ('74', '图像信息', 'Image Information'), ('75', '图片要求', 'Image requirements'), ('76', '每件作品至少上传1张照片，但不超过6张', '1 or max 6 images per entry'), ('77', '图像尺寸：长28cm（最小20cm），宽21cm（最小18cm）', 'Image size: 28cm height(20cm min), 21cm width(18cm min)'), ('78', '图像分辨率：300dpi', 'Image Resolution: 300dpi'), ('79', '颜色模式：CMYK', 'Color Mode: CMYK'), ('80', '文件格式：GIF or JPG', 'File Format: GIF or JPG file'), ('81', '视频要求', 'Video requirements'), ('82', '视频文件为可选，如有需要则上传', 'Video is optional and please upload if necessary'), ('83', '最长时间：10分钟', 'Max 10mins'), ('84', '文件格式：MP4', 'File Format: MP4'), ('85', '补充文件要求', 'Complementary file requirements'), ('86', '补充文件为可选，如对作品有额外说明（如商业成效、社会影响等），可上传补充文件', 'Complementary file is optional and any complementary file about commercial performance, social impact can be added'), ('87', '最大文件大小：5MB', 'Max size: 5M'), ('88', '文件格式：DOC or PDF', 'File Format: doc or pdf file'), ('89', '保存', 'Save'), ('90', '下一步', 'Next'), ('91', '<span style=\"margin-left:0;\">完成支付</span>后，系统将发送<span>确认邮件</span>并提供<span>下一步操作</span>链接', '<span style=\"margin-left:0;\">After payment</span>, the system will send a <span>confirmation email</span> providing link for <span>next operation</span>'), ('92', '请注意，<span>未支付</span>的作品可以在右上角头像中<span>我的作品</span>内找到', 'Attention, registrations <span>not paid</span> can be found in <span>My Registrations</span> in the portrait of the top right corner '), ('93', '完成支付', 'Payments'), ('94', '您将支付的作品为', 'The registration you are paying for'), ('95', '支付费用（元）', 'Fees (RMB)'), ('96', '（早鸟优惠）', '(Early Birds Discount)'), ('97', '使用以下任意一种方式进行支付', 'Pay via any one of the following ways'), ('98', '支付成功后请等待系统确认，系统将发送确认邮件并提供填写作品详情链接', 'Please wait for the system\'s confirmation after payments, the system will send a confirmation email with the link to fill in registration details '), ('99', '只有<span>成功提交</span>的作品，才能最终进入<span>审核环节</span>', 'Only registrations that are <span>successfully submitted</span> can finally enter the <span>judging phase</span>'), ('100', '请注意，提交之后将<span>无法再次编辑</span>作品各项信息', 'Attetion, you <span>can\'t edit</span> registration details after submission any more'), ('101', '提交作品', 'Submit'), ('102', '您将提交的作品为', 'The registration you are submitting'), ('103', '成功提交的作品无法再次编辑，因此建议在截止日期前的最后时间内提交，为作品的修改和完善保留更改的机会', 'Successfully submitted registrations can\'t be edited any more. So it\'s recommended to submit right before the deadline in order to allow for more opportunities of edition and optimization'), ('104', '已提交', 'Submitted'), ('105', '提交', 'Submit'), ('106', '2016年度成功设计大赛新春启动会在沪成功举办', '2016 Successful Design Awards are open'), ('107', '国内首个评估设计商业和社会价值的<br/>全球综合类设计大赛', 'What is Successful Design Award ?'), ('108', '成功设计大赛是中国最具影响力的国际化商业设计大赛。', 'Successful Design Award is the first international awards to evaluate designs based on their business & social impact.'), ('109', '创立于2006年，历经多年辉煌积淀，它已成为设计创新与管理的标杆。不同于其他设计赛事，成功设计大赛不仅仅关注设计风格，更注重的是从管理者的角度审视设计，挖掘设计背后的策略、流程以及对企业、社会、环境、人类生活带来的价值和深远影响。', 'SuccessfulDesign.org created the Successful Design Awards to reward and empower the individuals, teams and companies that make up our industry. We have spent the last 10 years raising the profile of design among industry, academia and media circles.'), ('110', '我们的舞台既有众多知名企业科勒、飞利浦、惠普、奔驰、宝马、海尔、长虹等，也有恩诺童、Parallaxe在此一夜成名，被打造成知名设计品牌。至今，已有超过20多个国家、20多个行业，1200名的参赛企业获得了成功设计大赛的殊荣。', 'We work with a team of global leaders to make a difference by recognizing excellence in designs and innovations that create positive social and strategic impact and not just market success. '), ('111', '立即参加', 'Register Now'), ('112', '大赛类别', 'Project Categories'), ('113', '我们欢迎所有对中国市场产生积极影响的设计师和设计企业前来参赛。', 'Designers and companies looking to actively create a positive impact and influence in the Chinese market are welcomed to participate.'), ('114', '其设计作品需在 <span style=\"color:#f90;\">2016年10月前在中国市场上市或已发布</span>', 'Our only caveat is that your submission is restricted to products developed during 2015 <span style=\"color:#f90;\">and/or launched by October 2016</span>.'), ('115', '点击类别查看详细分类', 'Click categories for the details'), ('116', '商业<br/>模式', 'Business<br/>Model'), ('117', '社会<br/>创新', 'Social<br/>Innovation'), ('118', '众创<br/>空间', 'Creative<br/>Space'), ('119', '电子、通信及数码产品', 'Digital and electronic devices'), ('120', '工业机械及工具', 'Tools and Machinery'), ('121', '家居及家用电器类', 'Home ware'), ('122', '办公类用品', 'Office ware'), ('123', '医疗设备', 'Health medical and safety'), ('124', '娱乐设备', 'Entertainment'), ('125', '交通工具', 'Transportation'), ('126', '酒店', 'Hospitality'), ('127', '1000平米以下工作空间', 'Wrokplace under 1000sqm'), ('128', '公共空间', 'Public space'), ('129', '商业空间', 'Commercial space'), ('130', '1000平米以上工作空间', 'Wrokplace above1000sqm'), ('131', '多层住宅', 'Multi-story residential'), ('132', '单个住宅', 'Single residential'), ('133', '景观设计', 'Landscape Design'), ('134', '展览/展示', 'Exihibition'), ('135', '活动策划', 'Event Plan'), ('136', '广告策划', 'Advertising'), ('137', '网站', 'Website'), ('138', '游戏', 'Game'), ('139', 'UI设计', 'UI'), ('140', 'APP应用', 'APP'), ('141', '包装设计', 'Package'), ('142', '企业形象识别', 'CIS'), ('143', '海报', 'Posters'), ('144', '字体', 'Fonts'), ('145', '创新的管理模式', 'Creative management model'), ('146', '商业模式', 'B-B/B-C'), ('147', '推动社会发展的非营利性项目', 'Non-profit project that drove social development'), ('148', '联合空间', 'Co-working space'), ('149', '孵化器', 'Incubator'), ('150', '创业加速器', 'Start-up accelerator'), ('151', '创新空间', 'Creative space'), ('152', '创业咖啡', 'Creative Coffee'), ('153', '获奖收益', 'Winners benefits'), ('155', '更多定制推广服务', 'More customized promotion packages'), ('156', '获奖作品将有机会被推荐接受我们合作媒体和网站的专访，获得价值百万的媒体传播。可针对您的需求，定制更加多元化的线上线下的推广服务，展示您的获奖作品，加深您的品牌影响力。', 'Each winner can customize their promotion plan according to their different needs, in order to exposure the product and expand the brand influence.'), ('157', '合作赛事推荐', 'Recommendation to our partner awards'), ('158', '成功设计大赛的获奖者将被免费推荐到亚洲设计管理论坛以及台湾的金点奖，角逐国际大奖。', 'Winning submission will be recommended to our partners at Asia Design Management and Taiwan Golden Pin Design. Entry fees for these competitions will be waived – i.e. participation with Successful Design Awards offers you three opportunities to win.'), ('159', '成功设计自媒体传播', 'Media Promotion'), ('160', '您的获奖作品将在颁奖典礼后发布于成功设计线上展览及微信传播！您将成为行业标杆之一，名列杰出企业之列。这更是为您提供与社会大众交流的平台，以提高您的品牌影响力。', 'As with previous years Successful Design.org will help bring together local and international media to maximize public exposure for the winning designs. Winners of the Successful Design Awards will have the opportunity to be interviewed by our media partners either free or if partners require at a discount.'), ('161', '证书与奖杯', 'Certificate and Trophy'), ('162', '颁奖典礼当日，获奖者将获得一套相关获奖作品的获奖证书和奖杯，以彰显获奖者的殊荣。另外在活动后，获奖者可以获得自己的电子版获奖证书，以作宣传使用。', 'After the results are announced, each winner will receive a certificate and trophy to honor their achievement. Meanwhile, they can download the digital file of the certificate online.'), ('163', '成功设计之夜', 'Successful Design Night - Awards Ceremony'), ('164', '获奖者将受邀参加于2016年9月举行的“ 成功设计之夜”颁奖典礼。最成功设计大奖得主将在聚光灯下接受颁奖。届时将有各大主流媒体前来报道， 是您进行品牌宣传的极好机会。另外也会有专业摄影师拍照记录，照片可在活动后下载，作为您宣传的助力。', 'The ceremony provides you the opportunity to exchange ideas with international design leaders, and rub shoulders with industry and brand leader. We invite you to seize your moment under the spotlight and showcase your credentials to our professional audience of media and potential business partners.'), ('165', '成功设计大奖年鉴', 'Awards Yearbook'), ('166', '今年所有获奖作品将被收录入《2016 成功设计大赛》年鉴，以展示获奖作品、分享成功设计和团队背后的故事。', 'All winning entries will be profiled in the 2016 Successful Design Awards Yearbook, acknowledged by the global design community as an invaluable reference tool for gauging the cutting edge of design and identifying design leaders in China. All award winners will receive a copy of yearbooks.'), ('167', '成功设计线上展览', 'Online Exhibition'), ('168', '您的获奖作品将在颁奖典礼后发布于成功设计线上展览，更棒的是这是一项永久福利，展览没有时间限制！您将成为行业标杆之一，名列杰出企业之列。这更是为您提供与社会大众交流的平台，以提高您的品牌影响力。', 'All winners will be profiled in both English and Chinese on the Successful Design Awards website. These profiles will be permanently displayed.'), ('169', '成功设计标识', 'Endorsement Mark'), ('170', '获奖标识只要您的参赛作品获奖，您就可以使用成功设计标识。成功设计标识的使用不受时间或区域限制，线上线下、广告新闻均可使用。得奖者可在成功设计平台官网下载获奖标识。', 'Once the competition results have been announced winners will be granted the right to display the Successful Design Awards Endorsement Mark on their promotion materials and describe themselves as winners of the Successful Design Awards 2015.'), ('171', '大赛时间表', 'Timeline'), ('172', '奖项设置', 'Awards Categories'), ('173', '重置密码', 'Reset Password'), ('174', '请输入注册邮箱', 'Please enter the registration email'), ('175', '重置邮件已发送，请登录您的邮箱查收', 'The resetting email has been sent, please check your mailbox '), ('176', '已有账号', 'Login'), ('177', '注册账号', 'Sign Up'), ('178', '账号登录', 'Login'), ('179', '密码', 'Password'), ('180', '注册账号', 'Sign Up'), ('181', '忘记密码', 'Forget Password'), ('182', '登陆', 'Login'), ('183', '账号注册', 'Sign Up'), ('184', '重复密码', 'Repeat Password'), ('185', '已经激活？前往登陆', 'Login'), ('186', '账号激活', 'Activate Account'), ('187', '激活邮件已发送，请激活邮箱后登陆', 'The email for activation has been sent, please check your mailbox'), ('188', '没有收到邮件？', 'No email received?'), ('189', '重发', 'Resend'), ('190', '输入新密码', 'Enter new password'), ('191', '重复新密码', 'Repeat new password'), ('192', '编辑', 'Edit'), ('193', '公司地址（中文）', 'Company Address (Chinese)'), ('194', '公司地址（英文）', 'Company Address (English)'), ('195', '传真', 'Fax'), ('196', '邮编', 'Zipcode'), ('197', '更换头像', 'Change Portrait'), ('198', '昵称', 'Nickname'), ('199', '性别', 'Gender'), ('200', '男', 'Male'), ('201', '女', 'Female'), ('202', '已支付', 'Paid'), ('203', '未支付', 'Unpaid'), ('204', '未提交', 'Unsubmitted'), ('205', '支付', 'Pay'), ('206', '补充文件', 'Complementary File'), ('207', '全部作品', 'All'), ('208', '信息未完成', 'Incomplete'), ('209', '未付款作品', 'Unpaid'), ('210', '未提交作品', 'Unsubmitted'), ('211', '作品名称', 'Entry Name'), ('212', '信息完整度', 'Details'), ('213', '作品状态', 'Entry Status'), ('214', '操作', ''), ('215', '详情', 'Details'), ('217', '* 早鸟报名开放至3月31日（1800RMB）', '* Early bird registration close on March 31th (1800 RMB)'), ('218', '** 一般报名开放至5月31日（2000RMB）', '** Hard line registration close on May 31th (2000 RMB)'), ('219', '*** 获奖者需支付后期宣传推广费用（3000RMB）', '*** All of the winners shall pay 3000 RMB for the promotion after winning'), ('220', '年度成功市场价值营销大奖', 'Market Value'), ('221', '年度成功绿色设计大奖', 'Green Design'), ('222', '年度成功社会影响大奖', 'Social Impact'), ('223', '年度成功客户体验大奖', 'Customer Experience'), ('224', '年度成功品牌价值大奖', 'Brand Value'), ('225', '年度成功组织影响大奖', 'Organization Impacts'), ('226', '年度成功商业模式大奖', 'Business Model'), ('227', '年度成功创业大奖', 'Design Start up'), ('228', '年度成功策略大奖', 'Strategy'), ('229', '年度成功领导力大奖', 'Leadership'), ('230', '选择支付方式', 'Select your payment method'), ('231', '已经支付', 'Confirm payment'), ('232', '请在转账备注里填写', 'Please fill in transfer remark with'), ('233', '公司信息＋转账人＋联系手机＋作品名称', 'Company + Your Name + Cellphone + Entry Name'), ('234', '，这将作为作品支付的唯一凭证', ', which will be the only evidence of your payment'), ('235', '户名：上海成功商务服务有限公司', 'Account Name: Successful Commercial Service Ltd, Shanghai'), ('236', '开户行：工行静安支行', 'Bank: Industrial and Commercial Bank of China, Jingan Branch, Shanghai'), ('237', '帐号：1001211309207989444', 'Account Number: 1001211309207989444'), ('238', '外币：ICBKCNBJSHI', 'Foreign Currency: ICBKCNBJSHI'), ('239', '人民币：102290021133', 'RMB: 102290021133'), ('240', '恭喜，你已经完成支付，将很快收到一封确认邮件', 'Congratulation, you finished the payment step. You will receive a confirmation email soon'), ('241', '请记住，在5月31日之前，你需要填写作品详情并提交', 'Remember, you have until may 31th to filling up details and submit your project'), ('242', '修改密码', 'Change Password'), ('243', '作品详情', 'Registration Details'), ('244', '支付中', 'Paying'), ('245', '待管理员确认支付后方可编辑作品详情', 'you will be able to edit the details once your payment is confirmed'), ('246', '联系信息', 'Contact Information'), ('247', '系统管理', 'Admin');
COMMIT;

-- ----------------------------
--  Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `gender` int(1) NOT NULL,
  `cellphone` varchar(255) NOT NULL,
  `portrait` varchar(255) NOT NULL,
  `companyc` varchar(255) NOT NULL,
  `companye` varchar(255) NOT NULL,
  `companyp` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `companyac` varchar(255) NOT NULL,
  `companyae` varchar(255) NOT NULL,
  `activate` int(11) NOT NULL,
  `activate_key` varchar(255) NOT NULL,
  `reset_key` varchar(255) NOT NULL,
  `role` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
