<?php
/**
 * Interface for url validation implementation
 * 
 * @author Heiko Weber
 * @ingroup Interfaces
 */
interface IUrlRestriction {
	/**
	 * Check if a url validates
	 *
	 * @param string $name Name of the field we validate
	 * @param string $url The url to validate
	 * @param array $parts The results of parse_url($url)
	 * @return Status
	 */
	public function validate($name, $url, $url_parts);
}
