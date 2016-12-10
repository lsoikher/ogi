<?php
namespace Ghostmonitor\Api\Utils\Response;

class Response
{
    protected $content;
    protected $status;
    protected $headers;

    public function __construct($content = '', $status = 200, array $headers = array())
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function render_json_response(array $data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
