<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  <!-- Force Latest IE rendering engine -->
<meta name="description" content="Online Data Management Tool for the Ministry of Health, Governent of Kenya <?php echo date('Y')?>">
<meta name="keywords" content="Kenya,MNH,commodity assessment,Nairobi,eHealth,ministry of health,GoK,government,survey,health analytics,mfl,maternal health,new born health care" />
<meta name="author" content="Ministry of Health, Government of Kenya">

<!--fav and touch icons -->
<link rel="shortcut icon"  href="<?php echo base_url(); ?>/images/favicon.ico">
<title><?php echo $title; ?></title>

<!-- Le styles -->
<link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.css" rel="stylesheet">

<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic' rel='stylesheet' type='text/css'>


<!-- New Bower Components -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/flat-ui-official/css/flat-ui.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/stylesheets/flat.css">

<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/flat-ui-official/bootstrap/css/prettify.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/intro.js/introjs.css">

<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/select2/select2-bootstrap.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/select2/select2.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css">

<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/semantic-ui/packaged/css/semantic.css">
<!--
<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/qunit/qunit/qunit.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/tablesaw/dist/tablesaw.css">
-->
<!--<link rel="stylesheet" href="<?php echo base_url();?>assets/bower_components/datatables/media/css/jquery.dataTables.css">-->



<!-- Load JS here for greater good

<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/jquery-ui-1.10.3.custom.min.js"></script>
=============================-->
<script src="<?php echo base_url(); ?>js/js_libraries.js"></script>
<script src="<?php echo base_url(); ?>js/js_ajax_load.js"></script>

<script src="<?php echo base_url();?>assets/bower_components/moment/moment.js"></script>
<!--<script src="<?php echo base_url();?>assets/bower_components/jquery/dist/jquery.js"></script>-->
<script src="<?php echo base_url();?>assets/bower_components/select2/select2.js"></script>
<script src="<?php echo base_url();?>js/core.js"></script>
<!-- Bower Components -->
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/jquery.ui.touch-punch.min.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/bootstrap-select.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/bootstrap-switch.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/flatui-checkbox.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/flatui-radio.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/jquery.tagsinput.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/flat-ui-official/js/jquery.placeholder.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/respond/dest/respond.min.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/qunit/qunit/qunit.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/tablesaw/dist/tablesaw.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/intro.js/intro.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/skrollr/dist/skrollr.min.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/skrollr-menu/dist/skrollr.menu.min.js"></script>

<script src="<?php echo base_url();?>assets/bower_components/cheet.js/cheet.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/numeraljs/numeral.js"></script>
<script src="<?php echo base_url()?>js/jquery.quickfit.js"></script>
<!-- Semantic UI -->
<script src="<?php echo base_url();?>assets/bower_components/semantic-ui/packaged/javascript/semantic.js"></script>
<!-- Scripts for Editable and Searchable Tables -->
<script src="<?php echo base_url();?>assets/bower_components/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="<?php echo base_url();?>assets/bower_components/list.js/dist/list.js"></script


<!--script src="<?php echo base_url();?>assets/bower_components/"></script-->


<script src="<?php echo base_url()?>js/highcharts.js"></script>
<script src="<?php echo base_url()?>js/exporting.js"></script>

<!--<script src="<?php echo base_url()?>js/Merged-JS.js"></script>-->
<script src="<?php echo base_url()?>js/FusionMaps/FusionCharts.js"></script>



<link rel="shortcut icon"  href="<?php echo base_url(); ?>/images/favicon.ico">

<script>
    $(document).ready(function(){
        var selectClicked2;
        var selectValue2;
        var selectLink;
        base_url = '<?php echo base_url();?>'
        //$('.select2').select2();
        $('.level2').click(function(){
            selectClicked2  = $(this).attr('id');
            selectValue2 = $('#'+selectClicked2).attr('value');
            //alert(selectValue);
            //if(selectValue==2){
            switch(selectClicked2){
                case 'mnh-level2':
                    selectLink = '<?php echo base_url(); ?>'+'mnh/analytics';
                    break;
                case 'ch-level2' :
                    selectLink = '<?php echo base_url(); ?>'+'ch/analytics';
                    break;
            }
            //}

        });
        $('#mnh-btn').click(function(){
            //$('body').load(selectLink);
        });
        $('#ch-btn').click(function(){
            //$('body').load(selectLink);
        });
        $('#master_list').click(function(){

            showMasterFacilityList(base_url,'table');

        });

        $(document).on('load','.activity-text',function(){
            alert('loaded');
        });



        
        
        // $('.activity').text(moment($('.activity').text()).fromNow()  );
        // $('.modal-footer .btn-primary').click(function()){
            
        // }


       // var s = skrollr.init(/*other stuff*/);

//The options (second parameter) are all optional. The values shown are the default values.
//skrollr.menu.init(s, {
    //skrollr will smoothly animate to the new position using `animateTo`.
    //animate: true,

    //The easing function to use.
    //easing: 'sqrt',

    //Multiply your data-[offset] values so they match those set in skrollr.init
    //scale: 2,

    //How long the animation should take in ms.
    //duration: function(currentTop, targetTop) {
        //By default, the duration is hardcoded at 500ms.
        //return 500;

        //But you could calculate a value based on the current scroll position (`currentTop`) and the target scroll position (`targetTop`).
        //return Math.abs(currentTop - targetTop) * 10;
    //},

    //If you pass a handleLink function you'll disable `data-menu-top` and `data-menu-offset`.
    ////You are in control where skrollr will scroll to. You get the clicked link as a parameter and are expected to return a number.
    //handleLink: function(link) {
       // return 400;//Hardcoding 400 doesn't make much sense.
    //}
//});
    });
</script>

<link rel="shortcut icon"  href="<?php echo base_url(); ?>/images/favicon.ico">





<!-- CODEMIRROR: Download from http://codemirror.net/codemirror.zip -->
<!--link rel="stylesheet" href="<?php echo base_url(); ?>assets/third-party/codemirror/codemirror.css" />
<script src="<?php echo base_url(); ?>assets/third-party/codemirror/codemirror.js"></script>

<!-- Download from http://www.firepad.io/firepad.zip -->
<!--link rel="stylesheet" href="<?php echo base_url(); ?>assets/third-party/firepad/firepad.css" />
<script src="<?php echo base_url(); ?>assets/third-party/firepad/firepad.js"></script-->
