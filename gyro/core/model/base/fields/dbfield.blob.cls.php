<?php
require_once dirname(__FILE__) . '/dbfield.text.cls.php'; 

/**
 * A BLOB field im DB
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldBlob extends DBFieldText {
	
	/**
	 * Format values that are not NULL
	 */
	protected function do_format_not_null($value) {
		if ($value !== '') {
			return '0x' . bin2hex($value);
		}
		else {
			return parent::do_format_not_null('');
		}
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param string $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = new Status();
		$l = strlen(strval($value));
		if ($l > $this->length) {
			$ret->append(tr(
				'%field may have no more than %num bytes', 
				'core', 
				array(
					'%field' => $this->get_field_name_translation(),
					'%num' => $this->length
				)
			));
		}
		else if ($l == 0 && !$this->get_null_allowed()) {
			$ret->append(tr(
				'%field may not be empty', 
				'core', 
				array(
					'%field' => $this->get_field_name_translation(),
				)
			));
		}
		return $ret;
	}	
}
