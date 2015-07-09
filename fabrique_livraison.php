<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2015-07-09 16:46:39
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

$data = array (
  'fabrique' => 
  array (
    'version' => 5,
  ),
  'paquet' => 
  array (
    'nom' => 'Livraison',
    'slogan' => 'Frais de livraison des achats',
    'description' => '',
    'prefixe' => 'livraison',
    'version' => '1.0.0',
    'auteur' => 'Cédric',
    'auteur_lien' => 'http://www.nursit.com/',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.0;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => '',
    'fichiers' => 
    array (
      0 => 'fonctions',
      1 => 'options',
      2 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => '',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Modes de livraison',
      'nom_singulier' => 'Mode de livraison',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_livraisonmodes',
      'cle_primaire' => 'id_livraisonmode',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'livraisonmode',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Date',
          'champ' => 'date',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Descriptif',
          'champ' => 'descriptif',
          'sql' => 'longtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'rows=5',
        ),
        3 => 
        array (
          'nom' => 'Zone',
          'champ' => 'zone',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Liste des codes pays ISO livrables par ce mode, séparés par une virgule',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'TVA applicable',
          'champ' => 'taxe',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Prix Forfaitaire H.T.',
          'champ' => 'prix_forfait_ht',
          'sql' => 'float not null default 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Coût forfaitaire pour la livraison, indépendant du contenu',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Prix Unitaite H.T.',
          'champ' => 'prix_unit_ht',
          'sql' => 'float not null default 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Coût de la livraison par produit livré',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Prix au poids H.T.',
          'champ' => 'prix_poids_ht',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => 'Liste des tranches de prix, une tranche par ligne, au format <tt>Poids (g)|Prix HT</tt>',
          'saisie_options' => 'rows=5',
        ),
        8 => 
        array (
          'nom' => 'Prix au volume H.T.',
          'champ' => 'prix_volume_ht',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => 'Liste des tranches de prix, une tranche par ligne, au format <tt>Volume (cm3)|Prix HT</tt>',
          'saisie_options' => 'rows=5',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Modes de livraison',
        'titre_objet' => 'Mode de livraison',
        'info_aucun_objet' => 'Aucun mode de livraison',
        'info_1_objet' => 'Un mode de livraison',
        'info_nb_objets' => '@nb@ modes de livraison',
        'icone_creer_objet' => 'Créer un mode de livraison',
        'icone_modifier_objet' => 'Modifier ce mode de livraison',
        'titre_logo_objet' => 'Logo de ce mode de livraison',
        'titre_langue_objet' => 'Langue de ce mode de livraison',
        'texte_definir_comme_traduction_objet' => 'Ce mode de livraison est une traduction du mode de livraison numéro :',
        'titre_objets_rubrique' => 'Modes de livraison de la rubrique',
        'info_objets_auteur' => 'Les modes de livraison de cet auteur',
        'retirer_lien_objet' => 'Retirer ce mode de livraison',
        'retirer_tous_liens_objets' => 'Retirer tous les modes de livraison',
        'ajouter_lien_objet' => 'Ajouter ce mode de livraison',
        'texte_ajouter_objet' => 'Ajouter un mode de livraison',
        'texte_creer_associer_objet' => 'Créer et associer un mode de livraison',
        'texte_changer_statut_objet' => 'Ce mode de livraison est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/squelettes/contenu/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
      ),
    ),
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAESElEQVRYw+2XXYhVVRiGn2+tddbeM5io/QipmWkjhDmkmKZMoYLhTFSSYIFYGv2XhTfmRVBYWl1I+YNRoSlKdhEmpeJIhqVgaoUyFFM2DjH+5Tjj6PHozNl7rS72njMzNNqcIbILP1h3+32/97zv9629j3jvuZqlrmr3awL+DwIEMBsXVW7JS2kl9H4gSzj/1awl1TOAqBicAcJY96kcU/Egvd0IEajZs/kBIASyRQsQBQMGDeenbesQpRCRHoG993jnuKtyDkpg5sIVqwerptlyGSfr7bCnNr/x+BrAdRaglCiifCsqY1BKF/XrnYsTrFIMlJbZ94wbkwhLj4jgfdIvPnhoFbABuNRZAFprXL6NsdNn9iqC3LnWhMM7hg4dwmefb2HY0CHEzqFEaDh+krdfX4Tz2GkLls+pXjb/43YXDIAoRRzn8T6TetvDzmlS3sWIVngPra1tKKV5+onZRFGMUsKH6zZy7MQpJt87ib0HV74PrG93wQAoneR+5MCeZKKKKe8ZNHIsSiUbHcUxNrBcyF2krTWPSWM9duIkZcNvY3z5nWHdguVzdqQuqCQCgwjYklKCIo8tKUUEjEnci50jDEOiKMJ7TxzHWGtpPNNMPh9xX8VERpjGFYDtcEApBMgEAUiRd5N3gO/kgCMMAz7asAnnPForjNYE1gLQ97o+qKR5KXCpEIFSYDJhj1ewIwGPkmSQIdkKG1gWPPck2WyO7V9/S2PzGc7nLvDpF1+itG4Xazo5oAGPsbZXMwAe0RrvPc5DEIR8sH4TIoJSCmuDlFdQQYi3JQV4RwRKYTK2Y8BV1yi8c5fVIFqjVCLceU8YBu0k7WhAcGIoj3dyclt1VwFaa7QWbJgoExFEKyQl9c7jY1e4XLo0F0EpQSsNCKI0QViSilIF8c45YhQtv5zGdaIwABlr0IFFGQUiuChizZpN/NbQBMDtgwcwb96jKGNSy//ugLWG/qUGdAZjDD9u/ZQzp08BcP2NAxlT9RjWBjSPe55T2b3ww8IEC/QDyla9++b3gkNEqKmp4Y6RZUyYMg2Afbuq+bn2V0aNGoWPu4lCCRltyLY00a9/fw4fPkRZWRkTJt+f4L/ZQW1tLaNHlxeiEaWY+/KrgQFyQGPD0SOIJJY2N5+l/O6JvPfOEgCeeWE+e/ftp6G+rtsY2mvp6nUPvfbi3C1/NrUwc3wFy1L8sy+9wp59Bzha93sBmzKUGqANaFq6+pOxqRt2VtXU7RdzOTLGAHAxlyOOI95atXZ6+nx3lQVyi1eurZhVNfW7XDZbwOeyWaIoYvHKLvizhRlIXagjuSD6tebj+v27d9768CPJy2n/7p205uP69JmzlxHQlp6beohvA3LdLX1fYETVlEmbjdK3AEQu/mPrrr0zgCPAOa5cReG7E2CAG4CbUzJS0HGgkX/+5CoKL1cgKU0jKdjVg+b/Fv6/K7n21+yagKst4C+i6cFkwiEdCgAAAABJRU5ErkJggg==',
        ),
      ),
    ),
    'objets' => 
    array (
      0 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAESElEQVRYw+2XXYhVVRiGn2+tddbeM5io/QipmWkjhDmkmKZMoYLhTFSSYIFYGv2XhTfmRVBYWl1I+YNRoSlKdhEmpeJIhqVgaoUyFFM2DjH+5Tjj6PHozNl7rS72njMzNNqcIbILP1h3+32/97zv9629j3jvuZqlrmr3awL+DwIEMBsXVW7JS2kl9H4gSzj/1awl1TOAqBicAcJY96kcU/Egvd0IEajZs/kBIASyRQsQBQMGDeenbesQpRCRHoG993jnuKtyDkpg5sIVqwerptlyGSfr7bCnNr/x+BrAdRaglCiifCsqY1BKF/XrnYsTrFIMlJbZ94wbkwhLj4jgfdIvPnhoFbABuNRZAFprXL6NsdNn9iqC3LnWhMM7hg4dwmefb2HY0CHEzqFEaDh+krdfX4Tz2GkLls+pXjb/43YXDIAoRRzn8T6TetvDzmlS3sWIVngPra1tKKV5+onZRFGMUsKH6zZy7MQpJt87ib0HV74PrG93wQAoneR+5MCeZKKKKe8ZNHIsSiUbHcUxNrBcyF2krTWPSWM9duIkZcNvY3z5nWHdguVzdqQuqCQCgwjYklKCIo8tKUUEjEnci50jDEOiKMJ7TxzHWGtpPNNMPh9xX8VERpjGFYDtcEApBMgEAUiRd5N3gO/kgCMMAz7asAnnPForjNYE1gLQ97o+qKR5KXCpEIFSYDJhj1ewIwGPkmSQIdkKG1gWPPck2WyO7V9/S2PzGc7nLvDpF1+itG4Xazo5oAGPsbZXMwAe0RrvPc5DEIR8sH4TIoJSCmuDlFdQQYi3JQV4RwRKYTK2Y8BV1yi8c5fVIFqjVCLceU8YBu0k7WhAcGIoj3dyclt1VwFaa7QWbJgoExFEKyQl9c7jY1e4XLo0F0EpQSsNCKI0QViSilIF8c45YhQtv5zGdaIwABlr0IFFGQUiuChizZpN/NbQBMDtgwcwb96jKGNSy//ugLWG/qUGdAZjDD9u/ZQzp08BcP2NAxlT9RjWBjSPe55T2b3ww8IEC/QDyla9++b3gkNEqKmp4Y6RZUyYMg2Afbuq+bn2V0aNGoWPu4lCCRltyLY00a9/fw4fPkRZWRkTJt+f4L/ZQW1tLaNHlxeiEaWY+/KrgQFyQGPD0SOIJJY2N5+l/O6JvPfOEgCeeWE+e/ftp6G+rtsY2mvp6nUPvfbi3C1/NrUwc3wFy1L8sy+9wp59Bzha93sBmzKUGqANaFq6+pOxqRt2VtXU7RdzOTLGAHAxlyOOI95atXZ6+nx3lQVyi1eurZhVNfW7XDZbwOeyWaIoYvHKLvizhRlIXagjuSD6tebj+v27d9768CPJy2n/7p205uP69JmzlxHQlp6beohvA3LdLX1fYETVlEmbjdK3AEQu/mPrrr0zgCPAOa5cReG7E2CAG4CbUzJS0HGgkX/+5CoKL1cgKU0jKdjVg+b/Fv6/K7n21+yagKst4C+i6cFkwiEdCgAAAABJRU5ErkJggg==',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAESElEQVRYw+2XXYhVVRiGn2+tddbeM5io/QipmWkjhDmkmKZMoYLhTFSSYIFYGv2XhTfmRVBYWl1I+YNRoSlKdhEmpeJIhqVgaoUyFFM2DjH+5Tjj6PHozNl7rS72njMzNNqcIbILP1h3+32/97zv9629j3jvuZqlrmr3awL+DwIEMBsXVW7JS2kl9H4gSzj/1awl1TOAqBicAcJY96kcU/Egvd0IEajZs/kBIASyRQsQBQMGDeenbesQpRCRHoG993jnuKtyDkpg5sIVqwerptlyGSfr7bCnNr/x+BrAdRaglCiifCsqY1BKF/XrnYsTrFIMlJbZ94wbkwhLj4jgfdIvPnhoFbABuNRZAFprXL6NsdNn9iqC3LnWhMM7hg4dwmefb2HY0CHEzqFEaDh+krdfX4Tz2GkLls+pXjb/43YXDIAoRRzn8T6TetvDzmlS3sWIVngPra1tKKV5+onZRFGMUsKH6zZy7MQpJt87ib0HV74PrG93wQAoneR+5MCeZKKKKe8ZNHIsSiUbHcUxNrBcyF2krTWPSWM9duIkZcNvY3z5nWHdguVzdqQuqCQCgwjYklKCIo8tKUUEjEnci50jDEOiKMJ7TxzHWGtpPNNMPh9xX8VERpjGFYDtcEApBMgEAUiRd5N3gO/kgCMMAz7asAnnPForjNYE1gLQ97o+qKR5KXCpEIFSYDJhj1ewIwGPkmSQIdkKG1gWPPck2WyO7V9/S2PzGc7nLvDpF1+itG4Xazo5oAGPsbZXMwAe0RrvPc5DEIR8sH4TIoJSCmuDlFdQQYi3JQV4RwRKYTK2Y8BV1yi8c5fVIFqjVCLceU8YBu0k7WhAcGIoj3dyclt1VwFaa7QWbJgoExFEKyQl9c7jY1e4XLo0F0EpQSsNCKI0QViSilIF8c45YhQtv5zGdaIwABlr0IFFGQUiuChizZpN/NbQBMDtgwcwb96jKGNSy//ugLWG/qUGdAZjDD9u/ZQzp08BcP2NAxlT9RjWBjSPe55T2b3ww8IEC/QDyla9++b3gkNEqKmp4Y6RZUyYMg2Afbuq+bn2V0aNGoWPu4lCCRltyLY00a9/fw4fPkRZWRkTJt+f4L/ZQW1tLaNHlxeiEaWY+/KrgQFyQGPD0SOIJJY2N5+l/O6JvPfOEgCeeWE+e/ftp6G+rtsY2mvp6nUPvfbi3C1/NrUwc3wFy1L8sy+9wp59Bzha93sBmzKUGqANaFq6+pOxqRt2VtXU7RdzOTLGAHAxlyOOI95atXZ6+nx3lQVyi1eurZhVNfW7XDZbwOeyWaIoYvHKLvizhRlIXagjuSD6tebj+v27d9768CPJy2n/7p205uP69JmzlxHQlp6beohvA3LdLX1fYETVlEmbjdK3AEQu/mPrrr0zgCPAOa5cReG7E2CAG4CbUzJS0HGgkX/+5CoKL1cgKU0jKdjVg+b/Fv6/K7n21+yagKst4C+i6cFkwiEdCgAAAABJRU5ErkJggg==',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAB/UlEQVR42sXT309SYRgH8Pfv6M/wsi68yKj1w1rq+qHAwjTBsNUQrVMwa5gJjKzFRj9B3M6AIgThCBaM46GDWKlAU0ssc1ld1Vqty2/nfTc2LTcvvOjZPtv3fb7bc/cSANuy/QMh68FRH9cE76WGTQ1zjRiz1gXs508RxwXdBk5TKyH81ZMoJkcwN+7fVGl8BMG+Jhh6rsFotqLLbIFR0ckNLNIjJGhTY7WUg+AyQrh5bj22Wy1K7EChvAAhIyGRFkHH5roDlSNdQ54O6rBcSOLrYlYh/iXLunD/caSnZuDweGF3P8Tyx08oz79F+8XrJTI21IGlfBwyfwNMYJCpvpfkGGIOLYTJAtz+ANzDPHLTs/j2/Qc6u6+AJN1dqORjmEvcRVG4R7E8G/cwFXkUqdttiGZk/Pz1G74nUcYfEaDv7gPJPDBh5VUc7+XH+DAVpliu5IJ4JwWwMh1D9r4RUTGP8PNJRBgRkfQLSJ7TIC9DFnxZSIO/ZUHL0UMUy5/nn2GtnMLamwm8DnFwOe3QNexHq2LIOYDMTAmG3n4Qc4cWPYoT9fsgphIUy+YzGpja1DC1a9Cr16L58F5IE3GmpV4FTq/GZYMGRJka6tiB3YjyPobm6v7f3ruxt51tJo11O4mqdtejI3tqQdFMd7Tbql8/O6pXWWazdf//f+MfKy5/H5I5i7kAAAAASUVORK5CYII=',
          ),
        ),
      ),
    ),
  ),
);

?>