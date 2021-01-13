<?php

namespace App\Tests\Unit;

class ReflectionUtil
{
    public static function callMethod($obj, $name, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }

    public static function setProperty($obj, string $name, $value)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($obj, $value);
    }
}
