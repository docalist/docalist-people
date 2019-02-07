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

use Docalist\Data\Field\DateField as BaseDateField;

/**
 * Champ "date" pour les entités "person".
 *
 * Cette classe hérite simplement du champ standard de docalist-data et modifie les paramètres par défaut.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class DateField extends BaseDateField
{
    public static function loadSchema()
    {
        return [
            'fields' => [
                'type' => [
                    'table' => 'table:person-date',
                ],
            ],
            'default' => [['type' => 'birth']],
        ];
    }
}
