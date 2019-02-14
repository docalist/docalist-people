<?php
/**
 * This file is part of Docalist People.
 *
 * Copyright (C) 2017-2019 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
declare(strict_types=1);

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
        $tableManager = docalist('table-manager'); /* @var TableManager $tableManager */

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
        $tableManager = docalist('table-manager'); /* @var TableManager $tableManager */

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
        return $this->getCommonTables() + $this->getTablesForOrganization() + $this->getTablesForPerson();
    }

    /**
     * Tables communes à différents types d'entités.
     *
     * @return array
     */
    protected function getCommonTables()
    {
        $dir = DOCALIST_PEOPLE_DIR . '/tables/';

        return [
            'name-type' => [
                'path' => $dir . 'name-type.txt',
                'label' => __('Nom - Exemple de table "types de noms"', 'docalist-people'),
                'format' => 'table',
                'type' => 'name-type',
                'creation' => '2015-12-08 17:04:05',
            ],
        ];
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
            'org-person-relation' => [
                'path' => $dir . 'org-person-relation.txt',
                'label' => __('Structure - Relations avec des personnes', 'docalist-people'),
                'format' => 'table',
                'type' => 'relation-type',
                'creation' => '2016-01-07 15:02:44',
            ],
            'org-org-relation' => [
                'path' => $dir . 'org-org-relation.txt',
                'label' => __("Structure - Relations avec d'autres structures", 'docalist-people'),
                'format' => 'table',
                'type' => 'relation-type',
                'creation' => '2016-01-07 14:29:43',
            ],
        ];
    }

    /**
     * Tables spécifiques à l'entité Person.
     *
     * @return array
     */
    protected function getTablesForPerson()
    {
        $dir = DOCALIST_PEOPLE_DIR . '/tables/person/';

        return [
            'person-gender' => [
                'path' => $dir . 'person-gender.txt',
                'label' => __('Personne - Genre', 'docalist-people'),
                'format' => 'table',
                'type' => 'person-gender',
                'creation' => '2015-12-08 15:37:05',
            ],
            'person-person-relation' => [
                'path' => $dir . 'person-person-relation.txt',
                'label' => __("Personne - Relations avec d'autres personnes", 'docalist-people'),
                'format' => 'table',
                'type' => 'relation-type',
                'creation' => '2016-01-07 15:02:44',
            ],
        ];
    }
}
