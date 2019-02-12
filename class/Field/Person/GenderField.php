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

namespace Docalist\People\Field\Person;

use Docalist\Type\TableEntry;

/**
 * Champ "gender" pour les entités "person" : genre ou sexe de la personne.
 *
 * Le champ est associé à une table d'autorité qui indique les entrées disponibles
 * ("table:person-gender" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class GenderField extends TableEntry
{
    public static function loadSchema()
    {
        return [
            'name' => 'gender',
            'label' => __('Genre', 'docalist-people'),
            'description' => __('Genre de la personne.', 'docalist-people'),
            'table' => 'table:person-gender',
            'editor' => 'radio-inline',
        ];
    }
}
