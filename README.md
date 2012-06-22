# Kickipedia 2 #

Kickipedia 2 is a database application designed for the administrative staff of big forums. On default Kickipedia 2 stores warnings, blocked or banned users and provides statistics. If you need more types, just add them to the config file.

Kickipedia 2 features a RESTful API with HTTP digest authentication. Data is provided as JSON or XML. Custom format can be easily implementend into the related view classes.

# Requirements #

- Apache 2 with enabled mod_rewrite and a possibilty to create your own virtual host
- PHP 5.3.3 or higher with following extensions:
  - mongo
  - json
  - hash
  - mcrypt
  - session
- MongoDB 2.0 or higher
- Composer

# Installation #

1. Install Composer
   `curl -s http://getcomposer.org/installer | php` (skip to the next step if it's already installed on your server)

2. Clone the repository in a directory inside the htdocs directory of your web server

3. Run `php composer.phar install` to resolve all dependencies

4. Configure a virtual host (see below)

5. Open yourdomain.com/setup.php and follow the instructions

6. You're finished. Have fun!

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

# Technology #

## Used frameworks ##

- [Silex](https://github.com/fabpot/Silex) (URL routing, request handling and much more)
- [Twig](https://github.com/fabpot/Twig) (template engine)
- [Gibberish AES for PHP](https://github.com/ivantcholakov/gibberish-aes-php) (AES encryption) Symfony components)
- [Phpass](https://github.com/rchouinard/phpass) (secure password encryption with salting and key streching)
- [MongoAppKit](https://github.com/MadCatme/mongoappkit) (MongoDB abstraction layer)