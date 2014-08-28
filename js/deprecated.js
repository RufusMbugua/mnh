	//Load Initial Graphs
	$('#graph_5').load(base_url + 'c_analytics/getFacilityOwnerPerCounty/' + county);
	$('#graph_6').load(base_url + 'c_analytics/getFacilityLevelPerCounty/' + county);


	$('#graph_40').load(base_url + 'c_analytics/case_summary/Cases');
	$('#graph_41').load(base_url + 'c_analytics/case_summary/Classification');


	$('#graph_42').load(base_url + 'c_analytics/guidelines_summary/2012%20IMCI');
	$('#graph_43').load(base_url + 'c_analytics/guidelines_summary/ICCM');
	$('#graph_44').load(base_url + 'c_analytics/guidelines_summary/ORT%20Corner');
	$('#graph_45').load(base_url + 'c_analytics/guidelines_summary/Paediatric%20Protocol');
	//Tools Summary
	$('#graph_46').load(base_url + 'c_analytics/tools_summary/Under%205%20register');
	ort = 'ORT Corner register';
	ort = encodeURI(ort);
	$('#graph_47').load(base_url + 'c_analytics/tools_summary/' + ort);
	$('#graph_48').load(base_url + 'c_analytics/tools_summary/Mother%20Child%20Booklet');
	$('#graph_49').load(base_url + 'c_analytics/getFacilityLevelAll/' + survey);
	$('#graph_60').load(base_url + 'c_analytics/getFacilityOwnerAll/' + survey);

	$('#graph_50').load(base_url + 'c_analytics/training_summary/ICCM');
	$('#graph_51').load(base_url + 'c_analytics/training_summary/IMCI');
	$('#graph_52').load(base_url + 'c_analytics/training_summary/Enhanced%20Diarrhoea%20Management');

	$('#graph_70').load(base_url + 'c_analytics/training_summaryMnh/BEmONC');
	$('#graph_71').load(base_url + 'c_analytics/training_summaryMnh/PNC');
	$('#graph_72').load(base_url + 'c_analytics/training_summaryMnh/Essential%20Newborn%20care');
	$('#graph_73').load(base_url + 'c_analytics/training_summaryMnh/SBM-R');
	$('#graph_74').load(base_url + 'c_analytics/training_summaryMnh/FANC');
	$('#graph_75').load(base_url + 'c_analytics/training_summaryMnh/PAC');
	$('#graph_76').load(base_url + 'c_analytics/training_summaryMnh/MPDSR');
	$('#graph_77').load(base_url + 'c_analytics/training_summaryMnh/UBT');


	$('#graph_78').load(base_url + 'c_analytics/guidelines_summaryMNH/National%20Roadmap%20MMR');
	$('#graph_79').load(base_url + 'c_analytics/guidelines_summaryMNH/PMTCT%20guidelines');
	$('#graph_80').load(base_url + 'c_analytics/guidelines_summaryMNH/IYCF%20policy%20statement');
	$('#graph_81').load(base_url + 'c_analytics/guidelines_summaryMNH/Quality%20Obstetric%20and%20Prenatal%20Care');
	$('#graph_82').load(base_url + 'c_analytics/guidelines_summaryMNH/Baby%20Friendly%20Hospital%20Initiative');
	$('#graph_83').load(base_url + 'c_analytics/guidelines_summaryMNH/Post%20Abortion%20Guidelines');



	$('select#county_select').change(function() {
		countyClicked = $(this).val(); //$('select#county_select option:selected').text();
		countyClicked = encodeURIComponent(countyClicked);

		window.location.href = base_url + 'c_analytics/setActive/' + countyClicked + '/' + survey;
	});

	/**
	 *Assign County and Facility
	 */
	currentChart = '';
	district = '';

	facility = '';

	//Assign Secondary Filter
	filter = 'SevereDehydration';
	//Hide Secondary Filter
	$('.secondary-filter').hide();


	//Load District Names
	$('select#fi_district').load(base_url + 'c_analytics/getSpecificDistrictNames');
	//$('select#fi_district2').load(base_url+'c_analytics/getSpecificDistrictNames');

	//Change Event for District Select
	$('select#fi_district').change(function() {

		$('#graph_3').empty();
		$('#graph_4').empty();
		district = $('select#fi_district option:selected').text();
		district = encodeURIComponent(district);
		//alert(currentChart+district+'/ch/'+extraStat);
		$(currentDiv).load(currentChart + 'district/' + district + '/' + survey + '/' + extraStat);
		//$('#graph_10').load(currentChart+'district/'+district+'/ch/'+extraStat);
		$('select#fi_facility').load(base_url + 'c_analytics/getFacilitiesByDistrictOptions/' + district + '/' + survey);
	});

	$('select#fi_facility').change(function() {
		$('#graph_4').empty();
		facility = $('select#fi_facility option:selected').attr('value');
		$('#graph_4').load(currentChart + 'facility/' + facility + '/' + survey + '/' + extraStat);
	});
	$('#facility_list').click(function() {
		window.open(base_url + 'c_analytics/getFacilityListForNo/district/' + district + '/' + survey + '/' + noList);

	});

	$('#facility_list_never').click(function() {
		window.open(base_url + 'c_analytics/getFacilityListForNever/district/' + district + '/' + survey + '/' + neverList);

	});

	$('#facility_list_no_mnh').click(function() {
		window.open(base_url + 'c_analytics/getFacilityListForNoMNH/district/' + district + '/' + survey + '/' + neverList);

	});
	$('#facility_list_commodity_supplies_county').click(function() {
		window.open(base_url + 'c_analytics/commodity_supplies_summary/county/' + county + '/' + survey);

	});
	$('#facility_list_commodity_supplies').click(function() {
		window.open(base_url + 'c_analytics/commodity_supplies_summary/district/' + district + '/' + survey);

	});

$('ul.sub li').click(function() {
			$('#facility_list').hide();
			$('#facility_list_never').hide();
			$('#facility_list_commodity_supplies_county').hide();
			$('#facility_list_commodity_supplies').hide();
			$('#facility_list_no_mnh').hide();
			$('#reporting-parent').hide();
			$('#analytics-page').show();
			click = 1;
			subID = $(this).attr('id');
			//parentDiv = $('#'+subID+' a').parents('ul');
			//alert(parentDiv);
			//alert();
			$('ul.sub li').removeClass('active');
			$('#' + subID).addClass('active');
			$('.has-sub.start').removeClass('active');
			$('.has-sub.start a').remove('span');

			if (subID == 'facilities') {
				$('.span6').hide();
				$('#span1').show();
				$('#span1').animate({
					height: '200%',
					width: '100%'
				});
				$('#graph_2').animate({
					width: '2000px'
				});
				$('#port2').animate({
					height: '400px',
					width: '100%'
				});
			} else {
				$('.span6').css({
					'height': '100%',
					'width': '48.717948717948715%'
				});
				$('#graph_2').css({
					'width': '100%'
				});
				$('.span6').show();

			}

			if (subID == 'caseTreatment') {
				$('.secondary-filter').show();
			} else {
				$('.secondary-filter').hide();
			}
			//Change Event for Secondary Filter
			$('select.secondary-filter').change(function() {
				$('#graph_1').empty();
				$('#graph_2').empty();
				$('#graph_3').empty();
				$('#graph_4').empty();
				thisID = $(this).attr('id');
				filter = $('select#' + thisID + ' option:selected').attr('value');
				$('#graph_1').load(base_url + 'c_analytics/getDiarrhoeaCaseTreatment/national/n/' + survey + '/' + filter);
				$('#graph_2').load(base_url + 'c_analytics/getDiarrhoeaCaseTreatment/county/' + county + '/' + survey + '/' + filter);
				if (thisID == 'county') {
					$('select.secondary-filter#district').val(filter);
				} else {
					$('select.secondary-filter#county').val(filter);
				}

			});


			//Empty Graphs
			$('#graph_1').empty();
			$('#graph_2').empty();
			$('#graph_3').empty();
			$('#graph_4').empty();
			//reload district
			//Load District Names
			$('select#fi_district').load(base_url + 'c_analytics/getSpecificDistrictNames');

			/*	switch (subID) {

			/*
			 * Overview Statistics
			 */
			/*		case 'facilities':

				$('span.statistic').text('Facilities');
				$('#overview-statistics-parent').addClass('active');
				$('#overview-statistics-parent a').append('<span class="selected"></span>');

				$('#graph_1').load(base_url + 'c_analytics/getCountyFacilities/1');
				$('#graph_2').load(base_url + 'c_analytics/getCountyFacilitiesByOwner/' + county);
				break;
				/*
				 * Facility Statistics
				 */
			/*	case 'communityStrategy':
				currentChart = base_url + 'c_analytics/getCommunityStrategy/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Community Strategy');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');

				$('#graph_1').load(base_url + 'c_analytics/getCommunityStrategy/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommunityStrategy/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommunityStrategy/county/'+county+'/ch/'+extraStat);
				break;

			case 'guidelines':
				noList = 'GuidelinesAvailability';
				$('#facility_list').show();
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getGuidelinesAvailability/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Guidelines');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getGuidelinesAvailability/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getGuidelinesAvailability/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getGuidelinesAvailability/county/'+county+'/ch/'+extraStat);

				break;

			case 'training':
				$('#facility_list').show();
				appendToTitle = ' in the last 2 years';
				currentChart = base_url + 'c_analytics/getTrainedStaffOne/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Training');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getTrainedStaffOne/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getTrainedStaffOne/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getTrainedStaff/county/'+county+'/ch/'+extraStat);
				break;

			case 'childrenServices':
				noList = 'ServicesOffered';
				$('#facility_list').show();
				appendToTitle = ' to children with diarrhoea';
				currentChart = base_url + 'c_analytics/getChildrenServices/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Services Offered');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getChildrenServices/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getChildrenServices/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getChildrenServices/county/'+county+'/ch/'+extraStat);
				break;

			case 'dangerSigns':
				noList = 'DangerSigns';
				$('#facility_list').show();
				appendToTitle = ' assessed in ongoing session for a child with diarrhoea';
				currentChart = base_url + 'c_analytics/getDangerSigns/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Danger Signs');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getDangerSigns/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getDangerSigns/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getDangerSigns/county/'+county+'/ch/'+extraStat);
				break;

			case 'actionsPerformed':
				noList = 'ActionsPerformed';
				$('#facility_list').show();
				appendToTitle = ' in ongoing sessions for a child with diarrhoea';
				currentChart = base_url + 'c_analytics/getActionsPerformed/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Actions Performed');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getActionsPerformed/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getActionsPerformed/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getActionsPerformed/county/'+county+'/ch/'+extraStat);

				break;

			case 'counselGiven':
				noList = 'CounselGiven';
				$('#facility_list').show();
				appendToTitle = ' in ongoing session for a child with diarrhoea';
				currentChart = base_url + 'c_analytics/getCounselGiven/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Counsel Given');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCounselGiven/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCounselGiven/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCounselGiven/county/'+county+'/ch/'+extraStat);
				break;

			case 'tools':
				noList = 'Tools';
				$('#facility_list').show();
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getTools/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Tools in a given Unit');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getTools/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getTools/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getTools/county/'+county+'/ch/'+extraStat);
				break;

				/*
				 * ------End of Facility Statistics
				 */
			/*
			 * Commodities
			 */
			/*case 'commodityFrequency':
				$('#facility_list_never').show();
				neverList = 'Commodity';
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityFrequency/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Availability');
				$('#commodities-parent').addClass('active');
				$('#commodities-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityFrequency/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityFrequency/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommodityAvailabilityFrequency/county/'+county+'/ch/'+extraStat);
				break;

			case 'commodityUnavailability':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityUnavailability/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Reasons For Unavailability');
				$('#commodities-parent').addClass('active');
				$('#commodities-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityUnavailability/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityUnavailability/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommodityAvailabilityUnavailability/county/'+county+'/ch/'+extraStat);
				break;

			case 'commodityLocation':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityLocation/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Location');
				$('#commodities-parent').addClass('active');
				$('#commodities-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityLocation/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityLocation/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommodityAvailabilityLocation/county/'+county+'/ch/'+extraStat);
				break;

			case 'commodityQuantities':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityQuantities/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Quantities');
				$('#commodities-parent').addClass('active');
				$('#commodities-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityQuantities/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityQuantities/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommodityAvailabilityQuantities/county/'+county+'/ch/'+extraStat);
				break;

			case 'commoditySuppliers':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCHCommoditySupplier/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Suppliers');
				$('#commodities-parent').addClass('active');
				$('#commodities-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCHCommoditySupplier/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCHCommoditySupplier/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCHCommoditySupplier/county/'+county+'/ch/'+extraStat);
				break;
				/*
				 * ------End of Commodities
				 */
			/*
			 * Diarrhoea Cases
			 */
			/*case 'caseNumbers':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getDiarrhoeaCaseNumbers/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Case Numbers');
				$('#diarrhoea-cases-parent').addClass('active');
				$('#diarrhoea-cases-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getDiarrhoeaCaseNumbers/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getDiarrhoeaCaseNumbers/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getDiarrhoeaCaseNumbers/county/'+county+'/ch/'+extraStat);
				break;

			case 'caseTreatment':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getDiarrhoeaCaseTreatment/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Case Treatment');
				$('#diarrhoea-cases-parent').addClass('active');
				$('#diarrhoea-cases-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getDiarrhoeaCaseTreatment/national/n/ch/' + filter);
				$('#graph_2').load(base_url + 'c_analytics/getDiarrhoeaCaseTreatment/county/' + county + '/ch/' + filter);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getDiarrhoeaCaseTreatment/county/'+county+'/ch/'+extraStat);
				break;
				/*
				 * ------End of Diarrhoea Cases
				 */
			/*
			 * ORT Corner
			 */
			/*case 'ORTAssessment':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getORTCornerAssessment/';
				currentDiv = '#graph_3';
				$('span.statistic').text('ORT Assessment');
				$('#ORT-Corner-parent').addClass('active');
				$('#ORT-Corner-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getORTCornerAssessment/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getORTCornerAssessment/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getORTCornerAssessment/county/'+county+'/ch/'+extraStat);
				break;

			case 'ORTEquipmentAvailability':
				$('#facility_list_never').show();
				neverList = 'ORT';
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getORTCornerEquipmentFrequency/';
				currentDiv = '#graph_3';
				$('span.statistic').text('ORT Equipment Availability');
				$('#ORT-Corner-parent').addClass('active');
				$('#ORT-Corner-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getORTCornerEquipmentFrequency/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getORTCornerEquipmentFrequency/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getORTCornerEquipmentFrequency/county/'+county+'/ch/'+extraStat);
				break;

			case 'ORTEquipmentLocation':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getORTCornerEquipmentLocation/';
				currentDiv = '#graph_3';
				$('span.statistic').text('ORT Equipment Location');
				$('#ORT-Corner-parent').addClass('active');
				$('#ORT-Corner-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getORTCornerEquipmentLocation/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getORTCornerEquipmentLocation/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getORTCornerEquipmentLocation/county/'+county+'/ch/'+extraStat);
				break;

			case 'ORTEquipmentFunctionality':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getORTCornerEquipmentAvailability/';
				currentDiv = '#graph_3';
				$('span.statistic').text('ORT Equipment Functionality');
				$('#ORT-Corner-parent').addClass('active');
				$('#ORT-Corner-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getORTCornerEquipmentAvailability/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getORTCornerEquipmentAvailability/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getORTCornerEquipmentAvailability/county/'+county+'/ch/'+extraStat);
				break;

			case 'ORTSupplies':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getORTCornerSupplies/';
				currentDiv = '#graph_3';
				$('span.statistic').text('ORT Supplies');
				$('#ORT-Corner-parent').addClass('active');
				$('#ORT-Corner-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getORTCornerSupplies/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getORTCornerSupplies/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getORTCornerSupplies/county/'+county+'/ch/'+extraStat);
				break;
				/*
				 * ------End of ORT Corner
				 */
			/*
			 * Supplies
			 */
			/*case 'suppliesFrequency':
				$('#facility_list_never').show();
				neverList = 'Water';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				currentChart = base_url + 'c_analytics/getSuppliesFrequency/';
				$('span.statistic').text('Supplies Availability');
				$('#supplies-parent').addClass('active');
				$('#supplies-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getSuppliesFrequency/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getSuppliesFrequency/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'suppliesLocation':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getSuppliesLocation/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Supplies Location');
				$('#supplies-parent').addClass('active');
				$('#supplies-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getSuppliesLocation/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getSuppliesLocation/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'suppliesSuppliers':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCHSuppliesSupplier/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Suppliers');
				$('#supplies-parent').addClass('active');
				$('#supplies-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCHSuppliesSupplier/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCHSuppliesSupplier/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;
				/*
				 * Resources
				 */

			/*case 'resourceAvailability':
				$('#facility_list_never').show();
				neverList = 'Resources';
				currentChart = base_url + 'c_analytics/getResourcesFrequency/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Hardware Resource Availability');
				$('#resources-parent').addClass('active');
				$('#resources-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getResourcesFrequency/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getResourcesFrequency/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'resourceLocation':
				currentChart = base_url + 'c_analytics/getResourceLocation/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Hardware Resource Location');
				$('#resources-parent').addClass('active');
				$('#resources-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getResourceLocation/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getResourceLocation/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

				/**
				 * MNH Section
				 */
			/*case 'nursesDeployed':
				currentChart = base_url + 'c_analytics/getNursesDeployed/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				extraStat = 'nur';
				$('span.statistic').text('Nurses Deployed');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getNursesDeployed/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getNursesDeployed/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'beds':
				currentChart = base_url + 'c_analytics/getBeds/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Beds in Facility');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getBeds/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getBeds/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'services':
				currentChart = base_url + 'c_analytics/getService/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Services in Facility');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getService/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getService/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'hfm':
				currentChart = base_url + 'c_analytics/getHFM/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Health Facility Management');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getHFM/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getHFM/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;
			case 'deliveries':
				currentChart = base_url + 'c_analytics/getDeliveries/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				neverList = 'prep';
				$('#facility_list_no_mnh').show();
				$('span.statistic').text('Deliveries');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getDeliveries/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getDeliveries/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'hiv':
				currentChart = base_url + 'c_analytics/getQuestionStatistics/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				neverList = 'hiv';
				extraStat = 'hiv';
				$('#facility_list_no_mnh').show();
				$('span.statistic').text('HIV Testing');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getQuestionStatistics/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getQuestionStatistics/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'newborn':
				currentChart = base_url + 'c_analytics/getQuestionStatistics/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				neverList = extraStat = 'newb';
				$('#facility_list_no_mnh').show();
				$('span.statistic').text('Newborn Care');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getQuestionStatistics/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getQuestionStatistics/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'kmc':
				currentChart = base_url + 'c_analytics/getQuestionStatistics/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				neverList = extraStat = 'kang';
				$('#facility_list_no_mnh').show();
				$('span.statistic').text('Kangaroo Mother Care');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getQuestionStatistics/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getQuestionStatistics/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'jobaids':
				currentChart = base_url + 'c_analytics/getQuestionStatistics/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				neverList = extraStat = 'job';
				$('#facility_list_no_mnh').show();
				$('span.statistic').text('Job Aids');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getQuestionStatistics/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getQuestionStatistics/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'guidelinesmnh':
				currentChart = base_url + 'c_analytics/getGuidelinesAvailabilityMNH/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				neverList = extraStat = 'guide';
				$('#facility_list_no_mnh').show();
				$('span.statistic').text('Guidelines Availability');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getGuidelinesAvailabilityMNH/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getGuidelinesAvailabilityMNH/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'communitystrategy':
				currentChart = base_url + 'c_analytics/getCommunityStrategyMNH/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Community Strategy');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommunityStrategyMNH/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommunityStrategyMNH/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'staffTrainingMnh':
				currentChart = base_url + 'c_analytics/getTrainedStaffOne/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Staff Training');
				$('#facility-statistics-parent').addClass('active');
				$('#facility-statistics-parent a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getTrainedStaffOne/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getTrainedStaffOne/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

				/*
				 * Commodities
				 */
			/*case 'commodityFrequencyMnh':
				$('#facility_list_never').show();
				neverList = 'Commodity';
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityFrequency/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Availability');
				$('#commodities-parent-mnh').addClass('active');
				$('#commodities-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityFrequency/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityFrequency/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommodityAvailabilityFrequency/county/'+county+'/mnh'+extraStat);
				break;

			case 'commodityUnavailabilityMnh':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityUnavailability/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Reasons For Unavailability');
				$('#commodities-parent-mnh').addClass('active');
				$('#commodities-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityUnavailability/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityUnavailability/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommodityAvailabilityUnavailability/county/'+county+'/mnh'+extraStat);
				break;

			case 'commodityLocationMnh':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityLocation/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Location');
				$('#commodities-parent-mnh').addClass('active');
				$('#commodities-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityLocation/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityLocation/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getCommodityAvailabilityLocation/county/'+county+'/mnh'+extraStat);
				break;

			case 'commodityQuantitiesMnh':
				appendToTitle = ' ';
				$('#facility_list_commodity_supplies_county').show();
				$('#facility_list_commodity_supplies').show();
				currentChart = base_url + 'c_analytics/getCommodityAvailabilityQuantities/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Quantities');
				$('#commodities-parent-mnh').addClass('active');
				$('#commodities-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCommodityAvailabilityQuantities/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCommodityAvailabilityQuantities/county/' + county + '/mnh/' + extraStat);
				break;

			case 'commoditySuppliersMnh':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCHCommoditySupplier/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Commodity Suppliers');
				$('#commodities-parent-mnh').addClass('active');
				$('#commodities-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCHCommoditySupplier/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCHCommoditySupplier/county/' + county + '/mnh/' + extraStat);
				break;
				/*
				 * ------End of Commodities
				 */

			/*
			 * Supplies
			 */
			/*case 'suppliesFrequencyMnh':
				$('#facility_list_never').show();
				neverList = 'Water';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				currentChart = base_url + 'c_analytics/getSuppliesFrequency/';
				$('span.statistic').text('Supplies Availability');
				$('#supplies-parentMnh').addClass('active');
				$('#supplies-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getSuppliesFrequency/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getSuppliesFrequency/county/' + county + '/mnh/' + extraStat);
				break;

			case 'suppliesLocationMnh':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getSuppliesLocation/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Supplies Location');
				$('#supplies-parentMnh').addClass('active');
				$('#supplies-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getSuppliesLocation/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getSuppliesLocation/county/' + county + '/mnh/' + extraStat);
				break;

			case 'suppliesSuppliersMnh':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCHSuppliesSupplier/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Suppliers');
				$('#supplies-parentMnh').addClass('active');
				$('#supplies-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCHSuppliesSupplier/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCHSuppliesSupplier/county/' + county + '/mnh/' + extraStat);
				break;

				/**
				 *Signal functions
				 */
			/*case 'cemonc':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCEOC/';
				currentDiv = '#graph_3';
				neverList = 'ceoc';
				$('#facility_list_no_mnh').show();
				$('span.statistic').text('CEmONC Signal Function');
				$('#signal-parentMnh').addClass('active');
				$('#signal-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCEOC/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCEOC/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/mnh'+extraStat);
				break;

			case 'cemoncReason':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getCEOCReason/';
				currentDiv = '#graph_3';
				$('span.statistic').text('CEmONC Signal Function Challenges');
				$('#signal-parentMnh').addClass('active');
				$('#signal-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getCEOCReason/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getCEOCReason/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/mnh'+extraStat);
				break;

			case 'bemonc':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getBEMONC/';
				currentDiv = '#graph_3';
				$('span.statistic').text('BEmONC Signal Function');
				$('#signal-parentMnh').addClass('active');
				$('#signal-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getBEMONC/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getBEMONC/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/mnh'+extraStat);
				break;

			case 'bemoncReason':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getBEMONCReason/';
				currentDiv = '#graph_3';
				$('span.statistic').text('BEmONC Signal Function Challenges');
				$('#signal-parentMnh').addClass('active');
				$('#signal-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getBEMONCReason/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getBEMONCReason/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/mnh'+extraStat);
				break;

				/*
				 * Equipment Corner
				 */


			/*case 'equipmentFrequency':
				$('#facility_list_never').show();
				neverList = 'ORT';
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getEquipmentFrequency/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Equipment Availability');
				$('#equipments-parent-mnh').addClass('active');
				$('#equipments-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getEquipmentFrequency/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getEquipmentFrequency/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getEquipmentFrequency/county/'+county+'/ch/'+extraStat);
				break;

			case 'equipmentLocation':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getEquipmentLocation/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Equipment Location');
				$('#equipments-parent-mnh').addClass('active');
				$('#equipments-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getEquipmentLocation/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getEquipmentLocation/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getEquipmentLocation/county/'+county+'/ch/'+extraStat);
				break;

			case 'equipmentFunctionality':
				$('#facility_list_commodity_supplies_county').show();
				$('#facility_list_commodity_supplies').show();
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getEquipmentFunctionality/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Equipment Functionality');
				$('#equipments-parent-mnh').addClass('active');
				$('#equipments-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getEquipmentFunctionality/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getEquipmentFunctionality/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getEquipmentAvailability/county/'+county+'/ch/'+extraStat);
				break;

			case 'equipmentFunctionality':
				appendToTitle = ' ';
				currentChart = base_url + 'c_analytics/getEquipmentAvailability/';
				currentDiv = '#graph_3';
				$('span.statistic').text('Equipment Functionality');
				$('#equipments-parent-mnh').addClass('active');
				$('#equipments-parent-mnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getEquipmentAvailability/national/n/ch/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getEquipmentAvailability/county/' + county + '/ch/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getEquipmentAvailability/county/'+county+'/ch/'+extraStat);
				break;

			case 'hardwareFrequencyMnh':
				$('#facility_list_never').show();
				neverList = 'Resources';
				currentChart = base_url + 'c_analytics/getResourcesFrequency/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Hardware Resource Availability');
				$('#hardware-parentMnh').addClass('active');
				$('#hardware-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getResourcesFrequency/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getResourcesFrequency/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'resourcesFrequencyMnh':

				currentChart = base_url + 'c_analytics/getRunningWaterFrequency/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Running Water Availability');
				$('#resources-parentMnh').addClass('active');
				$('#resources-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getRunningWaterFrequency/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getRunningWaterFrequency/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

			case 'resourcesLocationMnh':
				currentChart = base_url + 'c_analytics/getRunningWaterLocation/';
				appendToTitle = ' ';
				currentDiv = '#graph_3';
				$('span.statistic').text('Running Water Location');
				$('#resources-parentMnh').addClass('active');
				$('#resources-parentMnh a').append('<span class="selected"></span>');
				$('#graph_1').load(base_url + 'c_analytics/getRunningWaterLocation/national/n/mnh/' + extraStat);
				$('#graph_2').load(base_url + 'c_analytics/getRunningWaterLocation/county/' + county + '/mnh/' + extraStat);
				//$('#graph_2').load('<?php //echo base_url();?>c_analytics/getResources/county/'+county+'/ch/'+extraStat);
				break;

		}


		//Set Title Text

		smallText = $('ul.sub li.active a').text();
		//$('h3.page-title').text($('li.has-sub.start.open a span.title').text());
		$('h3.page-title').text(smallText + appendToTitle);
		$('#breadcrumb-title').text($('li.has-sub.start.open a span.title').text());
		$('#breadcrumb-sub-title').text(smallText);
	});


	//Bootbox
	$("#district_compare").click(function() {
		compare = 'district';
		//Load fields
		$('select#compare_1').load(base_url + 'c_analytics/getSpecificDistrictNames');
		$('select#compare_2').load(base_url + 'c_analytics/getSpecificDistrictNames');
		$("#graph_10").empty();
		$("#graph_11").empty();
		$('#compareModal').modal('show');

		//district = encodeURI(district);
		statistic = $('ul.sub li.active a').text();
		$('#compare_title').text(statistic);
		$('#compare_title_1').text(statistic);
		$('#compare_title_2').text(statistic);
		$('#graph_10').delay(4000).queue(function(nxt) {
			district = decodeURI(district);
			$("select#compare_1").val(district);
			district = encodeURI(district);
			$('#graph_10').load(currentChart + 'district/' + district + '/' + survey + '/' + extraStat);
			nxt();
		});

		//clearInterval(loadChart);



		//$('').load('');
		//bootbox.alert().load("<div>HELLO</div>");
	});*/

			$("#county_compare").click(function() {
				compare = 'county';
				$('select#compare_1').empty();
				$('select#compare_2').empty();
				$('select#compare_1').load(base_url + 'c_analytics/getReportingCountyList/' + survey);
				$('select#compare_2').load(base_url + 'c_analytics/getReportingCountyList/' + survey);
				$("#graph_10").empty();
				$("#graph_11").empty();
				$('#compareModal').modal('show');

				//county = decodeURI(county);
				statistic = $('ul.sub li.active a').text();
				$('#compare_title').text(statistic);
				$('#compare_title_1').text(statistic);
				$('#compare_title_2').text(statistic);
				$('#graph_10').delay(4000).queue(function(nxt) {
					county = decodeURI(county);
					$("select#compare_1").val(county);
					county = encodeURI(county);
					$('#graph_10').load(currentChart + 'county/' + county + '/' + survey + '/' + extraStat);
					nxt();
				});
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

			

			//Summary
			$('#mnh-form').click(function() {
				window.open(base_url + 'c_pdf/loadPDF/mnh');
			});
			$('#mch-form').click(function() {
				window.open(base_url + 'c_pdf/loadPDF/mch');
			});
			$('#hcw-form').click(function() {
				window.open(base_url + 'c_pdf/loadPDF/hcw');
			});

			$('#mnh-completed').click(function() {
				window.open(base_url + 'c_statistics/reportingFacilitiesNew/complete/mnh/baseline/');
			});
			$('#mnh-partially').click(function() {
				window.open(base_url + 'c_statistics/reportingFacilitiesNew/partial/mnh/baseline/');
			});