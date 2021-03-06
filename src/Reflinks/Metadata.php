<?php
/*
	Copyright (c) 2014, Zhaofeng Li
	All rights reserved.
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	* Redistributions of source code must retain the above copyright notice, this
	list of conditions and the following disclaimer.
	* Redistributions in binary form must reproduce the above copyright notice,
	this list of conditions and the following disclaimer in the documentation
	and/or other materials provided with the distribution.
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
	FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
	DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
	SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
	CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
	OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
	OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/*
	Metadata model
*/

namespace Reflinks;

use Reflinks\Exceptions\MetadataException;
use Reflinks\Exceptions\NoSuchMetadataFieldException;

class Metadata implements \Iterator {
	public $rawMetadata = array();
	public static $fields = array(
		"type", "url", "title", "date", "accessdate", "author", "publisher", "work", "archiveurl", "archivedate", "deadurl"
	);
	
	function __construct( array $rawMetadata = array() ) {
		$this->load( $rawMetadata );
	}
	// Iterator interface
	public function rewind() {
		reset( $this->rawMetadata );
	}
	public function current() {
		return current( $this->rawMetadata );
	}
	public function key() {
		return key( $this->rawMetadata );
	}
	public function next() {
		return next( $this->rawMetadata );
	}
	public function valid() {
		$key = key( $this->rawMetadata );
		return $this->validField( $key );
	}

	public static function validField( $name ) {
		return in_array( $name, self::$fields );
	}
	public function exists( $name ) {
		if ( !self::validField( $name ) ) {
			throw new NoSuchMetadataFieldException( $name );
		} else {
			return !empty( $this->rawMetadata[$name] );
		}
	}
	public function __isset( $name ) {
		return $this->exists( $name );
	}
	public function __set( $name, $value ) {
		if ( !self::validField( $name ) ) {
			throw new NoSuchMetadataFieldException( $name );
		} else {
			$this->rawMetadata[$name] = $value;
		}
	}
	public function set( $name, $value ) {
		return $this->__set( $name, $value );
	}
	public function __get( $name ) {
		if ( !self::validField( $name ) ) {
			throw new NoSuchMetadataFieldException( $name );
		} elseif ( !isset( $this->rawMetadata[$name] ) ) {
			return null;
		} else {
			return $this->rawMetadata[$name];
		}
	}
	public function get( $name ) {
		return $this->__get( $name );
	}
	public function __unset( $name ) {
		if ( !self::validField( $name ) ) {
			throw new NoSuchMetadataFieldException( $name );
		} elseif ( isset( $this->rawMetadata[$name] ) ) {
			unset( $this->rawMetadata[$name] );
		}
	}
	public function dump() {
		return $this->rawMetadata;
	}
	public function load( array $rawMetadata = array() ) {
		foreach( $rawMetadata as $name => $value ) {
			$this->__set( $name, $value );
		}
		return $this;
	}
	public function merge( self $metadata ) {
		$this->load( $metadata->dump() );
		return $this;
	}
}
