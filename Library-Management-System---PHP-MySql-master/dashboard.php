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
    // Initialize search variable
    $bookNameSearch = '';
    if(isset($_POST['search']) && !empty($_POST['search'])){
        $bookNameSearch = $_POST['search'];
    }

    // Pagination settings
    $limit = 8; // Number of entries to show in a page.
    if (isset($_GET["page"])) { 
        $page  = $_GET["page"]; 
    } else { 
        $page = 1; 
    };
    $offset = ($page - 1) * $limit;

    // Fetch book details from the database
    if($bookNameSearch != ''){
        $sql = "SELECT id, BookName, CatId, AuthorId, ISBNNumber, BookPrice, RegDate, UpdationDate, BookImage FROM tblbooks WHERE BookName LIKE :bookNameSearch LIMIT :limit OFFSET :offset";
        $query = $dbh->prepare($sql);
        $bookNameSearch = "%$bookNameSearch%";
        $query->bindParam(':bookNameSearch', $bookNameSearch, PDO::PARAM_STR);
    } else {
        $sql = "SELECT id, BookName, CatId, AuthorId, ISBNNumber, BookPrice, RegDate, UpdationDate, BookImage FROM tblbooks LIMIT :limit OFFSET :offset";
        $query = $dbh->prepare($sql);
    }
    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
    $query->bindParam(':offset', $offset, PDO::PARAM_INT);

    // Execute the query and check for errors
    if (!$query->execute()) {
        echo "Error executing query: ";
        print_r($query->errorInfo());
    }
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    // Fetch the total number of records for pagination
    if($bookNameSearch != ''){
        $sql_total = "SELECT COUNT(*) FROM tblbooks WHERE BookName LIKE :bookNameSearch";
        $query_total = $dbh->prepare($sql_total);
        $query_total->bindParam(':bookNameSearch', $bookNameSearch, PDO::PARAM_STR);
    } else {
        $sql_total = "SELECT COUNT(*) FROM tblbooks";
        $query_total = $dbh->prepare($sql_total);
    }

    if (!$query_total->execute()) {
        echo "Error executing total count query: ";
        print_r($query_total->errorInfo());
    }
    $total_results = $query_total->fetchColumn();
    $total_pages = ceil($total_results / $limit);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | User Dashboard</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .book-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin: 15px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .book-card img {
            width: 150px;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .book-card h5 {
            margin-bottom: 10px;
        }
        .book-card p {
            font-size: 14px;
            color: #555;
        }
        .book-card .price {
            color: #d9534f;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .book-card button {
            background-color: #5cb85c;
            border: none;
            color: white;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .pagination {
            display: inline-block;
            margin-top: 20px;
        }
        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">USER DASHBOARD</h4>
                </div>
            </div>

            <!-- Search Form -->
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action="dashboard.php">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by Book Name" value="<?php echo htmlentities($bookNameSearch); ?>">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Search</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <?php 
                if($query->rowCount() > 0)
                {
                    foreach($results as $result)
                    {               
                ?> 
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="book-card">
                        <img src="assets/images/<?php echo htmlentities($result->BookImage);?>" alt="Book Image">
                        <h5><?php echo htmlentities($result->BookName);?></h5>
                        <p>Author ID: <?php echo htmlentities($result->AuthorId);?></p>
                        <p>ISBN: <?php echo htmlentities($result->ISBNNumber);?></p>
                        <p class="price">$<?php echo htmlentities($result->BookPrice);?></p>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="book_id" value="<?php echo htmlentities($result->id); ?>">
                            <button type="submit">Add to Cart</button>
                        </form>
                    </div>
                </div>
                <?php }} else { ?>
                <div class="col-md-12">
                    <div class="alert alert-warning text-center">
                        No books found with the given name.
                    </div>
                </div>
                <?php } ?>
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="pagination">
                        <?php if($total_pages > 1) { ?>
                            <?php for($i=1; $i<=$total_pages; $i++) { ?>
                                <a href="dashboard.php?page=<?php echo $i; ?>" class="<?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END-->
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
