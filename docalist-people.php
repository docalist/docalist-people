<?php
/**
 * This file is part of Docalist People.
 *
 * Copyright (C) 2017-2019 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 *
 * Plugin Name: Docalist People
 * Plugin URI:  https://docalist.org/
 * Description: Entités "organization", "place" et "person" pour docalist.
 * Version:     3.0.1
 * Author:      Daniel Ménard
 * Author URI:  https://docalist.org/
 * Text Domain: docalist-people
 * Domain Path: /languages
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
declare(strict_types=1);

namespace Docalist\People;

/**
 * Version du plugin.
 */
define('DOCALIST_PEOPLE_VERSION', '3.0.1'); // Garder synchro avec la version indiquée dans l'entête

/**
 * Path absolu du répertoire dans lequel le plugin est installé.
 *
 * Par défaut, on utilise la constante magique __DIR__ qui retourne le path réel du répertoire et résoud les liens
 * symboliques.
 *
 * Si le répertoire du plugin est un lien symbolique, la constante doit être définie manuellement dans le fichier
 * wp_config.php et pointer sur le lien symbolique et non sur le répertoire réel.
 */
!defined('DOCALIST_PEOPLE_DIR') && define('DOCALIST_PEOPLE_DIR', __DIR__);

/**
 * Path absolu du fichier principal du plugin.
 */
define('DOCALIST_PEOPLE', DOCALIST_PEOPLE_DIR . DIRECTORY_SEPARATOR . basename(__FILE__));

/**
 * Url de base du plugin.
 */
define('DOCALIST_PEOPLE_URL', plugins_url('', DOCALIST_PEOPLE));

/**
 * Initialise le plugin.
 */
add_action('plugins_loaded', function () {
    // Auto désactivation si les plugins dont on a besoin ne sont pas activés
    $dependencies = ['DOCALIST_DATA'];
    foreach ($dependencies as $dependency) {
        if (! defined($dependency)) {
            add_action('admin_notices', function () use ($dependency) {
                deactivate_plugins(DOCALIST_PEOPLE);
                unset($_GET['activate']); // empêche wp d'afficher "extension activée"
                printf(
                    '<div class="%s"><p><b>%s</b> has been deactivated because it requires <b>%s</b>.</p></div>',
                    'notice notice-error is-dismissible',
                    'Docalist People',
                    ucwords(strtolower(strtr($dependency, '_', ' ')))
                );
            });
            return;
        }
    }

    // Ok
    docalist('autoloader')
        ->add(__NAMESPACE__, __DIR__ . '/class')
        ->add(__NAMESPACE__ . '\Tests', __DIR__ . '/tests');

    docalist('services')->add('docalist-people', new Plugin());
});

/*
 * Activation du plugin.
 */
register_activation_hook(DOCALIST_PEOPLE, function () {
    // Si docalist-core n'est pas dispo, on ne peut rien faire
    if (defined('DOCALIST_CORE')) {
        // plugins_loaded n'a pas encore été lancé, donc il faut initialiser notre autoloader
        docalist('autoloader')->add(__NAMESPACE__, __DIR__ . '/class');
        (new Installer())->activate();
    }
});

/*
 * Désactivation du plugin.
*/
register_deactivation_hook(DOCALIST_PEOPLE, function () {
    // Si docalist-core n'est pas dispo, on ne peut rien faire
    if (defined('DOCALIST_CORE')) {
        docalist('autoloader')->add(__NAMESPACE__, __DIR__ . '/class');
        (new Installer())->deactivate();
    }
});
