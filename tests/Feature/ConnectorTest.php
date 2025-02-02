<?php

namespace Botika\LaravelQueueRabbitMQ\Tests\Feature;

use Illuminate\Queue\QueueManager;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use Botika\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class ConnectorTest extends \Botika\LaravelQueueRabbitMQ\Tests\TestCase
{
    public function testLazyConnection(): void
    {
        $this->app['config']->set('queue.connections.rabbitmq', [
            'driver' => 'rabbitmq',
            'queue' => env('RABBITMQ_QUEUE', 'default'),
            'connection' => AMQPLazyConnection::class,

            'hosts' => [
                [
                    'host' => getenv('HOST'),
                    'port' => getenv('PORT'),
                    'user' => 'guest',
                    'password' => 'guest',
                    'vhost' => '/',
                ],
            ],

            'options' => [
                'ssl_options' => [
                    'cafile' => env('RABBITMQ_SSL_CAFILE', null),
                    'local_cert' => env('RABBITMQ_SSL_LOCALCERT', null),
                    'local_key' => env('RABBITMQ_SSL_LOCALKEY', null),
                    'verify_peer' => env('RABBITMQ_SSL_VERIFY_PEER', true),
                    'passphrase' => env('RABBITMQ_SSL_PASSPHRASE', null),
                ],
            ],

            'worker' => env('RABBITMQ_WORKER', 'default'),
        ]);

        /** @var QueueManager $queue */
        $queue = $this->app['queue'];

        /** @var RabbitMQQueue $connection */
        $connection = $queue->connection('rabbitmq');

        $this->assertInstanceOf(RabbitMQQueue::class, $connection);
        $this->assertInstanceOf(AMQPLazyConnection::class, $connection->getConnection());
        $this->assertTrue($connection->getConnection()->isConnected());
        $this->assertTrue($connection->getChannel()->is_open());
    }

    public function testSslConnection(): void
    {
        $this->markTestSkipped();

        $this->app['config']->set('queue.connections.rabbitmq', [
            'driver' => 'rabbitmq',
            'queue' => env('RABBITMQ_QUEUE', 'default'),
            'connection' => AMQPSSLConnection::class,

            'hosts' => [
                [
                    'host' => getenv('HOST'),
                    'port' => getenv('PORT_SSL'),
                    'user' => 'guest',
                    'password' => 'guest',
                    'vhost' => '/',
                ],
            ],

            'options' => [
                'ssl_options' => [
                    'cafile' => getenv('RABBITMQ_SSL_CAFILE'),
                    'local_cert' => null,
                    'local_key' => null,
                    'verify_peer' => true,
                    'passphrase' => null,
                ],
            ],

            'worker' => env('RABBITMQ_WORKER', 'default'),
        ]);

        /** @var QueueManager $queue */
        $queue = $this->app['queue'];

        /** @var RabbitMQQueue $connection */
        $connection = $queue->connection('rabbitmq');
        $this->assertInstanceOf(RabbitMQQueue::class, $connection);
        $this->assertInstanceOf(AMQPSSLConnection::class, $connection->getConnection());
        $this->assertTrue($connection->getConnection()->isConnected());
        $this->assertTrue($connection->getChannel()->is_open());
    }
}
