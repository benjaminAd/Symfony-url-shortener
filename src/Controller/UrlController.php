<?php

namespace App\Controller;

use App\Repository\UrlRepository;
use App\service\UrlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlController extends AbstractController
{
    private UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    #[Route('/url', name: 'url')]
    public function index(): Response
    {
        return $this->render('url/index.html.twig', [
            'controller_name' => 'UrlController',
        ]);
    }

    #[Route('/ajax/shorten', name: 'url_add')]
    public function add(Request $request): Response
    {
        $longurl = $request->request->get('url');
        if (!$longurl) {
            return $this->json([
                'statusCode' => 400,
                'statusText' => "Missing args",
            ]);
        }
        $domaine = $this->urlService->parseUrl($longurl);
        if (!$domaine) {
            return $this->json([
                "statusCode" => 500,
                "statusText" => "Invalid arg URl"
            ]);
        }
        $url = $this->urlService->addUrl($longurl, $domaine);
        return $this->json([
            "link" => $url->getShortUrl(),
            "longUrl" => $url->getLongUrl(),
        ]);
    }

    #[Route('/{hash}', name: 'url_view ')]
    public function view(string $hash, UrlRepository $urlRepository): Response
    {
        $url = $urlRepository->findOneBy(['hash' => $hash]);
        if (!$url) {
            return $this->redirectToRoute('app_homepage');
        }

        return $this->redirect($url->getLongUrl());
    }
}
