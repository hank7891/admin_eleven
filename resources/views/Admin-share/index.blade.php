{{--
    Legacy 轉接層 — 既有業務頁的 `@extends('Admin-share/index')` 透過此檔導向新 layout `layouts.admin`。
    P2-P4 階段業務頁陸續改 `@extends('layouts.admin')` 後，此檔於 P5 刪除。
--}}
@extends('layouts.admin')
