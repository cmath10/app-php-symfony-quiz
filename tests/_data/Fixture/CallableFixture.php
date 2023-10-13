<?php

namespace App\Tests\Data\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class CallableFixture extends AbstractFixture
{
    private \Closure $fn;

    public function __construct(\Closure $fn)
    {
        $this->fn = $fn;
    }

    public function load(ObjectManager $manager): void
    {
        $fn = $this->fn;
        $fn($manager);
    }
}