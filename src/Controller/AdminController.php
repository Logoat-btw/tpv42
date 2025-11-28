<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\VideoGame;

use App\Entity\Panier\Commande;
use App\Form\CommandeType;

class AdminController extends AbstractController
{
	private EntityManagerInterface $entityManager;
	private LoggerInterface $logger;
	
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}

    #[Route('/admin/article', name: 'adminArticle')]
    public function adminArticleAction(Request $request): Response
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
		$article = $query->getResult();
		return $this->render('admin.articles.html.twig', [
            'articles' => $article,
        ]);
    }
	
    #[Route('/admin/musiques', name: 'adminMusiques')]
    public function adminMusiquesAction(Request $request): Response
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Musique a");
		$articles = $query->getResult();
		return $this->render('admin.musiques.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/admin/livres', name: 'adminLivres')]
    public function adminLivresAction(Request $request): Response
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Livre a");
		$articles = $query->getResult();
		return $this->render('admin.livres.html.twig', [
            'articles' => $articles,
        ]);
    }
	
    #[Route('/admin/jeux', name: 'adminJeux')]
    public function adminJeuxAction(Request $request): Response
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\VideoGame a");
		$jeux = $query->getResult();
		return $this->render('admin.jeux.html.twig', [
            'articles' => $jeux,
        ]);
    }
	
    #[Route('/admin/musiques/supprimer', name: 'adminMusiquesSupprimer')]
    public function adminMusiquesSupprimerAction(Request $request): Response
    {
		$entityArticle = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
		if ($entityArticle !== null) {
			$this->entityManager->remove($entityArticle);
			$this->entityManager->flush();
		}
		return $this->redirectToRoute("adminMusiques") ;
    }
	
    #[Route('/admin/livres/supprimer', name: 'adminLivresSupprimer')]
    public function adminLivresSupprimerAction(Request $request): Response
    {
		$entityArticle = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
		if ($entityArticle !== null) {
			$this->entityManager->remove($entityArticle);
			$this->entityManager->flush();
		}
		return $this->redirectToRoute("adminLivres") ;
    }
	#[Route('/admin/jeux/supprimer', name: 'adminLivresSupprimer')]
    public function adminJeuxSupprimerAction(Request $request): Response
    {
		$entityJeu = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
		if ($entityJeu !== null) {
			$this->entityManager->remove($entityJeu);
			$this->entityManager->flush();
		}
		return $this->redirectToRoute("adminJeux") ;
    }

    #[Route('/admin/livres/ajouter', name: 'adminLivresAjouter')]
    public function adminLivresAjouterAction(Request $request): Response
    {
		$entity = new Livre() ;
		$formBuilder = $this->createFormBuilder($entity);
		$formBuilder->add("titre", TextType::class) ;
		$formBuilder->add("auteur", TextType::class) ;
		$formBuilder->add("prix", NumberType::class) ;
		$formBuilder->add("disponibilite", IntegerType::class) ;
		$formBuilder->add("image", TextType::class) ;
		$formBuilder->add("ISBN", TextType::class) ;
		$formBuilder->add("nbPages", IntegerType::class) ;
		$formBuilder->add("dateDeParution", TextType::class) ;
		$formBuilder->add("valider", SubmitType::class) ;
		// Generate form
		$form = $formBuilder->getForm();
		
		$form->handleRequest($request) ;
		
		if ($form->isSubmitted()) {
			$entity = $form->getData() ;
			$entity->setId(hexdec(uniqid()));
			$this->entityManager->persist($entity);
			$this->entityManager->flush();
			return $this->redirectToRoute("adminLivres") ;
		}
		else {
			return $this->render('admin.form.html.twig', [
				'form' => $form->createView(),
			]);
		}
    }
	
    #[Route('/admin/musiques/ajouter', name: 'adminMusiquesAjouter')]
    public function adminMusiquesAjouterAction(Request $request): Response
    {
		$entity = new Musique() ;
		$formBuilder = $this->createFormBuilder($entity);
		$formBuilder->add("titre", TextType::class) ;
		$formBuilder->add("artiste", TextType::class) ;
		$formBuilder->add("prix", NumberType::class) ;
		$formBuilder->add("disponibilite", IntegerType::class) ;
		$formBuilder->add("image", TextType::class) ;
		$formBuilder->add("dateDeParution", TextType::class) ;
		$formBuilder->add("valider", SubmitType::class) ;
		// Generate form
		$form = $formBuilder->getForm();
		
		$form->handleRequest($request) ;
		
		if ($form->isSubmitted()) {
			$entity = $form->getData() ;
			$entity->setId(hexdec(uniqid()));
			$this->entityManager->persist($entity);
			$this->entityManager->flush();
			return $this->redirectToRoute("adminMusiques") ;
		}
		else {
			return $this->render('admin.form.html.twig', [
				'form' => $form->createView(),
			]);
		}
    }
	#[Route('/admin/jeux/ajouter', name: 'adminJeuxAjouter')]
    public function adminJeuxAjouterAction(Request $request): Response
    {
		$entity = new VideoGame() ;
		$formBuilder = $this->createFormBuilder($entity);
		$formBuilder->add("titre", TextType::class) ;
		$formBuilder->add("prix", NumberType::class) ;
		$formBuilder->add("disponibilite", IntegerType::class) ;
		$formBuilder->add("image", TextType::class) ;
		$formBuilder->add("valider", SubmitType::class) ;
		
		$form = $formBuilder->getForm();
		
		$form->handleRequest($request) ;
		
		if ($form->isSubmitted()) {
			$entity = $form->getData() ;
			$entity->setId(hexdec(uniqid()));
			$this->entityManager->persist($entity);
			$this->entityManager->flush();
			return $this->redirectToRoute("adminJeux") ;
		}
		else {
			return $this->render('admin.form.html.twig', [
				'form' => $form->createView(),
			]);
		}
    }
	#[Route('/admin/jeux/modifier', name: 'adminJeuxModifier')]
    public function adminJeuxModifierAction(Request $request): Response
    {
		$entity = $this->entityManager->getReference("App\Entity\Catalogue\VideoGame", $request->query->get("id"));
		if ($entity === null) 
			$entity = $this->entityManager->getReference("App\Entity\Catalogue\VideoGame", $request->request->get("id"));
		if ($entity !== null) {
			$formBuilder = $this->createFormBuilder($entity);
			$formBuilder->add("id", HiddenType::class) ;
			$formBuilder->add("titre", TextType::class) ;
			$formBuilder->add("prix", NumberType::class) ;
			$formBuilder->add("disponibilite", IntegerType::class) ;
			$formBuilder->add("image", TextType::class) ;
			$formBuilder->add("valider", SubmitType::class) ;
			// Generate form
			$form = $formBuilder->getForm();
			
			$form->handleRequest($request) ;
			
			if ($form->isSubmitted()) {
				$entity = $form->getData() ;
				$this->entityManager->persist($entity);
				$this->entityManager->flush();
				return $this->redirectToRoute("adminJeux") ;
			}
			else {
				return $this->render('admin.form.html.twig', [
					'form' => $form->createView(),
				]);
			}
		}
		else {
			return $this->redirectToRoute("adminLivres") ;
		}
    }

    #[Route('/admin/livres/modifier', name: 'adminLivresModifier')]
    public function adminLivresModifierAction(Request $request): Response
    {
		$entity = $this->entityManager->getReference("App\Entity\Catalogue\Livre", $request->query->get("id"));
		if ($entity === null) 
			$entity = $this->entityManager->getReference("App\Entity\Catalogue\Livre", $request->request->get("id"));
		if ($entity !== null) {
			$formBuilder = $this->createFormBuilder($entity);
			$formBuilder->add("id", HiddenType::class) ;
			$formBuilder->add("titre", TextType::class) ;
			$formBuilder->add("auteur", TextType::class) ;
			$formBuilder->add("prix", NumberType::class) ;
			$formBuilder->add("disponibilite", IntegerType::class) ;
			$formBuilder->add("image", TextType::class) ;
			$formBuilder->add("ISBN", TextType::class) ;
			$formBuilder->add("nbPages", IntegerType::class) ;
			$formBuilder->add("dateDeParution", TextType::class) ;
			$formBuilder->add("valider", SubmitType::class) ;
			// Generate form
			$form = $formBuilder->getForm();
			
			$form->handleRequest($request) ;
			
			if ($form->isSubmitted()) {
				$entity = $form->getData() ;
				$this->entityManager->persist($entity);
				$this->entityManager->flush();
				return $this->redirectToRoute("adminLivres") ;
			}
			else {
				return $this->render('admin.form.html.twig', [
					'form' => $form->createView(),
				]);
			}
		}
		else {
			return $this->redirectToRoute("adminLivres") ;
		}
    }
	
    #[Route('/admin/musiques/modifier', name: 'adminMusiquesModifier')]
    public function adminMusiquesModifierAction(Request $request): Response
    {
		$entity = $this->entityManager->getReference("App\Entity\Catalogue\Musique", $request->query->get("id"));
		if ($entity === null) 
			$entity = $this->entityManager->getReference("App\Entity\Catalogue\Musique", $request->request->get("id"));
		if ($entity !== null) {
			$formBuilder = $this->createFormBuilder($entity);
			$formBuilder->add("id", HiddenType::class) ;
			$formBuilder->add("titre", TextType::class) ;
			$formBuilder->add("artiste", TextType::class) ;
			$formBuilder->add("prix", NumberType::class) ;
			$formBuilder->add("disponibilite", IntegerType::class) ;
			$formBuilder->add("image", TextType::class) ;
			$formBuilder->add("dateDeParution", TextType::class) ;
			$formBuilder->add("valider", SubmitType::class) ;
			// Generate form
			$form = $formBuilder->getForm();
			
			$form->handleRequest($request) ;
			
			if ($form->isSubmitted()) {
				$entity = $form->getData() ;
				$this->entityManager->persist($entity);
				$this->entityManager->flush();
				return $this->redirectToRoute("adminMusiques") ;
			}
			else {
				return $this->render('admin.form.html.twig', [
					'form' => $form->createView(),
				]);
			}
		}
		else {
			return $this->redirectToRoute("adminMusiques") ;
		}
    }
	#[Route('/admin', name: 'adminDashboard')]
public function adminDashboardAction(): Response
{
    return $this->render('admin.html.twig');
}
#[Route('/admin/commandes', name: 'liste_commandes')]
public function afficherCommandes(EntityManagerInterface $em): Response
{
	$commandes = $em->getRepository(Commande::class)->findAll();

	return $this->render('admin.liste.commandes.html.twig', [
		'commandes' => $commandes
	]);
}
}
