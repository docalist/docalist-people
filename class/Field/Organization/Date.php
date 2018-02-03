<?php
/**
 * This file is part of Docalist People.
 *
 * Copyright (C) 2017-2018 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
namespace Docalist\People\Field\Organization;

use Docalist\Type\TypedFuzzyDate;

/**
 * Dates de l'organisme.
 *
 * Ce champ permet d'indiquer des dates clés pour la structure (date de création, date de fin...)
 *
 * Chaque date comporte deux sous-champs :
 * - `type` : type de date,
 * - `value` : date.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de dates disponibles
 * ("table:organization-name" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class Date extends TypedFuzzyDate
{
    public static function loadSchema()
    {
        return [
            'label' => __('Dates', 'docalist-people'),
            'description' => __('Dates.', 'docalist-people'),
            'fields' => [
                'type' => [
                    'table' => 'table:organization-date',
                    'editor' => 'select',
                ],
                'value' => [
                    'label' => __('Date', 'docalist-people'),
                ],
            ],
            'default' => [['type' => 'start']],
        ];
    }
}
