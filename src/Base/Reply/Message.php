<?php


namespace Base\Reply;


class Message
{
    /**
     * @var array
     */
    protected $messages;
    protected $type = 'error';
    protected $icon = true;

    /**
     * MessageBbCode constructor.
     * @param array $messages
     * @param string $type
     * @param bool $icon
     */
    public function __construct(array $messages, $type = "error", $icon = true)
    {
        $this->messages = $messages;
        $this->type = $type;
        $this->icon = $icon;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->renderMessage($this->type, $this->icon);
    }

    /**
     * @param bool $icon
     * @return string
     */
    public function renderError($icon = true)
    {
        return $this->renderMessage('error', $icon);
    }

    /**
     * @param bool $icon
     * @return string
     */
    public function renderImportant($icon = true)
    {
        return $this->renderMessage('important', $icon);
    }

    /**
     * @param bool $icon
     * @return string
     */
    public function renderSusses($icon = true)
    {
        return $this->renderMessage('susses', $icon);
    }

    /**
     * @param $type
     * @param bool $icon
     * @return string
     */
    protected function renderMessage($type, $icon = true)
    {
        $message = $this->messages;
        if (!empty($message)) {
            $output = "";
            if (count($message) > 1)
            {
                $output .= "<ul>";
                foreach ($message as $error)
                {
                    $output .= "<li>" . $error . "</li>";
                }
                $output .= "</ul>";
            }
            else
            {
                $output = $message[0];
            }
            $classIcon = $icon ? 'block-rowMessage--iconic' : '' ;
            return "<div class=\"blockMessage blockMessage--$type $classIcon\">"
                . $output .
                "</div>";
        }
        else
        {
            return "";
        }
    }
    public function renderMessageNoHtml()
    {
        $message = $this->messages;
        if (!empty($message)) {
            $output = "";
            if (count($message) > 1)
            {
                $output .= "<ul>";
                foreach ($message as $error)
                {
                    $output .= "<li>" . $error . "</li>";
                }
                $output .= "</ul>";
            }
            else
            {
                $output = $message[0];
            }
            return "<div class=\"bock-errors-ajax\">"
                . $output .
                "</div>";
        }
        else
        {
            return "";
        }
    }
}