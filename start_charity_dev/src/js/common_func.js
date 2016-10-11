function LoadQuestionairePage() {
    hideAllPage();
    showPreload();
    $(document).ready(function() {
        $.get(MY_PAGES.questionairePage, function(data) {
            $('#questionairePage').html(data);
            hideAllPage();
            if (USER_PROFILE.questionaire.gender != 'na') {
                $.each(USER_PROFILE.questionaire, function(i, v) {
                    if (i == 'gender' || i == 'charityWilling') {
                        $("#questionaire-" + i + " input[value='" + USER_PROFILE.questionaire[i][0] + "'] ").prop("checked", true);
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
                                $('#charityTendencyOther').val(USER_PROFILE.charityTendencyOther);
                            } else {
                                $('#other-input').hide();
                            }
                        }
                        $.each(v, function(v_index, v_value) {
                            $("#questionaire-" + i + " select option[value='" + v_value + "']").attr('selected', true);
                        })
                    }
                });
                $('select').material_select();
            }
            sharePlugin(false);
            $('#questionairePage').show();

            $(document).scrollTop(0);
            AfterLoadQuestionaire();
            hidePreload();
        });
    });
}

function LoadSurveyPage() {
    RandomAssignCases();
    hideAllPage();
    showPreload();
    $.get(MY_PAGES.surveyPage, function(data) {
        $('#surveyPage').html(data);
        hideAllPage();
        sharePlugin(false);
        $('#surveyPage').show();
        $(document).scrollTop(0);
        AfterLoadSurvey();
        hidePreload();
        promptMaterial('promptSurvey');
    });
}

function LoadLandPage() {
    hideAllPage();
    showPreload();
    $.get(MY_PAGES.landingPage, function(data) {
        BeforeLoadLanding();
        $('#landingPage').html(data);
        hideAllPage();
        sharePlugin(true);
        $('#landingPage').show();
        $(document).scrollTop(0);
        AfterLoadLanding();
        hidePreload();
    });
}

function LoadThankPage() {
    hideAllPage();
    showPreload();
    $(document).ready(function() {
        $.get(MY_PAGES.thankPage, function(data) {
            AfterLoadThank();
            hidePreload();
            $('#thankPage').html(data);
            $('#bounty_uid').val(USER_PROFILE.uniqId);
            $('#bounty_fbid').val(USER_PROFILE.fbId);
            hideAllPage();
            $('#thankPage').show();
            sharePlugin(true);
            $(document).scrollTop(0);
        });
    });
}

function LoadSubscribingPage(method = 'uid_from_last') {
    console.log(method);
    hideAllPage();
    showPreload();

    $.get(MY_PAGES.subscribingPage, function(data) {

        $('#subscribingPage').html(data);
        if (method == 'subscribe_email') {
            $('#subscribing-freq-container').css('display', 'none');
            $('#after-subscribing-button').off('click').on('click', ClickAfterFeedback);
        } else {
            if (USER_PROFILE.subscribe > 0) {
                $("#subscribing-frequency input[type='checkbox']").attr('checked', true);
                $('.subscribe-info-container').show();
                $('#email').val(USER_PROFILE.email);
                $("#subscribing-frequency select option[value='0']").attr('selected', false);
                $("#subscribing-frequency select option[value='" + USER_PROFILE.subscribe + "']").attr('selected', true);
            } else if (USER_PROFILE.subscribe == 0) {
                $("#subscribing-frequency input[type='checkbox']").attr('checked', false);
                $('.subscribe-info-container').hide();
                $("#subscribing-frequency select option[value='0']").attr('selected', true);
            } else {
                $("#subscribing-frequency input[type='checkbox']").attr('checked', true);
                $('.subscribe-info-container').show();
                $("#subscribing-frequency select option[value='0']").attr('selected', true);
            }

            $('select').material_select();
        }
        $(document).scrollTop(0);

        // RECOMMEND_PROFILE.aidList = ["A3939", "A3936", "A3924", "A3920", "A3929", "A3923", "A3935", "A3927", "A3919", "A3928", "A3921", "A3934", "A3932", "A3925", "A3922", "A3918", "A3930", "A3938", "A3933", "A3931", "A3917", "A3937", "A3915", "A3916"];
        // var method = 'uid_from_last';
        RecommendSet(method, function(result) {
            if (result['status'] == 'success' && result['msg'] != null) {
                RECOMMEND_PROFILE.aidList = result['msg']['aid'];
                RECOMMEND_PROFILE.prList = result['msg']['pre'];
                RECOMMEND_PROFILE.history_id = result['msg']['history_id'];
                RECOMMEND_PROFILE.uid = result['msg']['uid'];
                if (result['msg']['aid_score'] != null) {
                    RECOMMEND_PROFILE.aid_score = result['msg']['aid_score'];
                }
                CreateRecommendTable();
            } else {
                $('#recommend-container').html('<div class="center">推薦系統有誤。請連絡曙光再現計畫!</div>');
            }
            AfterLoadSubscribing();
            hidePreload();
            $('#subscribingPage').show();
        });

        sharePlugin(true);


        // var msg = checkSurvey();
        // if (msg != 'success') {

        // }

        // RandomRecommendCases();



    });
}

function showPreload() {
    $('.preloader').css('display', 'block');
    $('.preloader_image').css('display', 'block');
}

function hidePreload() {
    $('.preloader_image').css('display', 'none');
    $(".preloader").delay(200).fadeOut("slow");
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

function Particle() {
    // console.log('particles')
    particlesJS("particles-js", { "particles": { "number": { "value": 128, "density": { "enable": true, "value_area": 800 } }, "color": { "value": "#ffffff" }, "shape": { "type": "circle", "stroke": { "width": 0, "color": "#000000" }, "polygon": { "nb_sides": 5 }, "image": { "src": "img/github.svg", "width": 100, "height": 100 } }, "opacity": { "value": 1, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0, "sync": false } }, "size": { "value": 3, "random": true, "anim": { "enable": false, "speed": 4, "size_min": 0.3, "sync": false } }, "line_linked": { "enable": false, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 }, "move": { "enable": true, "speed": 1, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false, "attract": { "enable": false, "rotateX": 600, "rotateY": 600 } } }, "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "bubble" }, "onclick": { "enable": true, "mode": "bubble" }, "resize": true }, "modes": { "grab": { "distance": 400, "line_linked": { "opacity": 1 } }, "bubble": { "distance": 250, "size": 0, "duration": 2, "opacity": 0, "speed": 3 }, "repulse": { "distance": 400, "duration": 0.4 }, "push": { "particles_nb": 4 }, "remove": { "particles_nb": 2 } } }, "retina_detect": true });
}

function sharePlugin(show) {
    if (show) {
        $('.at4-share-outer').show();
    } else {
        $('.at4-share-outer').hide();
    }
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

function CreateSlider(sliderId) {
    // sliderId = 'slider-scoring';
    // console.log(sliderId);
    MY_FORMS[sliderId] = document.getElementById(sliderId);
    ROUND_PROFILE.caseType = sliderId;

    noUiSlider.create(MY_FORMS[sliderId], {
        start: 50,
        behaviour: 'snap',
        connect: 'lower',
        range: {
            'min': 0,
            'max': 100
        }
    });

    $('#' + sliderId).addClass('slider-initial-state');
    MY_FORMS[sliderId].noUiSlider.on('slide', SliderOnSlide);
    MY_FORMS[sliderId].noUiSlider.on('change', SliderOnChange);
}

function CreateRecommendTable() {
    if (RECOMMEND_PROFILE.aidList == null) {
        $('#recommend-container').html('<div class="center">推薦系統有誤。請連絡曙光再現計畫!</div>');
    } else {
        $.get(MY_URLS.titlePendingList, function(data) {
            RECOMMEND_PROFILE.titleList = data;
            var tbody = $('#recommend-container tbody');
            $(tbody).html('');
            var tbodyContent = '';
            for (var i = 0; i < 10; i++) {
                var score = RECOMMEND_PROFILE.aid_score != null && (RECOMMEND_PROFILE.aid_score[RECOMMEND_PROFILE.aidList[i]] != -1) ? RECOMMEND_PROFILE.aid_score[RECOMMEND_PROFILE.aidList[i]] : '*';
                tbodyContent = '<tr data-var="' + i + '" ><td>' + (i + 1) + '</td><td>' + RECOMMEND_PROFILE.aidList[i] + '</td><td>' + RECOMMEND_PROFILE.titleList[RECOMMEND_PROFILE.aidList[i]]['title'] + '</td><td class="recommend-score">' + score + '</td></tr>';
                $(tbody).append(tbodyContent);
            }
            $("#recommend-container tr").click(function() {
                var recommendIndex = $(this).data("var");
                var score = $(this).find('.recommend-score');
                console.log(RECOMMEND_PROFILE.cases[recommendIndex]['score']);
                var feedback_string = '';
                for (var i = 0; i < 10; i++) {
                    feedback_string += i + ':' + RECOMMEND_PROFILE.cases[i]['score'] + ', ';
                }

                $('#modalArticle').openModal({
                    in_duration: 400, // Transition in duration
                    out_duration: 400, // Transition out duration
                    ready: function() {
                        setRecommendRound(recommendIndex);
                        ResetSlider('slider-scoring-recommend');
                    },
                    complete: function() {
                        if (ROUND_PROFILE.caseResult != 'na') {
                            score.text(ROUND_PROFILE.caseResult);
                        }
                        MY_FORMS['slider-scoring-recommend'].noUiSlider.set(50);
                        $('#slider-scoring-recommend').addClass('slider-initial-state');
                        $('#slider-score-text').html('<span>拖曳上方按紐，往<span class="text-underline">左</span>表示捐款<span class="text-underline">意願低</span>，往<span class="text-underline">右</span>表示<span class="text-underline">意願高</span></span>');
                        $('#case-title-text').text('');
                        $('#article-iframe').attr('src', '');
                    }

                });
            });
            RandomRecommendCases();
        });
    }

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

function TitleListLoading(result) {
    console.log('TitleListLoading');
    $.get(MY_URLS.titleList, function(data) {
        if (data) {
            EXPERIMENT_PROFILE.titleList = data;
            QryAidList(function(aidlist) {
                    if (aidlist['status'] == 'success') {
                        EXPERIMENT_PROFILE.aidList = aidlist['msg'];
                        result(true);
                    }
                })
                // EXPERIMENT_PROFILE.aidList = Object.keys(data).sort().reverse().splice(0, EXPERIMENT_PROFILE.totalArticles);

        } else {
            result(false);
        }

    });

}

function TitlePendingLoading() {
    $.get(MY_URLS.titlePendingList, function(data) {
        RECOMMEND_PROFILE.titleList = data;
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
    RECOMMEND_PROFILE.cases = ret;
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

function RandomRecommendCases() {
    var indexPoped, aidPoped;
    for (var i = 0; i < RECOMMEND_PROFILE.numCases; i++) {
        aidPoped = RECOMMEND_PROFILE.aidList.splice(0, 1);
        RECOMMEND_PROFILE.cases[i]['aid'] = RECOMMEND_PROFILE.titleList[aidPoped]['aid'];
        RECOMMEND_PROFILE.cases[i]['title'] = RECOMMEND_PROFILE.titleList[aidPoped]['title'];
        RECOMMEND_PROFILE.cases[i]['article'] = RECOMMEND_PROFILE.titleList[aidPoped]['article'];
        if (RECOMMEND_PROFILE.aid_score != null && RECOMMEND_PROFILE.aid_score[RECOMMEND_PROFILE.cases[i]['aid']] != -1) {
            RECOMMEND_PROFILE.cases[i]['score'] = RECOMMEND_PROFILE.aid_score[RECOMMEND_PROFILE.cases[i]['aid']];
        } else {
            RECOMMEND_PROFILE.cases[i]['score'] = 'na';
        }
        RECOMMEND_PROFILE.cases[i]['change'] = 0;
    }
}

function ResetSlider(sliderId) {
    if (ROUND_PROFILE.caseResult == 'na') {
        MY_FORMS[sliderId].noUiSlider.set(50);
        $("div[id^='slider-scoring']").addClass('slider-initial-state');
        $('#slider-score-text').html('<span>拖曳上方按紐，往<span class="text-underline">左</span>表示捐款<span class="text-underline">意願低</span>，往<span class="text-underline">右</span>表示<span class="text-underline">意願高</span></span>');
    } else {
        MY_FORMS[sliderId].noUiSlider.set(ROUND_PROFILE.caseResult);
        $("div[id^='slider-scoring']").removeClass('slider-initial-state');
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
    USER_PROFILE.questionaire.career = 'na';
    USER_PROFILE.questionaire.careerUsed = 'na';
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
        if (i == ('gender' || 'charityWilling')) {
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
        return 'success';
    } else {
        var subscribe = getFormData('subscribing-frequency');
        var email = $('#email').val();
        if (!$('#subscribing-frequency')[0].checkValidity()) {
            return 'email';
        } else if (email == '') {
            return 'email';
        } else if (!subscribe.length) {
            return 'subscribe-frequency';
        } else {
            USER_PROFILE.subscribe = parseInt(subscribe[0]);
            USER_PROFILE.email = email;
            return 'success';
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
    console.log('setuserdata');
    setTimeout(function() {
        $.getJSON('www-data/libfm_objects/' + USER_PROFILE.fbId + '_libfm.json?nocache=' + (new Date()).getTime(), function(json) {
            // console.log(json);
            USER_PROFILE.subscribe = json['SUBSCRIBING'];
            USER_PROFILE.email = json['EMAIL'];
            if (json['USER_RAW'] !== undefined) {
                USER_PROFILE.questionaire = json['USER_RAW'];
                if (json['USER_CharityTendencyOther'] != '') {
                    USER_PROFILE.charityTendencyOther = json['USER_CharityTendencyOther'];
                }
            }

        });
    }, 10);
}

// function RecommendSingle(aid, score, timestamp) {
//     setTimeout(function() {
//         $.ajax({
//             url: MY_URLS.recommendList,
//             type: 'get',
//             dataType: 'json',
//             data: {
//                 fbid: USER_PROFILE.fbId,
//                 uid: USER_PROFILE.uniqId,
//                 aid: aid,
//                 score: score,
//                 timestamp: timestamp,
//                 train_method: 'uid_single_train',
//                 fb_fav_like: true,
//                 fb_cat: true,
//                 fb_catlist: true
//             },
//             success: function(data, textStatus, jqXHR) {
//                 console.log(data);
//             },
//             error: function(jqXHR, textStatus, errorThrown) {
//                 console.log(data);
//             }
//         });
//     }, 10);
// }
function Feedback(result) {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.feedback,
            type: 'post',
            dataType: 'json',
            data: {
                uid: RECOMMEND_PROFILE.uid,
                lib_his_id: RECOMMEND_PROFILE.history_id,
                feedback: JSON.stringify(RECOMMEND_PROFILE.cases),
                prList: RECOMMEND_PROFILE.prList
            },
            success: function(data, textStatus, jqXHR) {
                console.log(data);
                result(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // console.log(textStatus);
                // console.log(errorThrown);
                data = { status: 'fail' };
                result({ status: 'fail' });
            }
        });
    }, 10);
}

function RecommendSet(method, result) {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.recommendList,
            type: 'get',
            dataType: 'json',
            data: {
                fbid: USER_PROFILE.fbId,
                uid: USER_PROFILE.uniqId,
                train_method: method,
                fb_fav_like: 0,
                fb_cat: 0,
                fb_catlist: 0,
                w2v: 0,
                time_status: 0,
                history_id: RECOMMEND_PROFILE.history_id
            },
            success: function(data, textStatus, jqXHR) {
                // console.log(data);
                result(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // console.log(textStatus);
                // console.log(errorThrown);
                data = { status: 'fail' };


                result({ status: 'fail' });
            }
        });
    }, 10);
}

function QryAidList(result) {
    setTimeout(function() {
        $.ajax({
            url: MY_URLS.qryAidList,
            type: 'post',
            dataType: 'json',
            data: {
                FB_ID: USER_PROFILE.fbId,
                TOTAL_ARTICLES: EXPERIMENT_PROFILE.totalArticles,
                USER_THRESHOLD: EXPERIMENT_PROFILE.USER_THRESHOLD
            },

            success: function(data, textStatus, jqXHR) {
                console.log(data);
                result(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
                data = { status: 'fail' };


                result({ status: 'fail' });
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
                EMAIL: USER_PROFILE.email
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

function RecordLibfm(result) {
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
                USER_CharityTendencyOther: USER_PROFILE.charityTendencyOther,
                SUBSCRIBING: USER_PROFILE.subscribe,
                EMAIL: USER_PROFILE.email
            },
            success: function(data, textStatus, jqXHR) {
                result(true);
                if (BOOL_VARS.isTesting) {
                    console.log('[success] RecordLibfm');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                result(false);
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
