<?php

namespace Tests\Unit\Domain\Entities\Traits;

use Core\Domain\Entities\Traits\MethodsMagicsTrait;
use Exception;
use PHPUnit\Framework\TestCase;

class MethodsMagicsTraitTest extends TestCase
{
    private function createTraitInstance()
    {
        return new class {
            use MethodsMagicsTrait;
            
            public $id;
            public $testProperty = 'value';
        };
    }

    public function testGetExistingProperty()
    {
        $instance = $this->createTraitInstance();
        $instance->id = 'test-id';
        
        $this->assertEquals('test-id', $instance->id);
        $this->assertEquals('value', $instance->testProperty);
    }

    public function testGetNonExistingProperty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Property invalidProperty not found in class');

        $instance = $this->createTraitInstance();
        $instance->invalidProperty;
    }

    public function testIdMethod()
    {
        $instance = $this->createTraitInstance();
        $instance->id = 'test-id-123';
        
        $this->assertEquals('test-id-123', $instance->id());
    }
}
