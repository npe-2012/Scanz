Scanz
=====

PHP Script : Auto download Mangas/Scans

This Script will download the last chapters of the Scans you like in your Dropbox (it creates a /Scanz folder).
Make a cron and your favorite Scans will be downloaded automatically in your Dropbox.

##Database init

First, here is the script to init the database :

	CREATE DATABASE  `scanz` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
	CREATE USER 'scanz'@'localhost';
	GRANT USAGE ON * . * TO  'scanz'@'localhost' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
	GRANT ALL PRIVILEGES ON  `scanz` . * TO  'scanz'@'localhost';
	CREATE TABLE  `scanz`.`manga` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`label` VARCHAR( 255 ) NOT NULL ,
		`site_url` VARCHAR( 255 ) NOT NULL ,
		`unread` INT NOT NULL
	) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

	CREATE TABLE  `scanz`.`log` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`label` VARCHAR( 255 ) NOT NULL ,
		`created_at` DATETIME NOT NULL
	) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

## Add a scan
	
To add a scan, just add it to the "manga" table.
If you want to add Naruto and OnePiece for example :

	INSERT INTO  `scanz`.`manga` (`id` , `label` , `site_url` , `unread`) VALUES 
	(NULL ,  'Naruto',  'http://baraddur.free.fr',  '583'), 
	(NULL ,  'OnePiece',  'http://opluffy.com/lecture/lec',  '663');

The url structure has to be site_url/chapter/filename, for example for Naruto "http://baraddur.free.fr/582/18.jpg". You only fill the database with the site_url part

## Link it to your Dropbox

You have to write your Dropbox username and password in the PHP Script to allow the connection.

## Make it automatic

Upload this script to your server and add it to your cron table

If you already have one :

    crontab -e

If you don't, create it :

    vi my_crontab
    crontab my_crontab

To run the script every 6 hours, you should write :

    00 */6 * * * php /var/www/Scanz/auto_dl.php


