<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\Admin\CountryService;
use App\Services\Admin\AdminLogService;
use App\Services\Share\MessageService;

class CountryController extends Controller
{
    const POST_SESSION = 'country_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected CountryService $service,
        protected AdminLogService $logService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 列表頁面
     */
    public function list(Request $request)
    {
        # 篩選條件
        $filters = $request->only(['name', 'country_code', 'is_active']);

        # 取得分頁資料
        $result = $this->service->fetchPaginatedData($filters);

        $this->settingService->setSetData('data', $result['data']);
        $this->settingService->setSetData('pagination', $result['pagination']);
        $this->settingService->setSetData('filters', $filters);
        $this->settingService->setSetData('statusOptions', config('constants.status'));

        return view('admin/country/list', $this->settingService->fetchSetData());
    }

    /**
     * 編輯頁面
     * @param int $id
     */
    public function edit(int $id)
    {
        try {
            $data = ($id > 0) ? $this->service->fetchDataByID($id) : [];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $this->settingService->setSetData('data', $data);
            return view('admin/country/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/country/list');
        }
    }

    /**
     * 編輯實作
     */
    public function editDo(Request $request)
    {
        # 正規化再驗證（確保 unique 比對正確）
        $request->merge([
            'country_code' => strtoupper(trim((string) $request->input('country_code', ''))),
            'abbreviation' => strtoupper(trim((string) $request->input('abbreviation', ''))) ?: null,
        ]);

        $validator = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:100'],
            'abbreviation' => ['nullable', 'string', 'max:10'],
            'country_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('countries', 'country_code')->ignore((int) $request->input('id', 0)),
            ],
            'is_active'    => ['required', 'integer', Rule::in(array_keys(config('constants.status')))],
        ], [
            'name.required'         => '國名為必填欄位',
            'country_code.required' => '國家代碼為必填欄位',
            'country_code.unique'   => '國家代碼不可重複！',
        ]);

        $post = $request->only(['id', 'name', 'abbreviation', 'country_code', 'is_active']);

        if ($validator->fails()) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());
            return redirect('admin/country/edit/' . ((int) ($post['id'] ?? 0)));
        }

        try {
            $id = (int) ($post['id'] ?? 0);

            if ($id === 0) {
                # 新增
                $id = $this->service->addData($post);

                # 記錄操作日誌
                $this->logService->recordSimple($request, 'country', 'create', $id, $post['name'] ?? null);
            } else {
                # 取得修改前資料
                $oldData = $this->service->fetchDataByID($id);

                # 編輯
                $this->service->updateData($id, $post);

                # 記錄操作日誌
                $this->logService->recordUpdate(
                    $request,
                    'country',
                    $id,
                    $oldData['name'] ?? null,
                    $oldData,
                    $post,
                    ['name', 'abbreviation', 'country_code', 'is_active']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/country/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/country/edit/' . ((int) ($post['id'] ?? 0)));
        }
    }

    /**
     * 刪除
     */
    public function delete(Request $request, int $id)
    {
        try {
            $data = $this->service->fetchDataByID($id);
            $this->service->deleteData($id);

            # 記錄操作日誌
            $this->logService->recordSimple($request, 'country', 'delete', $id, $data['name'] ?? null);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '刪除成功！');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/country/list');
    }
}



