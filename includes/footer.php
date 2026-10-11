</main>
<footer class="site-footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <a href="index.php" class="footer-logo"><img src="logo_fondation.png" alt="<?= e(SITE_NAME) ?>"></a>
            <p><?= e(t('footer_intro')) ?></p>
            <div class="footer-social" aria-label="<?= $lang === 'en' ? 'Follow Dangote Group' : 'Suivre Dangote Group' ?>">
                <?php
                $socialLinks = [
                    'facebook' => ['facebook', 'https://web.facebook.com/dangotegroup'],
                    'X' => ['twitter-x', 'https://twitter.com/DangoteGroup'],
                    'instagram' => ['instagram', 'https://www.instagram.com/dangotegroup/'],
                    'youtube' => ['youtube', 'https://www.youtube.com/dangote'],
                    'linkedin' => ['linkedin', 'https://ng.linkedin.com/company/dangotegroup'],
                ];
                foreach ($socialLinks as $key => [$icon, $defaultUrl]):
                    $url = setting(strtolower($key)) ?: $defaultUrl; ?>
                    <a href="<?= e($url) ?>" aria-label="<?= e($key === 'X' ? 'X' : ucfirst($key)) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i></a>
                <?php endforeach; ?>
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
        <a href="terms.php"><?= e(t('terms')) ?></a>
    </div>
</footer>
<a class="back-to-top" href="#top" aria-label="<?= $lang === 'en' ? 'Back to top' : 'Remonter en haut de page' ?>" title="<?= $lang === 'en' ? 'Back to top' : 'Remonter en haut de page' ?>"><i class="bi bi-arrow-up" aria-hidden="true"></i></a>
<?php $chatUrl = whatsapp_url(); if ($chatUrl): ?>
    <a class="whatsapp-float" href="<?= e($chatUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="assets/js/site.js" defer></script>
</body>
</html>
