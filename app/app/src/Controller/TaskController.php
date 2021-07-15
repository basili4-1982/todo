<?php

/**
 * This file is part of Spiral package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Database\Task;
use App\Repository\TaskRepository;
use Exception;
use Spiral\Prototype\Traits\PrototypeTrait;

class TaskController
{
    use PrototypeTrait;

    private TaskRepository $repository;

    /**
     * ProjectController constructor.
     */
    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function index(): array
    {
        $projectId = (int) $this->request->input('project');

        return $this->repository->getTasks($projectId);
    }

    /**
     * @throws Exception
     */
    public function create(): array
    {
        $projectId = (int) $this->request->input('project');
        $data = $this->request->post('task');

        $task = new Task();

        try {
            $task->load($data);
        } catch (Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }

        return $this->repository->addTask($projectId, $task)->jsonSerialize();
    }

    public function update(): array
    {
        $data = $this->request->post('task');

        $task = new Task();

        try {
            $task->load($data);

            if (empty($task->getId())) {
                throw new Exception('Необходимо заполнить id');
            }
        } catch (Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }

        return $this->repository->updateTask($task->getId(), $task)->jsonSerialize();
    }

    public function delete(): array
    {
        $taskId = (int) $this->request->input('task');

        $this->repository->deleteTask($taskId);

        return ['status' => 'Ok', 'ID' => $taskId];
    }

    public function get(): array
    {
        $taskId = (int) $this->request->input('task');

        $task = $this->repository->getTask($taskId);

        if ($task === null) {
            return [];
        }
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
        ];
    }
}
