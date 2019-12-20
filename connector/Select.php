<?php

class Select
{
    private static $parts = [
        "select" => "SELECT",
        "from" => "FROM",
        "where" => "WHERE",
        "group" => "GROUP BY",
        "having" => "HAVING",
        "order" => "ORDER BY",
        "limit" => "LIMIT"
    ];
    private $data = [];

    public function __construct()
    {
        $this->clear();
    }

    public function clear()
    {
        $this->data = [];
    }

    public function __call($name, $arguments)
    {
        $fun = substr($name, 0, 3);
        $part = substr($name, 3);
        switch ($fun) {
            case "set":
                switch ($part) {
                    case $part == "select" or $part == "from":
                        $this->data[$part] = "`" . $arguments[0] . "`";
                        break;
                    default:
                        $this->data[$part] = $arguments[0];
                        break;
                }
                break;
            case "clr":
                $this->data[$part] = null;
                break;
            case "add":
                switch ($part) {
                    case $part == "select" or $part == "from":
                        $this->data[$part] = $this->data[$part] . ", `" . $arguments[0] . "`";
                        break;
                    case $part == "where":
                        if (!isset($arguments)) {
                            if ($arguments[1] == "OR") {
                                $this->data[$part] = $this->data[$part] . " OR " . $arguments[0];
                            } else {
                                $this->data[$part] = $this->data[$part] . " AND " . $arguments[0];
                            }
                        } else {
                            $this->data[$part] = $this->data[$part] . " AND " . $arguments[0];
                        }
                        break;
                    default:
                        $this->data[$part] = $this->data[$part] . " " . $arguments[0];
                        break;
                }
                break;
            default:
                echo "funkce \"" . $name . "\" není podporovaná<br>\n";
                break;
        }
    }

    public function __toString()
    {
        $sql = "";
        foreach (Select::$parts as $key => $value) {
            if (!empty($this->data[$key])) {
                $sql = $sql . $value . " " . $this->data[$key] . " ";
            }
        }
        return preg_replace('/\s+/', ' ', $sql);;
    }
}
