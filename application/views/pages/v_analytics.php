<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <?php //$this->load->view('segments/meta');?>
    <?php $this->load->view('segments/head');?>
    <?php //$this->load->view('segments/analytics_css');?>
    <!--link rel="stylesheet" href="<?php echo base_url();?>assets/stylesheets/metronic.css"-->

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top" >
    <!-- BEGIN HEADER -->

    <?php $this->load->view('segments/header');?>

    <?php $this -> load -> view('segments/nav-public'); ?>


    <!-- END HEADER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container row-fluid">
        <!-- BEGIN SIDEBAR -->

        <!-- END SIDEBAR -->
        <!-- BEGIN PAGE -->
        <div class="page-content">
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div id="portlet-config" class="modal hide">
                <div class="modal-header">
                    <button data-dismiss="modal" class="close" type="button"></button>
                    <h3>portlet Settings</h3>
                </div>
                <div class="modal-body">
                    <p>Here will be a configuration form</p>
                </div>
            </div>
            <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <!-- BEGIN PAGE CONTAINER-->
            <div class="container-fluid" data-spy="scroll" data-target="#sectionList">
                <!-- BEGIN PAGE HEADER-->
                <div class="row-fluid">
                    <div class="span12">

                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                        <h4 class="page-title">

                            <?php echo $analytics_main_title; ?> 
                            <!--for <?php echo $this->session->userdata('county_analytics')." County:"?> <?php echo strtoupper($this->session->userdata('survey')) ?> <small><?php echo $analytics_mini_title; ?></small>-->

                        </h4>

                        <ul class="breadcrumb" data-start="border-bottom:0;opacity:1;position:relative" data-top="opacity:0.9;z-index:1000;position:fixed;top:0;width:100%;border-bottom:1px solid #ddd">
                         
                                <select name="survey_type" id="survey_type" class="input">
                                    <option>No Survey Type Selected</option>
                                    <option value="mnh">MNH</option>
                                    <option value="ch">CH</option>
                                    <option value="hcw">IMCI FOLLOW UP</option>
                                </select>
                                <select  name="survey_category" id="survey_category" class="input">
                                    <option>No Survey Category Selected</option>
                                    <option value="baseline">Baseline</option>
                                    <option value="mid-term">Mid-Term</option>
                                    <option value="end-term">End-Term</option>
                                </select>
                            
                                <select name="county_select" id="county_select" class="input">
                                    <option data-scope="national" >All Counties Selected</option>
                                    <?php echo $this->selectReportingCounties;?>
                                </select>
                            
                                <select name="sub_county_select" id="sub_county_select" class="input">
                                    <option data-scope="county" >All Sub-Counties Selected</option>

                                </select>
                                <!-- <select name="facility_select" id="facility_select" class="input">
                                    <option data-scope="national" >All Facilities Selected</option>

                                </select> -->
                            <!--<a data-start="display:none" data-top="display:inline-block" href="" class="go-top">
                <i class="fa fa-chevron-up title="Top""></i>
                &nbsp; Move to Top
            </a>*/-->
                               
                           


                        </ul>
                        <!-- END PAGE TITLE & BREADCRUMB-->
                    </div>
                </div>
                <div class="row">
                    <?php //$this->load->view('segments/analytics_sidebar_menu');?>
                    <?php $this->load->view($analytics_content_to_load);?>
                </div>

                <!-- END PAGE CONTENT-->
            </div>
            <!-- BEGIN PAGE CONTAINER-->
        </div>
        <!-- END PAGE -->
    </div>
    <!-- END CONTAINER -->

    <!-- BEGIN FOOTER -->
    <div id="footer">
        &copy; <?php echo date('Y');?> Ministry of Health, Government of Kenya
            <a data-start="display:none" data-top="display:inline-block" href="" class="go-top">
                <i class="fa fa-chevron-up title="Top""></i>
                &nbsp; Move to Top
            </a>
    </div>
    <!-- END FOOTER -->
    <!-- BEGIN JAVASCRIPTS -->
    <?php //$this->load->view('segments/analytics_js'); ?>
    <script src="<?php echo base_url();?>js/core.js"></script>
    <script src="<?php echo base_url();?>js/analytics.js"></script>
    <script>
        var base_url = "<?php echo base_url();?>";
        var county   = "<?php echo $this->session->userdata('county_analytics');?>";
        var survey   = "<?php echo $this->session->userdata('survey')?>";
        var survey_category   = "<?php echo $this->session->userdata('survey_category')?>";
        $(document).ready(startAnalytics(base_url,county,survey,survey_category));
    </script>
    <!-- END JAVASCRIPTS -->
    <?php $this->load->view('segments/modals')?>
</body>
<!-- END BODY -->
</html>
