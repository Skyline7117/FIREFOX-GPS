var map, featureList,locationLat,locationLng,geolocation;

 /**************************************************************/
 /**************************************************************/
 /**************************************************************/
 //reconnaissance vocale 
 
 function speechVoice()
{
	 var recognition = new webkitSpeechRecognition();
	 
	  recognition.continuous = true;
	  recognition.interimResults = true;
	  recognition.lang='fr-FR';
	  recognition.start();
	  recognition.onstart = function() { console.log('reconnaissance démarrée');}
	  
	  recognition.onresult = function(event) {
	      
	    var interim_transcript = '';
	    var final_transcript='';
	    var searchbox=document.getElementById('searchbox');
	    for (var i = event.resultIndex; i < event.results.length; ++i) {
	      if (event.results[i].isFinal) {
	        final_transcript += event.results[i][0].transcript;
	      } else {
	        interim_transcript += event.results[i][0].transcript;
	      }
	    }
	    final_transcript = capitalize(final_transcript);
	    searchbox.value=linebreak(final_transcript);
	    
	    /*final_span.innerHTML = linebreak(final_transcript);
	    interim_span.innerHTML = linebreak(interim_transcript);*/
	  };
	  var first_char = /\S/;
	    function capitalize(s) {
	      console.log(s);
	      return s.replace(first_char, function(m) { return m.toUpperCase(); });
	    }
	var two_line = /\n\n/g;
	var one_line = /\n/g;
	function linebreak(s) {
	  return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
	}
 
} 
 
/********************************************************************/
/********************************************************************/
/********************************************************************/


$(window).resize(function() {
  sizeLayerControl();
});

$(document).on("click", ".feature-row", function(e) {
  $(document).off("mouseout", ".feature-row", clearHighlight);
  sidebarClick(parseInt($(this).attr("id"), 10));
});

if ( !("ontouchstart" in window) ) {
  $(document).on("mouseover", ".feature-row", function(e) {
    highlight.clearLayers().addLayer(L.circleMarker([$(this).attr("lat"), $(this).attr("lng")], highlightStyle));
  });
}

$(document).on("mouseout", ".feature-row", clearHighlight);

$("#about-btn").click(function() {
  $("#aboutModal").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$("#nav-btn").click(function() {
  $(".navbar-collapse").collapse("toggle");
  return false;
});

$("#sidebar-toggle-btn").click(function() {
  $("#sidebar").toggle();
  map.invalidateSize();
  return false;
});

$("#sidebar-hide-btn").click(function() {
  $('#sidebar').hide();
  map.invalidateSize();
});

function sizeLayerControl() {
  $(".leaflet-control-layers").css("max-height", $("#map").height() - 50);
}

function clearHighlight() {
  highlight.clearLayers();
}


/* Contribution Carte*/
var mapquestOSM = L.tileLayer("https://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {
  maxZoom: 19,
  subdomains: ["otile1-s", "otile2-s", "otile3-s", "otile4-s"],
  attribution: 'Carte extraite de <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a>.'
});

/* POIs et tÃ©lÃ©chargement des POIs */
var highlight = L.geoJson(null);
var highlightStyle = {
  stroke: false,
  fillColor: "#00FFFF",
  fillOpacity: 0.7,
  radius: 10
};

var boroughs = L.geoJson(null, {
  style: function (feature) {
    return {
      color: "black",
      fill: false,
      opacity: 1,
      clickable: false
    };
  },
  onEachFeature: function (feature, layer) {
    boroughSearch.push({
      name: layer.feature.properties.BoroName,
      source: "Boroughs",
      id: L.stamp(layer),
      bounds: layer.getBounds()
    });
  }
});
$.getJSON("data/boroughs.geojson", function (data) {
  boroughs.addData(data);
});

/* POIs et tÃ©lÃ©chargement des POIs */
var markerClusters = new L.MarkerClusterGroup({
  spiderfyOnMaxZoom: true,
  showCoverageOnHover: false,
  zoomToBoundsOnClick: true,
  disableClusteringAtZoom: 16
});

/*$.getJSON("data/DOITT_MUSEUM_01_13SEPT2010.geojson", function (data) {
  museums.addData(data);
});*/

map = L.map("map", {
  zoom: 18,
  center: [48.85813641430712, 2.2946834564208984],
  layers: [mapquestOSM, boroughs, markerClusters, highlight],
  zoomControl: false,
  attributionControl: false
});

//geolocalisation 
/*************************************************************/
/*************************************************************/
/*************************************************************/
function onLocationFound(e)
{
	var radius = e.accuracy / 2;
	
	L.marker(e.latlng).addTo(map)
		.bindPopup("Vous êtes à " + radius + " mètres de ce point !").openPopup();
	
	L.circle(e.latlng, radius).addTo(map);
}

function onLocationError(e) {
	alert(e.message);
}

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

map.locate({setView: true, maxZoom: 16});

/* Contribution*/
function updateAttribution(e) {
  $.each(map._layers, function(index, layer) {
    if (layer.getAttribution) {
      $("#attribution").html((layer.getAttribution()));
    }
  });
}
map.on("layeradd", updateAttribution);
map.on("layerremove", updateAttribution);

var attributionControl = L.control({
  position: "bottomright"
});
attributionControl.onAdd = function (map) {
  var div = L.DomUtil.create("div", "leaflet-control-attribution");
  div.innerHTML = "<span class='hidden-xs'>DÃ©veloppÃ© par <a href='https://github.com/Skyline7117/FIREFOX-GPS'>NicEvry</a> | </span><a href='#' onclick='$(\"#attributionModal\").modal(\"show\"); return false;'>Attribution</a>";
  return div;
};
map.addControl(attributionControl);

var zoomControl = L.control.zoom({
  position: "bottomright"
}).addTo(map);

/* activation geolocation GPS et localisation de l'utilisateur */
var locateControl = L.control.locate({
  position: "bottomright",
  drawCircle: true,
  follow: true,
  setView: true,
  keepCurrentZoomLevel: true,
  markerStyle: {
    weight: 1,
    opacity: 0.8,
    fillOpacity: 0.8
  },
  circleStyle: {
    weight: 1,
    clickable: false
  },
  icon: "fa fa-location-arrow",
  metric: false,
  strings: {
    title: "Ma location",
    popup: "vous Ãªtes Ã  {distance} {unit} depuis ce point",
    outsideMapBoundsMsg: "Vous Ãªtes en dehors de la carte"
  },
  locateOptions: {
    maxZoom: 18,
    watch: true,
    enableHighAccuracy: true,
    maximumAge: 10000,
    timeout: 10000
  }
}).addTo(map);

var wayPoints=[];

map.on('startfollowing',getGeolocation);

map.on('locationfound',getGeolocation);

// routing
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: 'Â© OpenStreetMap contributors'
}).addTo(map);




var routingControl=L.Routing.control({
    routeWhileDragging: true,
    autoRoute : true,
    language:'fr',
    
    
}).addTo(map);

/*Pop-up*/
var popup = L.popup();

/*************************************************************/
/*************************************************************/
/*************************************************************/
/* utilisation Leaflet Control Geocoder avec utilisation des icons */
var geocoder = L.Control.Geocoder.nominatim(),
  myIcon = L.icon({
    iconUrl: './assets/img/favicon.ico',
    //shadowUrl: 'leaf-shadow.png',

    iconSize:     [32, 32], // size of the icon
    //shadowSize:   [50, 64], // size of the shadow
    iconAnchor:   [16, 30], // point of the icon which will correspond to marker's location
    //shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
  }),
  marker;

function reverseGeocoding(lat,lon)
{
    var location = new L.LatLng(lat, lon);
    
    var adresse;

    geocoder.reverse(location, map.options.crs.scale(map.getZoom()), function(results) {
         var r = results[0];

         if (r) {

               adresse=(r.html || r.name);
                console.log(adresse);


         }


       });
       console.log('adresse est :' +adresse);

    
}

function onMapClick(e) {
    geocoder.reverse(e.latlng, map.options.crs.scale(map.getZoom()), function(results) {
      var r = results[0];
      
      if (r) {
        if (marker) {
            
          marker.
            setLatLng(r.center).
            setPopupContent(r.html || r.name).
            openPopup();
        } else {
         
          marker = L.marker(r.center,{icon:myIcon}).bindPopup(r.name).addTo(map).openPopup();
        }
      }
    });

    /*popup
        .setLatLng(e.latlng)
        .setContent("Adresse : " + e.latlng.toString())
        .openOn(map);*/
}

map.on('click', onMapClick);

/*************************************************************/
/*************************************************************/
/*************************************************************/
/*get la geolocalisation de l'utilisateur, 
le control.locate emet les evenements "startfollowing" */

function getGeolocation(e)
{
    console.log(e.latlng);
    geolocation=e.latlng;
    var myLocation=new L.LatLng(locationLat,locationLng);
    console.log(myLocation);
    if(myLocation)
    {
        wayPoints=[];
        wayPoints.push(geolocation);
        wayPoints.push(myLocation);
        routingControl.setWaypoints(wayPoints);
        console.log(myLocation);
        routingControl.route();   
        
    }
   
}


//recherche adresse
/*************************************************************/
/*************************************************************/
/*************************************************************/
//Module recherche
function search_adresse(adresse) 
{
	
	var masquer = document.getElementById('results'); 
	var demasquer = document.getElementById('suggestions');  
	
	$.getJSON('http://nominatim.openstreetmap.org/search?format=json&limit=5&q=' + adresse, function(data) 
	{
		masquer.style.display="";
		demasquer.style.display="";
		
		var items = [];

		$.each(data, function(key, val)
		{
		  items.push
		  (
			'<ul><li><a href="#" onclick="chooseAdresse(' +
			val.lat + ', ' + val.lon +' ,\' '+val.display_name+ '\' );return false;">' + val.display_name +
			'</a></li></ul>'
		  );
		  
		});
		  
		$('#results').empty();
		if (items.length != 0) 
		{
		  //$('<p>', { html: "Search results :" }).appendTo('#results');
		  $('<ul/>', {
			'class': 'my-new-list',
			html: items.join('')
		  }).appendTo('#results');
		} 
		else 
		{
		  $('<li>', { html: "No results found" }).appendTo('#results');
		}
	});
  
}

// choix de l'adresse
/**********************************************************/
/**********************************************************/
function chooseAdresse(lat, lng, type) {

 var location = new L.LatLng(lat, lng);
 locationLat=lat;locationLng=lng;
 
 var masquer = document.getElementById('results');  
 var demasquer = document.getElementById('suggestions'); 
 var rech = document.getElementById('searchbox'); 

 masquer.style.display="none";
 demasquer.style.display="none";
 rech.value=type;
 
 //map.panTo(location);
  
   
  
  /*if (type == 'city' || type == 'administrative') {
    map.setZoom(11);
  } else {
    map.setZoom(13);
  }*/
    
    //marker avec popup contenat l'adresse 
      geocoder.reverse(location, map.options.crs.scale(map.getZoom()), function(results) {
      var r = results[0];
      
      if (r) {
      
            adresse=(r.html || r.name);
            var markerRecherche;
             markerRecherche=L.marker(location,{icon:myIcon}).bindPopup(adresse).addTo(map).openPopup();
            if(geolocation)
            {
               // document.getElementById("results").hide;
                wayPoints=[];
                wayPoints.push(geolocation);
                wayPoints.push(location);
                
                routingControl.setWaypoints(wayPoints);
                console.log(routingControl.getWaypoints());
                routingControl.route();
            }
            else
            {
                alert('Pour vous indiquer le chemin vers ce lieu vous devez d abord vous geolocaliser');
            }
      
      }
      
    });
        
}

/*************************************************************/
/*************************************************************/
/*************************************************************/

/*Routage
var map = L.map('map');

L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: 'Â© OpenStreetMap contributors'
}).addTo(map);

L.Routing.control({
    waypoints: [
        L.latLng(57.74, 11.94),
        L.latLng(57.6792, 11.949)
    ],
    routeWhileDragging: true
}).addTo(map);*/
