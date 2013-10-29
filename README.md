discoveryutils
==============

Discovery Service Utilities

Uitities to interact with Summon, Primo, and the in-house request form system.

To Install

1. clone repo
2. cd repo-name
3. run ```php composer.phar install``` to pull down dependency packages to /vendor
4. Make sure /cache and /log are writable by web server
5. The .htaccess file currently expects this to be dropped somewhere where it is acccessible at "/searchit" on http://mywebserveriamusing.princeton.edu/searchit.


To Configure
1. File out /conf/summon.yml with summon client key and host name 
