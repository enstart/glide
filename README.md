# Enstart Extension: Glide

Wrapper for Glide (on-demand image manipulation) for the enstart framework

### Dependencies:

* `enstart/core` version 0.2+
* PHP 7.0+ with either the gd- or imagick library

### Install

    composer require enstart/glide

### Config:

    // Settings
    'glide' => [
        'source'    => /path/to/upload/folder,
        'cache'     => /path/to/cache/folder',
    ],

    // Register the service provider
    'providers' => [
        ...
        'Enstart\Ext\Glide\ServiceProvider',
    ],


### Optional settings

There are a few optional settings as well:

    'glide' => [
        // If you want to make sure that all resize-requests are from your app, you can
        // add a sign key. This prevents mass image-resize attacks
        'sign_key'  => 'a-128-bit-random-key',

        // The default driver is 'gd', but if you rather use imagick, simply change it
        'driver'    => 'imagick',

        // To set some presets (predefined manipulators) which you then access through `?p=thumb`
        'presets' => [
            'thumb' => [
                'w'   => 200,
                'h'   => 200,
                'fit' => 'crop',
            ],
        ],
    ]

To read more about settings for glide, visit [Glides documentation](http://glide.thephpleague.com/1.0/config/setup/). This package just passes the `glide`-config to Glide, so anything in the Glide doc will work here as well.

### Access the extension

    // Get a copy of the instance
    $glide = $app->container->make('Enstart\Ext\Glide\Glide');

    // or through the alias:
    $app->glide

    // or through dependency injection (if you type hint it in your constructor)
    use Enstart\Ext\ImageResize\Glide;


### Get a link to a resized image

In your code:

    $url = $app->glide->getResizedImage('/path/to/image.jpg', [
        'w'   => 200,
        'h'   => 200,
        'fit' => crop
    ]);

    // or through a preset
    $url = $app->glide->getResizedImage('/path/to/image.jpg', ['p' => 'thumb']);

    // alternative preset request
    $url = $app->glide->getResizedImage('/path/to/image.jpg', 'thumb');


### View helpers

When you're in a view, you can use the view helper:

    <img src="<?= $this->glide('/path/to/image.jpg', 'thumb') ?>" />

The paths is relative from the source folder in your config. The view helper takes all the same parameters as the method `$app->glide->getResizedImage()` described above;

### Routing

There is a route (namned `glide`) which also has the slug `glide`. Any request to `yoursite.com/glide/{anything}` will be handled by the extension.

If you wish to change the route slug, simply create a new route in your routes.php with the same name.

    $app->get('/something-else/(:all)', function ($file) use ($app) {
        return $app->glide->getResizedImage($file, $app->request->get()->all());
    }, ['name' => 'glide']);

### Delete files

Only delete cached files:

    $app->glide->deleteCache('/path/to/image.jpg');

Delete cache and the original:

    $app->glide->remove('/path/to/image.jpg');

