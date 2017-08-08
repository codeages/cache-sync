CREATE TABLE `cache_sync` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `op` enum('set','del') NOT NULL COMMENT 'Cache操作',
  `k` varchar(1024) NOT NULL DEFAULT '' COMMENT 'Cache的Key',
  `v` text COMMENT '当op为set时，v为set的值',
  `t` int(10) unsigned NOT NULL  COMMENT '时间戳', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=663 DEFAULT CHARSET=utf8;