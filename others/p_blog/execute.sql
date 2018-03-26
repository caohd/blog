SHOW TABLES ;

SHOW COLUMNS FROM b_picture;

CREATE TABLE b_apicture (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  aid INT UNSIGNED NOT NULL COMMENT '朋友圈id',
  src CHAR(64) NOT NULL COMMENT '图片路径'
) DEFAULT CHARSET utf8;
CREATE TABLE b_friend (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '发表博文的用户id',
  `content` VARCHAR(300) NOT NULL COMMENT '内容',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '发表时间',
  `comment` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `belike` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET utf8;
DROP TABLE b_friend;
SHOW tables;
SHOW CREATE TABLE b_friend;
SHOW COLUMNS FROM b_apicture;
SHOW COLUMNS FROM b_comment;
SHOW COLUMNS FROM b_article;

SHOW CREATE TABLE b_article ;

SHOW CREATE TABLE b_article;

ALTER TABLE b_article
    MODIFY
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '上次修改的时间';

ALTER TABLE b_chat
    MODIFY
  belongid INT(10) UNSIGNED
  COMMENT '属于哪个用户id的聊天';

ALTER TABLE b_chat
    ADD
  readflag TINYINT(1) DEFAULT 1;

SELECT *
FROM b_chat;

SELECT *
FROM b_message;
SHOW COLUMNS FROM b_message;

SELECT b_comment.*, b_user.nickname, b_moment.id AS mid, b_article.id AS aid
FROM b_comment
  LEFT JOIN b_user ON b_comment.uid=b_user.id
  LEFT JOIN b_article ON b_article.id=b_comment.aid AND b_comment.typeof=1
  LEFT JOIN b_moment ON b_moment.id=b_comment.aid AND b_comment.typeof=2
WHERE b_comment.aid IN (SELECT id FROM b_article WHERE b_article.uid=16)
  OR b_comment.aid IN (SELECT id FROM b_moment WHERE b_moment.uid=16)
ORDER BY b_comment.time DESC

