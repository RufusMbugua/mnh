<?php
/*helps authenticate a user*/
class C_Auth extends MY_Controller {
    var $data;

    public function __construct() {
        parent::__construct();
        $this->data='';
    }

    public function go(){
        $this->load->model('m_mnh_survey');
        $this->m_mnh_survey->verifyRespondedByDistrict();
        if ($this->m_mnh_survey->isDistrict=='true') {
        	//$this->m_zinc_ors_inventory->retrieveMFLInformation();


            // $this->facilityInDistrict=$this->m_mnh_survey->districtFacilities;

            // $this->createFacilitiesListSection();

$assessment = $this->input->post('assessment');
$category = $this->input->post('term');
            if($assessment=='Child Health'){
                $assessment='ch';
            }
            elseif($assessment=='Maternal and Neonatal Health'){
                $assessment='mnh';
            }
            else{
                $assessment='hcw';
            }
            /*create session data*/
        /**/	$newdata = array('dName' => $this->m_mnh_survey->district->getDistrictName(),
                             'dCode'=>$this->m_mnh_survey->district->getDistrictID(),'survey'=>$assessment,'survey_category'=>$category);
           //var_dump($newdata); exit;

           $this -> session -> set_userdata($newdata);



            redirect(base_url().'assessment', 'refresh');
            $data['title']='MoH::Data Management Tool';
            $this -> load -> view('survey/index', $this->data);


        }else {
            #use an ajax request and not a whole refresh
            $data['title']='MoH::Data Management Tool';
            //die('Failed');
            $this->data['login_message'] = 'Invalid District and Password Combination!';
            $this->data['survey'] = '';
                        $this -> data['content'] = 'pages/v_login';
            //$this -> load -> view('index', $this->data); //login view
            $this -> load -> view('template', $this -> data);

        }
    }

   public function doCheckFacilityCode(){/**from the session data*/
    if(!$this -> session -> userdata('dName')){
        redirect(base_url() . '/assessment', 'refresh');
        return true;

        }else{
            $this->requestMFC();
            return false;

        }
    }

   private function requestMFC(){
   	        #use an ajax request and not a whole refresh
            $this->data['form'] = '<p>Facility Identification Required!<p>';
            $this->data['title']='MoH Data Management Tool::Authentication';
            $this -> load -> view('pages/v_login', $this->data);
   }



    public function logout(){
        //$data['facility']=$this ->selectFacility;
        $data['title']='MoH::Data Management Tool';
        //$data['title']='MoH Data Management Tool::Authentication';
        $data['form'] = '<p>You need to login.<p>';
        $this -> load -> view('pages/v_home', $data);
        $this->session->sess_destroy();
        redirect(base_url(), 'refresh');

    }
}
