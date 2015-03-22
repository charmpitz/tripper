<?php 
class SearchFactory {

	public static function build($type, $options, $html)
  	{
	    $search = "Search_" . ucwords($type);

	    if(class_exists($search))
	    	return new $search($options, $html);
	    else 
	    	throw new Exception("Invalid search type given.");
  	}
}

class Search_Series {

	public $name;
	public $query;
	public $resolution;
	public $torrents;

	public function __construct($options, $torrents) {

		$this->name = $options['name'];
		$this->query = $options['query'];
		$this->resolution = $options['resolution'];
		$this->torrents = $torrents;
	}

	// Choosing the best match
	protected function choose($array) {
		return $array[0];
	}

	protected function filter() {

		if (!empty($this->query))
		{
			$query = $this->query;

			$multiple = false;
			if (strpos($query, "-"))
			{
				// Multiple torrents
				$multiple = true;
				$episode = false;

				$interval = explode("-", $query);
				
				if (preg_match("/S([0-9]{2})/i", $interval[0], $start))
				{
					$season_start = intval($start[1]);
					$episode_start = 0;
				}

				if (preg_match("/S([0-9]{2})/i", $interval[1], $end))
				{
					$season_end = intval($end[1]);
					$episode_end = 99;
				}

				if (preg_match("/S([0-9]{2})E([0-9]{1,2})/i", $interval[0], $start))
				{
					$season_start = intval($start[1]);
					$episode_start = intval($start[2]);
				}

				if (preg_match("/S([0-9]{2})E([0-9]{1,2})/i", $interval[1], $end))
				{
					$season_end = intval($end[1]);
					$episode_end = intval($end[2]);
				}
			}
			else
			{
				// Single torrent

				if (preg_match("/S([0-9]{2})E([0-9]{1,2})/i", $query, $aux))
				{
					$season = intval($aux[1]);
					$episode = intval($aux[2]);
				}
				else
				{
					preg_match("/S([0-9]{2})/i", $query, $aux);
					$season = intval($aux[1]);
					$episode = false;
				}
			}

			$new = array();
			foreach($this->torrents as $element)
			{
				if (!empty($this->resolution))
					$regex = "/".$this->name.".(S([0-9]{2})E([0-9]{1,2})?|S([0-9]{2})).*(".$this->resolution.")+.*/i";
				else
					$regex = "/".$this->name.".(S([0-9]{2})E([0-9]{1,2})?|S([0-9]{2})).*/i";

				if (preg_match($regex, $element['name'], $out) > 0)
				{
					$current_season = intval($out[2]);
					$current_episode = intval($out[3]);
					$full_season = intval($out[4]);

					// Store the torrents in a smart way
					if (!empty($current_episode))
					{
						$episode_list[$current_season.($current_episode < 10 ? "0" : "").$current_episode][] = $element;
					}
					else
					{
						$season_list[$full_season][] = $element;
					}
				}
			}

			if ($multiple)
			{
				// Start Case
				if (in_array($episode_start, array(0, 1)))
				{
					// Check if the season torrent exist
					if (!empty($season_list[$season_start]))
					{
						// Add the season to the list
						$new[] = $this->choose($season_list[$season_start]);
					}
				}
				else
				{
					// Add the episodes to the list
					for ($i=$episode_start;$i<=99;$i++)
					{
						$arg = $season_start.($i < 10 ? "0" : "").$i;
						if (!empty($episode_list[$arg]))
						{
							$new[] = $this->choose($episode_list[$arg]);
						}
					}
				}

				// Middle Case
				for ($i=$season_start+1;$i<$season_end;$i++)
				{
					$new[] = $this->choose($season_list[$i]);
				}

				// End Case
				$arg = $season_end.($episode_end < 10 ? "0" : "").$episode_end;
				if ((is_null($episode_list[intval($arg)+1])) && (!is_null($season_list[$season_end])))
				{
					// Add the season to the list
					$new[] = $this->choose($season_list[$season_end]);
				}
				else
				{
					// Add the episodes to the list
					for ($i=0;$i<=$episode_end;$i++)
					{
						$arg = $season_end.($i < 10 ? "0" : "").$i;
						if (!empty($episode_list[$arg]))
						{
							$new[] = $this->choose($episode_list[$arg]);
						}
					}
				}
			}
			else
			{
				$new[] = is_numeric($episode) ? $this->choose($episode_list[$season.($episode < 10 ? "0" : "").$episode]) : $this->choose($season_list[$season]);
			}

		}

		$this->torrents = $new;
	}

	public function search() {
		$this->filter();

		// Add the .torrent extension
		foreach ($this->torrents as $key => $torrent) {
			$result[$key]['name'] = $torrent['name'].".torrent";
			$result[$key]['href'] = $torrent['href'];
		}

		return $result;
	}
}

class Search_Movies {

	public $name;
	public $year;
	public $resolution;
	public $quality;
	public $number_of_results = 1;
	public $torrents;

	public function __construct($args, $torrents) {

		$this->name = $args['name'];
		$this->year = $args['year'];
		$this->resolution = $args['resolution'];
		$this->quality = $args['quality'];
		$this->number_of_results = $args['number_of_results'];

		$this->torrents = $torrents;
	}

	protected function filter() {
		if (!empty($this->year))
		{
			$year = $this->year;

			$new = array();
			$count = 0;

			foreach($this->torrents as $element)
			{
				if (!empty($this->resolution))
					$regex = "/".$this->name.".". $this->year .".*".$this->resolution.".*/";
				else
					$regex = "/".$this->name.".". $this->year .".*/";

				$regex2 = "/.*".$this->quality.".*/";

				if ((preg_match($regex, $element['name'], $out) > 0) && (preg_match($regex2, $element['name']) > 0))
				{
					$new[] = $element;
					$count++;
				}
				if ($count == $this->number_of_results) break;
			}

		}
		$this->torrents = $new;
		print_r($new);
	}

	public function search() {
		$this->filter();

		// Add the .torrent extension
		foreach ($this->torrents as $key => $torrent) {
			$result[$key]['name'] = $torrent['name'].".torrent";
			$result[$key]['href'] = $torrent['href'];
		}

		return $result;
	}
}