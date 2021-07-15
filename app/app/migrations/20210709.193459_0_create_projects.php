<?php

/**
 * {project-name}
 *
 * @author {author-name}
 */
declare(strict_types=1);

namespace App;

use Spiral\Migrations\Migration;

class CreateProjectsMigration extends Migration
{
    private const TABLE = 'projects';

    /**
     * Create tables, add columns or insert data here
     */
    public function up(): void
    {
        $this->table(self::TABLE)
            ->addColumn('id', 'primary')
            ->addColumn('title', 'string', ['length' => 255, 'nullable' => false])
            ->addColumn('description', 'text', ['nullable' => true])
            ->addColumn('start_date', 'datetime', ['nullable' => true])
            ->addColumn('end_date', 'datetime', ['nullable' => true])
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
