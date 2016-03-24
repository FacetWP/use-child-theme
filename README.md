# use-child-theme.php
This drop-in helps ensure that customers are using a child theme. Child themes allow users to upgrade your theme without losing any customizations.

## How it works
This drop-in checks whether the current theme is a child. If not, it then checks to see if a child theme exists.

If a child theme doesn't exist, it creates one. Finally, it'll notify users to switch to the child theme.

## How to use it

If you're a theme developer, include `use-child-theme.php` in your theme, then add this code into functions.php:

```php
include( 'use-child-theme.php' );
```
