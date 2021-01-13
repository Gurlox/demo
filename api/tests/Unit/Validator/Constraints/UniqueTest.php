<?php

namespace App\Tests\Unit\Validator\Constraints;

use App\Validator\Constraints\Unique;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class UniqueTest extends TestCase
{
    public function testWithInvalidOptions(): void
    {
        $this->expectException(MissingOptionsException::class);
        new Unique(['class' => 'someClass']);

        $this->expectException(MissingOptionsException::class);
        new Unique(['field' => 'someField']);
    }
}
