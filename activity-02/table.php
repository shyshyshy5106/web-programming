<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dynamic Table</title>
</head>

<body>

    <?php
        $products = array(
            array("name"=>"Product A", "price"=>10.50, "stock"=>12),
            array("name"=>"Product B", "price"=>5.60, "stock"=>7),
            array("name"=>"Product C", "price"=>7.00, "stock"=>5),
            array("name"=>"Nissin Cup Noodles (Spicy Creamy Seafood)", "price"=>30.00, "stock"=>6),
            array("name"=>"Penoy w/ *GoodLuck* sauce", "price"=>36.00, "stock"=>8),
            array("name"=>"Famous Belgian Waffle", "price"=>75.00, "stock"=>5)
        );
    ?>

    <table border=1s>
        <tr>
            <th>No.</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Stock</th>
        </tr>

        <?php
            $no = 0;
            foreach($products as $p) {
                $no++;
                $low = ($p["stock"] < 10) ? "style='color: red'": ""
        ?>      

            <tr <?= $low ?>>
                <td class="center"><?= $no ?></td>
                <td><?= $p["name"] ?></td>
                <td class="center"><?= $p["price"] ?></td>
                <td class="center"><?= $p["stock"] ?></td>
            </tr>
        <?php
            }
        ?>
    </table>

</body>
</html>