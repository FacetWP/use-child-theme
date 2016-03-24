# use-child-theme.php
This drop-in helps ensure that customers are using a child theme. Child themes allow users to upgrade your theme without losing any customizations.

<img src="http://i.imgur.com/dvLchUj.png" width="508" height="167" />

## How it works

_If a child theme isn't active..._
* It creates a child theme if one doesn't exist already
* It notifies users to activate the child theme (including a 1-click **Activate** button)
* It copies the parent theme's settings to the child theme (if needed)

## How to use it

If you're a theme developer, include `use-child-theme.php` in your theme, then add this code into functions.php:

```php
include( 'use-child-theme.php' );
```
