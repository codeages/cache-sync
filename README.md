Cache Sync
==========

[![Build Status](https://travis-ci.org/codeages/cache-sync.svg?branch=master)](https://travis-ci.org/codeages/cache-sync)

应用为了解决单数据中心的单点问题，往往会在多数据中心部署。主数据中心的 MySQL 通常会通过 MySQL 复制的方式同步到从数据中心，而通常从数据中心也有 Cache 层，这时 Cache 层无法感知数据库层数据有更新，导致读到旧数据。因此当主数据中心的 MySQL 数据有更新时，需要通知从数据中心清除 Cache，这个项目就是为了这个需求而生。

## 实现原理

主数据中心更新数据时，同时将需要更新的 Cache Key 记录到 MySQL 的表中，然后通过 MySQL 复制同步到从数据中心。从数据中心启动一个后台服务，查询有更新的 Cache Key 记录，清除Cache Key 或重置 Cache Key 的值。

**注意：该方案适用于数据更新不怎么频繁的场景，数据更新频繁可以采用解析 MySQL Binlog 做同步更新 Cache 的方案。**

## Usage

**创建Cache同步记录表**

```sql
CREATE TABLE `cache_sync` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `op` enum('set','del') NOT NULL COMMENT 'Cache操作',
  `k` varchar(1024) NOT NULL DEFAULT '' COMMENT 'Cache的Key',
  `v` text COMMENT '当op为set时，v为set的值',
  `t` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=663 DEFAULT CHARSET=utf8;
```
