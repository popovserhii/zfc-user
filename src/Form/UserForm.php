<?php
namespace Popov\ZfcUser\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function init()
    {
        $this->setName('user');
        //$this->setAttributes(['id' => $this->getName() . '-form', 'class' => 'ajax']);
        $this->setAttributes(['id' => $this->getName() . '-form', /*'class' => 'ajax'*/]);

        // Add the project fieldset, and set it as the base fieldset
        $this->add([
            'name' => 'user',
            'type' => UserFieldset::class,
            'options' => [
                'label' => 'User',
                'use_as_base_fieldset' => true,
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Save',
                'class' => 'btn btn-primary',
                //'data-group-id' => 'keys',
            ],
        ]);
    }
}