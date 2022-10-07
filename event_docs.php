<?php
$conn = mysqli_connect('localhost', 'root', '', 'pizza_db');
if (isset($_POST['id'])) {
    $output = "";
    $query = "SELECT * FROM products WHERE category_id ='".$_POST['id']."'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $output .= "<div class='box'>
                <div class='price'>
                    " . $row['price'] . " \ ريال </div>
                     <img height='250' src='uploaded_img/" . $row['image'] . "'>
                    <div class='name'>
                        " . $row['name'] . "
                    </div>
                    <form action='' method='post'>
                        <input type='hidden' name='pid' value='" . $row['id'] . "'>
                        <input type='hidden' name='name' value='" . $row['name'] . "'>
                        <input type='hidden' name='price' value='" . $row['price'] . "'>
                        <input type='hidden' name='image' value='" . $row['image'] . "'>
                        <input type='number' name='qty' class='qty' min='1' max='99'
                            onkeypress='if(this.value.length == 2) return false;' value='1'>
                        <input type='submit' class='btn' name='add_to_cart' value='أضف الى الطاولة'>
                    </form>
                </div>";
        }
    }
    else {
        $output .= "<h3 style='text-align:center'>ليست هناك وجبة بهذا الاسم</h3> ";
    }
    echo $output;
}
?>