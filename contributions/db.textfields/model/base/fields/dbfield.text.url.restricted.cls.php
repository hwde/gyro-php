<?php
/**
 * A field to hold aa restricted URL
 * 
 * Value gets validated and matched against restrictions,
 * without restrictions this field behaves like DBFieldTextUrl.
 *
 * Restrictions needs to implement IUrlRestriction.
 *
 * @author Heiko Weber
 * @ingroup TextFields
 */
class DBFieldTextUrlRestricted extends DBFieldTextUrl {
    protected $restrictions;
    
	public function __construct($name, $default_value = null, $policy = self::NOT_NULL, $size = 255, $restrictions = array()) {
		parent::__construct($name, $default_value, $policy, $size);
        $this->restrictions = $restrictions;
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param string $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = parent::validate($value);
		if ($ret->is_ok() && Cast::string($value) !== '' && count($this->restrictions) != 0) {
            $urlParts = @parse_url($value);
            foreach($this->restrictions as $restriction) {
                $ret->merge($restriction->validate($this->name, $value, $urlParts));
            }
        }
		return $ret;
    }
}
