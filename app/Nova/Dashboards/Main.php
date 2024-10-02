<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\Events;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\BetaUsers;
use App\Nova\Metrics\Categories;

use App\Nova\Metrics\Parameters;
use Laravel\Nova\Dashboards\Main as Dashboard;


class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new NewUsers,
            new BetaUsers,
            new Categories,
            new Parameters,
            new Events,
        ];
    }
}
