<?php


namespace Base;


class Phrase
{
    /**
     * @var string[]
     */
    protected $month = [
        'month_1' => 'Janvier',
        'month_2' => 'Février',
        'month_3' => 'Mars',
        'month_4' => 'Avril',
        'month_5' => 'Mai',
        'month_6' => 'Juin',
        'month_7' => 'Juillet',
        'month_8' => 'Août',
        'month_9' => 'Septembre',
        'month_10' => 'Octobre',
        'month_11' => 'Novembre',
        'month_12' => 'Décembre'
    ];

    /**
     * @var string[]
     */
    protected $monthShort = [
        'month_1_short' => 'Jan',
        'month_2_short' => 'Fév',
        'month_3_short' => 'Mar',
        'month_4_short' => 'Avr',
        'month_5_short' => 'Mai',
        'month_6_short' => 'Jun',
        'month_7_short' => 'Jul',
        'month_8_short' => 'Aoû',
        'month_9_short' => 'Sep',
        'month_10_short' => 'Oct',
        'month_11_short' => 'Nov',
        'month_12_short' => 'Déc'
    ];

    /**
     * @var string[]
     */
    protected $day = [
        'day_monday' => 'Lundi',
        'day_tuesday' => 'Mardi',
        'day_wednesday' => 'Mercredi',
        'day_thursday' => 'Jeudi',
        'day_friday' => 'Vendredi',
        'day_saturday' => 'Samedi',
        'day_sunday' => 'Dimanche',
    ];

    /**
     * @var string[]
     */
    protected $dayShort = [
        'day_monday_short' => 'Lun',
        'day_tuesday_short' => 'Mar',
        'day_wednesday_short' => 'Mer',
        'day_thursday_short' => 'Jeu',
        'day_friday_short' => 'Ven',
        'day_saturday_short' => 'Sam',
        'day_sunday_short' => 'Dim',
    ];

    /**
     * @var string[]
     */
    protected $phrase = [
        'days' => 'Jours',
        'months' => 'Mois',
        'years' => 'Années',
        'x_minutes_ago' => 'Il y a {minutes} minutes',
        'yesterday_at_x' => 'Hier à {time}',
        'in_a_moment' => 'Dans un instant',
        'in_x_minutes' => 'Dans {minutes} minutes',
        'later_today_at_x' => 'Plus tard, aujourd\'hui à {time}',
        'tomorrow_at_x' => 'Demain à {time}',
        'a_moment_ago' => 'Il y a un instant',
        'one_minute_ago' => 'Il y a une minute',
        'today_at_x' => 'Aujourd\'hui à {time}',
        'day_x_at_time_y' => '{day} à {time}',
        'time_pm_lower' => 'pm',
        'time_am_lower' => 'am',
        'time_pm_upper' => 'PM',
        'time_am_upper' => 'AM',
        'requested_page_not_found' => 'Le page demandé n\'a pas été trouvée.',
        'error_title' => 'Oups! Nous avons rencontré des problèmes.',
        'no_thread_entry' => 'Pas de topic dans cette catégorie',
        'save' => 'Save',
        'edit' => 'Edit',
        'edit_type' => 'Edit {type}',
        'delete' => 'Delete',
        'send' => 'Envoyer',
        'add_type' => 'Add {type}',
        'title' => 'Titre',
        'username' => 'Username',
        'the_requested_page_could_not_be_found_by_x' => 'La page demandée n\'a pu être trouvée. (Code: {code}, contrôleur: {controller}, action: {action})',
        'the_requested_page_could_not_be_found' => 'La page demandée n\'a pu être trouvée.',
        'log_out' => 'Se déconnecter',
        'search' => 'Rechercher',
        'connexion' => 'Connexion',
        'registration' => 'Inscription',
        'password' => 'Password',
        'type_password' => '{type} password',
        'email' => 'Email',
        'required' => 'Requis',
        'next' => 'Next',
        'prev' => 'Prev',
        'type' => 'Type',
        'home' => 'Accueil',
        'user' => 'Utilisateur',
        'node' => 'Node',
        'description' => 'Description',
        'please_enter_value_for_required_field_x' => 'Veuillez saisir une valeur pour le champ obligatoire \'{field}\'.',
        'percent' => 'Pourcentage',
        'date' => 'Date',
        'n_a' => 'N/A',
        'wait' => 'En attente',
        'yes' => 'Oui',
        'admin' => 'Admin',
        'users' => 'Utilisateurs'
    ];
    protected $AddPhrase = [];
    /**
     * Phrase constructor.
     */
    public function __construct()
    {
        $this->setMonth();
        $this->setDay();
        $this->setMonthShort();
        $this->setDayShort();
        $this->addPhrase();

    }
    protected function addPhrase()
    {
        $this->phrase += \App\addPhrase::addPhrase();
    }
    /**
     *
     */
    protected function setMonth()
    {
        $this->phrase += $this->month;
    }

    /**
     *
     */
    protected function setDay()
    {
        $this->phrase += $this->day;
    }

    /**
     *
     */
    protected function setMonthShort()
    {
        $this->phrase += $this->monthShort;
    }

    /**
     *
     */
    protected function setDayShort()
    {
        $this->phrase += $this->dayShort;
    }

    /**
     * @param $phraseKey
     * @param array $values
     * @return string|string[]
     */
    public function getPhrase($phraseKey, array $values = [])
    {
        $matchedSuffix = false;
        $suffixes = "";
        if(substr($phraseKey, -3) == '...')
        {
            $suffixes = '...';
            $phraseKey = substr($phraseKey, 0, -3);
            $matchedSuffix = true;
        }
        else
        {
            $lastChar = substr($phraseKey, -1);
            switch ($lastChar)
            {
                case ':':
                case ',':
                    $suffixes = $lastChar;
                    $matchedSuffix = true;
                    $phraseKey = substr($phraseKey, 0, -1);
                    break;
            }
        }

        if(!empty($this->phrase[$phraseKey]))
        {
            $phrase = $this->phrase[$phraseKey];
            if(!empty($values))
            {
                foreach ($values as  $key => $value)
                {
                    $phrase = preg_replace("#\{$key\}#", $value, $phrase);
                }
            }
            if($matchedSuffix)
            {
                return $phrase . $suffixes;
            }
            else
            {
                return $phrase;
            }
        }
        else
        {
            return  $phraseKey;
        }

    }

    /**
     * @param $value
     * @return $this
     */
    public function setPhrase($value)
    {
        $this->phrase += $value;
        return $this;
    }
}