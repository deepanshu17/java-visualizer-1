<?php
/**
 * Definition of log events
 *
 * @package    mod
 * @subpackage jeliot
 */
defined('MOODLE_INTERNAL') || die();
$logs = array(
    array('module'=>'jeliot', 'action'=>'view', 'mtable'=>'newmodule', 'field'=>'name'),
    array('module'=>'jeliot', 'action'=>'update', 'mtable'=>'newmodule', 'field'=>'name'),
    array('module'=>'jeliot', 'action'=>'add', 'mtable'=>'newmodule', 'field'=>'name'),
    );