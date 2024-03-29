# Simple PHP Basic Template #

A Simple plain PHP Templating Engine. You can see a running Application on [demo-page](https://www.mvattersen.de/basic).

It run by Files and has no direct dependencie to any Database. For Persistence of Runtimedata you can use a SQLite Database (an example Database is shipped under `inc/db/database.sqlite.example`) or setup a MySQL Database for the Application (Keep in Mind that you then need a PDO Class to use a MySQL Database!). Both Connections can be configured in the `config.php` file.

On a nginx Webserver this web page achieves a carbon rating of A+.
Only 0.03g of CO2 is produced every time someone visits this web page: [websitecarbon](https://www.websitecarbon.com/website/mvattersen-de-basic/)


# Installation #

## Dependencies ##

A running PHP Webserver (A space where you can place PHP Files). I've testet on **apache2** and **nginx** Servers. The Packagenames can variate a little, depending on you operation System:

Neccessarry Pakages:

- php > 7.1
- php_curl

Optional (but usefull) packages:

- git
- php_sqlite3


## Execution ##

Installation ist straight forrward. You just need to clone the current Repossitory:

	git clone https://github.com/vaddi/basic.git

After getting the Base Application, you should copy the `pages` Folder to `sites` to use this for your own Pages:

	cp -r inc/pages inc/sites

Copy the config file:

	cp inc/config.php.example inc/config.php

And Edit the `inc/config.php` File:

```php
// change this
define( 'PAGES',      __DIR__ . "/pages/" );
// to this
define( 'PAGES',      __DIR__ . "/sites/" );
```

Now you can edit and add Pages under the `sites` Folder. Don't forget to Edit the edit `styles.css` to fit your needs (Hint: If you copy the file and place a new one into the css Folder, only the new css File(s) will be loaded automaticly into the header section of the Template).


## Metrics ##

If you want to use the Visitor Metrics, you need to enable the sqlite Database in to the config file and have Setup a Database.


## SQLite3 Database ##

You an easily use the shipped SQLite3 Dateabase. Enable them into the `config.php` 

```php
// change this
define( 'SQLITE_USE', false );
// to this
define( 'SQLITE_USE', true );
```

An example Database is placed under `inc/db/database.sqlite.example`, just copy them and make the Folder and the File writeable for the webserver:

	cp inc/db/database.sqlite.example inc/db/database.sqlite

The right command to set the Fileaccess should appear if you open the Site for the first time in a Browser after activating and copiing into place. Something like this:

	SQLite file not writeable by webserver user, please add write permissions to file and Folder!
	sudo chown -R www-data /var/www/Sites/basic/inc/db

You find also creation Schemas in the `assets` Folder.


# Updating #

Updating a old Version which is installed via git command (which is recommend) is also straight forrward.

1. Check if the are any Overwrites by you into the Base Files `git status`
2. If you found `Untracked files`, you can ignore the Messages. Only Changes on Base Files will result in a merge conflict!  
3. If there are no changes found by git, just update by running `git pull`


See [git-merge](https://www.freecodecamp.org/news/the-ultimate-guide-to-git-merge-and-git-rebase/) for more about how to merge your Setup and the base Setup.


# Content/Pages #

First: You can use PHP Files. No static replace Content like "{{TITLE}}" or something.

If you just want to sendout simple html content, you can also by write them into the files. But you have to keep the filenames Prefix `.php`, only files with the Prefix `.php` will be used by the Templating!

Just place you PHP files into the `sites` Folder (don't use the shipped `pages` Folder, oserwise you'r not able to simply update!). All Content will be rendered inside of a Template. You can find the Template Files under the `tpl` Folder and can also just edit them as you like, they are also just plain PHP Files. 


## Special Pages ##

There will be a few Pages shipped whithin this Application. You can use them to build your own pages or use them as they are. Some are hidden by the `MENU_EXCLUDE` array in the `config.php`, you have to remove their name from there to make them visible in the Sitemenu.

List of shipped Pages:

- Authtest (a simple site which show if you are logged in or not)
- Contact (a full contactform which is able to send Mails by smtp or php mail function, there is also a map and Link section in this Page )
- Documentation (this Page will render the README.md file like github do)
- Environment (a few enviroment tests to ensure you have installed and configured your System well)
- Imprint (an Imprint page)
- News (a very simple News page with simple CRUD functionality, all Enties will be loaded and saved on the shipped sqlite3 database)


## Classes ##

The Templating is done by the two Classes `Sites.php`, wich load Content from the `sites` Folder and the `Template.php` Class, wich loads template Parts from `tpl` Folder and mainly combine all to the outputed HTML or Text.


### Site.php ###

This Class read and parse the Content from the Files in the `sites` Folder. It also wraps the Content by the Template parts by using the `Template.php` Class.


### Template.php ###

Read the content from the `tpl` Folder and combines them into a basic HTML Page. The `Template.php` class has the base structure of the HTML Site, there you can find the starting `DOCTYPE` Element and also the `body` and `html` HTML tags.


## Extensions Classes ##

You can extend the Application by your own classes. Just place them into the Folder `inc/class/extensions/`. There are also some other Helper Classes which will be used by the Application (so dont remove them until you're shure they wont be used anywhere!). 

All Classes under the extensions Folder will be automaticly instanciated by the `Base.php` Class by the PHP `spl_autoload_register` function in the Head of the Page.

To get a simple description, add a Comment in line 3 of your Class. This will be used in the `index.php` Page (in the Section Pages) as description of your Class and help you to keep in mind what a Class is used for.


# Layout #

You only have to place the file into the `css` or `js` Folder and the Template Class will render the neccessary Links into the Page head Section to include them. 


## CSS ##

A pure Layout is allready shiped by default. It uses the `style.css` inside of the `css` Folder and contains pure CSS. If there are other files in this Folder, the shipped `styles.css` file won't be used anymore, so you can easily overwrite the default layout by add your own css files into the folder whithout the need to delete the shipped `styles.css` file.


## HTML ##

The layout of the outputed HTML is controlled by the Files into the `tpl` Folder and the class `Template.php`. You can also inserd plain HTML into the Pages. There is no need to use PHP into the files.


## JavaScript ##

The Base Template contains some JavaScript Files for clock, pageload, highlight the current navigation link and some other things which are used by the shipped Pages. 
All files will be added to the Site head from the `tpl/head.php` file.


# Application Exporter #

The Application has a Prometheus ready scrape target endpoint on where prometheus can find some Metrics when requesting the metrics Page:

	http[s]://[domain.tld]/[appname]/?page=metrics

All Metrics will be named by the Application `SHORTNAME` from the `config.php` file, wich is the Besefolder Page name (default Name is `basic`).

List of Metrics:

- basic_info = like current Version and Application Domain
- basic_pages = Amount of Files/Pages in the configured PAGES destination Folder and the Localpath.
- basic_commits = Amount of git Commits
- basic_appsize = Total Size of the Application
- basic_todos = amount of TODO mentions in the Code
- basic_updates = Are there a new Version available on github


You can use my `PHP Applications` Dasboard as Dashboard Example: [dashboard](https://github.com/vaddi/basic/tree/main/inc/asset/dashboard.json)

The Exprter can turned off by Set Config Constant `METRICS` to false into the `config.php` file.


# Bugs #

Bugs can also be Reported under: [git-issues](https://github.com/vaddi/basic/issues)


# WIP Parts #

Currently there are some incomplete Things in this Application, feel free to create a Fork or Send Solutions to me via GitHub.

- [x] Build a Class for RSS/Atom Feeds
- [x] Add RSS & Atom Feed Icons and Links (On the Site and on the Newspage)
- [x] Cleanup extension Classes, reorganise Functions into speaking Classes
- [ ] Add Back to Top scroll
- [ ] Add a Database Wrapperclass to use more than the SQLite Database
- [ ] Add MySQL Database class and Schama Asset
- [ ] Refractoring dsgvo.js (Alos Cookie und Localstorage Settings in Page login.pgp)


# Links #

- [git-merge](https://www.freecodecamp.org/news/the-ultimate-guide-to-git-merge-and-git-rebase/)
- [git-issues](https://github.com/vaddi/basic/issues)
- [demo-page](https://www.mvattersen.de/basic)
- [dashboard](https://github.com/vaddi/basic/tree/main/inc/asset/dashboard.json)
- [websitecarbon](https://www.websitecarbon.com/website/mvattersen-de-basic/)
