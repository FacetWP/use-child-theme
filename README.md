# use-child-theme.php
This drop-in helps ensure that customers are using a child theme. Child themes allow users to upgrade your theme without losing any customizations.

## How it works

```
- Is this a child theme?
    - No
        - If child theme doesn't exist, create it
        - Nag users to switch to child theme
```

## How to use it

If you're a theme developer, include `use-child-theme.php` in your theme, then add this code into functions.php:

```php
include( 'use-child-theme.php' );
```
