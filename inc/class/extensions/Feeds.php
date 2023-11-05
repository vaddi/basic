<?php

// Class <strong>Feeds</strong> will be used to get an Endpoint for RSS/Atom Newsfeeds from the News Page of the Site. This is currently a Work in Progress and is not ready yet.


/**
 * vaddis Feedcreator
 * 
 * Part of PHP basic Page Templating
 * Source: https://github.com/vaddi/basic/blob/main/inc/class/extensions/Feeds.php
 */
class Feeds {

	static private $_encoding = "utf-8";

	public static function generate( $data = null ) {
    $result = false;
    if( $data === null ) return $result;
		$doc = self::createDoc( $data );
	  $result = $doc->saveXML();
		header("Content-type: text/plain; charset=" . self::$_encoding );
    http_response_code( 200 );
		// output the Feeds
		print_r( $result );
    // exit after we hafe rendered the feeds
    exit;
	}

	public static function innerNodes( $inner_value, $inner_key, $doc, $root ) {
		// first level Nodes
		if( is_array ( $inner_value ) ) {
			$attributes = array();
			$content = null;
			foreach( $inner_value as $key => $value ) {
				if( $key == 'text' ) {
					// the text
					$content = $value;
				} else {
					$attributes[ $key ] = $value;
				}
			} // end forach inner_value
			self::createNode( $doc, $root, $inner_key, $content, $attributes );
		} else {
			self::createNode( $doc, $root, $inner_key, $inner_value );
		} 
	}

	/**
	 * Elipses Text
	 */
	public static function ellipsesText( $input, $length = 320, $ellipses = true, $strip_html = true ) {
		// strip tags, if desired
		if( $strip_html ) {
			$input = strip_tags( $input );
		}
		// no need to trim, already shorter than trim length
		if( strlen( $input ) <= $length ) {
			return $input;
		}
		// find last space within length
		$last_space = strrpos( substr( $input, 0, $length ), ' ' );
		$trimmed_text = substr( $input, 0, $last_space );
		// add ellipses (...)
		if( $ellipses ) {
			$trimmed_text .= '...';
		}
		return $trimmed_text;
	}
	
	/**
	 * create an Item
	 */
	public static function createNode( $doc = null, $parent = null, $name = null, $content = null, $attributes = null ) {
		if( $doc == null || $parent == null || $name == null ) return false;
	  $nodeId = $doc->createElement( $name );
	  $parent->appendChild( $nodeId );
		if( $content != null && $content != "" ) {
			if( is_array( $content ) ) {
				foreach( $content as $nodeName => $nodeValue ) {
					$contentNodeName = $doc->createElement( $nodeName );
					$nodeId->appendChild( $contentNodeName );
					if( strpos( $nodeValue, "\n" ) != false || strpos( $nodeValue, "&" ) != false ) { // contains linebreack or ampersand
						$contentNodeValue = $doc->createCDATASection( $nodeValue );
						$contentNodeName->appendChild( $contentNodeValue );
					} else {
						$contentNodeValue = $doc->createTextNode( $nodeValue );
						$contentNodeName->appendChild( $contentNodeValue );
					}
				}
			} else {
				// When content contains spezial chars, use cdata
				if( strpos( $content, "\n" ) != false || strpos( $content, "&" ) != false ) { // contains linebreack or ampersand
			  	$nodeContent = $doc->createCDATASection( $content );
			  	$nodeId->appendChild( $nodeContent );
				} else {
			  	$nodeContent = $doc->createTextNode( $content );
			  	$nodeId->appendChild( $nodeContent );
				}
			}
		}
		if( is_array( $attributes ) ) {
			foreach( $attributes as $attName => $attValue ) {
				$attributeName = $doc->createAttribute( $attName );
				$nodeId->appendChild( $attributeName );
				$attributeContent = $doc->createTextNode( $attValue );
				$attributeName->appendChild( $attributeContent );
			}
		}
	}
	
	public static function createDoc( $data ) {
		$index = 0;
		foreach( $data as $data_key => $data_value ) {
			if( $index == 0 ) {
				// xml version and encoding
				$doc = new DomDocument( '1.0', self::$_encoding );
				$doc->formatOutput = true;
			  // create root node and append to doc
			  $root = $doc->createElement( $data_key );
			  $doc->appendChild( $root );
				if( isset( $data_value['channel'] ) && is_array( $data_value['channel']  ) ) {
					// channels node -> rss feedstyle
					$channel = $doc->createElement( 'channel' );
					$root->appendChild( $channel );
					// iterate over childs
					foreach( $data_value['channel']  as $inner_key => $inner_value ) {
						// entries
						if( $inner_key == 'entry' || $inner_key == 'item' ) {
							// entries
							$index_entries = 0;
							$entry = array();
							foreach( $inner_value as $entry_key => $entry_value ) {
								foreach( $entry_value as $item_key => $item_value ) {
									$entry[ $item_key ] = $item_value;
								}
								// add entry
						    $item_node = $doc->createElement( $inner_key );
						    $channel->appendChild( $item_node );
								foreach( $entry as $ekey => $evalue ) {
									self::createNode( $doc, $item_node, $ekey, $evalue );
								}
								$index_entries++;
							} // end forach entries
						} else {
							// first level Nodes
							if( is_array ( $inner_value ) ) {
								$attributes = array();
								$content = null;
								foreach( $inner_value as $key => $value ) {
									if( $key == 'text' ) {
										// the text
										$content = $value;
									} else {
										$attributes[ $key ] = $value;
									}
								} // end forach inner_value
								self::createNode( $doc, $channel, $inner_key, $content, $attributes );
							} else {
								self::createNode( $doc, $channel, $inner_key, $inner_value );
							}
						}
					} // end forach childs
				} else {
					// no channel node -> atom feedstyle
					// iterate over childs
					foreach( $data_value  as $inner_key => $inner_value ) {
						// entries
						if( $inner_key == 'entry' || $inner_key == 'item' ) {
							// entries
							$index_entries = 0;
							$entry = array();
							foreach( $inner_value as $entry_key => $entry_value ) {
								foreach( $entry_value as $item_key => $item_value ) {
									$entry[ $item_key ] = $item_value;
								}
								// add entry
						    $item_node = $doc->createElement( $inner_key );
						    $root->appendChild( $item_node );
								foreach( $entry as $ekey => $evalue ) {
									self::createNode( $doc, $item_node, $ekey, $evalue );
								}
								$index_entries++;
							} // end forach entries
						} else {
							// first level Nodes
							if( is_array ( $inner_value ) ) {
								$attributes = array();
								$content = null;
								foreach( $inner_value as $key => $value ) {
									if( $key == 'text' ) {
										// the text
										$content = $value;
									} else {
										$attributes[ $key ] = $value;
									}
								} // end forach inner_value
								self::createNode( $doc, $root, $inner_key, $content, $attributes );
							} else {
								self::createNode( $doc, $root, $inner_key, $inner_value );
							} 
						}
					} // end forach childs
				}
			} else {
				// apend attributes to root Node
			  $version = $doc->createAttribute( $data_key );
			  $root->appendChild( $version );
			  $text = $doc->createTextNode( $data_value );
			  $version->appendChild( $text );
			}
			$index++;
		}
		return $doc;
	}
	

}

?>
