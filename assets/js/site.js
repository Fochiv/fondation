(() => {
    const menuButton = document.querySelector(".menu-toggle");
    const primaryNav = document.querySelector(".primary-nav");
    if (menuButton && primaryNav) {
        menuButton.addEventListener("click", () => {
            const open = menuButton.getAttribute("aria-expanded") !== "true";
            menuButton.setAttribute("aria-expanded", String(open));
            primaryNav.classList.toggle("is-open", open);
        });
        primaryNav.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", () => {
                menuButton.setAttribute("aria-expanded", "false");
                primaryNav.classList.remove("is-open");
            });
        });
        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                menuButton.setAttribute("aria-expanded", "false");
                primaryNav.classList.remove("is-open");
            }
        });
    }

    const sectorToggle = document.querySelector(".sector-toggle");
    const sectorList = sectorToggle && document.getElementById(sectorToggle.getAttribute("aria-controls"));
    if (sectorToggle && sectorList) {
        const showLabel = sectorToggle.dataset.showLabel || sectorToggle.textContent.trim();
        const hideLabel = sectorToggle.dataset.hideLabel || showLabel;
        sectorToggle.addEventListener("click", () => {
            const open = sectorToggle.getAttribute("aria-expanded") !== "true";
            sectorToggle.setAttribute("aria-expanded", String(open));
            sectorList.hidden = !open;
            const label = sectorToggle.querySelector("span");
            if (label) label.textContent = open ? hideLabel : showLabel;
            sectorToggle.querySelector("i")?.classList.toggle("bi-chevron-up", open);
            sectorToggle.querySelector("i")?.classList.toggle("bi-chevron-down", !open);
        });
    }

    const backToTop = document.querySelector(".back-to-top");
    if (backToTop) {
        const syncBackToTop = () => backToTop.classList.toggle("is-visible", window.scrollY > 420);
        window.addEventListener("scroll", syncBackToTop, { passive: true });
        syncBackToTop();
    }

    const statValues = [...document.querySelectorAll(".stat-value[data-count]")];
    if (statValues.length) {
        const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        const animateStat = (element) => {
            const target = Number(element.dataset.count);
            const suffix = element.dataset.suffix || "";
            if (!Number.isFinite(target) || prefersReducedMotion) {
                element.textContent = `${target}${suffix}`;
                return;
            }
            const duration = 1200;
            const started = performance.now();
            const frame = (now) => {
                const progress = Math.min((now - started) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                element.textContent = `${Math.round(target * eased)}${suffix}`;
                if (progress < 1) window.requestAnimationFrame(frame);
            };
            window.requestAnimationFrame(frame);
        };
        if ("IntersectionObserver" in window && !prefersReducedMotion) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        animateStat(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.55 });
            statValues.forEach((element) => observer.observe(element));
        } else {
            statValues.forEach(animateStat);
        }
    }

    const adminMenuButton = document.querySelector(".admin-menu-toggle");
    const adminSidebar = document.querySelector(".admin-sidebar");
    if (adminMenuButton && adminSidebar) {
        adminMenuButton.addEventListener("click", () => {
            const open = adminMenuButton.getAttribute("aria-expanded") !== "true";
            adminMenuButton.setAttribute("aria-expanded", String(open));
            adminSidebar.classList.toggle("is-open", open);
        });
        document.addEventListener("click", (event) => {
            if (window.innerWidth <= 1020 && !adminSidebar.contains(event.target) && !adminMenuButton.contains(event.target)) {
                adminMenuButton.setAttribute("aria-expanded", "false");
                adminSidebar.classList.remove("is-open");
            }
        });
    }

    const applicationForm = document.querySelector("#application-form");
    if (applicationForm) {
        const isEnglish = document.documentElement.lang === "en";
        const steps = [...applicationForm.querySelectorAll(".form-step")];
        const progress = [...document.querySelectorAll("[data-progress-step]")];
        const previous = applicationForm.querySelector("[data-step-prev]");
        const next = applicationForm.querySelector("[data-step-next]");
        const submit = applicationForm.querySelector("[data-step-submit]");
        const activityFields = applicationForm.querySelector("#existing-activity");
        let activeStep = Math.max(0, steps.findIndex((step) => step.classList.contains("is-visible")));

        const syncRequired = () => {
            steps.forEach((step, index) => {
                step.querySelectorAll("[data-required]").forEach((field) => {
                    field.required = index === activeStep;
                });
                const hasActivity = applicationForm.querySelector('input[name="has_activity"]:checked')?.value === "yes";
                step.querySelectorAll("[data-activity-required]").forEach((field) => {
                    field.required = index === activeStep && hasActivity;
                });
            });
            if (activityFields) {
                const show = applicationForm.querySelector('input[name="has_activity"]:checked')?.value === "yes";
                activityFields.hidden = !show;
            }
        };

        const showStep = (index) => {
            activeStep = Math.max(0, Math.min(index, steps.length - 1));
            steps.forEach((step, stepIndex) => {
                const current = stepIndex === activeStep;
                step.hidden = !current;
                step.classList.toggle("is-visible", current);
            });
            progress.forEach((item, stepIndex) => {
                item.classList.toggle("is-current", stepIndex === activeStep);
                item.classList.toggle("is-complete", stepIndex < activeStep);
            });
            previous.hidden = activeStep === 0;
            next.hidden = activeStep === steps.length - 1;
            submit.hidden = activeStep !== steps.length - 1;
            syncRequired();
            if (activeStep > 0) applicationForm.scrollIntoView({ behavior: "smooth", block: "start" });
        };

        const validateCurrent = () => {
            syncRequired();
            const invalid = steps[activeStep].querySelector(":invalid");
            if (invalid) {
                invalid.focus({ preventScroll: false });
                invalid.reportValidity();
                return false;
            }
            if (activeStep === 1 && applicationForm.querySelector('input[name="has_activity"]:checked')?.value === "yes") {
                const description = applicationForm.querySelector("#company_description");
                if (!description.value.trim()) {
                    description.setCustomValidity(isEnglish ? "Please describe your current activity." : "Veuillez décrire votre activité.");
                    description.reportValidity();
                    description.setCustomValidity("");
                    return false;
                }
            }
            if (activeStep === 3 && !steps[activeStep].querySelector('input[name="fund_uses[]"]:checked')) {
                window.alert(isEnglish ? "Select at least one intended use for the funding." : "Sélectionnez au moins une utilisation prévue du financement.");
                steps[activeStep].querySelector('input[name="fund_uses[]"]').focus();
                return false;
            }
            return true;
        };

        next?.addEventListener("click", () => {
            if (validateCurrent()) showStep(activeStep + 1);
        });
        previous?.addEventListener("click", () => showStep(activeStep - 1));
        applicationForm.querySelectorAll('input[name="has_activity"]').forEach((input) => {
            input.addEventListener("change", syncRequired);
        });
        applicationForm.addEventListener("submit", (event) => {
            if (!validateCurrent()) {
                event.preventDefault();
                return;
            }
            if (!applicationForm.querySelector('input[name="fund_uses[]"]:checked')) {
                event.preventDefault();
                showStep(3);
                window.alert(isEnglish ? "Select at least one intended use for the funding." : "Sélectionnez au moins une utilisation prévue du financement.");
            }
        });
        showStep(activeStep);
    }
})();
