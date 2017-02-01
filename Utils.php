<?php
namespace app\tools;

/**
 * Classe abstraite des fonctions génériques
 */
class Utils
{

    
    public function obtain_POST_value($key){
        if(array_key_exists($key, $_POST)) return htmlspecialchars($_POST[$key], ENT_COMPAT);
        return "";
    }

    /**
     * Tableau des mois en français dont janvier commence à l'index 1
     *
     * @var array
     */
    public static $aMonths = array(
        '',
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre'
    );

    /**
     * Tableau des jours en français dont dimanche commence à l'index 0
     *
     * @var array
     */
    public static $aDays = array(
        'dimanche',
        'lundi',
        'mardi',
        'mercredi',
        'jeudi',
        'vendredi',
        'samedi',
        'dimanche'
    );

    /*
     * Method debugTrace - UNIQUEMENT POUR LE DEV
     * Debug apparaissant dans les stats
     * L'appel a cet méthode ne doit pas apparaître dans le code commité!!
     */
    public static function debugTrace($class='',$method='',$line='',$subject='',$object='',$color='#2ECCFA')
    {
        if (defined('ENVIRONNEMENT') === true && ENVIRONNEMENT === 'DEV') {
            if (defined('STAT') === true && STAT === true) {
                Stat::getInstance()->log("<b>DebugTrace</b> <br> Subject: ".$subject."<br>Class: ".$class."<br>Method: ".$method."<br>Line: ".$line, 0, $color, STAT_DEV, print_r($object, true));
            }
        }else{
            throw new \Exception('Method debugTrace can be used only in dev environment!  Class:'.__CLASS__."  Method:".$method);
        }
    }

}