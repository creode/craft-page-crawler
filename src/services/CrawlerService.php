<?php

namespace creode\pagecrawler\services;

use craft\base\Component;
use creode\pagecrawler\Plugin;
use craft\helpers\UrlHelper;
use PhpQuery\PhpQuery;

class CrawlerService extends Component
{

    protected $elementsToRemoveFromPage = [
        'head',
        'script'
    ];

    public function init(): void
    {
        parent::init();

        if(!empty(Plugin::$plugin->config['elementsToRemove'])) {
            $this->elementsToRemoveFromPage = array_merge($this->elementsToRemoveFromPage, Plugin::$plugin->config['elementsToRemove']);
        }
    }

    /**
     * Will perform a full page crawl.
     *
     * @param string $urlPath The relative page path to crawl.
     * @param ?int $limit The total number of allowed bytes in the returned text. If null, the full crawled text will be returned. Defaults to null.
     * @return string The text extracted from the page during the crawl.
     */
    public function crawl(string $urlPath = '', int $limit = null) {
        $baseUrl = UrlHelper::baseUrl();

        if(substr($baseUrl, -1) != '/') {
          $baseUrl = $baseUrl . '/';
        }

        $markup = $this->getPageMarkup($baseUrl . $urlPath);

        if(!$markup) {
            return '';
        }

        foreach($this->elementsToRemoveFromPage as $selector) {
            $markup = $this->removeElementsFromMarkup($markup, $selector);
        }

        $text = $this->convertMarkupToReadableText($markup);

        if($limit) {
            $text = mb_strcut($text, 0, $limit);
        }

        return $text;
    }

    protected function getPageMarkup($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($httpCode != 200) {
            return '';
        }

        return $output;
    }

    /**
     * Will remove any elements from a full HTML document markup by CSS selector.
     *
     * @param string $markup The full HTML document markup.
     * @param string $selector The CSS selector for elements to be removed.
     * @return string The full HTML document markup with elements removed.
     */
    protected function removeElementsFromMarkup(string $markup = '', string $selector = '')
    {
        $phpQuery = new PhpQuery;

        $phpQuery->load_str($markup);

        $elements = $phpQuery->query($selector);

        if (!$elements->count()) {
            return $markup;
        }

        foreach ($elements as $element) {
            $element->parentNode->removeChild($element);
        }

        return $elements->item(0)->ownerDocument->saveHTML();
    }

    /**
     * Will format and remove tags from an HTML string.
     *
     * @param string $markup The HTML string.
     * @return string The formatted text.
     */
    protected function convertMarkupToReadableText(string $markup = '')
    {
        $markup = str_replace(PHP_EOL, '', $markup);
        $markup = str_replace('<', 'PHP_EOL<', $markup);
        $markup = strip_tags($markup);
        $markup = html_entity_decode($markup);
        $markup = preg_replace('/\s+/', ' ', $markup);
        $markup = str_replace('PHP_EOL ', 'PHP_EOL', $markup);
        $markup = preg_replace('/(PHP_EOL)+/', 'PHP_EOL', $markup);
        $markup = str_replace('PHP_EOL', PHP_EOL, $markup);
        $markup = trim($markup);

        return $markup;
    }

}
