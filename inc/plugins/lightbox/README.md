# LightBox Galerie

A simple HTML Imagegalerie.
Source: [simpleLightbox](https://dbrekalo.github.io/simpleLightbox/)


# Installation

Create a image Folder and copie your Images into them:

    mkdir inc/images


copie the galerie files into desired location:

```bash
	cp inc/plugins/lightbox/simpleLightbox.css inc/css/
	cp inc/plugins/lightbox/simpleLightbox.js inc/js/
```

Remove the `galerie` Entry from the Excluded Menu Entries in the `config.php` File.
Keep in mind to setup the correct image Folder **IMGFOLDER** in the `config.php` File!

# Features

## Mimetypes

Setup in the constant **IMGTYPES** into the `config.php` File.
Default Imagefile mimetype: 

* jpg
* jpeg
* png
* gif


## Subfolders

You can just place single Imagefiles into **IMGFOLDER** or organise them into Subfolders. Subfolder names will be used as headline and to group they're Images into Single Galeries (rotate image Galeries only in her Groups and not global on all images on the Page).
