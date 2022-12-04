<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAllNotSold();

        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index',
            'annonces' => $annonces
        ]);
    }

    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine): Response
    {
        $annonce = new Annonce();
        $annonce
            ->setTitle('Ma collection de canards')
            ->setDescription('Vends car plus d\'utilité')
            ->setPrice(10)
            ->setStatus(Annonce::STATUS_BAD)
            ->setIsSold(false)
        ;

        // On récupère l'EntityManager
        $em = $doctrine->getManager();
        // On « persiste » l'entité
        $em->persist($annonce);
        // On envoie tout ce qui a été persisté avant en base de données
        $em->flush();

        return new Response('annonce bien créée');
    }

    #[Route('/annonce/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}
