<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

// The Concurrency suite uses DatabaseMigrations (migrate:fresh, no wrapping transaction)
// instead of RefreshDatabase: rows must be COMMITTED so the forked processes started by
// Concurrency::driver('fork') can read them over their own database connections.
pest()->extend(TestCase::class)
    ->use(DatabaseMigrations::class)
    ->in('Concurrency');
