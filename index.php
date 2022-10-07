<?php
use LDAP\Result;

include 'config.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
}
else {
   $user_id = '';
}
;

if (isset($_POST['register'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `user` WHERE name = ? AND email = ?");
   $select_user->execute([$name, $email]);

   if ($select_user->rowCount() > 0) {
      setcookie('message', 'username or email already exists!', time() + 4);
   }
   else {
      if ($pass != $cpass) {
         setcookie('confirm password not matched!', time() + 4);

      }
      else {
         $insert_user = $conn->prepare("INSERT INTO `user`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);
         setcookie('registered successfully, login now please!', time() + 4);
      }
   }
   header('location:index.php');

}

if (isset($_POST['update_qty'])) {
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   setcookie('message', 'cart quantity updated!', time() + 4);
   header('location:index.php');
}

if (isset($_GET['delete_cart_item'])) {
   $delete_cart_id = $_GET['delete_cart_item'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_cart_id]);
   header('location:index.php');
}

if (isset($_GET['logout'])) {
   session_unset();
   session_destroy();
   header('location:index.php');
}

if (isset($_POST['add_to_cart'])) {

   if ($user_id == '') {
      setcookie('message', 'سجل الدخول اولا', time() + 4);
   }
   else {

      $pid = $_POST['pid'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $image = $_POST['image'];
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_STRING);

      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND name = ?");
      $select_cart->execute([$user_id, $name]);
      if ($select_cart->rowCount() > 0) {
         setcookie('message', 'هناك طلبات في عربة التسوق', time() + 4);
      }
      else {
         $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
         $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
         setcookie('message', 'added to cart!', time() + 4);

      }

   }
   header('location:index.php');
}

if (isset($_POST['order'])) {

   if ($user_id == '') {
      setcookie('message', 'سجل الدخول اولا', time() + 4);
   }
   else {
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $number = $_POST['number'];
      $number = filter_var($number, FILTER_SANITIZE_STRING);
      $address = '' . $_POST['flat'];
      $address = filter_var($address, FILTER_SANITIZE_STRING);
      $method = $_POST['method'];
      $method = filter_var($method, FILTER_SANITIZE_STRING);
      $total_price = $_POST['total_price'];
      $total_products = $_POST['total_products'];

      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);

      if ($select_cart->rowCount() > 0) {
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $method, $address, $total_products, $total_price]);
         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);
         setcookie('message', 'تم الطلب', time() + 4);
      }
      else {
         setcookie('message', 'عربة التسوق فارغة', time() + 4);
      }
   }
   header('location:index.php');
}


?>


<html dir=rtl>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة وجباتنا </title>

    <style>
    .search-form {
        text-align: center;
        padding: 100px 0px;
    }

    .search-form input[type='search'] {
        width: 250px;
        height: 35px;
        background-color: #fff;
        box-shadow: 0px 0px 14px 0.4px rgba(0, 0, 0, 0.1);
        border-radius: 3px;
        color: #333;
        font-size: 17px;
        padding: 10px;
        margin-right: 5px;
    }

    .search-form select {
        width: 80px;
        height: 35px;
        background-color: #fff;
        box-shadow: 0px 0px 14px 0.4px rgba(0, 0, 0, 0.1);
        border-radius: 3px;
        color: #333;
        font-size: 14px;
        padding: 8px;
        margin-right: 5px;
    }

    a {
        text-decoration: none !important;
    }

    .home .carousel>h3 {
        font-size: 60px;
        text-align: center;
        color: #fff;
        font-weight: bold;
    }

    .carousel-item img{
       border-radius: 40px;
       height: 500px;
       width: 60%;
       opacity: 0.9;      
      }
      .carousel-item h5{
         font-size: 30px ;
         font-weight: bold;
      }

    .home-bg{
      background-position: 60% !important;
      background-size: cover !important;
    }
    </style>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php
if (isset($_COOKIE['message'])) {
   echo '
         <div class="message">
            <span>' . $_COOKIE['message'] . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
}
?>

    <!-- header section starts  -->

    <header class="header">

        <section class="flex">

            <a href="#home" class="logo"><span>C</span>offi.</a>

            <nav class="navbar">
                <a href="#home">الصفحة الرئيسية</a>
                <a href="#menu">قائمة الطعام</a>
                <a href="#order">الطلبات</a>
                <a href="#about">حول</a>
                <a href="admin_accounts.php">لوحة التحكم</a>
            </nav>

            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="order-btn" class="fas fa-box"></div>
                <?php
$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$count_cart_items->execute([$user_id]);
$total_cart_items = $count_cart_items->rowCount();
?>
                <div id="cart-btn" class="fas fa-shopping-cart"><span>(
                        <?= $total_cart_items; ?>)
                    </span></div>
            </div>

        </section>

    </header>

    <!-- header section ends -->

    <div class="user-account">

        <section>

            <div id="close-account"><span>إغلاق</span></div>

            <div class="user">
                <?php
$select_user = $conn->prepare("SELECT * FROM `user` WHERE id = ?");
$select_user->execute([$user_id]);
if ($select_user->rowCount() > 0) {
   while ($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)) {
      echo '<p>welcome ! <span>' . $fetch_user['name'] . '</span></p>';
      echo '<a href="index.php?logout" class="btn">logout</a>';
   }
}
else {
   echo '<p><span>لم تسجل دخولك </span></p>';
}
?>
            </div>

            <div class="display-orders">
                <?php
$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
if ($select_cart->rowCount() > 0) {
   while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
      echo '<p>' . $fetch_cart['name'] . ' <span>(' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')</span></p>';
   }
}
else {
   echo '<p><span>لمشاهدة جميع طلباتك  سجل الدخول </span></p>';
}
?>
            </div>

            <div class="flex">

                <form action="user_login.php" method="post">
                    <h3>login now</h3>
                    <input type="email" name="email" required class="box" placeholder="enter your email" maxlength="50">
                    <input type="password" name="pass" required class="box" placeholder="enter your password"
                        maxlength="20">
                    <input type="submit" value="login now" name="login" class="btn">
                </form>

                <form action="" method="post">
                    <h3>register now</h3>
                    <input type="text" name="name" oninput="this.value = this.value.replace(/\s/g, '')" required
                        class="box" placeholder="enter your username" maxlength="20">
                    <input type="email" name="email" required class="box" placeholder="enter your email" maxlength="50">
                    <input type="password" name="pass" required class="box" placeholder="enter your password"
                        maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
                    <input type="password" name="cpass" required class="box" placeholder="confirm your password"
                        maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
                    <input type="submit" value="register now" name="register" class="btn">
                </form>

            </div>

        </section>

    </div>

    <div class="my-orders">

        <section>

            <div id="close-orders"><span>اغلاق</span></div>

            <h3 class="title"> طلباتي </h3>

            <?php
$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
$select_orders->execute([$user_id]);
if ($select_orders->rowCount() > 0) {
   while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
?>
            <div class="box">
                <p> التاريخ : <span>
                        <?= $fetch_orders['placed_on']; ?>
                    </span> </p>
                <p> الاسم : <span>
                        <?= $fetch_orders['name']; ?>
                    </span> </p>
                <p> رقم الهاتف : <span>
                        <?= $fetch_orders['number']; ?>
                    </span> </p>
                <p> رقم الطاولة : <span>
                        <?= $fetch_orders['address']; ?>
                    </span> </p>
                <p> طريقة الدفع : <span>
                        <?= $fetch_orders['method']; ?>
                    </span> </p>
                <p> جميع الطلبات : <span>
                        <?= $fetch_orders['total_products']; ?>
                    </span> </p>
                <p> سعر جميع الوجبات : <span>
                        <?= $fetch_orders['total_price']; ?> \ ريال
                    </span> </p>
                <p> حالة الطلب : <span style="color:<?php if ($fetch_orders['payment_status'] == 'تحت المعالجة') {
         echo 'red';
      }
      elseif ($fetch_orders['payment_status'] == 'مكتمل') {
         echo 'green';
      }
      else {
         echo 'orange';
      }
      ; ?>">
                        <?= $fetch_orders['payment_status']; ?>
                    </span>
                </p>
            </div>
            <?php
   }
}
else {
   echo '<p class="empty">لا توجد طلبات !</p>';
}
?>

        </section>

    </div>

    <div class="shopping-cart">

        <section>

            <div id="close-cart"><span>اغلاق</span></div>

            <?php
$grand_total = 0;
$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
if ($select_cart->rowCount() > 0) {
   while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
      $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
      $grand_total += $sub_total;
?>
            <div class="box">
                <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times"
                    onclick="return confirm('delete this cart item?');"></a>
                <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
                <div class="content">
                    <p>
                        <?= $fetch_cart['name']; ?> <span>(
                            <?= $fetch_cart['price']; ?> x
                            <?= $fetch_cart['quantity']; ?>)
                        </span>
                    </p>
                    <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                        <input type="number" name="qty" class="qty" min="1" max="99"
                            value="<?= $fetch_cart['quantity']; ?>"
                            onkeypress="if(this.value.length == 2) return false;">
                        <button type="submit" class="fas fa-edit" name="update_qty"></button>
                    </form>
                </div>
            </div>
            <?php
   }
}
else {
   echo '<p class="empty"><span>عربة التسوق فارغة </span></p>';
}
?>
            <div class="cart-total"> سعر الفاتورة : <span>
                    <?= $grand_total; ?>\ ريال
                </span></div>
            <a href="#order" class="btn">اطلب الآن</a>
        </section>
    </div>
    <div class="home-bg" style=" background:url(images/old-spoon-dark-table.jpg)">
        <section class="home" id="home">
            <div id="demo" class="carousel slide" data-ride="carousel">
                <h3>وجباتنا المميزة</h3>
                <!-- Indicators -->
                <ul class="carousel-indicators">
                    <?php
                        $select_img = $conn->prepare("SELECT image,name FROM `products` ORDER BY date desc LIMIT 3");
                        $select_img->execute();
                        if ($select_img->rowCount() > 0) {
                           $i=0;
                        while ($fetch_img = $select_img->fetch(PDO::FETCH_ASSOC)) {
                        $actives='';
                        if($i==0){
                           $actives='active';
                        }
                     
                    ?>
                <li data-target="#demo" data-slide-to="<?=$i?>" class="<?=$actives?>"></li>
                <?php    
                        $i++;
                        }
                     }
                  ?>
                </ul>

                <!-- The slideshow -->
                <div class="carousel-inner">
                    <?php
$select_img = $conn->prepare("SELECT image,name FROM `products`");
$select_img->execute();
                     if ($select_img->rowCount() > 0) {
                     $i=0;
                     while ($fetch_img = $select_img->fetch(PDO::FETCH_ASSOC)) {
                     $actives='';
                     if($i==0){
                     $actives='active';
                     }
                     ?>
                    <div class="carousel-item <?= $actives?>">
                        <img src="uploaded_img/<?=$fetch_img['image']?>" class="d-block  m-auto ">
                        <div class="carousel-caption d-none d-md-block">
                           <h5><?=$fetch_img['name']?></h5>
                        </div>
                     </div>
                    <?php
                             $i++;
                            }
                           }
                           else {
                                 echo "<img src='images/home-bg.jpg'>";
                           }
                     ?>
                </div>
                <!-- Left and right controls -->
                <a class="carousel-control-prev" href="#demo" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </a>
                <a class="carousel-control-next" href="#demo" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </a>

            </div>
        </section>
    </div>
    <form class="search-form" method="post">
        <input type="search" name="search" id="search" placeholder="ابحث هنا">
        <select name="category" class="event_change" id="event_change">
            <?php
$select_category = $conn->prepare("SELECT * FROM `tbl_category`");
$select_category->execute();
if ($select_category->rowCount() > 0) {
   while ($fetch_category = $select_category->fetch(PDO::FETCH_ASSOC)) {
?>
            ?>
            <option value="<?= $fetch_category['id']; ?>">
                <?= $fetch_category['title']; ?>
            </option>
            <?php
   }
}
else {
   echo '<p class="empty">لا توجد تصنيفات</p>';
}
?>
        </select>

    </form>
    <!-- about section ends -->

    <!-- menu section starts  -->

    <section id="menu" class="menu">
        <h1 class="heading">قائمة الطعام</h1>
        <div class="box-container" id="product_list"></div>
        <div class="box-container" id="main_show">
            <?php
$select_products = $conn->prepare("SELECT * FROM `products`");
$select_products->execute();
if ($select_products->rowCount() > 0) {
   while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
?>
            <div class="box">
                <div class="price">
                    <?= $fetch_products['price']?> \ ريال
                </div>
                <img height="250" src="uploaded_img/<?= $fetch_products['image']?>" alt="">
                <div class="name">
                    <?= $fetch_products['name']?>
                </div>
                <form action="" method="post">
                    <input type="hidden" name="pid" value="<?= $fetch_products['id']?>">
                    <input type="hidden" name="name" value="<?= $fetch_products['name']?>">
                    <input type="hidden" name="price" value="<?= $fetch_products['price']?>">
                    <input type="hidden" name="image" value="<?= $fetch_products['image']?>">
                    <input type="number" name="qty" class="qty" min="1" max="99"
                        onkeypress="if(this.value.length == 2) return false;" value="1">
                    <input type="submit" class="btn" name="add_to_cart" value="أضف الى الطاولة">
                </form>
            </div>
            <?php
   }
}
else {
   echo '<p class="empty">لا توجد طلبات مضافة</p>';
}
?>

        </div>

    </section>

    <!-- menu section ends -->

    <!-- order section starts  -->

    <section class="order" id="order">

        <h1 class="heading">اطلب الآن</h1>

        <form action="" method="post">

            <div class="display-orders">

                <?php
$grand_total = 0;
$cart_item[] = '';
$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
if ($select_cart->rowCount() > 0) {
   while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
      $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
      $grand_total += $sub_total;
      $cart_item[] = $fetch_cart['name'] . ' ( ' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ' ) - ';
      $total_products = implode($cart_item);
      echo '<p>' . $fetch_cart['name'] . ' <span>(' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')</span></p>';
   }
}
else {
   echo '<p class="empty"><span>عربة التسوق فارغة</span></p>';
}
?>

            </div>

            <div class="grand-total"> المجموع : <span>
                    <?= $grand_total; ?> \ ريال
                </span></div>

            <input type="hidden" name="total_products" value="<?= $total_products; ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

            <div class="flex">
                <div class="inputBox">
                    <span>اسمك :</span>
                    <input type="text" name="name" class="box" required placeholder="ادخل الاسم " maxlength="20">
                </div>
                <div class="inputBox">
                    <span>رقم الهاتف :</span>
                    <input type="number" name="number" class="box" required placeholder="ادخل الرقم " min="0"
                        max="9999999999" onkeypress="if(this.value.length == 10) return false;">
                </div>
                <div class="inputBox">
                    <span>طريقة الدفع</span>
                    <select name="method" class="box">
                        <option value="كاش">كاش </option>
                        <option value="بطاقة">بطاقة </option>

                    </select>
                </div>
                <div class="inputBox">
                    <span>رقم الطاولة </span>
                    <input type="text" name="flat" class="box" required placeholder="رقم الطاولة" maxlength="50">
                </div>

                <input type="submit" value="اطلب الآن" class="btn" name="order">

        </form>

    </section>

    <!-- order section ends -->
    <section class="about" id="about">

        <h1 class="heading">من نحن </h1>

        <div class="box-container">

            <div class="box">
                <img src="images/about-1.svg" alt="">
                <h3>صنع بحب من اجلكم </h3>
                <p>نتطلع الى خدمتكم </p>
                <a href="#menu" class="btn">قائمة الطعام</a>
            </div>


        </div>

    </section>

    <!-- footer section starts  -->

    <section class="footer">

        <div class="box-container">

            <div class="box">
                <i class="fas fa-phone"></i>
                <h3>رقم الهاتف</h3>
                <p>+123-456-7890</p>
                <p>+123-456-7890</p>
            </div>

            <div class="box">
                <i class="fas fa-map-marker-alt"></i>
                <h3>العنوان</h3>
                <p>جازان</p>
            </div>

            <div class="box">
                <i class="fas fa-clock"></i>
                <h3>اوقات الدوام</h3>
                <p>24 H</p>
            </div>

            <div class="box">
                <i class="fas fa-envelope"></i>
                <h3>email address</h3>
                <p>441219152@TVTC.EDU.SA</p>
                <p>KSA@GMX.US</p>
            </div>

        </div>

        <div class="credit">
            حقوق الموقع محفوظة &copy;
            <?= date('Y'); ?>
        </div>

    </section>

    <!-- footer section ends -->
    <!-- custom js file link  -->
    <script src="js/jquery.js"></script>
    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>

    <script>
    $(document).ready(function() {
        $('#search').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                $('#main_show').hide();
                $.ajax({
                    url: "search.php",
                    method: "post",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#product_list').fadeIn();
                        $('#product_list').html(data);
                    }
                });
            } else {
                $('#main_show').show();
                $('#product_list').fadeOut();
            }
        });
        $('.event_change').change(function() {
            var id = $('.event_change option:selected').val();
            if (id != '') {
                $('#main_show').hide();
                $.ajax({
                    url: 'event_docs.php',
                    type: 'post',
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#product_list').fadeIn();
                        $('#product_list').html(data);
                    }
                });
            }
        });
    });
    </script>
</body>

</html>