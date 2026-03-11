<?php
$g5_path = realpath(dirname(__FILE__) . '/../../../../..');
include_once($g5_path . '/common.php');
include_once(dirname(__FILE__) . '/../../../filter_config.php');

header('Content-Type: application/json; charset=utf-8');

$query = isset($_POST['query']) ? trim($_POST['query']) : '';

if (!$query) {
    echo json_encode(['success' => false, 'message' => '검색어를 입력해주세요.']);
    exit;
}

// TODO: 발급받은 OpenAI API 키를 설정 파일(api_key.php)에 입력하세요.
$api_key = '';
if (file_exists(dirname(__FILE__) . '/api_key.php')) {
    include_once(dirname(__FILE__) . '/api_key.php');
}

if (empty($api_key) || $api_key === 'sk-YOUR_OPENAI_API_KEY_HERE') {
    echo json_encode(['success' => false, 'message' => 'API 키가 설정되지 않았습니다. 관리자에게 문의하여 ajax.ai_search.php 파일에 API 키를 입력해주세요.']);
    exit;
}

// 필터 태그 수집
$available_tags = array();
foreach ($tour_filters as $cat_key => $category) {
    foreach ($category['items'] as $k => $v) {
        $label = is_array($v) ? $v['label'] : $v;
        $available_tags[$k] = $label;
    }
}

// AI용 프롬프트 생성
$system_prompt = "You are a travel assistant for Danyang (단양).
The user will provide a text describing the type of travel accommodation, restaurant, or activity they are looking for.
Your task is to match their intent to our predefined tags.
Here is the JSON map of available tags {tag_key: tag_label}:
" . json_encode($available_tags, JSON_UNESCAPED_UNICODE) . "

Analyze the user's query and find ALL the corresponding tag_keys that best match their requirements.
Return ONLY a valid JSON array of strings containing the selected tag_keys (e.g. [\"stay_family\", \"spot_dodam\"]).
Do NOT output anything else (no markdown, no backticks, no explanations). If no tags match, return [].";

$data = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user', 'content' => $query]
    ],
    'temperature' => 0.0,
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response && $http_code === 200) {
    $result = json_decode($response, true);
    if (isset($result['choices'][0]['message']['content'])) {
        $content = trim($result['choices'][0]['message']['content']);
        // 안전하게 불필요한 마크다운을 제거 (가끔 AI가 룰을 어기고 주는 경우)
        $content = preg_replace('/```json/i', '', $content);
        $content = preg_replace('/```/i', '', $content);
        $content = trim($content);

        $tags = json_decode($content, true);

        if (is_array($tags)) {
            echo json_encode(['success' => true, 'tags' => array_values($tags)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'AI 응답 형식이 올바르지 않습니다. 다시 시도해주세요.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'AI 요청 중 오류가 발생했습니다.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'API 통신에 실패했습니다 (HTTP Code: ' . $http_code . '). API 키와 네트워크를 확인하세요.']);
}
