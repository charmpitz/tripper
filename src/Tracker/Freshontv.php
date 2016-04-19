<?php 

namespace Charmpitz;

class Freshontv extends TrackerAbstract implements TrackerInterface {

    public $domain    = "https://freshon.tv";
    public $loginUrl  = "https://freshon.tv/login.php?action=makelogin";
    public $searchUrl = "https://freshon.tv/browse.php?search={search}";

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