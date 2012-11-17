<?PHP
require_once('recaptchalib.php');
$publickey = "6LfFl8YSAAAAACB6drjxyY6H4ArzBVTAikB-BJAr";
$privatekey = "6LfFl8YSAAAAAHJ5oZJbhu-JyUBdEymW29Yc11VZ";
 
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
 
if ($resp->is_valid) {
    ?>success<?
}
else
{
    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
       "(reCAPTCHA said: " . $resp->error . ")");
}