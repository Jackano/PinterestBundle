Pinterest Bundle Overview
=======================

### Presentation

  This bundle goal is to provide command lines and model for pins from the pinterest.com website.
  As there is no API available yet for Pinterest, it uses HTTP requests and crawl pinterest.com to retrieve pins and statistics.
  By updating regularly, it is for example able to sort your pins actions / repins / likes numbers (see usage).
  
### Installation

  * 1. Download and enable the bundle
  
  Enable the bundle in the kernel:

	``` php
	<?php
	// app/AppKernel.php

	public function registerBundles()
	{
		$bundles = array(
			// ...
			new Zeldanet\PinterestBundle\ZeldanetPinterestBundle() 
		);
	}
	```

  * 2. Update your database schema
  
	``` bash
	$ php app/console doctrine:schema:update --force
	```
  
  * 3. Optionnaly import routing
  
  This will enable the pre-configured /pin and /pin/{id} routes.
  
  ``` yaml
  # app/config/routing.yml
    _pinterest:
      resource: "@ZeldanetPinterestBundle/Resources/config/routing.yml"
      prefix:   /pin/
	
  ```
  

### Usage

  * Discover Command
    Use it to discover and create pin.
	
	``` bash
        php app/console pinterest:discover user board
		php app/console pinterest:discover zeldanet best-of
   ```
	
	Will for example retrieve pins on the http://pinterest.com/user/board/ and http://pinterest.com/zeldanet/best-of/ pages.
	As those pages are currently paginated on pinterest.com, you can also use the extended DiscoverByUrl command line instead.
	
	
	* DiscoverByUrl Command
    Use it to discover and create pin with an URL.
	
	``` bash
        php app/console pinterest:discoverbyurl http://pinterest.com/zeldanet/best-of/
		php app/console pinterest:discoverbyurl http://pinterest.com/zeldanet/best-of/?page=2
   ```
    
	Note Pinterest's URL schema to get older pins.
	
	
	* Update Command
    Use it to update pin statistics (actions, repins, comments and likes counts).
	
	``` bash
        php app/console pinterest:update
   ```
    
	This will be limited to the 100 older pins in your database.
	
	
	* UpdateBroken Command
    This will fix your broken pins (pins in your datablase without image).
	Giving the regular period with 502 errors on pinterest.com, it is quiet usefull.
	
	``` bash
        php app/console pinterest:updatebroken
   ```
    
### General usage
	
	At first run the Discover (or DiscoverByUrl on paginated content) command on your choosen boards.
	Then setup an hourly cron job with the Discover for each of the choosen boards, and another job for the Update commands.
	
	You can also setup a daily job with the UpdateBroken command.
	

### TODO list

	> Update command: Add optionnals user and board parameters.
	> Update command: Update board field when updating a pin.
	> Better handling of errors 500 on requests.
	> Investigate usage of Pinterest RSS feeds (eg. http://pinterest.com/zeldanet/feed.rss and http://pinterest.com/zeldanet/zelda-fans.rss ).
	> Add thumbnail (seems just to be in pinterest.com /236x/ folder instead of /736x/.

