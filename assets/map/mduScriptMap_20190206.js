resetSearch();
/*function resetSearch(){
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
                console.log(ui);
                if (typeof ui.item.id  != 'undefined'){
                    //var ds = zoomArea[this.value];
                    //console.log(ui);

                    map.location({
                        lon: ui.item.lon,
                        lat: ui.item.lat,
                    }, true);
                    map.zoom(17);

                    //zoomx('cccc');
                    //clearMarker();
                    //addMarker(ui.item.lon, ui.item.lat, {"type":ui.item.type}, ui.item.icon);
                    // addMarker(ui.item.lon, ui.item.lat, "", 'marker.png');
                    // markCircle(ui.item.lon, ui.item.lat);
                    var laton_x = [];
                    laton_x['lat'] = ui.item.lat;
                    laton_x['lon'] = ui.item.lon;
                    //getNearBy(laton_x);
                    if (typeof getAddress === 'function') {
                        //getAddress(ds);
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

}*/
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
                            addMarker(v[ v.length-1 ]['lon'] , v[ v.length-1 ]['lat'] , {"type":'mark_line'}, ui.item.icon);
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
                        clearPOILINE();
                        addMarker(ui.item.lon, ui.item.lat, {"type":ui.item.type}, ui.item.icon);
                        // addMarker(ui.item.lon, ui.item.lat, "", 'marker.png');
                        markCircle(ui.item.lon, ui.item.lat);
                        var laton_x = [];
                        laton_x['lat'] = ui.item.lat;
                        laton_x['lon'] = ui.item.lon;
                        getNearBy(laton_x);
                        if (typeof getAddress === 'function') {
                            getAddress(laton_x);
                        }
                        
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

function clearPOILINE() {
    var mark = map.Overlays.list();
    // console.log(mark) ;
    var i = 0;
    $.each(mark, function(key, value) {
        //console.log(value instanceof longdo.Marker);

        if(value instanceof longdo.Polyline == true || value instanceof longdo.Marker == true ){

            if(value.type == 'mark_line'){
                map.Overlays.remove(value);
            }

        }


        i++;
    });
}
 
 function clearMarker() {
    var mark = map.Overlays.list();
    // console.log(mark) ;
    var i = 0;
    $.each(mark, function(key, value) {
        console.log(value instanceof longdo.Marker);

        if(value instanceof longdo.Marker == true && value.hd==0  ){
            
            if(value.type != 'police_car' && value.type != 'mark_line'){
                map.Overlays.remove(value);
            }
            
        }

        if(value instanceof longdo.Circle == true){
            map.Overlays.remove(value);
        }
        i++;
    });
}
