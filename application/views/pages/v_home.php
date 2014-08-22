<style type="text/css">
progress
{
    width: 89%;
}
progress[value] {
  /* Reset the default appearance */
  -webkit-appearance: none;
  -moz-appearance: none;
   appearance: none;

  width: 250px;
  height: 20px;
}

progress[value]::-webkit-progress-bar {
  background-color: #eee;
  border-radius: 2px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25) inset;
}

progress[value]::-webkit-progress-value::before {
  content: '80%';
  position: absolute;
  right: 0;
  top: -125%;
}

</style>
<div id="site">
    <div class="center-wrapper">

        <!--logo and main nav-->
        <?php $this -> load -> view('segments/nav-public'); ?>

        <div class="main" >
            <div class="row">


                <div class="tile map" id="reporting-rates">
                    <h5>Reporting Rates</h5>
                    <div class="outer">
                        <div class="map-header">

                            <div class="btn-group">
                                <button type="button" class="dropdown-toggle" data-toggle="dropdown">
                                    Maternal and Neonatal Health <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-survey="mnh" data-survey-category="baseline" href="#">Baseline</a></li>
                                    <li><a data-survey="mnh" data-survey-category="mid-term" href="#">Mid-Term</a></li>
                                    <li><a data-survey="mnh" data-survey-category="end-term" href="#">End-Term</a></li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="dropdown-toggle" data-toggle="dropdown">
                                    Child Health <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-survey="ch" data-survey-category="baseline" href="#">Baseline</a></li>
                                    <li><a data-survey="ch" data-survey-category="mid-term" href="#">Mid-Term</a></li>
                                    <li><a data-survey="ch" data-survey-category="end-term" href="#">End-Term</a></li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="dropdown-toggle" data-toggle="dropdown">
                                    IMCI Follow-Up Tool <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-survey="hcw" data-survey-category="baseline" href="#">Baseline</a></li>
                                    <li><a data-survey="hcw" data-survey-category="mid-term" href="#">Mid-Term</a></li>
                                    <li><a data-survey="hcw" data-survey-category="end-term" href="#">End-Term</a></li>
                                </ul>
                            </div>

                            <h6 id="current-map">Please Choose an Assessment Above</h6>

                        </div>

                        <div class="post" id="map">

                        </div><!--./kenya_county_map-->
                        <h6>Click a county to see county data</h6>

                    </div>

                </div><!--./middle_column-->

                <!-- Data analysis section-->
                <div class="tile map-info" id="map-info">
                    <h5>Data From the Map</h5>
                    <div class="outer">
                       <div class="statistic"><div class="icon"><span><i class="fa fa-map-marker"></i></span></div><div class="data"><span class="text" id="county_name">No County Chosen</span></div></div>
                        <div class="statistic"><div class="icon"><span><i class="fa fa-pencil"></i></span></div><div class="data" id="survey"><span class="text" id="survey_type">No Survey Type Chosen</span><span class="text" id="survey_category">No Survey Category Chosen</span></div></div>

                        <div class="statistic"><div class="icon"><span><i class="fa fa-hospital-o"></i></span></div><div class="data" id="targeted"><span class="text">Targeted Facilities</span><span class="digit">0</span></div></div>
                        <div class="statistic"><div class="icon"><span><i class="fa fa-hospital-o"></i></span></div><div class="data" id="finished"><span class="text">Facilities that have finished reporting</span><span class="digit">0</span></div></div>
                        <div class="statistic"><div class="icon"><span><i class="fa fa-hospital-o"></i></span></div><div class="data" id="started"><span class="text">Facilities that have started but not finished</span><span class="digit">0</span></div></div>
                        <div class="statistic"><div class="icon"><span><i class="fa fa-hospital-o"></i></span></div><div class="data" id="not_started"><span class="text">Facilities that have not started</span><span class="digit">0</span></div></div>
                        <div class = "statistic"><div class = "icon"><span><i class = "fa fa-hospital-o"></i></span></div>
                        <div id = "county_progress"><span>No county selected</span><progress id = "c_progress" max = "100" value = "0">Progress: 80%</progress></div></div>
                        <button id="load_analytics"><i class="fa fa-bar-chart-o"></i>Click to View Analytics</button>
                        <button id="load_county_summary"><i class="fa fa-bar-chart-o"></i>Click to Download Excel Summary</button>
                    </div>
                </div>

            </div>


            <!--footer-->
            <?php //$this->load->view('segments/footer-public'); ?>

        </div>
    </div>
    <script>
        $(document).ready(function(){
            var styles1={
                'padding':'2%',
                'border':'2px solid #ddd',
                'color':'black'
            }
            var styles2={
                'text-decoration':'none',
                'color':'#005580',
                'border':'none'
            }



            $('#mnh_map').hide();
            $('#ch-map').css(styles1);
            $('#hcw_map').hide();


            $('#mnh-map').click(function(){
                //alert(' ');
                $('#mnh_map').show();
                $('#ch_map').hide();
                $('#hcw_map').hide();

                $('#mnh-map').css(styles1);
                $('#ch-map').css(styles2);
                $('#hcw-map').css(styles2);
            });


            $('button').css('display','inline-block');
            $('#ch-map').click(function(){
                //alert(' ');
                $('#ch_map').show();
                $('#mnh_map').hide();
                $('#hcw_map').hide();

                $('#ch-map').css(styles1);
                $('#mnh-map').css(styles2);
                $('#hcw-map').css(styles2);
            });


            $('#hcw-map').click(function(){
                //alert(' ');
                $('#hcw_map').show();
                $('#mnh_map').hide();
                $('#ch_map').hide();

                $('#hcw-map').css(styles1);
                $('#mnh-map').css(styles2);
                $('#ch-map').css(styles2);
            });
            runMap('mnh','end-term');
            $('.dropdown-menu li a').click(function(){
                survey=$(this).attr('data-survey');
                survey_category=$(this).attr('data-survey-category');
                survey_in_full=$(this).parent().parent().parent().find('.dropdown-toggle').text();
                $('#current-map').text(survey_in_full+' : '+survey_category);
                $('#current-map').css('textTransform', 'capitalize');
                runMap(survey,survey_category);

            });
            $('#load_analytics').click(function(){
                url = $(this).attr('data-url');
                window.open(url);
            });
            $('#load_county_summary').click(function(){
                url = $(this).attr('data-url');
                window.open(url);
            });
        });

        function runCountyData(data){
            newData=data.split(',');
            //console.log(newData);
            base_url = '<?php echo base_url();?>';
            getCountyData(base_url,newData[0],newData[1],newData[2]);
        }


        function runMap(survey,survey_category){

            $.ajax({
                url: '<?php echo base_url();?>c_front/runMap/'+survey+'/'+survey_category,
                beforeSend: function(xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function(data) {
                    //console.log(data);

                    obj = jQuery.parseJSON(data);
                    var map= new FusionMaps ("js/FusionMaps/Maps/FCMap_KenyaCounty.swf","KenyaMap","100%","70%","0","0");
                    map.setJSONData(data);
                    map.render("map");

                }
            });
        }
    </script>
