<?php
require_once "../classes/book.php";
$booksObj = new Books();
$search = $genre = "";
if($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"])? trim(htmlspecialchars($_GET["search"])) : "";
    $genre = isset($_GET["genre"])? trim(htmlspecialchars($_GET["genre"])) : "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link rel="stylesheet" href="styleView.css">
</head>
<body>
    <div class="divheader"><h1 class="title">View Books</h1></div>

    <form action="" method="get">
        <div class="searchbox">
            <label for="">Search:</label>
            <input type="search" name="search" id="search" value="<?= $search ?>">
            <select name="genre" id="genre">
                <option value="">All</option>
                <option value="History" <?= (isset($genre) && $genre == "History")? "selected":"" ?>>History</option>
                <option value="Science" <?= (isset($genre) && $genre == "Science")? "selected":"" ?>>Science</option>
                <option value="Fiction" <?= (isset($genre) && $genre == "Fiction")? "selected":"" ?>>Fiction</option>
            </select>
            <input type="submit" value="Search"> <span><button><a href="addBook.php">Add Book</a></button></span>
        </div>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Book Title</th>
                <th>Book Author</th>
                <th>Book Genre</th>
                <th>Publication Date</th>
                <th>Publisher</th>
                <th>Copies</th>
            </tr>
        </thead>
        <?php
        $id = 1;
        foreach($booksObj->viewBook($search, $genre)as $books) {
        ?>
        
            <tr class="dataRow">
                <td> <?= $id++ ?> </td>
                <td class="leftalign"> <?= $books["title"] ?> </td>
                <td class="leftalign"> <?= $books["author"] ?> </td>
                <td> <?= $books["genre"] ?> </td>
                <td> <?= $books["publication_year"] ?> </td>
                <td class="leftalign"> <?= $books["publisher"] ?> </td>
                <td> <?= $books["copies"] ?> </td>
            </tr>
        
        <?php
        }
        ?>
    
</body>
</html>
