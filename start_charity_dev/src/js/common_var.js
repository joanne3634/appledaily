var BOOL_VARS = {
    'isTesting': true,
    'turnOffLogin': true
};

var MY_PAGES = {
    'landingPage': 'src/html/landing-page.html',
    'subscribingPage': 'src/html/subscribing-page.html',
    'questionairePage': 'src/html/questionaire-page.html',
    'surveyPage': 'src/html/survey-page.html',
    'thankPage': 'src/html/thank-page.html'
};

var USER_PROFILE = {
    'ip': 'na',
    'uniqId': 'na',
    'fbId': 'na',
    'fbToken': 'na',
    'subscribe': -1,
    'email': 'na',
    'charityTendencyOther': 'na',
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
    'totalArticles': 10,
    'USER_THRESHOLD': 10,  // 一篇文章被做過的次數上限
    'cases': null 
};

var RECOMMEND_PROFILE = {
    'uid': 'na',
    'titleList': null,
    'aidList': null,
    'numCases': 10,
    'totalArticles': 10,
    'cases': null,
    'prList': null,
    'history_id': 0,
    'aid_score': null
};


var ROUND_PROFILE = {
    'caseIndex': 0,
    'caseRound': 0,
    'caseAction': null, 
    'caseResult': null, //score
    'caseId': 'na', // aid
    'caseTitle': 'na',
    'caseStart': 0,
    'caseEnd': 0,
    'caseType': null
};

var MY_URLS = {
    'getIp': 'src/php/get_ip.php',
    'titleList': 'db_lists/titles_done.json',
    'titlePendingList': 'db_lists/titles_pending.json',
    // 'aidList': 'db_lists/aids.csv',
    // 'aidListHighOrder': 'db_lists/aids_high_order.csv', //需要優先出現在實驗中的 ID 列表
    'recordTimeStart': 'src/php/record_time_start.php',
    'recordArticleTime': 'src/php/record_article_time.php',
    'recordException': 'src/php/record_exception.php',
    'recordFbObject': 'src/php/record_fb_objects.php',
    'recordLibfm': 'src/php/record_libfm_objects.php',
    'recordOther': 'src/php/record_other.php', // 問卷紀錄其他使用者自填 
    'recommendList': 'src/php/recommend_list.php',
    'qryAidList': 'src/php/query_aid_list.php',
    'feedback': 'src/php/record_recommend_feedback.php'
};

var MY_FORMS = {
    'slider-scoring': null,
    'slider-scoring-recommend': null
}
