<?php

namespace Trello\Model\Lists\Cards;

use Trello\Model\Lists;

class Cards extends Lists\Lists
{
    protected $_uri = '/lists/%%list_id%%/cards';
    protected $_request_arguments = array(
        'fields'   => 'name',
    );
}
