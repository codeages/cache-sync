Cache Sync
==========

[![Build Status](https://travis-ci.org/codeages/cache-sync.svg?branch=master)](https://travis-ci.org/codeages/cache-sync)

## Usage

**创建Cache同步记录表**

```sql
CREATE TABLE `cache_sync` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `op` enum('set','del') NOT NULL COMMENT 'Cache操作',
  `k` varchar(1024) NOT NULL DEFAULT '' COMMENT 'Cache的Key',
  `v` text COMMENT '当op为set时，v为set的值',
  `created_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=663 DEFAULT CHARSET=utf8;
```
