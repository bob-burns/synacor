<?php

class Configuration {

   protected static $values = array();

   public static function init(array $values) {

     self::$values = $values;

   }

   public static function get($key) {

      if (!isset(self::$values[$key])) {

         throw new Exception('No such key');

      }

      return self::$values[$key];

   }
   
}

class View {

   protected $template;
   
   public function __construct($template) {
   
      $this->template = $template;
      
   }
   
}

class ErrorView extends View {

	protected $errorMessage;

	public function __construct($template, $errorMessage) {

		parent::__construct($template);
		
	}
	
}

class CryptHelper {

   protected static $salt = 'salt is delicious';

   public static function getConfirmationCode() {

      return sha1(uniqid(self::$salt, TRUE));

   }

}

class UserController {

	public function resetPassword() {

		if (!isset($_POST['email'])) {

			return new ErrorView('resetPassword', 'No email specified');

		}

		$db        = new PDO('sqlite::memory:');
		$statement = $db->prepare('SELECT * FROM Users WHERE email =:email;');

		$statement->bindValue(':email', $_POST['email'], PDO::PARAM_STR);

		$statement->execute();

		$record = $statement->fetch(PDO::FETCH_ASSOC);

		if ($record === FALSE) {

			return new ErrorView('resetPassword', 'No user with email ' . $_POST['email']);

		}

		$code      = CryptHelper::getConfirmationCode();
		$statement = $db->prepare('UPDATE Users SET code =:code WHERE email =:email;');

		$statement->bindValue(':code', $code, PDO::PARAM_STR);

		$statement->bindValue(':email', $_POST['email'], PDO::PARAM_STR);

		$statement->execute();

		mail($_POST['email'], 'Password Reset', 'Confirmation code: ' . $code);

		return new View('passwordResetRequested');

	}

}