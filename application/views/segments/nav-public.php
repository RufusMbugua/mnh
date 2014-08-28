<div id="header">
    <div class="right" id="toolbar"></div>


    <div id="site-title">
        <div align="center">
            <h3><a href="#"><img src="<?php echo base_url()?>images/logo_combined.png" /></a></h3>
        </div>

    </div>

    <div id="navigation">
        <nav class="" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="<?php echo base_url('analytics'); ?>">Home</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('takesurvey'); ?>">Take Survey</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('analytics'); ?>">View Analytics</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('home'); ?>">Reporting Progress</a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Offline Forms <b class="caret"></b> </a>
                            <ul class="dropdown-menu">
                                <li id="mnh-form">
                                    <a href="#"> 1. Maternal Neonatal Health - Emergency Obstetric Care  </a>
                                </li>
                                <li id="mch-form">
                                    <a href="#"> 2. Child Health - Diarrhoea, Treatment Scale Up  </a>
                                </li>
                                <li id="hcw-form">
                                    <a href="#"> 3. Follow-Up Tool after IMCI Training </a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="#" id="master_list">Master Facility List</a></li>
                        <!--li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> MNH Reporting Facilities (Baseline) <b class="caret"></b> </a>
                            <ul class="dropdown-menu">
                                <li id="mnh-completed">
                                    <a href="#"> 1. List of Completed facilities </a>
                                </li>
                                <li id="mnh-partially">
                                    <a href="#"> 2. List of Partially Completed facilities </a>
                                </li>
                            </ul>
                        </li-->
<!--
                        <li>
                            <a href="<?php echo $this -> config -> item('legacy_url'); ?>">Old System</a>
                        </li>
-->
                        <li>
                            <a href="<?php echo $this -> config -> item('project_url'); ?>">Program Monitoring Tool</a>
                        </li>
                        <li>
                            <a href="health-cmp.or.ke">HCMP</a>
                        </li>
                    </ul>


                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
    </div>

</div>
<script>
    $(document).ready(function(){
        $('#mnh-form').click(function(){
            window.open('<?php echo base_url();?>c_pdf/loadPDF/mnh');
        });
        $('#mch-form').click(function(){
            window.open('<?php echo base_url();?>c_pdf/loadPDF/mch');
        });
        $('#hcw-form').click(function(){
            window.open('<?php echo base_url();?>c_pdf/loadPDF/hcw');
        });

        $('#mnh-completed').click(function(){
            window.open('<?php echo base_url();?>c_statistics/reportingFacilitiesNew/complete/mnh/baseline/');
        });
        $('#mnh-partially').click(function(){
            window.open('<?php echo base_url();?>c_statistics/reportingFacilitiesNew/partial/mnh/baseline/');
        });

    });
</script>
