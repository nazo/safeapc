<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SafeApc\SafeApc;

final class SafeApcTest extends TestCase
{
    public function testSetAndGet(): void
    {
        $apc = new SafeApc();
        $apc->setCacheStartTime($_SERVER['REQUEST_TIME']);
        $apc->setCacheVersionKey('test');

        $apc->set('foo', 1);
        $this->assertSame(1, $apc->get('foo'));
    }
}

