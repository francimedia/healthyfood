#Healthy Food Compas

* Version: 0.1

## Description
...


## Example manager links

* Add new region: http://local.healthyfood.herokuapp.com/manager/tracking


## CLI tasks

* Make single venue scan (100 venues): php oil refine spider::foursquare_venues:updateLocations
* Start daemon for constant scanning: php oil refine spider::foursquare_venues:updateLocationsService
* Execute scans for region X: php oil refine spider::foursquare_venues X (X = region ID)

* Start daemon for updating instagram pictures: php oil refine spider::instagram_pictures  X (X = region ID)

* Update venue images: php oil refine spider::instagram_pictures:venue  X (X = venue ID)
* Update venue images: php oil refine spider::instagram_pictures:foursquare_venue  X (X = foursquare venue ID)


## Apache config / vhost

<VirtualHost *:80>
	ServerName local.healthyfood.herokuapp.com
	ServerAlias healthyfood.herokuapp.com

	SetEnv FUEL_ENV DEVELOPMENT

	DocumentRoot "/YOUR-PROJECT-DIR/public/"

	<Directory "/YOUR-PROJECT-DIR/public/">
		Options All
		AllowOverride All
	</Directory>

</VirtualHost>

