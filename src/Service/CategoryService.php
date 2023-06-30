<?php
/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\QuestionRepository;
use Doctrine\ORM\NoResultException;
use App\Repository\CategoryRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    /**
     * Category repository.
     */
    private CategoryRepository $categoryRepository;

    /**
     * Question repository.
     */
    private QuestionRepository $questionRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param CategoryRepository $repository
     * @param PaginatorInterface $paginator  Paginator
     */
    
    public function __construct(CategoryRepository $categoryRepository,QuestionRepository $questionRepository ,PaginatorInterface $paginator)
    {
        $this->categoryRepository = $categoryRepository;
        $this->paginator = $paginator;
        $this->questionRepository = $questionRepository;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }



    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Can Category be deleted?
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->questionRepository->countByCategory($category);

            return !($result > 0); 
        }catch(NoResultException  | NonUniqueResultException $e) { 
            return false; 
        }
    }
}
