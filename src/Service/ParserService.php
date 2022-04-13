<?php
  namespace App\Service;

  use GuzzleHttp\Client;
  use Symfony\Component\DomCrawler\Crawler;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;

   class ParserService
   {

       public function collect(string $url, Request $request)
       {

           $url = $request->request->all('form')['URL'];

           if (empty($page)) {
               return;
           }

           $client = new Client();
           $n = $client->get($url);

           $crawler = new Crawler($n->getBody()->getContents());

//Прописать условие? если товаров нет - возвращаем статистику - 0 товаров

           $pricePath = $crawler->filter(".ui-s4.ui-s6.ui-t");

           $tag = $crawler->filter(".in6");
           $b = [];
           foreach ($pricePath as $a) {
               var_dump($b[] = $a->nodeValue);
           }
       }
   }
