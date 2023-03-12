<!--==================== HEADER ====================-->
<header class="container-fluid" id="header">
    <div class="container">
        <div class="row">
            <div class="col-4">
                <h1><a href="<?php echo BASE_URL ?>">My Blog</a></h1>
            </div>
            <nav class="col-8">
                <ul>
                    <li><a href="<?php echo BASE_URL ?>">Home</a></li>
                    <li><a href="<?php echo BASE_URL . 'about.php' ?>">About</a></li>
                    <li><a href="#">Service</a></li>

                    <li>
                        <?php if (isset($_SESSION['id'])): ?>
                            <a href="#"> <!-- Username -->
                                <i class="ri-user-3-fill"></i>
                                <?php echo $_SESSION['login']; ?>
                            </a>
                            <ul>
                                <?php if ($_SESSION['admin']): ?>
                                    <li><a href="<?php echo BASE_URL . "../admin/posts/index.php"; ?>">Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo BASE_URL . "logout.php"; ?>">Выход</a></li>
                            </ul>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL . "log.php"; ?>">
                                <i class="ri-user-3-fill"></i>
                                Log in
                            </a>
                            <ul>
                                <li><a href="<?php echo BASE_URL . "reg.php"; ?>">Register</a></li>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<!-- <h1><a href="<php echo BASE_URL >">My Blog</a></h1> -->