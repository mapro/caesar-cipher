<?php
require_once('app/autoload/Autoloader.php');

use \app\autoload\Autoloader;
use \app\crypt\CryptSentence;

Autoloader::register();

$cryptSentence = new CryptSentence();
echo $cryptSentence->cipher('$text');

$text = $newtext = 'hello';
$shiftParameter = 0;

if (isset($_POST['text']) === true && isset($_POST['sp']) === true && is_numeric($_POST['sp']) === true) {
    $text = htmlspecialchars($_POST['text'],ENT_COMPAT);
    $shiftParameter = htmlspecialchars($_POST['sp'],ENT_COMPAT);
    $newtext = $cryptSentence->crypto($text,$shiftParameter);
}

?>

<form method="post">
    <table>
        <tr>
            <th class="w">Text:</th>
            <td><textarea class="t" onfocus='this.select()' name=text><?php echo $text; ?></textarea></td>
        </tr>
        <tr>
            <th>Shift Parameter:</th>
            <td><input type=text size=2 name=sp type="number" pattern="\-{0,1}[0-9]{1,10}" value=<?php echo $shiftParameter; ?> onkeyup="this.value=this.value.replace(/[^0-9\-]/g,'');"></td>
        </tr>
        <tr>
            <td></td><td><input type=submit name=submit value='Encode'>
            <input type=submit name=submit value='Decode'>
            <input type=button value='Clear' onclick='this.form.elements.text.value=""'</td>
        </tr>
        <tr><td></td></tr>
        <tr>
            <th class="w">Result:</th>
            <td style="background-color:#CEF6F5;"><?php echo $newtext; ?></td>
        </tr>
    </table>
</form>