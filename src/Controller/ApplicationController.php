<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Association;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, RedirectResponse};
use Symfony\Component\Routing\Attribute\Route;

final class ApplicationController extends AbstractController
{
    #[Route('/association/{id}/applications', name: 'app_association_applications')]
    public function index(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $association = $em->getRepository(Association::class)->find($id);
        if (!$association) {
            throw $this->createNotFoundException('Association introuvable.');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($association->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // ?status=pending|accepted|rejected (par défaut: pending)
        $status = strtolower($request->query->get('status', 'pending'));

        $qb = $em->createQueryBuilder()
            ->select('app', 'm', 'u')
            ->from(Application::class, 'app')
            ->join('app.mission', 'm')
            ->join('app.user', 'u')
            ->where('m.association = :assoc')
            ->setParameter('assoc', $association)
            ->orderBy('app.createdAt', 'DESC');

        // filtre isAccepted
        if ($status === 'accepted') {
            $qb->andWhere('app.isAccepted = :acc')->setParameter('acc', true);
        } elseif ($status === 'rejected') {
            $qb->andWhere('app.isAccepted = :acc')->setParameter('acc', false);
        } else {
            // pending
            $qb->andWhere('app.isAccepted IS NULL');
        }

        $applications = $qb->getQuery()->getResult();

        return $this->render('application/index.html.twig', [
            'association' => $association,
            'applications' => $applications,
            'status' => strtoupper($status),
        ]);
    }

    #[Route('/application/{id}/accept', name: 'app_application_accept', methods: ['POST'])]
    public function accept(int $id, EntityManagerInterface $em, Request $request): RedirectResponse
    {
        $app = $em->getRepository(Application::class)->find($id);
        if (!$app) {
            $this->addFlash('error', 'Candidature introuvable.');
            return $this->redirectToRoute('app_mission');
        }

        $association = $app->getMission()->getAssociation();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($association->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if (!$this->isCsrfTokenValid('accept_app_'.$app->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_association_applications', ['id' => $association->getId()]);
        }

        $app->setIsAccepted(true);
        $em->flush();

        $this->addFlash('success', 'Candidature acceptée.');
        return $this->redirectToRoute('app_association_applications', ['id' => $association->getId(), 'status' => 'accepted']);
    }

    #[Route('/application/{id}/reject', name: 'app_application_reject', methods: ['POST'])]
    public function reject(int $id, EntityManagerInterface $em, Request $request): RedirectResponse
    {
        $app = $em->getRepository(Application::class)->find($id);
        if (!$app) {
            $this->addFlash('error', 'Candidature introuvable.');
            return $this->redirectToRoute('app_mission');
        }

        $association = $app->getMission()->getAssociation();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($association->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if (!$this->isCsrfTokenValid('reject_app_'.$app->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_association_applications', ['id' => $association->getId()]);
        }

        $app->setIsAccepted(false);
        $em->flush();

        $this->addFlash('success', 'Candidature refusée.');
        return $this->redirectToRoute('app_association_applications', ['id' => $association->getId(), 'status' => 'rejected']);
    }
}
