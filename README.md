# blade

A library for using [Laravel Blade](https://laravel.com/docs/5.7/blade) templates in [WordPlate](https://wordplate.github.io/).

## Installation

TODO

## Usage

### Render

Use helper function `blade`:

```php
blade('index', ['machine' => 'Voight-Kampff']);
```

(This renders and echoes the template `/resources/views/index.blade.php` and caches it to `/storage/views`.)

... or instantiate `Blade` by passing the folder(s) where your view files are located, and a cache folder. Render a template by calling the `render` method.

```php
use Fiskhandlarn\Blade;

$blade = new Blade(get_stylesheet_directory() . '/views', get_stylesheet_directory() . '/cache');

echo $blade->render('index', ['machine' => 'Voight-Kampff']);
```

### Custom directive

Create a custom [directive](https://laravel.com/docs/5.7/blade#extending-blade) with helper function `blade_directive`:

```php
blade_directive('datetime', function ($expression) {
    return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
});
```

... or use the `directive` method on a `Blade` object:

```php
$blade->directive('datetime', function ($expression) {
    return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
});
```

Then you can use the directive in your templates:

```php
{{-- In your Blade template --}}
@php $dateObj = new DateTime('2019-11-01 00:02:42') @endphp
@datetime($dateObj)
```

## Cache

If `WP_DEBUG` is set to `true` templates will always be rendered and updated.

## Multisite

If run on a WordPress Multisite the cached files will be separated in subfolders by each site's blog id.

## Filters used by `blade` helper

Use the `blade/view/paths` filter to customize the base paths where your templates are stored. (Default value is `/resources/views`.)

```php
add_filter('blade/view/paths', function ($paths) {
    $paths = (array) $paths;

    $paths[] = get_stylesheet_directory() . '/views';

    return $paths;
});
```

Use the `blade/cache/path` filter to customize the cache folder path. (Default value is `/storage/views`.)

```php
add_filter('blade/cache/path', function ($path) {
    $uploadDir = wp_upload_dir();
    return $uploadDir['basedir'] . '/.bladecache/';
});
```

Use the `blade/cache/path` filter to control creation of cache folder. (Default value is `true`.)

```php
add_filter('blade/cache/create', '__return_false');
```
