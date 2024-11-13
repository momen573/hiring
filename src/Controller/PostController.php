<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Type;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\CategoryInputType;
use App\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Repository\ApprovalRepository;
use App\Pagination\Paginator;

#[Route('admin/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'post_index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        ApprovalRepository $approvalRepository,
        Request $request
    ): Response {
        $queryBuilder = $postRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC');

        $paginator = new Paginator($queryBuilder, 10);
        $page = $request->query->getInt('page', 1);
        $paginator->paginate($page);

        $approvals = $approvalRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $paginator->getResults(),
            'approvals' => $approvals,
            'currentPage' => $paginator->getCurrentPage(),
            'lastPage' => $paginator->getLastPage(),
        ]);
    }

    #[Route('/new', name: 'post_new', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        TypeRepository $typeRepository,
        CategoryRepository $categoryRepository,
        Security $security
    ): Response {
        $post = new Post();
        $user = $security->getUser();
        $post->setUser($user);

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('publishedAt', DateTimePickerType::class, [
                'data' => new \DateTimeImmutable(),
            ])
            ->add('types', EntityType::class, [
                'class' => Type::class,
                'choices' => $typeRepository->findAll(),
                'multiple' => true,
                'expanded' => false,
                'choice_label' => 'name',
            ])
            ->add('categories', CategoryInputType::class, [
                'class' => Category::class,
                'choices' => $categoryRepository->findAll(),
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            foreach ($form->get('categories')->getData() as $category) {
                $post->addCategory($category);
            }

            foreach ($form->get('types')->getData() as $type) {
                $post->addType($type);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



}
