-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-12-17 13:02:30
-- 服务器版本： 10.1.28-MariaDB
-- PHP Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `p_blog`
--

-- --------------------------------------------------------

--
-- 表的结构 `b_album`
--

CREATE TABLE `b_album` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '发布者用户id',
  `name` char(32) NOT NULL DEFAULT '新相册' COMMENT '相册名称',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  `pictures` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片数',
  `pageview` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '浏览量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_album`
--

INSERT INTO `b_album` (`id`, `uid`, `name`, `time`, `pictures`, `pageview`) VALUES
(2, 2, 'ssss', '2017-12-16 03:06:31', 0, 0),
(5, 2, '新相册', '2017-12-16 07:04:12', 2, 0);

-- --------------------------------------------------------

--
-- 表的结构 `b_article`
--

CREATE TABLE `b_article` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '发表博文的用户id',
  `title` char(64) NOT NULL COMMENT '文章标题',
  `content` text NOT NULL COMMENT '内容',
  `fromid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '转自哪个用户id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次修改的时间',
  `comment` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数',
  `repost` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '转发数',
  `belike` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点赞数',
  `readers` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读量',
  `flag` tinyint(1) NOT NULL COMMENT '文章的状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_article`
--

INSERT INTO `b_article` (`id`, `uid`, `title`, `content`, `fromid`, `time`, `comment`, `repost`, `belike`, `readers`, `flag`) VALUES
(1, 1, '童话故事', '《诚信的故事》《诚信的意义》《空花盆》《生活之本》《不吃偷来的石榴》《破碎的啤酒瓶》《华盛顿和樱桃树》《谁是英雄》《破碎的塑像》《晏殊信誉的树立》《孩子的本性》《一个贫穷的小提琴手》《自信的故事》《自信的意义》《坚持下去》《自信》《昂起头来真美》《过上好日子》《两国交战》《玉米地》《奥运冠军的成长》《英国首相》《背上的疤痕》《谦虚的故事》《谦虚的意义》《一封感谢信》《艺人与儿子》《孔子谈谦虚》《我叫陈阿土》《晏婴的车夫》《和你玩的人是谁》《行行出状元》《鹰王的代价》《名人谦虚的故事》《宽容的故事》《宽容的意义》《谁将继承王位》《宽容的故事》《化解仇恨的最好办法》《宰相肚里能撑船》《换东西》《善良的故事》《勤奋的故事》《积极、良好心态的故事》《勤于思考的故事》《节俭的故事》《协作的故事》《智慧的故事》《特别邮票(十二生肖系列之鸡)》《兔毛公司推销员(十二生肖系）》《翼展(超短篇系列)》《斑虎和雪兔的故事(十二生肖)》《虎王逗鼠(十二生肖系列之虎)》《《奔腾验钞机》精彩语言辑录》《红鼻子火车(超短篇系列)》《鼠王做寿(十二生肖系列之鼠)》《蓝耳朵飞船(超短篇系列)》', 0, '2017-12-14 11:32:30', 2, 0, 0, 0, 0),
(2, 1, '马云的三次高考', ' 第一次高考，遭遇滑铁卢。尽管马云的英语在同龄人中显得出奇的好，但他的数学却实在太差，只得了1分，全面败北。这之后他当过秘书、搬运工，后来踩着三轮车帮人家送书。有一次，他给一家文化单位送书时，捡到一本名为《人生》的小说。那是著名作家路遥的代表作。小说的主人公，农村知识青年高加林曲折的生活道路给马云带来了许多感悟。高加林是一个很有才华的青年，他对理想有着执著的追求，但在他追求理想的过程中，往往每向前靠近一步，就会有一种阻力横在眼前，使他得不到真正施展才华的机会，甚至又不得不面对重新跌落到原点的局面。', 0, '2017-12-15 06:50:48', 0, 0, 0, 6, 0),
(3, 1, '活稻草人', '每天早上，杰米·福克斯上身穿两层厚厚的毛衣，外裹一件橙色带帽子的防雨服，下身穿一条厚厚的保暖裤，脚上套三双袜子，骑着自行车来到工作地点。在油菜地的正中央，他支起帆布躺椅，开始工作。除了手机来电，以及零星过往的遛狗人和农夫，杰米·福克斯要在寂静的田野中打发8个小时。为了让工作变得更有趣，杰米·福克斯从家里带来了书、一把夏威夷四弦琴、一把六角手风琴和一个牛颈铃，“我喜欢弹钢琴，但是，自行车可驮不起这个大件儿。”杰米·福克斯笑着说。', 0, '2017-12-16 01:56:28', 0, 1, 0, 0, 0),
(4, 1, '别对自己说不可能', '为了自强自立，更为了用他的拼搏精神和不甘向命运低头的意志去激励别人，约翰·库提斯在向命运和自身残疾挑战的同时，喜欢上了演讲事业。在8年多的激情演讲中，他“走”过190个国家和地区，成为闻名各国的传奇人物，并被誉为世界激励大师。在“走”向各个国家和地区的演讲征程中，他经常会用一只胳膊支撑着身体，腾出另一只手推动滑轮，驱动不到1米高的躯体在地面上快速前行。无论“走”到哪里，无论遇到多少困难，他的头始终高昂着，神情中甚至有几分骄傲。当有人对他如此“卖力”和不珍惜自已的身体有些不解时，他总是充满自信地说：“我这样做的唯一原因就是为了激励别人，证明自己没有什么不可能！”\r\n', 0, '2017-12-15 10:55:12', 0, 0, 1, 0, 0),
(5, 1, 'dfdfdff', '因为残疾和病痛，1970年出生的约翰·库提斯吃了多少苦、受了多少罪，他自己也说不清。他记得上小学时，有一次，一群孩子用胶布封上他的嘴，把他绑起来扔进了垃圾桶，然后点上火企图把他烧死。为了活命，他在垃圾桶里拼命扭动，直到把身边的火苗扑灭、被人发现。17岁那年，由于下肢病情恶化，他不得不从腰部截肢，剩下的身高不足1米。从此，他成为名副其实的仅有上半身的矮人。悲惨的命运总是喜欢拿他开玩笑，29岁那年，他又患上了癌症。\r\n\r\n', 0, '2017-12-08 13:07:46', 0, 0, 0, 0, 0),
(6, 2, '哈哈哈哈', '就为佛号位哦放寒假我I女都似乎滚哦I未婚女哦问V回味io减肥我I开户费弄I基础ip而我非农his东风街你小雏菊io金额没出息你居然快疯了愤怒的是佛IV喜剧io士大夫V么我I家', 0, '2017-12-14 13:17:36', 0, 0, 0, 0, 0),
(7, 2, '测试服价位哦IF奖哦诶', '大师傅的访问扶额未婚夫文化打飞机哦潍坊哪的三轮车蛮辛苦了吗吃了看到我曾经覅耳机佛挡杀佛将诶哦我减肥法及老师的空间诶王妃王妃江苏大丰', 0, '2017-12-14 13:29:29', 0, 0, 0, 0, 0),
(10, 2, '活稻草人', '每天早上，杰米·福克斯上身穿两层厚厚的毛衣，外裹一件橙色带帽子的防雨服，下身穿一条厚厚的保暖裤，脚上套三双袜子，骑着自行车来到工作地点。在油菜地的正中央，他支起帆布躺椅，开始工作。除了手机来电，以及零星过往的遛狗人和农夫，杰米·福克斯要在寂静的田野中打发8个小时。为了让工作变得更有趣，杰米·福克斯从家里带来了书、一把夏威夷四弦琴、一把六角手风琴和一个牛颈铃，“我喜欢弹钢琴，但是，自行车可驮不起这个大件儿。”杰米·福克斯笑着说。', 1, '2017-12-15 06:34:41', 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `b_chat`
--

CREATE TABLE `b_chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `fromid` int(10) UNSIGNED NOT NULL COMMENT '来自哪个用户id',
  `belongid` int(10) UNSIGNED NOT NULL COMMENT '属于哪个用户id的聊天',
  `toid` int(10) UNSIGNED NOT NULL COMMENT '发去哪个用户id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '发送时间',
  `content` tinytext NOT NULL COMMENT '发送内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_chat`
--

INSERT INTO `b_chat` (`id`, `fromid`, `belongid`, `toid`, `time`, `content`) VALUES
(2, 16, 16, 2, '2017-12-17 11:45:17', '你好啊'),
(3, 16, 2, 2, '2017-12-17 11:45:17', '你好啊'),
(4, 17, 17, 23, '2017-12-17 11:50:43', '呜呜呜'),
(5, 17, 23, 23, '2017-12-17 11:50:43', '呜呜呜'),
(6, 23, 17, 17, '2017-12-17 11:51:05', '怎么了？'),
(7, 23, 23, 17, '2017-12-17 11:51:05', '怎么了？');

-- --------------------------------------------------------

--
-- 表的结构 `b_comment`
--

CREATE TABLE `b_comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `aid` int(10) UNSIGNED NOT NULL COMMENT '文章id\\评论图片',
  `typeof` tinyint(1) NOT NULL COMMENT '评论种类',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '发表评论的用户id',
  `content` tinytext NOT NULL COMMENT '评论内容',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '评论时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_comment`
--

INSERT INTO `b_comment` (`id`, `aid`, `typeof`, `uid`, `content`, `time`) VALUES
(3, 1, 1, 2, '这真是是一篇好文章！\r\n      ', '2017-12-13 11:30:31'),
(4, 2, 1, 2, 'emmm？？？\r\n      ', '2017-12-13 11:35:26'),
(5, 2, 1, 2, '我来刷评论了', '2017-12-13 11:39:49'),
(6, 2, 1, 2, '再刷个评论怎样？开心不开心\r\n      ', '2017-12-13 11:43:32'),
(7, 2, 1, 2, '我再试试hhhhhhhhhhhhhhhhhhh', '2017-12-13 11:44:05'),
(8, 2, 1, 2, '我不信了', '2017-12-13 11:44:36'),
(9, 2, 1, 2, '掉', '2017-12-13 11:44:58'),
(10, 2, 1, 2, '掉你好', '2017-12-13 11:45:46'),
(11, 2, 1, 2, '擦送你吗', '2017-12-13 11:47:34'),
(12, 2, 1, 2, '操！', '2017-12-13 11:48:06'),
(13, 2, 1, 2, '？？？？？？？', '2017-12-13 11:48:34'),
(14, 2, 1, 2, 'ha ???\r\n      ', '2017-12-13 11:49:10'),
(15, 2, 1, 2, 'what??\r\n      ', '2017-12-13 11:58:14'),
(16, 2, 1, 2, 'cao ni  ma ??\r\n      ', '2017-12-13 11:59:00'),
(17, 2, 1, 2, 'hehehda', '2017-12-13 11:59:11'),
(18, 2, 1, 2, 'hehehda', '2017-12-13 11:59:55');

-- --------------------------------------------------------

--
-- 表的结构 `b_like`
--

CREATE TABLE `b_like` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '谁点的赞（用户id）',
  `typeof` tinyint(1) NOT NULL COMMENT '点赞类型',
  `aid` int(10) UNSIGNED NOT NULL COMMENT '博文id\\图片id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_like`
--

INSERT INTO `b_like` (`id`, `uid`, `typeof`, `aid`) VALUES
(1, 2, 1, 4),
(3, 2, 1, 5);

-- --------------------------------------------------------

--
-- 表的结构 `b_location`
--

CREATE TABLE `b_location` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `area` char(16) NOT NULL COMMENT '地区',
  `city` char(32) NOT NULL COMMENT '城市'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_location`
--

INSERT INTO `b_location` (`id`, `area`, `city`) VALUES
(1, '黑龙江', '哈尔滨'),
(2, '黑龙江', '齐齐哈尔'),
(3, '黑龙江', '鸡西'),
(4, '黑龙江', '鹤岗'),
(5, '黑龙江', '双鸭山'),
(6, '黑龙江', '大庆'),
(7, '黑龙江', '伊春'),
(8, '黑龙江', '佳木斯'),
(9, '黑龙江', '七台河'),
(10, '黑龙江', '牡丹江'),
(11, '黑龙江', '黑河'),
(12, '黑龙江', '绥化'),
(13, '黑龙江', '大兴安岭地区'),
(14, '吉林', '长春'),
(15, '吉林', '吉林'),
(16, '吉林', '四平'),
(17, '吉林', '辽源'),
(18, '吉林', '通化'),
(19, '吉林', '白山'),
(20, '吉林', '延边朝鲜族自治州'),
(21, '吉林', '松原'),
(22, '吉林', '白城'),
(24, '辽宁', '沈阳'),
(25, '辽宁', '大连'),
(26, '辽宁', '鞍山'),
(27, '辽宁', '抚顺'),
(28, '辽宁', '本溪'),
(29, '辽宁', '丹东'),
(30, '辽宁', '锦州'),
(31, '辽宁', '营口'),
(32, '辽宁', '阜新'),
(33, '辽宁', '辽阳'),
(34, '辽宁', '盘锦'),
(35, '辽宁', '铁岭'),
(36, '辽宁', '朝阳'),
(37, '辽宁', '葫芦岛'),
(38, '山东', '济南'),
(39, '山东', '青岛'),
(40, '山东', '淄博'),
(41, '河北', '衡水'),
(42, '河北', '廊坊'),
(43, '河北', '沧州'),
(44, '河北', '张家口'),
(45, '河北', '保定'),
(46, '河北', '邢台'),
(47, '河北', '邯郸'),
(48, '河北', '秦皇岛'),
(49, '河北', '唐山'),
(50, '河北', '石家庄'),
(51, '河南', '平顶山'),
(52, '河南', '洛阳'),
(53, '河南', '开封'),
(54, '河北', '承德'),
(55, '河南', '郑州'),
(81, '山东', '枣庄'),
(82, '山东', '东营'),
(83, '山东', '烟台'),
(84, '山东', '潍坊'),
(85, '山东', '济宁'),
(86, '山东', '泰安'),
(87, '山东', '威海'),
(88, '山东', '日照'),
(89, '山东', '莱芜'),
(90, '山东', '临沂'),
(91, '山东', '德州'),
(92, '山东', '聊城'),
(93, '山东', '滨州'),
(94, '山东', '菏泽'),
(95, '山西', '太原'),
(96, '山西', '大同'),
(97, '山西', '阳泉'),
(98, '山西', '长治'),
(99, '山西', '晋城'),
(100, '山西', '朔州'),
(101, '山西', '晋中'),
(102, '山西', '运城'),
(103, '山西', '忻州'),
(104, '山西', '临汾'),
(105, '山西', '吕梁'),
(106, '陕西', '西安'),
(107, '陕西', '铜川'),
(108, '陕西', '宝鸡'),
(109, '陕西', '咸阳'),
(110, '陕西', '渭南'),
(111, '陕西', '延安'),
(112, '陕西', '汉中'),
(113, '陕西', '榆林'),
(114, '陕西', '安康'),
(115, '陕西', '商洛'),
(116, '山东', '枣庄'),
(117, '山东', '东营'),
(118, '山东', '烟台'),
(119, '山东', '潍坊'),
(120, '山东', '济宁'),
(121, '山东', '泰安'),
(122, '山东', '威海'),
(123, '山东', '日照'),
(124, '山东', '莱芜'),
(125, '山东', '临沂'),
(126, '山东', '德州'),
(127, '山东', '聊城'),
(128, '山东', '滨州'),
(129, '山东', '菏泽'),
(130, '山西', '太原'),
(131, '山西', '大同'),
(132, '山西', '阳泉'),
(133, '山西', '长治'),
(134, '山西', '晋城'),
(135, '山西', '朔州'),
(136, '山西', '晋中'),
(137, '山西', '运城'),
(138, '山西', '忻州'),
(139, '山西', '临汾'),
(140, '山西', '吕梁'),
(141, '陕西', '西安'),
(142, '陕西', '铜川'),
(143, '陕西', '宝鸡'),
(144, '陕西', '咸阳'),
(145, '陕西', '渭南'),
(146, '陕西', '延安'),
(147, '陕西', '汉中'),
(148, '陕西', '榆林'),
(149, '陕西', '安康'),
(150, '陕西', '商洛'),
(151, '河南', '驻马店'),
(152, '河南', '周口'),
(153, '河南', '信阳'),
(154, '河南', '商丘'),
(155, '河南', '南阳'),
(156, '河南', '三门峡'),
(157, '河南', '漯河'),
(158, '河南', '许昌'),
(159, '河南', '济源'),
(160, '河南', '濮阳'),
(161, '河南', '焦作'),
(162, '河南', '新乡'),
(163, '河南', '鹤壁'),
(164, '河南', '安阳'),
(165, '湖北', '神农架林区'),
(166, '湖北', '天门'),
(167, '湖北', '潜江'),
(168, '湖北', '仙桃'),
(169, '湖北', '恩施土家族苗族自治州'),
(170, '湖北', '随州'),
(171, '湖北', '咸宁'),
(172, '湖北', '黄冈'),
(173, '湖北', '荆州'),
(174, '湖北', '孝感'),
(175, '湖北', '荆门'),
(176, '湖北', '鄂州'),
(177, '湖北', '襄樊'),
(178, '湖北', '宜昌'),
(179, '湖北', '十堰'),
(180, '湖北', '黄石'),
(181, '湖北', '武汉'),
(182, '湖南', '益阳'),
(183, '湖南', '张家界'),
(184, '湖南', '常德'),
(185, '湖南', '岳阳'),
(186, '湖南', '邵阳'),
(187, '湖南', '衡阳'),
(188, '湖南', '湘潭'),
(189, '湖南', '株洲'),
(190, '湖南', '长沙'),
(191, '湖南', '湘西土家族苗族自治州'),
(192, '湖南', '怀化'),
(193, '湖南', '永州'),
(194, '湖南', '郴州'),
(195, '湖南', '娄底'),
(196, '海南', '琼中黎族苗族自治县'),
(197, '海南', '保亭黎族苗族自治县'),
(198, '海南', '陵水黎族自治县'),
(199, '海南', '乐东黎族自治县'),
(200, '海南', '昌江黎族自治县'),
(201, '海南', '白沙黎族自治县'),
(202, '海南', '临高县'),
(203, '海南', '澄迈县'),
(204, '海南', '屯昌县'),
(205, '海南', '定安县'),
(206, '海南', '东方'),
(207, '海南', '万宁'),
(208, '海南', '文昌'),
(209, '海南', '儋州'),
(210, '海南', '琼海'),
(211, '海南', '五指山'),
(212, '海南', '三亚'),
(213, '海南', '海口'),
(214, '江苏', '宿迁'),
(215, '江苏', '泰州'),
(216, '江苏', '镇江'),
(217, '江苏', '扬州'),
(218, '江苏', '盐城'),
(219, '江苏', '淮安'),
(220, '江苏', '连云港'),
(221, '江苏', '南通'),
(222, '江苏', '苏州'),
(223, '江苏', '常州'),
(224, '江苏', '徐州'),
(225, '江苏', '无锡'),
(226, '江苏', '南京'),
(227, '江西', '九江'),
(228, '江西', '萍乡'),
(229, '江西', '景德镇'),
(230, '江西', '南昌'),
(231, '江西', '新余'),
(232, '江西', '抚州'),
(233, '江西', '上饶'),
(234, '江西', '宜春'),
(235, '江西', '吉安'),
(236, '江西', '赣州'),
(237, '江西', '鹰潭'),
(238, '广东', '云浮'),
(239, '广东', '揭阳'),
(240, '广东', '潮州'),
(241, '广东', '中山'),
(242, '广东', '东莞'),
(243, '广东', '清远'),
(244, '广东', '阳江'),
(245, '广东', '河源'),
(246, '广东', '汕尾'),
(247, '广东', '梅州'),
(248, '广东', '惠州'),
(249, '广东', '肇庆'),
(250, '广东', '茂名'),
(251, '广东', '湛江'),
(252, '广东', '江门'),
(253, '广东', '佛山'),
(254, '广东', '汕头'),
(255, '广东', '珠海'),
(256, '广东', '深圳'),
(257, '广东', '韶关'),
(258, '广东', '广州'),
(259, '广西', '河池'),
(260, '广西', '贺州'),
(261, '广西', '百色'),
(262, '广西', '玉林'),
(263, '广西', '贵港'),
(264, '广西', '钦州'),
(265, '广西', '防城港'),
(266, '广西', '北海'),
(267, '广西', '梧州'),
(268, '广西', '桂林'),
(269, '广西', '柳州'),
(270, '广西', '南宁'),
(271, '广西', '来宾'),
(272, '广西', '崇左'),
(273, '云南', '迪庆藏族自治州'),
(274, '云南', '怒江傈僳族自治州'),
(275, '云南', '德宏傣族景颇族自治州'),
(276, '云南', '大理白族自治州'),
(277, '云南', '西双版纳傣族自治州'),
(278, '云南', '文山壮族苗族自治州'),
(279, '云南', '红河哈尼族彝族自治州'),
(280, '云南', '楚雄彝族自治州'),
(281, '云南', '临沧'),
(282, '云南', '思茅'),
(283, '云南', '丽江'),
(284, '云南', '昭通'),
(285, '云南', '保山'),
(286, '云南', '玉溪'),
(287, '云南', '曲靖'),
(288, '云南', '昆明'),
(289, '贵州', '黔南布依族苗族自治州'),
(290, '贵州', '黔东南苗族侗族自治州'),
(291, '贵州', '毕节地区'),
(292, '贵州', '黔西南布依族苗族自治州'),
(293, '贵州', '铜仁地区'),
(294, '贵州', '安顺'),
(295, '贵州', '遵义'),
(296, '贵州', '六盘水'),
(297, '贵州', '贵阳'),
(298, '四川', '宜宾'),
(299, '四川', '眉山'),
(300, '四川', '南充'),
(301, '四川', '乐山'),
(302, '四川', '内江'),
(303, '四川', '遂宁'),
(304, '四川', '广元'),
(305, '四川', '绵阳'),
(306, '四川', '德阳'),
(307, '四川', '泸州'),
(308, '四川', '攀枝花'),
(309, '四川', '自贡'),
(310, '四川', '成都'),
(311, '四川', '广安'),
(312, '四川', '达州'),
(313, '四川', '雅安'),
(314, '四川', '巴中'),
(315, '四川', '资阳'),
(316, '四川', '凉山彝族自治州'),
(317, '四川', '甘孜藏族自治州'),
(318, '四川', '阿坝藏族羌族自治州'),
(319, '内蒙古自治区', '阿拉善盟'),
(320, '内蒙古自治区', '锡林郭勒盟'),
(321, '内蒙古自治区', '兴安盟'),
(322, '内蒙古自治区', '乌兰察布'),
(323, '内蒙古自治区', '巴彦淖尔'),
(324, '内蒙古自治区', '呼伦贝尔'),
(325, '内蒙古自治区', '鄂尔多斯'),
(326, '内蒙古自治区', '通辽'),
(327, '内蒙古自治区', '赤峰'),
(328, '内蒙古自治区', '乌海'),
(329, '内蒙古自治区', '包头'),
(330, '内蒙古自治区', '呼和浩特'),
(331, '宁夏回族自治区', '中卫'),
(332, '宁夏回族自治区', '固原'),
(333, '宁夏回族自治区', '吴忠'),
(334, '宁夏回族自治区', '银川'),
(335, '宁夏回族自治区', '石嘴山'),
(336, '福建', '福州'),
(337, '福建', '厦门'),
(338, '福建', '莆田'),
(339, '福建', '三明'),
(340, '福建', '泉州'),
(341, '福建', '漳州'),
(342, '福建', '南平'),
(343, '福建', '龙岩'),
(344, '福建', '宁德'),
(345, '西藏自治区', '阿里地区'),
(346, '西藏自治区', '那曲地区'),
(347, '西藏自治区', '日喀则地区'),
(348, '西藏自治区', '山南地区'),
(349, '西藏自治区', '昌都地区'),
(350, '西藏自治区', '拉萨'),
(351, '西藏自治区', '林芝地区'),
(352, '澳门', '澳门'),
(353, '浙江', '台州'),
(354, '浙江', '丽水'),
(355, '浙江', '舟山'),
(356, '浙江', '衢州'),
(357, '浙江', '金华'),
(358, '浙江', '绍兴'),
(359, '浙江', '湖州'),
(360, '浙江', '嘉兴'),
(361, '浙江', '温州'),
(362, '浙江', '宁波'),
(363, '浙江', '杭州'),
(364, '青海', '海西蒙古族藏族自治州'),
(365, '青海', '玉树藏族自治州'),
(366, '青海', '果洛藏族自治州'),
(367, '青海', '海南藏族自治州'),
(368, '青海', '黄南藏族自治州'),
(369, '青海', '海北藏族自治州'),
(370, '青海', '海东地区'),
(371, '青海', '西宁'),
(372, '新疆自治区', '五家渠'),
(373, '新疆自治区', '图木舒克'),
(374, '新疆自治区', '阿拉尔'),
(375, '新疆自治区', '石河子'),
(376, '新疆自治区', '阿勒泰地区'),
(377, '新疆自治区', '塔城地区'),
(378, '新疆自治区', '伊犁哈萨克自治州'),
(379, '新疆自治区', '和田地区'),
(380, '新疆自治区', '喀什地区'),
(381, '新疆自治区', '克孜勒苏柯尔克孜自治州'),
(382, '新疆自治区', '阿克苏地区'),
(383, '新疆自治区', '巴音郭楞蒙古自治州'),
(384, '新疆自治区', '博尔塔拉蒙古自治州'),
(385, '新疆自治区', '昌吉回族自治州'),
(386, '新疆自治区', '哈密地区'),
(387, '新疆自治区', '吐鲁番地区'),
(388, '新疆自治区', '克拉玛依'),
(389, '新疆自治区', '乌鲁木齐'),
(390, '北京市', '北京'),
(391, '上海市', '上海'),
(392, '天津市', '天津'),
(393, '重庆市', '重庆'),
(394, '甘肃', '甘南藏族自治州'),
(395, '甘肃', '临夏回族自治州'),
(396, '甘肃', '陇南'),
(397, '甘肃', '定西'),
(398, '甘肃', '庆阳'),
(399, '甘肃', '酒泉'),
(400, '甘肃', '平凉'),
(401, '甘肃', '张掖'),
(402, '甘肃', '武威'),
(403, '甘肃', '天水'),
(404, '甘肃', '白银'),
(405, '甘肃', '金昌'),
(406, '甘肃', '嘉峪关'),
(407, '甘肃', '兰州'),
(408, '安徽', '宣城'),
(409, '安徽', '池州'),
(410, '安徽', '亳州'),
(411, '安徽', '六安'),
(412, '安徽', '巢湖'),
(413, '安徽', '宿州'),
(414, '安徽', '阜阳'),
(415, '安徽', '滁州'),
(416, '安徽', '黄山'),
(417, '安徽', '安庆'),
(418, '安徽', '铜陵'),
(419, '安徽', '淮北'),
(420, '安徽', '马鞍山'),
(421, '安徽', '淮南'),
(422, '安徽', '蚌埠'),
(423, '安徽', '芜湖'),
(424, '安徽', '合肥'),
(425, '台湾', '台北'),
(426, '台湾', '高雄'),
(427, '台湾', '基隆'),
(428, '台湾', '台中'),
(429, '台湾', '台南'),
(430, '台湾', '新竹'),
(431, '台湾', '嘉义'),
(432, '香港', '香港');

-- --------------------------------------------------------

--
-- 表的结构 `b_message`
--

CREATE TABLE `b_message` (
  `id` int(10) UNSIGNED NOT NULL,
  `fromid` int(10) UNSIGNED NOT NULL COMMENT '来自哪个用户id发来的信息',
  `toid` int(10) UNSIGNED NOT NULL COMMENT '接收者用户id',
  `typeof` tinyint(1) NOT NULL COMMENT '消息种类',
  `isread` tinyint(1) DEFAULT '0' COMMENT '是否已读'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `b_moment`
--

CREATE TABLE `b_moment` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '发表博文的用户id',
  `content` varchar(300) NOT NULL COMMENT '内容',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发表时间',
  `comment` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数',
  `belike` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点赞数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='朋友圈';

--
-- 转存表中的数据 `b_moment`
--

INSERT INTO `b_moment` (`id`, `uid`, `content`, `time`, `comment`, `belike`) VALUES
(1, 2, 'www', '2017-12-17 07:35:22', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `b_mpicture`
--

CREATE TABLE `b_mpicture` (
  `id` int(10) UNSIGNED NOT NULL,
  `aid` int(10) UNSIGNED NOT NULL COMMENT '朋友圈id',
  `src` char(64) NOT NULL COMMENT '图片路径'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='朋友圈图片';

-- --------------------------------------------------------

--
-- 表的结构 `b_picture`
--

CREATE TABLE `b_picture` (
  `id` int(10) UNSIGNED NOT NULL,
  `aid` int(10) UNSIGNED NOT NULL COMMENT '所属相册id',
  `name` char(32) NOT NULL COMMENT '照片介绍名',
  `src` char(64) NOT NULL COMMENT '图片路径',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `belike` smallint(5) UNSIGNED NOT NULL COMMENT '点赞数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_picture`
--

INSERT INTO `b_picture` (`id`, `aid`, `name`, `src`, `time`, `belike`) VALUES
(3, 5, 'qqqq', 'b703c2cace.jpg', '2017-12-16 10:58:10', 0),
(7, 5, '你很垃圾诶', '98ea27da33.jpg', '2017-12-16 15:28:36', 0);

-- --------------------------------------------------------

--
-- 表的结构 `b_relationship`
--

CREATE TABLE `b_relationship` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid1` int(10) UNSIGNED NOT NULL COMMENT '用户1的用户id',
  `uid2` int(10) UNSIGNED NOT NULL COMMENT '用户2的用户id',
  `f1to2` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户1对用户2的关系',
  `f2to1` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户2对用户1的关系'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `b_relationship`
--

INSERT INTO `b_relationship` (`id`, `uid1`, `uid2`, `f1to2`, `f2to1`) VALUES
(1, 18, 2, 1, 0),
(3, 2, 16, 0, 1),
(4, 16, 2, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `b_user`
--

CREATE TABLE `b_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` char(32) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `nickname` char(32) NOT NULL COMMENT '昵称',
  `mail` char(32) DEFAULT '' COMMENT '邮箱',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户类型',
  `following` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注个数',
  `follower` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `logo` char(36) NOT NULL DEFAULT 'default.jpg' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '3' COMMENT '性别',
  `brief` char(32) NOT NULL DEFAULT '这个人很懒，什么都没有写' COMMENT '个性签名',
  `birthday` date DEFAULT '1900-01-01' COMMENT '生日',
  `visitors` smallint(6) NOT NULL DEFAULT '0' COMMENT '访客量',
  `locationid` smallint(10) UNSIGNED NOT NULL DEFAULT '258' COMMENT '所在地id 默认广州',
  `authflag` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息';

--
-- 转存表中的数据 `b_user`
--

INSERT INTO `b_user` (`id`, `username`, `password`, `nickname`, `mail`, `type`, `following`, `follower`, `logo`, `sex`, `brief`, `birthday`, `visitors`, `locationid`, `authflag`) VALUES
(1, 'test', '123456789', 'qqq', '', 0, 0, 0, 'DSC_5791.JPG', 1, 'dfsd', '2006-06-14', 0, 1, 1),
(2, 'chloe', '987654321', 'cccchloe', 'chloe@qq.com', 0, 0, 1, 'c2be063384.jpg', 1, 'ddddd', '2011-01-01', 0, 1, 1),
(16, 'caohd', '987654321', 'bluedog', '999@qq.com', 0, 1, 0, 'ff6f153ef0.jpg', 1, 'ilove欧姆哟', '1998-12-01', 0, 258, 1),
(17, 'wang', 'wang', 'keyi', '4444888@qq.com', 0, 0, 0, 'c8e3d380d9.jpg', 1, 'ememememme', '1992-10-09', 0, 2, 1),
(18, 'qqq', '66666', '6666', 'jjj@qqq.com', 0, 0, 0, 'd33d61f554.jpg', 0, '我不是蓝狗，我是死狗', '2011-01-01', 0, 1, 1),
(19, 'shuyin', '789456123', '舒茵', 'qqqq@qq.com', 0, 0, 0, '3e9ea2eaf6.jpg', 1, 'lalal我是舒茵噢', '2011-01-01', 0, 1, 1),
(20, 'jiahui', '999999', '嘉慧', 'jiahui@qq.com', 0, 0, 0, 'default.jpg', 1, '我是嘉慧', '1998-12-01', 0, 259, 1),
(21, 'caodd', '123456789', 'cccccdog', 'www@qq.com', 0, 0, 0, 'default.jpg', 1, 'llllll浏览量', '1998-12-01', 0, 259, 1),
(23, 'rexrex', '987654', 'RexGod', '888@qq.com', 0, 0, 0, 'default.jpg', 1, 'lalalal', '1996-12-01', 0, 261, 1),
(24, 'langou', '987654321', '蓝狗', 'qqqq@qq.com', 0, 0, 0, 'default.jpg', 1, '我是蓝狗啊啊啊啊啊', '1996-09-14', 0, 260, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `b_album`
--
ALTER TABLE `b_album`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_article`
--
ALTER TABLE `b_article`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_chat`
--
ALTER TABLE `b_chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_comment`
--
ALTER TABLE `b_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_like`
--
ALTER TABLE `b_like`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_location`
--
ALTER TABLE `b_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_message`
--
ALTER TABLE `b_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_moment`
--
ALTER TABLE `b_moment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_mpicture`
--
ALTER TABLE `b_mpicture`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_picture`
--
ALTER TABLE `b_picture`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_relationship`
--
ALTER TABLE `b_relationship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `b_user`
--
ALTER TABLE `b_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `nickname` (`nickname`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `b_album`
--
ALTER TABLE `b_album`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `b_article`
--
ALTER TABLE `b_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `b_chat`
--
ALTER TABLE `b_chat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `b_comment`
--
ALTER TABLE `b_comment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- 使用表AUTO_INCREMENT `b_like`
--
ALTER TABLE `b_like`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `b_location`
--
ALTER TABLE `b_location`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=433;

--
-- 使用表AUTO_INCREMENT `b_message`
--
ALTER TABLE `b_message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `b_moment`
--
ALTER TABLE `b_moment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `b_mpicture`
--
ALTER TABLE `b_mpicture`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `b_picture`
--
ALTER TABLE `b_picture`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `b_relationship`
--
ALTER TABLE `b_relationship`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `b_user`
--
ALTER TABLE `b_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
