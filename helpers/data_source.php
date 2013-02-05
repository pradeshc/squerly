<?php
/**
  *
  * Squerly - Data Source/Import Helpers
  * 
  * @author Eric Perez <ericperez@squerly.net>
  * @copyright (c)2012-2013 Squerly contributors (Eric Perez, et al.)
  * @license GNU General Public License, version 3 or later
  * @license http://opensource.org/licenses/gpl-3.0.html
  * @link http://www.squerly.net
  * 
  * @todo refactor this as a factory/plugin loader?
  * @todo Add more import methods for Google Spreadsheets, MongoDB, CouchDB, etc.
  * 
  */
class Data_Source {

 /**
  *
  * SQL SELECT import method
  * 
  * This method loads data from a MySQL, PostgreSQL, or SQLite database
  *
  * @param string $bound_sql_query SQL query with named bind-parameter placeholders
  * @param array $bind_params Associate array of parameters to be bound to SQL query in $bound_sql_query
  * @param string $DBC Name of the variable that holds a reference to the database handle
  * @param int $cache_expiry Number of seconds to cache the query
  * @return array 2D associative array holding the results of the query
  *
  */
  public static function loadSQL($bound_sql_query, $bind_params, $DBC = 'DB', $cache_expiry = null) {
    //If cache not set, use default; if default not set, expire immediately
    $cache_expiry = $cache_expiry ?: F3::get('REPORT_CACHE_EXPIRE') ?: 0;
    return DB::sql($bound_sql_query, $bind_params, $cache_expiry, $DBC);
  }


/**
  *
  * CSV File/URI import method
  * 
  * Loads data from a CSV file/URI and converts it into an associative array
  * 
  * @param string $file_path Local file path or URI that points to CSV data
  * @param int $max_rows Maximum number of rows of CSV data to load
  * @param string $delimiter Character that delimits the CSV fields (defaults to a comma)
  * @return array 2D associative array holding a representation of the CSV data
  *
  */
  public static function loadCSVFile($file_path, $max_rows = 0, $delimiter = ',') {
    ini_set("auto_detect_line_endings", "1");
    $output = array();
    if(($handle = fopen($file_path, "r")) !== FALSE) {
      $row = 1;
      while(($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        //Build the header/column names
        if($row === 1) {
          $header = $data; 
          $row++;
          continue;
        }
        $output[] = array_combine($header, array_map(function($input) { 
            //Deal with 'division by zero' string in Excel-generated CSV files
            return str_replace('#DIV/0!', '0', trim($input));
          }, $data));
        if($max_rows > 0 && $row++ > $max_rows) { break; }
      }
      fclose($handle);
    }
    ini_restore("auto_detect_line_endings");
    return $output;
  }


/**
  *
  * CSV String import method
  * 
  * Loads data from a CSV string and converts it into an associative array
  * 
  * @param string $input CSV string
  * @param int $max_rows Maximum number of rows of CSV data to load
  * @param string $delimiter Character that delimits the CSV fields (defaults to a comma)
  * @param array $header Array of field/header columns
  * @param string $row_trim_charts String of characters to trim off of each row
  * @param int $ignore_lines Number of lines to skip
  * @return array 2D associative array holding a representation of the CSV data
  *
  */
  public static function loadCSVString($input, $max_rows = 0, $delimiter = ',', array $header = array(), $row_trim_chars = '', $ignore_lines = 0) {
    $output = array();
    $input_rows = ($ignore_lines === 0) ? explode(PHP_EOL, $input) : array_slice(explode(PHP_EOL, $input), $ignore_lines); 
    $input = null;
    $row = 1;

    foreach($input_rows as &$data) {
      if($row_trim_chars !== '') { $data = trim($data, $row_trim_chars); }
      $data = explode($delimiter, $data);

      //Build the header/column names row
      if($row === 1 && empty($header)) {
        $header = array_map('strval', $data);
        $row++;
        continue;
      }

      //Build the data rows
      if(!(sizeof($data) == 1 && empty($data[0]))) { 
        $output[] = array_combine($header, array_map('trim', $data));
        if($max_rows > 0 && $row++ > $max_rows) { break; }
      }
    }
    return $output;
  }


/**
  *
  * JSON File/URI import method
  * 
  * Loads data from a JSON file/URI and converts it into an associative array
  * 
  * @param string $file_path Local file path or URI that points to JSON data
  * @return array 2D associative array holding a representation of the JSON data
  *
  */
  public static function loadJSONFile($file_path) {
    return json_decode(file_get_contents($file_path), true);
  }


/**
  *
  * XML File/URI import method
  * 
  * Loads data from an XML file/URI and converts it into an associative array
  * 
  * @param string $file_path Local file path or URI that points to XML data
  * @return array 2D associative array holding a representation of the XML data
  *
  */
  public static function loadXMLFile($file_path) {
    return XmlToArray::render(file_get_contents($file_path));
  }

}
