<?php

namespace example;

class Site
{
    public function index()
    {
        $html = <<<EOF
        <h1>Welcome to main Page</h1>
        <ul>
        <li><a href="products">See the Products</a></li>
        <li><a href="id/123">Route with number param</a></li>
        <li><a href="name/Francis">Route with string param</a></li>
        <li><a href="post/this-is-a-title">Route with string and special chars param</a></li>
        <li><a href="show/mini/128">Route with two params</a></li>
        <li><a href="client/555-8787">Route with custom params</a></li>
        </ul>
        EOF;
        echo $html;
    }
    public function productShow()
    {
        if(!isset($_SESSION['products'])){
            $products = [
                "book" => "15,00",
                "table" => "45,00",
                "pencil" => "0,95"
            ];
            $_SESSION['products'] = $products;
        }
        $html1 = <<<EOF
        <h1>Products</h1>
        <table>
            <tr>
            <th>Product</th><th>Price</th>
            <tr>
        EOF;
        $html2 = "";
        foreach($_SESSION['products'] as $productKey => $productValue){
            $html2 .= "<tr><td>".$productKey."</td><td>".$productValue."</td></tr>";
        }
        $html3 = <<<EOF
        </table>
        <br/>
        <h1>New Product</h1>
        <form action="product/new" method="POST">
        <label>Product</label> <input type="text" name="product">
        <label>Price</label> <input type="text" name="price"><br/>
        <input type="submit" value="Save">
        </form>
        EOF;
        echo $html1.$html2.$html3;
    }

    public function productNew()
    {
        global $router;
        $product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
        if(isset($_SESSION['products'])){
            $_SESSION['products'][$product] = $price;
        }
        $router->redirect('/products');
    }

    public function productOthers()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        header('Content-Type: application/json;charset=utf-8');
        $message = ["This is a $method method"];
        echo json_encode($message);
    }

    public function withNumber($param)
    {
        echo "<h1>This is a route with number param</h1>";
        echo "<p><strong>Param:</strong> $param[0] </p>";
    }

    public function withString($param)
    {
        echo "<h1>This is a route with string param</h1>";
        echo "<p><strong>Param:</strong> $param[0] </p>";
    }

    public function withStringAndSpecial($param)
    {
        echo "<h1>This is a route with string and special chars(_ and -) param</h1>";
        echo "<p><strong>Param:</strong> $param[0] </p>";
    }

    public function withTwoParams($param)
    {
        echo "<h1>This is a route with two params</h1>";
        echo "<p><strong>Param 1:</strong> $param[0] </p>";
        echo "<p><strong>Param 2:</strong> $param[1] </p>";
    }

    public function withCustom($param)
    {
        echo "<h1>This is a route with custom param</h1>";
        echo "<p><strong>Phone:</strong> $param[0] </p>";
    }
    public function error()
    {
        if(isset($_SESSION['errors'])){
            echo "Errors found:<br/>";
            foreach ($_SESSION['errors'] as $error) {
                echo "<strong>Error ".$error->code.":</strong> ".$error->message,"<br/>";
            }
            unset($_SESSION['errors']);
        }
    }
}