<?php

class Utils
{
    const emailRegex = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
    const emailRegexLogin = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))|admin|user$/";
    const namesRegex = "/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/";
    const passwordRegex = "/^(?=.*\d)(?=.*[a-zA-Z])[0-9a-zA-Z]{8,}$/";
    const passwordRegexLogin = "/^(?=.*\d)(?=.*[a-zA-Z])[0-9a-zA-Z]{8,}|admin|user$/";


    /**
     * Prints POST function's user feedback
     * @used_in updateInfoUtente()
     * @used_in deleteCandidatura()
     * @before_use check isset $_SESSION["method"] & $_SESSION["success"]
     * @before_use check $_SESSION["method"] equals to the correct submit value
     * @param $htmlPage string html page
     * @param $placeholder string placeholder tag to be replaced with feedback
     */
    public static function printFeedback(&$htmlPage, $placeholder)
    {
        $feedback = isset($_SESSION["feedback"]) ? $_SESSION["feedback"] : "";

        if ($_SESSION["success"]) {
            $feedback = "<span><strong class='feedbackPositive'>" . $feedback . "</strong></span>";
        } else {
            $feedback = "<span><strong class='feedbackNegative'>" . $feedback . "</strong></span>";
        }

        $htmlPage = str_replace($placeholder, $feedback, $htmlPage);
    }

    /**
     * Validates a string: checks if it's not empty and if it respects a given regular expression
     * @param $element string the input to be checked
     * @param $regex string regular espression
     * @return bool true is element is valid, else false
     */
    public static function validate($element, $regex)
    {
        return strlen($element) == 0
            || preg_match($regex, $element);
    }
}