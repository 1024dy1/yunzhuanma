<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Input extends CI_Input{
	
	protected function _fetch_from_array(&$array, $index = NULL, $xss_clean = NULL){
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;
		// If $index is NULL, it means that the whole $array is requested
		isset($index) OR $index = array_keys($array);
		// allow fetching multiple keys at once
		if (is_array($index)){
			$output = array();
			foreach ($index as $key){
				$output[$key] = $this->_fetch_from_array($array, $key, $xss_clean);
			}
			return $output;
		}

		if (isset($array[$index])){
			$value = $array[$index];
		}elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1){ // Does the index contain array notation
			$value = $array;
			for ($i = 0; $i < $count; $i++){
				$key = trim($matches[0][$i], '[]');
				if ($key === ''){ // Empty notation will return the value as array
					break;
				}
				if (isset($value[$key])){
					$value = $value[$key];
				}else{
					return NULL;
				}
			}
		}else{
			return NULL;
		}
		return ($xss_clean === TRUE)
			? safe_replace($this->security->xss_clean($value))
			: $value;
	}
} // END class MY_URI