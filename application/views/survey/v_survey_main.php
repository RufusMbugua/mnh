<?php
$mfName = $this -> session -> userdata('fName');
$mfacilityMFL = $this -> session -> userdata('facilityMFL');
?>


<script type="text/javascript" src="<?php echo base_url()?>js/style-table.js"></script>


<script>
    $().ready(function(){
        /**
             * variables
             */
        var form_id='';
        var link_id='';
        var linkIdUrl='';
        var linkSub='';
        var linkDomain='';
        var visit_site = '';
        var devices='';
        var fac_mfl = fac_county = fac_district = '';
        var base_url = '<?php echo base_url(); ?>';
        var survey = '<?php echo $this->session->userdata("survey"); ?>';
        var survey_category = '<?php echo $this->session->userdata("survey_category"); ?>';
        var county = '<?php echo $this->session->userdata("county"); ?>';
        var district = '<?php echo $this->session->userdata("district"); ?>';

        getDistrictData(base_url, district, survey, survey_category);
        //start of close_opened_form click event
        $("#close_opened_form").click(function() {

            $(".form-container").load('<?php echo base_url() . 'c_front/formviewer'; ?>',function(){

                //delegate events
                loadGlobalScript();

            });
        });/*end of close_opened_form click event

        
        /*----------------------------------------------------------------------------------------------------------------*/

        /*start of loadGlobalJS*/
        var onload_queue = [];
        var dom_loaded = false;

        function loadGlobalJS(src, callback) {
            var script = document.createElement('script');
            script.type = "text/javascript";
            script.async = true;
            script.src = src;
            script.onload = script.onreadystatechange = function() {
                if (dom_loaded)
                    callback();
                else
                    onload_queue.push(callback);
                // clean up for IE and Opera
                script.onload = null;
                script.onreadystatechange = null;
            };
            var head = document.getElementsByTagName('head')[0];
            head.appendChild(script);
        }/*end of loadGlobalJS*/

        function domLoaded() {
            dom_loaded = true;
            var len = onload_queue.length;
            for (var i = 0; i < len; i++) {
                onload_queue[i]();
            }
            onload_queue = null;
        };/*end of domLoaded*/

        /*-----------------------------------------------------------------------------------------------------------*/

        //check box/checked radio function was here

        domLoaded();

        /*----------------------------------------------------------------------------------------------------------------*/

        /*reset form event*/
        /*start of reset_current_form click event*/
        $("#reset_current_form").click(function() {
            $(form_id).resetForm();

        });/*end of reset_current_form click event*/

        /*----------------------------------------------------------------------------------------------------------------*/
        var loaded=false;
        function loadGlobalScript(){
            loaded=true;

            var scripts=['<?php echo base_url();?>js/js_ajax_load.js'];

            for(i=0;i<scripts.length;i++){
                loadGlobalJS(scripts[i],function(){});
            }
            form_id='#'+$(".form-container").find('form').attr('id');

        }
        /*----------------------------------------------------------------------------------------------------------------*/





        //load 1st section of the assessment on page load
        $(".form-container").load('<?php echo base_url() . 'c_load/get_facility_list'; ?>',function(){
            // facilityMFL=12864;
            //loadGlobalScript();//renderFacilityInfo(facilityMFL);

            $('.dataTable').dataTable({
                "sPaginationType": "full_numbers"
            });

            //so which link was clicked?
            $('.action').live('click',function(){
                link_id='#'+$(this).find('a').attr('id');
                link_id=link_id.substr(link_id.indexOf('#')+1,link_id.length);
                //linkSub=$(link_id).attr('class');
                //linkIdUrl=link_id.substr(link_id.indexOf('#')+1,(link_id.indexOf('_li')-1));
                facilityMFL=link_id;
                the_url='<?php echo base_url();?>c_load/startSurvey/<?php echo $this->session->userdata("survey");?>/<?php echo $this->session->userdata("survey_category");?>/'+facilityMFL+'/2013-2014';
                $.ajax({
                    type:'POST',
                    data: '',
                    async:false,
                    url:the_url,
                    beforeSend: function(){

                    },
                    success: function(data){
                        obj = jQuery.parseJSON(data);
                        console.log(obj);

                        fac_name=obj[0].fac_name;
                        fac_district=obj[0].fac_district;
                        fac_county=obj[0].fac_county;
                        message = obj[0].fac_name +' in ' +obj[0].fac_district+ ' District, ' +obj[0].fac_county+ ' County, is now reporting on the <?php echo strtoupper($this->session->userdata("survey"));?> Survey.' ;
                        console.log(message);
                        runNotification(base_url,'c_admin/getContacts',message);
                    }}
                      );
                //alert(link_id);
                if(link_id)

                    <?php if($this->session->userdata('survey')=='mnh'){?>
                    $(".form-container").load('<?php echo base_url();?>c_load/get_mnh_form',function(){
                        <?php }elseif($this->session->userdata('survey')=='hcw'){?>
                        $(".form-container").load('<?php echo base_url();?>c_load/get_hcw_form',function(){
                            <?php }else{?>
                            $(".form-container").load('<?php echo base_url();?>c_load/get_mch_form',function(){
                                <?php }?>

                                //delegate events
                                //if(loaded==false)
                                //include remote scripts
                                loadGlobalScript();renderFacilityInfo(facilityMFL);break_form_to_steps(form_id);select_option_changed();

                            });

                        })/*end of which link was clicked*/
                        /*----------------------------------------------------------------------------------------------------------------*/
                    });

                /*-----------------------------------------------------------------------------------------------------------------*/
                /*start of ajax data requests*/
                function renderFacilityInfo(facilityMFL){
                    $.ajax({
                        type: "GET",

                        url: "<?php echo base_url()?>c_load/getFacilityDetails",
                        dataType:"json",
                        cache:"true",
                        data:"facilityMFL="+facilityMFL,
                        success: function(data){
                            var info = data.rData;
                            //print(info);
                            $.each(info , function(i,facility) {
                                //render found data
                                //$("#fac_name").val(facility.fac_name).prop('disabled', true);
                                $("#facilityName").text(facility.facName);
                                $('#facilityMFLCode').val(facility.facMfl);
                                $('#facilityHName').val(facility.facHName);

                                //$("#facilityType").val(facility.facilityType);
                                $("#facilityLevel").val(facility.facLevel);
                                $("#facilityOwnedBy").val(facility.facOwnership);
                                //$("#facilityDistrict").val(facility.facilityDistrict);
                                //$("#facilityCounty").val(facility.facilityCounty);

                                $("#facilityType option").filter(function() {return $(this).text() == facility.facType;}).first().prop("selected", true);
                                $("#facilityLevel option").filter(function() {return $(this).text() == facility.facLevel;}).first().prop("selected", true);
                                $("#facilityOwnedBy option").filter(function() {return $(this).text() == facility.facOwnership;}).first().prop("selected", true);

                                $("#facilityDistrict option").filter(function() {return $(this).text() == facility.facDistrict;}).first().prop("selected", true);
                                $("#facilityCounty option").filter(function() {return $(this).text() == facility.facCounty;}).first().prop("selected", true);


                                $("#facilityInchargename").val(facility.facInchargeContactPerson);
                                $("#facilityInchargemobile").val(facility.facInchargeTelephone);
                                $("#facilityInchargeemail").val(facility.facInchargeEmail);

                                $("#facilityMchname").val(facility.facMCHContactPerson);
                                $("#facilityMchmobile").val(facility.facMCHTelephone);
                                $("#facilityMchemail").val(facility.facMCHEmail);

                                $("#facilityMaternityname").val(facility.facyMaternityContactPerson);
                                $("#facilityMaternitymobile").val(facility.facMaternityTelephone);
                                $("#facilityMaternityemail").val(facility.facMaternityEmail);

                                //}
                            });

                            //return false;
                        },
                        beforeSend:function(){},
                        afterSend:function(){}
                    });
                    return false;
                }
                /*end of ajax data requests*/
                /*-----------------------------------------------------------------------------------------------------------------*/


                //equipment availability change detectors
                function select_option_changed(){

                    /*
                 * Checking for all SELECT inputs
                 */
                    $(form_id).find('select').on("change",function() {
                        /*
                     * Identify the class of the SELECT input
                     *
                     * IF(class matches 'cloned is-guideline')
                     * Then
                     *  ->Get the SELECT's ID
                     *
                     */
                        if($(this).attr('class')=='cloned is-guideline'){
                            cb_id='#'+$(this).attr('id');

                            //alert(cb_id);
                            cb_no=cb_id.substr(cb_id.indexOf('_')+1,(cb_id.length))//for the numerical part of the id

                            //substr(id.indexOf('_')+1,id.length)
                            //cb_id=cb_id.substr(cb_id.indexOf('#'),(cb_id.indexOf('_')))//for the trimmed id
                            //alert(cb_no);
                            /*
                     * Checking if the user selected 'No'
                     */
                            if(($(cb_id).val() == "No")) {

                                //alert(cb_no);
                                //$('#ortcGuidesCount_'+cb_no).hide();
                                //$('#ortcGuidesCount_'+cb_no).removeClass('label.error');
                                $('#ortcGuidesCount_'+cb_no).val(0);
                                //$('#ortcGuidesCount_'+cb_no).prop('disabled', true);
                            }
                            /*
                         * Else leave activated
                         */
                            else{

                                //$('#ortcGuidesCount_'+cb_no).prop('disabled', false);
                                $('#ortcGuidesCount_'+cb_no).val('');
                                //$('#ortcGuidesCount_'+cb_no).show();
                                //  $('#ortcGuidesCount_'+cb_no).siblings('label').removeClass('error');
                            }
                        } //close if($(this).attr('class')=='cloned is-guideline')

                        //mnh section 2 follow up questions
                        //if($(this).attr('class')=='cloned ceoc'){
                        //transfusion
                        if($(this).attr('id')=='mnhceocAspectResponse_1' && $(this).val()=='Yes'){
                            //show yes follow up qn
                            $('#transfusion_n').hide();
                            $('#transfusion_n').prop('disabled',true);

                            $('#mnhceocFollowUpOther_1').prop('disabled',false);
                            $('#mnhceocFollowUpOther_1').show();
                            $('#mnhceocFollowUp_1').prop('disabled',false);
                            $('#mnhceocFollowUp_1').show();
                            $('#label_followup_other_1').show();

                            $('#transfusion_y').prop('disabled',false);
                            $('#transfusion_y').show();

                        }

                        if($(this).attr('id')=='mnhceocAspectResponse_1' && $(this).val()=='No'){

                            //show no follow up qn, and hide yes one
                            $('#transfusion_y').hide();
                            $('#transfusion_y').prop('disabled',true);

                            $('#mnhceocFollowUpOther_1').hide();
                            $('#label_followup_other_1').hide();
                            $('#mnhceocFollowUpOther_1').prop('disabled',true);
                            $('#mnhceocFollowUp_1').prop('disabled',true);
                            $('#mnhceocFollowUp_1').hide();


                            $('#transfusion_n').prop('disabled',false);
                            $('#transfusion_n').show();
                        }

                        if($(this).attr('id')=='mnhceocAspectResponse_2' && $(this).val()=='Yes'){
                            //CS conduction
                            //hide follow up qn
                            $('#csdone_n').hide();
                            $('#csdone_n').prop('disabled',true);

                            $('#mnhceocReasonOther_2').hide();
                            $('#label_reason_other_2').hide();
                            $('#mnhceocReasonOther_2').prop('disabled',true);


                        }
                        if($(this).attr('id')=='mnhceocAspectResponse_2' && $(this).val()=='No'){
                            //show no follow up qn
                            $('#csdone_n').prop('disabled',false);
                            $('#csdone_n').show();
                        }


                        if($(this).attr('id')=='mnhceocFollowUp_1' && $(this).val()=='Other'){
                            //show input field on other

                            $('#mnhceocFollowUpOther_1').show();
                            $('#label_followup_other_1').show();
                            $('#mnhceocFollowUpOther_1').prop('disabled',false);


                        }
                        if($(this).attr('id')=='mnhceocFollowUp_1' && $(this).val()!='Other'){

                            //hide other input field
                            $('#mnhceocFollowUpOther_1').prop('disabled',true);
                            $('#mnhceocFollowUpOther_1').hide();
                            $('#label_followup_other_1').hide();
                        }

                        if($(this).attr('id')=='mnhceocReason_1' && $(this).val()=='Other'){
                            //show input field on other

                            $('#mnhceocReasonOther_1').prop('disabled',false);
                            $('#mnhceocReasonOther_1').show();
                            $('#label_reason_other_1').show();

                        }

                        if($(this).attr('id')=='mnhceocReason_1' && $(this).val()!='Other'){

                            //hide other input field
                            $('#mnhceocReasonOther_1').prop('disabled',true);
                            $('#mnhceocReasonOther_1').hide();
                            $('#label_reason_other_1').hide();
                        }

                        if($(this).attr('id')=='mnhceocReason_2' && $(this).val()=='Other'){
                            //show input field on other

                            $('#mnhceocReasonOther_2').prop('disabled',false);
                            $('#mnhceocReasonOther_2').show();
                            $('#label_reason_other_2').show();

                        }

                        if($(this).attr('id')=='mnhceocReason_2' && $(this).val()!='Other'){

                            //hide other input field
                            $('#mnhceocReasonOther_2').prop('disabled',true);
                            $('#mnhceocReasonOther_2').hide();
                            $('#label_reason_other_2').hide();
                        }

                        //	}//close if class is ceoc
                    });

                    $(form_id).find(':radio').on('change',function(){
                        r_id='#'+$(this).attr('name');
                        r_no=r_id.substr(r_id.indexOf('_')+1,(r_id.length))//for the numerical part of the name
                        if($(this).val()=='Never Available'){
                            $('#cqNumberOfUnits_'+r_no).val(0);
                            $('#cqExpiryDate_'+r_no).val('n/a');
                            $('#cqLocNA_'+r_no).prop('checked', true);

                            $('#eqQtyFullyFunctional_'+r_no).val(0);
                            $('#eqQtyNonFunctional_'+r_no).val(0);
                            $('#eqLocOther_'+r_no).prop('checked', true);

                            if($(this).attr('name')=='sqAvailability_'+r_no){
                                $('#sqLocOther_'+r_no).prop('checked', true);
                                $('#sqNumberOfUnits_'+r_no).val(0);
                                // $("#sqSupplier_"+r_no+" option").filter(function() {return $('#sqSupplier_'+r_no).val() == 'Not Applicable';}).first().prop("selected", true);
                            }else if($(this).attr('name')=='hwAvailability_'+r_no){
                                $('#hwLocOther_'+r_no).prop('checked', true);
                                //$("#hwSupplier_"+r_no+" option").filter(function() {return $('#hwSupplier_'+r_no).val() == 'Not Applicable';}).first().prop("selected", true);
                            }

                        }else{
                            $('#cqNumberOfUnits_'+r_no).val('');
                            $('#cqExpiryDate_'+r_no).val('');
                            $('#cqLocNA_'+r_no).prop('checked', false);

                            $('#eqQtyFullyFunctional_'+r_no).val('');
                            $('#eqQtyNonFunctional_'+r_no).val('');
                            $('#eqLocOther_'+r_no).prop('checked', false);

                            if($(this).attr('name')=='sqAvailability_'+r_no){
                                $('#sqLocOther_'+r_no).prop('checked', false);
                                $('#sqNumberOfUnits_'+r_no).val('');
                                //$("#sqSupplier_"+r_no+" option").filter(function() {return $('#sqSupplier_'+r_no).val() == 'Select One';}).first().prop("selected", true);
                            }else if($(this).attr('name')=='hwAvailability_'+r_no){
                                $('#hwLocOther_'+r_no).prop('checked', false);
                                //$("#hwSupplier_"+r_no+" option").filter(function() {return $('#hwSupplier_'+r_no).val() == 'Select One';}).first().prop("selected", true);
                            }

                        }
                    });

                    //date picker
                    $('.expiryDate').datepicker({defaultDate:new Date(), changeMonth: true,changeYear: true,dateFormat:"yy-mm-dd",minDate: '-5y', maxDate: "5y"});


                    //to review equipment assessment--enables the disabled select options
                    $('#editEquipmentListTopButton,#editEquipmentListTopButton_2,#editEquipmentListTopButton_3a,#editEquipmentListTopButton_3b,#editEquipmentListTopButton_4').click(function(){
                        $('#tableEquipmentList,#tableEquipmentList_2,#tableEquipmentList_3a,#tableEquipmentList_3b,#tableEquipmentList_4').find('select[class="cloned left-combo"]').prop('disabled', false);
                    });


                }//end of select_option_changed
                $(".Options").click(function(){
                    //$("div[id*='PayMethod_']:visible").slideToggle("slow");
                    var ID = $(this).attr("id");
                    if (this.checked) {
                        $("#ind_"+ID).slideToggle("slow");
                    }
                });

            }); /*close document ready*/

            // function additionfunction()
            // {
            //     var malsevere = document.getElementById("malsevere").value;
            //     var malsome = document.getElementById("malsome").value;
            //     var malnodehydration = document.getElementById("malnodehydration").value;
            //     var maldysentry = document.getElementById("maldysentry").value;
            //     var malnoclass = document.getElementById("malnoclass").value;

            //     var diatotal = Number(malnoclass) + Number(malsome) + Number(malnodehydration) + Number(maldysentry) + Number(malsevere);
            //     document.getElementById("diatotal").value = diatotal;

            //     var severepne = document.getElementById("severepne").value;
            //     var pne = document.getElementById("pne").value;

            //     var pnetotal = Number(severepne) + Number(pne);
            //     document.getElementById("pnetotal").value = pnetotal;


            //     var malconfirmed = document.getElementById("malconfirmed").value;
            //     var malnotconfirmed = document.getElementById("malnotconfirmed").value;

            //     var malariatotal = Number(malconfirmed) + Number(malnotconfirmed);
            //     document.getElementById("malariatotal").value = malariatotal;

            //     var u5total = Number(diatotal) + Number(pnetotal) + Number(malariatotal);

            //     document.getElementById("totalu5").value = u5total;
            // }

            function selectpnesevereTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "pnesevereTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[SeverePneumonia][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    li.setAttribute('class','treatment');
                    ul.appendChild(li);
                    var code = select.options[select.selectedIndex].value;
                    var txt = document.createElement("input");
                    txt.setAttribute("value", code);
                    txt.setAttribute("type", "hidden");
                    txt.setAttribute("name", "pneTreat[]");

                    var diver = document.getElementById("pneTreatmentSection");

                    diver.appendChild(txt);


                }
            }// close select treatment

            function selectpneTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "pneTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[Pneumonia][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    li.setAttribute('class','treatment');
                    ul.appendChild(li);
                    var code = select.options[select.selectedIndex].value;
                    var txt = document.createElement("input");
                    txt.setAttribute("value", code);
                    txt.setAttribute("type", "hidden");
                    txt.setAttribute("name", "pneTreat[]");

                    var diver = document.getElementById("pneTreatmentSection");

                    diver.appendChild(txt);
                }
            }// close select treatment
            function selectmalconfirmedTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "malconfrimedTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[ConfirmedMalaria][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    li.setAttribute('class', 'treatment');
                    ul.appendChild(li);
                    var code = select.options[select.selectedIndex].value;
                    var txt = document.createElement("input");
                    txt.setAttribute("value", code);
                    txt.setAttribute("type", "hidden");
                    txt.setAttribute("name", "malTreat[]");

                    var diver = document.getElementById("malTreatmentSection");

                    diver.appendChild(txt);
                }
            }// close select treatment

            function selectmalnotconfirmedTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "malnotconfrimedTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[NotConfirmedMalaria][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    li.setAttribute('class', 'treatment');
                    ul.appendChild(li);
                    var code = select.options[select.selectedIndex].value;
                    var txt = document.createElement("input");
                    txt.setAttribute("value", code);
                    txt.setAttribute("type", "hidden");
                    txt.setAttribute("name", "malTreat[]");

                    var diver = document.getElementById("malTreatmentSection");

                    diver.appendChild(txt);
                }
            }// close select treatment
            function selectseverediaTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "severediaTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[SevereDehydration][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectsomedehydrationdiaTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "somedehydrationdiaTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[SomeDehydration][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectdysentryTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "dysentryTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[Dysentry][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectnodehydrationdiaTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "nodehydrationTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[NoDehydration][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectnoclassificationTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "noclassificationTreatment_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'mchtreatment[NoClassification][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectothertreatmentTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "othertreat_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'indicatormchsymptom[pne][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectdiaresponseTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "diaresponse_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'indicatormchsymptom[dia][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectfevresponseTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "fevresponse_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'indicatormchsymptom[fev][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function selectearresponseTreatment(select)
            {
                var value = select.options[select.selectedIndex].value;
                if(value != "earresponse_0")
                {
                    var option = select.options[select.selectedIndex];
                    var ul = select.parentNode.getElementsByTagName('ol')[0];
                    var choices = ul.getElementsByTagName('input');
                    for (var i = 0; i < choices.length; i++)
                        if (choices[i].value == option.value)
                            return;
                    var li = document.createElement('li');
                    var input = document.createElement('input');
                    var text = document.createTextNode(option.firstChild.data);
                    input.type = 'hidden';
                    input.name = 'indicatormchsymptom[ear][]';
                    input.value = option.value; li.appendChild(input);
                    li.appendChild(text);
                    li.setAttribute("id", code);
                    li.setAttribute('class', 'treatment');
                    li.setAttribute('onclick', 'this.parentNode.removeChild(this);');
                    var code = select.options[select.selectedIndex].value;
                    ul.appendChild(li);
                }
            }// close select treatment

            function toggle_table(el)
            {
                id = $(el).attr("id");
                name = $(el).attr("name");
                radioValue = $(el).attr("value");
                // alert("This is the id: "+id+" and definately the name: "+name);
                if(radioValue === '1')
                {
                    $('.'+name).show();
                }
                else
                {
                    $('.'+name).hide();
                }
            }
            function break_form_to_steps(form_id){
                //form_id='#zinc_ors_inventory';
                //alert(form_id);
                var end_url;
                $(form_id).formwizard({
                    formPluginEnabled: false,
                    validationEnabled: false,
                    historyEnabled:true,
                    focusFirstInput : true,
                    textNext : 'Save and Go to the Next Section',
                    textBack : 'View Previous Section',
                    formOptions :{
                        //success: function(data){$("#status").fadeTo(500,1,function(){ $(this).html("Thank you for completing this assessment! :) ").fadeTo(5000, 0); })},
                        //beforeSubmit: function(data){$("#data").html("Processing...");},
                        dataType: 'json',
                        resetForm: true,
                        disableUIStyles:true
                    }
                });

                //remove some jQueryUI styles
                $(form_id).find('input,select,radio,form').removeClass('ui-helper-reset ui-state-default ui-helper-reset ui-wizard-content');

                var remoteAjax = {}; // empty options object

                $(form_id+" .step").each(function(){ // for each step in the wizard, add an option to the remoteAjax object...
                    remoteAjax[$(this).attr("id")] = {
                        <?php if($this->session->userdata('survey')=='mnh'){?>
                        url : "<?php echo base_url()?>submit/c_form/complete_mnh_survey", // the url which stores the stuff in db for each step
                        <?php }elseif($this->session->userdata('survey')=='hcw'){?>
                        url : "<?php echo base_url()?>submit/c_form/complete_hcw_survey", // the url which stores the stuff in db for each step
                        <?php }else{?>
                        url : "<?php echo base_url()?>submit/c_form/complete_ch_survey", // the url which stores the stuff in db for each step
                        <?php }?>
                        dataType : 'json',
                        beforeSubmit: function(data){$("#data").html("<div class='error ui-autocomplete-loading' style='width:auto;height:25px'>Processing...</div>")},
                        //beforeSubmit: function(data){$("#data").html("Saving the previous section's response")},
                        success : function(data){
                            if(data){ //data is either true or false (returned from store_in_database.html) simulating successful / failing store
                                //$("#data").show();
                                $("#data").html("Section was Saved Successfully...").fadeTo("slow",0);
                                $(form_id).bind("after_remote_ajax", function(event, fdata){
                                    //console.log($(form_id).formwizard('state'));
                                    survey = '<?php echo $this -> session -> userdata('survey');?>';
                                    if(survey=='mnh'){
                                        if(fdata.currentStep=='section-8'){

                                            $(".form-container").load('<?php echo base_url();?>c_load/survey_complete',function(){
                                                window.location='<?php echo base_url();?>/assessment'; });

                                            message = fac_name +' in ' +fac_district+ ' District, ' +fac_county+ ' County, has completed the <?php echo strtoupper($this->session->userdata("survey"));?> Survey.' ;
                                            console.log(message);
                                            runNotification('<?php echo base_url(); ?>','c_admin/getContacts',message);
                                        }

                                    }
                                    else if(survey=='ch'){
                                        if(fdata.currentStep=='section-9'){
                                            //alert('Yes');
                                            //$(form_id).formwizard('reset');
                                            //$(form_id).formwizard('show','No');
                                            // console.log($(form_id).formwizard('state'));
                                            $(".form-container").load('<?php echo base_url();?>c_load/survey_complete',function(){
                                                window.location='<?php echo base_url();?>/assessment'; });

                                            message = fac_name +' in ' +fac_district+ ' District, ' +fac_county+ ' County, has completed the <?php echo strtoupper($this->session->userdata("survey"));?> Survey.' ;
                                            console.log(message);
                                            runNotification('<?php echo base_url(); ?>','c_admin/getContacts',message);
                                        }
                                    }
                                    else{
                                        if(fdata.currentStep=='section-5'){
                                            //alert('Yes');
                                            //$(form_id).formwizard('reset');
                                            //$(form_id).formwizard('show','No');
                                            // console.log($(form_id).formwizard('state'));
                                            $(".form-container").load('<?php echo base_url();?>c_load/survey_complete',function(){
                                                window.location='<?php echo base_url()?>/assessment'; });

                                            message = fac_name +' in ' +fac_district+ ' District, ' +fac_county+ ' County, has completed the <?php echo strtoupper($this->session->userdata("survey"));?> Survey.' ;
                                            console.log(message);
                                            runNotification('<?php echo base_url(); ?>','c_admin/getContacts',message);
                                        }
                                    }

                                });
                            }else{
                                $("#data").html("");
                                alert("An unknown error occurred, try retaking the survey later on. Kindly report this incidence.");
                                return false;
                            }

                            return data; //return true to make the wizard move to the next step, false will cause the wizard to stay on the current step
                        }

                    };


                });

                $(form_id).formwizard("option", "remoteAjax", remoteAjax); // set the remoteAjax option for the wizard



                $(form_id).bind("before_step_shown", function(event, data){

                    //alert(form_id);
                    if(form_id=="#mch_tool"){
                        if(data.previousStep=='section-6'){
                            //alert('yes');
                            if(data.currentStep=='No'){
                                $("#data").fadeTo(5000,0);
                                $('#sectionNavigation').hide();

                            }
                        }else if(data.currentStep=='section-6'){
                            //$(form_id).formwizard("destroy");
                            $('#back').prop('disabled',true);
                        }else{
                            $('#sectionNavigation').show();
                        }

                    }else{
                        if(data.previousStep=='section-1'){
                            //alert('yes');
                            if(data.currentStep=='No'){
                                $("#data").fadeTo(5000,0);
                                //$('#sectionNavigation').hide();
                                $(".form-container").load('<?php echo base_url();?>c_load/survey_complete',function(){
                                    window.location='<?php echo base_url();?>/assessment'; });

                            }
                        }else if(data.currentStep=='section-6'){
                            //$(form_id).formwizard("destroy");
                            $('#back').prop('disabled',true);
                        }else{
                            $('#sectionNavigation').show();
                        }
                    }
                });

                //check if deliveries are conducted
                $('#facDeliveriesDone').change(function(){
                    if($(this).val()=="Yes" || $(this).val()=="" ){
                        //show next section, hide this section
                        $('#delivery_centre').find('input').prop('disabled',true);
                        $('#delivery_centre').hide();

                        //alert('Y');
                    }else if($(this).val()=="No"){
                        //show the follow up qn
                        $('#delivery_centre').find('input').prop('disabled',false);
                        $('#delivery_centre').show();

                        //alert('N');
                    }
                });

                //fixed heading function
                function UpdateTableHeaders() {
                    $(".persist-area").each(function() {

                        var el             = $(this),
                            offset         = el.offset(),
                            scrollTop      = $(window).scrollTop(),
                            floatingHeader = $(".floatingHeader", this)

                        if ((scrollTop > offset.top) && (scrollTop < offset.top + el.height())) {
                            floatingHeader.css({
                                "visibility": "visible"
                            });
                        } else {
                            floatingHeader.css({
                                "visibility": "hidden"
                            });
                        };
                    });
                }

                $(function() {

                    var clonedHeaderRow;

                    $(".persist-area").each(function() {
                        clonedHeaderRow = $(".persist-header", this);
                        clonedHeaderRow
                        .before(clonedHeaderRow.clone())
                        .css("width", clonedHeaderRow.width())
                        .addClass("floatingHeader");

                    });

                    $(window)
                    .scroll(UpdateTableHeaders())
                    .trigger("scroll");

                });

                /*
                                 	Get Section
                                  */
                //$(form_id).formwizard('show','section-2');

                the_url='<?php echo base_url();?>c_load/getFacilitySection/<?php echo $this->session->userdata("survey");?>/'+facilityMFL+'/<?php echo $this->session->userdata("survey_category");?>';
                $.ajax({
                    type:'GET',
                    url:the_url,
                    dataType:'json',
                    beforeSend: function(){

                    },
                    success: function(data){
                        survey = '<?php echo $this->session->userdata("survey");?>';
                        //alert(data);

                        (data!='') ? $(form_id).formwizard('show','section-'+(data+1)):$(form_id).formwizard('show','section-1');


                        //console.log(data);

                    }}
                      );

            }//--end of function break_form_to_steps(form_id)
            function getDistrictData(base_url, county, survey_type, survey_category) {
            $.ajax({
                url: base_url + 'c_analytics/getCountyData/' + county + '/' + survey_type + '/' + survey_category + '/table',
                beforeSend: function(xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function(data) {
                    obj = jQuery.parseJSON(data);
                    console.log(obj);
                    $('#targeted').text(obj[0].actual);
                    $('#finished').text(obj[0].reported);
                    $('#not-finished').text((obj[0].actual)-(obj[0].reported));
                    $('#not-started').text(obj[0].notstarted);
                    var percentage = Math.round((obj[0].reported / obj[0].actual * 100), 2);
                    $('#county_progress .progress-bar').text(percentage + '%');
                    $('#county_progress .progress-bar').attr('aria-valuenow', percentage);
                    $('#county_progress .progress-bar').css('width', percentage + '%');
                }
            });
        }

            function check(el)
            {

                id = $(el).attr("id");
                name = $(el).attr("name");


                if($('#'+id).prop("checked"))
                {
                    $('.'+id).attr("readonly",false);
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'mchtreatmentnew['+name+'][]';
                    input.value = el.value;
                    document.getElementById("chells").appendChild(input);

                }
                else{
                    $('.'+id).attr("readonly",true);
                    $('.'+id).attr("value", 0);
                }

            }



            /*---------------------end form wizard functions----------------------------------------------------------------*/

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
