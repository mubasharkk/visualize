<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cluster
 *
 * @author Mubashar Khokhar
 */
class Cluster {
    
    private $nodes;
    private $sourceNode;
    
    function __construct() {
        ;
    }
    
    function addNode($x, $y, $value = 0){
        $node = new Node($x, $y, $value);
        $this->nodes[$node->name] = $node;
        return $node->name;
    }
    
    function buildRelation(){
        foreach ($this->nodes as $name => $node){
            foreach ($this->nodes as $nm => $n){
                if ($name != $nm){
                    $distance = $this->calculateDistance($node, $n);
                    $this->nodes[$name]->setRelation($nm, $distance);
                }
            }
            
            $distance = $this->calculateDistance($node, $this->sourceNode);
            $this->sourceNode->setRelation($name, $distance);            
        }
        
        $this->buildHierarchy();        
    }
    
    function traceAll(){
        debug_arr($this->nodes);
    }
    
    function calculateDistance($nodeA, $nodeB){
        $distance = pow(($nodeA->getX() - $nodeB->getX()),2) + pow(($nodeA->getY() - $nodeB->getY()),2);
        $distance = sqrt($distance);
        $distance = round($distance, 3);
        return $distance;
    }
    
    function getNodes($simplify = FALSE){
        if ($simplify){
            $nodes = array();
            foreach ($this->nodes as $name => $n){
                $nodes[$name] = array(
                    'x' => $n->getX(),
                    'y' => $n->getY(),
                    'value' => NULL,
                    'type' => $n->getType()
                );
            }
            
            return $nodes;
        }else 
            return $this->nodes;
    }
    
    function setSourceNode ($x, $y, $value = 0) {
//        $this->sourceNode = new Node($x, $y, $value, array('type'=>Node::N_TYPE_SOURCE));
        // create a new node as source
        $name = $this->addNode($x, $y, $value, array('type' => Node::N_TYPE_SOURCE));        
        // set cluster source node
        $this->sourceNode = &$this->nodes[$name];
    }
    
    function getSourceNode ($simplify = FALSE) {
        if ($simplify){
            return array( 
                'x' => $this->sourceNode->getX(), 
                'y' => $this->sourceNode->getY(),
                'value' => NULL, 
                'type' => $this->sourceNode->getType() 
            );
        }else 
            return $this->sourceNode;
    }  
    
    function buildHierarchy(){        
        $sourceClosest = $this->sourceNode->findClosest();        
        $this->connect($this->sourceNode, $this->nodes[$sourceClosest]);
    }
    
    function findClosestNodes(){
        
    }
    
    function connect(Node &$nodeA, Node &$nodeB){
        
//        $orphan = $nodeB->findClosestOrphan($this->nodes);
        $orphan = $nodeB->findClosest();
        
        if (!$orphan){
            return;
        }
        
        $nodeC = &$this->nodes[$nodeB->findClosest()];
        
        $dAC = $nodeA->getRelation($nodeC->name);
        $dBC = $nodeB->getRelation($nodeC->name);
        
        if ($dBC <= $dAC){
            
            $midPointBC = array(
                'x' => ($nodeB->getX() + $nodeC->getX())/2,
                'y' => ($nodeB->getY() + $nodeC->getY())/2
            );        
            
            $cPointX = ($midPointBC['x']+$nodeA->getX())/2;
            $cPointY = ($midPointBC['y']+$nodeA->getY())/2;
            
            $CPname = $this->addNode($cPointX, $cPointY);            
            $this->nodes[$CPname]->addChild($nodeB);
            $this->nodes[$CPname]->addChild($nodeC);
            $this->nodes[$CPname]->setType(Node::N_TYPE_CTRL_POINT);
            
            $this->sourceNode->addChild($this->nodes[$CPname]);
        }
        
        if($this->hasOrphans()){
            
            $nextNode = $nodeB->findClosestOrphan($this->nodes);
            $nextNode = $this->nodes[$nextNode];
            if ( $nextNode->getRelation($nodeB->name) <= $this->sourceNode->getRelation($nextNode->name)  && $nodeB->getChildrenCount() < 2 ){
                if ($nodeC->getRelation($nextNode->name) < $nodeB->getRelation($nextNode->name) ){
                    $this->nodes[$nodeC->name]->addChild($nextNode);                    
                }else {
                    $this->nodes[$nodeB->name]->addChild($nextNode);
                }
            }
        }
        
//        if($this->hasOrphans()){
//            $this->connect($this->nodes[$nodeB->name], $this->nodes[$nodeC->name]);
//        }
        
//        debug_arr($this->sourceNode->getChildren());
        
        $this->nodes[$nodeA->name] = $nodeA;
        
    }
    
    function hasOrphans(){
        foreach ($this->nodes as $node){
            if ($node->isOrphan()){
                return TRUE;
            }
        }
        
        return FALSE;
    }    
}


class Node  {
    
    private $prop;
    private $relation;
    public  $name;
    
    private $children;
    
    private $parentNode;
    
    const N_TYPE_NODE = 'node';
    const N_TYPE_SOURCE = 'source';
    const N_TYPE_CTRL_POINT = 'cpoint';
    
    function __construct($x = NULL, $y = NULL, $value = NULL, $prop = array()) {
        
        $this->prop = array(
            'x'     => $x,
            'y'     => $y,
            'value' => $value,
            'type'  => self::N_TYPE_NODE
        );
        
        $this->prop = (object) ($prop + $this->prop);
        
        $this->name = uniqid('node_');
        
        $this->relation = array();
        
        $this->children = array();
        
        $this->parentNode = NULL;        
    }
    
    function setType ($type){
        $this->prop->type = $type;
    }
    
    function getType(){
        return $this->prop->type;
    }
    
    function setValue ($value) {
        $this->prop->value = $value;
    }
    
    function setX($x){
        $this->prop->x = $x;
    }
    
    function setY($y){
        $this->prop->y = $y;
    }
    
    function getX(){
        return $this->prop->x;
    }
    
    function getY(){
        return $this->prop->y;
    }
    
    function setRelation($name, $distance){
        $this->relation[$name] = $distance;
        asort($this->relation);
    }
    
    function getRelation($name){
        return $this->relation[$name];
    }
    
    function findClosest(){        
        foreach ($this->relation as $index => $relation){
            return $index;
        }
    }
    
    function findClosestOrphan(array $cluster){
        foreach($this->relation as $name=>$node){
            if ($cluster[$name]->isOrphan())
                return $name;
        }
        
        return NULL;
    }
    
    function addChild(Node $node){
        $this->children[$node->name] = $node;
        $node->setParent($this);
    }
    
    function getChild($name){
        return $this->children[$node->name];
    }
    
    function removeChild($name){
        unset($this->children[$name]);
    }
    
    function getChildrenCount(){
        return count($this->children);
    }
    
    function isOrphan (){
        if ($this->parentNode != NULL){
            return FALSE;
        }else {
            return TRUE;
        }
    }
    
    function setParent(Node $parent){
        $this->parentNode = $parent;
    }
    
    function getParent(){
        return $this->parentNode;
    }
    
    function getChildren(){
        return $this->children;
    }
}