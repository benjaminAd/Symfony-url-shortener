<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UrlRepository;
use App\Repository\UrlStatisticRepository;
use App\service\UrlService;
use App\service\UrlStatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User getUser()
 */
class UrlController extends AbstractController
{
    private UrlService $urlService;
    private UrlStatisticService $urlStatisticService;

    public function __construct(UrlService $urlService, UrlStatisticService $urlStatisticService)
    {
        $this->urlService = $urlService;
        $this->urlStatisticService = $urlStatisticService;
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

        if (!$url->getUserproperty()) {
            return $this->redirect($url->getLongUrl());
        }

        $urlStatistic = $this->urlStatisticService->findOneByUrlAndDate($url, new \DateTime);
        $this->urlStatisticService->incrementUrlStatistic($urlStatistic);

        return $this->redirect($url->getLongUrl());
    }

    #[Route('/ajax/delete/{hash}', name: 'url_delete')]
    public function delete(string $hash): Response
    {
        return $this->urlService->deleteUrl($hash);
    }

    #[Route('/user/links', name: 'links_view ')]
    public function list()
    {
        $user = $this->getUser();

        if (!$user || $user->getUrls()->count() == 0) {
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render("url/list.html.twig", [
            'urls' => $user->getUrls()
        ]);

    }

    #[Route('/statistics/{hash}', name: 'url_stats')]
    public function statistics(string $hash, UrlRepository $urlRepository, UrlStatisticRepository $urlStatisticRepository): Response
    {
        $url = $urlRepository->findOneBy([
            'hash' => $hash
        ]);
        if (!$url) {
            return $this->redirectToRoute('app_homepage');
        }

        $url_statistique = $urlStatisticRepository->findOneByUrl($url);

        $chart = $this->urlStatisticService->createChart($url_statistique['labels'], $url_statistique['datasets']['data']);

        return $this->render('url/statistics.html.twig', [
            'chart' => $chart,
            'domain' => $url->getDomain()
        ]);

    }
}
