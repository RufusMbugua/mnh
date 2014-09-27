<?php

//# Extend CI_Model to include Doctrine Entity Manager
date_default_timezone_set('Africa/Nairobi');

class MY_Model extends CI_Model
{
    
    public $em, $response, $theForm, $district, $commodity, $supplier, $county, $province, $owner, $ownerName, $level, $levelName, $supplies, $equipment, $equipmentName, $supplyName, $query, $type, $formRecords, $facilityFound, $facility, $section, $ort, $sectionExists, $signalFunction, $mchIndicator, $mchIndicatorName, $mnhIndicator, $mchTreatment, $mchTreatmentName, $ortAspect, $trainingGuidelines, $trainingGuideline, $commodityName, $districtFacilities, $facilityMFL, $strategy, $strategyName, $guideline, $chQuestion, $treatmentCommodity;
    
    function __construct() {
        parent::__construct();
        
        /* Instantiate Doctrine's Entity manage so we don't have
         to everytime we want to use Doctrine */
        
        $this->em = $this->doctrine->em;
        $this->load->database();
        $this->response = '';
        $this->theForm = $this->facilityMFL = '';
        $this->facilityFound = false;
        $this->sectionExists = false;
    }
    public function verifyRespondedByDistrict() {
        
        if ($this->input->post()) {
            
            //Get Variables
            $county = $this->input->post('county');
            $district = $this->input->post('district');
            $assessment = $this->input->post('assessment');
            $term = $this->input->post('term');
            switch ($assessment) {
                case 'Maternal and Neonatal Health':
                    $assessment = 'mnh';
                    break;

                case 'Child Health':
                    $assessment = 'ch';
                    break;

                case 'IMCI Follow-Up':
                    $assessment = 'hcw';
                    break;
            }
            $data = array('survey' => $assessment, 'survey_category' => $term, 'county' => $county, 'district' => $district);
            
            //echo $county;die;
            //check if a post was made
            $this->session->set_userdata($data);
            
            //Working with an object of the entity
            try {
                $this->district = $this->em->getRepository('models\Entities\Districts')->findOneBy(array('districtName' => $this->input->post('district', TRUE), 'districtAccessCode' => md5($this->input->post('usercode', TRUE))));
                
                if ($this->district) {
                    return $this->isDistrict = 'true';
                } else {
                    return $this->isDistrict = 'false';
                }
            }
            catch(exception $ex) {
                
                //ignore
                die($ex->getMessage());
            }
        }
        
        //close the this->input->post
        
        
    }
    
    /*utilized in several models*/
    public function getFacilityName($id) {
        try {
            $this->centre = $this->em->getRepository('models\Entities\Facilities')->findOneBy(array('facMfl' => $id));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
        return $this->centre->getFacName();
    }
    
    /*utilized in several models*/
    public function getAllFacilitiesByDistrict($districtName) {
        try {
            
            //Using DQL
            
            $this->districtFacilities = $this->em->createQuery('SELECT f.facMfl,f.facName FROM models\Entities\Facilities f WHERE f.facDistrict= :district ORDER BY f.facName ASC ');
            $this->districtFacilities->setParameter('district', $districtName);
            
            $this->districtFacilities = $this->districtFacilities->getArrayResult();
            
            //var_dump($this -> districtFacilities);die;
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    public function getAllFacilitiesByDistrictOptions($districtName) {
        try {
            
            //Using DQL
            
            $this->districtFacilities = $this->em->createQuery('SELECT f.facMfl,f.facName,f.ss_id FROM models\Entities\Facilities f WHERE f.facDistrict= :district AND =:status ORDER BY f.facName ASC ');
            $this->districtFacilities->setParameter('district', $districtName);
            $this->districtFacilities->setParameter('status', 'complete');
            
            $this->districtFacilities = $this->districtFacilities->getArrayResult();
            return $this->districtFacilities;
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getAllGovernmentOwnedFacilitiesByDistrict($districtName) {
        try {
            
            /*DQL
             $this->districtFacilities = $this->em->createQuery("SELECT f.facMFL,f.facName,f.ss_id FROM models\Entities\Facilities f WHERE f.facilityDistrict=: district AND f.fac_ownership IN (: condition) ORDER BY f.facName ASC");
             $this->districtFacilities->setParameter('district',$districtName);
             $this->districtFacilities->setParameter('condition','Ministry of Health,Local Authority,Local Authority T Fund');
            
             $this->districtFacilities = $this->districtFacilities->getArrayResult();*/
            
            /*Using CI*/
            $this->query = "SELECT f.facMFL,f.facName,f.ss_id FROM facilities f WHERE f.facilityDistrict='$districtName'
                          AND f.fac_ownership IN ('Ministry of Health','Local Authority','Local Authority T Fund','Armed Forces','Community Development Fund','Community','Parastatal','State Corporation') ORDER BY f.facName ASC";
            $this->districtFacilities = $this->db->query($this->query);
            $this->districtFacilities = $this->districtFacilities->result_array();
            
            //die(var_dump($this->districtFacilities));
            
            
        }
        catch(exception $ex) {
            
            //ignor
            //die($ex->getMessage());
            
            
        }
    }
    
    public function getAllFacilityNames() {
        $query = $this->em->createQuery('SELECT f.facName FROM models\Entities\Facilities f');
        
        //$query->setParameter('fname','%'.$options['keyword'].'%');
        
        $this->formRecords = $query->getArrayResult();
        
       // die(var_dump($this->formRecords));
        return $this->formRecords;
    }
    
    public function getSpecificFacilityNames($mfc) {
        $query = $this->em->createQuery('SELECT f.facName FROM models\Entities\Facilities f where f.facMFL=' . $mfc);
        
        //$query->setParameter('fname','%'.$options['keyword'].'%');
        
        $this->facility = $query->getArrayResult();
        
        // die(var_dump($this->formRecords));
        return $this->facility;
    }
    
    function getTreatmentCommodity($surveyName) {
        try {
            $this->treatmentCommodity = $this->em->createQuery('SELECT DISTINCT c.commName, c.commCode, c.commFor, c.commUnit FROM models\Entities\Commodities c WHERE c.commFor = :surveyName ORDER BY c.commCode ASC');
            $this->treatmentCommodity->setParameter('surveyName', $surveyName);
            $this->treatmentCommodity = $this->treatmentCommodity->getResult();
        }
        catch(Exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->treatmentCommodity;
    }
    function getAllCommodityNames() {
        
        /*using DQL*/
        try {
            $this->commodity = $this->em->createQuery('SELECT c.commId, c.commCode, c.commName, c.commUnit, c.commFor FROM models\Entities\Commodities c ORDER BY c.commFor, c.commCode ASC');
            
            $this->commodity = $this->commodity->getResult();
            
            //die(var_dump($this->commodity));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->commodity;
    }
    
    /*end of getAllCommodityNames*/
    
    function getCommodityUsageOptionsList() {
        
        /*using DQL*/
        try {
            $this->commodityOptions = $this->em->createQuery('SELECT c.cooId, c.cooDescription FROM models\Entities\CommodityOutageOptions c ');
            
            $this->commodityOptions = $this->commodityOptions->getResult();
            
            //die(var_dump($this->commodity));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->commodityOptions;
    }
    
    /*end of getAllCommodityNames*/
    
    function getAllSupplyNames($surveyName) {
        
        /*using DQL*/
        try {
            $this->supplies = $this->em->createQuery('SELECT s.supplyId, s.supplyCode, s.supplyName, s.supplyUnit FROM models\Entities\Supplies s WHERE s.supplyFor= :survey ORDER BY s.supplyId ASC');
            $this->supplies->setParameter('survey', $surveyName);
            $this->supplies = $this->supplies->getResult();
            
            // die(var_dump($this->supplies));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->supplies;
    }
    
    /*end of getAllSupplyNames*/
    
    function getTotalSupplyNames() {
        
        /*using DQL*/
        try {
            $this->supplies = $this->em->createQuery('SELECT s.supplyId, s.supplyCode, s.supplyName, s.supplyUnit,s.supplyFor FROM models\Entities\Supplies s ORDER BY s.supplyFor, s.supplyId ASC');
            
            $this->supplies = $this->supplies->getResult();
            
            // die(var_dump($this->supplies));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->supplies;
    }
    
    /*end of getAllSupplyNames*/
    
    function getAllAccessChallenges() {
        
        /*using DQL*/
        try {
            $this->challenges = $this->em->createQuery('SELECT s.achId, s.achCode, s.achName FROM models\Entities\AccessChallenges s ORDER BY s.achId ASC');
            $this->challenges = $this->challenges->getResult();
            
            // die(var_dump($this->supplies));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->challenges;
    }
    
    /*end of getAllSupplyNames*/
    
    function getAllEquipmentNames() {
        
        /*using DQL*/
        try {
            $this->equipment = $this->em->createQuery('SELECT e.eqId, e.eqCode, e.eqName, e.eqUnit, e.eqFor FROM models\Entities\Equipments e  ORDER BY e.eqFor, e.eqCode ASC');
            
            $this->equipment = $this->equipment->getResult();
            
            //die(var_dump($this->equipment));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->equipment;
    }
    function getSpecificEquipmentNames($for) {
        
        /*using DQL*/
        try {
            $this->equipment = $this->em->createQuery('SELECT e.eqId, e.eqCode, e.eqName, e.eqUnit, e.eqFor FROM models\Entities\Equipments e WHERE e.eqFor= :for ORDER BY e.eqCode ASC');
            $this->equipment->setParameter('for', $for);
            $this->equipment = $this->equipment->getResult();
            
            //die(var_dump($this->equipment));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->equipment;
    }
    
    /*end of getAllEquipmentNames*/
    
    function getAllCommoditySupplierNames($surveyName) {
        
        /*using DQL*/
        try {
            $this->supplier = $this->em->createQuery('SELECT s.supplierId, s.supplierCode, s.supplierName, s.supplierFor FROM models\Entities\Suppliers s WHERE s.supplierFor= :survey ORDER BY s.supplierName ASC');
            $this->supplier->setParameter('survey', $surveyName);
            
            // echo $this->supplier->getSQL();die;
            $this->supplier = $this->supplier->getResult();
            
            //die(var_dump($this->supplier));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->supplier;
    }
    
    /*end of getAllCommoditySupplierNames*/
    
    function getAllSources($surveyName) {
        
        /*using DQL*/
        try {
            $this->supplier = $this->em->createQuery('SELECT s.supplierId, s.supplierCode, s.supplierName FROM models\Entities\Suppliers s WHERE s.supplierFor= :survey ORDER BY s.supplierName ASC');
            $this->supplier->setParameter('survey', $surveyName);
            
            // echo $this->supplier->getSQL();die;
            $this->supplier = $this->supplier->getResult();
            
            //die(var_dump($this->supplier));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->supplier;
    }
    
    /*end of getAllCommoditySupplierNames*/
    
    function getAllSignalFunctions() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT s.sfacilityMFL, s.sfName FROM models\Entities\SignalFunctions s ORDER BY s.sfacilityMFL ASC');
            $this->signalFunction = $query->getResult();
            
            //die(var_dump($this->signalFunction));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->signalFunction;
    }
    
    /*end of getAllSignalFunctions*/
    
    function getAllOrtAspects($for) {
        
        /*using DQL*/
        try {
            $this->ortAspect = $this->em->createQuery('SELECT q.questionCode, q.questionName FROM models\Entities\questions q WHERE q.questionFor= :for  ORDER BY q.questionCode ASC');
            $this->ortAspect->setParameter('for', $for);
            $this->ortAspect = $this->ortAspect->getResult();
            
            //die(var_dump($this->ortAspect));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->ortAspect;
    }
    
    /*end of getAllOrtAspects*/
    
    function getQuestionsBySection($for, $code) {
        
        /*using DQL*/
        try {
            $this->questions = $this->em->createQuery('SELECT q.questionCode, q.questionName FROM models\Entities\questions q WHERE q.questionFor= :for AND q.questionCode LIKE :code ORDER BY q.questionCode ASC');
            $this->questions->setParameter('for', $for);
            $this->questions->setParameter('code', $code . '%');
            $this->questions = $this->questions->getResult();
            
            //die(var_dump($this -> questions));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->questions;
    }
    
    /*end of getAllOrtAspects*/
    
    function getAllQuestions() {
        
        /*using DQL*/
        try {
            $this->questions = $this->em->createQuery('SELECT q.questionCode, q.questionName,q.questionFor FROM models\Entities\questions q ORDER BY q.questionFor, q.questionCode ASC');
            $this->questions = $this->questions->getResult();
            
            //die(var_dump($this -> questions ));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->questions;
    }
    
    /*end of getAllOrtAspects*/
    
    function getAllMCHIndicators() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT i.indicatorCode, i.indicatorName,i.indicatorFor,i.indicatorFindings  FROM models\Entities\indicators i ORDER BY i.indicatorFor, i.indicatorCode ASC');
            $this->mchIndicator = $query->getResult();
            
            //die(var_dump($this->mchIndicator));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->mchIndicator;
    }
    
    /*end of getAllMCHIndicators*/
    
    function getAllMCHTreatments() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT t.treatmentCode, t.treatmentName,t.treatmentFor, tc.tcName FROM models\Entities\treatments t JOIN models\Entities\treatmentclassifications tc  WHERE t.treatmentFor = tc.tcFor ORDER BY t.treatmentCode ASC');
            $this->mchTreatment = $query->getResult();
            
            //die(var_dump($this->mchTreatment));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->mchTreatment;
    }
    
    /*end of getAllMCHTreatments*/
    
    function getTreatmentsByType($type) {
        try {
            $this->treatmentbytype = $this->em->createQuery('SELECT t.treatmentCode, t.treatmentName,t.treatmentFor FROM models\Entities\treatments t WHERE t.treatmentFor = :type ORDER BY t.treatmentCode ASC');
            $this->treatmentbytype->setParameter('type', $type);
            $this->treatmentbytype = $this->treatmentbytype->getResult();
            
            //die(var_dump($this->treatmentbytype));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->treatmentbytype;
    }
    
    function getTreatmentsTotal() {
        try {
            $this->treatmentbytype = $this->em->createQuery('SELECT t.treatmentCode, t.treatmentName,t.treatmentFor FROM models\Entities\treatments t ORDER BY t.treatmentFor,t.treatmentCode ASC');
            
            $this->treatmentbytype = $this->treatmentbytype->getResult();
            
            //die(var_dump($this->treatmentbytype));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->treatmentbytype;
    }
    
    function getAllTrainingGuidelines($surveyName) {
        
        /*using DQL*/
        try {
            $this->trainingGuidelines = $this->em->createQuery('SELECT g.guideCode, g.guideName FROM models\Entities\Guidelines g WHERE g.guideFor= :survey ORDER BY g.guideCode ASC');
            $this->trainingGuidelines->setParameter('survey', $surveyName);
            $this->trainingGuidelines = $this->trainingGuidelines->getResult();
            
            //die(var_dump($this->trainingGuidelines));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->trainingGuidelines;
    }
    
    /*end of getAllTrainingGuidelines*/
    
    function getAllDistrictNames() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT d.districtId,d.districtName FROM models\Entities\Districts d ORDER BY d.districtName ASC');
            $this->district = $query->getResult();
            
            //die(var_dump($this->district));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();
            
            
        }
        return $this->district;
    }
    
    /*end of getDistrictNames*/
    
    function getAllCountyNames() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT c.countyId,c.countyName,c.countyFusionMapId FROM models\Entities\Counties c ORDER BY c.countyName ASC');
            $this->county = $query->getResult();
            
            //var_dump($this -> county);
            
            
        }
        catch(exception $ex) {
            
            //$ex->getMessage();
            
            
        }
        return $this->county;
    }
    
    /*end of getAllCountyNames*/
    
    function getAllProvinceNames() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT p.provinceId, p.provinceName FROM models\Entities\e_province p ORDER BY p.provinceName ASC');
            $this->province = $query->getResult();
            
            //die(var_dump($this->level));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();//exit;
            
            
        }
        return $this->province;
    }
    
    /*end of getAllProvinceNames*/
    
    function getAllFacilityOwnerNames() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT o.foId, o.foName FROM models\Entities\FacilityOwners o ORDER BY o.foName ASC');
            $this->owner = $query->getResult();
            
            //die(var_dump($this->level));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();//exit;
            
            
        }
        return $this->owner;
    }
    
    /*end of getAllFacilityOwnerNames*/
    
    function getAllGovernmentOwnedNames() {
        
        /*using CI Database Active Record*/
        try {
            
            /* $query = $this->em->createQuery('SELECT t.ft_id,t.ft_name FROM models\Entities\Facilities_type t
             WHERE t.ft_name IN (Ministry of Health,Local Authority,Local Authority T Fund) ORDER BY t.ft_name ASC');*/
            if ($this->session->userdata('survey') == 'ch') {
                $query = "SELECT o.facilityOwnerId,o.facilityOwner FROM facilities_owner o WHERE o.facilityOwner IN ('Ministry of Health','Local Authority','Local Authority T Fund','Armed Forces','Community Development Fund','Community','Parastatal','State Corporation') OR o.facilityOwnerFor='mch' ORDER BY o.facilityOwner ASC";
            } else {
                $query = "SELECT o.facilityOwnerId,o.facilityOwner FROM facilities_owner o WHERE o.facilityOwner IN ('Ministry of Health','Local Authority','Local Authority T Fund','Armed Forces','Community Development Fund','Community','Parastatal','State Corporation') ORDER BY o.facilityOwner ASC";
            }
            
            $this->owner = $this->db->query($query);
            $this->owner = $this->owner->result_array();
            
            // die(var_dump($this->owner));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        return $this->owner;
    }
    
    /*end of getAllGovernmentOwnedNames*/
    
    function getAllFacilityTypes() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT t.ft_id,t.ft_name FROM models\Entities\Facilities_type t ORDER BY t.ft_name ASC');
            $this->type = $query->getResult();
            
            //die(var_dump($this->type));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //$ex->getMessage();//exit;
            
            
        }
        return $this->type;
    }
    
    /*end of getAllFacilityTypes*/
    
    function getAllGovernmentFacilityTypes() {
        
        /*using CI Database Active Record*/
        try {
            
            /* $query = $this->em->createQuery('SELECT t.ft_id,t.ft_name FROM models\Entities\Facilities_type t
             WHERE t.ft_name IN (Ministry of Health,Local Authority,Local Authority T Fund) ORDER BY t.ft_name ASC');*/
            $query = "SELECT DISTINCT(fac_type),t.ft_id FROM facilities f,facility_types t WHERE f.fac_ownership IN ('Ministry of Health','Local Authority','Local Authority T Fund','Armed Forces','Community Development Fund','Community','Parastatal','State Corporation') AND t.ft_name=fac_type ORDER BY fac_type ASC";
            $this->type = $this->db->query($query);
            $this->type = $this->type->result_array();
            
            //die(var_dump($this->type));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        return $this->type;
    }
    
    /*end of getAllGovernmentFacilityTypes*/
    
    function getAllFacilityLevels() {
        
        /*using DQL*/
        try {
            $query = $this->em->createQuery('SELECT l.flId,l.flName FROM models\Entities\FacilityLevels l ORDER BY l.flName ASC');
            $this->level = $query->getResult();
            
            //die(var_dump($this->level));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            $ex->getMessage();
            
            //exit;
            
            
        }
        return $this->level;
    }
    
    /*end of getAllFacilityLevels*/
    
    /*utilized in several models*/
    public function getDistrictName($id) {
        try {
            $this->district = $this->em->getRepository('models\Entities\Districts')->findOneBy(array('districtId' => $id));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    //end of getDistrictName
    
    /*utilized in several models*/
    public function getCountyName($id) {
        try {
            $this->county = $this->em->getRepository('models\Entities\Counties')->findOneBy(array('countyId' => $id));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getCommunityStrategyName($id) {
        try {
            $this->strategy = $this->em->getRepository('models\Entities\questions')->findOneBy(array('questionCode' => $id));
            
            if ($this->strategy) {
                $this->strategyName = $this->strategy->getQuestionName();
                return $this->strategyName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getTrainingGuidelineName($id) {
        try {
            $this->guideline = $this->em->getRepository('models\Entities\questions')->findOneBy(array('questionCode' => $id));
            
            if ($this->guideline) {
                $this->guideline = $this->guideline->getQuestionName();
                return $this->guideline;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getChildHealthQuestionName($id) {
        try {
            $this->chQuestion = $this->em->getRepository('models\Entities\questions')->findOneBy(array('questionCode' => $id));
            
            if ($this->chQuestion) {
                $this->chQuestion = $this->chQuestion->getMCHQuestion();
                return $this->chQuestion;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getQuestionName($id) {
        try {
            $this->mnhQuestion = $this->em->getRepository('models\Entities\questions')->findOneBy(array('questionCode' => $id));
            
            if ($this->mnhQuestion) {
                
                $this->mnhQuestion = $this->mnhQuestion->getQuestionName();
                return $this->mnhQuestion;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    public function getSignalName($id) {
        try {
            $this->signalFunction = $this->em->getRepository('models\Entities\SignalFunctions')->findOneBy(array('sfacilityMFL' => $id));
            
            if ($this->signalFunction) {
                $this->signalFunction = $this->signalFunction->getSfName();
                return $this->signalFunction;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getChildHealthIndicatorName($id) {
        try {
            $this->mchIndicatorName = $this->em->getRepository('models\Entities\indicators')->findOneBy(array('indicatorCode' => $id));
            
            if ($this->mchIndicatorName) {
                $this->mchIndicatorName = $this->mchIndicatorName->getIndicatorName();
                echo $this->mchIndicatorName;
                die;
                return $this->mchIndicatorName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    
    public function getChildHealthTreatmentName($id) {
        try {
            $this->mchTreatmentName = $this->em->getRepository('models\Entities\treatments')->findOneBy(array('treatmentCode' => $id));
            
            if ($this->mchTreatmentName) {
                $this->mchTreatmentName = $this->mchTreatmentName->getTreatmentName();
                return $this->mchTreatmentName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getCHEquipmentName($id) {
        try {
            $this->equipmentName = $this->em->getRepository('models\Entities\Equipments')->findOneBy(array('eqCode' => $id));
            
            if ($this->equipmentName) {
                $this->equipmentName = $this->equipmentName->getEqName();
                return $this->equipmentName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getSupplyName($id, $curr) {
        try {
            $this->supplyName = $this->em->getRepository('models\Entities\Supplies')->findOneBy(array('supplyCode' => $id, 'supplyFor' => $curr));
            if ($this->supplyName) {
                $this->supplyName = $this->supplyName->getSupplyName();
                return $this->supplyName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getCHResourcesName($id) {
        try {
            $this->resourcesName = $this->em->getRepository('models\Entities\Equipments')->findOneBy(array('eqCode' => $id));
            
            //var_dump($this -> resourcesName);
            if ($this->resourcesName) {
                $this->resourcesName = $this->resourcesName->getEqName();
                return $this->resourcesName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getLevelName($id) {
        try {
            $this->level = $this->em->getRepository('models\Entities\FacilityLevels')->findOneBy(array('facilityLevelId' => $id));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getLevelNameById($id) {
        try {
            $this->level = $this->em->getRepository('models\Entities\FacilityLevels')->findOneBy(array('facilityLevelId' => $id));
            if ($this->level) {
                $this->levelName = $this->level->getfacilityLevel();
                return $this->levelName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getStaffTrainingGuidelineById($id) {
        try {
            $this->trainingGuideline = $this->em->getRepository('models\Entities\Guidelines')->findOneBy(array('guideCode' => $id));
            if ($this->trainingGuideline) {
                $this->trainingGuideline = $this->trainingGuideline->getGuideName();
                return $this->trainingGuideline;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getCommodityNameById($id) {
        try {
            $this->commodityName = $this->em->getRepository('models\Entities\Commodities')->findOneBy(array('commCode' => $id));
            if ($this->commodityName) {
                $this->commodityName = $this->commodityName->getCommName();
                return $this->commodityName;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getFacilityOwnerNameById($id) {
        try {
            $this->ownerName = $this->em->getRepository('models\Entities\FacilityOwners')->findOneBy(array('facilityOwnerId' => $id));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    /*utilized in several models*/
    public function getProvinceName($id) {
        try {
            $this->province = $this->em->getRepository('models\Entities\e_province')->findOneBy(array('provinceId' => $id));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
    }
    
    //check if facility name exists
    public function facilityExists($mfc) {
        try {
            $this->facility = $this->em->getRepository('models\Entities\Facilities')->findOneBy(array('facName' => $mfc));
            if ($this->facility) {
                $this->facilityFound = true;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
        return $this->facility;
    }
    
    /*close facilityExists($mfc)*/
    
    //check if tracker entry has already been done
    public function sectionEntryExists($mfc, $section, $survey) {
        try {
            $this->section = $this->em->getRepository('models\Entities\AssessmentTracker')->findOneBy(array('facilityCode' => $mfc, 'astSection' => $section, 'astSurvey' => $survey, 'ssId' => (int)$this->session->userdata('survey_status')));
            if ($this->section) {
                $this->sectionExists = true;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
        return $this->section;
    }
    
    /*close sectionEntryExists($mfc,$section,$survey)*/
    
    //used in m_zinc_ors_inventory
    public function findOrtCodeByFacility($mfc) {
        try {
            $this->ort = $this->em->getRepository('models\Entities\e_ortc_assessment')->findOneBy(array('facMFL' => $mfc));
            return $this->ort;
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            return false;
        }
    }
    
    /*close findOrtCodeByFacility($mfc)*/
    
    //checks if commodity name exists
    public function commodityExists($cName) {
        try {
            $this->commodity = $this->em->getRepository('models\Entities\Commodities')->findOneBy(array('commodityName' => $cName));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
        return $this->commodity;
    }
    
    /*close commodityExists($cName)*/
    
    /*update the MFL data*/
    protected function updateFacilityInfo() {
        
        //pick facility name and code for temp session use
        if ($this->input->post() && $this->input->post('facilityHName', TRUE)) {
            
            //print $this -> session -> userdata('facilityMFL'); die;
            if (!$this->session->userdata('facilityMFL')) {
                $new_data = array('fName' => $this->input->post('facilityHName', TRUE), 'facilityMFL' => $this->input->post('facilityMFLCode', TRUE));
                $this->session->set_userdata($new_data);
            }
        }
        
        //analyse all posted vals and collect them
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            
            if (strpos($key, 'fac') !== FALSE) {
                
                //get the fields carrying facility info only
                
                $this->attr = $key;
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    // $this->elements[$this->id][$this->attr]=htmlentities($val);
                    $this->elements[$this->attr] = htmlentities($val);
                } else {
                    $this->elements[$this->attr] = '';
                }
                
                // print $this->attr.' val='.$val.' id='.$this->id.' <br />';
                
                
            }
            
            //end of cadre fields only-filter
            
            // print $key.' val='.$val.' <br />';
            
            
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        
        //print var_dump($this->elements);
        //exit;
        
        //check if facility exists
        //$this->facility=$this->facilityExists($this -> session -> userdata('fName'));
        
        //print var_dump($this->facility);
        
        //get county name,district name,level name by id
        //$this->getCountyName($this->input->post('facilityCounty'));/*method defined in MY_Model*/
        //$this->getDistrictName($this->input->post('facilityDistrict'));/*method defined in MY_Model*/
        
        $this->getLevelName($this->input->post('facilityLevel', TRUE));
        
        /*method defined in MY_Model*/
        
        //get owner name by id passed
        $this->getFacilityOwnerNameById($this->input->post('fac_ownership', TRUE));
        
        //$this->getProvinceName($this->input->post('facilityProvince'));/*method defined in MY_Model*/
        
        //insert facility if new, else update the existing one
        /*  if(!$this->facility){
         //die('New entry, enter new one');
         $this -> theForm = new \models\Entities\Facilities(); //create an object of the model
         $this -> theForm -> setCreatedAt(new DateTime()); //timestamp option
         $this -> theForm -> setfacName($this->input->post('facName',TRUE));
         $this -> theForm -> setfacMFL($this -> session -> userdata('facilityMFL'));//obtain facility code from current session
         }else{*/
        
        //$this -> theForm = new \models\Entities\Facilities(); //create an object of the model
        //die('Duplicate entry, so update');
        
        //  echo 'Name: '. $this -> session -> userdata('fName');die;
        try {
            $this->theForm = $this->em->getRepository('models\Entities\Facilities')->findOneBy(array('facName' => $this->input->post('facilityHName', TRUE), 'facMFL' => $this->session->userdata('facilityMFL')));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            return false;
        }
        
        //}
        
        $this->theForm->setUpdatedAt(new DateTime());
        
        /*timestamp option*/
        
        //$this -> theForm -> setFacilityDistrict($this->district->getDistrictName());
        $this->theForm->setFacilityLevel($this->input->post('facilityLevel', TRUE));
        $this->theForm->setFacilityOwnedBy($this->ownerName->getFacilityOwner());
        
        //$this -> theForm -> setFacilityProvince($this->province->getProvinceName());
        //$this -> theForm -> setFacilityCounty($this->county->getCountyName());
        $this->theForm->setFacilityInchargeContactPerson($this->input->post('facilityInchargename', TRUE));
        ($this->input->post('facilityInchargemobile', TRUE) != '') ? $this->theForm->setFacilityInchargeTelephone($this->input->post('facilityInchargemobile', TRUE)) : 'n/a';
        
        $this->theForm->setFacilityInchargeEmail($this->input->post('facilityInchargeemail', TRUE));
        
        ($this->elements['facilityMchname'] == '') ? $this->theForm->setFacilityMCHContactPerson('n/a') : $this->theForm->setFacilityMCHContactPerson($this->input->post('facilityMchname', TRUE));
        ($this->input->post('facilityMchmobile', TRUE) != '') ? $this->theForm->setFacilityMCHTelephone($this->input->post('facilityMchmobile', TRUE)) : 'n/a';
        
        ($this->elements['facilityMchemail'] == '') ? $this->theForm->setFacilityMCHEmail('n/a') : $this->theForm->setFacilityMCHEmail($this->input->post('facilityMchemail', TRUE));
        
        //facility data specific to mnh survey only
        if ($this->session->userdata('survey') == 'mnh') {
            
            //  print 'yes,true'; die;
            (isset($this->elements['facilityMaternityname']) && $this->elements['facilityMaternityname'] != '') ? $this->theForm->setFacilityMaternityContactPerson($this->input->post('facilityMaternityname', TRUE)) : $this->theForm->setFacilityMaternityContactPerson('n/a');
            (isset($this->elements['facilityMaternitymobile']) && $this->elements['facilityMaternitymobile'] != '') ? $this->theForm->setFacilityMaternityTelephone($this->input->post('facilityMaternitymobile', TRUE)) : 'n/a';
            (isset($this->elements['facilityMaternityemail']) && $this->elements['facilityMaternityemail'] != '') ? $this->theForm->setFacilityMaternityEmail($this->input->post('facilityMaternityemail', TRUE)) : $this->theForm->setFacilityMaternityEmail('n/a');
            
            if ($this->elements['facDeliveriesDone'] == 'No') {
                (isset($this->elements['facRsnNoDeliveries'])) ? $this->theForm->setReasonDeliveryNotDone($this->elements['facRsnNoDeliveries']) : $this->theForm->setReasonDeliveryNotDone('n/a');
                $this->theForm->setss_id('complete');
            } else {
                $this->theForm->setReasonDeliveryNotDone('n/a');
            }
            
            $this->theForm->setDeliveriesDone($this->elements['facDeliveriesDone']);
        }
        
        //close if statement
        
        $this->em->persist($this->theForm);
        
        try {
            
            $this->em->flush();
            $this->em->clear();
            
            //detaches all objects from doctrine
            return true;
            
            //print 'true';
            
            
        }
        catch(Exception $ex) {
            
            //die($ex->getMessage());
            //print 'false';
            return false;
            
            /*display user friendly message*/
        }
        
        //end of catch
        
        
    }
    
    //close updateFacilityInfo
    
    //assuming mnh/mch assessment is taken, every facility has exactly 6 and 7 entries respectively, for each active survey
    protected function writeAssessmentTrackerLog() {
        
        //check if entry exists
        $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
        
        //print var_dump($this->section);
        
        //insert log entry if new, else update the existing one
        if ($this->sectionExists == false) {
            
            //die('New entry, enter new one');
            $this->theForm = new \models\Entities\AssessmentTracker();
            
            //create an object of the model
            $this->theForm->setAstSection($this->input->post('step_name', TRUE));
            $this->theForm->setAstSurvey($this->session->userdata('survey'));
            
            //obtain facility code from current survey session val
            $this->theForm->setAstLastActivity(new DateTime());
            
            /*timestamp option*/
            $this->theForm->setFacilitycode($this->session->userdata('facilityMFL'));
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            //obtain facility code from current temp session val
            
            
        } else {
            
            // die('Update log');
            try {
                $this->theForm = $this->em->getRepository('models\Entities\AssessmentTracker')->findOneBy(array('facilityCode' => $this->session->userdata('facilityMFL'), 'astSection' => $this->input->post('step_name', TRUE), 'astSection' => $this->session->userdata('survey')));
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());
                
                
            }
        }
        
        $this->theForm->setAstLastActivity(new DateTime());
        
        /*timestamp option*/
        
        $this->em->persist($this->theForm);
        
        try {
            
            $this->em->flush();
            $this->em->clear();
            
            //detaches all objects from doctrine
            //print 'true';
            
            
        }
        catch(Exception $ex) {
            
            // die($ex->getMessage());
            //print 'false';
            /*display user friendly message*/
        }
        
        //end of catch
        
        
    }
    
    protected function markSurveyStatusAsComplete() {
        try {
            $this->theForm = $this->em->getRepository('models\Entities\Facilities')->findOneBy(array('facMFL' => $this->session->userdata('facilityMFL')));
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());
            
            
        }
        
        if ($this->session->userdata('survey') == 'mnh') {
            
            //$this -> theForm -> setss_id('complete');
            
            
        } else {
            
            //$this -> theForm -> setFacilityCHSurveyStatus('complete');
            
            
        }
        
        $this->em->persist($this->theForm);
        
        try {
            
            $this->em->flush();
            $this->em->clear();
            
            //detaches all objects from doctrine
            //print 'true';
            
            
        }
        catch(Exception $ex) {
            
            //die($ex->getMessage());
            //print 'false';
            /*display user friendly message*/
        }
        
        //end of catch
        
        
    }
    
    public function getvalueby($table, $data) {
        try {
            $result = $this->em->getRepository($table)->findOneBy($data);
        }
        catch(Exception $ex) {
            echo $ex->getMessage();
        }
        
        return $result;
    }
    
    public function retrieveData($table_name, $identifier) {
        $results = $this->db->get_where($table_name, array('ss_id' => $this->session->userdata('survey_status')));
        $results = $results->result_array();
        
        foreach ($results as $result) {
            $data[$result[$identifier]] = $result;
        }
        return $data;
    }
    
    /**
     * [universalEditor description]
     * @param  [type] $table       [description]
     * @param  [type] $column      [description]
     * @param  [type] $value       [description]
     * @param  [type] $primary_key [description]
     * @param  [type] $pk_value    [description]
     * @return [type]              [description]
     */
    public function universalEditor($table, $column, $value, $primary_key, $pk_value) {
        $query = "UPDATE $table SET $column = '$value' WHERE $primary_key=$pk_value";
        try {
            $this->dataSet = $this->db->query($query);
            $this->dataSet = $this->dataSet->result_array();
            if ($this->dataSet) {
                return $this->dataSet;
            } else {
                return $this->dataSet = false;
            }
        }
        catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    /**
     * [get_survey_info description]
     * @param  [type] $survey_type     [description]
     * @param  [type] $survey_category [description]
     * @param  [type] $facMFL          [description]
     * @return [type]                  [description]
     */
    public function get_survey_info($survey_type, $survey_category,$statistic, $facMFL) {
        $query = 'CALL get_survey_info("' . $survey_type . '","' . $survey_category . '","' . $statistic .  '",' . $facMFL . ');';
        
        try {
            $myData = $this->db->query($query);
            $finalData = $myData->result_array();
//            print($this->db->last_query());die;
            $myData->next_result();
            
            // Dump the extra resultset.
            $myData->free_result();
            
            // Does what it says.
            
            
        }
        catch(exception $ex) {
        }
        return $finalData;
    }

    public function getFacilityInfo()
    {
        $results = $this->db->get_where('facilities', array('fac_mfl' => $this->session->userdata('facilityMFL')));
        $results = $results->result_array();
        foreach ($results as $result) {
            $data[$result['fac_mfl']] = $result;
        }
        return $data;

        // var_dump($this->dataSet);
    }
}
