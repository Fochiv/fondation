<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'Stories and testimonials' : 'Témoignages';
$testimonials = [];
try {
    $query = db()->query('SELECT full_name, country, city, activity, quote, photo_url, published_at FROM testimonials WHERE is_active = 1 ORDER BY published_at DESC, id DESC');
    $testimonials = $query->fetchAll();
} catch (Throwable) {
    // Public browsing remains available while MySQL is being configured.
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-navy"><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'COMMUNITY STORIES' : 'PARCOURS ET INITIATIVES' ?></span><h1><?= $en ? 'Experiences from the community.' : 'Les expériences de notre communauté.' ?></h1><p><?= $en ? 'Read stories shared by programme participants.' : 'Découvrez les témoignages publiés par les participants au programme.' ?></p></div></section>
<section class="section"><div class="container">
    <?php if ($testimonials): ?>
        <div class="testimonial-grid">
            <?php foreach ($testimonials as $person):
                $initials = '';
                foreach (preg_split('/\s+/', trim($person['full_name'])) ?: [] as $part) {
                    if ($part !== '') $initials .= mb_substr($part, 0, 1);
                }
                ?>
                <article class="testimonial-card">
                    <div class="quote-mark"><i class="bi bi-quote"></i></div>
                    <blockquote><?= e($person['quote']) ?></blockquote>
                    <div class="testimonial-person">
                        <?php if (!empty($person['photo_url'])): ?><img src="<?= e($person['photo_url']) ?>" alt="<?= e($person['full_name']) ?>"><?php else: ?><span class="avatar-initials"><?= e(mb_strtoupper(mb_substr($initials, 0, 2))) ?></span><?php endif; ?>
                        <div><strong><?= e($person['full_name']) ?></strong><span><?= e(implode(' · ', array_filter([$person['activity'], $person['city'], $person['country']]))) ?></span></div>
                    </div>
                    <?php if (!empty($person['published_at'])): ?><time datetime="<?= e($person['published_at']) ?>"><?= e(date('d/m/Y', strtotime($person['published_at']))) ?></time><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-chat-square-quote"></i></div><span class="eyebrow"><?= $en ? 'COMING SOON' : 'BIENTÔT' ?></span><h2><?= $en ? 'Community stories will appear here.' : 'Les témoignages de la communauté seront publiés ici.' ?></h2><p><?= $en ? 'Stories are shared only after they have been reviewed and approved for publication.' : 'Les témoignages sont publiés uniquement après vérification et autorisation de diffusion.' ?></p></div>
    <?php endif; ?>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
