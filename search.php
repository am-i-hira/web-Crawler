<?php
include_once("dbProcess.php");
set_time_limit(1000);

class crawlWeb {
    private $visitedUrls = [];
    private $searchResults = [];
    private $maxDepth;
    private $key;
    private $storeID;

    public function search($query, $maxDepth = 3) {
        $this->key=$query;
        $this->maxDepth = $maxDepth;
        $searchUrl = $query;
        $this->visitedUrls = []; // Reset visited URLs for each search
        $this->searchResults = []; // Reset search results for each search

        $this->crawlUrl($searchUrl, 0);
        // Store the results to JSON after processing each URL
        $this->storeResultsToDB();
        return $this->storeID;
    }

    private function crawlUrl($url, $depth) {
        if ($depth <= $this->maxDepth && !in_array($url, $this->visitedUrls) && $this->testRobotsTxt($url)) {
            $htmlContent = $this->fetchHtmlContent($url);

            if ($htmlContent !== false) {
                $this->parseSearchResults($url, $htmlContent);
                $this->visitedUrls[] = $url; // Mark the URL as visited

                // Recursively crawl links in the current page
                $this->crawlPageLinks($url, $htmlContent, $depth + 1);
            }
        }
    }

    private function crawlPageLinks($baseUrl, $htmlContent, $depth) {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $linkQuery = "//a[@href]";
        $links = $xpath->query($linkQuery);

        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            $absoluteUrl = $this->resolveUrl($baseUrl, $href);
            $this->crawlUrl($absoluteUrl, $depth);
        }
        
    }

    private function fetchHtmlContent($url) {
        $curl = curl_init($url);

        // Set cURL options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // Execute cURL session
        $htmlContent = curl_exec($curl);

        // test for errors during the cURL request
        if (curl_errno($curl)) {
            echo 'Error fetching URL: ' . curl_error($curl) . PHP_EOL;
            $htmlContent = false;
        }

        // Close cURL session
        curl_close($curl);
        return $htmlContent;
    }

    private function buildUrl($urlParts) {
        $scheme   = isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '';
        $host     = isset($urlParts['host']) ? $urlParts['host'] : '';
        $port     = isset($urlParts['port']) ? ':' . $urlParts['port'] : '';
        $path     = isset($urlParts['path']) ? $urlParts['path'] : '';
        $query    = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';
        $fragment = isset($urlParts['fragment']) ? '#' . $urlParts['fragment'] : '';
    
        return $scheme . $host . $port . $path . $query . $fragment;
    }

    private function testRobotsTxt($url) {
        $robotsUrl = parse_url($url);
        $robotsUrl['path'] = '/robots.txt';
        $robotsTxtUrl = $this->buildUrl($robotsUrl);   
    
        $robotsTxtContent = $this->fetchHtmlContent($robotsTxtUrl);
    
        // Implement logic to test if $robotsTxtContent allows crawling of $url
        if ($robotsTxtContent !== false) {
            // Split the robots.txt content into lines
            $lines = explode("\n", $robotsTxtContent);
    
            // Loop through each line in the robots.txt file
            foreach ($lines as $line) {
                // Remove leading and trailing whitespaces
                $line = trim($line);
    
                // test for comments and ignore them
                if (empty($line) || $line[0] === '#') {
                    continue;
                }
    
                
                // test for Disallow directive if the user agent is applicable
                if (strpos($line, 'Disallow:') !== false) {
                    // Extract the disallowed path
                    $disallowedPath = trim(str_ireplace('Disallow:', '', $line));
    
                    // test if the URL matches the disallowed path
                    if (isset($robotsUrl['path']) && $robotsUrl['path'] == $disallowedPath) {
                        return false; // URL is disallowed by robots.txt
                    }   
                }
            }
        }
    
        return true; // URL is allowed by robots.txt
    }

    private function parseSearchResults($baseUrl,$htmlContent) {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $query = "//a | //p | //h1 | //h2 | //h3 | //h4 | //h5 | //h6";

        $results = $xpath->query($query);

        if ($results->length > 0) {
            $urlResults = ['url' => $baseUrl, 'data' => []];

            foreach ($results as $result) {
                if ($result->nodeName === 'a') {
                    continue;
                } else {
                    $content = $result->nodeValue;
                    $urlResults['data'][] = $content;
                }
            }

            $this->searchResults[] = $urlResults;
        }
    }

    private function storeResultsToDB() {
        $db = new DBPROCESS();
        $id=$db->addUrl($this->key);
        $this->storeID=$id;
        $jsonData = json_encode($this->searchResults, JSON_PRETTY_PRINT);
        $db->addData($jsonData,$id);
    }

    private function resolveUrl($baseUrl, $url) {
        // test if the URL is already absolute
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;  // If it's absolute, return as it is
        }
        // Use PHP's built-in function to resolve relative URLs
        $absoluteUrl = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
    
        return $absoluteUrl;
    }
}



if(isset($_POST['url']) && isset($_POST['depth'])){
    $url=$_POST['url'];
    $depth=$_POST['depth'];
    $urlSearch=new DBPROCESS();
    $id=$urlSearch->test($url);
    if($id){
        header("location: crawlResults.php?id=$id");
    }else{
        $webSearch = new crawlWeb();
        $res=$webSearch->search($url,$depth);
        if($res){
            header("location: crawlResults.php?id=$res");
        }else{
            header("location: index.html");
        }
    }
}
?>
