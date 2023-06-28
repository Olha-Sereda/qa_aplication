<?php
/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class CommentService.
 */
class CommentService
{
    /**
     * Comment repository.
     *
     * @var \App\Repository\CommentRepository
     */
    private CommentRepository $commentRepository;

    /**
     * Paginator.
     *
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * Entity manager.
     *
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * CommentService constructor.
     *
     * @param \App\Repository\CommentRepository       $commentRepository Comment repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator         Paginator
     */
    public function __construct(CommentRepository $commentRepository, PaginatorInterface $paginator , EntityManagerInterface $entityManager)
    {
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
    }


    /**
     * Save comment.
     *
     * @param \App\Entity\Comment $comment Comment entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Delete comment.
     *
     * @param \App\Entity\Comment $comment Comment entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    /**
     * Find comment by task with sorting
     * @param array $task
     * @param array<string, string>|null $orderBy
     * 
     * @return Comment[]
     */
    public function findBy(array $task, ?array $orderBy = null): array
    {
        return $this->commentRepository->findBy($task, $orderBy);
    }

    public function saveBestAnswer(Comment $comment)
    {

        //dd($comment);
        $result = $this->findBy([ "task" =>  $comment->getTask()]);
        foreach( $result as $item)
        {
            $item->setBestAnswer(null);
            $this->commentRepository->save($item,false);
        }
        $comment->setBestAnswer(true);
        $this->commentRepository->save($comment,true);
     

 
    }
}