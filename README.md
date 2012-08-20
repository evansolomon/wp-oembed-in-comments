# oEmbed In Comments

WordPress supports oEmbed, but only in post content. Lame. Let's see if we can make it work in comment text.


### Beta version

This has gone through some testing but may not be totally finished.

### Notes
WordPress core caches oEmbed results in post meta. Since this plugin uses WordPress core's oEmbed methods, comment oEmbed results get cached in their parents' post meta. If comments with oEmbed media ever gets rendered in a context where the `$post` global is not the comment's parent, you may end up with weird (but probably not problematic) data in your database. There is an [open WordPress core ticket](http://core.trac.wordpress.org/ticket/14759) about this.