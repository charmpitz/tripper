<?php

namespace Charmpitz;

class Tripper
{
    /**
     * Contains the tracker
     */
    private $data = [];

    /**
     * Contains the tracker
     */
    private $tracker = null;

    /**
     * Contains the client
     */
    private $client = null;

    /**
     * Contains the tracker
     */
    private $query = null;

    function __construct($data = []) {
        $this->data = $data;

        if (!empty($this->data['tracker'])) {
            $this->tracker = TrackerFactory::build($this->data['tracker']);
        }
        
        if (!empty($this->data['client'])) {
            $this->client  = ClientFactory::build($this->data['client']);
        }

        if (!empty($this->data['query'])) {
            $this->query   = new Query($this->data['query']);
        }
    }

    public function setTracker($trackerData) {
        $this->tracker = TrackerFactory::build($trackerData);
    }

    public function setClient($clientData) {
        $this->client = ClientFactory::build($clientData);
    }

    public function setQuery($queryData) {
        $this->query   = new Query($queryData);
    }

    public function execute() {
        if (empty($this->tracker)) {
            throw new Exception("No tracker specified.");
        }

        if (empty($this->query)) {
            throw new Exception("No query specified.");
        }

        $results = $this->tracker->execute($this->query);

        return $results;
    }

}