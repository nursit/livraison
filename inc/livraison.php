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
 * @param int $id_commande
 * @param int $id_livraisonmode
 * @param string $pays
 * @param string $code_postal
 * @param null|array $partiel
 * @return array|bool
 */
function livraison_calculer_cout($id_commande,$id_livraisonmode,$pays,$code_postal, $partiel = null){
	$id_non_livres = array();

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
	$where_partiel = '';
	if ($partiel) {
		$where_partiel = " AND ".sql_in('id_commandes_detail', $partiel);
	}
	$details = sql_allfetsel("*","spip_commandes_details","id_commande=".intval($id_commande)." AND objet<>".sql_quote('livraisonmode') . $where_partiel);
	$prix = 0;
	$taxe = 0;

	// verifier que le mode est applicable a toutes les lignes de la commande
	// ou au moins a certaines lignes si on accepte un mode de livraison partiel
	$partiellement_applicable = false;
	foreach($details as $k => $detail){
		// si on a fourni une liste $partiel des details a livrer,
		// on accepte une livraison partielle en renvoyant la liste des id non livres
		if (!livraison_applicable($detail['objet'],$detail['id_objet'],$id_livraisonmode)) {
			if ($partiel) {
				$id_non_livres[] = $detail['id_commandes_detail'];
				unset($details[$k]);
			}
			else {
				return false;
			}
		}
		else {
			$partiellement_applicable = true;
		}
	}
	// si le mode ne s'applique a aucune ligne, on arrete la
	if (!$partiellement_applicable){
		return false;
	}

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

	return array($prix, $taxe, $id_non_livres);
}

/**
 * Verifier qu'un mode est applicable a un objet de la commande
 * @param string $objet
 * @param int $id_objet
 * @param int $id_livraisonmode
 * @return bool
 */
function livraison_applicable($objet, $id_objet, $id_livraisonmode) {
	// si l'objet est immateriel c'est OK pour la livraison
	$table = table_objet_sql($objet);
	$primary = id_table_objet($objet);
	$data = sql_fetsel("*",$table,"$primary=".intval($id_objet));
	if (!isset($data['immateriel']) OR $data['immateriel']){
		return true;
	}

	$modespossibles = sql_allfetsel('id_livraisonmode','spip_livraisonmodes_liens','objet='.sql_quote($objet).' AND id_objet='.sql_quote($id_objet));
	// si aucun mode associe a l'objet, tous les modes sont possibles, donc OK
	if (count($modespossibles)) {
		$modespossibles = array_map('reset', $modespossibles);
		if (!in_array($id_livraisonmode, $modespossibles)) {
			return false;
		}
	}
	return true;
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
 * Construire la liste des choix de livraison possible, si necessaire en combinant plusieurs livraisons
 *
 * @param int $id_commande
 * @param string $pays
 * @param string $code_postal
 * @param null|array $partiel
 * @param int $from_id_livraisonmode
 * @return array
 */
function commande_trouver_livraisons_possibles($id_commande, $pays, $code_postal, $partiel = null, $from_id_livraisonmode = 0){

	$choix_livraison = array();

	// tous les modes de livraison disponibles
	$id_livraisonmodes = sql_allfetsel("id_livraisonmode", "spip_livraisonmodes", "statut=" . sql_quote('publie') . ' AND id_livraisonmode>' . intval($from_id_livraisonmode), '', 'id_livraisonmode');
	$id_livraisonmodes = array_map('reset', $id_livraisonmodes);

	// d'abord on cherche des modes de livraison qui permettent de tout livrer d'un coup
	// uniquement si pas sous ensemble $partiel fourni !
	if (is_null($partiel)){
		foreach ($id_livraisonmodes as $id_livraisonmode){
			if ($cout = livraison_calculer_cout($id_commande, $id_livraisonmode, $pays, $code_postal)){
				list($prix_ht, $taxe) = $cout;
				$prix_ttc = round($prix_ht+$prix_ht*$taxe, 2);
				$choix_livraison[$id_livraisonmode] = array(
					'id' => $id_livraisonmode,
					'prix_ht' => $prix_ht,
					'prix_ttc' => $prix_ttc,
					'decomposition' => array(
						$id_livraisonmode => array(
							'prix_ht' => $prix_ht,
							'taxe' => $taxe,
							'prix_ttc' => $prix_ttc,
						)
					)
				);
			}
		}

		// si on a trouve au moins un mode de livraison globale on arrete la
		if (count($choix_livraison)){
			return $choix_livraison;
		}
	}

	// sinon on va chercher des combinaisons de mode de livraison par parties
	$where_partiel = '';
	if ($partiel){
		$where_partiel = " AND " . sql_in('id_commandes_detail', $partiel);
	}
	$id_commandes_details = sql_allfetsel("id_commandes_detail", "spip_commandes_details", "id_commande=" . intval($id_commande) . " AND objet<>" . sql_quote('livraisonmode') . $where_partiel);
	$id_commandes_details = array_map('reset', $id_commandes_details);
	foreach ($id_livraisonmodes as $id_livraisonmode){
		if ($cout = livraison_calculer_cout($id_commande, $id_livraisonmode, $pays, $code_postal, $id_commandes_details)){
			list($prix_ht, $taxe, $id_non_livres) = $cout;
			// chercher d'autres modes de livraison pour le restant
			if ($id_non_livres){
				$sous_choix = commande_trouver_livraisons_possibles($id_commande, $pays, $code_postal, $id_non_livres, $id_livraisonmode);
				if (count($sous_choix)){
					foreach ($sous_choix as $sous){
						$id = explode(',', $sous['id']);
						array_unshift($id, $id_livraisonmode);
						$id = implode(',', $id);
						$prix_ttc = round($prix_ht+$prix_ht*$taxe, 2);
						$decomposition = array();
						$decomposition[$id_livraisonmode] = array(
							'prix_ht' => $prix_ht,
							'taxe' => $taxe,
							'prix_ttc' => $prix_ttc,
						);
						foreach ($sous['decomposition'] as $k=>$s) {
							$decomposition[$k] = $s;
						}
						$choix_livraison[$id] = array(
							'id' => $id,
							'prix_ht' => $sous['prix_ht']+$prix_ht,
							'prix_ttc' => $sous['prix_ttc']+$prix_ttc,
							'decomposition' => $decomposition,
						);
					}
				}
			} // on arrive a livrer tout le restant d'un coup !
			else {
				$prix_ttc = round($prix_ht+$prix_ht*$taxe, 2);
				$choix_livraison[$id_livraisonmode] = array(
					'id' => $id_livraisonmode,
					'prix_ht' => $prix_ht,
					'prix_ttc' => $prix_ttc,
					'decomposition' => array(
						$id_livraisonmode => array(
							'prix_ht' => $prix_ht,
							'taxe' => $taxe,
							'prix_ttc' => $prix_ttc,
						)
					)
				);
			}
		}
	}

	return $choix_livraison;
}


/**
 * Ajouter/mettre a jour le mode et le cout de livraison de la commande
 * @param int $id_commande
 * @param int $id_livraisonmode
 *   si pas fourni on reprend celui deja existant pour une mise a jour du cout
 * @return bool
 */
function fixer_livraison_commande($id_commande, $id_livraisonmode=0){
	$where = "id_commande=".intval($id_commande)." AND objet=".sql_quote('livraisonmode');

	if (!$id_commande
	  OR !$commande = sql_fetsel("*","spip_commandes","id_commande=".intval($id_commande))){
		return false;
	}

	if ($commande['livraison_nom']) {
		$pays = $commande['livraison_adresse_pays'];
		$cp = $commande['livraison_adresse_cp'];
	}
	else {
		$pays = $commande['facturation_adresse_pays'];
		$cp = $commande['facturation_adresse_cp'];
	}

	$choix_livraison = commande_trouver_livraisons_possibles($id_commande, $pays, $cp);

	if (!$id_livraisonmode and count($choix_livraison) == 1) {
		$id_livraisonmode = array_keys($choix_livraison);
		$id_livraisonmode = reset($id_livraisonmode);
	}

	// pas de mode de livraison applicable ou incorrect : on sort de la
	// en faisant reset sur la livraison de la commande
	if (!$id_livraisonmode or !isset($choix_livraison[$id_livraisonmode])){
		sql_delete("spip_commandes_details",$where);
		return false;
	}

	$livraison = $choix_livraison[$id_livraisonmode];
	$id_livraisonmodes = array_keys($livraison['decomposition']);

	// enlever les modes de livraison dont on a pas besoin
	sql_delete("spip_commandes_details",$where . ' AND '.sql_in('id_objet', $id_livraisonmodes, 'NOT'));

	foreach($livraison['decomposition'] as $id_livraisonmode => $detailprix) {
		$mode = sql_getfetsel("titre","spip_livraisonmodes","id_livraisonmode=".intval($id_livraisonmode));
		include_spip('inc/texte');
		$mode = typo($mode);
		$id_commandes_detail = sql_getfetsel("id_commandes_detail","spip_commandes_details",$where . ' AND id_objet='.intval($id_livraisonmode),'');
		$set = array(
			'id_commande' => $id_commande,
			'descriptif' => _T('livraison:info_livraison_par',array('mode'=>$mode)),
			'quantite' => 1,
			'prix_unitaire_ht' => $detailprix['prix_ht'],
			'taxe' => $detailprix['taxe'],
			'objet' => 'livraisonmode',
			'id_objet' => $id_livraisonmode,
			'statut' => 'attente',
		);
		if (!$id_commandes_detail) {
			sql_insertq("spip_commandes_details",$set);
		}
		else {
			sql_updateq("spip_commandes_details",$set,'id_commandes_detail='.intval($id_commandes_detail));
		}
	}

	return true;
}
