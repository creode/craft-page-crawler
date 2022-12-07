# Page Crawler

## Versions

For details about which version of this package to use with your version of Craft CMS please see the table below:

| Craft Version | Page Crawler Version |
|-------------|---------------------|
| <4.0.0      | 0.x                 |
| 4.0.0       | 1.x                 |

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

## Performing a crawl

You can perform a crawl using PHP by calling the following function. This function accepts a relative page path and will return all relevant page content as text.

```
$content = \creode\pagecrawler\Plugin::$plugin->crawlerService->crawl($pagePath);
```
