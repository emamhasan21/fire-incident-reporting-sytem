<?php
require 'vendor/autoload.php';
require 'config/config.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class GPSHandler implements MessageComponentInterface {
    protected $clients;
    protected $conn;

    public function __construct($conn) {
        $this->clients = new \SplObjectStorage;
        $this->conn = $conn;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        // Update the GPS location in the database
        $stmt = $this->conn->prepare("UPDATE fire_units SET latitude=?, longitude=? WHERE id=?");
        $stmt->bind_param("ddi", $data['latitude'], $data['longitude'], $data['unit_id']);
        $stmt->execute();
        $stmt->close();

        // Broadcast the message to all clients
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}

$server = new Ratchet\App('localhost', 8080);
$server->route('/gps', new GPSHandler($conn), ['*']);
$server->run();
