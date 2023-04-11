<?php

namespace App\Controller\Ajax;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/ajax')]
class AnnonceController extends AbstractController
{
    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository, UrlGeneratorInterface $urlGenerator): Response
    {
        $annonces = $annonceRepository->findAllNotSold();
        $data = [];

        foreach ($annonces as $property => $annonce) {
            $data[] = [
                'id' => $annonce->getId(),
                'title' => $annonce->getTitle(),
                'description' => $annonce->getDescription(),
                'price' => $annonce->getPrice(),
                'status' => $annonce->getStatus(),
                'createdAt' => $annonce->getCreatedAt(),
                'updatedAt' => $annonce->getCreatedAt(),
                'slug' => $annonce->getStatus(),
                'imageUrl' => $annonce->getImageUrl(),
                'street' => $annonce->getStreet(),
                'postcode' => $annonce->getPostcode(),
                'city' => $annonce->getCity(),
                'lat' => $annonce->getLat(),
                'lng' => $annonce->getLng(),
                'link' => $urlGenerator->generate('app_annonce_show', ['id' => $annonce->getId(), 'slug' => $annonce->getSlug()])
            ];
        }
        // héhé ! Tu as vu cette petite filouterie ?
        // Symfony nous met à disposition cette fonction qui renvoie une réponse JSON et qui applique json_encode !
        return $this->json($data); 
    }
}