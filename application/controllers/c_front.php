<?php
class C_Front extends MY_Controller
{
    var $data, $survey;
    var $instructions;

    public function __construct() {

        parent::__construct();
        $this->data = array();
        $this->load->model('m_analytics');
    }

    public function index() {
        $data['title'] = 'MoH::Data Management Tool';
        $data['content'] = 'pages/v_home';
        //$data['mapsCH'] = $this->runMap('ch','baseline');
        //$data['mapsMNH'] = $this->runMap('mnh','baseline');
        //$data['mapsHCW'] = $this->runMap('hcw','baseline');

        //var_dump($this -> runMap());
        $this->load->view('template', $data);

        //landing page
        //$this -> load -> view('home_view', $data); //landing page


    }

    //End of index file

    public function runMap($survey,$survey_category,$statistic) {
        $counties = $this->m_analytics->runMap($survey,$survey_category,$statistic);
        $map = array();
        $datas = array();
        $status = '';
        foreach ($counties as $county) {

            //var_dump($county);
            $percentage = (int)$county[0][0]['percentage'];
            $reported = (int)$county[0][0]['reported'];
            $actual = (int)$county[0][0]['actual'];
            $countyMap = (int)$county[1];
            $countyName = $county[2];

            //echo $percentage.',';

            switch ($percentage) {
                case ($percentage == 0):
                    $status = '#ffffff';
                    break;

                case ($percentage < 20):
                    $status = '#e93939';
                    break;

                case ($percentage < 40):
                    $status = '#da8a33';
                    break;

                case ($percentage < 60):
                    $status = '#dad833';
                    break;

                case ($percentage < 80):
                    $status = '#91da33';
                    break;

                case ($percentage <= 100):
                    $status = '#7ada33';
                    break;

                    //case ($percentage===100) :
                    //	$status = '#13b00b';
                    //	break;


                default:
                    $status = '#ffffff';
                    break;
            }
            $datas[] = array('id' => $countyMap, 'value' => $countyName, 'color' => $status, 'tooltext' => $countyName . '  Percentage Complete:  ' . $percentage . '% (' . $reported . '/' . $actual . ')', 'link' => "Javascript:runCountyData('" . $countyName . "," . $survey.",".$survey_category."')");
            //$datas[] = array('id' => $countyMap, 'value' => $countyName, 'color' => $status, 'tooltext' => $countyName . '  Percentage Complete:  ' . $percentage . '% (' . $reported . '/' . $actual . ')', 'link' => base_url() . 'c_analytics/setActive/' . $countyName . '/' . $survey.'/'.$survey_category);
        }
        $map = array("canvasBorderColor" => "#ffffff", "hoverColor" => "#aaaaaa", "fillcolor" => "D7F4FF", "numbersuffix" => "M", "includevalueinlabels" => "1", "labelsepchar" => ":", "basefontsize" => "9", "borderColor" => '#999999', "showBevel" => "0", 'showShadow' => "0");
        $styles = array("showBorder" => 0);
        $finalMap = array('map' => $map, 'data' => $datas, 'styles' => $styles);
        $finalMap = json_encode($finalMap);
        echo $finalMap;
    }

    public function active_survey() {

        /**/

        if (!$this->session->userdata('dCode')) {

            // $data['facility']=$this ->selectFacility;
            $this->data['title'] = 'MoH Data Management Tool::Authentication';
            $this->data['form'] = '<p>User Login<p>';
            $this->data['login_response'] = '';
            $this->data['login_message'] = 'Login to Take Survey';
            $this->data['survey'] = strtoupper($this->survey);
            $this->data['content'] = 'pages/v_login';

            //$this -> load -> view('index', $this->data); //login view
            $this->load->view('template', $this->data);

            //login view


        } else {
            $this->inventory();
        }

        //$this->inventory();
        //dashboard

        //$this->inventory();
        //redirect(base_url() . 'c_auth/index', 'refresh');


    }

    //End of index file

    public function inventory() {

        //print 'sess val: '.var_dump($this->session->all_userdata()); die;
        /**/
        if ($this->session->userdata('dCode')) {
            $this->data['survey'] = $this->survey;
            $this->data['hidden'] = "display:none";
            $this->data['status'] = "";
            $this->data['response'] = "";
            $this->data['form'] = '<div class="error ui-autocomplete-loading" style="width:200px;height:76px"><br/><br/>Loading...please wait.<br/><br/></div>';
            if ($this->session->userdata('survey') == 'mnh') {
                $this->data['title'] = strtoupper($this->session->userdata('survey')) . '::Commodity, Equipment and Supplies Assessment';
            } elseif ($this->session->userdata('survey') == 'hcw') {
                $this->data['title'] = strtoupper($this->session->userdata('survey')) . '::Follow-Up Tool After IMCI Training';
            } else {
                $this->data['title'] = strtoupper($this->session->userdata('survey')) . '::Diarrhoea Treatment Scale Up Baseline Assessment';
            }
            $this->data['logged'] = 1;
            $this->data['form_id'] = '';
            $this->data['content'] = 'survey/v_survey_main';
            $this->load->view('template', $this->data);
        } else {
            redirect(base_url() . 'home', 'refresh');
        }

        /*$data['hidden']="display:none";
        $data['status']="";
        $data['response']="";
        $data['form'] = '<div class="error ui-autocomplete-loading" style="width:200px;height:76px"><br/><br/>Loading...please wait.<br/><br/></div>';
        $data['title']='MNH::Commodity Assessment';
        $data['form_id']='';
        $this -> load -> view('survey/v_survey_main', $data);*/

        //echo 'inventory';


    }

    public function formviewer() {
        $data['form'] = '<p class="error"><br/><br/>Click on any of the tabs to continue<br/><br/><p>';
        $this->load->view('form', $data);
    }

    public function reports() {
        $data['status'] = "";
        $data['response'] = "";
        $data['form'] = '<p class="error"><br/><br/>No report has been chosen<br/><br/><p>';
        $data['form_id'] = '';
        $this->load->view('reports', $data);

        //echo 'Vehicles';


    }

    
        function getReportingRatio($survey, $survey_category, $county,$statistic) {
            
            /*using DQL*/
            
            $finalData = array();
            
            try {
                
                $query = 'CALL get_reporting_ratio("' . $survey . '","' . $survey_category . '","' . $county . '","' . $statistic . '");';
                $myData = $this->db->query($query);
                $finalData = $myData->result_array();
                
                $myData->next_result();
                
                // Dump the extra resultset.
                $myData->free_result();
                
                // Does what it says.
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //echo($ex -> getMessage());
                
                
            }
            print_r($finalData);die;
            return $finalData;
        }


    public function retrieveData($table_name,$identifier){
        echo $this->session->userdata('survey_status');
        $this->load->model('m_retrieve');
        $data = $this->m_retrieve->retrieveData($table_name,$identifier);
        echo '<pre>';print_r($data);echo '</pre>';
    }

}
