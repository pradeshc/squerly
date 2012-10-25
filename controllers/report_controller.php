<?php
/**
  *
  * Squerly - Report Controller
  * 
  * This file contains all the additional routes and supporting code that is specific to reports
  * 
  * @author Eric Perez <ericperez@squerly.net>
  * @copyright (c)2012 Squerly contributors (Eric Perez, et. al.)
  * @license GNU General Public License, version 3 or later
  * @license http://opensource.org/licenses/gpl-3.0.html
  * @link http://www.squerly.net
  * 
  */


class Report_Controller extends Crud_Controller {
  //TODO: implement this
  protected static $_forms = array('add' => 'Form_Report_Add');

 /**
  *
  * Load a report
  *
  * @param int $id Report ID to load
  * @return object Report object from the report factory based on 'type' field
  *
  */
  protected static function _loadReport($id) {
    $id = is_int($id) ? $id : (int) F3::get('PARAMS["id"]') ?: null;
    if(!$id) { F3::reroute(F3::get('URL_BASE_PATH') . '/report'); }
    $report = Report::delegate($id);
    return $report;
  }


 /**
  *
  * Email report action
  *
  * @param int $id Report ID to load
  *
  */
  public static function email($id = null) {
    $report = self::_loadReport($id);
    //TODO: 
    //Load a saved configuration
    //Validate form
    //Get the report results
    //Send out the email

  }


 /**
  *
  * 'Form Action'
  * 
  * Renders an HTML form for the given report
  *
  * @param int $id Report ID to load
  * 
  * @todo Finish this
  *
  */
  public static function form($id = null) {
    $report = self::_loadReport($id);
    //TODO: 
    //Load a saved configuration
    //Validate form
    //Get the report results
    //Send out the email

  }


 /**
  *
  * 'List Records/Index' action
  * 
  * @param boolean $bogus - Unused param for inheritance sake
  *
  *   
  */
  public static function index($bogus = false) {
    //These are the fields that show up on the index page
    $index_fields = 'id, type, name, enabled, hidden_from_ui, created_at, updated_at';
    self::_getIndexRecords($index_fields);
    parent::index(false);
  }


 /**
  *
  * 'HTML Select' Action - Echos ID/name value pairs for a given model as an HTML select element
  * 
  * This can be used in AJAX calls to populate the innerHTML of a DIV with the list of available model instances
  * 
  * @param array $config Form select element configuration
  * @param string $where unused
  * @param string $order_by unused
  * 
  * @todo: Allow config to be passed in or read from GET params
  *
  */
  public static function optionlist($config = null, $where = '', $order_by = '') {
    $hidden_from_ui_where = " (hidden_from_ui = 0 OR hidden_from_ui = 'false') ";
    $where = empty($where) ? $hidden_from_ui_where : $where . ' AND ' . $hidden_from_ui_where;
    parent::optionlist($config, $where, $order_by);
  }


 /**
  *
  * Render report results/output (AJAX) action
  * 
  * In contrast to 'results,' this action will load any front-end plugins necessary to render the report 
  *   results; if no render template is available, this method will returns the same data as 'results'
  *
  * @param int $id Report ID to load
  *
  */
  public static function render($id = null) {
    session_write_close(); //Open sessions will block concurrent requests
    $report = self::_loadReport($id);
    //TODO: run form validation and spit out messages on failure
    //Load the data from the data source and render the results
    $filename = String::machine($report->name) . '_results_' . date('m-d-Y');
    $preview = isset($_GET['preview']);
    echo Export::loadLayout(Export::render($report->getResults($preview), $filename), $report->name);
  }


 /**
  *
  * Report results/output (AJAX) action
  *
  * @param int $id Report ID to load
  *
  */
  public static function results($id = null) {
    session_write_close(); //Open sessions will block concurrent requests
    $report = self::_loadReport($id);
    //TODO: run form validation and spit out messages on failure
    //Load the data from the data source and render the results
    $filename = String::machine($report->name) . '_results_' . date('m-d-Y');
    $preview = isset($_GET['preview']);
    echo Export::render($report->getResults($preview), $filename);
  }


 /**
  *
  * Run report action
  *
  * @param int $id Report ID to load
  * 
  * @todo Finish this
  *
  */
  public static function run($id = null) {
    $report = self::_loadReport($id);
    //Get the template tags out of the report query and input URI
    //TODO: make this an array with report field names as keys??

    //Parse out the mustache tags
    //Build a form from tags
    //Send form to view
  }


 /**
  *
  * Report validation (AJAX) action
  *
  * @param int $id Report ID to load
  * 
  * @todo Finish this
  *
  */
  public static function validate($id = null) {
    session_write_close(); //Open sessions will block concurrent requests
    //Load the report
    //TODO: run form validation and spit out messages on failure

    //Run the report against the DB and render the results
    //$filename = String::machine($report->name) . '_results_' . date('m-d-Y');
    //echo Export::render($report->getResults(), $filename);
  }

}


//Report Routes
//TODO: put these into a method
F3::route('GET ' . F3::get('URL_BASE_PATH') . 'report/optionlist', 'Report_Controller::optionlist', 30);
F3::route('GET ' . F3::get('URL_BASE_PATH') . 'report/email/@id', 'Report_Controller::email');
F3::route('GET ' . F3::get('URL_BASE_PATH') . 'report/form/@id', 'Report_Controller::form', 10);
F3::route('GET ' . F3::get('URL_BASE_PATH') . 'report/render/@id', 'Report_Controller::render', 10);
F3::route('GET ' . F3::get('URL_BASE_PATH') . 'report/results/@id', 'Report_Controller::results', 10);
F3::route('GET ' . F3::get('URL_BASE_PATH') . 'report/run/@id', 'Report_Controller::run', 600);
F3::route('GET ' . F3::get('URL_BASE_PATH') . 'report/validate/@id', 'Report_Controller::validate');
