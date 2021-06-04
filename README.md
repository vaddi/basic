# Simple PHP Basic Template #

A Simple plain PHP Templating Engine. 


## Installation ##

Installation ist straight forrward. You just need to clone the current Repossitory:

	git clone https://github.com/vaddi/basic.git

After getting the Base Application, you can add Pages into the `pages` Folder and Edit the Base css to fit your needs.


### Dependencies ###

A running PHP Webserver (A space where you can place PHP Files).


## Updating ##

Updating a old Version which is installed via git command is also straight forrward (you have to chack all changes on existing files):

	git fetch
	git merge


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


## Links ##

- [git-merge](https://www.freecodecamp.org/news/the-ultimate-guide-to-git-merge-and-git-rebase/)

