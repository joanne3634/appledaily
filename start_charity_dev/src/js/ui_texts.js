var GET_CHECK_OPTS = function (arg, questionId) {
	var opts = [];
	var opt = {};

	for (var i=1; i<=arg.length; i++) {
		var opt = {};
		opt['id'] = 'qi-' + questionId + '-' + String(i);
		opt['index'] = i-1;
		opt['css'] = 'filled-in';
		opt['label'] = arg[i-1];
		opts.push(opt);
	}
	return opts;
};

var GET_SELECT_OPTS = function (arg) {
	var opts = [];
	var opt = {};

	// for 第一個空值
	opt['value'] = '';
	opts.push (opt);

	for (var i=1; i<=arg.length; i++) {
		var opt = {};
		opt['index'] = i-1;
		opt['label'] = arg[i-1];
		opts.push(opt);
	}
	return opts;
};

var GET_RADIO_OPTS = function (arg, questionId) {
	var opts = [];
	var opt = {};

	for (var i=1; i<=arg.length; i++) {
		var opt = {};
		opt['id'] = 'qi-' + questionId + '-' + String(i);
		opt['index'] = i-1;
		opt['label'] = arg[i-1];
		opt['group'] = questionId;
		opts.push(opt);
	}
	return opts;
}

var MY_TEXTS= {
	'textTestingTitle': '我是測試用!!!',
	'textHtmlTitle': '曙光再現計劃',
	'textProjectName': '急難家庭曙光再現計劃',
	'textAboutUs': '關於我們',
	'textAboutProject': '關於此計劃',
	'textIntroProject': '簡短介紹...',
	'textSubscribing': '文章訂閱',
	'textQuestionaire': '請讓我們更瞭解您 :)',
	'textScoreHead': '我有 <span class="text-underline">',
	'textScoreTail': '％ 的意願捐款</span></span>給文章內的受訪者'
};

var GET_FREQ_OPTIONS = function (arg) {
	var options = [];
	options.push('');
	for (var i=1; i<=arg; i++) {
		options.push(i);
	}
	options = options.join('</option><option>');
	options = '<option disabled selected>' + options + '</option>';
	return options;
};
var SUBSCRIBING_FREQ = {
	'week': "<select style='display:none;'>" + GET_FREQ_OPTIONS(6) + "</select>",
	'month': "<select style='display:none;'>" + GET_FREQ_OPTIONS(4) + "</select>",
	'year': "<select style='display:none;'>" + GET_FREQ_OPTIONS(12) + "</select>"
};

var SUBSCRIBING_FORM_TEXTS = {
	'methodTitle': '訂閱方式',
	'methondOpts': GET_CHECK_OPTS(['Facebook 通知', 'Email 通知'], 'subscribingMethod'),
	'freqTitle': '訂閱頻率',
	'freqOpts': GET_RADIO_OPTS(['每天一次', '每周　'+SUBSCRIBING_FREQ.week+'　次', '每月　'+ SUBSCRIBING_FREQ.month+'　次', '每年　'+SUBSCRIBING_FREQ.year+'　次'], 'subscribingFreq')
};

// 記得要連 common.php 一起改
var QUESTIONAIRE_FORM_TEXTS = {
	'genderTitle': '性別',
	'genderOpts': GET_RADIO_OPTS(['男', '女','其他'], 'gender'),
	'ageTitle': '年齡',
	'ageOpts': GET_SELECT_OPTS(['18 歲或以下', '19 - 24 歲', '25 - 34 歲', '35 - 44 歲', '45 - 54 歲', '55 - 64 歲', '65 歲或以上']),
	'educationTitle': '學歷',
	'educationOpts': GET_SELECT_OPTS(['國小', '國中', '高中', '高職', '專科', '大學', '碩士', '博士']),
	'marriageTitle': '婚姻狀況',
	'marriageOpts': GET_SELECT_OPTS(['未婚', '已婚無子女', '已婚有子女', '離婚／失婚無子女', '離婚／失婚有子女', '其他']),
	'religionTitle': '宗教信仰',
	'religionOpts': GET_SELECT_OPTS(['無', '佛教／道教', '基督教', '天主教', '伊斯蘭教', '一貫道', '其他']),
	'careerUsedTitle': '從事過行業類別',
	'careerUsedOpts': GET_SELECT_OPTS(['經營／人資類', '行銷／企劃／專案管理類', '餐飲／旅遊／美容美髮類', '操作／技術／維修類', '營建／製圖類', '文字／傳媒工作類', '學術／教育／輔導類', '生產製造／品管／環衛類', '財會／金融專業類', '行政／總務／法務類', '客服／門市／業務／貿易類', '資訊軟體系統類', '資材／物流／運輸類', '傳播藝術／設計類', '醫療／保健服務類', '研發相關類', '軍警消／保全類', '農林漁牧相關類', '其他職類']),
	'careerTitle': '目前就職狀態',
	'careerOpts': GET_SELECT_OPTS(['在職 ( 包含 soho, 接案 )','待業','學生','家管','退休']),
	'incomeTitle': '年收入區間',
	'incomeOpts': GET_SELECT_OPTS(['20 萬以下', '20 - 30 萬', '30 - 50 萬', '50 - 80 萬', '80 - 120 萬', '120 - 150 萬', '150 萬以上']),
	'charityHistoryTitle': '最近兩年的公益捐款行為',
	'charityHistoryOpts': GET_SELECT_OPTS(['平均每個月數次', '平均每個月一次', '平均每二個月一次', '平均每半年一次', '平均每年一次', '沒有印象']),
	'charityTendencyTitle': '請問您的捐款意向為何?（可複選）',
	'charityTendencyOpts': GET_SELECT_OPTS(['捐款至對您有意義或對您在乎的人有幫助或您曾受過幫助的非營利組織，例如您所就讀的母校。', '捐款至普遍大型的非營利組織，由於該組織的活動為多數人所認同，且您也容易進行捐款，例如聯合勸募。', '捐款至富理想性的非營利組織，由於您認為他們做的事是最為重要的，例如國際特赦組織。', '捐款至與您的宗教信仰符合的非營利組織，例如教會與寺廟。', '捐款至當地的非營利組織，通常您的捐款對他們而言是一筆不小的幫助，這會讓您確切感受到您的付出。', '捐款至您所熟識的人建立的非營利組織，因個人因素而讓您想幫助他(她)所建立的組織。','其他']),
	'charityActivityTitle': '除了公益捐款，請問您最近兩年還參與哪些類型的公益活動?（可複選）',
	'charityActivityOpts': GET_SELECT_OPTS(['捐贈發票', '捐贈物資', '捐血／捐骨髓', '購買公益彩卷', '環保／社區發展', '身心障礙者服務', '老人服務', '兒童服務', '動物保護', '公益健走／路跑', '課業輔導', '參與其他類型的志工活動']),
	'charityWillingTitle': '請問您樂意在社群網站 (例如 Facebook 或 Plurk) 分享公益募款文章給朋友嗎?',
	'charityTendencyOtherTitle' : '其他捐款意向'
};