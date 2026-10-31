const form = document.getElementById("tripForm");
const requirements = document.getElementById("requirements");
const charCount = document.getElementById("charCount");

if (requirements && charCount) {
    const updateCount = () => {
        charCount.textContent = requirements.value.length;
    };
    requirements.addEventListener("input", updateCount);
    updateCount();
}

if (form) {
    form.addEventListener("submit", (event) => {
        const phone = document.getElementById("phone");
        const age = document.getElementById("age");
        const travellers = document.getElementById("travellers");
        const travelDate = document.getElementById("travel_date");

        const phonePattern = /^[0-9+\-\s]{7,15}$/;
        if (!phonePattern.test(phone.value.trim())) {
            event.preventDefault();
            alert("Please enter a valid phone number.");
            phone.focus();
            return;
        }

        if (Number(age.value) < 1 || Number(age.value) > 120) {
            event.preventDefault();
            alert("Please enter an age between 1 and 120.");
            age.focus();
            return;
        }

        if (Number(travellers.value) < 1 || Number(travellers.value) > 20) {
            event.preventDefault();
            alert("Please enter between 1 and 20 travellers.");
            travellers.focus();
            return;
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selected = new Date(`${travelDate.value}T00:00:00`);
        if (!travelDate.value || selected < today) {
            event.preventDefault();
            alert("Please select today or a future travel date.");
            travelDate.focus();
            return;
        }

        if (!form.reportValidity()) {
            event.preventDefault();
            return;
        }

        if (!window.confirm("Submit your trip registration now?")) {
            event.preventDefault();
        }
    });
}
