function ClickStartBtn() {
	LoadSubscribingPage ();
}

function ClickAfterSubscribingBtn () {
	LoadQuestionairePage ();
}

function ClickAfterQuestionaireBtn () {
	LoadSurveyPage ();
}

function BeforeLoadLanding () {
	$.get(MY_URLS.getIp, function(data){
		USER_PROFILE.ip = data.ip;
	}, 'json');

	EXPERIMENT_PROFILE.id = MY_URLS.titleList.split ('/')[1];

	SetShortcuts ();

	TitleListLoading ();

	SetupHash ();

	SetupCases ();
}

function AfterLoadLanding () {
	USER_PROFILE.timeRecording.timingType = 0;
	USER_PROFILE.timeRecording.startLanding = GetCurrentTimeMilli();

	RecordTimeStart ();
}

function AfterLoadSubscribing () {
	USER_PROFILE.timeRecording.startSubscribing = GetCurrentTimeMilli();

	RecordTimeStart ();

	RandomAssignCases ();
}

function AfterLoadQuestionaire () {
	USER_PROFILE.timeRecording.startQuestionaire = GetCurrentTimeMilli();

	RecordTimeStart ();
}

function AfterLoadSurvey () {
	USER_PROFILE.timeRecording.startSurvey = GetCurrentTimeMilli();

	RecordTimeStart ();

	BeforeRoundStart ();

	CreateSlider ();
}

function BeforeRoundStart () {
	var currentIdx = ROUND_PROFILE.caseIndex;

	ROUND_PROFILE.caseAction = [];
	ROUND_PROFILE.caseResult = EXPERIMENT_PROFILE.cases[currentIdx]['score'];
	ROUND_PROFILE.caseId = EXPERIMENT_PROFILE.cases[currentIdx]['aid'];
	ROUND_PROFILE.caseTitle = EXPERIMENT_PROFILE.cases[currentIdx]['title'];
	ROUND_PROFILE.caseArticle = EXPERIMENT_PROFILE.cases[currentIdx]['article'];
	ROUND_PROFILE.caseCover = EXPERIMENT_PROFILE.cases[currentIdx]['cover'];
	ROUND_PROFILE.caseStart = GetCurrentTimeMilli ();
	ROUND_PROFILE.caseRound = currentIdx + 1;

	$('#round-text').text ('第 ' + String(ROUND_PROFILE.caseRound) + ' 回合 (共 ' + String(EXPERIMENT_PROFILE.numCases) + ' 回合)');
	$('#case-title-text').text (ROUND_PROFILE.caseTitle);
	$('#article-iframe').attr('src', ROUND_PROFILE.caseArticle);
}

function AfterRoundEnd () {
	ROUND_PROFILE.caseEnd = GetCurrentTimeMilli ();
}

function SliderOnSlide () {
	$('#slider-scoring').removeClass('slider-initial-state');

	var sliderValue = MY_FORMS.slider.noUiSlider.get();
	$('#slider-score-text').html(MY_TEXTS.textScoreHead+ '<span class="slider-score">' + Math.floor(sliderValue) + MY_TEXTS.textScoreTail);
}

function SliderOnChange () {
	var sliderValue = MY_FORMS.slider.noUiSlider.get();
	ROUND_PROFILE.caseResult = sliderValue;

	var currentIdx = ROUND_PROFILE.caseIndex;
	EXPERIMENT_PROFILE.cases[currentIdx]['score'] = sliderValue;

	EnableNaviationBtn ();
}

function ClickNavigateBefore () {
	var currentIdx = ROUND_PROFILE.caseIndex;
	if (currentIdx > 0) {
		ROUND_PROFILE.caseIndex = ROUND_PROFILE.caseIndex - 1;
	}

	AfterRoundEnd ();
	BeforeRoundStart ();
	ResetSlider ();

	EnableNaviationBtn ();
}

function ClickNavigateNext () {
	var currentIdx = ROUND_PROFILE.caseIndex;
	if (currentIdx < (EXPERIMENT_PROFILE.numCases-1)) {
		ROUND_PROFILE.caseIndex = ROUND_PROFILE.caseIndex + 1;
	}

	AfterRoundEnd ();
	BeforeRoundStart ();
	ResetSlider ();

	EnableNaviationBtn ();
}