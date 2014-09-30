<?php $this -> load -> view('segments/nav-public'); ?>

<div id="site-title">

</div>


<div class="login-container">
    <div class="row">
        
		<div id="form-login" style = "width: 60%: margin: 0 auto;">
            <form id="authenticate" name="authenticate" action="<?php echo base_url().'session/new'?>" method="post" accept-charset="utf-8">
                <h2><?php echo $login_message; ?></h2>

                <!--p style="margin-bottom:5px"><label for="username">Facility Name</label</p><p><input id="username" name="username" type="text" placeholder="Facility Name"></p-->
                <div class="form-group">
                    <input id="assessment" name="assessment" class="select2"/>

                </div>
                <div class="form-group">
                    <input id="term" name="term" class="select2">

                </div>
                <div class="form-group">
                    <input id="county" name="county" class="select2">

                </div>
                <div class="form-group">
                    <input id="district" name="district" class="select2">

                </div>

                <div class="form-group">
                    <input id="usercode" name="usercode" type="password" placeholder="Please Enter the Correct Password"/>
                </div>
                <label style="color: #e34848;display:none" for="buttonsPane" >Invalid District/Sub County and Password Combination!</label>
                <div class="buttonsPane">
                    <button type="submit" class="" style="width:inherit">Begin Survey</button>
                </div>
            </form>
        </div>
    </div>

    <!--p class="forgot">Forgot your password? <a href="">Click here to reset it.</a></p-->
    <script>
        $(document).ready(function(){
            base_url='<?php echo base_url();?>';
            $('#run').select2();
            loadData(base_url,'getSurveyTypeNamesJSON','','#assessment','Please Select a Survey Type');
            loadData(base_url,'getSurveyCategoryNamesJSON','','#term','Please Select a Survey Term');
            loadData(base_url,'getCountyNamesJSON','','#county','Please Select as County');

            $('#county').change(function() {
                value = $(this).val();
                loadData(base_url,'getDistrictNamesJSON',value,'#district','Please Select a Sub County');
            });
        });
    </script>

</div><!-- container -->

