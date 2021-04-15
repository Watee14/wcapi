var map;
var marker;
var areaNumber = 0;
var zoomArea = [];
var base_url;
var destLL = [] ;
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
        if(stage=='view'){

        }else{
            clearMarker();
            var lonLat = map.location(longdo.LocationMode.Pointer);
            addMarker(lonLat['lon'], lonLat['lat'], {"type":"case"}, 'flag.png' , "destinate");
            destLL  =  lonLat ;
            //markCircle(lonLat['lon'], lonLat['lat']);
            // getNearBy(lonLat);
            getAddress(lonLat);
        }
		
	});
	map.Event.bind('overlayClick', function(overlay) {
		// console.log("AreaID: " + overlay.title);
		// setPolice(overlay.title);

		/*var clickedPolygon = polygonList.filter(p => { return longdo.Util.contains(map.location(longdo.LocationMode.Pointer), p.location());});
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
*/




		/*
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
		var cc = $('#casetype_code').val();*/
		// getDuplicateCase(lonLat, '001');
	});
	map.Event.bind('doubleClick', function(overlay) {
		if (typeof getPolyLines === 'function') {
			//getPolyLines();
		}
	});
	resetSearch();
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


function resetSearch(){

    var datasetx = new Array();
    $("input:checkbox[name=dataset]:checked").each(function(){
       datasetx.push($(this).val());
    });

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
        $("#q").attr("value","");
        
        $("#q").autocomplete({
            source: base_url + "mapAPI/searchSuggest?dataset="+datasetx.join(',') ,
            minLength: 2,
            autoFocus:false ,
            select: function(event, ui) {
                $('.map-search-list').hide();
                $('#q').val(ui.item.name);
                console.log(ui.item);
                if (typeof ui.item.id  != 'undefined'){
                    if(ui.item.poi_line!='' && ui.item.poi_line!= null ){
                        clearMarker();
                        clearPOILINE();
                        console.log('LINE');
                        var obj = $.parseJSON(ui.item.poi_line);
                        console.log(obj[0][ obj[0].length-1 ] );


                        $.each(obj,function(k,v){
                            addMarker(v[0]['lon'], v[0]['lat'] , {"type":'mark_line'}, ui.item.icon);
                            addMarker(v[ v.length-1 ]['lon'] , v[ v.length-1 ]['lat'] , {"type":'mark_line'}, ui.item.icon ,  "destinate");
                        });



                        map.location({
                            lon: obj[0][0]['lon'] ,
                            lat: obj[0][0]['lat'] ,
                           }, true);
                        map.zoom(13);

                        setPolyLine_x(ui.item.poi_line , 'mark_line');
                    }else{
                        console.log('MARK');
                        //var ds = zoomArea[this.value];
                        map.location({
                            lon: ui.item.lon,
                            lat: ui.item.lat,
                        }, true);
                        map.zoom(17);
                        clearMarker();
                        //clearPOILINE();
                        addMarker(ui.item.lon, ui.item.lat, {"type":ui.item.type}, ui.item.icon ,  "destinate" );
                        // addMarker(ui.item.lon, ui.item.lat, "", 'marker.png');
                        //markCircle(ui.item.lon, ui.item.lat);
                        var laton_x = [];
                        laton_x['lat'] = ui.item.lat;
                        laton_x['lon'] = ui.item.lon;
                        getNearBy(laton_x);
                        if (typeof getAddress === 'function') {
                            getAddress(laton_x);
                        }
                        resetSearch()
                        
                    }

                    window.opener.return2elements('case_location_detail',   ui.item.name );
                }else{
                    searchQ();
                }

            },
        }).autocomplete("instance")._renderItem = function(ul, item) {
            // return $('<li class="map-search-list">').append('<div class="map-search-tab"><img src="' + base_url + 'assets//icon/' + item.icon + '" class="map-search-icon"><p class="map-search-name">' + item.name + '<p><p class="map-search-address">' + item.address + "</p></div>").appendTo(ul);
            
            if (typeof item.id != 'undefined'){
                //console.log('<div class="media-body"><h5 class="media-heading"><b>' + item.name + '</b></h5></div>');
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
                //console.log('<div class="media-body"><h5 class="media-heading"><b>' + item.name + '</b></h5></div>');
                /*if(item.value!=0 && item.value!= 1){
                    console.log(item.name+'-'+item.value);
                }*/
                console.log(item.name+'-'+item.value);
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

        var htSel = '';
        $.each(obj, function(key, value) {
            var st = value.name+'|'+value.lon+'|'+value.lat+'|'+value.icon+'|'+value.poi_line+'|'+value.type ;
             htSel += '<li class="media longdo-mirror ui-menu-item" onclick="markLo(\''+st+'\')">'   +
                    '<div class="media-left ui-menu-item-wrapper" id="ui-id-12" tabindex="-1">' +
                        '<img class="media-object"' + (value.icon != '' && value.icon != 'undefined' ? 'src="' + base_url + 'assets/icon/' + value.icon : '') + '" width="36">' +
                    '</div>' +
                    '<div class="media-body ui-menu-item-wrapper" id="ui-id-13" tabindex="-1">' +
                        '<h5 class="media-heading"><b>' + value.name + '</b></h5>' +
                        '<p>' + value.address + '</p>' +
                    '</div></li>' ;

        });
        /*<li class="media longdo-mirror ui-menu-item"><div class="media-left ui-menu-item-wrapper" id="ui-id-12" tabindex="-1"><img class="media-object" src="http://10.0.106.50/portal191b/assets/icon/temple.png" width="36"></div><div class="media-body ui-menu-item-wrapper" id="ui-id-13" tabindex="-1"><h5 class="media-heading"><b>วัดหัวลำโพง</b></h5><p>แขวงสี่พระยา เขตบางรัก กรุงเทพมหานคร 10500</p></div></li>*/
        if(htSel!=''){
            $('#ui-id-1').html(htSel);
            $('#ui-id-1').show();
        }
        

        //$('#q').autocomplete("option", { source: obj });
         

        //$('#q').val( qx );
        //$('#q').autocomplete("search");
        resetSearch();
        $("#q").removeAttr("disabled");
        $('#q').focus(); 
        /*$('#ui-id-1').html('cccccc');
        $('#ui-id-1').show();*/
    });
}
function markLo(data){
    //var st = value.name+'|'+value.lon+'|'+value.lat+'|'+value.icon+'|'+value.poi_line ;
    data_t = data.split('|');
    //console.log(data_t);
    var data_y = [];
    data_y['name'] = data_t[0] ;
    data_y['lon'] = data_t[1] ;
    data_y['lat'] = data_t[2] ;
    data_y['icon'] = data_t[3] ;
    data_y['poi_line'] = data_t[4] ;
    data_y['type'] = data_t[5] ;
    console.log(data_y);
    console.log(data);

    //window.opener.return2elements('case_location_detail',   data_y.name );
    if(data_y.poi_line!='' && data_y.poi_line!= null && data_y.poi_line!="undefined"){
        //clearMarker();
        //clearPOILINE();
        console.log('LINE');
        var obj = $.parseJSON(data_y.poi_line);
        //console.log(obj[0][ obj[0].length-1 ] );


        $.each(obj,function(k,v){
            addMarker(v[0]['lon'], v[0]['lat'] , {"type":'mark_line'}, data_y.icon);
            addMarker(v[ v.length-1 ]['lon'] , v[ v.length-1 ]['lat'] , {"type":'mark_line'}, data_y.icon);
        });



        map.location({
            lon: obj[0][0]['lon'] ,
            lat: obj[0][0]['lat'] ,
           }, true);
        map.zoom(13);

        setPolyLine_x(data_y.poi_line , 'mark_line');
    }else{
        console.log('MARK');
        //var ds = zoomArea[this.value];
        map.location({
            lon: data_y.lon,
            lat: data_y.lat,
        }, true);
        map.zoom(17);
        //clearMarker();
        //clearPOILINE();
        addMarker(data_y.lon, data_y.lat, {"type":data_y.type}, data_y.icon);
        getAddress(data_y);
        // addMarker(ui.item.lon, ui.item.lat, "", 'marker.png');
        //markCircle(data_y.lon, data_y.lat);
        //var laton_x = [];
        //laton_x['lat'] = data_y.lat;
        //laton_x['lon'] = data_y.lon;
        //getNearBy(laton_x);
        /*if (typeof getAddress === 'function') {
            getAddress(laton_x);
        }*/
        
    }


}
 