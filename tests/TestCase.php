<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';

        if ($dbConnection === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('Feature tests require the pdo_sqlite extension or a non-SQLite testing database configuration.');
        }
    }
}
