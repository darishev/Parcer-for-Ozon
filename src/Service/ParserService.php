<?php
  namespace App\Service;

  use Symfony\Component\DomCrawler\Crawler;
  use Symfony\Component\HttpFoundation\Response;
  use App\Service\DOMDocument;
  use App\Service\DOMXPath;

   class ParserService
   {
       public function parse(string $url, Response $response)
       {
           $httpClient = new \GuzzleHttp\Client();
           $response = $httpClient->get("https://www.ozon.ru/category/sportivnye-aksessuary-33004/");
           $htmlString = (string) $response->getBody();
//add this line to suppress any warnings
           libxml_use_internal_errors(true);

           $doc = new DOMDocument();
           $doc->loadHTML($htmlString);
           $xpath = new DOMXPath($doc);
           $crawler = new Crawler($page, "https://www.ozon.ru/");
        }
   }
