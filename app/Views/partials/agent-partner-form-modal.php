<style>
.multiselect-container{position:relative}
.multiselect-selected{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.3);border-radius:6px;padding:10px 14px;color:#fff;cursor:pointer;min-height:44px;display:flex;flex-wrap:wrap;gap:5px;align-items:center}
.multiselect-selected .placeholder{color:rgba(255,255,255,0.5)}
.multiselect-selected .tag{background:rgba(255,255,255,0.2);border-radius:4px;padding:2px 8px;font-size:12px;display:flex;align-items:center;gap:4px}
.multiselect-selected .tag .remove{cursor:pointer;font-weight:bold}
.multiselect-dropdown{display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ddd;border-radius:6px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.15)}
.multiselect-dropdown.open{display:block}
.multiselect-dropdown .option{padding:10px 14px;cursor:pointer;color:#333;font-size:14px;transition:background .2s}
.multiselect-dropdown .option:hover{background:#f0f0f0}
.multiselect-dropdown .option.selected{background:#e8f5e9;color:#1a7a3a;font-weight:600}
</style>

<div class="modal fade" id="applyNowModal" tabindex="-1" aria-labelledby="applyNowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content multiple-quote-form" style="border:none;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="apFormSuccess" class="alert alert-success d-none">Thank you! Your application has been submitted. We will contact you shortly.</div>
                <div id="apFormError" class="alert alert-danger d-none"></div>

                <h2 class="text-center mb-4">Apply Now</h2>
                <form id="agentPartnerApplyForm" action="<?= base_url('contact/submit-ajax') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="agent_partner_application">
                    <input type="hidden" name="source_page" value="become-our-agent-partner">

                    <div class="form-input mt-3">
                        <input type="text" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-input">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-input">
                        <input type="tel" name="phone" class="phone" placeholder="Phone number" required>
                    </div>
                    <div class="whatsapp-checkbox">
                        <input type="checkbox" name="whatsapp" id="ap_whatsapp" value="1">
                        <label for="ap_whatsapp">Whatsapp <img src="<?= base_url('assets/images/whatsapp-icon.svg') ?>" width="15px"></label>
                    </div>
                    <div class="form-input">
                        <select class="form-control country-select" required name="country_id">
                            <option value="" selected>Country</option>
                            <option value="1">Andorra</option><option value="2">United Arab Emirates</option><option value="3">Afghanistan</option><option value="4">Antigua and Barbuda</option><option value="5">Anguilla</option><option value="6">Albania</option><option value="7">Armenia</option><option value="8">Netherlands Antilles</option><option value="9">Angola</option><option value="10">Argentina</option><option value="11">Austria</option><option value="12">Australia</option><option value="13">Aruba</option><option value="14">Azerbaijan</option><option value="15">Bosnia and Herzegovina</option><option value="16">Barbados</option><option value="17">Bangladesh</option><option value="18">Belgium</option><option value="19">Burkina Faso</option><option value="20">Bulgaria</option><option value="21">Bahrain</option><option value="22">Burundi</option><option value="23">Benin</option><option value="24">Bermuda</option><option value="25">Brunei Darussalam</option><option value="26">Bolivia</option><option value="27">Brazil</option><option value="28">Bahamas</option><option value="29">Bhutan</option><option value="30">Botswana</option><option value="31">Belarus</option><option value="32">Belize</option><option value="33">Canada</option><option value="34">Cocos (Keeling) Islands</option><option value="35">Democratic Republic of the Congo</option><option value="36">Central African Republic</option><option value="37">Congo</option><option value="38">Switzerland</option><option value="39">Cote D'Ivoire (Ivory Coast)</option><option value="40">Cook Islands</option><option value="41">Chile</option><option value="42">Cameroon</option><option value="43">China</option><option value="44">Colombia</option><option value="45">Costa Rica</option><option value="46">Cuba</option><option value="47">Cape Verde</option><option value="48">Christmas Island</option><option value="49">Cyprus</option><option value="50">Czech Republic</option><option value="51">Germany</option><option value="52">Djibouti</option><option value="53">Denmark</option><option value="54">Dominica</option><option value="55">Dominican Republic</option><option value="56">Algeria</option><option value="57">Ecuador</option><option value="58">Estonia</option><option value="59">Egypt</option><option value="60">Western Sahara</option><option value="61">Eritrea</option><option value="62">Spain</option><option value="63">Ethiopia</option><option value="64">Finland</option><option value="65">Fiji</option><option value="66">Falkland Islands (Malvinas)</option><option value="67">Federated States of Micronesia</option><option value="68">Faroe Islands</option><option value="69">France</option><option value="70">Gabon</option><option value="71">United Kingdom</option><option value="72">Grenada</option><option value="73">Georgia</option><option value="74">French Guiana</option><option value="76">Ghana</option><option value="77">Gibraltar</option><option value="78">Greenland</option><option value="79">Gambia</option><option value="80">Guinea</option><option value="81">Guadeloupe</option><option value="82">Equatorial Guinea</option><option value="83">Greece</option><option value="84">S. Georgia and S. Sandwich Islands</option><option value="85">Guatemala</option><option value="86">Guinea-Bissau</option><option value="87">Guyana</option><option value="88">Hong Kong</option><option value="89">Honduras</option><option value="90">Croatia (Hrvatska)</option><option value="91">Haiti</option><option value="92">Hungary</option><option value="93">Indonesia</option><option value="94">Ireland</option><option value="95">Israel</option><option value="96">India</option><option value="97">Iraq</option><option value="98">Iran</option><option value="99">Iceland</option><option value="100">Italy</option><option value="101">Jamaica</option><option value="102">Jordan</option><option value="103">Japan</option><option value="104">Kenya</option><option value="105">Kyrgyzstan</option><option value="106">Cambodia</option><option value="107">Kiribati</option><option value="108">Comoros</option><option value="109">Saint Kitts and Nevis</option><option value="110">Korea (North)</option><option value="111">South Korea</option><option value="112">Kuwait</option><option value="113">Cayman Islands</option><option value="114">Kazakhstan</option><option value="115">Laos</option><option value="116">Lebanon</option><option value="117">Saint Lucia</option><option value="118">Liechtenstein</option><option value="119">Sri Lanka</option><option value="120">Liberia</option><option value="121">Lesotho</option><option value="122">Lithuania</option><option value="123">Luxembourg</option><option value="124">Latvia</option><option value="125">Libya</option><option value="126">Morocco</option><option value="127">Monaco</option><option value="128">Moldova</option><option value="129">Madagascar</option><option value="130">Marshall Islands</option><option value="131">Macedonia</option><option value="132">Mali</option><option value="133">Myanmar</option><option value="134">Mongolia</option><option value="135">Macao</option><option value="136">Northern Mariana Islands</option><option value="137">Martinique</option><option value="138">Mauritania</option><option value="139">Montserrat</option><option value="140">Malta</option><option value="141">Mauritius</option><option value="142">Maldives</option><option value="143">Malawi</option><option value="144">Mexico</option><option value="145">Malaysia</option><option value="146">Mozambique</option><option value="147">Namibia</option><option value="148">New Caledonia</option><option value="149">Niger</option><option value="150">Norfolk Island</option><option value="151">Nigeria</option><option value="152">Nicaragua</option><option value="153">Netherlands</option><option value="154">Norway</option><option value="155">Nepal</option><option value="156">Nauru</option><option value="157">Niue</option><option value="158">New Zealand</option><option value="159">Oman</option><option value="160">Panama</option><option value="161">Peru</option><option value="162">French Polynesia</option><option value="163">Papua New Guinea</option><option value="164">Philippines</option><option value="165">Pakistan</option><option value="166">Poland</option><option value="167">Saint Pierre and Miquelon</option><option value="168">Pitcairn</option><option value="169">Palestinian Territory</option><option value="170">Portugal</option><option value="171">Palau</option><option value="172">Paraguay</option><option value="173">Qatar</option><option value="174">Reunion</option><option value="175">Romania</option><option value="176">Russia</option><option value="177">Rwanda</option><option value="178">Saudi Arabia</option><option value="179">Solomon Islands</option><option value="180">Seychelles</option><option value="181">Sudan</option><option value="182">Sweden</option><option value="183">Singapore</option><option value="184">Saint Helena</option><option value="185">Slovenia</option><option value="186">Svalbard and Jan Mayen</option><option value="187">Slovakia</option><option value="188">Sierra Leone</option><option value="189">San Marino</option><option value="190">Senegal</option><option value="191">Somalia</option><option value="192">Suriname</option><option value="193">Sao Tome and Principe</option><option value="194">El Salvador</option><option value="195">Syria</option><option value="196">Swaziland</option><option value="197">Turks and Caicos Islands</option><option value="198">Chad</option><option value="199">French Southern Territories</option><option value="200">Togo</option><option value="201">Thailand</option><option value="202">Tajikistan</option><option value="203">Tokelau</option><option value="204">Turkmenistan</option><option value="205">Tunisia</option><option value="206">Tonga</option><option value="207">Republic of Türkiye</option><option value="208">Trinidad and Tobago</option><option value="209">Tuvalu</option><option value="210">Taiwan</option><option value="211">Tanzania</option><option value="212">Ukraine</option><option value="213">Uganda</option><option value="214">Uruguay</option><option value="215">Uzbekistan</option><option value="216">Saint Vincent and the Grenadines</option><option value="217">Venezuela</option><option value="218">Virgin Islands (British)</option><option value="219">Virgin Islands (U.S.)</option><option value="220">Vietnam</option><option value="221">Vanuatu</option><option value="222">Wallis and Futuna</option><option value="223">Samoa</option><option value="224">Yemen</option><option value="225">Mayotte</option><option value="226">South Africa</option><option value="227">Zambia</option><option value="228">Zaire (former)</option><option value="229">Zimbabwe</option><option value="230">USA</option><option value="231">American Samoa</option><option value="232">Antarctica</option><option value="233">British Indian Ocean Territory</option><option value="234">Curacao</option><option value="235">East Timor</option><option value="236">Guam</option><option value="237">Isle of Man</option><option value="238">Jersey</option><option value="239">Kosovo</option><option value="240">Montenegro</option><option value="241">Puerto Rico</option><option value="242">Saint Barthelemy</option><option value="243">Saint Martin</option><option value="244">Serbia</option><option value="245">Sint Maarten</option><option value="246">South Sudan</option><option value="247">Vatican</option>
                        </select>
                    </div>

                    <div class="form-input selling-field">
                        <div class="multiselect-container">
                            <div class="multiselect-selected" id="multiselect-display" tabindex="0">
                                <span class="placeholder">What Partnership you want...</span>
                            </div>
                            <div class="multiselect-dropdown" id="multiselect-options">
                                <div class="option" data-value="Exclusive Countrywide Rights of Resellership">Exclusive Countrywide Rights of Resellership</div>
                                <div class="option" data-value="Agency Partnership">Agency Partnership</div>
                                <div class="option" data-value="Affiliate Marketing Program">Affiliate Marketing Program</div>
                                <div class="option" data-value="Direct Partnership">Direct Partnership</div>
                                <div class="option" data-value="Brand Collaboration">Brand Collaboration</div>
                            </div>
                        </div>
                        <select name="partnership[]" id="partnership-hidden" multiple style="display:none;">
                            <option value="Exclusive Countrywide Rights of Resellership">Exclusive Countrywide Rights of Resellership</option>
                            <option value="Agency Partnership">Agency Partnership</option>
                            <option value="Affiliate Marketing Program">Affiliate Marketing Program</option>
                            <option value="Direct Partnership">Direct Partnership</option>
                            <option value="Brand Collaboration">Brand Collaboration</option>
                        </select>
                    </div>

                    <div class="form-input buying-field" style="display:none;">
                        <input type="text" placeholder="Buying Products">
                    </div>

                    <div class="radio-join d-flex gap-2 align-items-start">
                        <input type="checkbox" name="OPT_IN" id="ap_OPT_IN" required>
                        <label for="ap_OPT_IN"><span style="color:#0F9EA5;">*</span>By joining. I agree to terms of use, privacy policy, IPR and agree to receive emails related to our services.</label>
                    </div>
                    <div class="submit-btn-gradient mt-3">
                        <button type="submit" id="apFormBtn" class="gradeint-cta">Join Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var display  = document.getElementById('multiselect-display');
    var dropdown = document.getElementById('multiselect-options');
    var hidden   = document.getElementById('partnership-hidden');
    var selected = [];

    function renderTags() {
        display.innerHTML = '';
        if (!selected.length) {
            display.innerHTML = '<span class="placeholder">What Partnership you want...</span>';
        } else {
            selected.forEach(function(val) {
                var tag = document.createElement('span');
                tag.className = 'tag';
                tag.innerHTML = val + ' <span class="remove" data-val="' + val.replace(/"/g,'&quot;') + '">&times;</span>';
                display.appendChild(tag);
            });
        }
        Array.from(hidden.options).forEach(function(o) { o.selected = selected.indexOf(o.value) > -1; });
        dropdown.querySelectorAll('.option').forEach(function(o) { o.classList.toggle('selected', selected.indexOf(o.dataset.value) > -1); });
    }

    display.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove')) { selected = selected.filter(function(v){ return v !== e.target.dataset.val; }); renderTags(); return; }
        dropdown.classList.toggle('open');
    });
    dropdown.querySelectorAll('.option').forEach(function(o) {
        o.addEventListener('click', function() {
            var v = this.dataset.value, i = selected.indexOf(v);
            i > -1 ? selected.splice(i,1) : selected.push(v); renderTags();
        });
    });
    document.addEventListener('click', function(e) {
        if (!display.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove('open');
    });

    var modal = document.getElementById('applyNowModal');
    if (modal) modal.addEventListener('hidden.bs.modal', function() { selected = []; renderTags(); });

    var form = document.getElementById('agentPartnerApplyForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = document.getElementById('apFormBtn');
        var ok  = document.getElementById('apFormSuccess');
        var err = document.getElementById('apFormError');
        btn.disabled = true; btn.textContent = 'Submitting...';
        ok.classList.add('d-none'); err.classList.add('d-none');
        fetch(form.action, { method:'POST', body:new FormData(form), headers:{'X-Requested-With':'XMLHttpRequest'} })
        .then(function(r){ return r.json(); })
        .then(function(d) {
            if (d.status === 'success') { ok.classList.remove('d-none'); form.reset(); selected=[]; renderTags(); }
            else { err.textContent = d.message || 'Something went wrong.'; err.classList.remove('d-none'); }
            btn.disabled = false; btn.textContent = 'Join Now';
        })
        .catch(function() { err.textContent = 'Something went wrong. Please try again.'; err.classList.remove('d-none'); btn.disabled=false; btn.textContent='Join Now'; });
    });
})();
</script>
