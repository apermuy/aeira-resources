(function ($, Drupal) {
    Drupal.behaviors.aeiraresources = {
      attach: function (context) {

        $.ajax({
          type: 'GET',
          url: '/api/1.0/elementos',
          async: false,
          cache: true,
          success: function (data) {
              console.log('Carga ok API');
              buildMap(data);

          },
          error: function (xhr, ajaxOptions, thrownError) {
              console.log(xhr.status);
              console.log(thrownError);
          }
        });

      /**
       * Build popup of marker
       * @param {*} props
       */
      function buildPopup(props) {
        //console.log(props.markerurl);
        var content = '<div class="element-popup"><h1>It works</h1></div>';
        console.log('Carga popups');

        //content += '<h5><a href="'+ props.url+'">' + props.title + '</a></h5>';
        //content += '<div class="element-popup-content">' + props.concello + ' - ' + props.parroquia +
        //              '</div>';
        //content += '<div class="element-popup-body">' + props.classification  +
        //              '</div>';
        //content += '</div>';
        return content;
      }

      /**
       * Build map
       * @param {*} geoJsonData
       */
      function buildMap(geoJsonData){
        //Map declaration
        var frontpagemap = L.map('frontpagemap', {
          center: [42.8737, -8.6433],
          zoom: 15,
          fullscreenControl: true,
        });
        console.log(frontpagemap);
        //Geolocation
        $('#geolocate-position').on('click', function(){
          frontpagemap.locate({setView: true, maxZoom: 17});
        });

        //Default layer
        let default_layer = L.tileLayer('http://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', {
          id: 'frontpagemap',
          attribution: '© CartoDB - © Openstreetmap',
          maxZoom: 19,
          minZoom: 3,
        }).addTo(frontpagemap);

        //Cluster and layer control

        var markerCluster = L.markerClusterGroup({
          maxClusterRadius: 90,
        });
        var layerControl = L.control.layers(null, null,{ collapsed: false });
        var layer_group = '';

        geoJsonData.forEach(element => {
          //Foreach element (category) create group
          layer_group = L.featureGroup.subGroup(markerCluster);
          L.geoJSON(element, {
            onEachFeature: onEachFeatureMap,
          });

          if (element.features.length > 0){
            var text_layer = '<p class="frontpagemap-layer-control-text">' + element.features[0].properties.tipo + '</p>';
            layerControl.addOverlay(layer_group, text_layer);
            layer_group.addTo(frontpagemap);
          }
        });

        markerCluster.addTo(frontpagemap);
        layerControl.addTo(frontpagemap);
        console.log('Carga buildmap');

       /**
         * Set behaviour on each feature of layer
         * @param {*} feature
         * @param {*} layer
        */
       function onEachFeatureMap(feature, layer) {
        let markerUrl = feature.properties.marker_url;

        if (feature.geometry.type == 'Point'){
          let latlng = L.latLng(feature.geometry.coordinates[1], feature.geometry.coordinates[0]);
          var finalMarker = '';
          if (markerUrl != '') {
            var customMarker = L.icon({
              iconUrl: feature.properties.markerurl,
              iconSize:     [25, 40], // Tamaño da icona
              iconAnchor:   [14, 34], // point of the icon which will correspond to marker's location
              popupAnchor:  [0, -34] // point from which the popup should open relative to the iconAnchor
            });

            finalMarker = L.marker(latlng, {icon: customMarker});
          } else {
            finalMarker = L.marker(latlng);
          }
        } else if (feature.geometry.type == 'Polygon') {
          var pointList = [];
          feature.geometry.coordinates[0].forEach(element => {
            pointList.push([element[1], element[0]]);
          });
          finalMarker = new L.Polygon(pointList, {weight: mapSettings.line_string_width});
        } else if (feature.geometry.type == 'LineString') {
          var pointList = [];
          feature.geometry.coordinates.forEach(element => {
            pointList.push([element[1], element[0]]);
          });
          finalMarker = new L.Polyline(pointList, {weight: mapSettings.line_string_width});
        }
        else {
          return;
        }
        finalMarker.bindPopup(buildPopup(feature.properties));
        finalMarker.addTo(layer_group);
      }
    }
   }
  };
})(jQuery, Drupal);
