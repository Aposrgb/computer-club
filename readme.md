1. Build project with docker
~~~
docker-compose up --build -d
~~~
2. Open command line with php-fpm
~~~
docker-compose exec php-fpm bash
~~~
3. Then (install all dependecies)
~~~
composer install
~~~
4. Update DB without migrations (in command line php-fpm)
~~~
php bin/console doctrine:schema:update -f
~~~

Doctrine is ORM for Symfony and other frameworks (required PHP 7.1)

If you're needed to write custom command, you are write command in directory 
club/src/Command, example command you found in ../src/Command/ExampleCommand.
Then to execute you're custom command needed open php-fpm container and execute with
bin/console or php bin/console example:entity:insert is example command.

To create entity execute command
~~~
bin/console make:entity
~~~
Then you're to write name and type for each property's.
Example type for property's
~~~
int,

string (and length, in postgres string convert to varchar(length)),

text (without length, in postgres string convert to varchar())

json (array),

array,

simple array (in postrges array convert to text
and looks like that 'one el,two el')

datetime (is object with full date time, convert to timestamp)

boolean,

ManyToOne, OneToOne, OneToMany, ManyToMany
~~~
