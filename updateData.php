<?php

$base      = "/opt/build/repo/";
$comments = 'https://api.github.com/repos/phalcon/cphalcon/issues/14608/comments?page=';

//$base      = __DIR__ . '/'; // local

echo "Updating JSON" . PHP_EOL;
$languages = array_filter(glob($base . '4.0/*'), 'is_dir');

$languages = array_map(
    function ($element) use ($base) {
        return str_replace($base . "4.0/", "", $element);
    },
    $languages
);

foreach ($languages as $language) {
    $source = $base . '4.0/' . $language . '/meta-home.json';
    $target = $base . '_data/4-0-' . $language . '-meta-home.json';
    copy($source, $target);
    echo '.';
    $source = $base . '4.0/' . $language . '/meta-topics.json';
    $target = $base . '_data/4-0-' . $language . '-meta-topics.json';
    copy($source, $target);
    echo '.';
}

echo PHP_EOL;
echo "Updating Sponsors" . PHP_EOL;

$data = file_get_contents(
    'https://raw.githubusercontent.com/phalcon/assets/master/phalcon/sponsors-fragment.html'
);

file_put_contents(
    $base . '_includes/sponsors.html',
    $data
);

echo "Updating Fan Art" . PHP_EOL;

$data = file_get_contents(
    'https://raw.githubusercontent.com/phalcon/assets/master/phalcon/fanart-fragment.html'
);

file_put_contents(
    $base . '_includes/fanart.html',
    $data
);

echo "Updating Footer" . PHP_EOL;

$data = file_get_contents(
    'https://raw.githubusercontent.com/phalcon/assets/master/phalcon/footer-fragment.html'
);

file_put_contents(
    $base . '_includes/footer.html',
    $data
);


echo "Getting NFR Reactions" . PHP_EOL;

$result = [];
for ($i = 1; $i < 6; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $comments . $i);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Phalcon Agent');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/vnd.github.squirrel-girl-preview+json']);

    $content = curl_exec($ch);
    curl_close($ch);

    echo "Got content " . $i . PHP_EOL;
    $data = json_decode($content, true);

    foreach ($data as $comment) {
        $id        = $comment['id'] ?? '';
        $url       = $comment['html_url'] ?? '';
        $body      = $comment['body'] ?? '';
        $reactions = $comment['reactions'] ?? [];
        $plusone   = $reactions['+1'] ?? 0;
        $body = explode(PHP_EOL, $body);
        $body = str_replace(["\r", "\r\n"], "", $body[0]);

        $plusone = substr("00" . $plusone, -3);
        $result[$plusone . '-' . $id] = [
            'reaction' => $plusone,
            'body'     => "[" . $body . "](" . $url . ")",
        ];
    }
}

echo "Sorting Results" . PHP_EOL;

krsort($result);

echo "Creating Content" . PHP_EOL;

$output = "| Votes  | Description             |" . PHP_EOL;
$output .=  "|--------|-------------------------|" . PHP_EOL;
foreach ($result as $item) {
    $output .=  '| ' . $item['reaction'] . ' | ' . $item['body'] . ' |' . PHP_EOL;
}

$output .= PHP_EOL;

foreach (glob($base . '4.0/*', GLOB_ONLYDIR) as $language) {
    echo "Processing language " . $language . PHP_EOL;
    $fileName = $language . '/new-feature-request-list.md';
    $existing = file_get_contents($fileName);
    $existing .= PHP_EOL . PHP_EOL . $output;
    file_put_contents($fileName, $existing);
}