var BOOL_VARS = {
	'isTesting': true,
	'turnOffLogin': true
};

var MY_PAGES = {
	'landingPage': 'src/html/landing-page.html',
	'subscribingPage': 'src/html/subscribing-page.html',
	'questionairePage': 'src/html/questionaire-page.html',
	'surveyPage': 'src/html/survey-page.html'
};

var USER_PROFILE = {
	'ip': 'na',
	'uniqId': 'na',
	'fbId': 'na',
	'timeRecording': {
		'timingType': 0, // 0->start, 1->end
		'startLanding': 0,
		'startSubscribing': 0,
		'startQuestionaire': 0,
		'startSurvey': 0,
		'startRecommendation': 0,
		'startThanks': 0
	}
};

var EXPERIMENT_PROFILE = {
	'id': 'na',
	'subId': '20160104',
	'titleList': null,
	'aidList': null,
	'hashFunction': null,
	'hashKey': 'na',
	'exceptionMsg': 'na',
	'numCases': 10,
	'cases': null
};

var ROUND_PROFILE = {
	'caseIndex': 0,
	'caseRound': 0,
	'caseAction': null,
	'caseResult': null,
	'caseId': 'na',
	'caseTitle': 'na',
	'caseStart': 0,
	'caseEnd': 0
};

var MY_URLS = {
	'getIp': 'src/php/get_ip.php',
	'titleList': 'db_lists/titles_1590.json',
	'aidList': 'db_lists/aids.csv',
	'aidListHighOrder': 'db_lists/aids_high_order.csv',
	'recordTimeStart': 'src/php/record_time_start.php',
	'recordException': 'src/php/record_exception.php'
};

var MY_FORMS = {
	'slider': null
}