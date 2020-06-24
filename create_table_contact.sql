CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'お問い合わせID',
  `name` varchar(255) DEFAULT NULL COMMENT '名前',
  `email` varchar(255) DEFAULT NULL COMMENT 'メールアドレス',
  `age` tinyint(4) DEFAULT NULL COMMENT '年齢',
  `gender` varchar(255) DEFAULT NULL COMMENT '性別',
  `lang` varchar(255) DEFAULT NULL COMMENT '言語',
  `message` varchar(255) DEFAULT NULL COMMENT 'メッセージ',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
