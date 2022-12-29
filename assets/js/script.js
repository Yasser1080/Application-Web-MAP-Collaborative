$(init);

var map;
var markers_ville = [];
var marker;
var elementClicked = false;

function init(){

  mbAttr = 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>';
  mbUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

  osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
  });
  streets = L.tileLayer(mbUrl, { id: 'mapbox/streets-v11', tileSize: 512, zoomOffset: -1, attribution: mbAttr });
  satellite = L.tileLayer(mbUrl, { id: 'mapbox/satellite-v9', tileSize: 512, zoomOffset: -1, attribution: mbAttr });

  // config map
  let config = {
    minZoom: 2,
    maxZoom: 18,
    layers: [osm],
  };
  // magnification with which the map will start
  const zoom = 5;
  // co-ordinates
  const lat = 46.232192999999995;
  const lng = 2.209666999999996;

  // calling map
  map = L.map("map", config).setView([lat, lng], zoom);

  L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
    layers: [osm],
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map);

  baseMaps = {
    "Plan": osm,
    "Streets": streets,
    "Satellite": satellite
  };

  layerControl = L.control.layers(baseMaps).addTo(map);

  // --------------------------------------------------

  // create custom button
  const customControl = L.Control.extend({
    // button position
    options: {
      position: "topleft",
      className: "locate-button leaflet-bar",
      html: '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0 0 13 3.06V1h-2v2.06A8.994 8.994 0 0 0 3.06 11H1v2h2.06A8.994 8.994 0 0 0 11 20.94V23h2v-2.06A8.994 8.994 0 0 0 20.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>',
      style:
        "margin-top: 0; left: 0; display: flex; cursor: pointer; justify-content: center; font-size: 2rem;",
    },

    // method
    onAdd: function (map) {
      this._map = map;
      const button = $('<div>');
      button.click(function(e) {
        e.stopPropagation();
      });
    
      button.attr('title', 'locate');
      button.html(this.options.html);
      button.addClass(this.options.className);
      button.attr('style', this.options.style);
    
      button.click(this._clicked.bind(this));
    
      return button[0];
    },
    _clicked: function (e) {
      L.DomEvent.stopPropagation(e);

      // this.removeLocate();

      this._checkLocate();

      return;
    },
    _checkLocate: function () {
      return this._locateMap();
    },

    _locateMap: function () {
      const locateActive = $('.locate-button');
      const locate = locateActive.hasClass('locate-active');
      // add/remove class from locate button
      locateActive.toggleClass('locate-active', !locate);
    
      // remove class from button
      // and stop watching location
      if (locate) {
        this.removeLocate();
        this._map.stopLocate();
        return;
      }

      // location on found
      this._map.on("locationfound", this.onLocationFound, this);
      // locataion on error
      this._map.on("locationerror", this.onLocationError, this);

      // start locate
      this._map.locate({ setView: true, enableHighAccuracy: true });
    },
    onLocationFound: function (e) {
      // add circle
      this.addCircle(e).addTo(this.featureGroup()).addTo(map);

      // add marker
      this.addMarker(e).addTo(this.featureGroup()).addTo(map);

      // add legend
    },
    // on location error
    onLocationError: function (e) {
      this.addLegend("Location access denied.");
    },
    // feature group
    featureGroup: function () {
      return new L.FeatureGroup();
    },
    // add legend
    addLegend: function (text) {
      const checkIfDescriotnExist = $('.description');
    
      if (checkIfDescriotnExist.length) {
        checkIfDescriotnExist.text(text);
        return;
      }
    
      const legend = L.control({ position: "bottomleft" });
    
      legend.onAdd = function () {
        let div = $('<div>').addClass('description');
        div.click(function(e) {
          e.stopPropagation();
        });
        const textInfo = text;
        div.append(textInfo);
        return div[0];
      };
      legend.addTo(this._map);
    },
    addCircle: function ({ accuracy, latitude, longitude }) {
      return L.circle([latitude, longitude], accuracy / 2, {
        className: "circle-test",
        weight: 2,
        stroke: false,
        fillColor: "#136aec",
        fillOpacity: 0.15,
      });
    },
    addMarker: function ({ latitude, longitude }) {
      return L.marker([latitude, longitude], {
        icon: L.divIcon({
          className: "located-animation",
          iconSize: L.point(17, 17),
          popupAnchor: [0, -15],
        }),
      }).bindPopup("Votre position :)");
    },
    removeLocate: function () {
      this._map.eachLayer(function (layer) {
        if (layer instanceof L.Marker) {
          const { icon } = layer.options;
          if (icon?.options.className === "located-animation") {
            map.removeLayer(layer);
          }
        }
        if (layer instanceof L.Circle) {
          if (layer.options.className === "circle-test") {
            map.removeLayer(layer);
          }
        }
      });
    },
  });

  // adding new button to map controll
  map.addControl(new customControl());


  $("#parc").hide();
  $("#id").hide();   
  park = L.layerGroup();
  var markersCluster;
  var idparcc;

  $.ajax({
    type: "GET",
    url: "https://queue-times.com/fr/parks.json",
    success: function(retour) {
      markersCluster = new L.MarkerClusterGroup();
      for (var j = 0; j < retour.length; j++) {
        for (var i = 0; i < retour.at(j).parks.length; i++) {
          var latLng = new L.LatLng(retour.at(j).parks.at(i).latitude, retour.at(j).parks.at(i).longitude);
          marker = new L.Marker(latLng, {id: retour.at(j).parks.at(i).id, name:retour.at(j).parks.at(i).name}).addTo(park); 
          marker.on("click", function (){
            $("#h3avis").html("");
            idparcc = this.options.id;
            elementClicked = true;
            $("#emailll").show();
            $("#emaill").hide();
            $("#parc").val(this.options.name);
            $("#nomparc").html(this.options.name);
            $("#id").val(this.options.id);
            $("body").toggleClass("active-sidebar");
            showContent("email");
            addRemoveActiveItem($("li").get().at(3), "active-item");
            elementClicked = false;
            $("#afficheravis").on("click",function (){
              $.post("../php/affichercom.php",{id : idparcc},function(data){
                $("#h3avis").html(data);
              });
              return false;
            });
          });
          markersCluster.addLayer(marker);
        }
      }
      layerControl.addOverlay(markersCluster, "Parc d'attraction");
    }
  });

  const menuItems = $('.menu-item');
  const sidebar = $('.sidebar');
  const buttonClose = $('.close-button');

  menuItems.each((index, item) => {
    $(item).click((e) => {
      const target = e.target;

      if ($(target).hasClass('active-item') || !$('.active-sidebar').length) {
        $('body').toggleClass('active-sidebar');
      }

      // show content
      showContent(target.dataset.item);
      // add active class to menu item
      addRemoveActiveItem(target, 'active-item');
      aff();
    });
  });


  // close sidebar when click on close button
  buttonClose.click(() => {
    closeSidebar();
  });
  
  // remove active class from menu item and content
  function addRemoveActiveItem(target, className) {
    const element = $(`.${className}`);
    $(target).addClass(className);
    if (!element.length) return;
    element.removeClass(className);
  }

  // show specific content
  function showContent(dataContent) {
    const idItem = $(`#${dataContent}`);
    addRemoveActiveItem(idItem, 'active-content');
  }

  // --------------------------------------------------
  // close when click esc
  $(document).keydown(function (event) {
    if (event.key === 'Escape') {
      closeSidebar();
    }
  });

  // --------------------------------------------------
  // close sidebar

  function closeSidebar() {
    $('body').removeClass('active-sidebar');
    const element = $('.active-item');
    const activeContent = $('.active-content');
    if (!element.length) return;
    element.removeClass('active-item');
    activeContent.removeClass('active-content');
  }

  L.control
    .scale({
      imperial: false,
    })
    .addTo(map);

  // --------------------------------------------------------------
  // create seearch button

  // add "random" button
  const buttonTemplate = `<div class="leaflet-search"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M31.008 27.231l-7.58-6.447c-0.784-0.705-1.622-1.029-2.299-0.998 1.789-2.096 2.87-4.815 2.87-7.787 0-6.627-5.373-12-12-12s-12 5.373-12 12 5.373 12 12 12c2.972 0 5.691-1.081 7.787-2.87-0.031 0.677 0.293 1.515 0.998 2.299l6.447 7.58c1.104 1.226 2.907 1.33 4.007 0.23s0.997-2.903-0.23-4.007zM12 20c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z"></path></svg></div><div class="auto-search-wrapper max-height"><input type="text" id="marker" autocomplete="off"  aria-describedby="instruction" aria-label="Search ..." /><div id="instruction" class="hidden">When autocomplete results are available use up and down arrows to review and enter to select. Touch device users, explore by touch or with swipe gestures.</div></div>`;

  // create custom button
  const customControle = L.Control.extend({
    // button position
    options: {
      position: "topleft",
      className: "leaflet-autocomplete",
    },
  
    // method
    onAdd: function () {
      return this._initialLayout();
    },
  
    _initialLayout: function () {
      // create button
      const container = $('<div>').addClass(
        'leaflet-bar ' + this.options.className
      );
  
      L.DomEvent.disableClickPropagation(container[0]);
  
      container.html(buttonTemplate);
  
      return container[0];
    },
  });

  // adding new button to map controll
  map.addControl(new customControle());

  // --------------------------------------------------------------

  // input element
  const root = $('#marker');

  function addClassToParent() {
    var searchBtn = $('.leaflet-search');
    searchBtn.click(function(e) {
      // toggle class
      $(e.target).closest('.leaflet-autocomplete').toggleClass('active-autocomplete');
  
      // add placeholder
      root.attr('placeholder', 'Search ...');
  
      // focus on input
      root.focus();
  
      // click on clear button
      clickOnClearButton();
    });
  }

  // function click on clear button
  function clickOnClearButton() {
    $('.auto-clear').click();
  }

  addClassToParent();

  // function clear input
  map.on("click", function() {
    $('.leaflet-autocomplete').removeClass('active-autocomplete');
    clickOnClearButton();
  });

  new Autocomplete("marker", {
    delay: 1000,
    selectFirst: true,
    howManyCharacters: 2,
  
    onSearch: function ({ currentValue }) {
      const api = `https://nominatim.openstreetmap.org/search?format=geojson&limit=5&q=${encodeURI(
        currentValue
      )}`;
      return new Promise((resolve) => {
        $.ajax({
          url: api,
          type: 'GET',
          success: function(data) {
            resolve(data.features);
          },
          error: function(error) {
            console.error(error);
          }
        });
      });
    },

    onResults: function ({ currentValue, matches, template }) {
      const regex = new RegExp(currentValue, "i");
      // checking if we have results if we don't
      // take data from the noResults method
      return matches === 0
        ? template
        : matches
            .map(function(element) {
              return `
                <li role="option">
                  <p>${element.properties.display_name.replace(
                    regex,
                    (str) => `<b>${str}</b>`
                  )}</p>
                </li> `;
            })
            .join("");
    },

    onSubmit: function ({ object }) {
      const { display_name } = object.properties;
      const cord = object.geometry.coordinates;

      map.eachLayer(function (layer) {
        if (layer.options && layer.options.pane === "markerPane") {
          if (layer._icon.classList.contains("leaflet-marker-locate")) {
            map.removeLayer(layer);
          }
        }
      });
      
      // add marker
      const marker = L.marker([cord[1], cord[0]], {
        title: display_name,
      });

      // add marker to map
      marker.addTo(map).bindPopup(display_name);

      // set marker to coordinates
      map.setView([cord[1], cord[0]], 8);

      // add class to marker
      $(marker._icon).addClass("leaflet-marker-locate");
    },

    // the method presents no results
    noResults: function ({ currentValue, template }) {
      return template(`<li>No results found: "${currentValue}"</li>`);
    }
  });

  var btnvalider;

  btnvalider = $("#valider");
  btnvalider.unbind("click", itineraire);
  btnvalider.bind("click", itineraire);
};

function aff (){
  if($("#maiil").hasClass("active-item") && elementClicked == false){
    $("#emailll").hide();
    $("#emaill").show();
  }
}

function itineraire() {
  markers_ville.forEach(item => {
    map.removeLayer(item)
  });
  $.ajax({
    type: "GET",
    url: "https://nominatim.openstreetmap.org/search?&q=" + $("#depart").val() + "&limit=1&format=json",
    success: function(retour) {
      lat0 = parseFloat(retour.at(0).lat).toFixed(10);
      lot0 = parseFloat(retour.at(0).lon).toFixed(10);
      coordonnees0 = [lat0, lot0];
      detaille = retour.at(0).display_name;
      map.flyTo(coordonnees0, 5);
      var ite = L.marker(coordonnees0).addTo(map);
      markers_ville.push(ite);
      $.ajax({
        type: "GET",
        url: "https://nominatim.openstreetmap.org/search?&q=" + $("#destination").val() + "&limit=1&format=json",
        success: function(retour2) {
          lat1 = parseFloat(retour2.at(0).lat).toFixed(10);
          lot1 = parseFloat(retour2.at(0).lon).toFixed(10);
          coordonnees1 = [lat1, lot1];
          detaille1 = retour2.at(0).display_name;
          map.flyTo(coordonnees1, 5);
          var ite1 = L.marker(coordonnees1).addTo(map);
          markers_ville.push(ite1);
          var type;
          type = $(".fld-mode");
          for(var i = 0; i < 3; i++){
            if(type[i].checked){
              $.ajax({
                type: "GET",
                url: "https://maps.open-street.com/api/route/?origin=" + lat0 + "," + lot0 + "&destination=" + lat1 + "," + lot1 + "&mode=" + type[i].value + "&key=0fa914417d534cfe7fa01ac004ecb389",
                success: function(retour3) {
                  console.log(retour3);
                  ite = retour3.polyline;
                  polyline = new L.Polyline(L.PolylineUtil.decode(ite, 6), { color: 'red' })
                  var poly = polyline.addTo(map);
                  map.fitBounds(polyline.getBounds());
                  markers_ville.push(poly);
                  $("#km").html("Distance : " + parseFloat((retour3.total_distance) / 1000).toFixed(2) + " km(s)");
                  $("#temps").html("Durée : " + parseFloat((retour3.total_time) / 3600).toFixed(2)+ " heure(s)");
                }
              });
            }
           }
        }
      });
    }
  });
}