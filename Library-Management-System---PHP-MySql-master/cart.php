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
    if(isset($_POST['book_id']) && !empty($_POST['book_id'])){
        $book_id = $_POST['book_id'];
        
        // Fetch book details from the database
        $sql = "SELECT BookName, CatId, AuthorId, ISBNNumber, BookPrice, BookImage FROM tblbooks WHERE id = :book_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            echo "Book not found.";
            exit;
        }

        $book_name = $result->BookName;
        $author_id = $result->AuthorId;
        $isbn = $result->ISBNNumber;
        $book_price = $result->BookPrice;
        $book_image = $result->BookImage;
    } else {
        echo "No book selected.";
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
    <title>Online Library Management System | Cart</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .cart-container {
            margin-top: 50px;
        }
        .cart-container table {
            width: 100%;
            margin-bottom: 30px;
        }
        .cart-container th, .cart-container td {
            padding: 10px;
            text-align: left;
        }
        .cart-container th {
            background-color: #f2f2f2;
        }
        .total-amount {
            font-weight: bold;
            color: #d9534f;
        }
        .checkout-btn {
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
    </style>
    <script>
        function calculateTotal() {
            var start_date = new Date(document.getElementById('start_date').value);
            var return_date = new Date(document.getElementById('return_date').value);
            var total_days = 0;
            if (start_date && return_date && start_date <= return_date) {
                total_days = Math.ceil((return_date - start_date) / (1000 * 60 * 60 * 24));
            }
            var book_price = parseFloat("<?php echo $book_price; ?>");
            var deposit = 350;
            var total_amount = (total_days * book_price) + deposit;
            document.getElementById('total_days').innerText = total_days;
            document.getElementById('total_amount').innerText = total_amount.toFixed(2);
            document.getElementById('hidden_total_days').value = total_days;
            document.getElementById('hidden_total_amount').value = total_amount.toFixed(2);
            document.getElementById('hidden_book_id').value = "<?php echo $book_id; ?>";
        }
    </script>
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="container cart-container">
        <h2>Your Cart</h2>
        <form action="checkout.php" method="post">
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
                    <td><input type="date" id="start_date" name="start_date" onchange="calculateTotal()" required></td>
                    <td><input type="date" id="return_date" name="return_date" onchange="calculateTotal()" required></td>
                    <td><span id="total_days">0</span></td>
                    <td><span id="total_amount">0.00</span></td>
                </tr>
            </table>
            <input type="hidden" name="book_image" value="<?php echo htmlentities($book_image); ?>">
            <input type="hidden" name="book_name" value="<?php echo htmlentities($book_name); ?>">
            <input type="hidden" name="book_price" value="<?php echo htmlentities($book_price); ?>">
            <input type="hidden" name="author_id" value="<?php echo htmlentities($author_id); ?>">
            <input type="hidden" name="isbn" value="<?php echo htmlentities($isbn); ?>">
            <input type="hidden" name="total_days" id="hidden_total_days">
            <input type="hidden" name="total_amount" id="hidden_total_amount">
            <input type="hidden" name="book_id" id="hidden_book_id">
            <button class="checkout-btn" type="submit">Checkout</button>
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
