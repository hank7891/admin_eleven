<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\ProductTagService;
use App\Services\Share\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductTagController extends Controller
{
    const POST_SESSION = 'product_tag_edit_post';

    protected $settingService;

    public function __construct(
        protected ProductTagService $service,
        protected AdminLogService $logService
    ) {
        $this->settingService = app('setting');
    }

    public function list(Request $request)
    {
        $filters = $request->only(['keyword', 'is_active']);
        $result = $this->service->fetchPaginatedData($filters);

        $this->settingService->setSetData('data', $result['data']);
        $this->settingService->setSetData('pagination', $result['pagination']);
        $this->settingService->setSetData('filters', $filters);
        $this->settingService->setSetData('statusOptions', config('constants.status'));

        return view('Admin/product/tag/list', $this->settingService->fetchSetData());
    }

    public function edit(int $id)
    {
        try {
            $data = $id > 0 ? $this->service->fetchDataByID($id) : [];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $this->settingService->setSetData('data', $data);

            return view('Admin/product/tag/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/product.tag/list');
        }
    }

    public function editDo(Request $request)
    {
        $id = (int) $request->input('id', 0);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('product_tags', 'name')->ignore($id),
            ],
            'is_active' => ['required', 'integer', Rule::in(array_keys(config('constants.status')))],
        ], [
            'name.required' => '標籤名稱為必填欄位',
            'name.unique' => '標籤名稱不可重複',
        ]);

        $post = $request->only(['id', 'name', 'is_active']);

        if ($validator->fails()) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());

            return redirect('admin/product.tag/edit/' . $id);
        }

        try {
            if ($id === 0) {
                $id = $this->service->addData($post);
                $this->logService->recordSimple($request, 'product_tag', 'create', $id, $post['name'] ?? null);
            } else {
                $oldData = $this->service->fetchDataByID($id);
                $this->service->updateData($id, $post);

                $this->logService->recordUpdate(
                    $request,
                    'product_tag',
                    $id,
                    $oldData['name'] ?? null,
                    $oldData,
                    $post,
                    ['name', 'is_active']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');

            return redirect('admin/product.tag/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/product.tag/edit/' . $id);
        }
    }

    public function delete(Request $request, int $id)
    {
        try {
            $data = $this->service->fetchDataByID($id);
            $this->service->deleteData($id);
            $this->logService->recordSimple($request, 'product_tag', 'delete', $id, $data['name'] ?? null);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '刪除成功！');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/product.tag/list');
    }
}


