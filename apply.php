<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'Submit your project' : 'Soumettre votre projet';
$sectors = $en
    ? ['Agriculture', 'Livestock', 'Commerce', 'Technology', 'Industry', 'Processing', 'Crafts', 'Education', 'Health', 'Transport', 'Energy', 'Services', 'Hospitality', 'Fashion', 'Other']
    : ['Agriculture', 'Élevage', 'Commerce', 'Technologie', 'Industrie', 'Transformation', 'Artisanat', 'Éducation', 'Santé', 'Transport', 'Énergie', 'Services', 'Restauration', 'Mode', 'Autres'];
$uses = $en
    ? ['Equipment', 'Inventory and goods', 'Construction', 'Fit-out', 'Agriculture', 'Livestock', 'Transport', 'Recruitment', 'Marketing', 'Technology', 'Training', 'Other']
    : ['Achat de matériel', 'Achat de marchandises', 'Construction', 'Aménagement', 'Agriculture', 'Élevage', 'Transport', 'Recrutement', 'Marketing', 'Technologie', 'Formation', 'Autre'];
$stages = $en
    ? ['idea' => 'Idea', 'preparation' => 'In preparation', 'launched' => 'Already launched', 'growth' => 'Growing', 'existing' => 'Existing business']
    : ['idea' => 'Idée', 'preparation' => 'En préparation', 'launched' => 'Déjà lancé', 'growth' => 'En développement', 'existing' => 'Entreprise existante'];
$values = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];
$errors = [];
$reference = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $string = static fn(string $key, int $max = 5000): string => mb_substr(trim((string)($_POST[$key] ?? '')), 0, $max);
    $required = [
        'first_name' => 100, 'last_name' => 100, 'country' => 100, 'phone' => 40, 'email' => 190,
        'project_name' => 190, 'project_sector' => 120, 'project_country' => 100,
        'project_description' => 10000, 'project_problem' => 5000, 'project_solution' => 5000,
        'project_stage' => 50, 'fund_use_details' => 5000,
    ];
    $clean = [];
    foreach ($required as $key => $limit) {
        $clean[$key] = $string($key, $limit);
        if ($clean[$key] === '') $errors[] = ($en ? 'Please complete: ' : 'Veuillez renseigner : ') . str_replace('_', ' ', $key);
    }
    $optional = [
        'birth_date' => 10, 'gender' => 30, 'nationality' => 100, 'region' => 100, 'city' => 100,
        'neighborhood' => 120, 'address' => 255, 'whatsapp_number' => 40, 'employment_status' => 100,
        'education_level' => 100, 'company_name' => 190, 'company_created' => 10, 'company_sector' => 120,
        'company_description' => 5000, 'company_location' => 190, 'turnover' => 100,
        'registration_number' => 100, 'promoter_experience' => 5000, 'project_subsector' => 120,
        'project_region' => 100, 'project_city' => 100, 'project_location' => 190,
        'project_objective' => 5000, 'target_customers' => 3000, 'competitors' => 3000,
        'competitive_advantage' => 3000,
    ];
    foreach ($optional as $key => $limit) $clean[$key] = $string($key, $limit);
    if (!filter_var($clean['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = $en ? 'Enter a valid email address.' : 'Indiquez une adresse e-mail valide.';
    if (!preg_match('/^[\d+\s().-]{6,40}$/', $clean['phone'] ?? '')) $errors[] = $en ? 'Enter a valid phone number.' : 'Indiquez un numéro de téléphone valide.';
    if (!empty($_POST['website_url'])) $errors[] = $en ? 'The application could not be submitted.' : 'La candidature n’a pas pu être envoyée.';

    $birthDate = $clean['birth_date'] ?? '';
    $companyDate = $clean['company_created'] ?? '';
    foreach (['birth_date' => $birthDate, 'company_created' => $companyDate] as $field => $date) {
        if ($date !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !checkdate((int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4)))) {
            $errors[] = $en ? 'Check the date entered.' : 'Vérifiez la date saisie.';
        }
    }
    $hasActivity = ($_POST['has_activity'] ?? '') === 'yes';
    if (!in_array($_POST['has_activity'] ?? '', ['yes', 'no'], true)) $errors[] = $en ? 'Please say whether you already have an activity.' : 'Indiquez si vous avez déjà une activité.';
    if ($hasActivity && (($clean['company_name'] ?? '') === '' || ($clean['company_description'] ?? '') === '')) {
        $errors[] = $en ? 'Add the name and description of your existing activity.' : 'Indiquez le nom et la description de votre activité existante.';
    }
    if (!in_array($clean['project_sector'] ?? '', $sectors, true)) $errors[] = $en ? 'Choose a valid project sector.' : 'Choisissez un secteur valide.';
    if (!array_key_exists($clean['project_stage'] ?? '', $stages)) $errors[] = $en ? 'Choose a valid project stage.' : 'Choisissez un stade de projet valide.';

    $amount = filter_var($_POST['amount_requested'] ?? null, FILTER_VALIDATE_FLOAT);
    $contribution = filter_var($_POST['personal_contribution'] ?? null, FILTER_VALIDATE_FLOAT);
    $totalCost = filter_var($_POST['total_cost'] ?? null, FILTER_VALIDATE_FLOAT);
    if ($amount === false || $amount <= 0 || $amount > 99999999999999) $errors[] = $en ? 'Enter a valid funding amount.' : 'Indiquez un montant demandé valide.';
    if ($contribution === false || $contribution < 0 || $contribution > 99999999999999) $errors[] = $en ? 'Enter a valid personal contribution.' : 'Indiquez un apport personnel valide.';
    if ($totalCost === false || $totalCost <= 0 || $totalCost > 99999999999999) $errors[] = $en ? 'Enter a valid total project cost.' : 'Indiquez un coût total du projet valide.';
    $currencies = ['XAF', 'XOF', 'NGN', 'USD', 'EUR', 'GHS', 'KES'];
    $currency = (string)($_POST['currency'] ?? '');
    if (!in_array($currency, $currencies, true)) $errors[] = $en ? 'Choose a valid currency.' : 'Choisissez une devise valide.';

    $intFields = ['employee_count', 'current_jobs', 'jobs_to_create'];
    $ints = [];
    foreach ($intFields as $field) {
        $raw = trim((string)($_POST[$field] ?? ''));
        $ints[$field] = $raw === '' ? null : filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
        if ($raw !== '' && $ints[$field] === false) $errors[] = $en ? 'Check the number of employees or jobs.' : 'Vérifiez le nombre d’employés ou d’emplois.';
    }
    $selectedUses = array_values(array_intersect($uses, array_map('strval', (array)($_POST['fund_uses'] ?? []))));
    if (!$selectedUses) $errors[] = $en ? 'Select at least one intended use for the funding.' : 'Sélectionnez au moins une utilisation prévue du financement.';
    foreach (['consent_accuracy', 'consent_privacy', 'consent_processing'] as $consent) {
        if (($_POST[$consent] ?? '') !== '1') $errors[] = $en ? 'Please accept all required declarations.' : 'Veuillez accepter toutes les déclarations obligatoires.';
    }

    if (!$errors) {
        try {
            $record = [
                'status' => 'new', 'first_name' => $clean['first_name'], 'last_name' => $clean['last_name'],
                'birth_date' => $birthDate ?: null, 'gender' => $clean['gender'] ?: null, 'nationality' => $clean['nationality'] ?: null,
                'country' => $clean['country'], 'region' => $clean['region'] ?: null, 'city' => $clean['city'] ?: null,
                'neighborhood' => $clean['neighborhood'] ?: null, 'address' => $clean['address'] ?: null,
                'phone' => $clean['phone'], 'whatsapp_number' => $clean['whatsapp_number'] ?: null, 'email' => $clean['email'],
                'employment_status' => $clean['employment_status'] ?: null, 'education_level' => $clean['education_level'] ?: null,
                'has_activity' => $hasActivity ? 1 : 0, 'company_name' => $hasActivity ? ($clean['company_name'] ?: null) : null,
                'company_created' => $hasActivity ? ($companyDate ?: null) : null,
                'company_sector' => $hasActivity ? ($clean['company_sector'] ?: null) : null,
                'company_description' => $hasActivity ? ($clean['company_description'] ?: null) : null,
                'company_location' => $hasActivity ? ($clean['company_location'] ?: null) : null,
                'employee_count' => $hasActivity ? ($ints['employee_count'] === false ? null : $ints['employee_count']) : null,
                'turnover' => $hasActivity ? ($clean['turnover'] ?: null) : null,
                'registration_number' => $hasActivity ? ($clean['registration_number'] ?: null) : null,
                'promoter_experience' => $clean['promoter_experience'] ?: null,
                'project_name' => $clean['project_name'], 'project_sector' => $clean['project_sector'],
                'project_subsector' => $clean['project_subsector'] ?: null, 'project_country' => $clean['project_country'],
                'project_region' => $clean['project_region'] ?: null, 'project_city' => $clean['project_city'] ?: null,
                'project_location' => $clean['project_location'] ?: null, 'project_description' => $clean['project_description'],
                'project_objective' => $clean['project_objective'] ?: null, 'project_problem' => $clean['project_problem'],
                'project_solution' => $clean['project_solution'], 'target_customers' => $clean['target_customers'] ?: null,
                'competitors' => $clean['competitors'] ?: null, 'competitive_advantage' => $clean['competitive_advantage'] ?: null,
                'current_jobs' => $ints['current_jobs'] === false ? null : $ints['current_jobs'],
                'jobs_to_create' => $ints['jobs_to_create'] === false ? null : $ints['jobs_to_create'],
                'project_stage' => $clean['project_stage'], 'amount_requested' => $amount, 'currency' => $currency,
                'personal_contribution' => $contribution, 'total_cost' => $totalCost,
                'fund_uses' => json_encode($selectedUses, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'fund_use_details' => $clean['fund_use_details'], 'consent_accuracy' => 1, 'consent_privacy' => 1, 'consent_processing' => 1,
            ];
            $pdo = db();
            $pdo->beginTransaction();
            $columns = array_keys($record);
            $insert = $pdo->prepare('INSERT INTO applications (' . implode(',', $columns) . ') VALUES (' . implode(',', array_fill(0, count($columns), '?')) . ')');
            $insert->execute(array_values($record));
            $id = (int)$pdo->lastInsertId();
            $reference = 'DG-' . date('Y') . '-' . str_pad((string)$id, 6, '0', STR_PAD_LEFT);
            $update = $pdo->prepare('UPDATE applications SET reference_no = ? WHERE id = ?');
            $update->execute([$reference, $id]);
            $history = $pdo->prepare('INSERT INTO application_history (application_id,event_type,new_value,description) VALUES (?,"created","new","Candidature reçue")');
            $history->execute([$id]);
            $pdo->commit();
            $_SESSION['confirmation_reference'] = $reference;
            redirect('confirmation.php');
        } catch (Throwable) {
            if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
            $errors[] = $en
                ? 'Your application could not be saved. Check the MySQL configuration and try again.'
                : 'Votre candidature n’a pas pu être enregistrée. Vérifiez la configuration MySQL et réessayez.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-navy page-hero-compact"><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'PROJECT APPLICATION' : 'CANDIDATURE DE PROJET' ?></span><h1><?= $en ? 'Tell us about your project.' : 'Parlez-nous de votre projet.' ?></h1><p><?= $en ? 'Complete each step. Your details stay in the form while you move between sections.' : 'Complétez chaque étape. Vos informations restent dans le formulaire lorsque vous naviguez entre les sections.' ?></p></div></section>
<section class="section apply-section"><div class="container apply-layout">
    <div class="apply-aside">
        <div class="apply-aside-card"><span class="eyebrow"><?= $en ? 'BEFORE YOU BEGIN' : 'AVANT DE COMMENCER' ?></span><h2><?= $en ? 'A little preparation helps.' : 'Quelques minutes de préparation.' ?></h2><p><?= $en ? 'Keep your project description, location and estimated costs nearby.' : 'Gardez près de vous la description, la localisation et le budget estimatif de votre projet.' ?></p><div class="aside-divider"></div><p class="aside-assurance"><i class="bi bi-shield-lock"></i><?= $en ? 'Your information is used to review your application and contact you.' : 'Vos informations servent à étudier votre candidature et à vous contacter.' ?></p></div>
        <div class="disclaimer-card"><i class="bi bi-info-circle"></i><p><?= e(t('not_funding')) ?></p></div>
    </div>
    <div class="application-panel">
        <div class="form-progress" aria-label="<?= $en ? 'Application progress' : 'Progression de la candidature' ?>">
            <?php for ($step = 1; $step <= 5; $step++): ?><div class="progress-step <?= $step === 1 ? 'is-current' : '' ?>" data-progress-step="<?= $step ?>"><span><?= $step ?></span><small><?= e(($en ? ['Personal', 'Activity', 'Project', 'Budget', 'Consent'][$step - 1] : ['Profil', 'Activité', 'Projet', 'Budget', 'Envoi'][$step - 1])) ?></small></div><?php endfor; ?>
        </div>
        <?php if ($errors): ?><div class="error-banner" role="alert"><i class="bi bi-exclamation-circle"></i><div><strong><?= $en ? 'Please review your application:' : 'Vérifiez les informations suivantes :' ?></strong><ul><?php foreach (array_unique($errors) as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div></div><?php endif; ?>
        <form method="post" action="apply.php" class="application-form" id="application-form" novalidate>
            <?= csrf_field() ?>
            <label class="honeypot" aria-hidden="true">Website<input name="website_url" tabindex="-1" autocomplete="off"></label>
            <section class="form-step is-visible" data-step="1">
                <div class="form-step-heading"><span><?= $en ? 'STEP 01 / 05' : 'ÉTAPE 01 / 05' ?></span><h2><?= $en ? 'Your details' : 'Vos informations personnelles' ?></h2><p><?= $en ? 'Tell us who is leading this project.' : 'Indiquez qui porte ce projet.' ?></p></div>
                <div class="form-grid">
                    <div class="form-group"><label for="first_name"><?= $en ? 'First name' : 'Prénom' ?> <span>*</span></label><input id="first_name" name="first_name" maxlength="100" autocomplete="given-name" data-required value="<?= e($values['first_name'] ?? '') ?>"></div>
                    <div class="form-group"><label for="last_name"><?= $en ? 'Last name' : 'Nom' ?> <span>*</span></label><input id="last_name" name="last_name" maxlength="100" autocomplete="family-name" data-required value="<?= e($values['last_name'] ?? '') ?>"></div>
                    <div class="form-group"><label for="birth_date"><?= $en ? 'Date of birth' : 'Date de naissance' ?></label><input id="birth_date" type="date" name="birth_date" max="<?= date('Y-m-d') ?>" value="<?= e($values['birth_date'] ?? '') ?>"></div>
                    <div class="form-group"><label for="gender"><?= $en ? 'Gender (optional)' : 'Genre (facultatif)' ?></label><select id="gender" name="gender"><option value=""><?= $en ? 'Choose if you wish' : 'Choisissez si vous le souhaitez' ?></option><option value="female" <?= ($values['gender'] ?? '') === 'female' ? 'selected' : '' ?>><?= $en ? 'Female' : 'Femme' ?></option><option value="male" <?= ($values['gender'] ?? '') === 'male' ? 'selected' : '' ?>><?= $en ? 'Male' : 'Homme' ?></option><option value="prefer_not_to_say" <?= ($values['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>><?= $en ? 'Prefer not to say' : 'Préfère ne pas répondre' ?></option></select></div>
                    <div class="form-group"><label for="nationality"><?= $en ? 'Nationality' : 'Nationalité' ?></label><input id="nationality" name="nationality" maxlength="100" value="<?= e($values['nationality'] ?? '') ?>"></div>
                    <div class="form-group"><label for="country"><?= $en ? 'Country of residence' : 'Pays de résidence' ?> <span>*</span></label><input id="country" name="country" maxlength="100" autocomplete="country-name" data-required value="<?= e($values['country'] ?? '') ?>"></div>
                    <div class="form-group"><label for="region"><?= $en ? 'Region / state' : 'Région / département' ?></label><input id="region" name="region" maxlength="100" autocomplete="address-level1" value="<?= e($values['region'] ?? '') ?>"></div>
                    <div class="form-group"><label for="city"><?= $en ? 'City' : 'Ville' ?></label><input id="city" name="city" maxlength="100" autocomplete="address-level2" value="<?= e($values['city'] ?? '') ?>"></div>
                    <div class="form-group"><label for="neighborhood"><?= $en ? 'Neighborhood' : 'Quartier' ?></label><input id="neighborhood" name="neighborhood" maxlength="120" value="<?= e($values['neighborhood'] ?? '') ?>"></div>
                    <div class="form-group"><label for="address"><?= $en ? 'Address (optional)' : 'Adresse (facultatif)' ?></label><input id="address" name="address" maxlength="255" autocomplete="street-address" value="<?= e($values['address'] ?? '') ?>"></div>
                    <div class="form-group"><label for="phone"><?= $en ? 'Phone number' : 'Téléphone' ?> <span>*</span></label><input id="phone" name="phone" maxlength="40" type="tel" autocomplete="tel" data-required value="<?= e($values['phone'] ?? '') ?>"></div>
                    <div class="form-group"><label for="whatsapp_number"><?= $en ? 'WhatsApp number (optional)' : 'Numéro WhatsApp (facultatif)' ?></label><input id="whatsapp_number" name="whatsapp_number" maxlength="40" type="tel" value="<?= e($values['whatsapp_number'] ?? '') ?>"></div>
                    <div class="form-group"><label for="email"><?= $en ? 'Email address' : 'Adresse e-mail' ?> <span>*</span></label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" data-required value="<?= e($values['email'] ?? '') ?>"></div>
                    <div class="form-group"><label for="employment_status"><?= $en ? 'Employment status' : 'Situation professionnelle' ?></label><input id="employment_status" name="employment_status" maxlength="100" value="<?= e($values['employment_status'] ?? '') ?>"></div>
                    <div class="form-group"><label for="education_level"><?= $en ? 'Education level' : 'Niveau d’études' ?></label><input id="education_level" name="education_level" maxlength="100" value="<?= e($values['education_level'] ?? '') ?>"></div>
                </div>
            </section>
            <section class="form-step" data-step="2" hidden>
                <div class="form-step-heading"><span><?= $en ? 'STEP 02 / 05' : 'ÉTAPE 02 / 05' ?></span><h2><?= $en ? 'Your current activity' : 'Votre activité actuelle' ?></h2><p><?= $en ? 'Whether you are starting or already in business, tell us about it.' : 'Que vous démarriez ou exerciez déjà une activité, parlez-nous-en.' ?></p></div>
                <fieldset class="choice-fieldset"><legend><?= $en ? 'Do you already have an activity?' : 'Avez-vous déjà une activité ?' ?> <span>*</span></legend><div class="choice-cards">
                    <label class="choice-card"><input type="radio" name="has_activity" value="yes" data-required <?= ($values['has_activity'] ?? '') === 'yes' ? 'checked' : '' ?>><span class="choice-symbol"><i class="bi bi-shop"></i></span><span><strong><?= $en ? 'Yes, I do' : 'Oui, j’en ai une' ?></strong><small><?= $en ? 'I run a business or activity' : 'J’exerce une activité ou dirige une entreprise' ?></small></span></label>
                    <label class="choice-card"><input type="radio" name="has_activity" value="no" data-required <?= ($values['has_activity'] ?? '') === 'no' ? 'checked' : '' ?>><span class="choice-symbol"><i class="bi bi-lightbulb"></i></span><span><strong><?= $en ? 'Not yet' : 'Pas encore' ?></strong><small><?= $en ? 'I am developing a new project' : 'Je prépare le lancement de mon projet' ?></small></span></label>
                </div></fieldset>
                <div id="existing-activity" class="conditional-fields" <?= ($values['has_activity'] ?? '') === 'yes' ? '' : 'hidden' ?>>
                    <div class="form-grid">
                        <div class="form-group"><label for="company_name"><?= $en ? 'Business name' : 'Nom de l’entreprise' ?> <span>*</span></label><input id="company_name" name="company_name" maxlength="190" data-activity-required value="<?= e($values['company_name'] ?? '') ?>"></div>
                        <div class="form-group"><label for="company_created"><?= $en ? 'Business start date' : 'Date de création' ?></label><input id="company_created" type="date" name="company_created" max="<?= date('Y-m-d') ?>" value="<?= e($values['company_created'] ?? '') ?>"></div>
                        <div class="form-group"><label for="company_sector"><?= $en ? 'Business sector' : 'Secteur de l’activité' ?></label><select id="company_sector" name="company_sector"><option value=""><?= $en ? 'Select a sector' : 'Choisir un secteur' ?></option><?php foreach ($sectors as $sector): ?><option value="<?= e($sector) ?>" <?= ($values['company_sector'] ?? '') === $sector ? 'selected' : '' ?>><?= e($sector) ?></option><?php endforeach; ?></select></div>
                        <div class="form-group"><label for="company_location"><?= $en ? 'Business location' : 'Localisation de l’activité' ?></label><input id="company_location" name="company_location" maxlength="190" value="<?= e($values['company_location'] ?? '') ?>"></div>
                        <div class="form-group"><label for="employee_count"><?= $en ? 'Current employees' : 'Nombre d’employés' ?></label><input id="employee_count" type="number" min="0" max="1000000" name="employee_count" value="<?= e($values['employee_count'] ?? '') ?>"></div>
                        <div class="form-group"><label for="turnover"><?= $en ? 'Approximate annual turnover' : 'Chiffre d’affaires approximatif' ?></label><input id="turnover" name="turnover" maxlength="100" value="<?= e($values['turnover'] ?? '') ?>"></div>
                        <div class="form-group"><label for="registration_number"><?= $en ? 'Registration number (if applicable)' : 'Numéro d’immatriculation (si applicable)' ?></label><input id="registration_number" name="registration_number" maxlength="100" value="<?= e($values['registration_number'] ?? '') ?>"></div>
                        <div class="form-group form-group-wide"><label for="company_description"><?= $en ? 'What does the activity do?' : 'Décrivez votre activité' ?> <span>*</span></label><textarea id="company_description" name="company_description" rows="3" maxlength="5000" data-activity-required><?= e($values['company_description'] ?? '') ?></textarea></div>
                        <div class="form-group form-group-wide"><label for="promoter_experience"><?= $en ? 'Your relevant experience' : 'Votre expérience dans ce domaine' ?></label><textarea id="promoter_experience" name="promoter_experience" rows="3" maxlength="5000"><?= e($values['promoter_experience'] ?? '') ?></textarea></div>
                    </div>
                </div>
                <div class="info-inline"><i class="bi bi-info-circle"></i><?= $en ? 'You will describe your proposed project in the next step.' : 'Vous décrirez votre projet dans l’étape suivante.' ?></div>
            </section>
            <section class="form-step" data-step="3" hidden>
                <div class="form-step-heading"><span><?= $en ? 'STEP 03 / 05' : 'ÉTAPE 03 / 05' ?></span><h2><?= $en ? 'Your project' : 'Votre projet' ?></h2><p><?= $en ? 'Help the review team understand what you want to build and why.' : 'Aidez l’équipe d’étude à comprendre ce que vous souhaitez réaliser et pourquoi.' ?></p></div>
                <div class="form-grid">
                    <div class="form-group"><label for="project_name"><?= $en ? 'Project name' : 'Nom du projet' ?> <span>*</span></label><input id="project_name" name="project_name" maxlength="190" data-required value="<?= e($values['project_name'] ?? '') ?>"></div>
                    <div class="form-group"><label for="project_sector"><?= $en ? 'Sector' : 'Secteur d’activité' ?> <span>*</span></label><select id="project_sector" name="project_sector" data-required><option value=""><?= $en ? 'Select a sector' : 'Choisir un secteur' ?></option><?php foreach ($sectors as $sector): ?><option value="<?= e($sector) ?>" <?= ($values['project_sector'] ?? '') === $sector ? 'selected' : '' ?>><?= e($sector) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label for="project_subsector"><?= $en ? 'Sub-sector' : 'Sous-secteur' ?></label><input id="project_subsector" name="project_subsector" maxlength="120" value="<?= e($values['project_subsector'] ?? '') ?>"></div>
                    <div class="form-group"><label for="project_stage"><?= $en ? 'Project stage' : 'Stade du projet' ?> <span>*</span></label><select id="project_stage" name="project_stage" data-required><option value=""><?= $en ? 'Choose a stage' : 'Choisir un stade' ?></option><?php foreach ($stages as $key => $stage): ?><option value="<?= e($key) ?>" <?= ($values['project_stage'] ?? '') === $key ? 'selected' : '' ?>><?= e($stage) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label for="project_country"><?= $en ? 'Project country' : 'Pays du projet' ?> <span>*</span></label><input id="project_country" name="project_country" maxlength="100" data-required value="<?= e($values['project_country'] ?? '') ?>"></div>
                    <div class="form-group"><label for="project_region"><?= $en ? 'Region / state' : 'Région / département' ?></label><input id="project_region" name="project_region" maxlength="100" value="<?= e($values['project_region'] ?? '') ?>"></div>
                    <div class="form-group"><label for="project_city"><?= $en ? 'City' : 'Ville' ?></label><input id="project_city" name="project_city" maxlength="100" value="<?= e($values['project_city'] ?? '') ?>"></div>
                    <div class="form-group"><label for="project_location"><?= $en ? 'Project location / neighbourhood' : 'Localisation / quartier du projet' ?></label><input id="project_location" name="project_location" maxlength="190" value="<?= e($values['project_location'] ?? '') ?>"></div>
                    <div class="form-group form-group-wide"><label for="project_description"><?= $en ? 'Describe the project' : 'Description détaillée du projet' ?> <span>*</span></label><textarea id="project_description" name="project_description" rows="4" maxlength="10000" data-required><?= e($values['project_description'] ?? '') ?></textarea></div>
                    <div class="form-group form-group-wide"><label for="project_objective"><?= $en ? 'Main objective' : 'Objectif principal' ?></label><textarea id="project_objective" name="project_objective" rows="2" maxlength="5000"><?= e($values['project_objective'] ?? '') ?></textarea></div>
                    <div class="form-group form-group-wide"><label for="project_problem"><?= $en ? 'What problem does it address?' : 'À quel problème répond le projet ?' ?> <span>*</span></label><textarea id="project_problem" name="project_problem" rows="3" maxlength="5000" data-required><?= e($values['project_problem'] ?? '') ?></textarea></div>
                    <div class="form-group form-group-wide"><label for="project_solution"><?= $en ? 'What solution do you propose?' : 'Quelle solution proposez-vous ?' ?> <span>*</span></label><textarea id="project_solution" name="project_solution" rows="3" maxlength="5000" data-required><?= e($values['project_solution'] ?? '') ?></textarea></div>
                    <div class="form-group"><label for="target_customers"><?= $en ? 'Target customers or beneficiaries' : 'Clientèle cible ou bénéficiaires' ?></label><textarea id="target_customers" name="target_customers" rows="3" maxlength="3000"><?= e($values['target_customers'] ?? '') ?></textarea></div>
                    <div class="form-group"><label for="competitors"><?= $en ? 'Main alternatives or competitors' : 'Principaux concurrents ou alternatives' ?></label><textarea id="competitors" name="competitors" rows="3" maxlength="3000"><?= e($values['competitors'] ?? '') ?></textarea></div>
                    <div class="form-group form-group-wide"><label for="competitive_advantage"><?= $en ? 'What makes your approach distinctive?' : 'Quel est votre avantage concurrentiel ?' ?></label><textarea id="competitive_advantage" name="competitive_advantage" rows="2" maxlength="3000"><?= e($values['competitive_advantage'] ?? '') ?></textarea></div>
                    <div class="form-group"><label for="current_jobs"><?= $en ? 'Current jobs supported' : 'Emplois actuels' ?></label><input id="current_jobs" type="number" min="0" max="1000000" name="current_jobs" value="<?= e($values['current_jobs'] ?? '') ?>"></div>
                    <div class="form-group"><label for="jobs_to_create"><?= $en ? 'Jobs you expect to create' : 'Emplois à créer' ?></label><input id="jobs_to_create" type="number" min="0" max="1000000" name="jobs_to_create" value="<?= e($values['jobs_to_create'] ?? '') ?>"></div>
                </div>
            </section>
            <section class="form-step" data-step="4" hidden>
                <div class="form-step-heading"><span><?= $en ? 'STEP 04 / 05' : 'ÉTAPE 04 / 05' ?></span><h2><?= $en ? 'Project budget' : 'Budget du projet' ?></h2><p><?= $en ? 'Provide an estimate in the currency you selected.' : 'Indiquez vos estimations dans la devise choisie.' ?></p></div>
                <div class="form-grid">
                    <div class="form-group"><label for="amount_requested"><?= $en ? 'Funding requested' : 'Montant demandé' ?> <span>*</span></label><input id="amount_requested" name="amount_requested" type="number" min="0.01" step="0.01" data-required value="<?= e($values['amount_requested'] ?? '') ?>"></div>
                    <div class="form-group"><label for="currency"><?= $en ? 'Currency' : 'Devise' ?> <span>*</span></label><select id="currency" name="currency" data-required><option value=""><?= $en ? 'Select currency' : 'Choisir une devise' ?></option><?php foreach (['XAF','XOF','NGN','USD','EUR','GHS','KES'] as $code): ?><option value="<?= e($code) ?>" <?= ($values['currency'] ?? 'XAF') === $code ? 'selected' : '' ?>><?= e($code) ?></option><?php endforeach; ?></select></div>
                    <div class="form-group"><label for="personal_contribution"><?= $en ? 'Your contribution' : 'Apport personnel' ?> <span>*</span></label><input id="personal_contribution" name="personal_contribution" type="number" min="0" step="0.01" data-required value="<?= e($values['personal_contribution'] ?? '0') ?>"></div>
                    <div class="form-group"><label for="total_cost"><?= $en ? 'Total project cost' : 'Coût total du projet' ?> <span>*</span></label><input id="total_cost" name="total_cost" type="number" min="0.01" step="0.01" data-required value="<?= e($values['total_cost'] ?? '') ?>"></div>
                    <div class="form-group form-group-wide">
                        <fieldset class="use-fieldset"><legend><?= $en ? 'How will the funding be used?' : 'Utilisation prévue du financement' ?> <span>*</span></legend><p><?= $en ? 'Select all that apply.' : 'Sélectionnez toutes les options pertinentes.' ?></p><div class="use-grid">
                            <?php foreach ($uses as $use): ?><label class="check-tile"><input type="checkbox" name="fund_uses[]" value="<?= e($use) ?>" <?= in_array($use, (array)($values['fund_uses'] ?? []), true) ? 'checked' : '' ?>><span><i class="bi bi-check2"></i><?= e($use) ?></span></label><?php endforeach; ?>
                        </div></fieldset>
                    </div>
                    <div class="form-group form-group-wide"><label for="fund_use_details"><?= $en ? 'Explain how you will use the requested funding' : 'Expliquez précisément comment le financement sera utilisé' ?> <span>*</span></label><textarea id="fund_use_details" name="fund_use_details" rows="4" maxlength="5000" data-required><?= e($values['fund_use_details'] ?? '') ?></textarea></div>
                </div>
            </section>
            <section class="form-step" data-step="5" hidden>
                <div class="form-step-heading"><span><?= $en ? 'STEP 05 / 05' : 'ÉTAPE 05 / 05' ?></span><h2><?= $en ? 'Review and confirm' : 'Vérification et consentements' ?></h2><p><?= $en ? 'Before submitting, confirm the statements below.' : 'Avant l’envoi, confirmez les déclarations suivantes.' ?></p></div>
                <div class="review-panel">
                    <div class="review-icon"><i class="bi bi-shield-check"></i></div>
                    <h3><?= $en ? 'Your declaration' : 'Votre déclaration' ?></h3>
                    <p><?= $en ? 'Please confirm each required statement. Your application will be reviewed according to the programme criteria.' : 'Veuillez confirmer chaque déclaration requise. Votre candidature sera étudiée selon les critères du programme.' ?></p>
                </div>
                <div class="consent-list">
                    <label class="consent-item"><input type="checkbox" name="consent_accuracy" value="1" data-required <?= isset($values['consent_accuracy']) ? 'checked' : '' ?>><span><i class="bi bi-check2"></i></span><span><?= $en ? 'I certify that the information provided is accurate to the best of my knowledge.' : 'Je certifie que les informations fournies sont exactes.' ?> <em>*</em></span></label>
                    <label class="consent-item"><input type="checkbox" name="consent_privacy" value="1" data-required <?= isset($values['consent_privacy']) ? 'checked' : '' ?>><span><i class="bi bi-check2"></i></span><span><?= $en ? 'I have read and accept the' : 'J’ai lu et j’accepte la' ?> <a href="privacy.php" target="_blank"><?= e(t('legal')) ?></a>. <em>*</em></span></label>
                    <label class="consent-item"><input type="checkbox" name="consent_processing" value="1" data-required <?= isset($values['consent_processing']) ? 'checked' : '' ?>><span><i class="bi bi-check2"></i></span><span><?= $en ? 'I authorise the processing of my information to review this application.' : 'J’autorise le traitement de mes données dans le cadre de l’étude de ma candidature.' ?> <em>*</em></span></label>
                </div>
                <div class="notice-box notice-box-compact"><i class="bi bi-info-circle"></i><p><?= e(t('not_funding')) ?></p></div>
            </section>
            <div class="form-navigation">
                <button class="button button-outline" type="button" data-step-prev hidden><i class="bi bi-arrow-left"></i> <?= e(t('previous')) ?></button>
                <span class="form-nav-spacer"></span>
                <button class="button button-red" type="button" data-step-next><?= e(t('next')) ?> <i class="bi bi-arrow-right"></i></button>
                <button class="button button-red" type="submit" data-step-submit hidden><?= e(t('submit')) ?> <i class="bi bi-send"></i></button>
            </div>
        </form>
    </div>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
