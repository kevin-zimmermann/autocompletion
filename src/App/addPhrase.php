<?php

namespace App;

class addPhrase
{
    protected static $AddPhrase = [
        'incorrect_password' => 'Le mot de passe incorrect',
        'login_incorrect' => 'Le login n\'existe pas',
        'user_list' => 'User list',
        'user_edit' => 'User edit',
        'no_privilege_admin' => 'Vous n\'avait pas les privilège Admin !',
        'login_is_already_taken' => 'Le login est déjà pris !',
        'the_e_mail_address_is_not_valid' => 'L\'adresse e-mail n\'est pas valide',
        'you_cannot_delete_your_account' => 'Vous ne pouvez pas supprimer votre compte',
        'Don_t_you_have_an_account' => 'Vous n\'avez pas de compte?',
        'creator_article' => 'Créer article',
        'you_do_not_have_permission_to_view_this_page_or_perform_this_action' => 'Vous n\'êtes pas autorisé à afficher cette page ou à effectuer cette action.',
        'the_login_must_have_more_than_4_characters' => 'Le login doit avoir plus de 4 caractères',
        'The_passwords_don_t_match' => 'Les password ne corresponde pas !',
        'please_enter_all_fields' => 'Veuillez saisir tous les champs.',
        'continue' => 'Continue',
        'validate' => 'Validée',
        'action' => 'Action',
        'upload_img' => 'Upload image',
        'name' => 'Titre',
        'display_order' => 'Ordre d\'affichage',
        'n_0' => 'n°',
        'log_date' => 'Date',
        'from' => 'À partir de',
        'account' => 'Compte',
        'my_account' => 'Mon compte',
        'you_have_to_upload_an_image' => 'Vous devez upload une image !',
        'incorrect_login_or_password' => 'Username ou mot de passe incorrect',
        'start_private_conversation' => 'Démarrer Conversation privée',
        'recipient' => 'Destinataire',
        'message' => 'Message',
        'room_list' => 'Salon textuel',
        'conversations_list' => 'Conversation privée',
        'room' => 'Channel',
        'the_password_must_have_more_than_4_characters' => 'le mot de passe doit comporter plus de 4 caractères',
        'avatar' => 'Avatar',
        'change' => 'Changement'
    ];

    public static function addPhrase()
    {
        return self::$AddPhrase;
    }
}