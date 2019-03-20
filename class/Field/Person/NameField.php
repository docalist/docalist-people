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

use Docalist\Type\MultiField;
use Docalist\Type\Text;
use Docalist\Type\TableEntry;

/**
 * Champ "name" pour les entités "person" : nom, prénom et précision sur le type de patronyme de la personne.
 *
 * Ce champ permet de cataloguer le nom et le prénom d'une personne et d'apporte une précision sur la
 * nature du nom (nom usuel, surnom, pseudo, nom d'artiste...)
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de noms disponibles
 * ("table:person-name" par défaut).
 *
 * @property Text       $name       Nom
 * @property Text       $firstname  Prénom
 * @property TableEntry $type       Précision sur la nature du nom (nom usuel, surnom, nom d'artiste..)
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class NameField extends MultiField
{
    public static function loadSchema(): array
    {
        return [
            'name' => 'name',
            'repeatable' => true,
            'label' => __('Noms', 'docalist-people'),
            'description' => __('Nom(s) de la personne', 'docalist-people'),
            'fields' => [
                'name' => [
                    'type' => Text::class,
                    'label' => __('Nom', 'docalist-people'),
                    'description' => __('Nom de la personne.', 'docalist-people'),
                ],
                'firstname' => [
                    'type' => Text::class,
                    'label' => __('Prénom(s)', 'docalist-people'),
                    'description' => __('Prénom(s) ou initiales.', 'docalist-people'),
                ],
                'type' => [
                    'type' => TableEntry::class,
                    'table' => 'table:name-type',
                    'label' => __('Précision', 'docalist-people'),
                    'description' => __('Type du nom.', 'docalist-people'),
                    'editor' => 'select',
                ],
            ],
        ];
    }

    protected function getCategoryField(): string
    {
        return 'type';
    }

    public function getAvailableFormats(): array
    {
        return [
            'f n (r)'   => __('Charlie Chaplin (pseudo)', 'docalist-people'),
            'f n'       => __('Charlie Chaplin', 'docalist-people'),
            'n (f) / r' => __('Chaplin (Charlie) / pseudo', 'docalist-people'),
            'n (f)'     => __('Chaplin (Charlie)', 'docalist-people'),
        ];
    }

    public function getFormattedValue($options = null)
    {
        $format = $this->getOption('format', $options, $this->getDefaultFormat());

        $t = [];
        switch ($format) {
            case 'f n (r)':
                isset($this->firstname) && $t[] = $this->formatField('firstname', $options);
                isset($this->name) && $t[] = $this->formatField('name', $options);
                isset($this->type) && $t[] =  '(' . $this->formatField('type', $options) . ')';
                break;

            case 'f n':
                isset($this->firstname) && $t[] = $this->formatField('firstname', $options);
                isset($this->name) && $t[] = $this->formatField('name', $options);
                break;

            case 'n (f) / r':
                isset($this->name) && $t[] = $this->formatField('name', $options);
                isset($this->firstname) && $t[] = '(' . $this->formatField('firstname', $options) . ')';
                isset($this->type) && $t[] =  '/ ' . $this->formatField('type', $options); // insécable après le slash
                break;

            case 'n (f)':
                isset($this->name) && $t[] = $this->formatField('name', $options);
                isset($this->firstname) && $t[] = '(' . $this->formatField('firstname', $options) . ')';
                break;

            default:
                return parent::getFormattedValue($options);
        }

        return implode(' ', $t); // espace insécable
    }

    public function filterEmpty(bool $strict = true): bool
    {
        // Supprime les éléments vides
        $empty = parent::filterEmpty();

        // Si tout est vide ou si on est en mode strict, terminé
        if ($empty || $strict) {
            return $empty;
        }

        // Retourne true si on n'a pas de nom
        return $this->filterEmptyProperty('name');
    }
}
