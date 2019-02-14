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

use Docalist\People\Entity\OrganizationEntity;
use Docalist\People\Entity\PlaceEntity;
use Docalist\People\Entity\PersonEntity;

/**
 * Plugin docalist-people.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class Plugin
{
    /**
     * Initialise le plugin.
     */
    public function __construct()
    {
        // Déclare nos assets
        require_once dirname(__DIR__) . '/assets/register.php';

        // Déclare les entités définies dans ce plugin
        add_filter('docalist_databases_get_types', function (array $types) {
            return $types + [
                'organization' => OrganizationEntity::class,
                'place' => PlaceEntity::class,
                'person' => PersonEntity::class,
            ];
        });
    }
}
