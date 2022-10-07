<?php
include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
   header('location:admin_login.php');
}
if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
   $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_payment->execute([$payment_status, $order_id]);
  setcookie('message', 'حالة الطلب', time()+4);
   header('location:admin_orders.php');

}
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:admin_orders.php');
}
?>

<!DOCTYPE html>
<html  dir="rtl">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>الطلبات</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="orders">

<h1 class="heading">الطلبات</h1>

<div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders`");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> التاريخ : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> الإسم : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> رقم الهاتف : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> رقم الطاولة : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> جميع الطلبات : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> السعر : <span><?= $fetch_orders['total_price']; ?></span> </p>
      <p> طريقة الدفع : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="post">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="select">
            <option selected disabled><?= $fetch_orders['payment_status']; ?></option>
            <option value="تحت المعالجة" name="pending">تحت المعالجة</option>
			<option value="جاري التحضير" name="processing" >جاري التحضير</option>
            <option value="مكتمل" name="completd" >مكتمل</option>
         </select>
        <div class="flex-btn">
         <input type="submit" value="تحديث" class="option-btn" name="update_payment">
         <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">حذف</a>
        </div>
      </form>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">لا يوجد طلبات</p>';
      }
   ?>

</div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>