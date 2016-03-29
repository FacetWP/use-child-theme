**Use Child Theme**: Add child theme support to any WordPress theme

<img src="http://i.imgur.com/XqYZcTA.png" width="680" height="349" />

### What problem does this solve?
If you make changes to a theme, those changes will be lost when the theme is updated.
With a child theme, your changes are kept separate, so the parent theme can be safely updated.

### How it works

* If a child theme is not active, the user will see a WP notice with an "Activate" link **on the Theme Editor screen**
* When clicked, a child theme is created (if needed) and enabled

### Setup

<a href="https://vimeo.com/160399404" target="_blank">Watch screencast (27 seconds)</a>

If you're a theme developer, include `use-child-theme.php` in your theme, then add this code into functions.php:

```php
include( dirname( __FILE__ ) . '/use-child-theme.php' );
```

### Changelog

**0.4**
* Added `uct_dismiss_timeout` filter

**0.3**
* Code cleanup

**0.2**
* Create a child theme (if needed) only when user clicks "Activate"
* Hide notice for 24 hours when dismissed
* Support .gif and .jpeg screenshots

**0.1**
* Initial release
