<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <h1>Flow Map <small>Enter Your Data</small></h1>
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
        <div class="col-lg-6">
            <?php echo form_open('/', array('role' => 'form', 'id' => 'flow-map-form')); ?>
            <div class="form-group">
                <?php echo form_label('Select Source', 'flow-map-source'); ?>
                <?php echo form_dropdown('source', $states_options, array(), 'class="form-control" id="flow-map-source"'); ?>
            </div>
            <div class="table-responsive">
                <?php echo $this->table->generate(); ?>
            </div>
            <div class="form-group" align="right">
                <?php echo form_submit(array('class' => 'btn btn-primary', 'value' => 'Generate Map'));?>
            </div>
            <?php echo form_close(); ?>

        </div>
    </div>
</div><!-- /#page-wrapper -->