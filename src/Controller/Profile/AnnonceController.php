<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/profile/annonce', name: 'app_profile_annonce')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('profile/annonce/index.html.twig', [
            'annonces' => $user->getAnnonces()
        ]);
    }
}
