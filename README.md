# Rabble Menu Bundle
A bundle that allows adding content from the content bundle to a menu navigation.

# Installation
Install the bundle by running
```sh
composer require rabble/menu-bundle
```

Add the following class to your `config/bundles.php` file:
```php
return [
    ...
    Rabble\MenuBundle\RabbleMenuBundle::class => ['all' => true],
]
```
