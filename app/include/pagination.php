<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">

        <!--======== Previous ========-->
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo ($page - 1);?>">Previous</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link">Previous</a>
            </li>
        <?php endif;?>


        <!--======== For | All Pages ========-->
        <?php for($i = 1; $i <= $total_pages; $i++): ?>

            <?php if ($page == $i): ?>
                <li class="page-item active" aria-current="page">
            <?php else: ?>
                <li class="page-item">
            <?php endif;?>

            <a class="page-link" href="?page=<?=$i?>"><?=$i?></a>
        </li>

        <?php endfor; ?>


        <!--======== Next ========-->
        <?php if ($total_pages > $page): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo ($page + 1);?>">Next</a>
            </li>
        <?php endif;?>


        <!--======== Last ========-->
        <?php if ($total_pages > $page): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $total_pages?>">Last</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link"">Last</a>
            </li>
        <?php endif;?>

    </ul>
</nav>