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

	$maj['create'] = array(
		array('maj_tables', array('spip_livraisonmodes','spip_commandes')),
		array('livraison_installer_modes'),
	);

	$maj['1.0.1'] = array(
		array('maj_tables', array('spip_livraisonmodes')),
	);
	$maj['1.1.1'] = array(
		array('maj_tables', array('spip_commandes')),
	);

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
	livraison_generer_csv_installation();

	sql_drop_table("spip_livraisonmodes");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('livraisonmode')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('livraisonmode')));
	sql_delete("spip_forum",                 sql_in("objet", array('livraisonmode')));

	effacer_meta($nom_meta_base_version);
}

function livraison_installer_modes(){
	include_spip("action/editer_objet");
	if ($importer_csv = charger_fonction("importer_csv","inc",true)
	  AND $f = find_in_path("base/livraisonmodes.csv")){
		$modes = $importer_csv($f,true);

		foreach($modes as $mode){
			$id = objet_inserer("livraisonmode");
			$set = $mode;
			$set['prix_poids_ht'] = preg_replace(",\s+,","\n",$set['prix_poids_ht']);
			$set['prix_volume_ht'] = preg_replace(",\s+,","\n",$set['prix_volume_ht']);
			$set['statut'] = 'prop';
			objet_modifier("livraisonmode",$id,$set);
		}
	}
}

function livraison_generer_csv_installation(){
	if ($exporter_csv = charger_fonction("exporter_csv","inc",true)){
		$champs = array(
			"titre","descriptif","zone_pays","zone_cp","zone_cp_exclus","taxe","prix_forfait_ht","prix_unit_ht","prix_poids_ht","prix_volume_ht"
		);
		$modes = sql_allfetsel(implode(",",$champs),"spip_livraisonmodes","statut='publie'");
		$exporter_csv("livraisonmodes",$modes,",",$champs,false);
	}
}
