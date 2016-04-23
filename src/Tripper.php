<?php

namespace Charmpitz;

class Tripper
{
    /**
     * Contains the data
     */
    private $data = [];

    /**
     * Contains the tracker
     */
    private $tracker = null;

    /**
     * Contains the tracker
     */
    private $query = null;

    function __construct($data = []) {
        $this->data = $data;

        if (!empty($this->data['tracker'])) {
            $this->tracker = TrackerFactory::build($this->data['tracker']);
        }

        if (!empty($this->data['query']['data']['name'])) {
            $this->tracker->setSearchName($this->data['query']['data']['name']);
        }
    }

    public function setTracker($trackerData) {
        $this->tracker = TrackerFactory::build($trackerData);
    }

    public function execute() {
        if (empty($this->tracker)) {
            throw new \Exception("No tracker specified.");
        }

        $this->tracker->connect();
        $results = $this->tracker->execute($this->query);
        
        if ($this->data['options']['download']) {
            $this->tracker->download($results, $this->data['options']['download_path']);
        }

        $this->tracker->disconnect();

        return $results;
    }

}