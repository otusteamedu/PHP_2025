<?php
declare(strict_types=1);

class User
{
    private ?int $id;

    private string $name;

    private string $email;

    private \DateTimeImmutable $createdAt;

    /**
     * @param int|null $id
     * @param string $name
     * @param string $email
     * @param DateTimeImmutable $createdAt
     */
    public function __construct(
        ?int $id,
        string $name,
        string $email,
        \DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function assignId(int $id): void
    {
        if ($this->id !== null) {
            throw new LogicException('ID already assigned');
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
