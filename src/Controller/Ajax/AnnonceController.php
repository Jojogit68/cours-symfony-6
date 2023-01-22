<?php

namespace App\Controller\Ajax;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/ajax')]
//, condition: "request.headers.get('Accept') matches '#application/json#'"
class AnnonceController extends AbstractController
{
    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository, UrlGeneratorInterface $urlGenerator, Request $request): Response
    {
        $lat = (float)$request->query->get('lat');
        $lng = (float)$request->query->get('lng');
        $distance = (int)$request->query->get('distance');
        $annonces = $annonceRepository->findByDistance($lat, $lng, $distance);
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
        return $this->json($data);
    }
}
