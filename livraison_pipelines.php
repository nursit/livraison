<?php
/**
 * Utilisations de pipelines par Livraison
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cédric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

function livraison_afficher_contenu_objet($flux){

	if ($flux['args']['type']=='commande'
	  AND $id_commande = $flux['args']['id_objet']){

		$adresse = recuperer_fond("prive/objets/contenu/commande-adresse_livraison",array('id_commande'=>$id_commande));

		if ($p = strpos($flux['data'],"</table>")){
			$flux['data'] = substr_replace($flux['data'],$adresse,$p+8,0);
		}
		else {
			$flux['data'] .= $adresse;
		}
	}
	return $flux;
}


?>