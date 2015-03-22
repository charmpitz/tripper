<?php 
class Tripper {

	public $tracker_type;
	public $tracker_options;
	public $tracker_credentials;
	public $search_type;
	public $search_options;
	public $client_type;
	public $client_options;
	public $options;
	public $result;

	public function __construct($array) {
		$this->options = $array['options'];
		$this->tracker_type = $array['tracker']['type'];
		$this->tracker_options = $array['tracker']['options'];
		$this->credentials = $array['tracker']['credentials'];
		$this->search_type = $array['search']['type'];
		$this->search_options = $array['search']['options'];
		$this->client_type = $array['client']['type'];
		$this->client_options = $array['client']['options'];
	}

	public function setOptions($options) {
		$this->search_options = $options;
	}

	public function execute() {

		// Configure a custom search on the tracker if set
		if (!isset($search_options['custom_search_name']))
			$search_name = $this->search_options['name'];
		else
			$search_name = $this->search_options['custom_search_name'];

		// Get Tracker result data
		$tracker = TrackerFactory::build($this->tracker_type, $this->credentials, $this->tracker_options, $search_name);

		$tracker->connect();

		$torrents = $tracker->execute();

		// Search through the data
		$search = SearchFactory::build($this->search_type, $this->search_options, $torrents);
		$this->result = $search->search();

		// Download the results
		if ($this->options['download'])
		{
			$files = $tracker->download($this->result, $this->options['download_path']);
			
			// Send to client
			if ($this->options['send_to_client'])
			{
				$client = ClientFactory::build($this->client_type, $this->client_options);
				$client->addTorrents($files);

			}
		}

		$tracker->disconnect();

	}

}
