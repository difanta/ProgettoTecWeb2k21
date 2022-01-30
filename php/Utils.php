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
            $feedback = "<strong class='feedbackPositive' tabindex='-1'>" . $feedback . "</strong>";
        } else {
            $feedback = "<strong class='feedbackNegative' tabindex='-1'>" . $feedback . "</strong>";
        }

        $htmlPage = str_replace($placeholder, $feedback, $htmlPage);

        unset($_SESSION["feedback"]);
    }

    /**
     * Deletes all printed non-used placeholders
     * @param $htmlPage string html page
     * @param ...$placeholders array of placeholders
     */
    public static function feedbackCleanUp(&$htmlPage, ...$placeholders)
    {
        foreach ($placeholders as $elem)
            $htmlPage = str_replace($elem, "", $htmlPage);
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

    /**
     * Takes a date and formats as per given format and translates it to italian.
     * Workaround to using setlocale() and strftime() as it_IT.utf8 is installed on the server is not set by setlocale.
     * @author stack overflow's user: rpravisani
     * @reference https://stackoverflow.com/questions/12565981/setlocale-and-strftime-not-translating-month
     * @param $format string format to transalte a date to
     * @param $timestamp timestamp the date to be manipulated
     * @return array|mixed|string|string[] transalted data
     */
    public static function strftimeIta($format, $timestamp)
    {
        $months = [
            1 => "gennaio",
            "febbraio",
            "marzo",
            "aprile",
            "maggio",
            "giugno",
            "luglio",
            "agosto",
            "settembre",
            "ottobre",
            "novembre",
            "dicembre"];

        $weekdays = [
            "domenica",
            "luned&igrave,",
            "marted&igrave,",
            "mercoled&igrave,",
            "gioved&igrave,",
            "venerd&igrave,",
            "sabato"];

        preg_match_all('/%([a-zA-Z])/', $format, $results);

        $originals = $results[0];
        $factors = $results[1];

        foreach ($factors as $key => $factor) {
            switch ($factor) {
                case 'a':
                    /*** Abbreviated textual representation of the day ***/
                    $n = date('w', $timestamp); // number of the weekday (0 for sunday, 6 for saturday);
                    $replace = ucfirst($weekdays[$n]);
                    $replace = substr($replace, 0, 3);
                    break;
                case 'A':
                    /*** Full textual representation of the day ***/
                    $n = date('w', $timestamp); // number of the weekday (0 for sunday, 6 for saturday);
                    $replace = ucfirst($weekdays[$n]);
                    break;
                case 'h':
                case 'b':
                    /*** Abbreviated month name ***/
                    $n = date('n', $timestamp); // Numeric representation of a month, without leading zeros
                    $replace = ucfirst($months[$n]);
                    $replace = substr($replace, 0, 3);
                    break;
                case 'B':
                    /*** Full month name ***/
                    $n = date('n', $timestamp); // Numeric representation of a month, without leading zeros
                    $replace = ucfirst($months[$n]);
                    break;
                default:
                    /*** Use standard strftime function ***/
                    $replace = strftime("%" . $factor, $timestamp);
                    break;
            }
            $search = $originals[$key];
            $format = str_replace($search, $replace, $format);
        }
        return $format;
    }
}