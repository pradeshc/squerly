<?php
/**
  *
  * Squerly - Transformation class to calculate absolute values for numeric data in a 2D array
  * 
  * 
  * @author Eric Perez <ericperez@squerly.net>
  * @copyright (c)2012-2013 Squerly contributors (Eric Perez, et al.)
  * @license GNU General Public License, version 3 or later
  * @license http://opensource.org/licenses/gpl-3.0.html
  * @link http://www.squerly.net
  * 
  */
class Transform_Abs implements Transform_Interface {


/**
  *
  *  Calculates absolute values for numeric data in a 2D array
  * 
  * @param array $data 2D associative array of data to be transformed
  * @param array $fields Array of fields to apply the tranformation to (defaults to all fields)
  * @return array $data Array after transformation
  *
  * @todo Implement $fields limitation
  * 
  */
  public static function run(array $data, array $fields = array()) {
    $output = array();
    foreach($data as $row) {
      //Calculate the absolute values
      $vals = array_values($row);
      $vals = array_map('floatval', $vals);
      $vals = array_map('abs', $vals);

      //Get the key values
      $keys = array_keys($row);
      $output[] = array_combine($keys, $vals); 
    }
    return $output;
  }

}

