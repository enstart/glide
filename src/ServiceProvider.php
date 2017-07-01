<?php namespace Enstart\Ext\Glide;

use Enstart\Container\ContainerInterface;
use Enstart\ServiceProvider\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Register the service provider
     *
     * @param  ContainerInterface $c
     */
    public function register(ContainerInterface $c)
    {
        // Add the view extension
        $extensions = $c->config->get('views.extensions', []);
        if (is_array($extensions)) {
            $extensions[] = 'Enstart\Ext\Glide\ViewExtension';
            $c->config->set('views.extensions', $extensions);
        }

        // Load and replace the default config with the user config
        $c->config->set('glide', array_replace_recursive(
            include __DIR__ . '/config.php',
            $c->config->get('glide', [])
        ));

        // Register the Glide extension
        $c->singleton('Enstart\Ext\Glide\Glide');
        $c->alias('Enstart\Ext\Glide\Glide', 'glide');

        // Add the routec-
        $c->router->get('/glide/(:all)', function ($file) use ($c) {
            return $c->glide->getResizedImage($file, $c->request->get()->all());
        }, ['name' => 'glide']);

    }
}
