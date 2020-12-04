<?php if($breadcrumb->isBreadcrumb()) { ?>
    <ul class="p-breadcrumbs <?= $breadcrumb->getParentClassInString() ?>">
        <?php
        if($breadcrumb)
        {
           echo $breadcrumb->getBreadcrumb();
        }
        ?>
    </ul>
<?php }  ?>


