var map;
var marker;
var areaNumber = 0;
var zoomArea = [];
var base_url;
function init(zoom_depth = 12) {
	// กำหนดขนาดแผนที่
	base_url = base_url;
	resize();
	window.addEventListener('resize', resize);
	map = new longdo.Map({
		layer: [
			longdo.Layers.GRAY,
			// longdo.Layers.TRAFFIC
		],
		zoom: zoom_depth,
		placeholder: document.getElementById('map'),
		lastView: false
	});
	map.Event.bind('click', function(overlay) {
		clearMarker();
		var lonLat = map.location(longdo.LocationMode.Pointer);
		addMarker(lonLat['lon'], lonLat['lat'], {"type":"case"}, 'marker.png');
		markCircle(lonLat['lon'], lonLat['lat']);
		// getNearBy(lonLat);
	});
	map.Event.bind('overlayClick', function(overlay) {
		// console.log("AreaID: " + overlay.title);
		// setPolice(overlay.title);

		var clickedPolygon = polygonList.filter(p => { return longdo.Util.contains(map.location(longdo.LocationMode.Pointer), p.location());});
		// console.log(clickedPolygon);
		var areaLayer;
		$.each(clickedPolygon, function(key, value) {
			if(value instanceof longdo.Polygon == true    ){

				if(value.type == "inspection"){
					console.log(value.title);
					areaLayer = value.title.split('-');
				}
			}
		});





		
		try {
			window.opener.return2dropdown('police_station_code', overlay.title != '' ? overlay.title : '');
		}
		catch(err) {
		   // window.opener.return2dropdown('police_station_code', overlay.title != '' ? overlay.title : '');
		}
		// console.log("END: ");
		clearMarker();
		var lonLat = map.location(longdo.LocationMode.Pointer);
		addMarker(lonLat['lon'], lonLat['lat'], {"type":"case"} , 'marker.png');
		markCircle(lonLat['lon'], lonLat['lat']);
		// getNearBy(lonLat);
		if (typeof getAddress === 'function') {
			getAddress(lonLat);
		}
		if (typeof getPolices === 'function') {
			//getPolices(overlay.title != '' ? overlay.title : '')
			getPolices( areaLayer[0] , areaLayer[1]) ;
		}
		// getPoliceCar();
		var cc = $('#casetype_code').val();
		// getDuplicateCase(lonLat, '001');
	});
	map.Event.bind('doubleClick', function(overlay) {
		if (typeof getPolyLines === 'function') {
			getPolyLines();
		}
	});
}

function getAddressX(lonLat) {
	// alert(base_url + "/mapApi");
	$.ajax({
		url: base_url + "MapAPI/address",
		data: {
			"lat": lonLat['lat'],
			"lon": lonLat['lon']
		}
	}).done(function(data) {
		res = data;
		obj = $.parseJSON(res);
		// console.log(obj);
		var road = '';
		var province = '';
		var district = '';
		var subdistrict = '';
		var postcode = '';
		var address = '';
		if (obj['road'] != undefined)	{ road 			= obj['road']; 			}
		if (obj['province'])			{ province 		= obj['province']; 		}
		if (obj['district'])			{ district 		= obj['district']; 		}
		if (obj['subdistrict'])			{ subdistrict 	= obj['subdistrict']; 	}
		if (obj['postcode'])			{ postcode 		= obj['postcode']; 		}
		address = road + ' ' + subdistrict + ' ' + district + ' ' + province + ' ' + postcode;
		// console.log("--Address--" + address);
		// return address;
	});
}

function getNearBy(latlon) {
	$("#desc").html('');
	$.ajax({
		type : "POST" ,
		url: base_url + "mapApi/nearby",
		data: {
			"lat": latlon['lat'],
			"lon": latlon['lon']
		}
	}).done(function(data) {
		res = data;
		obj = $.parseJSON(res);
		// console.log("--POI--");
		// console.log(obj);
		// desc += "<br><--- NearBy----><br>";
		$.each(obj['data'], function(key, value) {
			if (value['type'] == 'nearby') {
				// desc += value['id'] + " : " + value['name'] + " <br> " + value['address'];
				// desc += "<br>---------------<br>";
				// value['detail'] = value['address'];
				// addMarker(value['lon'], value['lat'], value, 'pin.png');
				addMarker(value['lon'], value['lat'], value, value['icon']);
			}
		});
		$("#desc").html(desc);
	});
}

function getDuplicateCase(latlon, casetype_code) {
	return;
	console.log("CASE: " + casetype_code);
	if (casetype_code != undefined) {
		// console.log("CASE OK ");
	}
	else {
		return;
	}
	$.ajax({
		type: "POST" ,
		url: base_url + "mapApi/duplicateCase",
		data: {
			"lat": latlon['lat'],
			"lon": latlon['lon'],
			"casetype_code": casetype_code,
		},
	}).done(function(data) {
		res = data;
		obj = $.parseJSON(res);
		console.log("--POI--");
		console.log(obj);
		// desc += "<br><--- NearBy----><br>";
		var dsx = [];
		$.each(obj['data'], function(key, value) {
			console.log(value);
			var c = longdo.Util.distance([{lon: latlon['lon'], lat: latlon['lat']}, {lon: value['case_lon'], lat: value['case_lat']}]);
			console.log('DisT : '+ parseInt(c) +" :: "+ "850" );
			// if (value['type'] == 'poi') {
				// desc += value['id'] + ": " + value['name'] + "<br>" + value['address'];
				// desc += "<br>---------------<br>";
				// value['detail'] = value['address'];
				if (parseInt(c) < 850) {
					addMarker(value['case_lon'], value['case_lat'], {"type": "case_duplicate"}, 'pin.png');
					dsx.push(value);
				}
				// value['detail'] = 'xxxxx';
				// addMarker(value['lon'], value['lat'], value, value['icon']);
			// }
		});
		console.log(dsx);
		// $("#desc").html(desc);
	});
}


resetSearch();
function resetSearch(){
    $(document).ready(function() {
        // $('#q').on('autocompleteselect', function() {
        //  var desc = '';
        //  // console.log(zoomArea[this.value] );
        //  var ds = zoomArea[this.value];
        //  map.location({
        //      lon: ds['lon'],
        //      lat: ds['lat']
        //  }, true);
        //  map.zoom(17);
        //  // $.each(ds, function(key, value) {
        //  //  desc += key + ": " + value + '<br>';
        //  // });
        //  // $('#desc').html('You selected: <br>' + desc);
        //  // console.log(ds);
        //  clearMarker();
        //  addMarker(ds['lon'], ds['lat'], "", ds['icon']);
        //  addMarker(ds['lon'], ds['lat'], "", 'marker.png');
        //  markCircle(ds['lon'], ds['lat']);
        //  getNearBy(ds);
        //  if (typeof getAddress === 'function') {
        //      getAddress(ds);
        //  }
        // }).change();
         
        $("#q").autocomplete({
            source: base_url + "mapAPI/searchSuggest",
            minLength: 2,
            autoFocus:false ,
            select: function(event, ui) {
                $('.map-search-list').hide();
                $('#q').val(ui.item.name);
                if (typeof ui.item.id  != 'undefined'){
                    var ds = zoomArea[this.value];
                    map.location({
                        lon: ui.item.lon,
                        lat: ui.item.lat,
                    }, true);
                    map.zoom(17);
                    clearMarker();
                    addMarker(ui.item.lon, ui.item.lat, {"type":ui.item.type}, ui.item.icon);
                    // addMarker(ui.item.lon, ui.item.lat, "", 'marker.png');
                    // markCircle(ui.item.lon, ui.item.lat);
                    var laton_x = [];
                    laton_x['lat'] = ui.item.lat;
                    laton_x['lon'] = ui.item.lon;
                    getNearBy(laton_x);
                    if (typeof getAddress === 'function') {
                        getAddress(ds);
                    }
                }else{
                    searchQ();
                }
                
            },
        }).autocomplete("instance")._renderItem = function(ul, item) {
            // return $('<li class="map-search-list">').append('<div class="map-search-tab"><img src="' + base_url + 'assets//icon/' + item.icon + '" class="map-search-icon"><p class="map-search-name">' + item.name + '<p><p class="map-search-address">' + item.address + "</p></div>").appendTo(ul);
            if (typeof item.id != 'undefined'){
                return $('<li class="media longdo-mirror">').append(''  +
                    '<div class="media-left">' +
                        '<img class="media-object"' + (item.icon != '' && item.icon != 'undefined' ? 'src="' + base_url + 'assets/icon/' + item.icon : '') + '" width="36">' +
                    '</div>' +
                    '<div class="media-body">' +
                        '<h5 class="media-heading"><b>' + item.name + '</b></h5>' +
                        '<p>' + item.address + '</p>' +
                    '</div>' +
                '').appendTo(ul);
            }else{
                console.log('<div class="media-body"><h5 class="media-heading"><b>' + item.name + '</b></h5></div>');

                return $('<li class="media longdo-mirror">').append(''  +
                    '<div class="media-body">' +
                        '<h5 class="media-heading"><b>' + item.name + '</b></h5>' +
                    '</div>' +
                '').appendTo(ul);
            }
            
        };

    });

}
function searchQ() {
    // return;
    //console.log("CASE: " + casetype_code);
     $("#q").attr("disabled", "disabled"); 
    var qx = $('#q').val() ;
    $.ajax({
        type: "POST",
        url: base_url + "mapAPI/searchQ",
        data: { "q": qx },
    }).done(function(data) {
        res = data;
        obj = $.parseJSON(res); 

        $('#q').autocomplete("option", { source: obj });
        //$('#q').val( '' );

        $('#q').val( qx );
        $('#q').autocomplete("search");
        resetSearch();
        $("#q").removeAttr("disabled");  
        $('#q').focus()
    });
}
 