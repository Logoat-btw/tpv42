<?php
namespace App\Controller;

use App\Entity\Panier\Commande;
use App\Form\CommandeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Panier\Panier;


class CommandeController extends AbstractController
{
    #[Route('/commander', name: 'commander')]
    public function commander(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', new Panier());

        if (count($panier->getLignesPanier()) === 0) {
            return $this->redirectToRoute('accederAuPanier');
        }

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Génération des données d’articles du panier
            $articles = [];

            foreach ($panier->getLignesPanier() as $ligne) {
                $articles[] = [
                    'id' => $ligne->getArticle()->getId(),
                    'titre' => $ligne->getArticle()->getTitre(),
                    'prix' => $ligne->getPrixUnitaire(),
                    'quantite' => $ligne->getQuantite(),
                    'total' => $ligne->getPrixTotal()
                ];
            }

            $commande->setArticles($articles);
            $commande->setTotal($panier->getTotal());

            $em->persist($commande);
            $em->flush();

            $session->remove('panier');

            return $this->render('confirmation.html.twig', [
                'commande' => $commande
            ]);
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
            'panier' => $panier
        ]);
    }

   
}
