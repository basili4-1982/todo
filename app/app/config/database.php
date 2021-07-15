<?php

/**
 * This file is part of Spiral package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Spiral\Database\Driver\MySQL\MySQLDriver;

return [
    /**
     * Default database connection
     */
    'default' => 'db',

    /**
     * The Spiral/Database module provides support to manage multiple databases
     * in one application, use read/write connections and logically separate
     * multiple databases within one connection using prefixes.
     *
     * To register a new database simply add a new one into
     * "databases" section below.
     */
    'databases' => [
        'db' => [
            'driver' => 'mysql',
        ],
    ],

    /**
     * Each database instance must have an associated connection object.
     * Connections used to provide low-level functionality and wrap different
     * database drivers. To register a new connection you have to specify
     * the driver class and its connection options.
     */
    'drivers' => [
        'mysql' => [
            'driver' => MySQLDriver::class,
            'connection' => 'mysql:host=db;dbname=db',
            'username' => 'root',
            'password' => 'secret',
        ],
    ],
];
