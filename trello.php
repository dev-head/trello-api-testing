<?php


/**
 * Example usage for testing potential use cases.
 *
 *
 *
 */
include_once 'lib/Trello.php';

/**
 * @key: https://trello.com/app-key
 * @token: 
 * 
 */
$auth_options   = array(
    'api_version'   => 1,
    'key'           => '',
    'token'         => '',
);

$api    = new Trello\Trello($auth_options);
$data   = array(
    'board_id'  => '4e99eb7aa9797361bc22e6ce', // https://trello.com/b/rq2mYJNn/public-trello-boards.json
);

$cards  	= array();
$board 		= $api->getBoards($data);
$lists 		= $api->getBoardsLists($data);
$lists		= $lists->getResponseData();
$list_id    = null;
$list_name  = 'To Do';

for ($i = 0; $i < $c = count($lists); $i++) {
    if ($lists[$i]['name'] == $list_name) {
        $list_id    = $lists[$i]['id'];
        break;
    }
}

if ($list_id) {
    $cards  = $api->getListsCards(array('list_id' => $list_id));
    $cards  = $cards->getResponseData();
}

$output = null;
$EOL    = "\n";
$TAB    = " ";

if ($cards) {
    for ($i = 0; $i < $c = count($cards); $i++) {
        $card   = $api->getCards(array('id' => $cards[$i]['id']));
        $card   = $card->getResponseData();
        $files  = array();
		$output	.= $card['name'] . $EOL . $card['url'] . $EOL . $EOL;

        if (isset($card['actions']) && isset($card['actions'][0])) {
        
            // We're using the comment action right now, so we're going to look 
            // through all that data to find comments w/files.
            for ($ii = 0; $ii < $c = count($card['actions']); $ii++) {
                $comment    = isset($card['actions'][$ii]['data']) && isset($card['actions'][$ii]['data']['text'])? $card['actions'][$ii]['data']['text'] : null;

                // We're going to parse out the comment and see if there's any file data we can use.
                if ($comment) {
					$output .= $comment . $EOL . $EOL;
                    $matches    = array();
                    $check      = '/^[^*?"<>|:]*$/i';
                    $check      = "/.php$/";
                    $check  = '/\\\(.php?)\n/';
                    preg_match_all($check, $comment, $matches);
                    $files[]  = $matches;
                }
            }
        }
        
        $card['files']  = $files;
        $cards[$i]  = array_merge($cards[$i], $card);
        $output .= "+++++++++++++++++++++++++++++++++++++++" . $EOL . $EOL;
    }
}


/**
 * Testing output
 */
echo $output;