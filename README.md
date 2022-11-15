# Page Crawler

## Required config file

Please include and populate the config file "config/page-crawler.php". Use the following as a starting point.

```
<?php

return [
    /*
     * CSS selectors for elements which should be removed from rendered page markup during a page crawl.
     */
    'elementsToRemove' => [

    ]
];
```

## Performing a crawl.

You can perform a crawl using PHP by calling the following function. This function accepts a relative page path and will return all relevant page content as text.

```
$content = \creode\pagecrawler\Plugin::$plugin->crawlerService->crawl($pagePath);
```
