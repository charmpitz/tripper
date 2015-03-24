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
	public $ripper;

	public function __construct($options, $torrents) {

		$this->name = str_replace(' ', '.', $options['name']);
		$this->query = $options['query'];
		$this->resolution = $options['resolution'];
		$this->ripper = $options['ripper'];
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

			$list = array();
			$episode_list = array();
			$season_list = array();

			// Preparing regex
			$regex = "/^".$this->name.".(S([0-9]{2})E([0-9]{1,2})?|S([0-9]{2})).*";

			if (isset($this->resolution))
				$regex .= "(".$this->resolution.")+.*";
			
			if (isset($this->ripper))
				$regex .= "-(".$this->ripper.")+.*";

			$regex .= "/i";

			foreach($this->torrents as $element)
			{
				if (preg_match($regex, $element['name'], $out) > 0)
				{
					$current_season = intval($out[2]);
					$current_episode = intval($out[3]);
					$full_season = intval($out[4]);


					// Store the torrents in a smart way
					if ($current_episode)
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
					if (isset($season_list[$season_start]))
					{
						// Add the season to the list
						$list[] = $this->choose($season_list[$season_start]);
					}
				}
				else
				{
					// Add the episodes to the list
					for ($i=$episode_start;$i<=99;$i++)
					{
						$arg = $season_start.($i < 10 ? "0" : "").$i;
						if (isset($episode_list[$arg]))
						{
							$list[] = $this->choose($episode_list[$arg]);
						}
					}
				}

				// Middle Case
				for ($i=$season_start+1;$i<$season_end;$i++)
				{
					if (isset($season_list[$i]))
					{
						$list[] = $this->choose($season_list[$i]);
					}
					else
					{
						for ($j=0;$j<=99;$j++)
						{
							$arg = $i.($j < 10 ? "0" : "").$j;
							if (isset($episode_list[$arg]))
							{
								$list[] = $this->choose($episode_list[$arg]);
							}
						}
					}
				}

				// End Case
				$arg = $season_end.($episode_end < 10 ? "0" : "").$episode_end;
				if ((!isset($episode_list[intval($arg)+1])) && (isset($season_list[$season_end])))
				{
					// Add the season to the list
					$list[] = $this->choose($season_list[$season_end]);
				}
				else
				{
					// Add the episodes to the list
					for ($i=0;$i<=$episode_end;$i++)
					{
						$arg = $season_end.($i < 10 ? "0" : "").$i;
						if (!empty($episode_list[$arg]))
						{
							$list[] = $this->choose($episode_list[$arg]);
						}
					}
				}
			}
			else
			{
				$list[] = is_numeric($episode) ? $this->choose($episode_list[$season.($episode < 10 ? "0" : "").$episode]) : $this->choose($season_list[$season]);
			}

		}
		$this->torrents = $list;
	}

	public function search() {
		$this->filter();

		$result = array();

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
	public $ripper;
	public $torrents;

	public function __construct($options, $torrents) {

		$this->name = str_replace(' ', '.', $options['name']);
		$this->year = $options['year'];
		$this->resolution = $options['resolution'];
		$this->quality = $options['quality'];
		$this->number_of_results = $options['number_of_results'];
		$this->ripper = $options['ripper'];
		$this->torrents = $torrents;
	}

	protected function filter() {
		if (!empty($this->year))
		{
			$list = array();
			$count = 0;

			$regex0 = "/.*".$this->name.".*/i";
			$regex1 = "/.*".$this->year.".*/i";
			$regex2 = "/.*".$this->resolution.".*/i";
			$regex3 = "/.*".$this->quality.".*/i";
			$regex4 = "/.*".$this->ripper.".*/i";

			foreach($this->torrents as $element)
			{
				if ((preg_match($regex0, $element['name']) > 0) && 
					(preg_match($regex1, $element['name']) > 0) &&
					(preg_match($regex2, $element['name']) > 0) &&
					(preg_match($regex3, $element['name']) > 0) &&
					(preg_match($regex4, $element['name']) > 0))
				{
					$list[] = $element;
					$count++;
				}
				if ($count == $this->number_of_results) break;
			}

		}
		$this->torrents = $list;
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