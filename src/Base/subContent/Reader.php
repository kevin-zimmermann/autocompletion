<?php
namespace Base\subContent;

class Reader
{
    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $url;

    /**
     * @param $url
     * @return $this
     */
    public function getUntrusted($url)
    {
        $this->url = $url;
        $this->content = file_get_contents($url);
        return $this;
    }

    /**
     * @return array|mixed
     */
    public function getContent()
    {
        return json_decode($this->content);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return (int)$this->getHeader()[1];
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        $header = file_get_contents($this->url);
        return list($version, $statusCode, $msg) = explode(' ', $http_response_header[0], 3);
    }
}