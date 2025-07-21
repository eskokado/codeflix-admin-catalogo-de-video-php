<?php

namespace Tests\Unit;

use Core\Teste;
use PHPUnit\Framework\TestCase;

class TesteUnitTest extends TestCase
{
    public function testFoo()
    {
        $teste = new Teste();
        $this->assertEquals('foo', $teste->foo());
    }
}