<?php namespace Enstart\Ext\Glide;

use Enstart\Ext\Glide\Glide;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class ViewExtension implements ExtensionInterface
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var Glide
     */
    protected $glide;


    /**
     * @param Glide $glide
     */
    public function __construct(Glide $glide)
    {
        $this->glide = $glide;
    }


    /**
     * Register the extension
     *
     * @param  Engine $engine
     */
    public function register(Engine $engine)
    {
        $this->engine = $engine;

        $engine->registerFunction('glide', [$this, 'glide']);
    }


    /**
     * Generate url to a resized image
     *
     * @param  string        $filename
     * @param  string|array  $params
     * @return string
     */
    public function glide($filename, $params = [])
    {
        return $this->glide->getUrl($filename, $params);
    }
}
