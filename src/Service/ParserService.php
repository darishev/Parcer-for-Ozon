<?php

namespace App\Service;

use App\Controller\Admin\DashboardController;
use App\Entity\Seller;
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
            foreach ($encode[$i]['mainState'] as $main) {
                if ($main['id'] === 'name') {
                    $name = $main['atom']['textAtom']['text'];
                    $products[$i]['name'] = $name;
                }
//print_r($mainState);
                if ($main['atom']['type'] === 'rating') {
                    $reviews = intval($main['atom']['rating']['count']);
                    $products[$i]['reviews'] = $reviews;
                } else return null;
            }
//$price
            $price = intval($encode[$i]['mainState'][0]['atom']['price']['price']);
            $products[$i]['price'] = intval((str_replace([' ₽', ' '], '', $price)));

//$sku
            $sku = intval($encode[$i]['topRightButtons'][0]['favoriteProductMolecule']['sku']);
            $products[$i]['sku'] = $sku;
//$seller
            $seller = $encode[$i]['multiButton']['ozonSubtitle']['textAtomWithIcon']['text'];
            $seller = strstr($seller, 'продавец');
            $seller = strip_tags($seller);
            $seller = str_replace('продавец ', '', $seller);
            $products[$i]['seller'] = $seller;
        }
        $result = $this->saveProduct($products);
        return $result;
    }

        public function saveProduct(array $products)
        {
            $em = $this->em;
            foreach ($products as $value) {
                $seller = $this->isSellerAlreadyExists($value['seller']);
                if ($seller === 0) {
                    $seller = new Seller();
                    $seller->setName($value['seller']);

                    $em->persist($seller);
                    $em->flush($seller);
                }

                $products = new Products();
                $products->setName($value['name'])
                    ->setPrice($value['price'])
                    ->setReviews($value['reviews'])
                    ->setSku($value['sku'])
                    ->setSeller($seller);

                $em->persist($products);
                $em->flush($products);
            }
            return dd($products);
        }

    public function isSellerAlreadyExists(string $onlyOneSeller)
    {
        //Проверка наличия продавца в базе данных под другим id
        $repository = $this->em->getRepository(Seller::class);
        $allData = $repository->findAll();
        foreach ($allData as $sellerData) {
            if ($onlyOneSeller == $sellerData->getName()) {
                return $sellerData;
            }
        }
        return 0;
    }
}


//        for ($seller = 1; $seller < 10; $seller++) {
//            return dd($this->em->getRepository(Seller::class)
//                ->findBy(array('id' => $seller)));
//        }
//        return dd($this->em->getRepository(Seller::class));
//        }
//        $em = $this->em;
//        foreach ($products as $value) {
//        $sellerSetname = new Seller();
//            $product = new Products();
//            $product->setPrice($value['price'])
//            ->setName($value['name'])
//            ->setSku($value['sku'])
//            ->setSeller(
//                $sellerSetname->setName('Testovoe imya'));
////            ->setReviewsCount($value['reviews']);
//            $seller= 'Базиатор';
//            dd($this->em->getRepository(Seller::class)->findBy(array('name'=>$seller)));
////              $seller = new Seller();
////              $seller->setName($value['seller']);
//
////            $em->persist($seller);
////            $em->flush($seller);
//
//            $em->persist($product);
//            $em->flush($product);
//        }
//        return $products;


//        $this->saveProduct($product);

//    public function saveProduct()
//    {
//        $em = $this->em;
//        foreach ($products as $value) {
//            $sellerSetname = new Seller();
//            $product = new Products();
//            $product->setPrice($value['price'])
//                ->setName($value['name'])
//                ->setSku($value['sku'])
//                ->setSeller(
//                    $sellerSetname->setName('Testovoe imya'));
////            ->setReviewsCount($value['reviews']);
//            $seller['id'] = 1;
//            dd($this->em->getRepository(Seller::class)->findBy(array('name' => $seller)));

//            $newSeller = $this->em
//                ->getRepository(Seller::class)
//                ->find($id);
////              $seller = new Seller();
////              $seller->setName($value['seller']);
//
////            $em->persist($seller);
////            $em->flush($seller);
//
//            $em->persist($product);
//            $em->flush($product);
//        }
//        }
//    }
//}
