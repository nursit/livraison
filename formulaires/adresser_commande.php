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


function formulaires_adresser_commande_charger_dist($id_commande, $retour=''){

	$commande = sql_fetsel("*","spip_commandes","id_commande=".intval($id_commande));

	$valeurs = array(
		'_id_commande'=>$id_commande,
		'modif' => '',
		'livraison_nom'=>'',
		'livraison_societe'=>'',
		'livraison_adresse'=>'',
		'livraison_adresse_cp'=>'',
		'livraison_adresse_ville'=>'',
		'livraison_adresse_pays'=>'',
		'livraison_telephone'=>'',
	);

	if ($commande['id_auteur']
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

	if (!$valeurs['livraison_nom']
		OR !$valeurs['livraison_adresse']
		OR !$valeurs['livraison_adresse_cp']
		OR !$valeurs['livraison_adresse_ville']
		OR !$valeurs['livraison_adresse_pays']){
		$valeurs['modif'] = ' ';
	}

	#var_dump($valeurs);

	return $valeurs;
}