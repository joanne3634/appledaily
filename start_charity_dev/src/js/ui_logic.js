function ClickStartBtn() {
    LoadQuestionairePage();
}

function ClickLoginBtn() {
    FB.login(CheckLoginState, {
        scope: 'public_profile,user_friends,user_likes',
        auth_type: 'rerequest'
    });
}

function ClickAfterSubscribingBtn() {
    if (!saveSubscribe()) {
        Materialize.toast('欄位有錯或是空的', 3000);
        return false;
    }
    // RecordSubscribeInLibfm();
    LoadThankPage();
}

function ClickAfterQuestionaireBtn() {
    var msg = saveQuestionaire();
    if (!BOOL_VARS.isTesting) {
        if (msg != 'success') {
            console.log(QUESTIONAIRE_FORM_TEXTS[msg + 'Title']);
            Materialize.toast('請完成欄位: ' + QUESTIONAIRE_FORM_TEXTS[msg + 'Title'], 3000);
            $('html, body').animate({
                scrollTop: $("#questionaire-" + msg).offset().top - 120
            }, 1000);
            return false;
        }
    }
    LoadSurveyPage();
}

function ClickAfterSurveyBtn() {
    var msg = checkSurvey();
    if (!BOOL_VARS.isTesting) {
        if (msg != 'success') {
            Materialize.toast('第' + (parseInt(msg) + 1) + '回還沒完成 請完成', 3000);
            return false;
        }
    }
    if (checkMemberStatus()) {
        LoadThankPage();
    } else {
        LoadSubscribingPage();
    }
    RecordLibfm();
}

function ClickReRound() {
    ResetUserProfile();
    ResetRoundTitle();
    SetupHash();
    LoadSurveyPage();
}

function BeforeLoadLanding() {
    GetIp();
    SetShortcuts();
    TitleListLoading();
    SetupHash();
    SetupCases();
}

function AfterLoadLanding() {
    USER_PROFILE.timeRecording.timingType = 0;
    USER_PROFILE.timeRecording.startLanding = GetCurrentTimeMilli();
    RecordTimeStart();
}

function AfterLoadSubscribing() {
    USER_PROFILE.timeRecording.startSubscribing = GetCurrentTimeMilli();
    RecordTimeStart();
}

function AfterLoadQuestionaire() {
    USER_PROFILE.timeRecording.startQuestionaire = GetCurrentTimeMilli();
    RecordTimeStart();
}

function AfterLoadSurvey() {
    USER_PROFILE.timeRecording.startSurvey = GetCurrentTimeMilli();

    RecordTimeStart();

    BeforeRoundStart();

    CreateSlider();
    setProgress();
}

function AfterLoadThank() {
    USER_PROFILE.timeRecording.startThanks = GetCurrentTimeMilli();
    USER_PROFILE.timeRecording.timingType = 1;
    RecordTimeStart();
    RecordSubscribeInLibfm();
}

function BeforeRoundStart() {
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
    $('#case-title-text').text(ROUND_PROFILE.caseTitle);
    $('#article-iframe').attr('src', ROUND_PROFILE.caseArticle)
        .css({ width: $('body').width() > 768 ? $('body').width() * 0.7 : $('body').width(), height: 0 })
        .load(function() {
            $(this).contents().find('.mpatc').css("padding", $('.fix-slider').height() + 60 + "px 20px");
            $(this).css("height", $(this).contents().find('.mpatc').height() + 260 + 'px');
        });
}

function AfterRoundEnd() {
    ROUND_PROFILE.caseEnd = GetCurrentTimeMilli();
    RecordArticleTime(ROUND_PROFILE.caseStart, ROUND_PROFILE.caseEnd, ROUND_PROFILE.caseId, ROUND_PROFILE.caseRound);
}

function SliderOnSlide() {
    $('#slider-scoring').removeClass('slider-initial-state');

    var sliderValue = MY_FORMS.slider.noUiSlider.get();
    $('#slider-score-text').html(MY_TEXTS.textScoreHead + '<span class="slider-score">' + Math.floor(sliderValue) + MY_TEXTS.textScoreTail);
}

function SliderOnChange() {
    var sliderValue = MY_FORMS.slider.noUiSlider.get();
    ROUND_PROFILE.caseResult = sliderValue;

    var currentIdx = ROUND_PROFILE.caseIndex;
    EXPERIMENT_PROFILE.cases[currentIdx]['score'] = sliderValue;
    EXPERIMENT_PROFILE.cases[currentIdx]['change'] = GetCurrentTimeMilli();

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
    ResetSlider();
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
        promptMaterial('modal1');
    }
    if (currentIdx < (EXPERIMENT_PROFILE.numCases - 1)) {
        ROUND_PROFILE.caseIndex = ROUND_PROFILE.caseIndex + 1;
    }

    AfterRoundEnd();
    BeforeRoundStart();
    ResetSlider();
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
        StatusChangeCallback(response);
    });
}

function checkMemberStatus() {
    var req = new XMLHttpRequest();
    var url = 'www-data/libfm_objects/' + USER_PROFILE.fbId + '_libfm.json?nocache=' + (new Date()).getTime();
    req.open('GET', url, false);
    req.send();

    return req.status == 200;
}

function showStartButton() {
    if (checkMemberStatus()) {
        $("div[id^='old-member']").show();
        $("div[id^='new-member']").hide();
    } else {
        $("div[id^='old-member']").hide();
        $("div[id^='new-member']").show();
    }
    $("div[id^='before-login']").hide();
    $("div[id^='check-login']").hide();
}

function showLoginButton() {
    $("div[id^='old-member']").hide();
    $("div[id^='before-login']").show();
    $("div[id^='new-member']").hide();
    $("div[id^='check-login']").hide();
}

function StatusChangeCallback(response) {
    if (response.status === 'connected') {
        // console.log('[success] fb connected');
        FB.api('/me', function(res) {
            USER_PROFILE.fbToken = response.authResponse.accessToken;
            USER_PROFILE.fbId = res.id;
            console.log(res.id);
            SetUserData();
            FB.api('/me/permissions', function(response) {
                var str_response = JSON.stringify(response);
                // console.log( response );
                if (str_response.indexOf('declined') == -1) {
                    RecordFbInfo();
                    showStartButton();
                } else if (str_response.indexOf('error') > -1) {
                    EXPERIMENT_PROFILE.exceptionMsg = 'fail in FB connect: ' + str_response;
                    RecordException();
                    showLoginButton();
                } else {
                    showLoginButton();
                }
            });
        });
        // console.log( response );

        // USER_PROFILE.fbToken = response.authResponse.accessToken;
        // USER_PROFILE.fbId = response.authResponse.userID;
        // SetUserData();


    } else if (response.status === 'not_authorized') {
        showLoginButton();
        if (BOOL_VARS.isTesting) {
            console.log('[fail] fb connected : fb not authorized');
        }
    } else {
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
