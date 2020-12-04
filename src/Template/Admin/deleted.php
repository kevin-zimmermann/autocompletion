<h1 class="p-title-value">
    Confirmer l'action
</h1>
<?php
if(empty($errors))
{
    $html = "<div class=\"block-delete\">
                <div class=\"block-text\">
                    <p>Veuillez confirmer que vous souhaitez faire cette action :</p>
                    <strong><a href=\"$params->EditLink\">$params->title</a> </strong>
                </div>
                <form action=\"$params->linkDeleted\" method=\"post\">
                    <div class=\"formSubmitRow-main\">
                        <div class=\"formSubmitRow-bar\"></div>
                        <div class=\"formSubmitRow-controls\" >
                            <button type=\"submit\" name=\"delete\" value=\"1\" class=\"button--primary button button--icon button--icon--delete\">
                            <span class=\"button-text\">Delete</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>";
}
else
{
    $html = $renderError;
}

if(!$BaseApp->request()->isXhr())
{
    echo $html;
}