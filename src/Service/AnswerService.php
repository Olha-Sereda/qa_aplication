<?php
/**
 * Answer service.
 */

namespace App\Service;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class AnswerService.
 */
class AnswerService
{
    /**
     * Answer repository.
     *
     * @var \App\Repository\AnswerRepository
     */
    private AnswerRepository $answerRepository;

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
     * AnswerService constructor.
     *
     * @param \App\Repository\AnswerRepository       $answerRepository Answer repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator         Paginator
     */
    public function __construct(AnswerRepository $answerRepository, PaginatorInterface $paginator , EntityManagerInterface $entityManager)
    {
        $this->answerRepository = $answerRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
    }


    /**
     * Save answer.
     *
     * @param \App\Entity\Answer $answer Answer entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Answer $answer): void
    {
        $this->answerRepository->save($answer);
    }

    /**
     * Delete answer.
     *
     * @param \App\Entity\Answer $answer Answer entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Answer $answer): void
    {
        $this->answerRepository->delete($answer);
    }

    /**
     * Find answer by question with sorting
     * @param array $question
     * @param array<string, string>|null $orderBy
     * 
     * @return Answer[]
     */
    public function findBy(array $question, ?array $orderBy = null): array
    {
        return $this->answerRepository->findBy($question, $orderBy);
    }

    public function saveBestAnswer(Answer $answer)
    {

        //dd($answer);
        $result = $this->findBy([ "question" =>  $answer->getQuestion()]);
        foreach( $result as $item)
        {
            $item->setBestAnswer(null);
            $this->answerRepository->save($item,false);
        }
        $answer->setBestAnswer(true);
        $this->answerRepository->save($answer,true);
     

 
    }
}