<?php namespace Ghostmonitor;

class Logger
{
    public $logentries_token;
    public $log_path         = '';
    private $local_logging   = false;
    private $logging_enabled = false;
    public $log_severity     = LOG_DEBUG;
    private $gm_id;
    private $gm_domain;
    private $gm_session;

    public function __construct($gm_id, $gm_domain, $gm_session)
    {
        $this->gm_id = $gm_id;
        $this->gm_domain = $gm_domain;
        $this->gm_session = $gm_session;

        $this->checkCookies();
    }

    public function logDebug($line)
    {
        $this->log($line, 'debug');
    }

    public function logError($line)
    {
        $this->log($line);
    }

    public function log($line, $severity = 'Error')
    {
        if (false === $this->logging_enabled) {
            return false;
        }

        if ($this->local_logging || empty($this->logentries_token) || false === class_exists('\Logentries\LeLogger')) {
            $this->logLocal($line);
            return false;
        }

        $line     = $this->createLogLine($line);
        $severity = ucfirst(strtolower($severity));
        $logger   = new \Logentries\LeLogger($this->logentries_token, $this->log_severity);

        if (!method_exists($logger, $severity)) {
            return false;
        }

        call_user_func_array(array($logger, $severity), array($line));
    }

    public function getLoggingInfo()
    {
        if (false === $this->logging_enabled) {
            return '';
        }

        $info = 'LOGGING ENABLED | ';
        $info .= 'LOCAL LOGGING: ' . ($this->local_logging ? 'TRUE' : 'FALSE');
        $info .= ($this->local_logging ? ' | LOG PATH ' . $this->log_path : '');
        $info = htmlspecialchars($info);
        $info = "console.log('$info')";

        return $info;
    }
    
    public function isLoggingEnabled()
    {
        return $this->logging_enabled;
    }

    private function logLocal($line)
    {
        if ('' === $this->log_path && false === strpos($this->log_path)) {
            return false;
        }

        $log_file_name = is_dir($this->log_path) ? join('/', array(trim($this->log_path, '/'), 'log.txt')) : $this->log_path;

        if (file_exists($log_file_name) && filesize($log_file_name) > pow(1024, 2))
        {
            file_put_contents($log_file_name, '');
        }

        file_put_contents($log_file_name, $this->createLogLine($line, true) . PHP_EOL, FILE_APPEND);
    }

    private function createLogLine($line, $timestamp = false)
    {
        $gm_id     = empty($this->gm_id) ? 'empty site_id' : $this->gm_id;
        $gm_domain = empty($this->gm_domain) ? 'empty domain' : $this->gm_domain;
        $gm_session = empty($this->gm_session) ? 'empty domain' : $this->gm_session;
        $line      = '[[ SITE_ID: ' . $gm_id . ' | DOMAIN: ' . $gm_domain . ' | SESSION_ID: ' . $gm_session . ' ]] MESSAGE: ' . var_export($line, true);

        if ($timestamp) {
            $line = date(DATE_ATOM) . ' ' . $line;
        }

        return $line;
    }

    private function checkCookies()
    {
        if (isset($_COOKIE['ghostmonitor_debug_enable_logging'])) {
            $this->logging_enabled = true;
        }
        if (isset($_COOKIE['ghostmonitor_debug_local_logging'])) {
            $this->local_logging = true;
        }
    }
}
