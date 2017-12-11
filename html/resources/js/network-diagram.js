function AjaxCaller(){
    var xmlhttp=false;
    try{
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }catch(e){
        try{
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }catch(E){
            xmlhttp = false;
        }
    }

    if(!xmlhttp && typeof XMLHttpRequest!='undefined'){
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

function callPage(url, div){
    ajax=AjaxCaller(); 
    ajax.open("GET", url, true); 
    ajax.onreadystatechange=function(){
        if(ajax.readyState==4){
            if(ajax.status==200){
                div.innerHTML = ajax.responseText;
            }
        }
    }
    ajax.send(null);
} 

var switchesJSON;
$.get("getSwitches.php",function(data){
  switchesJSON = JSON.parse(data);
});

var networkJSON;
$.get("getSwitchDevices.php",function(data){
  networkJSON = JSON.parse(data);
});

var nodes = new vis.DataSet();
var edges = new vis.DataSet();
var network = new vis.DataSet();

var DIR = '../plugins/vis-js/examples/network/img/refresh-cl/';
var EDGE_LENGTH_MAIN = 150;
var EDGE_LENGTH_SUB = 50;

// Called when the Visualization API is loaded.
function draw() {

  // Create a data table with nodes.
  nodes = [];

  // Create a data table with links.
  edges = [];

  // callPage("getSwitches.php",document.getElementById("switchesJSON"));
  // switchesJSON = JSON.parse(document.getElementById("switchesJSON").innerHTML);
  

  // callPage("getSwitchDevices.php",document.getElementById("networkJSON"));
  // networkJSON = JSON.parse(document.getElementById("networkJSON").innerHTML);

  // console.log(switchesJSON);
  // console.log(networkJSON);

  for(var i = 0; i< switchesJSON.length; i++){
    nodes.push({id: switchesJSON[i].switchID, label: switchesJSON[i].name, image: DIR + 'Network-Pipe-icon.png', shape: 'image'})
  }

  for(var i = 0; i < networkJSON.length; i++){
    /*alert('i: ' + i + " id:" + nodes[i].label);*/
    if(nodes[i].label != "")
    {
      nodes.push({id: networkJSON[i].deviceID, label: networkJSON[i].device_name, image: DIR + 'Hardware-My-Computer-3-icon.png', shape: 'image'})
    }
    edges.push({from: networkJSON[i].deviceID, to: networkJSON[i].switchID, length: EDGE_LENGTH_MAIN});
  }

  /*nodes.push({id: 1, label: 'Main', image: DIR + 'Network-Pipe-icon.png', shape: 'image'});
  nodes.push({id: 2, label: 'Office', image: DIR + 'Network-Pipe-icon.png', shape: 'image'});
  nodes.push({id: 3, label: 'Wireless', image: DIR + 'Network-Pipe-icon.png', shape: 'image'});
  edges.push({from: 1, to: 2, length: EDGE_LENGTH_MAIN});
  edges.push({from: 1, to: 3, length: EDGE_LENGTH_MAIN});

  for (var i = 4; i <= 7; i++) {
    nodes.push({id: i, label: 'Computer', image: DIR + 'Hardware-My-Computer-3-icon.png', shape: 'image', title: 'I am a Computer!'});
    edges.push({from: 2, to: i, length: EDGE_LENGTH_SUB});
  }

  nodes.push({id: 101, label: 'Printer', image: DIR + 'Hardware-Printer-Blue-icon.png', shape: 'image'});
  edges.push({from: 2, to: 101, length: EDGE_LENGTH_SUB});

  nodes.push({id: 102, label: 'Laptop', image: DIR + 'Hardware-Laptop-1-icon.png', shape: 'image'});
  edges.push({from: 3, to: 102, length: EDGE_LENGTH_SUB});

  nodes.push({id: 103, label: 'network drive', image: DIR + 'Network-Drive-icon.png', shape: 'image'});
  edges.push({from: 1, to: 103, length: EDGE_LENGTH_SUB});

  nodes.push({id: 104, label: 'Internet', image: DIR + 'System-Firewall-2-icon.png', shape: 'image'});
  edges.push({from: 1, to: 104, length: EDGE_LENGTH_SUB});

  for (var i = 200; i <= 201; i++ ) {
    nodes.push({id: i, label: 'Smartphone', image: DIR + 'Hardware-My-PDA-02-icon.png', shape: 'image'});
    edges.push({from: 3, to: i, length: EDGE_LENGTH_SUB});
  }*/

  // create a network
  var container = document.getElementById('mynetwork');
  var nodes = new vis.DataSet(nodes);
  var edges = new vis.DataSet(edges);

  var data = {
    nodes: nodes,
    edges: edges
  };
  
  var options = {
    //interaction:{hover:true},
    /*manipulation: {
      // enabled: true
    }*/
  };

  network = new vis.Network(container, data, options);

  network.on("selectNode",function(params){
    $("#network-info-box").removeClass("hidden");
    var ids = params.nodes;
    // console.log(JSON.stringify(nodes.get(ids)));
  });

  network.on("selectEdge",function(params){
    var ids = params.edges;
    var edge = edges.get(ids);
    var from = edge[0].from;
    var to = edge[0].to;
  });

  network.on("deselectNode",function(params){
  });

  network.on("zoom", function (params) {
    });

  network.on("hoverNode", function (params) {
  });
}