<?php

namespace Popov\ZfcUser\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Stagem\ZfcLang\TranslatorAwareTrait;

class LoginForm extends Form implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    public function init()
    {
        $this->setName('login');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'email',
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => $this->translate('Email'),
            ],
        ]);

        $this->add([
            'type' => 'password',
            'name' => 'password',
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => $this->translate('Password'),
            ],
        ]);

        $this->add([
            'name' => 'save',
            'options' => [
                'ignore' => true,
                'label' => $this->translate('Login'),
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