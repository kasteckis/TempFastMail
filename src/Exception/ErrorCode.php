<?php

namespace App\Exception;

enum ErrorCode: int
{
    case THERE_ARE_NO_DOMAINS = 1001;
    case UNAUTHORIZED_TO_CREATE_DOMAINS = 1002;
}
