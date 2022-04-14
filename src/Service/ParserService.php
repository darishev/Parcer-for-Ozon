<?php

namespace App\Service;

use App\Entity\Product;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ParserService
{
    private $entityManager;

//    public function __construct(EntityManagerInterface $entityManager)
//    {
//        $this->entityManager = $entityManager;
//    }

    public function collect(string $url)
    {

        $client = new Client();
        $new = $client->get($url);

        $crawler = new Crawler($new->getBody()->getContents());

//Прописать условие? если товаров нет - возвращаем статистику - 0 товаров

//           $tag = $crawler->filterXpath('//*[@id="state-searchResultsV2-252189-default-1"]')->outerHtml();
//           $cut = strstr($tag, '{"items');
//           $encode = strstr($cut, '\' > </div>', true);
//           $encode = json_decode($encode, true);
        $tag = $crawler->filterXPath('//*[@id="state-searchResultsV2-252189-default-1"]')->outerHtml();
        //*[@id="state-searchResultsV2-1359355-default-1"]
        $cut = strstr($tag, '{"items');
        $encode = strstr($cut, '\'></div>', true);
        $encode = json_decode($encode, true);
//           foreach ($encode as $key => $value){
//               var_dump($value);
//           }
//           return dd($encode);
//Json parsing
        $id = 3;
        $product = [
            'Counts' => count($encode['items']),
            'Name' => $encode['items'][$id]['mainState'][2]['atom']['textAtom']['text'],
            'Price' => $encode['items'][$id]['mainState'][0]['atom']['price']['price'],
            'Reviews' => $encode['items'][$id]['mainState'][3]['atom']['rating']['count'],
            'Sku' => $encode['items'][$id]['topRightButtons'][0]['favoriteProductMolecule']['sku'],
        ];

        $this->saveProduct($product);
        return dd($product);
    }

    public function saveProduct()
    {
        $em = $this->entityManager;

        $product = new Product();
        $product->setPrice('Price')
            ->setName('Name')
            ->setSku('Sku')
            ->setReviewsCount('Reviews');

        $em->persist($product);
        $em->flush($product);

    }
}
