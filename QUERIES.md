.CSV	Comma-separated values and Tab delimited file

SELECT lat, lng, name, street, postalCode, city, checkin  FROM `venue`
JOIN venue_record ON (venue.id = venue_record.id)
 WHERE `region_id` = 13