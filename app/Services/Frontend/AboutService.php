<?php

namespace App\Services\Frontend;

use App\Repositories\Admin\AboutPageRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AboutService
{
    # 建構元
    public function __construct(protected AboutPageRepository $repository)
    {
    }

    /**
     * 取得前台關於我們頁資料契約
     */
    public function fetch(): array
    {
        return Cache::remember('about:page', 1800, function () {
            $aboutPage = $this->repository->getSingleton()->toArray();

            $mission = $this->buildOptionalSection(
                $aboutPage['mission_title'] ?? null,
                $aboutPage['mission_content'] ?? null
            );
            $vision = $this->buildOptionalSection(
                $aboutPage['vision_title'] ?? null,
                $aboutPage['vision_content'] ?? null
            );

            $contact = [
                'email' => $this->normalizeNullable($aboutPage['contact_email'] ?? null),
                'phone' => $this->normalizeNullable($aboutPage['contact_phone'] ?? null),
                'address' => $this->normalizeNullable($aboutPage['contact_address'] ?? null),
            ];

            return [
                'hero_title' => (string) ($aboutPage['hero_title'] ?? ''),
                'hero_subtitle' => $this->normalizeNullable($aboutPage['hero_subtitle'] ?? null),
                'hero_image_url' => !empty($aboutPage['hero_image_path'])
                    ? Storage::url($aboutPage['hero_image_path'])
                    : null,
                'story' => [
                    'title' => (string) ($aboutPage['story_title'] ?? ''),
                    'content' => (string) ($aboutPage['story_content'] ?? ''),
                ],
                'mission' => $mission,
                'vision' => $vision,
                'contact' => (empty($contact['email']) && empty($contact['phone']) && empty($contact['address']))
                    ? null
                    : $contact,
                'meta_description' => $this->normalizeNullable($aboutPage['meta_description'] ?? null),
            ];
        });
    }

    /**
     * 組裝可隱藏區塊
     */
    protected function buildOptionalSection(?string $title, ?string $content): ?array
    {
        $normalizedTitle = $this->normalizeNullable($title);
        $normalizedContent = $this->normalizeNullable($content);

        if ($normalizedTitle === null && $normalizedContent === null) {
            return null;
        }

        return [
            'title' => $normalizedTitle ?? '',
            'content' => $normalizedContent ?? '',
        ];
    }

    /**
     * 轉換空字串為 null
     */
    protected function normalizeNullable(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}

