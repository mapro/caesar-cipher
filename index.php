<?php

$text = 'hello';
$sp = 0;

if (isset($_POST['text'])) {
    $text = htmlspecialchars($_POST['text']);
    $sp = $_POST['sp'];
    if (is_numeric($sp)) {
        if ($sp<0) {
            $sp%=26;
            $sp+=26;
        }

    } else {
        $sp = 0;
    }
}

$newtext = $text;

if(isset($_POST['text'])){
	for ($i=0;$i<strlen($text);$i++) {

	  $ascii = ord($text[$i]);
	  for($j=0;$j<$sp;$j++){
		if($ascii == 90) { //uppercase bound
		  $ascii = 65; //reset back to 'A'
		}
		else if($ascii == 122) { //lowercase bound
		  $ascii = 97; //reset back to 'a'
		}
		else {
		  $ascii++;
		}
	  }
	  $newtext[$i] = chr($ascii);

	}
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
            <td><input type=text size=2 name=sp type="number" pattern="\-{0,1}[0-9]{1,10}" value=<?php echo $sp; ?> onkeyup="this.value=this.value.replace(/[^0-9\-]/g,'');"></td>
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