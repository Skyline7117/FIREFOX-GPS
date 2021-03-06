<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>FireGPS</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.css">
    <link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css">
    <link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css">
    <link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.43.0/L.Control.Locate.css">
    <link rel="stylesheet" href="assets/leaflet-groupedlayercontrol/leaflet.groupedlayercontrol.css">
    <link rel="stylesheet" href="assets/css/app.css">

	<link rel="stylesheet" href="assets/css/leaflet.compass.css" />
	
	<link rel="stylesheet" href="assets/js/leaflet-routing-machine-2.6.1/css/leaflet-routing-machine.css">
	
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/favicon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/img/favicon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/img/favicon-152.png">
    <link rel="icon" sizes="196x196" href="assets/img/favicon-196.png">
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
  
  </head>

  <body onload="chargement();">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <div class="navbar-icon-container">
            <a href="#" class="navbar-icon pull-right visible-xs" id="nav-btn"><i class="fa fa-bars fa-lg white"></i></a>
            <a href="#" class="navbar-icon pull-right visible-xs" id="sidebar-toggle-btn"><i class="fa fa-search fa-lg white"></i></a>
          </div>
		  
          <a class="navbar-brand" href="#">FireGPS</a><img style="width:35px;margin-top:10px;" src="assets/img/icone-carte-128.png" id="img"/>
        </div>
		
		
		
        <div class="navbar-collapse collapse">
		
			 
		  <div class="navbar-form navbar-right" role="search">
			<div id="suggestionList" class="form-group has-feedback">
				<a onclick="speechVoice();">
					<img style="width:30px;" src="assets/img/mic.gif" id="start_img"/>
				</a>
				<input id="searchbox" type="text" placeholder="Rechercher" onkeyup="search_adresse(this.value);" value="<?php if(isset($_GET['searchbox'])){echo $_GET['searchbox'];}?>" class="form-control">  
				<input id="lat" value="<?php if(isset($_GET["lat"])){echo $_GET["lat"];}?>" type="text" style="display:none;">  
				<input id="lng" value="<?php if(isset($_GET["lng"])){echo $_GET["lng"];}?>" type="text" style="display:none;"> 
				<span id="searchicon" class="fa fa-search form-control-feedback"></span>
				
				<div class="suggestionsBox" id="suggestions" style="display:none;">
					<div id="results" class="suggestionList" >
					</div>
				</div>
			</div>
		  </div>
			
		 	  
          <ul class="nav navbar-nav">
            <li><a href="#" data-toggle="collapse" data-target=".navbar-collapse.in" id="about-btn"><i class="fa fa-question-circle white"></i>&nbsp;&nbsp;A propos</a></li>
		
            <li class="dropdown">
                <a class="dropdown-toggle" id="downloadDrop" href="#" role="button" data-toggle="dropdown"><i class="fa fa-cloud-download white"></i>&nbsp;&nbsp;Télécharger <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="data/boroughs.geojson" download="boroughs.geojson" target="_blank" data-toggle="collapse" data-target=".navbar-collapse.in"><i class="fa fa-download"></i>&nbsp;&nbsp;Arrondissements</a></li>
                  <li><a href="data/subways.geojson" download="subways.geojson" target="_blank" data-toggle="collapse" data-target=".navbar-collapse.in"><i class="fa fa-download"></i>&nbsp;&nbsp;Métros</a></li>
                  <li><a href="data/DOITT_THEATER_01_13SEPT2010.geojson" download="theaters.geojson" target="_blank" data-toggle="collapse" data-target=".navbar-collapse.in"><i class="fa fa-download"></i>&nbsp;&nbsp;Théâtres</a></li>
                  <li><a href="data/DOITT_MUSEUM_01_13SEPT2010.geojson" download="museums.geojson" target="_blank" data-toggle="collapse" data-target=".navbar-collapse.in"><i class="fa fa-download"></i>&nbsp;&nbsp;Musées</a></li>
                </ul>
            </li>
          </ul>
        </div><!--/.navbar-collapse -->
		
      </div>
    </div>

	

    <div id="container">
	
        <div id="map"></div>
    </div>
    
    <div class="modal fade" id="aboutModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Bienvenu sur FireGPS</h4>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs nav-justified" id="aboutTabs">
              <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-question-circle"></i>&nbsp;A propos du projet</a></li>
              <li><a href="#contact" data-toggle="tab"><i class="fa fa-envelope"></i>&nbsp;Contactez-nous</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-globe"></i>&nbsp;Metadonnées <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#boroughs-tab" data-toggle="tab">Arrondissements</a></li>
                  <li><a href="#subway-lines-tab" data-toggle="tab">Métros</a></li>
                  <li><a href="#theaters-tab" data-toggle="tab">Théâtres</a></li>
                  <li><a href="#museums-tab" data-toggle="tab">Musées</a></li>
                </ul>
              </li>
            </ul>
            <div class="tab-content" id="aboutTabsContent">
              <div class="tab-pane fade active in" id="about">
                <p>FireGPS utilise <a href="http://getbootstrap.com/">Bootstrap 3</a>, <a href="http://leafletjs.com/" target="_blank">Leaflet</a>, et <a href="http://twitter.github.io/typeahead.js/" target="_blank">typeahead.js</a>. Il s'agit d'une application Open source, disponible sur <a href="https://github.com/Skyline7117/FIREFOX-GPS" target="_blank">GitHub</a>.</p>
                <div class="panel panel-primary">
                  <div class="panel-heading">Fonctionalités</div>
                  <ul class="list-group">
                    <li class="list-group-item">Application Responsive : Mode plein écran, mobile, tablet. Grâce à <a href="http://getbootstrap.com/">Bootstrap</a> </li>
                    <li class="list-group-item">Chargement pour les fichers GeoJSON externes. Grâce à <a href="http://Jquery.com/">Jquery</a></li>
                    <li class="list-group-item">Fonction recherche avec saisie semi-automatique. Grâce à <a href="http://twitter.github.io/typeahead.js/" target="_blank">typeahead.js</a></li>
                    <li class="list-group-item">Icon du marker. Grâce à <a href="https://github.com/ismyrnow/Leaflet.groupedlayercontrol">Leaflet</a></li>
                  </ul>
                </div>
              </div>

              <div class="tab-pane fade" id="contact">
                <form id="contact-form">
                  <div class="well well-sm">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="first-name">Nom:</label>
                          <input type="text" class="form-control" id="first-name">
                        </div>
                        <div class="form-group">
                          <label for="last-name">Prénom:</label>
                          <input type="text" class="form-control" id="last-email">
                        </div>
                        <div class="form-group">
                          <label for="email">Email:</label>
                          <input type="text" class="form-control" id="email">
                        </div>
                      </div>
                      <div class="col-md-8">
                        <label for="message">Message:</label>
                        <textarea class="form-control" rows="8" id="message"></textarea>
                      </div>
                      <div class="col-md-12">
                        <p>
                          <button type="submit" class="btn btn-primary pull-right" data-dismiss="modal">Envoyer</button>
                        </p>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
			  
              <div class="tab-pane fade" id="boroughs-tab">
                <p>Les arrondissements <a href="http://www.nyc.gov/html/dcp/pdf/bytes/nybbwi_metadata.pdf" target="_blank">de Paris</a></p>
              </div>
              <div class="tab-pane fade" id="subway-lines-tab">
                <p>Les métros<a href="http://spatialityblog.com/2010/07/08/mta-gis-data-update/#datalinks" target="_blank"> de Paris</a></p>
              </div>
              <div class="tab-pane fade" id="theaters-tab">
                <p>Les théâtres <a href="https://data.cityofnewyork.us/Recreation/Theaters/kdu2-865w" target="_blank"></a> de Paris</p>
              </div>
              <div class="tab-pane fade" id="museums-tab">
                <p>Les musées<a href="https://data.cityofnewyork.us/Recreation/Museums-and-Galleries/sat5-adpb" target="_blank"> de Paris</a></p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="attributionModal" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              Développé par <a href='https://github.com/Skyline7117/FIREFOX-GPS'>NicEvry</a>
            </h4>
          </div>
          <div class="modal-body">
            <div id="attribution"></div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

	 <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.5/typeahead.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.3/handlebars.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.js"></script>
    <script src="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js"></script>
    <script src="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.43.0/L.Control.Locate.min.js"></script>
	<script src="assets/leaflet-groupedlayercontrol/leaflet.groupedlayercontrol.js"></script>
    
	<script src="assets/js/Control.Geocoder.js"></script>
	
	<script src="assets/js/cordova-2.3.0.js"></script>
	<script src="assets/js/leaflet.compass.js"></script>
	
    <script src="assets/js/leaflet-routing-machine-2.6.1/dist/leaflet-routing-machine.js"></script>
    <script src="assets/js/leaflet-routing-machine-2.6.1/src/L.Routing.Localization.js"></script>
    
    <script src="assets/js/app.js"></script>
  </body>
</html>
