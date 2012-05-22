<?php
/**
 * This is a helper class for WordPress that allows format HTML tags, including inputs, textareas, etc
 *
 * @author Rinat Khaziev
 * @version 0.1
 */
class Html_Helper {
	
	/**
	 * Render multiple choice checkboxes
	 *
	 *	@param string $name
	 *	@param string $description
	 *	@param array $data 
	 */
	function checkboxes( $name = '', $description = '', $data = array(), $checked = array() ) {
		if ( $name != '' ) {
			$name = filter_var ( $name, FILTER_SANITIZE_STRING );
			if ($description);
			echo $this->element('p', __( $description ) );
			echo '<input type="hidden" name="' . esc_attr( $name ) .'" value="" />';
			foreach ( (array) $data as $item ) {
				$is_checked_attr =  in_array( $item, (array) $checked ) ? ' checked="true" ' : '';
				$item = filter_var ( $item, FILTER_SANITIZE_STRING );
				echo '<div class="sm-input-wrapper">';
				echo '<input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . esc_attr( $item ) . '" id="' .esc_attr( $name ) . esc_attr( $item )  . '" ' . $is_checked_attr . ' />';
				echo '<label for="' .esc_attr( $name ) . esc_attr( $item )  . '">' . esc_attr ( $item ) . '</label>';
				echo '</div>';
			}
		}
	}
	/**
	 * this method supports unlimited arguments,
	 * each argument represents html value
	 */
	function table_row() {
		$data = func_get_args();
		$ret = '';
		foreach ( $data as $cell )
	  		$ret .= $this->element ('td', $cell, null, false );
  		return "<tr>" . $ret . "</tr>\n";
	}

	/**
	 * easy wrapper method
	 *
	 * @param $type (select|input)
	 * @param string $name
	 * @param mixed $data
	 */
	function input( $type, $name, $data = null, $attrs = array() ) {
		if ($type == 'select')
			return $this->_select( $name, $data, $attrs );
		elseif ( in_array($type, array( 'text', 'hidden', 'submit' ) ) )
			return $this->_text( $name, $type,  $data, $attrs ) ;
	}
	
	/**
	 * This is a private method to render inputs
	 * 
	 * @access private
	 */
	function _text ( $name = '', $type='text', $data = '', $attrs = array() ) {
		return '<input type="' . esc_attr( $type ) . '" value="'. esc_attr( $data ) . '" name="' . esc_attr( $name ) . '" '.$this->_format_attributes($attrs) . ' />';
	}
	
	/**
	 *
	 * @access private
	 */
	function _select( $name, $data = array(), $attrs ) {
		$ret  = '';
		foreach ( (array) $data as $key => $value ) {
		$attrs_to_pass = array( 'value' => $key );
		if ( isset( $attrs[ 'default' ] ) && $key = $attrs[ 'default' ] )
			$attrs_to_pass[ 'selected' ] = 'selected';
			$ret .= $this->element( 'option', $value, $attrs_to_pass, false );
		}
		return '<select name="' . esc_attr( $name ) . '">' . $ret . '</select>';
	}
	
	
	function table_head( $data = array(), $params = null ) {
	echo '<table><thead>';
		foreach ($data as $th) {
			echo '<th>' . esc_html( $th ) . '</th>';
		}
	echo '</thead><tbody>';
	}

	function table_foot() {
		echo '</tbody></table>';
	}

	function form_start( $attrs = array() ) {
		echo '<form' . $this->_format_attributes($attrs) .'>';
	}

	function form_end () {
		echo '</form>';
	}
	/**
	 * cast to string and return with leading zero
	 */
	function leading_zero( $number ) {
		$number = (string) $number;
		if ( strlen( $number ) > 1 )
			return $number;
		else
			return '0' . $number;
	}

	/**
	 * renders html element
	 *
	 * @param string $tag one of allowed tags
	 * @param string content innerHTML content of tag
	 * @param array $params additional attributes
	 * @param bool $escape escape innerHTML or not, defaults to true
	 * @return string rendered html tag
	 */
	function element( $tag, $content, $params = array(), $escape = true ) {
	  $allowed = array( 'div', 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'td', 'option' );
	  $attr_string = $this->_format_attributes( $params );
	  if ( in_array ( $tag, $allowed) )
	  return "<{$tag} {$attr_string}>" . ( $escape ? esc_html ( $content ) : $content ) . "</{$tag}>";
	}

	/**
	 * format and return string of allowed html attrs
	 *
	 * @param array $attrs
	 */
	function _format_attributes( $attrs = array() ) {
		$attr_string = '';
	  	foreach ( (array) $attrs as $attr => $value ) {
		  if ( in_array( $attr, $this->_allowed_html_attrs() ) )
			$attr_string .= " {$attr}='" . esc_attr ( filter_var ($value, FILTER_SANITIZE_STRING ) ) . "'";
		}
		return $attr_string;
	}
	/**
	 * validates and returns url as A HTML element
	 *
	 * @param string $url any valid url
	 * @param string $title
	 * @param $params array of html attributes
	 */
	function a( $url, $title = '', $params = array() ) {
		$attr_string = $this->_format_attributes( $params );
		if ( filter_var( trim ( $url ), FILTER_VALIDATE_URL ) )
			return '<a href="' . esc_url( trim( $url ) ) . '" ' . $attr_string . '>' . ( $title != '' ? esc_html ( $title ) : esc_url( trim( $url ) ) ) . '</a>';
	}
	/**
	 * returns allowed HTML attributes
	 */
	function _allowed_html_attrs() {
		return apply_filters( 'hh_allowed_html_attributes', array( 'href', 'class', 'id', 'value', 'action', 'name', 'method', 'selected', 'checked' ) );
	}
}