/**
 * [runGraph description]
 * @param  {[type]} container             [description]
 * @param  {[type]} chart_title           [description]
 * @param  {[type]} chart_stacking        [description]
 * @param  {[type]} chart_type            [description]
 * @param  {[type]} chart_categories      [description]
 * @param  {[type]} chart_series          [description]
 * @param  {[type]} chart_drilldown       [description]
 * @param  {[type]} chart_length          [description]
 * @param  {[type]} chart_width           [description]
 * @param  {[type]} chart_margin          [description]
 * @param  {[type]} color_scheme          [description]
 * @param  {[type]} chart_label_rotation  [description]
 * @param  {[type]} chart_legend_floating [description]
 * @return {[type]}                       [description]
 */
function runGraph(container, chart_title, chart_stacking, chart_type, chart_categories, chart_series, chart_drilldown, chart_length, chart_width, chart_margin, color_scheme, chart_label_rotation, chart_legend_floating) {
    file_name = container.replace('#', '');
    file_name = file_name.replace('_', ' ');
    $('#' + container).highcharts({
        colors: color_scheme,
        /*exporting: {
            filename: file_name
        },*/
        chart: {
            zoomType: 'x',
            height: chart_length,
            width: chart_width,
            type: chart_type,
            marginBottom: chart_margin
            /*events: {
                load: function () {
                    var ch = this;
                    setTimeout(function(){
                        ch.exportChart();
                    },1);
                }
            }*/
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: chart_categories,
            labels: {
                rotation: chart_label_rotation
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: chart_title,
                align: 'high'
            },
            labels: {
                overflow: 'justify',
                style: {
                    'word-break': 'break-all'
                }
            }
        },
        tooltip: {
            formatter: function() {
                if (typeof this.series.options.stack != 'undefined') {
                    return this.series.name + '<i>(' + this.series.options.stack + ')</i><br/>' + this.point.category + ' : <b>' + this.y + '</b>';
                } else if (typeof this.point.category == 'undefined') {
                    return this.point.name + ' : ' + this.y
                } else {
                    return this.point.category + '<br/>' + this.series.name + ' : <b>' + this.y + '</b>';

                }
                /**
 * else if(typeof this.point.category != 'undefined' && this.point.category.length>0) {
                    return this.point.category + '<br/>' + this.series.name + ' : <b>' + this.y + '</b>';

                } else{
 return this.x.name + ' : <b>' + this.y + '</b>';
                }
 */
            },
            followPointer: true

        },

        plotOptions: {
            series: {
                stacking: chart_stacking
            },
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true,
                tooltip: {
                    formatter: function() {

                        return this.series.name + ' : <b>' + this.y + '</b>';

                    }

                },
                followPointer: true
            },
            bar: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        if (this.y != 0 && chart_stacking == 'percent') {
                            return Math.round(this.percentage) + '%';
                        } else {
                            return this.value;
                        }
                    },
                    color: 'white'
                },
                events: {
                    legendItemClick: function() {
                        return false; // <== returning false will cancel the default action
                    }
                }
            },
        },
        legend: {
            layout: 'horizontal',
            align: 'left',
            floating: true,
            borderWidth: 1,
            backgroundColor: '#FFFFFF',
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: chart_series,
        drilldown: {
            series: chart_drilldown
        }
    });
}

/**
 * [loadGraph description]
 * @param  {[type]} base_url      [description]
 * @param  {[type]} function_url  [description]
 * @param  {[type]} graph_section [description]
 * @return {[type]}               [description]
 */
function loadGraph(base_url, function_url, graph_section) {

    $.ajax({
        url: base_url + function_url,
        beforeSend: function(xhr) {
            $(graph_section).empty();
            $(graph_section).append('<div class="loader" >Loading...</div>');
        },
        success: function(data) {
            //console.log(data);
            obj = jQuery.parseJSON(data);
            $(graph_section).empty();
            if (graph_section == '#MNHsupplies_location') {
                console.log(obj.chart_series);
            }
            if (obj.chart_series != null && obj.chart_series[0] != null) {
                $(graph_section).append('<div id="' + obj.container + '" ></div>');
                $(graph_section).parent().parent().find('.portlet-title h6 .sizer').attr('data-url', function_url);
                $(graph_section).parent().parent().find('.portlet-title h6 .sizer').attr('data-for', obj.data_for);
                $(graph_section).parent().parent().find('.portlet-title h6 .sizer').attr('data-parent', obj.data_parent);
                $(graph_section).parent().parent().find('.portlet-title h6 .sizer').attr('data-statistic', obj.statistics);
                graph_text = $(graph_section).parent().parent().find('.portlet-title h6 .graph-title').text();
                $(graph_section).parent().parent().find('.portlet-title h6 .sizer').attr('data-text', graph_text);
                runGraph(obj.container, obj.chart_title, obj.chart_stacking, obj.chart_type, obj.chart_categories, obj.chart_series, obj.chart_drilldown, obj.chart_length, obj.chart_width, obj.chart_margin, obj.color_scheme, obj.chart_label_rotation, obj.chart_legend_floating);
            } else {
                $(graph_section).append('<div class="null_message"><i class="fa fa-exclamation-triangle"></i>No Data Found</div>');

            }
        },
        error: function(xhr) {
            $(graph_section).empty();
            $(graph_section).append('<div class="null_message"><i class="fa fa-exclamation-triangle"></i>Process Interrupted</div>');
        }
    });
}

function loadFacilityRawData(base_url, function_url, container) {
    $.ajax({
        url: base_url + function_url + '/table',
        beforeSend: function(xhr) {
            $(container).empty();
            $(container).append('<div class="loader" >Loading...</div>');
        },
        success: function(data) {
            $(container).empty();
            $(container).append(data);
            $(document).trigger('datatable_loaded');
            $('#pdf').attr('data-url', base_url + function_url + '/pdf');
            $('#excel').attr('data-url', base_url + function_url + '/excel');
        }

    });
    $('h6.table-header > span > button').click(function() {
        alert('clicked');
        url = $(this).attr('data-url');
        window.open(url);
    });

}

/**
 * [getCountryInfo description]
 * @param  {[type]} country [description]
 * @return {[type]}         [description]
 */
function getCountryInfo(country) {
    var country_code = '';
    $.ajax({
        async: false,
        url: 'http://localhost/mnh/assets/data/countries.json',
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            //console.log(data);

            obj = jQuery.parseJSON(data);
            $.each(obj, function(k, v) {
                if (v["name"] == country) {
                    //console.log(v.callingCode[0])
                    country_code = v.callingCode[0];

                }
            });

        }
    });
    return country_code;

}

/**
 * [runNotification description]
 * @param  {[type]} base_url     [description]
 * @param  {[type]} function_url [description]
 * @param  {[type]} messsage     [description]
 * @return {[type]}              [description]
 */
function runNotification(base_url, function_url, messsage) {
    var period = '';
    $.ajax({
        //url: base_url + function_url,
        url: base_url + function_url,
        async: false,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            //console.log(data);die;
            obj = jQuery.parseJSON(data);
            $.each(obj, function(k, v) {
                //console.log(v.cl_country);
                //console.log(getCountryInfo(v.cl_country));
                phoneNumber = encodeURIComponent(getCountryInfo(v.cl_country) + v.cl_phone_number);
                email = encodeURIComponent(v.cl_email_address);
                today = new Date();
                hours = today.getHours();
                //console.log(hours);


                if (hours < 12) {
                    period = 'Morning';
                } else if (hours <= 18) {
                    period = 'Afternoon';
                } else if (hours > 18) {
                    period = 'Evening';
                } else {
                    period = '';
                }

                // newMessage = period + ' ' + v.cl_name + ',  ' + message;
                // var emailmessage = [period, v.cl_name, message];
                // emailmessage = JSON.stringify(emailmessage);
                // console.log(emailmessage);
                // notify_email(email, emailmessage);
                //notify_sms(phoneNumber, newMessage);

            });
        }
    });
}


/**
 * [notify_sms description]
 * @param  {[type]} phoneNumber [description]
 * @param  {[type]} message     [description]
 * @return {[type]}             [description]
 */
function notify_sms(phoneNumber, message) {
    //message="test";
    $.ajax({
        //url: base_url + function_url,
        url: 'http://localhost/mnh/c_admin/notify/sms/' + phoneNumber + '/' + encodeURIComponent(message),
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            console.log(data);

        }
    });
}
/**
 * [notify_email description]
 * @param  {[type]} email   [description]
 * @param  {[type]} message [description]
 * @return {[type]}         [description]
 */
function notify_email(email, message) {
    //message="test";
    $.ajax({
        //url: base_url + function_url,
        url: 'http://localhost/mnh/c_admin/notify/email/' + email + '/' + encodeURIComponent(message),
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            console.log(data);

        }
    });
}

/**
 * [getCountyData description]
 * @param  {[type]} base_url        [description]
 * @param  {[type]} county          [description]
 * @param  {[type]} survey_type     [description]
 * @param  {[type]} survey_category [description]
 * @return {[type]}                 [description]
 */
function getCountyData(base_url, county, survey_type, survey_category) {
    decodedCounty = county;
    county = encodeURIComponent(county);
    $.ajax({
        url: base_url + 'c_analytics/getCountyData/' + survey_type + '/' + survey_category + '/' + county,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            obj = jQuery.parseJSON(data);
            console.log(obj);
            $('#county_name').text(decodedCounty);
            $('#survey_type').text(survey_type.toUpperCase());
            $('#survey_category').text(survey_category.toUpperCase());
            $('#targeted .digit').text(obj[0].actual);
            $('#finished .digit').text(obj[0].reported);
            $('#started .digit').text(obj[0].unfinished);
            $('#not_started .digit').text(obj[0].notstarted);
<<<<<<< HEAD
<<<<<<< HEAD
            var percent = obj[0].reported / obj[0].actual * 100;
            $( "#c_progress" ).attr( "value", percent );
            $("#county_progress span").text(obj[0].reported + ' / ' + obj[0].actual + '(' + percent +'%)');
=======
            percentage = Math.round((obj[0].reported / obj[0].actual * 100),2);
            $('#county_progress .progress-bar').text(percentage+'%' );
             $('#county_progress .progress-bar').attr('aria-valuenow',percentage );
             $('#county_progress .progress-bar').css('width',percentage+'%' );
>>>>>>> 1a20efed662b0f2cb0636541cd76449374e1fec5
=======
            percentage = Math.round((obj[0].reported / obj[0].actual * 100), 2);
            $('#county_progress .progress-bar').text(percentage + '%');
            $('#county_progress .progress-bar').attr('aria-valuenow', percentage);
            $('#county_progress .progress-bar').css('width', percentage + '%');
>>>>>>> 82c13dd18304b9e07024fdd1a5ecf77746b9a63d
            url = base_url + 'c_analytics/setActive/' + county + '/' + survey_type + '/' + survey_category;
            $('#load_analytics').attr('data-url', url);
            new_url = base_url + 'c_analytics/getCountyReportingSummary/' + county + '/' + survey + '/' + survey_category;
            $('#load_county_summary').attr('data-url', new_url);
        }
    });
}



/**
 * [loadData description]
 * @param  {[type]} base_url         [description]
 * @param  {[type]} function_url     [description]
 * @param  {[type]} value            [description]
 * @param  {[type]} container        [description]
 * @param  {[type]} placeholder_text [description]
 * @return {[type]}                  [description]
 */
function loadData(base_url, function_url, value, container, placeholder_text) {
    if (value !== '') {
        ajax_url = base_url + 'c_analytics/' + function_url + '/' + value
    } else {
        ajax_url = base_url + 'c_analytics/' + function_url
    }
    $.ajax({
        url: ajax_url,
        async: false,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");

            $(container).empty();
        },
        success: function(data) {
            obj = jQuery.parseJSON(data);
            $(container).select2({
                placeholder: placeholder_text,
                data: obj
            });

        }
    });
}

/**
 * [loadMasterFacilityList description]
 * @param  {[type]} base_url  [description]
 * @param  {[type]} container [description]
 * @param  {[type]} form      [description]
 * @return {[type]}           [description]
 */
function loadMasterFacilityList(base_url, container, form) {
    $.ajax({
        url: base_url + 'c_analytics/getMasterFacilityList',
        async: false,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");

            $(container).empty();
        },
        success: function(data) {
            $(container.empty);
            $(container).append(data);
            $('.dataTable').on('load', function() {
                $('.dataTable').dataTable({
                    "sPaginationType": "full_numbers"
                });

            });
        }
    });
}
/**
 * [submitForm description]
 * @param  {[type]} base_url     [description]
 * @param  {[type]} function_url [description]
 * @return {[type]}              [description]
 */
function submitForm(base_url, function_url) {
    $.ajax({
        url: base_url + function_url,
        async: false,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            console.log('Submitted');
        }
    });
}
/**
 * [loadModalForm description]
 * @param  {[type]} base_url     [description]
 * @param  {[type]} function_url [description]
 * @param  {[type]} modal_title  [description]
 * @param  {[type]} modal_width  [description]
 * @param  {[type]} contents     [description]
 * @return {[type]}              [description]
 */
function loadModalForm(base_url, function_url, modal_title, modal_width, contents) {
    $('#universalModal .modal-title').text(modal_title);
    $('#universalModal .modal-dialog').css('width', modal_width);

    $('#universalModal .modal-body').empty();
    $('#universalModal .modal-body').append(contents);
    $('#universalModal form').attr('action', base_url + function_url);
}
/**
 * [loadHelpForm description]
 * @param  {[type]} base_url [description]
 * @return {[type]}          [description]
 */
function loadHelpForm(base_url) {
    helpform = '<form id="modal-form" method="post"> <label>Email Address</label> <input type="email" placeholder="Please Enter Email Address Here...">' +
        '<label>County</label><input type="text" id="county">' +
        '<label>District</label><input name="district" type="text" id="district">' +
        '<label>Facility</label><input name=""type="text" id="facility">' +
        '<label>Question / Complaint</label><textarea rows="4" placeholder="Please Enter Query Here..."></textarea>' +
        '</form>';
    loadModalForm(base_url, 'c_analytics/submit_help', 'Help', '60%', helpform);

    $('#county').select2({
        placeholder: 'Please Select Your County',
        data: getCountyDataAll()
    });
    $('#county').change(function() {
        county = $(this).val();
        $('#district').select2({
            placeholder: 'Please Select Your District',
            data: getSpecificDistrictData(county)
        });
    });
    $('#district').change(function() {
        district = $(this).val();
        $('#facility').select2({
            placeholder: 'Please Select Your Facility',
            data: getSpecificFacilityData(district)
        });
    });
}


/**
 * [getFacilityData description]
 * @return {[type]} [description]
 */
function getFacilityData() {
    var result;
    $.ajax({
        url: base_url + 'assets/data/fac_name.json',
        async: false,
        success: function(data) {
            obj = jQuery.parseJSON(data);
            result = obj;
        }
    });
    return result;
}
/**
 * [getCountyData description]
 * @return {[type]} [description]
 */
function getCountyDataAll() {
    var result;
    $.ajax({
        url: base_url + 'assets/data/fac_county.json',
        async: false,
        success: function(data) {
            obj = jQuery.parseJSON(data);
            result = obj;
        }
    });
    return result;
}
/**
 * [getDistrictData description]
 * @return {[type]} [description]
 */
function getAllDistrictData() {
    var result;
    $.ajax({
        url: base_url + 'assets/data/fac_district.json',
        async: false,
        success: function(data) {
            obj = jQuery.parseJSON(data);
            result = obj;
        }
    });
    return result;
}
/**
 * [getSpecificDistrictData description]
 * @param  {[type]} county [description]
 * @return {[type]}        [description]
 */
function getSpecificDistrictData(county) {
    var result;
    $.ajax({
        url: base_url + 'c_analytics/getDistrictNamesJSON/' + county,
        async: false,
        success: function(data) {
            obj = jQuery.parseJSON(data);
            result = obj;
        }
    });
    return result;
}
/**
 * [getSpecificFacilityData description]
 * @param  {[type]} district [description]
 * @return {[type]}          [description]
 */
function getSpecificFacilityData(district) {
    var result;
    $.ajax({
        url: base_url + 'c_analytics/getFacilityNamesJSON/' + district,
        async: false,
        success: function(data) {
            obj = jQuery.parseJSON(data);
            result = obj;
        }
    });
    return result;
}
/**
 * [showMasterFacilityList description]
 * @param  {[type]} base_url [description]
 * @param  {[type]} form     [description]
 * @return {[type]}          [description]
 */
function showMasterFacilityList(base_url, form) {
    $('#universalModal').modal('show');
    $('#universalModal').delay(2000, function(nxt) {
        $('#universalModal .modal-title').text('Master Facility List');
        loadMasterFacilityList(base_url, '#universalModal .modal-body', form);
        $('.dataTable').dataTable();
        nxt();
    });
}
/**
 * [showHelp description]
 * @param  {[type]} base_url [description]
 * @return {[type]}          [description]
 */
function showHelp(base_url) {
    $('#universalModal').modal('show');
    $('#universalModal').delay(2000, function(nxt) {
        loadHelpForm(base_url);
        nxt();
    });
}
/**
 * [showEnlargedGraph description]
 * @param  {[type]} base_url [description]
 * @return {[type]}          [description]
 */
function showEnlargedGraph(base_url, function_url, title, raw_url) {
    $('#universalModal').modal('show');
    $('#universalModal').delay(2000, function(nxt) {
        loadEnlargedGraph(base_url, function_url, title, raw_url);

        nxt();
    });
}
/**
 * [showAnalytics description]
 * @param  {[type]} base_url [description]
 * @return {[type]}          [description]
 */
function showAnalytics(base_url) {
    url = base_url + 'analytics'
    window.open(url);
}
/**
 * [startIntro description]
 * @return {[type]} [description]
 */
function startIntro() {
    var intro = introJs();
    intro.setOptions({
        steps: [{
            element: '#network',
            intro: "This is a Top Bar showing the Date, User and System Information.",
            position: 'bottom'
        }, {
            element: '#navigation',
            intro: "The <b>Navigation</b> Menu has the links to the Surveys and Analytics as well as <b>HCMP</b> and <b>PMT</b>.",
            position: 'top'
        }, {
            element: '#surveys',
            intro: 'The <b>Surveys</b> Section contains links to access the <span style="color:blue">Forms</span> for the 3 Surveys i.e. MNH, CH and HCW. ',
            position: 'right'
        }, {
            element: '#reporting-rates',
            intro: "The <b>Reporting</b> Section displays the Kenyan Map, <span style='color:red'>C</span> <span style='color:orange'>o</span> <span style='color:gold'>l</span> <span style='color:lightgreen'>o</span> <span style='color:green'>r</span> Coded to represent the Completion Rate.",
            position: 'left'
        }, {
            element: '#analytics',
            intro: 'The <b>Analytics</b> Analytics contains links to access the <span style="color:blue">Data</span> for the 3 Surveys i.e. MNH, CH and HCW.',
            position: 'left'
        }]
    });

    intro.start();
}
$(document).ready(function() {
    var theclass;

    //startIntro();
    $('.panel-collapse.collapse.in').parent().find('.panel-heading h4 a i').attr('class', 'fa fa-chevron-down');
    //Handling Collapses
    $('.panel-collapse').on('show.bs.collapse', function() {
        $(this).parent().find('.panel-heading h4 a i').attr('class', 'fa fa-chevron-down');
        //$('.panel-collapse collapse in').collapse('hide');
        //$(this).collapse('show');

    })
    $('.panel-collapse').on('hide.bs.collapse', function() {
        $(this).parent().find('.panel-heading h4 a i').attr('class', 'fa fa-chevron-right');
        //$('.panel-collapse collapse in').collapse('hide');
        //$(this).collapse('show');

    })

    // $('.dataTable').on('load',function(){
    //     $('.dataTable').dataTable({
    //             "sPaginationType": "full_numbers"
    //         });
    //     });
    // $.fn.editable.defaults.mode = 'inline';
    // $.fn.editableform.buttons =
    //     '<button type="submit" class="btn btn-success editable-submit btn-mini"><i class="fa fa-check-circle"></i></button>' +
    //     '<button type="button" class="btn btn-danger editable-cancel btn-mini"><i class="fa fa-ban"></i></button>';

    // cheet('ctrl m f l', function() {
    //     showMasterFacilityList(base_url, 'table');
    // });

    // cheet('ctrl e d i t m f l', function() {
    //     showMasterFacilityList(base_url, 'editable');
    // });
    // cheet('ctrl h e l p', function() {
    //     showHelp(base_url);
    // });
    // cheet('g r a p h', function() {
    //     showAnalytics(base_url);
    // });

    $('.dataTable').on('load', function() {
        $('.dataTable').dataTable({
            "sPaginationType": "full_numbers"
        });
    });


});
