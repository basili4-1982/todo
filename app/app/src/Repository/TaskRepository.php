<?php

/**
 * {project-name}
 *
 * @author {author-name}
 */
declare(strict_types=1);

namespace App\Repository;

use App\Database\Task;
use App\Mix\TimeTrait;
use Exception;
use Spiral\Database\Database;

class TaskRepository
{
    use TimeTrait;

    private Database $database;

    /**
     * TaskRepository constructor.
     */
    public function __construct(Database $db)
    {
        $this->database = $db;
    }

    public function getTasks(int $projectId): array
    {
        $all = $this->database->select(["id", "title", "description", "start_date as start", "duration", "done"])->from('tasks')->andWhere(['project_id' => $projectId])->fetchAll(
        );

        return array_filter(
            array_map(
                static function (array $item) {
                    try {
                        return (new Task())->load($item);
                    } catch (Exception $e) {
                        return null;
                    }
                },
                $all
            )
        );
    }

    /**
     * description: ""
     * duration: "4h"
     * start: "12.07.2021 19:09:05"
     * title: ""
     *
     * @throws Exception
     */
    public function addTask(int $projectId, Task $task): Task
    {
        $start = $task->getDateStart();

        $id = $this->database->insert('tasks')->values(
            [
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'duration' => $task->getDuration(),
                'start_date' => $start === null ? null : $start->format('Y.m.d H:i:s'),
                'end_date' => $start === null ? null : $this->durationToDateTime($start, $task->getDuration())->format('Y.m.d H:i:s'),
                'project_id' => $projectId,
            ]
        )->run();

        return $task->setId($id);
    }

    public function updateTask(int $id, Task $task): Task
    {
        $start = $task->getDateStart();
        $this->database->update(
            'tasks',
            [
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'duration' => $task->getDuration(),
                'start_date' => $start === null ? null : $start->format('Y.m.d H:i:s'),
                'end_date' => $start === null ? null : $this->durationToDateTime($start, $task->getDuration())->format('Y.m.d H:i:s'),
                'done' => $task->isDone(),
            ],
            ['id' => $id]
        )->run();
        return $task;
    }

    public function deleteTask(int $id): bool
    {
        $this->database->delete('tasks', ['id' => $id])->run();

        return true;
    }

    public function getTask(int $taskId): ?Task
    {
        $all = $this->database->select("*")->from('tasks')->andWhere(['id' => $taskId])->fetchAll();

        $a = array_map(
            static function (array $item) {
                return (new Task())
                    ->setId((int) $item['id'])
                    ->setTitle($item['title'])
                    ->setDescription($item['description']);
            },
            $all
        );

        return $a[0] ?? null;
    }
}
