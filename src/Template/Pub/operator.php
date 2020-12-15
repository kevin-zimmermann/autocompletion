
<div class="block-operator">
    <div class="info-operator">
        <div class="logo-operator">
            <?= \Base\Util\Img::getImg($params->operator, 'operator_name', 'defaultOperator', 'operator', 's') ?>
        </div>
        <div class="name-operator">
            Nom de l'opérateur : <span><?= $params->operator->operator_name ?></span>
        </div>
        <div class="side-operator">
            Attaque ou défense :<span> <?= $params->operator->getSide() ?></span>
        </div>
        <div class="reveal-operator">
            Nom de l'opération de sortie de l'opérateur :<span> <?= ucwords($params->operator->operation_reveal ) ?></span>
        </div>
        <div class="year-reveal">
            Date de sortie de l'opérateur : <span><?= $params->operator->getYear() ?></span>
        </div>
    </div>
</div>
<div class="back-home">
    <a href="<?= $app->buildLink('Pub:')?>"><i class="fa fa-home"></i></a>
</div>
