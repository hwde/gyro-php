<?php
/**
 * Implementation of a generic Url validation rule
 * 
 * @author Heiko Weber
 * @ingroup Interfaces
 */

require_once dirname(__FILE__).'/../interfaces/iurlrestriction.cls.php';

class UrlPartialRestriction implements IUrlRestriction {
    const COMPARE_STRING  = 1;
    const COMPARE_REGEX   = 2;
    const COMPARE_CLOSURE = 3;

    public $type;
    public $part;
    public $aCmp;
    public $message_source;
    
	/**
	 * Construct a UrlRestriction
	 *
	 * @param int $type Type of the compare (COMPARE_STRING, ...)
	 * @param string $part Which part of the parsed Url do we validate
     * @param mixed (string or array) objects we compare the value with
     * @param string Template for building the Status message in case the validate fails
	 */
    public function __construct($type, $part, $cmp, $message_source) {
        $this->type = $type;
        $this->part = $part;
        $this->aCmp = Arr::force($cmp);
        $this->message_source = $message_source;
    }

	/**
	 * Check if a url validates
	 *
	 * @param string $url The url to validate
	 * @param array $parts The results of parse_url($url)
	 * @return Status
	 */
	public function validate($name, $url, $url_parts) {
        $ret = new Status();
        
        switch($this->type) {
            case self::COMPARE_STRING:
                if (in_array($url_parts[$this->part], $this->aCmp)) {
                    return $ret;
                }
                break;
                    
            case self::COMPARE_REGEX:
                foreach($this->aCmp as $regex) {
                    if (preg_match($regex, $url_parts[$this->part]) === 1) {
                        return $ret;
                    }
                }
                break;
                    
            case self::COMPARE_CLOSURE:
                foreach($this->aCmp as $closure) {
                    if (!is_callable($closure)) {
                        throw new Exception('UrlPartialRestriction: Callable expected');
                    } else {
                        if ($closure($url_parts[$this->part])) {
                            return true;
                        }
                    }
                }
                break;
        }
        
        $ret->append(tr($this->message_source, 'db.textfields', array('%name' => $name, '%value' => $url_parts[$this->part])));

        return $ret;        
    }    
}
