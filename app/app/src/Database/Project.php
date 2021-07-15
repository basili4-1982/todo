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
 *     repository = "Repository\ProjectRepository",
 *     role="projects"
 * )
 */
class Project implements JsonSerializable
{
    /** @Cycle\Column (type = "primary") */
    protected int $id;

    /** @Cycle\Column (type = "string") */
    protected string $title = '';

    /** @Cycle\Column (type = "string") */
    protected ?string $description = '';

    private DateTime $dateStart;

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
     * @return Project
     */
    public function setId(int $id): Project
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
     * @return Project
     */
    public function setTitle(string $title): Project
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
     * @param string $description
     *
     * @return Project
     */
    public function setDescription(?string $description): Project
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
        ];
    }

    /**
     * @throws Exception
     */
    public function load($data): Project
    {
        $keys = array_keys($data);

        $req = ['title', 'description'];

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

//        if (isset($data['duration'])) {
//            $this->setDuration($data['duration']);
//        }

        $this->setTitle($data['title'])
            ->setDescription($data['description']);

        return $this;
    }

    public function getDateStart(): DateTime
    {
        return new DateTime();
    }

    private function setDateStart(DateTime $start)
    {
        $this->dateStart = $start;
        return $this;
    }
}
