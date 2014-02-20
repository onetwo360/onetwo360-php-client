OneTwo360 API Client
=====================

[![Build Status](https://travis-ci.org/onetwo360/onetwo360-php-client.png?branch=master)](https://travis-ci.org/onetwo360/onetwo360-php-client)

Installation
------------

You can (and should) use Composer to install the client.

This is done by adding the following line to the require section of your composer.json file

	"onetwo360/onetwo360-api-client": "dev-master"

Now all you need to do is run the installation like this

	$ php composer.phar install

Test it out!
------------

To test the client simply `cd` to the root dir of your project and use the built-in web server in PHP.

	$ php -S localhost:8080 -t vendor/onetwo360/onetwo360-api-client/examples/

You can now point your browser to http://localhost:8080/ and test it out.

Autoloading
-----------

Composer generates an autoloader file that you need to require in order to use the library.

	require 'vendor/autoload.php';

You should now have autoloading for all the libraries installed through Composer.