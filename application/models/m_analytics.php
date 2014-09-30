<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @description Model contains query definitions to extract data for analytical purposes
 * @param entities
 * @author John Adamsy
 * @modified June 14th 2013
 *
 */

//for the query builder
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class M_Analytics extends MY_Model
{
    
    /*user variables*/
    var $dataSet, $final_data_set, $query, $rsm, $districtName, $countyFacilities;
    
    /*constructor*/
    function __construct() {
        parent::__construct();
        
        //var initialization
        $this->dataSet = $this->query = null;
    }
    
    public function getCountyReportingSummary($county, $survey, $survey_category) {
        
        /* using CI database active record*/
        try {
            $query = "SELECT 
                        f.fac_mfl, f.fac_name ,f.fac_ownership ,f.fac_type ,f.fac_level ,
                        f.fac_district ,f.fac_county 
                        FROM
                        assessment_tracker ast
                            JOIN
                        facilities f ON ast.facilityCode = f.fac_mfl AND f.fac_county = '" . $county . "'
                            JOIN
                        survey_status ss ON ss.fac_id = f.fac_mfl
                            JOIN
                        survey_types st ON st.st_id = ss.st_id AND st.st_name = '" . $survey . "'
                           JOIN
                        survey_categories sc ON sc.sc_id = ss.sc_id AND sc.sc_name = '" . $survey_category . "'
                        WHERE ast.ast_section = 'section-6'
                        GROUP BY f.fac_mfl
                    ORDER BY f.fac_county , f.fac_district";
            
            $this->dataSet = $this->db->query($query, array($survey));
            $this->dataSet = $this->dataSet->result_array();
            if ($this->dataSet) {
                return $this->dataSet;
            } else {
                return $this->dataSet = false;
            }
            
            //var_dump($this->dataSet);die;
            
            
        }
        catch(exception $ex) {
        }
    }
    
    public function getReportingList($survey,$survey_category){
    	$query = "CALL get_reporting_list('".$survey."','".$survey_category."');";
    	$this->dataSet = $this->db->query($query, array($survey));
            $this->dataSet = $this->dataSet->result_array();
            if ($this->dataSet) {
            	//echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                return $this->dataSet;
            } 
     }
    public function get_facility_reporting_summary($survey) {
        
        /*using CI Database Active Record*/
        try {
            $query = "SELECT fac_mfl,fac_name,fac_district,fac_county,fac_incharge_contact_person,fac_incharge_email,fac_updated

                     FROM facilities   ORDER BY fac_name ASC";
            $this->dataSet = $this->db->query($query, array($survey));
            $this->dataSet = $this->dataSet->result_array();
            
            if ($this->dataSet) {
                return $this->dataSet;
            } else {
                return $this->dataSet = false;
            }
         }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
    }
    
    //community strategy
    public function getCommunityStrategy($criteria, $value, $survey, $survey_category, $for) {
        
        /*using CI Database Active Record*/
        
        //$data=array();
        $data = '';
        $query = "CALL get_community_strategy('".$criteria."','".$value."','".$survey."','".$survey_category."','".$for."');";
        
        try {
            $this->dataSet = $this->db->query($query, array($value));
            $this->dataSet = $this->dataSet->result_array();
            if ($this->dataSet !== NULL) {
            	foreach ($this->dataSet as $value) {
                	//echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                	if(array_key_exists('strategy', $value)){
                		$data[$value['question_name']][$value['strategy']] = (int)$value['strategy_number'];
                	}
                 }
			}
             }
        catch(Exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
         }
		 //die(var_dump($data));die;
		return $data;
    }
    
    /*
     * Guidelines Availability
    */
    
    public function getGuidelinesAvailability($criteria, $value, $survey, $survey_category) {
        
        /*using CI Database Active Record*/
        $data = array();
        $data_prefix_y = '';
        $data_prefix_n = '';
        $data_y = $data_n = $data_categories = array();
        
        switch ($criteria) {
            case 'national':
                $criteria_condition = ' ';
                break;

            case 'county':
                $criteria_condition = 'WHERE fac_county=?';
                break;

            case 'district':
                $criteria_condition = 'WHERE fac_district=?';
                break;

            case 'facility':
                $criteria_condition = 'WHERE fac_mfl=?';
                break;

            case 'none':
                $criteria_condition = '';
                break;
        }
        
        $query = "SELECT
    COUNT(lq.fac_mfl) AS total_facilities,
    lq.question_code AS guideline,
    lq.lq_response AS availability
FROM
    log_questions lq
WHERE
    lq.question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = 'gp')
        AND lq.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
        JOIN
    survey_status ss ON ss.fac_id = f.fac_mfl
        JOIN
    survey_types st ON (st.st_id = ss.st_id
        AND st.st_name = '" . $survey . "')
" . $criteria_condition . ")
GROUP BY lq.lq_response , lq.question_code
ORDER BY lq.lq_response ASC";
        try {
            $this->dataSet = $this->db->query($query, array($value));
            $this->dataSet = $this->dataSet->result_array();
            if ($this->dataSet !== NULL) {
                
                //prep data for the pie chart format
                $size = count($this->dataSet);
                $i = 0;
                
                //get a set of the 4 guidelines
                $data['categories'] = array('2012 IMCI', 'ORT Corner', 'ICCM', 'Paediatric Protocol');
                
                //$data['categories'] = json_encode($data_categories);
                
                foreach ($this->dataSet as $value) {
                    switch ($this->getTrainingGuidelineName($value['guideline'])) {
                        case 'Does the facility have updated 2012 IMCI guidelines?':
                            $guideline = '2012 IMCI';
                            break;

                        case 'Does the facility have updated ORT Corner guidelines?':
                            $guideline = 'ORT Corner';
                            break;

                        case 'Does the facility have an updated Paediatric Protocol?':
                            $guideline = 'Paediatric Protocol';
                            break;

                        case 'Does the facility have updated ICCM guidelines?':
                            $guideline = 'ICCM';
                            break;
                    }
                    
                    if ($value['availability'] == 'Yes') {
                        $data_y[] = array($guideline => (int)$value['total_facilities']);
                    } else {
                        $data_n[] = array($guideline => (int)$value['total_facilities']);
                    }
                    
                    //$data['categories'][]=$guideline;
                    
                    
                }
                
                $data['yes_values'] = $data_y;
                $data['no_values'] = $data_n;
                
                $this->dataSet = $data;
                
                //var_dump($this->dataSet);die;
                
                
            } else {
                return $this->dataSet = null;
            }
            
            //die(var_dump($this->dataSet));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        return $this->dataSet;
    }
    
    /*
     * Trained Staff
    */
    public function getTrainedStaff($criteria, $value, $survey, $survey_category, $for) {
        $value = urldecode($value);
        
        /*using CI Database Active Record*/
        $data = array();
        $data_prefix_y = '';
        
        //"name:'Trained (Last 2 years)',data:";
        $data_prefix_n = '';
        
        //"name:'Trained & Working in CH',data:";
        $data_t = $data_w = $data_categories = array();
        
        $query = "CALL get_staff_trained('" . $criteria . "', '" . $value . "', '" . $survey . "','" . $survey_category . "','" . $for . "');";
        
        try {
            $queryData = $this->db->query($query, array($value));
            $this->dataSet = $queryData->result_array();
            $queryData->next_result();
            
            // Dump the extra resultset.
            $queryData->free_result();
            $category = array();
            
            //echo '<pre>';print_r($this->dataSet);echo '</pre>';
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                foreach ($this->dataSet as $value) {
                    
                    $data[$value['guide_name']][$value['cadre']]['total'] = $value['total'];
                    $data[$value['guide_name']][$value['cadre']]['before'] = $value['trained'];
                    $data[$value['guide_name']][$value['cadre']]['after'] = $value['trained_after'];
                }
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        return $data;
    }
    
    public function getStaffRetention($criteria, $value, $survey, $survey_category, $for) {
        $value = urldecode($value);
        
        /*using CI Database Active Record*/
        $data = array();
        $data_prefix_y = '';
        
        //"name:'Trained (Last 2 years)',data:";
        $data_prefix_n = '';
        
        //"name:'Trained & Working in CH',data:";
        $data_t = $data_w = $data_categories = array();
        
        $query = "CALL get_staff_retention('" . $criteria . "', '" . $value . "', '" . $survey . "','" . $survey_category . "','" . $for . "');";
        
        try {
            $queryData = $this->db->query($query, array($value));
            $this->dataSet = $queryData->result_array();
            $queryData->next_result();
            
            // Dump the extra resultset.
            $queryData->free_result();
            $category = array();
            
            //echo '<pre>';print_r($this->dataSet);echo '</pre>';
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                foreach ($this->dataSet as $value) {
                    
                    $data[$value['guide_name']][$value['cadre']]['trained'] = $value['trained'];
                    $data[$value['guide_name']][$value['cadre']]['trained_after'] = $value['trained_after'];
                }
            }
        }
        
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        return $data;
    }

    public function getStorageStatistics($criteria, $value, $survey, $survey_category) {
            
        $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_storage_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','mnhw');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $value) {
                        //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                        if (array_key_exists('lq_specified_or_follow_up', $value)) {
                            $data[$value['lq_specified_or_follow_up']] = (int)$value['total_response'];
                        }
                    }
                    
                    /**
                     * Fix Data
                     */
                    
                    
                } else {
                    return null;
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
    
    /*
     *Staff Availability
    */
    public function getStaffAvailability($criteria, $value, $survey, $survey_category, $for) {
        $value = urldecode($value);
        $query = "CALL get_staff_training('" . $criteria . "', '" . $value . "', '" . $survey . "','" . $survey_category . "','" . $for . "');";
        try {
            $queryData = $this->db->query($query, array($value));
            $this->dataSet = $queryData->result_array();
            $queryData->next_result();
            
            // Dump the extra resultset.
            $queryData->free_result();
            $result = $category = array();
            
            //echo($this->db->last_query());die;
            
            if ($this->dataSet !== NULL) {
                foreach ($this->dataSet as $value) {
                    $data[$value['guide_name']][$value['cadre']]['total_facility'] = $value['total_in_facility'];
                    $data[$value['guide_name']][$value['cadre']]['total_duty'] = $value['total_on_duty'];
                }
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        return $data;
    }
    
    // get treatment statistics
    public function getTreatmentStatistics($criteria, $value, $survey, $survey_category, $statistic) {
        $value = urldecode($value);
        $query = "CALL get_treatment_statistics('" . $criteria . "', '" . $value . "', '" . $survey . "','" . $survey_category . "','" . $statistic . "');";
        try {
            $queryData = $this->db->query($query, array($value));
            $this->dataSet = $queryData->result_array();
            $queryData->next_result();
            
            // Dump the extra resultset.
            $queryData->free_result();
            $result = $category = array();
            
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                
                foreach ($this->dataSet as $value) {
                    switch ($statistic) {
                        case 'cases':
                            $data[$value['treatment_for']][$value['treatment']] = (int)$value['total'];
                            
                            break;

                        case 'treatment':
                            $treatment_array = explode(',', $value['lt_treatments']);
                            
                            //var_dump($treatment_array);
                            foreach ($treatment_array as $treatment) {
                                $data[$value['treatment_for']][$value['treatment']][$this->getCommodityNameById($treatment) ]+= (int)$value['total_treatment'];
                            }
                            break;

                        case 'other_treatment':
                            $treatment_array = explode(',', $value['lt_other_treatments']);
                            
                            //var_dump($treatment_array);
                            foreach ($treatment_array as $treatment) {
                                $data[$value['treatment_for']][$value['treatment']][$this->getChildHealthTreatmentName($treatment) ]+= (int)$value['total_treatment'];
                            }
                            break;
                    }
                }
                
                // echo "<pre>";
                //             print_r($data);
                //             echo "</pre>";
                //             die;
                
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        //print_r($data);die;
        
        return $data;
    }
    
    // get treatment symptoms
    public function getTreatmentSymptoms($criteria, $value, $survey, $survey_category) {
        $value = urldecode($value);
        $query = "CALL get_treatment_symptoms('" . $criteria . "', '" . $value . "', '" . $survey . "', '" . $survey_category . "');";
        try {
            $queryData = $this->db->query($query, array($value));
            $this->dataSet = $queryData->result_array();
            $queryData->next_result();
            
            // Dump the extra resultset.
            $queryData->free_result();
            $result = $category = array();
            
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                
                // echo "<pre>";
                // print_r($this->dataSet);
                // echo "</pre>";
                // die;
                
                
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        return $this->dataSet;
    }
    
    /*
     * Availability, Location and Functionality of Equipment at ORT Corner
    */
    
    public function getORTCornerEquipmement($criteria, $value, $survey, $survey_category) {
        
        /*using CI Database Active Record*/
        $data = $data_set = $data_series = $analytic_var = $data_categories = array();
        
        //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
        
        
        
        /**
         * something of this kind:
         * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
         */
        
        /*--------------------begin ort equipment availability by frequency----------------------------------------------*/
        $query = "CALL get_resources('" . $criteria . "' , '" . $analytic_value . "', '" . $survey_type . "', '" . $survey_category . "', '" . $equipmentfor . "','availability' ); ";
        
        try {
            
            $this->dataSet = $this->db->query($query, array($value));
            
            $this->dataSet = $this->dataSet->result_array();
            
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                
                //prep data for the pie chart format
                $size = count($this->dataSet);
                
                foreach ($this->dataSet as $value_) {
                    
                    //1. collect the categories
                    $data_categories[] = $this->getCHEquipmentName($value_['equipment']);
                    
                    //incase of duplicates--do an array_unique outside the foreach()
                    
                    //2. collect the analytic variables
                    if ($value_['frequency'] == 'Some Available') {
                        
                        //a hardcore fix...for Nairobi County data only--> (there was a typo in the naming 'Sometimes Available', so Nairobi data has it as 'Some Available')
                        
                        $frequency = 'Sometimes Available';
                    } else {
                        $frequency = $value_['frequency'];
                    }
                    $analytic_var[] = $frequency;
                    
                    //includes duplicates--so we'll array_unique outside the foreach()
                    
                    //collect the data_sets for the 3 analytic variables under availability
                    if ($frequency == 'Available') {
                        $data_set['Available'][] = intval($value_['total_response']);
                    } else if ($frequency == 'Sometimes Available') {
                        $data_set['Sometimes Available'][] = intval($value_['total_response']);
                    } else if ($frequency == 'Never Available') {
                        $data_set['Never Available'][] = intval($value_['total_response']);
                    }
                }
                
                //var_dump($data_set);die;
                
                //make cat array unique if we got duplicates then json_encode and set to $data array
                $data['categories'] = (array_values(array_unique($data_categories)));
                
                //expected 28
                
                //get a unique set of analytic variables
                $analytic_var = array_unique($analytic_var);
                
                //expected to be 3 in this particular context
                $data['analytic_variables'] = $analytic_var;
                
                //get the data sets
                $data['responses'] = $data_set;
                
                //sets of the 3 analytic variables: Available | Sometimes Available | Never Available
                
                $this->final_data_set['frequency'] = $data;
                
                //note, I've introduced $final_data_set to be used in place of $data since $data is reset and reused
                
                //unset the arrays for reuse in the next query
                $data = $data_set = $data_series = $analytic_var = $data_categories = array();
                
                //return $this -> final_data_set;
                
                
            } else {
                return null;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        /*--------------------end ort equipment availability by frequency----------------------------------------------*/
        
        /*--------------------begin ort equipment location of availability----------------------------------------------*/
        $query = "CALL get_resources('" . $criteria . "' , '" . $analytic_value . "', '" . $survey_type . "', '" . $survey_category . "', '" . $equipmentfor . "','availability' ); ";
        
        /*$query = "SELECT
        count(ea.ae_location) AS total_response,
        ea.eq_code as equipment,
        ea.ae_location AS location
        FROM
        available_equipments ea
        WHERE
        ea.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')" . $criteria_condition . ")
        AND ea.eq_code IN (SELECT
            eq_code
        FROM
            equipments
        WHERE
            eq_for = 'ort')
        AND ea.ae_location NOT LIKE '%Not Applicable%'
        GROUP BY ea.eq_code , ea.ae_location
        
        ORDER BY ea.eq_code ASC";
        
        
        try {
            
            //echo $query;die;
            //die(print $status.$value);
            $this->dataSet = $this->db->query($query, array($value));
            
            //var_dump($this->dataSet);die;
            $this->dataSet = $this->dataSet->result_array();
            
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                
                //prep data for the pie chart format
                $size = count($this->dataSet);
                $count_instances = array('MCH' => 0, 'OPD' => 0, 'U5 Clinic' => 0, 'Ward' => 0, 'Other' => 0);
                
                //to hold the location instances
                foreach ($this->dataSet as $value_) {
                    
                    //1. collect the categories
                    $data_categories[] = $this->getCHEquipmentName($value_['equipment']);
                    
                    //incase of duplicates--do an array_unique outside the foreach()
                    
                    //2. collect the analytic variables
                    //$analytic_var[] = $value['location'];-->hard fix outside the loop as values are coma separated...good fix..have v-look up in the db
                    
                    //collect the data_sets from the coma separated responses
                    if (strpos($value_['location'], 'OPD') !== FALSE) {
                        $count_instances['OPD']+= intval($value_['total_response']);
                        $data_set[$this->getCHEquipmentName($value_['equipment']) ]['OPD'] = $count_instances['OPD'];
                    }
                    if (strpos($value_['location'], 'MCH') !== FALSE) {
                        $count_instances['MCH']+= intval($value_['total_response']);
                        $data_set[$this->getCHEquipmentName($value_['equipment']) ]['MCH'] = $count_instances['MCH'];
                    }
                    if (strpos($value_['location'], 'U5 Clinic') !== FALSE) {
                        $count_instances['U5 Clinic']+= intval($value_['total_response']);
                        $data_set[$this->getCHEquipmentName($value_['equipment']) ]['U5 Clinic'] = $count_instances['U5 Clinic'];
                    }
                    if (strpos($value_['location'], 'Ward') !== FALSE) {
                        $count_instances['Ward']+= intval($value_['total_response']);
                        $data_set[$this->getCHEquipmentName($value_['equipment']) ]['Ward'] = $count_instances['Ward'];
                    }
                    if (strpos($value_['location'], 'Other') !== FALSE) {
                        $count_instances['Other']+= intval($value_['total_response']);
                        $data_set[$this->getCHEquipmentName($value_['equipment']) ]['Other'] = $count_instances['Other'];
                    }
                }
                
                //var_dump($count_instances);die;
                //var_dump($data_set);die;
                
                //make array unique if we got duplicates and set to $data array
                $data['categories'] = array_values(array_unique($data_categories));
                
                //expected 28
                
                //get a unique set of analytic variables
                $analytic_var = array('OPD', 'MCH', 'U5 Clinic', 'Ward', 'Other');
                
                //expected to be 5 in this particular context, again we know they r just these 5 :)
                $data['analytic_variables'] = $analytic_var;
                
                //get the data sets
                $data['responses'] = $data_set;
                
                //sets of the 5 analytic variables: 'OPD', 'MCH', 'U5 Clinic', 'Ward', 'Other'
                
                $this->final_data_set['location'] = $data;
                
                //note, I've introduced $final_data_set to be used in place of $data since $data is reset and reused
                
                $data = $data_set = $data_series = $analytic_var = $data_categories = array();
                
                //unset the arrays for reuse
                
                //return $this -> final_data_set;
                
                
            } else {
                return null;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        /*--------------------end ort equipment location of availability----------------------------------------------*/
        
        /*--------------------begin ort equipment availability by functionality----------------------------------------------*/
        $query = "SELECT
    ea.eq_code as equipment,
    SUM(ea.ae_fully_functional) AS total_functional,
    SUM(ea.ae_non_functional) AS total_non_functional
FROM
    available_equipments ea
WHERE
    ea.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')" . $criteria_condition . ")
        AND ea.eq_code IN (SELECT
            eq_code
        FROM
            equipments
        WHERE
            eq_for = 'ort')
        AND ea.ae_fully_functional != - 1
        AND ea.ae_non_functional != - 1
GROUP BY ea.eq_code
ORDER BY ea.eq_code ASC";
        
        //echo $query; die;
        try {
            $this->dataSet = $this->db->query($query, array($value));
            
            $this->dataSet = $this->dataSet->result_array();
            
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                
                //prep data for the pie chart format
                $size = count($this->dataSet);
                
                foreach ($this->dataSet as $value_) {
                    if ($this->getCHEquipmentName($value_['equipment']) == 'Table spoons' || $this->getCHEquipmentName($value_['equipment']) == 'Wall Clock/Timing device' || $this->getCHEquipmentName($value_['equipment']) == 'Weighing scale' || $this->getCHEquipmentName($value_['equipment']) == 'Thermometer') {
                        
                        //1. collect the categories
                        $data_categories[] = $this->getCHEquipmentName($value_['equipment']);
                        
                        //includes duplicates--so we'll array_unique outside the foreach()
                        
                        //data set by each equipment
                        $data_set[$this->getCHEquipmentName($value_['equipment']) ][] = array('Fully-functional' => intval($value_['total_functional']), 'Non-functional' => intval($value_['total_non_functional']));
                    }
                }
                
                //var_dump($analytic_var);die;
                
                //make cat array unique if we got duplicates and set to $data array
                $data['categories'] = array_values(array_unique($data_categories));
                
                //expected 28
                
                //get a unique set of analytic variables
                $analytic_var = array('Fully-functional', 'Non-functional');
                
                //expected to be 2 in this particular context
                
                //assign data set to $data
                $data['responses'] = $data_set;
                
                //assign $data to $final_data_set
                $this->final_data_set['quantities'] = $data;
                $data = $data_set = $data_series = $analytic_var = $data_categories = array();
                
                //unset the arrays for reuse
                
                /*--------------------end ort equipment availability by quantity----------------------------------------------*/
                
                // /var_dump($this -> final_data_set['quantities']);die;
                
                return $this->final_data_set;
            } else {
                return null;
            }
            
            //die(var_dump($this->final_data_set));
            
            
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
    }
    
    public function getCHCommoditySupplier($criteria, $value, $survey, $survey_category) {
        
        /*using CI Database Active Record*/
        $data = $data_set = $data_series = $analytic_var = $data_categories = array();
        
        //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
        
        
        
        /**
         * something of this kind:
         * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
         */
        
        switch ($criteria) {
            case 'national':
                $criteria_condition = ' ';
                break;

            case 'county':
                $criteria_condition = 'WHERE fac_county=?';
                break;

            case 'district':
                $criteria_condition = 'WHERE fac_district=?';
                break;

            case 'facility':
                $criteria_condition = 'WHERE fac_mfl=?';
                break;

            case 'none':
                $criteria_condition = '';
                break;
        }
        
        /*--------------------begin equipment main supplier----------------------------------------------*/
        $query = "SELECT count(ca.supplier_code) AS total_response,ca.comm_code as commodities,ca.supplier_code AS supplier, c.comm_unit as unit FROM available_commodities ca,commodities c
                 WHERE ca.comm_code=c.comm_code AND ca.fac_mfl IN (SELECT fac_mfl FROM facilities f
        JOIN
    survey_status ss ON ss.fac_id = f.fac_mfl
        JOIN
    survey_types st ON (st.st_id = ss.st_id
        AND st.st_name = '" . $survey . "')" . $criteria_condition . ")

                 AND ca.comm_code IN (SELECT comm_code FROM commodities WHERE comm_for='" . $survey . "')
                GROUP BY ca.comm_code,ca.supplier_code
                ORDER BY ca.comm_code";
        try {
            
            $this->dataSet = $this->db->query($query, array($value));
            
            $this->dataSet = $this->dataSet->result_array();
            
            //echo($this->db->last_query());die;
            if ($this->dataSet !== NULL) {
                
                //prep data for the pie chart format
                $size = count($this->dataSet);
                
                foreach ($this->dataSet as $value_) {
                    
                    //1. collect the categories
                    $data_categories[] = $value_['supplier'];
                    
                    //incase of duplicates--do an array_unique outside the foreach()
                    
                    //2. collect the analytic variables
                    $analytic_var[] = $this->getCommodityNameById($value_['commodities']) . '[' . $value_['unit'] . ']';
                    
                    //includes duplicates--so we'll array_unique outside the foreach()
                    
                    //data set by each analytic variable
                    $data_set[$value_['supplier']][] = intval($value_['total_response']);
                }
                
                //var_dump($data_set);die;
                
                //make cat array unique if we got duplicates then json_encode and set to $data array
                $data['categories'] = (array_values(array_unique($data_categories)));
                
                //expected 28
                
                //get a unique set of analytic variables
                $analytic_var = array_unique($analytic_var);
                
                //expected to be 3 in this particular context
                $data['analytic_variables'] = $analytic_var;
                
                //get the data sets
                $data['responses'] = $data_set;
                
                //sets of the 3 analytic variables: Available | Sometimes Available | Never Available
                
                $this->dataSet = $data;
                
                return $this->dataSet;
            } else {
                return $this->dataSet = null;
            }
        }
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
    }
    
    /*
     * Services to Children with Diarrhoea
    */
    public function getChildrenServices($criteria, $value, $survey, $survey_category) {
        
        /*using CI Database Active Record*/
        $data = $data_set = $data_series = $analytic_var = $data_categories = array();
        $data_y = array();
        $data_n = array();
        $temp = $muac = $weight = $height = $mch = array();
        $MCHY = $MCHN = $temperatureY = $temperatureN = $weightY = $weightN = $HLY = $HLN = $MUACY = $MUACN = 0;
        
        //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
        
        
        
        /**
         * something of this kind:
         * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
         */
        
        switch ($criteria) {
            case 'national':
                $criteria_condition = ' ';
                break;

            case 'county':
                $criteria_condition = 'WHERE fac_county=?';
                break;

            case 'district':
                $criteria_condition = 'WHERE fac_district=?';
                break;

            case 'facility':
                $criteria_condition = 'WHERE fac_mfl=?';
                break;

            case 'none':
                $criteria_condition = '';
                break;
        }
        
        $query = "SELECT
    il.indicator_code AS indicator, il.li_response as response
FROM
    log_indicators il
WHERE
    il.indicator_code IN (SELECT
            indicator_code
        FROM
            indicators
        WHERE
            indicator_for = 'svc')
        AND il.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
        JOIN
    survey_status ss ON ss.fac_id = f.fac_mfl
        JOIN
    survey_types st ON (st.st_id = ss.st_id
        AND st.st_name = 'mnh')" . $criteria_condition . ")";
        
        try {
            $this->dataSet = $this->db->query($query, array($value));
            $this->dataSet = $this->dataSet->result_array();
            
            //echo $this->db->last_query();die;
            if ($this->dataSet !== NULL) {
                
                //prep data for the pie chart format
                $size = count($this->dataSet);
                $i = 0;
                $yesCount = 0;
                $noCount = 0;
                
                //var_dump($this->dataSet);
                
                //Forced One Values
                foreach ($this->dataSet as $value) {
                    switch ($this->getChildHealthIndicatorName($value['indicator'])) {
                        case 'Use of MCH booklet':
                            if ($value['response'] == 'Yes') {
                                $MCHY++;
                            } else if ($value['response'] == 'No') {
                                $MCHN++;
                            }
                            break;

                        case 'Temperature taken':
                            if ($value['response'] == 'Yes') {
                                $temperatureY++;
                            } else if ($value['response'] == 'No') {
                                $temperatureN++;
                            }
                            break;

                        case 'Weight taken':
                            if ($value['response'] == 'Yes') {
                                $weightY++;
                            } else if ($value['response'] == 'No') {
                                $weightN++;
                            }
                            break;

                        case 'Height/Length taken':
                            if ($value['response'] == 'Yes') {
                                $HLY++;
                            } else if ($value['response'] == 'No') {
                                $HLN++;
                            }
                            break;

                        case 'MUAC taken':
                            if ($value['response'] == 'Yes') {
                                $MUACY++;
                            } else if ($value['response'] == 'No') {
                                $MUACN++;
                            }
                            break;
                        }
                        
                        //echo $MCHY;
                        /*if ($value['response'] == 'Yes') {
                        $data_y[] = array($this -> getChildHealthIndicatorName($value['indicator']), 1);
                        $yesCount++;
                        } else if ($value['response'] == 'No') {
                        $data_n[] = array($this -> getChildHealthIndicatorName($value['indicator']), 1);
                        $noCount++;
                        }*/
                        
                        //get a set of the 5 services offered
                        
                        
                    }
                    $data_categories = array('Use of MCH booklet', 'Temperature taken', 'Weight taken', 'Height/Length taken', 'MUAC taken');
                    $data['categories'] = $data_categories;
                    
                    $data['yes_values'] = array((int)$MCHY, (int)$temperatureY, (int)$weightY, (int)$HLY, (int)$MUACY);
                    $data['no_values'] = array((int)$MCHN, (int)$temperatureN, (int)$weightN, (int)$HLN, (int)$MUACN);
                    
                    $this->dataSet = $data;
                    
                    //var_dump($this->dataSet);die;
                    return $this->dataSet;
                } else {
                    return $this->dataSet = null;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $this->dataSet;
        }
        
        /**
         * [getIndicatorStatistics description]
         * @param  [type] $criteria [description]
         * @param  [type] $value    [description]
         * @param  [type] $survey   [description]
         * @param  [type] $for      [description]
         * @return [type]           [description]
         */
        public function getIndicatorStatistics($criteria, $value, $survey, $survey_category, $for) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            $data_y = array();
            $data_n = array();
            
            $query = "CALL get_indicator_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo $this->db->last_query();die;
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    $i = 0;
                    
                    //echo '<pre>';print_r($this->dataSet);echo '</pre>';die;
                    // foreach ($this->dataSet as $value) {
                    //     if (array_key_exists('response', $value)) {
                    //         $data[$value['indicator_name']][$value['frequency']] = (int)$value['total_response'];
                    //     }
                    // }
                    
                    foreach ($this->dataSet as $value) {
                       
                        $indicator = $value['indicator_name'];
                        
                        //echo $value['indicator'];die;
                        $data['response'][$indicator][$value['response']] = (int)$value['total_response'];
                        
                        $data['categories'] = array_keys($data['response']);
                    }
                    $this->dataSet = $data;
                    
                    return $this->dataSet;
                } else {
                    
                    return $this->dataSet = null;
                }
                
                //die(var_dump($this->dataSet));
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $this->dataSet;
        }
        public function getIndicatorComparison($criteria, $value, $survey, $survey_category, $for) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            $data_y = array();
            $data_n = array();
            
            $query = "CALL get_indicator_comparison('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo $this->db->last_query();die;
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    $i = 0;
                    
                    //var_dump($this->dataSet);
                    foreach ($this->dataSet as $value) {
                        $data[$value['indicator_name']][$value['verdict']] = (int)$value['total'];
                    }
                    $this->dataSet = $data;
                    
                    return $this->dataSet;
                } else {
                    
                    return $this->dataSet = null;
                }
                
                //die(var_dump($this->dataSet));
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        public function getIndicatorTypes() {
            $query = "SELECT * FROM indicator_lookup";
            try {
                $this->dataSet = $this->db->query($query);
                $this->dataSet = $this->dataSet->result_array();
            }
            catch(exception $ex) {
            }
            
            return $this->dataSet;
        }
        
        /*
         * Diarrhoea case numbers per Month
        */
        public function getDiarrhoeaCaseNumbers($criteria, $value, $survey) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            
            
            /**
             * something of this kind:
             * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
             */
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            
            $query = "SELECT SUM(d.jan13) AS jan, SUM(d.feb13) AS feb, SUM(d.mar13) AS mar, SUM(d.apr13) AS apr,
SUM(d.may13) AS may, SUM(d.june13) AS june, SUM(d.july13) AS july, SUM(d.aug13) AS aug,
SUM(d.sept13) AS sept, SUM(d.oct13) AS oct, SUM(d.nov13) AS nov, SUM(d.dec13) AS december
FROM morbidity_data_log d WHERE d.fac_mfl IN (SELECT fac_mfl FROM facilities
WHERE " . $status_condition . "  " . $criteria_condition . ")";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                //echo $this->db->last_query();die;
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    $i = 0;
                    
                    foreach ($this->dataSet as $value => $key) {
                        $data['num_of_diarrhoea_cases'][] = $key;
                    }
                    
                    //fixed set of 12 months in a year..not an option but to hard code.. :)
                    $data_categories = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
                    
                    $data['categories'] = $data_categories;
                    
                    $this->dataSet = $data;
                    
                    //var_dump($this->dataSet);die;
                    return $this->dataSet;
                } else {
                    return $this->dataSet = null;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
        }
        
        /*
         * Diarrhoea case treatments
        */
        
        public function getDiarrhoeaCaseTreatment($criteria, $value, $survey, $survey_category) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            
            
            /**
             * something of this kind:
             * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
             */
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            
            $query = "SELECT tl.treatmentID AS treatment,SUM(tl.severeDehydrationNo) AS severe_dehydration, SUM(tl.someDehydrationNo) AS some_dehydration,
SUM(tl.noDehydrationNo) AS no_dehydration, SUM(tl.dysentryNo) AS dysentry, SUM(tl.noClassificationNo) AS no_classification
FROM log_treatment tl WHERE tl.treatmentID IN (SELECT treatmentCode FROM mch_treatments
WHERE treatmentFor='dia') AND tl.fac_mfl IN (SELECT fac_mfl FROM facilities WHERE " . $status_condition . "  " . $criteria_condition . ")
GROUP BY tl.treatmentID ORDER BY tl.treatmentID ASC";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                //echo $this->db->last_query();die;
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    $i = 0;
                    
                    foreach ($this->dataSet as $value) {
                        
                        //get a set of the 5 diarrhoea treatment types available
                        $data_categories[] = $this->getChildHealthTreatmentName($value['treatment']);
                        
                        //get the responses by classification per given treatment type
                        $data[$this->getChildHealthTreatmentName($value['treatment']) ] = array('severe_dehydration' => intval($value['severe_dehydration']), 'some_dehydration' => intval($value['some_dehydration']), 'no_dehydration' => intval($value['no_dehydration']), 'dysentry' => intval($value['dysentry']), 'no_classification' => intval($value['no_classification']));
                    }
                    
                    $data['categories'] = $data_categories;
                    
                    $this->dataSet = $data;
                    
                    //var_dump($this->dataSet);die;
                    return $this->dataSet;
                } else {
                    return $this->dataSet = null;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
        }
        
        /*
         * ORT Corner Assessment
        */
        public function getORTCornerAssessment($criteria, $value, $survey, $survey_category) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            $data_y = array();
            $data_n = array();
            $functionalTotalY = $functionalTotalN = $rehydrationTotalY = $rehydrationTotalN = $locationY = $locationN = 0;
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            
            
            /**
             * something of this kind:
             * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
             */
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            
            $query = "SELECT
    oa.question_code AS assessment_item, oa.lq_response as response
FROM
    log_questions oa
WHERE
    oa.question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = 'ort')
        AND oa.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = 'ch')" . $criteria_condition . ")
ORDER BY oa.question_code ASC";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                //echo $this->db->last_query();
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    $i = 0;
                    
                    foreach ($this->dataSet as $value) {
                        switch ($this->getQuestionName($value['assessment_item'])) {
                            case 'Is the ORT Corner functional?':
                                if ($value['response'] == 'Yes') {
                                    $functionalTotalY++;
                                } else if ($value['response'] == 'No') {
                                    $functionalTotalN++;
                                }
                                break;

                            case 'Does this Facility have a designated location for oral rehydration?':
                                if ($value['response'] == 'Yes') {
                                    $rehydrationTotalY++;
                                } else if ($value['response'] == 'No') {
                                    $rehydrationTotalN++;
                                }
                                break;

                            case 'Where is the designated location of the ORT Corner?':
                                if ($value['response'] == 'Yes') {
                                    $locationY++;
                                } else if ($value['response'] == 'No') {
                                    $locationN++;
                                }
                                break;
                            }
                            
                            /*if ($value['response'] == 'Yes') {
                                $data_y[] = array($this -> getChildHealthQuestionName($value['assessment_item']), 1);
                                } else if ($value['response'] == 'No') {
                                $data_n[] = array($this -> getChildHealthQuestionName($value['assessment_item']), 1);
                                }*/
                            
                            //get a set of the 3 items for ORT assessment
                            
                            
                        }
                        $data['categories'] = array('Is the ORT Corner functional?', 'Does this Facility have a designated location for oral rehydration?');
                        $data['yes_values'] = array($functionalTotalY, $rehydrationTotalY);
                        $data['no_values'] = array($functionalTotalN, $rehydrationTotalN);
                        
                        $this->dataSet = $data;
                        
                        //var_dump($this->dataSet);die;
                        return $this->dataSet;
                    } else {
                        return $this->dataSet = null;
                    }
                    
                    //die(var_dump($this->dataSet));
                    
                    
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
        }
        
        public function getReasonStatistics($criteria, $value, $survey, $survey_category, $for) {
            $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_reason_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $value) {
                        if (array_key_exists('frequency', $value)) {
                            $data[$value['question_name']][$value['frequency']] = (int)$value['total_response'];
                        }
                    }
                }
                
                /**
                 * Fix Data
                 */
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        /**
         * [getEquipmentStatistics description]
         * @param  [type] $criteria  [description]
         * @param  [type] $value     [description]
         * @param  [type] $survey    [description]
         * @param  [type] $for       [description]
         * @param  [type] $statistic [description]
         * @return [type]            [description]
         */
        public function getEquipmentStatistics($criteria, $value, $survey, $survey_category, $for, $statistic) {
            $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_equipment_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "','" . $statistic . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $value) {
                        if (array_key_exists('frequency', $value)) {
                            $data[$value['equipment_name']][$value['frequency']] = (int)$value['total_response'];
                        } else if (array_key_exists('location', $value)) {
                            $location = explode(',', $value['location']);
                            foreach ($location as $place) {
                                $data[$value['equipment_name']][$place]+= (int)$value['total_response'];
                            }
                        } else if (array_key_exists('total_functional', $value)) {
                            $data[$value['equipment_name']]['functional']+= (int)$value['total_functional'];
                            $data[$value['equipment_name']]['non_functional']+= (int)$value['total_non_functional'];
                        }
                    }
                    
                    /**
                     * Fix Data
                     */
                    switch ($survey) {
                        case 'mnh':
                            $location = array('Delivery room', 'Store', 'Pharmacy', 'Other');
                            break;

                        case 'ch':
                            $location = array('MCH', 'OPD', 'Ward', 'Other', 'U5 Clinic');
                            break;

                        default:
                            $location = array();
                            break;
                    }
                    if ($statistic == 'location') {
                        foreach ($data as $key => $value) {
                            foreach ($location as $place) {
                                if (array_key_exists($place, $value) == false) {
                                    $newData[$key][$place] = 0;
                                } else {
                                    $newData[$key][$place] = $value[$place];
                                }
                            }
                        }
                        $data = $newData;
                    }
                } else {
                    return null;
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }

        public function getCommodityUsageOptions($criteria, $value, $survey, $survey_category, $for, $statistic) {
        	
            $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_commodity_usage('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "','" . $statistic . "');";
            //echo $query;die;
            try {
                $queryData = $this->db->query($query);
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    foreach ($this->dataSet as $value) {
                        $data['data'][] = $value;
                    }
                    $commodities = $this->getAllCommodityNames();
                    foreach ($commodities as $commodity) {
                        if ($commodity['commFor'] == $for) {
                            $data['commodities'][] = $commodity['commName'];
                        }
                    }

                    $commodityOptions = $this->getCommodityUsageOptionsList();
                    foreach ($commodityOptions as $option) {
                        $data['commodity_options'][$option['cooId']] = $option['cooDescription'];
                    }
                     //echo "<pre>";print_r($data);echo "</pre>";die;
                }
                
                //echo "<pre>";print_r($data);echo "</pre>";die;
                
                
            }
            
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        public function getCommodityStatistics($criteria, $value, $survey, $survey_category, $for, $statistic) {
            $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_commodity_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "','" . $statistic . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $value) {
                        
                        //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                        if (array_key_exists('frequency', $value)) {
                            $data[$value['commodity_name']][$value['frequency']] = (int)$value['total_response'];
                        } else if (array_key_exists('location', $value)) {
                            $location = explode(',', $value['location']);
                            foreach ($location as $place) {
                                $data[$value['commodity_name']][$place]+= (int)$value['total_response'];
                            }
                        } else if (array_key_exists('reason', $value)) {
                                 $reason = $value['reason'];
                                 $reason = trim($reason , "1. ");
                                 $reason = trim($reason , "2. ");
                                 $reason = trim($reason , "3. ");
                                 if($reason!='' && $reason!='Select One'){
$data[$value['commodity_name']][$reason]+= (int)$value['total_response'];
                                 }
                                 

                        } else if (array_key_exists('unit', $value)) {
                            $data[$value['commodity_name']][$value['unit']] = (int)$value['total_response'];
                        } else if (array_key_exists('supplier_code', $value)) {
                            $data[$value['commodity_name']][$value['supplier_code']] = (int)$value['supplier_name'];
                        }
                    }
                    // unset($data['']);
                    // unset($data['Select One']);
                    // echo "<pre>";print_r($data);echo "</pre>";
                    
                    
                    
                    /**
                     * Fix Data
                     */
                    switch ($survey) {
                        case 'mnh':
                            $location = array('Delivery room', 'Store', 'Pharmacy', 'Other');
                            break;

                        case 'ch':
                            $location = array('MCH', 'OPD', 'Ward', 'Other', 'U5 Clinic');
                            break;

                        default:
                            $location = array();
                            break;
                    }
                    if ($statistic == 'location') {
                        foreach ($data as $key => $value) {
                            foreach ($location as $place) {
                                if (array_key_exists($place, $value) == false) {
                                    $newData[$key][$place] = 0;
                                } else {
                                    $newData[$key][$place] = $value[$place];
                                }
                            }
                        }
                        $data = $newData;
                    } else {
                    }
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    
                    
                }
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        /**
         * [getSuppliesStatistics description]
         * @param  [type] $criteria  [description]
         * @param  [type] $value     [description]
         * @param  [type] $survey    [description]
         * @param  [type] $for       [description]
         * @param  [type] $statistic [description]
         * @return [type]            [description]
         */
        public function getSuppliesStatistics($criteria, $value, $survey, $survey_category, $for, $statistic) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_supplies_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "','" . $statistic . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    
                     //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    foreach ($this->dataSet as $value) {
                        if (array_key_exists('frequency', $value)) {
                            $data[$value['supply_name']][$value['frequency']] = (int)$value['total_response'];
                        } else if (array_key_exists('location', $value)) {
                            $location = explode(',', $value['location']);
                           foreach ($location as $place) {
                                $data[$value['supply_name']][$place]+= (int)$value['total_response'];
                            }
                           //$data[$value['supply_name']][$value['location']]=(int)$value['total_response'];
                        } else if (array_key_exists('total_functional', $value)) {
                            $data[$value['supply_name']]['functional']+= (int)$value['total_functional'];
                            $data[$value['supply_name']]['non_functional']+= (int)$value['total_non_functional'];
                        } else if (array_key_exists('supply_name', $value)) {
                            $data[$value['supply_name']][$value['supply_name']] = (int)$value['total_response'];
                        }
                    }
                     //echo "<pre>";print_r($data);echo "</pre>";die;
                    /**
                     * Fix Data
                     */
                    // switch ($survey) {
                    //     case 'mnh':
                    //         $location = array('Delivery room', 'Store', 'Pharmacy', 'Other');
                    //         break;

                    //     case 'ch':
                    //         $location = array('MCH', 'OPD', 'Ward', 'Other', 'U5 Clinic');
                    //         break;

                    //     default:
                    //         $location = array();
                    //         break;
                    // }
                    // if ($statistic == 'location') {
                    //     foreach ($data as $key => $value) {
                    //         foreach ($location as $place) {
                    //             if (array_key_exists($place, $value) == false) {
                    //                 $newData[$key][$place] = 0;
                    //             } else {
                    //                 $newData[$key][$place] += $value[$place];
                    //             }
                    //         }
                    //     }
                    //     $data = $newData;
                    // }
                     //echo "<pre>";print_r($data);echo "</pre>";die;
                }
            }
            catch(exception $ex) {
            }
            return $data;
        }
        
        /*
         * Availability, Location and Functionality of Supplies at ORT Corner
        */
        public function getCHSuppliesSupplier($criteria, $value, $survey, $survey_category) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            
            
            /**
             * something of this kind:
             * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
             */
            
            /*--------------------begin equipment main supplier----------------------------------------------*/
            $query = "SELECT
    count(sq.supply_code)/2 AS total_response,
    sq.supply_code as supplies,
    sq.supplier_code AS supplier
FROM
    available_supplies sq,
    supplies c
WHERE
    sq.supply_code = c.supply_code
        AND sq.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
                 " . $criteria_condition . ")
        AND sq.supply_code IN (SELECT
            supply_code
        FROM
            supplies
        WHERE
            supply_for = '$survey')
GROUP BY sq.supply_code , sq.supply_code
ORDER BY sq.supply_code
LIMIT 0 , 1000
";
            try {
                
                $this->dataSet = $this->db->query($query, array($value));
                
                $this->dataSet = $this->dataSet->result_array();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    
                    foreach ($this->dataSet as $value_) {
                        
                        //1. collect the categories
                        $data_categories[] = $value_['supplier'];
                        
                        //incase of duplicates--do an array_unique outside the foreach()
                        
                        //2. collect the analytic variables
                        $analytic_var[] = $this->getSupplyName($value_['supplies'], $survey);
                        
                        //includes duplicates--so we'll array_unique outside the foreach()
                        
                        //data set by each analytic variable
                        $data_set[$value_['supplier']][] = intval($value_['total_response']);
                    }
                    
                    //var_dump($data_set);die;
                    
                    //make cat array unique if we got duplicates then json_encode and set to $data array
                    $data['categories'] = (array_values(array_unique($data_categories)));
                    
                    //expected 28
                    
                    //get a unique set of analytic variables
                    $analytic_var = array_unique($analytic_var);
                    
                    //expected to be 3 in this particular context
                    $data['analytic_variables'] = $analytic_var;
                    
                    //get the data sets
                    $data['responses'] = $data_set;
                    
                    //sets of the 3 analytic variables: Available | Sometimes Available | Never Available
                    
                    $this->dataSet = $data;
                    
                    return $this->dataSet;
                } else {
                    return $this->dataSet = null;
                }
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
        }
        
        public function getChallengeStatistics($criteria, $value, $survey, $survey_category) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            $data = array();
            
            $query = "CALL get_challenge_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    foreach ($this->dataSet as $value) {
                        if (array_key_exists('challenge', $value)) {
                            $data[$value['ch_name']] = (int)$value['total_response'];
                        }
                    }
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    //die(var_dump($this->dataSet));
                    
                    
                }
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        /*
         *  Availability, Location and Functionality of Electricity and Hardware Resources
        */
        public function getResourcesStatistics($criteria, $value, $survey, $survey_category, $for, $statistic) {
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            
            
            /**
             * something of this kind:
             * $data_series[0]="name: '.$value['analytic_variable'].',data:".json_encode($data_set[0])
             */
            
            $query = "CALL get_resource_statistics('" . $criteria . "', '" . $value . "', '" . $survey . "', '" . $survey_category . "', '" . $for . "','" . $statistic . "');";
            
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    foreach ($this->dataSet as $value) {
                        if (array_key_exists('frequency', $value)) {
                            $data[$value['resource_name']][$value['frequency']] = (int)$value['total_response'];
                        } else if (array_key_exists('location', $value)) {
                        	$location = explode(',', $value['location']);
                            foreach ($location as $place) {
                                $data[$this->getCHEquipmentName($value['equipment'])][$place]+= (int)$value['total_response'];
                            }
                        }
                        if (array_key_exists('suppliers', $value)) {
                            $data[$value['resource_name']][$value['suppliers']] = (int)$value['total_response'];
                        }if(array_key_exists('mainsource', $value)){
                        	$data[$value['equipment_name']][$value['mainsource']]=(int)$value['total_response'];
                        }
                    }
                    // echo "<pre>";print_r($data);echo "</pre>";die;
                    
                    /**
                     * Fix Data
                     */
                    switch ($survey) {
                        case 'mnh':
                            $location = array('Delivery room', 'Pharmacy', 'Store', 'Other', 'Not Applicable');
                            break;

                        case 'ch':
                            $location = array('MCH', 'OPD', 'Ward', 'Other', 'U5 Clinic');
                            break;

                        default:
                            $location = array();
                            break;
                    }
                    if ($statistic == 'location') {
                        foreach ($data as $key => $value) {
                            foreach ($location as $place) {
                                if (array_key_exists($place, $value) == false) {
                                    $newData[$key][$place] = 0;
                                } else {
                                    $newData[$key][$place] = $value[$place];
                                }
                            }
                        }
                        $data = $newData;
                    }
                } else {
                }
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //var_dump($data);die;
            
            return $data;
        }
        
        public function get_response_count($survey) {
            try {
                
                /*using CI Database Active Record*/
                try {
                    $query = "SELECT DISTINCT(facilityCode),trackerID,lastActivity FROM assessment_tracker WHERE survey=? AND trackerSection='section-6'
ORDER BY lastActivity DESC";
                    $this->dataSet = $this->db->query($query, array($survey));
                    $this->dataSet = $this->dataSet->result_array();
                    
                    //die(var_dump($this->dataSet));
                    
                    
                }
                catch(exception $ex) {
                    
                    //ignore
                    //die($ex->getMessage());//exit;
                    
                    
                }
                return $this->dataSet;
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex -> getMessage());
                
                
            }
            
            return $this->dataSet;
        }
        
        function getSpecificDistrictNames($county) {
            
            /*using DQL*/
            try {
                $query = $this->em->createQuery('SELECT DISTINCT(f.facDistrict) FROM  models\Entities\Facilities f WHERE f.facCounty = :county ORDER BY f.facDistrict ASC');
                $query->setParameter('county', $county);
                $this->districtName = $query->getResult();
                
                //die(var_dump($this->districtName));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //$ex->getMessage();
                
                
            }
            return $this->districtName;
            
            //
            
            
        }
        function getSpecificFacilityNames($district) {
            
            /*using DQL*/
            try {
                $query = $this->em->createQuery('SELECT DISTINCT(f.facName,f.facMfl) FROM  models\Entities\Facilities f WHERE f.facDistrict = :district ORDER BY f.facName ASC');
                $query->setParameter('district', $district);
                $this->facilityName = $query->getResult();
                
                //die(var_dump($this->districtName));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //$ex->getMessage();
                
                
            }
            return $this->facilityName;
        }
        
        /*end of getSpecificDistrictNames*/
        
        function getCountyFacilities() {
            
            /*using DQL*/
            try {
                $query = "SELECT COUNT(facilities.fac_name),fac_county FROM facilities GROUP BY facilities.fac_county ORDER BY COUNT(facilities.fac_name) DESC;";
                $this->countyFacilities = $this->db->query($query);
                $this->countyFacilities = $this->countyFacilities->result_array();
                
                //die(var_dump($this->districtName));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //$ex->getMessage();
                
                
            }
            return $this->countyFacilities;
        }
        
        /*end of getSpecificDistrictNames*/
        
        function getCountyFacilitiesByOwner($county) {
            
            /*using DQL*/
            try {
                $query = "SELECT COUNT(facilityOwnedBy),facilityOwnedBy FROM facilities WHERE fac_county='Nairobi' GROUP BY facilityOwnedBy ORDER BY COUNT(facilityOwnedBy) DESC;";
                $this->countyFacilities = $this->db->query($query);
                $this->countyFacilities = $this->countyFacilities->result_array();
                
                //die(var_dump($this->districtName));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //$ex->getMessage();
                
                
            }
            return $this->countyFacilities;
        }
        
        /*end of getSpecificDistrictNames*/
        public function getFacilitiesByDistrictOptions($district, $survey) {
            switch ($survey) {
                case 'ch':
                    $search = "facilityCHSurveyStatus='complete'";
                    break;

                case 'mnh':
                    $search = "ss_id='complete'";
                    break;
            }
            $myOptions = '<option>Please Select a Facility</option>';
            
            /*using CI Database Active Record*/
            try {
                $query = "SELECT DISTINCT
    f.fac_mfl, f.fac_name
            FROM
    facilities f
        JOIN
    survey_status ss ON ss.fac_id = f.fac_mfl
        JOIN
    survey_types st ON (st.st_id = ss.st_id
        AND st.st_name = '" . $survey . "')
WHERE
    fac_district = '" . $district . "'
ORDER BY fac_name;";
                $this->dataSet = $this->db->query($query);
                $this->dataSet = $this->dataSet->result_array();
                
                //die(var_dump($this->dataSet));
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    
                    foreach ($this->dataSet as $value_) {
                        $myOptions.= '<option value=' . $value_['fac_mfl'] . '>' . $value_['fac_name'] . '</option>';
                        
                        //1. collect the categories
                        //$data_categories[] = $this -> getCHEquipmentName($value_['equipment']);
                        //incase of duplicates--do an array_unique outside the foreach()
                        
                        
                    }
                    
                    //unset the arrays for reuse
                    
                    //return $this -> final_data_set;
                    
                    
                } else {
                    return null;
                }
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            return $myOptions;
        }
        
        public function getFacilitiesByDistrictOptionsNew($district, $table) {
            $myOptions = '<option>Please Select a Facility</option>';
            
            /*using CI Database Active Record*/
            try {
                $query = "SELECT DISTINCT
facilities.fac_mfl, facilities.fac_name
FROM
facility,
mch_indicator_log
WHERE
fac_district = '" . $district . "'
AND facilities.fac_mfl = " . $table . ".fac_mfl
ORDER BY fac_name;";
                $this->dataSet = $this->db->query($query);
                $this->dataSet = $this->dataSet->result_array();
                
                //die(var_dump($this->dataSet));
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    
                    foreach ($this->dataSet as $value_) {
                        $myOptions.= '<option value=' . $value_['fac_mfl'] . '>' . $value_['fac_name'] . '</option>';
                        
                        //1. collect the categories
                        //$data_categories[] = $this -> getCHEquipmentName($value_['equipment']);
                        //incase of duplicates--do an array_unique outside the foreach()
                        
                        
                    }
                    
                    //unset the arrays for reuse
                    
                    //return $this -> final_data_set;
                    
                    foreach ($this->dataSet as $value_) {
                        $myOptions.= '<option value=' . $value_['fac_mfl'] . '>' . $value_['fac_name'] . '</option>';
                        
                        //1. collect the categories
                        //$data_categories[] = $this -> getCHEquipmentName($value_['equipment']);
                        //incase of duplicates--do an array_unique outside the foreach()
                        
                        
                    }
                } else {
                    return null;
                }
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            return $myOptions;
        }
        
        public function getReportingCountiesCore() {
            
            /*using CI Database Active Record*/
            try {
                $query = "SELECT
    f.fac_county as county,c.countyID as countyID
FROM
    mnh_latest.assessment_tracker t,
    facilities f,county c
WHERE
    t.facilityCode = f.fac_mfl
        and t.trackerSection = 'section-6'
AND
c.countyName =  f.fac_county
GROUP BY f.fac_county
ORDER BY f.fac_county ASC;";
                $this->dataSet = $this->db->query($query);
                $this->dataSet = $this->dataSet->result_array();
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //var_dump($myOptions);
            //var_dump($this -> dataSet);
            return $this->dataSet;
        }
        
        /**
         * List of Counties that have reported
         */
        public function getReportingCounties() {
            
            /*using CI Database Active Record*/
            try {
                
                /*$query = "SELECT
                f.fac_county as county, c.county_id as countyID
                FROM
                facilities f
                JOIN
                survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
                survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
                JOIN
                survey_categories sc ON (ss.sc_id = sc.sc_id
                AND sc.sc_name = '" . $survey_category . "')
                JOIN assessment_tracker ast ON ast.facilityCode = f.fac_mfl
                AND ast.ast_section = 'section-6' AND ast.ast_survey='" . $survey . "',
                counties c
                WHERE
                c.county_name = f.fac_county
                GROUP BY f.fac_county
                ORDER BY f.fac_county ASC;";*/
                $query = "SELECT county_name as county, county_id as countyID FROM counties";
                $this->dataSet = $this->db->query($query);
                $this->dataSet = $this->dataSet->result_array();
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //var_dump($myOptions);
            //var_dump($this -> dataSet);
            return $this->dataSet;
        }
        
        public function generateFacilityList() {
            $result;
            try {
                $query = "";
                $this->dataSet = $this->db->query($query);
                $this->dataSet = $this->dataSet->result_array();
                
                //die(var_dump($this->dataSet));
                if ($this->dataSet !== NULL) {
                    
                    //prep data for the pie chart format
                    $size = count($this->dataSet);
                    
                    foreach ($this->dataSet as $value_) {
                    }
                } else {
                    return null;
                }
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            return $result;
        }
        
        function getAllReportingRatio($survey, $survey_category) {
            $reportingCounties = $this->getReportingCounties($survey, $survey_category);
            
            //var_dump($reportingCounties);die;
            for ($x = 0; $x < sizeof($reportingCounties); $x++) {
                $allData[$reportingCounties[$x]['county']] = $this->getReportingRatio($survey, $survey_category, $reportingCounties[$x]['county'],'county');
            }
            
            //echo '<pre>';print_r($allData);echo '</pre>';
            return $allData;
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
            return $finalData;
        }
        
        function getFacilityOwnerPerCounty($criteria, $value, $survey, $survey_category) {
            
            /*using DQL*/
            
            $query = "CALL get_ownership_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
            $myData = $this->db->query($query);
            $finalData = $myData->result_array();
            
            //echo $this->db->last_query();die;
            //print_r($finalData);die;
            
            return $finalData;
        }
        
        function getFacilityLevelPerCounty($criteria, $value, $survey, $survey_category) {
            
            /*using DQL*/
            try {
                
                $query = "CALL get_facility_level('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
                
                $myData = $this->db->query($query);
                
                // echo $this->db->last_query();die;
                
                $finalData = $myData->result_array();
                
                //echo $finalData;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //echo($ex -> getMessage());
                
                
            }
            return $finalData;
        }
        function getFacilityTypePerCounty($criteria, $value, $survey, $survey_category) {
            
            /*using DQL*/
            try {
                
                $query = "CALL get_facility_type('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
                
                $myData = $this->db->query($query);
                
                // echo $this->db->last_query();die;
                
                $finalData = $myData->result_array();
                
                //echo $finalData;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //echo($ex -> getMessage());
                
                
            }
            return $finalData;
        }
        
        /**
         * Run County Maps
         */
        public function runMap($survey, $survey_category,$statistic) {
            $myData = array();
            $counties = $this->getAllCountyNames();
            foreach ($counties as $county) {
                $countyName = $county['countyName'];
                
                //$countyName=str_replace("'","", $countyName);
                $myData[$countyName] = array($this->getReportingRatio($survey, $survey_category, $countyName,$statistic), $county['countyFusionMapId'], $countyName);
            }
            
            return $myData;
        }
        
        /**
         * Lists for NO
         */
        public function getFacilityListForNo($criteria, $value, $survey, $survey_category, $choice) {
            urldecode($value);
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    $value = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            switch ($choice) {
                case 'GuidelinesAvailability':
                    
                    //Facility List
                    $query = "SELECT DISTINCT lq.fac_mfl, g.question_name, f.fac_name
                    FROM log_questions lq,questions g, facilities f WHERE response = 'No'AND lq.question_code IN (SELECT question_code FROM questions
                    WHERE  question_for = 'gp') AND lq.fac_mfl IN (SELECT fac_mfl FROM facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")
                     AND lq.question_code = g.question_code AND lq.fac_mfl=f.fac_mfl;";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                $facilities[$value['question_name']][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                        } else {
                            return $this->dataSet = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'ServicesOffered':
                    $query = "SELECT
    i.indicatorName, il.fac_mfl, f.fac_name
FROM
    mch_indicator_log il,
    mch_indicators i,
    facilities f
WHERE
    il.response = 'No'
        AND il.indicatorID = i.indicatorCode
        AND il.fac_mfl = f.fac_mfl
        AND il.indicatorID IN (SELECT
            indicatorCode
        FROM
            mch_indicators
        WHERE
            indicatorFor = 'svc')
        AND il.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                $facilities[$value['indicatorName']][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $this->dataSet = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'DangerSigns':
                    $query = "SELECT
    i.indicatorName, il.fac_mfl, f.fac_name
FROM
    mch_indicator_log il,
    mch_indicators i,
    facilities f
WHERE
    il.response = 'No'
        AND il.indicatorID = i.indicatorCode
        AND il.fac_mfl = f.fac_mfl
        AND il.indicatorID IN (SELECT
            indicatorCode
        FROM
            mch_indicators
        WHERE
            indicatorFor = 'sgn')
        AND il.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                $facilities[$value['indicatorName']][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $this->dataSet = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'ActionsPerformed':
                    $query = "SELECT
    i.indicatorName, il.fac_mfl, f.fac_name
FROM
    mch_indicator_log il,
    mch_indicators i,
    facilities f
WHERE
    il.response = 'No'
        AND il.indicatorID = i.indicatorCode
        AND il.fac_mfl = f.fac_mfl
        AND il.indicatorID IN (SELECT
            indicatorCode
        FROM
            mch_indicators
        WHERE
            indicatorFor = 'dgn')
        AND il.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                $facilities[$value['indicatorName']][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $this->dataSet = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'Counsel Given':
                    $query = "SELECT
    i.indicatorName, il.fac_mfl, f.fac_name
FROM
    mch_indicator_log il,
    mch_indicators i,
    facilities f
WHERE
    il.response = 'No'
        AND il.indicatorID = i.indicatorCode
        AND il.fac_mfl = f.fac_mfl
        AND il.indicatorID IN (SELECT
            indicatorCode
        FROM
            mch_indicators
        WHERE
            indicatorFor = 'cns')
        AND il.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                $facilities[$value['indicatorName']][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $this->dataSet = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'Tools':
                    $query = "SELECT
    i.indicatorName, il.fac_mfl, f.fac_name
FROM
    mch_indicator_log il,
    mch_indicators i,
    facilities f
WHERE
    il.response = 'No'
        AND il.indicatorID = i.indicatorCode
        AND il.fac_mfl = f.fac_mfl
        AND il.indicatorID IN (SELECT
            indicatorCode
        FROM
            mch_indicators
        WHERE
            indicatorFor = 'ror')
        AND il.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                $facilities[$value['indicatorName']][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $this->dataSet = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;
                    
                    /**
                     * MNH Questions
                     */
            }
        }
        
        /**
         * Lists for NEVER
         */
        public function getFacilityListForNever($criteria, $value, $survey, $survey_category, $choice) {
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    $value = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            switch ($choice) {
                case 'Commodity':
                    $query = "SELECT
    ca.fac_mfl,
    f.fac_name,
    ca.Availability AS frequency,
    ca.comm_code as commodities,
    c.comm_unit as unit
FROM
    available_equipments ca,
    commodities c,
    facilities f
WHERE
    ca.Availability = 'Never Available'
        AND ca.comm_code = c.comm_code
        AND ca.fac_mfl = f.fac_mfl
        AND ca.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")
        AND ca.comm_code IN (SELECT
            comm_code
        FROM
            commodities
        WHERE
            comm_for = 'mch')
ORDER BY ca.comm_code";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                
                                //$title[$this->getCommodityNameById($value['commodities'])][]=$this->getCommodityNameById($value['commodities']).'  ['.$value['unit'].']';
                                $facilities[$this->getCommodityNameById($value['commodities']) . '  [' . $value['unit'] . ']'][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //$this -> dataSet = array('breastfeed' => $breastfeed, 'lethargy' => $lethargy);
                            
                            
                        } else {
                            return $facilities = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'ORT':
                    $query = "SELECT
    ea.fac_mfl,
    f.fac_name,
    ea.ae_availability AS frequency,
    ea.eq_code as equipment
FROM
    available_equipments ea,
    facilities f
WHERE
    ea.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")
        AND ea.eq_code IN (SELECT
            eq_code
        FROM
            equipments
        WHERE
            eq_for = 'ort')
        AND ea.ae_availability = 'Never Available'
        AND ea.fac_mfl = f.fac_mfl
ORDER BY ea.eq_code ASC";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            $facilities = array();
                            $size = count($this->dataSet);
                            $i = 0;
                            
                            foreach ($this->dataSet as $value) {
                                $facilities[$this->getCHEquipmentName($value['equipment']) ][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $facilities = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'Water':
                    $query = "SELECT
    sq.fac_mfl,
    f.fac_name,
    sq.supply_code as supplies,
    sq.as_availability AS frequency
FROM
    available_supplies sq,
    supplies s,
    facilities f
WHERE
    sq.supply_code = s.supply_code
        AND sq.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")
        AND sq.supply_code IN (SELECT
            supply_code
        FROM
            supplies
        WHERE
            supply_for = 'mch')
        AND sq.as_availability = 'Never Available'
        AND sq.fac_mfl = f.fac_mfl
ORDER BY sq.supply_code;";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            $facilities = array();
                            $size = count($this->dataSet);
                            $i = 0;
                            
                            foreach ($this->dataSet as $value) {
                                $facilities[$this->getSupplyName($value['supplies']) ][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $facilities = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'Running Water':
                    $query = "SELECT
    sq.fac_mfl,
    f.fac_name,
    sq.supply_code as supplies,
    sq.as_availability AS frequency
FROM
    available_supplies sq,
    supplies s,
    facilities f
WHERE
    sq.supply_code = s.supply_code
        AND sq.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")
        AND sq.supply_code IN (SELECT
            supply_code
        FROM
            supplies
        WHERE
            supply_for = 'mh')
        AND sq.as_availability = 'Never Available'
        AND sq.fac_mfl = f.fac_mfl
ORDER BY sq.supply_code;";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            $facilities = array();
                            $size = count($this->dataSet);
                            $i = 0;
                            
                            foreach ($this->dataSet as $value) {
                                $facilities[$this->getSupplyName($value['supplies']) ][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $facilities = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;

                case 'Resources':
                    switch ($survey) {
                        case 'mnh':
                            $for = "mhw";
                            break;

                        case 'ch':
                            $for = "hwr";
                            break;
                    }
                    $query = "SELECT

    ra.fac_mfl,
    f.fac_name,
    ra.eq_code as equipment,
    ra.ar_availability AS frequency
FROM
    available_resources ra,
    facilities f
WHERE
    ra.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
               " . $criteria_condition . ")
        AND ra.eq_code IN (SELECT
            eq_code
        FROM
            equipments
        WHERE
            eq_for = '" . $for . "')
        AND ra.ar_availability = 'Never Available'
        AND ra.fac_mfl = f.fac_mfl
ORDER BY ra.eq_code ASC";
                    try {
                        $this->dataSet = $this->db->query($query, array($value));
                        $this->dataSet = $this->dataSet->result_array();
                        
                        if ($this->dataSet !== NULL) {
                            
                            $size = count($this->dataSet);
                            $i = 0;
                            $facilities = array();
                            foreach ($this->dataSet as $value) {
                                $facilities[$this->getCHEquipmentName($value['equipment']) ][] = array($value['fac_mfl'], $value['fac_name']);
                            }
                            
                            return $facilities;
                            
                            //var_dump($this->dataSet);die;
                            
                            
                        } else {
                            return $facilities = null;
                        }
                    }
                    catch(exception $ex) {
                    }
                    break;
            }
        }
        
        public function case_summary($county, $choice) {
            $final = array();
            $query = '';
            switch ($choice) {
                case 'Cases':
                    $query = "SELECT
    SUM(CASE
        WHEN tl.lt_severe_dehydration_number = - 1 THEN 0
        ELSE tl.lt_severe_dehydration_number
    END) AS severe_dehydration,
    SUM(CASE
        WHEN tl.lt_some_dehydration_number = - 1 THEN 0
        ELSE tl.lt_some_dehydration_number
    END) AS some_dehydration,
    SUM(CASE
        WHEN tl.lt_no_dehydration_number = - 1 THEN 0
        ELSE tl.lt_no_dehydration_number
    END) AS no_dehydration,
    SUM(CASE
        WHEN tl.lt_dysentry_number = - 1 THEN 0
        ELSE tl.lt_dysentry_number
    END) AS dysentry,
    SUM(CASE
        WHEN tl.lt_no_Classification_number = - 1 THEN 0
        ELSE tl.lt_no_Classification_number
    END) AS no_classification
FROM
    log_treatment tl
WHERE
    tl.treatment_code IN (SELECT
            treatment_code
        FROM
            treatments
        WHERE
            treatment_for = 'dia')
        AND tl.facility_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
    JOIN survey_status ss ON ss.fac_id = f.fac_mfl
    JOIN survey_types st ON (st.st_id = ss.st_id
        AND st.st_name = 'ch') WHERE fac_county='$county');";
                    $results = $this->db->query($query);
                    return $results->result_array();
                    break;

                case 'Classification':
                    $final = array();
                    $query = "SELECT
    tl.treatment_code AS treatment,
    (SUM(CASE
        WHEN tl.lt_severe_Dehydration_number = - 1 THEN 0
        ELSE tl.lt_severe_Dehydration_number
    END) + SUM(CASE
        WHEN tl.lt_some_Dehydration_number = - 1 THEN 0
        ELSE tl.lt_some_Dehydration_number
    END) + SUM(CASE
        WHEN tl.lt_no_Dehydration_number = - 1 THEN 0
        ELSE tl.lt_no_Dehydration_number
    END) + SUM(CASE
        WHEN tl.lt_dysentry_number = - 1 THEN 0
        ELSE tl.lt_dysentry_number
    END) + SUM(CASE
        WHEN tl.lt_no_Classification_number = - 1 THEN 0
        ELSE tl.lt_no_Classification_number
    END)) as total
FROM
    log_treatment tl
WHERE
    tl.treatment_code IN (SELECT
            treatment_code
        FROM
            treatments
        WHERE
            treatment_for = 'dia')
        AND tl.facility_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = 'ch')WHERE fac_county='$county')
GROUP BY tl.treatment_code
ORDER BY tl.treatment_code ASC";
                    $results = $this->db->query($query);
                    $results = $results->result_array();
                    
                    foreach ($results as $result) {
                        
                        //echo $this->getChildHealthTreatmentName($result['treatment']);
                        //$result['treatment']=$this->getChildHealthTreatmentName($result['treatment']);
                        $final[$this->getChildHealthTreatmentName($result['treatment']) ][] = array('treatment' => $this->getChildHealthTreatmentName($result['treatment']), 'total' => $result['total']);
                    }
                    return $final;
                    break;
            }
        }
        
        /**
         * Mother and Neonatal Health Section
         */
        
        //Section 1
        //-----------------------------------------------------------------------------
        
        
        
        /**
         * Nurses Deployed in Maternity
         */
        public function getNursesDeployed($criteria, $value, $survey, $survey_category) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = array();
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            $query = "SELECT
    question_code, SUM(lq_response_count) as response
FROM
    log_questions
WHERE
    question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = 'nur')
        AND fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
                 " . $criteria_condition . ")
GROUP BY question_code
ORDER BY question_code";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                //echo $this->db->last_query();die;
                foreach ($this->dataSet as $value_) {
                    $question = $this->getQuestionName($value_['question_code']);
                    $response = $value_['response'];
                    
                    //1. collect the categories
                    $data[$question][] = $response;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        /**
         * Beds in facility
         */
        public function getBeds($criteria, $value, $survey, $survey_category, $for) {
            
            $query = "CALL get_beds_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                foreach ($this->dataSet as $value_) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    
                    $question = $this->getQuestionName($value_['question_code']);
                    $response = $value_['total_response'];
                    
                    //1. collect the categories
                    $data[$question][] = $response;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        /**
         * 24 Hour Service
         */
        public function getService($criteria, $value, $survey, $survey_category) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = array();
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            $query = "SELECT
    question_code,SUM(lq_response_count) as response
FROM
    log_questions
WHERE
    question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = 'serv')
        AND fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
                 " . $criteria_condition . ")
            GROUP BY question_code
ORDER BY question_code";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                foreach ($this->dataSet as $value_) {
                    $question = $this->getQuestionName($value_['question_code']);
                    $response = $value_['response'];
                    
                    //1. collect the categories
                    $data[$question][] = $response;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        /**
         * Health Facility Management
         */
        public function getHFM($criteria, $value, $survey) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = array();
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            $query = "SELECT
    question_code,sum(if (lq_response ='Yes' , 1 , 0)) as yes_values,
    sum(if (lq_response ='No' , 1 , 0)) as no_values
FROM
    log_questions
WHERE
    question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = 'commi')
        AND fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')
                 " . $criteria_condition . ")
            GROUP BY question_code
ORDER BY question_code";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                foreach ($this->dataSet as $value_) {
                    $question = $this->getQuestionName($value_['question_code']);
                    $yes = $value_['yes_values'];
                    $no = $value_['no_values'];
                    
                    //1. collect the categories
                    $data[$question]['yes'] = $yes;
                    $data[$question]['no'] = $no;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        /**
         * Deliveries
         */
        public function getDeliveries($criteria, $value, $survey, $survey_category) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            $data = array();
            
            $query = "CALL get_question_statistics('" . $criteria . "','" . $value . "','" . $survey . "',,'" . $survey_category . "','" . $for . "');";
            
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                foreach ($this->dataSet as $value_) {
                    $fac_level = $value_['facility_level'];
                    $yes = $value_['yes_values'];
                    
                    //1. collect the categories
                    $data['drilldown'][$fac_level] = $yes;
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        //Section 2
        //-----------------------------------------------------------------------------
        
        
        
        /**
         * Deliveries Conducted
         */
        public function getDeliveriesConducted($criteria, $value, $survey, $survey_category) {
        }
        
        /**
         * Signal Functions
         * Options:
         *      .bemonc
         *      .cemonc
         */
        public function getBemONCQuestion($criteria, $value, $survey, $survey_category) {
            $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            $query = "CALL get_bemonc_question('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                
                foreach ($this->dataSet as $value_) {
                    
                    //print_r($this->dataSet);die;
                    $question = $this->getSignalName($value_['sf_code']);
                    
                    $yes = $value_['yes_values'];
                    $no = $value_['no_values'];
                    
                    //1. collect the categories
                    $data[$question]['yes'] = $yes;
                    $data[$question]['no'] = $no;
                }
            }
            
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            // var_dump($data);die;
            return $data;
        }
        
        public function getBemONCReason($criteria, $value, $survey, $survey_category) {
            $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            $query = "CALL get_bemonc_reason('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    foreach ($this->dataSet as $value) {
                        if (array_key_exists('challenge', $value)) {
                            $data[$value['flevel']][$value['challenge']] = (int)$value['total_response'];
                        }
                    }
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        public function getSignalFunction($criteria, $value, $survey, $survey_category, $function) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = array();
            
            $query = "CALL get_bemonc_question('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
            
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                // echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                foreach ($this->dataSet as $value_) {
                    
                    $question = $this->getSignalName($value_['sf_code']);
                    $yes = $value_['yes_values'];
                    $no = $value_['no_values'];
                    
                    //1. collect the categories
                    $data['conducted'][$question]['yes'] = $yes;
                    $data['conducted'][$question]['no'] = $no;
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //die(var_dump($this->dataSet));
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
            break;
        }
        
        /**
         * [getDiarrhoeaStatistics description]
         * @param  [type] $criteria        [description]
         * @param  [type] $value           [description]
         * @param  [type] $survey          [description]
         * @param  [type] $survey_category [description]
         * @return [type]                  [description]
         */
        public function getDiarrhoeaStatistics($criteria, $value, $survey, $survey_category) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            $data = array();
            
            $query = "CALL get_diarrhoea_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                foreach ($this->dataSet as $value) {
                    $data[$value['month']] = (int)$value['sum(ld_number)'];
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            // var_dump($data);die;
            return $data;
        }
        
        /**
         * [getQuestionStatistics description]
         * @param  [type] $criteria [description]
         * @param  [type] $value    [description]
         * @param  [type] $survey   [description]
         * @param  [type] $for      [description]
         * @return [type]           [description]
         */
        public function getQuestionStatistics($criteria, $value, $survey, $survey_category, $for, $statistics) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            $data = array();
            
            $query = "CALL get_question_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "','" . $statistics . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                foreach ($this->dataSet as $value_) {
                	$question = $this->getQuestionName($value_['question_code']);
                   	// $question = trim($question, 'Does this facility have an updated');
                    // $question = trim($question, '?');
                    
                    // // if ($question == 'Has the facility done baby friendly hospital initiative in the last 6 months') {
                    // //     $question = 'Baby Friendly Hospital Initiative';
                    // // } else if ($question == 'National Guidelines for Quality Obstetric and Prenatal Care') {
                    // //     $question = 'Quality Obstetric and Prenatal Care';
                    // // } else {
                    
                    // //     //$question = trim($question, 'National Guidelines for ');
                    
                    // // }
                    // // if ($question == 'Does this Facility have a designated location for oral rehydration?') {
                    // // }
                    //echo $question;
                  //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    switch ($statistics) {
                        case 'response':
                            $yes = $value_['yes_values'];
                            $no = $value_['no_values'];
                            
                            //1. collect the categories
                            $data[$question]['yes'] = $yes;
                            $data[$question]['no'] = $no;
                            break;

                        case 'total':
                           
                            $data[$question] = (int)$value_['total'];
                            break;
                        case 'reason':
                            $question = $this->getQuestionName($value_['questions']);
                            $data[$question][$value_['reason']] = $value_['total_response'];
                            break;
							
						case 'healthservice':
							$data[$question][$value_['response']]=(int)$value_['total_response'];
							
							break;
                        case 'reason_raw':
                        case 'response_raw':
                        case 'total_raw':
                            $data[] = $value_;
                            break;
                    }
                }
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //var_dump($data);die;
            return $data;
        }
        public function getQuestionStatisticsSingle($criteria, $value, $survey, $survey_category, $for, $statistics) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            $data = array();
            
            $query = "CALL get_question_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "','" . $statistics . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                foreach ($this->dataSet as $value_) {
                    
                    switch ($statistics) {
                        case 'response':
                            $question = $this->getQuestionName($value_['question_code']);
                            foreach ($value_ as $key => $v) {
                                $data[$question][$key] = $v;
                            }
                            break;

                        case 'reason':
                            $question = $this->getQuestionName($value_['questions']);
                            $data[$question][$value_['reason']] = $value_['total_response'];
                            break;
                    }
                    
                    unset($data[$question]['question_code']);
                }
                
                //echo '<pre>';print_r($data);echo '</pre>';die;
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            // var_dump($data);die;
            return $data;
        }
	/*	public function getStorageStatistics($criteria, $value, $survey, $survey_category) {
            
        $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
          /*  $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_storage_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','mnhw');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $value) {
                        //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                        if (array_key_exists('lq_specified_or_follow_up', $value)) {
                            $data[$value['lq_specified_or_follow_up']] = (int)$value['total_response'];
                        }
                    }
                    
                    /**
                     * Fix Data
                     */
                    
                    
              /*  } else {
                    return null;
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }*/
        public function getSupplyLocation($criteria, $value, $survey, $survey_category, $for) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_supplies_location('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                $pharmacyvalue = 0;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $key => $value) {
                        
                        if (array_key_exists('phar_values', $value)) {
                            $pharmacyvalue+= $value['phar_values'];
                        }
                        
                        if (array_key_exists('sto_values', $value)) {
                            $storevalue+= $value['sto_values'];
                        }
                        if (array_key_exists('del_values', $value)) {
                            $deliveryvalue+= $value['del_values'];
                        }
                        if (array_key_exists('ot_values', $value)) {
                            $othervalue+= $value['ot_values'];
                        }
                    }
                    
                   
                    
                    //1. collect the categories
                    $data[$question]['pharmacy'] = $pharmacyvalue;
                    $data[$question]['store'] = $storevalue;
                    $data[$question]['delivery room'] = $deliveryvalue;
                    $data[$question]['other'] = $othervalue;
                } else {
                    return null;
                }
                
                //echo "<pre>";print_r($other);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //echo "<pre>";print_r($data);echo "</pre>";die;
            
            return $data;
        }


        public function getDeliveryReason($criteria, $value, $survey, $survey_category) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_delivery_reasons('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                $pharmacyvalue = 0;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $key => $value) {
                        
                        if (array_key_exists('skill', $value)) {
                            $skillvalue+= $value['skill'];
                        }
                        
                        if (array_key_exists('staff', $value)) {
                            $staffvalue+= $value['staff'];
                        }
                        if (array_key_exists('infrastructure', $value)) {
                            $infrastructurevalue+= $value['infrastructure'];
                        }
                        if (array_key_exists('equipment', $value)) {
                            $equipmentvalue+= $value['equipment'];
                        }
                        if (array_key_exists('commodities', $value)) {
                            $commoditiesvalue+= $value['commodities'];
                        }
                        if (array_key_exists('other', $value)) {
                            $othervalue+= $value['other'];
                        }
                    }
                    
                   
                    
                    //1. collect the categories
                    $data[$question]['Inadequate_skill'] = $skillvalue;
                    $data[$question]['Inadequate_staff'] = $staffvalue;
                    $data[$question]['Inadequate_infrastructure'] = $infrastructurevalue;
                    $data[$question]['Inadequate_equipment'] = $equipmentvalue;
                    $data[$question]['Inadequate_commodities'] = $commoditiesvalue;
                    $data[$question]['other'] = $othervalue;
                } else {
                    return null;
                }
                //echo "<pre>";print_r($infrastructurevalue);echo "</pre>";die;
                //echo "<pre>";print_r($other);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //echo "<pre>";print_r($data);echo "</pre>";die;
            
            return $data;
        }



            public function getCommodityUsage($criteria, $value, $survey, $survey_category, $for, $statistic) {
            $value = urldecode($value);
            $newData = array();
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_commodity_usage('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "','" . $statistic . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    foreach ($this->dataSet as $value) {
                        $data['data'][] = $value;
                    }
                    $commodities = $this->getAllCommodityNames();
                    foreach ($commodities as $commodity) {
                        if ($commodity['commFor'] == $for) {
                            $data['commodities'][] = $commodity['commName'];
                        }
                    }
                    $commodityOptions = $this->getCommodityUsageOptions();
                    foreach ($commodityOptions as $option) {
                         $data['commodity_options'][$option['cooId']] = $option['cooDescription'];
                    }
                   
                }
                
                //echo "<pre>";print_r($commodityOptions);echo "</pre>";die;
                
            }
        
        
        catch(exception $ex) {
            
            //ignore
            //die($ex->getMessage());//exit;
            
            
        }
        
        return $data;
    }
        public function getEquipmentLocation($criteria, $value, $survey, $survey_category, $for) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_equipment_location('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                $pharmacyvalue = 0;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $key => $value) {
                        
                        if (array_key_exists('del_values', $value)) {
                            $pharmacyvalue+= $value['del_values'];
                        }
                        
                        if (array_key_exists('phar_values', $value)) {
                            $storevalue+= $value['phar_values'];
                        }
                        if (array_key_exists('sto_values', $value)) {
                            $deliveryvalue+= $value['sto_values'];
                        }
                        if (array_key_exists('ot_values', $value)) {
                            $othervalue+= $value['ot_values'];
                        }
                    }
                    
                    //1. collect the categories
                    $data[$question]['delivery room'] = $deliveryvalue;
                    $data[$question]['pharmacy'] = $pharmacyvalue;
                    $data[$question]['store'] = $storevalue;
                    $data[$question]['other'] = $othervalue;
                } else {
                    return null;
                }
                
                //echo "<pre>";print_r($other);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //echo "<pre>";print_r($data);echo "</pre>";die;
            
            return $data;
        }
        
        public function getResourcesLocation($criteria, $value, $survey, $survey_category, $for) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_resource_location('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                // Dump the extra resultset.
                $queryData->free_result();
                $pharmacyvalue = 0;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $key => $value) {
                        
                        if (array_key_exists('opd_values', $value)) {
                            $opdvalue+= $value['opd_values'];
                        }
                        
                        if (array_key_exists('mch_values', $value)) {
                            $mchvalue+= $value['mch_values'];
                        }
                        if (array_key_exists('clinic_values', $value)) {
                            $clinicvalue+= $value['clinic_values'];
                        }
                        if (array_key_exists('ward_values', $value)) {
                            $wardvalue+= $value['ward_values'];
                        }
                        if (array_key_exists('other_values', $value)) {
                            $othervalue+= $value['other_values'];
                        }
                    }
                    
                    //1. collect the categories
                    $data[$question]['opd'] = $opdvalue;
                    $data[$question]['mch'] = $mchvalue;
                    $data[$question]['clinic'] = $clinicvalue;
                    $data[$question]['ward'] = $wardvalue;
                    $data[$question]['other'] = $othervalue;
                } else {
                    return null;
                }
                
                //echo "<pre>";print_r($other);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //echo "<pre>";print_r($data);echo "</pre>";die;
            
            return $data;
        }
        
        // public function getCommodityLocation($criteria, $value, $survey, $survey_category, $for) {
        //     $value = urldecode($value);
        
        //         return $data;
        //     }
        
        public function getCommodityLocation($criteria, $value, $survey, $survey_category, $for) {
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = $data_set = $data_series = $analytic_var = $data_categories = array();
            
            //data to hold the final data to relayed to the view,data_set to hold sets of data, analytic_var to hold the analytic variables to be used in the data_series,data_series to hold the title and the json encoded sets of the data_set
            
            $query = "CALL get_commodity_location('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                $pharmacyvalue = 0;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $key => $value) {
                        
                        if (array_key_exists('phar_values', $value)) {
                            $pharmacyvalue+= $value['phar_values'];
                        }
                        
                        if (array_key_exists('sto_values', $value)) {
                            $storevalue+= $value['sto_values'];
                        }
                        if (array_key_exists('del_values', $value)) {
                            $deliveryvalue+= $value['del_values'];
                        }
                        if (array_key_exists('ot_values', $value)) {
                            $othervalue+= $value['ot_values'];
                        }
                    }
                    
                    //1. collect the categories
                    $data[$question]['pharmacy'] = $pharmacyvalue;
                    $data[$question]['store'] = $storevalue;
                    $data[$question]['delivery room'] = $deliveryvalue;
                    $data[$question]['other'] = $othervalue;
                } else {
                    return null;
                }
                
                //echo "<pre>";print_r($other);echo "</pre>";die;
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            //echo "<pre>";print_r($data);echo "</pre>";die;
            
            return $data;
        }
        
        public function getLocationStatistics($criteria, $value, $survey, $survey_category, $for) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            $data = array();
            
            $query = "CALL get_location_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                foreach ($this->dataSet as $value_) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    $question = $this->getQuestionName($value_['question_code']);
                    
                    $mch = $value_['mch_values'];
                    $ward = $value_['ward_values'];
                    $opd = $value_['opd_values'];
                    $uc = $value_['uc_values'];
                    $other = $value_['other_values'];
                    
                    //1. collect the categories
                    $data[$question]['mch'] = $mch;
                    $data[$question]['ward'] = $ward;
                    $data[$question]['opd'] = $opd;
                    $data[$question]['u5 clinic'] = $uc;
                    
                    $data[$question]['other'] = $other;
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //die(var_dump($this->dataSet));
                
                $mch = $value_['mch_values'];
                $ward = $value_['ward_values'];
                $opd = $value_['opd_values'];
                $uc = $value_['uc_values'];
                $other = $value_['other_values'];
                
                //1. collect the categories
                $data[$question]['mch'] = $mch;
                $data[$question]['ward'] = $ward;
                $data[$question]['opd'] = $opd;
                $data[$question]['u5 clinic'] = $uc;
                
                $data[$question]['other'] = $other;
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            // var_dump($data);die;
            
            return $data;
        }
        
        public function getWasteStatistics($criteria, $value, $survey, $survey_category, $for) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            $data = array();
            
            $query = "CALL get_waste_statistics('" . $criteria . "','" . $value . "','" . $survey . "','" . $survey_category . "','" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                foreach ($this->dataSet as $value_) {
                    
                    //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                    $question = $this->getQuestionName($value_['question_code']);
                    
                    $waste = $value_['waste_values'];
                    $placenta = $value_['placenta_values'];
                    $inci = $value_['inci_values'];
                    $burning = $value_['burning_values'];
                    $other = $value_['other_values'];
                    
                    //1. collect the categories
                    $data[$question]['waste_pit'] = $waste;
                    $data[$question]['placenta_pit'] = $placenta;
                    $data[$question]['incinerator'] = $inci;
                    $data[$question]['burning'] = $burning;
                    $data[$question]['other'] = $other;
                }
                
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            // var_dump($data);die;
            
            return $data;
        }
        
        /**
         * Community Strategy
         */
        
        public function getCommunityStatstics($criteria, $value, $survey, $survey_category, $for) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            $data = array();
            
            $query = "CALL get_community_statistics('" . $criteria . "', '" . $value . "', '" . $survey . "', '" . $survey_category . "', '" . $for . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                //echo "<pre>";print_r($this->dataSet);echo "</pre>";die;
                //echo($this->db->last_query());die;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $value) {
                        if (array_key_exists('question_code', $value)) {
                            $data[$value['question_name']][$value['question_code']] = (int)$value['total_response'];
                        }
                    }
                    
                    /**
                     * Fix Data
                     */
                } else {
                    return null;
                }
                
                
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                //echo "<pre>";print_r($data);echo "</pre>";die;
                
            }
            
            return $data;
        }
        
        //Summary Excel
        //-----------------------------------------------------------------------------
        
        
        
        /**
         *
         */
        public function commodities_supplies_summary($criteria, $value, $survey, $survey_category) {
            
            /*using CI Database Active Record*/
            $value = urldecode($value);
            
            /*using CI Database Active Record*/
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            $query = "SELECT
    f.fac_name,f.fac_district,SUM(ca.ac_quantity) AS total_quantity,
    ca.comm_code as commodities,commodities.comm_unit AS unit
FROM
    available_commodities as ca
        INNER JOIN
    facilities as f ON ca.fac_mfl = f.fac_mfl,
    commodities
WHERE
commodities.comm_code=ca.comm_code AND
    ca.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')" . $criteria_condition . ")
        AND ca.comm_code IN (SELECT
            comm_code
        FROM
            commodities
        WHERE
            comm_for = '" . $survey . "')
        AND ca.ac_quantity != - 1
GROUP BY f.fac_name,ca.comm_code
ORDER BY f.fac_district,f.fac_name,ca.comm_code;";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                //echo $this->db->last_query();
                $data['commodity_categories'][] = 'Facility Name';
                $data['commodity_categories'][] = 'District/Sub County';
                foreach ($this->dataSet as $value_) {
                    
                    //$data['commodities_categories'][0] = 'Facility Name';
                    $supply = $this->getCommodityNameById($value_['commodities'], $survey) . ' ' . $value_['unit'];
                    $facility = $value_['fac_name'];
                    $district = $value_['fac_district'];
                    
                    //$response = $value_['supplies'];
                    //1. collect the categories
                    $data['commodities'][$facility]['facility'] = $facility;
                    $data['commodities'][$facility]['district'] = $district;
                    $data['commodities'][$facility][$supply] = $value_['total_quantity'];
                    $data['commodity_categories'][] = $supply;
                }
                $data['commodity_categories'] = array_unique($data['commodity_categories']);
                
                //echo '<pre>';print_r($data['commodity_categories']);echo '</pre>';
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            $query = "SELECT
    f.fac_name,f.fac_district,SUM(sa.as_quantity) AS total_quantity,
    sa.supply_code as Supplies
FROM
    available_supplies as sa
        INNER JOIN
    facilities as f ON sa.fac_mfl = f.fac_mfl,
    Supplies
WHERE
Supplies.supply_code=sa.supply_code AND
    sa.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')" . $criteria_condition . ")
        AND sa.supply_code IN (SELECT
            supply_code
        FROM
            Supplies
        WHERE
            supply_for = '" . $survey . "')
        AND sa.as_quantity != - 1
GROUP BY f.fac_name,sa.supply_code
ORDER BY f.fac_name,sa.supply_code;";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                foreach ($this->dataSet as $value_) {
                    $supply = $this->getSupplyName($value_['Supplies'], $survey);
                    $facility = $value_['fac_name'];
                    $district = $value_['fac_district'];
                    
                    //1. collect the categories
                    $data['supplies'][$facility][$supply] = $value_['total_quantity'];
                    $data['supply_categories'][] = $supply;
                }
                $data['supply_categories'] = array_unique($data['supply_categories']);
                
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            $query = "SELECT
    f.fac_name,
    f.fac_district,
    SUM(sa.ae_fully_functional) AS fully,
    SUM(sa.ae_non_functional) AS non,
    sa.eq_code as Equipments
FROM
    available_equipments as sa
        INNER JOIN
    facilities as f ON sa.fac_mfl = f.fac_mfl
WHERE
    sa.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')" . $criteria_condition . ")
        AND sa.eq_code IN (SELECT
            eq_code
        FROM
            equipments
        WHERE
            eq_for = '" . $survey . "')
        AND sa.ae_fully_functional != - 1 AND sa.ae_non_functional!=-1
GROUP BY f.fac_name , sa.eq_code
ORDER BY f.fac_district,f.fac_name , sa.eq_code;";
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                //echo $this->db->last_query();
                
                foreach ($this->dataSet as $value_) {
                    
                    //$data['commodities_categories'][0] = 'Facility Name';
                    $supply = $this->getCHEquipmentName($value_['Equipments'], $survey);
                    $facility = $value_['fac_name'];
                    $district = $value_['fac_district'];
                    
                    //$response = $value_['supplies'];
                    //1. collect the categories
                    $data['equipments'][$facility][$supply . ' (Fully Functional)'] = $value_['fully'];
                    $data['equipments'][$facility][$supply . ' (Non-Functional)'] = $value_['non'];
                    $data['equipment_categories'][] = $supply . ' (Fully Functional)';
                    $data['equipment_categories'][] = $supply . ' (Non-Functional)';
                }
                $data['equipment_categories'] = array_unique($data['equipment_categories']);
                
                //echo '<pre>';print_r($data['equipment_categories']);echo '</pre>';die;
                //die(var_dump($this->dataSet));
                
                
            }
            catch(exception $ex) {
                
                //ignore
                //die($ex->getMessage());//exit;
                
                
            }
            
            return $data;
        }
        
        public function getFacilityListForNoMNH($criteria, $value, $survey, $survey_category, $question) {
            urldecode($value);
            
            switch ($criteria) {
                case 'national':
                    $criteria_condition = ' ';
                    $value = ' ';
                    break;

                case 'county':
                    $criteria_condition = 'WHERE fac_county=?';
                    break;

                case 'district':
                    $criteria_condition = 'WHERE fac_district=?';
                    break;

                case 'facility':
                    $criteria_condition = 'WHERE fac_mfl=?';
                    break;

                case 'none':
                    $criteria_condition = '';
                    break;
            }
            
            $query = "SELECT
    q.question_name, lq.fac_mfl, f.fac_name
FROM
    log_questions lq,
    questions q,
    facilities f
WHERE
    lq.lq_response = 'No'
        AND lq.question_code = q.question_code
        AND lq.fac_mfl = f.fac_mfl
        AND lq.question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = '$question')
        AND lq.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = '" . $survey . "')" . $criteria_condition . ") ";
            
            try {
                $this->dataSet = $this->db->query($query, array($value));
                $this->dataSet = $this->dataSet->result_array();
                
                if ($this->dataSet !== NULL) {
                    
                    $size = count($this->dataSet);
                    $i = 0;
                    $facilities = array();
                    foreach ($this->dataSet as $value) {
                        $facilities[$value['question_name']][] = array($value['fac_mfl'], $value['fac_name']);
                    }
                    return $facilities;
                    
                    //var_dump($this->dataSet);die;
                    
                    
                } else {
                    return $this->dataSet = null;
                }
            }
            catch(exception $ex) {
            }
        }
        
        public function getFacilityProgress($survey, $survey_category) {
            
            $query = "CALL get_facility_activity('" . $survey . "','" . $survey_category . "');";
            try {
                $queryData = $this->db->query($query, array($value));
                $this->dataSet = $queryData->result_array();
                $queryData->next_result();
                
                // Dump the extra resultset.
                $queryData->free_result();
                
                //echo $this->db->last_query();die;
                if ($this->dataSet !== NULL) {
                    foreach ($this->dataSet as $value) {
                        
                        //echo $value['ast_last_activity'];
                        $day = new DateTime($value['ast_last_activity']);
                        $day = $day->format('M-Y');
                        $data[$day][] = $value;
                    }
                    
                    //die;
                    
                    
                }
                return $data;
            }
            catch(exception $e) {
            }
        }
    }
    
    
