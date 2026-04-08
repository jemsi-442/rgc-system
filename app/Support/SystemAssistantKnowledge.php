<?php

namespace App\Support;

class SystemAssistantKnowledge
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function topics(string $locale = 'en'): array
    {
        $locale = $locale === 'sw' ? 'sw' : 'en';

        return array_values(array_filter(
            self::defaultRows(),
            fn (array $row): bool => $row['locale'] === $locale
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function defaultRows(): array
    {
        return [
            ...self::englishTopics(),
            ...self::swahiliTopics(),
        ];
    }

    /**
     * @return array{topic:string, answer:string, suggestions:array<int,string>}
     */
    public static function fallback(string $locale = 'en'): array
    {
        if ($locale === 'sw') {
            return [
                'topic' => 'system-help',
                'answer' => 'Nipo kusaidia kueleza mfumo huu wa RGC kwa lugha rahisi. Uliza kuhusu usajili, kuingia, roles za viongozi, branches, matangazo, branch chat, malipo ya utoaji, risiti, lugha, au dashboard ya mfumo huu.',
                'suggestions' => [
                    'Ninawezaje kusajili akaunti?',
                    'Nani anaweza kutuma tangazo kwa wote?',
                    'Ninawezaje kutoa sadaka au offering?',
                ],
            ];
        }

        return [
            'topic' => 'system-help',
            'answer' => 'I can explain this RGC platform in plain language. Ask me about registration, login, leadership roles, branches, announcements, branch chat, giving payments, receipts, language switching, or dashboard use.',
            'suggestions' => [
                'How do I register an account?',
                'Who can send announcements to everyone?',
                'How do I make an offering payment?',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function englishTopics(): array
    {
        return [
            [
                'slug' => 'greeting',
                'locale' => 'en',
                'title' => 'Welcome and help',
                'keywords' => ['hi', 'hello', 'help', 'hey', 'good morning', 'good evening'],
                'answer' => 'Welcome. I am the RGC system assistant. I can explain registration, login, dashboards, branches, announcements, branch chat, giving payments, and receipts in simple language.',
                'suggestions' => ['How do I log in?', 'What roles exist in the system?', 'How do I make an offering payment?'],
                'roles' => null,
                'sort_order' => 10,
            ],
            [
                'slug' => 'registration',
                'locale' => 'en',
                'title' => 'Registration flow',
                'keywords' => ['register', 'registration', 'sign up', 'create account', 'new account'],
                'answer' => 'To register, open the Register page and fill in your name, email, password, region, district, and branch in the correct hierarchy. The system validates that the district belongs to the selected region and the branch belongs to that district before it allows registration.',
                'suggestions' => ['How do I log in after registering?', 'How do I change my password?', 'How do I switch language?'],
                'roles' => null,
                'sort_order' => 20,
            ],
            [
                'slug' => 'login',
                'locale' => 'en',
                'title' => 'Login and password',
                'keywords' => ['login', 'log in', 'sign in', 'password', 'email'],
                'answer' => 'Use your registered email and password on the Login page. After successful sign-in, you are taken to a dashboard that is automatically scoped to your role. You can also change your own password from the My Password area inside the dashboard.',
                'suggestions' => ['What does the member dashboard show?', 'Who can see all users?', 'How do I change my password?'],
                'roles' => null,
                'sort_order' => 30,
            ],
            [
                'slug' => 'roles',
                'locale' => 'en',
                'title' => 'Roles and governance',
                'keywords' => ['roles', 'role', 'super admin', 'regional admin', 'district admin', 'branch admin', 'member', 'authority'],
                'answer' => 'The platform uses governance roles such as Super Admin, Regional Admin, District Admin, Branch Admin, Pastor, Bishop, Accountant, and Member. Each role only sees data inside its approved scope. Super Admin works at national level, while the other roles stay inside their region, district, or branch boundaries.',
                'suggestions' => ['What can Super Admin do?', 'Where can a regional admin send announcements?', 'What does Branch Admin see on the dashboard?'],
                'roles' => null,
                'sort_order' => 40,
            ],
            [
                'slug' => 'users-super-admin',
                'locale' => 'en',
                'title' => 'Super Admin user control',
                'keywords' => ['users', 'create user', 'delete user', 'edit user', 'reset password', 'all users'],
                'answer' => 'For your Super Admin role, the Users area lets you view all accounts, create new users, assign leadership roles, change another user’s password, and delete accounts except your own self-delete. This is the full governance user control surface.',
                'suggestions' => ['How do I create a regional admin?', 'Can I reset another user password?', 'Where is the Users page?'],
                'roles' => ['super_admin'],
                'sort_order' => 50,
            ],
            [
                'slug' => 'branches-super-admin',
                'locale' => 'en',
                'title' => 'Branch management',
                'keywords' => ['branch', 'branches', 'create branch', 'import branch', 'csv', 'excel'],
                'answer' => 'For Super Admin, the Branches module supports manual branch creation, edit, delete, branch profile view, CSV or Excel import with preview, template download, and filtered export. The system validates hierarchy, branch type, and duplicates before saving anything.',
                'suggestions' => ['How do I download a branch template?', 'Can I import many branches at once?', 'What is on the branch profile page?'],
                'roles' => ['super_admin'],
                'sort_order' => 60,
            ],
            [
                'slug' => 'announcements-super-admin',
                'locale' => 'en',
                'title' => 'National announcements',
                'keywords' => ['announcement', 'announcements', 'send to everyone', 'global announcement', 'selected branches'],
                'answer' => 'For Super Admin, announcements can go to the whole platform or only to selected branches. You can attach an image, pin the announcement, set expiry, open details, and export it as PDF with the official RGC letterhead.',
                'suggestions' => ['Can I target selected branches only?', 'Can announcements include images?', 'What happens when an announcement expires?'],
                'roles' => ['super_admin'],
                'sort_order' => 70,
            ],
            [
                'slug' => 'announcements-regional-admin',
                'locale' => 'en',
                'title' => 'Regional announcement delivery',
                'keywords' => ['regional announcement', 'regional admin announcement', 'district only announcement', 'branch only announcement'],
                'answer' => 'For Regional Admin, announcements can be sent to the whole region, a selected district, or a selected branch inside your region. The form automatically limits you to districts and branches that belong to your approved region.',
                'suggestions' => ['Can I target one branch only?', 'Can I send to the whole region?', 'How does the delivery preview work?'],
                'roles' => ['regional_admin'],
                'sort_order' => 80,
            ],
            [
                'slug' => 'announcements-district-admin',
                'locale' => 'en',
                'title' => 'District announcement delivery',
                'keywords' => ['district announcement', 'district admin announcement', 'district scope'],
                'answer' => 'For District Admin, announcements remain inside your district scope. The system will not let you publish outside your approved district, so visibility stays consistent with governance rules.',
                'suggestions' => ['Who can send to the whole platform?', 'Can I send outside my district?', 'How do members see my announcement?'],
                'roles' => ['district_admin'],
                'sort_order' => 90,
            ],
            [
                'slug' => 'branch-operations',
                'locale' => 'en',
                'title' => 'Branch operations',
                'keywords' => ['branch admin', 'pastor', 'bishop', 'accountant', 'operations', 'offerings', 'expenses'],
                'answer' => 'For branch leadership roles, your dashboard focuses on branch operations: chat, announcements inside scope, offerings, expenses, giving requests, payment alerts, and branch-level records. Everything remains limited to your approved branch or inherited scope.',
                'suggestions' => ['How do payment alerts work?', 'Can I review alerts?', 'Where do I record offerings?'],
                'roles' => ['branch_admin', 'pastor', 'bishop', 'accountant'],
                'sort_order' => 100,
            ],
            [
                'slug' => 'chat',
                'locale' => 'en',
                'title' => 'Branch chat',
                'keywords' => ['chat', 'branch chat', 'message', 'messages', 'reply', 'attachment', 'file'],
                'answer' => 'Branch Chat is the internal conversation area for each branch. It supports sending messages, replying, editing inside the allowed time window, deleting within policy scope, and attaching one or many files. It also has near-realtime updates, unread indicators, and a message-app style layout.',
                'suggestions' => ['How do I send attachments?', 'Who can delete a message?', 'Does chat work well on mobile?'],
                'roles' => null,
                'sort_order' => 110,
            ],
            [
                'slug' => 'giving',
                'locale' => 'en',
                'title' => 'Giving and offering payments',
                'keywords' => ['offering', 'giving', 'payment', 'sadaka', 'contribution', 'snippe'],
                'answer' => 'You can use the Giving flow to choose the giving type, enter an amount, add the payer phone, and send a secure payment prompt. After payment confirmation, the system records the final offering in the correct branch ledger and makes a receipt available on the status page.',
                'suggestions' => ['Where is the Giving page?', 'How do I get a receipt?', 'What giving types can I use?'],
                'roles' => null,
                'sort_order' => 120,
            ],
            [
                'slug' => 'giving-member',
                'locale' => 'en',
                'title' => 'Member giving',
                'keywords' => ['offering', 'giving', 'payment', 'sadaka', 'contribution', 'snippe'],
                'answer' => 'As a member, you can use the Giving page to choose the giving type, enter an amount, and send a payment prompt to the payer phone. After payment confirmation, the system posts the final offering to the branch ledger and makes a receipt available for download.',
                'suggestions' => ['Where is the Giving page?', 'How do I get a receipt?', 'What giving types can I use?'],
                'roles' => ['member'],
                'sort_order' => 121,
            ],
            [
                'slug' => 'giving-admin',
                'locale' => 'en',
                'title' => 'Admin payment requests',
                'keywords' => ['payment request', 'snippe', 'payment prompt', 'offering payment', 'sync payment'],
                'answer' => 'For admins, the Offering payment tools can create direct payment prompts, track pending requests, sync status, review alerts, and confirm that successful payments have already posted into the branch ledger.',
                'suggestions' => ['How do I create a payment request?', 'How do payment alerts work?', 'Can I review all alerts at once?'],
                'roles' => ['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant'],
                'sort_order' => 130,
            ],
            [
                'slug' => 'receipts',
                'locale' => 'en',
                'title' => 'Receipts and alerts',
                'keywords' => ['receipt', 'pdf', 'payment status', 'status page', 'receipt email'],
                'answer' => 'When a payment is completed, the system provides a status page and a PDF receipt. The donor can download that receipt, and if mail delivery is configured for production, the receipt can also be emailed. Branch leaders can receive a payment completion alert as well.',
                'suggestions' => ['When do alert emails go out?', 'How do I check payment status?', 'Does the dashboard show payment alerts?'],
                'roles' => null,
                'sort_order' => 140,
            ],
            [
                'slug' => 'language',
                'locale' => 'en',
                'title' => 'Language switching',
                'keywords' => ['language', 'swahili', 'english', 'locale', 'change language'],
                'answer' => 'The platform supports both Kiswahili and English. You can switch language from the top of the page, and the preference can be saved on your account so it returns after login. Many validation messages are also localized.',
                'suggestions' => ['How do I switch language?', 'Does the dashboard remember my language?', 'Does the API support Kiswahili too?'],
                'roles' => null,
                'sort_order' => 150,
            ],
            [
                'slug' => 'slides',
                'locale' => 'en',
                'title' => 'Homepage slides',
                'keywords' => ['slides', 'slider', 'homepage', 'hero image'],
                'answer' => 'Super Admin manages homepage slides from the Slides module. It supports create, edit, delete, quick status toggle, quick sort ordering, drag-and-drop upload, and live preview. Slide images are wired to render correctly on the homepage, including mobile views.',
                'suggestions' => ['How do I add a slide?', 'Why was a slide image not showing?', 'Where do I change slide order?'],
                'roles' => ['super_admin'],
                'sort_order' => 160,
            ],
            [
                'slug' => 'dashboard',
                'locale' => 'en',
                'title' => 'Dashboard scope',
                'keywords' => ['dashboard', 'home screen', 'shortcuts', 'stats', 'alerts'],
                'answer' => 'Each dashboard is automatically shaped by the user’s role and governance scope. It shows the right statistics, announcements, quick actions, payment alerts, and management tools for that level. Members see the giving surface, while leaders see wider operational panels.',
                'suggestions' => ['What does the member dashboard show?', 'What does Super Admin see?', 'How are payment alerts reviewed?'],
                'roles' => null,
                'sort_order' => 170,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function swahiliTopics(): array
    {
        return [
            [
                'slug' => 'greeting',
                'locale' => 'sw',
                'title' => 'Karibu na msaada',
                'keywords' => ['habari', 'hi', 'hello', 'mambo', 'shikamoo', 'salaam', 'msaada'],
                'answer' => 'Karibu. Mimi ni msaidizi wa mfumo wa RGC. Naweza kueleza jinsi ya kutumia usajili, login, dashboard, branches, matangazo, branch chat, na malipo ya utoaji kwa lugha rahisi ya kueleweka.',
                'suggestions' => ['Ninawezaje kuingia?', 'Roles za mfumo ni zipi?', 'Ninawezaje kutoa offering?'],
                'roles' => null,
                'sort_order' => 10,
            ],
            [
                'slug' => 'registration',
                'locale' => 'sw',
                'title' => 'Usajili wa akaunti',
                'keywords' => ['register', 'usajili', 'jisajili', 'fungua akaunti', 'akaunti mpya', 'signup'],
                'answer' => 'Ili kujisajili, fungua ukurasa wa Register, kisha jaza jina, email, password, region, district, na branch kwa mpangilio sahihi. Mfumo unakagua kuwa wilaya ni ya mkoa uliouchagua na tawi ni la wilaya hiyo ili usajili usivurugike.',
                'suggestions' => ['Ninawezaje kuingia baada ya usajili?', 'Nilibadilisheje password?', 'Ninawezaje kubadili lugha?'],
                'roles' => null,
                'sort_order' => 20,
            ],
            [
                'slug' => 'login',
                'locale' => 'sw',
                'title' => 'Kuingia na nenosiri',
                'keywords' => ['login', 'ingia', 'sign in', 'nenosiri', 'password', 'email'],
                'answer' => 'Ili kuingia, tumia email na password yako kwenye ukurasa wa Login. Ukishathibitishwa utaingia kwenye dashboard yenye scope ya role yako. Pia unaweza kubadili password yako ukiwa ndani ya dashboard kupitia sehemu ya My Password.',
                'suggestions' => ['Dashboard ya member ina nini?', 'Nani anaweza kuona users wote?', 'Ninawezaje kubadili password?'],
                'roles' => null,
                'sort_order' => 30,
            ],
            [
                'slug' => 'roles',
                'locale' => 'sw',
                'title' => 'Roles na utawala',
                'keywords' => ['roles', 'role', 'viongozi', 'super admin', 'regional admin', 'district admin', 'branch admin', 'member', 'mamlaka'],
                'answer' => 'Mfumo una ngazi za uongozi kama Super Admin, Regional Admin, District Admin, Branch Admin, Pastor, Bishop, Accountant, na Member. Kila role huona data ndani ya scope yake tu. Super Admin ana mamlaka ya kitaifa, huku viongozi wengine wakibaki kwenye region, district, au branch yao.',
                'suggestions' => ['Super Admin anaweza kufanya nini?', 'Regional admin anaweza kutuma tangazo wapi?', 'Branch admin anaona nini kwenye dashboard?'],
                'roles' => null,
                'sort_order' => 40,
            ],
            [
                'slug' => 'users-super-admin',
                'locale' => 'sw',
                'title' => 'Udhibiti wa users kwa Super Admin',
                'keywords' => ['users', 'watumiaji', 'ongeza user', 'futa user', 'edit user', 'badili password ya user', 'all users'],
                'answer' => 'Kwa role yako ya Super Admin, sehemu ya Users inakuruhusu kuona accounts zote, kuongeza users wapya, kugawa roles za uongozi, kubadili password ya user mwingine, na kufuta account yoyote isipokuwa kujifuta mwenyewe. Hii ndiyo control kamili ya users wa mfumo.',
                'suggestions' => ['Ninawezaje kuunda regional admin?', 'Naweza kubadili password ya user mwingine?', 'Users page iko wapi?'],
                'roles' => ['super_admin'],
                'sort_order' => 50,
            ],
            [
                'slug' => 'branches-super-admin',
                'locale' => 'sw',
                'title' => 'Usimamizi wa matawi',
                'keywords' => ['branch', 'branches', 'tawi', 'matawi', 'ongeza branch', 'create branch', 'import branch', 'csv', 'excel'],
                'answer' => 'Kwa Super Admin, sehemu ya Branches ina support ya kuunda tawi kwa mkono, ku-edit, kufuta, kuona profile ya tawi, ku-import matawi mengi kwa CSV au Excel, kupakua templates, na kufanya export yenye filters. Mfumo hukagua hierarchy, branch type, na duplicates kabla ya kusave.',
                'suggestions' => ['Ninawezaje kupakua branch template?', 'Naweza ku-import matawi mengi mara moja?', 'Branch profile inaonyesha nini?'],
                'roles' => ['super_admin'],
                'sort_order' => 60,
            ],
            [
                'slug' => 'announcements-super-admin',
                'locale' => 'sw',
                'title' => 'Matangazo ya kitaifa',
                'keywords' => ['announcement', 'announcements', 'tangazo', 'matangazo', 'kwa wote', 'global announcement', 'selected branches'],
                'answer' => 'Kwa Super Admin, tangazo linaweza kwenda kwa mfumo wote au kwa branches ulizochagua tu. Unaweza kuambatisha picha, kupiga pin tangazo, kuweka expiry date, kufungua details, na kupakua PDF yenye official letterhead ya RGC.',
                'suggestions' => ['Naweza kuchagua branches maalum tu?', 'Tangazo linaweza kuwa na picha?', 'Tangazo liki-expire linafanyikaje?'],
                'roles' => ['super_admin'],
                'sort_order' => 70,
            ],
            [
                'slug' => 'announcements-regional-admin',
                'locale' => 'sw',
                'title' => 'Usambazaji wa tangazo la mkoa',
                'keywords' => ['regional announcement', 'regional admin announcement', 'district only announcement', 'branch only announcement', 'tangazo la mkoa'],
                'answer' => 'Kwa Regional Admin, tangazo linaweza kwenda kwa mkoa mzima, wilaya iliyochaguliwa, au branch iliyochaguliwa ndani ya region yako. Form inakuzuia kuchagua wilaya au branch nje ya region yako iliyoidhinishwa.',
                'suggestions' => ['Naweza kulenga branch moja tu?', 'Naweza kutuma kwa mkoa wote?', 'Delivery preview inafanyaje kazi?'],
                'roles' => ['regional_admin'],
                'sort_order' => 80,
            ],
            [
                'slug' => 'announcements-district-admin',
                'locale' => 'sw',
                'title' => 'Usambazaji wa tangazo la wilaya',
                'keywords' => ['district announcement', 'district admin announcement', 'district scope', 'tangazo la wilaya'],
                'answer' => 'Kwa District Admin, tangazo hubaki ndani ya district scope yako. Mfumo hautakuruhusu kuchapisha nje ya district yako iliyoidhinishwa, hivyo uonekano wa tangazo unafuata sheria za utawala wa mfumo.',
                'suggestions' => ['Nani anaweza kutuma kwa mfumo wote?', 'Naweza kutuma nje ya wilaya yangu?', 'Members wanaonaje tangazo langu?'],
                'roles' => ['district_admin'],
                'sort_order' => 90,
            ],
            [
                'slug' => 'branch-operations',
                'locale' => 'sw',
                'title' => 'Kazi za uongozi wa tawi',
                'keywords' => ['branch admin', 'pastor', 'bishop', 'accountant', 'operations', 'offerings', 'expenses'],
                'answer' => 'Kwa roles za uongozi wa tawi, dashboard yako inalenga shughuli za branch: chat, matangazo ndani ya scope, offerings, expenses, giving requests, payment alerts, na records za branch. Kila kitu hubaki ndani ya branch au scope uliyoidhinishwa.',
                'suggestions' => ['Payment alerts zinafanyaje kazi?', 'Naweza ku-review alerts?', 'Offerings zinawekwa wapi?'],
                'roles' => ['branch_admin', 'pastor', 'bishop', 'accountant'],
                'sort_order' => 100,
            ],
            [
                'slug' => 'chat',
                'locale' => 'sw',
                'title' => 'Branch chat',
                'keywords' => ['chat', 'branch chat', 'ujumbe', 'message', 'messages', 'reply', 'attachment', 'file'],
                'answer' => 'Branch Chat ni mawasiliano ya ndani ya tawi. Inasaidia kutuma ujumbe, reply, edit ndani ya muda uliowekwa, delete kwa scope inayoruhusiwa, na attachments moja au nyingi. Pia kuna near-realtime updates, unread indicators, na muonekano unaofanana na message app.',
                'suggestions' => ['Ninawezaje kutuma attachment?', 'Nani anaweza kufuta ujumbe?', 'Chat inafanya kazi kwa simu?'],
                'roles' => null,
                'sort_order' => 110,
            ],
            [
                'slug' => 'giving',
                'locale' => 'sw',
                'title' => 'Utoaji na malipo ya sadaka',
                'keywords' => ['offering', 'sadaka', 'giving', 'mchango', 'payment', 'snippe', 'toa sadaka', 'toa offering'],
                'answer' => 'Unaweza kutumia mtiririko wa Giving kuchagua aina ya utoaji, kuingiza kiasi, kuongeza namba ya mlipaji, na kutuma payment prompt iliyo salama. Malipo yakithibitishwa, mfumo unaandika offering ya mwisho kwenye ledger sahihi ya tawi na kufanya receipt ipatikane kwenye status page.',
                'suggestions' => ['Giving page iko wapi?', 'Receipt inapatikana wapi?', 'Ni aina gani za utoaji zinaruhusiwa?'],
                'roles' => null,
                'sort_order' => 120,
            ],
            [
                'slug' => 'giving-member',
                'locale' => 'sw',
                'title' => 'Giving ya member',
                'keywords' => ['offering', 'sadaka', 'giving', 'mchango', 'payment', 'snippe', 'toa sadaka', 'toa offering'],
                'answer' => 'Kama member, unaweza kutumia Giving page kuchagua aina ya utoaji, kuingiza kiasi, na kutuma payment prompt kwenye simu ya mlipaji. Malipo yakithibitishwa, mfumo unaandika offering ya mwisho kwenye ledger ya tawi na kufanya receipt ipatikane kwa kupakua.',
                'suggestions' => ['Giving page iko wapi?', 'Receipt inapatikana wapi?', 'Ni aina gani za utoaji zinaruhusiwa?'],
                'roles' => ['member'],
                'sort_order' => 121,
            ],
            [
                'slug' => 'giving-admin',
                'locale' => 'sw',
                'title' => 'Maombi ya malipo kwa admin',
                'keywords' => ['payment request', 'snippe', 'payment prompt', 'offering payment', 'sync payment'],
                'answer' => 'Kwa admins, zana za Offering payments zinaweza kutengeneza direct payment prompts, kufuatilia requests zinazosubiri, kusync status, ku-review alerts, na kuthibitisha kuwa malipo yaliyofanikiwa tayari yameingia kwenye branch ledger.',
                'suggestions' => ['Ninaanzishaje payment request?', 'Payment alerts zinafanyaje kazi?', 'Naweza ku-review alerts zote kwa pamoja?'],
                'roles' => ['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant'],
                'sort_order' => 130,
            ],
            [
                'slug' => 'receipts',
                'locale' => 'sw',
                'title' => 'Risiti na alerts',
                'keywords' => ['receipt', 'risiti', 'pdf', 'payment status', 'status page', 'receipt email'],
                'answer' => 'Malipo yakikamilika, mfumo unatengeneza status page na receipt PDF. Donor anaweza kupakua receipt hiyo, na kama mailer imewekwa kwa production, receipt inaweza kutumwa kwa email pia. Viongozi wa tawi wanaweza pia kupata alert ya payment mpya iliyokamilika.',
                'suggestions' => ['Email za alerts zinatoka lini?', 'Ninawezaje kuona status ya malipo?', 'Dashboard inaonyesha payment alerts?'],
                'roles' => null,
                'sort_order' => 140,
            ],
            [
                'slug' => 'language',
                'locale' => 'sw',
                'title' => 'Kubadili lugha',
                'keywords' => ['language', 'lugha', 'swahili', 'kiswahili', 'english', 'badili lugha'],
                'answer' => 'Mfumo una support ya Kiswahili na English. Unaweza kubadili lugha juu ya ukurasa, na preference hiyo inaweza kuhifadhiwa kwenye account yako ili irudi hata ukilogin tena. Validation messages nyingi pia zimewekewa Kiswahili.',
                'suggestions' => ['Ninawezaje kubadili lugha?', 'Dashboard inahifadhi lugha yangu?', 'API nayo ina support ya Kiswahili?'],
                'roles' => null,
                'sort_order' => 150,
            ],
            [
                'slug' => 'slides',
                'locale' => 'sw',
                'title' => 'Homepage slides',
                'keywords' => ['slides', 'slider', 'homepage', 'hero image', 'slaidi'],
                'answer' => 'Super Admin anaweza kusimamia homepage slides kupitia module ya Slides. Kuna create, edit, delete, quick status toggle, quick sort order, drag-and-drop upload, na live preview. Picha za slides zinaonyeshwa vizuri kwenye homepage na pia kwenye mobile.',
                'suggestions' => ['Ninawezaje kuongeza slide?', 'Kwa nini slide image haikuonekana?', 'Sort order ya slide inabadilishwa wapi?'],
                'roles' => ['super_admin'],
                'sort_order' => 160,
            ],
            [
                'slug' => 'dashboard',
                'locale' => 'sw',
                'title' => 'Dashboard na scope',
                'keywords' => ['dashboard', 'home screen', 'shortcuts', 'stats', 'alerts'],
                'answer' => 'Dashboard ya kila user hujipanga kulingana na role na scope yake. Hapo utaona statistics, announcements, quick actions, payment alerts, na sehemu nyingine za kazi zinazofaa ngazi yako. Member anaona sehemu ya Giving, huku viongozi wa branches au ngazi za juu wakiona panels pana zaidi.',
                'suggestions' => ['Member dashboard ina nini?', 'Super Admin dashboard ina nini?', 'Payment alerts zina-reviewiwaje?'],
                'roles' => null,
                'sort_order' => 170,
            ],
        ];
    }
}
