function LoadQuestionairePage() {
    $(document).ready(function() {
        $.get(MY_PAGES.questionairePage, function(data) {
            $('#questionairePage').html(data);
            hideAllPage();
            if( USER_PROFILE.questionaire.gender != 'na'){
                $.each(USER_PROFILE.questionaire, function(i, v) {
                    if (i == 'gender' || i =='charityWilling') {
                        $("#questionaire-"+ i + " input[value='"+ USER_PROFILE.questionaire[i][0]+"'] ").prop("checked", true); 
                    } else {
                        if (i == 'charityTendency') {
                            var otherCheck = USER_PROFILE.questionaire['charityTendency'];
                            var otherIndex = $.map(QUESTIONAIRE_FORM_TEXTS['charityTendencyOpts'], function(val, key) {
                                if (val['label'] == '其他') {
                                    return (val['index']);
                                }
                            })
                            if ($.inArray(otherIndex + '', otherCheck) != -1) { // 資料裡有其他 
                                $('#other-input').show();
                                $('#charityTendencyOther').val( USER_PROFILE.charityTendencyOther );
                            } else {
                                $('#other-input').hide();
                            }
                        }
                        $.each(v, function(v_index, v_value) {
                            $("#questionaire-"+i+" select option[value='"+ v_value +"']").attr('selected', true);
                        })
                    }
                });
                $('select').material_select();
            }
            $('#questionairePage').show();
            $(document).scrollTop(0);
            AfterLoadQuestionaire();
        });
    });
}

function LoadSurveyPage() {
    RandomAssignCases();
    $.get(MY_PAGES.surveyPage, function(data) {
        $('#surveyPage').html(data);
        hideAllPage();
        $('#surveyPage').show();
        $(document).scrollTop(0);
        AfterLoadSurvey();
        promptMaterial('promptSurvey');
    });
}

function LoadThankPage() {
    $(document).ready(function() {
        $.get(MY_PAGES.thankPage, function(data) {
            TitleListLoading();
            $('#thankPage').html(data);
            hideAllPage();
            $('#thankPage').show();
            $(document).scrollTop(0);

            AfterLoadThank();
        });
    });
}

function LoadSubscribingPage() {

    $(document).ready(function() {
        $.get(MY_PAGES.subscribingPage, function(data) {
            $('#subscribingPage').html(data);
            hideAllPage();
            if (USER_PROFILE.subscribe != 0) {
                $("#subscribing-frequency input[type='checkbox']").attr('checked', true);
                $('.subscribe-info-container').show();
                $('#email').val(USER_PROFILE.email);
                $("#subscribing-frequency select option[value='0']").attr('selected', false);
                $("#subscribing-frequency select option[value='" + USER_PROFILE.subscribe + "']").attr('selected', true);
                $('select').material_select();
            }
            $('#subscribingPage').show();
            $(document).scrollTop(0);

            AfterLoadSubscribing();
        });
    });
}

function hideAllPage() {
    $.each(MY_PAGES, function(i, v) {
        $('#' + i).hide();
    })
}

function AddPushpin() {
    $('.tabs-wrapper').pushpin({
        top: $('.tabs-wrapper').offset().top
    });
}

function IniInlineSelect() {
    $(document).ready(function() {
        $('select').material_select();
        $('.select-wrapper').addClass('form-inline');
    });
}

function IniSelect() {
    $(document).ready(function() {
        $('select').material_select();
    });
}

function ArticleIframeCss(selector, css) {
    var head = $(selector).contents().find("head"); //#article-iframe
    var css = '<style type="text/css">' + css + '</style>'; //'article.mpatc.clearmen { padding: 0 20px !important; }'
    $(head).append(css);
}

function CreateCheckbox(myOptions, myContianer) {
    var container = $('#' + myContianer);
    for (var i = 0; i < myOptions.length; i++) {
        var myElement = $("<p></p>");
        var myId = myOptions[i].id;
        var myCss = myOptions[i].css;
        var myLabel = myOptions[i].label;
        $('<input />', { type: 'checkbox', id: myId, class: 'filled-in' }).appendTo(myElement);
        $('<label />', { 'for': myId, text: myLabel, class: 'grey-text text-darken-2' }).appendTo(myElement);
        myElement.appendTo(container);
    }
}

function CreateRadio(myOptions, myContianer) {
    var container = $('#' + myContianer);
    for (var i = 0; i < myOptions.length; i++) {
        var myElement = $("<p></p>");
        var myId = myOptions[i].id;
        var myGroup = myOptions[i].group;
        var myLabel = myOptions[i].label;
        var myIndex = myOptions[i].index;

        $('<input />', { type: 'radio', id: myId, name: myGroup, value: myIndex }).appendTo(myElement);
        $('<label />', { 'for': myId, html: myLabel, class: 'grey-text text-darken-2' }).appendTo(myElement);
        myElement.appendTo(container);
    }

    IniInlineSelect();
}

function CreateSelect(myOptions, myContianer) {
    var container = $('#' + myContianer);
    var myElement = $("<select name='" + myContianer + "' style='display:none;'></select>");
    for (var i = 0; i < myOptions.length; i++) {
        var myLabel = myOptions[i].label;
        var myIndex = myOptions[i].index;

        if (i == 0) {
            $('<option />', { text: myLabel }).attr({
                disabled: 'disabled',
                selected: 'selected'
            }).appendTo(myElement);
        } else {
            $('<option />', {
                text: myLabel,
                value: myIndex
            }).appendTo(myElement);
        }
    }
    myElement.appendTo(container);

    IniSelect();
}

function CreateMultiSelect(myOptions, myContianer) {
    var container = $('#' + myContianer);
    var myElement = $("<select name='" + myContianer + "' multiple style='display:none;'></select>");
    for (var i = 0; i < myOptions.length; i++) {
        var myLabel = myOptions[i].label;
        var myIndex = myOptions[i].index;

        if (i == 0) {
            $('<option />', { text: myLabel }).attr({
                disabled: 'disabled',
                selected: 'selected'
            }).appendTo(myElement);
        } else {
            $('<option />', {
                text: myLabel,
                value: myIndex
            }).appendTo(myElement);
        }
    }
    myElement.appendTo(container);

    IniSelect();
}

function CreateOptGroupSelect(myOptions, myContianer) {
    var container = $('#' + myContianer);
    var myElement = $("<select name='" + myContianer + "' style='display:none;'></select>");
    for (var i = 0; i < myOptions.length; i++) {
        var myLabel = myOptions[i].label;
        var myIndex = myOptions[i].index;

        if (i == 0) {
            $('<option />', { text: myLabel }).attr({
                disabled: 'disabled',
                selected: 'selected'
            }).appendTo(myElement);
        } else {
            $('<option />', {
                text: myLabel,
                value: myIndex
            }).appendTo(myElement);
        }
    }
    myElement.appendTo(container);

    IniSelect();
}

function CreateScaleForm(leftLabel, rightLabel, scale, groupName, myContianer) {
    var container = $('#' + myContianer);
    var myElement = $("<table><tbody><tr aria-hidden='true'></tr><tr role='radiogroup'></tr></tbody></table>")
    var trScale = myElement.children().first().children().first();
    var trRadio = myElement.children().first().children().last();

    $('<td />', { addClass: 'ss-scalenumbers' }).appendTo(trScale);
    for (var i = 1; i <= scale; i++) {
        var myTd = $('<td />', { addClass: 'ss-scalenumbers' });
        var myId = 'qi-' + groupName + '-' + String(i);
        $('<label />', { 'for': myId, text: String(i), addClass: 'ss-scalenumbers' }).appendTo(myTd);
        myTd.appendTo(trScale);
    }
    $('<td />', { addClass: 'ss-scalenumbers' }).appendTo(trScale);

    var myLabel = $('<td />', { addClass: 'ss-scalerow' });
    $('<div />', { text: leftLabel, addClass: 'ss-scalerow scale-left-label' }).attr({
        'aria-hidden': true
    }).appendTo(myLabel);
    myLabel.appendTo(trRadio);
    for (var i = 1; i <= scale; i++) {
        var myTd = $('<td />', { addClass: 'ss-scalerow' });
        var myId = 'qi-' + groupName + '-' + String(i);
        var myRadio = $('<div />', { addClass: 'ss-scalerow' });
        $('<input />', { type: 'radio', id: myId, name: groupName, addClass: 'ss-form-input', value: i }).appendTo(myRadio);
        $('<label />', { 'for': myId, html: '', addClass: 'ss-form-input' }).appendTo(myRadio);
        myRadio.appendTo(myTd);
        myTd.appendTo(trRadio);
    }
    var myLabel = $('<td />', { addClass: 'ss-scalerow' });
    $('<div />', { text: rightLabel, addClass: 'ss-scalerow scale-right-label' }).attr({
        'aria-hidden': true
    }).appendTo(myLabel);
    myLabel.appendTo(trRadio);

    myElement.appendTo(container);
}

function CreateSlider() {
    MY_FORMS.slider = document.getElementById('slider-scoring');

    noUiSlider.create(MY_FORMS.slider, {
        start: 50,
        behaviour: 'snap',
        connect: 'lower',
        range: {
            'min': 0,
            'max': 100
        }
    });

    $('#slider-scoring').addClass('slider-initial-state');
    MY_FORMS.slider.noUiSlider.on('slide', SliderOnSlide);
    MY_FORMS.slider.noUiSlider.on('change', SliderOnChange);
}

function SetShortcuts() {
    $(window).on('keydown', function(event) {
        switch (event.keyCode) {
            case 8: //backspace
                // console.log (event.target.tagName);
                if (event.target.tagName.search(/(input|TEXTAREA)/i) == -1) return false;
                break;
            case 81: // q -> ctrl or alt
                if (USER_PROFILE.ip.indexOf("140.109.") !== -1) {
                    if (event.altKey) {
                        return false;
                    }
                    if (event.ctrlKey) {
                        return false;
                    }
                }
            default:
                break;
        }
    });
}

function TitleListLoading() {
    $.get(MY_URLS.titleList, function(data) {
        EXPERIMENT_PROFILE.titleList = data;
        EXPERIMENT_PROFILE.aidList = Object.keys(data).sort().reverse().splice(10, EXPERIMENT_PROFILE.totalArticles);
    });
}


// function AidListLoading() {
//     $.get(MY_URLS.aidListHighOrder, function(data) { // article id 由高排到低 
//         if (data.length != 0) {
//             EXPERIMENT_PROFILE.aidList = data.split('\n');
//             EXPERIMENT_PROFILE.aidList = EXPERIMENT_PROFILE.aidList.filter(function(value) {
//                 return value != '';
//             });
//         } else {
//             $.get(MY_URLS.aidList, function(data2) {
//                 EXPERIMENT_PROFILE.aidList = data2.split('\n');
//                 EXPERIMENT_PROFILE.aidList = EXPERIMENT_PROFILE.aidList.filter(function(value) {
//                     return value != '';
//                 });
//             });
//         }
//     });
// }

function GetRandomCharacter() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function GetCurrentTimeMilli() {
    return new Date().getTime();
}

function GetIp() {
    $.get(MY_URLS.getIp, function(data) {
        USER_PROFILE.ip = data.ip;
    }, 'json');
}

function SetupHash() {
    EXPERIMENT_PROFILE.id = MY_URLS.titleList.split('/')[1];
    EXPERIMENT_PROFILE.hashFunction = new Hashids(EXPERIMENT_PROFILE.id, 4, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
    EXPERIMENT_PROFILE.hashKey = EXPERIMENT_PROFILE.hashFunction.encode(parseInt(EXPERIMENT_PROFILE.subId));
    var randomChars = '';
    for (var i = 0; i < 3; i++) randomChars += GetRandomCharacter();
    USER_PROFILE.uniqId = GetCurrentTimeMilli().toString() + randomChars;
}

function RecordException() {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.recordException,
            type: 'post',
            dataType: 'json',
            data: {
                uniqId: USER_PROFILE.uniqId,
                exceptionMsg: EXPERIMENT_PROFILE.exceptionMsg
            },
            success: function(data, textStatus, jqXHR) {
                if (BOOL_VARS.isTesting) {
                    console.log('Good in RecordException');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (BOOL_VARS.isTesting) {
                    console.log('Bad in RecordException');
                }
            }
        });
    }, 10);
}

function RecordTimeStart() {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.recordTimeStart,
            type: 'post',
            dataType: 'json',
            data: {
                uniqId: USER_PROFILE.uniqId,
                fbId: USER_PROFILE.fbId,
                ip: USER_PROFILE.ip,
                timeRecording: JSON.stringify(USER_PROFILE.timeRecording),
                hashKey: EXPERIMENT_PROFILE.hashKey,
                expId: EXPERIMENT_PROFILE.id,
                expSubId: EXPERIMENT_PROFILE.subId,
                numCases: EXPERIMENT_PROFILE.numCases
            },
            success: function(data, textStatus, jqXHR) {
                if (BOOL_VARS.isTesting) {
                    console.log('[success] RecordTimeStart');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (BOOL_VARS.isTesting) {
                    console.log('[fail] RecordTimeStart');
                }

                EXPERIMENT_PROFILE.exceptionMsg = ' [fail] RecordTimeStart: ' + textStatus + ' : ' + errorThrown;
                RecordException();
            }
        });

    }, 10);
}

function RecordArticleTime(start, end, id, round) {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.recordArticleTime,
            type: 'post',
            dataType: 'json',
            data: {
                uniqId: USER_PROFILE.uniqId,
                start: start,
                end: end,
                id: id,
                round: round,
                hashKey: EXPERIMENT_PROFILE.hashKey,
                ip: USER_PROFILE.ip
            },
            success: function(data, textStatus, jqXHR) {
                if (BOOL_VARS.isTesting) {
                    console.log('[success] RecordArticleTime');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (BOOL_VARS.isTesting) {
                    console.log('[fail] RecordArticleTime');
                }
                EXPERIMENT_PROFILE.exceptionMsg = ' [fail] RecordArticleTime: ' + textStatus + ' : ' + errorThrown;
                RecordException();
            }
        });

    }, 10);
}

function SetupCases() {
    var ret = [];
    var aid = '',
        // cover = '',
        title = '',
        article = '';
    for (var i = 0; i < EXPERIMENT_PROFILE.numCases; i++) {
        aid = 'A3565';
        // cover = 'db_covers_1590/A1472.jpg';
        title = '孝女喝湯果腹 打工養家';
        article = 'db_articles/A3565.htm';

        var myObj = {};
        myObj['id'] = 'case_' + (i + 1).toString();
        myObj['aid'] = aid;
        // myObj['cover'] = cover;
        myObj['title'] = title;
        myObj['article'] = article;
        myObj['score'] = 'na';

        ret.push(myObj);
    }

    EXPERIMENT_PROFILE.cases = ret;
}

function RandomAssignCases() {
    var indexPoped, aidPoped;
    for (var i = 0; i < EXPERIMENT_PROFILE.cases.length; i++) {
        // indexPoped = Math.floor(Math.random() * EXPERIMENT_PROFILE.aidList.length);
        aidPoped = EXPERIMENT_PROFILE.aidList.splice(0, 1);
        EXPERIMENT_PROFILE.cases[i]['aid'] = EXPERIMENT_PROFILE.titleList[aidPoped]['aid'];
        // EXPERIMENT_PROFILE.cases[i]['cover'] = EXPERIMENT_PROFILE.titleList[aidPoped]['cover'];
        EXPERIMENT_PROFILE.cases[i]['title'] = EXPERIMENT_PROFILE.titleList[aidPoped]['title'];
        EXPERIMENT_PROFILE.cases[i]['article'] = EXPERIMENT_PROFILE.titleList[aidPoped]['article'];
        EXPERIMENT_PROFILE.cases[i]['score'] = 'na';
        EXPERIMENT_PROFILE.cases[i]['change'] = 0;
    }
}


function ResetSlider() {
    if (ROUND_PROFILE.caseResult == 'na') {
        MY_FORMS.slider.noUiSlider.set(50);
        $('#slider-scoring').addClass('slider-initial-state');
        $('#slider-score-text').html('<span>拖曳上方按紐，往<span class="text-underline">左</span>表示捐款<span class="text-underline">意願低</span>，往<span class="text-underline">右</span>表示<span class="text-underline">意願高</span></span>');
    } else {
        MY_FORMS.slider.noUiSlider.set(ROUND_PROFILE.caseResult);
        $('#slider-scoring').removeClass('slider-initial-state');
        $('#slider-score-text').html(MY_TEXTS.textScoreHead + '<span class="slider-score">' + Math.floor(ROUND_PROFILE.caseResult) + MY_TEXTS.textScoreTail);
    }
}

function ResetRoundTitle() {
    ROUND_PROFILE.caseIndex = 0;
    ROUND_PROFILE.caseRound = 0;
    ROUND_PROFILE.caseAction = null;
    ROUND_PROFILE.caseResult = null;
    ROUND_PROFILE.caseId = 'na';
    ROUND_PROFILE.caseTitle = 'na';
    ROUND_PROFILE.caseStart = 0;
    ROUND_PROFILE.caseEnd = 0;
}

function ResetUserProfile() {
    // 清空不用清ip,fb,subscribe
    USER_PROFILE.uniqId = 'na';
    USER_PROFILE.timeRecording.timingType = 0;
    USER_PROFILE.timeRecording.startLanding = 0;
    USER_PROFILE.timeRecording.startQuestionaire = 0;
    USER_PROFILE.timeRecording.startSurvey = 0;
    USER_PROFILE.timeRecording.startSubscribing = 0;
    USER_PROFILE.timeRecording.startThanks = 0;
    USER_PROFILE.questionaire.gender = 'na';
    USER_PROFILE.questionaire.age = 'na';
    USER_PROFILE.questionaire.education = 'na';
    USER_PROFILE.questionaire.marriage = 'na';
    USER_PROFILE.questionaire.religion = 'na';
    USER_PROFILE.questionaire.income = 'na';
    USER_PROFILE.questionaire.charityHistory = 'na';
    USER_PROFILE.questionaire.charityTendency = 'na';
    USER_PROFILE.questionaire.charityActivity = 'na';
    USER_PROFILE.questionaire.charityWilling = 'na';
}

function setProgress() {
    $('#progress').css('width', (ROUND_PROFILE.caseIndex + 1) * 100 / EXPERIMENT_PROFILE.numCases + '%');
    $('#progress').text((ROUND_PROFILE.caseIndex + 1) + '/' + (EXPERIMENT_PROFILE.numCases));
}

function EnableNaviationBtn() {
    var currentIdx = ROUND_PROFILE.caseIndex;

    if (currentIdx == 0) {
        $('#navigate-before').addClass('disabled');
        $('#navigate-next').removeClass('disabled');
    } else {
        $('#navigate-before').removeClass('disabled');
        $('#navigate-next').removeClass('disabled');
    }

    var myScore = EXPERIMENT_PROFILE.cases[currentIdx]['score'];
    if (myScore == 'na') {
        $('#navigate-next').addClass('disabled');
    } else {
        $('#navigate-next').removeClass('disabled');
    }
}

function checkSurvey() {
    var msg = 'success';
    $.each(EXPERIMENT_PROFILE.cases, function(i, v) {
        if (v.score == 'na') {
            msg = i;
            return false;
        }
    })
    return msg;
}

function saveQuestionaire() {
    var msg = 'success';
    $.each(USER_PROFILE.questionaire, function(i, v) {
        var check_value = [];
        // console.log( i );
        if (i == 'gender' || i =='charityWilling') {
            tmp = $("#questionaire-" + i + " input[type='radio']:checked").val();
            // console.log( tmp );
            if (!tmp || tmp == null) {
                msg = i;
                return false;
            } else {
                check_value[0] = tmp;
            }
        } else {

            if (i == 'charityTendency') {
                var otherCheck = getFormData('questionaire-charityTendency');
                var otherIndex = $.map(QUESTIONAIRE_FORM_TEXTS['charityTendencyOpts'], function(val, key) {
                    if (val['label'] == '其他') {
                        return (val['index']);
                    }
                })
                var other = $('#charityTendencyOther').val();
                if ($.inArray(otherIndex + '', otherCheck) != -1) { // 選了其他 
                    if (other.length) { // 有填其他
                        SaveOther('charityTendencyOther', $('#charityTendencyOther').val());
                        USER_PROFILE.charityTendencyOther = $('#charityTendencyOther').val();
                    } else { // 沒填
                        msg = 'charityTendencyOther';
                        return false;
                    }
                }
            }

            check_value = getFormData('questionaire-' + i);
            if (!check_value.length) {
                msg = i;
                return false;
            }
        }
        USER_PROFILE.questionaire[i] = check_value;
    })
    return msg;
}

function saveSubscribe() {
    var subscribe_freq = !$("#subscribing-frequency input[type='checkbox']:checked").val() ? 0 : 1;

    if (subscribe_freq == 0) {
        USER_PROFILE.subscribe = parseInt(subscribe_freq);
        return true;
    } else {
        var subscribe = getFormData('subscribing-frequency');
        var email = $('#email').val();
        if (!subscribe.length || !$('#subscribing-frequency')[0].checkValidity()) {
            return false;
        } else {
            USER_PROFILE.subscribe = parseInt(subscribe[0]);
            USER_PROFILE.email = email;
            return true;
        }
    }
}

function getFormData(form) {
    var data = $('#' + form).serializeArray();
    var a = [];
    $.each(data, function(i, v) {
        a[i] = v.value;
    });
    return a;
}

function SetUserData() {
    setTimeout(function() {
        $.getJSON('www-data/libfm_objects/' + USER_PROFILE.fbId + '_libfm.json?nocache=' + (new Date()).getTime(), function(json) {
            // console.log(json);
            USER_PROFILE.subscribe = json['SUBSCRIBING'];
            USER_PROFILE.email = json['EMAIL'];
            USER_PROFILE.questionaire = json['USER_RAW'];
            if( json['USER_CharityTendencyOther'] != '' ){
                USER_PROFILE.charityTendencyOther = json['USER_CharityTendencyOther'];
            }
        });
    }, 10);
}

function SaveOther(name, value) {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.recordOther,
            type: 'post',
            dataType: 'json',
            data: {
                name: name,
                value: value,
                uid: USER_PROFILE.uniqId,
                fbid: USER_PROFILE.fbId
            },
            success: function(data, textStatus, jqXHR) {
                if (BOOL_VARS.isTesting) {
                    console.log('[success] SaveOther');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (BOOL_VARS.isTesting) {
                    console.log('[fail] SaveOther');
                }
                console.log(jqXHR);
                EXPERIMENT_PROFILE.exceptionMsg = ' [fail] SaveOther: ' + textStatus + ' : ' + errorThrown;
                RecordException();
            }
        });
    }, 10);
}

function RecordSubscribeInLibfm() {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.recordLibfm,
            type: 'post',
            dataType: 'json',
            data: {
                FB_ID: USER_PROFILE.fbId,
                SUBSCRIBING: USER_PROFILE.subscribe,
                EMAIL: USER_PROFILE.email,
                timeRecording: JSON.stringify(USER_PROFILE.timeRecording)
            },
            success: function(data, textStatus, jqXHR) {
                if (BOOL_VARS.isTesting) {
                    console.log('[success] RecordSubscribeInLibfm');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (BOOL_VARS.isTesting) {
                    console.log('[fail] RecordSubscribeInLibfm');
                }
                console.log(jqXHR);
                EXPERIMENT_PROFILE.exceptionMsg = ' [fail] RecordSubscribeInLibfm: ' + textStatus + ' : ' + errorThrown;
                RecordException();
            }
        });
    }, 10);
}

function RecordLibfm() {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.recordLibfm,
            type: 'post',
            dataType: 'json',
            data: {
                UNIQ_ID: USER_PROFILE.uniqId,
                FB_ID: USER_PROFILE.fbId,
                USER_QUESTIONAIRE: JSON.stringify(USER_PROFILE.questionaire),
                ROUND_RESULT: JSON.stringify(EXPERIMENT_PROFILE.cases),
                timeRecording: JSON.stringify(USER_PROFILE.timeRecording),
                USER_CharityTendencyOther: USER_PROFILE.charityTendencyOther
            },
            success: function(data, textStatus, jqXHR) {
                if (BOOL_VARS.isTesting) {
                    console.log('[success] RecordLibfm');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (BOOL_VARS.isTesting) {
                    console.log('[fail] RecordLibfm');
                }
                console.log(jqXHR);
                EXPERIMENT_PROFILE.exceptionMsg = ' [fail] RecordLibfm: ' + textStatus + ' : ' + errorThrown;
                RecordException();
            }
        });

    }, 10);
}
