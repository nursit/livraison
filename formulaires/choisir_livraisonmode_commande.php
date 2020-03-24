<?php
/**
 * Gestion du formulaire pour choisir le mode de livraison d'une commande
 *
 * Ce formulaire est une alternative à adresser_commande :
 * c'est une version simplifiée qui ne traite que le choix du mode de livraison.
 * Dans ce cas, on est censé avoir défini l'adresse de livraion en amont.
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\Formulaires
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Charger les valeurs
 *
 * @param integer $id_commande
 * @param string $redirect
 * @param array $options
 *     Tableau d'options :
 *     - pays
 *     - code_postal
 *     - titre_suite : intitulé du bouton pour continuer
 * @return void
 */
function formulaires_choisir_livraisonmode_commande_charger_dist($id_commande, $redirect = '', $options = array()) {

	// Il faut avoir une commande en cours
	if (
		!$id_commande
		or !$commande = sql_fetsel('*', 'spip_commandes', 'id_commande='.intval($id_commande))
	) {
		return false;
	}

	include_spip('inc/livraison');

	// Soit on a le pays et le code postal dans les options,
	// soit on les récupère dans la commande
	if (
		!isset($options['pays'])
		and !isset($options['code_postal'])
	) {
		if ($commande['livraison_nom']) {
			$pays        = $commande['livraison_adresse_pays'];
			$code_postal = $commande['livraison_adresse_cp'];
		} else {
			$pays        = $commande['facturation_adresse_pays'];
			$code_postal = $commande['facturation_adresse_cp'];
		}
	} else {
		$pays        = $options['pays'];
		$code_postal = $options['code_postal'];
	}
	if (!$pays or !$code_postal) {
		return false;
	}

	// Trouver les modes de livraison dispos et leurs prix en fonction de l'adresse
	if ($livraison_necessaire = commande_livraison_necessaire($id_commande)) {
		$choix_livraisonmode = commande_trouver_livraisons_possibles($id_commande, $pays, $code_postal);
	}

	// Pas de mode de livraison, pas de chocolat
	if (
		$livraison_necessaire
		and !$choix_livraisonmode
	) {
		$valeurs['message_erreur'] = _T('livraison:erreur_adresse_non_livrable');
		$valeurs['editable'] = false;
	} else {
		$valeurs['id_commande'] = $id_commande;
		$valeurs['_url_suite'] = $redirect;
		$valeurs['_titre_suite'] = (!empty($options['titre_suite']) ? $options['titre_suite'] : '');
		$valeurs['_livraison_necessaire'] = $livraison_necessaire;
		$valeurs['_choix_livraisonmode'] = $choix_livraisonmode;
	}

	// Gestion du cache
	$valeurs['_hash'] = md5(serialize(sql_allfetsel('id_commandes_detail,prix_unitaire_ht,taxe,objet,id_objet,quantite', 'spip_commandes_details', 'id_commande='.intval($id_commande))));

	return $valeurs;
}

/**
 * Vérifier les valeurs postées
 *
 * @param integer $id_commande
 * @param string $redirect
 * @param array $options
 *     Tableau d'options :
 *     - code_pays
 *     - code_postal
 *     - titre_suite : intitulé du bouton pour continuer
 * @return void
 */
function formulaires_choisir_livraisonmode_commande_verifier_dist($id_commande, $redirect = '', $options = array()) {
	$erreurs = array();
	return $erreurs;
}

/**
 * Traitement
 *
 * @param integer $id_commande
 * @param string $redirect
 * @param array $options
 *     Tableau d'options :
 *     - code_pays
 *     - code_postal
 *     - titre_suite : intitulé du bouton pour continuer
 * @return void
 */
function formulaires_choisir_livraisonmode_commande_traiter_dist($id_commande, $redirect = '', $options = array()) {

	$res = array();

	if ($choixmode = _request('choixmode')) {
		include_spip('inc/livraison');
		$choixmode = array_keys($choixmode);
		$choixmode = reset($choixmode);
		$fixer = fixer_livraison_commande($id_commande, $choixmode, $options);
		$res['message_ok'] = _T('livraison:info_livraisonmode_enregistre');
		$res['editable'] = true;
	}

	if ($redirect) {
		include_spip('inc/filtres');
		$redirect = parametre_url($redirect, 'id_livraisonmode', $choixmode);
		$res['redirect'] = $redirect;
	}

	return $res;
}
