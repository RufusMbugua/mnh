<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *model to persist data for mnh form
 */

class M_MNH_Survey extends MY_Model
{
    var $id, $attr, $frags, $elements, $noOfInserts, $batchSize, $mfcCode, $suppliesList, $facility, $commodity, $isFacility, $commodityList, $supplierList, $signalFunctionList, $trainingGuidelinesList, $facilityList, $countyList, $districtList, $facilityOwnerList, $specificDistrictList, $facilityLevelList, $facilityTypeList, $isDistrict, $mnhWaterAspectList, $mnhCeocQuestionsList;
    
    function __construct() {
        parent::__construct();
        $this->isFacility = 'false';
        $this->isDistrict = 'false';
        $this->commodityList = $this->countyList = $this->facilityTypeList = $this->specificDistrictList = $this->districtList = $this->facilityLevelList = $this->facilityOwnerList = $this->signalFunctionList = $this->supplierList = $this->trainingGuidelinesList = $this->suppliesList = '';
    }
    
    /*calls the query defined in MY_Model*/
    public function getCommodityNames() {
        $this->commodityList = $this->getAllCommodityNames();
        
        //var_dump($this->commodityList);die;
        return $this->commodityList;
    }
    
    public function getEquipmentNames() {
        $this->equipmentList = $this->getAllEquipmentNames();
        
        //var_dump($this->equipmentList);die;
        return $this->equipmentList;
    }
    
    public function getSupplyNames() {
        $this->suppliesList = $this->getAllSupplyNames('mnh');
        
        //var_dump($this->suppliesList);die;
        return $this->suppliesList;
    }
    
    public function getOtherSupplyNames() {
        $this->suppliesList = $this->getAllSupplyNames('mh');
        
        //var_dump($this->suppliesList);die;
        return $this->suppliesList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getOtherSupplierNames() {
        $this->supplierList = $this->getAllCommoditySupplierNames('mh');
        
        //var_dump($this->supplierList);die;
        return $this->supplierList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhWaterAspectQuestions() {
        $this->mnhWaterAspectList = $this->getQuestionsBySection('mnhw', 'QMNH');
        
        //var_dump($this->mnhWaterAspectList);die;
        return $this->mnhWaterAspectList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhCommunityStrategy() {
        $data = $this->getQuestionsBySection('cms', 'QMNH');
        
        //var_dump($this->mnhWaterAspectList);die;
        return $data;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhCeocAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('ceoc', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhPostNatalAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('pnat', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhWasteDisposalAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('waste', 'QMN');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhCommitteeAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('commi', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhNewbornAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('newb', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhBedsAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('bed', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhServicesAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('serv', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getMnhKangarooAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('kang', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getCommoditySupplierNames() {
        $this->supplierList = $this->getAllCommoditySupplierNames('mnh');
        
        //var_dump($this->supplierList);die;
        return $this->supplierList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getSignalFunctions() {
        $this->signalFunctionList = $this->getAllSignalFunctions();
        
        //var_dump($this->signalFunctionList);die;
        return $this->signalFunctionList;
    }
    
    /*calls the query defined in MY_Model*/
    public function getTrainingGuidelines() {
        $this->trainingGuidelinesList = $this->getAllTrainingGuidelines('mnh');
        
        //var_dump($this->trainingGuidelinesList);die;
        return $this->trainingGuidelinesList;
    }
    
    public function getFacilityNames() {
        $this->facilityList = $this->getAllFacilityNames();
        
        //var_dump($this->facilityList);die;
        return $this->facilityList;
    }
    
    public function getSpecificFacilityNames($mfc) {
        $this->facilityList = $this->getSpecificFacilityNames($mfc);
        
        //var_dump($this->facilityList);die;
        return $this->facilityList;
    }
    
    public function getCountyNames() {
        $this->countyList = $this->getAllCountyNames();
        
        //var_dump($this->countyList);die;
        return $this->countyList;
    }
    
    public function getDistrictNames() {
        $this->districtList = $this->getAllDistrictNames();
        
        //var_dump($this->districtList);die;
        return $this->districtList;
    }
    
    public function getAllDistrictNamesByCounty($county) {
        $this->specificDistrictList = $this->getAllDistrictNamesByCounty($county);
        
        //var_dump($this->districtList);die;
        return $this->specificDistrictList;
    }
    
    public function getFacilityOwnerNames() {
        
        //$this->facilityOwnerList=$this->getAllFacilityOwnerNames();
        $this->facilityOwnerList = $this->getAllFacilityOwnerNames();
        
        //var_dump($this->facilityOwnerList);die;
        return $this->facilityOwnerList;
    }
    
    public function getFacilityLevelNames() {
        $this->facilityLevelList = $this->getAllFacilityLevels();
        
        //var_dump($this->facilityLevelList);die;
        return $this->facilityLevelList;
    }
    
    public function getFacilityTypeNames() {
        $this->facilityTypeList = $this->getAllGovernmentFacilityTypes();
        
        //var_dump($this->facilityTypeList);die;
        return $this->facilityTypeList;
    }
    
    //new mnh sections
    /*calls the query defined in MY_Model*/
    public function getNursesAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('nur', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    //new mnh sections
    /*calls the query defined in MY_Model*/
    public function getMnhHIVTestingAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('hiv', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    public function getMnhPreparednessTestingAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('prep', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    public function getMnhGuidelinesAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('guide', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    public function getMnhJobAidsAspectQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('job', 'QMNH');
        
        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    
    public function verifyRespondedByDistrict() {
        if ($this->input->post()) {
            
            //check if a post was made
            
            // echo '<pre>';print_r($this->input->post()); echo '</pre>';die;
            
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
    
    /*close verifyRespondedByDistrict*/
    
    public function getFacilitiesByDistrict($district) {
        
        //$this->getAllGovernmentOwnedFacilitiesByDistrict($district);
        $this->getAllFacilitiesByDistrict($district);
        
        //echo count($this->districtFacilities);die;
        
        
    }
    
    public function getFacilitiesByDistrictOptions($district) {
        $myOptions = '';
        
        //$this->getAllGovernmentOwnedFacilitiesByDistrict($district);
        $options = $this->getAllFacilitiesByDistrictOptions($district);
        
        //var_dump($options);
        foreach ($options as $option) {
            $myOptions.= '<option value=' . $option['fac_mfl'] . '>' . $option['fac_name'] . '</option>';
        }
        return $myOptions;
        
        //echo count($this->districtFacilities);die;
        
        
    }
    
    /*retrieve facility mfl info*/
    function retrieveFacilityInfo($mfc) {
        
        /*using DQL*/
        try {
            
            //geting server side param: $store=$this->uri->segment(param_position_from_base_url);
            $query = $this->em->createQuery('SELECT f FROM models\Entities\Facilities f WHERE f.facMfl = :facilityMFL');
            $query->setParameter('facilityMFL', $mfc);
            
            $this->formRecords = $query->getArrayResult();
            
            if (max($this->formRecords) != 0) $this->response = array('rData' => $this->formRecords);
            
            //json format
            $this->formRecords = json_encode($this->response);
            
            // var_dump($this->formRecords);
            
            
        }
        catch(exception $ex) {
            
            //ignore
            die($ex->getMessage());
            return false;
        }
        
        return true;
    }
    
    /*close retrieveFacilityInfo($mfc)*/
    
    function addRecord() {
        $s = microtime(true);
        
        /*mark the timestamp at the beginning of the transaction*/
        
        $this->elements = array();
        $this->theIds = array();
        
        if ($this->input->post()) {
            
            //check if a post was made
            
            //just a thought..thread this for performance...??
            
            // $this->updateFacilityInfo();
            //$this->addDeliveryByMonthInfo();
            //$this->addBemoncSignalFunctionsInfo();
            //$this->addCommodityQuantityAvailabilityInfo();
            //$this->addGuidelinesStaffInfo();
            //$this->addCommodityUsageAndStockOutageInfo();
            
            //exit;
            
            
        }
        
        //close the this->input->post
        $e = microtime(true);
        $this->executionTime = round($e - $s, '4');
        $this->rowsInserted = $this->noOfInsertsBatch;
        return $this->response = 'ok';
    }
    
    //end of addRecord()
    // private function addhrinfromation()
    // {
    //     foreach ($this->input->post() as $key => $val) {
    //         if (stripos($key, 'facility')) {
                
    //             $this->frags = explode('_', $key);
    //         }
    //     }
    // }
    private function addDiarrhoeaByMonthInfo() {
        
        //print_r($this -> input -> post());die;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'dnm') !== FALSE) {
                
                //select data for number of deliveries
                $this->attr = $key;
                
                //the attribute name
                
                //split into 2 years: 2012 & 2013 --for later :-)
                
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    // $this->elements[$this->id][$this->attr]=htmlentities($val);
                    $count = 1;
                    
                    // print_r($val);die;
                    foreach ($val as $k => $month) {
                        $month = (int)$month;
                        
                        //echo ($k.' '.$month);die;
                        $this->elements[$count]['monthData'] = htmlentities($month);
                        $this->elements[$count]['monthName'] = htmlentities($k);
                        $count++;
                    }
                } else {
                    $this->elements[$this->attr] = '';
                }
                
                //print $key.' val='.$val.' <br />';
                
                
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = 12;
        
        //labour and delivery Qn5 to 8 will have a single response each
        //print_r($this -> elements);
        
        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {
            
            //echo 'Done'.$i;
            $this->theForm = new \models\Entities\LogDiarrhoea();
            
            //create an object of the model
            
            $this->theForm->setCreatedAt(new DateTime());
            
            /*timestamp option*/
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            /*if no value set, then set to -1*/
            
            //print_r($this->elements);die;
            $this->theForm->setMonth($this->elements[$i]['monthName']);
            $this->theForm->setLdNumber($this->elements[$i]['monthData']);
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                //on the last record to be inserted, log the process and return true;
                if ($i == $this->noOfInsertsBatch) {
                    
                    //die(print $i);
                    // $this->writeAssessmentTrackerLog();
                    return true;
                }
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addDiarrhoeaCasesByMonthInfo()

    //close editQuestions
    private function addMnhCommunityStrategyInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'question') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "questionCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            $this->theForm = $this->getvalueby('models\Entities\LogQuestions', array('ssId' => $this->session->userdata('survey_status'), 'questionCode' => $this->elements[$i]['questionCode']));

            if($this->theForm == NULL)
            {
                $this->theForm = new \models\Entities\LogQuestions();
            }

            //echo "<pre>";print_r($this->theForm);echo "</pre>";die;
            //echo $this -> elements[$i]['mnhCommunityStrategyQCode'];
            //go ahead and persist data posted
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['questionCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['questionCount'])) ? $this->theForm->setLqResponseCount((int)$this->elements[$i]['questionCount']) : $this->theForm->setLqResponseCount(0);
            $this->theForm->setLqResponse('n/a');
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addJobAidsInfo()
    
    private function addBemoncSignalFunctionsInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'bms') !== FALSE) {
                
                //select data for bemonc signal functions
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "bmsfSignalCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print_r($this->elements);die;
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        //echo  $this->noOfInsertsBatch;die;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\BemoncFunctions();
            
            //create an object of the model
            
            $this->theForm->setFacId($this->session->userdata('facilityMFL'));
            
            //echo $this->elements[$i]['bmsfSignalCode'];
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['bmsfSignalFunctionConducted'])) ? $this->theForm->setBemConducted($this->elements[$i]['bmsfSignalFunctionConducted']) : $this->theForm->setBemConducted("N/A");
            (isset($this->elements[$i]['bmsfSignalCode'])) ? $this->theForm->setSfCode($this->elements[$i]['bmsfSignalCode']) : $this->theForm->setSfCode("n/a");
            (isset($this->elements[$i]['bmsfChallenge'])) ? $this->theForm->setChallengeCode($this->elements[$i]['bmsfChallenge']) : $this->theForm->setChallengeCode("N/A");
            
            $this->theForm->setBemCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                //on the last record to be inserted, log the process and return true;
                if ($i == $this->noOfInsertsBatch) {
                    
                    //die(print $i);
                    // $this->writeAssessmentTrackerLog();
                    return true;
                }
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addBemoncSignalFunctionsInfo
    
    private function addCEOCServicesInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'mnhceoc') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "mnhceocAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['mnhceocAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setLqResponseCount(0);
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mnhceocAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['mnhceocAspectResponse']) : $this->theForm->setLqResponse('n/a');
            
            //check if there's a reason
            if (isset($this->elements[$i]['mnhceocReason'])) {
                ($this->elements[$i]['mnhceocReason'] == 'Other') ? $this->theForm->setLqReason($this->elements[$i]['mnhceocReasonOther']) : $this->theForm->setLqReason($this->elements[$i]['mnhceocReason']);
            } else {
                $this->theForm->setLqReason('n/a');
            }
            
            //check if there's a follow up qn
            if (isset($this->elements[$i]['mnhceocFollowUp'])) {
                
                //check if reason is 'Other'
                //if($this -> elements[$i]['mnhceocFollowUp'] != ''
                ($this->elements[$i]['mnhceocFollowUp'] == 'Other') ? $this->theForm->setLqSpecifiedOrFollowUp($this->elements[$i]['mnhceocFollowUpOther']) : $this->theForm->setLqSpecifiedOrFollowUp($this->elements[$i]['mnhceocFollowUp']);
            } else {
                $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            }
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addCEOCServicesInfo()
    
    private function addServicesInfo() {
        $this->elements = array();
        
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //print_r ($this->input->post()); die;
            //For every posted values
            if (strpos($key, 'service') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "serviceAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = $this->getvalueby('models\Entities\LogQuestions', array('ssId' => $this->session->userdata('survey_status'), 'questionCode' => $this->elements[$i]['serviceAspectCode']));

            if($this->theForm == NULL)
            {
                $this->theForm = new \models\Entities\LogQuestions();
            }
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['serviceAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            $this->theForm->setLqResponseCount(0);
            (isset($this->elements[$i]['serviceAspect'])) ? $this->theForm->setLqResponse($this->elements[$i]['serviceAspect']) : $this->theForm->setLqResponse('n/a');
            
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addCommitteeInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'committee') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "committeeAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print var_dump($this->elements);]
        //echo "<pre>";print_r($this->elements);echo "</pre>";die;
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = $this->getvalueby('models\Entities\LogQuestions', array('ssId' => $this->session->userdata('survey_status'), 'questionCode' => $this->elements[$i]['committeeAspectCode']));

            if($this->theForm == NULL)
            {
                $this->theForm = new \models\Entities\LogQuestions();
            }
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['committeeAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['committeeAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['committeeAspectResponse']) : $this->theForm->setLqResponse('n/a');
            
            (isset($this->elements[$i]['committeeCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['committeeCount']) : $this->theForm->setLqResponseCount(0);
            
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addNewbornInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'newborn') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "newbornAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['newbornAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['newbornAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['newbornAspectResponse']) : $this->theForm->setLqResponse('n/a');
            
            (isset($this->elements[$i]['newbornCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['newbornCount']) : $this->theForm->setLqResponseCount(0);
            
            //check if there's a reason
            if (isset($this->elements[$i]['newbornReason'])) {
                ($this->elements[$i]['newbornReason'] == 'Other') ? $this->theForm->setLqReason($this->elements[$i]['newbornReasonOther']) : $this->theForm->setLqReason($this->elements[$i]['newbornReason']);
            } else {
                $this->theForm->setLqReason('n/a');
            }
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addWasteDisposalInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'wastedisposal') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "wastedisposalAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['wastedisposalAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            $this->theForm->setLqResponse('n/a');
            
            $this->theForm->setLqResponseCount(0);
            
            //check if there's a reason
            if (isset($this->elements[$i]['wastedisposalReason'])) {
                ($this->elements[$i]['wastedisposalReason'] == 'Other') ? $this->theForm->setLqReason($this->elements[$i]['wastedisposalReasonOther']) : $this->theForm->setLqReason($this->elements[$i]['wastedisposalReason']);
            } else {
                $this->theForm->setLqReason('n/a');
            }
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addNurseInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'nurse') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "nurseAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['nurseAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            $this->theForm->setLqResponse('n/a');
            (isset($this->elements[$i]['nurseCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['nurseCount']) : $this->theForm->setLqResponseCount(0);
            
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addBedsInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'bed') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "bedAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
           $this->theForm = $this->getvalueby('models\Entities\LogQuestions', array('ssId' => $this->session->userdata('survey_status'), 'questionCode' => $this->elements[$i]['bedAspectCode']));

            if($this->theForm == NULL)
            {
                $this->theForm = new \models\Entities\LogQuestions();
            }
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['bedAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            $this->theForm->setLqResponse('n/a');
            (isset($this->elements[$i]['bedCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['bedCount']) : $this->theForm->setLqResponseCount(0);
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addKangarooInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'kangaroo') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "kangarooAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['kangarooAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            //$this -> theForm -> setResponse('n/a');
            (isset($this->elements[$i]['kangarooAspect'])) ? $this->theForm->setLqResponse($this->elements[$i]['kangarooAspect']) : $this->theForm->setLqResponse('n/a');
            
            $this->theForm->setLqResponseCount(0);
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addGuidelinesInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'mnhGuidelines') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "mnhGuidelinesAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\logQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['mnhGuidelinesAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mnhGuidelinesAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['mnhGuidelinesAspectResponse']) : $this->theForm->setLqResponse('n/a');
            (isset($this->elements[$i]['mnhGuidelinesAspectCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['mnhGuidelinesAspectCount']) : $this->theForm->setLqResponseCount(0);
            
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addHIVTestingInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'mnhHIV') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "mnhHIVAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhHIVReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['mnhHIVAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mnhHIVAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['mnhHIVAspectResponse']) : $this->theForm->setLqResponse('n/a');
            
            //check if there's a reason
            if (isset($this->elements[$i]['mnhHIVReason'])) {
                ($this->elements[$i]['mnhHIVReason'] == 'Other') ? $this->theForm->setReason($this->elements[$i]['mnhHIVReasonOther']) : $this->theForm->setLqReason($this->elements[$i]['mnhHIVReason']);
            } else {
                $this->theForm->setLqReason('n/a');
            }
            $this->theForm->setLqReason('n/a');
            $this->theForm->setLqResponseCount(0);
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addHIVTestingInfo()
    
    private function addPreparednessInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'mnhPreparedness') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "mnhPreparednessAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhPreparednessReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['mnhPreparednessAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mnhPreparednessAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['mnhPreparednessAspectResponse']) : $this->theForm->setLqResponse('n/a');
            
            $this->theForm->setLqReason('n/a');
            $this->theForm->setLqResponseCount(0);
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setlqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addGuidelinesInfo()
    
    private function addJobAidsInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'mnhJobAids') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "mnhJobAidsAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhJobAidsReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['mnhJobAidsAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mnhJobAidsAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['mnhJobAidsAspectResponse']) : $this->theForm->setLqResponse('n/a');
            (isset($this->elements[$i]['mnhJobAidsAspectCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['mnhJobAidsAspectCount']) : $this->theForm->setLqResponseCount(0);
            
            $this->theForm->setLqReason('n/a');
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addJobAidsInfo()
    
    private function addMNHWaterSourceAspectsInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'mnhwAspect') !== FALSE) {
                
                //select data for mnh community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "mnhwAspectCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        // print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();
            
            //create an object of the model
            
            $this->theForm->setQuestionCode($this->elements[$i]['mnhwAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mnhwAspectResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['mnhwAspectResponse']) : $this->theForm->setLqResponse('n/a');
            
            //no input needed for reason for response
            $this->theForm->setLqReason('n/a');
            $this->theForm->setLqResponseCount(0);
            
            //check if there's a follow up answer
            if (isset($this->elements[$i]['mnhwAspectWaterSpecify'])) {
                
                //check if reason is 'Other'
                //if($this -> elements[$i]['mnhceocFollowUp'] != ''
                ($this->elements[$i]['mnhwAspectWaterSpecify'] != '') ? $this->theForm->setLqSpecifiedOrFollowUp($this->elements[$i]['mnhwAspectWaterSpecify']) : $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            } else {
                $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            }
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addMNHWaterSourceAspectsInfo()
    
    private function addCommodityQuantityAvailabilityInfo() {
        $count = $finalCount = 1;
        
        // print_r($this->input->post('supplierName'));die;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'cq') !== FALSE) {
                
                //select data for availability of commodities
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                //  print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "cqCommCode") {
                    
                    //print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    //print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print var_dump($this->elements);
        
        /*foreach($this->elements as $key=>$value){
        if(isset($value['cqNumberOfUnits'])){
        print $value['cqNumberOfUnits'].'<br/>';
        }else{
        print 'Missing...'.'<br/>';
        }
        
        }*/
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        //  print 'Found :'.$this->noOfInsertsBatch;die;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            $this->theForm = $this->getvalueby('models\Entities\AvailableCommodities', array('ssId' => $this->session->userdata('survey_status'), 'commCode' => $this->elements[$i]['cqCommCode']));
            // print_r($this->theForm);
            if($this->theForm == NULL)
            {
                //go ahead and persist data posted
                $this->theForm = new \models\Entities\AvailableCommodities();
            }
            
            //create an object of the model
            
            //  die(print 'Code: '.$this -> session -> userdata('facilityMFL'));
            
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setCommCode($this->elements[$i]['cqCommCode']);
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['cqExpiryDate'])) ? $this->theForm->setAcExpiryDate($this->elements[$i]['cqExpiryDate']) : $this->theForm->setAcExpiryDate('n/a');
            (isset($this->elements[$i]['cqNumberOfUnits'])) ? $this->theForm->setAcQuantity($this->elements[$i]['cqNumberOfUnits']) : $this->theForm->setAcQuantity(-1);
            ($this->input->post('supplierName') != '') ? $this->theForm->setSupplierCode($this->input->post('supplierName')) : $this->theForm->setSupplierCode("Other");
            (isset($this->elements[$i]['cqReason']) || $this->elements[$i]['cqReason'] == '') ? $this->theForm->setAcReasonUnavailable($this->elements[$i]['cqReason']) : $this->theForm->setAcReasonUnavailable("N/A");
            (isset($this->elements[$i]['cqAvailability'])) ? $this->theForm->setAcAvailability($this->elements[$i]['cqAvailability']) : $this->theForm->setAcAvailability("N/A");
            (isset($this->elements[$i]['cqLocation'])) ? $this->theForm->setAcLocation($this->elements[$i]['cqLocation']) : $this->theForm->setAcLocation("N/A");
            $this->theForm->setAcCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                // die(print 'I completed well after iteration: '.$i);
                
                //print 'I just saved rec no: '.$i;
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addCommodityQuantityAvailabilityInfo
    
    private function addSuppliesQuantityAvailabilityInfo() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'sq') !== FALSE) {
                
                //select data for availability of commodities
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                //  print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "sqsupplyCode") {
                    
                    //print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    //print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        //  print 'Found :'.$this->noOfInsertsBatch;die;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\AvailableSupplies();
            
            //create an object of the model
            
            //  die(print 'Code: '.$this -> session -> userdata('facilityMFL'));
            
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setSupplyCode($this->elements[$i]['sqsupplyCode']);
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['sqNumberOfUnits'])) ? $this->theForm->setAsQuantity($this->elements[$i]['sqNumberOfUnits']) : $this->theForm->setAsQuantity(-1);
            ($this->input->post('supplierName') != '') ? $this->theForm->setSupplierCode($this->input->post('supplierName')) : $this->theForm->setSupplierCode("Other");
            if (isset($this->elements[$i]['sqReason'])) {
                ($this->elements[$i]['sqReason'] != '') ? $this->theForm->setAsReasonUnavailable($this->elements[$i]['sqReason']) : $this->theForm->setAsReasonUnavailable("N/A");
            } else {
                $this->theForm->setAsReasonUnavailable("N/A");
            }
            
            (isset($this->elements[$i]['sqAvailability'])) ? $this->theForm->setAsAvailability($this->elements[$i]['sqAvailability']) : $this->theForm->setAsAvailability("N/A");
            (isset($this->elements[$i]['sqLocation'])) ? $this->theForm->setAsLocation($this->elements[$i]['sqLocation']) : $this->theForm->setAsLocation("N/A");
            $this->theForm->setAsCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addSuppliesQuantityAvailabilityInfo
    
    private function addEquipmentQuantityAvailabilityInfo() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'eq') !== FALSE) {
                
                //select data for availability of commodities
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                //  print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "eqCode") {
                    
                    //print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    //print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print var_dump($this->elements);
        
        /*foreach($this->elements as $key=>$value){
        if(isset($value['cqNumberOfUnits'])){
        print $value['cqNumberOfUnits'].'<br/>';
        }else{
        print 'Missing...'.'<br/>';
        }
        
        }*/
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        //  print 'Found :'.$this->noOfInsertsBatch;die;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\AvailableEquipments();
            
            //create an object of the model
            
            //  die(print 'Code: '.$this -> session -> userdata('facilityMFL'));
            
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setEqCode($this->elements[$i]['eqCode']);
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['eqAvailability'])) ? $this->theForm->setAeAvailability($this->elements[$i]['eqAvailability']) : $this->theForm->setAeAvailability("N/A");
            (isset($this->elements[$i]['eqLocation'])) ? $this->theForm->setAeLocation($this->elements[$i]['eqLocation']) : $this->theForm->setAeLocation("N/A");
            (isset($this->elements[$i]['eqQtyFullyFunctional']) || $this->elements[$i]['eqQtyFullyFunctional'] != '') ? $this->theForm->setAeFullyFunctional($this->elements[$i]['eqQtyFullyFunctional']) : $this->theForm->setAeFullyFunctional(-1);
            (isset($this->elements[$i]['eqQtyPartiallyFunctional'])) ? $this->theForm->setAePartiallyFunctional($this->elements[$i]['eqQtyFullyFunctional']) : $this->theForm->setAePartiallyFunctional(-1);
            (isset($this->elements[$i]['eqQtyNonFunctional']) || $this->elements[$i]['eqQtyNonFunctional'] != '') ? $this->theForm->setAeNonFunctional($this->elements[$i]['eqQtyFullyFunctional']) : $this->theForm->setAeNonFunctional(-1);
            
            $this->theForm->setAeCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                // die(print 'I completed well after iteration: '.$i);
                
                //print 'I just saved rec no: '.$i;
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    private function addMchStaffTrainingInfo() {
        
        //echo "<pre>";print_r($this->input->post());echo "</pre>";die;
        $this->elements = array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'mchTraining') !== FALSE) {
                
                //select data for mch community strategy
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "mchTrainingTotalStaffMembersStillWorking") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                if ($this->attr == "mchTrainingStaff") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $staff = $val;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                if (is_array($val)) {
                    
                    //var_dump($val);die;
                    foreach ($val as $guide => $trained) {
                        
                        $data[$staff][$this->attr][$guide] = $trained;
                    }
                } else {
                    $data[$staff][$this->attr] = $val;
                }
                
                // die;
                
                //collect key and value to an array
                /* if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                }*/
            }
        }
        
        //echo "<pre>";print_r($data);echo "</pre>";die;
        $counter = 0;
        foreach ($data as $value) {
            foreach ($value['mchTrainingBefore'] as $key => $training) {
                $counter++;
                $newData[$counter]['mchTrainingStaff'] = $value['mchTrainingStaff'];
                $newData[$counter]['mchTrainingTotalinFacility'] = $value['mchTrainingTotalinFacility'];
                $newData[$counter]['mchTrainingTotalAvailableOnDuty'] = $value['mchTrainingTotalAvailableOnDuty'];
                $newData[$counter]['mchTrainingTotalStaffMembersStillWorking'] = $value['mchTrainingTotalStaffMembersStillWorking'];
                $newData[$counter]['mchGuideline'] = $key;
                $newData[$counter]['mchBefore'] = $training;
                $newData[$counter]['mchAfter'] = $value['mchTrainingAfter'][$key];
            }
        }
        
        $this->elements = $newData;
        
        //echo "<pre>";print_r( $this->elements);echo "</pre>";die;
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $counter;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\TrainingGuidelinesN();
            
            //create an object of the model
            
            //$this -> elements[$i]['mchCommunityStrategyQCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mchTrainingStaff']) && $this->elements[$i]['mchTrainingStaff'] != '') ? $this->theForm->setTgStaff($this->elements[$i]['mchTrainingStaff']) : $this->theForm->setTgStaff(-1);
            (isset($this->elements[$i]['mchTrainingTotalinFacility']) && $this->elements[$i]['mchTrainingTotalinFacility'] != '') ? $this->theForm->setTgTotalFacility($this->elements[$i]['mchTrainingTotalinFacility']) : $this->theForm->setTgTotalFacility(-1);
            (isset($this->elements[$i]['mchTrainingTotalAvailableOnDuty']) && $this->elements[$i]['mchTrainingTotalAvailableOnDuty'] != '') ? $this->theForm->setTgTotalDuty($this->elements[$i]['mchTrainingTotalAvailableOnDuty']) : $this->theForm->setTgTotalDuty(-1);
            (isset($this->elements[$i]['mchTrainingTotalStaffMembersStillWorking']) && $this->elements[$i]['mchTrainingTotalStaffMembersStillWorking'] != '') ? $this->theForm->setTgWorking($this->elements[$i]['mchTrainingTotalStaffMembersStillWorking']) : $this->theForm->setTgWorking(-1);
            (isset($this->elements[$i]['mchGuideline']) && $this->elements[$i]['mchGuideline'] != '') ? $this->theForm->setGuideCode($this->elements[$i]['mchGuideline']) : $this->theForm->setGuideCode(-1);
            (isset($this->elements[$i]['mchBefore']) && $this->elements[$i]['mchBefore'] != '') ? $this->theForm->setTgBefore($this->elements[$i]['mchBefore']) : $this->theForm->setTgBefore(-1);
            (isset($this->elements[$i]['mchAfter']) && $this->elements[$i]['mchAfter'] != '') ? $this->theForm->setTgAfter($this->elements[$i]['mchAfter']) : $this->theForm->setTgAfter(-1);
            $this->theForm->setTgCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addMchCommunityStrategyInfo()
    
    private function addCommodityUsageAndStockOutageInfo() {
        $this->elements = array();
        $count = $finalCount = 1;
        
        //var_dump($this->input->post());die;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'usoc') !== FALSE) {
                
                //select data for availability of commodities
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                // print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "usoccommCode") {
                    
                    //print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    //print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print var_dump($this->elements);die;
        
        /*foreach($this->elements as $key=>$value){
        if(isset($value['cqNumberOfUnits'])){
        print $value['cqNumberOfUnits'].'<br/>';
        }else{
        print 'Missing...'.'<br/>';
        }
        
        }*/
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; $i++) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogCommodityStockOuts();
            
            //create an object of the model
            
            $this->theForm->setFacId($this->session->userdata('facilityMFL'));
            $this->theForm->setCommId($this->elements[$i]['usoccommCode']);
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['usocUsage'])) ? $this->theForm->setLcsoUsage($this->elements[$i]['usocUsage']) : $this->theForm->setLcsoUsage(-1);
            (isset($this->elements[$i]['usocTimesUnavailable']) || $this->elements[$i]['usocTimesUnavailable'] == '') ? $this->theForm->setLcsoUnavailableTimes($this->elements[$i]['usocTimesUnavailable']) : $this->theForm->setLcsoUnavailableTimes('n/a');
            (isset($this->elements[$i]['usocWhatHappened'])) ? $this->theForm->setLcsoOptionOnOutage($this->elements[$i]['usocWhatHappened']) : $this->theForm->setLcsoOptionOnOutage('n/a');
            $this->theForm->setLcsoCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print $i);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print $i);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addCommodityUsageAndStockOutageInfo
    private function addNoReasonForDeliveries() {
        $count = $finalCount = 1;

        // echo "<pre>";print_r($this->input->post());echo "</pre>";die;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'facRsn') !== FALSE) {
                
                //select data for availability of commodities
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                // print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "facRsnNoDeliveriesCode") {
                    
                    //print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    //print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //echo '<pre>';print_r($this->elements);echo '</pre>';die;
        //exit;
        //get the highest value of the array that will control the number of inserts to be done
        
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //echo $this -> elements[$i]['mnhceocReason'];exit;
            //go ahead and persist data posted
            $this->theForm = $this->getvalueby('models\Entities\LogQuestions', array('ssId' => $this->session->userdata('survey_status'), 'questionCode' => $this->elements[$i]['facRsnNoDeliveriesCode']));

            if($this->theForm == NULL)
            {
                $this->theForm = new \models\Entities\LogQuestions();
            }
            
            //create an object of the model
            
            $this->theForm->setLqReason($this->elements[$i]['facRsnNoDeliveries']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            //(isset($this->elements[$i]['mnhGuidelinesAspectCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['mnhGuidelinesAspectCount']) : $this->theForm->setLqResponseCount(0);
            $this->theForm->setLqResponseCount('n/a');
            $this->theForm->setLqResponse('No');
            $this->theForm->setQuestionCode($this->elements[$i]['facRsnNoDeliveriesCode']);
            
            $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            
            $this->theForm->setLqCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                    
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addNoReasonForDeliveries()
    
    private function addSuppliesUsageAndStockOutageInfo() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'usos') !== FALSE) {
                
                //select data for availability of commodities
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                // print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "usossupplyCode") {
                    
                    //print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    //print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; $i++) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogCommodityStockOuts();
            
            //create an object of the model
            
            $this->theForm->setFacId($this->session->userdata('facilityMFL'));
            $this->theForm->setCommId($this->elements[$i]['usosSupplyCode']);
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['usosUsage'])) ? $this->theForm->setLcsoUsage($this->elements[$i]['usosUsage']) : $this->theForm->setLcsoUsage(-1);
            (isset($this->elements[$i]['usosTimesUnavailable']) || $this->elements[$i]['usosTimesUnavailable'] == '') ? $this->theForm->setLcsoUnavailableTimes($this->elements[$i]['usosTimesUnavailable']) : $this->theForm->setLcsoUnavailableTimes('n/a');
            (isset($this->elements[$i]['usosWhatHappened'])) ? $this->theForm->setLcsoOptionOnOutage($this->elements[$i]['usosWhatHappened']) : $this->theForm->setLcsoOptionOnOutage('n/a');
            $this->theForm->setLcsoCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print $i);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print $i);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    //close addSuppliesUsageAndStockOutageInfo
    
    private function addResourceAvailabilityInfo() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'hw') !== FALSE) {
                
                //select data for availability of commodities
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //stringify any array value
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                //  print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                
                //mark the end of 1 row...for record count
                if ($this->attr == "hweqCode") {
                    
                    //print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    //print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //print var_dump($this->elements);
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        //  print 'Found :'.$this->noOfInsertsBatch;die;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\AvailableResources();
            
            //create an object of the model
            
            //die(print 'Code: '.$this -> session -> userdata('facilityMFL'));
            
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setEquipmentCode($this->elements[$i]['hweqCode']);
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['hwNumberOfUnits'])) ? $this->theForm->setArQuantity($this->elements[$i]['hwNumberOfUnits']) : $this->theForm->setArQuantity(-1);
            ($this->input->post('supplierName') != '') ? $this->theForm->setSupplierCode($this->input->post('supplierName')) : $this->theForm->setSupplierCode("Other");
            (isset($this->elements[$i]['hwReason'])) ? $this->theForm->setArReasonUnavailable($this->elements[$i]['hwReason']) : $this->theForm->setArReasonUnavailable("N/A");
            (isset($this->elements[$i]['hwAvailability'])) ? $this->theForm->setArAvailability($this->elements[$i]['hwAvailability']) : $this->theForm->setArAvailability("N/A");
            (isset($this->elements[$i]['hwLocation'])) ? $this->theForm->setArLocation($this->elements[$i]['hwLocation']) : $this->theForm->setArLocation("N/A");
            
            //$this -> theForm -> setSource($this -> elements[$i]['hwSource']);
            $this->theForm->setArCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    
                    //on the last record to be inserted, log the process and return true;
                    if ($i == $this->noOfInsertsBatch) {
                        
                        //die(print 'Limit: '.$this->noOfInsertsBatch);
                        //$this->writeAssessmentTrackerLog();
                        return true;
                    }
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
        
        
    }
    
    private function addQuestionsInfo() {
        $this->elements=array();
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            //For every posted values
            if (strpos($key, 'question') !== FALSE) {
                 //select data for bemonc signal functions
                //we separate the attribute name from the number

                $this->frags = explode("_", $key);

                //$this->id = $this->frags[1];  // the id

                $this->id = $count;

                // the id

                $this->attr = $this->frags[0];

                //the attribute name

                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                if (is_array($val)) {
                    $val = implode(',', $val);
                }

                //mark the end of 1 row...for record count
                if ($this->attr == "questionCode") {

                    // print 'count at:'.$count.'<br />';

                    $finalCount = $count;
                    $count++;

                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';

                }

                //collect key and value to an array
                if (!empty($val)) {

                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);

                    //$this->elements[$this->attr]=htmlentities($val);

                } else {
                    $this->elements[$this->id][$this->attr] = '';

                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');

                }
            }
        }
         //close foreach ($this -> input -> post() as $key => $val)
        //echo '<pre>';print_r($this->elements);echo '</pre>';die;

        //exit;

        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;

        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {

            $this->theForm = $this->getvalueby('models\Entities\LogQuestions', array('ssId' => $this->session->userdata('survey_status'), 'questionCode' => $this->elements[$i]['questionCode']));

            if($this->theForm == NULL)
            {
                $this->theForm = new \models\Entities\LogQuestions();
            }

            //echo "<pre>";print_r($this->theForm);echo "</pre>";die;
            //go ahead and persist data posted

            //create an object of the model

            //$this -> theForm -> setIdMCHQuestionLog($this->elements[$i]['ortcAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));

            //check if that key exists, else set it to some default value

            (array_key_exists('questionResponse', $this->elements[$i])) ? $this->theForm->setLqResponse($this->elements[$i]['questionResponse']) : $this->theForm->setLqResponse('n/a');
            (array_key_exists('questionResponseOther', $this->elements[$i]) && $this->elements[$i]['questionResponseOther']!='' ) ? $this->theForm->setLqResponse($this->elements[$i]['questionResponseOther']) : $x=1;

            (array_key_exists('questionCount', $this->elements[$i])) ? $this->theForm->setLqResponseCount($this->elements[$i]['questionCount']) : $this->theForm->setLqResponseCount(-1);
            (array_key_exists('questionReason', $this->elements[$i])) ? $this->theForm->setLqReason($this->elements[$i]['questionReason']) : $this->theForm->setLqReason('n/a');
            (array_key_exists('questionSpecified', $this->elements[$i])) ? $this->theForm->setLqSpecifiedOrFollowUp($this->elements[$i]['questionSpecified']) : $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            $this->theForm->setQuestionCode($this->elements[$i]['questionCode']);
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            $this->theForm->setLqCreated(new DateTime());

            /*timestamp option*/
            $this->em->persist($this->theForm);

            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {

                    $this->em->flush();
                    $this->em->clear();

                    //detaches all objects from doctrine
                    //return true;

                }
                catch(Exception $ex) {

                    die($ex->getMessage());
                    return false;

                    /*display user friendly message*/
                }
                 //end of catch


            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {

                //total records less than a batch, insert all of them
                try {

                    $this->em->flush();
                    $this->em->clear();

                    //detactes all objects from doctrine
                    //return true;

                }
                catch(Exception $ex) {

                    die($ex->getMessage());
                    return false;

                    /*display user friendly message*/
                }
                 //end of catch

                //on the last record to be inserted, log the process and return true;
                if ($i == $this->noOfInsertsBatch) {

                    //die(print $i);
                    // $this->writeAssessmentTrackerLog();
                    return true;
                }
            }

            //end of batch condition

        }
         //end of innner loop


    }
    
    private function addIndicatorInfo() {
        $count = $finalCount = 1;
        $this->elements = array();
        foreach ($this->input->post() as $key => $val) {
            
            //For every posted values
            if (strpos($key, 'indicator') !== FALSE) {
                
                //select data for bemonc signal functions
                //we separate the attribute name from the number
                
                $this->frags = explode("_", $key);
                
                //$this->id = $this->frags[1];  // the id
                
                $this->id = $count;
                
                // the id
                
                $this->attr = $this->frags[0];
                
                //the attribute name
                
                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                
                //mark the end of 1 row...for record count
                if ($this->attr == "indicatorCode") {
                    
                    // print 'count at:'.$count.'<br />';
                    
                    $finalCount = $count;
                    $count++;
                    
                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';
                    
                    
                }
                
                //collect key and value to an array
                if (!empty($val)) {
                    
                    //We then store the value of this attribute for this element.
                    $this->elements[$this->id][$this->attr] = htmlentities($val);
                    
                    //$this->elements[$this->attr]=htmlentities($val);
                    
                    
                } else {
                    $this->elements[$this->id][$this->attr] = '';
                    
                    //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');
                    
                    
                }
            }
        }
        
        //close foreach ($this -> input -> post() as $key => $val)
        //echo '<pre>';print_r($this->elements);echo '</pre>';die;
        
        //exit;
        
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;
        
        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {
            
            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogIndicators();
            
            //create an object of the model
            
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            
            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['indicatorhcwResponse'])) ? $this->theForm->setLiHcwresponse($this->elements[$i]['indicatorhcwResponse']) : $this->theForm->setLiHcwresponse("N/A");
            (isset($this->elements[$i]['indicatorhcwFindings'])) ? $this->theForm->setLiHcwfindings($this->elements[$i]['indicatorhcwFindings']) : $this->theForm->setLiHcwfindings("N/A");
            (isset($this->elements[$i]['indicatorassessorResponse'])) ? $this->theForm->setLiAssessorresponse($this->elements[$i]['indicatorassessorResponse']) : $this->theForm->setLiAssessorresponse("N/A");
            (isset($this->elements[$i]['indicatorassessorFindings'])) ? $this->theForm->setLiAssessorfindings($this->elements[$i]['indicatorassessorFindings']) : $this->theForm->setLiAssessorfindings("N/A");
            $this->theForm->setIndicatorCode($this->elements[$i]['indicatorCode']);
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            $this->theForm->setLiCreated(new DateTime());
            
            /*timestamp option*/
            $this->em->persist($this->theForm);
            
            //now do a batched insert, default at 5
            $this->batchSize = 5;
            if ($i % $this->batchSize == 0) {
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detaches all objects from doctrine
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                
            } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {
                
                //total records less than a batch, insert all of them
                try {
                    
                    $this->em->flush();
                    $this->em->clear();
                    
                    //detactes all objects from doctrine
                    //return true;
                    
                    
                }
                catch(Exception $ex) {
                    
                    die($ex->getMessage());
                    return false;
                    
                    /*display user friendly message*/
                }
                
                //end of catch
                
                //on the last record to be inserted, log the process and return true;
                if ($i == $this->noOfInsertsBatch) {
                    
                    //die(print $i);
                    // $this->writeAssessmentTrackerLog();
                    return true;
                }
            }
            
            //end of batch condition
            
            
        }
        
        //end of innner loop
    }
    
    private function addMnhHRInfo() {
       $count = $finalCount = 1;
       foreach ($this->input->post() as $key => $val) {
            //For every posted values
           if (strpos($key, 'contactfacility') !== FALSE) {
            //var_dump($val);die;
                //select data for availability of commodities
               //we separate the attribute name from the number

               $this->frags = explode("_", $key);

               //$this->id = $this->frags[1];  // the id

               $this->id = $count;

               // the id

               $this->attr = $this->frags[0];

               //the attribute name

               //stringify any array value
               if (is_array($val)) {
                   $val = implode(',', $val);
               }

               // print $key.' ='.$val.' <br />';
               //print 'ids: '.$this->id.'<br />';

               //mark the end of 1 row...for record count
               if ($this->attr == "contactfacilityopdemail") {

                   //print 'count at:'.$count.'<br />';

                   $finalCount = $count;
                   $count++;

                   //print 'count at:'.$count.'<br />';
                   //print 'final count at:'.$finalCount.'<br />';
                   //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';

               }

               //collect key and value to an array
               if (!empty($val)) {

                   //We then store the value of this attribute for this element.
                   $this->elements[$this->id][$this->attr] = htmlentities($val);

                   //$this->elements[$this->attr]=htmlentities($val);

               } else {
                   $this->elements[$this->id][$this->attr] = '';

                   //$this->element=array('id'=>$this->id,'name'=>$this->attr,'value'=>'');

               }
           }
       }
        //close foreach ($this -> input -> post() as $key => $val)
       // print_r($this->elements);die;
       //get the highest value of the array that will control the number of inserts to be done
       $this->noOfInsertsBatch = $finalCount;

       for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {

           //go ahead and persist data posted
           $this->theForm = $this->getvalueby('models\Entities\HrInformation', array('ssId' => $this->session->userdata('survey_status'), 'facilityMfl' => $this->session->userdata('facilityMFL')));

            if($this->theForm == NULL)
            {
                $this->theForm = new \models\Entities\HrInformation();
            }

           //create an object of the model

           //$this->theForm->setStrategyCode(1);
            //$this -> elements[$i]['mchCommunityStrategyQCode']);
           $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));

           //check if that key exists, else set it to some default value
           (isset($this->elements[$i]['contactfacilityInchargename']) && $this->elements[$i]['contactfacilityInchargename'] != '') ? $this->theForm->setFacilityInchargeName($this->elements[$i]['contactfacilityInchargename']) : $this->theForm->setFacilityInchargeName('N/A');
           (isset($this->elements[$i]['contactfacilityInchargemobile']) && $this->elements[$i]['contactfacilityInchargemobile'] != '') ? $this->theForm->setFacilityInchargeMobile($this->elements[$i]['contactfacilityInchargemobile']) : $this->theForm->setFacilityInchargeMobile(-1);
           (isset($this->elements[$i]['contactfacilityInchargeemail']) && $this->elements[$i]['contactfacilityInchargeemail'] != '') ? $this->theForm->setFacilityInchargeEmailaddress($this->elements[$i]['contactfacilityInchargeemail']) : $this->theForm->setFacilityInchargeEmailaddress('N/A');
           (isset($this->elements[$i]['contactfacilityMchname']) && $this->elements[$i]['contactfacilityMchname'] != '') ? $this->theForm->setMchInchargeName($this->elements[$i]['contactfacilityMchname']) : $this->theForm->setMchInchargeName('N/A');
           (isset($this->elements[$i]['contactfacilityMchmobile']) && $this->elements[$i]['contactfacilityMchmobile'] != '') ? $this->theForm->setMchInchargeMobile($this->elements[$i]['contactfacilityMchmobile']) : $this->theForm->setMchInchargeMobile('-1');
           (isset($this->elements[$i]['contactfacilityMchemail']) && $this->elements[$i]['contactfacilityMchemail'] != '') ? $this->theForm->setMchInchargeEmailaddress($this->elements[$i]['contactfacilityMchemail']) : $this->theForm->setMchInchargeEmailaddress('N/A');

           (isset($this->elements[$i]['contactfacilityMaternityname']) && $this->elements[$i]['contactfacilityMaternityname'] != '') ? $this->theForm->setMaternityInchargeName($this->elements[$i]['contactfacilityMaternityname']) : $this->theForm->setMchInchargeName('N/A');
           (isset($this->elements[$i]['contactfacilityMaternitymobile']) && $this->elements[$i]['contactfacilityMaternitymobile'] != '') ? $this->theForm->setMaternityInchargeMobile($this->elements[$i]['contactfacilityMaternitymobile']) : $this->theForm->setMchInchargeMobile('-1');
           (isset($this->elements[$i]['contactfacilityMaternityemail']) && $this->elements[$i]['contactfacilityMaternityemail'] != '') ? $this->theForm->setMaternityInchargeEmailaddress($this->elements[$i]['contactfacilityMaternityemail']) : $this->theForm->setMchInchargeEmailaddress('N/A');

           $this->theForm->setCreated(new DateTime());
           $this->theForm->setSsId((int)$this->session->userdata('survey_status'));

           /*timestamp option*/
           $this->em->persist($this->theForm);

           //now do a batched insert, default at 5
           $this->batchSize = 5;
           if ($i % $this->batchSize == 0) {
               try {

                   $this->em->flush();
                   $this->em->clear();

                   //detaches all objects from doctrine

                   //on the last record to be inserted, log the process and return true;
                   if ($i == $this->noOfInsertsBatch) {

                       //die(print 'Limit: '.$this->noOfInsertsBatch);
                       //$this->writeAssessmentTrackerLog();
                       return true;
                   }

                   //return true;

               }
               catch(Exception $ex) {

                   //die($ex -> getMessage());
                   return false;

                   /*display user friendly message*/
               }
                //end of catch


           } else if ($i < $this->batchSize || $i > $this->batchSize || $i == $this->noOfInsertsBatch && $this->noOfInsertsBatch - $i < $this->batchSize) {

               //total records less than a batch, insert all of them
               try {

                   $this->em->flush();
                   $this->em->clear();

                   //detactes all objects from doctrine

                   //on the last record to be inserted, log the process and return true;
                   if ($i == $this->noOfInsertsBatch) {

                       //die(print 'Limit: '.$this->noOfInsertsBatch);
                       //$this->writeAssessmentTrackerLog();
                       return true;
                   }

                   //return true;

               }
               catch(Exception $ex) {

                   //die($ex->getMessage());
                   return false;

                   /*display user friendly message*/
               }
                //end of catch


           }

           //end of batch condition

       }
        //end of innner loop

   }
    //close addMNHHRInfo()

    function store_data() {
        
        /*check assessment tracker log*/
        if ($this->input->post()) {
            $step = $this->input->post('step_name', TRUE);
            switch ($step) {
                case 'section-1':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    //print var_dump($this->section);
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        // if ($this->updateFacilityInfo()==true && 
                           if($this->addQuestionsInfo() == true && $this->addServicesInfo() == true && $this->addCommitteeInfo() == true && $this->addBedsInfo() == true && $this->addNoReasonForDeliveries() == true && $this->addMnhHRInfo() == true) {
                            
                            //Defined in MY_Model
                            //if($this->addNoReasonForDeliveries()== true){
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        return $this->response = 'true';
                    }
                    
                    //return $this -> response = 'true';
                    break;

                case 'section-2':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    
                    //print var_dump($this->section);
                    
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {

                        
                        /* if ($this -> addBemoncSignalFunctionsInfo() == true ) {//defined in this model
                         $this -> writeAssessmentTrackerLog();*/
                        if ($this->addKangarooInfo() == true && $this->addNewbornInfo() == true && $this->addHIVTestingInfo() == true && $this->addPreparednessInfo() == true && $this->addCEOCServicesInfo() == true && $this->addDiarrhoeaByMonthInfo() == true && $this->addBemoncSignalFunctionsInfo() == true) {
                            

                            //} == true  && $this->addDiarrhoeaByMonthInfo() == true
                            //) {
                            //defined in this model
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        return $this->response = 'true';
                    }
                    break;

                case 'section-3':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    
                    //print var_dump($this->section);
                    
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addQuestionsInfo() == true && $this->addIndicatorInfo() == true
                        
                        //&& $this->addGuidelinesInfo() == true
                        ) {
                            
                            //$this->addCommodityQuantityAvailabilityInfo() == true) {
                            //defined in this model
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        return $this->response = 'true';
                    }
                    break;

                case 'section-4':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    
                    //print var_dump($this->section);
                    
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addMchStaffTrainingInfo() == true) {
                            
                            //defined in this model
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        return $this->response = 'true';
                    }
                    break;

                case 'section-5':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    
                    //print var_dump($this->section);
                    
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addCommodityQuantityAvailabilityInfo() == true) {
                            
                            //defined in this model
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        return $this->response = 'true';
                    }
                    break;

                case 'section-6':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    
                    //print var_dump($this->section);
                    
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addCommodityUsageAndStockOutageInfo() == true) {
                            
                            //$this->addEquipmentQuantityAvailabilityInfo() == true && $this->addSuppliesQuantityAvailabilityInfo() == true && $this->addMNHWaterSourceAspectsInfo() == true) {
                            
                            //defined in this model
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        return $this->response = 'true';
                    }
                    break;

                case 'section-7':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    
                    //print var_dump($this->section);
                    
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addResourceAvailabilityInfo() == true && $this->addEquipmentQuantityAvailabilityInfo() == true && $this->addSuppliesQuantityAvailabilityInfo() == true
                         //&& $this->addSuppliesUsageAndStockOutageInfo() == true
                         && $this->addWasteDisposalInfo() == true) {
                            
                            //defined in this model
                            $this->writeAssessmentTrackerLog();
                            
                            //update facility survey status
                            $this->markSurveyStatusAsComplete();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        $this->markSurveyStatusAsComplete();
                        return $this->response = 'true';
                    }
                    
                    break;

                case 'section-8':
                    
                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));
                    
                    //print var_dump($this->section);
                    
                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addQuestionsInfo() == true) {
                            
                            // && $this->addSuppliesUsageAndStockOutageInfo() == true && $this->addWasteDisposalInfo() == true) {
                            
                            //defined in this model
                            $this->writeAssessmentTrackerLog();
                            
                            //update facility survey status
                            $this->markSurveyStatusAsComplete();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        
                        //die('Entry exsits');
                        $this->markSurveyStatusAsComplete();
                        return $this->response = 'true';
                    }
                    
                    break;
            }
            
            //close switch
            //print var_dump($this->input->post());
            
            //return $this -> response = 'true';
            
            
        }
    }
}

//end of class M_MNH_Survey
