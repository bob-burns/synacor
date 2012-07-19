<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../src/UserControllerRefactored.php';

class UserControllerRefactoredTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {

		$this->gateway      = $this->getMock('UsersTableDataGateway', array(), array(), '', FALSE);
		$this->cryptHelper  = $this->getMock('CryptHelper', array(), array(), '', FALSE);
		$this->mailer       = $this->getMock('Mailer', array(), array(), '', FALSE);
        $this->controller   = new UserControllerRefactored(
            $this->gateway,
            $this->cryptHelper,
            $this->mailer
        );

	}

	protected function tearDown() {

		unset($this->gateway);
		unset($this->mailer);
		unset($this->cryptHelper);
		unset($this->controller);

	}

	public function testDisplaysErrorViewWhenNoEmailAddressGiven() {

		$request    = new Request(array());
		$view       = $this->controller->resetPassword($request);

		$this->assertInstanceOf('ErrorView', $view);
		$this->assertEquals('No email specified', $view->errorMessage);

    }

	public function testDisplaysErrorViewWhenNoUserFound() {
	
        $this->gateway
			->expects($this->once())
			->method('findUserByEmail')
			->will($this->returnValue(FALSE));

		$request    = new Request(array(
			'email' => 'nonesuch@user.org'
		));
		$view       = $this->controller->resetPassword($request);

		$this->assertInstanceOf('ErrorView', $view);
		$this->assertEquals('No user with email nonesuch@user.org', $view->errorMessage);

    }

	public function testDisplaysViewWhenEmailAddressGiven() {

		$this->gateway
	         ->expects($this->once())
	         ->method('findUserByEmail')
	         ->will($this->returnValue(array(
			 	'id'       => 42,
			 	'username' => 'John Doe',
			 	'email'    => 'user@example.com',
			 	'code'     => NULL
			 )));
			 
		$this->cryptHelper
	         ->expects($this->once())
	         ->method('getConfirmationCode')
	         ->will($this->returnValue('123456789'));
			
		$this->gateway
             ->expects($this->once())
	         ->method('updateUserWithConfirmationCode')
	         ->with('user@example.com', '123456789');

		$this->mailer
	         ->expects($this->once())
	         ->method('send')
	         ->with('user@example.com', 'Password Reset', '123456789');

		$request    = new Request(array(
			'email' => 'user@example.com'
		));
        $view       = $this->controller->resetPassword($request);

        $this->assertInstanceOf('View', $view);

	}

}