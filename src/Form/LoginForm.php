<?php

namespace Popov\ZfcUser\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name ?: 'login');
        $this->setAttribute('method', 'post');
        $this->add([
            'name' => 'email',
            'attributes' => [
                'required' => 'required',
            ],
        ]);
        $this->add([
            'type' => 'password',
            'name' => 'password',
            'attributes' => [
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'save',
            'options' => [
                'ignore' => true,
                'label' => 'Login',
            ],
        ]);

        $inputFilter = new InputFilter();
        $factory = new InputFactory();
        $inputFilter->add($factory->createInput([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 100,
                    ],
                ],
                ['name' => 'EmailAddress'],
            ],
        ]));
        $inputFilter->add($factory->createInput([
            'name' => 'password',
            'required' => true,
        ]));
        $this->setInputFilter($inputFilter);
    }
}