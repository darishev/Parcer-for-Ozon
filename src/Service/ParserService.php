<?php

namespace App\Service;

use App\Entity\Products;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ParserService
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function collect(string $url)
    {

        $client = new Client();
        $new = $client->get($url);

        $crawler = new Crawler($new->getBody()->getContents());

//Прописать условие? если товаров нет - возвращаем статистику - 0 товаров

        $tag = $crawler->filterXPath('//*[@id="state-searchResultsV2-252189-default-1"]')->outerHtml();
        $cut = strstr($tag, '{"items');
        $encode = strstr($cut, '\'></div>', true);
        $encode = json_decode($encode, true)['items'];

        $count = count($encode);

        $products = [];
        for ($i = 0; $i < $count; $i++) {
            foreach ($encode[$i]['mainState'] as $mainState) {
                if ($mainState['id'] === 'name') {
                    $name = $mainState['atom']['textAtom']['text'];
                    $products[$i]['name'] = $name;
                }

//print_r($mainState);
                if ($mainState['atom']['type'] === 'rating') {
                    $reviews = intval($mainState['atom']['rating']['count']);
                    $products[$i]['reviews'] = $reviews;
                }
            }
//$price
            $price = $encode[$i]['mainState'][0]['atom']['price']['price'];
            $products[$i]['price'] = intval(str_replace([' ₽', ' '], '', $price));
//$sku
            $sku = intval($encode[$i]['topRightButtons'][0]['favoriteProductMolecule']['sku']);
            $products[$i]['sku'] = $sku;
            $seller = $encode[$i]['multiButton']['ozonSubtitle']['textAtomWithIcon']['text'];
//$seller
            $seller = stristr($seller, 'продавец');
            $seller = strip_tags($seller);
            $seller = str_replace('продавец ', '', $seller);
            $products[$i]['seller'] = $seller;
        }

        $this->saveProduct($products);
        return dd($products);
    }

//        $this->saveProduct($product);

    public function saveProduct()
    {
        $em = $this->em;

        $product = new Products();
        $product->setPrice('price')
            ->setName('name')
            ->setSku('sku')
            ->setReviewsCount('reviews');
//            ->setSeller();
        $em->persist($product);
        $em->flush($product);
    }
}
