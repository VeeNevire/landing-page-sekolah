const portalSidebar = document.getElementById("portalSidebar");
document.getElementById("portalMenuButton")?.addEventListener("click", () => {
    portalSidebar?.classList.toggle("open");
});
document.addEventListener("click", (event) => {
    if (
        portalSidebar?.classList.contains("open") &&
        !portalSidebar.contains(event.target) &&
        !event.target.closest("#portalMenuButton")
    )
        portalSidebar.classList.remove("open");
});

document
    .getElementById("studentSwitcher")
    ?.addEventListener("change", (event) => {
        const url = new URL(window.location.href);
        url.searchParams.set("student_id", event.target.value);
        window.location.href = url.toString();
    });

document
    .getElementById("printReport")
    ?.addEventListener("click", () => window.print());

document
    .getElementById("subjectFilter")
    ?.addEventListener("change", (event) => {
        const selected = event.target.value;
        document.querySelectorAll("[data-subject-row]").forEach((row) => {
            row.hidden =
                selected !== "all" && row.dataset.subjectRow !== selected;
        });
        document.querySelectorAll("[data-subject-detail]").forEach((row) => {
            row.hidden =
                selected !== "all" && row.dataset.subjectDetail !== selected;
        });
    });

document.querySelectorAll("[data-toggle-password]").forEach((button) => {
    button.addEventListener("click", () => {
        const input = document.getElementById(button.dataset.togglePassword);
        if (!input) return;
        input.type = input.type === "password" ? "text" : "password";
        button.textContent =
            input.type === "password" ? "Tampilkan" : "Sembunyikan";
    });
});
