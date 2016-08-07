<?php

namespace Trello\Model\Boards;

use Trello\Model;

class Boards extends Model\Model
{
    protected $_uri = '/boards/%%board_id%%';
    protected $_request_arguments = array();

}