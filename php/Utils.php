<?php

include_once "sanitizer.php";

class Utils
{
    const emailRegex = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
    const emailRegexLogin = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))|admin|user$/";
    const namesRegex = "/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/";
    const passwordRegex = "/^(?=.*\d)(?=.*[a-zA-Z])[0-9a-zA-Z]{8,}$/";
    const passwordRegexLogin = "/^(?=.*\d)(?=.*[a-zA-Z])[0-9a-zA-Z]{8,}|admin|user$/";
    const titoloRegex = "/[\wàèéìòù]{1,}/";
    const descrizioneRegex = "/^.{10,}$/";
    const durataRegex = "/^[6-9][0-9]|1[0-7][0-9]|180$/";
    const annoRegex = "/^19[0-9][0-9]|20[0-1][0-9]|202[0-2]$/";
    const regista_produttoreRegex = "/[a-zA-Zàèéìòù]{1,}/";
    const castRegex = "/^.{5,}$/";
    const altRegex = "/^.{4,125}$/";

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
        $feedback = isset($_SESSION["feedback"]) ? Sanitizer::forHtml($_SESSION["feedback"]) : "";

        if ($_SESSION["success"]) {
            $feedback = "<strong class='feedbackPositive'>" . $feedback . "</strong>";
        } else {
            $feedback = "<strong class='feedbackNegative'>" . $feedback . "</strong>";
        }

        $htmlPage = str_replace($placeholder, $feedback, $htmlPage);

        unset($_SESSION["feedback"]);
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