$(init);

var map;
var streets;
var baseMaps;
var overlayMaps;
var layerControl;
var satellite;
var osm;
var mbAttr;
var mbUrl;
var tiles;
var recherche;
var btnvalider;
var lat;
var lot;
var lat0;
var lot0;
var lat1;
var lot1;
var detaille;
var detaille1;
var coordonnees;
var coordonnees0;
var coordonnees1;
var popup
var depart;
var destination;
var btnvalider1;
var ite;
var latlngs;
var polyline;

function init(){
	mbAttr = 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>';
	mbUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

	osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    	maxZoom: 19,
    	attribution: '© OpenStreetMap'
	});
	streets = L.tileLayer(mbUrl, {id: 'mapbox/streets-v11', tileSize: 512, zoomOffset: -1, attribution: mbAttr});
	satellite = L.tileLayer(mbUrl, {id: 'mapbox/satellite-v9', tileSize: 512, zoomOffset: -1, attribution: mbAttr});


    map = L.map('map',{
		maxZoom: 19,
		layers: [osm]
	});
	map.locate({setView: true, maxZoom: 10});

	tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		layers: [osm],
		attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	}).addTo(map);


	baseMaps = {
		"Plan": osm,
		"Streets": streets,
		"Satellite" : satellite
	};
	
	layerControl = L.control.layers(baseMaps).addTo(map);

	recherche = $("#ville");
	btnvalider = $("#valider");

	depart = $("#depart");
	destination = $("#destination");
	btnvalider1 = $("#validere");


	btnvalider.click(afficher);
	$("#center").click(center);

	btnvalider1.unbind("click",itineraire);
	btnvalider1.bind("click",itineraire);
	
	map.on('locationfound', onLocationFound);
	map.on('locationerror', onLocationError);
}

function afficher(){
	
	$.ajax({
        type: "GET",
        url: "https://nominatim.openstreetmap.org/search?&q="+recherche.val()+"&limit=1&format=json",
        success: function(retour){
            console.log(retour);
            lat = retour.at(0).lat;
            lot = retour.at(0).lon;
			coordonnees = [lat,lot];
			detaille = retour.at(0).display_name;
            map.flyTo(coordonnees,13);
            L.marker(coordonnees).addTo(map)
            .bindPopup(detaille)
            .openPopup();
        }
    });
}

function center(){
	map.locate({setView: true, maxZoom: 10});
}

function itineraire(){
	$.ajax({
        type: "GET",
        url: "https://nominatim.openstreetmap.org/search?&q="+depart.val()+"&limit=1&format=json",
        success: function(retour){
            lat0 = parseFloat(retour.at(0).lat).toFixed(10);
            lot0 = parseFloat(retour.at(0).lon).toFixed(10);
			coordonnees0 = [lat0,lot0];
			detaille = retour.at(0).display_name;
            map.flyTo(coordonnees0,5);
            L.marker(coordonnees0).addTo(map);
			$.ajax({
				type: "GET",
				url: "https://nominatim.openstreetmap.org/search?&q="+destination.val()+"&limit=1&format=json",
				success: function(retour2){
					lat1 = parseFloat(retour2.at(0).lat).toFixed(10);
					lot1 = parseFloat(retour2.at(0).lon).toFixed(10);
					coordonnees1 = [lat1,lot1];
					detaille1 = retour2.at(0).display_name;
					map.flyTo(coordonnees1,5);
					L.marker(coordonnees1).addTo(map);
					$.ajax({
						type: "GET",
						url: "https://maps.open-street.com/api/route/?origin="+(lat0)+","+(lot0)+"&destination="+(lat1)+","+(lot1)+"&mode=driving&key=0fa914417d534cfe7fa01ac004ecb389",
						success: function(retour3){
							console.log(retour3);
							ite = retour3.polyline;
							polyline = new L.Polyline(L.PolylineUtil.decode(ite, 6), {color: 'red'})
							polyline.addTo(map);
							map.fitBounds(polyline.getBounds());
						}
					});
				}
			});
        }
    });
}

function onLocationFound(e) {
    var radius = e.accuracy;

    L.marker(e.latlng).addTo(map);

    L.circle(e.latlng, radius).addTo(map);
}

function onLocationError(e) {
    alert(e.message);
	map.setView([46.232192999999995, 2.209666999999996], 5);
}