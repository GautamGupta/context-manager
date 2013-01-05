<?php
// General base class
abstract class PB_Framework_Base {

    protected $vars;

    function __get( $name ) {
        return apply_filters(
            strtolower( get_class( $this ) ) . '_' . $name,
            isset( $this->vars[$name] ) ? $this->vars[$name] : null
        );
    }

    function __set( $name, $value ) {
        $this->vars[$name] = $value;
    }

    function __unset( $name ) {
        unset( $this->vars[$name] );
    }

    function __isset( $name ) {
        return isset( $this->vars[$name] );
    }
}