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
        Materialize.toast('請選擇訂閱頻率', 3000);
        return false;
    }
    RecordSubscribeInLibfm();
    LoadThankPage();
}

function ClickAfterQuestionaireBtn() {
    // TODO 測試階段先關掉必填

    var msg = saveQuestionaire();
    // if (msg != 'success') {
    //     Materialize.toast('欄位是空的 請完成', 3000);
    //     $('html, body').animate({
    //         scrollTop: $("#questionaire-"+msg).offset().top - 120
    //     }, 1000);
    //     return false;
    // }
    LoadSurveyPage();
}

function ClickAfterSurveyBtn() {
    var msg = checkSurvey();
    // if (msg != 'success') {
    //     Materialize.toast('第' + (parseInt(msg) + 1) + '回還沒完成 請完成', 3000);
    //     return false;
    // } else {
  

    if (checkMemberStatus()) {
    	LoadThankPage();
    } else {
        LoadSubscribingPage();
    }
    RecordLibfm();
    // }
}

function ClickReRound(){
	ResetUserProfile();
	ResetRoundTitle();
	SetupHash();
    // RandomAssignCases();
	LoadSurveyPage();
}

function BeforeLoadLanding() {
    /* charity index.phtml */
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
    RandomAssignCases();
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
}

function BeforeRoundStart() {
    var currentIdx = ROUND_PROFILE.caseIndex;

    ROUND_PROFILE.caseAction = [];
    ROUND_PROFILE.caseResult = EXPERIMENT_PROFILE.cases[currentIdx]['score'];
    ROUND_PROFILE.caseId = EXPERIMENT_PROFILE.cases[currentIdx]['aid'];
    ROUND_PROFILE.caseTitle = EXPERIMENT_PROFILE.cases[currentIdx]['title'];
    ROUND_PROFILE.caseArticle = EXPERIMENT_PROFILE.cases[currentIdx]['article'];
    ROUND_PROFILE.caseCover = EXPERIMENT_PROFILE.cases[currentIdx]['cover'];
    ROUND_PROFILE.caseStart = GetCurrentTimeMilli();
    ROUND_PROFILE.caseRound = currentIdx + 1;

    $('#round-text').text('第 ' + String(ROUND_PROFILE.caseRound) + ' 回合 (共 ' + String(EXPERIMENT_PROFILE.numCases) + ' 回合)');
    $('#case-title-text').text(ROUND_PROFILE.caseTitle);
    $('#article-iframe').attr('src', ROUND_PROFILE.caseArticle);
}

function AfterRoundEnd() {
    ROUND_PROFILE.caseEnd = GetCurrentTimeMilli();
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

    EnableNaviationBtn();
}

function ClickNavigateBefore() {
    // if ($('#navigate-before').hasClass('disabled')) {
    //     return false;
    // }
    var currentIdx = ROUND_PROFILE.caseIndex;
    if (currentIdx > 0) {
        ROUND_PROFILE.caseIndex = ROUND_PROFILE.caseIndex - 1;
    }

    AfterRoundEnd();
    BeforeRoundStart();
    ResetSlider();
    setProgress();

    //TODO 有沒有要限制做完才能到下一題
    EnableNaviationBtn();
}

function ClickNavigateNext() {
    // if ($('#navigate-next').hasClass('disabled')) {
    //     return false;
    // }
    var currentIdx = ROUND_PROFILE.caseIndex;
    if (currentIdx == EXPERIMENT_PROFILE.numCases - 1) {
        ClickAfterSurveyBtn();
    }
    if (currentIdx < (EXPERIMENT_PROFILE.numCases - 1)) {
        ROUND_PROFILE.caseIndex = ROUND_PROFILE.caseIndex + 1;
    }

    AfterRoundEnd();
    BeforeRoundStart();
    ResetSlider();
    setProgress();


    //TODO 有沒有要限制做完才能到下一題
    EnableNaviationBtn();
}



function CheckLoginState() {
    FB.getLoginStatus(function(response) {
        console.log('checklogin');
        StatusChangeCallback(response);
    });
}

function checkMemberStatus() {
    var req = new XMLHttpRequest();
    var url = 'www-data/libfm_objects/' + USER_PROFILE.fbId + '_libfm.json';
        req.open('GET', url, false);
    req.send();
    return req.status == 200;
}
//old-member new-member before-login 
function showStartButton() {
    if (checkMemberStatus()) {
        $('#old-member').show();
        $('#new-member').hide();
    } else {
        $('#old-member').hide();
        $('#new-member').show();
    }
    $('#before-login').hide();
    $('#check-login').hide();
}

function showLoginButton() {
    $('#old-member').hide();
    $('#before-login').show();
    $('#new-member').hide();
    $('#check-login').hide();
}

function StatusChangeCallback(response) {
    if (response.status === 'connected') {
        console.log('[success] fb connected');

        USER_PROFILE.fbToken = response.authResponse.accessToken;
        USER_PROFILE.fbId = response.authResponse.userID;

        FB.api('/me/permissions', function(response) {
            var str_response = JSON.stringify(response);

            if (str_response.indexOf('declined') == -1) {
                // TODO 登入成功 登入前button 換成 進去填表單
                RecordFbInfo();
                showStartButton();
            } else if (str_response.indexOf('error') > -1) {

                EXPERIMENT_PROFILE.exceptionMsg = 'fail in FB connect: ' + str_response;
                RecordException();

                // TODO 處理fb登入失敗 換回登入前button
                showLoginButton();
            } else {
                // TODO 換回登入前button
                showLoginButton();
            }
        });
    } else if (response.status === 'not_authorized') {
        showLoginButton();
        console.log('[fail] fb connected : fb not authorized');
    } else {
        showLoginButton();
        console.log('[fail] fb connected : fb not logged in');
    }
}

function RecordFbInfo() {
    FB.api('/me', function(response) {
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
                    console.log('[success] record fb info');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    EXPERIMENT_PROFILE.exceptionMsg = '[fail] record fb info :' + textStatus + ' : ' + errorThrown;
                    RecordException();
                    console.log(EXPERIMENT_PROFILE.exceptionMsg);
                }
            });
        }, 10);
    });
}