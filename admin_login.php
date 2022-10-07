<?php

include 'config.php';

session_start();

if(isset($_POST['login'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);
   $row = $select_admin->fetch(PDO::FETCH_ASSOC);

   if($select_admin->rowCount() > 0){
      $_SESSION['admin_id'] = $row['id'];
      header('location:admin_page.php');
   }else{
      $_SESSION['message'] = 'incorrect username or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>تسجيل الدخول</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link  -->
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      .link{
         font-size:18px ;
         display: inline-block;
         margin-top: 30px;
      }
   </style>
</head>
<body>


<?php
if (isset($_SESSION['message'])) {
   echo '
         <div class="message">
            <span>' . $_SESSION['message'] . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
   $_SESSION['message'] = null;
}
?>

<section class="form-container">

   <form action="" method="post">
      <h3>دخول</h3>
      <!--اسم المستخدم admin كلمة المرور 111</p> -->
      <input type="text" name="name" required placeholder="ادخل اسم المستخدم " maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="ادخل كلمة السر" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="دخول" class="btn" name="login">
      <a href="index.php" class="link">الرجوع للصفحة الرئيسية</a>
</form>

</section>
   
</body>
</html>