<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        # 禁止使用 RefreshDatabase，避免 migrate:fresh 清除遺留資料表
        if (in_array(RefreshDatabase::class, class_uses_recursive($this))) {
            throw new \LogicException(
                static::class . ' 使用了 RefreshDatabase，請改用 DatabaseTransactions。'
            );
        }

        parent::setUp();
    }
}
