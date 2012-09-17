# Kickipedia 2 #

Kickipedia 2 is a database application designed for the administrative staff of big forums. On default Kickipedia 2 stores warnings, blocked or banned users and provides statistics. If you need more types, just add them to the config file.

Kickipedia 2 features a RESTful API with HTTP digest authentication. Data is provided as JSON or XML. Custom formats can be easily implementend into the related view classes.

# Requirements #

- Apache 2 with enabled mod_rewrite
- PHP 5.3.3 or higher with following extensions:
  - mongo
  - json
  - hash
  - mcrypt
  - phar
  - session
- MongoDB 2.0 or higher
- [Composer](http://getcomposer.org/)

# Installation #


1. Clone the repository

2. Install Composer (skip to the next step if you already installed composer)

   Run `curl -s http://getcomposer.org/installer | php`

3. Run `php composer.phar install` to resolve all dependencies

4. Open yourdomain.com/setup.php (not yet finished) and follow the instructions

5. You're finished. Have fun!

# Technology #

## Frameworks ##

Kickipedia 2 relies on the power of [MongoAppKit](https://github.com/MadCatme/mongoappkit). Originally MongoAppKit was an integral part of Kickipedia 2. After a while I decided to decouple it from Kickipedia 2 and to rewrite MongoAppKit on top of [Silex](https://github.com/fabpot/Silex), a simple web framework based on Symfony2 components and the [Twig](https://github.com/fabpot/Twig) template engine. Together they form a reliable base to build web applications with PHP.

MongoAppKit provides:

- A basic integration of Silex and Twig to easily create a base of an application.
- An Object Document Mapper (ODM) for MongoDB
- A service for HTTP digest authentication (f.e. to create a RESTful web service).
- Powerful AES encryption to secure your data in a MongoDB database. (encrypted fields are not searchable yet!)
- A basic view class with pagination tools and support for HTML, JSON or XML as output formats.