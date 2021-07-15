<?php

/**
 * {project-name}
 *
 * @author {author-name}
 */
declare(strict_types=1);

namespace App\Database;

use Cycle\Annotated\Annotation as Cycle;
use DateTime;
use Exception;
use JsonSerializable;

/**
 * @Cycle\Entity(
 *     table = "projects"
 *     repository = "Repository\TaskRepository",
 *     role="tasks"
 * )
 */
class Task implements JsonSerializable
{
    /** @Cycle\Column (type = "primary") */
    protected int $id;

    /** @Cycle\Column (type = "string") */
    protected string $title = '';

    /** @Cycle\Column (type = "string") */
    protected ?string $description = '';

    private ?DateTime $dateStart = null;

    private string $duration = '';

    private bool $done = false;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Task
     */
    public function setId(int $id): Task
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Task
     */
    public function setTitle(string $title): Task
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string| null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Task
     */
    public function setDescription(?string $description): Task
    {
        $this->description = $description;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $start = $this->getDateStart();

        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'start' => $start !== null ? $start->format(DATE_ATOM) : null,
            'duration' => $this->getDuration(),
            'done' => $this->isDone(),
        ];
    }

    public function getDateStart(): ?DateTime
    {
        return $this->dateStart;
    }

    /**
     * @param DateTime|null $dateStart
     *
     * @return Task
     */
    public function setDateStart(?DateTime $dateStart): Task
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function load(array $data): Task
    {
        $keys = array_keys($data);

        $req = ['title', 'description', 'done'];

        $diff = array_diff($req, $keys);
        if (!empty($diff)) {
            throw new Exception(' не заполнено: ' . implode(', ', $diff));
        }

        if (isset($data['id'])) {
            $this->setId((int) $data['id']);
        }

        if (isset($data['start'])) {
            $start = new DateTime($data['start']);
            $this->setDateStart($start);
        }

        if (isset($data['duration'])) {
            $this->setDuration($data['duration']);
        }

        if (isset($data['done'])) {
            $this->setDone((bool) $data['done']);
        }

        $this->setTitle($data['title'])
            ->setDescription($data['description']);

        return $this;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    /**
     * @param string $duration
     *
     * @return Task
     */
    public function setDuration(string $duration): Task
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * @param bool $done
     *
     * @return Task
     */
    public function setDone(bool $done): Task
    {
        $this->done = $done;
        return $this;
    }
}
