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

use Docalist\People\Field\Organization\NameField;
use Docalist\People\Field\Organization\ContentField;
use Docalist\People\Field\Organization\TopicField;
use Docalist\People\Field\Organization\AddressField;
use Docalist\People\Field\Organization\PhoneField;
use Docalist\People\Field\Organization\LinkField;
use Docalist\People\Field\Organization\OrganizationField;
use Docalist\People\Field\Organization\PersonField;
use Docalist\People\Field\Organization\NumberField;
use Docalist\People\Field\Organization\DateField;
use Docalist\People\Field\Organization\FigureField;

use Docalist\Type\Collection\TypedValueCollection;
use Docalist\Data\Type\Collection\TopicCollection;
use Docalist\Type\Collection\MultiFieldCollection;
use Docalist\Data\Type\Collection\TypedRelationCollection;

use Docalist\Data\GridBuilder\EditGridBuilder;
use Docalist\Search\MappingBuilder;

/**
 * Un organisme, une structure ou un un groupe de personnes.
 *
 * @property TypedValueCollection       $name           Noms.
 * @property TypedValueCollection       $content        Présentation.
 * @property TopicCollection            $topic          Mots-clés.
 * @property TypedValueCollection       $address        Adresses postales
 * @property TypedValueCollection       $phone          Numéros de téléphone
 * @property MultiFieldCollection       $link           Liens
 * @property TypedRelationCollection    $organization   Organismes liés.
 * @property TypedRelationCollection    $person         Personnes liées.
 * @property TypedValueCollection       $number         Numéros officiels.
 * @property TypedValueCollection       $date           Dates.
 * @property TypedValueCollection       $figure         Chiffres clés.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class OrganizationEntity extends ContentEntity
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema(): array
    {
        return [
            'name' => 'organization',
            'label' => __('Structure', 'docalist-people'),
            'description' => __('Un organisme, une structure ou un groupe de personnes.', 'docalist-people'),
            'fields' => [
                'name'          => NameField::class,
                'content'       => ContentField::class,
                'topic'         => TopicField::class,
                'address'       => AddressField::class,
                'phone'         => PhoneField::class,
                'link'          => LinkField::class,
                'organization'  => OrganizationField::class,
                'person'        => PersonField::class,
                'number'        => NumberField::class,
                'date'          => DateField::class,
                'figure'        => FigureField::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function assign($value): void
    {
        // 06/02/19 - gère la compatibilité ascendante avec le site svb
        // dans svb, le champ "figure" s'appellait "figures"
        if (is_array($value) && isset($value['figures'])) {
            $value['figure'] = $value['figures'];
            unset($value['figures']);
        }

        parent::assign($value);
    }

    /**
     * {@inheritDoc}
     */
    protected function initPostTitle()
    {
        $this->posttitle =
            isset($this->name) && !empty($firstName = $this->name->first()) /** @var NameField $firstName */
            ? $firstName->getFormattedValue(['format' => 'v'])
            : __('(organisme sans nom)', 'docalist-people');
    }

    /**
     * {@inheritDoc}
     */
    public static function getEditGrid()
    {
        $builder = new EditGridBuilder(self::class);

        $builder->setProperty('stylesheet', 'docalist-people-edit-organization');

        $builder->addGroups([
            __('Présentation de la structure', 'docalist-people')       => 'name,content,topic',
            __('Coordonnées', 'docalist-people')                        => 'address,phone,link',
            __('Relations', 'docalist-people')                          => 'organization,person',
            __('Numéros, dates et chiffres clés', 'docalist-people')    => 'number,date,figure',
            __('Informations de gestion', 'docalist-people')            => '-type,ref,source',
        ]);

        $builder->setDefaultValues([
            'name'          => [ ['type' => 'usual'], ['type' => 'acronym'] ],
            'content'       => [ ['type' => 'overview'] ],
            'address'       => [ ['type' => 'main', 'value' => ['country' => 'FR']] ],
            'phone'         => [ ['type' => 'management'], ['type' => 'commercial'], ['type' => 'contact'] ],
            'link'          => [ ['type' => 'mail'], ['type' => 'site'], ['type' => 'facebook'] ],
            'organization'  => [ ['type' => 'affiliation'], ['type' => 'member-of'], ['type' => 'partner'] ],
            'person'        => [ ['type' => 'management'], ['type' => 'webmaster'], ['type' => 'contact'] ],
            'number'        => [ ['type' => 'siren'], ['type' => 'siret'], ['type' => 'ape'], ['type' => 'tva'] ],
            'date'          => [ ['type' => 'start'] ],
            'figure'        => [ ['type' => 'staff'] ],
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

        // Name
        $mapping->addField('name')->text()->suggest()
                ->addTemplate('name-*')->copyFrom('name')->copyDataTo('name');

        // Date
        $mapping->addField('date')->date()
                ->addTemplate('date-*')->copyFrom('date')->copyDataTo('date');

        // Content
        $mapping->addField('content')->text()
                ->addTemplate('content-*')->copyFrom('content')->copyDataTo('content');

        // Topic
        $mapping->addField('topic')->text()->filter()->suggest()
                ->addTemplate('topic-*')->copyFrom('topic')->copyDataTo('topic');

        // Crée un champ 'hierarchy' pour tous les topics qui sont associés à une table de type thesaurus
        foreach ($this->topic->getThesaurusTopics() as $topic) {
            $mapping->addField("topic-$topic-hierarchy")->hierarchy();
        }

        // Organization
        $mapping->addField('organization')->integer()
                ->addTemplate('organization-*')->copyFrom('organization')->copyDataTo('organization');

        // Person
        $mapping->addField('person')->integer()
                ->addTemplate('person-*')->copyFrom('person')->copyDataTo('person');

        // Address
        $mapping->addField('geoloc-hierarchy')->hierarchy();

        // Phone

        // Figures
        $mapping->addField('figure')->decimal()
                ->addTemplate('figure-*')->copyFrom('figure')->copyDataTo('figure');

        // Number
        $mapping->addField('number')->literal()
                ->addTemplate('number-*')->copyFrom('number')->copyDataTo('number');

        // Link
        $mapping->addField('link')->url()
                ->addTemplate('link-*')->copyFrom('link')->copyDataTo('link');

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

        // Mapping standard pour tous les champs multifield
        $this->mapMultiField($document, 'name');
        $this->mapMultiField($document, 'date');
        $this->mapMultiField($document, 'content');
        $this->mapMultiField($document, 'topic', 'term');
        $this->mapMultiField($document, 'organization');
        $this->mapMultiField($document, 'person');
        $this->mapMultiField($document, 'figure');
        $this->mapMultiField($document, 'number');
        $this->mapMultiField($document, 'link', 'url');

        // Address
        if (isset($this->address)) {
            foreach ($this->address as $address) { /** @var AddressField $address */
                $document['geoloc-hierarchy'][] = $address->value->getContinentAndCountry();
            }
        }

        // Phone

        // Initialise le champ 'hierarchy' pour tous les topics qui sont associés à une table de type thesaurus
        foreach ($this->topic->getThesaurusTopics() as $table => $topic) {
            if (isset($this->topic[$topic])) {
                $terms = $this->topic[$topic]->term->getPhpValue();
                $document["topic-$topic-hierarchy"] = $this->getTermsPath($terms, $table);
            }
        }

        // Ok
        return $document;
    }
}
