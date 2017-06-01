<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Livraison
 * @copyright  2015
 * @author     Cédric
 * @licence    GNU/GPL
 * @package    SPIP\Livraison\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function livraison_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['livraisonmodes'] = 'livraisonmodes';
	$interfaces['table_des_tables']['livraisonmodes_liens'] = 'livraisonmodes_liens';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function livraison_declarer_tables_objets_sql($tables) {

	$tables['spip_livraisonmodes'] = array(
		'type' => 'livraisonmode',
		'principale' => "oui",
		'field'=> array(
			"id_livraisonmode"   => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"descriptif"         => "longtext NOT NULL DEFAULT ''",
			"zone_pays"          => "text NOT NULL DEFAULT ''",
			"zone_pays_exclus"   => "text NOT NULL DEFAULT ''",
			"zone_cp"            => "text NOT NULL DEFAULT ''",
			"zone_cp_exclus"     => "text NOT NULL DEFAULT ''",
			"taxe"               => "varchar(25) NOT NULL DEFAULT ''",
			"prix_forfait_ht"    => "float not null default 0",
			"prix_unit_ht"       => "float not null default 0",
			"prix_poids_ht"      => "text NOT NULL DEFAULT ''",
			"prix_volume_ht"     => "text NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_livraisonmode",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'descriptif', 'zone_pays', 'zone_pays_exclus', 'zone_cp', 'zone_cp_exclus', 'taxe', 'prix_forfait_ht', 'prix_unit_ht', 'prix_poids_ht', 'prix_volume_ht'),
		'champs_versionnes' => array('titre', 'descriptif', 'zone_pays', 'zone_pays_exclus', 'zone_cp', 'zone_cp_exclus', 'taxe', 'prix_forfait_ht', 'prix_unit_ht', 'prix_poids_ht', 'prix_volume_ht'),
		'rechercher_champs' => array('titre'=>4,'descriptif'=>1),
		'tables_jointures'  => array('spip_livraisonmodes_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'livraisonmode:texte_changer_statut_livraisonmode', 
		

	);

	// ajouter les champs a la commande
	$tables['spip_commandes']['field']['livraison_nom'] =	"varchar(200) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['livraison_societe'] =	"varchar(200) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['livraison_adresse'] =	"text NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['livraison_adresse_cp'] =	"varchar(15) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['livraison_adresse_ville'] =	"varchar(100) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['livraison_adresse_pays'] =	"varchar(5) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['livraison_telephone'] =	"varchar(25) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_nom'] =	"varchar(200) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_societe'] =	"varchar(200) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_adresse'] =	"text NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_adresse_cp'] =	"varchar(15) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_adresse_ville'] =	"varchar(100) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_adresse_pays'] =	"varchar(5) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_telephone'] =	"varchar(25) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['field']['facturation_no_tva_intra'] =	"varchar(25) NOT NULL DEFAULT ''";
	$tables['spip_commandes']['champs_editables'][] = 'livraison_nom';
	$tables['spip_commandes']['champs_editables'][] = 'livraison_societe';
	$tables['spip_commandes']['champs_editables'][] = 'livraison_adresse';
	$tables['spip_commandes']['champs_editables'][] = 'livraison_adresse_cp';
	$tables['spip_commandes']['champs_editables'][] = 'livraison_adresse_ville';
	$tables['spip_commandes']['champs_editables'][] = 'livraison_adresse_pays';
	$tables['spip_commandes']['champs_editables'][] = 'livraison_telephone';
	$tables['spip_commandes']['champs_versionnes'][] = 'livraison_nom';
	$tables['spip_commandes']['champs_versionnes'][] = 'livraison_societe';
	$tables['spip_commandes']['champs_versionnes'][] = 'livraison_adresse';
	$tables['spip_commandes']['champs_versionnes'][] = 'livraison_adresse_cp';
	$tables['spip_commandes']['champs_versionnes'][] = 'livraison_adresse_ville';
	$tables['spip_commandes']['champs_versionnes'][] = 'livraison_adresse_pays';
	$tables['spip_commandes']['champs_versionnes'][] = 'livraison_telephone';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_nom';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_societe';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_adresse';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_adresse_cp';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_adresse_ville';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_adresse_pays';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_telephone';
	$tables['spip_commandes']['champs_editables'][] = 'facturation_no_tva_intra';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_nom';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_societe';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_adresse';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_adresse_cp';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_adresse_ville';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_adresse_pays';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_telephone';
	$tables['spip_commandes']['champs_versionnes'][] = 'facturation_no_tva_intra';

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function livraison_declarer_tables_auxiliaires($tables) {

	$tables['spip_livraisonmodes_liens'] = array(
		'field' => array(
			'id_livraisonmode'     => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'             => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'                => 'VARCHAR(25) DEFAULT "" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'          => 'id_livraisonmode,id_objet,objet',
			'KEY id_livraisonmode' => 'id_livraisonmode',
		)
	);

	return $tables;
}