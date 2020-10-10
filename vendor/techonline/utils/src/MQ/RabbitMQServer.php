<?php


namespace TechOnline\Utils\MQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQServer
{
    private $host;
    private $port;
    private $user;
    private $password;

    private $connection;
    private $channelCached = [];

    
    public function __construct($host = 'localhost', $port = 5672, $user = 'guest', $password = 'guest')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
    }

    public function close()
    {
        foreach ($this->channelCached as $v) {
            $v->close();
        }
        $this->connection->close();
    }

    public function sendTopic($exchange, $topic, $message)
    {
        $k = "sendTopic.$exchange";
        if (!key_exists($k, $this->channelCached)) {
            $this->channelCached[$k] = $this->connection->channel();
            $this->channelCached[$k]->exchange_declare($exchange, AMQPExchangeType::TOPIC, false, true, false);
        }
        $msg = new AMQPMessage(json_encode($message), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channelCached[$k]->basic_publish($msg, $exchange, $topic);
    }

    public function listenTopic($exchange, $queue, $topic, $callback, $blocking = true, $queueArguments = [])
    {
        $k = "listenTopic";
        $this->channelCached[$k] = $this->connection->channel();
        $this->channelCached[$k]->queue_declare($queue, false, true, false, false, false);
        $this->channelCached[$k]->exchange_declare($exchange, AMQPExchangeType::TOPIC, false, true, false);
        $this->channelCached[$k]->queue_bind($queue, $exchange, $topic);
        $this->channelCached[$k]->basic_consume($queue, null, false, false, false, false, function (AMQPMessage $message) use ($callback) {
            $msg = @json_decode($message->getBody(), true);
            $ret = $callback($msg);
            if ($ret === 'quit') {
                $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
            } else {
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            }
        });
        if ($blocking) {
            while ($this->channelCached[$k]->is_consuming()) {
                $this->channelCached[$k]->wait();
            }
        }
    }

    public function listenTopicIsConsuming()
    {
        $k = "listenTopic";
        return $this->channelCached[$k]->is_consuming();
    }

    public function listenTopicWaitNoneBlock()
    {
        $k = "listenTopic";
        $this->channelCached[$k]->wait(null, true);
    }

}