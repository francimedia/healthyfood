#promote.me

* Version: 0.1

## Description
...


## Example manager links

* Add new region: http://local.promoteme.herokuapp.com/manager/tracking



 	
## CLI tasks

* Make single venue scan (100 venues): php oil refine spider::foursquare_venues:updateLocations
* Start daemon for constant scanning: php oil refine spider::foursquare_venues:updateLocationsService
* Execute scans for region X: php oil refine spider::foursquare_venues X (X = region ID)




## Apache config / vhost

<VirtualHost *:80>
	ServerName local.promoteme.herokuapp.com
	ServerAlias promoteme.herokuapp.com

	SetEnv FUEL_ENV DEVELOPMENT

	DocumentRoot "/YOUR-PROJECT-DIR/public/"

	<Directory "/YOUR-PROJECT-DIR/public/">
		Options All
		AllowOverride All
	</Directory>

</VirtualHost>

