<?php
/**
 * Task service.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Category;
use App\Repository\TaskRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class TaskService.
 */
class TaskService implements TaskServiceInterface
{
    /**
     * Task repository.
     */
    private TaskRepository $taskRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param TaskRepository     $taskRepository Task repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(TaskRepository $taskRepository, PaginatorInterface $paginator)
    {
        $this->taskRepository = $taskRepository;
        $this->paginator = $paginator;
    }

   /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->taskRepository->queryAll(),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

   /**
     * Get paginated list by author.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterfaceByAuthor<string, mixed> Paginated list
     */
    public function getPaginatedListByAuthor(int $page, User $author): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->taskRepository->queryByAuthor($author),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list by Category.
     *
     * @param int  $page   Page number
     * @param Category $category Category
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface {

        //dd($this->taskRepository->findBy(["category" => $category]));
        
        return $this->paginator->paginate(
            $this->taskRepository->queryByCategory($category),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list by Tag.
     *
     * @param int  $page   Page number
     * @param Tag $tag Tag
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByTag(int $page, Tag $tag): PaginationInterface {

        //dd($this->taskRepository->findBy(["tag" => $tag]));
        
        return $this->paginator->paginate(
            $this->taskRepository->queryByTag($tag),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function getOne(int $id)
    {
        return $this->categoryRepository->findOneById($id);
    }

    /**
     * Save entity.
     *
     * @param Task $category Category entity
     */
    public function save(Task $task): void
    {
        $this->taskRepository->save($task);
    }

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     */
    public function delete(Task $task): void
    {
        $this->taskRepository->delete($task);
    }
    

}
