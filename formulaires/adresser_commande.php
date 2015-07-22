<?php
/**
 * Gestion du formulaire pour definir l'adresse de livraison/facturation de la commande
 * et le mode de livraison
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cédric
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
	if (!$valeurs['livraison_nom']
		OR !$valeurs['livraison_adresse']
		OR !$valeurs['livraison_adresse_cp']
		OR !$valeurs['livraison_adresse_ville']
		OR !$valeurs['livraison_adresse_pays']){
		$valeurs['modif'] = ' ';
	}

	// si aucune info adresse est renseignee dans la commande,
	// on recupere l'adresse depuis l'auteur si possible pour pre-remplir le formulaire
	if (!strlen(trim(implode('',$valeurs)))
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

	$valeurs['_id_commande'] = $id_commande;
	$valeurs['_url_suite'] = $url_suite;
	$valeurs['_titre_suite'] = $titre_suite;
	$valeurs['_id_livraisonmode'] = array();

	if (!$valeurs['modif']) {
		// trouver les modes de livraison dispo et leurs prix, en fonction de l'adresse
		include_spip('inc/livraison');
		$ids = sql_allfetsel("id_livraisonmode","spip_livraisonmodes","statut=".sql_quote('publie'));
		$ids = array_map('reset',$ids);
		foreach($ids as $id){
			if ($cout = livraison_calculer_cout($id_commande,$id,$valeurs['livraison_adresse_pays'],$valeurs['livraison_adresse_cp'])){
				list($prix_ht,$taxe) = $cout;
				$valeurs['_id_livraisonmode'][$id] = round($prix_ht + $prix_ht * $taxe,2);
			}
		}

		// si un seul mode possible l'affecter directement sans passer par l'etape choix du mode
		if (count($valeurs['_id_livraisonmode'])==1 AND !$valeurs['modif']){
			include_spip('inc/livraison');
			fixer_livraison_commande($id_commande,reset(array_keys($valeurs['_id_livraisonmode'])));
		}
	}

	// gestion du cache
	$valeurs['_hash'] = md5(serialize(sql_allfetsel("id_commandes_detail,prix_unitaire_ht,taxe,objet,id_objet,quantite","spip_commandes_details","id_commande=".intval($id_commande))));

	return $valeurs;
}

function formulaires_adresser_commande_verifier_dist($id_commande, $url_suite='', $titre_suite=''){
	$erreurs = array();

	$oblis = array(
		'livraison_nom',
		'livraison_adresse',
		'livraison_adresse_cp',
		'livraison_adresse_ville',
		'livraison_adresse_pays'
	);

	foreach ($oblis as $obli){
		if (!strlen(trim(_request($obli)))){
			$erreurs[$obli] = _T('livraison:erreur_' . $obli . '_obligatoire');
			set_request('modif',' ');
		}
	}

	// TODO : verifier la validite du CP et du pays ?

	return $erreurs;
}

function formulaires_adresser_commande_traiter_dist($id_commande, $url_suite='', $titre_suite=''){
	include_spip('inc/livraison');
	$res = array();

	// mettre a jour l'adresse de livraison de la commande
	if (_request('save')){
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