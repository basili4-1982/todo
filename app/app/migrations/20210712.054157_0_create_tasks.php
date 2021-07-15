<?php

/**
 * {project-name}
 *
 * @author {author-name}
 */
declare(strict_types=1);

namespace App;

use Spiral\Migrations\Migration;

class CreateTasksMigration extends Migration
{
    private const TABLE = 'tasks';

    /**
     * Create tables, add columns or insert data here
     */
    public function up(): void
    {
        $this->table(self::TABLE)
            ->addColumn('id', 'primary')
            ->addColumn('project_id', 'integer', ['nullable' => false])
            ->addColumn('title', 'string', ['length' => 255, 'nullable' => false])
            ->addColumn('description', 'text', ['nullable' => true])
            ->addColumn('start_date', 'datetime', ['nullable' => true])
            ->addColumn('end_date', 'datetime', ['nullable' => true])
            ->addColumn('duration', 'string', ['length' => 20, 'nullable' => true])
            ->addColumn('done', 'boolean', ['nullable' => false, 'default' => false])
            ->create();
    }

    /**
     * Drop created, columns and etc here
     */
    public function down(): void
    {
        $this->table(self::TABLE)->drop();
    }
}
