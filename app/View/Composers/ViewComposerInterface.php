<?php declare(strict_types=1);

namespace REBELinBLUE\Deployer\View\Composers;

use Illuminate\Contracts\View\View;

interface ViewComposerInterface
{
    /**
     * @param View $view
     */
    public function compose(View $view);
}
