# Tripper
PHP class that connects to given Trackers, searches for torrents and adds them into your favorite Torrent Downloader.


### Installation
To use this class you will need to do
```
$ git clone https://github.com/charmpitz/Tripper.git
$ composer.phar install
```

Currently supported trackers:
- http://filelist.ro
- https://freshon.tv

Currently supported searches:
- Series
- Movies

Currently supported Torrent apps:
- QBittorrent


### Usage
```php
$freshon = 
new Tripper(
    array(
        'tracker' => array(
        	// FreshonTv, Filelist
            'type' => 'FreshonTv',
            'options' => array(
                'hd_only' => true
            ),
            'credentials' => array(
                'username' => '',
                'password' => ''
            )
        ),
        'search' => array(
            'type' => 'Series',
            'options' => array(
                'name' => 'Supernatural',
                'query' => 'S07-S10E04',
                'resolution' => '480p|720p|1080p',
                // 'ripper' => 'NTb|DIMENSION|LOL|2HD|TvT|KiNGS|W4F|DEMAND|SAiNTS|CtrlHD|ECI',
                // 'custom_search_name' => '',
            )
        ),
        'client' => array(
            'type' => 'QBittorrent',
            'options' => array(
                'host' => 'http://localhost:8080',
                'auth' => true,
                'username' => '',
                'password' => '',
            ),
        ),
        'options' => array(
            'download' => true,
            'download_path' => "./torrents/",
            'send_to_client' => false
        ),
    )
);

$freshon->execute();

$filelist = 
new Tripper(
    array(
        'tracker' => array(
            'type' => 'Filelist'
            'credentials' => array(
                'username' => '',
                'password' => ''
            )
        ),
        'search' => array(
            'type' => 'Movies',
            'options' => array(
                'name' => 'Shawshank Redemption',
                'year' => '1994',
                'resolution' => '720p', // |1080p|480p
                'quality' => 'Bluray', // |BluRay|BRRip|DVDRip|DVD5.PAL
                // 'ripper' => 'NTb|DIMENSION|LOL|2HD|TvT|KiNGS|W4F|DEMAND|SAiNTS|CtrlHD|ECI',
                'number_of_results' => 1
            )
        ),
        'client' => array(
            'type' => 'QBittorrent',
            'options' => array(
                'host' => 'http://localhost:8080',
                'auth' => true,
                'username' => '',
                'password' => '',
            ),
        ),
        'options' => array(
            'download' => true,
            'download_path' => "./torrents/",
            'send_to_client' => false
        ),
    )
);

$filelist->execute();
```

