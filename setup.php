<?php

require_once(__DIR__ . '/../bootstrap.php');

use MongoAppKit\Input,
	Kickipedia2\Models\UserDocument,
	Kickipedia2\Models\UserDocumentList;

$sErrorMessage = null;

if(isset($_POST['setup'])) {
	$bCreateUser = true;
	$aUserData = Input::getInstance()->getPostData('user');

	foreach(array('name', 'password', 'email') as $sRequiredField) {
		if(!isset($aUserData[$sRequiredField]) || empty($aUserData[$sRequiredField])) {
			$sErrorMessage = "Required field {$sRequiredField} is empty!";
			$bCreateUser = false;
			break;
		}
	}

	try {		
		$oUserList = new UserDocumentList();
		$aUserDocument = $oUserList->getUser($aUserData['name']);
		$bCreateUser = false;
		$sErrorMessage = "User {$aUserData['name']} already exists!";
	} catch(\InvalidArgumentException $e) {
		// no user found, proceed to create user
	}

	if($bCreateUser === true) {
		$sToken = md5("{$aUserData['name']}:Kickipedia2:{$aUserData['password']}");

		$entry = new UserDocument();
		$entry->setProperty('name', $aUserData['name']);
		$entry->setPassword($aUserData['password']);
		$entry->setProperty('token', $sToken);
		$entry->setProperty('email', $aUserData['email']);
		$entry->save();

		if(!unlink(__FILE__)) {
			$sErrorMessage = 'Could not delete setup.php automaticly. Please delete it manually for security reasons!';
		} else {
			header('Location: /home');
		}
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Setup</title>
	</head>
	<body>
		<?php if($sErrorMessage !== null): ?>
			<p class="error">
				<?php echo $sErrorMessage; ?>
			</p>
		<?php endif;?>
		<form action="/setup.php?create" method="post">
			<h1>Kickipedia 2 setup</h1>
			<fieldset>
				<ul>
					<li>
						<input type="text" name="user[name]" placeholder="Name">
					</li>
					<li>
						<input type="text" name="user[email]" placeholder="E-Mail address">
					</li>
					<li>
						<input type="password" name="user[password]" placeholder="Password">
					</li>
				</ul>
				<input type="submit" name="setup" value="Create user">
			</fieldset>
		</form>
	</body>
</html>