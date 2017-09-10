<?php
namespace Popov\ZfcUser\Form;

use Zend\Form\Form,
	Zend\InputFilter\Factory as InputFactory,
	Zend\InputFilter\InputFilter;

class Login extends Form {

	public function __construct($name = null)
	{
		parent::__construct('login');

		$this->setAttribute('method', 'post');


		$this->add([
			'name' => 'email',
			'attributes' => [
				'required' => 'required'
			],
		]);
		$this->add([
			'name' => 'password',
			'attributes' => [
				'required' => 'required'
			],
		]);

		$this->add([
			'name' => 'save',
			'options' => [
				'ignore' => true,
				'label' => 'Login'
			]
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

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'password',
			'required' => true,
		)));


		$this->setInputFilter($inputFilter);
	}

}