<?php

namespace TechOnline\Laravel\MQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use TechOnline\Utils\MQ\RabbitMQServer;


class RabbitMQUtil
{
    
    public static function sendTopics($bulks = [])
    {
        if (empty($bulks)) {
            return;
        }
        $server = new RabbitMQServer(
            env('RABBITMQ_HOST', 'docker-master'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
        foreach ($bulks as $bulk) {
            $server->sendTopic($bulk[0], $bulk[1], $bulk[2]);
        }
        $server->close();
    }

    public static function sendTopic($exchange, $topic, $message)
    {
        $server = new RabbitMQServer(
            env('RABBITMQ_HOST', 'docker-master'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
        $server->sendTopic($exchange, $topic, $message);
        $server->close();
    }

    
    public static function listenTopic($exchange, $queue, $topic, $callback)
    {
        $server = new RabbitMQServer(
            env('RABBITMQ_HOST', 'docker-master'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
        $server->listenTopic($exchange, $queue, $topic, function ($message) use ($callback) {
            $callback($message);
        });
        $server->close();
    }
}