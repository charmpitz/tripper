<?php
include "../vendor/autoload.php";
include "../src/Tripper.php";
include "../src/TrackerFactory.php";
include "../src/ClientFactory.php";
include "../src/SearchFactory.php";

$freshon = 
new Tripper(
    array(
        'tracker' => array(
            // FreshonTv, Filelist
            'type'    => 'FreshonTv',
            'options' => array(
                'hd_only' => true
            ),
            'credentials' => array(
                'username' => 'armpitz',
                'password' => 'laptop100'
            )
        ),
        'search' => array(
            'type'    => 'Series',
            'options' => array(
                'name'       => 'Supernatural',
                'query'      => 'S07-S11E04',
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