<?php namespace Enstart\Ext\Glide;

use Enstart\Config\ConfigInterface;
use Enstart\Http\RequestInterface;
use Enstart\Router\RouterInterface;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Urls\UrlBuilderFactory;

class Glide
{
    protected $config;
    protected $router;
    protected $request;
    protected $server;


    /**
     * @param ConfigInterface  $config
     * @param RequestInterface $request
     * @param RouterInterface  $router
     */
    public function __construct(ConfigInterface $config, RequestInterface $request, RouterInterface $router)
    {
        $this->config  = $config;
        $this->router  = $router;
        $this->request = $request;
    }


    /**
     * Get the URL to the resized photo
     *
     * @param  string       $filename
     * @param  string|array $params
     * @return string
     */
    public function getUrl($filename, $params)
    {
        if ($params && is_string($params)) {
            $params = ['p' => $params];
        }

        $route = $this->router->getRoute('glide.resize') . '/';

        $urlBuilder = UrlBuilderFactory::create($route, $this->config->get('glide.sign_key', null));
        $file       = $urlBuilder->getUrl($filename, $params);

        return $this->router->getRoute('glide', [$file]);
    }


    /**
     * Get the resized image
     *
     * @param  string       $filename
     * @param  string|array $params
     * @return SymfonyResponse
     */
    public function getResizedImage($filename, $params)
    {
        $route   = $this->router->getRoute('glide.resize') . '/';
        $signkey = $this->config->get('glide.sign_key');

        if ($signkey) {
            try {
                SignatureFactory::create($signkey)->validateRequest($route .  $filename, $params);
            } catch (SignatureException $e) {
                http_response_code(404);
                exit;
            }
        }

        $server = $this->server();

        // Set response factory
        $server->setResponseFactory(
            new \League\Glide\Responses\SymfonyResponseFactory()
        );

        return $server->getImageResponse($filename, $params);
    }


    /**
     * Delete cached files
     *
     * @param  string $filename
     * @return boolean
     */
    public function deleteCache($filename)
    {
        return $this->server()->deleteCache($filename);
    }


    /**
     * Delete cached and orignal file
     *
     * @param  string $filename
     * @return boolean
     */
    public function remove($filename)
    {
        $original = rtrim($this->config->get('glide.source'), '/');
        $original .= '/' . ltrim($filename, '/');

        if (is_file($original)) {
            unlink($original);
        }

        return $this->deleteCache($filename);
    }


    /**
     * Get the server instance
     *
     * @return League\Glide\Server
     */
    public function server()
    {
        if (is_null($this->server)) {
            $settings = $this->config->get('glide', []);
            $settings['base_url'] = $this->router->getRoute('glide.resize') . '/';

            $this->server = \League\Glide\ServerFactory::create($settings);
        }

        return $this->server;
    }
}
