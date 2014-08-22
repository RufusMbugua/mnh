/**
 * [runGraph description]
 * @param  {[type]} container        [description]
 * @param  {[type]} chart_title      [description]
 * @param  {[type]} chart_stacking   [description]
 * @param  {[type]} chart_type       [description]
 * @param  {[type]} chart_categories [description]
 * @param  {[type]} chart_series     [description]
 * @param  {[type]} chart_drilldown  [description]
 * @param  {[type]} chart_size       [description]
 * @param  {[type]} chart_margin     [description]
 * @param  {[type]} color_scheme     [description]
 * @return {[type]}                  [description]
 */
function runGraph(container, chart_title, chart_stacking, chart_type, chart_categories, chart_series, chart_drilldown, chart_length, chart_width, chart_margin, color_scheme,chart_label_rotation,chart_legend_floating) {
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
                rotation:chart_label_rotation
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
            if (obj.chart_series != null && obj.chart_series[0] != null) {
                $(graph_section).append('<div id="' + obj.container + '" ></div>');
                runGraph(obj.container, obj.chart_title, obj.chart_stacking, obj.chart_type, obj.chart_categories, obj.chart_series, obj.chart_drilldown, obj.chart_length, obj.chart_width, obj.chart_margin, obj.color_scheme,obj.chart_label_rotation,obj.chart_legend_floating);
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

                newMessage = period + ' ' + v.cl_name + ',  ' + message;
                var emailmessage = [period, v.cl_name, message];
                emailmessage = JSON.stringify(emailmessage);
                console.log(emailmessage);
                notify_email(email, emailmessage);
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

function getCountyData(base_url, county, survey_type, survey_category) {
    $.ajax({
        url: base_url + 'c_analytics/getCountyData/' + county + '/' + survey_type + '/' + survey_category,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(data) {
            obj = jQuery.parseJSON(data);
            console.log(obj);
            $('#county_name').text(county);
            $('#survey_type').text(survey_type.toUpperCase());
            $('#survey_category').text(survey_category.toUpperCase());
            $('#targeted .digit').text(obj[0].actual);
            $('#finished .digit').text(obj[0].reported);
            $('#started .digit').text(obj[0].unfinished);
            $('#not_started .digit').text(obj[0].notstarted);
            var percent = obj[0].reported / obj[0].actual * 100;
            $( "#c_progress" ).attr( "value", percent );
            $("#county_progress span").text(obj[0].reported + ' / ' + obj[0].actual + '(' + percent +'%)');
            url = base_url + 'c_analytics/setActive/' + county + '/' + survey_type + '/' + survey_category;
            $('#load_analytics').attr('data-url', url);
            new_url = base_url + 'c_analytics/getCountyReportingSummary/'+survey + '/' + survey_category;
            $('#load_county_summary').attr('data-url',new_url);
        }
    });
}


function loadData(base_url,function_url,value,container,placeholder_text){
    if(value!==''){
       ajax_url= base_url + 'c_analytics/' + function_url + '/' + value
    }
    else{
        ajax_url= base_url + 'c_analytics/' + function_url
    }
    $.ajax({
        url: ajax_url ,
        async:false,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");

            $(container).empty();
        },
        success: function(data) {
            obj = jQuery.parseJSON(data);
            $(container).select2({ placeholder:placeholder_text, data: obj});

        }
    });
}

function loadMasterFacilityList(base_url,container){
    $.ajax({
        url: base_url+'c_analytics/getMasterFacilityList' ,
        async:false,
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");

            $(container).empty();
        },
        success: function(data) {
            $(container.empty);
            $(container).append(data);
                $('.dataTable').on('load',function(){
        $('.dataTable').dataTable({
                "sPaginationType": "full_numbers"
            });

})

        }
    });
}
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
    $('.dataTable').on('load',function(){
        $('.dataTable').dataTable({
                "sPaginationType": "full_numbers"
            });

});
});
