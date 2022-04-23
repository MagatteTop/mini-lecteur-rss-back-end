<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/charger", name="app_api")
     */
    public function index(ManagerRegistry $registry): Response
    {
          $rss = simplexml_load_file("http://localhost/en_continu.xml");
//          $rss = simplexml_load_file("https://www.lemonde.fr/rss/en_continu.xml");
          $repository=new ArticleRepository($registry);
          foreach ($rss->channel->item as $item) {
            $article = new Article();
            $article->setTitre($item->title);
            $article->setDescription($item->description);
            $datePub=new \DateTime($item->pubDate->__toString());
            $article->setDatePub($datePub);
            $article->setLien($item->link);
            $article->setCategorie($rss->channel->title);
            $article->setLienImage("https://img.lemde.fr/2022/04/22/2000/0/4000/2000/2048/1024/45/0/77782a4_805786930-ad021-lepenarras-dsc07825.jpg");
            $repository->add($article);
        }


        $response = new Response();
        $response->setContent(json_encode([
            'data' => $rss,
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
