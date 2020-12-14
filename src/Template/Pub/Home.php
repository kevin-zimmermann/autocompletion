<div class="block">
    <div class="logo">
        <img src="<?= \Base\BaseApp::getBaseLink() ?>Styles/image/R6-logo.png"
             alt="<?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?>">
        <p>Search</p>

    </div>
</div>
<div class="r6-search">
    <div class="search-input">
        <input type="text" id="recipient" class="input" name="recipient" data-auto-url="/pub/autocompletion//user"
               data-entity="App:Operators" data-search="operator"> <i class="fa fa-search"></i>
    </div>
</div>