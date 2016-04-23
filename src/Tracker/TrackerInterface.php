<?php 

namespace Charmpitz\Tracker;

interface TrackerInterface {

	/**
	 * Used to open a curl connection and get the cookie jar
	 */
    public function connect();

    /**
     * Closes the curl connection
     */
    public function disconnect();

    /**
     * Executes the query and returns an array with the results
     */
    public function execute();

    /**
     * Downloads the given files to the path
     */
    public function download($links, $path = false);

    /**
     * Returns the data of the given URL
     */
    public function getData($url);

}