<?php

/*
 * class.mdb.php
 *
 * Primitive "drop in" replacement of ezSQL
 * Based on php mysqli
 *
 * @Maadinsh - http://coding.lv/ - 2012
 *
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 *
 */

class mdb extends mysqli {

    var $debug = 0;
    var $num_queries = 0;

    function __construct($username, $password, $database, $hostname = 'localhost') {
        parent::__construct($hostname, $username, $password, $database);
        if ($this->connect_errno) {
            if ($this->debug) {
                $this->error('MySQL error #' . $this->connect_errno . ': ' . $this->connect_error, true);
            } else {
                $this->error('Ooops! Something went wrong... Come back later', true);
            }
        }
        $this->query("set names utf8");
    }

    function query($query = null) {
        $this->num_queries++;
        $this->last_query = $query;
        $this->last_result = parent::query($query);

        if ($this->error) {
            try {
                throw new Exception("$this->error<br />Query:<div style='color:#d22;padding:3px 0'>$query</div>", $this->errno);
            } catch (Exception $e) {
                if ($this->debug) {
                    $this->error("MySQL error #" . $e->getCode() . " - " . $e->getMessage() .
                            '<div style="font-family:monospace;color:#555">' . nl2br($e->getTraceAsString()) . '</div>');
                }
            }
        }

        return $this->last_result;
    }

    function get_results($query = null) {
        if (!empty($query) && $data = $this->query($query)) {
            $return = array();
            while ($row = $data->fetch_object()) {
                $return[] = $row;
            }
            return $return;
        }
        return null;
    }

    function get_row($query = null) {
        if (!empty($query) && $data = $this->query($query)) {
            while ($row = $data->fetch_object()) {
                return $row;
            }
        }
        return null;
    }

    function get_var($query = null) {
        if (!empty($query) && $data = $this->query($query)) {
            while ($row = $data->fetch_row()) {
                return $row[0];
            }
        }
        return null;
    }

    function get_col($query = null) {
        if (!empty($query) && $data = $this->query($query)) {
            $return = array();
            while ($row = $data->fetch_array()) {
                $return[] = $row[0];
            }
            return $return;
        }
        return null;
    }

    function escape($input) {
        if (is_array($input)) {
            foreach ($input as $k => $i) {
                $output[$k] = $this->escape($i);
            }
        } else {
            $output = $this->real_escape_string($input);
        }
        return $output;
    }

    function error($err = 'Database error', $critical = false) {
        echo '<div style="
            padding: 5px;
            margin:10px;border: 2px solid #dde;
            background:#eef;
            font-family:arial;
            font-size: 12px;
            color:#933
            ">' . $err . '</div>';
        if ($critical) {
            exit;
        }
    }

    function debug() {
        $out = '<div style="color:#222;font-family:monospace;padding:3px">' . htmlspecialchars($this->last_query) . '</div>';

        if (!empty($this->last_result) && !is_bool($this->last_result)) {
            $out .= '<table style="border: 1px solid #dde;border-collapse:collapse">';
            $this->last_result->data_seek(0);
            $i = 1;
            while ($row = $this->last_result->fetch_assoc()) {
                $out .= '<tr>';
                if ($i == 1) {
                    foreach ($row as $key => $val) {
                        $out .= '<th style="font-size:11px;background:#dde">' . $key . '</th>';
                    }
                    $out .= '</tr><tr>';
                }
                foreach ($row as $key => $val) {
                    $out .= '<td style="font-size:11px;color:#555;padding:2px 4px;border: 1px solid #dde">' . htmlspecialchars($val) . '</td>';
                }
                $out .= '</tr>';
                $i++;
            }
            $out .= '</table>';
        } elseif (is_bool($this->last_result)) {
            $out .= '<strong>' . $this->last_result . '</strong>';
        } else {
            $out .= 'No results!';
        }
        $this->error($out);
    }

}
