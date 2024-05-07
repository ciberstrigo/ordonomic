<?php

namespace Jegulnomic\Entity;

use Jegulnomic\Systems\Database\Attributes\Column;

trait TimestampableTrait
{
    #[Column(name: 'created_at')]
    public readonly \DateTimeInterface $createdAt;

    #[Column(name: 'updated_at')]
    public readonly \DateTimeInterface $updatedAt;

    /**
     * @param \DateTimeInterface $createdAt
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
