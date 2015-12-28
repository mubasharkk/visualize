<?php

class Node {

    public $name;
    private $prop;
    private $relation;
    private $children;
    private $parentNode;

    const N_TYPE_NODE = 'node';
    const N_TYPE_SOURCE = 'source';
    const N_TYPE_CTRL_POINT = 'cpoint';

    function __construct($x = NULL, $y = NULL, $value = NULL, $prop = array()) {

        $this->prop = array(
            'x' => $x,
            'y' => $y,
            'value' => $value,
            'type' => self::N_TYPE_NODE
        );

        $this->prop = (object) ($prop + $this->prop);

        $this->name = uniqid('node_');

        $this->relation = array();

        $this->children = array();

        $this->parentNode = NULL;
    }

    function setType($type) {
        $this->prop->type = $type;
    }

    function getType() {
        return $this->prop->type;
    }

    function setValue($value) {
        $this->prop->value = $value;
    }

    function setX($x) {
        $this->prop->x = $x;
    }

    function setY($y) {
        $this->prop->y = $y;
    }

    function getX() {
        return $this->prop->x;
    }

    function getY() {
        return $this->prop->y;
    }

    function setRelation($name, $distance) {
        $this->relation[$name] = $distance;
        asort($this->relation);
    }

    function getRelation($name) {
        return $this->relation[$name];
    }

    function findClosest($enableDistance = FALSE) {
        foreach ($this->relation as $index => $distance) {
            if ($enableDistance) {
                return array('name' => $index, 'distance' => $distance);
            } else {
                return $index;
            }
        }
    }
    
    function findClosestOrphan(array $cluster) {
        foreach ($this->relation as $name => $node) {
            if ($cluster[$name]->isOrphan())
                return $name;
        }

        return NULL;
    }

    function addChild(Node $node) {
        $this->children[$node->name] = $node;
        $node->setParent($this);
    }

    function getChild($name) {
        return $this->children[$node->name];
    }

    function removeChild($name) {
        unset($this->children[$name]);
    }

    function getChildrenCount() {
        return count($this->children);
    }

    function isOrphan() {
        if ($this->parentNode != NULL || $this->prop->type == self::N_TYPE_SOURCE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function setParent(Node $parent) {
        $this->parentNode = $parent;
    }

    function getParent() {
        return $this->parentNode;
    }

    function getChildren() {
        return $this->children;
    }
    
    function dissociate(){
        $this->relation = array();
    }
    
    function hasChildren(){
        if (!empty($this->children)) return TRUE;
        
        return FALSE;
    }

}
