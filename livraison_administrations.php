<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Livraison
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cédric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Livraison.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function livraison_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_livraisonmodes')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_livraisonmodes')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Livraison.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function livraison_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_livraisonmodes");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('livraisonmode')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('livraisonmode')));
	sql_delete("spip_forum",                 sql_in("objet", array('livraisonmode')));

	effacer_meta($nom_meta_base_version);
}

?>