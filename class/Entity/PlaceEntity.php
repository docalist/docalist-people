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

namespace Docalist\People\Entity;

use Docalist\People\Entity\OrganizationEntity;
use Docalist\Data\GridBuilder\EditGridBuilder;

/**
 * Un lieu, un site, un endroit.
 *
 * Cette entité hérire de OrganizationEntity et n'introduit aucun champ supplémentaire, elle se contente
 * de changer le paramétrage par défaut (libellés des champs , tables utilisées...)
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class PlaceEntity extends OrganizationEntity
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema()
    {
        return [
            'name' => 'place',
            'label' => __('Lieu', 'docalist-people'),
            'description' => __('Un lieu, un site, un endroit.', 'docalist-people'),

            // Pas de nouveaux champs
            'fields' => [
                // name
                'name' => [
                    'description' => __('Noms et appellations utilisés pour désigner le lieu.', 'docalist-people'),
                ],

                // content
                'content' => [
                    'description' => __(
                        'Présentation du lieu, historique, caractéristiques, notes...',
                        'docalist-people'
                    ),
                ],

                // topic
                'topic' => [
                    'description' => __('Mots-clés permettant de décrire et de classer le lieu.', 'docalist-people'),
                ],

                // address

                // phone

                // link

                // organization
                'organization' => [
                    'description' => __('Structures et organismes en lien avec ce lieu.', 'docalist-people'),
                ],

                // person
                'person' => [
                    'description' => __('Personnes liées à ce lieu.', 'docalist-people'),
                ],

                // number
                'number' => [
                    'description' => __('Numéros officiels', 'docalist-people'),
                ],

                // date

                // figure
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function getEditGrid()
    {
        $builder = new EditGridBuilder(self::class);

        $builder->setProperty('stylesheet', 'docalist-people-edit-organization');

        $builder->addGroup(
            __('Présentation du lieu', 'docalist-people'),
            'name,content,topic'
        );
        $builder->addGroup(
            __('Coordonnées', 'docalist-people'),
            'address,phone,link'
            );
        $builder->addGroup(
            __('Relations', 'docalist-people'),
            'organization,person'
        );
        $builder->addGroup(
            __('Numéros, dates et chiffres clés', 'docalist-people'),
            'number,date,figure'
            );
        $builder->addGroup(
            __('Informations de gestion', 'docalist-people'),
            'type,ref,source',
            'collapsed'
        );

        $builder->setDefaultValues([
            'name'          => [ ['type' => 'usual'], ['type' => 'acronym'] ],
            'content'       => [ ['type' => 'overview'] ],
            'address'       => [ ['type' => 'main', 'value' => ['country' => 'FR']] ],
            'phone'         => [ ['type' => 'contact'] ],
            'link'          => [ ['type' => 'mail'], ['type' => 'site'], ['type' => 'facebook'] ],
            'organization'  => [ ['type' => 'managing'], ['type' => 'other'] ],
            'person'        => [ ['type' => 'contact'] ],
            'number'        => [ ['type' => 'other'] ],
            'date'          => [  ],
            'figure'        => [ ['type' => 'area'] ],
        ]);

        return $builder->getGrid();
    }
}
