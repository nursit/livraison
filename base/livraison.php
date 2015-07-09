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
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"texte"              => "longtext NOT NULL DEFAULT ''",
			"zone"               => "text NOT NULL DEFAULT ''",
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
		'champs_editables'  => array('titre', 'texte', 'zone', 'taxe', 'prix_forfait_ht', 'prix_unit_ht', 'prix_poids_ht', 'prix_volume_ht'),
		'champs_versionnes' => array('titre', 'texte', 'zone', 'taxe', 'prix_forfait_ht', 'prix_unit_ht', 'prix_poids_ht', 'prix_volume_ht'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
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

	return $tables;
}



?>