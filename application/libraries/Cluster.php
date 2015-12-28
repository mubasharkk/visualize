<?php

/**
 * Description of Cluster
 *
 * @author Mubashar Khokhar
 */
include_once 'Node.php';

class Cluster {

    private $nodes;
    private $sourceNode;
    private $nodeBin;
    public $connections;

    function __construct() {
        $this->nodeBin = array();
        $this->connections = array();
    }

    function addNode($x, $y, $value = 0, $prop = array()) {
        $node = new Node($x, $y, $value, $prop);
        
        if (!empty($prop['type']) && $prop['type'] ==  NODE::N_TYPE_SOURCE)
            $this->sourceNode = $node;
        
        $this->nodes[$node->name] = $node;
        return $node->name;
    }

    function removeNode($name) {
        $this->nodeBin[$name] = $this->nodes[$name];
        unset($this->nodes[$name]);
    }

    function buildRelation($name) {
        foreach ($this->nodes as $nm => $n) {
            if ($name != $nm) {
                $distance = $this->calculateDistance($this->nodes[$name], $n);
                $this->nodes[$name]->setRelation($nm, $distance);
            }
        }
    }

    function rebuildRelationAll() {
        foreach ($this->nodes as $name => $node) {
            $this->nodes[$name]->dissociate();
            $this->buildRelation($name);
        }
    }

    function traceAll() {
        debug_arr($this->nodes);
    }

    function calculateDistance($nodeA, $nodeB) {
        $distance = pow(($nodeA->getX() - $nodeB->getX()), 2) + pow(($nodeA->getY() - $nodeB->getY()), 2);
        $distance = sqrt($distance);
        $distance = round($distance, 3);
        return $distance;
    }

    function getNodes($simplify = FALSE) {
        if ($simplify) {
            $nodes = array();
            foreach ($this->nodeBin as $name => $n) {
                $nodes[$name] = array(
                    'x' => $n->getX(),
                    'y' => $n->getY(),
                    'value' => NULL,
                    'type' => $n->getType()
                );
            }

            return $nodes;
        } else
            return $this->nodes;
    }

    function setSourceNode($x, $y, $value = 0) {
        $this->sourceNode = new Node($x, $y, $value, array('type' => Node::N_TYPE_SOURCE));
    }

    function getSourceNode($simplify = FALSE) {
        if ($simplify) {
            return array(
                'x' => $this->sourceNode->getX(),
                'y' => $this->sourceNode->getY(),
                'value' => NULL,
                'type' => $this->sourceNode->getType()
            );
        } else
            return $this->sourceNode;
    }

    function findClosestNodes() {
        $closest = array();
        $distance = 0;
        foreach ($this->nodes as $name => $node) {
            $arr = $node->findClosest(TRUE);
            if (empty($closest)) {
                $distance = $arr['distance'];
                $closest = array($name, $arr['name']);
            } elseif ($distance > $arr['distance']) {
                $distance = $arr['distance'];
                $closest = array($name, $arr['name']);
            }
        }

        return $closest;
    }

    function buildHierarchy() {
        foreach ($this->nodes as $node) {
            $this->buildRelation($node->name);
        }

        $this->connect();
        $this->rootIt();
        $this->buildConnections($this->nodes);
    }

    function size() {
        return count($this->nodes);
    }

    function connect() {


        $closest = $this->findClosestNodes();
        // find first node (can be any node)
        $nodeA = $this->nodes[$closest[0]];
        // find closest to nodeA
        $nodeB = $this->nodes[$closest[1]];
        // while points set has any orphan

        while ($this->size() > 1) {
            // find closest to nodeB
            $nodeC = $this->nodes[$nodeB->findClosest()];

            // if nodeA is the same as nodeC
            if ($nodeA->name == $nodeC->name) {
                // add a control point
                $cpName = $this->addControlPoint($nodeA, $nodeB);
                // set newly added control point
                $nodeA = $this->nodes[$cpName];

                // if no closest node
                if ($nodeA->findClosest() == NULL)
                    continue;

                // find closest node to control point
                $nodeB = $this->nodes[$nodeA->findClosest()];
            }else {
                // set nodeA as nodeB
                $nodeA = $nodeB;
                // set nodeB as nodeC
                $nodeB = $nodeC;
            }
        }
        
//        $this->traceAll();
    }

    function hasOrphans() {
        foreach ($this->nodes as $node) {
            if ($node->isOrphan()) {
                return TRUE;
            }
        }

        return FALSE;
    }

    function addControlPoint(Node $nodeA, Node $nodeB) {

        $cPointX = ($nodeA->getX() + $nodeB->getX()) / 2;
        $cPointY = ($nodeA->getY() + $nodeB->getY()) / 2;

        $CPname = $this->addNode($cPointX, $cPointY);

        // remove A from dataset
        $this->removeNode($nodeA->name);
        // remove B from dataset
        $this->removeNode($nodeB->name);

        // make A/B children of new control point
        $this->nodes[$CPname]->addChild($nodeA);
        $this->nodes[$CPname]->addChild($nodeB);

        // set new node type as control point
        $this->nodes[$CPname]->setType(Node::N_TYPE_CTRL_POINT);

        $this->rebuildRelationAll();
        
        $this->connections[$nodeA->name] = $CPname;
        $this->connections[$nodeB->name] = $CPname;

        $this->addToBin($this->nodes[$CPname]);

        return $CPname;
    }

    function addToBin($node) {
        $this->nodeBin[$node->name] = $node;
    }

    function buildConnections($nodes) {
        foreach ($nodes as $name => $node) {
            foreach ($node->getChildren() as $child){
                $this->connections[$child->name] = $name;
                $this->buildConnections($child->getChildren());
            }
        }
    }

    function log($data) {
        $data = print_r($data, true);
        $ci = &get_instance();
        $ci->load->helper('file');
        write_file('logs.txt', $data. PHP_EOL, 'a+');
    }
    
    function rootIt(){
        
        $this->treeSourceTraverse($this->nodes);
        
        foreach ($this->nodes as $node){
            $node->setX($this->sourceNode->getX());
            $node->setY($this->sourceNode->getY());
            $node->setType(Node::N_TYPE_SOURCE);  
            $node->name = $this->sourceNode->name;
            
            foreach ($this->sourceNode->getChildren() as $child){
                $node->addChild($child);
            }
        }
        
        
//        debug_arr($this->sourceNode);
    }
    
    function treeSourceTraverse($nodes){
        foreach ($nodes as $node){
            // find source node in the tree
            if ($node->getType() == Node::N_TYPE_SOURCE){
                // get source parent
                $parent = $node->getParent();
                // remove source from parent
                $parent->removeChild($node->name);
                // disconnect source from parent
                unset($this->connections[$node->name]);
                // get source grand parent
                $grandParent = $parent->getParent();
                // give source siblings to grand parents
                foreach ($parent->getChildren() as $child){
                    $grandParent->addChild($child);
                    // connect source siblings with grand parent
                    $this->connections[$child->name] = $grandParent->name;
                }
                // remove source parent from grand parent
                $grandParent->removeChild($parent->name);
                // disconnect source parent from grand parent
                unset($this->connections[$parent->name]);
                // permanently remove source parent
                unset($this->nodeBin[$parent->name]);
            }else {
                $this->treeSourceTraverse($node->getChildren());
            }
        }
    }

}
