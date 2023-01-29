<?php

namespace App\Controller\Ajax;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ajax', condition: "request.headers.get('Accept') matches '#application/json#'")]
class AnnonceController extends AbstractController
{
    #[Route('/annonce', methods: ['GET'])]
    public function index(
        AnnonceRepository $annonceRepository,
        Request $request
    ): Response
    {
        $lat = (float)$request->query->get('lat');
        $lng = (float)$request->query->get('lng');
        $distance = (int)$request->query->get('distance');
        $annonces = $annonceRepository->findByDistance($lat, $lng, $distance);
        return $this->json($annonces, Response::HTTP_OK, [], ['groups' => 'annonce:read']);
    }
}
