<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;


class ParserController extends AbstractController
{
    #[Route('/parser', name: 'app_parser')]
    public function index(): Response
    {
//        $client = new Client([
//            // Base URI is used with relative requests
//            'base_uri' => 'https://www.ozon.ru/',
//            // You can set any number of default request options.
//            'timeout'  => 2.0,
//        ]);
        return $this->render('parser/content.html.twig', [
            'controller_name' => 'ParserController',
        ]);
    }
}
