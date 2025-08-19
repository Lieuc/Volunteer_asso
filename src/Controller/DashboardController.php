<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Form\ProfileImageType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DashboardController extends AbstractController
{
    private string $postApi = 'http://localhost:8080/post-api/api/posts/';
    private $http;

    public function __construct() {
        $this->http = HttpClient::create();
    }

    #[Route('/', name: 'app_dashboard')]
    public function index(Request $request, LoggerInterface $logger, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $userId = $user?->getId();

        // Récup posts via API
        try {
            $response = $this->http->request('GET', $this->postApi . $userId);
            $posts = $response->toArray();
            $logger->info('Posts API Response', $posts);
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
            $posts = [];
        }

        // Formulaire Post
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour poster.');
            }

            $post->setUser($user);

            // Upload image du post
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('uploads_dir'), $newFilename);
                } catch (FileException $e) {
                    $logger->error($e->getMessage());
                }
                $post->setImageUrl('/uploads/'.$newFilename);
            }

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        // Formulaire Image de profil
        $formImg = $this->createForm(\App\Form\ProfileImageType::class, $user);
        $formImg->handleRequest($request);

        if ($formImg->isSubmitted() && $formImg->isValid()) {
            $imageFile = $formImg->get('profileImageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('uploads_dir'), $newFilename);

                $user->setAvatarUrl('/uploads/'.$newFilename);
                $em->flush();

                $this->addFlash('success', 'Photo de profil mise a jour.');
                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'posts' => $posts,
            'form' => $form->createView(),
            'profileForm' => $formImg->createView(),
        ]);
    }


    #[Route('/post', name: 'app_create_post')]
    public function createPost(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // associer user
            if (!$this->getUser()) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour poster.');
            }
            $post->setUser($this->getUser());

            // uploader image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('uploads_dir'), $newFilename);
                } catch (FileException $e) {

                }
                $post->setImageUrl('/uploads/'.$newFilename);
            }

            $em->persist($post);
            $em->flush();




            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_profile')]
    public function profile(User $user, HttpClientInterface $http): Response
    {
        $response = $http->request('GET', $this->postApi . $user->getId());
        $posts = $response->toArray();

        return $this->render('dashboard/otherUser.html.twig', [
            'userProfile' => $user,
            'posts' => $posts,
        ]);
    }


}
