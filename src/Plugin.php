<?php

namespace creode\pagecrawler;

use Craft;
use creode\pagecrawler\services\CrawlerService;

class Plugin extends \craft\base\Plugin
{

    public $config = [];

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Plugin::$plugin
     *
     * @var Plugin
     */
    public static $plugin;

    public function init()
    {
        parent::init();
        self::$plugin = $this;
        $this->config = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));
        $this->registerComponents();
    }

    private function registerComponents()
    {
        $this->setComponents(
            [
                'crawlerService' => CrawlerService::class,
            ]
        );
    }
}
