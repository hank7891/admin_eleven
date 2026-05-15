<footer class="fe-footer" role="contentinfo" id="footer">
    <div class="fe-container">
        <div class="fe-footer-inner">
            <div class="fe-footer-brand">
                <div class="fe-brand fe-footer-brand-name">Aura &amp; Heirloom</div>
                <p class="fe-meta fe-footer-tagline">
                    為日常留一個慢下來的位置。<br>關於選物、生活與一點點儀式感。
                </p>
            </div>

            <div class="fe-footer-cols">
                @forelse ($footerColumns ?? [] as $column)
                    <div class="fe-footer-col">
                        <h4>{{ $column['title'] }}</h4>
                        <ul>
                            @foreach ($column['links'] as $link)
                                <li><a href="#">{{ $link }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="fe-footer-col">
                        <h4>探索</h4>
                        <ul>
                            <li><a href="{{ url('product') }}">商品列表</a></li>
                            <li><a href="{{ url('announcement') }}">最新公告</a></li>
                            <li><a href="{{ url('about') }}">關於我們</a></li>
                        </ul>
                    </div>
                    <div class="fe-footer-col">
                        <h4>會員</h4>
                        <ul>
                            <li><a href="{{ url('member/login') }}">會員登入</a></li>
                            <li><a href="{{ url('member/register') }}">會員註冊</a></li>
                            <li><a href="{{ url('member/profile') }}">會員專區</a></li>
                        </ul>
                    </div>
                    <div class="fe-footer-col">
                        <h4>聯絡</h4>
                        <ul>
                            <li><a href="#">hello@aura-heirloom.com</a></li>
                            <li><a href="#">客服 LINE</a></li>
                            <li><a href="#">隱私權政策</a></li>
                        </ul>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="fe-footer-foot">
            <span>&copy; {{ date('Y') }} Aura &amp; Heirloom. All rights reserved.</span>
            <span>Made in Taiwan · 慢慢來，比較快。</span>
        </div>
    </div>
</footer>
