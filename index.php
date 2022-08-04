<?php 
add_shortcode('PropertyMaps', 'propertyMapCall');

function propertyMapCall(){

    $args = array(
        'post_type' => 'property',
        'post_status' => 'publish',
        'posts_per_page' => -1, 
    );

    $loop = new WP_Query( $args ); 

    $data = array();
    $i = 0;

foreach( $loop->posts as $post){

   // $post = $loop->posts;   
    $post_id = $post->ID; 
    $post_title = $post->post_title;
    $post_link = get_post_permalink($post_id);
    $property_image = get_field('image', $post_id); 
    $price = get_field('price', $post_id);
    $latitude = get_field('latitude', $post_id);
    $longitude = get_field('longitude', $post_id);  
    $zip = get_field('zip', $post_id);  
 

    $data[$i]['property_id'] = $post_id; 
    $data[$i]['property_url'] = $post_link; 
    $data[$i]['property_address'] = $post_title; 
    $data[$i]['property_title'] = $post_title; 
    $data[$i]['price'] = $price; 
    $data[$i]['latitude'] = $latitude; 
    $data[$i]['longitude'] = $longitude;  
    $data[$i]['zip'] = $zip;  
 
    $i++;
 
}

//$property_data['propertys'] = $data;
$property_data = $data;
    wp_reset_query();
   
    $site_url = get_site_url();

?>
  
 
    <input id="pac-input" class="controls" type="text" placeholder="Search Your City">
    <div id="map"></div>

   <!--  -->
   <script>
    // This example adds a search box to a map, using the Google Place Autocomplete
    // feature. People can enter geographical searches. The search box will return a
    // pick list containing a mix of places and predicted search terms.

    // This example requires the Places library. Include the libraries=places
    // parameter when you first load the API. For example:
    // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
    
    
      var map;
      var plots = <?php echo json_encode( $property_data ); ?>;
      var siteDomain = 'http://floridahouses.tv'; <?php //echo $site_url; ?>//;
      var bounds;
      var markers = [];
      var infowindow;
 
    function initAutocomplete() {
 
        map = new google.maps.Map(document.getElementById('map'), {
              //center: {lat: 39.465885, lng: -98.745117},
              center: {lat: 27.664827, lng: -81.515755},
              zoom: 4,
              zoomControl:true,
              disableDefaultUI: true,
              scaleControl: true,
              mapTypeControl: true
          });
          
          bounds = new google.maps.LatLngBounds();
          
          infowindow = new google.maps.InfoWindow({
              content: ''
          });
          
        makeInitMarkers();		  
        
 
        
       /* var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: -33.8688, lng: 151.2195},
        zoom: 13,
        mapTypeId: 'roadmap'
      }); */

      // Create the search box and link it to the UI element.
      var input = document.getElementById('pac-input');
      var searchBox = new google.maps.places.SearchBox(input);
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      // Bias the SearchBox results towards current map's viewport.
      map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
      });

      var markers = [];
      // Listen for the event fired when the user selects a prediction and retrieve
      // more details for that place.
      searchBox.addListener('places_changed', function() {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
          return;
        }

        // Clear out the old markers.
        markers.forEach(function(marker) {
          marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
       
        places.forEach(function(place) {
          if (!place.geometry) {
            console.log("Returned place contains no geometry");
            return;
          }
          var icon = {
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(25, 25)
          };

          // Create a marker for each place.
          markers.push(new google.maps.Marker({
            map: map,
            icon: icon,
            title: place.name,
            position: place.geometry.location
          }));

          if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
          } else {
            bounds.extend(place.geometry.location);
          }
        });
        map.fitBounds(bounds);
      });
    }
    
      function makeInitMarkers() {
         // console.log(plots)
          for ( var key in plots ) {
              var lat = parseFloat( plots[key].latitude );
              var lng = parseFloat( plots[key].longitude );
              var foreclosure = 0; // parseFloat( plots[key].foreclosure);
                  
              if ( lat != 0 && lng != 0 ) {
                  var myLatLng = {lat: lat, lng: lng};
                  //var imgURL = 'http://floridahouses.tv/wp-content/uploads/2022/07/house-c.png';
                  var imgURL = 'http://floridahouses.tv/wp-content/uploads/2022/08/0f61ba72e0e12ba59d30a50295964871.png';
                  if(foreclosure == 0){
                     // imgURL = 'http://floridahouses.tv/wp-content/uploads/2022/07/house-f.png';
                      imgURL = 'http://floridahouses.tv/wp-content/uploads/2022/08/0f61ba72e0e12ba59d30a50295964871.png';
                  }
                  
                  var image = {
                      url: imgURL,
                      size: new google.maps.Size(30, 30),
                      origin: new google.maps.Point(0, 0),
                      anchor: new google.maps.Point(15, 0)
                  };

                  var marker = new google.maps.Marker({
                      position: myLatLng,
                      map: map,
                      icon: image,
                      markerData: plots[key]
                  });
                  
                  marker.addListener('click', function() {
                      var md = this.markerData; 
                      
                      var htmlText = '';
                        //console.log(plots)
                      var property_appraised_price = md.price;
                      var property_appraised_price = property_appraised_price.split('.')[0];
                      var foreclosure = md.foreclosure;
                      var property_appraised_price = property_appraised_price.replace(",","");
                      var property_appraised_price = property_appraised_price.replace(",","");
                      var property_appraised_price = property_appraised_price.replace(".00","");
                      
                      var property_sale_price = md.price;
                      var property_sale_price = property_sale_price.replace(",", "");
                      var property_sale_price = property_sale_price.replace(".00", "");
                      
                      var profit = property_appraised_price - property_sale_price;
                      //console.dir(property_appraised_price); console.dir(property_sale_price);
                      //console.dir(profit);
                      var profit_age = Math.round((profit/property_sale_price)*1000) / 10;						
                      
                      //console.dir(md);
                      htmlText += '<a  href="'+ md.property_url + '"><h4 class="left green" style="line-height:10px;font-size:22px;">';
                      htmlText += '' + md.property_address;
                      htmlText += '</h4></a>';
                      if(md.price){
                          var appraised_price = md.price;
                          var appraised_price = appraised_price.split('.')[0];
                          appraised_price = appraised_price.replace(/\.00/,'');
                          appraised_price = appraised_price.replace(/\D/g,'');
                          appraised_price = appraised_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                          htmlText += '<span class="property_url"  >Estimated Price: $'+ appraised_price +'</span>';
                      }
                      if(foreclosure == 1){
                          htmlText += '<br><span>Pre-Foreclosure</span>';
                      }
                      infowindow.setContent(htmlText);
                      infowindow.open(map, this);
                  });
                  
                  bounds.extend( marker.getPosition() );
                  markers.push(marker);
              }
          }
          
          //map.fitBounds(bounds);
      }
       
      

  </script> 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzVtPMMWBx30mmz0eg-n-w-y25ENCBd_4&libraries=places&callback=initAutocomplete" async defer></script>
   
<style>
 
 div#map {
    max-width: 1000px;
    margin: auto;
}
#map {
    height: 600px;
}
 

h4.left.green {
    line-height: 24px !important;
}

input#pac-input {
    left: 30% !important;
    height: 38px !important;
    top: 10px !important;
}
#pac-input {
    background-color: #fff !important;
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
    margin-left: 12px;
    padding: 0 11px 0 13px;
    text-overflow: ellipsis;
    width: 400px;
}
h4.left.green {
    color: #000;
}
.property_url  { 
    color: #000;
    font-size: 12px;
    font-weight: 700; 
}
/* Mobile Media  */
@media only screen and (max-width: 425px) {
 
 
.icon-list.column-1 ul {
    display: flex;
    flex-wrap: wrap;
}
.apartments-title h2 {
    margin-top: 60%;
}

.icon-list.column-3 ul {
    display: flex;
    flex-wrap: wrap;
}
 

}


</style>
  
 <?php
return;
}

?>
 
