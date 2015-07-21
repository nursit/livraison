<?php
/**
 * Mesure par defaut d'un objet
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cédric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\inc\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Calculer le poids (en g) et le volume (en cm3) de N objets
 * (a livrer dans une commande)
 * la quantite est fournie en argument car pour certains objets il peut y avoir des effet de seuil
 * du au packaging
 * Ex : des bouteilles envoyees par caisse de 6 meme si caisse incomplete
 * a traiter au cas par cas
 *
 * La fonction est specialisable en fonction du type, sous le nom
 * mesure_{objet}_dist()
 *
 * @param string $objet
 * @param int $id_objet
 * @param int $quantite
 * @return array ($poids,$volume)
 */
function mesure_defaut_dist($objet, $id_objet, $quantite=1){
	$poids = $volume = 0;

	$table_sql = table_objet_sql($objet);
	$primary = id_table_objet($objet);
	$row = sql_fetsel("*",$table_sql,$primary."=".intval($id_objet));

	if (isset($row['poids'])){
		$poids = floatval($row['poids']) * $quantite;
	}
	if (isset($row['volume'])){
		$volume = floatval($row['volume']) * $quantite;
	}
	elseif(isset($row['longueur']) AND isset($row['largeur']) AND isset($row['hauteur'])){
		$volume = floatval($row['longueur']) * floatval($row['largeur']) * floatval($row['hauteur']) * $quantite;
	}

	return array($poids,$volume);
}