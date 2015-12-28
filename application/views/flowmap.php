<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <h1>Dashboard <small>Statistics Overview</small></h1>
            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
            </ol>
            <!--            <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            Welcome to SB Admin by <a class="alert-link" href="http://startbootstrap.com">Start Bootstrap</a>! Feel free to use this template for your admin needs! We are using a few different plugins to handle the dynamic tables and charts, so make sure you check out the necessary documentation links provided.
                        </div>-->
        </div>
    </div><!-- /.row -->

    <div class="row">
        <div class="col-lg-12" >
            <canvas id="flowmap" width="950" height="600" style="border:1px solid #CCC;" >
                <?php /* background-image: url(<?php echo site_url('assets/svg/US_map_blank.svg');?>); */ ?>
            </canvas>
        </div>
        <?php 
        /*
        <div class="col-lg-12">
            <?php echo form_open('home/test', array('onsubmit' => 'return false;')); ?>
            <?php echo form_dropdown('state_id', $states_options, array(), "id='states'"); ?>
            <?php echo form_input(array("id" => 'topC', 'name' => 'top')); ?>
            <?php echo form_input(array("id" => 'leftC', 'name' => 'left')); ?>
            <?php echo form_close(); ?>
        </div>
         * 
         */
        ?>
    </div>
</div><!-- /#page-wrapper -->