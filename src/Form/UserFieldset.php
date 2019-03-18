<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2017 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_<package>
 * @author Serhii Popov <popow.sergiy@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace Popov\ZfcUser\Form;

use Zend\Form\Fieldset;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorAwareInterface;

use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

use Popov\ZfcCore\Service\DomainServiceAwareInterface;
use Popov\ZfcCore\Service\DomainServiceAwareTrait;
use Popov\ZfcRole\Model\Role;
use Popov\ZfcUser\Model\User;

class UserFieldset extends Fieldset
    implements InputFilterProviderInterface, TranslatorAwareInterface, DomainServiceAwareInterface, ObjectManagerAwareInterface
{
    use TranslatorAwareTrait;

    use ProvidesObjectManager;

    use DomainServiceAwareTrait;

    public function init()
    {
        $this->setName('user');

        $this->add([
            'type' => 'hidden',
            'name' => 'id',
        ]);
        $this->add([
            'name' => 'email',
            'options' => [
                'label' => $this->__('Email'),
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => $this->__('Password'),
            ],
            'attributes' => [
                //'required' => true,
            ],
        ]);
        $this->add([
            'name' => 'firstName',
            'options' => [
                'label' => $this->__('First Name'),
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'name' => 'lastName',
            'options' => [
                'label' => $this->__('Last Name'),
            ],
            'attributes' => [
                //'required' => 'required',
            ],
        ]);

        /*$this->add([
            'name' => 'patronymic',
            'options' => [
                'label' => $this->__('Patronymic'),
            ],
            'attributes' => [
            ],
        ]);*/

        $this->add([
            'name' => 'phone',
            'options' => [
                'label' => $this->__('Phone'),
            ],
        ]);

        $this->add([
            'name' => 'phoneWork',
            'options' => [
                'label' => $this->__('Work phone'),
            ],
        ]);

        /*$this->add([
            'name' => 'phoneInternal',
            'options' => [
                'label' => $this->__('Internal phone'),
            ],
        ]);
        $this->add([
            'name' => 'post',
            'options' => [
                'label' => $this->__('Position'),
            ],
        ]);

        $this->add([
            //'type' => 'datetime',
            'name' => 'birthedAt',
            'options' => [
                'label' => $this->__('Date of birth'),
                'format' => 'd/m/Y',
            ],
            'attributes' => [
                //'placeholder' => 'Date format: 21/06/1990',
            ],
        ]);

        $this->add([
            'name' => 'employedAt',
            'options' => [
                'label' => $this->__('Date of taking on the job'),
                'format' => 'd/m/Y',
            ],
            'attributes' => [
                //'readonly' => 'readonly',
            ],
        ]);
        $this->add([
            'name' => 'photo',
            'attributes' => [
                'type' => 'file',
            ],
        ]);*/

        $this->add([
            'name' => 'notation',
            'type' => 'textarea',
            'options' => [
                'label' => $this->__('Notation'),
            ],
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'isInner',
            'options' => [
                'label' => 'Is Inner',
                'value_options' => [
                    '0' => 'No',
                    '1' => 'Yes',
                ],
            ],
        ]);

        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'roles',
            'options' => [
                'label' => $this->__('Role'),
                'display_empty_item' => true,
                'empty_item_label'   => '---',
                'object_manager' => $this->getObjectManager(),
                'target_class' => Role::class,
                'property' => 'name',
            ],
            'attributes' => [
                'required' => true,
                'multiple' => 'multiple',
                'size' => '5',
            ],
        ]);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'id' => [
                'required' => false,
            ],
            'password' => [
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'The password cannot be empty',
                            ],
                        ],
                    ],
                ],
            ],
            'firstName' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                        ],
                    ],
                ],
            ],
            'phone' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    [
                        'name' => 'PregReplace',
                        'options' => [
                            'pattern' => '/[^0-9]/',
                            'replacement' => '',
                        ],
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 11,
                            'max' => 13,
                            'message' => 'Phone number must contains 11-13 numbers',
                        ],
                    ],
                    [
                        'name' => 'Digits',
                    ],
                ],
            ],
            'phoneWork' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    [
                        'name' => 'PregReplace',
                        'options' => [
                            'pattern' => '/[^0-9]/',
                            'replacement' => '',
                        ],
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 11,
                            'max' => 13,
                            'message' => 'Phone number must contains 11-13 numbers',
                        ],
                    ],
                    [
                        'name' => 'Digits',
                    ],
                ],
            ],
            /*'email' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'message' => 'Invalid email address',
                        ],
                    ],
                ],
            ],*/
            'email' => [
                'required' => true,
                'name' => 'email',
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'max' => 100,
                        ],
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'message' => 'Invalid email address',
                        ],
                    ],
                    [
                        'name' => \DoctrineModule\Validator\UniqueObject::class,
                        'options' => [
                            'object_manager' => $om = $this->getObjectManager(),
                            'object_repository' => $om->getRepository(User::class),
                            'target_class' => User::class,
                            'fields' => ['id', 'email'],
                            'messages' => [
                                //'objectNotUnique' => 'The email must be unique',
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }

    public function __($message)
    {
        return $this->getTranslator()->translate($message, $this->getTranslatorTextDomain());
    }
}