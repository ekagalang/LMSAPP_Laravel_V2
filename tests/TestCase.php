<?php

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithVite;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithVite;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
