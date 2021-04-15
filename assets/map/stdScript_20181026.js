function renderArea() {
	// alert(base_url + "assets/map/data.txt");
	$.ajax({
		url: base_url + "assets/map/data.txt",
		data: {}
	}).done(function(data) {
		res = data;
		obj = $.parseJSON(res);
		var area = [];
		$.each(obj['data'], function(key, value) {
			area = '';
			var latlon = value['latlng'].split('|');
			var latlon_2 = [];
			$.each(latlon, function(k, v) {
				var ll = v.split(',');
				// var ll2 = { lon: parseFloat(ll[1]), lat: parseFloat(ll[0]) };
				var ll2 = { lon : parseFloat(ll[0]), lat: parseFloat(ll[1]) };
				latlon_2.push(ll2);
			});
			// console.log(latlon_2);
			var hex = hexToRgb(value['bg_color']);
			var geom2 = new longdo.Polygon(latlon_2, {
				title: value['area_code'] + "" ,
				// title: '' ,
				detail: '',
				label: value['name'],
				lineWidth: 0.7,
				lineColor: 'rgba(' + hex.r + ',' + hex.g + ',' + hex.b + ', 0.8)',
				fillColor: 'rgba(' + hex.r + ',' + hex.g + ',' + hex.b + ', 0.4)',
				// editable: true,
			});
			// console.log(geom2);
			map.Overlays.add(geom2);
			areaNumber++;
		});
	});
}

function searchLocation() {
	$.ajax({
		url: base_url + "mapApi/searchQ",
		data: { "q": $("#q").val() }
	}).done(function(data) {
		res = data;
		obj = $.parseJSON(res);
		var dx = [];
		$.each(obj['data'], function(key, value) {
			dx.push(value['name']);
			zoomArea[value['name']] = value;
		});
		// console.log('Ass');
		// console.log(dx);
		$("#q").autocomplete({
			source: dx
		});
	});
}

function addMarker(lon, lat, desc, icon) {
	// alert(base_url);
	// var marker1 = new longdo.Marker({ lon: lon, lat: lat });
	// console.log("DDDDD");
	// console.log(desc);
	// console.log("YYYY");
	marker = new longdo.Marker({
		lon: lon,
		lat: lat
	}, {
		title: desc['name'],
		icon: {
			url: base_url + '/assets/icon/' + icon,
			//html: '<img src="'+ base_url + '/assets/icon/' + icon+'" width="24">' ,
			//offset: { x: 12, y: 45 }
			// offset: { x: 12, y: 45 }
			size: { width: 200, height: 200 },
		},
		detail: desc['detail'],
		// visibleRange: { min: 7, max: 8 },
		// draggable: true,
		weight: longdo.OverlayWeight.Top
	});
	marker['type'] = desc['type'] ;
	map.Overlays.add(marker);
	map.location({
		lon: lon,
		lat: lat
	}, true);
	map.zoom(14);
}

function resize() {
	var style = document.getElementById('map').style
	style.height = (window.innerHeight - 68) + 'px';
	// style.width = (window.innerWidth - 8) + 'px';
}

function hexToRgb(hex) {
	// console.log(hex);
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? {
		r: parseInt(result[1], 16),
		g: parseInt(result[2], 16),
		b: parseInt(result[3], 16)
	} : null;
}

function markCircle(lon, lat) {
	var geom3 = new longdo.Circle({
		lon: lon,
		lat: lat
	}, 0.01, {
		title: '',
		detail: '',
		lineWidth: 3,
		lineColor: 'rgba(0, 255, 0, 1.0)',
		fillColor: false,
		// visibleRange: { min: 7, max: 8 },
		editable: true
	});
	map.Overlays.add(geom3);
}

function clearMarker() {
	var mark = map.Overlays.list();
	// console.log(mark) ;
	var i = 0;
	$.each(mark, function(key, value) {
		// console.log(value instanceof longdo.Marker);

		if(value instanceof longdo.Marker == true ){

			if(value.type != 'police_car'){
				map.Overlays.remove(value);
			}

		}

		if(value instanceof longdo.Circle == true){
			map.Overlays.remove(value);
		}
		i++;
	});
}

function getPolyLine() {
	var line = map.Overlays.list();
	var ds = [] ;
	var i = 0;
	$.each(line, function(key, value) {
	// console.log(value instanceof longdo.Marker);
		if (value instanceof longdo.Polyline == true) {
			var dx = value;
			ds[i] = dx.location();
			i++;
		}
	});
	var xx = JSON.stringify(ds);
	console.log(xx);
	return xx;
}

function setPolyLine(data) {
	obj2 = $.parseJSON(data);
	$.each(obj2, function(key, value) {
		var geom1 = new longdo.Polyline(value);
		map.Overlays.add(geom1);
	});
}