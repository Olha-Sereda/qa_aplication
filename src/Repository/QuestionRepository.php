<?php
/**
 * Question repository.
 */

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Question;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class QuestionRepository.
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in configuration files.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial question.{id, createdAt, updatedAt, title}',
                'partial category.{id, title}'
            )
            ->join('question.category', 'category')
            ->orderBy('question.updatedAt', 'DESC');
    }

    /**
     * Count questions by category.
     *
     * @param Category $category Category
     *
     * @return int Number of questions in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('question.id'))
            ->where('question.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save entity.
     *
     * @param Question $question Question entity
     */
    public function save(Question $question): void
    {
        $this->_em->persist($question);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Question $question Question entity
     */
    public function delete(Question $question): void
    {
        $this->_em->remove($question);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('question');
    }

    /**
     * Query questions by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('question.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }


    /**
     * Query questions by Category.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByCategory(Category $category): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('question.category = :category')
            ->setParameter('category', $category);

        return $queryBuilder;
    }
    
    /**
     * Query questions by Tag.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByTag(Tag $tag): QueryBuilder
    {

        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial question.{id, createdAt, updatedAt, title}',
                'partial tag.{id, title}'
            )
            ->join('question.tags', 'tag')
            ->orderBy('question.updatedAt', 'DESC');

        // return $this->createQueryBuilder('c')
        //     ->innerJoin('c.tags', 's', 'WITH', 's.id = :tag')
        //     ->setParameter('tag', $tag);

    }
    
}
