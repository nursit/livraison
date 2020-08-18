<?php
/**
 * Gestion du formulaire pour definir l'adresse de livraison/facturation de la commande
 * et le mode de livraison
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_adresser_commande_charger_dist($id_commande, $url_suite='', $titre_suite=''){

	// il faut avoir une commande en cours
	if (!$id_commande
	  OR !$commande = sql_fetsel("*","spip_commandes","id_commande=".intval($id_commande))){
		return 	false;
	}

	$valeurs = array(
		'livraison_nom'=>$commande['livraison_nom'],
		'livraison_societe'=>$commande['livraison_societe'],
		'livraison_adresse'=>$commande['livraison_adresse'],
		'livraison_adresse_cp'=>$commande['livraison_adresse_cp'],
		'livraison_adresse_ville'=>$commande['livraison_adresse_ville'],
		'livraison_adresse_pays'=>$commande['livraison_adresse_pays'],
		'livraison_telephone'=>$commande['livraison_telephone'],
		'modif' => '',
	);

	// si une des infos est manquante, ouvrir le formulaire en edition de l'adresse
	$search_adresse = false;
	if (!$valeurs['livraison_nom']
		OR !$valeurs['livraison_adresse']
		OR !$valeurs['livraison_adresse_cp']
		OR !$valeurs['livraison_adresse_ville']
		OR !$valeurs['livraison_adresse_pays']){
		$valeurs['modif'] = ' ';
		$search_adresse = true;
	}

	// si aucune info adresse est renseignee dans la commande,
	// on recupere l'adresse depuis l'auteur si possible pour pre-remplir le formulaire
	if ($search_adresse
	  AND $commande['id_auteur']
	  AND $renseigner_adresse_auteur = charger_fonction("renseigner_adresse_auteur","inc",true)){
		if ($adresse = $renseigner_adresse_auteur($commande['id_auteur'])){
			foreach (array('nom',
				         'societe',
				         'adresse',
				         'adresse_cp',
				         'adresse_ville',
				         'adresse_pays',
				         'telephone') as $champ){
				if (isset($adresse[$champ]) AND $adresse[$champ]){
					$valeurs["livraison_".$champ] = $adresse[$champ];
				}
			}
		}
	}
	// sinon chercher dans une commande precedente ?
	if ($search_adresse
		AND (!$valeurs['livraison_nom']
		OR !$valeurs['livraison_adresse']
		OR !$valeurs['livraison_adresse_cp']
		OR !$valeurs['livraison_adresse_ville']
		OR !$valeurs['livraison_adresse_pays'])
	  AND $commande['id_auteur']
		AND $row = sql_fetsel('livraison_nom,livraison_societe,livraison_adresse,livraison_adresse_cp,livraison_adresse_ville,livraison_adresse_pays,livraison_telephone','spip_commandes','id_auteur='.intval($commande['id_auteur'])." AND livraison_adresse<>''","","date DESC","0,1")){
		foreach($row as $k=>$v){
			if (isset($row[$k]) AND $row[$k]){
				$valeurs[$k] = $row[$k];
			}
		}
	}


	// l'adresse de facturation, qui peut etre vide
	// (dans ce cas on considere qu'elle est identique a la livraison)
	$valeurs['facturation_nom'] = $commande['facturation_nom'];
	$valeurs['facturation_societe'] = $commande['facturation_societe'];
	$valeurs['facturation_adresse'] = $commande['facturation_adresse'];
	$valeurs['facturation_adresse_cp'] = $commande['facturation_adresse_cp'];
	$valeurs['facturation_adresse_ville'] = $commande['facturation_adresse_ville'];
	$valeurs['facturation_adresse_pays'] = $commande['facturation_adresse_pays'];
	$valeurs['facturation_telephone'] = $commande['facturation_telephone'];
	$valeurs['facturation_identique_livraison'] = '';
	if (!trim($valeurs['facturation_nom'])){
		$valeurs['facturation_identique_livraison'] = 'oui';
	}

	$valeurs['_id_commande'] = $id_commande;
	$valeurs['_url_suite'] = $url_suite;
	$valeurs['_titre_suite'] = $titre_suite;
	$valeurs['_choix_livraisonmode'] = array();
	include_spip('inc/livraison');
	$valeurs['_livraison_necessaire'] = (commande_livraison_necessaire($id_commande)?' ':'');
	$valeurs['_telephone_obligatoire'] = lire_config('livraison/telephone_obligatoire') == "on" ? $valeurs['_livraison_necessaire'] : 0;

	if (!$valeurs['modif'] AND $valeurs['_livraison_necessaire']) {
		// trouver les modes de livraison dispo et leurs prix, en fonction de l'adresse
		include_spip('inc/livraison');
		$valeurs['_choix_livraisonmode'] = commande_trouver_livraisons_possibles($id_commande, $valeurs['livraison_adresse_pays'], $valeurs['livraison_adresse_cp']);

		// si un seul mode possible l'affecter directement sans passer par l'etape choix du mode
		if (count($valeurs['_choix_livraisonmode'])==1 AND !$valeurs['modif']){
			include_spip('inc/livraison');
			$arg = array_keys($valeurs['_choix_livraisonmode']);
			fixer_livraison_commande($id_commande,reset($arg));
		}

	}

	// gestion du cache
	$valeurs['_hash'] = md5(serialize(sql_allfetsel("id_commandes_detail,prix_unitaire_ht,taxe,objet,id_objet,quantite","spip_commandes_details","id_commande=".intval($id_commande))));

	return $valeurs;
}

function formulaires_adresser_commande_verifier_dist($id_commande, $url_suite='', $titre_suite=''){
	$erreurs = array();

	if (_request('modif')){
		$erreurs['dummy'] = ' '; // forcer la resaisie en mode modification
		$erreurs['message_erreur'] = '';
		// vider ce get pour retomber sur la valeur par defaut
		set_request('facturation_identique_livraison');
		return $erreurs;
	}

    include_spip('inc/livraison');
	$livraison_necessaire = commande_livraison_necessaire($id_commande);

	$oblis = array(
		'livraison_nom',
		'livraison_adresse',
		'livraison_adresse_cp',
		'livraison_adresse_ville',
		'livraison_adresse_pays'
	);
	if ($livraison_necessaire && lire_config('livraison/telephone_obligatoire') == "on") {
		$oblis[] = 'livraison_telephone';
	}
	if (_request('facturation_identique_livraison')!=='oui'){
		$oblis[] = 'facturation_nom';
		$oblis[] = 'facturation_adresse';
		$oblis[] = 'facturation_adresse_cp';
		$oblis[] = 'facturation_adresse_ville';
		$oblis[] = 'facturation_adresse_pays';
	}

	foreach ($oblis as $obli){
		if (!strlen(trim(_request($obli)))){
			$erreurs[$obli] = _T('livraison:erreur_' . $obli . '_obligatoire');
			set_request('modif',' ');
		}
	}

	if (isset($erreurs['facturation_nom']) AND isset($erreurs['facturation_adresse'])){
		$erreurs['message_erreur'] = _T('livraison:erreur_facturation_adresse_obligatoire');
		unset($erreurs['facturation_nom']);
		unset($erreurs['facturation_adresse']);
		unset($erreurs['facturation_adresse_cp']);
		unset($erreurs['facturation_adresse_ville']);
		unset($erreurs['facturation_adresse_pays']);
	}

	// TODO : verifier la validite du CP et du pays ?
    
	return $erreurs;
}

function formulaires_adresser_commande_traiter_dist($id_commande, $url_suite='', $titre_suite=''){
	include_spip('inc/livraison');
	$res = array();

	// mettre a jour l'adresse de livraison de la commande
	if (_request('save')){
		// adresse de facturation identique a la livraison ? on laisse vide
		if (_request('facturation_identique_livraison')==='oui'){
			set_request('facturation_nom','');
			set_request('facturation_societe','');
			set_request('facturation_adresse','');
			set_request('facturation_adresse_cp','');
			set_request('facturation_adresse_ville','');
			set_request('facturation_adresse_pays','');
			set_request('facturation_telephone','');
		}


		include_spip('inc/editer');
		$res = formulaires_editer_objet_traiter('commande', $id_commande);
		// mettre a jour le cout de livraison existant
		fixer_livraison_commande($id_commande);
		if (isset($res['message_ok'])){
			$res['message_ok'] = _T('livraison:info_adresse_enregistree');
		}
	}

	if ($choixmode = _request('choixmode')){
		$choixmode = array_keys($choixmode);
		$choixmode = reset($choixmode);
		fixer_livraison_commande($id_commande,$choixmode);
		$res['message_ok'] = _T('livraison:info_livraisonmode_enregistre');
	}

	if (_request('resetlivraison')){
		reset_livraison_commande($id_commande);
	}

	return $res;
}
