<?php

namespace App\Controller;

use App\Entity\Association;
use App\Entity\Mission;
use App\Form\AssociationAddType;
use App\Form\AssociationSearch;
use App\Form\MissionAddType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AssociationController extends AbstractController
{
    #[Route('/association', name: 'app_association')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        // --- Formulaire d'ajout d'association (POST) ---
        $associationToAdd = new Association();
        $addForm = $this->createForm(AssociationAddType::class, $associationToAdd, [
            'method' => 'POST',
        ]);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            // Propriétaire
            $associationToAdd->setOwner($user);

            // Upload image (champ non mappé 'imageFile')
            $imageFile = $addForm->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('associations_dir'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                    // on peut aussi logger l’erreur
                }
                // suppose que l’entité Association possède un champ string imageUrl
                $associationToAdd->setLogoUrl('/associations/'.$newFilename);
            }

            $em->persist($associationToAdd);
            $em->flush();

            $this->addFlash('success', 'Association ajoutée avec succès.');
            return $this->redirectToRoute('app_association');
        }

        // --- Formulaire de recherche (GET) ---
        $searchForm = $this->createForm(AssociationSearch::class, null, [
            'method' => 'GET',
        ]);
        $searchForm->handleRequest($request);

        // Query de base : toutes les associations de l’utilisateur connecté
        $qb = $em->createQueryBuilder()
            ->select('a')
            ->from(Association::class, 'a')
            ->where('a.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('a.name', 'ASC');

        // Si recherche remplie, on filtre par nom (LIKE)
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->get('searchTerm')->getData();
            if ($search) {
                $qb->andWhere('a.name LIKE :search')
                    ->setParameter('search', '%'.$search.'%');
            }
        }

        $associations = $qb->getQuery()->getResult();

        return $this->render('association/index.html.twig', [
            'controller_name' => 'AssociationController',
            'addForm'        => $addForm->createView(),
            'form'           => $searchForm->createView(), // ton form de recherche existant
            'associations'   => $associations,
        ]);
    }


    #[Route('/association/page/{id}', name: 'app_association_page')]
    public function page(Request $request, EntityManagerInterface $em, int $id): Response
    {
        // Récupération de l'association
        $association = $em->getRepository(Association::class)->find($id);
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée.');
        }

        $user = $this->getUser();
        $isOwner = ($user && $user === $association->getOwner());

        // Création du formulaire MissionAddType
        $mission = new Mission();
        $form = $this->createForm(MissionAddType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Lier la mission à l'association
            $mission->setAssociation($association);

            $em->persist($mission);
            $em->flush();

            $this->addFlash('success', 'Mission ajoutée avec succès !');
            return $this->redirectToRoute('app_association_page', ['id' => $association->getId()]);
        }

        // Récupération des missions de l'association
        $missions = $association->getMissions();

        return $this->render('association/associationPage.html.twig', [
            'controller_name' => 'AssociationController',
            'association'     => $association,
            'isOwner'         => $isOwner,
            'missions'        => $missions,
            'form'            => $form->createView(), // ✅ On envoie le formulaire à Twig
        ]);
    }

}
