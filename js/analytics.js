function startAnalytics(base_url, county, survey, survey_category) {
    var chart, div;
    var subID, parentDiv;
    var facility, smallText;
    var district;
    var currentChart, currentDiv;
    var selectedOption;
    var appendToTitle, filter, click, neverList, noList;
    var comparing;
    var extraStat;
    var criteria, value, stat_for, statistic, indicator_type;
    var countyClicked;

    criteria = value = stat_for = statistic = indicator_type = '';
    loadIndicatorTypes();

    if (county === '') {
        county = 'Unselected';
    }
    $('.analytics_row').hide();
    loadSurvey(survey);

    if (survey_category !== '') {
        $("select#survey_category").find("option").filter(function(index) {
            return survey_category === $(this).attr('value');
        }).prop("selected", "selected");


    }
    if (survey !== '') {
        $("select#survey_type").find("option").filter(function(index) {
            return survey === $(this).attr('value');
        }).prop("selected", "selected");


    }
    $('#sectionList li').click(function() {
        $('#sectionList').find('li').removeClass('active');
        $(this).addClass('active');

    });
    $('select#sub_county_select').load(base_url + 'c_analytics/getSpecificDistrictNames');


    $('select#survey_type').change(function() {
        district = $('select#sub_county_select option:selected').text();

        survey = $('select#survey_type option:selected').attr('value');


        if (district != 'All Sub-Counties Selected') {
            district = encodeURIComponent(district);
            loadFacilities(base_url, district, survey, survey_category);
        }
        console.log(survey);
        loadSurvey(survey);
        console.log(district);
        console.log(survey);
        //variableHandler('national', survey, survey_category,indicator_type);

    });
    $('select#survey_category').change(function() {
        district = $('select#sub_county_select option:selected').text();

        survey = $('select#survey_type option:selected').attr('value');
        survey_category = $('select#survey_category option:selected').attr('value');

        if (district != 'All Sub-Counties Selected') {
            district = encodeURIComponent(district);
            loadFacilities(base_url, district, survey, survey_category);
        }
        getReportingData(base_url, survey, survey_category, '#reporting');
        variableHandler('national', county, district, facility, survey, survey_category, indicator_type);

    });
    $('select#county_select').change(function() {

        county = $('select#county_select option:selected').text();
        county = encodeURIComponent(county);
        //console.log(county);
        //alert(currentChart+district+'/ch/'+extraStat);

        loadDistricts(base_url, county);
        district = $('select#sub_county_select option:selected').text();

        survey = $('select#survey_type option:selected').attr('value');
        survey_category = $('select#survey_category option:selected').attr('value');

        if (district != 'All Sub-Counties Selected') {
            district = encodeURIComponent(district);
            loadFacilities(base_url, district, survey, survey_category);
        }
        variableHandler('county', county, district, facility, survey, survey_category, indicator_type);
    });
    $('select#sub_county_select').change(function() {

        district = $('select#sub_county_select option:selected').text();
        district = encodeURIComponent(district);
        loadFacilities(base_url, district, survey, survey_category);
        variableHandler('district', county, district, facility, survey, survey_category, indicator_type);
    });

    $('select#indicator_types').change(function() {
        indicator_type = $('select#indicator_types option:selected').attr('value');
        console.log(indicator_type);
        if (county == 'Unselected') {
            subHandler('national', county, district, facility, survey, survey_category, indicator_type);
        } else {
            if (district == NULL) {
                subHandler('county', county, district, facility, survey, survey_category, indicator_type);
            } else {
                subHandler('district', county, district, facility, survey, survey_category, indicator_type);
            }

        }

    });

    //Change Event for District Select
    $('select#compare_1').change(function() {
        $("#graph_10").empty();
        compar2 = $('select#compare_1 option:selected').text();
        compar2 = encodeURIComponent(compar2);
        $("#graph_10").load(currentChart + compare + '/' + compar2 + '/' + survey + '/' + extraStat);
        //$('#graph_10').load(currentChart+'district/'+district+'/ch/'+extraStat);
    });

    $('select#compare_2').change(function() {
        $("#graph_11").empty();
        compar = $('select#compare_2 option:selected').text();
        compar = encodeURIComponent(compar);
        $("#graph_11").load(currentChart + compare + '/' + compar + '/' + survey + '/' + extraStat);
        //$('#graph_10').load(currentChart+'district/'+district+'/ch/'+extraStat);
    });

    /**
     * [Facility List]
     * @return {[type]} [description]
     */
    $('#facility_list').click(function() {
        window.open(base_url + 'c_analytics/getFacilityListForNo/district/' + district + '/' + survey + '/' + noList);

    });
    /**
     * [description]
     * @return {[type]} [description]
     */
    $('#facility_list_never').click(function() {
        window.open(base_url + 'c_analytics/getFacilityListForNever/district/' + district + '/' + survey + '/' + neverList);

    });
    /**
     * [description]
     * @return {[type]} [description]
     */
    $('#facility_list_no_mnh').click(function() {
        window.open(base_url + 'c_analytics/getFacilityListForNoMNH/district/' + district + '/' + survey + '/' + neverList);

    });
    /**
     * [description]
     * @return {[type]} [description]
     */
    $('#facility_list_commodity_supplies_county').click(function() {
        window.open(base_url + 'c_analytics/commodity_supplies_summary/county/' + county + '/' + survey);

    });
    /**
     * [description]
     * @return {[type]} [description]
     */
    $('#facility_list_commodity_supplies').click(function() {
        window.open(base_url + 'c_analytics/commodity_supplies_summary/district/' + district + '/' + survey);

    });
    $("select").selectpicker({
        style: 'btn-primary',
        menuStyle: 'dropdown'
    });
    //$('body').scrollspy({ target: 'ul.dropdown-menu.down'});

}

function loadSurvey(survey) {
    $('.analytics_row').hide();
    $('#reporting-parent').show();
    $('.analytics_row[data-survey="' + survey + '"]').show();

    $.ajax({
        url: base_url + 'c_analytics/getSectionsChosen/' + survey,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            obj = jQuery.parseJSON(data);
            console.log(obj);
            $('#sectionList').empty();
            $('#sectionList').append(obj);
        }
    });
}
/**
 * [getReportingData description]
 * @param  {[type]} base_url        [description]
 * @param  {[type]} survey_type     [description]
 * @param  {[type]} survey_category [description]
 * @param  {[type]} container       [description]
 * @return {[type]}                 [description]
 */
function getReportingData(base_url, survey_type, survey_category, container) {
    progressRow='';
    $.ajax({
        url: base_url + 'c_analytics/getAllReportedCounties/' + survey_type + '/' + survey_category,
        beforeSend: function(xhr) {
            $(container).empty();
            $(container).append('<div class="loader" >Loading...</div>');
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            obj = jQuery.parseJSON(data);
            //console.log(obj);
            $.each(obj, function(k, v) {
                //alert(k);
                county = '<label>' + v['county'] + '</label>';
                progress = '<div class = "progress"><div class = "progress-bar" aria-valuenow = "' + v['percentage'] + '" aria-valuemax = "100" style="width:'+ v['percentage'] + '%;background:'+v['color']+'">' + v['percentage'] + '%</div></div>';
                progressRow += '<div class="progressRow">' + county + progress + '</div>';
                alert (progressRow);
            });
            $(container).empty();
            $(container).append(progressRow);
        }
    });
}

function loadIndicatorTypes() {
    $('#indicator_types').load(base_url + 'c_analytics/getIndicatorTypes');
}

function loadDistricts(base_url, county) {
    $('select#sub_county_select').load(base_url + 'c_analytics/getSpecificDistrictNamesChosen/' + county);
}

function loadFacilities(base_url, district, survey, survey_category) {
    $('select#facility_select').load(base_url + 'c_analytics/getFacilitiesByDistrictOptions/' + district + '/' + survey + '/' + survey_category);

}

function variableHandler(criteria, county, district, facility, survey, survey_category, indicator_type) {
    switch (criteria) {
        case 'national':
            value = 'Aggegated';
            statisticsHandler(criteria, value, survey, survey_category, indicator_type);
            break;
        case 'county':
            value = county;
            statisticsHandler(criteria, value, survey, survey_category, indicator_type);
            break;
        case 'district':
            value = district;
            statisticsHandler(criteria, value, survey, survey_category, indicator_type);
            break;
        case 'facility':
            value = facility;
            statisticsHandler(criteria, value, survey, survey_category, indicator_type);
            break;
    }
}

function subHandler(criteria, county, district, facility, survey, survey_category, indicator_type) {
    switch (criteria) {
        case 'national':
            value = 'Aggegated';
            indicatorHandler(criteria, value, survey, survey_category, indicator_type);
            break;
        case 'county':
            value = county;
            indicatorHandler(criteria, value, survey, survey_category, indicator_type);
            break;
        case 'district':
            value = district;
            indicatorHandler(criteria, value, survey, survey_category, indicator_type);
            break;
        case 'facility':
            value = facility;
            indicatorHandler(criteria, value, survey, survey_category, indicator_type);
            break;
    }
}
<<<<<<< HEAD
function indicatorHandler(criteria, value, survey, survey_category,indicator_type){
    loadGraph(base_url, 'c_analytics/getIndicatorComparison/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/'+indicator_type, '#indicator_comparison');
}
function statisticsHandler(criteria, value, survey, survey_category,indicator_type) {
switch(survey){
    case'mnh':
        //MNH Analytics
    //Section 1 MNH
    loadGraph(base_url, 'c_analytics/getFacilityOwnerPerCounty/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#MNHfacility_ownership');
    loadGraph(base_url, 'c_analytics/getFacilityLevelPerCounty/' + criteria +'/'+ value + '/' + survey + '/' + survey_category, '#MNHfacility_levels');
    loadGraph(base_url, 'c_analytics/getFacilityTypePerCounty/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#MNHfacility_type');
    loadGraph(base_url, 'c_analytics/getTotalBeds/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#mnhBeds');
    loadGraph(base_url, 'c_analytics/getServiceOperation/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#mnhOperation');
	loadGraph(base_url, 'c_analytics/getTotalHFM/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#mnhHFM');
    
    //Message me when you're online
    
    //Section 2 MNH 
    loadGraph(base_url, 'c_analytics/getDiarrhoeaStatistics/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#MNHdeliveries');

    loadGraph(base_url, 'c_analytics/getBemoncQuestion/' + criteria +'/'+ value + '/' + survey + '/' + survey_category, '#BEMONCQuestions');
    loadGraph(base_url, 'c_analytics/getBemONCReason/' + criteria +'/'+ value + '/' + survey + '/' + survey_category, '#BEMONCReasons');
    loadGraph(base_url, 'c_analytics/getCEOC/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#CEmONC');
    loadGraph(base_url, 'c_analytics/getCSReasons/' + criteria + '/' + value + '/' + survey +  '/' + survey_category+'/cs', '#CEOCReasons');
    loadGraph(base_url, 'c_analytics/getCSReasons/' + criteria + '/' + value + '/' + survey +  '/' + survey_category+'/transfusion', '#TransfusionReasons');
    loadGraph(base_url, 'c_analytics/getCEOCA/' + criteria +'/'+ value + '/' + survey + '/' + survey_category, '#CEOCA');
    loadGraph(base_url, 'c_analytics/getCEOCB/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#CEOCB');
    loadGraph(base_url, 'c_analytics/getCEOCC/' + criteria + '/' + value + '/' + survey +  '/' + survey_category+'/' + survey, '#CEOCC');

    loadGraph(base_url, 'c_analytics/getNewborn/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#MNHnewborn');
    loadGraph(base_url, 'c_analytics/getKangarooMotherCare/' + criteria +'/'+ value + '/' + survey + '/' + survey_category, '#MNHkmc');
    loadGraph(base_url, 'c_analytics/getDeliveryStatistics/' + criteria + '/' + value + '/' + survey+ '/' + survey_category+'/total', '#delivery_preparedness');
    loadGraph(base_url, 'c_analytics/getHIV/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHhiv');
    //Section 3  MNH
    loadGraph(base_url, 'c_analytics/getGuidelinesAvailabilityMNH/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#MNHguidelines');
    loadGraph(base_url, 'c_analytics/getJobAids/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#MNHjob_aids');
    loadGraph(base_url, 'c_analytics/getMNHTools/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#MNHtools');
    
    //Section 4  MNH
    loadGraph(base_url, 'c_analytics/getTrainedStaff/' + criteria + '/' + value + '/' + survey+ '/' + survey_category+ '/' + survey, '#mnhStaffAvailability');
    loadGraph(base_url, 'c_analytics/getStaffRetention/' + criteria + '/' + value + '/' + survey+ '/' + survey_category+ '/' + survey, '#MNHstaffRetention');
    loadGraph(base_url, 'c_analytics/getStaffAvailability/' + criteria + '/' + value + '/' + survey+ '/' + survey_category+ '/' + survey, '#MNHStaffTraining');
    
    //Sections 5 MNH
    loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey +'/consumption', '#MNHcommodity_consumption');
    loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey +'/unavailability', '#MNHunavailability');
    loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey+'/reason', '#MNHReason');
    
    //Section 6 MNH
    loadGraph(base_url, 'c_analytics/getMNHCommodityAvailabilityFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey , '#MNHcommodity_availability');
    loadGraph(base_url, 'c_analytics/getMNHCommodityAvailabilityUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey , '#MNHcommodity_unavailability');
    loadGraph(base_url, 'c_analytics/getMNHCommodityLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_location');
    loadGraph(base_url, 'c_analytics/getMNHCommoditySupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_supplier');
    
    //Section 7 MNH
    loadGraph(base_url, 'c_analytics/getMNHEquipmentFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey , '#mnhequipment_availability');
    loadGraph(base_url, 'c_analytics/getMNHEquipmentFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey , '#mnhequipment_functionality');
    loadGraph(base_url, 'c_analytics/getMNHEquipmentLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhequipment_location');
    
    //Section 8 MNH
    loadGraph(base_url, 'c_analytics/getMNHSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#MNHsupplies_availability');
    loadGraph(base_url, 'c_analytics/getMNHSuppliesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#MNHsupplies_location');
    loadGraph(base_url, 'c_analytics/getMNHSuppliers/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/', '#MNHsupplies_supplier'); 
    //Section 9 MNH

    loadGraph(base_url, 'c_analytics/getMNHresourcesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#mnhresource_availability');
    loadGraph(base_url, 'c_analytics/getMNHresourcesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#mnhresource_mainSource');
	loadGraph(base_url, 'c_analytics/getMNHResourcesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#mnhresource_location');
	loadGraph(base_url, 'c_analytics/getWasteStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#mnhresource_wasteDisposal');
    loadGraph(base_url, 'c_analytics/getStorageStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#mnhresource_storage');

    
    //Section 10 MNH
   loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/community' , '#community_units');
   loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/referral' , '#community_cases');
   loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/trained' , '#imci_trainings');
   
    break;
    case 'ch':
        //Section 1 CH
    loadGraph(base_url, 'c_analytics/getFacilityOwnerPerCounty/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#facility_owner');
    loadGraph(base_url, 'c_analytics/getFacilityLevelPerCounty/' + criteria +'/'+ value + '/' + survey + '/' + survey_category, '#facility_levels');
    loadGraph(base_url, 'c_analytics/getFacilityTypePerCounty/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#facility_type');
    loadGraph(base_url, 'c_analytics/getTrainedStaff/' + criteria + '/' + value + '/' + survey +  '/' + survey_category+'/' + survey, '#staff_training');
    loadGraph(base_url, 'c_analytics/getStaffAvailability/' + criteria + '/' + value + '/' + survey +  '/' + survey_category+'/' + survey, '#staff_availability');
    loadGraph(base_url, 'c_analytics/getStaffRetention/' + criteria + '/' + value + '/' + survey +  '/' + survey_category+'/' + survey, '#staff_retention');

    //Section 2 CH
    loadGraph(base_url, 'c_analytics/getGuidelinesAvailabilityCH/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#guidelines');
    loadGraph(base_url, 'c_analytics/getTools/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#tools');
    loadGraph(base_url, 'c_analytics/getChallengeStatistics/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#challenge');
	loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey+ '/' + survey_category+'/cases', '#u5_register');
    loadGraph(base_url, 'c_analytics/getDangerSigns/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#danger_signs');
    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey+ '/' + survey_category+'/treatment', '#treatment_options');
    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey+ '/' + survey_category+'/other_treatment', '#other_treatment_options');
    
    //Section 3 CH
    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey+ '/' + survey_category, '#u5_register');
    loadGraph(base_url, 'c_analytics/getDangerSigns/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#danger_signs');
    loadGraph(base_url, 'c_analytics/getIndicatorComparison/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/'+indicator_type, '#indicator_comparison');

    //Section 4 CH
    loadGraph(base_url, 'c_analytics/getCHCommodityAvailabilityFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey , '#commodity_availability');
    loadGraph(base_url, 'c_analytics/getCHCommodityAvailabilityUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey , '#commodity_unavailability');
    loadGraph(base_url, 'c_analytics/getCHCommodityLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_location');
    loadGraph(base_url, 'c_analytics/getCHCommoditySuppliers/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_supplier');
    loadGraph(base_url, 'c_analytics/getbundlingFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_availability');
    loadGraph(base_url, 'c_analytics/getbundlingUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category+ '/' + survey , '#bundling_unavailability');
    loadGraph(base_url, 'c_analytics/getbundlingLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_location');
    
    //Section 5 CH
    loadGraph(base_url, 'c_analytics/getORTOne/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_availability');
    loadGraph(base_url, 'c_analytics/getLocationStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_location');
    loadGraph(base_url, 'c_analytics/getNonFunctional/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_nonfunctional');
   loadGraph(base_url, 'c_analytics/getFunctional/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_functional');

    //Section 6 CH
    loadGraph(base_url, 'c_analytics/getCHEquipmentFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#equipment_availability');
    loadGraph(base_url, 'c_analytics/getCHEquipmentLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#equipment_location');
    loadGraph(base_url, 'c_analytics/getCHEquipmentFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#equipment_functionality');
    
    //Section 7 CH
    loadGraph(base_url, 'c_analytics/getCHSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#supplies_availability');
    loadGraph(base_url, 'c_analytics/getCHSuppliesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#supplies_location');
    loadGraph(base_url, 'c_analytics/getCHSuppliesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#ch_suppliers');
    
    //Section 8 CH
    loadGraph(base_url, 'c_analytics/getCHresourcesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#resource_availability');
    loadGraph(base_url, 'c_analytics/getCHResourcesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#resource_location');
    loadGraph(base_url, 'c_analytics/getCHresourcesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category , '#resource_suppliers');
    
    break;
}
    
	 
=======

function indicatorHandler(criteria, value, survey, survey_category, indicator_type) {
    loadGraph(base_url, 'c_analytics/getIndicatorComparison/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + indicator_type, '#indicator_comparison');
>>>>>>> 1a20efed662b0f2cb0636541cd76449374e1fec5
}

function statisticsHandler(criteria, value, survey, survey_category, indicator_type) {
    switch (survey) {
        case 'mnh':
            //MNH Analytics
            //Section 1 MNH
            loadGraph(base_url, 'c_analytics/getFacilityOwnerPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHfacility_ownership');
            loadGraph(base_url, 'c_analytics/getFacilityLevelPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHfacility_levels');
            loadGraph(base_url, 'c_analytics/getFacilityTypePerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHfacility_type');
            loadGraph(base_url, 'c_analytics/getServices/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#24Hr');
            loadGraph(base_url, 'c_analytics/getHFM/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#HFM');
            loadGraph(base_url, 'c_analytics/getBedStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/total', '#NnB');

            //Message me when you're online

            //Section 2 MNH 
            loadGraph(base_url, 'c_analytics/getDiarrhoeaStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHdeliveries');

            loadGraph(base_url, 'c_analytics/getBemoncQuestion/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#BEMONCQuestions');
            loadGraph(base_url, 'c_analytics/getBemONCReason/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#BEMONCReasons');
            loadGraph(base_url, 'c_analytics/getCEOC/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#CEmONC');
            loadGraph(base_url, 'c_analytics/getCSReasons/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/cs', '#CEOCReasons');
            loadGraph(base_url, 'c_analytics/getCSReasons/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/transfusion', '#TransfusionReasons');
            loadGraph(base_url, 'c_analytics/getCEOCA/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#CEOCA');
            loadGraph(base_url, 'c_analytics/getCEOCB/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#CEOCB');
            loadGraph(base_url, 'c_analytics/getCEOCC/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#CEOCC');

            loadGraph(base_url, 'c_analytics/getNewborn/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHnewborn');
            loadGraph(base_url, 'c_analytics/getKangarooMotherCare/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHkmc');
            loadGraph(base_url, 'c_analytics/getDeliveries/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/total', '#delivery_preparedness');
            loadGraph(base_url, 'c_analytics/getHIV/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHhiv');
            //Section 3  MNH
            loadGraph(base_url, 'c_analytics/getGuidelinesAvailabilityMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHguidelines');
            loadGraph(base_url, 'c_analytics/getJobAids/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHjob_aids');
            loadGraph(base_url, 'c_analytics/getMNHTools/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHtools');

            //Section 4  MNH
            loadGraph(base_url, 'c_analytics/getTrainedStaff/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhStaffAvailability');
            loadGraph(base_url, 'c_analytics/getStaffRetention/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHstaffRetention');
            loadGraph(base_url, 'c_analytics/getStaffAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHStaffTraining');

            //Sections 5 MNH
            loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey + '/consumption', '#MNHcommodity_consumption');
            loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey + '/unavailability', '#MNHunavailability');
            loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey + '/reason', '#MNHReason');

            //Section 6 MNH
            loadGraph(base_url, 'c_analytics/getMNHCommodityAvailabilityFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_availability');
            loadGraph(base_url, 'c_analytics/getMNHCommodityAvailabilityUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_unavailability');
            loadGraph(base_url, 'c_analytics/getMNHCommodityLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_location');
            loadGraph(base_url, 'c_analytics/getMNHCommoditySupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_supplier');

            //Section 7 MNH
            loadGraph(base_url, 'c_analytics/getMNHEquipmentFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhequipment_availability');
            loadGraph(base_url, 'c_analytics/getMNHEquipmentFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhequipment_functionality');
            loadGraph(base_url, 'c_analytics/getMNHEquipmentLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhequipment_location');

            //Section 8 MNH
            loadGraph(base_url, 'c_analytics/getMNHSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHsupplies_availability');
            loadGraph(base_url, 'c_analytics/getMNHSuppliesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHsupplies_location');
            loadGraph(base_url, 'c_analytics/getMNHSuppliers/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/', '#MNHsupplies_supplier');
            //Section 9 MNH

            loadGraph(base_url, 'c_analytics/getMNHresourcesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_availability');
            loadGraph(base_url, 'c_analytics/getMNHresourcesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_mainSource');
            loadGraph(base_url, 'c_analytics/getMNHResourcesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_location');
            loadGraph(base_url, 'c_analytics/getWasteStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_wasteDisposal');


            //Section 10 MNH
            loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/community', '#community_units');
            loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/referral', '#community_cases');
            loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/trained', '#imci_trainings');

            break;
        case 'ch':
            //Section 1 CH
            loadGraph(base_url, 'c_analytics/getFacilityOwnerPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#facility_owner');
            loadGraph(base_url, 'c_analytics/getFacilityLevelPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#facility_levels');
            loadGraph(base_url, 'c_analytics/getFacilityTypePerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#facility_type');
            loadGraph(base_url, 'c_analytics/getTrainedStaff/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#staff_training');
            loadGraph(base_url, 'c_analytics/getStaffAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#staff_availability');
            loadGraph(base_url, 'c_analytics/getStaffRetention/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#staff_retention');

            //Section 2 CH
            loadGraph(base_url, 'c_analytics/getGuidelinesAvailabilityCH/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#guidelines');
            loadGraph(base_url, 'c_analytics/getTools/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#tools');
            loadGraph(base_url, 'c_analytics/getChallengeStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#challenge');
            loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/cases', '#u5_register');
            loadGraph(base_url, 'c_analytics/getDangerSigns/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#danger_signs');
            loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/treatment', '#treatment_options');
            loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/other_treatment', '#other_treatment_options');

            //Section 3 CH
            loadGraph(base_url, 'c_analytics/getIndicatorComparison/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + indicator_type, '#indicator_comparison');

            //Section 4 CH
            loadGraph(base_url, 'c_analytics/getCHCommodityAvailabilityFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_availability');
            loadGraph(base_url, 'c_analytics/getCHCommodityAvailabilityUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_unavailability');
            loadGraph(base_url, 'c_analytics/getCHCommodityLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_location');
            loadGraph(base_url, 'c_analytics/getCHCommoditySuppliers/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_supplier');
            loadGraph(base_url, 'c_analytics/getbundlingFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_availability');
            loadGraph(base_url, 'c_analytics/getbundlingUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_unavailability');
            loadGraph(base_url, 'c_analytics/getbundlingLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_location');

            //Section 5 CH
            loadGraph(base_url, 'c_analytics/getORTOne/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_availability');
            loadGraph(base_url, 'c_analytics/getLocationStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_location');
            loadGraph(base_url, 'c_analytics/getNonFunctional/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_reason');

            //Section 6 CH
            loadGraph(base_url, 'c_analytics/getCHEquipmentFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#equipment_availability');
            loadGraph(base_url, 'c_analytics/getCHEquipmentLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#equipment_location');
            loadGraph(base_url, 'c_analytics/getCHEquipmentFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#equipment_functionality');

            //Section 7 CH
            loadGraph(base_url, 'c_analytics/getCHSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#supplies_availability');
            loadGraph(base_url, 'c_analytics/getCHSuppliesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#supplies_location');
            loadGraph(base_url, 'c_analytics/getCHSuppliers/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ch_suppliers');

            //Section 8 CH
            loadGraph(base_url, 'c_analytics/getCHresourcesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#resource_availability');
            loadGraph(base_url, 'c_analytics/getCHResourcesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#resource_location');
            loadGraph(base_url, 'c_analytics/getCHresourcesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#resource_suppliers');

            break;
    }


}
