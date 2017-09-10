<?php
namespace Popov\ZfcUser\Form;

use Zend\Form\Form,
	Zend\InputFilter\Factory as InputFactory,
	Zend\InputFilter\InputFilter;

class ChangePassword extends Form {

	public function __construct($dbAdapter)
	{
		parent::__construct('change-password');

		$this->setAttribute('method', 'post');
		$this->setAttribute('enctype', 'multipart/form-data');


		$this->add(['name' => 'passwordOld']);
		$this->add(['name' => 'password']);
		$this->add(['name' => 'supplierId']);
		$this->add([
			'name' => 'email',
			'attributes' => [
				'required'		=> 'required',
				'autocomplete'	=> 'off',
			],
		]);
		$this->add([
			'name' => 'firstName',
			'attributes' => [
				'required' => 'required',
				'onkeyup' => 'preventDigits(this);',
			],
		]);
		$this->add([
			'name' => 'lastName',
			'attributes' => [
				'required' => 'required',
				'onkeyup' => 'preventDigits(this);',
			],
		]);
		$this->add([
			'name' => 'patronymic',
			'attributes' => [
				'onkeyup' => 'preventDigits(this);',
			],
		]);
		$this->add(['name' => 'phone']);
		$this->add(['name' => 'phoneWork']);
		$this->add(['name' => 'phoneInternal']);
		$this->add(['name' => 'post']);
		$this->add([
			'name' => 'dateBirth',
			'attributes' => [
				'readonly' => 'readonly',
				'required' => 'required',
			],
		]);
		$this->add([
			'name' => 'dateEmployment',
			'attributes' => [
				'readonly' => 'readonly',
			],
		]);
		$this->add([
			'name' => 'photo',
			'attributes' => [
				'type' => 'file',
			],
		]);
		$this->add([
			'name' => 'showIndex',
			'type' => 'Zend\Form\Element\Radio',
			'options' => array(
				'value_options' => array(
					'supplier'	=> 'Поставщик',
					'city'		=> 'Город',
				),
			)
		]);
		$this->add(['name' => 'notation']);


		$inputFilter = new InputFilter();
		$factory = new InputFactory();

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'passwordOld',
			'required' => true,
			'validators' => array(
				array(
					'name' => '\Popov\Agere\Validator\Db\OldPassword',
					'options' => array(
						'table' => 'users',
						'field' => 'password',
						'fields' => ['id' => '?'],
						'adapter' => $dbAdapter,
						'methodHashPassword' => '\Popov\Users\Service\UsersService::getHashPassword',
					)
				),
			),
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'password',
			'required' => true,
			'validators' => array(
				array(
					'name' => '\Popov\Agere\Validator\Password\Simple',
					'options' => array(
						'min' => 6,
					)
				)
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'supplierId',
			'required' => false,
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'firstName',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 30
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'lastName',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 30
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'patronymic',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 50
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'phone',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 20
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'phoneWork',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 20
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'phoneInternal',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 20
					)
				),
				['name' => 'Digits']
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'post',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 100
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'dateBirth',
			'required' => true,
			'filters' => array(
				array('name' => 'StringTrim'),
			),
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'dateEmployment',
			'required' => false,
			'filters' => array(
				array('name' => 'StringTrim'),
			),
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'photo',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 255
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'showIndex',
			'required' => false,
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'notation',
			'required' => false,
		)));


		$this->setInputFilter($inputFilter);
	}

}