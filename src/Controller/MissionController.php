<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MissionController extends AbstractController
{
    #[Route('/mission', name: 'app_mission')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MissionSearchType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $missions = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('searchTerm')->getData();

            if ($search) {
                // Recherche par email, prénom ou nom
                $qb = $em->createQueryBuilder();
                $qb->select('u')
                    ->from(Mission::class, 'u')
                    ->where($qb->expr()->orX(
                        $qb->expr()->like('u.name', ':search'),
                        $qb->expr()->like('u.description', ':search'),
                    ))
                    ->setParameter('search', '%'.$search.'%');

                $missions = $qb->getQuery()->getResult();
            }
        }


        return $this->render('mission/index.html.twig', [
            'controller_name' => 'MissionController',
            'form' => $form->createView(),
            'missions' => $missions,
        ]);
    }
}
