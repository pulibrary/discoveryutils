# Discovery Service Utilities
==============

Utilities to interact with Summon, Primo, and the in-house request form system. Written using Silex, Symfony Components, and GuzzleHTTP. See http://silex.sensiolabs.org/documentation for more context and information about Silex applications. 

## Dependecies
1. PHP 5.4 or higher
2. PHP compiled with curl

## To Install

1. clone repo
2. cd repo-name
3. run ```php composer.phar install``` to pull down dependency packages to /vendor
4. Make sure /cache and /log are writable by web server
5. Copy all *.yml files from /conf to your local environment
6. Make sure workstation IP is registered with Primo Web Services as a trusted client. 
7. The .htaccess file currently expects this to be dropped somewhere where it is acccessible at "/searchit" on http://mywebserveriamusing.princeton.edu/searchit.

## Apache Configuration

1. In the Virtual host block where the app will live mark:
```
Alias /searchit /var/www/apps/discoveryutils
<Directory "/var/www/apps/discoveryutils">
  Options +Indexes
  AllowOverride All
</Directory>
```    
2. In the .htacess provided with the app the base path needs to be set
```
<IfModule mod_rewrite.c>
    Options -MultiViews FollowSymLinks

    RewriteEngine On
    RewriteBase /searchit
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Application Configuration

1. File out /conf/summon.yml with summon client key and host name 
2. Set the environment and base URL you want to use for the app in /conf/enviornment.yml
3. /conf/primo.yml contains details about the primo application

## Unit Tests 

### Caveats
1. Currently a Few Failing Ones, also test coverage is not complete for all classes/features in the Project
2. To run some of the tests related to Primo Web Services you must be at IP that has been explicitly allowed to query them. 

Tests use phpunit https://phpunit.de/manual/current/en/index.html

### To Run 

1. Install PHP Unit (On OSX ```brew install phpunit```)
2. Go to the Project Root directory
3. For all tests run ```phpunit```
4. For all single test run something like 
```
phpunit tests/LookupApp/Tests/PrimoQueryTest.php
```

### Testing Web Services Calls
Still looking for a good methodology for doing this in PHP. For now I'm using fixture files in ```tests/support```. Too stub one out for a test you can use the the following methodology:

```
### Sets Up the Test
protected function setUp() {
  $primo_server_connection = array(
    'base_url' => 'http://searchit.princeton.edu',
    'institution' => 'PRN',
    'default_view_id' => 'PRINCETON',
    'default_pnx_source_id' => 'PRN_VOYAGER',
  );
  $single_record_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER4773991.xml');
  $this->single_source_record = new \Primo\Record($single_record_response, $primo_server_connection);
}

function testGetSinglePrintRecordLocations() {
  $this->assertInternalType('array', $this->single_source_record->getAvailableLibraries());
  //print_r($this->single_source_record->getAvailableLibraries());
  $this->assertEquals(1, count($this->single_source_record->getAvailableLibraries()));
  $this->assertArrayHasKey('PRN_VOYAGER4773991', $this->single_source_record->getAvailableLibraries());
}
```

See https://github.com/pulibrary/discoveryutils/blob/master/tests/LookupApp/Tests/PrimoRecordTest.php for more details. 

