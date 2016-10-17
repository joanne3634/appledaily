function ClickStartBtn() {
    LoadQuestionairePage();
}

function ClickLoginBtn() {
    FB.login(CheckLoginState, {
        scope: 'public_profile,user_friends,user_likes',
        auth_type: 'rerequest'
    });
}

function ClickFBStatusBtn() {

    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            FB.logout(function(response) {
                // this part just clears the $_SESSION var
                // replace with your own code
                LoadLandPage();
            });
        } else {
            promptMaterial('promptLogin');
            // ClickLoginBtn();
        }
    });
}

function ClickAfterSubscribingBtn() {
    var msg = saveSubscribe();
    if (msg != 'success') {
        if (msg == 'subscribe-frequency') {
            Materialize.toast('收信頻率未選', 3000);
        }
        if (msg == 'email') {
            Materialize.toast('信箱有誤', 3000);
        }
        // Materialize.toast(msg, 3000);
        $('html, body').animate({
            scrollTop: $("#" + msg).offset().top - 120
        }, 1000);
        return false;
    }else{
        Feedback( function(result) {});
        LoadThankPage();
    }
    // RecordSubscribeInLibfm();
}
function ClickAfterFeedback(){
    Feedback( function(result) {});
    window.location.assign(window.location.pathname);
}
function ClickAfterQuestionaireBtn() {
    var msg = saveQuestionaire();
    if (!BOOL_VARS.isTesting) {
        if (msg != 'success') {
            //console.log(QUESTIONAIRE_FORM_TEXTS[msg + 'Title']);
            Materialize.toast('請完成欄位: ' + QUESTIONAIRE_FORM_TEXTS[msg + 'Title'], 3000);

            $('html, body').animate({
                scrollTop: $("#questionaire-" + msg).offset().top - 120
            }, 1000);
            return false;
        }
    }
    promptMaterial('modalQuestion');
}

function ClickAfterSurveyBtn() {
    checkMemberStatus(function(isMember) {
        if (isMember) {
            LoadThankPage();
        } else {
            LoadSubscribingPage();
        }
    })
    RecordLibfm(function(Done) {});
}

function ClickReRound() {
    // ResetUserProfile();
    TitleListLoading(function(result) {
        if (result) {
            ResetRoundTitle();
            SetupHash();
            LoadSurveyPage();
        }
    });

}

function BeforeLoadLanding() {
    // console.log('BeforeLoadLanding');
    GetIp();
    SetShortcuts();
    TitleListLoading(function(result) {});
    // ResetUserProfile();
    ResetRoundTitle();
    SetupHash();
    SetupCases();
}

function AfterLoadLanding() {
    Particle();
    USER_PROFILE.timeRecording.timingType = 0;
    USER_PROFILE.timeRecording.startLanding = GetCurrentTimeMilli();
    RecordTimeStart();
}

function AfterLoadSubscribing() {
    USER_PROFILE.timeRecording.startSubscribing = GetCurrentTimeMilli();
    RecordTimeStart();

    BeforeRoundStart();
    CreateSlider('slider-scoring-recommend');
}

function AfterLoadQuestionaire() {
    USER_PROFILE.timeRecording.startQuestionaire = GetCurrentTimeMilli();
    RecordTimeStart();
}

function AfterLoadSurvey() {
    USER_PROFILE.timeRecording.startSurvey = GetCurrentTimeMilli();

    RecordTimeStart();

    BeforeRoundStart();

    CreateSlider('slider-scoring');
    setProgress();
}

function AfterLoadThank() {
    USER_PROFILE.timeRecording.startThanks = GetCurrentTimeMilli();
    USER_PROFILE.timeRecording.timingType = 1;
    RecordTimeStart();
    if( USER_PROFILE.subscribe != -1 ){
        RecordSubscribeInLibfm();
    }
}

function BeforeRoundStart() {
    $('#surveyPage .preloader_image').css('display', 'block');
    var currentIdx = ROUND_PROFILE.caseIndex;

    ROUND_PROFILE.caseAction = [];
    ROUND_PROFILE.caseResult = EXPERIMENT_PROFILE.cases[currentIdx]['score'];
    ROUND_PROFILE.caseId = EXPERIMENT_PROFILE.cases[currentIdx]['aid'];
    ROUND_PROFILE.caseTitle = EXPERIMENT_PROFILE.cases[currentIdx]['title'];
    ROUND_PROFILE.caseArticle = EXPERIMENT_PROFILE.cases[currentIdx]['article'];
    // ROUND_PROFILE.caseCover = EXPERIMENT_PROFILE.cases[currentIdx]['cover'];
    ROUND_PROFILE.caseStart = GetCurrentTimeMilli();
    ROUND_PROFILE.caseRound = currentIdx + 1;
    // $('#round-text').text('第 ' + String(ROUND_PROFILE.caseRound) + ' 回合 (共 ' + String(EXPERIMENT_PROFILE.numCases) + ' 回合)');
    $('#case-title-text-survey').text(ROUND_PROFILE.caseTitle);
    $('#article-iframe-survey').attr('src', ROUND_PROFILE.caseArticle)
        .css({ width: $('body').width() > 768 ? ($('#surveyPage .slider-row').width() + 20 + "px") : $('body').width(), height: 0 })
        .load(function() {
            $(this).contents().find('.mpatc').css("padding", $('#surveyPage .fix-slider').height() + "px .8em");
            $(this).css("height", $(this).contents().find('.mpatc').height() + 200 + 'px');
            $('#surveyPage .preloader_image').css('display', 'none');
        });
}

function setRecommendRound(currentIdx) {
    $('#modalArticle .preloader_image').css('display', 'block');

    ROUND_PROFILE.caseIndex = currentIdx;
    ROUND_PROFILE.caseAction = [];
    ROUND_PROFILE.caseResult = RECOMMEND_PROFILE.cases[currentIdx]['score'];
    ROUND_PROFILE.caseId = RECOMMEND_PROFILE.cases[currentIdx]['aid'];
    ROUND_PROFILE.caseTitle = RECOMMEND_PROFILE.cases[currentIdx]['title'];
    ROUND_PROFILE.caseArticle = RECOMMEND_PROFILE.cases[currentIdx]['article'];
    // ROUND_PROFILE.caseCover = EXPERIMENT_PROFILE.cases[currentIdx]['cover'];
    ROUND_PROFILE.caseStart = GetCurrentTimeMilli();
    ROUND_PROFILE.caseRound = currentIdx + 1;
    // $('#round-text').text('第 ' + String(ROUND_PROFILE.caseRound) + ' 回合 (共 ' + String(EXPERIMENT_PROFILE.numCases) + ' 回合)');
    $('#case-title-text').text(ROUND_PROFILE.caseTitle);
    $('#article-iframe').attr('src', ROUND_PROFILE.caseArticle)
        .css({ width: $('.modal').width() - 20 + 'px', height: 0 })
        .load(function() {
            var slider_height = $('#modalArticle .fix-slider').css('height');
            $('#modalArticle article .modal-content').css('top', slider_height);
            $(this).contents().find('.mpatc').css({ "padding": "0px 1.2em", "font-size": ".9em" });
            $(this).contents().find('.trans').css("font-size", "1em");
            $(this).css("height", $(this).contents().find('.mpatc').height() + 'px');
            $(this).css("background-color", "white");
            $('#modalArticle .preloader_image').css('display', 'none');
        });
}

function AfterRoundEnd() {
    ROUND_PROFILE.caseEnd = GetCurrentTimeMilli();
    RecordArticleTime(ROUND_PROFILE.caseStart, ROUND_PROFILE.caseEnd, ROUND_PROFILE.caseId, ROUND_PROFILE.caseRound);
}

function SliderOnSlide() {
    sliderId = ROUND_PROFILE.caseType;
    // console.log(sliderId + ':sliderchange');
    $('#' + sliderId).removeClass('slider-initial-state');
    var sliderValue = MY_FORMS[sliderId].noUiSlider.get();
    $('#slider-score-text').html(MY_TEXTS.textScoreHead + '<span class="slider-score">' + Math.floor(sliderValue) + MY_TEXTS.textScoreTail);
}

function SliderOnChange() {
    sliderId = ROUND_PROFILE.caseType;
    var sliderValue = MY_FORMS[sliderId].noUiSlider.get();
    ROUND_PROFILE.caseResult = sliderValue;
    var currentIdx = ROUND_PROFILE.caseIndex;
    EXPERIMENT_PROFILE.cases[currentIdx]['score'] = sliderValue;
    EXPERIMENT_PROFILE.cases[currentIdx]['change'] = GetCurrentTimeMilli();
    RECOMMEND_PROFILE.cases[currentIdx]['score'] = sliderValue;
    RECOMMEND_PROFILE.cases[currentIdx]['change'] = GetCurrentTimeMilli();

    var slider_height = $('#modalArticle .fix-slider').css('height');
    $('#modalArticle article .modal-content').css('top', slider_height);

    EnableNaviationBtn();
}

function ClickNavigateBefore() {
    if (!BOOL_VARS.isTesting) {
        if ($('#navigate-before').hasClass('disabled')) {
            return false;
        }
    }
    var currentIdx = ROUND_PROFILE.caseIndex;
    if (currentIdx > 0) {
        ROUND_PROFILE.caseIndex = ROUND_PROFILE.caseIndex - 1;
    }

    AfterRoundEnd();
    BeforeRoundStart();
    ResetSlider('slider-scoring');
    setProgress();
    EnableNaviationBtn();
}

function ClickNavigateNext() {
    if (!BOOL_VARS.isTesting) {
        if ($('#navigate-next').hasClass('disabled')) {
            return false;
        }
    }
    var currentIdx = ROUND_PROFILE.caseIndex;
    if (currentIdx == EXPERIMENT_PROFILE.numCases - 1) {
        var msg = checkSurvey();
        if (!BOOL_VARS.isTesting) {
            if (msg != 'success') {
                Materialize.toast('第' + (parseInt(msg) + 1) + '回還沒完成 請完成', 3000);
                return false;
            }else{
                promptMaterial('modal1');
            }
        }
    }
    if (currentIdx < (EXPERIMENT_PROFILE.numCases - 1)) {
        ROUND_PROFILE.caseIndex = ROUND_PROFILE.caseIndex + 1;
    }

    AfterRoundEnd();
    BeforeRoundStart();
    ResetSlider('slider-scoring');
    setProgress();
    EnableNaviationBtn();
}

function promptMaterial(id) {
    $('#' + id).openModal({
        dismissible: true,
        opacity: .6,
        in_duration: 300,
        out_duration: 500
    });
}


function CheckLoginState() {
    FB.getLoginStatus(function(response) {
        // console.log('checklogin');
        StatusChangeCallback(response,function(fb){
            console.log('fb: '+fb);
        });
    });
}

function checkMemberStatus(result) {
    $.ajax({
        type: 'GET',
        url: 'www-data/libfm_objects/' + USER_PROFILE.fbId + '_libfm.json?nocache=' + (new Date()).getTime(), // Your form script
        success: function(response, textS, xhr) {
            result(true);
        },
        error: function(xmlHttpRequest, textStatus, errorThrown) {
            result(false);
        }
    });
}

function showStartButton() {
    checkMemberStatus(function(isMember) {
        if (isMember) {
            $("div[id^='old-member']").show();
            $("a[id^='update-subscribe-button']").show();
            $("div[id^='new-member']").hide();
        } else {
            $("div[id^='old-member']").hide();
            $("a[id^='update-subscribe-button']").hide();
            $("div[id^='new-member']").show();
        }
        $("div[id^='before-login']").hide();
        $("div[id^='check-login']").hide();
    })
}

function showLoginButton() {
    $("div[id^='old-member']").hide();
    $("a[id^='update-subscribe-button']").hide();
    $("div[id^='before-login']").show();
    $("div[id^='new-member']").hide();
    $("div[id^='check-login']").hide();
    $("a[id^='fb-status']").text('登入');
}

function StatusChangeCallback(response,result) {
    // console.log('fb_check');
    if (response.status === 'connected') {

        FB.api('/me', function(res) {
            USER_PROFILE.fbToken = response.authResponse.accessToken;
            USER_PROFILE.fbId = res.id;
            $('#contact_fb_name').val(res.name);
            $('#contact_fb_link').val(res.link);
            result('showStart');
            // console.log(res.id);
            SetUserData();
            FB.api('/me/permissions', function(response) {
                var str_response = JSON.stringify(response);
                // console.log( response );
                if (str_response.indexOf('declined') == -1) {
                    showStartButton();
                    $("a[id^='fb-status']").text('登出');
                    RecordFbInfo();
                } else if (str_response.indexOf('error') > -1) {
                    EXPERIMENT_PROFILE.exceptionMsg = 'fail in FB connect: ' + str_response;
                    RecordException();
                    showLoginButton();
                } else {
                    showLoginButton();
                }
            });
        });

    } else if (response.status === 'not_authorized') {
        result('showLogin');
        showLoginButton();
        if (BOOL_VARS.isTesting) {
            console.log('[fail] fb connected : fb not authorized');
        }
    } else {
        result('showLogin');
        showLoginButton();
        if (BOOL_VARS.isTesting) {
            console.log('[fail] fb connected : fb not logged in');
        }
    }
}

function RecordFbInfo() {
    FB.api('/me', function(response) {
        console.log(response);
        // console.log( USER_PROFILE.fbId );
        console.log('[success] login facebook for: ' + response.name + ' ... start record info');
        setTimeout(function() {
            $.ajax({
                url: MY_URLS.recordFbObject,
                cache: false,
                type: 'post',
                dataType: 'json',
                data: {
                    UNIQ_ID: USER_PROFILE.uniqId,
                    FACEBOOK_UID: USER_PROFILE.fbId,
                    FACEBOOK_TOKEN: USER_PROFILE.fbToken
                },
                success: function(data, textStatus, jqXHR) {
                    if (BOOL_VARS.isTesting) { console.log('[success] record fb info'); }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    EXPERIMENT_PROFILE.exceptionMsg = '[fail] record fb info :' + textStatus + ' : ' + errorThrown;
                    RecordException();
                    if (BOOL_VARS.isTesting) { console.log(EXPERIMENT_PROFILE.exceptionMsg); }
                }
            });
        }, 10);
    });
}
