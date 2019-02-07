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

use Docalist\Data\Entity\ContentEntity;
use Docalist\People\Field\Person\GenderField;
use Docalist\People\Field\Person\NameField;
use Docalist\People\Field\Person\ContentField;
use Docalist\People\Field\Person\TopicField;
use Docalist\People\Field\Person\LinkField;
use Docalist\People\Field\Person\PhoneField;
use Docalist\People\Field\Person\DateField;
use Docalist\People\Field\Person\AddressField;
use Docalist\Search\MappingBuilder;

/**
 * Une personne physique.
 *
 * @property GenderField        $gender     Genre / sexe de la personne.
 * @property NameField[]        $name       Nom de la personne.
 * @property ContentField[]     $content    Contenus : présentation, biographie, travaux...
 * @property LinkField[]        $link       Liens.
 * @property PhoneField[]       $phone      Numéros de téléphone.
 * @property DateField[]        $date       Dates.
 * @property AddressField[]     $address    Adresses postales.
 * @property TopicField[]       $topic      Mots-clés.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class PersonEntity extends ContentEntity
{
    public static function loadSchema()
    {
        return [
            'name' => 'person',
            'label' => __('Personne', 'docalist-people'),
            'description' => __('Une personne physique.', 'docalist-people'),
            'fields' => [
                'gender'    => GenderField::class,
                'name'      => NameField::class,
                'content'   => ContentField::class,
                'link'      => LinkField::class,
                'phone'     => PhoneField::class,
                'date'      => DateField::class,
                'address'   => AddressField::class,
                'topic'     => TopicField::class,
            ],
        ];
    }

    protected function initPostTitle()
    {
        $this->posttitle =
            isset($this->name) && !empty($firstName = $this->name->first()) /** @var NameField $firstName */
            ? $firstName->getFormattedValue(['format' => 'f n'])
            : __('(personne sans nom)', 'docalist-people');
    }

    protected function buildMapping(MappingBuilder $mapping)
    {
        // Le mapping des champs de base est construit par la classe parent
        $mapping = parent::buildMapping($mapping);

        // Gender
        $mapping->addField('gender')->text()->filter();

        // Name
        $mapping->addField('person_name')->literal()->filter()->suggest();

        // Content
        $mapping->addField('content')->text();

        // Link
        $mapping->addField('link')->url()
            ->addTemplate('link-*')->copyFrom('link')->copyDataTo('link');

        // Phone

        // Date
        $mapping->addField('date')->date()
            ->addTemplate('date_*')->copyFrom('date')->copyDataTo('date');

        // Address
        $mapping->addField('geoloc-hierarchy')->text('hierarchy')->setProperty('search_analyzer', 'keyword');

        // Topic
        $mapping->addField('topic')->text()->filter()->suggest()
            ->addTemplate('topic_*')->copyFrom('topic')->copyDataTo('topic');

        // Crée un champ 'hierarchy' pour tous les topics qui sont associés à une table de type thesaurus
        foreach ($this->topic->getThesaurusTopics() as $topic) {
            $mapping->addField("topic-$topic-hierarchy")->text('hierarchy')->setProperty('search_analyzer', 'keyword');
        }

        // Ok
        return $mapping;
    }

    public function map()
    {
        // Le mapping des champs de base est fait par la classe parent
        $document = parent::map();

        // Gender
        if (isset($this->gender)) {
            $document['gender'] = $this->gender->getEntryLabel();
        }

        // Name
        // Pas de mapMultiField car le nom du champ ES (person_name) est différent du champ Person (name)
        if (isset($this->name)) {
            foreach ($this->name as $name) { /** @var NameField $name */
                $document['person_name'][] = $name->name->getPhpValue() . '¤' . $name->firstname->getPhpValue();
            }
        }

        // Content
        // Pas de mapMultiField() car on génère un champ unique ('content') et non pas un champ par type
        if (isset($this->content)) {
            foreach ($this->content as $content) { /** @var ContentField $content */
                $document['content'][] = $content->value->getPhpValue();
            }
        }

        // Link
        // Pas de mapMultiField() car on génère un champ unique ('link') et non pas un champ par type
        if (isset($this->link)) {
            foreach ($this->link as $link) { /** @var LinkField $link */
                $document['link'][] = $link->url->getPhpValue();
            }
        }

        // Phone : non indexé

        // Date
        $this->mapMultiField($document, 'date');  // générait date_xx (et non pas date-xx) dans svb

        // Address
        if (isset($this->address)) {
            foreach ($this->address as $address) { /** @var AddressField $address */
                $document['geoloc-hierarchy'][] = $address->value->getContinentAndCountry();
            }
        }

        // Topic
        $this->mapMultiField($document, 'topic', 'term'); // générait topic_xx (et non pas topic-xx) dans svb

        if (isset($this->topic)) {
            // Initialise le champ 'hierarchy' pour tous les topics qui sont associés à une table de type thesaurus
            foreach ($this->topic->getThesaurusTopics() as $table => $topic) {
                if (isset($this->topic[$topic])) {
                    $terms = $this->topic[$topic]->term->getPhpValue();
                    $document["topic-$topic-hierarchy"] = $this->getTermsPath($terms, $table);
                }
            }
        }

        // Ok
        return $document;
    }
}
