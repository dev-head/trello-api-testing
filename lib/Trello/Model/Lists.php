<?php

namespace Trello\Model\Lists;

use Trello\Model;

class Lists extends Model\Model
{
    protected $_uri = '/lists/%%list_id%%';
    protected $_request_arguments = array(
        'fields'   => '',
    );

}