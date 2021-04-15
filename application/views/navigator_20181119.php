<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title></title>
    <style>

    </style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/style-metro.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/style.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/style-responsive.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/themes/light.css" id="style_color">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker-4.17.47.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/datatables/media/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/map/longdo/css/style.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/map/longdo/css/jquery-ui.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/select2/dist/css/select2.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/select2-bootstrap-theme/dist/select2-bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/toggle/bootstrap-toggle.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/style-custom.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/form-wizard.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/loading.css">



<link rel="stylesheet" href="<?php echo base_url();?>assets/css/style.css">
<link href="<?php echo base_url();?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

  <style type="text/css">
    .mt-0 { margin-top:     0   !important }
    .mr-0 { margin-right:   0   !important }
    .mb-0 { margin-bottom:  0   !important }
    .ml-0 { margin-left:    0   !important }
    .m-0  { margin:         0   !important }
    .pt-0 { padding-top:    0   !important }
    .pr-0 { padding-right:  0   !important }
    .pb-0 { padding-bottom: 0   !important }
    .pl-0 { padding-left:   0   !important }
    .p-0  { padding:        0   !important }
    .pl-2 { padding-left:   2px !important }
  </style>

</head>

<body onload="init();">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt-0 pr-0 pb-0 pl-2">
    <div style="left:50%;position:absolute">
      <div id="filters" style="height:auto;left:-50%;margin:5px;position:relative;width:auto;z-index:256">
        <div class="form-inline">
          <div class="form-group" style="margin-bottom:0!important">
            <label class="control-label" style="margin-top:0;margin-bottom:5px">
              <span class="glyphicon glyphicon-map-marker small"></span>
              <span class="small">ค้นหาสถานที่:</span>
            </label>
            <div class="input-group">
             <input type="text" id="q" class="form-control input-sm" placeholder="ใส่ชื่อสถานที่ หรือชื่อถนน"  style="font-size:12px!important;margin-top:0;width:200px">
              <a class="glyphicon glyphicon-remove form-control-feedback text-muted small" aria-hidden="true" role="button" onclick="$('#q').val(null);$('#q').focus();$('#q').removeAttr('disabled');   " style="pointer-events:auto;right:43px;top:0!important;z-index:1000" title="ล้างข้อมูล"></a>
              <a class="input-group-addon btn blue" role="button" onclick="searchQ()" style="border:0!important">
              <!-- <a class="input-group-addon btn blue" role="button" onclick="$('#q').autocomplete('search', $('#q').val() );" style="border:0!important"> -->
                <span class="glyphicon glyphicon-search small"></span>
              </a>
            </div>
            <div id="searchSelect"></div>
            <div class="checkbox-container non-margin -bottom" style="padding-bottom:0!important">
                  <div class="checkbox-inline">
                    <input type="checkbox" name="dataset" value="0" checked onclick="resetSearch()">
                    <label>ถนน/ซอย</label>
                  </div>
                  <div class="checkbox-inline">
                    <input type="checkbox" name="dataset" value="1" checked onclick="resetSearch()">
                    <label>สถานที่</label>
                  </div>
                </div>
          </div>
        </div>
      </div>
    </div>
    <?php if($case_id!=''){ ?>
    <div id="desc"><? echo $_SERVER['HTTP_HOST']; ?></div>
    <?php } ?>
    <div id="map" class="m-0"></div>
</div>



  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/jquery-1.12.4.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/map/longdo/js/jquery-3.1.1.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.ui.widget.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.validate.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/bootstrap-datetimepicker/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/bootstrap-datetimepicker/js/moment-with-locales.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/map/longdo/js/html2canvas.js"></script>
  <script src="<?php echo _MAP_SERVER;?>?key=<?=_MAP_KEY ;?>"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/map/longdo/js/jquery-ui.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/select2/dist/js/select2.full.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/select2/dist/js/i18n/th.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/toggle/bootstrap-toggle.min.js"></script>




<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/map/longdo/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/map/mduScriptMap.js"></script>

<script>
      var base_url = "<?php echo base_url();?>";
        var map;
        var route = '' ;
        var taskC = "<?php echo $task;?>" ;
        var case_id = "<?php echo $case_id;?>" ;
        var tracking_loop ;
        var getPoliceVe_loop ;
        function init() {
            // กำหนดขนาดแผนที่
            resize();
            window.addEventListener('resize', resize);

            console.log(longdo.MapTheme.ui.layerSelectorDropdown.splice(19, 1)   );
            console.log(longdo.MapTheme.ui.layerSelectorDropdown.splice(18, 1)   );
            console.log(longdo.MapTheme.ui.layerSelectorDropdown.splice(17, 1)   );
            console.log(longdo.MapTheme.ui.layerSelectorDropdown.splice(16, 1)   );
            console.log(longdo.MapTheme.ui.layerSelectorDropdown.splice(15, 1)   );
            console.log(longdo.MapTheme.ui.layerSelectorDropdown.splice(14, 1)   );
            console.log(longdo.MapTheme.ui.layerSelectorOption.th.button.splice(2, 1) );

            map = new longdo.Map({
                layer: [
                    longdo.Layers.GRAY,
                    //longdo.Layers.TRAFFIC
                ],
                zoom: 12,
                placeholder: document.getElementById('map'),
                lastView: false
            });

            map.Event.bind('click', function(overlay) {
                //map.Overlays.clear();
                var lonLat = map.location(longdo.LocationMode.Pointer);
                console.log(lonLat);
            });


            renderArea();
            /*if(taskC=='current'){
              console.log(taskC);
              var lat = "<?php echo $lat;?>" ;
              var lon =  "<?php echo $lon;?>" ;
              addMarker( lon , lat , '', 'police_car.png') ;
               map.location({
                lon: lon  ,
                lat: lat
              }, true);
              map.zoom(12);
           }*/

           if(case_id!=''){
               //tracking_loop = setInterval(function(){  getTracking(); }, 3000);
               getTracking();
           }else{

           }
        }

        function getTracking(){
          var dx = {} ;
          dx['case_id'] = "<?php echo $case_id;?>" ;
          dx['police_vehicle_code'] = "<?php echo $police_vehicle_code ;?>" ;
          dx['user_code'] = "<?php echo $user_code; ?>" ;
          console.log(dx);
          $.ajax({
              url: "<?php echo base_url(); ?>Mdu_map/tracking",
              data: dx,
              //data: { "case_id": "<?php echo $case_id;?>" , "police_vehicle_code" : "<?php echo $police_vehicle_code;?>" } ,
              type : 'POST'
          }).done(function(data) {
              //desc = '<pre>';
              res = data;
              obj = $.parseJSON(res);
              //console.log(obj['LatLon'][1]);

              var casex = obj['routing']['from'] ;
              var from = [] ;
              from['lat'] = casex['lat'] ;
              from['lon'] = casex['lon'] ;
              from['name'] = casex['name'];
              from['icon'] = casex['icon'] ;
              from['data_type'] = casex['type'] ;
              //from['detail'] = "<b>ประเภทเหตุ : </b>"+casex['casetype_name'] + "<br><b>สถานะเหตุ : </b>"+casex['case_status_name']+"<br><b>สถานที่ : </b>"+ casex['address'];

              var casey = obj['routing']['to'] ;
              var to = [] ;
              to['lat'] = casey['lat'] ;
              to['lon'] = casey['lon'] ;
              to['name'] = casey['name'] ;
              to['icon'] = casey['icon'] ;
              to['data_type'] = casey['type'] ;
              //to['detail'] = "<b>พนักงาน : </b>"+casey['user'] ;
              console.log(route);

              if(route==''){
                console.log('ROUTE');
                routing(from , to);
              }else if(route==from['lon']+"_"+from['lat']){

              }else{
                console.log('ADD_MARK');
                clearMarker_mdu();
                addMarker( from['lon'] , from['lat'] , from['detail'] ,  from['icon'] ) ;
              }


              $.each(obj['mdu']  , function(key, value) {
                value['name'] = value['name'] ;
                //value['detail'] = "พนักงาน : "+value['user'] ;
                //addMarker(value['lon'] , value['lat'] , value ,  value['icon']) ;
              })

          });
        }

        function getPoliceVe(){
          clearInterval(getPoliceVe_loop) ;
          var dx = {} ;

          dx['police_vehicle_code'] = "<?php echo $police_vehicle_code ;?>" ;
          console.log(dx);
          $.ajax({
              url: "<?php echo base_url(); ?>Mdu_map/currentLocation/"+dx['police_vehicle_code'] ,
              //data: dx,
              //data: { "case_id": "<?php echo $case_id;?>" , "police_vehicle_code" : "<?php echo $police_vehicle_code;?>" } ,
              type : 'POST'
          }).done(function(data) {
              //desc = '<pre>';
              clearMarker_mdu();
              res = data;
              obj = $.parseJSON(res);
              //console.log("tracking");
               //console.log(obj );
              //addMarker(lon, lat, desc, icon);



            var ico =  'police_car.png';
              ico =  'police_car_white.png';
            if(obj['is_login']==0){
              ico =  'police_car_white.png';
            }else{
              if (obj['police_vehicle_status_code']=='000') {
                ico =  'police_car_white.png';
              }else if(obj['police_vehicle_status_code']=='001') {
                ico =  'police_car_green.png';
              }else if(obj['police_vehicle_status_code']=='002') {
                ico =  'police_car.png';
              }else if(obj['police_vehicle_status_code']=='003') {
                ico =  'police_car.png';
              }else if(obj['police_vehicle_status_code']=='004') {
                ico =  'police_car.png';
              }else if(obj['police_vehicle_status_code']=='005') {
                ico =  'police_car.png';
              }else if(obj['police_vehicle_status_code']=='006') {
                ico =  'police_car.png';
              }
            }

            obj['name'] = obj['police_vehicle_number'] ;
            obj['type'] = 'mdu';
            addMarker(obj['police_vehicle_lon'], obj['police_vehicle_lat'], obj , ico  );

            //getPoliceVe =  setInterval(function(){  getPoliceVe(); }, 3000);

            getPoliceVe_loop = setInterval(function(){
              getPoliceVe();
            }, 5000);

          });
        }

        function addMarker(lon, lat, desc, icon) {
            //console.log("<?php echo base_url();?>" + '/assets/icon/' + icon);
            //var marker1 = new longdo.Marker({ lon: lon, lat: lat });
            console.log(lon+"--"+ lat +"--"+  desc['name']+"--"+  icon);
            var task_x = "<?php echo $task ;?>" ;
            var marker2 = new longdo.Marker({
                lon: lon,
                lat: lat
            }, {
                title: desc['name'],
                icon: {
                    url: "<?php echo base_url();?>" + '/assets/icon/' + icon,
                    //offset: { x: 12, y: 45 }
                },
                detail: desc['detail'],
                //visibleRange: { min: 7, max: 8 },
                //draggable: true,
                weight: longdo.OverlayWeight.Top
            });
            if(desc['type']!=''){
              marker2['data_type'] = desc['type'] ;
            }else{
              marker2['data_type'] = 'mdu' ;
            }


            if(desc['police_vehicle_bearing']!=null && desc['police_vehicle_bearing']!=-1){
              marker2.update({rotate:desc['police_vehicle_bearing']}) ;
            }
            if(task_x=='current'){
              /*map.location({
                        lon: lon,
                        lat: lat,
                    }, true);
                    map.zoom(17);*/
            }
            map.Overlays.add(marker2);

        }

        function routing(from , to){

          map.Route.placeholder(document.getElementById('desc'));


          var from_a = new longdo.Marker({
                lon: from['lon'],
                lat: from['lat']
            }, {
                title: from['name'],
                icon: {
                    url: "<?php echo base_url();?>" + '/assets/icon/' + from['icon'],
                },
                detail: from['detail'],
                weight: longdo.OverlayWeight.Top
            });
          from_a['data_type'] = "mdu" ;
          map.location({
            lon: from['lon']  ,
            lat: from['lat']
          }, true);
          map.zoom(12);
          route = from['lon']+"_"+from['lat']  ;
          map.Route.add( from_a );

          var to_a = new longdo.Marker({
                lon: to['lon'],
                lat: to['lat']
            }, {
                title: to['name'],
                icon: {
                    url: "<?php echo base_url();?>" + '/assets/icon/' + to['icon'],
                },
                detail: to['detail'],
                weight: longdo.OverlayWeight.Top
            });
          to_a['data_type'] = "case" ;
          map.Route.add( to_a );

          map.Route.search();
          //getPoliceVe();
        }

        function renderArea() {
         // alert( "<?php echo base_url();?>map/data.txt");
          $.ajax({
             url: "<?php echo base_url();?>assets/map/data.txt",
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
                title: value['area_code']+"" ,
                // title: '' ,
                detail: '',
                //label: value['name'],
                lineWidth: 0.7,
                lineColor: 'rgba(' + hex.r + ',' + hex.g + ',' + hex.b + ', 0.8)',
                fillColor: 'rgba(' + hex.r + ',' + hex.g + ',' + hex.b + ', 0.4)',
                // editable: true,
              });
              // console.log(geom2);
              map.Overlays.add(geom2);
              //areaNumber++;
            });
          });
        }

        function resize() {
            var style = document.getElementById('map').style
            // style.height = (window.innerHeight - 10) + 'px';
            style.height = window.innerHeight + 'px';
            // style.width = (window.innerWidth - 8) + 'px';
            style.width = (window.innerWidth - 2) + 'px';
        }

        function hexToRgb(hex) {
          //console.log(hex);
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
        function clearMarker_mdu() {
          var mark = map.Overlays.list();
          // console.log(mark) ;
          var i = 0;
          $.each(mark, function(key, value) {
            //console.log(value instanceof longdo.Marker);

            if(value instanceof longdo.Marker == true && value.hd==0){
              //map.Overlays.remove(value);
              if( value['data_type']=='mdu' ){
                  map.Overlays.remove(value);
              }
            }

           /* if(value instanceof longdo.Circle == true){
              map.Overlays.remove(value);
            }*/
            i++;
          });
        }


        function zoomx(ui){
          alert('ccc');
          /* map.location({
                        lon: 99.7381007452238,
                        lat: 15.7301884673348,
                    }, true);
                    map.zoom(17);*/
        }

       //getPoliceVe =  setInterval(function(){  getPoliceVe(); }, 3000);
        getPoliceVe_loop = setInterval(function(){
          getPoliceVe();
        }, 5000);
    </script>



</body>

</html>