(function () {
    function initAgendaSlider(root = document) {
        const sliders = root.querySelectorAll('.agenda-slider');

        sliders.forEach((slider) => {
            if (slider._agendaSliderInterval) {
                window.clearInterval(slider._agendaSliderInterval);
            }

            let currentSlide = 0;
            const slides = slider.querySelectorAll('.agenda-slide');

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.remove('active');
                    if (i === index) {
                        slide.classList.add('active');
                    }
                });
            }

            function nextSlide() {
                currentSlide++;
                if (currentSlide >= slides.length) {
                    currentSlide = 0;
                }
                showSlide(currentSlide);
            }

            if (slides.length > 0) {
                showSlide(currentSlide);
                if (slides.length > 1) {
                    slider._agendaSliderInterval = window.setInterval(nextSlide, 10000);
                }
            }
        });
    }

    window.initAgendaSlider = initAgendaSlider;
    document.addEventListener("DOMContentLoaded", () => initAgendaSlider());
})();
