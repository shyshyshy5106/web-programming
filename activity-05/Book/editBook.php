<?php
require_once "../classes/book.php";
$booksObj = new Books();

$books = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["id"]))
    {
        $pid = trim(htmlspecialchars($_GET["id"]));
        $books = $booksObj->fetchBook($pid);
        if(!$books)
        {
            echo "<a href= 'viewBook.php' > View Book </a>";
            exit("Book Not Found!");
        }
    } else {
            echo "<a href= 'viewBook.php' > Return to View Book </a>";
            exit("Book Not Found!");
        }
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $pid = trim(htmlspecialchars($_POST["id"]));
    $books["title"] = trim(htmlspecialchars($_POST["title"]));
    $books["author"] = trim(htmlspecialchars($_POST["author"]));
    $books["genre"] = trim(htmlspecialchars($_POST["genre"]));
    $books["publication_year"] = trim(htmlspecialchars($_POST["publication_year"]));
    $books["publisher"] = trim(htmlspecialchars($_POST["publisher"]));
    $books["copies"] = trim(htmlspecialchars($_POST["copies"]));

    if(empty($books["title"])) {
        $errors["title"] = "Title is required.";
    } elseif ($booksObj->isBookExist($books["title"], $_GET["id"])){
        $errors["title"] = "Book Title already exist.";
    }

    if(empty($books["author"])) {
        $errors["author"] = "Author is required.";
    }

    if(empty($books["genre"])) {
        $errors["genre"] = "Genre is required.";
    }

    if (empty($books["publication_year"])) {
        $errors["publication_year"] = "Publication year is required.";
    } elseif  (strtotime($books["publication_year"]) > strtotime(date("Y-m-d"))) {
        $errors["publication_year"] = "Publication year cannot be in the future.";
    }


    if (empty($books["copies"])) {
        $errors["copies"] = "Number of copies is required.";
    } elseif (!filter_var($books["copies"], FILTER_VALIDATE_INT)) {
        $errors["copies"] = "Number of copies must be a valid integer.";
    } elseif ($books["copies"] < 1) {
        $errors["copies"] = "Number of copies must be at least 1.";
    }

    if(empty(array_filter($errors))){
        $booksObj->title = $books["title"];
        $booksObj->author = $books["author"];
        $booksObj->genre = $books["genre"];
        $booksObj->publication_year = $books["publication_year"];
        $booksObj->publisher = $books["publisher"];
        $booksObj->copies = $books["copies"];
        
        if($booksObj->editBook($_GET["id"])){
           header("Location: viewBook.php");
        } 
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Books</title>
    <style>
    label {display:block; }
    span, .error {color: red; margin: 0;}
    </style>
    <link rel="stylesheet" href="styleAdd.css">
</head>
<body>
    <div class="title"> <h1>Add Books </h1> </div>
    <div class="form-container">
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $books["id"] ?? ""?>">
            <div class="anti-button">
                <label for=""><h6 class="asterisk">Field with <span>*</span> is required</h6></label><br>

                <label for="title">Book Title <span>*</span></label>
                <input class="csspurposes" type="text" name="title" id="title" value="<?= $books["title"] ?? "" ?>">
                <p class="error"><?= $errors["title"] ?? ""?></p>

                <label for="author">Author <span>*</span></label>
                <input class="csspurposes" type="text" name="author" id="author" value="<?= $books["author"] ?? "" ?>">
                <p class="error"><?= $errors["author"] ?? ""?></p>

                <label for="genre">Genre <span>*</span></label>
                <select class="csspurposes" name="genre" id="genre">
                    <option value="">---Select Genre---</option>
                    <option value="History" <?= (isset($books["genre"]) && $books["genre"] == "History")? "selected":""?>>History</option>
                    <option value="Science" <?= (isset($books["genre"]) && $books["genre"] == "Science")? "selected":""?>>Science</option>
                    <option value="Fiction" <?= (isset($books["genre"]) && $books["genre"] == "Fiction")? "selected":""?>>Fiction</option>
                </select>
                <p class="error"><?= $errors["genre"] ?? ""?></p>


                <label for="publication_year">Publication Year <span>*</span></label> 
                <input class="csspurposes" type="date" name="publication_year" id="publication_year" value="<?= $books["publication_year"] ?? "" ?>">
                <p class="error"><?= $errors["publication_year"] ?? ""?></p>
                
                <label for="publisher">Publisher</label>
                <input class="csspurposes" type="text" name="publisher" id="publisher" value="<?= $books["publisher"] ?? ""?>">

                <label for="copies">Copies <span>*</span></label>
                <input class="csspurposes" type="text" name="copies" id="copies" value="<?= $books["copies"] ?? ""?>">
                <p class="error"><?= $errors["copies"] ?? ""?></p> <br><br>
            </div>
                <input type="submit" value="Save Book"> <span><button><a href="viewBook.php">View Books</a></button></span>
        </form>
    </div>
</body>
</html>
