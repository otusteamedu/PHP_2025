<?php

declare(strict_types=1);

namespace Camal\AppDataMapperLocal\Domain\Entities;

class Task
{
    /**
     * @param int $id
     * @param string $title
     * @param string $text
     * @param \Camal\AppDataMapperLocal\Domain\Entities\TaskPriority $priority
     * @param \Camal\AppDataMapperLocal\Domain\Entities\TaskStatus $status
     * @param \Camal\AppDataMapperLocal\Domain\Entities\TaskDateTime $created
     * @param mixed $completed
     * @param mixed $completeBefore
     */
    public function __construct(
        private int $id,
        private string $title,
        private string $text,
        private TaskPriority $priority,
        private TaskStatus $status,
        private ?TaskDateTime $created,
        private ?TaskDateTime $completed = null,
        private ?TaskDateTime $completeBefore = null
    ) {
        if($this->created === null){
            $this->created = new TaskDateTime();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getCompleted()
    {
        return $this->completed;
    }

    public function getCompleteBefore()
    {
        return $this->completeBefore;
    }
    
    public function start(): void
    {
        $this->status->tryChangeToInProgress();
    }

    public function complete(): void
    {
        $this->status->tryChangeToCompleted();
        $this->completed = new TaskDateTime();
    }

    public function cancel(): void
    {
        $this->status->tryChangeToCanceled();
    }

    public function changeTitle(string $title): void
    {
        $this->title = $title;
    }

    public function changeText(string $text): void
    {
        $this->text = $text;
    }

    public function changeCompleteBefore(TaskDateTime $date): void
    {
        $this->completeBefore = $date;
    }
}
