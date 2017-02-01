<?php
namespace app\crypt;

/**
 * Class cryptSentence
 *
 * Permet de crypter et de déchiffrer un texte
 */
class CryptSentence
{
    /**
     * @var String $text
     *
     * Texte à crypter
     */
    protected $text;

    /**
     * @var string $privateKey
     *
     * Clef secrete pour crypter le texte
     * A NE JAMAIS DIVULGUER
     */
    protected $privateKey = "johnny";

    // for testing static functions
    public static function cipher2($text)
    {
        $textNumber = $this->convertLetterToNumber($text);
        $textOffset = $this->offsetLetter($textNumber);
        $finalText = $this->convertNumberToLetter($textOffset);

        return $finalText;
    }

    public function crypto($text,$shiftParameter){
        $newtext = '';

        if ($shiftParameter<0) {
            $shiftParameter%=26;
            $shiftParameter+=26;
        }

        for ($i=0;$i<strlen($text);$i++) {
          $ascii = ord($text[$i]);
          for($j=0;$j<$shiftParameter;$j++){
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
          $newtext .= chr($ascii);
        }

        return $newtext;
    }

    public function cipher($text)
    {
        $textNumber = $this->convertLetterToNumber($text);
        $textOffset = $this->offsetLetter($textNumber);
        $finalText = $this->convertNumberToLetter($textOffset);

        return $finalText;
    }

    public function decipher($text)
    {
        $textNumber = $this->convertLetterToNumber($text);
        $textOffsetReverse = $this->offsetReverseLetter($textNumber);
        $finalText = $this->convertNumberToLetter($textOffsetReverse);

        return $finalText;
    }

    /**
     * Convertit une phrase en chiffre
     *
     * @param string $text Le texte à convertir
     * @return string $finalText
     */
    private function convertLetterToNumber($text)
    {
        // Init
        $textToNumber = [];
        $patterns = [];
        $finalText = '';

        // Création de l'alphabet
        $alphabet = array_merge(range('a', 'z'));

        // Suppression des phrases lisibles
        //// Récupération des positions des phrases visibles
        $positionPhrases = $this->getPositionLisibleSentence($text);
        //// S'il y a des phrases lisibles
        if ($positionPhrases[1][0] !== false) {
            //// Récupération des morceaux de phrases qui ne seront pas cryptés
            foreach ($positionPhrases as $pos) {
                $patterns[] = substr($text, $pos[0] + 2, $pos[1] - $pos[0] - 2);
            }
            //// Remplacement des morceaux de phrases qui ne doivent pas être cryptés
            foreach ($patterns as $pattern) {
                $text = str_replace($pattern, '', $text);
            }
        }

        // Transformation du texte en tableau
        $textAsArray = str_split($text);

        // Transformation des caractères alphabétique par des caracteres numériques
        foreach ($textAsArray as $letter) {
            if (in_array($letter, $alphabet)) {
                $textToNumber[] = "%".array_search($letter, $alphabet, true)."%"; // Stockage de la clef
            } else {
                $textToNumber[] = (string)$letter; // Les caractères spéciaux sont stockés dans un tableau
            }
        }

        // Ajout des phrases lisibles
        //// S'il y a des phrases lisibles
        if ($positionPhrases[1][0] !== false) {
            $findPattern = 0;
            $loopIndex = 0;
            foreach ($textToNumber as $key => $num) {
                if ($num === '#') {
                    $findPattern += 1;
                    if ($findPattern === 2) {
                        $textToNumber[$key] = "#".$patterns[$loopIndex];
                        $loopIndex += 1;
                    } elseif ($findPattern === 4) {
                        $findPattern = 0;
                    }
                }
            }
        }

        // Transformation du tableau en texte
        foreach ($textToNumber as $word) {
            $finalText .= $word;
        }


        return $finalText;
    }


    /**
     * Permet de récupérer le couples de positions des phrases visibles
     *
     * @param string $text Texte à crypter
     * @return array Tableau contenant les couples de positions des phrases lisibles
     */
    private function getPositionLisibleSentence($text)
    {
        // Texte temporaire qui est modifié dans cette fonction
        $tempText = $text;

        // Pour passer au moins une fois dans la boucle au cas où le motif ## est en 1ere position
        do {
            $posLisibleSentence[] = stripos($tempText, '##'); // Stockage de la position du motif ##

            // Remplacement de toute la phrase jusqu'au motif inclut par un caractere arbitraire % afin de conserver
            // la postion des caracteres et supprimer le motif ##
            $tempText = substr_replace(
                $tempText,
                str_repeat('%', stripos($tempText, '##') + 2),
                0,
                stripos($tempText, '##') + 2
            );

        } while (stripos($tempText, '##'));

        // Association des positions 2 par 2 (début, fin)
        $position = $this->createPair($posLisibleSentence);

        return $position;
    }

    /**
     * Permet de créer des couples de valeurs
     *
     * @param array $array Tableau pour créer les couples
     * @return array Tableau contenant les couples
     */
    private function createPair($array)
    {
        $newArray = [];
        $posArray = 1;

        for ($i = 0; $i < count($array); $i++) {
            if ($i % 2 === 1) {
                $newArray[$posArray][] = $array[$i];
                $posArray += 1;
            } else {
                $newArray[$posArray][] = $array[$i];
            }
        }

        return $newArray;
    }

    /**
     * Décale la position d'un chiffre par rapport à la clef privé (sens ->)
     *
     * @param string $text Le texte convertit en nombre à crypter
     * @return string $text Texte crypté
     */
    private function offsetLetter($text)
    {
        // Init
        $hashText = [];
        $keyToNumber = $this->convertLetterToNumber($this->privateKey);
        $finalText = '';

        // Conversion du texte en tableau
        $textAsArray = preg_split("/(%\d+%)/i", $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $keyToNumber = preg_split("/(%\d+%)/i", $keyToNumber, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // Récupération de la longueur de la clef
        $privateKeyLength = count($keyToNumber);

        // Position initial du curseur sur la clef privée
        $positionPrivateKey = 0;

        // Bouclage sur le tableau pour énuméré un à un les caractères de la phrase
        foreach ($textAsArray as $number) {
            // Si c'est un nombre, on applique le code de vigenere
            if (preg_match("/(%\d+%)/i", $number)) {
                $n = explode("%", $number);
                $nKey = explode("%", $keyToNumber[$positionPrivateKey]);


                // Calcul
                $nFinal = (int)$n[1] + (int)$nKey[1];

                // Si dépassement de la lettre z
                if ($nFinal > 25) {
                    $nFinal = $nFinal - 25;
                }


                $hashText[] = "%".($nFinal)."%";
            } else { // Sinon on ne touche pas au caractère (majuscule, ponctuation, espace)
                $hashText[] = $number;
            }

            // Incrementation du curseur pour la position de la clef privée
            $positionPrivateKey += 1;
            // Si le curseur a dépassé le nombre de caractere de la clef privé
            if ($positionPrivateKey >= $privateKeyLength) {
                $positionPrivateKey = 0; // On repart de 0
            }
        }

        // Transformation du tableau en texte
        foreach ($hashText as $word) {
            $finalText .= $word;
        }

        return $finalText;
    }

    /**
     * Décale la position d'un chiffre par rapport à la clef privé (sens <-)
     *
     * @param string $text Le texte convertit en nombre à crypter
     * @return string $text Texte crypté
     */
    private function offsetReverseLetter($text)
    {
        // Init
        $hashText = [];
        $keyToNumber = $this->convertLetterToNumber($this->privateKey);
        $finalText = '';

        // Conversion du texte en tableau
        $textAsArray = preg_split("/(%\d+%)/i", $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $keyToNumber = preg_split("/(%\d+%)/i", $keyToNumber, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // Récupération de la longueur de la clef
        $privateKeyLength = count($keyToNumber);

        // Position initial du curseur sur la clef privée
        $positionPrivateKey = 0;

        // Bouclage sur le tableau pour énuméré un à un les caractères de la phrase
        foreach ($textAsArray as $number) {
            // Si c'est un nombre, on applique le code de vigenere
            if (preg_match("/(%\d+%)/i", $number)) {
                $n = explode("%", $number);
                $nKey = explode("%", $keyToNumber[$positionPrivateKey]);


                // Calcul
                $nFinal = (int)$n[1] - (int)$nKey[1];

                // Si dépassement de la lettre z
                if ($nFinal < 0) {
                    $nFinal = $nFinal + 25;
                }


                $hashText[] = "%".($nFinal)."%";
            } else { // Sinon on ne touche pas au caractère (majuscule, ponctuation, espace)
                $hashText[] = $number;
            }

            // Incrementation du curseur pour la position de la clef privée
            $positionPrivateKey += 1;
            // Si le curseur a dépassé le nombre de caractere de la clef privé
            if ($positionPrivateKey >= $privateKeyLength) {
                $positionPrivateKey = 0; // On repart de 0
            }
        }

        // Transformation du tableau en texte
        foreach ($hashText as $word) {
            $finalText .= $word;
        }

        return $finalText;
    }

    /**
     * Permet de convertir un texte avec des nombres en texte lisible
     *
     * @param string $text Le texte en nombre
     * @return string Le texte convertit
     */
    private function convertNumberToLetter($text)
    {
        // Init
        $alphabet = array_merge(range('a', 'z'));
        $numberToText = [];
        $finalText = '';

        // Convertit le texte en tableau
        $textAsArray = preg_split("/(%\d+%)/i", $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // Bouclage sur chaque lettre
        foreach ($textAsArray as $letter) {
            // Si c'est un nombre crypté
            if (preg_match("/(%\d+%)/i", $letter)) {
                $n = explode("%", $letter); // Récupération que du nombre
                $numberToText[] = $alphabet[$n[1]]; // On repere sa place dans l'alphabet
            } else { // Sino c'est une lettre majuscule ou un signe de ponctuation ou un espace
                $numberToText[] = $letter;
            }
        }

        // Transformation du tableau en texte
        foreach ($numberToText as $word) {
            $finalText .= $word;
        }

        return $finalText;
    }
}