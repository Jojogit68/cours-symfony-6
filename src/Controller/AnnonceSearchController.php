<?php

namespace App\Controller;

use App\Entity\AnnonceSearch;
use App\Form\AnnonceSearchType;
use App\Repository\AnnonceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceSearchController extends AbstractController
{
    #[Route('/annonce/search', name: 'app_annonce_search', methods: ['GET'])]
    public function index(Request $request, AnnonceRepository $annonceRepository, PaginatorInterface $paginator)
    {
        $annonceSearch = new AnnonceSearch();
        $form = $this->createForm(AnnonceSearchType::class, $annonceSearch);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on fera le traitement plus tard
            $annonces = $paginator->paginate(
                $annonceRepository->findByAnnonceSearchQuery($annonceSearch),
                $request->query->getInt('page', 1),
                12
            );
            dump($annonces);
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);


        }

        return $this->render('annonce_search/_search-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}