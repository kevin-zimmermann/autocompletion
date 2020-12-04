<?php if($breadcrumb->isBreadcrumb()) { ?>
    <div class="p-breadcrumbs <?= $breadcrumb->getParentClassInString() ?>">
        <ul>
            <?php
            if($breadcrumb)
            {
                echo $breadcrumb->getBreadcrumb();
            }
            ?>
        </ul>
    </div>
<?php }  ?>
