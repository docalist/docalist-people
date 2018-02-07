<?php
/**
 * This file is part of Docalist People.
 *
 * Copyright (C) 2017-2018 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
namespace Docalist\People;

use Docalist\Table\TableManager;
use Docalist\Table\TableInfo;

/**
 * Installation/désinstallation de docalist-people.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class Installer
{
    /**
     * Activation : enregistre les tables prédéfinies.
     */
    public function activate()
    {
        $tableManager = docalist('table-manager'); /** @var TableManager $tableManager */

        // Enregistre les tables prédéfinies
        foreach ($this->getTables() as $name => $table) {
            $table['name'] = $name;
            $table['path'] = strtr($table['path'], '/', DIRECTORY_SEPARATOR);
            $table['lastupdate'] = date_i18n('Y-m-d H:i:s', filemtime($table['path']));
            $tableManager->register(new TableInfo($table));
        }
    }

    /**
     * Désactivation : supprime les tables prédéfinies.
     */
    public function deactivate()
    {
        $tableManager = docalist('table-manager'); /** @var TableManager $tableManager */

        // Supprime les tables prédéfinies
        foreach (array_keys($this->getTables()) as $table) {
            $tableManager->unregister($table);
        }
    }

    /**
     * Retourne la liste des tables prédéfinies.
     *
     * @return array
     */
    protected function getTables()
    {
        return $this->getTablesForOrganization();
            // + $this->getTablesForPerson();
    }

    /**
     * Tables spécifiques à l'entité Organization.
     *
     * @return array
     */
    protected function getTablesForOrganization()
    {
        $dir = DOCALIST_PEOPLE_DIR . '/tables/organization/';

        return [
            'organization-content' => [
                'path' => $dir . 'organization-content.txt',
                'label' => __('Organisme - Contenus', 'docalist-people'),
                'format' => 'table',
                'type' => 'content',
                'creation' => '2016-01-06 22:05:12',
            ],
            'organization-figure' => [
                'path' => $dir . 'organization-figure.txt',
                'label' => __('Organisme - Chiffres clés', 'docalist-people'),
                'format' => 'table',
                'type' => 'figures',
                'creation' => '2016-02-22 14:57:49',
            ],
            'organization-name' => [
                'path' => $dir . 'organization-name.txt',
                'label' => __('Organisme - Noms', 'docalist-people'),
                'format' => 'table',
                'type' => 'organization-name',
                'creation' => '2016-06-01 21:50:36',
            ],
            'organization-number' => [
                'path' => $dir . 'organization-number.txt',
                'label' => __('Organisme - Numéros', 'docalist-people'),
                'format' => 'table',
                'type' => 'numbers',
                'creation' => '2016-01-07 00:20:32',
            ],
            'organization-relation' => [
                'path' => $dir . 'organization-relation.txt',
                'label' => __('Organisme - Organismes liés', 'docalist-people'),
                'format' => 'table',
                'type' => 'relations',
                'creation' => '2016-01-07 14:29:43',
            ],
            'organization-status' => [ // type d'indexation pour topic
                'path' => $dir . 'organization-status.txt',
                'label' => __('Organisme - Statut légal', 'docalist-people'),
                'format' => 'table',
                'type' => 'thesaurus',
                'creation' => '2016-01-06 23:14:51',
            ],
            'organization-topic' => [
                'path' => $dir . 'organization-topic.txt',
                'label' => __('Organisme - Indexation', 'docalist-people'),
                'format' => 'table',
                'type' => 'topics',
                'creation' => '2016-01-06 22:10:51',
            ],
            'organization-type' => [ // type d'indexation pour topic
                'path' => $dir . 'organization-type.txt',
                'label' => __('Organisme - Types', 'docalist-people'),
                'format' => 'table',
                'type' => 'thesaurus',
                'creation' => '2016-01-06 23:14:51',
            ],
        ];
    }
}
