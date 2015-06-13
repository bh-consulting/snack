function toggleBlock(button) {
	$(button).next().slideToggle();
	$(button).find('i').toggleClass('icon-chevron-up');
	$(button).find('i').toggleClass('icon-chevron-down');
}

function countItems() {
    var n = $('input:checked[type=checkbox][id^=MultiSelection][value!=all]').length;
    var button = $('#modaldel .btn-danger');

    button.text(button.text().replace(/[0-9]+/, n));
}

function loading() {
    $("div.loading").html("<i class='fa fa-circle-o-notch fa-inverse fa-spin fa-1x'></i>");
}

function unloading() {
    $("div.loading").html("");
}

function loading_from_sidebar() {
    $("div.loading_from_sidebar").html("<i class='fa fa-circle-o-notch fa-inverse fa-spin fa-1x'></i>");
}

$(window).resize(function() {
    if($(window).width() > 979) {
	if($('.mainmenu').hasClass('menuphone'))
	    $('.mainmenu').toggleClass('menuphone');

	if($('.maintopmenu').hasClass('menutopphone'))
	    $('.maintopmenu').toggleClass('menutopphone');

    } else {

	if(!$('.mainmenu').hasClass('menuphone'))
	    $('.mainmenu').toggleClass('menuphone');

	if(!$('.maintopmenu').hasClass('menutopphone'))
	    $('.maintopmenu').toggleClass('menutopphone');
    }
});

$(document).ready(function() {
    $('#rootwizard').bootstrapWizard({onTabShow: function(tab, navigation, index) {
        var $total = navigation.find('li').length;
        var $current = index+1;
        var $percent = ($current/$total) * 100;
        if($percent != 0){
            $('#rootwizard').find('.bar').css({width:$percent+'%'});
        }

        // If it's the last tab then hide the last button and show the finish instead
        if($current >= $total) {
            $('#rootwizard').find('.pager .next').hide();
            $('#rootwizard').find('.pager .finish').show();
            $('#rootwizard').find('.pager .finish').removeClass('disabled');
        } else {
            $('#rootwizard').find('.pager .next').show();
            $('#rootwizard').find('.pager .finish').hide();
        }

        $('html, body').animate({scrollTop: 0}, 500);
    }});

    $("input[type='password']").val('');

    document.onkeydown = Tastendruck;
});

function test_users(){
    if(document.getElementById("users") !== null) {
        if (document.getElementById('SystemDetailsPassword').value == "") {
            password=null;
        } else {
            password=document.getElementById('SystemDetailsPassword').value;
        }
        var url="test_users/"+$("#SystemDetailsUsername option:selected").text()+"/"+password+"/"+$("#SystemDetailsAuthtype option:selected").text();
        //alert(url);
        $.get(url, function(data) {
                loading();
                $("#users").html(data);
            }).fail(function() {
                alert( "error" );
            })
            .always(function() {
                //alert( "finished" );
            });
    }
}

function getbackupid(id) {
    elt="backuptype_"+id;
    document.getElementById(elt).innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-1x'></i>";
    newURL="nas/backupconfig/"+id;
    $.ajax({
        url: newURL,
        cache: false,
        success: function(html){
            var res=html.split(":");
            var id = res[0].replace(/\s+/g,"");
            elt="backuptype_"+id;
            if (res[1] == "1") {
                document.getElementById(elt).innerHTML = "<i class='fa fa-check fa-1x text-success'></i>";
            } else {
                document.getElementById(elt).innerHTML = "<i class='fa fa-times fa-1x text-danger'></i>";
            }
        }
    })
}

function getbackupall() {
    $( "tr" ).each(function( index ) {
        if(this.id.match(/^nas_(\d+)$/)) {
            id=this.id.replace(/nas_/, '');
            nasname=document.getElementById("nasname_"+id).innerHTML;
            if (nasname != "127.0.0.1") {
                elt="backuptype_"+id;
                document.getElementById(elt).innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-1x'></i>";
                newURL="nas/backupconfig/"+id;
                $.ajax({
                    url: newURL,
                    cache: false,
                    success: function(html){
                        var res=html.split(":");
                        var id = res[0].replace(/\s+/g,"");
                        elt="backuptype_"+id;
                        if (res[1] == "1") {
                            document.getElementById(elt).innerHTML = "<i class='fa fa-check fa-1x text-success'></i>";
                        } else {
                            document.getElementById(elt).innerHTML = "<i class='fa fa-times fa-1x text-danger'></i>";
                        }
                    }
                })
            }
        }
    });
}

function reportsexpanderror(type, id) {
    $(".reports-"+type+"-msg-"+id).toggle();
}

function refreshCode(){
    if(document.getElementById("livelogs") !== null) {
        if (document.getElementById("LoglineAjax").checked == false) {
            var newURL=window.location.protocol + "//" + window.location.host;
            var pathArray = document.URL.split( '/' );
            newURL+="/loglines/logelementradius";
            if (pathArray.length <= 4) {
                newURL += "/index";
            } else {
                for (i = 4; i < pathArray.length; i++) {
                    newURL += "/";
                    newURL += pathArray[i];
                }
            }
            var filters = document.URL.split( '?' )[1];
            newURL = newURL+"?"+filters;
            $.ajax({
                    url: newURL,
                    cache: false,
                    success: function(html){
                      $("#livelogs").html(html);
                    }
            })
        }
    }
    if(document.getElementById("voicelivelogs") !== null) {
        if (document.getElementById("LoglineAjax").checked == false) {
            var newURL=window.location.protocol + "//" + window.location.host;
            var params = document.URL.split( '?' );
            newURL+="/loglines/logelementvoice";
            if (params.length > 1) {
                newURL += "?";
                newURL += params[1];
            }
            //alert(newURL);
            $.ajax({
                url: newURL,
                cache: false,
                success: function(html){
                  $("#voicelivelogs").html(html);
                }
            })
        }
    }
    if(document.getElementById("livesessions") !== null) {
        if (document.getElementById("RadacctAjax").checked == false) {
            var newURL=window.location.protocol + "//" + window.location.host;
            //var pathArray = document.URL.split( '/' );
            newURL+="/radaccts/get_sessions_ajax";
            var params=document.URL.split( '?' );
            if (params.length > 1) {
                newURL += "?";
                newURL += params[1];
            }
            //alert(newURL);
            $.ajax({
                url: newURL,
                cache: false,
                success: function(html){
                  $("#livesessions").html(html);
                }
            })
        }
    }
}

// SLIDER
function applySlider(index, elt, range) {
    var select = $(elt).parent().prev();
    var iSelected = select.find('option')
    .index(select.children('option[selected]'));

    var value = function(v) {
        return select.children(':nth-child(' + (v+1) + ')')
        .attr('value');
    };

    var label = function(v) {
        return select.children(':nth-child(' + (v+1) + ')')
        .text();
    };

    select.hide();

    var labelColor = select.children('option[selected]')
    .attr('label-color');
    $(elt).next()
    .css('color', labelColor === undefined ? '#000' : labelColor);
    $(elt).next()
    .text( label(iSelected) );

    $(elt).slider({
        range: range,
        value: iSelected,
        min: 0,
        max: select.find('option').size() - 1,
        step: 1,
        slide: function(event, ui) {
            select.val( value(ui.value) );
            $(elt).next().text( label(ui.value) );

            var labelColor = select.children('option[selected]')
            .attr('label-color');
            $(elt).next()
            .css('color', labelColor === undefined ? '#000' : labelColor);
        }
    });
}

// SLIDER MAX
$('<div class="slider"><div></div><span></span></div>')
.insertAfter('.slidermax');
$('.slidermax + div :first-child').each(function(index, elt){
    applySlider(index, elt, 'max');
});

// SLIDER MIN
$('<div class="slider"><div></div><span></span></div>')
.insertAfter('.slidermin');
$('.slidermin + div :first-child').each(function(index, elt){
    applySlider(index, elt, 'min');
});

// SELECT ROLE
$("#role").change(function() {
    var value = $(this).val();
    if (value == "master") {
        $("#ParameterMasterIp" ).prop( "readonly", true );
        $("#ParameterMasterIp" ).val("");
        $("#ParameterSlaveIpToMonitor" ).prop( "readonly", false );
    }
    if (value == "slave") {
        $("#ParameterMasterIp" ).prop( "readonly", false );
        $("#ParameterSlaveIpToMonitor" ).prop( "readonly", true );
        $("#ParameterSlaveIpToMonitor" ).val("");
    }
});

// CISCO ATTRIBUTE
$("#RaduserCisco").change(function() {
    var chk = $('#RaduserCisco').is(':checked');
    if (chk) {
        $("#RaduserNas-port-type").removeAttr("readonly");
        $("#RaduserPrivilege").removeAttr("readonly");
    } else {
        //alert(chk);
        $("#RaduserNas-port-type").attr( "readonly");
        $("#RaduserPrivilege").attr( "readonly");
        $("#RaduserPrivilege" ).prop( "readonly", true );
        
    }
});

// MAC ADDRESS ADD CISCO PHONE
$("#RaduserIsMac").change(function() {
    if($(this).is(":checked")) {
        $("#RaduserPasswd").prop( "readonly", true );
        $("#RaduserConfirmPassword").prop( "readonly", true );
    } else {
        $("#RaduserPasswd").prop( "readonly", false );
        $("#RaduserConfirmPassword").prop( "readonly", false );
    }
});

// DATETIMEPICKER
var_date=new Date();
str_date=var_date.getYear()+1900+"-"+var_date.getMonth()+"-"+var_date.getDate();
$('.datetimepicker').addClass('form-control');
        $('.datetimepicker').wrap('<div class="input-group date form_datetime col-sm-6" data-date="'+str_date+'T05:25:07Z" data-date-format="yyyy MM dd - HH:ii p"></div>');
        $('<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>').insertAfter(".datetimepicker");
        $('<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>').insertAfter(".datetimepicker");
$('.form_datetime').datetimepicker({
    //language:  'fr',
    format: "yyyy-mm-dd hh:ii:00",
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    showMeridian: 0
});

// HA LOGS
$(".halog").click(function(event) {
    //alert("Handler for .click() called.");
    $.get("halog/"+this.id, function(data) {            
        $("div.halogs").html(data);
        //alert(data);
    }).fail(function() {
        alert( "error" );
    })
    .always(function() {
        //alert( "finished" );
    });
});

// SYSTEMDETAILS / TEST
$("#SystemDetailsUsername").change(function() {
    //alert($("#SystemDetailsUsername option:selected").text());
    var patt = new RegExp("[0-9a-f]{12}");
    var res = patt.test($("#SystemDetailsUsername option:selected").text());
    if (res) {
        $("#SystemDetailsPassword").val($("#SystemDetailsUsername option:selected").text());
        $("#SystemDetailsAuthtype").val("eap-md5");
        $("#SystemDetailsPassword").prop( "readonly", false);
    }
});

// SYSTEMDETAILS / TEST
$("#SystemDetailsAuthtype").change(function() {
    //alert($("#SystemDetailsAuthtype option:selected").text());
    if ($("#SystemDetailsAuthtype option:selected").text() == "EAP-TLS") {
        $("#SystemDetailsPassword").val("");
        $("#SystemDetailsPassword").prop( "readonly", true);
    } else {
        $("#SystemDetailsPassword").prop( "readonly", false);
    }
});


// NAS / backup checkbox
$("#NasBackup").change(function() {
    if($(this).is(":checked")) {
        $("#NasLogin").prop( "readonly", false );
        $("#NasPassword").prop( "readonly", false );
        $("#NasConfirmPassword").prop( "readonly", false );
        $("#NasEnablepassword").prop( "readonly", false );
        $("#NasConfirmEnablepassword").prop( "readonly", false );
    } else {
        $("#NasLogin").prop( "readonly", true );
        $("#NasPassword").prop( "readonly", true );
        $("#NasConfirmPassword").prop( "readonly", true );
        $("#NasEnablepassword").prop( "readonly", true );
        $("#NasConfirmEnablepassword").prop( "readonly", true );
    }
});

setInterval(function(){ refreshCode(); }, 3000)
