<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// XML sitemaps. These resolve through CI4 rather than being real files on
// disk, so they always reflect the live database (see Sitemap controller).
// The (:num) pages exist so RFQ/supplier sitemaps can split at 50k URLs.
$routes->get('sitemap.xml', 'Sitemap::index');
$routes->get('sitemap-categories.xml', 'Sitemap::categories');
$routes->get('sitemap-buyer-categories.xml', 'Sitemap::buyerCategories');
$routes->get('sitemap-locations.xml', 'Sitemap::locations');
$routes->get('sitemap-static.xml', 'Sitemap::staticPagesMap');
$routes->get('sitemap-rfqs-(:num).xml', 'Sitemap::inquiries/$1');
$routes->get('sitemap-suppliers-(:num).xml', 'Sitemap::suppliers/$1');

$routes->get('/', 'Pages::index');
$routes->get('about-us', 'Pages::index/about-us');
$routes->get('contact', 'Pages::index/contact');
$routes->get('success-stories', 'Pages::index/success-stories');
$routes->post('contact/submit', 'Contact::submit');
$routes->post('contact/submit-ajax', 'Contact::submitAjax');


$routes->get('dashboard/submissions', 'Dashboard::submissions');
$routes->get('dashboard/submissions/view/(:num)', 'Dashboard::viewSubmission/$1');
$routes->post('dashboard/submissions/update-status/(:num)', 'Dashboard::updateSubmissionStatus/$1');
$routes->get('dashboard/submissions/delete/(:num)', 'Dashboard::deleteSubmission/$1');
$routes->get('privacy-policy', 'Pages::index/privacy-policy');
$routes->get('refund-policy', 'Pages::index/refund-policy');
$routes->get('terms-and-conditions', 'Pages::index/terms-and-conditions');
$routes->get('user-guide', 'Pages::index/user-guide');
$routes->get('banned-keywords-and-illegal-products-policy', 'Pages::index/banned-keywords-and-illegal-products-policy');
$routes->get('become-our-agent-partner', 'Pages::index/become-our-agent-partner');
$routes->get('tradeshow-marketing-services', 'Pages::index/tradeshow-marketing-services');
$routes->get('premium-services', 'Pages::index/premium-services');
$routes->get('premium-services/starter-package', 'Pages::index/starter-package');
$routes->get('premium-services/gold-package', 'Pages::index/gold-package');
$routes->get('premium-services/platinum-package', 'Pages::index/platinum-package');
$routes->get('premium-services/vip-package', 'Pages::index/vip-package');
// Search: clean path-segment URLs are canonical (e.g. /buyer/search/steel-plates
// instead of /buyer/search?q=steel+plates). The bare '?q=' routes below stay
// registered so old links, bookmarks and submitted GET forms keep working --
// the controller 301s them to the equivalent clean path. See seo_helper.php
// (parse_search_path / build_search_path) for the shared encode/decode logic.
$routes->get('search', 'Search::index');
$routes->get('search/(:any)', 'Search::index/$1');
$routes->get('product', 'Product::index');
$routes->get('product/supplier/(:num)', 'Product::bySupplier/$1');
$routes->get('product/search', 'Product::search');
$routes->get('product/search/(:any)', 'Product::search/$1');
$routes->get('product/detail/(:num)', 'Product::detail/$1');

$routes->get('supplier', 'Supplier::index');
$routes->get('supplier/search', 'Supplier::search');
$routes->get('supplier/search/(:any)', 'Supplier::search/$1');
$routes->get('supplier/profile/(:any)', 'Supplier::profile/$1');
$routes->get('supplier-profile', 'Supplier::index');
$routes->get('supplier-country', 'Supplier::country');
$routes->get('supplier-country/(:segment)', 'Supplier::country/$1');
$routes->get('supplier-category', 'Supplier::category');
$routes->get('supplier-category/(:segment)', 'Supplier::category/$1');

$routes->get('buyer', 'Buyer::index');
$routes->get('buyers', 'Buyer::index');
$routes->get('buyer/post-rfq', 'Buyer::postRfq');
$routes->post('buyer/post-rfq', 'Contact::submit');
$routes->get('buyer/search', 'Buyer::search');
$routes->get('buyer/search/(:any)', 'Buyer::search/$1');
$routes->get('buyers/(:segment)', 'Buyer::category/$1');
$routes->get('buyer-category/(:segment)', static function($slug) { return redirect()->to(base_url('buyers/' . $slug), 301); });
$routes->get('buyer-category', static function() { return redirect()->to(base_url('buyers'), 301); });
$routes->get('buyer/detail/(:num)', 'Buyer::legacyRedirect/$1');
$routes->get('buyer-detail', 'Buyer::index');
$routes->get('buyer-detail/(:num)', 'Buyer::legacyRedirect/$1');
// Legacy "fake slug" URLs: /buyer-inquiry/{anything}/{id}. The slug segment was
// never validated against the row, so any slug served any inquiry. The id is
// authoritative, so these 301 to the canonical slug URL rather than rendering.
// Keep (:any) — it compiles to (.*), which is what still catches the
// /buyer-inquiry/a/b/c/{id} shapes that used to resolve and may be indexed.
$routes->get('buyer-inquiry/(:any)/(:num)', 'Buyer::legacyRedirect/$2');

// Canonical. (:segment) is ([^/]+) so it can never match the two-segment form
// above, and the two routes cannot shadow each other.
$routes->get('buyer-inquiry/(:segment)', 'Buyer::detail/$1');
$routes->get('buyer-inquiry', static function () { return redirect()->to(base_url('buyers'), 301); });

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::forgotPassword');
$routes->get('reset-password/(:alphanum)', 'Auth::resetPassword/$1');
$routes->post('reset-password/(:alphanum)', 'Auth::resetPassword/$1');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

$routes->get('dashboard', 'Dashboard::index');
$routes->get('dashboard/admin', 'Dashboard::admin');
$routes->get('dashboard/chart-data', 'Dashboard::chartData');
$routes->get('dashboard/supplier', 'Dashboard::supplier');
$routes->get('dashboard/buyer', 'Dashboard::buyer');
$routes->get('dashboard/profile', 'Dashboard::profile');
$routes->post('dashboard/profile/update', 'Dashboard::updateProfile');
$routes->get('dashboard/users', 'Dashboard::users');
$routes->get('dashboard/approve/(:num)', 'Dashboard::approveUser/$1');
$routes->get('dashboard/reject/(:num)', 'Dashboard::rejectUser/$1');
$routes->get('dashboard/delete/(:num)', 'Dashboard::deleteUser/$1');
$routes->get('dashboard/admin-edit-user/(:num)', 'Dashboard::adminEditUser/$1');
$routes->post('dashboard/admin-edit-user/(:num)', 'Dashboard::adminEditUser/$1');

$routes->get('dashboard/agents', 'Dashboard::agents');
$routes->post('dashboard/agents/add', 'Dashboard::addAgent');
$routes->get('dashboard/agents/edit/(:num)', 'Dashboard::editAgent/$1');
$routes->post('dashboard/agents/edit/(:num)', 'Dashboard::editAgent/$1');
$routes->get('dashboard/agents/delete/(:num)', 'Dashboard::deleteAgent/$1');

$routes->get('dashboard/suppliers', 'Dashboard::suppliers');
$routes->get('dashboard/suppliers/add', 'Dashboard::addSupplier');
$routes->post('dashboard/suppliers/add', 'Dashboard::addSupplier');
$routes->get('dashboard/suppliers/edit/(:num)', 'Dashboard::editSupplier/$1');
$routes->post('dashboard/suppliers/edit/(:num)', 'Dashboard::editSupplier/$1');
$routes->get('dashboard/suppliers/delete/(:num)', 'Dashboard::deleteSupplier/$1');
$routes->get('dashboard/suppliers/(:num)/add-product', 'Dashboard::adminAddProductForSupplier/$1');
$routes->post('dashboard/suppliers/(:num)/add-product', 'Dashboard::adminAddProductForSupplier/$1');
$routes->get('dashboard/suppliers/(:num)/edit-product/(:num)', 'Dashboard::adminEditProductForSupplier/$1/$2');
$routes->post('dashboard/suppliers/(:num)/edit-product/(:num)', 'Dashboard::adminEditProductForSupplier/$1/$2');

$routes->get('dashboard/inquiries', 'Dashboard::inquiries');
$routes->get('dashboard/inquiries/add', 'Dashboard::addInquiry');
$routes->post('dashboard/inquiries/add', 'Dashboard::addInquiry');
$routes->get('dashboard/inquiries/edit/(:num)', 'Dashboard::editInquiry/$1');
$routes->post('dashboard/inquiries/edit/(:num)', 'Dashboard::editInquiry/$1');
$routes->get('dashboard/inquiries/delete/(:num)', 'Dashboard::deleteInquiry/$1');

$routes->get('dashboard/supplier/products', 'Dashboard::supplierProducts');
$routes->get('dashboard/supplier/products/add', 'Dashboard::supplierAddProduct');
$routes->post('dashboard/supplier/products/add', 'Dashboard::supplierAddProduct');
$routes->get('dashboard/supplier/products/edit/(:num)', 'Dashboard::supplierEditProduct/$1');
$routes->post('dashboard/supplier/products/edit/(:num)', 'Dashboard::supplierEditProduct/$1');
$routes->post('dashboard/supplier/products/delete/(:num)', 'Dashboard::supplierDeleteProduct/$1');
$routes->post('dashboard/supplier/products/toggle-status/(:num)', 'Dashboard::supplierToggleProductStatus/$1');
$routes->get('dashboard/supplier/profile/edit', 'Dashboard::supplierEditProfile');
$routes->post('dashboard/supplier/profile/edit', 'Dashboard::supplierEditProfile');

$routes->get('dashboard/buyer/inquiries', 'Dashboard::buyerInquiries');
$routes->get('dashboard/buyer/inquiries/add', 'Dashboard::buyerAddInquiry');
$routes->post('dashboard/buyer/inquiries/add', 'Dashboard::buyerAddInquiry');
$routes->get('dashboard/buyer/inquiries/edit/(:num)', 'Dashboard::buyerEditInquiry/$1');
$routes->post('dashboard/buyer/inquiries/edit/(:num)', 'Dashboard::buyerEditInquiry/$1');
$routes->get('dashboard/buyer/inquiries/delete/(:num)', 'Dashboard::buyerDeleteInquiry/$1');

$routes->get('admin/import/suppliers', 'AdminImport::suppliers', ['filter' => 'role:admin']);
$routes->post('admin/import/suppliers', 'AdminImport::suppliers', ['filter' => 'role:admin']);
$routes->get('admin/import/products', 'AdminImport::products', ['filter' => 'role:admin']);
$routes->post('admin/import/products', 'AdminImport::products', ['filter' => 'role:admin']);
$routes->get('admin/import/inquiries', 'AdminImport::inquiries', ['filter' => 'role:admin']);
$routes->post('admin/import/inquiries', 'AdminImport::inquiries', ['filter' => 'role:admin']);

$routes->get('admin/settings', 'AdminSettings::index');
$routes->get('admin/settings/general', 'AdminSettings::general');
$routes->post('admin/settings/general', 'AdminSettings::general');
$routes->get('admin/settings/seo', 'AdminSettings::seo');
$routes->post('admin/settings/seo', 'AdminSettings::seo');
$routes->post('admin/settings/refresh-sitemaps', 'AdminSettings::refreshSitemaps');
$routes->get('admin/settings/moderation', 'AdminSettings::moderation');
$routes->post('admin/settings/moderation', 'AdminSettings::moderation');
$routes->get('admin/settings/categories', 'AdminSettings::categories');
$routes->post('admin/settings/categories', 'AdminSettings::categories');
$routes->get('admin/settings/listings', 'AdminSettings::listings');
$routes->post('admin/settings/listings', 'AdminSettings::listings');
$routes->get('admin/settings/registration', 'AdminSettings::registration');
$routes->post('admin/settings/registration', 'AdminSettings::registration');
$routes->get('admin/settings/email', 'AdminSettings::email');
$routes->post('admin/settings/email', 'AdminSettings::email');

// Popup lead-capture flow (T-29). Singular 'lead' -- deliberately distinct from
// the plural 'leads/*' CRM routes just below, which are LeadManagement's
// unrelated `users`-as-leads admin views. See .claude/plans/T-29-lead-capture.md
// (:alphanum), matching reset-password/(:alphanum) above -- the token is a
// plain hex string, never contains '/', so there's no risk of the (:any)
// re-splitting behaviour found and fixed during the search-URL work (T-28).
$routes->post('lead/capture', 'LeadCapture::capture');
$routes->get('lead/verify/(:alphanum)', 'LeadCapture::verify/$1');
$routes->get('lead/complete/(:alphanum)', 'LeadCapture::completeSignup/$1');
$routes->post('lead/complete/(:alphanum)', 'LeadCapture::completeSignup/$1');

$routes->get('leads/all', 'LeadManagement::allLeads');
$routes->get('leads/buyer', 'LeadManagement::buyerLeads');
$routes->get('leads/supplier', 'LeadManagement::supplierLeads');
$routes->get('leads/popup', 'LeadManagement::popupLeads');
$routes->get('leads/popup/edit/(:num)', 'LeadManagement::editPopupLead/$1');
$routes->post('leads/popup/edit/(:num)', 'LeadManagement::editPopupLead/$1');
$routes->post('leads/popup/delete/(:num)', 'LeadManagement::deletePopupLead/$1');
$routes->post('leads/popup/add-note', 'LeadManagement::addPopupLeadNote');
$routes->get('leads/my-supplier', 'LeadManagement::mySupplierLeads');
$routes->get('leads/my-buyer', 'LeadManagement::myBuyerLeads');
$routes->get('leads/detail/(:segment)', 'LeadManagement::leadDetail/$1');
$routes->post('leads/update-stage', 'LeadManagement::updateLeadStage');
$routes->post('leads/assign-agent', 'LeadManagement::assignAgent');
$routes->post('leads/add-note', 'LeadManagement::addNote');
$routes->post('leads/update-membership', 'LeadManagement::updateMembership');
$routes->post('leads/ajax-update-stage', 'LeadManagement::ajaxUpdateStage');
$routes->post('leads/ajax-add-note', 'LeadManagement::ajaxAddNote');
