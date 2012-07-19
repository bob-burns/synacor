<?php

require_once 'UserControllerLibs.php';

class UserControllerRefactored {

	protected $gateway;
	protected $crypthelper;

    public function __construct(
        UsersTableDataGateway $gateway,
        CryptHelper $cryptHelper,
        Mailer $mailer
    ) {

      $this->gateway        = $gateway;
      $this->crypthelper    = $cryptHelper;
      $this->mailer         = $mailer;

    }

	public function resetPassword(Request $request) {

		if (!$request->hasPost('email')) {

			return new ErrorView('resetPassword', 'No email specified');

		}

		$email = $request->getPost('email');
		$record = $this->gateway->findUserByEmail($email);

		if ($record === FALSE) {

			return new ErrorView('resetPassword', 'No user with email ' . $email);

		}

		$code = $this->crypthelper->getConfirmationCode();

		$this->gateway->updateUserWithConfirmationCode($email, $code);
		$this->mailer->send($email, 'Password Reset', $code);

		return new View('passwordResetRequested');

	}

}