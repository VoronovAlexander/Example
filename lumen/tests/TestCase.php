<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    use DatabaseMigrations;

    protected $faker;

    public function __construct()
    {
        parent::__construct();
        $this->faker = Faker\Factory::create();
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

}
