<?php

namespace App\Repositories\Admin;

use App\Models\AboutPage;

class AboutPageRepository
{
    # 建構元
    public function __construct(protected AboutPage $model)
    {
    }

    /**
     * 取得單例資料
     */
    public function getSingleton(): AboutPage
    {
        return $this->model::singleton()->load('updater');
    }

    /**
     * 更新單例資料
     */
    public function update(array $data): AboutPage
    {
        $aboutPage = $this->model::singleton();
        $aboutPage->update($data);

        return $aboutPage->fresh(['updater']);
    }
}

