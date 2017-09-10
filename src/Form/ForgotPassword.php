<?php
namespace Popov\ZfcUser\Form;

use Zend\Form\Form,
	Zend\InputFilter\Factory as InputFactory,
	Zend\InputFilter\InputFilter;

class ForgotPassword extends Form {

	public function __construct($name = null)
	{
		parent::__construct('forgot-password');

		$this->setAttribute('method', 'post');


		$this->add([
			'name' => 'email',
			'attributes' => [
				'required' => 'required'
			],
		]);


		$inputFilter = new InputFilter();
		$factory = new InputFactory();

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'email',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 100
					)
				),
				['name' => 'EmailAddress'],
			)
		)));


		$this->setInputFilter($inputFilter);
	}

}