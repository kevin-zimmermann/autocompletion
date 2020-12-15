<div class="block">
    <div class="logo">
        <div class="img">
        <img src="<?= \Base\BaseApp::getBaseLink() ?>Styles/image/R6-logo.png"
             alt="<?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?>">
        </div>
        <div class="text-logo">
        <p>Search</p>
        </div>

    </div>
</div>
<div class="r6-search">
    <div class="search-input">
            <input type="text" id="search-operator" class="input" name="search-operator"
                   data-auto-url="<?= $app->buildLink('Pub:operator/search') ?>"
                   data-entity="App:Operators" data-search="operator_name">
    </div>
</div>
<div class="results"></div>
<script>
    var auto = new AutoCompletions('search-operator');
    auto.run();
    $('.ui-content-auto').click(function() {
       console.log('dsfsdf'),
        window.location.replace($(this).data('link'));

    });

</script>