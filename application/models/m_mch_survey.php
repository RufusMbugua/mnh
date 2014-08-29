<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *model to persist data for mch form
 */

class M_MCH_Survey extends MY_Model
{
    var $id, $attr, $frags, $elements, $noOfInserts, $batchSize, $mfcCode, $facility, $commodity, $isFacility, $ortAspectsList, $mchGuidelineAvailabilityList, $commodityList, $trainingGuidelinesList, $equipmentList, $suppliesList, $indicatorList, $treatmentList, $healthServicesList, $treatmentcommodityList;

    function __construct() {
        parent::__construct();
        $this->isFacility = 'false';
    }

    /*calls the query defined in MY_Model*/
    public function getOrtAspectQuestions($for) {
        $this->ortAspectsList = $this->getAllOrtAspects($for);

        //var_dump($this->ortAspectsList);die;
        return $this->ortAspectsList;
    }

    /*calls the query defined in MY_Model*/
    public function getAccessChallenges() {
        $this->challengeList = $this->getAllAccessChallenges();

        //var_dump($this->ortAspectsList);die;
        return $this->challengeList;
    }

        /*calls the query defined in MY_Model*/
    public function getMchHealthQuestions() {
        $this->healthServicesList = $this->getQuestionsBySection('hs', 'QUC');

        //var_dump($this->healthServicesList);die;
        return $this->healthServicesList;
    }
     /*calls the query defined in MY_Model*/
    public function getTreatmentFor($type)

    {
        $this->treatmentList = $this->getTreatmentsByType($type);

        //var_dump($this->treatmentList);
        return $this->treatmentList;
    }
    /*calls the query defined in MY_Model*/
    public function getmchConsultationQuestions() {
        $this->mnhCeocQuestionsList = $this->getQuestionsBySection('imci', 'QUC');

        //var_dump($this->mnhCeocQuestionsList);die;
        return $this->mnhCeocQuestionsList;
    }
    /*calls the query defined in MY_Model*/
    public function getMchCommunityStrategyQuestions() {
        $this->ortAspectsList = $this->getQuestionsBySection('cms', 'QUC');

        //var_dump($this->ortAspectsList);die;
        return $this->ortAspectsList;
    }


    /*calls the query defined in MY_Model*/
    public function getGuidelineAvailabilityQuestions($for) {
        $this->mchGuidelineAvailabilityList = $this->getAllOrtAspects($for);

        //var_dump($this->ortAspectsList);die;
        return $this->mchGuidelineAvailabilityList;
    }

    /*calls the query defined in MY_Model*/
    public function getCommodityNames() {
        $this->commodityList = $this->getAllCommodityNames('ch');

        //var_dump($this->commodityList);die;
        return $this->commodityList;
    }

    public function getTreatmentCommodities()
    {
        $this->treatmentcommodityList = $this->getTreatmentCommodity('ch');

        return $this->treatmentcommodityList;
    }

    /*calls the query defined in MY_Model*/
    public function getBundlingNames() {
        $this->commodityList = $this->getAllCommodityNames('bun');

        //var_dump($this->commodityList);die;
        return $this->commodityList;
    }

    /*calls the query defined in MY_Model*/
    public function getCommoditySupplierNames() {
        $this->supplierList = $this->getAllCommoditySupplierNames('mnh');

        //var_dump($this->supplierList);die;
        return $this->supplierList;
    }

    /*calls the query defined in MY_Model*/
    public function getOtherSupplierNames() {
        $this->supplierList = $this->getAllCommoditySupplierNames('mch');

        //var_dump($this->supplierList);die;
        return $this->supplierList;
    }
    /*calls the query defined in MY_Model*/
    public function getEverySupplyName() {
        $this->supplierList = $this->getTotalSupplyNames();

        //var_dump($this->supplierList);die;
        return $this->supplierList;
    }

    /*calls the query defined in MY_Model*/
    public function getAllHWSources() {
        $this->supplierList = $this->getAllSources('sou');

        //var_dump($this->supplierList);die;
        return $this->supplierList;
    }

    /*calls the query defined in MY_Model*/
    public function getTrainingGuidelines() {
        $this->trainingGuidelinesList = $this->getAllTrainingGuidelines('ch');

        //var_dump($this->trainingGuidelinesList);die;
        return $this->trainingGuidelinesList;
    }

    public function getEquipmentNames($section) {
        $this->equipmentList = $this->getAllEquipmentNames($section);

        //var_dump($this->equipmentList);die;
        return $this->equipmentList;
    }

    public function getSupplyNames() {
        $this->suppliesList = $this->getAllSupplyNames('ch');

        //var_dump($this->suppliesList);die;
        return $this->suppliesList;
    }

    public function getIndicatorNames() {
        $this->indicatorList = $this->getAllMCHIndicators();

        //var_dump($this->indicatorList);die;
        return $this->indicatorList;
    }

    public function getTreatmentNames() {
        $this->treatmentList = $this->getAllMCHTreatments();

        //var_dump($this->treatmentList);die;
        return $this->treatmentList;
    }


    private function addQuestionsInfo() {
        // print_r($this->input->post());die;

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

        //var_dump($this->elements);die;



       // exit;

        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;

        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();

            //create an object of the model

            //$this -> theForm -> setIdMCHQuestionLog($this->elements[$i]['ortcAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));

            //check if that key exists, else set it to some default value


            (array_key_exists('questionLocResponse', $this->elements[$i])) ? $this->theForm->setLqResponse($this->elements[$i]['questionLocResponse']) : $this->theForm->setLqResponse($this->elements[$i]['questionResponse']);

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


                    //die($ex->getMessage());
                    return false;


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


                    //die($ex->getMessage());
                    return false;


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

     private function addMchCommunityStrategyInfo() {

        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
             //For every posted values
            if (strpos($key, 'mchCommunity') !== FALSE) {
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
                if ($this->attr == "mchCommunityStrategyQCode") {

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
        //print var_dump($this->elements);

        //exit;

        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;

        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\CommunityStrategies();

            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mchCommunityStrategy']) && $this->elements[$i]['mchCommunityStrategy'] != '') ? $this->theForm->setCsResponse($this->elements[$i]['mchCommunityStrategy']) : $this->theForm->setCsResponse(-1);
            $this->theForm->setStrategyCode('mchCommunityStrategyQCode');
            $this->theForm->setStrategyCode($this->elements[$i]['mchCommunityStrategyQCode']);
            $this->theForm->setCsCreated(new DateTime());
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
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
     //close addMchCommunityStrategyInfo()


     private function addMchStaffTrainingInfo() {

        //echo "<pre>";print_r($this->input->post());echo "</pre>";die;
        $this->elements=array();
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

                   $staff=$val;


                    // print 'count at:'.$count.'<br />';
                    //print 'final count at:'.$finalCount.'<br />';
                    //print 'DOM: '.$key.' Attr: '.$this->attr.' val='.$val.' id='.$this->id.' <br />';


                }

                if(is_array($val)){

                    //var_dump($val);die;
                    foreach($val as $guide=>$trained){

                        $data[$staff][$this->attr][$guide]=$trained;
                    }


                }
                else{
                    $data[$staff][$this->attr]=$val;

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
         $counter=0;
         foreach($data as $value){
        foreach ($value['mchTrainingBefore'] as $key=> $training){
            $counter++;
            $newData[$counter]['mchTrainingStaff']=$value['mchTrainingStaff'];
            $newData[$counter]['mchTrainingTotalinFacility']=$value['mchTrainingTotalinFacility'];
            $newData[$counter]['mchTrainingTotalAvailableOnDuty']=$value['mchTrainingTotalAvailableOnDuty'];
            $newData[$counter]['mchTrainingTotalStaffMembersStillWorking']=$value['mchTrainingTotalStaffMembersStillWorking'];
             $newData[$counter]['mchGuideline']=$key;
             $newData[$counter]['mchBefore']=$training;
            $newData[$counter]['mchAfter']=$value['mchTrainingAfter'][$key];


        }
       }

         $this->elements=$newData;



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

//$this->theForm->setTgCreated(new DateTime());

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
     //close addGuidelinesStaffInfo

    private function addAccessChallengesInfo() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
             //For every posted values
            if (strpos($key, 'ach') !== FALSE) {
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
                if ($this->attr == "achResponse") {

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
        //print var_dump($this->elements);

        //exit;

        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;

        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogChallenges();

            //create an object of the model

            $this->theForm->setAchCode($this->elements[$i]['achResponse']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));

            //check if that key exists, else set it to some default value
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

                    //die($ex->getMessage());
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


     //close addGuidelinesStaffInfo



    private function addCommodityQuantityAvailabilityInfo() {
        $supplier_code = $this->input->post('supplierName');
        //echo  $supplier_code;die;

        $count = $finalCount = 1;
       //print_r($this->input->post('supplierName'));die;

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

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\AvailableCommodities();

            //create an object of the model

            //  die(print 'Code: '.$this -> session -> userdata('facilityMFL'));


            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setCommCode($this->elements[$i]['cqCommCode']);

            //check if that key exists, else set it to some default value

            (isset($this->elements[$i]['cqExpiryDate'])) ? $this->theForm->setAcExpiryDate($this->elements[$i]['cqExpiryDate']) : $this->theForm->setAcExpiryDate('n/a');

            (isset($this->elements[$i]['cqNumberOfUnits'])) ? $this->theForm->setAcQuantity($this->elements[$i]['cqNumberOfUnits']) : $this->theForm->setAcQuantity(-1);

            (isset($supplier_code) || $supplier_code == '') ? $this->theForm->setSupplierCode($supplier_code) : $this->theForm->setSupplierCode("Other");

            (isset($this->elements[$i]['cqReason']) || $this->elements[$i]['cqReason'] == '') ? $this->theForm->setAcReasonUnavailable($this->elements[$i]['cqReason']) : $this->theForm->setAcReasonUnavailable("N/A");
            (isset($this->elements[$i]['cqAvailability'])) ? $this->theForm->setAcAvailability($this->elements[$i]['cqAvailability']) : $this->theForm->setAcAvailability("N/A");
            (isset($this->elements[$i]['cqLocation'])) ? $this->theForm->setAcLocation($this->elements[$i]['cqLocation']) : $this->theForm->setAcLocation("N/A");
            $this->theForm->setAcCreated(new DateTime());
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));


            //$this->theForm->setCreatedAt(new DateTime());


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

    //adding bundling function
    private function addBundling() {

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
            $this->theForm = new \models\Entities\AvailableCommodities();

            //create an object of the model

            //  die(print 'Code: '.$this -> session -> userdata('facilityMFL'));


            $this->theForm->setFacilityCode($this->session->userdata('facilityMFL'));
            $this->theForm->setCommodityID($this->elements[$i]['cqCommodityCode']);

            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['cqExpiryDate']) && $this->elements[$i]['cqExpiryDate'] != '') ? $this->theForm->setCommodityExpiryDate($this->elements[$i]['cqExpiryDate']) : $this->theForm->setCommodityExpiryDate('n/a');
            (isset($this->elements[$i]['cqNumberOfUnits'])) ? $this->theForm->setQuantityAvailable($this->elements[$i]['cqNumberOfUnits']) : $this->theForm->setQuantityAvailable(-1);
            //(isset($this->elements[$i]['cqSupplier']) || $this->elements[$i]['cqSupplier'] == '') ? $this->theForm->setSupplierID($this->elements[$i]['cqSupplier']) : $this->theForm->setSupplierID("Other");
            (isset($this->elements[$i]['cqReason']) || $this->elements[$i]['cqReason'] == '') ? $this->theForm->setReason4Unavailability($this->elements[$i]['cqReason']) : $this->theForm->setReason4Unavailability("N/A");
            (isset($this->elements[$i]['cqAvailability'])) ? $this->theForm->setAvailability($this->elements[$i]['cqAvailability']) : $this->theForm->setAvailability("N/A");
            (isset($this->elements[$i]['cqLocation'])) ? $this->theForm->setLocation($this->elements[$i]['cqLocation']) : $this->theForm->setLocation("N/A");
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));

            $this->theForm->setCreatedAt(new DateTime());


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

    private function addMCHIndicatorInfo() {
         $count = $finalCount = 1;
        $indicatorsymptom = $this->input->post('indicatormchsymptom');
        $count = 0;
        foreach ($indicatorsymptom as $key => $value) {
            $responselist[$count] = $value;
            $count++;
        }
        for ($i = 0; $i < count($responselist); $i++){
            if (is_array($responselist[$i])){
                $responselist[$i] = implode($responselist[$i], ',');
            }
        }

        $finallist = implode($responselist, ',');

        foreach ($this->input->post() as $key => $val) {
             //For every posted values
            if (strpos($key, 'indicator') !== FALSE) {
                 //select data for bemonc signal functions
                //we separate the attribute name from the number

                //print_r($indicatorsymptom);
                $this->frags = explode("_", $key);

                //print_r($this->frags);

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
                    $this->elements[$this->id]['symptomlist'] = $finallist;

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
            (isset($this->elements[$i]['indicatorhcwResponse'])) ? $this->theForm->setLiHcwResponse($this->elements[$i]['indicatorhcwResponse']) : $this->theForm->setLiHcwResponse("N/A");
            (isset($this->elements[$i]['indicatorhcwFindings'])) ? $this->theForm->setLiHcwFindings($this->elements[$i]['indicatorhcwFindings']) : $this->theForm->setLiHcwFindings("N/A");
            (isset($this->elements[$i]['indicatorassessorResponse'])) ? $this->theForm->setLiAssessorresponse($this->elements[$i]['indicatorassessorResponse']) : $this->theForm->setLiAssessorresponse("N/A");
            (isset($this->elements[$i]['indicatorassessorFindings'])) ? $this->theForm->setLiAssessorfindings($this->elements[$i]['indicatorassessorFindings']) : $this->theForm->setLiAssessorfindings("N/A");
            (isset($this->elements[$i]['symptomlist'])) ? $this->theForm->setLiTreatments($this->elements[$i]['symptomlist']) : $this->theForm->setLiTreatments("N/A");
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

        return true;
    }
     //close addMCHIndicatorInfo
        private function addmchConsultationQuestions() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
            //For every posted values
            if (strpos($key, 'mchC') !== FALSE) {
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
                if ($this->attr == "mchConsultationQCode") {

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

        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();

            //create an object of the model

            //$this -> theForm -> setIdMCHQuestionLog($this->elements[$i]['ortcAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));

            //check if that key exists, else set it to some default value

            (array_key_exists('questionLocResponse', $this->elements[$i])) ? $this->theForm->setLqResponse($this->elements[$i]['questionLocResponse']) : $this->theForm->setLqResponse($this->elements[$i]['mchConsultationResponse']);

            (array_key_exists('questionCount', $this->elements[$i])) ? $this->theForm->setLqResponseCount($this->elements[$i]['questionCount']) : $this->theForm->setLqResponseCount(-1);
            (array_key_exists('questionReason', $this->elements[$i])) ? $this->theForm->setLqReason($this->elements[$i]['questionReason']) : $this->theForm->setLqReason('n/a');
            (array_key_exists('questionSpecified', $this->elements[$i])) ? $this->theForm->setLqSpecifiedOrFollowUp($this->elements[$i]['questionSpecified']) : $this->theForm->setLqSpecifiedOrFollowUp('n/a');
            $this->theForm->setQuestionCode($this->elements[$i]['mchConsultationQCode']);
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
     //close addMchGuidelinesAvailabilityInfo

    private function addTotalMCHTreatment() {

        $treatment =$this->input->post('mchtreatment');
        $totalTreatment =$this->input->post('mchtotalTreatment');
        $realtreatments = $this->input->post('mchtreatmentnew');
        $numbers = $this->input->post('mchtreatmentnumbers');
        $valnum = array();
        $count = 0;
        foreach ($numbers as $key => $value) {
            $count++;
            $valnum[$key] = array_filter($value);
        }
        //print_r($cleanednumbers);die;
        //print_r($this->input->post());die;
      //echo "<pre>";print_r($treatment);echo"</pre>";die;
        $this->elements=array();
        $count=0;
        foreach ($totalTreatment as $key => $val) {
            $count++;
            $this->elements[$count]['totalTreatment'] = htmlentities($val);
            $this->elements[$count]['classification'] = htmlentities($key);
            $this->elements[$count]['treatment'] = implode(',', $treatment[$key]);
            $this->elements[$count]['treatmentnew'] = implode(',', $realtreatments[$key]);
            $this->elements[$count]['treatmentnumbers'] = implode(',', $valnum[$key]);


        }
        //die;
         //close foreach ($this -> input -> post() as $key => $val)
        //exit;
        //echo $count;
        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $count;
        //echo $this->noOfInsertsBatch;die;
        //labour and delivery Qn5 to 8 will have a single response each

        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {

            //echo 'Done '.$i;

            $this->theForm = new \models\Entities\LogTreatments();


            //create an object of the model

            $this->theForm->setLtCreated(new DateTime());

            /*timestamp option*/
            $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));



            /*if no value set, then set to -1*/

            //print_r($this->elements);die;

            $this->theForm->setLtClassification($this->elements[$i]['classification']);
            $this->theForm->setLtTotal($this->elements[$i]['totalTreatment']);
            (array_key_exists('treatment', $this->elements[$i]) && $this->elements[$i]['treatment']!='')? $this->theForm->setLtTreatments($this->elements[$i]['treatment']) : $this->theForm->setLtTreatments('n/a');;

            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            $this->theForm->setLtOtherTreatments($this->elements[$i]['treatmentnew']);
            $this->theForm->setLtOtherTreatmentsNumbers($this->elements[$i]['treatmentnumbers']);
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

                    //die(print . $i);
                    // $this->writeAssessmentTrackerLog();
                    return true;
                }
            }

            //end of batch condition

        }
         //end of innner loop
    }
     //close addTotalMCHTreatment
     private function addDiarrhoeaCasesByMonthInfo() {
        foreach ($this->input->post() as $key => $val) {
            //For every posted values
            if (strpos($key, 'dn') !== FALSE) {
                 //select data for number of deliveries
                $this->attr = $key;
                //print_r( $this->attr);die;
                //the attribute name

                //split into 2 years: 2012 & 2013 --for later :-)

                if (!empty($val)) {

                    //We then store the value of this attribute for this element.
                    // $this->elements[$this->id][$this->attr]=htmlentities($val);
                    $count = 1;
                    foreach ($val as $k => $month) {
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
            $this->theForm = new \models\Entities\LogMorbidity();

            //create an object of the model

            $this->theForm->setCreatedAt(new DateTime());

            /*timestamp option*/
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));

            /*if no value set, then set to -1*/

            //print_r($this->elements);die;
            $this->theForm->setMonth($this->elements[$i]['monthName']);
            $this->theForm->setLmNumber($this->elements[$i]['monthData']);
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

                    //die($ex->getMessage());
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

                    //die($ex->getMessage());
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

    private function addMCHTreatmentInfo() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
             //For every posted values
            if (strpos($key, 'mcht') !== FALSE) {
                 //select data for mch treatments
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
                if ($this->attr == "mchtTreatmentCode") {

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
        //print var_dump($this->elements);

        //exit;

        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;

        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogTreatment();

            //create an object of the model

            $this->theForm->setTreatmentCode($this->elements[$i]['mchtTreatmentCode']);
            $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));

            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['mchtSevereDehydration']) && $this->elements[$i]['mchtSevereDehydration'] != '') ? $this->theForm->setLtSevereDehydrationNumber($this->elements[$i]['mchtSevereDehydration']) : $this->theForm->setLtSevereDehydrationNumber(-1);
            (isset($this->elements[$i]['mchtSomeDehydration']) && $this->elements[$i]['mchtSomeDehydration'] != '') ? $this->theForm->setLtSomeDehydrationNumber($this->elements[$i]['mchtSomeDehydration']) : $this->theForm->setLtSomeDehydrationNumber(-1);
            (isset($this->elements[$i]['mchtNoDehydration']) && $this->elements[$i]['mchtNoDehydration'] != '') ? $this->theForm->setLtNoDehydrationNumber($this->elements[$i]['mchtNoDehydration']) : $this->theForm->setLtNoDehydrationNumber(-1);
            (isset($this->elements[$i]['mchtDysentry']) && $this->elements[$i]['mchtDysentry'] != '') ? $this->theForm->setLtDysentryNumber($this->elements[$i]['mchtDysentry']) : $this->theForm->setLtDysentryNumber(-1);
            (isset($this->elements[$i]['mchtNoClassification']) && $this->elements[$i]['mchtNoClassification'] != '') ? $this->theForm->setLtNoClassificationNumber($this->elements[$i]['mchtNoClassification']) : $this->theForm->setLtNoClassificationNumber(-1);

            //if other treatment has been entered
            (isset($this->elements[$i]['mchtTreatmentOther']) && $this->elements[$i]['mchtTreatmentOther'] != '') ? $this->theForm->setLtOtherTreatment($this->elements[$i]['mchtTreatmentOther']) : $this->theForm->setLtOtherTreatment('n/a');

            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            $this->theForm->setLtCreated(new DateTime());

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

                    //die($ex->getMessage());
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
     //close addMCHTreatmentInfo
    private function addMchOrtConerAssessmentInfo() {
        $count = $finalCount = 1;
        foreach ($this->input->post() as $key => $val) {
             //For every posted values
            if (strpos($key, 'ortc') !== FALSE) {
                 //select data for bemonc signal functions
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

                //print $key.' ='.$val.' <br />';
                //print 'ids: '.$this->id.'<br />';

                //mark the end of 1 row...for record count
                if ($this->attr == "ortcAspectCode") {

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
        //print var_dump($this->elements);

        //exit;

        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;

        for ($i = 1; $i <= $this->noOfInsertsBatch; ++$i) {

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\LogQuestions();

            //create an object of the model

            //$this -> theForm -> setIdMCHQuestionLog($this->elements[$i]['ortcAspectCode']);
            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));

            //check if that key exists, else set it to some default value

                (isset($this->elements[$i]['ortcAspectLocResponse'])) ? $this->theForm->setLqResponse($this->elements[$i]['ortcAspectLocResponse']) : $this->theForm->setLqResponse('n/a');
                (isset($this->elements[$i]['ortcAspect'])) ? $this->theForm->setLqResponse($this->elements[$i]['ortcAspect']) : $this->theForm->setLqResponse('n/a');
            $this->theForm->setQuestionCode($this->elements[$i]['ortcAspectCode']);
            (isset($this->elements[$i]['ortcGuidesCount'])) ? $this->theForm->setLqResponseCount($this->elements[$i]['ortcGuidesCount']) : $this->theForm->setLqResponseCount(-1);
            $this->theForm->setLqCreated(new DateTime());

            /*timestamp option */
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

                    //die($ex->getMessage());
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

                    //die($ex->getMessage());
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
                 //end of catch


            }

            //end of batch condition

        }

         //end of innner loop



    }
     //close addMchOrtConerAssessmentInfo
     

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

        //exit;

        //get the highest value of the array that will control the number of inserts to be done
        $this->noOfInsertsBatch = $finalCount;

        //  print 'Found :'.$this->noOfInsertsBatch;die;
        //print_r($this->elements);
        for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {

            //go ahead and persist data posted
            $this->theForm = new \models\Entities\AvailableEquipments();

            //create an object of the model

            //  die(print 'Code: '.$this -> session -> userdata('facilityMFL'));

            $this->theForm->setFacMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setEqCode($this->elements[$i]['eqCode']);

            //check if that key exists, else set it to some default value

            (array_key_exists('eqAvailability', $this->elements[$i])) ? $this->theForm->setAeAvailability($this->elements[$i]['eqAvailability']) : $this->theForm->setAeAvailability("N/A");
            (array_key_exists('eqLocation', $this->elements[$i])) ? $this->theForm->setAeLocation($this->elements[$i]['eqLocation']) : $this->theForm->setAeLocation("N/A");
            (array_key_exists('eqQtyFullyFunctional', $this->elements[$i])) ? $this->theForm->setAeFullyFunctional($this->elements[$i]['eqQtyFullyFunctional']) : $this->theForm->setAeFullyFunctional(-1);
            (array_key_exists('eqQtyPartiallyFunctional', $this->elements[$i])) ? $this->theForm->setAePartiallyFunctional($this->elements[$i]['eqQtyPartiallyFunctional']) : $this->theForm->setAePartiallyFunctional(-1);
            (array_key_exists('eqQtyNonFunctional', $this->elements[$i])) ? $this->theForm->setAeNonFunctional($this->elements[$i]['eqQtyNonFunctional']) : $this->theForm->setAeNonFunctional(-1);

            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));

            $this->theForm->setAeCreated(new DateTime());

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
     //close addEquipmentQuantityAvailabilityInfo

    private function addSuppliesQuantityAvailabilityInfo() {
        $supplier_code = $this->input->post('supplierName');
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
            //(isset($this->elements[$i]['sqNumberOfUnits']))?$this -> theForm -> setQuantityAvailable($this->elements[$i]['sqNumberOfUnits']):$this -> theForm -> setQuantityAvailable(-1);

            (isset($supplier_code) || $supplier_code == '') ? $this->theForm->setSupplierCode($supplier_code) : $this->theForm->setSupplierCode("Other");


            //(isset($this->elements[$i]['sqReason']) || $this->elements[$i]['sqReason']=='')?$this -> theForm -> setReason4Unavailability($this->elements[$i]['sqReason']):$this -> theForm -> setReason4Unavailability("N/A");
            (isset($this->elements[$i]['sqAvailability'])) ? $this->theForm->setAsAvailability($this->elements[$i]['sqAvailability']) : $this->theForm->setAsAvailability("N/A");
            (isset($this->elements[$i]['sqLocation'])) ? $this->theForm->setAsLocation($this->elements[$i]['sqLocation']) : $this->theForm->setAsLocation("N/A");
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));
            $this->theForm->setAsCreated(new DateTime());

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

    private function addResourceAvailabilityInfo() {
        $supplier_code = $this->input->post('supplierName');
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
                if ($this->attr == "hwEqCode") {

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
            $this->theForm->setEquipmentCode($this->elements[$i]['hwEqCode']);

            //check if that key exists, else set it to some default value
            (isset($this->elements[$i]['hwNumberOfUnits'])) ? $this->theForm->setArQuantity($this->elements[$i]['hwNumberOfUnits']) : $this->theForm->setArQuantity(-1);

             (isset($supplier_code) || $supplier_code == '') ? $this->theForm->setSupplierCode($supplier_code) : $this->theForm->setSupplierCode("Other");


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
     //close addResourceAvailabilityInfo

    private function addmalariatreatmentinfo()
    {
        //$count = $finalCount =0;
        $count = count($_POST['maltreatments']);
        $values = $_POST['maltreatments'];
        for ($i=0; $i < $count; $i++) {
           //echo $_POST['mchtMalariaTreatment'];die;
             $this->theForm = new \models\Entities\LogTreatmentmalnpne();
             //echo "Treatment " .$i . " " . $values[$i];
             $this->theForm->setTreatmentCode($values[$i]);
             $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));
             $this->theForm->setLtClassification($_POST['mchtMalariaTreatment']);
             $this->theForm->setLtCreated(new DateTime);
             $this->theForm->setSsId((int)$this->session->userdata('survey_status'));

             $this->em->persist($this->theForm);
        }
         return true;
    }

     private function addpneumoniatreatmentinfo()
    {
        //$count = $finalCount =0;
        $count = count($_POST['pnetreatments']);
        $values = $_POST['pnetreatments'];
        for ($i=0; $i < $count; $i++) {
           //echo $_POST['mchtMalariaTreatment'];die;
             $this->theForm = new \models\Entities\LogTreatmentmalnpne();
             //echo "Treatment " .$i . " " . $values[$i];
             $this->theForm->setTreatmentCode($values[$i]);
             $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));
             $this->theForm->setLtClassification($_POST['mchtPneumoniaTreatment']);
             $this->theForm->setLtCreated(new DateTime);
             $this->theForm->setSsId((int)$this->session->userdata('survey_status'));

             $this->em->persist($this->theForm);
        }
         return true;
    }

    private function adddiatreatmentinfo()
    {
        $couny = count($_POST['diatreatments']);
        $values = $_POST['diatreatments'];
        for ($i=0; $i < $count; $i++) {
            $this->theForm->setTreatmentCode($values[$i]);
            $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));
            $this->theForm->setLtClassification($_POST['mchtdiaTreatment']);
            $this->theForm->setLtCreated(new DateTime);
            $this->theForm->setSsId((int)$this->session->userdata('survey_status'));

            $this->em->persist($this->theForm);
        }

        return true;
    }

  private function addMchAssessorInfo() {
       $count = $finalCount = 1;
       foreach ($this->input->post() as $key => $val) {
            //For every posted values
           if (strpos($key, 'assesor') !== FALSE) {
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
               if ($this->attr == "assesorphoneNumber") {

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

       //get the highest value of the array that will control the number of inserts to be done
       $this->noOfInsertsBatch = $finalCount;

       for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {

           //go ahead and persist data posted
           $this->theForm = new \models\Entities\AssessorInformation();

           //create an object of the model

           //$this->theForm->setStrategyCode(1);
            //$this -> elements[$i]['mchCommunityStrategyQCode']);
           $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));

           //check if that key exists, else set it to some default value

           (isset($this->elements[$i]['assesoremail']) && $this->elements[$i]['assesoremail'] != '') ? $this->theForm->setAssessorEmailaddress($this->elements[$i]['assesoremail']) : $this->theForm->setAssessorEmailaddress(-1);
           (isset($this->elements[$i]['assesorname']) && $this->elements[$i]['assesorname'] != '') ? $this->theForm->setAssessorName($this->elements[$i]['assesorname']) : $this->theForm->setAssessorName(-1);
           (isset($this->elements[$i]['assesordesignation']) && $this->elements[$i]['assesordesignation'] != '') ? $this->theForm->setAssessorDesignation($this->elements[$i]['assesordesignation']) : $this->theForm->setAssessorDesignation(-1);
           (isset($this->elements[$i]['assesorphoneNumber']) && $this->elements[$i]['assesorphoneNumber'] != '') ? $this->theForm->setAssessorPhonenumber($this->elements[$i]['assesorphoneNumber']) : $this->theForm->setAssessorPhonenumber(-1);



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


                   return false;

                   /*display user friendly message*/
               }
                //end of catch


           }

           //end of batch condition

       }
        //end of innner loop

   }
    //close addMchAssessorInfo()

    private function addMchHRInfo() {
       $count = $finalCount = 1;
       foreach ($this->input->post() as $key => $val) {
            //For every posted values
           if (strpos($key, 'facility') !== FALSE) {
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
               if ($this->attr == "facilityopdemail") {

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

       //get the highest value of the array that will control the number of inserts to be done
       $this->noOfInsertsBatch = $finalCount;

       for ($i = 1; $i <= $this->noOfInsertsBatch + 1; ++$i) {

           //go ahead and persist data posted
           $this->theForm = new \models\Entities\HrInformation();

           //create an object of the model

           //$this->theForm->setStrategyCode(1);
            //$this -> elements[$i]['mchCommunityStrategyQCode']);
           $this->theForm->setFacilityMfl($this->session->userdata('facilityMFL'));

           //check if that key exists, else set it to some default value
           (isset($this->elements[$i]['facilityInchargename']) && $this->elements[$i]['facilityInchargename'] != '') ? $this->theForm->setFacilityInchargeName($this->elements[$i]['facilityInchargename']) : $this->theForm->setFacilityInchargeName('N/A');
           (isset($this->elements[$i]['facilityInchargemobile']) && $this->elements[$i]['facilityInchargemobile'] != '') ? $this->theForm->setFacilityInchargeMobile($this->elements[$i]['facilityInchargemobile']) : $this->theForm->setFacilityInchargeMobile(-1);
           (isset($this->elements[$i]['facilityInchargeemail']) && $this->elements[$i]['facilityInchargeemail'] != '') ? $this->theForm->setFacilityInchargeEmailaddress($this->elements[$i]['facilityInchargeemail']) : $this->theForm->setFacilityInchargeEmailaddress('N/A');
           (isset($this->elements[$i]['facilityMchname']) && $this->elements[$i]['facilityMchname'] != '') ? $this->theForm->setMchInchargeName($this->elements[$i]['facilityMchname']) : $this->theForm->setMchInchargeName('N/A');
           (isset($this->elements[$i]['facilityMchmobile']) && $this->elements[$i]['facilityMchmobile'] != '') ? $this->theForm->setMchInchargeMobile($this->elements[$i]['facilityMchmobile']) : $this->theForm->setMchInchargeMobile('-1');
           (isset($this->elements[$i]['facilityMchemail']) && $this->elements[$i]['facilityMchemail'] != '') ? $this->theForm->setMchInchargeEmailaddress($this->elements[$i]['facilityMchemail']) : $this->theForm->setMchInchargeEmailaddress('N/A');

           (isset($this->elements[$i]['facilityMaternityname']) && $this->elements[$i]['facilityMaternityname'] != '') ? $this->theForm->setMaternityInchargeName($this->elements[$i]['facilityMaternityname']) : $this->theForm->setMchInchargeName('N/A');
           (isset($this->elements[$i]['facilityMaternitymobile']) && $this->elements[$i]['facilityMaternitymobile'] != '') ? $this->theForm->setMaternityInchargeMobile($this->elements[$i]['facilityMaternitymobile']) : $this->theForm->setMchInchargeMobile('-1');
           (isset($this->elements[$i]['facilityMaternityemail']) && $this->elements[$i]['facilityMaternityemail'] != '') ? $this->theForm->setMaternityInchargeEmailaddress($this->elements[$i]['facilityMaternityemail']) : $this->theForm->setMchInchargeEmailaddress('N/A');

           (isset($this->elements[$i]['facilityopdname']) && $this->elements[$i]['facilityopdname'] != '') ? $this->theForm->setOpdInchargeName($this->elements[$i]['facilityopdname']) : $this->theForm->setOpdInchargeName('N/A');
           (isset($this->elements[$i]['facilityopdmobile']) && $this->elements[$i]['facilityopdmobile'] != '') ? $this->theForm->setOpdInchargeMobile($this->elements[$i]['facilityopdmobile']) : $this->theForm->setOpdInchargeMobile('-1');
           (isset($this->elements[$i]['facilityopdemail']) && $this->elements[$i]['facilityopdemail'] != '') ? $this->theForm->setOpdInchargeEmailaddress($this->elements[$i]['facilityopdemail']) : $this->theForm->setOpdInchargeEmailaddress('N/A');
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
    //close addMchHRInfo()

    function store_data() {

        /*check assessment tracker log*/
        if ($this->input->post()) {

            $step = $this->input->post('step_name', TRUE);
            switch ($step) {
                case 'section-1':

                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));


                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {

                        if ($this->addMchAssessorInfo() == true && $this->addMchHRInfo()==true && $this->addMchStaffTrainingInfo()==true && $this->addQuestionsInfo()== true && $this->addmchConsultationQuestions() == true ){
                             //Defined in MY_Model
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
                        if($this->addTotalMCHTreatment()== true  && $this->addQuestionsInfo() == true && $this->addMCHIndicatorInfo() == true){
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


                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {

                        if ($this->addMCHIndicatorInfo()== true) {
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
;

                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ( $this->addCommodityQuantityAvailabilityInfo() == true ) {
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


                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addAccessChallengesInfo() == true && $this->addQuestionsInfo() == true ) {
                             //defined in this model ....
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {
                        return $this->response = 'true';
                    }
                    break;

                    case 'section-6':

                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));


                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if($this->addEquipmentQuantityAvailabilityInfo()== true){
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


                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addSuppliesQuantityAvailabilityInfo() == true) {
                             //defined in this model
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {

                        return $this->response = 'true';
                    }
                    break;

                    case 'section-8':

                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));


                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addResourceAvailabilityInfo()== true) {
                             //defined in this model
                            $this->writeAssessmentTrackerLog();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {

                        return $this->response = 'true';
                    }
                    break;

                case 'section-9':

                    //check if entry exists
                    $this->section = $this->sectionEntryExists($this->session->userdata('facilityMFL'), $this->input->post('step_name', TRUE), $this->session->userdata('survey'));

                    //insert log entry if new, else update the existing one
                    if ($this->sectionExists == false) {
                        if ($this->addMchCommunityStrategyInfo() == true ) {

                             //defined in this model
                            $this->writeAssessmentTrackerLog();

                            //update facility survey status
                            //$this->markSurveyStatusAsComplete();
                            return $this->response = 'true';
                        } else {
                            return $this->response = 'false';
                        }
                    } else {

                        //die('Entry exsits');
                        //update facility survey status
                        //$this->markSurveyStatusAsComplete();
                        return $this->response = 'true';
                    }
                    break;






            }
             //close switch

            //return $this -> response = 'true';

        }
    }

}
 //end of class m_mch_survey