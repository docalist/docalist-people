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

use Docalist\Data\Type\TypedRelation;

/**
 * Champ "person" pour les entités person : personnes liées.
 *
 * Ce champ permet de créer des relations entre des personnes (parenté, amis...)
 *
 * Chaque occurence du champ comporte deux sous-champs :
 * - `type` : type de relation,
 * - `value` : Post ID de la personne liée.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de relations disponibles
 * ("table:person-person-relation" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class PersonField extends TypedRelation
{
    public static function loadSchema(): array
    {
        return [
            'name' => 'organization',
            'label' => __('Personnes liées', 'docalist-people'),
            'description' => __('Personnes liées à cette personne.', 'docalist-people'),
            'repeatable' => true,
            'fields' => [
                'type' => [
                    'label' => __('Relation', 'docalist-people'),
                    'table' => 'table:person-person-relation',
                ],
                'value' => [
                    'label' => __('Personne liée', 'docalist-people'),
                    'relfilter' => 'type:person',
                ],
            ],
        ];
    }
}
