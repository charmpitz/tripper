<?php 

namespace Charmpitz\Tracker;

use \Sunra\PhpSimple\HtmlDomParser;

class Freshontv extends TrackerAbstract implements TrackerInterface {

    protected $domain     = "https://freshon.tv";
    protected $loginUrl   = "https://freshon.tv/login.php?action=makelogin";
    protected $searchUrl  = "https://freshon.tv/browse.php?search=";

    protected function parseHtml($html) {
        $dom  = HtmlDomParser::str_get_html($html);

        # Search for the anchors
        $ancors = $dom->find("a.torrent_name_link");

        $data = [];
        foreach ($ancors as $anchor) {
            $data[] = [
                'href'  => $this->domain . str_replace('details', 'download', $anchor->href) . "&type=torrent",
                'name'  => $anchor->innertext
            ];
        }

        return $data;
    }
}