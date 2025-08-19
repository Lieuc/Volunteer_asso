<?php

namespace App\Controller;

use App\Entity\Association;
use App\Entity\Mission;
use App\Form\AssociationAddType;
use App\Form\AssociationEditType;
use App\Form\AssociationSearch;
use App\Form\MissionAddType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AssociationController extends AbstractController
{
    private string $postApi = 'http://localhost:5088/api/Rna/check';
    private $http;

    public function __construct()
    {
        $this->http = HttpClient::create();
    }

    #[Route('/association', name: 'app_association')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $associationToAdd = new Association();
        $addForm = $this->createForm(AssociationAddType::class, $associationToAdd, [
            'method' => 'POST',
        ]);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $associationToAdd->setOwner($user);

            // Vérification du RNA via API
            $rna = $associationToAdd->getRna();
            try {
                $response = $this->http->request('POST', $this->postApi, [
                    'json' => ['rnaSequence' => $rna],
                ]);
                $data = $response->toArray();

                if (isset($data['identite']['active']) && $data['identite']['active'] === true) {
                    $associationToAdd->setIsValidated(true);
                } else {
                    $associationToAdd->setIsValidated(false);
                }
            } catch (\Exception $e) {
                $associationToAdd->setIsValidated(false);
            }

            // Upload image
            $imageFile = $addForm->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('associations_dir'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
                $associationToAdd->setLogoUrl('/associations/'.$newFilename);
            }

            $em->persist($associationToAdd);
            $em->flush();

            $this->addFlash('success', 'Association ajoutée avec succès.');
            return $this->redirectToRoute('app_association');
        }

        $searchForm = $this->createForm(AssociationSearch::class, null, [
            'method' => 'GET',
        ]);
        $searchForm->handleRequest($request);

        $qb = $em->createQueryBuilder()
            ->select('a')
            ->from(Association::class, 'a')
            ->where('a.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('a.name', 'ASC');

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
            'form'           => $searchForm->createView(),
            'associations'   => $associations,
        ]);
    }

    #[Route('/association/page/{id}', name: 'app_association_page')]
    public function page(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $association = $em->getRepository(Association::class)->find($id);
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée.');
        }

        $user = $this->getUser();
        $isOwner = ($user && $user === $association->getOwner());

        $mission = new Mission();
        $form = $this->createForm(MissionAddType::class, $mission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mission->setAssociation($association);
            $em->persist($mission);
            $em->flush();
            $this->addFlash('success', 'Mission ajoutée avec succès !');
            return $this->redirectToRoute('app_association_page', ['id' => $association->getId()]);
        }

        $editFormView = null;
        if ($isOwner) {
            $editForm = $this->createForm(AssociationEditType::class, $association, [
                'action' => $this->generateUrl('app_association_page', ['id' => $association->getId()]),
                'method' => 'POST',
            ]);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid() && !$form->isSubmitted()) {
                // Vérification RNA si modifié
                $rna = $association->getRnaNumber();
                try {
                    $response = $this->http->request('POST', $this->postApi, [
                        'json' => ['rnaSequence' => $rna],
                    ]);
                    $data = $response->toArray();

                    if (isset($data['identite']['active']) && $data['identite']['active'] === true) {
                        $association->setIsValidated(true);
                    } else {
                        $association->setIsValidated(false);
                    }
                } catch (\Exception $e) {
                    $association->setIsValidated(false);
                }

                $em->flush();
                $this->addFlash('success', 'Association mise à jour.');
                return $this->redirectToRoute('app_association_page', ['id' => $association->getId()]);
            }

            $editFormView = $editForm->createView();
        }

        $missions = $association->getMissions();

        return $this->render('association/associationPage.html.twig', [
            'association' => $association,
            'isOwner' => $isOwner,
            'missions' => $missions,
            'form' => $form->createView(),
            'editForm' => $editFormView,
        ]);
    }
}
