<?php
session_start();
$product_ids = array();
//session_destroy();
//Check if add to cart button has submitted
    if(filter_input(INPUT_POST, 'addCart')) {
        if(isset($_SESSION['shopping_cart'])) {
            //keep track of how many products are in the shopping cart.
            $count = count($_SESSION['shopping_cart']);

            // create sequential array for matching arry keys to products ids'.
            $product_ids = array_column($_SESSION['shopping_cart'], 'id');

            //if product with the id does exist.
            if(!in_array(filter_input(INPUT_GET, 'id'), $product_ids)) {
                $_SESSION['shopping_cart'][$count] = array
                (
                    'id' => filter_input(INPUT_GET, 'id'),
                    'product_name' => filter_input(INPUT_POST, 'product_name'),
                    'price' => filter_input(INPUT_POST, 'price'),
                    'qty' => filter_input(INPUT_POST, 'qty')
                ); 
            } else { //product already exist, increase quantity.
                // match array key to id of the product being addend to the cart.
                for($i=0; $i<count($product_ids); $i++) {
                    if($product_ids[$i] == filter_input(INPUT_GET, 'id')){
                        //add item quantity to the existing product in the array
                        $_SESSION['shopping_cart'][$i]['qty'] += filter_input(INPUT_POST, 'qty');
                    }
                }
            }


        } else { //if shopping cart doesn't exist, create the first product with arry key 0.
            //create array using submitted form data, start from 0 and fill it with values.
            $_SESSION['shopping_cart'][0] = array
            (
                'id' => filter_input(INPUT_GET, 'id'),
                'product_name' => filter_input(INPUT_POST, 'product_name'),
                'price' => filter_input(INPUT_POST, 'price'),
                'qty' => filter_input(INPUT_POST, 'qty')
            );
        }
    }
    //pre_r($_SESSION);

    if(filter_input(INPUT_GET, 'action') == 'delete') {
        // loop through all the products on the shopping cart until it matches with GET id variable
        foreach($_SESSION['shopping_cart'] as $key => $product) {
            if($product['id'] == filter_input(INPUT_GET, 'id')) {
                //remove product from the shopping cart when it matches with GET id.
                unset($_SESSION['shopping_cart'][$key]);
            }
        }
        // reset sessionn array keys so they match to $product_ids numeric array.
        $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
    }

    function pre_r($array){
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
?>

<?php include('location/head.php'); ?>
    <div class="container">
        <?php
        include('includes/connect.php');

            $sql = 'SELECT * FROM products ORDER BY id ASC';
            $result = mysqli_query($connect, $sql);

            if($result):
                if(mysqli_num_rows($result) > 0):
                    while($product = mysqli_fetch_assoc($result)):
                    ?>
                        <div class="col-sm-4 col-md-3 float-left">
                            <form method="POST" action="cart.php?action=add&id=<?php echo $product['id']; ?>">
                                <div class="products">
                                    <img src="images/products/<?php echo $product['image']; ?>" class="img-fluid">
                                    <h4 class="text-info" style="padding-top: 10px;"><?php echo $product['product_name']; ?> </h4>
                                    <h4>₱ <?php echo $product['price']; ?></h4>
                                    <input type="text" name="qty" class="form-control" value="1">
                                    <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?> ">
                                    <input type="hidden" name="price" value="<?php echo $product['price']; ?>" >
                                    <input type="submit" name="addCart" class="btn btn-info" style="margin-top: 10px;" value="Add to Cart"> 
                                </div>
                            </form>
                        </div>
                    <?php    
                    endwhile;
                endif;
            endif;
        ?>
        <div style="clear:both;"></div><br/>
            <div class="container-fluid">
                
                
                <table class="table table-stripe table-hover">
                    <h2>Order Details</h2>
                        <tr>
                            <th width="40%">Product Name</th>
                            <th width="10%">Quantity</th>
                            <th width="20%">Price</th>
                            <th width="15%">Total</th>
                            <th width="5%">Action</th>
                        </tr>
                        <?php
                            if(!empty($_SESSION['shopping_cart'])):
                                $total=0;
                            foreach($_SESSION['shopping_cart'] as $key => $product):
                        ?>
                        <tbody>
                            <tr>
                                <td><?php echo $product['product_name']; ?></td>
                                <td><?php echo $product['qty']; ?></td>
                                <td><?php echo $product['price']; ?></td>
                                <td><?php echo number_format($product['qty'] * $product['price'], 2); ?></td>
                                <td>
                                    <a href="cart.php?action=delete&id=<?php echo $product['id']; ?>">
                                        <div class="btn-danger text-center">Remove</div>
                                    </a>
                                </td>
                            </tr>
                            <?php
                                $total = $total + ($product['qty'] * $product['price']);
                                endforeach;
                            ?>
                            <tr>
                                <td colspan="3" align="right">Total</td>
                                <td align="right">₱ <?php echo number_format($total, 2); ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <?php
                                        if(isset($_SESSION['shopping_cart'])):
                                            if(count($_SESSION['shopping_cart'])):
                                    ?>
                                                <a href="#" align="center">See more</a>
                                            <?php endif; endif;?>
                                </td>
                            </tr>
                            <?php
                                endif;
                            ?>
                        </tbody>
                </table>    
            </div>
    </div>
<?php include('location/foot.php'); ?>