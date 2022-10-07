<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}
;

if (isset($_POST['update_category'])) {

   $cid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;

   $update_product = $conn->prepare("UPDATE `tbl_category` SET title = ? WHERE id = ?");
   $update_product->execute([$name, $cid]);
   setcookie('message', 'تم ادخال التحديثات', time() + 4);
   if (!empty($image)) {
      if ($image_size > 2000000) {
         setcookie('message', ' image size is too large!', time() + 4);
      }
      else {
         $update_image = $conn->prepare("UPDATE `tbl_category` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $cid]);
         move_uploaded_file($image_tmp_name, $image_folder);
         unlink('uploaded_img/' . $old_image);
         setcookie('message', ' image updated successfully!', time() + 4);
      }
   }
   header('location:admin_category.php');

}

?>

<!DOCTYPE html>
<html lang="en" dir=rtl>
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>تحديث التصنيف</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="update-product">

   <h1 class="heading">تحديث التصنيف</h1>

   <?php
      $update_id = $_GET['update'];
      $select_category = $conn->prepare("SELECT * FROM `tbl_category` WHERE id = ?");
      $select_category->execute([$update_id]);
      if($select_category->rowCount() > 0){
         while($fetch_category = $select_category->fetch(PDO::FETCH_ASSOC)){ 
   ?>
      <form action="" enctype="multipart/form-data" method="post">
         <input type="hidden" name="pid" value="<?= $fetch_category['id']; ?>">
         <input type="hidden" name="old_image" value="<?= $fetch_category['image']; ?>">
         <img src="uploaded_img/<?= $fetch_category['image']; ?>" alt="">
         <input type="text" class="box" required maxlength="100" placeholder="enter product name" name="name"
            value="<?= $fetch_category['title']; ?>">
         <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
            <div class="flex-btn">
         <input type="submit" value="تحديث" class="btn" name="update_category">
         <a href="admin_category.php" class="option-btn">رجوع</a>
      </div>
   </form>

   <?php
         }
      }else{
         echo '<p class="empty"> لا يوجد تحديث</p>';
      }
   ?>

</section>




<script src="js/admin_script.js"></script>

</body>
</html>