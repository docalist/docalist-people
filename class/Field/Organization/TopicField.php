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

use Docalist\Data\Field\TopicField as BaseTopicField;

/**
 * Mots-clés décrivant l'organisme.
 *
 * Ce champ permet de saisir des mots-clés provenant de différents vocabulaires pour décrire et classer la structure.
 *
 * Chaque indexation comporte deux sous-champs :
 * - `type` : vocabulaire,
 * - `value` : mots-clés.
 *
 * Le sous-champ type est associé à une table d'autorité qui liste les types d'indexation disponibles
 * ("table:organization-topic" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class TopicField extends BaseTopicField
{
    public static function loadSchema()
    {
        return [
            'description' => __('Mots-clés permettant de décrire et de classer la structure.', 'docalist-people'),
            'fields' => [
                'type' => [
                    'table' => 'table:organization-topic',
                ],
            ],
        ];
    }
}
