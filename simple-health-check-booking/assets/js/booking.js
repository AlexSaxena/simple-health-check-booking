document.addEventListener("DOMContentLoaded", function () {
  const clinicSelect = document.getElementById("shcb_clinic");
  const dateInput = document.getElementById("shcb_date");
  const timeSelect = document.getElementById("shcb_time");
  const steps = document.querySelectorAll(".shcb-step");
  const nextButtons = document.querySelectorAll(".shcb-next");
  const prevButtons = document.querySelectorAll(".shcb-prev");
  let currentStep = 0;

  if (
    !clinicSelect ||
    !dateInput ||
    !timeSelect ||
    typeof shcbData === "undefined" ||
    !shcbData.clinics
  ) {
    console.error("A booking form element not found.");
    return;
  }

  const weekdayMap = [
    "söndag",
    "måndag",
    "tisdag",
    "onsdag",
    "torsdag",
    "fredag",
    "lördag",
  ];

  function updateAvailableTimes() {
    const clinicId = clinicSelect.value;
    const selectedDate = dateInput.value;

    timeSelect.innerHTML = '<option value="">Välj tid</option>';

    if (!clinicId || !selectedDate) {
      return;
    }

    const date = new Date(selectedDate);
    const weekday = weekdayMap[date.getDay()];

    const clinic = shcbData.clinics.find(
      (c) => String(c.id) === String(clinicId),
    );

    if (
      !clinic ||
      !clinic.tillgangliga_tider ||
      !clinic.tillgangliga_tider[weekday]
    ) {
      return;
    }

    const times = clinic.tillgangliga_tider[weekday];

    if (!times || times.length === 0) {
      const option = document.createElement("option");
      option.value = "";
      option.textContent = "Inga tider tillgängliga";
      timeSelect.appendChild(option);
      return;
    }

    times.forEach((time) => {
      const option = document.createElement("option");
      option.value = time;
      option.textContent = time;
      timeSelect.appendChild(option);
    });
  }

  function showStep(index) {
    steps.forEach((step, i) => {
      step.style.display = i === index ? "block" : "none";
    });
  }

  nextButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const currentStepEl = steps[currentStep];

      const inputs = currentStepEl.querySelectorAll("input, select");

      for (let input of inputs) {
        if (!input.checkValidity()) {
          input.reportValidity();
          return;
        }
      }

      if (currentStep < steps.length - 1) {
        currentStep++;
        showStep(currentStep);
      }
    });
  });

  prevButtons.forEach((button) => {
    button.addEventListener("click", function () {
      if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
      }
    });
  });

  showStep(currentStep);

  clinicSelect.addEventListener("change", updateAvailableTimes);
  dateInput.addEventListener("change", updateAvailableTimes);
});
