<html>
    <body>
        <h3>My First PHP Program</h3>
        <h1>Hello, from Princess Shaira Mae Sailela!</h1>
        
            <?php
                echo "Hello, World!";
            ?>
            <br>

        <h3>PHP Variables</h3>
            <?php
                $x = 20;
                $y = 5;
                $sum = $x + $y;
                $difference = $x - $y;
                $product = $x * $y; 
                $quotient = $x / $y;

                echo "The sum is $sum";
            ?>
            <br>
       
        <h3>Simple Arithmetic Operations</h3>
            <?php
                echo "Sum: $sum<br>";
                echo "Difference: $difference<br>";
                echo "Product: $product<br>";
                echo "Quotient: $quotient<br>";
            ?>
            <br>

        <h3>Conditional Statement</h3>
            <?php
                if ($x % $y == 0) {
                    echo "$y is a factor of $x";
                } else {
                    echo "$y is not a factor of $x";
                }
            ?>
            <br>
        
        <h3> PHP Loops</h3>
            <?php
                echo "Multiples of 5 between 1 and 100:<br>";
                for($i = 1; $i <= 100; $i++) {
                    if($i % 5 == 0) {
                        echo "$i <br>";
                    }
                }
            ?>
            <br>
        
        <h3>Arrays</h3>
            <?php
                $products = array("Product A", "Product B", "Product C");
                echo $products[0];
            ?>
            
            <?php
                $products = array("Product A" , "Product B", "Product C");
                $products[1] = "Product D";
                var_dump($products);
            ?>
            <br>

            <?php
                $products = array("Product A", "Product B", "Product C");
                foreach($products as $p) {
                    echo "$p <br>";
                }
            ?>

            <?php
                $products = array("name"=>"Product A", "price"=>10.50, "stock"=>12);
                echo $products["name"];
            ?>

            <?php
                $products = array(
                    array("name"=>"Product A", "price"=>10.50, "stock"=>12),
                    array("name"=>"Product B", "price"=>5.60, "stock"=>7),
                    array("name"=>"Product C", "price"=>7.00, "stock"=>5)
                );
            ?>
            <br>

            <?php
                foreach($products as $p) {
                    echo $p["name"] . " is " . $p["price"] . " pesos <br>";
                }
            ?>
    </body> 
</html>