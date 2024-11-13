<?php

namespace App\Controller;

use App\Entity\Type as PostType;
use App\Form\PostTypeType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/post_type')]
class PostTypeController extends AbstractController
{
    #[Route('/', name: 'app_post_type_index', methods: ['GET'])]
    public function index(TypeRepository $postTypeRepository): Response
    {
        return $this->render('post_type/index.html.twig', [
            'postTypes' => $postTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $postType = new PostType();
        $form = $this->createForm(PostTypeType::class, $postType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($postType);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_type/new.html.twig', [
            'post_type' => $postType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_type_show', methods: ['GET'])]
    public function show(PostType $postType): Response
    {
        return $this->render('post_type/show.html.twig', [
            'post_type' => $postType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PostType $postType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostTypeType::class, $postType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_type/edit2.html.twig', [
            'post_type' => $postType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_type_delete', methods: ['POST'])]
    public function delete(Request $request, PostType $postType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $postType->getId(), $request->request->get('_token'))) {
            $postType = $entityManager->find(PostType::class, $postType->getId());

            $entityManager->remove($postType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_type_index', [], Response::HTTP_SEE_OTHER);
    }

}
