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
