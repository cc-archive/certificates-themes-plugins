# CC-Certification Wordpress Themes and Plugins
blame Alan Levine • @cogdog • http://cog.dog

For development of the [Creative Commons Certification](http://certificates.creativecommons.org/)

[![CC-Certified Web Site](images/cc-cert-site.jpg)]((http://certificates.creativecommons.org/))

This site is running Wordpress multisite for:

* Main project site http://certificates.creativecommons.org/
* Public Version of CORE Certificate http://certificates.creativecommons.org/core
* Public Version of CORE Certificate in another theme http://certificates.creativecommons.org/demo
* Public Version of Librarian Certificate http://certificates.creativecommons.org/lib
* Public Version of Educator Certificate http://certificates.creativecommons.org/edu
* Public Version of Government Certificate http://certificates.creativecommons.org/gov
* Quest Bank http://certificates.creativecommons.org/quests

The public sites are using Markdown from GotHub rendered on viewing as HTML. Multisite will allow easy duplication of sites for hosted versions of the main certifications using NS Cloner plugin. When these are copied (or exported for hosting elsewhere), the content is now in standard HTML content

*See the [Technology Development blog posts](https://certificates.creativecommons.org/blog/category/tech/) from the project site for developer notes.*

## Main Project Site http://certificates.creativecommons.org/


### Wordpress Themes

* [CC-Cert Child Theme](wp-content/themes/cc-cert) version 0.61 child theme of [Cover](http://eichefam.net/projects/cover)

Theme includes custom front page modifications for links to the certificates, and a Gravity form driven systen for the [What If video collection](https://certificates.creativecommons.org/what-if/). 

### Wordpress Plugins
* [Easy Theme and Plugin Upgrades](https://wordpress.org/plugins/easy-theme-and-plugin-upgrades/) to update custom themes w/o need for FTP access
* [Flickr Justified Gallery](https://wordpress.org/plugins/flickr-justified-gallery/)  beautiful galleries from flickr user, set, groups
* [Gravity Forms](http://www.gravityforms.com) $ everything and anything for forms that work with Wordpress content
* [Jetpack](https://wordpress.org/plugins/jetpack/) combo tool, the Cover theme uses several modules like infinite scroll, etc.
* [NS Cloner](https://wordpress.org/plugins/ns-cloner-site-copier/) Network activated plugin for one click duplication of any internal site
* [Pagelist](https://wordpress.org/plugins/page-list/) dynamically creates lists of pags, subpages based on parent-child relationships
* [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/) Network activated for performance benefit
* [WP Video Lightbox](https://wordpress.org/plugins/wp-video-lightbox/) shows web video in overlay

### Custom Plugin
* [CC Certification Helper](wp-content/plugins/cc-cert-helper) - adds to Certification content pages the top and side navigation, provides filtering for GutHub content (search/replace on links to .md, and re-writes image URLs to use GitHub Pages URLs for images, adds admin metabox for options of Certification content. This will need an options panel once all functionality is finalized. It's likely the [CC Certification Assistant plugin]((wp-content/plugins/cc-cert-assistant) will supersede this one

## Certification Sites 

* http://certificates.creativecommons.org/core
* http://certificates.creativecommons.org/lib
* http://certificates.creativecommons.org/edu
* http://certificates.creativecommons.org/gov

### Wordpress Themes

* [CC-Certificates Child Theme](wp-content/themes/cc-certificates) version 0.65 child theme of [Cover](http://eichefam.net/projects/cover) for main hosted certificates

* https://certificates.creativecommons.org/demo/

* [CC Certificates Sixteen](wp-content/themes/cert-sixteen) version 0.1 child theme of [Twenty Sixteen](https://wordpress.org/themes/twentysixteen/) for demo version of Core Certificate in a different theme. Child theme needed to add full page width template

### Wordpress Plugins
* [Easy Theme and Plugin Upgrades](https://wordpress.org/plugins/easy-theme-and-plugin-upgrades/) to update custom themes w/o need for FTP access
* [Gravity Forms](http://www.gravityforms.com) $ everything and anything for forms that work with Wordpress content
* [Jetpack](https://wordpress.org/plugins/jetpack/) combo tool, the Cover theme uses several modules like infinite scroll, etc.
* [NS Cloner](https://wordpress.org/plugins/ns-cloner-site-copier/) Network activated plugin for one click duplication of any internal site
* [Pagelist](https://wordpress.org/plugins/page-list/) dynamically creates lists of pags, subpages based on parent-child relationships
* [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/) Network activated for performance benefit

### Custom Plugin
* [CC Certification Assistant](wp-content/plugins/cc-cert-assistant) - adds to Certification content pages the top and side navigation, provides filtering for GutHub content (search/replace on links to .md, and re-writes image URLs to use GitHub Pages URLs for images, adds admin metabox for options of Certification content. Admin users will be able to edit the options; other users can only add extra footer info, e.g. for featured image credit) This will need an options panel once all functionality is finalized. It's likely the [CC Certification Assistant plugin]((wp-content/plugins/cc-cert-assistant) will supercede this one

## Quest Bank http://certificates.creativecommons.org/quests

* [DS106 Assignment Bank Theme](https://github.com/cogdog/ds106bank)

### Wordpress Plugins

* [Easy Theme and Plugin Upgrades](https://wordpress.org/plugins/easy-theme-and-plugin-upgrades/) to update custom themes w/o need for FTP access
* [Flexible Posts Widget](https://wordpress.org/plugins/flexible-posts-widget/) for footer widgets to show custom post type listings of recent items
* [Jetpack](https://wordpress.org/plugins/jetpack/) combo tool, the Cover theme uses several modules like infinite scroll, etc.
* [WP Post Ratings](https://wordpress.org/plugins/wp-postratings/) for user rating of quests
* [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/) Network activated for performance benefit






