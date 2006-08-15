<?php

    include_once ('lib/ezutils/classes/ezfunctionhandler.php');
    include_once ('lib/ezutils/classes/ezsys.php');
    include_once( 'kernel/common/template.php' );
    include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
    include_once( "lib/ezutils/classes/ezhttptool.php" );

    $tpl = templateInit();
    $http = eZHTTPTool::instance();

    if ( $http->hasPostVariable( 'Username' ) );
    	$username = $http->postVariable( 'Username' );

    if ( $http->hasPostVariable( 'Password' ) );
    	$password = $http->postVariable( 'Password' );

    if ( $http->hasPostVariable( 'NodeID' ) );
    	$nodeID = $http->postVariable( 'NodeID' );

    // User authentication
	$user = eZUser::loginUser( $username, $password );
    if ( $user == false )
    {
        print( 'problem:Authentication failed' );
        eZExecution::cleanExit();
    }
    else
    {
        // Print the list of ID nodes..
        //Structure : name, type, ID
	$nodes = eZFunctionHandler::execute( 'content','list', array( 'parent_node_id' => $parentNodeID ) );

	$array = array();
	foreach( $nodes as $node )
	{
		$tpl->setVariable( 'node', $node );

		$nodeID = $node->attribute( 'node_id' );
		$name = $node->attribute( 'name' );
		$className = $node->attribute( 'class_name' );
		$object =& $node->object();
		$contentClass = $object->contentClass();
		$isContainer = $contentClass->attribute( 'is_container' );

		preg_match( '/\/+[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#;,]*[a-z0-9\/]{1}/si', $tpl->fetch( 'design:oo/icon.tpl' ), $matches );
		$iconPath = 'http://'. eZSys::hostname(). ':' . eZSys::serverPort() . $matches[0];
		$array[] = array( $nodeID, $name, $className, $isContainer, $iconPath );
	}

	//Test if not empty
	If ( count( $array ) == 0 )
	{
		print( 'problem:Empty' );
		eZExecution::cleanExit();
	}

	//Convert the array into a string and display it
        $display = '';
        foreach( $array as $line )
        {
            foreach( $line as $element )
            {
                $display .= $element . ';';
            }
            $display .= chr( 13 );
        }

        print( $display );

        // Don't display eZ publish page structure
        eZExecution::cleanExit();
    }
?>
