# Simple PHP Basic Template #

A Simple plain PHP Templating Engine. You can see a running Application on [demo-page]().


## Installation ##

Installation ist straight forrward. You just need to clone the current Repossitory:

	git clone https://github.com/vaddi/basic.git

After getting the Base Application, you can add Pages into the `pages` Folder and Edit the Base css to fit your needs.


### Dependencies ###

A running PHP Webserver (A space where you can place PHP Files).


## Updating ##

Updating a old Version which is installed via git command is also straight forrward (you have to chack all changes on existing files):

	git pull


See [git-merge]() for more about how to merge your Setup and the base Setup.


## Content/Pages ##

You can use PHP Files, no replaces Content like "{{TITLE}}" or something.

If you just want to sendout html content, you can also, you just have to keep the filenames Prefix `.php`!

Just place you PHP files into the `pages` Folder. All Content will be rendered inside of a Template. These Files will be find under `tpl` Folder and can also just edited like you want, they are also plan PHP Files. 


### Classes ###


#### Page.php ####

This Class read the Content from the Files in the `pages` Folder and wraps it by the Template parts.


#### Template.php ####

Read the content from the `tpl` Folder and wrap them into a basic HTML Page Template. 


## Layout ##

A pure Layout is allready shiped by default. It uses the `style.css` inside of the `css` Folder and contains pure CSS. 

You only have to place the file into the `css` or `js` Folder and the Template Class will render the neccessary Links into the Page head Section to include them. 


## JavaScript ##

The Base Template contains some JavaScript Files for clock, pageload and to highlight the current navigation link. 


## Application Exporter ##

The Application has a Prometheus ready scrape target url on where it can find some Metrics.

	http://domain.tld/basic/?page=metrics

All Metrics will be named by the Application `SHORTNAME`, wich is the Besefolder Page name (default might be `basic`).


- basic_info = like current Version and Application Domain
- basic_pages = Amount of Files/Pages in the configured PAGES destination Folder and the Localpath.
- basic_commits = Amount of git Commits
- basic_appsize = Total Size of the Application


## Known Bugs ##

Bugs can also be Reported under: [git-issues]()



## Links ##

- [git-merge](https://www.freecodecamp.org/news/the-ultimate-guide-to-git-merge-and-git-rebase/)
- [git-issues](https://github.com/vaddi/basic/issues)
- [demo-page](https://www.mvattersen.de/basic)
