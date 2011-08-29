<?php
/******************************************************************************
 SI_DomNode and SI_Dom objects
 
 Used to crawl the Benchmark XML
 ******************************************************************************/
class SI_DomNode
{
	var $dom;
	var $nodeName	= '';
	var $cdata		= '';
	var $nodeId;
	var $parentNodeId;
	var $childNodes = array();
	
	function SI_DomNode($nodeId, $nodeName = '', $attrs = array())
	{
		$this->nodeId			= $nodeId;
		$this->nodeName			= $nodeName;
		if (!empty($attrs))
		{
			foreach ($attrs as $attr => $value)
			{
				$attr = strtolower($attr);
				$this->$attr = $value;
			}
		}
	}
	
	function &getNodesByNodeName($nodeName)
	{
		$nodes = array();
		foreach($this->childNodes as $node)
		{
			//echo $node->selector."\r";
			if (strtolower($node->nodeName) == strtolower($nodeName))
			{
				array_push($nodes, $node);
			}
			$nestedNodes = $node->getNodesByNodeName($nodeName);
			$nodes = array_merge($nodes, $nestedNodes);
		}
		return $nodes;
	}
	
	function &getChildNodesByNodeName($nodeName)
	{
		$nodes = array();
		foreach($this->childNodes as $node)
		{
			//echo $node->selector."\r";
			if (strtolower($node->nodeName) == strtolower($nodeName))
			{
				array_push($nodes, $node);
			}
		}
		return $nodes;
	}
}

class SI_Dom extends SI_DomNode
{
    var $xmlObj;
    var $nodeLookUp = array();

    function SI_Dom($xml = '') 
    {
    	$this->name = 'DOM';
        $this->xmlObj = xml_parser_create();
        xml_set_object($this->xmlObj, $this);
        xml_set_element_handler($this->xmlObj, 'tagOpen', 'tagClose');
        xml_set_character_data_handler($this->xmlObj, "cdata");
        
        if (!empty($xml))
        {
        	$this->nodeId = count($this->nodeLookUp);
        	$this->nodeLookUp[] =& $this;
        	$this->parse($xml);
        }
    }

    function parse($data) 
    {
        if (!xml_parse($this->xmlObj, $data, true))
        {
        	printf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->xmlObj)), xml_get_current_line_number($this->xmlObj));
        }
    }

    function tagOpen($parser, $nodeName, $attrs)
    {
    	$node =& new SI_DomNode(count($this->nodeLookUp), $nodeName, $attrs);
    	$this->nodeLookUp[] = $node;
    	array_push($this->childNodes, $node);
    }

    function cdata($parser, $cdata) 
    {
    	$parentId = count($this->childNodes) - 1;
		$this->childNodes[$parentId]->cdata = $cdata;
    }

    function tagClose($parser, $nodeName) 
    {
    	$totalNodes = count($this->childNodes);
    	if ($totalNodes == 1)
    	{
    		$node =& $this->childNodes[0];
    		$node->parentNodeId = 0;
    		$container = strtolower($node->nodeName);
    		$this->$container =& $node;
    	}
		else if($totalNodes > 1)
		{
			$node		= array_pop($this->childNodes);
			$parentId	= count($this->childNodes) - 1;
			$node->parentNodeId = $this->childNodes[$parentId]->nodeId;
			$this->childNodes[$parentId]->childNodes[] =& $node;
			$this->nodeLookUp[$node->nodeId] =& $node;
		}
    }
}