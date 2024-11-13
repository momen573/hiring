<?php

namespace App\Controller;

use App\Repository\ApprovalRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class FrontPageController extends AbstractController
{
    #[Route('/api/posts', name: 'api_frontpage', methods: ['GET'])]
    public function app(PostRepository $postRepository, ApprovalRepository $approvalRepository, SerializerInterface $serializer): JsonResponse
    {
        $posts = $postRepository->findBy([], ['id' => 'DESC']);
        $acceptedPosts = [];

        foreach ($posts as $post) {
            $latestApproval = $approvalRepository->findOneBy(
                ['post' => $post],
                ['id' => 'DESC']
            );

            if ($latestApproval && $latestApproval->getChangeTo() === 'accepted') {
                $acceptedPosts[] = $post;
            }
        }

        $jsonPosts = $serializer->serialize($acceptedPosts, 'json', ['groups' => 'post:read']);
        return new JsonResponse($jsonPosts, JsonResponse::HTTP_OK, [], true);
    }
    #[Route('/api/cat/{categoryId}', name: 'posts_by_category', methods: ['GET'])]
    public function cat_app(
        PostRepository $postRepository,
        ApprovalRepository $approvalRepository,
        SerializerInterface $serializer,
        int $categoryId
    ): JsonResponse {

        $queryBuilder = $postRepository->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->orderBy('p.id', 'DESC');

        if ($categoryId) {
            $queryBuilder->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        $posts = $queryBuilder->getQuery()->getResult();

        $acceptedPosts = [];

        foreach ($posts as $post) {
            $latestApproval = $approvalRepository->findOneBy(
                ['post' => $post],
                ['id' => 'DESC']
            );

            if ($latestApproval && $latestApproval->getChangeTo() === 'accepted') {
                $acceptedPosts[] = $post;
            }
        }

        $jsonPosts = $serializer->serialize($acceptedPosts, 'json', ['groups' => 'post:read']);
        return new JsonResponse($jsonPosts, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/', name: 'app_frontpage')]
    public function index()
    {
        return $this->render('blog/frontpage.html.twig');
    }
    #[Route('/cat/{id}/{name}', name: 'app_cat')]
    public function category(int $id , string $name)
    {
        return $this->render('blog/cat.html.twig' ,['id' => $id , 'name'=>$name]);
    }
    #[Route('/post/{id}', name: 'post_show')]
    public function show(int $id, PostRepository $postRepository): Response
    {

        $post = $postRepository->find($id);


        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }


        return $this->render('blog/show.html.twig', [
            'post' => $post,
        ]);
    }
}
