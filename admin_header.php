<?php
   if(isset($_COOKIE['message'])){
         echo '
         <div class="message">
            <span>'.$_COOKIE['message'].'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
?>

<header class="header">

   <section class="flex">
      <a href="admin_page.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="admin_page.php">الصفحة الرئيسية</a>
         <a href="admin_products.php">اضافة وجبة</a>
         <a href="admin_orders.php">الطلبات</a>
         <a href="admin_accounts.php">المشرفين</a>
         <a href="admin_category.php">الفئات</a>
         <a href="users_accounts.php">المستخدمين</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="admin_profile_update.php" class="btn">تحديث الحساب</a>
         <a href="logout.php" class="delete-btn">تسجيل الخروج</a>
         <div class="flex-btn">

         </div>
      </div>
   </section>

</header>