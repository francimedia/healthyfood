ALTER TABLE  `record` CHANGE  `property`  `property` ENUM(  'singleton',  'checkin',  'checkin-unique',  'like',  'comment',  'review',  'rating',  'photos',  'specials',  'herenow',  'mayor',  'keyword',  'price' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL