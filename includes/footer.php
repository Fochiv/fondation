</main>
<footer class="site-footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <a href="index.php" class="footer-logo"><img src="logo_fondation.png" alt="<?= e(SITE_NAME) ?>"></a>
            <p><?= e(t('footer_intro')) ?></p>
            <div class="footer-social">
                <?php foreach (['facebook' => 'facebook', 'instagram' => 'instagram', 'linkedin' => 'linkedin'] as $key => $icon):
                    $url = setting($key);
                    if ($url !== ''): ?>
                        <a href="<?= e($url) ?>" aria-label="<?= e(ucfirst($key)) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-<?= e($icon) ?>"></i></a>
                    <?php endif;
                endforeach; ?>
            </div>
        </div>
        <div>
            <h2><?= e(t('quick_links')) ?></h2>
            <ul class="footer-links">
                <li><a href="program.php"><?= e(t('program')) ?></a></li>
                <li><a href="criteria.php"><?= e(t('criteria')) ?></a></li>
                <li><a href="apply.php"><?= e(t('apply')) ?></a></li>
                <li><a href="track.php"><?= e(t('track')) ?></a></li>
            </ul>
        </div>
        <div>
            <h2><?= e(t('contact')) ?></h2>
            <ul class="footer-links">
                <?php if (setting('contact_email') !== ''): ?><li><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li><?php endif; ?>
                <?php if (setting('phone') !== ''): ?><li><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li><?php endif; ?>
                <?php if (setting('address') !== ''): ?><li><?= e(setting('address')) ?></li><?php endif; ?>
                <li><a href="contact.php"><?= e(t('contact_button')) ?> <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© <?= date('Y') ?> <?= e(SITE_NAME) ?>. <?= e(t('rights')) ?></span>
        <a href="privacy.php"><?= e(t('legal')) ?></a>
        <a href="admin/login.php">Administration</a>
    </div>
</footer>
<?php $chatUrl = whatsapp_url(); if ($chatUrl): ?>
    <a class="whatsapp-float" href="<?= e($chatUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="assets/js/site.js" defer></script>
</body>
</html>
