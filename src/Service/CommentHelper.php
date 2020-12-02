<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\CommentRepository;

class CommentHelper
{
    /**
     * @var CommentRepository
     */
    private $repository;

    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function countRecentCommentsForUser(User $user): int
    {
        return $this->repository->countForUser(
            $user,
            new \DateTimeImmutable('-3 months')
        );
    }
}
