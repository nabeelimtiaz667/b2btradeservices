// Mobile Menu Toogle
function toggleNavigation() {
            var navigation = document.querySelector('.navigation');
            navigation.classList.toggle('active');
        }

//  Sticky Bar
    window.addEventListener("scroll", function () {
      const div = document.getElementById("sticky-bar");

      if (div) {
        if (window.scrollY >= 150) {
          div.style.display = "block";
        } else {
          div.style.display = "none";
        }
      }
    });
 
// Popup 
(function() {
    const popup = document.getElementById('popup');
    const overlay = document.getElementById('overlay');
    const openBtn = document.querySelector('.register-your-company');
    const closeBtn = document.getElementById('closePopup');

    if (openBtn && popup && overlay) {
        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            popup.style.display = 'block';
            overlay.style.display = 'block';
        });
    }

    if (closeBtn && popup && overlay) {
        closeBtn.addEventListener('click', function() {
            popup.style.display = 'none';
            overlay.style.display = 'none';
        });
    }

    if (overlay && popup) {
        overlay.addEventListener('click', function() {
            popup.style.display = 'none';
            overlay.style.display = 'none';
        });
    }
})();

 
// Popup 
(function() {
    const popup1 = document.getElementById('popup1');
    const overlay1 = document.getElementById('overlay1');
    const openBtn1 = document.querySelector('.bcm-cta');
    const closeBtn1 = document.getElementById('closePopup1');

    if (openBtn1 && popup1 && overlay1) {
        openBtn1.addEventListener('click', function(e) {
            e.preventDefault();
            popup1.style.display = 'block';
            overlay1.style.display = 'block';
        });
    }

    if (closeBtn1 && popup1 && overlay1) {
        closeBtn1.addEventListener('click', function() {
            popup1.style.display = 'none';
            overlay1.style.display = 'none';
        });
    }

    if (overlay1 && popup1) {
        overlay1.addEventListener('click', function() {
            popup1.style.display = 'none';
            overlay1.style.display = 'none';
        });
    }
})();
 

// Paswword Eye
function togglePassword() {
    const iconElement = event.currentTarget;

    const parent = iconElement.closest(".form-input.password-input");
    if (!parent) return;

    const password = parent.querySelector(".password");
    const eyeIcon = parent.querySelector(".eye-icon");

    if (!password || !eyeIcon) return;

    if (password.type === "password") {
        password.type = "text";
        eyeIcon.style.fill = "#45BB7F";
    } else {
        password.type = "password";
        eyeIcon.style.fill = "#DBDBDB";
    }
}
 
// Tel
// :not(.lead-capture-phone) -- lead-capture-inline-form.php's phone field
// keeps the .phone class for its existing CSS (e.g. .b2b-top-form .phone),
// but assets/js/homepage-lead-forms.js owns its intlTelInput init and submit
// handling instead (separate phone/phone_code fields, not a single E.164
// value). Double-initializing the same input here would create a second
// .iti widget and this file's own submit handler would overwrite the
// already-correct local-digits value with the full international number.
    document.querySelectorAll(".phone:not(.lead-capture-phone)").forEach(input => {
  const iti = window.intlTelInput(input, {
    initialCountry: "us",
    separateDialCode: true,
    preferredCountries: ["us", "gb", "in", "pk"],
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
  });
  const form = input.closest('form');
  if (form) {
    form.addEventListener('submit', function() {
      const fullNumber = iti.getNumber();
      if (fullNumber) {
        input.value = fullNumber;
      }
    });
  }
});

// Sticky bar radio/select sync and search form handling
document.querySelectorAll('.search-form').forEach(function(form) {
    const select = form.querySelector('.mobile-filter-select');
    const radios = form.querySelectorAll('input[name="type"]');

    if (select && radios.length) {
        select.addEventListener('change', function () {
            radios.forEach(function(radio) {
                if (radio.value === select.value) {
                    radio.checked = true;
                }
            });
        });

        radios.forEach(function(radio) {
            radio.addEventListener('change', function () {
                if (this.checked && select) {
                    select.value = this.value;
                }
            });
        });
    }
});


// Toggle More Options
function toggleOptions() {
  const options = document.getElementById("moreOptions");
  const arrow = document.getElementById("arrowIcon");
  const text = document.getElementById("btnText");

  const isHidden = getComputedStyle(options).display === "none";

  if (isHidden) {
    options.style.display = "block";
    arrow.classList.add("rotate");
    text.innerText = "Less Options";
  } else {
    options.style.display = "none";
    arrow.classList.remove("rotate");
    text.innerText = "More Options";
  }
}

// Radio Field On off
function toggleFields(radio) {
    const form = radio.closest("form");
    const selling = form.querySelector(".selling-field");
    const buying = form.querySelector(".buying-field");

    const sellingInputs = selling.querySelectorAll("input, select, textarea");
    const buyingInputs = buying.querySelectorAll("input, select, textarea");

    if (radio.value === "supplier") {
        selling.style.display = "block";
        buying.style.display = "none";

        // selling required
        sellingInputs.forEach(input => input.required = true);
        // buying not required
        buyingInputs.forEach(input => input.required = false);

    } else {
        selling.style.display = "none";
        buying.style.display = "block";

        // buying required
        buyingInputs.forEach(input => input.required = true);
        // selling not required
        sellingInputs.forEach(input => input.required = false);
    }
}

// Show More / Show Less for Premium Packages
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.show-more-wrapper').forEach(function(wrapper) {
        var visibleCount = parseInt(wrapper.getAttribute('data-visible')) || 5;
        var items = wrapper.querySelectorAll('p');
        var btn = wrapper.querySelector('.show-more-btn');
        
        items.forEach(function(item, index) {
            if (index < visibleCount) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });

        if (items.length <= visibleCount && btn) {
            btn.style.display = 'none';
        }

        if (btn) {
            btn.addEventListener('click', function() {
                var isExpanded = btn.getAttribute('data-expanded') === 'true';
                items.forEach(function(item, index) {
                    if (index >= visibleCount) {
                        item.style.display = isExpanded ? 'none' : 'flex';
                    }
                });
                btn.textContent = isExpanded ? 'Show More' : 'Show Less';
                btn.setAttribute('data-expanded', !isExpanded);
            });
        }
    });
});
