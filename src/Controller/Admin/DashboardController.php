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
use App\Entity\Products;
use App\Entity\Seller;


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
        $ParserService = new ParserService($this->em);

        $form = $this->createFormBuilder()
            ->add('URL', TextType::class)
            ->add('Search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $request->request->all('form')['URL'];
            //Проверка URL на абсолютный путь
            if (preg_match("/^(?:ftp|https?|feed)?:?\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*
    (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:
    (?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?]
    (?:[\w#!:\.\?\+\|=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi", $url)) {
                return $result = $ParserService->collect($url);
            }
        }
            return $this->render('/EasyAdminBundle/page/content.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        public function configureDashboard(): Dashboard
        {
            return Dashboard::new()
                ->setTitle('Parser for Ozon');
        }

        public function configureMenuItems(): iterable
        {
            yield MenuItem::linkToDashboard('parser', 'fa fa-home');
            // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
            yield MenuItem::linkToCrud('Products', 'fas fa-map-marker-alt', Products::class);
            yield MenuItem::linkToCrud('Seller', 'fas fa-comments', Seller::class);
        }
    }
