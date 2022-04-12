<?php

namespace App\Controller\Admin;

use App\Form\TestFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class DashboardController extends AbstractDashboardController
{

    #[Route('/parser', name: 'admin')]
    public function form(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('URL', TextType::class)
            ->add('Search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $request->request->all('form')['URL'];
//
            if (preg_match("/^(http:\/\/|https:\/\/)*[а-яА-ЯёЁa-z0-9\-_]+(\.[а-яА-ЯёЁa-z0-9\-_]+)+(\/\S*)*$/iu", $url)) {
// Инициализируем класс для работы с удаленными веб-ресурсами
            $client = new Client();

// Делаем запрос, получаем ответ
            $n = $client->get($url);

            $crawler = new Crawler($n->getBody()->getContents());
            var_dump($crawler);
//            foreach ($response as $domElement) {
//                var_dump($domElement->nodeName);
//                }

            } else echo 'URL has not valid values';
        }

        return $this->render('/EasyAdminBundle/page/content.html.twig', [
            'form' => $form->createView(),
        ]);
    }

//    public function collectData()
//    {
//// Инициализируем класс для работы с удаленными веб-ресурсами
//        $client = new Client();
//
//// Делаем запрос, получаем ответ
//        $response = $client->request('POST', $url, [
//        ]);
//
//// Выводим ответ
//        return $response->getBody();
//    }
//
//    public function collectData()
//    {
//        //  Проверка URL на валидность
//            if (preg_match("/^(http:\/\/|https:\/\/)*[а-яА-ЯёЁa-z0-9\-_]+(\.[а-яА-ЯёЁa-z0-9\-_]+)+(\/\S*)*$/iu", $url))
//            {
//                echo true;
//            }
//            else echo 'URL has not valid values';
//
//    }
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
