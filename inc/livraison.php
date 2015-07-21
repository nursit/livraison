<?php
/**
 * Fonctions utiles au plugin Livraison
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cédric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\inc\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Transformer le tableau texte du bareme poids/prix ou volume/prix
 * en tableau PHP
 * @param $bareme_str
 * @return array|bool
 */
function livraison_poids_volume_bareme_from_string($bareme_str){
	if (!strlen(trim($bareme_str))){
		return false;
	}
	$t = explode("\n",$bareme_str);
	$bareme = array();
	foreach($t as $i){
		$i = explode("|",$i);
		$bareme[trim(reset($i))] = floatval(trim(end($i)));
	}
	return $bareme;
}

/**
 * Calculer le cout en fonction de la mesure (poids, volume) en appliquant le bareme fourni
 * @param $mesure
 * @param $bareme
 * @return bool
 */
function livraison_appliquer_bareme($mesure,$bareme){

	foreach($bareme as $limite=>$prix){
		if ($mesure<floatval($limite)) {
			return $prix;
		}
	}

	// on est sorti du bareme, on ne peut pas l'appliquer
	return false;
}


/**
 * Calculer le cout de livraison
 * @param $id_commande
 * @param $id_livraisonmode
 * @param $pays
 * @param $code_postal
 * @return array|bool
 */
function livraison_calculer_cout($id_commande,$id_livraisonmode,$pays,$code_postal){

	$mode = sql_allfetsel("*","spip_livraisonmodes","id_livraisonmode=".intval($id_livraisonmode));

	// verifier si le pays est dans la zone_pays eventuelle
	if (strlen($mode['zone_pays'])){
		$zone_pays = explode(',',$mode['zone_pays']);
		$zone_pays = array_map('trim',$zone_pays);
		if (!in_array($pays,$zone_pays)){
			return false;
		}
	}

	// verifier si le CP est dans la zone_cp eventuelle
	if (strlen($mode['zone_cp'])){
		$zone_cp = explode(',',$mode['zone_cp']);
		$zone_cp = array_map('trim',$zone_cp);
		$ok = false;
		foreach($zone_cp as $cp_ok){
			if (strncmp($code_postal,$cp_ok,strlen($cp_ok))==0){
				$ok = true;
				continue;
			}
		}
		if (!$ok) {
			return false;
		}
	}

	// verifier si le CP est dans la zone_cp_exclus eventuelle
	if (strlen($mode['zone_cp_exclus'])){
		$zone_cp_exclus = explode(',',$mode['zone_cp_exclus']);
		$zone_cp_exclus = array_map('trim',$zone_cp_exclus);
		foreach($zone_cp_exclus as $cp_exclus){
			if (strncmp($code_postal,$cp_exclus,strlen($cp_exclus))==0){
				return false;
			}
		}
	}

	// OK on est dans le cas ou le mode s'applique a cette adresse
	$details = sql_allfetsel("*","spip_commandes_details","id_commande=".intval($id_commande)." AND objet<>".sql_quote('livraisonmode'));
	$prix = 0;
	$taxe = 0;

	if (strlen($mode['taxe'])) {
		$taxe = floatval($mode['taxe']);
	}

	// le prix forfaitaire initial
	if (strlen($mode["prix_forfait_ht"])){
		$prix += floatval($mode["prix_forfait_ht"]);
	}

	// le prix en fonction du nombre de produits
	if (strlen($mode["prix_unit_ht"])){
		$prix += count($details) * floatval($mode["prix_unit_ht"]);
	}

	// le prix en fonction du poids et/ou du volume
	$bareme_poids = livraison_poids_volume_bareme_from_string($mode['prix_poids_ht']);
	$bareme_volume = livraison_poids_volume_bareme_from_string($mode['prix_volume_ht']);

	if ($bareme_poids OR $bareme_volume){
		foreach($details as $detail){
			if ($mesure = charger_fonction($detail['objet'],"mesure",true)
			  OR $mesure = charger_fonction("defaut","mesure",true)){
				list($poids,$volume) = $mesure($detail['objet'],$detail['id_objet'],$detail['quantite']);

				if ($poids>0 AND $bareme_poids){
					$p = livraison_appliquer_bareme($poids,$bareme_poids);
					// si on est hors bareme, on ne peut pas utiliser ce mode de livraison
					if ($p===false) {
						return false;
					}
					$prix += $p;
				}
				if ($volume>0 AND $bareme_volume){
					$p = livraison_appliquer_bareme($volume,$bareme_volume);
					// si on est hors bareme, on ne peut pas utiliser ce mode de livraison
					if ($p===false) {
						return false;
					}
					$prix += $p;
				}
			}
		}
	}

	$prix = round($prix,2);

	return array($prix,$taxe);
}