function SetTexts () {
	if (BOOL_VARS.isTesting) {
		$('#html-title-text').html(MY_TEXTS.textTestingTitle);
	} else {
		$('#html-title-text').html(MY_TEXTS.textHtmlTitle);
	}

	if ($('#logo-container').length > 0) {
		$('#logo-container').text(MY_TEXTS.textHtmlTitle);
	}

	if ($('#landing-title-text').length > 0) {
		$('#landing-title-text').text(MY_TEXTS.textProjectName);
	}

	if ($('#about-project').length > 0) {
		$('#about-project').text(MY_TEXTS.textAboutProject);
	}
	if ($('#about-project-mobile').length > 0) {
		$('#about-project-mobile').text(MY_TEXTS.textAboutProject);
	}

	if ($('#about-us').length > 0) {
		$('#about-us').text(MY_TEXTS.textAboutUs);
	}
	if ($('#about-us-mobile').length > 0) {
		$('#about-us-mobile').text(MY_TEXTS.textAboutUs);
	}
}

function MakeBodyVisible () {
	$('body').css ('visibility', 'visible');
}

function LoadSubscribingPage () {
	$(document).ready(function() {
		$.get(MY_PAGES.subscribingPage, function(data) {
			/*optional stuff to do after success */
			$('#subscribing-page').append(data);
			$('#landing-page').hide();
			$('#subscribing-page').css('visibility', 'visible');
			$(document).scrollTop(0);

			AfterLoadSubscribing ();
		});
	});
}

function LoadQuestionairePage () {
	$.get(MY_PAGES.questionairePage, function(data) {
		/*optional stuff to do after success */
		$('#questionaire-page').append(data);
		$('#subscribing-page').hide();
		$('#questionaire-page').css('visibility', 'visible');
		$(document).scrollTop(0);

		AfterLoadQuestionaire ();
	});
}

function LoadSurveyPage () {
	$.get(MY_PAGES.surveyPage, function(data) {
		/*optional stuff to do after success */
		$('#survey-page').append(data);
		$('#questionaire-page').hide();
		$('#survey-page').css('visibility', 'visible');
		$(document).scrollTop(0);

		AfterLoadSurvey ();
	});
}

function AddPushpin () {
	$('.tabs-wrapper').pushpin({
		top: $('.tabs-wrapper').offset().top
	});
}

function IniInlineSelect () {
  $(document).ready(function() {
    $('select').material_select();
    $('.select-wrapper').addClass('form-inline');
  });
}

function IniSelect () {
  $(document).ready(function() {
    $('select').material_select();
  });
}

function CreateCheckbox (myOptions, myContianer) {
	var container = $('#'+myContianer);
	for(var i=0; i<myOptions.length; i++) {
		var myElement = $("<p></p>");
		var myId = myOptions[i].id;
		var myCss = myOptions[i].css;
		var myLabel = myOptions[i].label;
		$('<input />', { type: 'checkbox', id: myId, class: 'filled-in' }).appendTo(myElement);
   		$('<label />', { 'for': myId, text: myLabel, class: 'grey-text text-darken-2' }).appendTo(myElement);
   		myElement.appendTo(container);
	}
}

function CreateRadio (myOptions, myContianer) {
	var container = $('#'+myContianer);
	for(var i=0; i<myOptions.length; i++) {
		var myElement = $("<p></p>");
		var myId = myOptions[i].id;
		var myGroup = myOptions[i].group;
		var myLabel = myOptions[i].label;
		$('<input />', { type: 'radio', id: myId, name: myGroup }).appendTo(myElement);
   		$('<label />', { 'for': myId, html: myLabel, class: 'grey-text text-darken-2' }).appendTo(myElement);
   		myElement.appendTo(container);
	}

	IniInlineSelect ();
}

function CreateSelect (myOptions, myContianer) {
	var container = $('#'+myContianer);
	var myElement = $("<select style='display:none;'></select>");
	for(var i=0; i<myOptions.length; i++) {
		var myLabel = myOptions[i].label;
		if (i == 0) {
			$('<option />', { text: myLabel }).attr({
				disabled: 'disabled',
				selected: 'selected'
			}).appendTo(myElement);
		} else {
			$('<option />', { text: myLabel }).appendTo(myElement);
		}
	}
	myElement.appendTo(container);

	IniSelect ();
}

function CreateMultiSelect (myOptions, myContianer) {
	var container = $('#'+myContianer);
	var myElement = $("<select multiple style='display:none;'></select>");
	for(var i=0; i<myOptions.length; i++) {
		var myLabel = myOptions[i].label;
		if (i == 0) {
			$('<option />', { text: myLabel }).attr({
				disabled: 'disabled',
				selected: 'selected'
			}).appendTo(myElement);
		} else {
			$('<option />', { text: myLabel }).appendTo(myElement);
		}
	}
	myElement.appendTo(container);

	IniSelect ();
}

function CreateScaleForm (leftLabel, rightLabel, scale, groupName, myContianer) {
	var container = $('#'+myContianer);
	var myElement = $("<table><tbody><tr aria-hidden='true'></tr><tr role='radiogroup'></tr></tbody></table>")
	var trScale = myElement.children().first().children().first();
	var trRadio = myElement.children().first().children().last();

	$('<td />', {addClass: 'ss-scalenumbers'}).appendTo(trScale);
	for(var i=1; i<=scale; i++) {
		var myTd = $('<td />', {addClass: 'ss-scalenumbers'});
		var myId = 'qi-' + groupName + '-' + String(i);
		$('<label />', {'for':myId, text: String(i), addClass: 'ss-scalenumbers'}).appendTo(myTd);
		myTd.appendTo(trScale);
	}
	$('<td />', {addClass: 'ss-scalenumbers'}).appendTo(trScale);

	var myLabel = $('<td />', {addClass: 'ss-scalerow'});
	$('<div />', {text: leftLabel, addClass: 'ss-scalerow scale-left-label'}).attr({
		'aria-hidden': true
	}).appendTo(myLabel);
	myLabel.appendTo(trRadio);
	for(var i=1; i<=scale; i++) {
		var myTd = $('<td />', {addClass: 'ss-scalerow'});
		var myId = 'qi-' + groupName + '-' + String(i);
		var myRadio = $('<div />', {addClass: 'ss-scalerow'});
		$('<input />', { type: 'radio', id: myId, name: groupName, addClass: 'ss-form-input' }).appendTo(myRadio);
   		$('<label />', { 'for': myId, html: '', addClass: 'ss-form-input' }).appendTo(myRadio);
		myRadio.appendTo(myTd);
		myTd.appendTo(trRadio);
	}
	var myLabel = $('<td />', {addClass: 'ss-scalerow'});
	$('<div />', {text: rightLabel, addClass: 'ss-scalerow scale-right-label'}).attr({
		'aria-hidden': true
	}).appendTo(myLabel);
	myLabel.appendTo(trRadio);

	myElement.appendTo(container);
}

function CreateSlider () {
	MY_FORMS.slider = document.getElementById('slider-scoring');

	noUiSlider.create(MY_FORMS.slider, {
		start: 50,
		behaviour: 'snap',
		connect: 'lower',
		range: {
			'min':  0,
			'max':  100
		}
	});

	$('#slider-scoring').addClass('slider-initial-state');
	MY_FORMS.slider.noUiSlider.on('slide', SliderOnSlide);
	MY_FORMS.slider.noUiSlider.on('change', SliderOnChange);
}

function SetShortcuts () {
	$(window).on('keydown', function(event) {
		switch (event.keyCode) {
			case 8: //backspace
				// console.log (event.target.tagName);
				if (event.target.tagName.search(/(input|TEXTAREA)/i) == -1) return false;
				break;
			case 81: // q -> ctrl or alt
			  if(MY_IP.indexOf("140.109.") !== -1) {
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

function TitleListLoading ()  {
	$.get(MY_URLS.titleList, function(data) {
		EXPERIMENT_PROFILE.titleList = data;
		AidListLoading ();
	});
}

function AidListLoading ()  {
	$.get(MY_URLS.aidListHighOrder, function(data) {
		if (data.length != 0) {
			EXPERIMENT_PROFILE.aidList = data.split('\n');
			EXPERIMENT_PROFILE.aidList = EXPERIMENT_PROFILE.aidList.filter(function(value){
				return value != '';
			});
		} else {
			$.get(MY_URLS.aidList, function(data2) {
				EXPERIMENT_PROFILE.aidList = data2.split('\n');
				EXPERIMENT_PROFILE.aidList = EXPERIMENT_PROFILE.aidList.filter(function(value){
					return value != '';
				});
			});
		}
	});
}

function GetRandomCharacter () {
	var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function GetCurrentTimeMilli () {
	return new Date().getTime();
}

function SetupHash () {
	EXPERIMENT_PROFILE.hashFunction = new Hashids(EXPERIMENT_PROFILE.id, 4, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
	EXPERIMENT_PROFILE.hashKey = EXPERIMENT_PROFILE.hashFunction.encode (parseInt(EXPERIMENT_PROFILE.subId));
	var randomChars = '';
	for (var i=0; i<3; i++) randomChars = randomChars + GetRandomCharacter();
	USER_PROFILE.uniqId = GetCurrentTimeMilli ().toString() + randomChars;
}

function RecordException () {
	setTimeout(function(){
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

function RecordTimeStart () {
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
					console.log ('Good in RecordTimeStart');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				if (BOOL_VARS.isTesting) {
					console.log ('Bad in RecordTimeStart');
				}

				EXPERIMENT_PROFILE.exceptionMsg = textStatus + ' : ' + errorThrown + ' : fail in RecordTimeStart';
				RecordException ();
			}
		});

	}, 10);
}

function SetupCases () {
	var ret = [];
	var aid = '', cover = '', title = '', article = '';
	for (var i=0; i<EXPERIMENT_PROFILE.numCases; i++) {
		aid = 'A1472';
		cover = 'db_covers_1590/A1472.jpg';
		title = '夫猝逝 婦扛20萬債茫然';
		article = 'db_articles_1590/A1472.htm';

		var myObj = {};
		myObj['id'] = 'case_' + (i+1).toString();
		myObj['aid'] = aid;
		myObj['cover'] = cover;
		myObj['title'] = title;
		myObj['article'] = article;
		myObj['score'] = 'na';

		ret.push (myObj);
	}

	EXPERIMENT_PROFILE.cases = ret;
}

function RandomAssignCases ()  {
	var indexPoped, aidPoped;
	for (var i=0; i<EXPERIMENT_PROFILE.cases.length; i++) {
		indexPoped = Math.floor(Math.random()*EXPERIMENT_PROFILE.aidList.length);
		aidPoped = EXPERIMENT_PROFILE.aidList.splice(indexPoped, 1);
		EXPERIMENT_PROFILE.cases[i]['aid'] = EXPERIMENT_PROFILE.titleList[aidPoped]['aid'];
		EXPERIMENT_PROFILE.cases[i]['cover'] = EXPERIMENT_PROFILE.titleList[aidPoped]['cover'];
		EXPERIMENT_PROFILE.cases[i]['title'] = EXPERIMENT_PROFILE.titleList[aidPoped]['title'];
		EXPERIMENT_PROFILE.cases[i]['article'] = EXPERIMENT_PROFILE.titleList[aidPoped]['article'];
		EXPERIMENT_PROFILE.cases[i]['score'] = 'na';
	}
}

function AssignCurrentCase () {

}

function ResetSlider () {
	if (ROUND_PROFILE.caseResult == 'na') {
		MY_FORMS.slider.noUiSlider.set(50);
		$('#slider-scoring').addClass('slider-initial-state');
		$('#slider-score-text').html('<span>請拖曳上方按紐，往<span class="text-underline">左拖曳</span>表示捐款<span class="text-underline">意願低</span>，往<span class="text-underline">右拖曳</span>表示捐款<span class="text-underline">意願高</span></span>');
	} else {
		MY_FORMS.slider.noUiSlider.set(ROUND_PROFILE.caseResult);
		$('#slider-scoring').removeClass('slider-initial-state');
		$('#slider-score-text').html(MY_TEXTS.textScoreHead+ '<span class="slider-score">' + Math.floor(ROUND_PROFILE.caseResult) + MY_TEXTS.textScoreTail);
	}
}

function EnableNaviationBtn () {
	var currentIdx = ROUND_PROFILE.caseIndex;

	if (currentIdx == 0) {
		$('#navigate-before').addClass('disabled');
		$('#navigate-next').removeClass('disabled');
	} else {
		$('#navigate-before').removeClass('disabled');
		$('#navigate-next').removeClass('disabled');
	}

	var currentIdx = ROUND_PROFILE.caseIndex;
	var myScore = EXPERIMENT_PROFILE.cases[currentIdx]['score'];
	if (myScore == 'na') {
		$('#navigate-next').addClass('disabled');
	} else {
		$('#navigate-next').removeClass('disabled');
	}
}