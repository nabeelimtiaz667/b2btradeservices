<?php

if (! function_exists('check_restricted_keywords')) {
    /**
     * Checks $text against the admin-configured 'restricted_keywords' list
     * (Site Settings > Content Moderation) and, if 'profanity_filter' is
     * enabled, a small built-in list. Returns the first matched word/phrase,
     * or false if nothing matched.
     *
     * Single shared implementation -- this used to be copy-pasted separately
     * into Dashboard.php, Contact.php and Auth.php, which is how the
     * supplier/buyer profile-edit forms ended up with no check at all: it's
     * easy to add a new form and forget to paste this in. Every place that
     * checks user-submitted text against moderation settings should call
     * this instead of keeping its own copy.
     */
    function check_restricted_keywords(string $text)
    {
        $settingModel = new \App\Models\SiteSettingModel();

        $keywords = $settingModel->getSetting('restricted_keywords', '');
        if (!empty($keywords)) {
            $keywordList = array_map('trim', array_filter(preg_split('/[,\n]+/', strtolower($keywords))));
            foreach ($keywordList as $kw) {
                // Word-boundary match, same as the built-in profanity list
                // below -- a raw substring match would false-positive block
                // an admin-entered keyword like "spa" against "aerospace" or
                // "spare parts".
                if (!empty($kw) && preg_match('/\b' . preg_quote($kw, '/') . '\b/iu', $text)) {
                    return $kw;
                }
            }
        }

        $profanityEnabled = $settingModel->getSetting('profanity_filter', '0');
        if ($profanityEnabled === '1') {
            $profanityList = ['damn', 'hell', 'crap', 'stupid', 'idiot', 'fool', 'scam', 'fraud', 'fake', 'spam', 'porn', 'xxx', 'casino', 'gambling', 'drugs', 'narcotic', 'cocaine', 'heroin', 'marijuana', 'counterfeit', 'pirated', 'illegal', 'terrorist', 'weapon', 'explosive', 'smuggle', 'trafficking', 'money laundering'];
            foreach ($profanityList as $word) {
                if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
                    return $word;
                }
            }
        }

        return false;
    }
}
