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
		$unite = trim(reset($i));
		$cout = trim(end($i));
		$bareme[$unite] = (($cout==="NA" OR $cout==="N/A")?false:floatval(trim(end($i))));
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

	if (!$id_livraisonmode
	  OR !$mode = sql_fetsel("*","spip_livraisonmodes","id_livraisonmode=".intval($id_livraisonmode))){
		return false;
	}

	// verifier si le pays est dans la zone_pays eventuelle
	if (strlen($mode['zone_pays'])){
		$zone_pays = explode(',',$mode['zone_pays']);
		$zone_pays = array_map('trim',$zone_pays);
		if (!in_array($pays,$zone_pays)){
			return false;
		}
	}

	// verifier que le pays n'est pas dans la zone_pays_exclus eventuelle
	if (strlen($mode['zone_pays_exclus'])){
		$zone_pays_exclus = explode(',',$mode['zone_pays_exclus']);
		$zone_pays_exclus = array_map('trim',$zone_pays_exclus);
		if (in_array($pays,$zone_pays_exclus)){
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

	$poids_total = $volume_total = 0;
	if ($bareme_poids OR $bareme_volume){
		foreach($details as $detail){
			if ($mesure = charger_fonction($detail['objet'],"mesure",true)
			  OR $mesure = charger_fonction("defaut","mesure",true)){
				list($poids,$volume) = $mesure($detail['objet'],$detail['id_objet'],$detail['quantite']);
				$poids_total += $poids;
				$volume_total += $volume;
			}
		}
		if ($poids_total>0 AND $bareme_poids){
			$p = livraison_appliquer_bareme($poids_total,$bareme_poids);
			// si on est hors bareme, on ne peut pas utiliser ce mode de livraison
			// TODO : si le poids depasse le poids maxi, couper le colis en 2,3... pour calculer le prix
			if ($p===false) {
				return false;
			}
			$prix += $p;
		}
		if ($volume_total>0 AND $bareme_volume){
			$p = livraison_appliquer_bareme($volume_total,$bareme_volume);
			// si on est hors bareme, on ne peut pas utiliser ce mode de livraison
			// TODO : si le volume depasse le volume maxi, couper le colis en 2,3... pour calculer le prix
			if ($p===false) {
				return false;
			}
			$prix += $p;
		}
	}

	$prix = round($prix,2);

	return array($prix,$taxe);
}

/**
 * Supprimer le mode de livraison d'une commande
 * @param $id_commande
 */
function reset_livraison_commande($id_commande){
	$where = "id_commande=".intval($id_commande)." AND objet=".sql_quote('livraisonmode');
	sql_delete("spip_commandes_details",$where);
}

/**
 * Verifier si une commande necessite une livraison ou pas
 * @param $id_commande
 * @return bool
 */
function commande_livraison_necessaire($id_commande){
	static $livrable = array();
	if (isset($livrable[$id_commande])){
		return $livrable[$id_commande];
	}
	$items = sql_allfetsel("*","spip_commandes_details","id_commande=".intval($id_commande));
	$livrable[$id_commande] = false;
	foreach($items as $item){
		$table = table_objet_sql($item['objet']);
		$primary = id_table_objet($item['objet']);
		$objet = sql_fetsel("*",$table,"$primary=".intval($item['id_objet']));
		if (!isset($objet['immateriel']) OR !$objet['immateriel']){
			$livrable[$id_commande] = true;
			break;
		}
	}
	return $livrable[$id_commande];
}


/**
 * Ajouter/mettre a jout le mode et le cout de livraison de la commande
 * @param int $id_commande
 * @param int $id_livraisonmode
 *   si pas fourni on reprend celui deja existant pour une mise a jour du cout
 * @return bool
 */
function fixer_livraison_commande($id_commande,$id_livraisonmode=0){
	$where = "id_commande=".intval($id_commande)." AND objet=".sql_quote('livraisonmode');

	if (!$id_commande
	  OR !$commande = sql_fetsel("*","spip_commandes","id_commande=".intval($id_commande))){
		return false;
	}

	if (!$id_livraisonmode
	  AND !$id_livraisonmode = sql_getfetsel("id_objet","spip_commandes_details",$where)){
		return false;
	}

	$cout = livraison_calculer_cout($id_commande,$id_livraisonmode,$commande['livraison_adresse_pays'],$commande['livraison_adresse_cp']);

	$n = sql_countsel("spip_commandes_details",$where);
	// enlever les modes de livraison deja existant si en trop
	// ou si le mode de livraison demande n'est pas possible (incompatible avec l'adresse de la commande)
	if (!$cout OR $n>1){
		sql_delete("spip_commandes_details",$where);
		$n=0;
	}

	// si ce mode de livraison n'est pas possible on ne fait rien d'autre
	if (!$cout) return false;

	$mode = sql_getfetsel("titre","spip_livraisonmodes","id_livraisonmode=".intval($id_livraisonmode));
	// et en inserer 1 si besoin
	if (!$n){
		$set = array(
			'id_commande' => $id_commande,
			'descriptif' => _T('livraison:info_livraison_par',array('mode'=>$mode)),
			'quantite' => 1,
			'prix_unitaire_ht' => 0,
			'taxe' => 0,
			'objet' => 'livraisonmode',
			'id_objet' => $id_livraisonmode,
			'statut' => 'attente',
		);
		sql_insertq("spip_commandes_details",$set);
	}

	// mettre a jour le prix du mode de livraison restant
	$id_commandes_detail = sql_getfetsel("id_commandes_detail","spip_commandes_details",$where,'','id_commandes_detail','0,1');
	$set = array(
		'descriptif' => _T('livraison:info_livraison_par',array('mode'=>$mode)),
		'quantite' => 1,
		'prix_unitaire_ht' => reset($cout),
		'taxe' => end($cout),
		'statut' => 'attente',
		'id_objet' => $id_livraisonmode,
	);
	sql_updateq("spip_commandes_details",$set,"id_commandes_detail=".intval($id_commandes_detail));

	return true;
}
