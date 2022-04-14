<?php

namespace App\Controller\Admin;

use App\Service\ParserService;
use App\Form\TestFormType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Twig\Environment;


class DashboardController extends AbstractDashboardController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/parser', name: 'admin')]
    public function form(Request $request)
    {
        $ParserService = new ParserService(EntityManagerInterface::$em);

        $form = $this->createFormBuilder()
            ->add('URL', TextType::class)
            ->add('Search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $request->request->all('form')['URL'];
//            if (collectData()){
                $result = $ParserService->collect($url);
//Прописать условие? если товаров нет - возвращаем статистику - 0 товаров
            } else echo 'URL has not valid values';
//        }

        return $this->render('/EasyAdminBundle/page/content.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function collectData(Request $request)
    {
        $url = $request->request->all('form')['URL'];
        //  Проверка URL
            if (preg_match("/^(?:ftp|https?|feed)?:?\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*
    (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:
    (?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?]
    (?:[\w#!:\.\?\+\|=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi", $url))
            {
                return true;
            }
            else echo 'URL has not valid values';
    }


//if ($form->isSubmitted() && $form->isValid()) {
//$em = $this->getDoctrine()->getManager();
//
//    // Получаем информацию о товаре с Яндекс маркета.
//    // Возвращаем объект с нужными свойствами и потом записываем их в продукт.
//    // $y_market_url = $scrapper->parse($product->getUrl());
//    // The current node list is empty.
//
//try {
//$y_market_url = $scrapper->parse($product->getUrl());
//} catch (\Exception $e) {
//    return;
//}
//
//            if (isset($y_market_url)) {
//                $product->setTitle($y_market_url->title)
//                    ->setPrice($y_market_url->price)
//                    ->setDescription($y_market_url->description)
//                    ->setImage($y_market_url->image);
//            }
//            $em->persist($product);
//            $em->flush();
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Parser for Ozon');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('parser', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
