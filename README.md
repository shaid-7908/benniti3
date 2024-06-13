
# Bennit Prototype

## Pre-requisites

- PHP 7.4+ on a web server
- MySQL driver for your webserver
    - `sudo apt install php-mysql`
- MySQL or compatible database:
    - https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-20-04
- Snowflake
    - `composer require godruoyi/php-snowflake`
- Stripe PHP
    - See [below](#Stripe).

## Install

- Create the database in `mysql` with admin privileges...
 ```
 CREATE DATABASE bennit;
 ```

- Import database file:
 ```
 USE bennit;

 SOURCE /path/to/bennit.sql;
 ```

- Create user and permissions:
 ```
CREATE USER 'dev'@'localhost' IDENTIFIED BY 'Str0ngP@ssword';

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER 
 ON bennit.* TO 'dev'@'localhost' IDENTIFIED BY 'Str0ngP@ssword';

FLUSH PRIVILEGES;
 ```

## Default Credentials (You should change these!)

- Admin username: jonathan@bennit.ai
- Admin pass: 4SeekerSolverXChange(!)
- Guest username: guest@bennit.ai
- Guest password: bennitguest
- Other users password: 4Bennit!

## Stripe

- STRIPE_KEY in config (both live and debug)
- `sudo apt install composer`
- `sudo apt install php-curl`
- `composer require stripe/stripe-php` in the web app deploy folder

## Code Organization

This is basically just a CRUD system. 

Generally, the Create Read Update Delete functions are in a Class file in the `Class` roughly named after the corresponding table in the database. Classes start with a capital letter, and are instantiated in the `inc/header.php` with a lowercase letter.

The Create and Update (and sometimes Read) user interface generally is defined in a php file in the root roughly named after the corresponding class, but with an all lowercase file name. Generally, if a function in a class needs another class, it expects it to be passed in as an argument (the Views class is an exception, since it always needs the data Classes, so it keeps its own reference.)

The rest of the Read and Delete user interface is usually similarily named, but suffixed with "List.php" php file in the root, which loads a grid for list actions. The grids themselves are defined in the `Views` class. 

So for example, to show a list of users, a function in `classes/Users.php` class is used to query the database, the `userList.php` UI: checks permissions, invokes the class's query, sets up the page and grid settings, then calls `makeUserGrid` in `classes/Views.php` passing in the query result and config.

Generally, each UI page: checks if the user is logged in, shows any pending messages from server-side actions, checks if the user belongs there (has sufficient privileges and/or data), parses any queries (GET, POST or both, depending on the page), then interacts with the classes to perform any requested actions, then load and display data or results.

## Credits

- User/auth system prototype: Nababur Rahaman https://github.com/nababur
- Lots of stackoverflow

No ChatGPT was used in the creation of this code :->

## ToDo

- This was a prototype, that became a beta. Various todos can be found throughout the code!
