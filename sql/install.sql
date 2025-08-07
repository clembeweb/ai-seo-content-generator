CREATE TABLE IF NOT EXISTS `wp_aigsc_business_info` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `info` text NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wp_aigsc_urls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wp_aigsc_seo_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'generated',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_type` (`url`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
