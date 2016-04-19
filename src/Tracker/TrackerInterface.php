<?php 

namespace Charmpitz;

interface TrackerInterface {

    public function connect();

    public function disconnect();

    public function download($links, $path = false);

    public function getData($url);

    /**
     * Executes the query
     */
    public function execute();

}