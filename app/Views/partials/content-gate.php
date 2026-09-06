<?php
/**
 * Renders in place of a real results grid/list on page 2+ of a public
 * listing when the visitor isn't allowed to see it (BaseController::
 * contentAccessTier()). Nothing real is passed in -- $gateTier is the only
 * input this partial needs -- so there's no real product/supplier/inquiry
 * data anywhere in this partial's output to accidentally leak.
 *
 * $gateTier: 'guest' | 'free'
 * $placeholderCount: how many skeleton cards to render (default 6)
 */
$placeholderCount = $placeholderCount ?? 6;
?>
<div class="content-gate-wrap">
    <div class="content-gate-placeholders">
        <?php for ($i = 0; $i < $placeholderCount; $i++): ?>
            <div class="content-gate-card">
                <div class="cg-skel cg-skel-img"></div>
                <div class="cg-skel cg-skel-line" style="width: 80%;"></div>
                <div class="cg-skel cg-skel-line" style="width: 60%;"></div>
                <div class="cg-skel cg-skel-line" style="width: 40%;"></div>
            </div>
        <?php endfor; ?>
    </div>
    <div class="content-gate-overlay">
        <?php if ($gateTier === 'guest'): ?>
            <p class="content-gate-message">Sign in or create a free account to see more results.</p>
            <div class="content-gate-actions">
                <a href="<?= base_url('login') ?>" class="solid-btn">Sign In</a>
                <a href="<?= base_url('register') ?>" class="outline-btn btn">Register</a>
            </div>
        <?php else: ?>
            <p class="content-gate-message">Upgrade your membership to see more results.</p>
            <div class="content-gate-actions">
                <a href="<?= base_url('premium-services') ?>" class="solid-btn">View Membership Plans</a>
            </div>
        <?php endif; ?>
    </div>
</div>
