<?php

/**
 * {project-name}
 *
 * @author {author-name}
 */
declare(strict_types=1);

namespace App\Repository;

use App\Database\Project;
use Spiral\Database\Database;

class ProjectRepository
{
    private Database $database;

    /**
     * ProjectRepository constructor.
     */
    public function __construct(Database $db)
    {
        $this->database = $db;
    }

    public function getProjects(): array
    {
        $all = $this->database->select("*")->from('projects')->fetchAll();

        return array_map(
            static function (array $item) {
                return (new Project())
                    ->setId((int) $item['id'])
                    ->setTitle($item['title'])
                    ->setDescription($item['description']);
            },
            $all
        );
    }

    public function addProject(Project $project): Project
    {
        $start = $project->getDateStart();

        $id = $this->database->insert('projects')->values(
            [
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'start_date' => $start === null ? null : $start->format('Y.m.d H:i:s'),
            ]
        )->run();

        return $project->setId($id);
    }

    public function updateProject(int $id, Project $project): Project
    {
        $start = $project->getDateStart();
        $this->database->update(
            'projects',
            [
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'start_date' => $start === null ? null : $start->format('Y.m.d H:i:s'),
            ],
            ['id' => $id]
        )->run();
        return $project;
    }

    public function deleteProject(int $id): bool
    {
        $this->database->delete('projects', ['id' => $id])->run();

        return true;
    }
}
