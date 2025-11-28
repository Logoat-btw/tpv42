<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Psr\Log\LoggerInterface;

use App\Entity\Catalogue\Article;
use App\Entity\Panier\Panier;
use App\Entity\Panier\LignePanier;

use Doctrine\ORM\EntityManagerInterface;

class PanierController extends AbstractController
{
	private EntityManagerInterface $entityManager;
	private LoggerInterface $logger;
	
	private Panier $panier;
	
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}
	
    #[Route('/ajouterLigne', name: 'ajouterLigne')]
    public function ajouterLigneAction(Request $request): Response
    {
		$session = $request->getSession() ;
		if ($session->has("panier"))
			$this->panier = $session->get("panier") ;
		else
			$this->panier = new Panier() ;
		$article = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
		$this->panier->ajouterLigne($article) ;
		$session->set("panier", $this->panier) ;
		return $this->render('panier.html.twig', [
            'panier' => $this->panier,
        ]);
    }
	
    #[Route('/supprimerLigne', name: 'supprimerLigne')]
    public function supprimerLigneAction(Request $request): Response
    {
		$session = $request->getSession() ;
		if ($session->has("panier"))
			$this->panier = $session->get("panier") ;
		else
			$this->panier = new Panier() ;
		$this->panier->supprimerLigne($request->query->get("id")) ;
		$session->set("panier", $this->panier) ;
		if (sizeof($this->panier->getLignesPanier()) === 0)
			return $this->render('panier.vide.html.twig');
		else
			return $this->render('panier.html.twig', [
				'panier' => $this->panier,
			]);
    }
	
    #[Route('/recalculerPanier', name: 'recalculerPanier', methods: ["GET", "POST"])]
    public function recalculerPanierAction(Request $request): Response
    {
		$session = $request->getSession() ;
		if ($session->has("panier"))
			$this->panier = $session->get("panier") ;
		else
			$this->panier = new Panier() ;
		$it = $this->panier->getLignesPanier()->getIterator();
		while ($it->valid()) {
			$ligne = $it->current();
			$article = $ligne->getArticle() ;
			// cart[1141555897821]["qty"]=4   https://symfony.com/doc/6.4/components/http_foundation.html
			$ligne->setQuantite($request->request->all("cart")[$article->getId()]["qty"]);
			$ligne->recalculer() ;
			$it->next();
		}
		$this->panier->recalculer() ;
		$session->set("panier", $this->panier) ;
		return $this->render('panier.html.twig', [
            'panier' => $this->panier,
        ]);
    }
	 
    #[Route('/accederAuPanier', name: 'accederAuPanier')]
    public function accederAuPanierAction(Request $request): Response
    {
		$session = $request->getSession() ;
		if ($session->has("panier"))
			$this->panier = $session->get("panier") ;
		else
			$this->panier = new Panier() ;
		if (sizeof($this->panier->getLignesPanier()) === 0)
			return $this->render('panier.vide.html.twig');
		else
			return $this->render('panier.html.twig', [
				'panier' => $this->panier,
			]);
    }
	
    #[Route('/commanderPanier', name: 'commanderPanier')]
    public function commanderPanierAction(Request $request): Response
    {
		return $this->render('commande.html.twig');
    }
	


    #[Route('/ajouterAuPanier', name: 'ajouterPanier')]
    public function ajouterPanierAction(Request $request): Response
    {
		//TODO créé une fonction qui permet d'enregistrer un article en restant sur la même page, ou a défault, en revenant sur la page rechercher
		// pour l'instant j'ai juste recopier ajouterLigne en changeant la fin pour ramener vers la page recherche.
		// Ca donne une erreur mais c'est à peu près ça je pense.
		
		
		$session = $request->getSession() ;
		if ($session->has("panier"))
			$this->panier = $session->get("panier") ;
		else
			$this->panier = new Panier() ;
		$article = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
		$this->panier->ajouterLigne($article) ;
		$session->set("panier", $this->panier) ;
		$searchParam = $request->get("search");
		if ($searchParam) {
			$query = $this->entityManager->createQuery("SELECT article FROM App\Entity\Catalogue\Article article WHERE". " UPPER(article.titre) LIKE UPPER(:paramMotCle)");
			$query->setParameter("paramMotCle", "%".$searchParam."%");
		} else {
			$query = $this->entityManager->createQuery("SELECT article FROM App\Entity\Catalogue\Article article");
		}

		$articles = $query->getResult();
		return $this->render('recherche.html.twig', [
            'articles' => $articles,
			'nbArticles'  => $this->panier->getNbArticles(),
        ]);
		
		
    }
}

