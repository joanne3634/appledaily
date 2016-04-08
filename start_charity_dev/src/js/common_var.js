var BOOL_VARS = {
    'isTesting': false,
    'turnOffLogin': true
};

var MY_PAGES = {
    'landingPage': 'src/html/landing-page.html',
    'subscribingPage': 'src/html/subscribing-page.html',
    'questionairePage': 'src/html/questionaire-page.html',
    'surveyPage': 'src/html/survey-page.html',
    'thankPage': 'src/html/thank-page.html'
};

// var FACEBOOK_TOKEN = 'na';
// var FACEBOOK_UID = 'na';
var USER_PROFILE = {
    'ip': 'na',
    'uniqId': 'na',
    'fbId': 'na',
    'fbToken': 'na',
    'subscribe': 0,
    'email': 'na',
    'timeRecording': {
        'timingType': 0, // 0->start, 1->end
        'startLanding': 0,
        'startQuestionaire': 0,
        'startSurvey': 0,
        'startSubscribing': 0,
        'startThanks': 0
    },
    'questionaire': {
        'gender': 'na',
        'age': 'na',
        'education': 'na',
        'marriage': 'na',
        'religion': 'na',
        'career': 'na',
        'careerUsed': 'na',
        'income': 'na',
        'charityHistory': 'na',
        'charityTendency': 'na',
        'charityActivity': 'na',
        'charityWilling': 'na'
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
    'cases': null // charity common_var var TITLES 
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
    'recordException': 'src/php/record_exception.php',
    'recordFbObject': 'src/php/record_fb_objects.php',
    'recordLibfm': 'src/php/record_libfm_objects.php',
    'recordOther': 'src/php/record_other.php'
};

var MY_FORMS = {
    'slider': null
}