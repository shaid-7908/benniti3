<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once dirname(__DIR__) . "/classes/Users.php";
require_once dirname(__DIR__) . "/classes/Opportunities.php";

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);

        // Associate the user ID with the connection
        $conn->userId = $queryarray['userId'];

        echo "New connection! ({$conn->resourceId}) User ID: {$conn->userId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $users = new \Users;
        $opportunities = new \Opportunities;
        $numRecv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );
        $data = json_decode($msg, true);

        // Extract message details from the JSON data
        $opportunity_public_id = '';
        $type = $data['type'];
        $message = $data['message'];
        if ($type === 'match_madeby_seeker' || $type === 'match_madeby_solver') {
            // Extract matchedIds array
            $matchedIds = $data['matchedIds'];
            $opportunity_public_id = 'na';
            // Process matchedIds array as needed
            foreach ($matchedIds as $matchedId) {
                // Perform any necessary actions with $matchedId
                $opportunities->addMatchMadeMessageQue($message, $type, $matchedId);
            }
            // Set opportunity_public_id accordingly
           
        } elseif ($type === 'opportunity_created') {
            $type = $data['type'];
            $opportunity_public_id = $data['opportunity_public_id'];
            $message = $data['message'];
            $match_id = 'na';
            $opportunities->addOpportunityCreationToMessageQue($message, $type, $opportunity_public_id);
        }
        
        foreach ($this->clients as $client) {

            if ($from !== $client && $users->checkUserRoleByPublicId($client->userId) == 1) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
    protected function parseQueryString($queryString)
    {
        parse_str($queryString, $params);
        return $params;
    }
}
