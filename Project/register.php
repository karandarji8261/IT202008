<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(	   isset($_POST['email']) 
	&& isset($_POST['password'])
	&& isset($_POST['confirm'])
	){
	$pass = $_POST['password'];
	$conf = $_POST['confirm'];
	if($pass != $conf){
		//echo "All good, 'registering user'";
		
		$msg = "Passwords don't match, what's going on there?";
	}
	else{
		$msg = "All good, user registered, whoohoo";
		//let's hash it
		$pass = password_hash($pass, PASSWORD_BCRYPT);
		echo "<br>$pass<br>";
		//it's hashed
		require("config.php");
		$connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
		try {
			$db = new PDO($connection_string, $dbuser, $dbpass);
			$stmt = $db->prepare("INSERT INTO `bank users`
							(email, password) VALUES
							(:email, :password)");
			$email = $_POST['email'];
			$params = array(":email"=> $email, 
						":password"=> $pass);
			$stmt->execute($params);
			echo "<pre>" . var_export($stmt->errorInfo(), true) . "</pre>";
		}
		catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
}
?>
<html>
	<head>
		<title> Karan Bank - Register</title>
		<style>
		body{
			background-color: black;
			background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcT07UGkZPH7pGhmu6Wsk7VNKiyHsuzkPHlsg_dxRripAP1ZRCHf');
			color: white;
		}
		</style>
		<script>
			function doValidations(form){
				let isValid = true;
				if(!verifyEmail(form)){
					isValid = false;
				}
				if(!verifyPasswords(form)){
					isValid = false;
				}
				return isValid;
			}
			function verifyEmail(form){
				let ee = document.getElementById("email_error");
				if(form.email.value.trim().length  == 0){
					ee.innerText = "Please enter an email address";
					return false;
				}
				else{
					ee.innerText = "";
					return true;
				}
			}
			function verifyPasswords(form){
				let pe = document.getElementById("password_error");
				if(form.password.value.length == 0 || form.confirm.value.length == 0){
					//alert("You must enter both a password and confirmation password");
					pe.innerText = "You must enter both a password and a confirm password";
					return false;
				}
				if(form.password.value != form.confirm.value){
					//alert("Uhh you made a typo");
					pe.innerText = "Passwords don't match, please try again.";
					return false;
				}
				pe.innerText = "";
				return true;
			}
		</script>
	</head>
	<body onload="findFormsOnLoad();">
		<!-- This is how you comment -->
		<form name="regform" id="myForm" method="POST"
					onsubmit="return doValidations(this)">
			<div style="position: absolute; left: 190; top: 30; ">
				<label for="email">EMAIL: </label><br>
				<input type="email" id="email" name="email" placeholder="Enter Email"/>
				<span id="email_error"></span>
			</div>
			<div style = "position: absolute; left: 190; top: 120;">
				<label for="pass">PASSWORD: </label><br>
				<input type="password" id="pass" name="password" placeholder="Enter password"/>
			</div>
			<div style = "position: absolute; left: 190; top: 210;">
			<label for="conf"> CONFIRM PASSWORD: </label><br>
				<input type="password" id="conf" name="confirm"/>
				<span id="password_error"></span>
			</div>
			<div style= "position: absolute; left:190; top: 230;"> >
				<div>&nbsp;</div>
				<input type="submit" value="Register"/>
			</div>
		</form>
		<?php if(isset($msg)):?>
			<span><?php echo $msg;?></span>
		<?php endif;?>
	</body>
</html>
