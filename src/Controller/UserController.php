<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileImageType;
use App\Form\UserSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserSearchType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $users = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('searchTerm')->getData();

            if ($search) {
                // Recherche par email, prénom ou nom
                $qb = $em->createQueryBuilder();
                $qb->select('u')
                    ->from(User::class, 'u')
                    ->where($qb->expr()->orX(
                        $qb->expr()->like('u.email', ':search'),
                        $qb->expr()->like('u.firstName', ':search'),
                        $qb->expr()->like('u.lastName', ':search')
                    ))
                    ->setParameter('search', '%'.$search.'%');

                $users = $qb->getQuery()->getResult();
            }
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }


    #[Route('/user/image', name: 'app_user_add_image')]
    public function editProfileImage(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre photo de profil.');
        }

        $form = $this->createForm(ProfileImageType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('profileImageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('profile_dir'), // paramètre à définir dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }

                $user->setAvatarUrl('/uploads/' . $newFilename);
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Votre image de profil a été mise à jour.');
                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('profiles/edit_image.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
