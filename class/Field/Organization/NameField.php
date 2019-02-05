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

namespace Docalist\People\Field\Organization;

use Docalist\Type\TypedText;

/**
 * Champ "name" pour les entités organization : noms de l'organisme.
 *
 * Ce champ permet d'indiquer les différents noms de la structure (nom usuel, sigle, ancien nom...)
 *
 * Chaque occurence du champ comporte deux sous-champs :
 * - `type` : type de nom,
 * - `value` : nom.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de noms disponibles
 * ("table:organization-name" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class NameField extends TypedText
{
    public static function loadSchema()
    {
        return [
            'label' => __('Nom', 'docalist-people'),
            'description' => __('Nom ou sigle de la structure.', 'docalist-people'),
            'repeatable' => true,
            'fields' => [
                'type' => [
                    'table' => 'table:organization-name',
                ],
                'value' => [
                    'label' => __('Nom', 'docalist-people'),
                ],
            ],
            'default' => [['type' => 'usual']],
        ];
    }
}
