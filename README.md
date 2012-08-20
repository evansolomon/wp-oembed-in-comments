# oEmbed In Comments

WordPress supports oEmbed, but only in post content. Lame.

### Usage
If the plugin is active, oEmbed will automatically be on in your comments on the front end. It doesn't have any UI, just activate it and go.

### Notes
WordPress core caches oEmbed results in post meta. Since this plugin uses WordPress core's oEmbed methods, comment oEmbed results get cached in their parents' post meta. If comments with oEmbed media ever gets rendered in a context where the `$post` global is not the comment's parent, you may end up with weird (but probably not problematic) data in your database. There is an [open WordPress core ticket](http://core.trac.wordpress.org/ticket/14759) about this.