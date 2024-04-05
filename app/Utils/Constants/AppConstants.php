<?php

namespace App\Utils\Constants;

class AppConstants
{
    public static $pagination = 10;
    public static $appName = 'pet diaries';
    public static $subscriptionPlans = array(
        'basic' => 'BASIC',
        'premium' => 'PREMIUM',
        'pro' => 'PRO',
    );

    public static $billingCycle = array(
        'monthly' => 'MONTHLY'
    );

    public static $PetTraitType = array(
        'like' => 'LIKE',
        'dislike' => 'DISLIKE'
    );
}
