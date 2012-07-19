<?php

class Request {

	protected $post = array();

	public function __construct(array $post) {

		$this->post = $post;

	}

	public function getPost($key) {

		return $this->post[$key];

	}

	public function hasPost($key) {

		return !empty($this->post[$key]);

	}

}

class TableDataGateway {}

class UsersTableDataGateway extends TableDataGateway {

	protected $db;

	public function __construct(PDO $db) {

		$this->db = $db;
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	}

	public function findUserByEmail($email) {

		$statement = $this->db->prepare('SELECT * FROM Users WHERE email=:email;');

		$statement->bindValue(':email', $email, PDO::PARAM_STR);
		$statement->execute();

		return $statement->fetch(PDO::FETCH_ASSOC);

	}

	public function updateUserWithConfirmationCode($email, $code) {

		$statement = $this->db->prepare('UPDATE Users SET code=:code WHERE email=:email;');

		$statement->bindValue(':code', $code, PDO::PARAM_STR);
		$statement->bindValue(':email', $email, PDO::PARAM_STR);

		return $statement->execute();

	}

}

class View {

   protected $template;

   public function __construct($template) {

      $this->template = $template;

   }

}

class ErrorView extends View {

	public $errorMessage;

	public function __construct($template, $errorMessage) {

		parent::__construct($template);

		$this->errorMessage = $errorMessage;

	}

}

class CryptHelper {

   protected static $salt = 'salt is delicious';

   public function getConfirmationCode() {

      return sha1(uniqid(self::$salt, TRUE));

   }

}

class Mailer {

	public function send($recipient, $subject, $content) {

		return mail($recipient, $subject, $content);

	}

}