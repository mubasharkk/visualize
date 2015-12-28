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

    function __construct() {
        ;
    }

    function addNode($x, $y, $value = 0) {
        $node = new Node($x, $y, $value);
        $this->nodes[$node->name] = $node;
        return $node->name;
    }

    function buildRelation() {
        foreach ($this->nodes as $name => $node) {
            foreach ($this->nodes as $nm => $n) {
                if ($name != $nm) {
                    $distance = $this->calculateDistance($node, $n);
                    $this->nodes[$name]->setRelation($nm, $distance);
                }
                
                $distance = $this->calculateDistance($this->sourceNode, $node);
                $this->sourceNode->setRelation($node->name, $distance);
            }
        }

        $this->buildHierarchy();
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
            foreach ($this->nodes as $name => $n) {
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
        $this->sourceNode = new Node($x, $y, $value, array('type'=>Node::N_TYPE_SOURCE));
        // create a new node as source
//        $name = $this->addNode($x, $y, $value, array('type' => Node::N_TYPE_SOURCE));
        // set cluster source node
//        $this->sourceNode = &$this->nodes[$name];
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

    function buildHierarchy() {
        $names = $this->findClosestNodes();
        
        $next = $this->connect($this->sourceNode);
        
        $this->nodes[$this->sourceNode->name] = $this->sourceNode;
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

    function connect(Node &$node) {
        // find closest to the node
        $closestA = &$this->nodes[$node->findClosestOrphan($this->nodes)];
        
        if (!$this->hasOrphans() || $this->sourceNode->getRelation($closestA->name) < $node->getRelation($closestA->name)) 
            return;             

        // if found nodes is orphan
        if ($closestA->isOrphan()){
            // find closest node to closestA
            $closestB = &$this->nodes[$closestA->findClosest()];
        }
        // create a control point
        $cpName = $this->addControlPoint($node, $closestA, $closestB);

        // if current node is source
        if ($node->getType() == Node::N_TYPE_SOURCE){
            // connect with source node
            $this->sourceNode->addChild($this->nodes[$cpName]);
        }else {
            $node->addChild($this->nodes[$cpName]);
            $this->connect($closestA);
        }
        
               
//        return $closestA->name;
//        debug_arr($this->sourceNode);
    }

    function hasOrphans() {
        foreach ($this->nodes as $node) {
            if ($node->isOrphan()) {
                return TRUE;
            }
        }

        return FALSE;
    }

    function addControlPoint(Node &$nodeA, Node &$nodeB, Node &$nodeC) {
        
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
        
        return $CPname;
    }

}

