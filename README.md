**Use Child Theme**: Add child theme support to any WordPress theme

<img src="http://i.imgur.com/XqYZcTA.png" width="680" height="349" />

### What problem does this solve?

If you make changes to a theme, those changes will be lost when the theme is updated.
With a child theme, your changes are kept separate, so the parent theme can be safely updated.

---

### How it works

* If a child theme is not active, the user will see a WP notice with an "Activate" link **on the Theme Editor screen**
* When clicked, a child theme is created (if needed) and enabled

---

### Setup

If you're a theme developer, include `use-child-theme.php` in your theme, then add this code into functions.php:

```php
require_once( trailingslashit( get_template_directory() ) . 'use-child-theme.php' );
```

#### Important:

You need to change `YOUR_THEME_SLUG` text domain in the `use-child-theme.php` file to match your (parent) theme folder name! Please, use a search & replace functionality of your code editor for that.

This is *required for you theme to pass WordPress.org theme review* (you can test your theme with [**Theme Check** plugin](https://wordpress.org/plugins/theme-check/)).

---

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
