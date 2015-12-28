<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard - Visualize</title>

    <!-- Bootstrap core CSS -->
    <?php echo link_tag(style_url('bootstrap.css'));?>

    <!-- Add custom CSS here -->
    <?php echo link_tag(style_url('sb-admin.css'));?>
    
    <?php echo link_tag(assets_url('font-awesome/css/font-awesome.min.css'));?>
    
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.4.3.min.css">
    <!-- JavaScript -->
    <script src="<?php echo script_url('jquery-2.0.3.js');?>"></script>
    <script src="<?php echo script_url('bootstrap.js');?>"></script>
    <script src="<?php echo script_url('fabric-1.4.0.js');?>"></script>
    
    <!-- Page Specific Plugins -->
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>-->
    <!--<script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>-->
    <!--<script src="<?php echo script_url('morris/chart-data-morris.js');?>"></script>-->
    <script src="<?php echo script_url('tablesorter/jquery.tablesorter.js');?>"></script>
    
    <script src="<?php echo script_url('visualize.js');?>"></script>
    <script src="<?php echo script_url('autoload.js');?>"></script>
    
    <script type="text/javascript">
        var SITE_URL = '<?php echo site_url();?>';
    </script>
  </head>

  <body>

    <div id="wrapper">
        
        <?php $this->load->view('nav');?>