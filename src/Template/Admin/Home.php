<div class="p-main-header">
    <div class="p-title ">
        <h1 class="p-title-value">
            <?= \Base\BaseApp::phrase('home') ?> - <?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?>
        </h1>
    </div>
</div>

<table class="dataList-table">
    <tbody class="dataList-rowGroup">
        <tr class="dataList-row dataList-row--noHover dataList-row--subSection" >
            <td class="dataList-cell" colspan="2">
                <div class="dataList-mainRow u-depth">
                    <?= \Base\BaseApp::phrase('setup') ?>
                </div>
            </td>
        </tr>
        <tr class="dataList-row dataList-sous-cat">
            <td class="dataList-cell dataList-cell--main ">
                <a href="<?= $app->buildLink('admin:room') ?>">
                    <div class="dataList-mainRow u-depth">
                        <?= \Base\BaseApp::phrase('room') ?>
                    </div>
                </a>
            </td>
        </tr>
        <tr class="dataList-row dataList-sous-cat">
            <td class="dataList-cell dataList-cell--main ">
                <a href="<?= $app->buildLink('admin:emoji') ?>">
                    <div class="dataList-mainRow u-depth">
                        <?= \Base\BaseApp::phrase('emoji') ?>
                    </div>
                </a>
            </td>
        </tr>
        <tr class="dataList-row dataList-sous-cat">
            <td class="dataList-cell dataList-cell--main ">
                <a href="<?= $app->buildLink('admin:conversation') ?>">
                    <div class="dataList-mainRow u-depth">
                        <?= \Base\BaseApp::phrase('conversations_list') ?>
                    </div>
                </a>
            </td>
        </tr>
    </tbody>
</table>