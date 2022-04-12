<?php
  namespace App\Service;

  use Symfony\Component\DomCrawler\Crawler;

   class ParserService
   {
       public function parse(string $url)
       {
           $c = curl_init($url);
           curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($c, CURLOPT_HEADER, false);

           // Сохраняем HTML страницы.
           $page = curl_exec($c);

           // Если не получилось получить доступ к странице (например, из-за частых
           // запросов или если страницы не существует).
           if (empty($page)) {
               return;
           }

           $crawler = new Crawler($page, "https://www.ozon.ru/");
        }
   }
