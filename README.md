# Discovery Service Utilities
==============

Utilities to interact with Summon, the PUL Blacklight Catalog, Springshare products, PUL Pulfa, DPUL and Princeton University Art Museum. Written using Symfony and GuzzleHTTP. See https://symfony.com/doc/current/index.html for more context and information about Silex applications.

## Development Environment Dependecies
1. docker
1. lando
    ```
    download dmg from https://github.com/lando/lando/releases
    ```

## To Install

1. clone repo
1. cd repo-name
1. Secret keys are to be put in both `.env.local` and `.env.test.local`  These files are not put in git.
    ```
    # .env.local & .env.test.local
    APP_SECRET=XXXXX
    LIB_GUIDES_KEY=YYYYY
    SUMMON_AUTHCODE=ZZZZZ
    ```

1. Start lando with `lando start`
1. `lando build` to install composer dependencies
1. Run `lando info` to see what is being run
1. check in your browser (at the port lando configured)
   1. You should see the Princeton University Libraries Shield

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
    RewriteBase /utils
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ public/index.php [L]
</IfModule>
```

## Unit Tests

### Caveats
1. Currently a Few Failing Ones, also test coverage is not complete for all classes/features in the Project

Tests use phpunit https://phpunit.de/manual/current/en/index.html

### To Run

1. Run the tests on the lando server by running `lando test`

## Deploy to server

1. We have capistrano set up to deploy our servers

    1. `cap staging deploy` will deploy the main branch to staging
    1. `BRANCH=other cap staging deploy` will deploy the other branch to staging
