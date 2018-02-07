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

use Docalist\Data\Field\ContentField as BaseContentField;

/**
 * Champ "content" pour les entités organization.
 *
 * Cette classe hérite simplement du champ standard de docalist-data et modifie les paramètres par défaut.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class ContentField extends BaseContentField
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
