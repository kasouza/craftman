# craftsman

Craftsman is a set of utilities to help you with managing your projects.

# Using

## Install it in your project
First, install it with
```composer require --dev kasouza/craftsman```

Then you can simply run the commands with
```./vendor/bin/craftsman```
or
```php vendor/bin/craftsma```

## Build the phar
If you prefer, you can also build a phar and just copy it into your projects or add it to yout path and use it anywhere

Clone the repo
```git clone https://github.com/kasouza/craftsman/```

Install the dependencies and build the phar
```
composer install
composer build
```


Then you can either run it with php
```php craftsman.phar```


Or you can make it into an executable
```
mv craftsman.phar craftsman`
chmod 755 craftsman
```


And, optionally, move it somewhere in your `$PATH`
```
mv craftsman /usr/local/bin
```


Or even just copy it to your project root and run it from there
```
mv craftsman /path/to/project/
```
