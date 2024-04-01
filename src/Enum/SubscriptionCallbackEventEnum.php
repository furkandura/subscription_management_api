<?php

namespace App\Enum;

enum SubscriptionCallbackEventEnum : string
{
    case STARTED = "started";
    case RENEWED = "renewed";
    case CANCELLED = "passive";


}
