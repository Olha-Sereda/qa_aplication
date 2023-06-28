<?php
/**
 * Task service interface.
 */

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TaskServiceInterface.
 */
interface TaskServiceInterface
{

        /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list by Author.
     *
     * @param int  $page   Page number
     * @param User $author Author
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByAuthor(int $page, User $author): PaginationInterface;

    /**
     * Get paginated list by Category.
     *
     * @param int  $page   Page number
     * @param Category $category Category
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     */
    public function save(Task $task): void;

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     */
    public function delete(Task $task): void;
}