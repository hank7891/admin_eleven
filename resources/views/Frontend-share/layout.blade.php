{{--
    Legacy 轉接層 — 既有業務頁的 `@extends('Frontend-share.layout')` 透過此檔導向新 layout `layouts.frontend`。
    P2 階段業務頁陸續改 `@extends('layouts.frontend')` 後，此檔於 P5 刪除。
--}}
@extends('layouts.frontend')
