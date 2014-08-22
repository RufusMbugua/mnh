<?php
class C_Analytics extends MY_Controller
{
    var $data;
    var $county;
    public function __construct() {
        parent::__construct();
        $this->data = '';
        $this->load->model('m_analytics');
        $this->load->library('PHPExcel');

        //$this -> county = $this -> session -> userdata('county_analytics');


    }

    /**
     * [setActive description]
     * @param [type] $county
     * @param [type] $survey
     */

    //function for Pie Chart
    public function bar() {

        //load database
        $this->load->helper("url");

        //load database
        $this->load->database();
        $res = array();

        //fetch data from db
        $query = $this->db->query("SELECT
    question_code,
    sum(if (lq_response ='Yes' , 1 , 0)) as yes_values,
    sum(if (lq_response ='No' , 1 , 0)) as no_values
FROM
    log_questions
WHERE
    question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = 'prep')
        AND fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = 'mnh')
                )
GROUP BY question_code
ORDER BY question_code;");
        foreach ($query->result() as $row) {
            $res = array_merge($res, array($row->no_values, $row->yes_values));
        }
        $data['dres'] = $res;
        $this->load->view('pie', $data);
    }

    //end of the function

    //function for the Yes Response
    public function Yes_Response() {

        //load the base url
        $this->load->helper("url");
        $this->load->database();
        $level_total = array();
        $level_type = array();
        $sql = $this->db->query("SELECT
    question_code,
    sum(if (lq_response ='Yes' , 1 , 0)) as yes_values,
        (fac_level) as facility_level
        FROM
            log_questions
        JOIN
            facilities
        ON
            facilities.fac_mfl = log_questions.fac_mfl
        WHERE
    question_code IN (SELECT
            question_code
        FROM
            questions
        WHERE
            question_for = 'prep')
        AND facilities.fac_mfl IN (SELECT
            fac_mfl
        FROM
            facilities f
                JOIN
            survey_status ss ON ss.fac_id = f.fac_mfl
                JOIN
            survey_types st ON (st.st_id = ss.st_id
                AND st.st_name = 'mnh')
                )
GROUP BY fac_level
ORDER BY fac_level;");

        //$sql = $sql->result_array();
        //echo '<pre>';
        //print_r($sql);
        //echo '</pre>';die;

        foreach ($sql->result() as $row) {
            $level_total = array_merge($level_total, array((int)$row->yes_values));
            $level_type = array_merge($level_type, array('level ' . $row->facility_level));
        }

        $data['level_total'] = json_encode($level_total);
        $data['level_type'] = json_encode($level_type);
        $this->load->view('Yes_Response', $data);
    }

    //end of the function

    public function setActive($county, $survey, $survey_category) {

        $county = urldecode($county);

        //$this -> session -> unset_userdata('county_analytics');

        $this->session->set_userdata(array('county_analytics' => $county, 'survey_category' => $survey_category));

        //$this -> session -> unset_userdata('survey');
        $this->session->set_userdata('survey', $survey);
        $this->getReportingCounties();
        $this->county = $this->session->userdata('county_analytics');

        redirect('analytics');
    }

    public function getFacilityProgress($survey, $survey_category) {
        $results = $this->m_analytics->getFacilityProgress($survey, $survey_category);

        foreach ($results as $day => $value) {
            $data[] = (int)sizeof($value);
            $category[] = $day;
            $resultArray[] = array('name' => 'Daily Entries', 'data' => $data);

            //echo '<pre>';print_r($resultArray);echo '</pre>';die;
            $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'line');
        }
    }

    /**
     * [active_results description]
     * @param  [type] $survey
     * @return [type]
     */
    public function active_results($survey) {

        //$this -> session -> unset_userdata('survey');
        $this->session->set_userdata('survey', $survey);

        $this->getReportingCounties();
        $this->data['title'] = 'MoH::Analytics';
        $this->data['active_link']['as'] = '<li class="start active">';
        $this->data['span_selected']['as'] = '<span class="selected"></span>';
        $this->data['active_link']['fi'] = '<li class="start has-sub">';
        $this->data['span_selected']['fi'] = '';
        $this->data['active_link']['s2'] = '<li class="has-sub start">';
        $this->data['span_selected']['s2'] = '';
        $this->data['analytics_main_title'] = 'Analytics Summary';
        $this->data['analytics_mini_title'] = 'Facts and Figures';
        $this->data['data_pie'] = null;
        $this->data['data_column'] = null;
        $this->data['data_column_combined'] = null;
        $this->data['analytics_content_to_load'] = 'analytics/content_visual_charts_commodity_availability';

        //$this -> data['analytics_content_to_load'] = 'analytics/content_dashboard';
        //$this -> ch_survey_response_rate();
        $this->load->view('pages/v_analytics', $this->data);
    }

    public function summary() {
        $this->data['title'] = 'MoH::Analytics';
        $this->data['active_link']['as'] = '<li class="start active">';
        $this->data['span_selected']['as'] = '<span class="selected"></span>';
        $this->data['active_link']['fi'] = '<li class="start has-sub">';
        $this->data['span_selected']['fi'] = '';
        $this->data['active_link']['s2'] = '<li class="has-sub start">';
        $this->data['span_selected']['s2'] = '';
        $this->data['analytics_main_title'] = 'Analytics Summary';
        $this->data['analytics_mini_title'] = 'Facts and Figures';
        $this->data['data_pie'] = null;
        $this->data['data_column'] = null;
        $this->data['data_column_combined'] = null;
        $this->data['analytics_content_to_load'] = 'analytics/content_visual_charts';

        //$this -> data['analytics_content_to_load'] = 'analytics/content_dashboard';
        //$this -> ch_survey_response_rate();
        $this->load->view('pages/v_analytics', $this->data);
    }

    public function facility_reporting() {
        $this->data['title'] = 'MoH::Facility Reporting Summary';
        $this->data['summary'] = $this->facility_reporting_summary();
        $this->load->view('pages/v_temporary_report', $this->data);
    }

    public function test_query() {
        $results = $this->m_analytics->getORTCornerEquipmement('county', 'Nairobi', 'complete', 'ch');

        //var_dump($results[1]);
        var_dump($results);
    }

    public function getReportingCountyList($survey) {

        /*obtained from the session data*/

        $options = '';
        $this->data_found = $this->m_analytics->getReportingCounties($survey);
        foreach ($this->data_found as $value) {
            $options.= '<option value="' . $value['county'] . '">' . $value['county'] . '</option>' . '<br />';
        }

        //var_dump($this -> session -> userdata('allCounties')); exit;
        echo $options;
    }
    public function getTotalCounties($survey) {
        $data = $this->m_analytics->getReportingCounties($survey);

        //echo '<pre>';print_r($data);echo '</pre>';
        $counties = (int)sizeof($data);
        echo $counties;
    }

    public function getAllReportedCounties($survey, $survey_category) {
        $reportingCounties = $this->m_analytics->getAllReportingRatio($survey, $survey_category);

        //m var_dump($reportingCounties);
        $counter = 0;
        $allProgress = '';
        foreach ($reportingCounties as $key => $county) {

            //echo $key;
            $allProgress.= $this->getReportedCounty($county, $key);
            $counter++;
        }
        echo $allProgress;
    }

    public function getOneReportingCounty($county, $survey_category) {
        $county = urldecode($county);
        $survey = $this->session->userdata('survey');

        //$nowCounty = $this->uri->segment(3);
        //echo $nowCounty;
        $reportingCounty = $this->m_analytics->getReportingRatio($county, $survey, $survey_category);
        $oneProgress = $this->getReportedCounty($reportingCounty, $county);
        echo ($oneProgress);
    }
    public function get_question_raw_data($survey, $survey_category, $question_for) {
        $result = $this->m_analytics->get_question_raw_data($survey, $survey_category, $question_for);
        $data['title'] = array('Facility MFL', 'Facility Name', 'Facility Ownership', 'Facility Type', 'Facility Level', 'Facility District', 'Facility County', 'Response');
        $data['data'] = $result;
        $this->loadExcel($data, 'Question Data' . ' ' . strtoupper($survey) . ' : ' . strtoupper($survey_category));

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // die;


    }
    public function get_signal_function_raw_data($survey, $survey_category) {
        $result = $this->m_analytics->get_signal_function_raw_data($survey, $survey_category);
        $data['title'] = array('Facility MFL', 'Facility Name', 'Facility Ownership', 'Facility Type', 'Facility Level', 'Facility District', 'Facility County', 'Signal Function', 'BEMONC Conducted', 'Challenge');
        $data['data'] = $result;
        $this->loadExcel($data, 'Signal Function Data' . ' ' . strtoupper($survey) . ' : ' . strtoupper($survey_category));

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // die;


    }

    /**
     * [getReportedCounty description]
     * @param  [type] $county [description]
     * @param  [type] $key    [description]
     * @return [type]         [description]
     */
    public function getReportedCounty($county, $key) {
        $progress = "";

        //var_dump($reportingCounties);
        //die ;

        $countyName = $key;
        $percentage = (int)$county[0]['percentage'];
        $reported = (int)$county[0]['reported'];
        $actual = (int)$county[0]['actual'];

        /**
         * Check status
         *
         * Different Colors for Different LEVELS
         */

        switch ($percentage) {
            case ($percentage == 0):
            $status = 'transparent';
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

            case ($percentage < 100):
            $status = '#7ada33';
            break;

            /**case ($percentage == 100):
                $status = '#13b00b';
                break;*/
            default:
            $status = 'transparent';
            break;
        }
        $progress = '<div class="progressRow"><label>' . $countyName . '</label><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $percentage . '%;background:' . $status . '">' . $percentage . '%</div><div style="float:right">' . $reported . ' / ' . $actual . '</div></div></div>';

        //$progress = ' <div class="progressRow"><label>' . $countyName . '</label><div class="progress">  <div class="bar" style="width: ' . $percentage . '%;background:' . $status . '">' . $percentage . '%</div>      <div style="float:right">' . $reported . ' / ' . $actual . '</div> </div></div>';
        return $progress;

        //echo($progress);


    }

    public function test_query_2() {
        $results = $this->m_analytics->getSpecificDistrictNames('Nairobi');
        var_dump($results);
    }

    private function ch_survey_response_rate() {
        $this->data['response_count'] = $this->m_analytics->get_response_count('ch');
    }

    /**
     * [facility_reporting_summary description]
     * @return [type]
     */
    public function facility_reporting_summary() {
        $results = $this->m_analytics->get_facility_reporting_summary('ch');

        //echo '<pre>';print_r($results);echo '</pre>';
        if ($results) {
            $dyn_table = "<table width='100%' id='facility_report' class='dataTables'>
            <tr>
            <tr>
            <tr><th>MFL Code</th></tr>
            <tr><th>Name</th></tr>
            <tr><th>District/Sub County</th></tr>
            <tr><th>County</th></tr>
            <tr><th>Contact Person</th></tr>
            <tr><th>Contact Person Email</th></tr>
            <tr><th>Date/Time Taken</th></tr>
            </tr></tr>
            <tbody>";
            foreach ($results as $result) {

                $dbdate = new DateTime($result['updatedAt']);

                $dbdate = $dbdate->format("d M, Y h:i:s A");

                $dyn_table.= "<tr><td>" . $result['fac_mfl'] . "</td><td>" . $result['fac_name'] . "</td><td>" . $result['fac_district'] . "</td><td>" . $result['fac_county'] . "</td><td>" . $result['facilityInchargeContactPerson'] . "</td><td>" . $result['facilityInchargeEmail'] . "</td><td>" . $dbdate . "</td></tr>";
            }
            $dyn_table.= "</tbody></table>";
            echo $dyn_table;

            //return $dyn_table;


        }
    }

    /**
     * [getCommunityStrategy description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCommunityStrategy($criteria, $value, $survey, $survey_category) {
        $value = urldecode($value);
        $results = array();
        $results = $this->m_analytics->getCommunityStrategy($criteria, $value, $survey, $survey_category);

        //echo '<pre>';print_r($results);echo '</pre>';
        $resultArray = array();
        $datas = array();

        //$value=urldecode($value);$results = $this -> m_analytics -> getCommunityStrategy('facility', '17052', 'complete', 'ch');

        if ($results != "") {
            foreach ($results as $result) {
                $category[] = $result[0];
                $resultData[] = (int)$result[1];
            }
        } else {
            $category = "";
            $resultData = 0;
        }

        $resultArray[] = array('name' => 'Quantity', 'data' => $resultData);

        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'bar');
    }

    /*
     * Guidelines Availability
    */
    public function getGuidelinesAvailability($criteria, $value, $survey, $survey_category) {
        $value = urldecode($value);
        $results = $this->m_analytics->getGuidelinesAvailability($criteria, $value, $survey, $survey_category);

        //echo '<pre>';print_r($results);echo '</pre>';
        //var_dump($results);die;

        $categories = $results['categories'];
        $yes = $results['yes_values'];
        $no = $results['no_values'];
        $yCount = 0;
        $nCount = 0;
        $yesSize = sizeof($yes);
        $noSize = sizeof($no);

        //var_dump($yes);
        if ($yes == null) {
            $yesF = array(0, 0, 0, 0);
        } else {
            foreach ($categories as $category) {
                if ($yCount < $yesSize) {
                    if (array_key_exists($category, $yes[$yCount])) {
                        $yesF[] = $yes[$yCount][$category];
                        $yCount++;
                    } else {
                        $yesF[] = 0;
                    }
                } else {
                    $yesF[] = 0;
                }
            }
        }
        if ($no == null) {
            $noF = array(0, 0, 0, 0);
        } else {
            foreach ($categories as $category) {
                if ($nCount < $noSize) {
                    if (array_key_exists($category, $no[$nCount])) {
                        $noF[] = $no[$nCount][$category];
                        $nCount++;
                    } else {
                        $noF[] = 0;
                    }
                } else {
                    $noF[] = 0;
                }
            }
        }

        $resultArray = array(array('name' => 'Yes', 'data' => $yesF), array('name' => 'No', 'data' => $noF));

        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar');
    }

    /*
     * Get Trained Stuff
    */
    public function getTrainedStaff($criteria, $value, $survey, $survey_category, $for) {

        //$yes = $no = $resultsArray = array();
        $value = urldecode($value);

        $results = $this->m_analytics->getTrainedStaff($criteria, $value, $survey, $survey_category, $for);

        //echo '<pre>';print_r($results);echo '</pre>'; exit;
        $category = array();
        foreach ($results as $guide => $result) {
            $category[] = $guide;
            foreach ($result as $name => $data) {
                foreach ($data as $stack => $value) {
                    $gData[$name][$stack][] = (int)$value;
                }
            }
        }

        //echo '<pre>';print_r($category);echo '</pre>'; exit;
        $colors = array('#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a');

        //echo '<pre>';print_r($gData);echo '</pre>'; exit;
        $colorCount = 0;
        foreach ($gData as $name => $data) {
            $color = $colors[$colorCount];
            $count = 0;
            foreach ($data as $stack => $actual) {
                if ($count == 0) {
                    $resultArray[] = array('name' => $name, 'data' => $actual, 'stack' => $stack, 'color' => $color);
                } else {
                    $resultArray[] = array('name' => $name, 'data' => $actual, 'stack' => $stack, 'linkedTo' => ':previous', 'color' => $color);
                }
                $count++;
            }
            $colorCount++;
        }

        //echo '<pre>';print_r($resultArray);echo '</pre>'; exit;

        $this->populateGraph($resultArray, '', $category, $criteria, 'normal', 90, 'bar');

        //echo '<pre>';print_r($resultArray);echo '</pre>'; exit;
        //  $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'column');


    }
    public function getStaffRetention($criteria, $value, $survey, $survey_category, $for) {
        $in_facility = $on_duty = $resultsArray = array();
        $results = $this->m_analytics->getStaffRetention($criteria, $value, $survey, $survey_category, $for);

        // echo '<pre>';print_r($results);echo '</pre>'; exit;
        $category = array();
        foreach ($results as $guide => $result) {
            $category[] = $guide;
            foreach ($result as $name => $data) {
                $gData[$name]['trained'][] = (int)$data['trained'];
                $gData[$name]['working'][] = (int)$data['working'];
            }
        }

        $colors = array('#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a');

        //echo '<pre>';print_r($gData);echo '</pre>'; exit;
        $colorCount = 0;
        foreach ($gData as $name => $data) {
            $color = $colors[$colorCount];
            $count = 0;
            foreach ($data as $stack => $actual) {
                if ($count == 0) {
                    $resultArray[] = array('name' => $name, 'data' => $actual, 'stack' => ucwords(str_replace('_', ' ', $stack)), 'color' => $color);
                } else {
                    $resultArray[] = array('name' => $name, 'data' => $actual, 'stack' => ucwords(str_replace('_', ' ', $stack)), 'linkedTo' => ':previous', 'color' => $color);
                }
                $count++;
            }
            $colorCount++;
        }

        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, 'normal', 90, 'bar');
    }

    public function getStaffAvailability($criteria, $value, $survey, $survey_category, $for) {
        $in_facility = $on_duty = $resultsArray = array();
        $value = urldecode($value);
        $results = $this->m_analytics->getStaffAvailability($criteria, $value, $survey, $survey_category, $for);

        $category = array();
        foreach ($results as $guide => $result) {
            $category[] = $guide;
            foreach ($result as $name => $data) {
                $gData[$name]['total_in_facility'][] = (int)$data['total_facility'];
                $gData[$name]['total_on_duty'][] = (int)$data['total_duty'];
            }
        }

        $colors = array('#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a');

        //echo '<pre>';print_r($gData);echo '</pre>'; exit;
        $colorCount = 0;
        foreach ($gData as $name => $data) {
            $color = $colors[$colorCount];
            $count = 0;
            foreach ($data as $stack => $actual) {
                if ($count == 0) {
                    $resultArray[] = array('name' => $name, 'data' => $actual, 'stack' => ucwords(str_replace('_', ' ', $stack)), 'color' => $color);
                } else {
                    $resultArray[] = array('name' => $name, 'data' => $actual, 'stack' => ucwords(str_replace('_', ' ', $stack)), 'linkedTo' => ':previous', 'color' => $color);
                }
                $count++;
            }
            $colorCount++;
        }

        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, 'normal', 90, 'bar');
    }

    //get treatment symptoms
    public function getTreatmentSymptoms($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getTreatmentSymptoms($criteria, $value, $survey, $survey_category);

        //echo "<pre>";print_r($results);echo "</pre>";die;


    }
    public function getTreatmentStatistics($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getTreatmentStatistics($criteria, $value, $survey, $survey_category);

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $category = array('Pneumonia', 'Malaria / Fever', 'Diarrhoea');
        $count = 0;
        foreach ($results as $stack => $result) {
            foreach ($result as $name => $data) {
                switch ($count) {
                    case 0:
                    $resultArray[] = array('name' => $name, 'data' => array(NULL, NULL, $data), 'stack' => $stack);
                    break;

                    case 1:
                    $resultArray[] = array('name' => $name, 'data' => array(NULL, $data, NULL), 'stack' => $stack);
                    break;

                    case 2:
                    $resultArray[] = array('name' => $name, 'data' => array($data, NULL, NULL), 'stack' => $stack);
                    break;
                }
            }
            $count++;
        }

        /*foreach ($results as $stack => $result) {
            foreach ($result as $name => $data) {

                $resultArray[] = array('name' => $name, 'data' => array($data), 'stack' => $stack, 'category' => $stack);
            }
        }*/

        //echo "<pre>";
        //print_r($resultArray);
        //echo "</pre>";
        //die;
        $this->populateGraph($resultArray, '', $category, $criteria, 'normal', 120, 'bar');
    }
    public function getMNHCommodityAvailabilityFrequency($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'availability');
    }

    public function getMNHCommodityAvailabilityUnavailability($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'unavailability');
    }
    

    public function getMNHCommodityAvailabilityQuantities($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'quantity');
    }
	public function getMNHCommoditySupplier($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'supplier');
    }
    public function getCHCommodityAvailabilityFrequency($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'ch', 'availability');
    }

    public function getCHCommodityAvailabilityUnavailability($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'ch', 'unavailability');
    }

    
	public function getCHCommoditySuppliers($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'ch', 'supplier');
    }
    public function getCHCommodityAvailabilityQuantities($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'ch', 'quantity');
    }
    public function getbundlingFrequency($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'bun', 'availability');
    }

    public function getbundlingUnavailability($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'bun', 'unavailability');
    }

    public function getbundlingLocation($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'bun', 'location');
    }

    public function getbundlingQuantities($criteria, $value, $survey, $survey_category) {
        $this->getCommodityStatistics($criteria, $value, $survey, $survey_category, 'bun', 'quantity');
    }

    public function getCommodityAvailability($criteria, $value, $survey, $survey_category, $for, $statistic) {
        $value = urldecode($value);
        $results = $this->m_analytics->getCommodityAvailability($criteria, $value, $survey, $survey_category, $for, $statistic);

        foreach ($results as $key => $result) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'Sometimes Available') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);

            //echo '<pre>';print_r($results);echo '</pre>';die;


        }
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
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
        $value = urldecode($value);
        $results = $this->m_analytics->getSuppliesStatistics($criteria, $value, $survey, $survey_category, $for, $statistic);

        //echo '<pre>';print_r($results);echo '</pre>';die;
        foreach ($results as $key => $result) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'Sometimes Available') {

                    //if ($name != 'Sometimes Available') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);
        }

        //echo '<pre>';print_r($resultArray);echo '</pre>';die;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 130, 'column',(int)sizeof($category));
    }

    /**
     * [getMNHSuppliesAvailability description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getMNHSuppliesAvailability($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'availability');
    }

    /**
     * [getMNHSuppliesLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getMNHSuppliesLocation($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesLocation($criteria, $value, $survey, $survey_category, 'mnh');
    }
	public function getMNHSuppliers($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'supplier');
    }
    /**
     * [getCHSuppliesAvailability description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCHSuppliesAvailability($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesStatistics($criteria, $value, $survey, $survey_category, 'ch', 'availability');
    }

    /**
     * [getCHSuppliesLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */

     
    public function getCHSuppliesLocation($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesLocation($criteria, $value, $survey, $survey_category, 'ch');
    }
	public function getCHSuppliers($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesStatistics($criteria, $value, $survey, $survey_category, 'ch', 'supplier');
    }
    /**
     * [getRunningWaterAvailability description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getRunningWaterAvailability($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesStatistics($criteria, $value, $survey, $survey_category, 'mh', 'availability');
    }

    /**
     * [getRunningWaterLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getRunningWaterLocation($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesStatistics($criteria, $value, $survey, $survey_category, 'mh', 'location');
    }

    /*public function getORTReason($criteria, $value, $survey, $survey_category) {
        $this->getReasonStatistics($criteria, $value, $survey, $survey_category, 'ortf');
    }*/

    /**
     * [getReasonStatistics description]
     * @param  [type] $criteria  [description]
     * @param  [type] $value     [description]
     * @param  [type] $survey    [description]
     * @param  [type] $for       [description]
     * @param  [type] $statistic [description]
     * @return [type]            [description]
     */
    public function getNonFunctional($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ort','response');
        $count=0;
        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        
        foreach ($results as $key => $value) {
            if ($count == 1):
            $q[] = $key;
            $yes[] = (int)$value['yes'];
            $no[] = (int)$value['no'];
            endif;
            $count++;
        }
        
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
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
        $results = $this->m_analytics->getEquipmentStatistics($criteria, $value, $survey, $survey_category, $for, $statistic);

        //echo "<pre>"; print_r($results);echo "</pre>";die;
        foreach ($results as $key => $result) {

            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'Sometimes Available') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);


            //echo "<pre>"; print_r($resultArray);echo "</pre>";die;



        }
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 130, 'column',(int)sizeof($category));
    }

    public function getCommodityStatistics($criteria, $value, $survey, $survey_category, $for, $statistic) {
        $results = $this->m_analytics->getCommodityStatistics($criteria, $value, $survey, $survey_category, $for, $statistic);
			foreach ($results as $key => $result) {
			$key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'Sometimes Available' && $name != 'N/A') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);
        }
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 130, 'column',(int)sizeof($category));
    }

    public function getCommodityUsage($criteria, $value, $survey, $survey_category, $for, $statistic) {
        $results = $this->m_analytics->getCommodityUsage($criteria, $value, $survey, $survey_category, $for, $statistic);
        $commodities = $results['commodities'];

        // echo "<pre>";
        // print_r($results);
        // echo "</pre>";
        // die;

        switch ($statistic) {
            case 'consumption':
            foreach ($results['data'] as $result) {
                $category[] = $result['comm_name'];
                $gData[] = (int)$result['consumption'];
            }
            $resultArray[] = array('name' => 'Commodity Usage', 'data' => $gData);

            $this->populateGraph($resultArray, '', $category, $criteria, '', 130, 'column', (int)sizeof($category));
            break;

            case 'unavailability':
            foreach ($results['data'] as $drug => $result) {

                //$category[] = $drug;
                $gData[$result['unavailable_times']][$result['commodity_name']] = (int)$result['frequency'];
            }

            $colors = array('#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a');

            //echo '<pre>';print_r($gData);echo '</pre>'; exit;
            //$color = $colors[$colorCount];
            //$colorCount = 0;
            foreach ($gData as $stack => $data) {
                foreach ($commodities as $drug) {
                    if (array_key_exists($drug, $data)) {
                        $fData[$stack][] = $data[$drug];
                    } else {
                        $fData[$stack][] = 0;
                    }
                }

                //$colorCount++;


            }

            foreach ($fData as $key => $value) {
                $resultArray[] = array('name' => $key, 'data' => $value);
            }

            $this->populateGraph($resultArray, '', $commodities, $criteria, 'percent', 130, 'column', (int)sizeof($commodities));
            break;

            case 'reason':
            foreach ($results['data'] as $result) {
                $chosenOptions = explode(',', $result['lcso_option_on_outage']);
                foreach ($chosenOptions as $chosen) {
                    $gData[$results['commodity_options'][$chosen]][] = $result['comm_name'];
                }
            }
            foreach($gData as $key=>$data){
                $fData[]=array('name'=>$key,'y'=>(int)sizeof($data));
            }
            $resultArray[]=array('name'=>'Reasons','data'=>$fData);
            $this->populateGraph($resultArray, '', $category, $criteria, '', 130, 'pie');
            break;
        }

        //echo sizeof($category);
        //echo "<pre>"; print_r($resultArray);echo "</pre>";die;

        //$this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'column', (int)sizeof($category));

    }

    /**
     * [getResourcesStatistics description]
     * @param  [type] $criteria  [description]
     * @param  [type] $value     [description]
     * @param  [type] $survey    [description]
     * @param  [type] $for       [description]
     * @param  [type] $statistic [description]
     * @return [type]            [description]
     */

    public function getResourcesStatistics($criteria, $value, $survey, $survey_category, $for, $statistic) {
        $results = $this->m_analytics->getResourcesStatistics($criteria, $value, $survey, $survey_category, $for, $statistic);

        //echo "<pre>"; print_r($results);echo "</pre>";die;

        foreach ($results as $key => $result) {

            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'Sometimes Available') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);

            //echo "<pre>"; print_r($resultArray);echo "</pre>";die;


        }
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    /**
     * [getHardwareFrequencyMnh description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $choice   [description]
     * @return [type]           [description]
     */
    public function getHardwareFrequencyMNH($criteria, $value, $survey, $survey_category) {

        $value = urldecode($value);


        $this->getResourcesStatistics($criteria, $value, $survey, $survey_category, 'hwr', 'availability');
    }

    /**
     * [getResourcesFrequencymnh description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $choice   [description]
     * @return [type]           [description]
     */
    public function getresourcesFrequencyMnh($criteria, $value, $survey, $survey_category) {


        $value = urldecode($value);

        $this->getResourcesStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'availability');

        //echo "<pre>"; print_r($results);echo "</pre>";die;


    }
	public function getCHresourcesAvailability($criteria, $value, $survey, $survey_category) {
		
        $this->getResourcesStatistics($criteria, $value, $survey, $survey_category, 'hwr', 'availability');
	}
	public function getCHResourceLocation($criteria, $value, $survey, $survey_category) {
		
        $this->getResourceLocation($criteria, $value, $survey, $survey_category, 'mch');
	}
	public function getCHresourcesSupplier($criteria, $value, $survey, $survey_category) {
	
        $this->getResourcesStatistics($criteria, $value, $survey, $survey_category, 'hwr', 'supplier');
	}
    public function getresourcesFrequencyCH($criteria, $value, $survey, $survey_category) {

       
        
        $this->getResourcesStatistics($criteria, $value, $survey, $survey_category, 'mhw', 'availability');
        
        //echo "<pre>"; print_r($results);echo "</pre>";die;
        
        
    }
    
    public function getCountyReportingSummary($survey, $survey_category) {
        $results = $this->m_analytics->getCountyReportingSummary($survey, $survey_category);
        $data['title'] = array('Facility MFL', 'Facility Name', 'Facility Ownership', 'Facility Type', 'Facility Level', 'Facility District', 'Facility County');

        //echo "<pre>"; print_r($titles);echo "</pre>";
        $data['data'] = $results;

        // $data['title'] = $titles;

        $this->loadExcel($data, 'Summary for Counties Reporting for' . ' ' . strtoupper($survey) . ' : ' . strtoupper($survey_category) . $value);
    }

    /**
     * [getSectionsChosen description]
     * @param  [type] $survey [description]
     * @return [type]         [description]
     */
    public function getSectionsChosen($survey) {
        switch ($survey) {
            case 'mnh':
            $sectionNames = array('Facility Information', 'Facility Data And Maternal And Neotanal Service Delivery', 'Guidelines, Job Aid and Tools Availability', 'Staff Training', 'Commodity Availability', 'Commodity  Usage', 'Equipment Availability and Functionality', 'Supplies', 'Resources');
            $sections = 8;
            break;

            case 'ch':
            $sectionNames = array('Facility Information', 'Guidelines,Job Aids and Tools', 'Assessment', 'Commodity & Bundling', 'On-Site Rehydration', 'Equipment', 'Supplies', 'Resources', 'Community Strategy');
            $sections = 9;
            break;

            case 'hcw':
            $sections = 5;
            break;

            default:
            break;
        }
        for ($x = 1; $x <= $sections; $x++) {
            $sectionList.= '<li><a href="#section-' . $x . '">Section ' . $x . ' : ' . $sectionNames[$x - 1] . '</a></li>';
        }
        echo json_encode($sectionList);
    }
    
    /**
     * [getMNHCommodityLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $choice   [description]
     * @return [type]           [description]
     */
    public function getMNHCommodityLocation($criteria, $value, $survey, $survey_category) {
  
        $this->getCommodityLocation($criteria, $value, $survey, $survey_category, 'mnh');

        //echo "<pre>"; print_r($results);echo "</pre>";die;


    }/**
     * [getCHCommodityLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $choice   [description]
     * @return [type]           [description]
     */
    public function getCHCommodityLocation($criteria, $value, $survey, $survey_category) {
  
        $this->getCommodityLocation($criteria, $value, $survey, $survey_category, 'ch');

        //echo "<pre>"; print_r($results);echo "</pre>";die;


    }

    /**
     * [getMNHResourcesLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $choice   [description]
     * @return [type]           [description]
     */
    public function getMNHResourceLocation($criteria, $value, $survey, $survey_category) {
  
        $this->getResourceLocation($criteria, $value, $survey, $survey_category, 'mnh');

        //echo "<pre>"; print_r($results);echo "</pre>";die;


    }
  

    /**
     * [getMNHEquipmentFrequency description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type] $for      [description]
     */
    public function getMNHEquipmentFrequency($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'availability');
    }

    /**
     * [getMNHEquipmentLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getMNHEquipmentLocation($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentLocation($criteria, $value, $survey, $survey_category, 'mnh');
    }

    /**
     * [getMNHEquipmentFunctionality description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getMNHEquipmentFunctionality($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentStatistics($criteria, $value, $survey, $survey_category, 'mnh', 'functionality');
    }

    /**
     * [getCHEquipmentFrequency description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCHEquipmentFrequency($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentStatistics($criteria, $value, $survey, $survey_category, 'ort', 'availability');
    }

    /**
     * [getCHEquipmentLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCHEquipmentLocation($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentLocation($criteria, $value, $survey, $survey_category, 'ch');
    }

    /**
     * [getCHEquipmentFunctionality description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCHEquipmentFunctionality($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentStatistics($criteria, $value, $survey, $survey_category, 'ort', 'functionality');
    }

    /**
     * [getHCWEquipmentFrequency description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getHCWEquipmentFrequency($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentStatistics($criteria, $value, $survey, $survey_category, 'hcw', 'availability');
    }

    /**
     * [getHCWEquipmentLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getHCWEquipmentLocation($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentStatistics($criteria, $value, $survey, $survey_category, 'hcw', 'location');
    }

    /**
     * [getHCWEquipmentFunctionality description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getHCWEquipmentFunctionality($criteria, $value, $survey, $survey_category) {
        $this->getEquipmentStatistics($criteria, $value, $survey, $survey_category, 'hcw', 'functionality');
    }

    // public function getCHCommoditySuppliers($criteria, $value, $survey, $survey_category) {
    //     $value = urldecode($value);
    //     $results = $this->m_analytics->getCHCommoditySupplier($criteria, $value, $survey, $survey_category);
    //     $category = $results['analytic_variables'];
    //     $suppliers = $results['responses'];
    //     $resultArray = array();
    //     foreach ($category as $cat) {
    //         if ($cat != null) {
    //             $newCat[] = $cat;
    //         }
    //     }

    //     //var_dump($newCat);die;
    //     foreach ($suppliers as $key => $value) {
    //         $finalD = array();
    //         foreach ($value as $key1 => $val) {
    //             $finalD[] = $val;
    //         }
    //         $resultArray[] = array('name' => $key, 'data' => $finalD);
    //         unset($finalD);
    //     }
    //     $newCat[] = 'Metronidazole (Flagyl)';
    //     $category = $newCat;

    //     $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    // }

    public function getChallengeStatistics($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getChallengeStatistics($criteria, $value, $survey, $survey_category);

        //echo "<pre>"; print_r($results);echo "</pre>";die;
       /* foreach ($results as $key => $result) {
            

            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != '') {
                    $data[$name][] = (int)$value;
                }
            }
        }*/
        foreach ($results as $key => $data) {
            //echo "<pre>"; print_r($key);echo "</pre>";die;
            foreach($data as $code => $val){
                 //echo "<pre>"; print_r($data);echo "</pre>";die;
            $category[] = $key;
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);

            $resultArray[] = array('name' => $key, 'data' => $val);
            
            
            
            }

        }
      
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'bar');
    }


    /**
     * [getLocationStatistics description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $for      [description]
     * @return [type]           [description]
     */

    public function getLocationStatistics($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getLocationStatistics($criteria, $value, $survey, $survey_category, 'ort');


        //echo "<pre>";print_r($results);echo "</pre>";die;
         $number = $resultArray = $q = $pharmacy = $store = $delivery = $other = array();
        $number = $resultArray = $q = array();
        $count = 0;

        foreach ($results as $key => $value) {

            //echo "<pre>";print_r($results);echo "</pre>";die;

            if ($count == 2):

            //var_dump($value);
            foreach ($value as $location => $val) {
                $gData[] = array(ucwords($location), (int)$val);
            }
            endif;
            $count++;
        }
        $category[] = "Location";

        //echo "<pre>";print_r($gData);echo "</pre>";die;
        $resultArray[] = array('name' => 'ORT Location', 'data' => $gData);



        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'pie');
    }

    
    /**
     * [getSuppliesLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $for      [description]
     * @return [type]           [description]
     */
    public function getSuppliesLocation($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getSupplyLocation($criteria, $value, $survey, $survey_category, $for);

        //echo "<pre>";print_r($results);echo "</pre>";die;
         $number = $resultArray = $q = $pharmacy = $store = $delivery = $other = array();
        $number = $resultArray = $q = array();
        $count = 0;

        foreach ($results as $key => $value) {

            //echo "<pre>";print_r($results);echo "</pre>";die;
           

            //var_dump($value);
            foreach ($value as $location => $val) {
                $gData[] = array(ucwords($location), (int)$val);
            }
            
        }
        $category[] = "Supplies";

        //echo "<pre>";print_r($gData);echo "</pre>";die;
        $resultArray[] = array('name' => 'Supply Location', 'data' => $gData);
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $category = $q;
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'pie');
    }
    
     /**
     * [getEquipmentLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $for      [description]
     * @return [type]           [description]
     */
    public function getEquipmentLocation($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getEquipmentLocation($criteria, $value, $survey, $survey_category, $for);

        //echo "<pre>";print_r($results);echo "</pre>";die;
         $number = $resultArray = $q = $pharmacy = $store = $delivery = $other = array();
        $number = $resultArray = $q = array();
        $count = 0;

        foreach ($results as $key => $value) {

            //echo "<pre>";print_r($results);echo "</pre>";die;
           

            //var_dump($value);
            foreach ($value as $location => $val) {
                $gData[] = array(ucwords($location), (int)$val);
            }
            
        }
        $category[] = "Equipments";

        //echo "<pre>";print_r($gData);echo "</pre>";die;
        $resultArray[] = array('name' => 'Equipment Location', 'data' => $gData);
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $category = $q;
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'pie');
    }

     /**
     * [getResourceLocation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $for      [description]
     * @return [type]           [description]
     */
    public function getResourceLocation($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getResourceLocation($criteria, $value, $survey, $survey_category, $for);

        //echo "<pre>";print_r($results);echo "</pre>";die;
         $number = $resultArray = $q = $pharmacy = $store = $delivery = $other = array();
        $number = $resultArray = $q = array();
        $count = 0;

        foreach ($results as $key => $value) {

            //echo "<pre>";print_r($results);echo "</pre>";die;
           

            //var_dump($value);
            foreach ($value as $location => $val) {
                $gData[] = array(ucwords($location), (int)$val);
            }
            
        }
        $category[] = "Resources";

        //echo "<pre>";print_r($gData);echo "</pre>";die;
        $resultArray[] = array('name' => 'Resource Location', 'data' => $gData);
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $category = $q;
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'pie');
    }


    public function getCommodityLocation($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getCommodityLocation($criteria, $value, $survey, $survey_category, $for);

        //echo "<pre>";print_r($results);echo "</pre>";die;
         $number = $resultArray = $q = $pharmacy = $store = $delivery = $other = array();
        $number = $resultArray = $q = array();
        $count = 0;

        foreach ($results as $key => $value) {

            //echo "<pre>";print_r($results);echo "</pre>";die;
           

            //var_dump($value);
            foreach ($value as $location => $val) {
                $gData[] = array(ucwords($location), (int)$val);
            }
            
        }
        $category[] = "Location";

        //echo "<pre>";print_r($gData);echo "</pre>";die;
        $resultArray[] = array('name' => 'Commodity Location', 'data' => $gData);
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $category = $q;
        //echo "<pre>";print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'pie');
    }


    /**
     * [getQuestionStatistics description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @param  [type] $for      [description]
     * @return [type]           [description]
     */
    public function getQuestionStatistics($criteria, $value, $survey, $survey_category, $for,$statistics) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, $for,$statistics);

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        foreach ($results as $key => $value) {
            $q[] = $key;
            $yes[] = (int)$value['yes'];
            $no[] = (int)$value['no'];
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        //print_r($resultArray);die;
        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    /**
     * [getHIV description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getHIV($criteria, $value, $survey, $survey_category) {

        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'hiv','response');

    }

    public function getCEOC($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ceoc','response');
    }
    
   // public function getWaste($criteria, $value, $survey, $survey_category) {
     //   $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'waste');
    //}

    public function getKangarooMotherCare($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'kang','response');
    }
    public function getNewborn($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatisticsSingle($criteria, $value, $survey, $survey_category, 'newb');
    }
	public function getCSReasons($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ceoc','reason');
    }
	

    /**
     * [getORTOne description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getORTOne($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ort','response');

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        $count = 0;
        foreach ($results as $key => $value) {

            if ($count == 0):
            $q[] = $key;
            $yes[] = (int)$value['yes'];
            $no[] = (int)$value['no'];
            endif;
            $count++;
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');

        // $this->getQuestionStatistics($criteria, $value, $survey, 'ort');


    }


    public function getWasteStatistics($criteria, $value, $survey,$survey_category) {
        $results = $this->m_analytics->getWasteStatistics($criteria, $value, $survey,$survey_category, 'waste');
        
        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $waste = $inci = $other = $placenta = $burning = array();
        $count = 0;
        
        foreach ($results as $key => $value) {
            
            //echo "<pre>";print_r($results);echo "</pre>";die;
    
                
                //var_dump($value);
                foreach ($value as $waste => $val) {
                    $getData[] = array(ucwords($waste), (int)$val);
                }
           
        }
        $category[] = "Question";
        
        //echo "<pre>";print_r($gData);echo "</pre>";die;
        $resultArray[] = array('name' => 'Waste', 'data' => $getData);
        
        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'pie');
    }
    
    /**
     * [getORTTwo description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getORTTwo($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ort','response');

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        $count = 0;
        foreach ($results as $key => $value) {

            if ($count == 1):
            $q[] = $key;
            $yes = (int)$value['yes'];
            $no = (int)$value['no'];
            endif;
            $count++;
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');

        // $this->getQuestionStatistics($criteria, $value, $survey, 'ort');


    }

    /**
     * [getORTThree description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getORTThree($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ortf');

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();

        foreach ($results as $key => $value) {

            $q[] = $key;
            $yes[] = (int)$value['yes'];
            $no[] = (int)$value['no'];
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');

        // $this->getQuestionStatistics($criteria, $value, $survey, 'ort');


    }

    /**
     * [getORTA description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getORTA($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ortf','response');
    }

    /**
     * [getJobAids description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getJobAids($criteria, $value, $survey, $survey_category) {

        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'job','response');

    }

    /**
     * [getGuidelinesAvailabilityCH description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getGuidelinesAvailabilityCH($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'guide','response');
    }

    /**
     * [getGuidelinesAvailabilityMNH description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getGuidelinesAvailabilityMNH($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'guide','response');
    }

    /**
     * [getIMCIInterview description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getIMCIInterview($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'int','response');
    }

    /**
     * [getIMCIConsultation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getIMCIConsultation($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'obs','response');
    }

    /**
     * [getIMCICertificate description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getIMCICertificate($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'out','response');
    }

    /**
     * [getIMCICertificateA description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getIMCICertificateA($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'certa','response');
    }

    /**
     * [getIMCICertificateB description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getIMCICertificateB($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'certb','response');
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
        $value = urldecode($value);
        $results = $this->m_analytics->getIndicatorStatistics($criteria, $value, $survey, $survey_category, $for);

        //echo "<pre>"; print_r($results);echo "</pre>";die;
        foreach ($results as $key => $result) {

            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'NULL') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);

            //echo "<pre>"; print_r($resultArray);echo "</pre>";die;


        }
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    /**
     * [getIndicatorComparison description]
     * @param  [type] $criteria        [description]
     * @param  [type] $value           [description]
     * @param  [type] $survey          [description]
     * @param  [type] $survey_category [description]
     * @param  [type] $for             [description]
     * @return [type]                  [description]
     */
    public function getIndicatorComparison($criteria, $value, $survey, $survey_category, $for) {
        $value = urldecode($value);
        $results = $this->m_analytics->getIndicatorComparison($criteria, $value, $survey, $survey_category, $for);

        //echo '<pre>';print_r($results);echo '</pre>';die;
        foreach ($results as $indicator => $values) {
            $category[] = $indicator;
            foreach ($values as $verdict => $answer) {
                $gData[$verdict][] = $answer;
            }
        }

        foreach ($gData as $name => $data) {
            $resultArray[] = array('name' => ucwords($name), 'data' => $data);
        }

        //echo '<pre>';print_r($resultArray);echo '</pre>';die;

        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }
    public function getIndicatorTypes() {
        $results = $this->m_analytics->getIndicatorTypes();

        //echo '<pre>';print_r($results);echo '</pre>';die;
        $options = '<option>Choose Indicator Type</option>';
        foreach ($results as $value) {
            $options.= '<option value="' . $value['il_for'] . '">' . $value['il_full_name'] . '</option>';
        }
        echo $options;
    }

    /**
     * [getChildrenServices description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getChildrenServices($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'svc');
    }

    /**
     * [getDangerSigns description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getDangerSigns($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'dgn');
    }

    /**
     * [getActionsPerformed description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getActionsPerformed($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'svc');
    }

    /**
     * [getCounselGiven description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCounselGiven($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'cns');
    }

    /**
     * [getTools description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getTools($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'tl');
    }

    /**
     * [getAnaemia description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getAnaemia($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'anm');
    }

    /**
     * [getBreastfeeding description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getBreastfeeding($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'brf');
    }

    /**
     * [getCounselling description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCounselling($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'cnl');
    }

    /**
     * [getCondition description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCondition($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'con');
    }

    /**
     * [getSymptomEar description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getSymptomEar($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'ear');
    }

    /**
     * [getSymptomEye description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getSymptomEye($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'eye');
    }

    /**
     * [getSymptomFever description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getSymptomFever($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'fev');
    }

    /**
     * [getSymptomJaundice description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getSymptomJaundice($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'jau');
    }

    /**
     * [getSymptomMalaria description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getSymptomMalaria($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'mal');
    }

    /**
     * [getSymptomPneumonia description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getSymptomPneumonia($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'pne');
    }

    /**
     * [getMNHTools description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getMNHTools($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'tl');
    }

    /**
     * [getChHealthServices description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getChHealthServices($criteria, $value, $survey, $survey_category) {

        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'hs');
    }

    /**
     * [getCaseManagement description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCaseManagement($criteria, $value, $survey, $survey_category) {
        $this->getIndicatorStatistics($criteria, $value, $survey, $survey_category, 'cert');
    }

    /**
     * [getIMCIConsultation description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */

    /*public function getIMCIConsultation($criteria, $value, $survey) {
        $this->getIndicatorStatistics($criteria, $value, $survey, 'imci');
    }*/

    /*
     * Diarrhoea case numbers per Month
    */

    public function getDiarrhoeaCaseNumbers($criteria, $value, $survey, $survey_category) {
        $value = urldecode($value);
        $results = $this->m_analytics->getDiarrhoeaCaseNumbers($criteria, $value, $survey, $survey_category);
        $resultData = $results['num_of_diarrhoea_cases'];
        $category = $results['categories'];

        $monthArray = array('jan', 'feb', 'mar', 'apr', 'may', 'june', 'july', 'aug', 'sept', 'oct', 'nov', 'december');
        $monthCounter = 0;
        foreach ($monthArray as $value) {

            //echo $value;
            $dataArray[] = (int)$resultData[0][$value];
            $monthCounter++;
        }
        $resultArray = array(array('name' => 'Cases', 'data' => $dataArray));
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    /*
     * Diarrhoea case treatments
    */

    public function getDiarrhoeaCaseTreatment($criteria, $value, $survey, $survey_category, $filter) {
        $value = urldecode($value);
        $results = $this->m_analytics->getDiarrhoeaCaseTreatment($criteria, $value, $survey, $survey_category);

        //var_dump($results);die;
        $categories = $results['categories'];
        $categoriesCount = 0;
        $resultArray = array();
        if ($results != null && count($results) > 0) {
            foreach ($results as $result => $val) {

                if ($categoriesCount < 6) {
                    $index = $categories[$categoriesCount];
                    if ($result == $index) {
                        $severe_dehydration[] = array($result, (int)$val['severe_dehydration']);
                        $some_dehydration[] = array($result, (int)$val['some_dehydration']);
                        $no_dehydration[] = array($result, (int)$val['no_dehydration']);
                        $dysentry[] = array($result, (int)$val['dysentry']);
                        $no_classification[] = array($result, (int)$val['no_classification']);
                        $category[] = $index;
                        $categoriesCount++;
                    }
                }
            }
        }
        switch ($filter) {
            case 'SevereDehydration':
            $resultArray[] = array('type' => 'pie', 'name' => 'Case Treatment', 'data' => $severe_dehydration);
            break;

            case 'SomeDehydration':
            $resultArray[] = array('type' => 'pie', 'name' => 'Case Treatment', 'data' => $some_dehydration);
            break;

            case 'NoDehydration':
            $resultArray[] = array('type' => 'pie', 'name' => 'Case Treatment', 'data' => $no_dehydration);
            break;

            case 'Dysentry':
            $resultArray[] = array('type' => 'pie', 'name' => 'Case Treatment', 'data' => $dysentry);
            break;

            case 'NoClassification':
            $resultArray[] = array('type' => 'pie', 'name' => 'Case Treatment', 'data' => $no_classification);
            break;
        }

        $resultArray = json_encode($resultArray);

        //var_dump($resultArray);
        $datas = array();
        $resultArraySize = count($categories);

        //$resultArraySize =  1;
        //$result[]=array('name'=>'Test','data'=>array(1,2,7,8,0,8,3,5));
        //$resultArray = 5;
        //var_dump($category);
        $datas['resultArraySize'] = $resultArraySize;

        $datas['container'] = 'chart_' . $criteria . rand(1, 10000);

        $datas['chartType'] = 'bar';
        $datas['chartMargin'] = 70;
        $datas['title'] = 'Chart';
        $datas['chartTitle'] = ' ';

        //$datas['chartTitle'] = 'Case Treatment';
        $datas['categories'] = '';
        $datas['yAxis'] = 'Occurence';
        $datas['resultArray'] = $resultArray;
        $this->load->view('charts/chart_pie_v', $datas);
    }

    public function getMNHSuppliesSuppliers($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesSupplier($criteria, $value, 'mnh', $survey_category);
    }

    public function getCHSuppliesSupplier($criteria, $value, $survey, $survey_category) {
        $this->getSuppliesSupplier($criteria, $value, 'ch', $survey_category);
    }

    /**
     *
     */
    public function getSuppliesSupplier($criteria, $value, $survey, $survey_category) {
        $value = urldecode($value);
        $results = $this->m_analytics->getCHSuppliesSupplier($criteria, $value, $survey, $survey_category);

        //var_dump($results);
        $category = $results['analytic_variables'];
        $suppliers = $results['responses'];
        $resultArray = $newCat = array();
        foreach ($category as $cat) {
            if ($cat != null) {
                $newCat[] = $cat;
            }
        }

        //var_dump($newCat);die;
        foreach ($suppliers as $key => $value) {
            $finalD = array();
            foreach ($value as $key1 => $val) {
                $finalD[] = $val;
            }
            $resultArray[] = array('name' => $key, 'data' => $finalD);
            unset($finalD);
        }

        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    /**
     * Lists for NEVER
     */
    public function getFacilityListForNo($criteria, $value, $survey, $survey_category, $choice) {
        $value = urldecode($value);
        $results = $this->m_analytics->getFacilityListForNo($criteria, $value, $survey, $survey_category, $choice);

        //var_dump($results);
        //die ;
        //echo '<pre>';
        //print_r($results);
        //echo '</pre>';
        $pdf = "<h3>Facility List that responded <em>NO</em> for $value District</h3>";
        $pdf.= '<table>';
        foreach ($results as $key => $value) {
            $pdf.= '<tr><th colspan="2">' . $key . '<th></tr>';

            //Per Title
            foreach ($value as $facility) {
                $pdf.= '<tr class="tableRow"><td width="70px">' . $facility[0] . '</td><td width="500px">' . $facility[1] . '</td></tr>';
            }
        }
        $pdf.= '</table>';
        $this->loadPDF($pdf);
    }

    public function getFacilityListForNoMNH($criteria, $value, $survey, $survey_category, $question) {
        $value = urldecode($value);
        $results = $this->m_analytics->getFacilityListForNoMNH($criteria, $value, $survey, $survey_category, $question);

        //echo '<pre>';
        //print_r($results);
        //echo '</pre>';
        //die ;

        $pdf = "<h3>Facility List that responded <em>NO</em> for $value District</h3>";
        $pdf.= '<table>';
        foreach ($results as $key => $value) {
            $pdf.= '<tr><th colspan="2">' . $key . '<th></tr>';

            //Per Title
            foreach ($value as $facility) {
                $pdf.= '<tr class="tableRow"><td width="70px">' . $facility[0] . '</td><td width="500px">' . $facility[1] . '</td></tr>';
            }
        }
        $pdf.= '</table>';

        //echo $pdf;
        $this->loadPDF($pdf);
    }

    /**
     * Lists for NEVER
     */
    public function getFacilityListForNever($criteria, $value, $survey, $survey_category, $choice) {
        urldecode($value);
        $results = $this->m_analytics->getFacilityListForNever($criteria, $value, $survey, $survey_category, $choice);

        //var_dump($results);
        //echo '<pre>';
        //print_r($results);
        //echo '</pre>';
        $pdf = "<h3>Facility List that responded <em>NEVER</em> for $value District</h3>";
        $pdf.= '<table>';
        foreach ($results as $key => $value) {
            $pdf.= '<tr><th colspan="2">' . $key . '<th></tr>';

            //Per Title
            foreach ($value as $facility) {
                $pdf.= '<tr class="tableRow"><td width="70px">' . $facility[0] . '</td><td width="500px">' . $facility[1] . '</td></tr>';
            }
        }
        $pdf.= '</table>';
        $this->loadPDF($pdf);
    }

    /**
     * Get Facility Ownership
     */
    public function getFacilityOwnerPerCounty($criteria, $value, $survey, $survey_category) {

        //$allCounties = $this -> m_analytics -> getReportingCounties('ch','mid-term');
        $county = urldecode($county);

        //foreach ($allCounties as $county) {
        $category[] = $county;
        $results = $this->m_analytics->getFacilityOwnerPerCounty($criteria, $value, $survey, $survey_category);
        $resultArray = array();
        foreach ($results as $value) {

            //$data = array();

            $name = $value['facilityOwner'];

            //echo '<pre>';print_r($name);echo '</pre>';die;
            //$gData[] = (int)$value['level_total'];
            $gData[] = array('name' => $name, 'y' => (int)$value['ownership_total']);

            //echo '<pre>';print_r($resultArray);echo '</pre>';die;


        }
        $resultArray[] = array('name' => 'Facility Ownership', 'data' => $gData);

        //echo '<pre>';print_r($resultArray);echo '</pre>';die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 50, 'pie');
    }

    /**
     * Get Lever Ownership
     */

    public function getFacilityLevelPerCounty($criteria, $value, $survey, $survey_category) {

        //$allCounties = $this -> m_analytics -> getReportingCounties('ch','mid-term');
        $value = urldecode($value);

        //foreach ($allCounties as $county) {

        $category[] = $value;
        $results = $this->m_analytics->getFacilityLevelPerCounty($criteria, $value, $survey, $survey_category);

        //echo '<pre>';print_r($results);echo '</pre>';die;
        $resultArray = array();
        foreach ($results as $value) {

            //$data = array();

            $name = 'Level  ' . $value['facilityLevel'];

            //echo '<pre>';print_r($name);echo '</pre>';die;
            //$gData[] = (int)$value['level_total'];
            $gData[] = array('name' => $name, 'y' => (int)$value['level_total']);

            //echo '<pre>';print_r($resultArray);echo '</pre>';die;


        }
        $resultArray[] = array('name' => 'Facility Levels', 'data' => $gData);

        //echo '<pre>';print_r($resultArray);echo '</pre>';die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 30, 'pie');
    }

    public function getFacilityTypePerCounty($criteria, $value, $survey, $survey_category) {

        //$allCounties = $this -> m_analytics -> getReportingCounties('ch','mid-term');
        $value = urldecode($value);

        //foreach ($allCounties as $county) {
        $category[] = $value;
        $results = $this->m_analytics->getFacilityTypePerCounty($criteria, $value, $survey, $survey_category);

        //echo '<pre>';print_r($results);echo '</pre>';die;
        $resultArray = array();
        foreach ($results as $value) {

            //$data = array();

            $name = $value['facilityType'];

            //echo '<pre>';print_r($name);echo '</pre>';die;
            //$gData[] = (int)$value['level_total'];
            $gData[] = array('name' => $name, 'y' => (int)$value['type_total']);

            //echo '<pre>';print_r($resultArray);echo '</pre>';die;


        }
        $resultArray[] = array('name' => 'Facility Types', 'data' => $gData);

        //echo '<pre>';print_r($resultArray);echo '</pre>';die;
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'pie');
    }

    public function getFacilityLevelAll($survey) {
        $counties = $this->m_analytics->getReportingCounties($survey);
        foreach ($counties as $county) {
            $results[$county['county']] = $this->m_analytics->getFacilityLevelPerCounty($county['county'], $survey);
            $categories[] = $county['county'];
        }

        //echo '<pre>';
        //print_r($results);
        //echo '</pre>';die;

        $resultArray = array();
        foreach ($results as $county) {
            foreach ($county as $level) {
                $data[$level['facilityLevel'] + 1][] = (int)$level['level_total'];
            }
        }
        unset($data[5]);
        unset($data[6]);
        foreach ($data as $key => $val) {
            $resultArray[] = array('name' => 'Level ' . $key, 'data' => $val);
        }
        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar', sizeof($categories));
    }

    public function getFacilityOwnerAll($criteria, $value, $survey, $survey_category) {
        $resultArray = array();
        $results = $this->m_analytics->getFacilityOwnerPerCounty($criteria, $value, $survey, $survey_category);
        foreach ($results as $value) {
            $data[$value['facilityOwner']] = (int)$value['ownership_total'];
            $category[] = $value['facilityOwner'];
        }

        //echo "<pre>";print_r($data);echo "</pre>";die;
        foreach ($data as $key => $value) {
            $resultArray[] = array('name' => $key, 'data' => $value);
        }
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 0, 'pie');

        // $counties = $this->m_analytics->getReportingCounties($criteria, $value,$survey);
        // foreach ($counties as $county) {
        // $results[$county['county']] = $this->m_analytics->getFacilityOwnerPerCounty($county['county'], $survey);
        // $categories[] = $county['county'];
        // }
        // $resultArray = array();
        // foreach ($results as $county) {
        // foreach ($county as $level) {
        // $data[$level['facilityOwner']][] = (int)$level['ownership_total'];
        // }
        // }
        // foreach ($data as $key => $val) {
        // $resultArray[] = array('name' => $key, 'data' => $val);
        // }
        //
        // $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');


    }

    /**
     * Get Specific Districts Filter
     */
    public function getSpecificDistrictNames() {
        $county = $this->session->userdata('county_analytics');
        $options = '';
        $results = $this->m_analytics->getSpecificDistrictNames($county);
        $options = '<option selected=selected>Please Select a District</option>';
        foreach ($results as $result) {
            $options.= '<option>' . $result['facDistrict'] . '</option>';
        }

        //return $dataArray;
        echo ($options);
    }

    /**
     * [getSpecificDistrictNamesChosen description]
     * @param  [type] $county [description]
     * @return [type]         [description]
     */
    public function getSpecificDistrictNamesChosen($county) {
        $county = urldecode($county);
        $options = '';
        $results = $this->m_analytics->getSpecificDistrictNames($county);
        $options = '<option selected=selected>All Sub-Counties Selected</option>';
        foreach ($results as $result) {
            $options.= '<option>' . $result['facDistrict'] . '</option>';
        }

        //return $dataArray;
        echo ($options);
    }

    public function getSurveyTypeNamesJSON() {
        $data=array('Maternal and Neonatal Health','Child Health','IMCI Follow-Up');
        foreach($data as $k=>$dat){
            $newData[]=array('id'=>$dat,'text'=>$dat);
        }
        echo json_encode($newData);
    }

    public function getSurveyCategoryNamesJSON() {
        $results = $this->db->get('survey_categories');
        $results = $results->result_array();
        foreach($results as $result){
            $data[]=array('id'=>ucwords($result['sc_name']),'text'=>ucwords($result['sc_name']));
        }
        echo json_encode($data);
    }

    public function getDistrictNamesJSON($county) {
        $county = urldecode($county);
        $options = '';
        $results = $this->m_analytics->getSpecificDistrictNames($county);
        foreach($results as $result){
            $data[]=array('id'=>ucwords($result['facDistrict']),'text'=>ucwords($result['facDistrict']));
        }
        echo json_encode($data);
    }

    public function getCountyNamesJSON() {
        $county = urldecode($county);
        $options = '';
        $results = $this->m_analytics->getReportingCounties();
        foreach($results as $result){
            $data[]=array('id'=>ucwords($result['county']),'text'=>ucwords($result['county']));
        }
        echo json_encode($data);
    }
    public function getMasterFacilityList(){
        $this->db->select('*')->from('facilities')->order_by('fac_county ASC')->order_by('fac_district ASC');
        $results = $this->db->get();
        //var_dump($results->result_array());
        $tmpl = array('table_open' => '<div class="table-container"><table border="0" cellpadding="4" cellspacing="0" class="table table-condensed table-striped table-bordered table-hover dataTable">', 'heading_row_start' => '<tr>', 'heading_row_end' => '</tr>', 'heading_cell_start' => '<th>', 'heading_cell_end' => '</th>', 'row_start' => '<tr>', 'row_end' => '</tr>', 'cell_start' => '<td>', 'cell_end' => '</td>', 'row_alt_start' => '<tr>', 'row_alt_end' => '</tr>', 'cell_alt_start' => '<td>', 'cell_alt_end' => '</td>', 'table_close' => '</table></div>');

        $this->table->set_template($tmpl);

        //set table headers
        $this->table->set_heading('Facility Name','Facility Level','Facility Type','District','County', 'Action');
        foreach ($results->result_array() as $result) {
            $facilityName=$result['fac_name'];
            $facilityLevel=$result['fac_level'];
            $facilityType=$result['fac_type'];
            $facilityDistrict=$result['fac_district'];
            $facilityCounty=$result['fac_county'];


            $activity_action = "<a href='#' class='btn-xs btn-primary'>Edit</a>
            <a href='#' class='btn-xs  imci_activity_source' id='" . $activity->activity_id . "' >View Source Data</a>";
            $this->table->add_row($facilityName,$facilityLevel,$facilityType,$facilityDistrict,$facilityCounty, $activity_action);
        }
        $activity_table = $this->table->generate();
        echo $activity_table;
    }
    //Get Facilities per County
    public function getCountyFacilities($criteria) {
        $result = $this->m_analytics->getCountyFacilities();

        foreach ($result as $result) {
            $county[] = $result['fac_county'];
            $facilities[] = (int)$result['COUNT(facility.fac_name)'];
        }
        $category = $county;
        $resultArray[] = array('type' => 'column', 'name' => 'Facilities', 'data' => $facilities);
        $resultArray = json_encode($resultArray);
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    public function getCountyFacilitiesByOwner($criteria) {
        $result = $this->m_analytics->getCountyFacilitiesByOwner($criteria);

        //var_dump($result);die;
        foreach ($result as $result) {
            $owners[] = $result['facilityOwnedBy'];
            $facilities[] = (int)$result['COUNT(facilityOwnedBy)'];
        }
        $category = $owners;
        $resultArray[] = array('type' => 'column', 'name' => 'Facility Owners', 'data' => $facilities);
        $resultArray = json_encode($resultArray);

        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    public function getFacilitiesByDistrictOptions($district, $survey) {
        $district = urldecode($district);
        $options = $this->m_analytics->getFacilitiesByDistrictOptions($district, $survey);

        //var_dump($options);
        echo $options;
    }

    /**
     *  Summary Data
     */

    public function case_summary($choice) {

        //Get All Reporting Counties
        $counties = $this->m_analytics->getReportingCounties('ch', 'mid-term');
        foreach ($counties as $county) {
            $results[$county['county']] = $this->m_analytics->case_summary($county['county'], $choice);
            $categories[] = $county['county'];
        }

        switch ($choice) {
            case 'Cases':

            //group cases
            foreach ($results as $result) {
                $severe_dehydration[] = (int)$result[0]['severe_dehydration'];
                $some_dehydration[] = (int)$result[0]['some_dehydration'];
                $no_dehydration[] = (int)$result[0]['no_dehydration'];
                $dysentry[] = (int)$result[0]['dysentry'];
                $no_classification[] = (int)$result[0]['no_classification'];
            }
            $resultArray = array(array('name' => 'Severe Dehydration', 'data' => $severe_dehydration), array('name' => 'Some Dehydration', 'data' => $some_dehydration), array('name' => 'No Dehydration', 'data' => $no_dehydration), array('name' => 'Dysentry', 'data' => $dysentry), array('name' => 'No Classification', 'data' => $no_classification));
            break;

            case 'Classification':
            foreach ($results as $value) {
                foreach ($value as $key => $val) {
                    $formattedArray[$key][] = (int)$val[0]['total'];
                }
            }
            foreach ($formattedArray as $key => $arr) {
                $resultArray[] = array('name' => $key, 'data' => $arr);
            }
            break;
        }

        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar', sizeof($categories));
    }

    public function guidelines_summary($guideline) {

        //$guideline = urldecode($guideline);

        //Get All Reporting Counties

        $finalYes = $finalNo = array();
        $counties = $this->m_analytics->getReportingCounties('ch', 'mid-term');

        foreach ($counties as $county) {
            $results[$county['county']] = $this->m_analytics->getGuidelinesAvailability('county', $county['county'], 'ch', 'gp');
            $categories[] = $county['county'];
        }

        //echo '<pre>';print_r($results);echo '</pre>';die;
        foreach ($results as $county) {
            foreach ($county['yes_values'] as $yes) {

                //var_dump($yes);

                //echo '<pre>';print_r($yes);echo '</pre>';die;
                foreach ($yes as $k => $y) {
                    if ($k == $guideline) {
                        $finalYes[] = $y;
                    }
                }
            }

            //echo '<pre>';print_r($yes);echo '</pre>';die;
            foreach ($county['no_values'] as $no) {

                //var_dump($no);
                //echo '<pre>';print_r($no);echo '</pre>';die;
                foreach ($no as $g => $n) {
                    if ($g == $guideline) {
                        $finalNo[] = $n;
                    }
                }
            }

            //echo '<pre>';print_r($guideline);echo '</pre>';die;


        }

        $resultArray = array(array('name' => 'Yes', 'data' => $finalYes), array('name' => 'No', 'data' => $finalNo));

        //echo '<pre>';print_r($guideline);echo '</pre>';die;
        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar', sizeof($categories));
    }

    public function guidelines_summaryMNH($guideline) {
        $guideline = urldecode($guideline);
        $categories = array();

        //echo $guideline;
        //Get All Reporting Counties
        $finalYes = $finalNo = array();
        $counties = $this->m_analytics->getReportingCounties('mnh', 'mid-term');
        foreach ($counties as $county) {
            $results[$county['county']] = $this->m_analytics->getQuestionStatistics('county', $county['county'], 'mnh', 'gp');
        }

        //echo '<pre>';print_r($results);echo '</pre>';die;
        foreach ($results as $k => $county) {

            foreach ($county as $guide => $val) {

                //echo $guide;
                if ($guideline == $guide) {

                    //echo $guideline.'  '.$guideline. '</br>';
                    $finalYes[] = (int)$val['yes'];
                    $finalNo[] = (int)$val['no'];
                    $categories[] = $k;
                }
            }
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $finalYes), array('name' => 'No', 'data' => $finalNo));
        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar', sizeof($categories));
    }

    public function training_summary($training) {
        $training = urldecode($training);

        //Get All Reporting Counties
        $finalYes = $finalNo = array();
        $counties = $this->m_analytics->getReportingCounties('ch', 'mid-term');
        foreach ($counties as $county) {

            $results[$county['county']] = $this->m_analytics->getTrainedStaff('county', $county['county'], 'ch', 'ch');

            $categories[] = $county['county'];
        }

        //echo '<pre>';print_r($results);echo '</pre>';die;
        foreach ($results as $county) {
            foreach ($county['trained'] as $k => $t) {

                if ($k == $training) {
                    $finalYes[] = $t;
                }
            }

            foreach ($county['working'] as $k => $w) {
                if ($k == $training) {
                    $finalNo[] = $w;
                }
            }
        }

        //echo '<pre>';print_r($finalYes);echo '</pre>';
        $resultArray = array(array('name' => 'Trained', 'data' => $finalYes), array('name' => 'Working', 'data' => $finalNo));
        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar', sizeof($categories));
    }

    public function tools_summary($tool) {
        $tool = urldecode($tool);

        //Get All Reporting Counties
        $finalYes = $finalNo = array();
        $counties = $this->m_analytics->getReportingCounties('ch', 'mid-term');

        //echo '<pre>';print_r($counties);echo '</pre>';die;
        foreach ($counties as $county) {
            $results[$county['county']] = $this->getTools('county', $county['county'], 'ch', 'tl');

            //echo '<pre>';print_r($results);echo '</pre>';die;
            $categories[] = $county['county'];
        }

        //echo '<pre>';print_r($results);echo '</pre>';die;
        foreach ($results as $county) {
            foreach ($county['response'] as $key => $currentTool) {
                if ($key == $tool) {
                    $yes[] = $currentTool['Yes'];

                    //echo '<pre>';print_r($yes);echo '</pre>';die;
                    $no[] = $currentTool['No'];

                    //echo '<pre>';print_r($yes);echo '</pre>';die;


                }
            }
        }

        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        //echo '<pre>';print_r($resultArray);echo '</pre>';die;
        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar', sizeof($categories));
    }

    public function training_summaryMNH($training) {
        $training = urldecode($training);

        //Get All Reporting Counties
        $finalYes = $finalNo = array();
        $counties = $this->m_analytics->getReportingCounties('mnh', 'mid-term');
        foreach ($counties as $county) {

            $results[$county['county']] = $this->m_analytics->getTrainedStaff('county', $county['county'], 'mnh', 'mnh');
        }
        foreach ($results as $key => $county) {
            foreach ($county['trained_values'] as $k => $t) {

                if ($k == $training) {
                    $finalYes[] = $t;
                    $categories[] = $key;

                    //echo '<pre>';print_r($categories);echo '</pre>';die;


                }
            }

            foreach ($county['working_values'] as $k => $w) {
                if ($k == $training) {
                    $finalNo[] = $w;
                    $categories[] = $key;
                }
            }
        }

        /*
        $categories = array_unique($categories);
        foreach ($categories as $c) {
            $cat[] = $c;
        }
        $categories = $cat;*/
        $resultArray = array(array('name' => 'Trained', 'data' => $finalYes), array('name' => 'Working', 'data' => $finalNo));

        //echo '<pre>';print_r($resultArray);echo '</pre>';die;
        $this->populateGraph($resultArray, '', $categories, $criteria, 'percent', 70, 'bar', sizeof($categories));
    }

    /**
     * Mother and Neonatal Health Section
     */

    //Section 1
    //-----------------------------------------------------------------------------



    /**
     * Nurses Deployed in Maternity
     */
    public function getNursesDeployed($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getNursesDeployed($criteria, $value, $survey, $survey_category, $for);
        $number = $resultArray = $q = $yes = $no = array();
        $question = '';
        foreach ($results as $key => $value) {
            $question = $key;
            $number[] = (int)$value[0];
        }
        $category[] = 'Numbers';
        $resultArray[] = array('name' => 'Nurses Deployed', 'data' => $number);

        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'bar');
    }

    /**
     * Beds in facility
     */
    public function getBeds($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getBeds($criteria, $value, $survey, $survey_category, $for);
        $number = $resultArray = array();
        foreach ($results as $key => $value) {
            $number[] = (int)$value[0];
            $resultArray[] = array('name' => $key, 'data' => $number);
            $number = array();
        }
        $category[] = 'Numbers';

        $this->populateGraph($resultArray, '', $category, $criteria, '', 120, 'bar');
    }

    /**
     * 24 Hour Service
     */
    public function getServices($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getService($criteria, $value, $survey, $survey_category, $for);
        $number = $resultArray = array();
        foreach ($results as $key => $value) {
            $number[] = (int)$value[0];
            $resultArray[] = array('name' => $key, 'data' => $number);
        }

        $category[] = 'Numbers';
        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'bar');
    }

    /**
     * Health Facility Management
     */
    public function getHFM($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getHFM($criteria, $value, $survey, $survey_category);

        //echo '<pre>';print_r($results);echo '</pre>';die;
        foreach ($results as $key => $val) {
            $yes[] = (int)$val['yes'];
            $no[] = (int)$val['no'];
            $committee = trim($key, 'Does this facility have a');
            $committee = trim($committee, '?');
            $category[] = $committee;
        }

        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    /**
     * Deliveries
     */
    public function getDeliveries($criteria, $value, $survey, $survey_category) {
    }

    //Section 2
    //-----------------------------------------------------------------------------



    /**
     * Deliveries Conducted
     */
    public function getDeliveriesConducted($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getDeliveries($criteria, $value, $survey, $survey_category);
    }

    /**
     * Signal Functions
     * Options:
     *      .bemonc
     *      .cemonc
     */


    public function getBemONCQuestion($criteria, $value, $survey, $survey_category){
         $results = $this->m_analytics->getBemONCQuestion($criteria, $value, $survey, $survey_category);
        

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        foreach ($results as $key => $value) {
            $q[] = $key;
            $yes[] = (int)$value['yes'];
            $no[] = (int)$value['no'];
        }

        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));
        

        //print_r($resultArray);die;
        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    public function getBemONCReason($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getBemONCReason($criteria, $value, $survey, $survey_category);

        //echo "<pre>"; print_r($results);echo "</pre>";die;
        foreach ($results as $key => $result) {

            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'n/a' && $name != '') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);
        }

        //echo "<pre>"; print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 120, 'bar');
    }

    public function getSignalFunction($criteria, $value, $survey, $survey_category, $function) {
        $results['conducted'] = array();
        $results = $this->m_analytics->getSignalFunction($criteria, $value, $survey, $survey_category, $function);

        //echo '<pre>';print_r($results);echo '</pre>';die ;

        $number = $q = $resultArray = $yes = $no = array();
        foreach ($results['conducted'] as $key => $value) {
            $q[] = $key;
            $yes[] = (int)$value['yes'];
            $no[] = (int)$value['no'];
        }
        $resultArray = array(array('name' => 'Deliveries', 'data' => $yes), array('name' => 'No', 'data' => $no));

        $category = $q;

        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }


    public function getBEMONC($criteria, $value, $survey, $survey_category, $function) {
        $this->getSignalFunction($criteria, $value, $survey, $survey_category, 'question');
    }

    public function getSignalFunctionReason($criteria, $value, $survey, $survey_category, $function) {

        $results = $this->m_analytics->getSignalFunction($criteria, $value, $survey, $survey_category, $function);

        //echo '<pre>';print_r($results);echo '</pre>';die ;
        foreach ($results as $key => $result) {

            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $category[] = $key;
            foreach ($result as $name => $value) {
                if ($name != 'n/a' && $name = "") {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);
        }

        //echo "<pre>"; print_r($resultArray);echo "</pre>";die;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
    }

    public function getCEOCB($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ceocx');

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        $count = 0;
        foreach ($results as $key => $value) {

            
            if ($count == 1):
                $q[] = $key;
                $yes[] = (int)$value['yes'];
                $no[] = (int)$value['no'];
            endif;
            $count++;
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');

        // $this->getQuestionStatistics($criteria, $value, $survey, 'ort');


    }

    public function getCEOCA($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ceocx');

        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        $count = 0;
        foreach ($results as $key => $value) {

            
            if ($count == 0):
                $q[] = $key;
                $yes[] = (int)$value['yes'];
                $no[] = (int)$value['no'];
            endif;
            $count++;
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));
        
        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');
        
        // $this->getQuestionStatistics($criteria, $value, $survey, 'ort');
        
        
    }

     public function getCEOCC($criteria, $value, $survey, $survey_category) {
        $results = $this->m_analytics->getQuestionStatistics($criteria, $value, $survey, $survey_category, 'ceocx');
        
        //echo "<pre>";print_r($results);echo "</pre>";die;
        $number = $resultArray = $q = array();
        $number = $resultArray = $q = $yes = $no = array();
        $count = 0;
        foreach ($results as $key => $value) {
            
            if ($count == 2):
                $q[] = $key;
                $yes[] = (int)$value['yes'];
                $no[] = (int)$value['no'];
            endif;
            $count++;
        }
        $resultArray = array(array('name' => 'Yes', 'data' => $yes), array('name' => 'No', 'data' => $no));

        $category = $q;
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 70, 'bar');

        // $this->getQuestionStatistics($criteria, $value, $survey, 'ort');


    }

    /**
     * [getQuestionStatisticsSingle description]
     * @param  [type] $criteria        [description]
     * @param  [type] $value           [description]
     * @param  [type] $survey          [description]
     * @param  [type] $survey_category [description]
     * @param  [type] $for             [description]
     * @return [type]                  [description]
     */
    public function getQuestionStatisticsSingle($criteria, $value, $survey, $survey_category, $for) {
        $results = $this->m_analytics->getQuestionStatisticsSingle($criteria, $value, $survey, $survey_category, $for);

        //echo '<pre>';print_r($results);echo '</pre>';die;
        $number = $q = $resultArray = array();

        foreach ($results as $key => $result) {
            $category[] = $key;
            foreach ($result as $name => $value) {
                $gData[] = array('name' => $name, 'y' => (int)$value);
            }
        }
        $resultArray[] = array('name' => 'Response', 'data' => $gData);
        $this->populateGraph($resultArray, '', $category, $criteria, 'percent', 0, 'pie');
    }

    /**
     * [getDeliveryPreparedness description]
     * @param  [type] $criteria        [description]
     * @param  [type] $value           [description]
     * @param  [type] $survey          [description]
     * @param  [type] $survey_category [description]
     * @return [type]                  [description]
     */
    public function getDeliveryPreparedness($criteria, $value, $survey, $survey_category) {
        $this->getQuestionStatisticsSingle($criteria, $value, $survey, $survey_category, 'prep');
    }

    /**
     * [getCommunityStrategyMNH description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function getCommunityStrategyMNH($criteria, $value, $survey, $survey_category) {

        $results = $this->m_analytics->getCommunityStrategyMNH($criteria, $value, $survey, $survey_category,'cms');
        foreach ($results as $key => $result) {
            //echo "<pre>"; print_r($results);echo "</pre>";die;
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);

            $category[] = $key;
            foreach ($result as $name => $value) {
                //if ($name != 'Sometimes Available') {
                //echo "<pre>"; print_r($name);echo "</pre>";die;
                if ($name = 'QUC07' || $name = 'QUC08' || $name = 'QUC10') {
                    $data[$name][] = (int)$value;
                }
            }
        }
        foreach ($data as $key => $val) {
            //echo "<pre>"; print_r($data);echo "</pre>";die;
            $key = str_replace('_', ' ', $key);
            $key = ucwords($key);
            $key = str_replace(' ', '-', $key);
            $resultArray[] = array('name' => $key, 'data' => $val);
            
            //echo "<pre>"; print_r($key);echo "</pre>";die;
            
            
        }


        $resultArray[] = array('name' =>$value[''], 'data' => $number);	
        $resultArray = json_encode($resultArray);


        $this->populateGraph($resultArray, '', $category, $criteria, '', 70, 'bar');
    }

    /**
     * [commodity_supplies_summary description]
     * @param  [type] $criteria [description]
     * @param  [type] $value    [description]
     * @param  [type] $survey   [description]
     * @return [type]           [description]
     */
    public function commodity_supplies_summary($criteria, $value, $survey, $survey_category) {

        /*using CI Database Active Record*/
        $value = urldecode($value);
        $results = $this->m_analytics->commodities_supplies_summary($criteria, $value, $survey, $survey_category);

        $supplies = $results['supplies'];
        $commodity = $results['commodities'];
        $equipments = $results['equipments'];
        $supplies_cat = $results['supply_categories'];
        $commodity_cat = $results['commodity_categories'];
        $equipment_cat = $results['equipment_categories'];
        $titles = array_merge_recursive(array_values($commodity_cat), array_values($supplies_cat), array_values($equipment_cat));

        //echo '<pre>';print_r($titles);    echo '<pre>';die;
        foreach ($supplies as $key => $facility) {
            foreach ($supplies_cat as $cat) {

                //echo $cat;
                if (!array_key_exists($cat, $facility)) {
                    $newArray[$key][$cat] = 0;
                } else {
                    $newArray[$key][$cat] = $supplies[$key][$cat];
                }
            }
        }

        $arr = array_merge_recursive($commodity, $newArray, $equipments);

        //echo '<pre>';print_r($arr);   echo '<pre>';die;
        $data['title'] = $titles;
        $data['data'] = $arr;
        $this->loadExcel($data, 'Commodity Supplies and Equipments for ' . $value);
    }

    /**
     * [loadExcel description]
     * @param  [type] $data     [description]
     * @param  [type] $filename [description]
     * @return [type]           [description]
     */
    public function loadExcel($data, $filename) {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Rufus Mbugua");
        $objPHPExcel->getProperties()->setLastModifiedBy("Rufus Mbugua");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription(" ");

        // Add some data
        //  echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0);

        $rowExec = 1;

        //Looping through the cells
        $column = 0;
        foreach ($data['title'] as $cell) {

            //echo $column . $rowExec; die;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowExec, $cell);
            $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($column) . $rowExec)->getFont()->setBold(true)->setSize(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($column))->setAutoSize(true);

            $column++;
        }
        $rowExec = 2;
        foreach ($data['data'] as $rowset) {

            //Looping through the cells per facility
            $column = 0;
            foreach ($rowset as $cell) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rowExec, $cell);
                $column++;
            }
            $rowExec++;
        }

        //die ;

        // Rename sheet
        //  echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Simple');

        // Save Excel 2007 file
        //echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');

        // It will be called file.xls
        header('Content-Disposition: attachment; filename=' . $filename . '.xlsx');

        // Write file to the browser
        $objWriter->save('php://output');

        // Echo done
        //echo date('H:i:s') . " Done writing file.\r\n";


    }

    /**
     * [loadPDF description]
     * @param  [type] $pdf [description]
     * @return [type]      [description]
     */
    public function loadPDF($pdf) {
        $stylesheet = ('
        th{
            padding:5px;
            text-align:left;
        }
        tr.tableRow:nth-child(even){
            background:#DDD;
        }
        h3 em {
            color:red;
        }
        ');
        $html = ($pdf);
        $this->load->library('mpdf');
        $this->mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
        $this->mpdf->SetTitle('Maternal Newborn and Child Health Assessment');
        $this->mpdf->SetHTMLHeader('<em>Assessment Tool</em>');
        $this->mpdf->SetHTMLFooter('<em>Assessment Tool</em>');
        $this->mpdf->simpleTables = true;
        $this->mpdf->WriteHTML($stylesheet, 1);
        $this->mpdf->WriteHTML($html, 2);
        $report_name = 'CH Assessment Tool_Facility List' . ".pdf";
        $this->mpdf->Output($report_name, 'I');
    }

    /**
     * [populateGraph description]
     * @param  string  $resultArray [description]
     * @param  string  $resultSize  [description]
     * @param  string  $drilldown   [description]
     * @param  string  $category    [description]
     * @param  string  $criteria    [description]
     * @param  string  $stacking    [description]
     * @param  integer $margin      [description]
     * @param  string  $type        [description]
     * @return [type]               [description]
     */
    public function populateGraph($resultArray = '', $drilldown = '', $category = '', $criteria = '', $stacking = '', $margin = 0, $type = '', $resultSize = '') {
        $datas = array();
        $chart_size = (count($category) < 5) ? 5 : count($category);
        $given_size = ($resultSize != '' && $resultSize < 5) ? 5 : $resultSize;

        //echo $given_size*80;die;
        $datas['container'] = 'chart_' . $criteria . mt_rand();
        $datas['chart_type'] = $type;
        $datas['chart_margin'] = $margin;
        switch ($type) {
            case 'line':
            case 'column':
            $datas['chart_width'] = ($resultSize != '') ? $given_size * 80 : $chart_size * 80;
            $datas['chart_length'] = 300;
            $datas['chart_label_rotation']=(int)-45;
            $datas['chart_legend_floating']=true;
            break;

            default:
            $datas['chart_length'] = ($resultSize != '') ? $given_size * 60 : $chart_size * 60;
            $datas['chart_label_rotation']=(int)0;
            $datas['chart_legend_floating']=false;
            //$datas['chart_width'] = 100;
            break;
        }
        $datas['chart_stacking'] = $stacking;
        $datas['color_scheme'] = ($stacking != '') ? array('#8bbc21', '#fb4347', '#92e18e', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a') : array('#66aaf7', '#f66c6f', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a');
        $datas['chart_categories'] = $category;
        $datas['chart_title'] = 'Values';
        $datas['chart_drilldown'] = $drilldown;
        $datas['chart_series'] = $resultArray;
        echo json_encode($datas);
    }

    public function getCountyData($county, $survey_type, $survey_category) {

        $county = urldecode($county);
        $results = $this->m_analytics->getReportingRatio($county, $survey_type, $survey_category);
        echo json_encode($results);
    }
}
