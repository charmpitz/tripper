# Tripper
PHP class that connects to given Trackers, searches for torrents and adds them into your favorite Torrent Downloader.

Currently supported trackers:
- http://www.filelist.ro
- https://freshon.tv


```php
$freshon = 
new Tripper(
    array(
        'tracker' => array(
            'type' => 'FreshonTv',
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
```

