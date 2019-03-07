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
use Docalist\People\Field\Person\DateField;
use Docalist\People\Field\Person\ContentField;
use Docalist\People\Field\Person\TopicField;
use Docalist\People\Field\Person\AddressField;
use Docalist\People\Field\Person\PhoneField;
use Docalist\People\Field\Person\LinkField;
use Docalist\People\Field\Person\PersonField;
use Docalist\People\Field\Person\NumberField;
use Docalist\People\Field\Person\FigureField;

use Docalist\Type\Collection;
use Docalist\Type\Collection\MultiFieldCollection;
use Docalist\Type\Collection\TypedValueCollection;
use Docalist\Data\Type\Collection\TopicCollection;
use Docalist\Data\Type\Collection\TypedRelationCollection;

use Docalist\Data\GridBuilder\EditGridBuilder;
use Docalist\Search\MappingBuilder;

/**
 * Une personne physique.
 *
 * @property Collection                 $gender     Genre / sexe de la personne.
 * @property MultiFieldCollection       $name       Noms de la personne.
 * @property TypedValueCollection       $date       Dates.
 * @property TypedValueCollection       $content    Contenus : présentation, biographie, travaux...
 * @property TopicCollection            $topic      Mots-clés.
 * @property TypedValueCollection       $address    Adresses postales.
 * @property TypedValueCollection       $phone      Numéros de téléphone.
 * @property MultiFieldCollection       $link       Liens.
 * @property TypedRelationCollection    $person     Personnes liées.
 * @property TypedValueCollection       $number     Numéros officiels.
 * @property TypedValueCollection       $figure     Chiffres clés.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class PersonEntity extends ContentEntity
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema()
    {
        return [
            'name' => 'person',
            'label' => __('Personne', 'docalist-people'),
            'description' => __('Une personne physique.', 'docalist-people'),
            'fields' => [
                'gender'    => GenderField::class,
                'name'      => NameField::class,
                'date'      => DateField::class,
                'content'   => ContentField::class,
                'topic'     => TopicField::class,
                'address'   => AddressField::class,
                'phone'     => PhoneField::class,
                'link'      => LinkField::class,
                // structures liées
                'person'    => PersonField::class,
                'number'    => NumberField::class,
                'figure'    => FigureField::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function initPostTitle()
    {
        $this->posttitle =
            isset($this->name) && !empty($firstName = $this->name->first()) /** @var NameField $firstName */
            ? $firstName->getFormattedValue(['format' => 'f n'])
            : __('(personne sans nom)', 'docalist-people');
    }

    /**
     * {@inheritDoc}
     */
    public static function getEditGrid()
    {
        $builder = new EditGridBuilder(self::class);

        $builder->setProperty('stylesheet', 'docalist-people-edit-person');

        $builder->addGroups([
            __('Identité de la personne', 'docalist-people')        => 'gender,name,date',
            __('Présentation de la personne', 'docalist-people')    => 'content,topic',
            __('Coordonnées', 'docalist-people')                    => 'address,phone,link',
            __('Relations', 'docalist-people')                      => 'person',
            __('Numéros et chiffres clés', 'docalist-people')       => 'number,figure',
            __('Informations de gestion', 'docalist-people')        => '-type,ref,source',
        ]);

        $builder->setDefaultValues([
            'gender'        => 'unknown',
            'name'          => [ ['type' => 'usual'], ['type' => 'birth'], ['type' => 'pseudonym'] ],
            'date'          => [ ['type' => 'birth'] ],
            'content'       => [ ['type' => 'overview'] ],
            'address'       => [ ['type' => 'main', 'value' => ['country' => 'FR']] ],
            'phone'         => [ ['type' => 'mobile'], ['type' => 'work'], ['type' => 'home'] ],
            'link'          => [ ['type' => 'mail'], ['type' => 'facebook'], ['type' => 'twitter'] ],
         // 'organization'  => [ ['type' => 'affiliation'], ['type' => 'member-of'], ['type' => 'partner'] ],
            'person'        => [ ['type' => 'friend'] ],
            'number'        => [ ['type' => 'other'] ],
            'figure'        => [  ],
        ]);

        return $builder->getGrid();
    }

    /**
     * {@inheritDoc}
     */
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
            $mapping->addField('geoloc-hierarchy')->hierarchy();

        // Topic
        $mapping->addField('topic')->text()->filter()->suggest()
            ->addTemplate('topic_*')->copyFrom('topic')->copyDataTo('topic');

        // Crée un champ 'hierarchy' pour tous les topics qui sont associés à une table de type thesaurus
        foreach ($this->topic->getThesaurusTopics() as $topic) {
            $mapping->addField("topic-$topic-hierarchy")->hierarchy();
        }

        // Ok
        return $mapping;
    }

    /**
     * {@inheritDoc}
     */
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
