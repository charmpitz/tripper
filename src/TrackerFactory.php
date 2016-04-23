<?php 

namespace Charmpitz;

use \Sunra\PhpSimple\HtmlDomParser;

class TrackerFactory {
    public static function build($data) {
        $tracker     = __NAMESPACE__. "\\Tracker\\" . ucwords($data['type']);
        $credentials = $data['credentials'];

        if (class_exists($tracker)) {
            return new $tracker($credentials);
        }

        throw new \Exception("Invalid tracker type given.");
    }
}