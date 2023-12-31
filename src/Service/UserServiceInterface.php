<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\Question;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     * @param int $userid User ID number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByAuthor(int $page, User $user): PaginationInterface;


    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void;
}