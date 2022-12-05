<?php

namespace App\Controller\Admin;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AnnonceController extends AbstractController
{
    #[Route('/annonce')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll();
        return $this->render('admin/annonce/index.html.twig', [
            'annonces' => $annonces
        ]);
    }
}
