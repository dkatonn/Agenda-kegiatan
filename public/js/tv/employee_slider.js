(function () {
    function initEmployeeSlider(root = document) {
        const container = root.querySelector(".profile-box");
        if (!container) {
            return;
        }

        if (container._employeeSliderInterval) {
            window.clearInterval(container._employeeSliderInterval);
        }

        const track = container.querySelector(".profile-track");
        const slides = container.querySelectorAll(".profile-slide");
        const dots = container.querySelectorAll(".dot");

        if (!track || slides.length === 0) {
            return;
        }

        let index = 0;

        function updateSlider() {
            track.style.transform = `translateX(-${index * 100}%)`;

            dots.forEach((dot, i) => {
                dot.classList.toggle("active", i === index);
            });
        }

        function nextSlide() {
            index = (index + 1) % slides.length;
            updateSlider();
        }

        updateSlider();

        if (slides.length > 1) {
            container._employeeSliderInterval = window.setInterval(nextSlide, 8000);
        }
    }

    window.initEmployeeSlider = initEmployeeSlider;
    document.addEventListener("DOMContentLoaded", () => initEmployeeSlider());
})();
