<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\ProductCategoryService;
use App\Services\Share\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    const POST_SESSION = 'product_category_edit_post';

    protected $settingService;

    public function __construct(
        protected ProductCategoryService $service,
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

        return view('Admin/product/category/list', $this->settingService->fetchSetData());
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

            return view('Admin/product/category/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/product.category/list');
        }
    }

    public function editDo(Request $request)
    {
        $id = (int) $request->input('id', 0);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_categories', 'name')->ignore($id),
            ],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'integer', Rule::in(array_keys(config('constants.status')))],
        ], [
            'name.required' => '類別名稱為必填欄位',
            'name.unique' => '類別名稱不可重複',
            'sort_order.min' => '排序不可小於 0',
        ]);

        $post = $request->only(['id', 'name', 'sort_order', 'is_active']);

        if ($validator->fails()) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());

            return redirect('admin/product.category/edit/' . $id);
        }

        try {
            if ($id === 0) {
                $id = $this->service->addData($post);
                $this->logService->recordSimple($request, 'product_category', 'create', $id, $post['name'] ?? null);
            } else {
                $oldData = $this->service->fetchDataByID($id);
                $this->service->updateData($id, $post);

                $this->logService->recordUpdate(
                    $request,
                    'product_category',
                    $id,
                    $oldData['name'] ?? null,
                    $oldData,
                    $post,
                    ['name', 'sort_order', 'is_active']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');

            return redirect('admin/product.category/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/product.category/edit/' . $id);
        }
    }

    public function delete(Request $request, int $id)
    {
        try {
            $data = $this->service->fetchDataByID($id);
            $this->service->deleteData($id);
            $this->logService->recordSimple($request, 'product_category', 'delete', $id, $data['name'] ?? null);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '刪除成功！');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/product.category/list');
    }
}


