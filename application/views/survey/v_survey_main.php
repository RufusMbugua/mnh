<?php
$mfName = $this -> session -> userdata('fName');
$mfacilityMFL = $this -> session -> userdata('facilityMFL');
?>


<script type="text/javascript" src="<?php echo base_url()?>js/style-table.js"></script>


<script src="<?php echo base_url()?>js/survey.js"></script>

<script>
    $().ready(function(){
        base_url='<?php  echo base_url(); ?>';
        survey='<?php echo $this->session->userdata("survey"); ?>';
        survey_category='<?php echo $this->session->userdata("survey_category");  ?>';
        district='<?php echo $this->session->userdata("dName");  ?>';
         $(document).ready(startSurvey(base_url, survey, survey_category, district));
    });

</script>

<style type="text/css">
    .ui-autocomplete-loading {
        background: white url('<?php echo base_url(); ?>images/ui-anim_basic_16x16.gif') right center no-repeat;
        border-color: #ffffff;
        color:#FF0000;
    }

</style>

</head>
<body id="top">


    <div id="site">
        <div class="center-wrapper">

            <!--logo and main nav-->
            <?php $this->load->view('segments/nav-logged-in'); ?>

            <div class="main form-container ui-widget" >
                <?php echo $form; ?>


            </div>



        </div>
    </div>
