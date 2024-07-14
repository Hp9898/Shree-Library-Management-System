<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{
    header('location:index.php');
}
else
{
    // Fetch user details from the database
    $sql = "SELECT StudentId, FullName, EmailId, MobileNumber FROM tblstudents WHERE EmailId = :emailid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':emailid', $_SESSION['login'], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if (!$result) {
        echo "User not found. Please check the session variable and database.";
        exit;
    }

    $student_id = $result->StudentId;
    $full_name = $result->FullName;
    $email_id = $result->EmailId;
    $mobile_number = $result->MobileNumber;

    // Fetch cart details from POST request
    $book_image = $_POST['book_image'];
    $book_name = $_POST['book_name'];
    $book_price = $_POST['book_price'];
    $author_id = $_POST['author_id'];
    $isbn = $_POST['isbn'];
    $start_date = $_POST['start_date'];
    $return_date = $_POST['return_date'];
    $total_days = $_POST['total_days'];
    $total_amount = $_POST['total_amount'];
    $book_id = $_POST['book_id'];

    // Handle form submission to issue book
    if(isset($_POST['issued_book'])) {
        // Insert the issued book details into the database
        $sql = "INSERT INTO tblissuedbookdetails(StudentID, BookId, IssuesDate, ReturnDate, ReturnStatus, fine) VALUES (:student_id, :book_id, :issues_date, :return_date, :return_status, :fine)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $query->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $query->bindParam(':issues_date', $start_date, PDO::PARAM_STR);
        $query->bindParam(':return_date', $return_date, PDO::PARAM_STR);
        $query->bindParam(':return_status', $return_status, PDO::PARAM_INT);
        $query->bindParam(':fine', $total_amount, PDO::PARAM_STR);

        $return_status = 0; // Assuming 0 indicates not returned
        $query->execute();

        // Redirect to issued_books.php
        header('location:issued-books.php');
        exit;
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Checkout</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .checkout-container {
            margin-top: 50px;
        }
        .checkout-container table {
            width: 100%;
            margin-bottom: 30px;
        }
        .checkout-container th, .checkout-container td {
            padding: 10px;
            text-align: left;
        }
        .checkout-container th {
            background-color: #f2f2f2;
        }
        .total-amount {
            font-weight: bold;
            color: #d9534f;
        }
        .print-btn, .submit-btn {
            background-color: #5cb85c;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .submit-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="container checkout-container">
        <h2>Checkout</h2>
        <form method="post" action="">
            <h3>User Details</h3>
            <table border="1">
                <tr>
                    <th>Student ID</th>
                    <td><?php echo htmlentities($student_id); ?></td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td><?php echo htmlentities($full_name); ?></td>
                </tr>
                <tr>
                    <th>Email ID</th>
                    <td><?php echo htmlentities($email_id); ?></td>
                </tr>
                <tr>
                    <th>Mobile Number</th>
                    <td><?php echo htmlentities($mobile_number); ?></td>
                </tr>
            </table>
            <h3>Book Details</h3>
            <table border="1">
                <tr>
                    <th>Image</th>
                    <th>Book Name</th>
                    <th>Book Price</th>
                    <th>Author ID</th>
                    <th>ISBN</th>
                    <th>Start Date</th>
                    <th>Return Date</th>
                    <th>Total Days</th>
                    <th>Total Amount</th>
                </tr>
                <tr>
                    <td><img src="assets/images/<?php echo htmlentities($book_image); ?>" alt="Book Image" style="width: 100px; height: 150px;"></td>
                    <td><?php echo htmlentities($book_name); ?></td>
                    <td><?php echo htmlentities($book_price); ?></td>
                    <td><?php echo htmlentities($author_id); ?></td>
                    <td><?php echo htmlentities($isbn); ?></td>
                    <td><?php echo htmlentities($start_date); ?></td>
                    <td><?php echo htmlentities($return_date); ?></td>
                    <td><?php echo htmlentities($total_days); ?></td>
                    <td><?php echo htmlentities($total_amount); ?></td>
                </tr>
            </table>
            <h3>Payment Mode</h3>
            <p>Cash</p>
            <button class="print-btn" type="button" onclick="window.print()">Print</button>
            <button class="submit-btn" type="submit" name="issue_book">Issue Book</button>
            <input type="hidden" name="book_id" value="<?php echo htmlentities($book_id); ?>">
        </form>
    </div>
    <!-- FOOTER SECTION START -->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END -->
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
