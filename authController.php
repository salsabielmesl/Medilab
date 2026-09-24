<?php

require 'db.php';

$errors=[];
$username="";
$Email_Address="";
$password="";
$DateofBirth="";
$Gender="";


if(isset($_POST['signup-btn'])){
    $username=$_POST['username'];
    $Email_Address=$_POST['Email_Address'];
    $password=$_POST['password'];
    $DateofBirth=$_POST['DateofBirth'];
    $Gender=$_POST['Gender'];
}


 if(empty($username)){
	   $errors['username']="Username required";
	   
  }

 if(empty($Email_Address)){
	   $errors['Email_Address']="Email_Address required";
	   
  }
  if(!filter_var($Email_Address, FILTER_VALIDATE_EMAIL)){
    $errors['Email_Address']="Email address is invalid";
  }

 if(empty($password)){
	   $errors['password']="Password required";
	   
  }

   if(empty($DateofBirth)){
	   $errors['DateofBirth']="DateofBirth required";
	   
  }

   if(empty($Gender)){
	   $errors['Gender']="Gender required";
	   
  }

   $Email_AddressQuery="SELECT * FROM users WHERE Email_Address=? LIMIT 1";
  
  $stmt=$conn->prepare($Email_AddressQuery);
  $stmt->bind_param("s",$Email_Address);
  $stmt->execute();
  $result=$stmt->get_result();
  $userCount=$result->num_rows;
  $stmt->close();
  
  if($userCount>0){
	  $errors['Email_Address']="Email already exists";
  }  
  if(count($errors)===0){
	 $password=password_hash($password, PASSWORD_DEFAULT);
	 $verified=false; 
	 $sql="INSERT INTO users (username,Email_Address,password,DateofBirth,Gender) values(?,?,?,?,?)";
	 $stmt=$conn->prepare($sql);
     $stmt->bind_param("sssss",$username,$Email_Address,$password,$DateofBirth,$Gender);
    if( $stmt->execute()){
		$sign_up_id=$conn->insert_id;
	    $_SESSION['id']=$sign_up_id;
		$_SESSION['Email_Address']=$Email_Address;
        $_SESSION['password']=$password;
        $_SESSION['verified']=$verified;
		$_SESSION['message']="You are now logged in!";
		$_SESSION['alert-class']="alert-success";
		header('location: homepage.php');
		exit();
  }else{
	$errors['db_error']="Database error:failed to register";  
  }
}
?>