<?php

namespace Trello\Model\Cards;

use Trello\Model;

class Cards extends Model\Model
{
    protected $_uri = '/cards/%%id%%';
    protected $_request_arguments = array(
        'fields'   => 'name,url,desc',
        'actions'    => 'commentCard',
    );

}