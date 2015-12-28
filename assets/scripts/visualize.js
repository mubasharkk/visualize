

function Visualize() {

    this.canvas = null;
    this.objects = [];

    this.setUSAMap = function(canvasId) {
        this.canvas = new fabric.Canvas(canvasId , {
//            backgroundImage: SITE_URL + 'assets/svg/US_map_blank.svg'
        });                
//        this.canvas.on('object:moving', function(e) {
//            var activeObject = e.target;
//            $("#topC").val(activeObject.get('top'));
//            $("#leftC").val(activeObject.get('left'));
//        });

//        var line = new fabric.Line([250, 125, 450, 175], {
//            fill: 'red',
//            stroke: 'red',
//            strokeWidth: 2,
//        });

        $.get(SITE_URL + 'test/json', function (response){
            var j = 0, options = null, label = '';
            for(var i in response.nodes){
                j++;
                label = 'N'+j.toString();
                if (response.nodes[i].type == 'cpoint'){
                    options = {
                        strokeWidth: 2,
                        radius: 2,
                        fill: '#fff',
                        stroke: '#f154aa'
                    };
                    label = '';
                }else if (response.nodes[i].type == 'source') {
                    options = {
                        strokeWidth: 2,
                        radius: 10,
                        fill: '#fff',
                        stroke: '#2593ed'
                    };
                    label = 'S';
                } else {
                    options = null;
                }
                
                visualize.createNode(parseFloat(response.nodes[i].x), parseFloat(response.nodes[i].y), label, options);
            }
            
            console.log(j);
            
//            visualize.createNode(parseFloat(response.source.x), parseFloat(response.source.y), 'S',{
//                strokeWidth: 2,
//                radius: 10,
//                fill: '#fff',
//                stroke: '#2593ed'
//            });

            var nodeB = null;
            var route = [];
            for (var nodeA in response.relation){
                
                nodeB = response.relation[nodeA];
                if (response.nodes[nodeB] == undefined) continue;
                var co_ord = [
                    response.nodes[nodeB].y,
                    response.nodes[nodeB].x,
                    response.nodes[nodeA].y,
                    response.nodes[nodeA].x
                ];
                console.log(co_ord);
                var line = new fabric.Line(co_ord, {
                    fill: '#93ed25',
                    stroke: '#93ed25',
                    strokeWidth: 1,
                });
                visualize.canvas.add(line);
                visualize.canvas.sendToBack(line)
                
            }
        });
        
//        this.canvas.add(line);

    }

    this.createNode = function(top, left, text, options) {
        
        var text = new fabric.Text(text,{
            fontSize:10, 
            fontWeight: 'bold', 
            fontFamily : 'courier',
            top: 0,
            left: 0
        });
        
        if (options == undefined || options == null){
            options = {
                strokeWidth: 2,
                radius: 10,
                fill: '#fff',
                stroke: '#e5131e'
            };
        }
        var circle = new fabric.Circle(options);

        var node = new fabric.Group([circle, text], {
            left: left,
            top: top,            
        });
        node.hasControls = node.hasBorders = false;

        this.canvas.add(node);
        return node;
    }
    
    this.connectNode = function(nodeA, nodeB){
        var coords = [nodeA.y, nodeA.x, nodeB.y, nodeB.x];
        console.log(coords);
        return new fabric.Line(coords, {
          fill: 'red',
          stroke: 'red',
          strokeWidth: 1,
          selectable: false
        });   
    }
}


var visualize = new Visualize();

fabric.Object.prototype.originX = fabric.Object.prototype.originY = 'center';