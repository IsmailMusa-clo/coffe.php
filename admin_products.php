<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_product = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_product->execute([$name]);

   if($select_product->rowCount() > 0){
      setcookie('message', 'product name already exist!', time()+4);
   }else{
      if($image_size > 2000000){
         setcookie('message', 'image size is too large!', time()+4);
         
      }else{
         $insert_product = $conn->prepare("INSERT INTO `products`(name,category_id,price,image) VALUES(?,?,?,?)");
         $insert_product->execute([$name,$category,$price, $image]);
         move_uploaded_file($image_tmp_name, $image_folder);
         setcookie('message', 'new product added!', time()+4);
      }
   }
   header('location:admin_products.php');

}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:admin_products.php');

}

?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>الوجبات</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="add-products">

   <h1 class="heading">اضافة وجبة</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <input type="text" class="box" required maxlength="100" placeholder="اسم المنتج" name="name">
      <select name="category" class="box">
         <?php
               $select_category = $conn->prepare("SELECT * FROM `tbl_category`");
               $select_category->execute();
               if($select_category->rowCount() > 0){
                  while($fetch_category = $select_category->fetch(PDO::FETCH_ASSOC)){ 
            ?>
         <option value="<?= $fetch_category['id']; ?>"><?= $fetch_category['title']; ?></option>
                     <?php
                           }
                        }
               else {
                        echo '<p class="empty">لا توجد تصنيفات</p>';
                  }
                     ?>
      </select>
      <input type="number" min="0" class="box" required max="9999999999" placeholder="سعر المنتج" onkeypress="if(this.value.length == 10) return false;" name="price">
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required >
      <input type="submit" value="اضافة" class="btn" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="heading">جميع الوجبات</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products`");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
            $select_cat = $conn->prepare("SELECT title FROM `tbl_category` WHERE id='$fetch_products[category_id]'");
            $select_cat->execute();
            $fetch_cat = $select_cat->fetch(PDO::FETCH_ASSOC);

   ?>
   <div class="box">
      <div class="price"><span><?=$fetch_products['price']; ?></span> \ ريال </div>
      <div class="" style="font-size:18px;margin-bottom:10px">الصنف/<span><?=$fetch_cat['title'];?></div>
      <img src="uploaded_img/<?=$fetch_products['image'];?>" height="250" alt="">
      <div class="name"><?=$fetch_products['name'];?></div>
      <div class="flex-btn">
         <a href="admin_product_update.php?update=<?= $fetch_products['id']; ?>" class="option-btn">تحديث</a>
         <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">حذف</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">تم الاضافة</p>';
      }
   ?>
   
   </div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>