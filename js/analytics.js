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
    var rawUrl;
    var procedure, statistic;
    criteria = district = value = stat_for = statistic = indicator_type = '';
    loadIndicatorTypes();
    var section;
    var scope;

    if (county === '') {
        county = 'Unselected';
    }
    $('.analytics_row').hide();
    loadSurvey(survey);
    if (survey_category != '') {
        if (county == 'Unselected') {
            variableHandler('national', county, district, facility, survey, survey_category, indicator_type);
        } else {
            if (district == '') {
                variableHandler('county', county, district, facility, survey, survey_category, indicator_type);
            } else {
                variableHandler('district', county, district, facility, survey, survey_category, indicator_type);
            }

        }
        getReportingData(base_url, survey, survey_category, '#reporting');
    }


    if (county !== '') {
        $("select#county_select").find("option").filter(function(index) {
            return county === $(this).attr('value');
        }).prop("selected", "selected");
    }
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
        district_select = $('select#sub_county_select option:selected').text();
        //alert(district_select)
        if (district_select !== 'Please Select a District' && district_select !== 'All Sub-Counties Selected') {
            district = district_select;
        } else {
            district = '';
        }
        survey = $('select#survey_type option:selected').attr('value');
        loadSurvey(survey);



    });
    $('select#survey_category').change(function() {
        district = $('select#sub_county_select option:selected').text();

        survey = $('select#survey_type option:selected').attr('value');
        survey_category = $('select#survey_category option:selected').attr('value');

        if (district !== 'All Sub-Counties Selected' && district !== 'Please Select a District') {
            district = encodeURIComponent(district);
            loadFacilities(base_url, district, survey, survey_category);
        } else {
            distrct = '';
        }
        getReportingData(base_url, survey, survey_category, '#reporting');
        scope = 'national';

        section = trim($('.collapse.in').parent().attr('id'), 'mnh-');
        section = trim(section, 'ch-');

        variableHandler(scope, county, district, facility, survey, survey_category, indicator_type, section);

        // variableHandler('national', county, district, facility, survey, survey_category, indicator_type);

    });
    $('select#county_select').change(function() {

        county = $('select#county_select option:selected').text();
        county = encodeURIComponent(county);
        //console.log(county);
        //alert(currentChart+district+'/ch/'+extraStat);

        loadDistricts(base_url, county);
        //district = $('select#sub_county_select option:selected').text();

        survey = $('select#survey_type option:selected').attr('value');
        survey_category = $('select#survey_category option:selected').attr('value');
        scope = 'county';
        section = trim($('.collapse.in').parent().attr('id'), 'mnh-');
        section = trim(section, 'ch-');
        if (decodeURIComponent(county) == 'All Counties Selected') {
            scope = 'national';
        }
        variableHandler(scope, county, district, facility, survey, survey_category, indicator_type, section);
        // variableHandler('county', county, district, facility, survey, survey_category, indicator_type);
    });
    $('select#sub_county_select').change(function() {

        district = $('select#sub_county_select option:selected').text();
        district = encodeURIComponent(district);
        loadFacilities(base_url, district, survey, survey_category);
        scope = 'district';
        survey = $('select#survey_type option:selected').attr('value');
        survey_category = $('select#survey_category option:selected').attr('value');
        section = trim($('.collapse.in').parent().attr('id'), 'mnh-');
        section = trim(section, 'ch-');

        variableHandler(scope, county, district, facility, survey, survey_category, indicator_type, section);
        // variableHandler('district', county, district, facility, survey, survey_category, indicator_type);
    });
    $(document).on('datatable_loaded', function() {
        $('.dataTable').dataTable();
    });
    $('select#indicator_types').change(function() {
        indicator_type = $('select#indicator_types option:selected').attr('value');
        console.log(indicator_type);
        if (county == 'Unselected') {
            subHandler('national', county, district, facility, survey, survey_category, indicator_type);
        } else {
            if (district == '') {
                subHandler('county', county, district, facility, survey, survey_category, indicator_type);
            } else {
                subHandler('district', county, district, facility, survey, survey_category, indicator_type);
            }

        }

    });
    /**
     * [description]
     * @return {[type]} [description]
     */
    $('.sizer').click(function() {
        graph_url = $(this).attr('data-url');
        graph_text = $(this).attr('data-text');
        data_parent = $(this).attr('data-parent');

        data_for = $(this).attr('data-for');
        statistics = $(this).attr('data-statistic') + '_raw';
        raw_url = '';
        if (county == 'Unselected') {
            raw_url = setRawUrl('national', county, district, facility, survey, survey_category, data_for, data_parent, statistics);
        } else {
            if (district == '' || district == 'Please Select a District') {
                raw_url = setRawUrl('county', county, district, facility, survey, survey_category, data_for, data_parent, statistics);
            } else {
                raw_url = setRawUrl('district', county, district, facility, survey, survey_category, data_for, data_parent, statistics);
            }

        }
        showEnlargedGraph(base_url, graph_url, graph_text, raw_url);

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
     * Collapse handler
     */
    $('.panel-collapse.collapse.in').parent().find('.panel-heading h4 a i').attr('class', 'fa fa-chevron-down');
    //Handling Collapses
    $('.panel-collapse').on('show.bs.collapse', function() {
        section = trim($(this).parent().attr('id'), 'mnh-');
        section = trim(section, 'ch-');
        // alert(section);
        $(this).parent().find('.panel-heading h4 a i').attr('class', 'fa fa-chevron-down');
        $(this).parent().find('.panel-heading h4 a span .txt').text('Click to Minimize');
        $('.chart div').width($('.chart div').parent().width());
        variableHandler(scope, county, district, facility, survey, survey_category, indicator_type, section);
        //$('.panel-collapse collapse in').collapse('hide');
        //$(this).collapse('show');

    })
    $('.panel-collapse').on('hide.bs.collapse', function() {
        $(this).parent().find('.panel-heading h4 a i').attr('class', 'fa fa-chevron-right');
        $(this).parent().find('.panel-heading h4 a span .txt').text('Click to Expand');
        //$('.panel-collapse collapse in').collapse('hide');
        //$(this).collapse('show');

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
    progressRow = '';
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
                progress = '<div class = "progress"><div class = "progress-bar" aria-valuenow = "' + v['percentage'] + '" aria-valuemax = "100" style="width:' + v['percentage'] + '%;background:' + v['color'] + '">' + v['percentage'] + '%</div></div>';
                progressRow += '<div class="progressRow">' + county + progress + '</div>';
                // alert(progressRow);
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

function variableHandler(criteria, county, district, facility, survey, survey_category, indicator_type, section) {
    switch (criteria) {
        case 'national':
            value = 'Aggegated';
            statisticsHandler(criteria, value, survey, survey_category, indicator_type, section);
            break;
        case 'county':
            value = county;
            statisticsHandler(criteria, value, survey, survey_category, indicator_type, section);
            break;
        case 'district':
            value = district;
            statisticsHandler(criteria, value, survey, survey_category, indicator_type, section);
            break;
        case 'facility':
            value = facility;
            statisticsHandler(criteria, value, survey, survey_category, indicator_type, section);
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


function indicatorHandler(criteria, value, survey, survey_category, indicator_type) {
    loadGraph(base_url, 'c_analytics/getIndicatorComparison/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + indicator_type, '#indicator_comparison');
}

function setRawUrl(criteria, county, district, facility, survey, survey_category, data_for, data_parent, statistic) {
    // alert(procedure);
    var raw_url = '';
    switch (criteria) {
        case 'national':
            value = 'Aggegated';
            break;
        case 'county':
            value = county;
            break;
        case 'district':
            value = district;
            break;
        case 'facility':
            value = facility;
            break;

    }
    switch (data_parent) {
        case 'question':
            raw_url = 'c_analytics/getQuestionRaw/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + data_for + '/' + statistic;
            break;

    }
    return raw_url;
}

// function setScope(criteria, county, district, facility, survey, survey_category) {

// }

function loadEnlargedGraph(base_url, graph_url, title, raw_url) {

    if (raw_url != '') {
        contents = '<div class="row">' +
            '<div class="medium-graph" id="graph"></div>' +
            '<div class="large-graph" ><h6 class="table-header">Facility Raw Information<span><button id="excel"><i class="fa fa-download"></i>Download Excel</button><button id="pdf"><i class="fa fa-download"></i>Download PDF</button></span></h6>' +
            '<div id="table" style="font-size:10px !important"></div></div>' +
            '</div>';
        loadModalForm(base_url, '', title, '90%', contents);
        loadGraph(base_url, graph_url, '#graph');
        loadFacilityRawData(base_url, raw_url, '#table');
    } else {
        contents = '<div class="row">' +
            '<div class="x-large-graph" id="graph"></div>' +
            '</div>';
        loadModalForm(base_url, '', title, '90%', contents);
        loadGraph(base_url, graph_url, '#graph');
    }

}

function statisticsHandler(criteria, value, survey, survey_category, indicator_type, section) {
    switch (survey) {
        case 'mnh':
            //MNH Analytics
            //Section 1 MNH
            switch (section) {
                case 'section-1':
                    loadGraph(base_url, 'c_analytics/getFacilityOwnerPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHfacility_ownership');
                    loadGraph(base_url, 'c_analytics/getFacilityLevelPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHfacility_levels');
                    loadGraph(base_url, 'c_analytics/getFacilityTypePerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHfacility_type');
                    loadGraph(base_url, 'c_analytics/getDeliveryServices/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#DeliveryReasons');
                    loadGraph(base_url,'c_analytics/getDeliveryReason/' + criteria + '/' + value + '/' + survey + '/' + survey_category,'#MainDeliveryReasons');
                    loadGraph(base_url, 'c_analytics/getServices/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#24Hr');
                    loadGraph(base_url, 'c_analytics/getHFM/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#HFM');
                    loadGraph(base_url, 'c_analytics/getBedStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/total', '#NnB');

                    break;

                case 'section-2':
                    //Section 2 MNH 
                    loadGraph(base_url, 'c_analytics/getDiarrhoeaStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHdeliveries');
                    loadGraph(base_url, 'c_analytics/getBemoncQuestion/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#BEMONCQuestions');
                    loadGraph(base_url, 'c_analytics/getBemONCReason/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#BEMONCReasons');
                    loadGraph(base_url, 'c_analytics/getCEOC/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#CEmONC');
                    loadGraph(base_url, 'c_analytics/getCSReasons/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/cs', '#CEOCReasons');
                    loadGraph(base_url, 'c_analytics/getCSReasons/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/transfusion', '#TransfusionReasons');
                    loadGraph(base_url, 'c_analytics/getNewborn/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHnewborn');
                    loadGraph(base_url, 'c_analytics/getKangarooMotherCare/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHkmc');
                    loadGraph(base_url, 'c_analytics/getDeliveries/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/total', '#delivery_preparedness');
                    loadGraph(base_url, 'c_analytics/getHIV/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHhiv');

                    break;

                case 'section-3':
                    loadGraph(base_url, 'c_analytics/getGuidelinesAvailabilityMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHguidelines');
                    loadGraph(base_url, 'c_analytics/getJobAids/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHjob_aids');
                    loadGraph(base_url, 'c_analytics/getMNHTools/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHtools');

                    break;

                case 'section-4':
                    loadGraph(base_url, 'c_analytics/getTrainedStaff/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhStaffAvailability');
                    loadGraph(base_url, 'c_analytics/getStaffRetention/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHstaffRetention');
                    loadGraph(base_url, 'c_analytics/getStaffAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHStaffTraining');

                    break;

                case 'section-5':
                    loadGraph(base_url, 'c_analytics/getMNHCommodityAvailabilityFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_availability');
                    loadGraph(base_url, 'c_analytics/getMNHCommodityAvailabilityUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_unavailability');
                    loadGraph(base_url, 'c_analytics/getMNHCommodityLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_location');
                    loadGraph(base_url, 'c_analytics/getMNHCommoditySupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#MNHcommodity_supplier');

                    break;

                case 'section-6':
                    loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey + '/consumption', '#MNHcommodity_consumption');
                    loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey + '/unavailability', '#MNHunavailability');
                    loadGraph(base_url, 'c_analytics/getCommodityUsage/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey + '/reason', '#MNHReason');

                    break;


                case 'section-7':
                         //section 7 of 8: Part I
                    loadGraph(base_url, 'c_analytics/getMNHEquipmentFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhequipment_availability');
                    loadGraph(base_url, 'c_analytics/getMNHEquipmentFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhequipment_functionality');
                    loadGraph(base_url, 'c_analytics/getMNHEquipmentLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhequipment_location');
					loadGraph(base_url, 'c_analytics/getMNHTestingSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhtesting_availability');
					loadGraph(base_url, 'c_analytics/getMNHTestingSuppliesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhtesting_supplies');
						//section 7 of 8: Part II
					loadGraph(base_url, 'c_analytics/getMNHDeliveryKitsAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhDelivery_availability');
                    loadGraph(base_url, 'c_analytics/getMNHDeliveryKitsLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhDelivery_location');
                    loadGraph(base_url, 'c_analytics/getMNHDeliveryKitsFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#mnhDelivery_functionality');
                    	//section 7 of 8: Part III
                    loadGraph(base_url, 'c_analytics/getRunningWaterAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_availability');
                    loadGraph(base_url, 'c_analytics/getRunningWaterLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_location');
                    loadGraph(base_url, 'c_analytics/getRunningWaterStorage/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhwatersource');
                    loadGraph(base_url, 'c_analytics/getmnhWaterStorage/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhwateravailability');
                    loadGraph(base_url, 'c_analytics/getMNHresourcesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_mainSource');
                    loadGraph(base_url, 'c_analytics/getWasteStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhresource_wasteDisposal');
                    loadGraph(base_url, 'c_analytics/getMNHEquipmentElectricity/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhequipment_electricity');
                    loadGraph(base_url, 'c_analytics/getMNHMainSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhelectricitysupplier');
                    //loadGraph(base_url, 'c_analytics/getMNHElectricityMainSource/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#mnhelectricitysource');
						//section 7 of 8: Other
                    loadGraph(base_url, 'c_analytics/getMNHSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHsupplies_availability');
                    loadGraph(base_url, 'c_analytics/getMNHSuppliesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#MNHsupplies_location');
                    loadGraph(base_url, 'c_analytics/getMNHSuppliers/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/', '#MNHsupplies_supplier');

                    break;

                case 'section-8':
                   	loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/community', '#community_units');
                    loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/referral', '#community_cases');
                    loadGraph(base_url, 'c_analytics/getCommunityStrategyMNH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/trained', '#imci_trainings');

                    break;


            }

            break;
        case 'ch':

            switch (section) {
                case 'section-1':
                    loadGraph(base_url, 'c_analytics/getFacilityOwnerPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#facility_owner');
                    loadGraph(base_url, 'c_analytics/getFacilityLevelPerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#facility_levels');
                    loadGraph(base_url, 'c_analytics/getFacilityTypePerCounty/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#facility_type');
                    loadGraph(base_url, 'c_analytics/getTrainedStaff/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#staff_training');
                    loadGraph(base_url, 'c_analytics/getStaffAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#staff_availability');
                    loadGraph(base_url, 'c_analytics/getStaffRetention/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#staff_retention');
					loadGraph(base_url, 'c_analytics/getIMCI/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#imci');
                    loadGraph(base_url, 'c_analytics/getHS/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#chhealth_service');
                    break;

                case 'section-2':
                    loadGraph(base_url, 'c_analytics/getGuidelinesAvailabilityCH/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#guidelines');
                    loadGraph(base_url, 'c_analytics/getTools/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#tools');
                    loadGraph(base_url, 'c_analytics/getChallengeStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#challenge');

                    break;

                case 'section-3':
                    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/cases', '#u5_register');
                    loadGraph(base_url, 'c_analytics/getDangerSigns/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#danger_signs');
                    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/treatment', '#treatment_options');
                    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/other_treatment/dia', '#other_treatment_options_dia');
                    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/other_treatment/pne', '#other_treatment_options_pne');
                    loadGraph(base_url, 'c_analytics/getTreatmentStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/other_treatment/fev', '#other_treatment_options_fev');
                    loadGraph(base_url, 'c_analytics/getIndicatorComparison/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + indicator_type, '#indicator_comparison');

                    break;

                case 'section-4':
                    loadGraph(base_url, 'c_analytics/getCHCommodityAvailabilityFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_availability');
                    loadGraph(base_url, 'c_analytics/getCHCommodityAvailabilityUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_unavailability');
                    loadGraph(base_url, 'c_analytics/getCHCommodityLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_location');
                    loadGraph(base_url, 'c_analytics/getCHCommoditySuppliers/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#commodity_supplier');
                    loadGraph(base_url, 'c_analytics/getbundlingFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_availability');
                    loadGraph(base_url, 'c_analytics/getbundlingUnavailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_unavailability');
                    loadGraph(base_url, 'c_analytics/getbundlingLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/' + survey, '#bundling_location');

                    break;

                case 'section-5':
                    loadGraph(base_url, 'c_analytics/getORTOne/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_availability');
                    loadGraph(base_url, 'c_analytics/getLocationStatistics/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_location');
                    loadGraph(base_url, 'c_analytics/getORTFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ort_reason');

                    break;

                case 'section-6':
                    loadGraph(base_url, 'c_analytics/getCHEquipmentFrequency/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#equipment_availability');
                    loadGraph(base_url, 'c_analytics/getCHEquipmentLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#equipment_location');
                    loadGraph(base_url, 'c_analytics/getCHEquipmentFunctionality/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#equipment_functionality');

                    break;

                case 'section-7':
                    loadGraph(base_url, 'c_analytics/getCHSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#supplies_availability');
                    loadGraph(base_url, 'c_analytics/getCHSuppliesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#supplies_location');
                    loadGraph(base_url, 'c_analytics/getCHTestingSupplies/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#testing_supplies');
                    loadGraph(base_url, 'c_analytics/getCHSuppliesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#ch_suppliers');
					loadGraph(base_url, 'c_analytics/getCHTestingSuppliesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#testingSuppliesAvailability');
                    break;

                case 'section-8':
                    loadGraph(base_url, 'c_analytics/getCHresourcesAvailability/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#resource_availability');
                    loadGraph(base_url, 'c_analytics/getCHResourcesLocation/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#resource_location');
                    loadGraph(base_url, 'c_analytics/getCHresourcesSupplier/' + criteria + '/' + value + '/' + survey + '/' + survey_category, '#resource_suppliers');

                    break;

                case 'section-9':
                    loadGraph(base_url, 'c_analytics/getCommunityStrategyCH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/community', '#chcommunity_units');
                    loadGraph(base_url, 'c_analytics/getCommunityStrategyCH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/referral', '#chCases');
                    loadGraph(base_url, 'c_analytics/getCommunityStrategyCH/' + criteria + '/' + value + '/' + survey + '/' + survey_category + '/trained', '#chIMCITraining');

                    break;
            }

            break;
    }


}
