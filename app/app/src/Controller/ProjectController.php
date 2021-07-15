<?php

/**
 * This file is part of Spiral package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Database\Project;
use App\Repository\ProjectRepository;
use Exception;
use Spiral\Prototype\Traits\PrototypeTrait;

class ProjectController
{
    use PrototypeTrait;

    private ProjectRepository $repository;

    /**
     * ProjectController constructor.
     */
    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function index(): array
    {
        return $this->repository->getProjects();
    }

    /**
     * @throws Exception
     */
    public function create(): array
    {
        $data = $this->request->post('project');

        $project = new Project();

        $project->load($data);

        return $this->repository->addProject($project)->jsonSerialize();
    }

    public function update(): array
    {
        $data = $this->request->post('project');

        $project = new Project();

        try {
            $project->load($data);

            if (empty($project->getId())) {
                throw new Exception('Необходимо заполнить id');
            }
        } catch (Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }

        return $this->repository->updateProject($project->getId(), $project)->jsonSerialize();
    }

    public function delete(): array
    {
        $projectId = (int) $this->request->input('project');
        $this->repository->deleteProject($projectId);
        return ['status' => 'Ok', 'ID' => $projectId];
    }
}
