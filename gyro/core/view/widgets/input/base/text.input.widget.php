<?php
/**
 * A text widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetTextBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		$attrs['value'] = $value;
	}
	
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
        $type = Arr::get_item($attrs, 'type', 'text');
		return html::input($type, $name, $attrs);
	}	
}