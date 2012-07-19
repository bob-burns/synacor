<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../src/UserController.php';

class UserControllerTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		
		$this->db = new PDO('sqlite::memory:');

        $this->db->exec("CREATE TABLE `Users` (
           `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
           `username` VARCHAR UNIQUE NOT NULL,
           `email` VARCHAR UNIQUE NOT NULL,
           `code` VARCHAR
        );");

        $this->db->exec("INSERT INTO Users(username, email)
            VALUES('John Doe', 'user@example . com');"
        );

        $this->controller = new UserController;

	}

	protected function tearDown() {

		unset($this->db);
		unset($this->controller);

		Configuration::init(array());

		$_POST = array();

	}

	public function testDisplaysErrorViewWhenNoEmailAddressGiven() {

		$_POST['email'] = '';

		$view = $this->controller->resetPassword();

		$this->assertType('ErrorView', $view);

    }

	public function testDisplaysViewWhenEmailAddressGiven() {

         $_POST['email'] = 'user@example . com';

         $view = $this->controller->resetPassword();

         $this->assertType('View', $view);

	}

}