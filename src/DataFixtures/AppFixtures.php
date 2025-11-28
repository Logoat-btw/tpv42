<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use GuzzleHttp\Client;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\VideoGame;
use App\Entity\Catalogue\Piste;
use App\Entity\Catalogue\Article;

use Psr\Log\LoggerInterface;

class AppFixtures extends Fixture
{
	protected $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function load(ObjectManager $manager): void
	{
		if (count($manager->getRepository("App\Entity\Catalogue\Article")->findAll()) == 0) {
			$ebay = new Ebay($this->logger);




			$keywords = ['JUL', 'jeux vidéo', 'Chenille', 'jeux vidéo'];
			$ebay->setCategory('CDs');
			$xml0 = simplexml_load_string($ebay->findItemsAdvanced($keywords[0], 10));

			$ebay->setCategory('Video Games');
			$xml1 = simplexml_load_string($ebay->findItemsAdvanced($keywords[1], 30));

			$ebay->setCategory('CDs');
			$xml2 = simplexml_load_string($ebay->findItemsAdvanced($keywords[2], 20));

			$ebay->setCategory('CDs');
			$xml3 = simplexml_load_string($ebay->findItemsAdvanced($keywords[3], 10));

			//Voir les informations qu'on obtiens
			// $xml1 = simplexml_load_string($formattedResponse1);
			// $xml2 = simplexml_load_string($formattedResponse2);
			// file_put_contents("ebayResponse1.xml", $ebay->findItemsAdvanced($keywords[1], 30));
			$xmlList = [$xml1, $xml0, $xml2, $xml3];
			foreach ($xmlList as $xmlCode => $xml) {
				if ($xml !== false) {
					foreach ($xml->children() as $child_1) {
						if ($child_1->getName() === "item") {
							if ($ebay->getParentCategoryIdById($child_1->primaryCategory->categoryId) == $ebay->getParentCategoryIdByName("Video Games")) {
								$entityVideoGame = new VideoGame();
								$entityVideoGame->setId((int) $child_1->itemId);
								$entityVideoGame->setTitre($child_1->title) ;
								// $author = $ebay->getItemSpecific("Author", $child_1->itemId) ;
								$entityVideoGame->setPrix((float) $child_1->sellingStatus->currentPrice);
								$entityVideoGame->setShippingPrice((float) $child_1->shippingInfo->shippingServiceCost);
								$entityVideoGame->setDisponibilite(1);
								$entityVideoGame->setImage($child_1->galleryURL);
								$entityVideoGame->setCategory($child_1->primaryCategory->categoryName);
								$manager->persist($entityVideoGame);
								$manager->flush();
							}
							if ($ebay->getParentCategoryIdById($child_1->primaryCategory->categoryId) == $ebay->getParentCategoryIdByName("Livres")) {
								$entityLivre = new Livre();
								$entityLivre->setId((int) $child_1->itemId);
								$title = $ebay->getItemSpecific("Book Title", $child_1->itemId);
								if ($title == null) $title = $child_1->title;
								$entityLivre->setTitre($title);
								$author = $ebay->getItemSpecific("Author", $child_1->itemId);
								if ($author == null) $author = "";
								$entityLivre->setAuteur($author);
								$entityLivre->setISBN("");
								$entityLivre->setPrix((float) $child_1->sellingStatus->currentPrice);
								$entityLivre->setShippingPrice((float) $child_1->shippingInfo->shippingServiceCost);
								$entityLivre->setDisponibilite(1);
								$entityLivre->setImage($child_1->galleryURL);
								$entityLivre->setCategory($child_1->primaryCategory->categoryName);
								// $entityLivre->setCategory($child_1->primaryCategory->categoryName);
								$manager->persist($entityLivre);
								$manager->flush();
							}
							if ($ebay->getParentCategoryIdById($child_1->primaryCategory->categoryId) == $ebay->getParentCategoryIdByName("CDs")) {
								$entityMusique = new Musique();
								$entityMusique->setId((int) $child_1->itemId);
								$title = $ebay->getItemSpecific("Release Title", $child_1->itemId);
								if ($title == null) $title = $child_1->title;
								$entityMusique->setTitre($title);
								$artist = $ebay->getItemSpecific("Artist", $child_1->itemId);
								if ($artist == null) $artist = "";
								$entityMusique->setArtiste($artist);
								$entityMusique->setDateDeParution("");
								$entityMusique->setPrix((float) $child_1->sellingStatus->currentPrice);
								$entityMusique->setShippingPrice((float) $child_1->shippingInfo->shippingServiceCost);
								$entityMusique->setDisponibilite(1);
								$entityMusique->setImage($child_1->galleryURL);
								$entityMusique->setCategory($child_1->primaryCategory->categoryName);
								// $entityMusique->setCategory($child_1->primaryCategory->categoryName);
								if (!isset($albums)) {
									$spotify = new Spotify($this->logger);
									$albums = $spotify->searchAlbumsByArtist($keywords[$xmlCode]);
								}
								$j = 0;
								$sortir = ($j == count($albums));
								$albumTrouve = false;
								while (!$sortir) {
									$titreSpotify = str_replace(" ", "", mb_strtolower($albums[$j]->name));
									$titreEbay = str_replace(" ", "", mb_strtolower($entityMusique->getTitre()));
									$titreSpotify = str_replace("-", "", $titreSpotify);
									$titreEbay = str_replace("-", "", $titreEbay);
									$albumTrouve = ($titreSpotify == $titreEbay);
									if (mb_strlen($titreEbay) > mb_strlen($titreSpotify))
										$albumTrouve = $albumTrouve || (mb_strpos($titreEbay, $titreSpotify) !== false);
									if (mb_strlen($titreSpotify) > mb_strlen($titreEbay))
										$albumTrouve = $albumTrouve || (mb_strpos($titreSpotify, $titreEbay) !== false);
									$j++;
									$sortir = $albumTrouve || ($j == count($albums));
								}
								if ($albumTrouve) {
									$tracks = $spotify->searchTracksByAlbum($albums[$j - 1]->id);
									foreach ($tracks as $track) {
										$entityPiste = new Piste();
										$entityPiste->setTitre($track->name);
										$entityPiste->setMp3($track->preview_url);
										$manager->persist($entityPiste);
										$manager->flush();
										$entityMusique->addPiste($entityPiste);
									}
								}
								$manager->persist($entityMusique);
								$manager->flush();
							}
						}
					}
				}
			}

			$entityLivre = new Livre();
			$entityLivre->setCategory("Livre");
			$entityLivre->setId(55677821);
			$entityLivre->setTitre("Le seigneur des bagues");
			$entityLivre->setAuteur("J.R.R. TOLKIEN");
			$entityLivre->setISBN("2075134049");
			$entityLivre->setNbPages(736);
			$entityLivre->setDateDeParution("03/10/19");
			$entityLivre->setPrix(8.90);
			$entityLivre->setShippingPrice(0);
			$entityLivre->setDisponibilite(1);
			$entityLivre->setImage("images/51O0yBHs+OL._SL140_.jpg");
			$manager->persist($entityLivre);

			$entityLivre = new Livre();
			$entityLivre->setCategory("Livre");
			$entityLivre->setId(55897821);
			$entityLivre->setTitre("Un enfer trompeur");
			$entityLivre->setAuteur("Henning Mankell");
			$entityLivre->setISBN("275784797X");
			$entityLivre->setNbPages(400);
			$entityLivre->setDateDeParution("09/10/14");
			$entityLivre->setPrix("6.80");
			$entityLivre->setShippingPrice(0);
			$entityLivre->setDisponibilite(1);
			$entityLivre->setImage("images/71uwoF4hncL._SL140_.jpg");
			$entityLivre->setPrice($child_1->sellingStatus->convertedCurrentPrice);
			$manager->persist($entityLivre);

			$entityLivre = new Livre();
			$entityLivre->setCategory("Livre");
			$entityLivre->setId(56299459);
			$entityLivre->setTitre("¨Cercle tome 1");
			$entityLivre->setAuteur("Stephen King");
			$entityLivre->setISBN("2212110685");
			$entityLivre->setNbPages(840);
			$entityLivre->setDateDeParution("06/03/13");
			$entityLivre->setPrix("8.90");
			$entityLivre->setShippingPrice(0);
			$entityLivre->setDisponibilite(1);
			$entityLivre->setImage("images/719FffADQAL._SL140_.jpg");
			$manager->persist($entityLivre);

			$entityLivre = new Livre();
			$entityLivre->setCategory("Livre");
			$entityLivre->setId(56299460);
			$entityLivre->setTitre("Cercle tome 2");
			$entityLivre->setAuteur("Stephen King 2");
			$entityLivre->setISBN("2212110685");
			$entityLivre->setNbPages(840);
			$entityLivre->setDateDeParution("06/03/13");
			$entityLivre->setPrix("8.90");
			$entityLivre->setShippingPrice(0);
			$entityLivre->setDisponibilite(1);
			$entityLivre->setImage("images/719FffADQAL._SL140_.jpg");
			$manager->persist($entityLivre);
			$manager->flush();
		}
	}
}
