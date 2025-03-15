# Project installation and configuration
## Setting up Laragon local server
Use this URL to auto-download: [Laragon6.0](https://github.com/leokhoa/laragon/releases/download/6.0.0/laragon-wamp.exe)
<p>Turn the Nginx service on and Apache off in the laragon setting</p>

### Download PHP 8.2 binaries
Non Thread Safe version: [PHP8.2](https://windows.php.net/downloads/releases/php-8.2.28-nts-Win32-vs16-x64.zip)
<p></p>  

Thread Safe version: 
[PHP8.2](https://windows.php.net/downloads/releases/php-8.2.28-Win32-vs16-x64.zip)

### Unzip the downloaded PHP8.2 and copy to laragon/bin/php

## Setting up project source code
Clone this project into <b>laragon/www<b/> folder 
<p></p>
Open the project folder and copy .env.example to .env


### Install project dependencies
Using Terminal in Laragon 
<br>
change directory into project folder and
run 'composer install' to install dependencies

### Generate app key
Run php artisan key:generate 

### Change db host
run 'ifconfig' command to get current local ip address and change the DB_HOST environment param <br>
DB_HOST = ip address

### Run project sql file
Open heidiSQL in Laragon and run the project2.sql

### Run migrations
Use Terminal and run 'php artisan migrate'

### install npm dependencies
Run 'npm install' then run 'npm run dev' to serve


# GET READY TO CODEEEEEEEEE :)