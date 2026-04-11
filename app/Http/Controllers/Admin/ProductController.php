<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\ProductService;
use App\Services\Share\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    const POST_SESSION = 'product_edit_post';

    protected $settingService;

    public function __construct(
        protected ProductService $service,
        protected AdminLogService $logService
    ) {
        $this->settingService = app('setting');
    }

    public function list(Request $request)
    {
        $filters = $request->only(['keyword', 'category_id', 'tag_id', 'status_key', 'is_featured', 'period_state']);
        $result = $this->service->fetchPaginatedData($filters);
        $options = $this->service->fetchFilterOptions();

        $this->settingService->setSetData('data', $result['data']);
        $this->settingService->setSetData('pagination', $result['pagination']);
        $this->settingService->setSetData('filters', $filters);
        $this->settingService->setSetData('filterOptions', $options);

        return view('Admin/product/list', $this->settingService->fetchSetData());
    }

    public function edit(int $id)
    {
        try {
            $data = $id > 0 ? $this->service->fetchDataByID($id) : [];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $options = $this->service->fetchFilterOptions();

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('options', $options);

            return view('Admin/product/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/product/list');
        }
    }

    public function editDo(Request $request)
    {
        $id = (int) $request->input('id', 0);
        $isEdit = $id > 0;
        $maxImageSizeMB = round((config('upload.image.max_size', 5120)) / 1024, 2);

        $rules = [
            'name' => ['required', 'string', 'max:200'],
            'tagline' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'description' => ['required', 'string', 'max:20000'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:product_tags,id'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_featured' => ['required', 'integer', Rule::in(array_keys(config('constants.product_featured')))],
            'status_key' => ['required', Rule::in(array_map('strval', array_keys(config('constants.product_status'))))],
            'start_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'end_at' => ['nullable', 'date_format:Y-m-d\TH:i', 'after_or_equal:start_at'],
            'images' => [$isEdit ? 'nullable' : 'required', 'array', 'max:' . PRODUCT_MAX_IMAGES],
            'images.*' => ['nullable', 'file', 'max:' . (config('upload.image.max_size') ?? 5120), 'mimes:' . implode(',', config('upload.image.mimes', []))],
            'kept_ids' => ['nullable', 'array'],
            'kept_ids.*' => ['integer', 'exists:product_images,id'],
            'deleted_ids' => ['nullable', 'array'],
            'deleted_ids.*' => ['integer', 'exists:product_images,id'],
            'primary_id' => ['nullable', 'integer', 'exists:product_images,id'],
            'primary_new_index' => ['nullable', 'integer', 'min:0'],
        ];

        $validator = Validator::make($request->all(), $rules, [
            'name.required' => '商品名稱為必填欄位',
            'price.required' => '價格為必填欄位',
            'description.required' => '商品描述為必填欄位',
            'images.required' => '請至少上傳一張商品圖片',
            'images.max' => '商品圖片最多 ' . PRODUCT_MAX_IMAGES . ' 張',
            'images.*.max' => '單張圖片大小不可超過 ' . $maxImageSizeMB . 'MB',
            'images.*.mimes' => '僅支援 jpg、jpeg、png、gif、webp 圖片格式',
            'end_at.after_or_equal' => '結束時間不可早於開始時間',
        ]);

        $validator->after(function ($validator) use ($request, $isEdit) {
            $keptCount = count(array_filter((array) $request->input('kept_ids', [])));
            $newCount = $request->hasFile('images') ? count($request->file('images')) : 0;
            $total = $isEdit ? $keptCount + $newCount : $newCount;

            if ($total < 1 || $total > PRODUCT_MAX_IMAGES) {
                $validator->errors()->add('images', '商品圖片張數需介於 1 ~ ' . PRODUCT_MAX_IMAGES . ' 張。');
            }

            if ((int) $request->input('primary_id', 0) <= 0 && $request->input('primary_new_index', '') === '') {
                $validator->errors()->add('primary', '請指定一張標題圖片。');
            }
        });

        $post = $request->only([
            'id',
            'name',
            'tagline',
            'price',
            'description',
            'category_id',
            'tag_ids',
            'sort_order',
            'is_featured',
            'status_key',
            'start_at',
            'end_at',
            'kept_ids',
            'deleted_ids',
            'primary_id',
            'primary_new_index',
        ]);

        if ($validator->fails()) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());

            return redirect('admin/product/edit/' . $id);
        }

        $images = $request->file('images', []);
        $imageMeta = [
            'kept_ids' => $request->input('kept_ids', []),
            'deleted_ids' => $request->input('deleted_ids', []),
            'primary_id' => $request->input('primary_id'),
            'primary_new_index' => $request->input('primary_new_index'),
            'new_alt' => $request->input('new_alt', []),
            'new_sort' => $request->input('new_sort', []),
        ];

        try {
            if ($id === 0) {
                $id = $this->service->addData($post, $images, $imageMeta);
                $this->logService->recordSimple($request, 'product', 'create', $id, $post['name'] ?? null);
            } else {
                $oldData = $this->service->fetchDataByID($id);
                $this->service->updateData($id, $post, $images, $imageMeta);

                $newData = $post;
                $newData['image_summary'] = '已更新圖片';
                $this->logService->recordUpdate(
                    $request,
                    'product',
                    $id,
                    $oldData['name'] ?? null,
                    $oldData,
                    $newData,
                    ['name', 'tagline', 'price', 'description', 'category_id', 'sort_order', 'is_featured', 'status_key', 'start_at', 'end_at', 'image_summary']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');

            return redirect('admin/product/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/product/edit/' . $id);
        }
    }

    public function delete(Request $request, int $id)
    {
        try {
            $data = $this->service->fetchDataByID($id);
            $this->service->deleteData($id);
            $this->logService->recordSimple($request, 'product', 'delete', $id, $data['name'] ?? null);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '刪除成功！');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/product/list');
    }

    public function bulkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
            'status_key' => ['required', Rule::in(array_map('strval', array_keys(config('constants.product_status'))))],
        ], [
            'ids.required' => '請至少選擇一筆商品。',
        ]);

        if ($validator->fails()) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());

            return redirect('admin/product/list?' . http_build_query($request->except(['ids', 'status_key', '_token'])));
        }

        try {
            $affected = $this->service->bulkUpdateStatus((array) $request->input('ids', []), (int) $request->input('status_key'));

            $statusText = config('constants.product_status.' . (string) $request->input('status_key'));
            $this->logService->recordSimple(
                $request,
                'product',
                'update',
                0,
                '批次更新',
                '批次更新 ' . $affected . ' 筆商品為' . $statusText
            );

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '已更新 ' . $affected . ' 筆商品。');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/product/list?' . http_build_query($request->except(['ids', 'status_key', '_token'])));
    }
}



