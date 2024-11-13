<?php

namespace App\Controller;

use App\Entity\Approval;
use App\Entity\Post;
use App\Repository\ApprovalRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApprovalController extends AbstractController
{
    #[Route('/approval/{id}/approve', name: 'approval_approve', methods: ['POST'])]
    public function approve(Post $post, ApprovalRepository $approvalRepository, EntityManagerInterface $entityManager): Response
    {
        $approval = $approvalRepository->findOneBy(['post' => $post, 'user' => $this->getUser()]);
        if (!$approval) {
            $approval = new Approval();
            $approval->setPost($post);
            $approval->setUser($this->getUser());
        }

        $approval->setChangeTo(Approval::STATUS_ACCEPTED);
        $entityManager->persist($approval);
        $entityManager->flush();

        $this->addFlash('success', 'Post has been approved.');

        return $this->redirectToRoute('post_index');
    }

    #[Route('/approval/{id}/draft', name: 'approval_draft', methods: ['POST'])]
    public function draft(Post $post, ApprovalRepository $approvalRepository, EntityManagerInterface $entityManager): Response
    {
        $approval = $approvalRepository->findOneBy(['post' => $post, 'user' => $this->getUser()]);
        if (!$approval) {
            $approval = new Approval();
            $approval->setPost($post);
            $approval->setUser($this->getUser());
        }

        $approval->setChangeTo(Approval::STATUS_DRAFT);
        $entityManager->persist($approval);
        $entityManager->flush();

        $this->addFlash('info', 'Post has been saved as draft.');

        return $this->redirectToRoute('post_index');
    }

    #[Route('/approval/{id}/reject', name: 'approval_reject', methods: ['POST'])]
    public function reject(Post $post, ApprovalRepository $approvalRepository, EntityManagerInterface $entityManager): Response
    {
        $approval = $approvalRepository->findOneBy(['post' => $post, 'user' => $this->getUser()]);
        if (!$approval) {
            $approval = new Approval();
            $approval->setPost($post);
            $approval->setUser($this->getUser());
        }

        $approval->setChangeTo(Approval::STATUS_REJECTED);
        $entityManager->persist($approval);
        $entityManager->flush();

        $this->addFlash('error', 'Post has been rejected.');

        return $this->redirectToRoute('post_index');
    }

    #[Route('/approval/log/{postId}', name: 'approval_log', methods: ['GET'])]
    public function log(int $postId, PostRepository $postRepository, ApprovalRepository $approvalRepository)
    {
        $post = $postRepository->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $approvals = $approvalRepository->findBy(
            ['post' => $post],
            ['changedAt' => 'DESC']
        );

        return $this->render('post/approval_log.html.twig', [
            'post' => $post,
            'approvals' => $approvals,
        ]);
    }
}
