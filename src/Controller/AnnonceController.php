<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $annonces = $paginator->paginate(
            $annonceRepository->findAllNotSoldQuery(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index',
            'annonces' => $annonces
        ]);
    }

    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($annonce);
            $em->flush();
            $this->addFlash('success', 'Annonce créée avec succès');
            return $this->redirectToRoute('app_annonce_index');
        }

        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }

    #[Route('/annonce/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Annonce modifiée avec succès');
            return $this->redirectToRoute('app_annonce_index');
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'formView' => $form->createView()
        ]);
    }

    #[Route('/annonce/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Annonce $annonce, EntityManagerInterface $em, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->get('_token'))) {
            $this->addFlash('success', 'Annonce supprimée avec succès');
            $em->remove($annonce);
            $em->flush();
        }

        return $this->redirectToRoute('app_annonce_index');
    }
}
