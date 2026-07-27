<?= $this->extend('layouts/inner') ?>

<?= $this->section('styles') ?>
 <style>
       
 
      .rfq-page.container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            gap: 60px;
                margin-top: 50px;
        }

         .rfq-page   .rfq-form-section {
            flex: 2;
        }

          .rfq-page  .rfq-tips-section {
            flex: 1;
            border-left: 1px solid #eee;
            padding-left: 40px;
        }

          .rfq-page  h1 {
            font-size: 28px;
            color: var(--dark-green);
            margin: 0 0 5px 0;
        }

        .rfq-page    .subtitle {
            font-size: 14px;
            color: #888;
            margin-bottom: 30px;
        }

         .rfq-page   .form-group {
            margin-bottom: 20px;
        }

         .rfq-page   .form-group label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            color: #555;
        }

          .rfq-page  .form-group input[type="text"],
          .rfq-page  .form-group input[type="email"],
         .rfq-page   .form-group select,
         .rfq-page   .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

         .rfq-page   .form-group input:focus,
          .rfq-page  .form-group textarea:focus {
            border-color: var(--primary-teal);
        }

         .rfq-page   .form-group textarea {
            height: 120px;
            resize: vertical;
        }

          .rfq-page  .row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

         .rfq-page   .row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

         .rfq-page   .attachment-group {
            margin-top: 30px;
        }

         .rfq-page   .file-input-wrapper {
            margin-top: 10px;
        }

          /* .rfq-page  .btn-submit {
            margin-top: 30px;
            padding: 12px 40px;
            background-color: var(--accent-orange);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

          .rfq-page  .btn-submit:hover {
            background-color: #e65c00;
        } */

        /* Tips Section Styling */
          .rfq-page  .rfq-tips-section h3 {
            font-size: 18px;
            color: var(--dark-green);
            margin-top: 0;
        }

        .rfq-page    .tip-item {
            margin-bottom: 25px;
            font-size: 14px;
            color: #777;
        }

         .rfq-page   .tip-item p {
            margin: 5px 0;
        }

          .rfq-page  .tip-example {
            color: var(--accent-orange);
            font-style: italic;
        }

        .rfq-page    .tip-list {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }

          .rfq-page  .tip-list li {
            margin-bottom: 5px;
        }

        /* Responsive */
        @media (max-width: 850px) {
              .rfq-page.container {
                flex-direction: column;
                padding: 20px;
            }
              .rfq-page  .rfq-tips-section {
                border-left: none;
                border-top: 1px solid #eee;
                padding-left: 0;
                padding-top: 30px;
            }
           .rfq-page .row {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/contact-us-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>

<div class="container rfq-page">
    
    <!-- Left: RFQ Form -->
    <div class="rfq-form-section">
        <h1>Tell suppliers what you need</h1>
        <p class="subtitle">The more detailed information, the better the reach to suppliers</p>

        <form>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" placeholder="Looking for">
            </div>

            <div class="form-group">
                <label>Details</label>
                <textarea placeholder="Please provide the details of the product that you want to purchase, such as color, material, size, weight, packaging, certification requirements, or any other specifications."></textarea>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Required Quantity</label>
                    <input type="text" placeholder="e.g. 1 Ton">
                </div>
                <div class="form-group">
                    <label>Buying Frequency</label>
                    <select>
                        <option value="" disabled selected>Select</option>
                        <option value="one-time">One Time</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annually">Annually</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" placeholder="Name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" placeholder="Phone Number">
                </div>
            </div>

            <div class="form-group attachment-group">
                <label>Attachment</label>
                <div class="file-input-wrapper">
                    <input type="file">
                </div>
            </div>

            <button type="submit" class="btn-submit gradeint-cta">Submit</button>
        </form>
    </div>

    <!-- Right: RFQ Tips -->
    <div class="rfq-tips-section">
        <h3>RFQ Tips</h3>
        
        <div class="tip-item">
            <p>- Specify complete subject of your buying requirement</p>
            <p class="tip-example">e.g. I want to buy basmati rice</p>
        </div>

        <div class="tip-item">
            <p>- Specify product attributes that could help in matching best products of your requirements. e.g.</p>
            <ul class="tip-list">
                <li>Application</li>
                <li>Certification</li>
                <li>Packaging</li>
                <li>Type</li>
            </ul>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
