<style>
    body { padding-top: 78px !important; }

    .site-promo-strip {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
        background: linear-gradient(90deg, #17324d 0%, #1f4f86 52%, #3d7dc2 100%);
        color: #f7fbff;
        box-shadow: 0 10px 24px rgba(23, 50, 77, 0.18);
    }

    .site-promo-inner {
        max-width: 1100px;
        margin: 0 auto;
        padding: 10px 20px;
        text-align: center;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    @media (max-width: 640px) {
        body { padding-top: 88px !important; }

        .site-promo-inner {
            font-size: 13px;
            line-height: 1.35;
            padding: 10px 14px;
        }
    }
</style>

<div class="site-promo-strip" role="status" aria-live="polite">
    <div class="site-promo-inner">Campus Style Sale: 50% off all items today. Grab yours now.</div>
</div>
