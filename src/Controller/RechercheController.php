<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\Piste;

use App\Entity\Panier\Panier;


class RechercheController extends AbstractController
{
	private EntityManagerInterface $entityManager;
	private LoggerInterface $logger;


	
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}
	
    #[Route('/afficheRecherche', name: 'afficheRecherche')]
    public function afficheRechercheAction(Request $request): Response
    {	
		$session = $request->getSession();
		$panier  = $session->get('panier', new Panier());
		// Chercher tout les articles
		// $query = $this->entityManager->createQuery("SELECT article FROM App\Entity\Catalogue\Article article");

		//Parametre de recherche
		$searchParam = $request->get("search");
		if ($searchParam) {
			$query = $this->entityManager->createQuery("SELECT article FROM App\Entity\Catalogue\Article article WHERE". " UPPER(article.titre) LIKE UPPER(:paramMotCle)");
			$query->setParameter("paramMotCle", "%".$searchParam."%");
		} else {
			$query = $this->entityManager->createQuery("SELECT article FROM App\Entity\Catalogue\Article article");
		}

		// Chercher les articles de sous-type livre
		//$query = $this->entityManager->createQuery("SELECT livre FROM App\Entity\Catalogue\Livre livre");



		$articles = $query->getResult();
		return $this->render('recherche.html.twig', [
            'articles' => $articles,
			'nbArticles'  => $panier->getNbArticles(),
        ]);
    }

	#[Route('/article', name:'article')]
	public function afficheArticleAction(Request $request): Response
	{
		$searchParam = $request->get("id");
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a "." where a.id=".$searchParam);
		
		$article = $query->getResult();
		return $this->render('article.html.twig', [
            'article' => $article[0],
        ]);
	}
	
    #[Route('/afficheRechercheParMotCle', name: 'afficheRechercheParMotCle')]
    public function afficheRechercheParMotCleAction(Request $request): Response
    {
		//$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a "
		//										  ." where a.titre like :motCle");
		//$query->setParameter("motCle", "%".$request->query->get("motCle")."%") ;
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a "
												  ." where a.titre like '%".addslashes($request->query->get("motCle"))."%'");
		$articles = $query->getResult();
		return $this->render('recherche.html.twig', [
            'articles' => $articles,
        ]);
    }
}
