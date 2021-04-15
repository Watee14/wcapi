<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/css/style-metro.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/css/style-responsive.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/css/themes/light.css'); ?>" rel="stylesheet" type="text/css" id="style_color">
	<link href="<?php echo base_url('assets/plugins/openlayers/css/ol.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/plugins/map/longdo/css/style.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/plugins/map/longdo/css/jquery-ui.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker-4.17.47.min.css'); ?>" rel="stylesheet" type="text/css">
	<link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('assets/css/style-custom.css'); ?>" rel="stylesheet" type="text/css">
	<style></style>
</head>
<body>
	<div class="ui-widget">
  <label for="tags">Tags: </label>
  <input id="tags">
</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div style="left:50%;position:absolute">
			<div id="filters" style="height:auto;left:-50%;margin:5px;position:relative;width:auto;z-index:256">
				<div class="form-inline">
					<div class="form-group" style="margin-bottom:0!important">
						<label class="control-label" style="margin-top:0;margin-bottom:5px">
							<span class="glyphicon glyphicon-map-marker small"></span>
							<span class="small">ค้นหาสถานที่:</span>
						</label>
						<div class="input-group">
							<input type="text" id="q" class="form-control input-sm" placeholder="ใส่ชื่อสถานที่ หรือชื่อถนน"  style="font-size:12px!important;margin-top:0">
							<a class="glyphicon glyphicon-remove form-control-feedback text-muted small" aria-hidden="true" role="button" onclick="$('#q').val(null);$('#q').focus();" style="pointer-events:auto;right:43px;top:0!important;z-index:1000" title="ล้างข้อมูล"></a>
							<a class="input-group-addon btn blue" role="button" onclick="searchQ()" style="border:0!important">
								<span class="glyphicon glyphicon-search small"></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="desc">10.0.106.66</div>
		<div id="map"></div>
	</div>
	<script src="<?php echo base_url('assets/js/jquery-3.1.1.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('assets/js/html2canvas.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo _MAP_SERVER; ?>?key=<?php echo _MAP_KEY; ?>"></script>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>

		$( function() {
			var availableTags = [
				"ActionScript",
				"AppleScript",
				"Asp",
				"BASIC",
				"C",
				"C++",
				"Clojure",
				"COBOL",
				"ColdFusion",
				"Erlang",
				"Fortran",
				"Groovy",
				"Haskell",
				"Java",
				"JavaScript",
				"Lisp",
				"Perl",
				"PHP",
				"Python",
				"Ruby",
				"Scala",
				"Scheme"
			];
			$( "#tags" ).autocomplete({
				source: availableTags
			});
		} );
	</script>
</body>
</html>