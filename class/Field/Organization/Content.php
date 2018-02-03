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

use Docalist\Data\Field\Content as BaseContent;

/**
 * Description de l'organisme.
 *
 * Ce champ permet de décrire la structure : présentation, historique, activités...
 *
 * Chaque contenu comporte deux sous-champs :
 * - `type` : type de contenu,
 * - `value` : contenu.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de contenu possibles
 * ("table:organization-content" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class Content extends BaseContent
{
    public static function loadSchema()
    {
        return [
            'label' => __('Présentation', 'docalist-people'),
            'description' => __('Présentation de la structure, historique, activités...', 'docalist-people'),
            'fields' => [
                'type' => [
                    'table' => 'table:organization-content',
                ],
            ],
            'default' => [['type' => 'overview']],
            'editor' => 'integrated',
        ];
    }
}
