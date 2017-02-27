<?php

namespace FDevs\ContactListBundle\Sonata\Admin;

use FDevs\ContactList\Form\Type\ConnectType;
use FDevs\ContactList\Form\Type\ContactType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContactAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $formOptions = ['cascade_validation' => true];

    /**
     * {@inheritdoc}
     */
    protected $baseRoutePattern = 'contact';

    /**
     * {@inheritdoc}
     */
    protected $baseRouteName = 'contact';

    /**
     * {@inheritdoc}
     */
    protected $translationDomain = 'FDevsContactListBundle';

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('contact', ContactType::class, ['inherit_data' => true, 'label' => false])
            ->add(
                'connectList',
                CollectionType::class,
                [
                    'type' => ConnectType::class,
                    'allow_delete' => true,
                    'allow_add' => true,
                    'required' => false,
                    'options' => ['label' => false],
                ]
            )
        ;

        $formMapper->get('contact')->remove('connectList');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('slug', null, ['editable' => true])
            ->add('show', null, ['editable' => true])
            ->add('_action', 'actions', ['actions' => ['edit' => [], 'delete' => []]])
        ;
    }
}
