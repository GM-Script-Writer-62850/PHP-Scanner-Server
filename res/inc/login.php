<?php
if(isset($_POST['json'])&&!isset($PAGE)){
	header('Content-type: application/json; charset=UTF-8');
	$file='../../config/.htaccess';
	if(!is_file($file)){// For security reasons
		$file=@fopen($file,'w+');
		@fwrite($file,"<files \"accounts.json\">\n\tDeny from all\n</files>\n");// Options All -Indexes\n
		@fclose($file);
	}
	function Allow(){
		setcookie("Authenticated",time(),0,"/",$_SERVER['SERVER_NAME']);
	}
	$file="../../config/accounts.json";
	$json=json_decode(is_file($file)?file_get_contents($file):'{}');

	$mode=$_POST["mode"];
	$user=$_POST["name"];
	$pass=$_POST["pass"];

	if(strlen($user)==0)
		die('{"message":"You must have a name","error":true}');
	if($mode=="login"){
		if(isset($json->{$user})){
			if($json->{$user}->{"md5"}===md5($pass)){
				$msg="You are now logged in";
				Allow();
			}
			else
				die('{"message":"Invalid password","error":true}');
		}
		else
			die('{"message":"Invalid User Name","error":true}');
	}
	else if($mode=="create"){
		if(isset($json->{$user})){
			die('{"message":"The user name <code>'.htmlspecialchars($user).'</code> is taken","error":true}');
		}
		if(!isset($json->{"root"})&&$user=='root'){
			if($pass!=$_POST["auth"])
				die('{"message":"Authorization was unsuccessful","error":true}');
			$json->{"root"}=array("md5" => md5($pass) );
			$msg="The user '$user' has been created. DON'T FORGET YOUR PASSWORD, YOU SHOULD KNOW BETTER";
		}
		else if(isset($json->{'root'})){
			if(md5($_POST["auth"])===$json->{"root"}->{"md5"}){
				$json->{$user}=array("md5" => md5($pass) );
				$msg="The user <code>".htmlspecialchars($user)."</code> has been created";
			}
		}
		else
			die('{"message":"Authorization was unsuccessful","error":true}');
		Allow();
	}
	else if($mode=="forgot"){
		if(!isset($json->{$user}))
			die('{"message":"Invalid User Name","error":true}');
		if(!isset($json->{'root'}))
			$json->{"root"}=array( "md5" => null );
		if($json->{"root"}->{"md5"}!==md5($_POST["auth"]))
			die('{"message":"Authorization was unsuccessful","error":true}');
		$json->{$user}->{"md5"}=md5($pass);
		if(strlen($_POST["newp"])==0){
			unset($json->{$user});
			$msg="The user <code>".htmlspecialchars($user)."</code> has been deleted";
		}
		else
			$msg="<code>".htmlspecialchars($user)."</code> now has a new password";
		Allow();
	}
	else if($mode=="change"){
		if(!isset($json->{$user}))
			die('{"message":"Invalid User Name","error":true}');
		if($json->{$user}->{"md5"}===md5($pass))
			$json->{$user}->{"md5"}=md5($_POST["newp"]);
		else
			die('{"message":"Invalid password","error":true}');
		if(strlen($_POST["newp"])==0){
			unset($json->{$user});
			$msg="The user <code>".htmlspecialchars($user)."</code> has been deleted";
		}
		else
			$msg="<code>".htmlspecialchars($user)."</code> now has a new password";
		Allow();
	}
	else
		die('{"message":"What mode?","error":true}');
	if($mode!="login"){
		$file=@fopen($file,'w+');
		@fwrite($file,json_encode($json));
		@fclose($file);
		if(is_bool($file))
			die(json_encode(array("message" => "Unable to create <code>$file</code>, go read the instructions.", "error" => true)));
	}
	die(json_encode(array("message" => $msg, "error" => false)));
}
else if(isset($_GET['nojs']))
	header("Location: http://www.enable-javascript.com/");
?><div class="box box-full dualForm"><h2>Authorization Required</h2>
<form action="res/inc/login.php?nojs=true" method="POST" onsubmit="return login(this);">
<h3>Login</h3><p>
<input type="hidden" name="mode" value="login"/>
<span>User Name:</span><input type="text" name="name"/><br/>
<span>Password:</span><input type="password" name="pass" ondblclick="this.type=(this.type=='text'?'password':'text')"/><br/>
<input type="submit" value="Login"/>
</p></form>
<form class="m" action="res/inc/login.php" method="POST" onsubmit="return login(this);" autocomplete="off">
<h3>Create Account</h3><p>
<input type="hidden" name="mode" value="create"/>
<span>User Name:</span><input type="text" name="name"/><br/>
<span>Password:</span><input type="password" name="pass" ondblclick="this.type=(this.type=='text'?'password':'text')"/><br/>
<span>Authorization:</span><input type="password" name="auth" ondblclick="this.type=(this.type=='text'?'password':'text')"/><br/>
<input type="submit" value="Register"/>
</p></form>
<div class="footer">Double click a password blank to toggle show/hide password</div>
</div>

<div class="box box-full dualForm"><h2>Account Recovery</h2>
<form action="res/inc/login.php?nojs=true" method="POST" onsubmit="return login(this);" autocomplete="off">
<h3>Change Password</h3><p>
<input type="hidden" name="mode" value="change"/>
<span>User Name:</span><input type="text" name="name"/><br/>
<span>Old Password:</span><input type="password" name="pass" ondblclick="this.type=(this.type=='text'?'password':'text')"/><br/>
<span>New Password:</span><input type="password" name="newp" ondblclick="this.type=(this.type=='text'?'password':'text')"/><br/>
<input type="submit" value="Change"/>
</p></form>
<form class="m" action="res/inc/login.php?nojs=true" method="POST" onsubmit="return login(this);" autocomplete="off">
<h3>Forgot Password</h3><p>
<input type="hidden" name="mode" value="forgot"/>
<span>User Name:</span><input type="text" name="name"/><br/>
<span>New Password:</span><input type="password" name="pass" ondblclick="this.type=(this.type=='text'?'password':'text')"/><br/>
<span>Authorization:</span><input type="password" name="auth" ondblclick="this.type=(this.type=='text'?'password':'text')"/><br/>
<input type="submit" value="Change"/>
</p></form>
<div class="footer">Leave 'New Password' blank to delete the account</div>
</div>
