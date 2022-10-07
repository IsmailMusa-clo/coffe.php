<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>لوحة التحكم</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="dashboard">

   <h1 class="heading">لوحة التحكم</h1>

   <div class="box-container">

      <div class="box">
         <?php
            $total_pendings = 0;
            $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_pendings->execute(['تحت المعالجة']);
            if($select_pendings->rowCount() > 0){
               while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
                  $total_pendings += $fetch_pendings['total_price'];
               }
            }
         ?>
         <h3> <?= $total_pendings; ?> \ ريال </h3>
         <p>طلبات معلقة</p>
         <a href="admin_orders.php" class="btn">انظر الى الطلبات</a>
      </div>

      <div class="box">
         <?php
            $total_completes = 0;
            $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_completes->execute(['مكتمل']);
            if($select_completes->rowCount() > 0){
               while($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)){
                  $total_completes += $fetch_completes['total_price'];
               }
            }
         ?>
         <h3><?= $total_completes;?> \ ريال </h3>
         <p>طلبات مكتملة</p>
         <a href="admin_orders.php" class="btn">انظر الى الطلبات</a>
      </div>

      <div class="box">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            $number_of_orders = $select_orders->rowCount()
         ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>الطلبات</p>
         <a href="admin_orders.php" class="btn">جميع الطلبات وحالتها</a>
      </div>

      <div class="box">
         <?php
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
            $number_of_products = $select_products->rowCount()
         ?>
         <h3><?= $number_of_products; ?></h3>
         <p>الوجبات </p>
         <a href="admin_products.php" class="btn">انظر الى الوجبات</a>
      </div>

      <div class="box">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `user`");
            $select_users->execute();
            $number_of_users = $select_users->rowCount()
         ?>
         <h3><?= $number_of_users; ?></h3>
         <p>حسابات الأعظاء</p>
         <a href="users_accounts.php" class="btn">انظر الى الأعظاء</a>
      </div>

      <div class="box">
         <?php
            $select_admins = $conn->prepare("SELECT * FROM `admin`");
            $select_admins->execute();
            $number_of_admins = $select_admins->rowCount()
         ?>
         <h3><?= $number_of_admins; ?></h3>
         <p>حسابات المشرفين</p>
         <a href="admin_accounts.php" class="btn">انظر الى المشرفين</a>
      </div>
	  
	  
	        <div class="box">
         <?php
            $select_cat = $conn->prepare("SELECT * FROM `tbl_category`");
            $select_cat->execute();
            $number_of_cat = $select_cat->rowCount()
         ?>
         <h3><?= $number_of_cat; ?></h3>
         <p>الفئات </p>
         <a href="admin_category.php" class="btn">انظر الى الفئات </a>
      </div>

   </div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>