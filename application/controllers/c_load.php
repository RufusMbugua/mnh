<?php
class C_Load extends MY_Controller {
    var $rows, $facilityList,$combined_form, $message;

    public function __construct() {
        parent::__construct();
        //print var_dump($this->tValue); exit;
        // var_dump($this->session->userdata);die;
        $this -> rows = '';
        $this -> combined_form;

    }

    public function getFacilityDetails() {
        /*retrieve facility info if any*/
        $this -> load -> model('m_mnh_survey');
        if (($this -> m_mnh_survey -> retrieveFacilityInfo($this -> input -> get_post('facilityMFL', TRUE))) == true) {
            //retrieve existing data..else just load a blank form
            //set facility code into the session
            $new_data = array('facilityMFL' => $this -> input -> get_post('facilityMFL', TRUE));
            $this -> session -> set_userdata($new_data);
            print $this -> m_mnh_survey -> formRecords;
        }
    }

    public function suggestfac_name() {
        $this -> load -> model('m_autocomplete');
        $fac_name = strtolower($this -> input -> get_post('term', TRUE));
        //term is obtained from an ajax call

        if (!strlen($fac_name) < 2)

            //echo $fac_name;

            try {
                $this -> rows = $this -> m_autocomplete -> getAutocomplete(array('keyword' => $fac_name));
                //die (var_dump($this->rows));
                $json_data = array();

                //foreach($this->rows as $key=>$value)
                //array_push($json_data,$value['fac_name']);
                foreach ($this->rows as $value) {
                    array_push($json_data, $value -> fac_name);

                    //print $key.' '.$value.'<br />';
                    //$json_data=array('code'=>$value->fac_mfl,'name'=>$value->fac_name);
                }
                print json_encode($json_data);
                //die;

            } catch(exception $ex) {
                //ignore
                //$ex->getMessage();
            }

    }

    /**
     * [startSurvey description]
     * @param  [type] $survey_type     [description]
     * @param  [type] $survey_category [description]
     * @param  [type] $fac_mfl         [description]
     * @param  [type] $survey_year     [description]
     * @return [type]                  [description]
     */
    public function startSurvey($survey_type,$survey_category,$fac_mfl,$survey_year){

        $result          =$this->db->get_where('survey_types',array('st_name'=>$survey_type));
        $result          =$result->result_array();
        $survey_type     =$result[0]['st_id'];

        $result          =$this->db->get_where('survey_categories',array('sc_name'=>$survey_category));
        $result          =$result->result_array();
        $survey_category =$result[0]['sc_id'];

        $data  =array('ss_year'=>$survey_year,'st_id'=>$survey_type,'sc_id'=>$survey_category,'fac_id'=>$fac_mfl);
//echo '<pre>';print_r($data);echo '</pre>';die;
        $count =$this->checkifExists($data,'survey_status');
        if($count==0){
            $this->db->insert('survey_status',$data);
        }
        else{

        }

        $result =$this->db->get_where('survey_status',array('ss_year'=>$survey_year,'st_id'=>$survey_type,'sc_id'=>$survey_category,'fac_id'=>$fac_mfl));
        $result = $result->result_array();
        $ss_id  =$result[0]['ss_id'];
        $data = array('survey_status'=>$ss_id,'facilityMFL'=>$fac_mfl);
        $this->session->set_userdata($data);

        $result =$this->db->get_where('facilities',array('fac_mfl'=>$fac_mfl));
        $result = $result->result_array();

        // print_r($result);die;

        echo json_encode($result);

    }
/**
 * [checkifExists description]
 * @param  [type] $data  [description]
 * @param  [type] $table [description]
 * @return [type]        [description]
 */
    public function checkifExists($data,$table) {
        $this -> db -> like($data);
        $this -> db -> from($table);
        $count = $this -> db -> count_all_results();
        return (int)$count;

    }

    public function getFacilitySection($survey,$fac_mfl,$survey_category){
        $section = $this->getSection($survey,$fac_mfl,$survey_category);
        echo $section;
    }


    public function suggest() {
        $this -> load -> model('m_autocomplete');
        //$fac_name=$this->input->post('username',TRUE);

        try {
            $this -> rows = $this -> m_autocomplete -> getAllFacilityNames();
            //die(var_dump($this->rows));
            $json_data = array();

            foreach ($this->rows as $key => $value)
            //array_push($json_names,$value['fac_name']);
                $json_data = array('code' => $value['fac_mfl'], 'name' => $value['fac_name']);
            print json_encode($json_data);
        } catch(exception $ex) {
            //ignore
            $ex -> getMessage();
        }

    }

    public function get_mnh_form() {
        $this -> combined_form .= '<h5 id="status"></h5>

                <form class="bbq" name="mnh_tool" id="mnh_tool" method="POST">

  				 <p id="data" class="feedback"></p>
                 <!--h3 align="center">COMMODITY, SUPPLIES AND EQUIPMENT ASSESSMENT</h3-->
                 <p style="color:#488214;font-size:20px;font-style:bold">You are currently taking ' . (((strtoupper($this -> session -> userdata('survey'))) == 'CH') ? 'Child Health' : 'Maternal and Newborn Health') . ' Survey.</p>
                 <div id="section-1" class="step">
                 <input type="hidden" name="step_name" value="section-1"/>
                  <p style="display:true" class="message success">SECTION 1 of 7: FACILITY INFORMATION</p>
                <table class="centre" >

               <thead><th colspan="9">FACILITY INFORMATION</th></thead>

            <tr>
            <td>Facility Name </td>
            <td>
            <!--input type="text" id="fac_name" name="fac_name" class="cloned" size="40" disabled/-->
            <label id="facilityName"  size="40" ></label>
            <input type="hidden" name="facilityMFLCode" id="facilityMFLCode" />
            <input type="hidden" name="facilityHName" id="facilityHName" />
            </td>
            <td>Facility Level </td>
            <td>
            <!--input type="text" id="facilityLevel" name="facilityLevel" class="cloned"  size="40"/-->
            <select name="facilityLevel" id="facilityLevel" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Level</option>
                            ' . $this -> selectFacilityLevel . '
                        </select>
            </td><td>County </td>
            <td>
            <select name="fac_county" id="fac_county" class="cloned" style="width:85%">
                            <option value="" selected="selected">Select County</option>
                            ' . $this -> selectCounties . '
                        </select>
            </td>
            </tr>
            <tr>
            <td>Facility Type </td><td>
            <select name="facilityType" id="facilityType" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Type</option>
                            ' . $this -> selectFacilityType . '
                        </select>

            </td>
            <td>Owned By </td><td>
            <select name="facilityOwnedBy" id="facilityOwnedBy" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Owner</option>
                            ' . $this -> selectFacilityOwner . '
                        </select>
            </td>

            <td>District/Sub County </td><td>
            <select name="fac_district" id="fac_district" class="cloned" style="width:85%">
                            <option value="" selected="selected">Select District/Sub County</option>
                            ' . $this -> selectDistricts . '
                        </select>
            </td>
            </tr>

        </table>
        <table class="centre">
        <thead>
        <th colspan="12" >FACILITY CONTACT INFORMATION</th>
        </thead>
        <tr >
            <th scope="col" colspan="2" >CADRE</th>
            <th>NAME</th>
            <th >MOBILE</th>
            <th >EMAIL</th>
        </tr>'.$this -> facilitycontactinformation .'
        </table>
        <table>
            <tr>
                <th> DOES THIS FACILITY CONDUCT DELIVERIES?</th>
            </tr>
                <tr><th colspan ="8"><select name="DeliveriesDone" id="DeliveriesDone" class="cloned">
                <option value="" selected="selected">Select One</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option></th></tr>
            </tr>
        </table>
        <table>
        <thead>
            <tr>
                <th colspan ="12">IF NO, WHAT ARE THE MAIN REASONS FOR NOT CONDUCTING DELIVERIES? </br>(multiple selections allowed)</th>
            </tr>
            <tr>
                <th colspan ="2">Inadequate skill</th>
                <th colspan ="2">Inadequate staff</th>
                <th colspan ="2"> Inadequate infrastructure </th>
                <th colspan="2">Inadequate Equipment</th>
                <th colspan ="2"> Inadequate commodities and supplies</th>
                <th colspan ="2"> Other (Please specify)</th>
            </tr>
         </thead>
                <tr>'.$this -> deliveriessection.'</tr>
            </table>
        <table>
            <thead>
                    <th colspan="2" >PROVISION OF Nurses</th>
                </thead>
                <tr>
                    <th >QUESTION</th>
                    <th>RESPONSE</th>
                </tr>

            ' . $this -> question['nur'] . '
        </table>


        <table>
            <thead>

                    <th colspan="2" >PROVISION OF Beds</th>
            </thead>
                <tr>
                    <th >QUESTION</th>
                    <th>RESPONSE</th>

                </tr>

            ' . $this -> beds . '
        </table>
        <table>
            <thead>

                    <th colspan="2" >PROVISION OF Services</th>
            </thead>
                <tr>
                    <th >QUESTION</th>
                    <th>RESPONSE</th>

                </tr>

            ' . $this -> services . '
        </table>


        <table>
        <tr>
        <th colspan="12" >Health Facility Management</th>
        </tr>
        <tr>
        <th colspan="7">QUESTION</th>
        <th colspan="5">RESPONSE</th>
        </tr>
        ' . $this ->  mnhCommitteeAspectSection  . '
    </table>

    </div><!--\.delivery_cenre-->

    </div><!--\.the section-1 -->



    <div id="section-2" class="step">
    <input type="hidden" name="step_name" value="section-2"/>
     <p style="display:true" class="message success">SECTION 2 of 7: DELIVERIES CONDUCTED DATA, PROVISION OF BEmONC FUNCTIONS</p>
    <table class="centre">

    <thead>
    <th colspan="7" >INDICATE THE NUMBER OF DELIVERIES CONDUCTED IN THE FOLLOWING PERIODS </th></thead>
    '. $this -> monthlydeliveries. '
    </table>

    <table class="centre">
        <thead>
            <th colspan="14" >PROVISION OF Basic Emergency Obstetric Neonatal Care(BEmONC) SIGNAL FUNCTIONS</th>
                <tr><td style="background:#fff" colspan="13"><p class="instruction">
        * Verify this information by looking at patients records: 5 Patients Files, Registers and Partograph
        </p></td></tr>
        </thead>
            <th  colspan="7">SIGNAL FUNCTION</th>
            <th   colspan="2"> WAS IT CONDUCTED? </th>
            <th  colspan="5">INDICATE <span style="text-decoration:underline">PRIMARY</span> CHALLENGE</th>

        </tr>' . $this -> signalFunctionsSection . '
    </table>

    <table class="centre">
        <thead>
            <th colspan="12" >PROVISION OF Comprehensive Emergency Obstetric and Newborn Care (CEmONC) SERVICES IN THE LAST THREE MONTHS</th>
        <tr><td style="background:#fff" colspan="13"><p class="instruction">
        * Verify this information by looking at patients records: 5 Patients Files, Registers and Partograph
        </p></td></tr>
        </thead>


            <th colspan="7">QUESTION</th>
            <th colspan="5">RESPONSE</th>


        </tr>' . $this -> mnhCEOCAspectsSection . '
    </table>
    <table >
            <thead>
                    <th colspan="12" >PROVISION OF HIV Testing and Counselling</th>
                <tr><td style="background:#fff" colspan="13"><p class="instruction">
        * Verify this information by looking at patients records: 5 Patients Files and Registers
        </p></td></tr>
                </thead>

                    <th colspan="7">QUESTION</th>
                    <th colspan="5">RESPONSE</th>



            ' . $this -> mnhHIVTestingAspectsSection . '
        </table>

        <table >
            <thead>
                    <th colspan="2" >PROVISION OF Newborn Care</th>
                    </thead>
                <tr>
                    <th colspan="1">QUESTION</th>
                    <th colspan="1">RESPONSE</th>
                </tr>


            ' . $this -> mnhNewbornCareAspectsSection . '
        </table>
        <table >
            <thead>
                <tr>
                    <th colspan="2" >PROVISION OF Kangaroo Mother Care</th>

                </tr>
                <tr>
                    <th colspan="1">QUESTION</th>
                    <th colspan="1">RESPONSE</th>
                </tr>
</thead>


            ' . $this -> mnhKangarooMotherCare . '
        </table>
        <table >
            <thead>

                    <th colspan="12" >Preparedness for Delivery</th>

                <tr>
                    <th colspan="12" style="background=#fff">
                    <strong>Criteria : </strong>Adult Resuscitation Kit Complete, Working and Clean	; Newborn Resuscitation Kit Complete, working and clean;
                 Receiving Place ; Adequate Light ; No draft(cold air); Clean (delivery beds, recovery beds and all surfaces)	; Waste Disposal System
                ; Sterilization color-coded	;Sharp Container; Privacy; Delivery Kit
                    </th>
                </tr>
                </thead>

                    <th colspan="7">QUESTION</th>
                    <th colspan="5">RESPONSE</th>



            ' . $this -> mnhPreparednessAspectsSection . '
        </table>
        </div><!--\.section 2-->

    <div id="section-3" class="step">
    <input type="hidden" name="step_name" value="section-3"/>
    <p style="display:true" class="message success">SECTION 3 of 8: GUIDELINES, JOB AIDS AND TOOLS AVAILABILITY</p>
        <table >
            <thead>

                    <th colspan="12" >GUIDELINES AVAILABILITY</th>

                </thead>

                    <th colspan="6">ASPECTS</th>
                    <th colspan="3">RESPONSE</th>
                    <th colspan="3">QUANTITY</th>



            ' . $this -> question['guide'] . '
        </table>
        <table >
            <thead>
                <tr>
                    <th colspan="11" >JOB AIDS</th>
                </tr>
            </thead>
                <tr>
                    <th colspan="6">ASPECTS</th>
                    <th colspan="3">RESPONSE</th>
                    <th colspan="3">QUANTITY</th>

                </tr>

            ' . $this -> question['job'] . '
        </table>
        <table class="centre">

            <thead>
                <tr>
                    <th colspan="2" >DOES THE UNIT HAVE THE FOLLOWING TOOLS? </th>
                </tr>

                <tr>
                    <th style="width:700px">TOOL</th>
                    <th> RESPONSE </th>

                </tr>
            </thead>
            ' . $this -> mchIndicatorsSection['tl'] . '
        </table>
        </div>

        <div id="section-4" class="step">
    <input type="hidden" name="step_name" value="section-4"/>
    <p style="display:true" class="message success">SECTION 4 of 8: STAFF TRAINING</p>
        <table class="centre">
        <thead>
            <tr>
                <th colspan="13"  > HOW MANY STAFF MEMBERS HAVE BEEN TRAINED IN THE FOLLOWING?</th>
            </tr>
            <tr>

                <th rowspan ="2" style="text-align:left"> Clinical Staff</th>
                <th rowspan ="2" style="text-align:left">Total in Facility</th>
                <th rowspan ="2" style="text-align:left">Total Available On Duty</th>
                <th colspan="2" ># of Staff Trained in Basic Emergency Obstetric Neonatal Care (BEmONC)</th>
                <th colspan="2"># of Staff Trained in Focused Antenatal Care</th>
                <th colspan="2"># of Staff Trained in Post Natal Care</th>
                <th colspan="2"># of Staff Trained in Essential Newborn Care</th>
                <th rowspan ="2">
                    How Many Of The Total Staff Members
                    Trained  are still Working in the Marternity/ MCH/ Gynaecological Ward?</th>
            </tr>
            <tr>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
            </tr>
        </thead>
        '.$this->trainingGuidelineSection[1].'
        </table>

        <table class="centre">
        <thead>
            <tr>
                <th colspan="13"  > HOW MANY STAFF MEMBERS HAVE BEEN TRAINED IN THE FOLLOWING?</th>
            </tr>
            <tr>
                <th rowspan ="2" style="text-align:left"> Clinical Staff</th>
                <th rowspan ="2" style="text-align:left">Total in Facility</th>
                <th rowspan ="2" style="text-align:left">Total Available On Duty</th>
                <th colspan="2" ># of Staff Trained in Maternal and Perinatal death Surveillance and review (MPDSR)</th>
                <th colspan="2"># of Staff Trained in Standards-Based Management and Recognition(SBM-R)</th>
                <th colspan="2"># of Staff Trained in Uterine Balloon Tamponade</th>
                <th colspan="2"># of Staff Trained in PP IUCD</th>
                <th rowspan ="2">
                    How Many Of The Total Staff Members
                    Trained are still Working in the Marternity/ MCH/ Gynaecological Ward?</th>
            </tr>
            <tr>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
            </tr>
        </thead>
        '.$this->trainingGuidelineSection[2].'
        </table>
</div>
<div id="section-5" class="step">
    <input type="hidden" name="step_name" value="section-5"/>
    <p style="display:true" class="message success">SECTION 5 of 8: COMMODITY AVAILABILITY</p>


     <table>
            <thead>
            <th colspan="2" style="text-align:left"><strong>Main Supplier</th></strong>
            </thead>
        <tr>
        <td> Who is the main supplier of the commodities <strong>Below</strong> ?</td>
           <td>'.$this->selectMCHCommoditySuppliersPDF.'</td>
      </tr>
        </table>
    <table  class="centre persist-area" >
    <thead>

            <th colspan="15">INDICATE THE AVAILABILITY, LOCATION, SUPPLIER AND QUANTITIES ON HAND OF THE FOLLOWING COMMODITIES.INCLUDE REASON FOR UNAVAILABILITY. </th>

        </thead>
        <tr>
            <th rowspan="2">Commodity Name</th>
            <th rowspan="2">Commodity Unit</th>
            <th colspan="2" style="text-align:center"> Availability
             <strong></br>
            (One Selection Allowed) </strong></div></th>
            <th rowspan="2">
                Main Reason For  Unavailability
            </th>
            <th colspan="7" style="text-align:center"> Location of Availability  </BR><strong> (Multiple Selections Allowed)</strong></th>
            <th colspan="2">Available Quantities</th>


        </tr>
        <tr >
            <th >Available</th>
            <th>Not Available</th>
            <th>OPD</th>
            <th>MCH</th>
            <th>U5 Clinic</th>
            <th>Ward</th>
            <th>Pharmacy</th>
            <th>Other</th>
            <th>Not Applicable</th>
            <th><div style="width:100px">No. of Units</div></th>
            <th><div style="width:100px">Expiry Date</div></th>
        </tr>
        ' . $this -> commodityAvailabilitySection['mnh'] . '

    </table>
    </div><!--\.section-5-->

    <div id="section-6" class="step">
    <input type="hidden" name="step_name" value="section-6"/>
     <p style="display:true" class="message success">SECTION 6 of 8: COMMODITY USAGE</p>
    <table  class="centre" >
        <thead>
            <th colspan="11"> IN THE LAST 3 MONTHS INDICATE THE USAGE, NUMBER OF TIMES THE COMMODITY WAS NOT AVAILABLE.</BR>
            WHEN THE COMMODITY WAS NOT AVAILABLE WHAT HAPPENED? </th>
        </thead>

        </tr>
        <tr >
            <th rowspan="2" ><div style="width: 100px" >Commodity Name</div></th>
            <th rowspan="2">
            <div style="width: 40px" >
                Unit Size
            </div></th>
            <th>
            <div style="width: 40px" >
                Usage
            </div></th>
            <th>
            <div style="width: 100px" >
                Number Of Times the commodity was unavailable
            </div></th>
            <th colspan="5">
            <div style="width: 600px" >
                When the commodity was not available what happened?
                </br>
                <strong>(Multiple Selections Allowed)</strong>
            </div></th>

        </tr>
        <tr >
            <th colspan="1">Total Units Used</th>
            <th>Times Unavailable </th>

            <th colspan="1">
            <div style="width: 100px" >
            Patient purchased the commodity privately</div></th>
            <th colspan="1"> <div style="width: 100px" >
            Facility purchased the commodity privately
            </div></td>
            <th colspan="1"><div style="width: 100px" >
            Facility received the commodity from another facility</div></th>
            <th colspan="1"><div style="width: 100px" >
            The procedure was not conducted </div></th>
            <th colspan="1"><div style="width: 100px" > The procedure was conducted without the commodity
            </div></th>

        </tr>
        ' . $this -> commodityUsageAndOutageSection['mnh'] . '
        </table>
    </div><!--\.section-5-->




    <div id="section-7" class="step">
    <input type="hidden" name="step_name" value="section-7"/>
     <p style="display:true" class="message success">SECTION 7 of 8: I. EQUIPMENT AVAILABILITY AND FUNCTIONALITY</p>

        <table  class="centre" >
        <thead>
            <th colspan="10">INDICATE THE AVAILABILITY, LOCATION  AND FUNCTIONALITY OF THE FOLLOWING EQUIPMENT.</th>
        </thead>

        </tr>
        <tr>
            <th scope="col" >Equipment Name</th>

            <th colspan="2" style="text-align:center">Availability
             <strong></BR>
            (One Selection Allowed) </strong></th>
            <th colspan="4" style="text-align:center"> Location of Availability  </BR><strong> (Multiple Selections Allowed)</strong></th>
            <th colspan="2">Available Quantities</th>
        </tr>
        <tr >
            <td>&nbsp;</td>

            <td >Available</td>
            <td>Not Available</td>
            <td>Delivery room</td>
            <td>Pharmacy</td>
            <td>Store</td>
            <td>Other</td>
            <td>Fully-Functional</td>
            <!--td>Partially Functional</td-->
            <td>Non-Functional</td>
            </tr>
            ' . $this -> equipmentsSection['mnh'] .'

            </table>
            <table  class="centre" >
            <thead>
                <tr>
                    <th colspan="1" rowspan="2">Testing Supplies</th>

                    <th colspan="2" style="text-align:center"> Availability <strong></BR> (One Selection Allowed) </strong></th>
                    <th colspan="5" style="text-align:center"> Location of Availability </BR><strong> (Multiple Selections Allowed)</strong></th>


                </tr>
                <tr >
                    <th >Available</th>
                    <th>Not Available</th>
                    <th>OPD</th>
                    <th>MCH</th>
                    <th>U5 Clinic</th>
                    <th>Ward</th>
                    <th>Other</th>
                </tr>
            </thead>
            ' . $this -> mchSupplies['tes'] . '
        </table>
        <p style="display:true" class="message success">
            SECTION 7 of 8: II. KITS/SETS AVAILABILITY
        </p>

        <table>
            <thead>
            <tr>
                <th scope="2" rowspan="2">Delivery Kit Components</th>

                <th colspan="2" style="text-align:center">Availability <strong></br> (One Selection Allowed) </strong></th>
                <th colspan="4" style="text-align:center"> Location of Availability </br><strong> (Multiple Selections Allowed)</strong></th>
                <th colspan="2">Available Quantities</th>
            </tr>
            <tr >
                <th >Available</th>
                <th>Not Available</th>
                <th>Delivery room</th>
                <th>Pharmacy</th>
                <th>Store</th>
                <th>Other</th>
                <th>Fully-Functional</th>
                <!--td>Partially Functional</td-->
                <th>Non-Functional</th>
            </tr>
            </thead>
            ' . $this -> equipmentsSection['dke'] . '

        </table>
        <p style="margin-top:100px"></p>
<table>
    <tr>
        <tr>
            <th colspan="2">Main Supplier</th>
        </tr>
        <tr>
            <td>Who is the Main Supplier of the Supplies <strong>Below</strong>?</td>
            <td>'.$this->selectMCHCommoditySuppliersPDF.'</td>
        </tr>
    </tr>
    </table>
        <table>
            <thead>
                <tr>
                    <th colspan="10">INDICATE THE AVAILABILITY, LOCATION, SUPPLIER AND QUANTITIES ON HAND OF THE FOLLOWING SUPPLIES.INCLUDE REASON FOR UNAVAILABILITY.</th>
                </tr>
                <tr>
                    <th colspan="1" rowspan="2">Supplies Name</th>

                    <th colspan="2" style="text-align:center"> Availability <strong></br> (One Selection Allowed) </strong></th>
                    <th colspan="1" rowspan="2">Main Reason For  Unavailability</th>
                    <th colspan="4" style="text-align:center"> Location of Availability </br><strong> (Multiple Selections Allowed)</strong></th>
                    <th colspan="1" rowspan="2">Available Supplies</th>



                </tr>

                <tr>
                    <th >Available</th>
                    <th>Not Available</th>
                    <th>Delivery room</th>
                    <th>Pharmacy</th>
                    <th>Store</th>
                    <th>Other</th>
                </tr>
            </thead>
            ' . $this -> mchSupplies['mnh'] . '
        </table>


            <p style="display:true" class="message success">SECTION 7 of 8: III. RESOURCE AVAILABILITY</p>
        <table>
            <thead>
                <th colspan="10">INDICATE THE AVAILABILITY, LOCATION AND MAIN SOURCE OF THE FOLLOWING.</th>

            <tr>
                <th  rowspan="2">Resource Name</th>

                <th colspan="2" style="text-align:center"> Availability <strong></br> (One Selection Allowed) </strong></th>
                <th colspan="5" style="text-align:center"> Location of Availability </br><strong> (Multiple Selections Allowed)</strong></th>
                <th rowspan="2" > Main Source </th>


            </tr>
            <tr >
                <th >Available</th>
                <th>Not Available</th>
                <th>OPD</th>
                <th>MCH</th>
                <th>U5 Clinic</th>
                <th>Maternity</th>
                <th>Other</th>
            </tr>
            </thead>
            ' . $this -> suppliesMNHOtherSection . '
        </table>
        <table >
            <thead>
            <th colspan="14" >INDICATE THE STORAGE AND ACCESS TO WATER BY THE COMMUNITY </th>
                <tr>
            <th  colspan="7">ASPECT</th>
            <th   colspan="5"> RESPONSE </th>
            <th   colspan="2"> SPECIFY </th>

        </tr>
        </thead>' . $this -> mnhWaterAspectsSection . '
        </table>
 <table >
            <thead>
                <tr>
                    <th colspan="12" >PROVISION OF Waste Disposal</th>
                </tr>
                </thead>
                <tr>
                    <th colspan="7" style="width:35%">QUESTION</th>
                    <th colspan="5" style="width:65%;text-align:left">RESPONSE</th>
                </tr>

            ' . $this -> mnhWasteDisposalAspectsSection . '
        </table>


         <table  class="centre" >
        <thead><tr>
            <th colspan="6">INDICATE THE AVAILABILITY, LOCATION AND SUPPLIER OF THE FOLLOWING.</th></tr>
        </thead>
        <tr>
            <th rowspan="2">Resource Name</th>

            <th colspan="2" style="text-align:center"> Availability
             <strong></BR>
            (One Selection Allowed) </strong></th>
            <th rowspan="2">

                Main Supplier
            </th>
            <th rowspan="2">
                Main Source
            </th>
        </tr>
        <tr >
            <th >Available</th>
            <th>Not Available</th>
        </tr>

        ' . $this -> hardwareMNHSection . '
        </table>

     </div><!--\.section-7-->

     <div class="step" id="section-8">
     <input type="hidden" name="step_name" value="section-8"/>
     <p style="display:true" class="message success">SECTION 8 of 8: COMMUNITY STRATEGY</p>
        <table class="centre">
        <thead>
            <th colspan="2" >COMMUNITY STRATEGY </th>
        </thead>
            <th  style="width:35%">ASPECT</th>
            <th   style="width:65%;text-align:left"> RESPONSE </th>


        </tr>' . $this -> mnhCommunityStrategySectionPDF . '
    </table>


     </div>
     <div id="sectionNavigation" class="buttonsPane">
        <input title="To View Previous Section" id="back" value="View Previous Section" class="" type="reset"/>
        <input title="To Save This Section" id="submit" class=""  type="submit" name="post_form" value="Save and Go to the Next Section"/>
        </div>
    </form>';
        $data['form'] = $this -> combined_form;
        $data['form_id'] = 'form_dcah';
        $this -> load -> view('form', $data);
    }

    public function get_mch_form() {
        $this -> combined_form .= '<h5 id="status"></h5>

                <form class="bbq" name="mch_tool" id="mch_tool" method="POST">

  				 <p id="data" class="feedback"></p>
                 <p style="color:#488214;font-size:20px;font-style:bold">You are currently taking ' . (((strtoupper($this -> session -> userdata('survey'))) == 'CH') ? 'Child Health' : 'Maternal and Newborn Health') . ' Survey</p>
                 <div id="section-1" class="step">
                 <input type="hidden" name="step_name" value="section-1"/>
                  <p style="display:true" class="message success">SECTION 1 of 7: FACILITY INFORMATION</p>
                <table class="centre" >

               <thead><th colspan="9">FACILITY INFORMATION</th></thead>

            <tr>
            <td>Facility Name </td>
            <td>
            <!--input type="text" id="fac_name" name="fac_name" class="cloned" size="40" disabled/-->
            <label id="facilityName"  size="40" ></label>
            <input type="hidden" name="facilityMFLCode" id="facilityMFLCode" />
            <input type="hidden" name="facilityHName" id="facilityHName" />
            </td>
            <td>Facility Level </td>
            <td>
            <!--input type="text" id="facilityLevel" name="facilityLevel" class="cloned"  size="40"/-->
            <select name="facilityLevel" id="facilityLevel" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Level</option>
                            ' . $this -> selectFacilityLevel . '
                        </select>
            </td><td>County </td>
            <td>
            <select name="fac_county" id="fac_county" class="cloned" style="width:85%">
                            <option value="" selected="selected">Select County</option>
                            ' . $this -> selectCounties . '
                        </select>
            </td>
            </tr>
            <tr>
            <td>Facility Type </td><td>
            <select name="facilityType" id="facilityType" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Type</option>
                            ' . $this -> selectFacilityType . '
                        </select>

            </td>
            <td>Owned By </td><td>
            <select name="facilityOwnedBy" id="facilityOwnedBy" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Owner</option>
                            ' . $this -> selectFacilityOwner . '
                        </select>
            </td>

            <td>District/Sub County </td><td>
            <select name="fac_district" id="fac_district" class="cloned" style="width:85%">
                            <option value="" selected="selected">Select District/Sub County</option>
                            ' . $this -> selectDistricts . '
                        </select>
            </td>
            </tr>

        </table>
        <table>
    <thead>
        <th colspan="12">ASSESSOR INFORMATION </th>
    </thead>
    <tbody>
        <tr>
            '. $this -> mchassessorinfo .'
        </tr>
    </tbody>
</table>
<p class="instruction">
        * For Facility Type(Dispensary, Health Centre etc.)
        * For Owned By (Public/Private/FBO/MOH/NGO)
</p>
        <table class="centre">
        <thead>
        <th colspan="12" >HR INFORMATION</th>
        </thead>
        <tr >
            <th colspan = "2">CADRE</th>
            <th>NAME</th>
            <th >MOBILE</th>
            <th >EMAIL</th>
        </tr>
        '.$this -> facilitycontactinformation .'
    </table>
    <table class="centre">
        <thead>
            <tr>
                <th colspan="13"  > HOW MANY STAFF MEMBERS HAVE BEEN TRAINED IN THE FOLLOWING?</th>
            </tr>
        </thead>
            <tr>

                <th rowspan ="2" style="text-align:left"> Clinical Staff</th>
                <th rowspan ="2" style="text-align:left">Total in Facility</th>
                <th rowspan ="2" style="text-align:left">Total Available On Duty</th>
                <th colspan="2"># of Staff Trained in IMCI</th>
                <th colspan="2"># of Staff Trained in ICCM</th>
                <th colspan="2"># of Staff Trained in Enhanced Diarrhoea Management</th>
                <th colspan="2"># of Staff Trained in Diarrhoea and Pnemonia CMEs for U5s</th>
                <th rowspan ="2">
                    How Many Of The Total Staff Members
                    Trained in IMCI are still Working in Child Health Unit?</th>
            </tr>
            <tr>
                <th style="text-align:left;width:50px">BEFORE 2010</th>
                <th style="text-align:left;width:50px">AFTER 2010</th>
                <th style="text-align:left;width:50px">BEFORE 2013</th>
                <th style="text-align:left">AFTER 2013</th>
                <th style="text-align:left">BEFORE 2010</th>
                <th style="text-align:left">AFTER 2010</th>
                <th style="text-align:left">BEFORE 2014</th>
                <th style="text-align:left">AFTER 2014</th>
            </tr>

        '.$this->mchTrainingGuidelineSection.'

    </table>
    <table>
  <table>
            <thead>

                    <th colspan = "12">HEALTH SERVICES</th>
            </thead>
                <tr>
                    <th >QUESTION</th>
                    <th>RESPONSE</th>

                </tr>
            '.$this->HealthSection.'
        </table>
    <table>
        <thead><th colspan = "12"> INFRASTRACTURE: IMCI CONSULTATION ROOM</th></thead>
        <tbody>
        <thead>
        <th colspan="12">Has IMCI consultation room been established?</th>
        </thead>
        <tr>
        </tr>' . $this -> question['imci'] . '
        </tbody>
       </table>

    </div><!--\.the section-1 -->

    <!--div id="No" class="step"--><!--end of assessment message section-->
    <!--input type="hidden" name="step_name" value="end_of_assessment"/>
    <div class="block">
            <p align="left" style="font-size:16px;color:#AA1317; font-weight:bold">Assessment Complete</p>
            <p id="data" class="message success">Thanks for your participation.<br></p><br>
            <p class="message success">' . anchor(base_url() . '/assessment', 'Select another Facility') . '</p>
            </div>
    </div--><!--\.end of assessment message section-->

    <div id="section-2" class="step">
    <input type="hidden" name="step_name" value="section-2"/>
     <p style="display:true" class="message success">SECTION 2 of 7: GUIDELINES, JOB AIDS AND TOOLS</p>

     <table class="centre">
        <thead>
            <th colspan="3" >GUIDELINES AND JOB AIDS AVAILABILITY</th>
        </thead>


            <th  style="width:35%">ASPECT</th>
            <th   style="width:10.5%;text-align:left"> RESPONSE </th>
            <th   style="width:52.5%;text-align:left"> If <strong>Yes</strong>, Indicate Total Quantities Available </th>


        </tr>' . $this -> mchGuidelineAvailabilitySection . '
    </table>
      <table class="centre">
            <thead>
                <th colspan="3" >DOES THE UNIT HAVE THE FOLLOWING TOOLS?</th>
            </thead>
                <th  style="width:35%">TOOLS</th>
                <th colspan = "2" style="width:65%;text-align:left">RESPONSE</th>
            </tr>' . $this -> mchIndicatorsSection['ror'] . '
        </table>
    <p class="instruction">
        <strong style="text-decoration:underline">Guide for the 1st part(Commodity List):</strong></br>

Please select all the treatment received. Multiple selection is allowed.
Click on the selected treatment to view the commodities and select accordingly.</br>


<strong style="text-decoration:underline">Guide for the 2nd part(Specified List with Numbers):</strong></br>
Indicate the total # of children that received the following treatment. </br>

<strong>NB: </strong> Tick a Treatment to enable the Number Field.</br>
    </p>

        <table class="centre">
            <tbody>
                <th colspan="2">TOTAL U5 CHILDREN SEEN IN THE LAST 1 MONTH</th>
                <td>'.$this -> totalsRows['totalu5'].'
                <th colspan = "2"></th>

            <tr>
                <th colspan = "5">Classification</th>
            </tr>
            <tr>
                <th colspan="2">Diarrhoea Total</th>
                <td>'.$this -> totalsRows['diatotal'].'
                <th colspan = "2"></th>
            </tr>
            </tbody>
            <tr>
            <td>Severe Dehydration: '.$this -> totalsRows['SevereDehydration'].'
            <td>Some Dehydration: '.$this -> totalsRows['SomeDehydration'].'
            <td>No Dehydration: '.$this -> totalsRows['NoDehydration'].'
            <td>Dysentry: '.$this -> totalsRows['Dysentry'].'
            <td>No Classification: '.$this -> totalsRows['NoClassification'].'
            </tr>
            <tr>
                <td>
                    <style type = "text/css">
                        .treatment
                        {
                            cursor: pointer;
                        }
                    </style>
                    <div class = "treatmentdropdownarea" id ="treat">
                    <span id = "malTreatmentSection">&nbsp</span>'
                    .$this -> severediatreatmentMCHSection.'
                    </div>
                </td>
                <td>
                    <div class = "treatmentdropdownarea" id ="treat">
                    <span id = "malTreatmentSection">&nbsp</span>'
                    .$this -> somedehydrationdiaTreatmentMCHSection.'
                    </div>
                </td>
                <td>
                    <div class = "treatmentdropdownarea" id ="treat">
                    <span id = "malTreatmentSection">&nbsp</span>'
                    .$this -> nodehydrationdiaTreatmentMCHSection.'
                    </div>
                </td>
                <td>
                    <div class = "treatmentdropdownarea" id ="treat">
                    <span id = "malTreatmentSection">&nbsp</span>'
                    .$this -> dysentrydiaTreatmentMCHSection.'
                    </div>
                </td>
                <td>
                    <div class = "treatmentdropdownarea" id ="treat">
                    <span id = "malTreatmentSection">&nbsp</span>'
                    .$this -> noclassificationdiaTreatmentMCHSection.'
                    </div>
                </td>
            </tr>
        </table>
        <table class="centre">
                <tbody>

                    <tr>
                    <th colspan = "2">Pneumonia Total: </th>
                    <td>'.$this -> totalsRows['pnetotal'].'
                    <th colspan = "3"></th>
                    </tr>
                </tbody>
                <tr>
                    <td colspan = "3">Severe Pneumonia: '.$this -> totalsRows['SeverePneumonia'].'
                    <td colspan = "3">Pneumonia: '.$this -> totalsRows['Pneumonia'].'
                </tr>
                <tr>
                <td colspan = "3">
                <div class = "treatmentdropdownarea">
                <span id = "pneTreatmentSection">&nbsp</span>'
            .$this -> mchpneumoniasevereTreatmentSection.
            '</div>
                </td>
                <td colspan = "3">
                <div class = "treatmentdropdownarea">
                <span id = "pneTreatmentSection">&nbsp</span>'
            .$this -> mchpneumoniaTreatmentSection.
            '</div>
                </td>
                </tr>

        </table>
        <table class="centre">
            <tbody>
                    <tr>
                    <th colspan = "2">Malaria Total: </th>
                    <td>'.$this -> totalsRows['malariatotal'].'
                    <th colspan = "3"></th>
                    </tr>
                </tbody>
                <tr>
                    <td colspan = "3">Confirmed: '.$this -> totalsRows['ConfirmedMalaria'].'
                    <td colspan = "3">Not Confirmed(Include Clinical Malaria):'.$this -> totalsRows['NotConfirmedMalaria'].'
                <tr>
                <td colspan = "3">
                <div class = "treatmentdropdownarea">
                <span id = "malTreatmentSection">&nbsp</span>'
            .$this -> mchmalariaconfrimedtreatmentSection.
            '</div>
                </td>
                <td colspan = "3">
                <div class = "treatmentdropdownarea" >
                <span id = "malTreatmentSection_2">&nbsp</span>'
            .$this -> mchmalarianotconfrimedtreatmentSection.
            '</div>
                </td>
                </tr>
        </table>


        <table class="centre">

            <tbody>
            <tr>
                <th colspan="2" >Others Total:</th>
                <td>'.$this -> totalsRows['OtherTotal'].'
            </tr>

            </tbody>
        </table>
        <table class="centre">
            <thead>
                <th colspan="3" >ARE THE FOLLOWING DANGER SIGNS ASSESSED IN ONGOING SESSION FOR A CHILD</th>
            </thead>
                <th  style="width:35%">SERVICE</th>
                <th colspan = "2" style="width:65%;text-align:left">RESPONSE</th>
            </tr>' . $this -> mchIndicatorsSection['sgn'] . '
        </table>
        <table class = "centre">
            <thead>
                <th colspan = "6">ASSESSMENT FOR THE MAIN SYMPTOMS IN AN ONGOING SESSION FOR A CHILD</th>
            </thead>
            <tbody>
                <th colspan = "3">DOES THE CHILD HAVE THE SYMPTOM BELOW?</th>
                <th colspan = "3">
                    <input type = "radio" name = "mchCheckSympton" value = "yes" id = "sgncheck" class = "Options">Yes</input>
                    <input type = "radio" name = "mchCheckSympton" value = "no" id = "sgncheck" class = "Options">No</input>
                </th>
                <p class = "instruction">* If NO proceed to the next symptom.</p>
            </tbody>
            <tbody>
                <th>Symptom</th>
                <th colspan = "2">HCW Response</th>
                <th colspan = "2">Assessor Response</th>
            </tbody>
        	<tr>
            	<th>1. Cough / Difficulty Breathing</th>
            	<th>Response</th>
            	<th>Findings</th>
            	<th>Response</th>
            	<th>Findings</th>
        	</tr>

     		' . $this -> mchIndicatorsSection['pne'] . '
     		<th colspan = "6">Treatment</th>
     		<tr>
     		<td colspan = "6">
                <div class = "treatmentdropdownarea" class = "pneumoniatreatments">
                <span id = "pneTreatmentSection">&nbsp</span>'
            .$this -> othertreatmentsection.
            '</div>
            </td>
            </tr>
        </table>
        <table>
            <tr>
                <th colspan = "3">DOES THE CHILD HAVE THE SYMPTOM BELOW?</th>
                <th colspan = "3">
                    <input type = "radio" name = "mchCheckSympton" value = "yes">Yes</input>
                    <input type = "radio" name = "mchCheckSympton" value = "no">No</input>
                </th>
            </tr>
            <p class = "instruction">* If NO proceed to the next symptom.</p>
            <tr>
                <th>Symptom</th>
                <th colspan = "2">HCW Response</th>
                <th colspan = "2">Assessor Response</th>
            </tr>
            <tr>
            	<th>2. Diarrhoea</th>
            	<th>Response</th>
            	<th>Findings</th>
            	<th>Response</th>
            	<th>Findings</th>
        	</tr>
        	' . $this -> mchIndicatorsSection['dgn'] . '
        	<th colspan = "6">Treatment</th>
     		<tr>
     		<td colspan = "6">
                <div class = "treatmentdropdownarea" class = "pneumoniatreatments">
                <span id = "pneTreatmentSection">&nbsp</span>'
            .$this -> diaresponsetreatmentsection.
            '</div>
            </td>
            </tr>
        </table>
        <table class = "centre">
        <tr>
                <th colspan = "3">DOES THE CHILD HAVE THE SYMPTOM BELOW?</th>
                <th colspan = "3">
                    <input type = "radio" name = "mchCheckSympton" value = "yes">Yes</input>
                    <input type = "radio" name = "mchCheckSympton" value = "no">No</input>
                </th>
            </tr>
            <tr>
            <p class = "instruction">* If NO proceed to the next symptom.</p>
                <th>Symptom</th>
                <th colspan = "2">HCW Response</th>
                <th colspan = "2">Assessor Response</th>
            </tr>
            <tr>
            	<th>3. Fever</th>
            	<th>Response</th>
            	<th>Findings</th>
            	<th>Response</th>
            	<th>Findings</th>
        	</tr>
        	'. $this -> mchIndicatorsSection['fev'].'
        	<th colspan = "6">Treatment</th>
     		<tr>
     		<td colspan = "6">
                <div class = "treatmentdropdownarea" class = "pneumoniatreatments">
                <span id = "pneTreatmentSection">&nbsp</span>'
            .$this -> fevresponsetreatmentsection.
            '</div>
            </td>
            </tr>
        </table>

        <table class = "centre">
        <tr>
                <th colspan = "3">DOES THE CHILD HAVE THE SYMPTOM BELOW?</th>
                <th colspan = "3">
                    <input type = "radio" name = "mchCheckSympton" value = "yes">Yes</input>
                    <input type = "radio" name = "mchCheckSympton" value = "no">No</input>
                </th>
            </tr>
            <tr>
                <th colspan="6" >TOTAL U5 CHILDREN SEEN IN THE LAST 3 MONTHS OF THOSE, HOW MANY CAME IN WITH THE FOLLOWING</th>
            </tr>
            <tr>
            	<th>4. Ear Infection</th>
            	<th>Response</th>
            	<th>Findings</th>
            	<th>Response</th>
            	<th>Findings</th>
        	</tr>
        	'. $this -> mchIndicatorsSection['ear'].'
        	<th colspan = "6">Treatment</th>
     		<tr>
     		<td colspan = "6">
                <div class = "treatmentdropdownarea" class = "pneumoniatreatments">
                <span id = "pneTreatmentSection">&nbsp</span>'
            .$this -> earresponsetreatmentsection.
            '</div>
            </td>
            </tr>
        </table>

    </table>


    </div><!--\.section 2-->

    <div id="section-3" class="step">
    <input type="hidden" name="step_name" value="section-3"/>
     <p style="display:true" class="message success">SECTION 3 of 9: DOES THE HCW CHECK FOR THE FOLLOWING CONDITIONS</p>

     <table class="centre">
     <tr>
     <th rowspan = "2">Malnutrition</th>
     <th colspan = "2">HCW Response</th>
     <th colspan = "2">Assessor Response</th>
     </tr>
     <tr>
     <th>Response</th>
     <th>Findings</th>
      <th>Response</th>
     <th>Findings</th>
     </tr>
' . $this -> mchIndicatorsSection['mal'] . '
    </table>

    <table class="centre">
     <tr>
     <th rowspan = "2">Anaemia</th>
     <th colspan = "2">HCW Response</th>
     <th colspan = "2">Assessor Response</th>
     </tr>
     <tr>
     <th>Response</th>
     <th>Findings</th>
      <th>Response</th>
     <th>Findings</th>
     </tr>
' . $this -> mchIndicatorsSection['anm'] . '
    </table>

    <table class="centre">
     <tr>
     <th rowspan = "2">Condition</th>
     <th colspan = "2">HCW Response</th>
     <th colspan = "2">Assessor Response</th>
     </tr>
     <tr>
     <th>Response</th>
     <th>Findings</th>
      <th>Response</th>
     <th>Findings</th>
     </tr>
' . $this -> mchIndicatorsSection['con'] . '
    </table>
    </div><!--\.section-3-->


    <div id="section-4" class="step">
    <input type="hidden" name="step_name" value="section-4"/>
    <p class="message success">
    SECTION 4 of 9: COMMODITY AND BUNDLING AVAILABILITY
    </p>
    <table>
    <tr>
        <tr>
            <th colspan="2">Main Supplier</th>
        </tr>
        <tr>
            <td>Who is the Main Supplier of the Commodities <strong>Below</strong>?</td>
            <td>'.$this-> selectCommoditySuppliersPDF.'</td>
        </tr>
    </tr>
    </table>
     <table  class="centre persist-area" >
    <thead>

            <th colspan="14">INDICATE THE AVAILABILITY, LOCATION, SUPPLIER AND QUANTITIES ON HAND OF THE FOLLOWING COMMODITIES.INCLUDE REASON FOR UNAVAILABILITY. </th>

        </thead>
        <tr>
            <th rowspan="2" >Commodity Name</th>
            <th rowspan="2" >Commodity Unit</th>
            <th colspan="2" style="text-align:center"> Availability <strong></br> (One Selection Allowed) </strong></th>
            <th rowspan="2"> Main Reason For  Unavailability </th>
            <th colspan="7" style="text-align:center"> Location of Availability </br><strong> (Multiple Selections Allowed)</strong></th>
            <th rowspan="1" colspan="2" >Available Quantities</th>
        </tr>
        <tr>
            <th >Available</th>
            <th>Not Available</th>
            <th>OPD</th>
            <th>MCH</th>
            <th>U5 Clinic</th>
            <th>Ward</th>
            <th>Pharmacy</th>
            <th>Other</th>
            <th>Not Applicable</th>
            <th><div style="width:100px">No. of Units</div></th>
            <th><div style="width:100px">Expiry Date</div></th>
         </tr>
        ' . $this -> commodityAvailabilitySection['ch'] . '

    </table>
    <table>
    </table>
    <table  class="centre persist-area" >
    <thead>
        <tr>

            <th colspan="14">BUNDLING: INDICATE THE AVAILABILITY, LOCATION, SUPPLIER AND QUANTITIES ON HAND OF THE FOLLOWING COMMODITIES. </th>
        </tr>
        </thead>
        <tr>
            <td colspan="14" style="background:#ffffff">
                <p class="instruction" >* Include all expiry dates(coma-separated) in the format (DD-MM-YYYY)</p>
            </td>
        </tr>

        <tr>
            <th rowspan="2" >Commodity Name</th>
            <th rowspan="2" >Commodity Unit</th>
            <th colspan="2" style="text-align:center"> Availability <strong></br> (One Selection Allowed) </strong></th>
            <th rowspan="2"> Main Reason For  Unavailability </th>
            <th colspan="7" style="text-align:center"> Location of Availability </br><strong> (Multiple Selections Allowed)</strong></th>
            <th rowspan="1" colspan="2" >Available Quantities</th>
        </tr>
        <tr>
            <th >Available</th>
            <th>Not Available</th>
            <th>OPD</th>
            <th>MCH</th>
            <th>U5 Clinic</th>
            <th>Ward</th>
            <th>Pharmacy</th>
            <th>Other</th>
            <th>Not Applicable</th>
            <th><div style="width:100px">No. of Units</div></th>
            <th><div style="width:100px">Expiry Date</div></th>
         </tr>
        ' . $this -> commodityAvailabilitySection['bun'] . '

    </table>


    </div><!--\.section-4-->

    <div id="section-5" class="step">
    <input type="hidden" name="step_name" value="section-5"/>
    <p class="message success">
    SECTION 5 of 9: REVIEW OF RECORDS
        </p>



        <table class="centre">

        <thead>
        <tr>
            <th colspan="6" > (C) WHAT IS THE MAIN CHALLENGE IN ACCESSING <span style="text-decoration:underline">DATA FROM</span> U5 REGISTERS IN THE LAST 3 MONTHS</th></tr>
        </thead>
        '.$this -> selectAccessChallenges.'


    </table>
    <table class="centre">
        <thead>
            <th colspan="2" >ORAL REHYDRATION THERAPY CORNER ASSESSMENT </th>
        </thead>


            <th  style="width:35%">ASPECT</th>
            <th   style="width:65%;text-align:left"> RESPONSE </th>


        </tr>' . $this -> ortCornerAspectsSection . '
    </table>
           </div><!--\.section-5-->

    <div id="section-6" class="step">
        <input type="hidden" name="step_name" value="section-6"/>
        <p class="message success">
            SECTION 6 of 9: EQUIPMENT AVAILABILITY AND STATUS
        </p>



        <table  class="centre" >
            <thead>
                <tr>
                    <th colspan="10">INDICATE THE AVAILABILITY, LOCATION  AND FUNCTIONALITY OF THE FOLLOWING EQUIPMENT AT THE ORT CORNER.</th>
                </tr>
                </thead>
                <tr>
                    <th colspan="1" rowspan="2">Equipment Name</th>
                    <th colspan="2" style="text-align:center">Availability <strong></br> (One Selection Allowed) </strong></th>
                    <th colspan="5" style="text-align:center"> Location of Availability </br><strong> (Multiple Selections Allowed)</strong></th>
                    <th colspan="2">Available Quantities</th>
                </tr>
                <tr >
                    <th >Available</th>
                    <th>Not Available</th>
                    <th>OPD</th>
                    <th>MCH</th>
                    <th>U5 Clinic</th>
                    <th>Ward</th>
                    <th>Other</th>
                    <th>Fully-Functional</th>
                    <th>Non-Functional</th>
                </tr>

            ' . $this -> equipmentsSection['ort'] . '

        </table>
        </div><!--\.section-6-->

    <div id="section-7" class="step">
    <input type="hidden" name="step_name" value="section-7"/>
     <p style="display:true" class="message success">SECTION 7 of 9: SUPPLIES AVAILABILITY</p>
     <table>
    <tr>
        <tr>
            <th colspan="2">Main Supplier</th>
        </tr>
        <tr>
            <td>Who is the Main Supplier of the Supplies <strong>Below</strong>?</td>
            <td>'.$this-> selectMCHOtherSuppliersPDF.'</td>
        </tr>
    </tr>
    </table>
        <table  class="centre" >
        <thead>
            <th colspan="10">INDICATE THE AVAILABILITY, LOCATION AND SUPPLIER OF THE FOLLOWING.</th>
        </thead>
        <tr>
            <th style="text-align:center" rowspan="2"> Supply Name </th>
            <th colspan="2" style="text-align:center"> Availability
             <strong></BR>
            (One Selection Allowed) </strong></th>
            <th colspan="5" style="text-align:center"> Location of Availability  </BR><strong> (Multiple Selections Allowed)</strong></th>
            <!--th>Available Supplies</th-->
            <!--th scope="col">
            <div style="width: 100px" >
                Main Reason For  Unavailability
            </div></th-->

        </tr>
        <tr >
            <th >Available</th>
            <th>Not Available</th>
            <th>OPD</th>
            <th>MCH</th>
            <th>U5 Clinic</th>
            <th>Ward</th>
            <th>Other</th>

        </tr>' . $this -> mchSupplies['ch'] . '
        </table>
        <table  class="centre" >
        <tr>
            <th style="text-align:center" rowspan="2"> Testing Supplies </th>
            <th colspan="2" style="text-align:center"> Availability
             <strong></BR>
            (One Selection Allowed) </strong></th>
            <th colspan="5" style="text-align:center"> Location of Availability  </BR><strong> (Multiple Selections Allowed)</strong></th>
            <!--th>Available Supplies</th-->
            <!--th scope="col">
            <div style="width: 100px" >
                Main Reason For  Unavailability
            </div></th-->

        </tr>
        <tr >
            <th >Available</th>
            <th>Not Available</th>
            <th>OPD</th>
            <th>MCH</th>
            <th>U5 Clinic</th>
            <th>Ward</th>
            <th>Other</th>

            <!--td style="text-align:center">No.of Supplies</td-->
            <!--td></td-->



        </tr>' . $this -> mchSupplies['tst'] . '
        </table>
        </div><!--\.section-7-->

    <div id="section-8" class="step">
    <input type="hidden" name="step_name" value="section-8"/>
        <p style="display:true" class="message success">
            SECTION 8 of 9: RESOURCE AVAILABILITY
        </p>
            <table>
    <tr>
        <tr>
            <th colspan="2">Main Supplier</th>
        </tr>
        <tr>
            <td>Who is the Main Supplier of the Resources <strong>Below</strong>?</td>
            <td>'.$this-> selectMCHOtherSuppliersPDF.'</td>
        </tr>
    </tr>
    </table>
        <table  class="centre" >
            <thead>
                <th colspan="9">INDICATE THE AVAILABILITY, LOCATION AND SUPPLIER OF THE FOLLOWING.</th>

                <tr>
                    <th colspan="1" rowspan="2">Resource Name</th>
                    <th colspan="2" style="text-align:center"> Availability <strong></br> (One Selection Allowed) </strong></th>
                    <th colspan="5" style="text-align:center"> Location of Availability </br><strong> (Multiple Selections Allowed)</strong></th>


                </tr>
                <tr >
                    <th>Available</th>
                    <th>Not Available</th>
                    <th>OPD</th>
                    <th>MCH</th>
                    <th>U5 Clinic</th>
                    <th>Ward</th>
                    <th>Other</th>
                </tr>
            </thead>
            ' . $this ->  equipmentsSection['hwr']. '
        </table>
        </div><!--\.section-8-->
    <div id="section-9" class="step">
    <input type="hidden" name="step_name" value="section-9"/>
        <table class="centre">
        <thead>
            <th colspan="2" >COMMUNITY STRATEGY </th>
        </thead>



            <th  style="width:35%">ASPECT</th>
            <th   style="width:65%;text-align:left"> RESPONSE </th>


        </tr>' . $this -> mchCommunityStrategySection . '
    </table>
    </div><!--\.section-9-->


     <div id="sectionNavigation" class="buttonsPane">
        <button title="To View Previous Section" id="back" value="" class="awesome blue medium" type="reset"><i class="fa fa-chevron-left"></i>View Previous Section</button>
        <button title="To Save This Section" id="submit" class="awesome blue medium"  type="submit" name="post_form" value="">Save and Go to the Next Section<i class="fa fa-chevron-right"></i></button>
        </div>
    </form>';
        $data['form'] = $this -> combined_form;
        $data['form_id'] = 'form_dcah';
        $this -> load -> view('form', $data);
    }
public function get_hcw_form() {
        $this -> combined_form .='
<h5 id="status"></h5>

                <form class="bbq" name="hcw_tool" id="hcw_tool" method="POST">
        <div id="section-1" class="step">
        <input type="hidden" name="step_name" value="section-1"/>
<table>
    <thead>
        <tr>
            <th colspan="6" style="font-size:22px">Follow up Support supervision checklist on IMCI after training </th>
        </tr>
        <tr>
            <th colspan="6" >Facility Information</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Facility Name </td>
            <td>
            <!--input type="text" id="fac_name" name="fac_name" class="cloned" size="40" disabled/-->
            <label id="facilityName"  size="40" ></label>
            <input type="hidden" name="facilityMFLCode" id="facilityMFLCode" />
            <input type="hidden" name="facilityHName" id="facilityHName" />
            </td>
            <td>Facility Level </td>
            <td>
            <!--input type="text" id="facilityLevel" name="facilityLevel" class="cloned"  size="40"/-->
            <select name="facilityLevel" id="facilityLevel" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Level</option>
                            ' . $this -> selectFacilityLevel . '
                        </select>
            </td><td>County </td>
            <td>
            <select name="fac_county" id="fac_county" class="cloned" style="width:85%">
                            <option value="" selected="selected">Select County</option>
                            ' . $this -> selectCounties . '
                        </select>
            </td>
            </tr>
            <tr>
            <td>Facility Type </td><td>
            <select name="facilityType" id="facilityType" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Type</option>
                            ' . $this -> selectFacilityType . '
                        </select>

            </td>
            <td>Owned By </td><td>
            <select name="facilityOwnedBy" id="facilityOwnedBy" class="cloned" style="width:75%">
                            <option value="" selected="selected">Select Owner</option>
                            ' . $this -> selectFacilityOwner . '
                        </select>
            </td>

            <td>District/Sub County </td><td>
            <select name="fac_district" id="fac_district" class="cloned" style="width:85%">
                            <option value="" selected="selected">Select District/Sub County</option>
                            ' . $this -> selectDistricts . '
                        </select>
            </td>
            </tr>

        </table>
        <p class="instruction">
        * For Facility Type(Dispensary, Health Centre etc.)
        * For Owned By (Public/Private/FBO/MOH/NGO)
        </p>
        <table class="centre">
        <thead>
        <th colspan="12" >FACILITY CONTACT INFORMATION</th>
        </thead>
        <tr >
            <th scope="col" colspan="2" >CADRE</th>
            <th>NAME</th>
            <th >MOBILE</th>
            <th >EMAIL</th>
        </tr>
        <tr>
            <TD  colspan="2">Incharge </TD><td>
            <input type="text" id="facilityInchargename" name="facilityInchargename" class="cloned" size="40"/>
            </td><td>
            <input type="text" id="facilityInchargemobile" name="facilityInchargemobile" class="phone" size="40"/>
            </td>
            <td>
            <input type="text" id="facilityInchargeemail" name="facilityInchargeemail" class="cloned mail" size="40"/>
            </td>
        </tr>
        <tr>
            <TD  colspan="2">MCH Incharge</TD><td>
            <input type="text" id="facilityMchname" name="facilityMchname" class="cloned" size="40"/>
            </td><td>
            <input type="text" id="facilityMchmobile" name="facilityMchmobile" class="phone" size="40"/>
            </td>
            <td>
            <input type="text" id="facilityMchemail" name="facilityMchemail" class="cloned mail" size="40"/>
            </td>
        </tr>
        <tr>
            <TD  colspan="2">Maternity Incharge</TD><td>
            <input type="text" id="facilityMaternityname" name="facilityMaternityname" class="cloned" size="40"/>
            </td>
            <td>
            <input type="text" id="facilityMaternitymobile" name="facilityMaternitymobile" class="phone" size="40"/>
            </td>
            <td>
            <input type="text" id="facilityMaternityemail" name="facilityMaternityemail" class="cloned mail" size="40"/>
            </td>
        </tr>
        <tr>
            <TD  colspan="2">Team Lead</TD><td>
            <input type="text" id="facilityteamleadname" name="facilityMaternityname" class="cloned" size="40"/>
            </td>
            <td>
            <input type="text" id="facilityteamleadmobile" name="facilityMaternitymobile" class="phone" size="40"/>
            </td>
            <td>
            <input type="text" id="facilityteamleademail" name="facilityMaternityemail" class="cloned mail" size="40"/>
            </td>
        </tr>
        </table>

        <table>
    <thead>
        <th colspan="12">ASSESSOR INFORMATION </th>
    </thead>
    <tbody>
        <tr>
            <td>Name </td><td>
            <input type="text" size="40" name = "assesorname_1">
            </td><td>Designation </td><td><!--input type="text" id="designation" name="designation" class="cloned"  size="40"/-->
            <input type="text" size="40" name = "assesordesignation_1">
            </td><td>Email </td>
            <td>
            <input type="email" size="40" name = "assesoremail_1">
            </td>
            </td>
            <td>Phone Number </td>
            <td>
            <input type="phoneNumber" size="40" name = "assesorphoneNumber_1">
            </td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="4">HCW Profile </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4">Name of Provider</td>
        </tr>
        <tr>
            <td>First Name</td>
            <td><input type="text" name="hpfirstname_1" id="hpfirstname"></td>
            <td>Surname</td>
            <td><input type="text" name="hpsurname_1" id="hpsurname"></td>
        </tr>
        <tr>
            <td>National ID</td>
            <td><input type="text" name="hpnationalid_1" id="hpnationalid"></td>
            <td>Phone Number</td>
            <td><input type="text" name="hpphonenumber_1" id="hpphonenumber"></td>
        </tr>
        <tr>
            <td>Personal Number</td>
            <td><input type="text" name="hppersonalnumber_1" id="hppersonalnumber"></td>
            <td colspan = "2"></td>
        </tr>
        <tr>
            <td colspan="1">Year, Month when trained <input type="text" name="hpyear_1" id="hpyear"></td>
            <td colspan="3"><p><b>Key coordinator of the training(Select one)</b></br>
                MOH/KPA/CHAI<input type="radio" name="hpcoordinator_1" value="MOH/KPA/CHAI"></br>
                MOH only<input type="radio" name="hpcoordinator_1" value="MOH only"></br>
                Other<input type="radio" name="hpcoordinator_1" value="Other"></br>
                (If other, indicate the name of the coordinator/partner)<input type="text" name="hpother" id="hp_other">
            </td>
        </tr>
        <tr>
            <td colspan="1"><label for="">Designation</label></td>
            <td colspan="3"><input type="text" name="hpdesignation_1" id="hpdesignation"></td>
        </tr>'. $this -> hcwProfileSection . '
    </tbody>
    <tfoot></tfoot>
</table>
<p class="message success">Work Station Profile</p>
<table>
    <tbody>
        <tr>
            <td>Current Unit</td>
            <td><input type="text" name="questionResponseCurrentUnit" id="questionResponseCurrentUnit"></td>
        </tr>
    </tbody>
<p class="instruction">
        * If healthcare worker works in many departments, write ALL
</p>
        <tr>
            <th>Question</th>
            <th>Response</th>
        </tr>'.$this->hcwWorkProfile.'
 </table>
</div> <!-- end of section 1 -->

<div id="section-2" class="step">

        <input type="hidden" name="step_name" value="section-2"/>
<p class="message success" style = "text-align: center;">SECTION 2</p>
<p class="message success">SECTION 2: OBSERVATION OF CASE MANAGEMENT: ONE CASE PER HCW</p>
<p class="instruction">
        * Assessor should indicate findings alongside Healthcare Worker findings.
</p>
<table class="centre">
    <thead>
    <tr>
        <th colspan="6">CHILD PROFILE</th>
    </tr>
    </thead>
        <tr>
            <td>Gender (M or F)</td><td><input type="text"></td>
            <td>Age (In Months)</td><td><input type="text"></td>
            <td>Presenting complaints?</td><td><input size="100" type="text"></td>
        </tr>
</table>
<table class="centre">

        <tr>
            <th colspan="2" >ARE THE FOLLOWING SERVICES OFFERED TO A CHILD</th>
        </tr>
        <tr>
            <th  width="700px">SERVICE</th>
            <th colspan="2"> RESPONSE </th>
        </tr>

    ' . $this -> mchIndicatorsSection['svc'] . '
</table>
<table class="centre">

        <tr>
            <th colspan="3" >ARE THE FOLLOWING DANGER SIGNS ASSESSED IN ONGOING SESSION FOR A CHILD</th>
        </tr>
        <tr>
            <th width="700px" >SERVICE</th>
            <th colspan="2" > RESPONSE </th>
        </tr>

    ' . $this -> mchIndicatorsSection['sgn'] . '
</table>
<p class="message success">SECTION 2A: ASSESSMENT OF THE SICK CHILD AGE 2 MONTHS UP TO 5 YEARS</p>
<table class="centre">
    <tr>
    <th colspan = "5">ASSESSMENT FOR THE MAIN SYMPTOMS IN AN ONGOING SESSION FOR A CHILD</th>
    </tr>
    	<tr>
    	<th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "pnechoice" value = "1">Yes</input> <input type = "radio" name = "pnechoice" value = "0">No</input></td>
        <p class = "instruction">* If NO proceed to the next symptom.</p>
    	</tr>
          <tr>
            <th width="500px">Symptom</th>

               <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th>1. Cough / Pneumonia</th>

        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        </tr>

     ' . $this -> mchIndicatorsSection['pne'] . '
</table>
<table class="centre">
    	<tr>
        <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "dgnchoice" value = "1">Yes</input> <input type = "radio" name = "dgnchoice" value = "0">No</input></td>
        <p class = "instruction">* If NO proceed to the next symptom.</p>
        </tr>
         <tr>
            <th width="500px">Symptom</th>

               <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th>2. Diarrhoea</th>

        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        </tr>

     ' . $this -> mchIndicatorsSection['dgn'] . '
</table>
<table class="centre">
    	<tr>
        <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "fevchoice" value = "1">Yes</input> <input type = "radio" name = "fevchoice" value = "0">No</input></td>
        <p class = "instruction">* If NO proceed to the next symptom.</p>
        </tr>
          <tr>
            <th width="500px">Symptom</th>

               <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th>3. Fever / Malaria</th>

        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        </tr>

     ' . $this -> mchIndicatorsSection['fev'] . '
</table>
<table class="centre">
        <tr>
        <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "earchoice" value = "1">Yes</input> <input type = "radio" name = "earchoice" value = "0">No</input></td>
        <p class = "instruction">* If NO proceed to the next symptom.</p>
        </tr>
        <tr>
            <th width="500px">Symptom</th>

               <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th>4. Ear Infection</th>

        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        </tr>
     ' . $this -> mchIndicatorsSection['ear'] . '
</table>
<p class="message success">SECTION 2B: ASSESMENT FOR THE SICK YOUNG INFANT AGE UPTO 2 MONTHS( IF APPLICABLE)</p>
<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "svdchoice" value = "1">Yes</input> <input type = "radio" name = "svdchoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">1. Very Severe Disease</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
        '.$this -> mchIndicatorsSection['svd'].'
</table>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "jauchoice" value = "1">Yes</input> <input type = "radio" name = "jauchoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">2. Jaundice</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
        '.$this -> mchIndicatorsSection['jau'].'
</table>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "eyechoice" value = "1">Yes</input> <input type = "radio" name = "eyechoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">3. Eye Infection</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
        '.$this -> mchIndicatorsSection['eye'].'
</table>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "dgnchoice" value = "1">Yes</input> <input type = "radio" name = "dgnchoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">4. Diarrhoea</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
        '.$this -> mchIndicatorsSection['dgn'].'
</table>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "5achoice" value = "1">Yes</input> <input type = "radio" name = "5achoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">5A. Feeding Problem</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
</table>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "5bchoice" value = "1">Yes</input> <input type = "radio" name = "5bchoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">5B. Weight</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
</table>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "5bchoice" value = "1">Yes</input> <input type = "radio" name = "5bchoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">6. Special treatments needs</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
</table>
</div>

<div id="section-3" class="step">

<input type="hidden" name="step_name" value="section-3"/>
<p class="message success">SECTION 3: DOES THE HCW CHECK FOR THE FOLLOWING</p>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "malchoice" value = "1">Yes</input> <input type = "radio" name = "malchoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">Malnutrition</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
        '.$this -> mchIndicatorsSection['mal'].'
</table>

<table class = "center">
<tr>
    <th colspan = "3">Does the child have the symptom below? <th><td colspan = "3"><input type = "radio" name = "anmchoice" value = "1">Yes</input> <input type = "radio" name = "anmchoice" value = "0">No</input></td>
    <p class = "instruction">* If NO proceed to the next symptom.</p>
</tr>
<tr>
    <th width="500px" rowspan = "2">Malnutrition</th>
    <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
            <th style="width:100px">Response</th>
            <th style="width:400px">Findings</th>
        </tr>
        '.$this -> mchIndicatorsSection['anm'].'
</table>
<table class="centre">

        <tr>
            <th width="700px" rowspan="2">Condition</th>
            <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
    <tr>
    <th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	</tr>
    <tbody>
     ' . $this -> mchIndicatorsSection['con'] . '
    </tbody>
</table>
<table class="centre">

        <tr>
            <th width="700px" rowspan="2">Treatment and Counselling</th>
            <th colspan="2">HCW Response</th>
            <th colspan="2">Assessor Response</th>
        </tr>
        <tr>
        <th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	<th style="width:100px">Response</th>
        	<th style="width:400px">Findings</th>
        	</tr>

    <tbody>
        ' . $this -> mchIndicatorsSection['cnl'] . '
    </tbody>
    </table>
</div>



<div id="section-4" class="step">

<input type="hidden" name="step_name" value="section-4"/>
<p class="message success">CONSULTATION OBSERVATION</p>
<table class="centre">

     	<tr>
            <th width="700px">4.1 Consultation observation (observe three patient consultations if possible): write N/A if not applicable </th>
        	<th>Case 1</th>
        </tr>

    <tbody>
       ' . $this -> question['obs'] . '

    </tbody>
    <tfoot></tfoot>
</table>
<table class="centre">

     	<tr>
            <th width="700px">4.2 Exit Interview With The Caregiver</th>
        	<th>Case 1</th>
        </tr>

    <tbody>
       ' . $this -> question['int'] . '

    </tbody>
    <tfoot></tfoot>
</table>
<table>
<thead>
    <tr>
        <th colspan="2">ASSESSMENT OUTCOME</th>
    </tr>
</thead>
    <tr>
        <td>
            <input name="questionResponse_1000" type="radio">   Fully Practicing IMCI
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <input name="questionResponse_1000" type="radio">   Practicing with gaps
        </td>
        <td>
            Reason <input name="questionResponseOther_1000" type="text" size="100">
        </td>
    </tr>
    <tr>
        <td>
            <input name="questionResponse_1000" type="radio">   Not practicing at all
        </td>
         <td>
            Reason <input name="questionResponseOther_1000" type="text" size="100">
        </td>
    </tr>
    <tr>

<th colspan="2">CRITERIA FOR CERTIFICATION: SECTION A</td>
</tr>

'.$this->question['certa'].'

<tr>
<td colspan="2">
<p class="instruction">
A participant MUST correctly identify all the above in section <strong>A</strong> to be CERTIFIED
</p>
</td>

</tr>


<tr>

<th colspan="2">CHECKED  FOR THE FOLLOWING:    SECTION B</td>
</tr>

'.$this->question['certb'].'
<tr>
<td colspan="2" style="background:#ffffff">
<p class="instruction">
    Where NO, these are gaps identified and the HCW will need mentorship to incorporate these in routine care for the child
<br/>
If YES to all, consider HCW for TOT and Mentorship Training
<br/>
(NOTE: IF THE HEALTHCARE WORKER FAILS TO ATTAIN ALLTHE POINTS IN SECTION A, THE PARTICIPANT SHOULD BE GIVEN A SECOND CHANCE. IF THE PARTICIPANT FAILS IN THE SECOND ATTEMPT, MENTORSHIP IS RECOMMENDED BEFORE FURTHER ASSESMENT)
</p>
</td>
</tr>
<tr>
<th colspan="2">CERTIFICATION</td>
</tr>

'.$this->question['out'].'
</table>
<table>
    <thead>
        <tr>
            <th colspan="2">Share your findings from observational sessions with provider.
            Praise for the things done well and discuss on the identified weakness, show how it could be done.
            <p></p>Ask provdier, for any problems regarding assessment, classification, treatment, counselling, follow up etc and solve the problem instantly.
            Note down the decisions which have been taken to improve the skills and continue the practices</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Action/s taken by supervisor:</td>
            <td>Action/s taken by supervisee:</td>
        </tr>
        <tr>
            <td><textarea style="width:400px;height:100px"></textarea></td>
            <td><textarea style="width:400px;height:100px"></textarea></td>
        </tr>
        <tr>
            <td>Supervisor Signature<input type="text" style="width:500px;padding:10px"></td>
            <td>Supervisee Signature<input type="text" style="width:500px;padding:10px"></td>
        </tr>
        <tr>
            <td>Date    <input type="text" style="width:500px;padding:10px"></td>
            <td>Date    <input type="text" style="width:500px;padding:10px"></td>
        </tr>
    </tbody>
</table>

<p style="margin-top:0.5px"></p>
<table style="border:2px solid #666">
    <tr>
        <td><i>Please leave a copy of signed report to respective facility before leaving and send one copy to district within 7 days of visit </i></td>
    </tr>
</table>
</div>


<div id="section-5" class="step">

<input type="hidden" name="step_name" value="section-5"/>

</div>
<div id="sectionNavigation" class="buttonsPane">
        <input title="To View Previous Section" id="back" value="View Previous Section" class="awesome blue medium" type="reset"/>
        <input title="To Save This Section" id="submit" class="awesome blue medium"  type="submit" name="post_form" value="Save and Go to the Next Section"/>
        </div>
    </form>
';
        $data['form'] = $this -> combined_form;
        $data['form_id'] = 'form_dcah';
        $this -> load -> view('form', $data);

    }
    public function survey_complete() {
        $this -> message .= '<div id="No" class="step"><!--end of assessment message section-->
    <input type="hidden" name="step_name" value="end_of_assessment"/>
    <div class="block">
            <p align="left" style="font-size:20px;color:#AA1317; font-weight:bold">Assessment Complete</p>
            <p id="data" class="message success">Thanks for your participation.<br></p><br>
            <!--p class="message success">' . anchor(base_url() . '/assessment', 'Select another Facility') . '</p-->
            </div>
    </div><!--\.end of assessment message section-->';
        $data['form'] = $this -> message;
        $this -> load -> view('form', $data);

    }

    public function get_facility_list() {
//<div class="breadcrumb">
        //     <th colspan="22" >' . strtoupper($this -> session -> userdata('dName')) . ' DISTRICT/SUB-COUNTY FACILITIES</th>
        //     <div>
        $this -> facilityList .= '
        <table class="centre dataTable">

<thead>
            <th>#</th>
            <th>MFL CODE</th>
            <th> FACILITY NAME </th>
            <th>REPORTING PROGRESS</th>
            <th style="width:100px">ACTIVITY</th>
            <th>LINK</th>
</thead>
        </tr>' . $this -> districtFacilityListSection . '
        </table>';
        $data['form'] = $this -> facilityList;
        $data['form_id'] = '';
        $this -> load -> view('form', $data);
    }

}
