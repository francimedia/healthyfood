-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 06, 2013 at 02:30 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `healthyfood`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(12) NOT NULL,
  `title` varchar(255) NOT NULL,
  `starttime` timestamp NULL DEFAULT NULL,
  `endtime` timestamp NULL DEFAULT NULL,
  `desc` text,
  `venue_id` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venue_id` (`venue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_meta_eventbrite`
--

CREATE TABLE `event_meta_eventbrite` (
  `id` int(12) NOT NULL,
  `eventbrite_id` bigint(20) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text,
  `status` varchar(32) DEFAULT NULL COMMENT 'change this to enum later',
  `organizer_name` varchar(255) DEFAULT NULL,
  `organizer_url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eventbrite_id` (`eventbrite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE `group` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_object`
--

CREATE TABLE `group_object` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `group_id` int(6) NOT NULL,
  `object_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `interaction`
--

CREATE TABLE `interaction` (
  `id` int(12) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `location_id` int(12) DEFAULT NULL,
  `interaction_type` enum('picture','comment','like') NOT NULL,
  `source` enum('instagram','foursquare') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `interaction_meta_common`
--

CREATE TABLE `interaction_meta_common` (
  `id` int(12) NOT NULL,
  `lat` decimal(13,10) DEFAULT NULL,
  `lng` decimal(13,10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `interaction_meta_foursquare`
--

CREATE TABLE `interaction_meta_foursquare` (
  `id` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `interaction_meta_instagram`
--

CREATE TABLE `interaction_meta_instagram` (
  `id` int(12) NOT NULL,
  `instagram_picture_id` varchar(48) NOT NULL,
  `user_id` int(12) NOT NULL,
  `username` varchar(255) NOT NULL,
  `image_1` varchar(1000) DEFAULT NULL,
  `image_2` varchar(1000) DEFAULT NULL,
  `image_3` varchar(1000) DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `likes` int(12) NOT NULL DEFAULT '0',
  `comments` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `interaction_meta_twitter`
--

CREATE TABLE `interaction_meta_twitter` (
  `id` int(12) NOT NULL,
  `mentions` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `record`
--

CREATE TABLE `record` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `record_hash` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `object_id` int(12) NOT NULL,
  `property` enum('singleton','checkin','checkin-unique','like','comment','review','rating','photos','specials','herenow','mayor') NOT NULL,
  `value` int(12) NOT NULL,
  `datatype` enum('absolute','relative') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_hash` (`record_hash`),
  KEY `property` (`property`),
  KEY `object_id` (`object_id`),
  KEY `datatype` (`datatype`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `singleton`
--

CREATE TABLE `singleton` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `object_type` enum('interaction','venue','event','user') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_type` (`object_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `statistic`
--

CREATE TABLE `statistic` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `time_start` timestamp NULL DEFAULT NULL,
  `time_end` timestamp NULL DEFAULT NULL,
  `timespan` varchar(255) DEFAULT NULL,
  `object_type` varchar(32) DEFAULT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `value` decimal(15,2) DEFAULT NULL,
  `text_value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracking_cycle`
--

CREATE TABLE `tracking_cycle` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `object_id` int(12) NOT NULL,
  `frequency` enum('quaterday','hourly','daily','weekly','monthly','disabled') NOT NULL,
  `TZ` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`),
  KEY `frequency` (`frequency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracking_log`
--

CREATE TABLE `tracking_log` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `object_id` int(12) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `is_latest` tinyint(1) NOT NULL DEFAULT '0',
  `source` enum('instagram','foursquare') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_latest` (`is_latest`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracking_log_ratelimit`
--

CREATE TABLE `tracking_log_ratelimit` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `service` enum('foursquare','instagram') NOT NULL,
  `limit` int(11) DEFAULT '0',
  `remaining` int(11) DEFAULT NULL,
  `response` text,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracking_point`
--

CREATE TABLE `tracking_point` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lat` decimal(13,10) DEFAULT NULL,
  `lng` decimal(13,10) DEFAULT NULL,
  `scanned` tinyint(1) NOT NULL DEFAULT '0',
  `region_id` int(12) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `region_id_idx` (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracking_region`
--

CREATE TABLE `tracking_region` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `lat` decimal(13,10) NOT NULL,
  `lng` decimal(13,10) NOT NULL,
  `radius` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(128) DEFAULT NULL,
  `picture_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_meta_eventbrite`
--

CREATE TABLE `user_meta_eventbrite` (
  `id` bigint(20) NOT NULL,
  `eventbrite_user_id` bigint(20) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eventbrite_user_id` (`eventbrite_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_meta_facebook`
--

CREATE TABLE `user_meta_facebook` (
  `id` bigint(20) NOT NULL,
  `facebook_user_id` bigint(20) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eventbrite_user_id` (`facebook_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `id` int(12) NOT NULL,
  `lat` decimal(13,10) NOT NULL,
  `lng` decimal(13,10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `postalCode` varchar(32) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(3) DEFAULT NULL,
  `cc` varchar(3) NOT NULL,
  `region_id` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `region_id` (`region_id`),
  KEY `lat` (`lat`,`lng`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `venue_meta_common`
--

CREATE TABLE `venue_meta_common` (
  `id` int(12) NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `venue_meta_foursquare`
--

CREATE TABLE `venue_meta_foursquare` (
  `id` int(12) NOT NULL,
  `venue_foursquare_id` varchar(32) NOT NULL,
  `canonicalUrl` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `venue_foursquare_id` (`venue_foursquare_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `venue_meta_instagram`
--

CREATE TABLE `venue_meta_instagram` (
  `id` int(12) NOT NULL,
  `venue_instagram_id` varchar(32) NOT NULL,
  `canonicalUrl` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `venue_instagram_id` (`venue_instagram_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `venue_meta_twitter`
--

CREATE TABLE `venue_meta_twitter` (
  `id` int(12) NOT NULL,
  `mentions` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `venue_record`
--

CREATE TABLE `venue_record` (
  `id` int(12) NOT NULL,
  `checkin` int(8) DEFAULT '0',
  `checkin_unique` int(8) DEFAULT '0',
  `comment` int(8) DEFAULT '0',
  `like` int(8) DEFAULT '0',
  `review` int(8) DEFAULT '0',
  `image` int(8) DEFAULT '0',
  `rating` int(4) DEFAULT NULL,
  `photos` int(8) DEFAULT NULL,
  `specials` int(6) DEFAULT NULL,
  `herenow` int(8) DEFAULT NULL,
  `mayor` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
