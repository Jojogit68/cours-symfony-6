<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/annonce')]
    public function index(): Response
    {
        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index'
        ]);
    }
}
