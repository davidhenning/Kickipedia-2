# Introduction #

## What is Kickipedia 2? ##

Kickipedia 2 is a database application designed for the administrative staff of big forums.

## Why? ##

I am an administrator in one of Germany's largest forums. About 10 years ago I decided to write an web-based app to keep track of all blocked, banned or warned users. The first Kickipedia was born.

It growed over the years and so the old PHP 4 based procedural code became really old-fashioned junk. I planned a rewrite several years ago but was never able to complete it.

In short: it's time for a completly new Kickipedia.

## Technology ##

The new version is based on PHP too, but of course on PHP 5.3 and is completely object-oriented. Instead of a relational database like MySQL, Kickipedia 2 features the document-oriented database MongoDB for a really fast, flexible and much more comfortable data storage.

To use MongoDB even more comfortable, I created MongoAppKit. A small but powerful framework to write apps with PHP 5.3 and MongoDB. It will be released separately on Github soon.

Kickipedia 2 features two more frameworks:

- Limonade: a powerful and really easy to use routing system inspired by Sinatra for Ruby
- Twig: the mighty template engine from Symfony 2

# REST #

Additional to the web interface Kickipedia 2 features a RESTful web-service API with HTTP digest authentication. The data provided as JSON or XML. If you need a custom format, just add your implementation of the format in a new render method of the related view and you're done.

# Requirements #

- Apache 2 with enabled mod_rewrite and a possibilty to create your own virtual host
- PHP 5.3 or higher with a MongoDB driver
- MongoDB 2.0 or higher

Kickipedia 2 will run on Windows, Mac OS X and Linux servers.

## Apache configuration ##

In order to function properly and for improved security you need to create an virtual host in your Apache configuration.

The following example is suited for a local installation on the host kickipedia.name on your localhost IP address. To document root points to the public directory. Feel free to add any other configuration options you may need.

```apache
<VirtualHost 127.0.0.1>
    DocumentRoot /var/www/kickipedia2/public
    ServerName kickipedia2.name
    
    <directory /var/www/kickipedia2/public>
        Order allow,deny
        Allow From All
        AllowOverride All
    </directory>
</VirtualHost>
```
