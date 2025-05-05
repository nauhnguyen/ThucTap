<?php
  // Xử lý phân trang
  if (isset($_GET['trang'])) {
    $page = $_GET['trang'];
  } else {
    $page = 1;
  }

  if ($page == '' || $page == 1) {
    $begin = 0;
  } else {
    $begin = ($page * 8) - 8;
  }

  $title = "All products";

  // Lọc theo danh mục
  if (isset($_GET['category_id'])) {
    $category_id = mysqli_real_escape_string($mysqli, $_GET['category_id']);
    $sql_product = "SELECT * FROM tblproduct WHERE category_id = '$category_id' ORDER BY product_id DESC LIMIT $begin, 8";
    $sql_count = "SELECT COUNT(*) AS total FROM tblproduct WHERE category_id = '$category_id'";
    $title = "Products in the category";
  }
  // Tìm kiếm sản phẩm
  else if (isset($_POST['keyword']) || isset($_GET['keyword'])) {
    if (isset($_POST['keyword'])) {
      $keyword = mysqli_real_escape_string($mysqli, $_POST['keyword']);
    } else {
      $keyword = mysqli_real_escape_string($mysqli, $_GET['keyword']);
    }

    $sql_product = "SELECT * FROM tblproduct 
                    WHERE product_title LIKE '%$keyword%' 
                    OR product_author LIKE '%$keyword%' 
                    ORDER BY product_id DESC LIMIT $begin, 8";

    $sql_count = "SELECT COUNT(*) AS total FROM tblproduct 
                  WHERE product_title LIKE '%$keyword%' 
                  OR product_author LIKE '%$keyword%'";

    $title = "Search results for \"$keyword\"";
  }
  // Hiển thị tất cả sản phẩm
  else {
    $sql_product = "SELECT * FROM tblproduct ORDER BY product_id DESC LIMIT $begin, 8";
    $sql_count = "SELECT COUNT(*) AS total FROM tblproduct";
  }

  $query_product = mysqli_query($mysqli, $sql_product);
  $result_count = mysqli_query($mysqli, $sql_count);
  $row_count = mysqli_fetch_assoc($result_count);
  $trang = ceil($row_count['total'] / 8);
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-lg-2 col-sm-12 sidebar">
      <?php include('pages/main/product/sidebar.php') ?>
    </div>
    <div class="container-fluid col-lg-10 col-sm-12">
      <h2 class="text-center"><?php echo $title ?></h2>
      <div class="row min-height-100">
        <?php
        while ($row_product = mysqli_fetch_array($query_product)) {
        ?>
          <form class="col-lg-3 col-md-4 col-sm-6" action="./index.php?navigate=product_info&product_id=<?php echo $row_product['product_id']; ?>" method="POST">
            <div class="card text-center mb-4" style="height: 380px;">
              <img class="card-img-top product-img" src="./assets/images/products/<?php echo $row_product['product_image']; ?>" />
              <div class="card-body">
                <h6><?php echo $row_product['product_title'] ?></h6>
                <?php if ($row_product['product_discount'] > 0) { ?>
                  <h6 class="text-danger">
                    <s><?php echo number_format($row_product['product_price'], 0, ',', '.'); ?> VND</s>
                    <sup class="badge badge-danger"><?php echo $row_product['product_discount']; ?>%</sup>
                  </h6>
                  <h6><?php echo number_format($row_product['product_price'] * (100 - $row_product['product_discount']) / 100, 0, ',', '.'); ?> VND</h6>
                <?php } else { ?>
                  <h6><?php echo number_format($row_product['product_price'], 0, ',', '.'); ?> VND</h6>
                <?php } ?>

                <?php if (isset($_SESSION['user_id'])) { ?>
                  <input type="submit" class="btn btn-info" name='submit' value="Buy">
                <?php } else { ?>
                  <input type="submit" class="btn btn-info" name='submit' value="View details">
                <?php } ?>
              </div>
            </div>
          </form>
        <?php
        }
        ?>
      </div>

      <!-- Phân trang -->
      <ul class="d-flex justify-content-center list-unstyled">
        <?php
        for ($i = 1; $i <= $trang; $i++) {
          // Giữ lại keyword hoặc category_id nếu có
          $url = "index.php?navigate=show_products&trang=$i";
          if (isset($category_id)) {
            $url .= "&category_id=" . urlencode($category_id);
          } else if (isset($keyword)) {
            $url .= "&keyword=" . urlencode($keyword);
          }
        ?>
          <li class="p-2 m-1" <?php if ($i == $page) {
                                echo 'style="background: #ccc"';
                              } else {
                                echo 'style="background: #fff"';
                              } ?>>
            <a class="text-dark" href="<?php echo $url ?>"><?php echo $i ?></a>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>