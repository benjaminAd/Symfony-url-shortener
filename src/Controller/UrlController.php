<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlController extends AbstractController
{
    #[Route('/url', name: 'url')]
    public function index(): Response
    {
        return $this->render('url/index.html.twig', [
            'controller_name' => 'UrlController',
        ]);
    }
}
