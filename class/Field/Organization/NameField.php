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
            'name' => 'name',
            'label' => __('Nom', 'docalist-people'),
            'description' => __(
                'Noms, sigles et appellations utilisés pour désigner la structure ou l\'organisme.',
                'docalist-people'
            ),
            'repeatable' => true,
            'fields' => [
                'value' => [
                    'label' => __('Nom ou sigle', 'docalist-people'),
                ],
                'type' => [
                    'label' => __('Type de nom', 'docalist-people'),
                    'table' => 'table:organization-name',
                ],
            ],
            'default' => [['type' => 'usual']],
        ];
    }
}
