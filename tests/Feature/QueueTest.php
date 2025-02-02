<?php

namespace Botika\LaravelQueueRabbitMQ\Tests\Feature;

use PhpAmqpLib\Connection\AMQPLazyConnection;

class QueueTest extends TestCase
{
    public function testConnection(): void
    {
        $this->assertInstanceOf(AMQPLazyConnection::class, $this->connection()->getChannel()->getConnection());
    }
}
