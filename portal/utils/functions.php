<?php

/*
 * Returns the portal's login URL.
 */
function getPortalLoginUrl($configData){ 
    $url = "https://" .$configData['SITE_NAME']. ".chargebeeportal.com/portal/login"; 
	
    if(empty($configData['APP_PATH'])){
        $url .= "?return_url=". $configData['SITE_URL']."/index.php";
    }else {
    	$url .= "?return_url=". $configData['SITE_URL']."/".$configData['APP_PATH']."/index.php";
    }
    if(!empty($configData['CANCEL_URL'])){
        $url .= "&cancel_url=". $configData['CANCEL_URL'];
    }
    return $url;
}

/*
 * Returns the portal's "change password" URL.
 */    
function getChangePasswordUrl($configData){
    $url = "https://" . $configData['SITE_NAME'] . ".chargebeeportal.com/portal/change_password"; 
    return $url;
}

/*
 * Returns the portal's logout URL.
 */    
function getLogoutUrl($configData){
    if(empty($configData['APP_PATH'])){
        $url = $configData['SITE_URL']."/index.php?do=logout";
    } else{
        $url = $configData['SITE_URL']."/".$configData['APP_PATH']."/index.php?do=logout";
    }    
    return $url;
}

/*
 * Returns the URL of the passed page name.
 */    
function getEditUrl($pageName,$configData){        
    if(empty($configData['APP_PATH'])){
        $url = $configData['SITE_URL']."/".$pageName;
    } else{
        $url = $configData['SITE_URL'] . "/" . $configData['APP_PATH'] . "/".$pageName;
    }
    return $url;
}

/*
 * Returns the portal's cancel URL.
 */    
function getCancelURL($configData){
    if(empty($configData['APP_PATH'])){
        $url = $configData['SITE_URL']."/index.php";
    } else{
        $url = $configData['SITE_URL'] . "/" . $configData['APP_PATH'] . "/index.php";
    }  
    return $url;
}

/*
 * Return and Cancel URL for the "Update Payment Method" API.
 */
function getReturnURL(){
	global $configData;
    if(empty($configData['APP_PATH'])){
        $url = $configData['SITE_URL']."/index.php";
    } else{
        $url = $configData['SITE_URL'] . "/" . $configData['APP_PATH'] . "/index.php";
    }  
    return $url;
}

function removeQueryArg( $key, $query = false ) {
    if ( is_array( $key ) ) { // removing multiple keys
        foreach ( $key as $k )
            $query = addQueryArg( $k, false, $query );
        return $query;
    }
    return addQueryArg( $key, false, $query );
}

/*
 * Escapes the content passed in parameter.
 */
function esc($content) {
  if( $content == null ) {
   return "";
  }
  return htmlspecialchars($content);
}


function wpParseStr( $string, &$array ) {
    parse_str( $string, $array );
    if ( get_magic_quotes_gpc() )
        $array = stripslashes_deep( $array );
    $array = applyFilters( 'wpParseStr', $array );
}
function addQueryArg() {
    $args = func_get_args();
    if ( is_array( $args[0] ) ) {
        if ( count( $args ) < 2 || false === $args[1] )
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[1];
    } else {
        if ( count( $args ) < 3 || false === $args[2] )
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[2];
    }

    if ( $frag = strstr( $uri, '#' ) )
        $uri = substr( $uri, 0, -strlen( $frag ) );
    else
        $frag = '';

    if ( 0 === stripos( $uri, 'http://' ) ) {
        $protocol = 'http://';
        $uri = substr( $uri, 7 );
    } elseif ( 0 === stripos( $uri, 'https://' ) ) {
        $protocol = 'https://';
        $uri = substr( $uri, 8 );
    } else {
        $protocol = '';
    }

    if ( strpos( $uri, '?' ) !== false ) {
        list( $base, $query ) = explode( '?', $uri, 2 );
        $base .= '?';
    } elseif ( $protocol || strpos( $uri, '=' ) === false ) {
        $base = $uri . '?';
        $query = '';
    } else {
        $base = '';
        $query = $uri;
    }

    wpParseStr( $query, $qs );
    $qs = urlencodeDeep( $qs ); // this re-URL-encodes things that were already in the query string
    if ( is_array( $args[0] ) ) {
        foreach ( $args[0] as $k => $v ) {
            $qs[ $k ] = $v;
        }
    } else {
        $qs[ $args[0] ] = $args[1];
    }

    foreach ( $qs as $k => $v ) {
        if ( $v === false )
            unset( $qs[$k] );
    }

    $ret = buildQuery( $qs );
    $ret = trim( $ret, '?' );
    $ret = preg_replace( '#=(&|$)#', '$1', $ret );
    $ret = $protocol . $base . $ret . $frag;
    $ret = rtrim( $ret, '?' );
    return $ret;
}

function applyFilters( $tag, $value ) {
    global $wp_filter, $merged_filters, $wp_current_filter;

    $args = array();

    // Do 'all' actions first.
    if ( isset($wp_filter['all']) ) {
        $wp_current_filter[] = $tag;
        $args = func_get_args();
        _wp_call_all_hook($args);
    }

    if ( !isset($wp_filter[$tag]) ) {
        if ( isset($wp_filter['all']) )
            array_pop($wp_current_filter);
        return $value;
    }

    if ( !isset($wp_filter['all']) )
        $wp_current_filter[] = $tag;

    // Sort.
    if ( !isset( $merged_filters[ $tag ] ) ) {
        ksort($wp_filter[$tag]);
        $merged_filters[ $tag ] = true;
    }

    reset( $wp_filter[ $tag ] );

    if ( empty($args) )
        $args = func_get_args();

    do {
        foreach ( (array) current($wp_filter[$tag]) as $the_ )
            if ( !is_null($the_['function']) ){
                $args[1] = $value;
                $value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
            }

    } while ( next($wp_filter[$tag]) !== false );

    array_pop( $wp_current_filter );

    return $value;
}


function urlencodeDeep( $value ) {
    return map_deep( $value, 'urlencode' );
}
function map_deep( $value, $callback ) {
    if ( is_array( $value ) || is_object( $value ) ) {
        foreach ( $value as &$item ) {
            $item = map_deep( $item, $callback );
        }
        return $value;
    } else {
        return call_user_func( $callback, $value );
    }
}

function buildQuery( $data ) {
    return httpBuildQuery( $data, null, '&', '', false );
}
function httpBuildQuery( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
    $ret = array();

    foreach ( (array) $data as $k => $v ) {
        if ( $urlencode)
            $k = urlencode($k);
        if ( is_int($k) && $prefix != null )
            $k = $prefix.$k;
        if ( !empty($key) )
            $k = $key . '%5B' . $k . '%5D';
        if ( $v === null )
            continue;
        elseif ( $v === false )
            $v = '0';

        if ( is_array($v) || is_object($v) )
            array_push($ret,httpBuildQuery($v, '', $sep, $k, $urlencode));
        elseif ( $urlencode )
            array_push($ret, $k.'='.urlencode($v));
        else
            array_push($ret, $k.'='.$v);
    }

    if ( null === $sep )
        $sep = ini_get('arg_separator.output');

    return implode($sep, $ret);
}
