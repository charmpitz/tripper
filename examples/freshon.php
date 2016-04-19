<?php 
include "../vendor/autoload.php";

$freshon = new Tripper([
    'tracker' => [
        // FreshonTv, Filelist
        'type'        => 'FreshonTv',
        'credentials' => [
            'username' => 'armpitz',
            'password' => 'laptop100'
        ]
    ],
    'query' => [
        'type'    => 'Series',
        'options' => [
            'name'       => 'Supernatural',
            'query'      => 'S07-S11E04',
            'resolution' => '480p|720p|1080p',
            // 'ripper' => 'NTb|DIMENSION|LOL|2HD|TvT|KiNGS|W4F|DEMAND|SAiNTS|CtrlHD|ECI',
            // 'custom_search_name' => '',
        ]
    ],
    'client' => [
        'type'    => 'QBittorrent',
        'options' => [
            'host'     => 'http://localhost:8080',
            'auth'     => true,
            'username' => '',
            'password' => '',
        ],
    ],
    'options' => [
        'download'       => true,
        'download_path'  => "./torrents/",
        'send_to_client' => false
    ]
]);

$freshon->execute();