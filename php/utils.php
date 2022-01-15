<?php

class Utils
{
    /**
     * Prints POST function's user feedback
     * @used_in updateInfoUtente()
     * @used_in deleteCandidatura()
     * @before_use check isset $_SESSION["method"] & $_SESSION["success"]
     * @before_use check $_SESSION["method"] equals to the correct submit value
     * @param $htmlPage string html page
     * @param $placeholder string placeholder tag to be replaced with feedback
     */
    public static function printFeedback(&$htmlPage, $placeholder){
        $feedback = isset($_SESSION["feedback"]) ? $_SESSION["feedback"] : "";

        if ($_SESSION["success"]) {
            $feedback = "<span><strong class='feedbackPositive'>" . $feedback . "</strong></span>";
        } else {
            $feedback = "<span><strong class='feedbackNegative'>" . $feedback . "</strong></span>";
        }

        $htmlPage = str_replace($placeholder, $feedback, $htmlPage);
    }
}