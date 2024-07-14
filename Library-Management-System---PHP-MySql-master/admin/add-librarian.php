<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
    {   
header('location:index.php');
}
else{ 

if(isset($_POST['create']))
{
$fullname=$_POST['fullname'];
$email=$_POST['email'];
$address=$_POST['address'];
$city=$_POST['city'];
$pincode=$_POST['pincode'];
$phone=$_POST['phone'];
$birthday=$_POST['birthday'];

// Format birthday as ddmmyy
$day = date('d', strtotime($birthday));
$month = date('m', strtotime($birthday));
$year = date('y', strtotime($birthday));
$password = $day . $month . $year;

$sql="INSERT INTO tbllibrarians(FullName, Email, Address, City, Pincode, PhoneNumber, Password) VALUES(:fullname, :email, :address, :city, :pincode, :phone, :password)";
$query = $dbh->prepare($sql);
$query->bindParam(':fullname',$fullname,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':address',$address,PDO::PARAM_STR);
$query->bindParam(':city',$city,PDO::PARAM_STR);
$query->bindParam(':pincode',$pincode,PDO::PARAM_STR);
$query->bindParam(':phone',$phone,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$_SESSION['msg']="Librarian added successfully";
header('location:manage-librarians.php');
}
else 
{
$_SESSION['error']="Something went wrong. Please try again";
header('location:manage-librarians.php');
}

}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Add Librarian</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>
<body>
      <!------MENU SECTION START-->
<?php include('includes/header.php');?>
<!-- MENU SECTION END-->
    <div class="content-wrapper">
         <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Add Librarian</h4>
                
                            </div>

</div>
<div class="row">
<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
<div class="panel panel-info">
<div class="panel-heading">
Librarian Info
</div>
<div class="panel-body">
<form role="form" method="post">
<div class="form-group">
<label>Full Name</label>
<input class="form-control" type="text" name="fullname" autocomplete="off" required />
</div>

<div class="form-group">
<label>Email</label>
<input class="form-control" type="email" name="email" autocomplete="off" required />
</div>

<div class="form-group">
<label>Address</label>
<input class="form-control" type="text" name="address" autocomplete="off" required />
</div>

<div class="form-group">
<label>City</label>
<input class="form-control" type="text" name="city" autocomplete="off" required />
</div>

<div class="form-group">
<label>Pincode</label>
<input class="form-control" type="text" name="pincode" autocomplete="off" required />
</div>

<div class="form-group">
<label>Phone Number</label>
<input class="form-control" type="text" name="phone" autocomplete="off" required />
</div>

<div class="form-group">
<label>Birthday</label>
<input class="form-control" type="date" name="birthday" autocomplete="off" required />
</div>

<button type="submit" name="create" class="btn btn-info">Add </button>

</form>
</div>
</div>
</div>

</div>

</div>
</div>
<!-- CONTENT-WRAPPER SECTION END-->
<?php include('includes/footer.php');?>
<!-- FOOTER SECTION END-->
<!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME -->
<!-- CORE JQUERY -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<!-- CUSTOM SCRIPTS -->
<script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
