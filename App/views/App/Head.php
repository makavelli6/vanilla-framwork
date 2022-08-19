<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<title><?=(isset($this->title)) ? $this->title :'Simple MVC'; ?></title>

	

	<!-- style -->
	<link rel="stylesheet" href="<?php echo URL; ?>public/css/main.css" type="text/css" />

	<!--custorm modular css -->
	<?php if (isset($this->css)) {
		foreach ($this->css as $css) {
			echo '<link rel="stylesheet" href="'.URL.'public/css/'.$css.' "type="text/css" />';
		}

	}?>
	<script type="text/javascript" src="<?php echo URL; ?>public/js/jquery-3.3.1.js"></script>

	<script type="text/javascript">
		var url ='<?php echo(SITE); ?>'
		//console.log('my url is'+url);
	</script>

	<?php if (isset($this->js)) {
		foreach ($this->js as $js) {
			echo '<script type="text/javascript" src="'.URL.'public/js/'.$js.'"></script>';
		}

	} ?>
	
	<?php Session::init(); ?>
</head>
<body class="  pace-done" data-ui-class="">