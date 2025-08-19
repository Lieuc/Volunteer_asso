<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Mission;
use App\Form\ApplyType;
use App\Form\MissionSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MissionController extends AbstractController
{
    #[Route('/mission', name: 'app_mission')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MissionSearchType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);

        $qb = $em->createQueryBuilder()
            ->select('m')
            ->from(Mission::class, 'm')
            ->orderBy('m.startAt', 'DESC');

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('searchTerm')->getData();
            $domain = $form->get('domain')->getData();

            if ($search) {
                $qb->andWhere('m.name LIKE :s OR m.description LIKE :s')
                    ->setParameter('s', '%'.$search.'%');
            }
            if ($domain) {
                $qb->join('m.domains', 'd')
                    ->andWhere('d = :domain')
                    ->setParameter('domain', $domain);
            }
        }

        $missions = $qb->getQuery()->getResult();


        $applyForms = [];
        foreach ($missions as $m) {
            $applyForms[$m->getId()] = $this->createForm(ApplyType::class, null, [
                'action' => $this->generateUrl('app_mission_apply', ['id' => $m->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        return $this->render('mission/index.html.twig', [
            'form'       => $form->createView(),
            'missions'   => $missions,
            'applyForms' => $applyForms,
        ]);
    }

    #[Route('/mission/{id}/apply', name: 'app_mission_apply', methods: ['POST'])]
    public function apply(int $id, EntityManagerInterface $em, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('apply_error', 'Vous devez être connecté pour postuler.');
            return $this->redirectToRoute('app_login');
        }

        $mission = $em->getRepository(Mission::class)->find($id);
        if (!$mission) {
            $this->addFlash('apply_error', 'Mission introuvable.');
            return $this->redirectToRoute('app_mission');
        }


        $form = $this->createForm(ApplyType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('apply_error', 'Soumission invalide (CSRF).');
            return $this->redirectToRoute('app_mission');
        }

        // Bloquer toute candidature existante (attente / acceptée / refusée)
        $existing = $em->getRepository(Application::class)->findOneBy([
            'mission' => $mission,
            'user'    => $user,
        ]);

        if ($existing) {
            if ($existing->isAccepted() === null) {
                $this->addFlash('apply_warning', 'Vous avez déjà une candidature en attente pour cette mission.');
            } elseif ($existing->isAccepted() === true) {
                $this->addFlash('apply_info', 'Vous avez déjà été accepté pour cette mission.');
            } else {
                $this->addFlash('apply_warning', 'Votre candidature a été refusée : vous ne pouvez pas repostuler à cette mission.');
            }
            return $this->redirectToRoute('app_mission');
        }

        $application = (new Application())
            ->setMission($mission)
            ->setUser($user)
            ->setCreatedAt(new \DateTimeImmutable());

        $em->persist($application);
        $em->flush();

        $this->addFlash('apply_success', 'Votre candidature a été envoyée !');
        return $this->redirectToRoute('app_mission');
    }

    #[Route('/me/missions/upcoming', name: 'app_user_upcoming_missions')]
    public function myUpcomingMissions(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $now = new \DateTimeImmutable();

        $qb = $em->createQueryBuilder()
            ->select('m')
            ->from(\App\Entity\Mission::class, 'm')
            ->join('m.applications', 'a')
            ->where('a.user = :user')
            ->andWhere('a.isAccepted = true')
            ->andWhere('m.startAt >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->orderBy('m.startAt', 'ASC');

        $missions = $qb->getQuery()->getResult();

        return $this->render('mission/upcoming.html.twig', [
            'missions' => $missions,
        ]);
    }


}
