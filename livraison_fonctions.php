<?php
/**
 * Fonctions utiles au plugin Livraison
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cédric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function filtre_commande_livraison_necessaire_dist($id_commande){
	include_spip('inc/livraison');
	return (commande_livraison_necessaire($id_commande)?' ':'');
}

