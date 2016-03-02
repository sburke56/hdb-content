=== AWSOM Pixgallery ===
Contributors: Harknell
Donate link: http://www.awsom.org
Tags: images, gallery, media, admin, post
Requires at least: 2.0.5
Tested up to: 3.2
Stable tag: 4.7.0

AWSOM Pixgallery is an Image Gallery/Archive plugin for Wordpress designed to make it easier for Artists or Webcomic creators to set up a portfolio of their artwork.
== Description ==

AWSOM Pixgallery is an Image Gallery/Archive plugin designed to make it easier for Artists or Webcomic creators to set up a portfolio of their artwork. It features Automatic Watermarking, captioning using the Visual Editor or HTML, sorting, auto-thumbnailing, Comicpress theme support, per image/gallery commenting and more. It is a Wordpress native Plugin and does not require any config or file changes or outside tool integration to work; just activate the plugin and add a line of text to any post or page and your Gallery of Images will appear. All options are handled through the regular Wordpress Admin interface. More features are in development now such as tagging, extensive theme control through Admin interface, and more.
Part of the www.AWSOM.org series of Wordpress Plugins developed by Harknell, webmaster of the Webcomic at www.onezumi.com


== Installation ==


The setup for the AWSOM Pixgallery plugin takes only a few steps and does not require you to edit config files or your theme. To get your gallery up and running please do the following steps in order (please read through the entire process before starting):

1) Download and unzip the plugin files to your computer.

2) Move the plugin folder called awsom-pixgallery to your website's wp-content/plugin folder (please note it MUST be called awsom-pixgallery).

3) Create a folder on your webserver to hold your images. You can call this anything, but remember the name. This folder does NOT have to be set to 777. If you need to create your own cache folder (see step 5) it is best to not have this folder or the cache folder in the same set of folders as either parent or child of the other or as any part of the same chain of folders, so don't make cache/MyImageFolder or MyImageFolder/cache.

4) Log into your site and go to the Admin area and click the link to Plugins. Find the AWSOM Pixgallery entry. Click the Activate link next to the plugin.

5) AWSOM Pixgallery will attempt to create a default cache folder for you. If this does not work and you receive an error statement please do the following to create a cache folder:

Create a new folder on your webserver called "cache", if you already have a folder called this you may name this folder differently, but remember the name you use. This is where the plugin will save the automatically created thumbnails of your image gallery images.
Set the permissions for the cache folder to 777 using your FTP program (if you need help on how to do this please read the tutorial called 3: Moving Files to Your Web Server at the www.awsom.org website.)
In the Admin area click the top level link of "Options" and then in the options sub menu click the link for "Pixgallery" to go to the options area.
In the Pixgallery options page scroll to the bottom and input the cache folder you created in step 3 in the field called Cache Path. Make sure it has a /  after it's name--so if you created it in the main folder of your site and it was called cache input it as cache/ If it was in a subfolder input it as MainFolderName/cache/ replacing the MainFolderName with whatever the name is of your folder you put the cache folder in.

6) Upload your images to the folder that you created in step 3. You can add images over time also and AWSOM Pixgallery will automatically thumbnail and add them to the archive whenever you add new images.

7) To make your archive visible on a page start a new page (best usage) or post and add the following line of code to it:

[pixgallery path="/MyImageFolder/"][/pixgallery]

Note how you place the MyImageFolder name in the code with a / before and a / after the folder name. For convenience you can copy this code from the PixGallery Options area where an example is kept or use the code above. You may place text above or below this code and it will appear on the main page of your gallery.

8) Once you save and publish the page your gallery will appear.

9) You may create any number of image gallery folders you like as separate galleries, simply add the above code to a post or page to show the new gallery by referencing the folder you create. You may even have multiple folders within the same gallery. Any folders under a folder referenced by the code will be displayed in your archive as a sub gallery. To see some examples of this please see the Art galleries at http://www.onezumiverse.com

At Stage 8 you may want to also look at the default options for how Pixgallery will create your thumbnails, what grid it will present the thumbnails to the viewer (how many across and how many down), what sorting order they will be in, etc. To read up on what each option does please see the AWSOM Pixgallery Options Guide.

== Upgrading From Previous Version ==


To upgrade from a previous version to the 4.7.X version:

1) Go to the Admin Plugins menu and deactivate the previous version of AWSOM Pixgallery.

2) Delete the pixgallery folder from your wp-content/plugins folder

3) copy the new awsom-pixgallery plugin folder to the wp-content/plugins folder (please note the folder name now MUST be awsom-pixgallery)

4) go to the Admin Plugins menu and activate the 4.7.X version.

5) Upgrade is complete.

6) Optional steps: There is a new option in the Pixgallery options area to create the default cache folder used by the new version of the plugin. 
It is not a requirement to switch to the new default cache folder if your previous one was working fine, it's just an option.
For anyone having issues with their cache folder it is suggested to click the "Create Default Cache" button, which should eliminate
your issue. The cache folder field will then automatically update to reflect that you are using the default cache folder. You may delete the old cache folder you
were originally using.

7) AWSOM Pixgallery now also has the ability to create a default gallery folder in your uploads folder. You can create this folder by going to the Appearance Galleries admin
menu and clicking the create default gallery button.


== Screenshots ==

1. Example screenshot of multi-level gallery showing subgallery folders and thumbnails for current gallery.
2. Caption adding area showing usage of Visual Editor.
3. Admin options page showing some of the newer options added.