<?php

class Sanitizer {


    public static function forHtml($string) {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
    }

    public static function forJs($string) {
        return addslashes(htmlspecialchars($string, ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML5)); // leave quotes but escape them
    }

    public static function forJson($entity) {
        return json_encode($entity, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

}

?>