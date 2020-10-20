<div class="form-group">
	<label for="farmNmae" class="col-md-12">Farm Name</label>
    {!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Enter name of the farm']) !!}
</div>
<div class="form-group">
	<label for="farmName" class="col-md-12">Delivery Time</label>
    {!! Form::input('number','delivery_time',NULL,['id'=>'del-time-input','class'=>'form-control','placeholder'=>'Enter farm Delivery Time here']) !!}
</div>
<div class="form-group">
	<label for="farmName" class="col-md-12">Packer Farm Name</label>
    {!! Form::text('packer',NULL,['id'=>'del-time-input','class'=>'form-control','placeholder'=>'Enter packer farm name here']) !!}
</div>
<div class="form-group">
	<label for="farmName" class="col-md-12">Contact Number</label>
	{!! Form::text('contact',NULL,['class'=>'form-control','placeholder'=>'Enter contact number']) !!}
</div>

<div class="form-group bins-box-form-group">
     <label for="input_farm_types" class="col-md-12">Farm Type</label>
     <!--<select name="farm_type" class="form-control">
     	<option value="none">None</option>
     	<option value="farrowing">Farrowing</option>
     	<option value="nursery">Nursery</option>
     	<option value="finisher">Finisher</option>
    </select>-->
    {!! Form::select("farm_type", $farmTypes, $selectedFarmType, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	<label for="farmName" class="col-md-12">Farm Owner</label>
	{!! Form::select("owner", $farmOwner, $selectedFarmOwner, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	<label for="farmName" class="col-md-12">Manual Update Notification</label>
	{!! Form::select("update_notification", $update_notification, $update_notification_selected, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	<label for="farmName" class="col-md-12">Notes</label>
	{!! Form::textarea('notes',NULL,['class'=>'form-control','rows'=>'3','placeholder'=>'Enter notes for the farm']) !!}
</div>

    {!! Form::hidden('lattitude', null, ["class" => "inputLat"]) !!}
    {!! Form::hidden('longtitude', null, ["class" => "inputLng"]) !!}
    {!! Form::hidden('loc', null, ["id" => "inputLoc"]) !!}

<div class="form-group">
	<label for="map" class="col-md-12">Address</label>
	{!! Form::text('address',NULL,['id'=>'pac-input','class'=>'controls','placeholder'=>'Enter farm Address here']) !!}
	<span class="address_holder"></span>
	<div id="map" style="width: 100%; height: 400px; background-color: #CCC;"></div>
</div>

<!-- Body Form Input-->
<!--<div class="form-group">
    {!! Form::label('tag_list', 'Tags: ') !!}
    {!! Form::select('tag_list[]', $tags, null, ['id' => 'tag_list','class' => 'form-control','multiple']) !!}
</div>-->

<!-- Add Article Form Input-->

<div class="form-group">
    {!! Form::button($submitButtonText, ['class' => 'btn btn-primary form-control add-farm','id'=>'add-farm']) !!}
</div>


<script type="text/javascript">
//farm form field
$(".inputLat").val();
$(".inputLng").val();

var current_address = $("#pac-input").val();

$(".address_holder").attr("current-address",current_address);

// Google map
var map;
var markers = [];

// Blade template if condition
@if(empty($farms->lattitude) && empty($farms->longtitude))
	var defaultLoc = {lat: 37.74, lng: -122.40}
@else
	var defaultLoc = {lat: {{$farms->lattitude}}, lng: {{$farms->longtitude}} }
@endif

function initialize() {
  map = new google.maps.Map(document.getElementById('map'), {
	center: defaultLoc,
	// Blade template if condition
	@if(empty($farms->lattitude) && empty($farms->longtitude))
		zoom: 6,
	@else
		zoom: 16,
	@endif

	mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  //// Create the search box and link it to the UI element.
  var input = document.getElementById('pac-input');
  var searchBox = new google.maps.places.SearchBox(input);
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

  // Bias the SearchBox results towards current map's viewport.
  map.addListener('bounds_changed', function() {
	searchBox.setBounds(map.getBounds());
  });

  // [START region_getplaces]
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
		$(".inputLat").val(place.geometry.location.lat())
		$(".inputLng").val(place.geometry.location.lng())

	  }
	  $(".inputLat").val(place.geometry.location.lat())
		$(".inputLng").val(place.geometry.location.lng())

	});
	map.fitBounds(bounds);
  });
  // [END region_getplaces]

  map.addListener('click', function(event) {
	  deleteMarkers();

	  $(".inputLat").val(event.latLng.lat());
	  $(".inputLng").val(event.latLng.lng());
	  placeMarker(event.latLng);
  });

  // Blade template if condition
  @unless(empty($farms->lattitude) && empty($farms->longtitude))
  	placeMarker(defaultLoc);
  @endunless
	var input = document.getElementById('pac-input');
	autocomplete = new google.maps.places.Autocomplete(input);
	google.maps.event.trigger(input, 'focus')
	google.maps.event.trigger(input, 'keydown', { keyCode: 13 });
	google.maps.event.trigger(autocomplete, 'places_changed');

	document.getElementById("add-farm").onclick = function(){
		var e = $.Event( "keypress", { which: 13 } );
		if($(".inputLat").val() == ""){
			var input = document.getElementById('pac-input');
			autocomplete = new google.maps.places.Autocomplete(input);
		  //google.maps.event.trigger(input, 'focus')
			document.getElementById('pac-input').focus();
			google.maps.event.trigger(input, 'keydown', { keyCode: 13 });
			$(input).trigger(e);
			google.maps.event.trigger(autocomplete, 'places_changed');
		}

		if( $(".address_holder").attr("current-address") != "" && $("#pac-input").val() != $(".address_holder").attr("current-address")) {
			//console.log($(".address_holder").attr("current-address"));
			//console.log($("#pac-input").val());
			var input = document.getElementById('pac-input');
			autocomplete = new google.maps.places.Autocomplete(input);
		  //google.maps.event.trigger(input, 'focus')
			document.getElementById('pac-input').focus();
			google.maps.event.trigger(input, 'keydown', { keyCode: 13 });
			$(input).trigger(e);
			google.maps.event.trigger(autocomplete, 'places_changed');
		}

	$("#create_farm").hide("");
	$(".panel-body").append("Please wait...");

	setTimeout(function(){
		$("#create_farm").submit();
	},1000);

  }

 /* $(".add-farm").click(function(){
	var location = $("#pac-input").val();
	google.maps.event.trigger(location, 'keydown', {
        keyCode: 13
    });


})*/

}

function placeMarker(location) {
	var marker = new google.maps.Marker({
		position: location,
		map: map
	});
	markers.push(marker);
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
  for (var i = 0; i < markers.length; i++) {
	markers[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
  setMapOnAll(null);
}

// Shows any markers currently in the array.
function showMarkers() {
  setMapOnAll(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
  clearMarkers();
  markers = [];
}




</script>
@section('footer')

@endsection
