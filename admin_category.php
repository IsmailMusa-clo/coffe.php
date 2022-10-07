<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['add_category'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);


   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;


   $select_category = $conn->prepare("SELECT * FROM `tbl_category` WHERE title = ?");
   $select_category->execute([$name]);

   if($select_category->rowCount() > 0){
      setcookie('message', 'product name already exist!', time()+4);
   }else{

      if($image_size > 2000000){
         setcookie('message', 'image size is too large!', time()+4);
         
      }else{

         $insert_category = $conn->prepare("INSERT INTO `tbl_category`(title,image) VALUES(?,?)");
         $insert_category->execute([$name,$image]);
         move_uploaded_file($image_tmp_name, $image_folder);
   }
}
setcookie('message', 'new category added!', time()+4);

   header('location:admin_category.php');
   }



if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_category = $conn->prepare("DELETE FROM `tbl_category` WHERE id = ?");
   $delete_category->execute([$delete_id]);
   header('location:admin_category.php');

}

?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>الفئات</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link  -->
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      /* start table setting */
      .tb{
            border-radius: 30px;
            background-color: var(--main-color);
         }
         .tb table th{
            border-bottom: 1px solid #fff;
            font-size: 23px;
            color: #fff;
         }
         .tb table td{
            text-align: center;
            font-size: 18px;
            color: aqua;
            line-height: 200%;
         }
         .tb table td a{
            color:aqua;
         }
         .tb table td a:hover{
            color: #fff ;
         }

      /* end table setting */
   </style>
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="add-products">

   <h1 class="heading">اضافة تصنيف جديد</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <input type="text" class="box" required maxlength="100" placeholder="اسم التصنيف" name="name">
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="اضافة" class="btn" name="add_category">
   </form>

</section>

<h1 class="heading">جميع الفئات</h1>
<section class="tb" >
   <table class="" style="width:100%;">
      <thead>
         <tr>
            <th>#</th>
            <th>اسم التصنيف</th>
            <th>صورة</th>
            <th>تحديث</th>
            <th>حذف</th>
         </tr>
      </thead>
      <tbody>
         <?php
               $select_category = $conn->prepare("SELECT * FROM `tbl_category`");
               $select_category->execute();
               if($select_category->rowCount() > 0){
                  $i=1;
                  while($fetch_category = $select_category->fetch(PDO::FETCH_ASSOC)){ 
           ?>
         <tr>
            <td><?=$i?></td>
            <td><?= $fetch_category['title'];?></td>
            <td><img width="100" height="100" src="projects images/<?=$fetch_category['image']?>" alt=""></td>
            <td>
               <a href="admin_category_update.php?update=<?= $fetch_category['id']; ?>"
                class="fa fa-pencil"></a>
            </td>
            <td>
               <a href="admin_category.php?delete=<?= $fetch_category['id']; ?>"
                  class="fa fa-trash"
                  onclick="return confirm('delete this category?');">
               </a>
            </td>
         </tr>
         <?php
         $i++;
               }
            }
            else{
            echo '<p class="empty">تم الاضافة</p>';
            }
         ?>
      </tbody>
   </table>
</section>



<script src="js/admin_script.js"></script>
</body>
</html>